<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Admin Registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $admin = DB::transaction(function () use ($request) {
                return Admin::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
            });

            $token = $admin->createToken('admin-token')->plainTextToken;

            return response()->json([
                'admin' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                ],
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Admin Register Error: ' . $e->getMessage());
            return response()->json(['error' => 'Registration failed'], 500);
        }
    }

    // Admin Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $admin->createToken('admin-token', ['admin'])->plainTextToken;

        return response()->json([
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
            'token' => $token,
        ], 200);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully.'], 200);
    }

    // Forget Password (send reset link)
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:admins,email']);

        try {
            $status = Password::broker('admins')->sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => 'Reset link sent to your email.'], 200);
            } else {
                return response()->json(['message' => 'Failed to send reset link.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Forgot Password Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }


    public function dashboard()
    {
        $totalUsers = User::count();

        $totalActiveSubscriptions = UserSubscription::where('status', 'succeeded')->count();

        $monthlyRevenue = UserSubscription::where('status', 'succeeded')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('plan_price');

        $monthlyTrilaing = UserSubscription::where('status', 'trialing')->orwhere('status', 'active')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('plan_price');

        $planUsage = UserSubscription::select('plan_id')
        ->selectRaw('COUNT(*) as total_users, SUM(plan_price) as total_revenue')
        ->groupBy('plan_id')
        ->with('plan')
        ->get()
        ->map(function ($item) {
            return [
                'plan_id' => $item->plan_id,
                'plan_name' => $item->plan->name ?? 'N/A',
                'total_users' => $item->total_users,
                'total_revenue' => $item->total_revenue,
            ];
        });

        $latestSubscriptions = UserSubscription::with('user', 'plan')
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($subscription) {
            return [
                'id' => $subscription->id ?? '',
                'user_name' => $subscription->user->name ?? 'N/A',
                'plan_name' => $subscription->plan->name ?? 'N/A',
                'status' => $subscription->status,
                'plan_price' => $subscription->plan_price,
                'created_at' => $subscription->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalActiveSubscription' => $totalActiveSubscriptions,
            'totalRevenue' => $monthlyRevenue,
            'totalTrialSubscription' => $monthlyTrilaing,
            'planUsage' => $planUsage,
            'latestSubscriptions' => $latestSubscriptions,
        ], 200);
    }
}
