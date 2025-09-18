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
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

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
            $paymentMethod = \Stripe\PaymentMethod::retrieve($request->stripeToken);
            $paymentMethod->attach(['customer' => $customer->id]);

            // Set as default payment method for invoices
            \Stripe\Customer::update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethod->id,
                ],
            ]);


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
                        return response()->json(['error' => 'User already has an active subscription'], 400);
                    }
                } catch (\Exception $e) {
                    $user->transaction_id = null;
                    $user->save();
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
                return UserSubscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'plan_price' => $plan->price ?? '',
                    'plan_duration' => $plan->duration_days ?? '',
                    'plan_no_of_albums' => $plan->no_of_albums ?? '',
                    'transaction_id' => $stripeSubscription->id ?? '',
                    'transaction_status' => $stripeSubscription->status ?? '',
                    'status' => $stripeSubscription->status, // trialing or active
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
                'message' => 'Stripe error',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('User Subscribe Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe',
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

            // Update subscription status in DB
            $UserSubscription->update([
                'status'   => 'canceled',
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
}
