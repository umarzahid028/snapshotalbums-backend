<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\SubscriptionPlanResource;

class SubscriptionPlanController extends Controller
{
    // List all plans
    public function index()
    {
        try {
            $plans = SubscriptionPlan::latest()->get();
            return SubscriptionPlanResource::collection($plans);
        } catch (\Exception $e) {
            Log::error('Subscription Plan Index Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to fetch plans', 'error' => $e->getMessage()], 500);
        }
    }

    // Show single plan
    public function show($id)
    {
        try {
            $plan = SubscriptionPlan::find($id);
            return new SubscriptionPlanResource($plan);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Plan not found'], 404);
        } catch (\Exception $e) {
            Log::error('Subscription Plan Show Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to fetch plan', 'error' => $e->getMessage()], 500);
        }
    }

    // Create plan
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subscription_plans,name',
            'description' => 'nullable|string|unique:subscription_plans,slug',
            'no_of_ablums' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
        ]);

        try {
            $plan = DB::transaction(function () use ($request) {
                return SubscriptionPlan::create($request->only(
                    'name',
                    'description',
                    'price',
                    'duration_days',
                    'features',
                    'is_active',
                    'no_of_ablums',
                    'is_popular'
                ));
            });


            return new SubscriptionPlanResource($plan);
        } catch (\Exception $e) {
            Log::error('Subscription Plan Store Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to create plan', 'error' => $e->getMessage()], 500);
        }
    }

    // Update plan
    public function update(Request $request, $id)
    {
        $plan = SubscriptionPlan::find($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:subscription_plans,name,' . $plan->id,
            'description' => 'sometimes|nullable|string',
            'no_of_ablums' => 'sometimes|required|numeric|min:1',
            'price' => 'sometimes|required|numeric|min:0',
            'duration_days' => 'sometimes|required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
        ]);

        try {
            DB::transaction(function () use ($plan, $request) {
                $plan->update($request->only('name', 'description', 'price', 'no_of_ablums', 'duration_days', 'features', 'is_active', 'is_popular'));
            });

            return new SubscriptionPlanResource($plan);
        } catch (\Exception $e) {
            Log::error('Subscription Plan Update Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to update plan', 'error' => $e->getMessage()], 500);
        }
    }

    public function update_status(Request $request, $id)
    {
        $plan = SubscriptionPlan::find($id);

        $request->validate([
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::transaction(function () use ($plan, $request) {
                $plan->update($request->only('is_active'));
            });

            return response()->json([
                'id' => $plan->id,
                'is_active' => $plan->is_active,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Subscription Plan Update Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to update plan', 'error' => $e->getMessage()], 500);
        }
    }


    // Delete plan
    public function destroy($id)
    {
        $plan = SubscriptionPlan::find($id);

        try {
            DB::transaction(function () use ($plan) {
                $plan->delete();
            });

            return response()->json(['message' => 'Plan deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Subscription Plan Delete Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to delete plan', 'error' => $e->getMessage()], 500);
        }
    }

    public function bill_Subscription()
    {
        try {
            $subscriptions = UserSubscription::with(['user', 'plan'])->paginate(7);

            $activeSubscriptions = UserSubscription::with(['user', 'plan'])
                ->where('status', 'succeeded')
                ->get();

            $activeCount = $activeSubscriptions->count();
            $totalRevenue = $activeSubscriptions->sum('plan_price');

            // Monthly Recurring Revenue (MRR)
            $mrr = $activeSubscriptions->sum(function ($sub) {
                if ($sub->plan_duration === 'monthly') {
                    return $sub->plan_price;
                } elseif ($sub->plan_duration === 'yearly') {
                    return $sub->plan_price / 12;
                }
                return 0;
            });

            return response()->json([
                'active_subscriptions'      => $activeCount,
                'total_revenue'             => $totalRevenue,
                'monthly_recurring_revenue' => round($mrr, 2),
                'subscriptions'             => $subscriptions,
            ]);
        } catch (\Exception $e) {
            \Log::error('Subscription Plan Index Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'Failed to fetch subscription stats',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
