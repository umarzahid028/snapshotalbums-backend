<?php

namespace App\Mail;

use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public UserSubscription $subscription;
    public $availablePlans;

    /**
     * Create a new message instance.
     */
    public function __construct(UserSubscription $subscription)
    {
        $this->subscription = $subscription;
        $this->availablePlans = SubscriptionPlan::where('is_active', true)->get();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Free Trial Expires in 1 Day',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.trial-expiring',
            with: [
                'subscription' => $this->subscription,
                'user' => $this->subscription->user,
                'dashboardUrl' => config('app.frontend_url') . '/dashboard',
                'availablePlans' => $this->availablePlans,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
