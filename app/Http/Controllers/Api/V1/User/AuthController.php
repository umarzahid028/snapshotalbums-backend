<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DriveAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

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
            ->redirectUrl(config('services.google.redirect'))
            ->redirect()
            ->getTargetUrl();

        return response()->json([
            'url' => $redirectUrl,
        ]);
    }

    /**
     * Handle Google callback.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Find user by email OR google_id
            $user = User::where('google_id', $googleUser->getId())->orWhere('drive_email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id'              => $googleUser->getId(),
                    'avatar'                 => $googleUser->getAvatar(),
                    'google_token'           => $googleUser->token,
                    'google_refresh_token'   => $googleUser->refreshToken,
                    'google_token_expires_in' => $googleUser->expiresIn,
                ]);
            } else {
                $user = User::create([
                    'name'                   => $googleUser->getName(),
                    'email'                  => $googleUser->getEmail(),
                    'google_id'              => $googleUser->getId(),
                    'avatar'                 => $googleUser->getAvatar(),
                    'password'               => bcrypt(Str::random(16)),
                    'google_token'           => $googleUser->token,
                    'google_refresh_token'   => $googleUser->refreshToken,
                    'google_token_expires_in' => $googleUser->expiresIn,
                ]);
            }

            // Generate Sanctum token
            $token = $user->createToken('api-token')->plainTextToken;

            // Sirf safe data redirect me bhejo
            $safeUser = [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
            ];

            return redirect("http://localhost:5173/google/callback?success=true&token=$token&user=" . urlencode(json_encode($safeUser)));
        } catch (\Exception $e) {
            return redirect("http://localhost:5173/google/callback?success=false&error=" . urlencode($e->getMessage()));
        }
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

    public function handleConnectGoogleDriveCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // $user = Auth::user();

            // if (!$user) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Unauthorized',
            //     ], 401);
            // }

            // Create or update drive account for this user
            $driveAccount = DriveAccount::updateOrCreate(
                [
                    'user_id'     => '9',
                    'drive_email' => $googleUser->getEmail(), 
                ],
                [
                    'drive_name'             => $googleUser->getName(),
                    'google_id'              => $googleUser->getId(),
                    'avatar'                 => $googleUser->getAvatar(),
                    'google_token'           => $googleUser->token,
                    'google_refresh_token'   => $googleUser->refreshToken,
                    'google_token_expires_in' => $googleUser->expiresIn,
                    'access_token'           => $googleUser->token,
                ]
            );

            // return response()->json([
            //     'success' => true,
            //     'message' => 'Google Drive connected successfully',
            //     'data'    => $driveAccount,
            // ]);

            return redirect("http://localhost:5173/google/callback?drive_connected=true&drive_token=$token&user=" . urlencode(json_encode($safeUser)));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google Drive connection failed: ' . $e->getMessage(),
            ], 500);
            return redirect("http://localhost:5173/google/callback?success=false&error=" . urlencode($e->getMessage()));

        }
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

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
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

        return response()->json(['user' => $user, 'token' => $token]);
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
}
