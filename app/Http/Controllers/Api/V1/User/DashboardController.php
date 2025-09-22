<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Resources\AlbumResource;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        try {
            $user = Auth::id();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // 1. Total albums (all time)
            $totalAlbums = Album::where('user_id', $user)->count();

            // 2. Total files
            $totalFiles = Album::where('user_id', $user)->sum('total_files');

            // 3. Total guests
            $totalGuest = Album::where('user_id', $user)->sum('total_guests');

            // 4. Total albums created this month
            $totalAlbumsThisMonth = Album::where('user_id', $user)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            // 5. Recent events (latest 3 albums)
            $recentEvents = Album::where('user_id', $user)->latest()->take(3)->get();

            return response()->json([
                'success' => true,
                'total_albums' => $totalAlbums,
                'total_file' => $totalFiles,
                'total_guest' => $totalGuest,
                'total_albums_this_month' => $totalAlbumsThisMonth,
                'recent_events' => AlbumResource::collection($recentEvents),
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.' . $e->getMessage()
            ], 500);
        }
    }

    public function profile()
    {
        try {

            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $plans = SubscriptionPlan::where('is_active',true)->latest()->get();
            $subscription = UserSubscription::where('user_id',$user->id)->latest()->first();

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'location' => $user->location,
                    'bio' => $user->bio,
                ],
                'notification' => [
                    'emailNotifications' => $user->email_notifications,
                    'eventReminders' => $user->event_reminders,
                ],
                'plans' => $plans,
                'subscription' => $subscription,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $data = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'phone' => 'nullable|string|max:20',
                'location' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:1000',
                'email_notifications' => 'nullable|boolean',
                'event_reminders' => 'nullable|boolean',
            ]);

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'location' => $user->location,
                    'bio' => $user->bio,
                ],
                'notification' => [
                    'emailNotifications' => $user->email_notifications,
                    'eventReminders' => $user->event_reminders,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile. ' . $e->getMessage(),
            ], 500);
        }
    }


    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Validate request
            $data = $request->validate([
                'currentPassword' => 'required|string|min:6',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            // Check current password
            if (!Hash::check($data['currentPassword'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 422);
            }

            // Update password
            $user->password = Hash::make($data['new_password']);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password. ' . $e->getMessage(),
            ], 500);
        }
    }



    public function deleteAccount(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            // Delete user account
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Your account has been deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account. ' . $e->getMessage(),
            ], 500);
        }
    }
}
