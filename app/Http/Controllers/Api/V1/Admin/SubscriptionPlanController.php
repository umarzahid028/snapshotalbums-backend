<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
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
            Log::error('Subscription Plan Index Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to fetch plans', 'error' => $e->getMessage()], 500);
        }
    }

    // Show single plan
    public function show($slug)
    {
        try {
            $plan = SubscriptionPlan::where('slug', $slug)->firstOrFail();
            return new SubscriptionPlanResource($plan);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Plan not found'], 404);
        } catch (\Exception $e) {
            Log::error('Subscription Plan Show Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to fetch plan', 'error' => $e->getMessage()], 500);
        }
    }

    // Create plan
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subscription_plans,name',
            'slug' => 'nullable|string|unique:subscription_plans,slug',
            'no_of_ablums' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $plan = DB::transaction(function () use ($request) {
                $slug = $request->slug ?? Str::slug($request->name);
                return SubscriptionPlan::create(array_merge(
                    $request->only('name', 'price', 'duration_days', 'features', 'is_active', 'no_of_ablums'),
                    ['slug' => $slug]
                ));
            });

            return new SubscriptionPlanResource($plan);
        } catch (\Exception $e) {
            Log::error('Subscription Plan Store Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to create plan', 'error' => $e->getMessage()], 500);
        }
    }

    // Update plan
    public function update(Request $request, $slug)
    {
        $plan = SubscriptionPlan::where('slug', $slug)->firstOrFail();

        $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:subscription_plans,name,'.$plan->id,
            'slug' => 'sometimes|nullable|string|unique:subscription_plans,slug,'.$plan->id,
            'no_of_ablums' => 'sometimes|required|numeric|min:1',
            'price' => 'sometimes|required|numeric|min:0',
            'duration_days' => 'sometimes|required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::transaction(function () use ($plan, $request) {
                if ($request->filled('name') && !$request->filled('slug')) {
                    $request->merge(['slug' => Str::slug($request->name)]);
                }
                $plan->update($request->only('name','slug','price','duration_days','features','is_active'));
            });

            return new SubscriptionPlanResource($plan);
        } catch (\Exception $e) {
            Log::error('Subscription Plan Update Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to update plan', 'error' => $e->getMessage()], 500);
        }
    }

    // Delete plan
    public function destroy($slug)
    {
        $plan = SubscriptionPlan::where('slug', $slug)->firstOrFail();

        try {
            DB::transaction(function () use ($plan) {
                $plan->delete();
            });

            return response()->json(['message' => 'Plan deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Subscription Plan Delete Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to delete plan', 'error' => $e->getMessage()], 500);
        }
    }
}
