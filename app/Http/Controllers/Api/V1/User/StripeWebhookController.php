<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Carbon\Carbon;



class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    Log::info('Payment succeeded: ' . $paymentIntent->id);
                    break;

                case 'invoice.paid':
                    $invoice = $event->data->object;
                    Log::info('Invoice paid: ' . $invoice->id);

                    // Update subscription in DB
                    $subscription = UserSubscription::where('transaction_id', $invoice->payment_intent)->first();
                    if ($subscription) {
                        $subscription->status = 'succeeded';
                        $subscription->ends_at = Carbon::createFromTimestamp($invoice->period_end);
                        $subscription->save();
                    }
                    break;

                case 'customer.subscription.created':
                    $stripeSub = $event->data->object;
                    Log::info('Subscription created: ' . $stripeSub->id);

                    // Store new subscription
                    UserSubscription::updateOrCreate(
                        ['transaction_id' => $stripeSub->latest_invoice], // Unique identifier
                        [
                            'user_id' => $stripeSub->metadata->user_id ?? null, // optional: pass user_id via metadata
                            'plan_id' => $stripeSub->plan->id ?? null,
                            'plan_price' => $stripeSub->plan->amount ?? 0,
                            'plan_duration' => $stripeSub->plan->interval ?? 'month',
                            'status' => $stripeSub->status ?? 'active',
                            'trial_ends_at' => isset($stripeSub->trial_end) ? Carbon::createFromTimestamp($stripeSub->trial_end) : null,
                            'ends_at' => isset($stripeSub->current_period_end) ? Carbon::createFromTimestamp($stripeSub->current_period_end) : null,
                            'card_last_four' => $stripeSub->default_payment_method->card->last4 ?? null,
                            'card_exp_month' => $stripeSub->default_payment_method->card->exp_month ?? null,
                            'card_exp_year' => $stripeSub->default_payment_method->card->exp_year ?? null,
                            'payment_token' => $stripeSub->id,
                        ]
                    );
                    break;

                default:
                    Log::info('Received unhandled event: ' . $event->type);
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }
}
