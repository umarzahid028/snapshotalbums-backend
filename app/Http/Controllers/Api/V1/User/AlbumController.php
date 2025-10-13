<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriveAccount;
use App\Models\Album;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use App\Http\Resources\AlbumResource;
use Illuminate\Support\Facades\Http;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlbumController extends Controller
{
    protected $gClient;

    public function __construct()
    {
        // Initialize Google Client
        $this->gClient = new Google_Client();
        $this->gClient->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->gClient->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->gClient->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->gClient->setAccessType('offline');
        $this->gClient->setPrompt('consent');
        $this->gClient->setScopes([Google_Service_Drive::DRIVE]);
    }

    public function list()
    {
        try {
            $albums = Album::where('user_id', Auth::id())->latest()->get();

            return response()->json([
                'success' => true,
                'data' => AlbumResource::collection($albums),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch albums: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function create(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $request->validate([
            'event_title' => 'required|string|max:255',
            'event_type' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'event_description' => 'nullable|string',
            // 'google_drive_folder_name' => 'required|string|max:255',
            'event_date' => 'required|date',
        ]);

        try {
            $user = Auth::user();

            $checkAlbumCount = Album::where('user_id', $user->id)->count();
            $driveAccount = DriveAccount::where('user_id', $user->id)->latest()->first();
            $subscription = UserSubscription::where('user_id', $user->id)->latest()->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'You don’t have an active subscription. Please activate one to continue.'
                ], 404);
            }

            if (!$driveAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please connect the drive account.'
                ], 404);
            }

            // Check if drive account is properly connected with tokens
            if (!$driveAccount->google_token || !$driveAccount->google_refresh_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your Google Drive account is not properly connected. Please reconnect it.'
                ], 400);
            }

            if ($subscription && $checkAlbumCount >= $subscription->plan_no_of_ablums) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the maximum number of albums allowed for your plan. Please <a href="https://snapshotalbums.net/pricing" style="color: #059669; text-decoration: underline;">update your plan</a> to create another album.'
                ], 403);
            }

            // 🔹 Check subscription status
            // Allow both 'trialing' and 'active' status, and transaction_status can be 'trialing' or 'succeeded'
            $validStatuses = ['active', 'trialing'];
            $validTransactionStatuses = ['succeeded', 'trialing'];

            if (!$subscription->transaction_id ||
                !in_array($subscription->status, $validStatuses) ||
                !in_array($subscription->transaction_status, $validTransactionStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You need an active subscription to create albums.'
                ], 403);
            }

            // Check if trial has ended for trialing subscriptions
            if ($subscription->status === 'trialing' && $subscription->trial_ends_at && now()->greaterThan($subscription->trial_ends_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your trial period has ended. Please upgrade to continue.'
                ], 403);
            }

            if (!$driveAccount->google_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Drive access token not found.'
                ], 401);
            }

            // Set user's access token
            $this->gClient->setAccessToken([
                'access_token'  => $driveAccount->google_token,
                'refresh_token' => $driveAccount->google_refresh_token,
                'expires_in'    => $driveAccount->google_token_expires_in,
                'created'       => time(),
            ]);

            // If expired, refresh
            if ($this->gClient->isAccessTokenExpired()) {
                try {
                    $newToken = $this->gClient->fetchAccessTokenWithRefreshToken($driveAccount->google_refresh_token);

                    if (isset($newToken['error'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to refresh token. Please reconnect Google Drive.',
                            'error'   => $newToken['error']
                        ], 401);
                    }

                    // Merge old + new token data
                    $updatedToken = array_merge([
                        'access_token'  => $driveAccount->google_token,
                        'refresh_token' => $driveAccount->google_refresh_token,
                        'expires_in'    => $driveAccount->google_token_expires_in,
                        'created'       => time(),
                    ], $newToken);

                    // Update the json_token field as well
                    $driveAccount->update([
                        'google_token'          => $updatedToken['access_token'],
                        'google_refresh_token'  => $updatedToken['refresh_token'] ?? $driveAccount->google_refresh_token,
                        'google_token_expires_in' => $updatedToken['expires_in'],
                        'google_token_json'     => json_encode($updatedToken),
                        'json_token'            => json_encode($updatedToken), // Add this line to fix token issues
                    ]);

                    $this->gClient->setAccessToken($updatedToken);
                } catch (\Exception $e) {
                    Log::error('Token refresh error: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to refresh Google Drive token. Please reconnect your account.',
                        'error'   => $e->getMessage()
                    ], 401);
                }
            }

            $service = new Google_Service_Drive($this->gClient);

            // Create Google Drive folder
            $folderMetadata = new Google_Service_Drive_DriveFile([
                'name' => $request->input('event_title'),
                'mimeType' => 'application/vnd.google-apps.folder',
            ]);

            $createdFolder = $service->files->create($folderMetadata, ['fields' => 'id,name']);
            $folderId = $createdFolder->id;

            // Make folder public (optional)
            $this->makeFolderPublic($folderId);

            // $folderId = 'asd12dd12ds122s12';
            // Save Album in a transaction
            $album = DB::transaction(function () use ($user, $request, $folderId) {
                return Album::create([
                    'user_id' => $user->id,
                    'event_title' => $request->input('event_title') ?? null,
                    'event_type' => $request->input('event_type') ?? null,
                    'location' => $request->input('location') ?? null,
                    'event_description' => $request->input('event_description') ?? null,
                    // 'google_drive_folder_name' => $request->input('google_drive_folder_name')  ?? null,
                    'google_drive_folder_id' => $folderId  ?? null,
                    'event_date' => $request->input('event_date') ?? null,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Album created successfully',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Album Create Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create album ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function makeFolderPublic($folderId)
    {
        $service = new Google_Service_Drive($this->gClient);

        $permission = new Google_Service_Drive_Permission([
            'type' => 'anyone',
            'role' => 'writer', // 'reader' if you want read-only
        ]);

        try {
            $service->permissions->create($folderId, $permission);
            return true;
        } catch (\Exception $e) {
            Log::error('Make Folder Public Error: ' . $e->getMessage());
            return false;
        }
    }

    public function upload(Request $request)
    {
        try {

            // Validate uploaded files
            $request->validate([
                'uploaded_files.*' => 'required|file|max:50240', // max 10MB per file
            ]);

            // Check if folder exists
            $folder = Album::where('google_drive_folder_id', $request->folder_id)->first();
            if (!$folder) {
                return response()->json([
                    'success' => false,
                    'message' => 'This folder does not exist on our server.'
                ], 404);
            }

            // Check if user exists
            $user = User::find($folder->user_id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user does not exist on our server.'
                ], 404);
            }

            // Check if user Drive exists
            $driveAccount = DriveAccount::where('user_id', $folder->user_id)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user Drive does not exist on our server.'
                ], 404);
            }

            // Initialize Google Drive
            // $tokenArray = [
            //     'access_token' => $ablum->google_token,
            //     'refresh_token' => $ablum->google_refresh_token,
            //     'expires_in' => $ablum->google_token_expires_in,
            //     'created' => time(),
            //     'token_type' => 'Bearer',
            // ];
            $token = json_decode($driveAccount->json_token, true);
            $this->gClient->setAccessToken($token);

            $service = new \Google_Service_Drive($this->gClient);

            // Refresh token if expired
            if ($this->gClient->isAccessTokenExpired()) {
                if ($driveAccount->google_refresh_token) {
                    $newToken = $this->gClient->fetchAccessTokenWithRefreshToken($driveAccount->google_refresh_token);
                    $driveAccount->update([
                        'google_token' => $newToken['access_token'],
                        'google_token_expires_in' => $newToken['expires_in'],
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access token expired and no refresh token available.'
                    ], 401);
                }
            }

            // Upload each file
            $uploadedFiles = [];

            $files = $request->file('uploaded_files');

            // Ensure we always have an array
            if (!is_array($files)) {
                $files = $files ? [$files] : [];
            }

            foreach ($files as $file) {
                try {
                    $mimeType = $file->getClientMimeType() ?? mime_content_type($file->getRealPath());

                    $fileMetadata = new \Google_Service_Drive_DriveFile([
                        'name' => $file->getClientOriginalName(),
                        'parents' => [$request->folder_id],
                    ]);

                    $uploaded = $service->files->create(
                        $fileMetadata,
                        [
                            'data' => file_get_contents($file->getRealPath()),
                            'mimeType' => $mimeType,
                            'uploadType' => 'multipart',
                            'fields' => 'id, name, webViewLink'
                        ]
                    );

                    $uploadedFiles[] = [
                        'id'   => $uploaded->id,
                        'name' => $uploaded->name,
                        'link' => $uploaded->webViewLink,
                    ];
                } catch (\Exception $e) {
                    \Log::error('Google Drive upload failed: ' . $e->getMessage());
                    dump('Error uploading ' . $file->getClientOriginalName() . ' : ' . $e->getMessage());
                }
            }

            $about = $service->about->get(['fields' => 'storageQuota']);
            $storage = $about->getStorageQuota();
            $totalStorageBytes = $storage->getLimit();
            $usedStorageBytes  = $storage->getUsage();

            // Convert bytes to GB
            $totalStorageGB = $totalStorageBytes / 1024 / 1024 / 1024;
            $usedStorageGB  = $usedStorageBytes / 1024 / 1024 / 1024;

            // Optional: round to 2 decimal places
            $totalStorageGB = round($totalStorageGB, 2);
            $usedStorageGB  = round($usedStorageGB, 2);

            $driveAccount->drive_storage = $totalStorageGB;
            $driveAccount->used_storage = $usedStorageGB;
            $driveAccount->save();


            $files = $service->files->listFiles([
                'q' => "'$request->folder_id' in parents and trashed = false",
                'fields' => 'files(id, name, webViewLink, webContentLink)',
            ]);

            $totalFilesInFolder = count($files->getFiles());

            $folder->total_files = $totalFilesInFolder;
            $folder->save();

            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
                'files' => $uploadedFiles,
                'total_files' => $folder->total_files ?? '',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function get_file(Request $request)
    {
        try {
            $folderId = $request->input('folder_id');

            $folder = Album::where('google_drive_folder_id', $folderId)->first();
            if (!$folder) {
                return response()->json([
                    'success' => false,
                    'message' => 'This folder does not exist on our server.'
                ], 404);
            }

            // ✅ Check if user exists
            $user = User::find($folder->user_id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user does not exist on our server.'
                ], 404);
            }

            // ✅ Check if Drive account exists
            $driveAccount = DriveAccount::where('user_id', $folder->user_id)->first();
            if (!$driveAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user Drive does not exist on our server.'
                ], 404);
            }

            // ✅ Load token
            $token = json_decode($driveAccount->json_token, true);
            $this->gClient->setAccessToken($token);

            // ✅ Refresh if expired
            if ($this->gClient->isAccessTokenExpired()) {
                if ($driveAccount->google_refresh_token) {
                    $newToken = $this->gClient->fetchAccessTokenWithRefreshToken($driveAccount->google_refresh_token);

                    $newToken['refresh_token'] = $driveAccount->google_refresh_token; // keep refresh
                    $newToken['created'] = time();

                    // Save new token
                    $driveAccount->update([
                        'json_token'               => json_encode($newToken),
                        'google_token'             => $newToken['access_token'],
                        'google_refresh_token'     => $newToken['refresh_token'],
                        'google_token_expires_in'  => $newToken['expires_in'],
                    ]);

                    $this->gClient->setAccessToken($newToken);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access token expired and no refresh token available.'
                    ], 401);
                }
            }

            // ✅ Call Drive API
            $service = new \Google_Service_Drive($this->gClient);

            $files = $service->files->listFiles([
                'q' => "'$folderId' in parents and trashed = false",
                'fields' => 'files(id, name, webViewLink, webContentLink)',
            ]);

            $totalFilesInFolder = count($files->getFiles());

            $folder->total_files = $totalFilesInFolder;
            $folder->save();


            $links = [];
            foreach ($files->getFiles() as $file) {
                $links[] = [
                    'id'   => $file->getId(),
                    'name' => $file->getName(),
                    'view' => $file->getWebViewLink(),
                    'download' => $file->getWebContentLink(),
                ];
            }

            $about = $service->about->get(['fields' => 'storageQuota']);
            $storage = $about->getStorageQuota();
            $totalStorageBytes =  $storage->getLimit();
            $usedStorageBytes =  $storage->getUsage();

            // Convert bytes to GB
            $totalStorageGB = $totalStorageBytes / 1024 / 1024 / 1024;
            $usedStorageGB  = $usedStorageBytes / 1024 / 1024 / 1024;


            $totalStorageGB = round($totalStorageGB, 2);
            $usedStorageGB  = round($usedStorageGB, 2);

            $driveAccount->drive_storage = $totalStorageGB;
            $driveAccount->used_storage = $usedStorageGB;
            $driveAccount->save();


            return response()->json([
                'success' => true,
                'files'   => $links,
                'files_all'   => $files->getFiles(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching files.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function save_image(Request $request)
    {
        // Validate the request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'folder' => 'required|string',
        ]);

        $image = $request->file('image');
        $folder = trim($request->input('folder'));

        $folder = trim($request->input('folder'));
        $filename = Str::random(20) . '.' . $image->getClientOriginalExtension();

        // Use 'public' disk
        $path = $image->storeAs($folder, $filename, 'public');

        // Public URL
        $publicPath = Storage::url($path);

        $fullUrl = url($publicPath);

        return response()->json([
            'success' => true,
            'path' => $fullUrl,
        ]);
    }
}
