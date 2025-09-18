<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Http\Resources\SubscriptionPlanResource;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        try {
            $plans = SubscriptionPlan::where('is_active',true)->latest()->get();
            return SubscriptionPlanResource::collection($plans);
        } catch (\Exception $e) {
            Log::error('Subscription Plan Index Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to fetch plans', 'error' => $e->getMessage()], 500);
        }
    }
}
