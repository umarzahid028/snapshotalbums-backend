<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            'event_time' => 'nullable|date_format:Y-m-d H:i:s',
            'location' => 'nullable|string|max:255',
            'event_description' => 'nullable|string',
            'max_photos_per_guest' => 'nullable|integer|min:1',
            'custom_welcome_message' => 'nullable|string',
            'privacy_level' => 'nullable|in:private,public',
            'allow_guest_uploads' => 'nullable|boolean',
            'google_drive_folder_name' => 'required|string|max:255',
            'event_date' => 'required|date',
        ]);

        try {
            $user = Auth::user();

            $checkAlbumCount = Album::where('user_id', $user->id)->count();
            $subscription = UserSubscription::where('user_id', $user->id)->latest()->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No subscription found.'
                ], 404);
            }

            if ($subscription && $checkAlbumCount > $subscription->plan_no_of_albums) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the maximum number of albums allowed for your plan.'
                ], 403);
            }

            // 🔹 Check active paid subscription
            if (
                $subscription->transaction_id &&
                $subscription->status === 'active' &&
                $subscription->transaction_status === 'succeeded'
            ) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your subscription is active.',
                    'data' => $subscription
                ], 200);
            }

            // 🔹 Otherwise check trial
            if ($subscription->trial_ends_at && now()->greaterThan($subscription->trial_ends_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your trial period has ended.'
                ], 403);
            }

            if (!$user->google_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Drive access token not found.'
                ], 401);
            }

            // Set user's access token
            $this->gClient->setAccessToken([
                'access_token'  => $user->google_token,
                'refresh_token' => $user->google_refresh_token,
                'expires_in'    => $user->google_token_expires_in,
                'created'       => time(),
            ]);

            // If expired, refresh
            if ($this->gClient->isAccessTokenExpired()) {
                $newToken = $this->gClient->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

                if (isset($newToken['error'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to refresh token. Please reconnect Google Drive.',
                        'error'   => $newToken['error']
                    ], 401);
                }

                // Merge old + new token data
                $updatedToken = array_merge([
                    'access_token'  => $user->google_token,
                    'refresh_token' => $user->google_refresh_token,
                    'expires_in'    => $user->google_token_expires_in,
                    'created'       => time(),
                ], $newToken);

                $user->update([
                    'google_token'          => $updatedToken['access_token'],
                    'google_refresh_token'  => $updatedToken['refresh_token'] ?? $user->google_refresh_token,
                    'google_token_expires_in' => $updatedToken['expires_in'],
                    'google_token_json'     => json_encode($updatedToken),
                ]);

                $this->gClient->setAccessToken($updatedToken);
            }

            $service = new Google_Service_Drive($this->gClient);

            // Create Google Drive folder
            $folderMetadata = new Google_Service_Drive_DriveFile([
                'name' => $request->input('google_drive_folder_name'),
                'mimeType' => 'application/vnd.google-apps.folder',
            ]);

            $createdFolder = $service->files->create($folderMetadata, ['fields' => 'id,name']);
            $folderId = $createdFolder->id;

            // Make folder public (optional)
            $this->makeFolderPublic($folderId);

            $folderId = 'asd12dd12ds122s12';
            // Save Album in a transaction
            $album = DB::transaction(function () use ($user, $request, $folderId) {
                return Album::create([
                    'user_id' => $user->id,
                    'event_title' => $request->input('event_title') ?? null,
                    'event_type' => $request->input('event_type') ?? null,
                    'event_time' => $request->input('event_time') ?? null,
                    'location' => $request->input('location') ?? null,
                    'event_description' => $request->input('event_description') ?? null,
                    'max_photos_per_guest' => $request->input('max_photos_per_guest') ?? null,
                    'custom_welcome_message' => $request->input('custom_welcome_message') ?? null,
                    'privacy_level' => $request->input('privacy_level', 'private'), // default to private
                    'allow_guest_uploads' => $request->input('allow_guest_uploads', true)  ?? null, // default true
                    'google_drive_folder_name' => $request->input('google_drive_folder_name')  ?? null,
                    'google_drive_folder_id' => $folderId  ?? null,
                    'event_date' => $request->input('event_date') ?? null,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Album created successfully',
                'data' => $album
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

            // Initialize Google Drive
            $this->gClient->setAccessToken([
                'access_token' => $user->google_token,
                'refresh_token' => $user->google_refresh_token,
                'expires_in' => $user->google_token_expires_in,
                'created' => time(),
            ]);
            $service = new \Google_Service_Drive($this->gClient);

            // Refresh token if expired
            if ($this->gClient->isAccessTokenExpired()) {
                if ($user->google_refresh_token) {
                    $newToken = $this->gClient->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                    $user->update([
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
                    dump('Processing file: ' . $file->getClientOriginalName());

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


            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
                'files' => $uploadedFiles
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
