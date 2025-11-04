<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DriveAccount;
use App\Models\EmailLog;
use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\HasApiTokens;


class AuthController extends Controller
{
    /**
     * Redirect to Google for authentication.
     */
    public function redirectToGoogle()
    {
        $redirectUrl = Socialite::driver('google')
            ->scopes([
                'openid',
                'email',
                'profile',
                // 'https://www.googleapis.com/auth/drive.file', // Upload & manage user's files
                // or 'https://www.googleapis.com/auth/drive.readonly' for read-only access
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent']) // ensures refresh token
            ->stateless()
            ->redirectUrl(config('services.google.redirect_login'))
            ->redirect()
            ->getTargetUrl();

        return response()->json([
            'url' => $redirectUrl,
        ]);
    }


    // Connect Google
    public function connectGoogleDrive()
    {
        $redirectResponse = Socialite::driver('google')
            ->scopes([
                'openid',
                'email',
                'profile',
                'https://www.googleapis.com/auth/drive.file', // full access to create/manage user files
                // or use 'https://www.googleapis.com/auth/drive.readonly' if you only need read access
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent']) // get refresh token
            ->stateless()
            ->redirectUrl(config('services.google.redirect'))
            ->redirect()
            ->getTargetUrl();

        // $targetUrl = $redirectResponse->getTargetUrl();

        return response()->json([
            'url' => $redirectResponse,
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // Send welcome email immediately
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));

            // Log the sent email
            EmailLog::create([
                'user_id' => $user->id,
                'email_type' => 'welcome',
                'recipient_email' => $user->email,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // Update the user's last welcome email sent timestamp
            $user->update(['last_welcome_email_sent_at' => now()]);
        } catch (\Exception $e) {
            // Log the error but don't fail the registration
            EmailLog::create([
                'user_id' => $user->id,
                'email_type' => 'welcome',
                'recipient_email' => $user->email,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id'   => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
            ],
            'token' => $token
        ], 201);
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id'   => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
            ],
            'token' => $token
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    // Forgot Password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent.'])
            : response()->json(['message' => 'Unable to send reset link.'], 500);
    }

    // Reset Password (optional, if you want full API flow)
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => bcrypt($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successfully.'])
            : response()->json(['message' => 'Failed to reset password.'], 500);
    }

    // Get Authenticated User
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function token(Request $request)
    {
        try {
            $code = $request->input('code');
            $value = $request->input('value');

            if (!$code) {
                return response()->json(['error' => 'No code provided'], 400);
            }

            $redirect = env('GOOGLE_REDIRECT_URI_LOGIN');

            if ($value === 'drive') {
                $redirect = env('GOOGLE_REDIRECT_URI');
            }
            // Exchange code for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect_uri' => $redirect,
                'grant_type' => 'authorization_code',
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Failed to get access token', 'details' => $response->body()], 500);
            }

            $tokenData = $response->json();
            $accessToken = $tokenData['access_token'];

            // Get user info from Google
            $googleUserResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://www.googleapis.com/oauth2/v2/userinfo');

            if ($googleUserResponse->failed()) {
                return response()->json(['error' => 'Failed to fetch Google user info'], 500);
            }

            $googleUser = (object) $googleUserResponse->json(); // convert array to object

            // Check if user exists by Google ID or email
            $user = User::Where('email', $googleUser->email)->orwhere('google_id', $googleUser->id)
                ->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->picture,
                    'google_token' => $accessToken,
                    'google_refresh_token' => $tokenData['refresh_token'] ?? null,
                    'google_token_expires_in' => $tokenData['expires_in'],
                ]);
            } else {
                if ($value == 'login') {
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->picture,
                        'password' => bcrypt(Str::random(16)),
                        'google_token' => $accessToken,
                        'google_refresh_token' => $tokenData['refresh_token'] ?? null,
                        'google_token_expires_in' => $tokenData['expires_in'],
                    ]);

                    // Send welcome email immediately for new Google signup
                    try {
                        Mail::to($user->email)->send(new WelcomeMail($user));

                        // Log the sent email
                        EmailLog::create([
                            'user_id' => $user->id,
                            'email_type' => 'welcome',
                            'recipient_email' => $user->email,
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);

                        // Update the user's last welcome email sent timestamp
                        $user->update(['last_welcome_email_sent_at' => now()]);
                    } catch (\Exception $e) {
                        // Log the error but don't fail the registration
                        EmailLog::create([
                            'user_id' => $user->id,
                            'email_type' => 'welcome',
                            'recipient_email' => $user->email,
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ]);
                    }
                }
            }

            $driveAccount = [];
            if ($value == 'drive' && $user) {
                $jsonToken = $tokenData;
                $jsonToken['created'] = time();
                $driveAccount = DriveAccount::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'drive_email' => $googleUser->email,
                    ],
                    [
                        'drive_name' => $googleUser->name,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->picture,
                        'google_token' => $accessToken,
                        'google_refresh_token' => $tokenData['refresh_token'] ?? null,
                        'google_token_expires_in' => $tokenData['expires_in'],
                        'access_token' => $accessToken,
                        'json_token' => json_encode($jsonToken),
                    ]
                );
            }

            $authToken = $user->createToken('bazzre-auth')->plainTextToken;

            return response()->json([
                'user' => [
                    'id'   => $user->id,
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'avatar' => $user->avatar,
                ],
                'driveAccount' => $driveAccount ? [
                    'drive_name'  => $driveAccount->drive_name,
                    'drive_email' => $driveAccount->drive_email,
                    'avatar'      => $driveAccount->avatar,
                ] : null,
                'bazzreToken' => $authToken,
                'tokenData' => $tokenData,
            ]);

            // return response()->json([
            //     'user' => $user,
            //     'driveAccount' => $driveAccount,
            //     'tokenData' => $tokenData,
            //     'bazzreToken' => $authToken,
            // ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Google login failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function disconnectGoogleDrive(Request $request)
    {
        try {
            $user = Auth::user();

            $driveAccount = DriveAccount::where('user_id', $user->id)->first();

            if (!$driveAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'No connected Google Drive account found.'
                ], 404);
            }

            if ($driveAccount->google_token) {
                $revoke = Http::asForm()->post('https://oauth2.googleapis.com/revoke', [
                    'token' => $driveAccount->google_token,
                ]);

                if ($revoke->failed()) {
                   
                    \Log::warning('Failed to revoke Google Drive token', [
                        'user_id' => $user->id,
                        'response' => $revoke->body()
                    ]);
                }
            }

            $driveAccount->delete();

            return response()->json([
                'success' => true,
                'message' => 'Google Drive disconnected successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to disconnect Google Drive: ' . $e->getMessage(),
            ], 500);
        }
    }
}
