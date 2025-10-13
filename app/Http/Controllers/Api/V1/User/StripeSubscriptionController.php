<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Http\Resources\UserSubscriptionResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Charge;
use Illuminate\Support\Facades\Auth;

class StripeSubscriptionController extends Controller
{

    public function subscribe(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'userId' => 'required|exists:users,id',
            'stripeToken' => 'required|string',
            'card_last_four' => 'required|string',
            'card_exp_month' => 'required|string',
            'card_exp_year' => 'required|string',
        ]);

        try {
            $stripeSecret = env('STRIPE_SECRET');

            if (!$stripeSecret) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe is not configured properly. Please contact support.'
                ], 500);
            }

            \Stripe\Stripe::setApiKey($stripeSecret);

            $user = User::find($request->userId);

            $plan = SubscriptionPlan::where('id', $request->plan_id)
                ->where('is_active', true)
                ->first();

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected plan does not exist or is inactive.'
                ], 404);
            }

            // Retrieve or create Stripe customer
            $customer = null;
            if ($user->stripe_customer_id) {
                try {
                    $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);
                } catch (\Exception $e) {
                    $customer = null;
                }
            }

            if (!$customer) {
                $customer = \Stripe\Customer::create([
                    'name' => $user->name,
                    'email' => $user->email,
                ]);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }

            // Attach the PaymentMethod to the customer
            try {
                Log::info('Attempting to retrieve PaymentMethod', [
                    'payment_method_id' => $request->stripeToken,
                    'stripe_account' => substr($stripeSecret, 0, 20) . '...' // Log partial key for debugging
                ]);

                $paymentMethod = \Stripe\PaymentMethod::retrieve($request->stripeToken);

                Log::info('PaymentMethod retrieved successfully', [
                    'payment_method_id' => $paymentMethod->id,
                    'customer' => $paymentMethod->customer
                ]);

                // Check if payment method is already attached to another customer
                if ($paymentMethod->customer && $paymentMethod->customer !== $customer->id) {
                    Log::warning('PaymentMethod already attached to different customer', [
                        'payment_method' => $paymentMethod->id,
                        'current_customer' => $paymentMethod->customer,
                        'new_customer' => $customer->id
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'This payment method is already attached to another customer.'
                    ], 400);
                }

                // Attach payment method to customer if not already attached
                if (!$paymentMethod->customer) {
                    $paymentMethod->attach(['customer' => $customer->id]);
                    Log::info('PaymentMethod attached to customer', [
                        'payment_method' => $paymentMethod->id,
                        'customer' => $customer->id
                    ]);
                }

                // Set as default payment method for invoices
                \Stripe\Customer::update($customer->id, [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethod->id,
                    ],
                ]);

                Log::info('PaymentMethod set as default for customer', [
                    'customer' => $customer->id,
                    'payment_method' => $paymentMethod->id
                ]);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // PaymentMethod doesn't exist or is invalid
                Log::error('Invalid PaymentMethod', [
                    'payment_method_id' => $request->stripeToken,
                    'error' => $e->getMessage(),
                    'stripe_error_code' => $e->getStripeCode()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'The payment method is invalid or was created with a different Stripe account. Please refresh the page and try again with a new card.',
                    'error' => $e->getMessage(),
                    'hint' => 'This usually happens when switching between different Stripe API keys. Please clear your browser cache and try again.'
                ], 400);
            }


            // Create Stripe Product
            $product = \Stripe\Product::create([
                'name' => ucfirst($plan->name) . ' Plan',
                'description' => 'Snapshot Albums ' . ucfirst($plan->name) . ' Subscription',
            ]);

            // Check for existing subscription
            $UserSubscription = UserSubscription::where('user_id', $user->id)->latest()->first();

            if ($UserSubscription && $UserSubscription->transaction_id) {
                try {
                    $existingSubscription = \Stripe\Subscription::retrieve($UserSubscription->transaction_id);
                    if ($existingSubscription->status === 'active' || $existingSubscription->status === 'trialing') {
                        // Check if user is trying to subscribe to the same plan
                        if ($UserSubscription->plan_id == $request->plan_id) {
                            return response()->json(['message' => 'You already have an active subscription to this plan'], 400);
                        }

                        // User is upgrading/downgrading - cancel the old subscription and create new one
                        Log::info('User upgrading subscription', [
                            'user_id' => $user->id,
                            'old_plan' => $UserSubscription->plan_id,
                            'new_plan' => $request->plan_id
                        ]);

                        // Cancel the old subscription immediately
                        $existingSubscription->delete();

                        // Update the old subscription record in DB
                        $UserSubscription->update([
                            'status' => false,
                            'ends_at' => now(),
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error checking existing subscription: ' . $e->getMessage());
                    $UserSubscription->transaction_id = null;
                    $UserSubscription->save();
                }
            }

            // Create Stripe Price
            $price = \Stripe\Price::create([
                'product' => $product->id,
                'unit_amount' => $plan->price * 100,
                'currency' => 'usd',
                'recurring' => [
                    'interval' => 'month',
                ],
            ]);

            // Create Stripe Subscription
            $stripeSubscription = \Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [[
                    'price' => $price->id,
                ]],
                'trial_period_days' => 7,
                // 'payment_behavior' => 'default_incomplete',
                // 'payment_settings' => [
                //     'default_payment_method' => $request->stripeToken,
                // ],
               'expand' => ['latest_invoice.payment_intent'],
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_name' => $plan->name,
                ],
            ]);

            // Save subscription in DB
            $userSubscription = DB::transaction(function () use ($user, $plan, $request, $stripeSubscription) {
                // Convert Stripe status string to boolean (true for active/trialing, false otherwise)
                $isActive = in_array($stripeSubscription->status, ['active', 'trialing']);

                return UserSubscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'plan_price' => $plan->price ?? '',
                    'plan_duration' => $plan->duration_days ?? '',
                    'plan_no_of_ablums' => $plan->no_of_ablums ?? '',
                    'transaction_id' => $stripeSubscription->id ?? '',
                    'transaction_status' => $stripeSubscription->status ?? '',
                    'status' => $isActive, // boolean: true for active/trialing, false otherwise
                    'trial_ends_at' => now()->addDays(7),
                    'ends_at' => now()->addDays($plan->duration_days),
                    'payment_token' => $request->stripeToken ?? '',
                    'card_last_four' => $request->card_last_four ?? '',
                    'card_exp_month' => $request->card_exp_month ?? '',
                    'card_exp_year' => $request->card_exp_year ?? '',
                ]);
            });

            $userSubscription->load('plan');

            return response()->json([
                'success' => true,
                'message' => 'Subscribed successfully',
                'data' => new UserSubscriptionResource($userSubscription),
            ], 201);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stripe error'.$e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('User Subscribe Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe'.$e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // public function subscribe(Request $request)
    // {
    //     $user = Auth::user();

    //     if (!$user) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'User not authenticated'
    //         ], 401);
    //     }

    //     $request->validate([
    //         'plan_id' => 'required|exists:subscription_plans,id',
    //         'userId' => 'required|exists:Users,id',
    //         'stripeToken' => 'required|string',
    //         'card_last_four' => 'required|string',
    //         'card_exp_month' => 'required|string',
    //         'card_exp_year' => 'required|string',
    //     ]);

    //     try {
    //         \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    //         $user = User::find($request->userId);

    //         $plan = SubscriptionPlan::where('id', $request->plan_id)
    //             ->where('is_active', true)
    //             ->first();

    //         if (!$plan) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'The selected plan does not exist or is inactive.'
    //             ], 404);
    //         }

    //         $customer = null;
    //         if ($user->stripe_customer_id) {
    //             try {
    //                 $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);
    //             } catch (\Exception $e) {
    //                 $customer = null;
    //             }
    //         }

    //         if (!$customer) {
    //             $customer = \Stripe\Customer::create([
    //                 'name' => $user->name,
    //                 'email' => $user->email,
    //                 'source' => $request->stripeToken,
    //             ]);
    //             $user->stripe_customer_id = $customer->id;
    //             $user->save();
    //         }

    //         $product = \Stripe\Product::create([
    //             'name' => ucfirst($plan->name) . ' Plan',
    //             'description' => 'Snapshot Albums ' . ucfirst($plan->name) . ' Subscription',
    //         ]);

    //         $UserSubscription = UserSubscription::where('user_id', $user->id)->latest()->first();

    //         if ($UserSubscription && $UserSubscription->transaction_id) {
    //             try {
    //                 $existingSubscription = \Stripe\Subscription::retrieve($UserSubscription->transaction_id);
    //                 if ($existingSubscription->status === 'active' || $existingSubscription->status === 'trialing') {
    //                     return response()->json(['error' => 'User already has an active subscription'], 400);
    //                 }
    //             } catch (\Exception $e) {
    //                 // Subscription doesn't exist in Stripe, clear the local reference
    //                 $user->transaction_id = null;
    //                 $user->save();
    //             }
    //         }

    //         $price = \Stripe\Price::create([
    //             'product' => $product->id,
    //             'unit_amount' => $plan->price * 100,
    //             'currency' => 'usd',
    //             'recurring' => [
    //                 'interval' => 'month',
    //             ],
    //         ]);

    //         $stripeSubscription = \Stripe\Subscription::create([
    //             'customer' => $customer->id,
    //             'items' => [[
    //                 'price' => $price->id,
    //             ]],
    //             'trial_period_days' => 7, // 7-day trial
    //             'payment_behavior' => 'default_incomplete', // Require payment method upfront
    //             'payment_settings' => [
    //                 'save_default_payment_method' => 'on_subscription',
    //             ],
    //             'expand' => ['latest_invoice.payment_intent'],
    //             'metadata' => [
    //                 'user_id' => $user->id,
    //                 'plan_name' => $plan->name,
    //             ],
    //         ]);


    //         $userSubscription = DB::transaction(function () use ($user, $plan, $request, $stripeSubscription) {
    //             return UserSubscription::create([
    //                 'user_id' => $user->id,
    //                 'plan_id' => $plan->id,
    //                 'plan_price' => $plan->price ?? '',
    //                 'plan_duration' => $plan->duration_days ?? '',
    //                 'plan_no_of_albums' => $plan->no_of_albums ?? '',
    //                 'transaction_id'    => $stripeSubscription->id ?? '',
    //                 'transaction_status' => $stripeSubscription->status ?? '',
    //                 'status' => $stripeSubscription->status == 'succeeded' ? 'active' : 'trialing',
    //                 'trial_ends_at' => now()->addDays(7) ?? '',
    //                 'ends_at' => now()->addDays($plan->duration_days) ?? '',
    //                 'payment_token' => $request->stripeToken ?? '',
    //                 'card_last_four' => $request->card_last_four ?? '',
    //                 'card_exp_month' => $request->card_exp_month ?? '',
    //                 'card_exp_year' => $request->card_exp_year ?? '',
    //             ]);
    //         });

    //         $userSubscription->load('plan');

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Subscribed successfully',
    //             'data' => new UserSubscriptionResource($userSubscription),
    //         ], 201);
    //     } catch (\Stripe\Exception\ApiErrorException $e) {
    //         Log::error('Stripe API Error: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Stripe error',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     } catch (\Exception $e) {
    //         Log::error('User Subscribe Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to subscribe',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // Cancel subscription
    public function cancel(Request $request)
    {
        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $user = auth()->user();

            $UserSubscription = UserSubscription::where('user_id', $user->id)->latest()->first();

            if (!$UserSubscription || !$UserSubscription->transaction_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active subscription found.'
                ], 404);
            }

            // Cancel the subscription at the end of the current period
            $subscription = \Stripe\Subscription::retrieve($UserSubscription->transaction_id);
            $subscription->cancel_at_period_end = true;
            $subscription->save();

            // Update subscription status in DB (false = canceled/inactive)
            $UserSubscription->update([
                'status'   => false,
                'ends_at'  => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end),
            ]);

            $cancelDate = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end);

            return response()->json([
                'success' => true,
                'message' => 'Your subscription has been cancelled. You will continue to have access until ' . $cancelDate->format('M d, Y') . '. You will not be charged again.',
                'data' => [
                    'subscription_id' => $UserSubscription->transaction_id,
                    'status'          => 'canceled',
                    'ends_at'         => $cancelDate->toDateTimeString(),
                ]
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription. Please try again or contact support.' . $e->getMessage(),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    // Check subscription status
    public function status(Request $request)
    {
        try {
            $subscription = UserSubscription::where('user_id', $request->user()->id)
                ->latest()
                ->with('plan')
                ->first();

            if (!$subscription) {
                return response()->json(['message' => 'No subscription found'], 404);
            }

            return new UserSubscriptionResource($subscription);
        } catch (\Exception $e) {
            Log::error('User Subscription Status Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Failed to fetch subscription status', 'error' => $e->getMessage()], 500);
        }
    }

    // Fix/refresh subscription data from plan
    public function refreshSubscription(Request $request)
    {
        try {
            $user = Auth::user();

            $subscription = UserSubscription::where('user_id', $user->id)
                ->latest()
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No subscription found'
                ], 404);
            }

            // Get the plan details
            $plan = SubscriptionPlan::find($subscription->plan_id);

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found'
                ], 404);
            }

            // Update subscription with correct plan details
            $subscription->update([
                'plan_no_of_ablums' => $plan->no_of_ablums,
                'plan_price' => $plan->price,
                'plan_duration' => $plan->duration_days,
            ]);

            $subscription->load('plan');

            return response()->json([
                'success' => true,
                'message' => 'Subscription refreshed successfully',
                'data' => new UserSubscriptionResource($subscription)
            ], 200);
        } catch (\Exception $e) {
            Log::error('Refresh Subscription Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
