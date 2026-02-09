<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class UserController extends Controller
{
    // ✅ List all users
    public function index()
    {
        try {
            $users = User::with(['activeSubscription.plan'])->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ Create new user
    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'status' => 'nullable|boolean',
            ]);

            // Hash the password
            $validated['password'] = bcrypt($validated['password']);

            // Set default status if not provided
            if (!isset($validated['status'])) {
                $validated['status'] = true; // default to active (true)
            }

            $user = User::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }


    // ✅ Show single user by ID
    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ Update user
    public function update(Request $request, $id)
    {
        // Find the user
        $user = User::findOrFail($id);

        // Validation rules
        $rules = [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'nullable|boolean',
        ];

        $validated = $request->validate($rules);

        // Update fields if provided
        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        // Update password only if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Update status (boolean value, no conversion needed)
        if (isset($validated['status'])) {
            $user->status = $validated['status'];
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    // ✅ Delete user
    public function destroy($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ Assign subscription to user
    public function assignSubscription(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            $validated = $request->validate([
                'plan_id' => 'required|exists:subscription_plans,id',
                'price' => 'nullable|numeric|min:0',
                'duration_days' => 'nullable|integer|min:1',
                'ends_at' => 'nullable|date',
            ]);

            $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

            // Cancel any existing active subscriptions
            UserSubscription::where('user_id', $userId)
                ->where('status', true)
                ->update(['status' => false]);

            // Determine end date
            $endsAt = null;
            if (isset($validated['ends_at'])) {
                $endsAt = Carbon::parse($validated['ends_at']);
            } elseif (isset($validated['duration_days'])) {
                $endsAt = Carbon::now()->addDays($validated['duration_days']);
            } else {
                $endsAt = Carbon::now()->addDays($plan->duration_days);
            }

            // Create new subscription
            $subscription = UserSubscription::create([
                'user_id' => $userId,
                'plan_id' => $plan->id,
                'plan_price' => $validated['price'] ?? $plan->price,
                'plan_duration' => $validated['duration_days'] ?? $plan->duration_days,
                'plan_no_of_ablums' => $plan->no_of_ablums,
                'transaction_status' => 'admin_assigned',
                'status' => true,
                'ends_at' => $endsAt,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription assigned successfully',
                'data' => $subscription->load('plan')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ Get user's subscription
    public function getUserSubscription($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $subscription = $user->activeSubscription()->with('plan')->first();

            return response()->json([
                'success' => true,
                'data' => $subscription
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }
}
