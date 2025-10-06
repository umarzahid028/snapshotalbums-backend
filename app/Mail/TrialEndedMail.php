<?php

namespace App\Mail;

use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrialEndedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $eventsCreated;
    public $photosCollected;
    public $availablePlans;

    /**
     * Create a new message instance.
     *
     * @param UserSubscription $subscription
     * @param int $eventsCreated
     * @param int $photosCollected
     */
    public function __construct(UserSubscription $subscription, int $eventsCreated = 0, int $photosCollected = 0)
    {
        $this->subscription = $subscription;
        $this->eventsCreated = $eventsCreated;
        $this->photosCollected = $photosCollected;
        $this->availablePlans = SubscriptionPlan::where('is_active', true)->get();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Trial Period Has Ended - Continue with SnapshotAlbums')
            ->view('emails.trial-ended')
            ->with([
                'subscription' => $this->subscription,
                'eventsCreated' => $this->eventsCreated,
                'photosCollected' => $this->photosCollected,
                'availablePlans' => $this->availablePlans,
            ]);
    }
}
