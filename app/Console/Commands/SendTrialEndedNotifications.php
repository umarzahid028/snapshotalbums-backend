<?php

namespace App\Console\Commands;

use App\Mail\TrialEndedMail;
use App\Models\UserSubscription;
use App\Models\Album;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTrialEndedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-trial-ended-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification emails for trials that ended today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to send trial ended notification emails...');

        // Get subscriptions where trial ended today or yesterday
        $today = Carbon::today()->endOfDay();
        $yesterday = Carbon::yesterday()->startOfDay();

        $endedTrials = UserSubscription::whereBetween('trial_ends_at', [$yesterday, $today])
            ->where('status', 'trialing')
            ->with(['user', 'plan'])
            ->get();

        $emailsSent = 0;

        foreach ($endedTrials as $subscription) {
            try {
                if ($subscription->user && $subscription->user->email && $subscription->user->email_notifications) {
                    // Get user statistics
                    $eventsCreated = Album::where('user_id', $subscription->user_id)->count();
                    $photosCollected = Album::where('user_id', $subscription->user_id)->sum('total_files');

                    Mail::to($subscription->user->email)->send(
                        new TrialEndedMail($subscription, $eventsCreated, $photosCollected)
                    );

                    $emailsSent++;
                    $this->info("Sent trial ended notification to: {$subscription->user->email}");
                    Log::info("Sent trial ended notification for subscription ID: {$subscription->id}");

                    // Update subscription status to expired
                    $subscription->update(['status' => 'expired']);
                }
            } catch (\Exception $e) {
                $this->error("Failed to send email for subscription ID: {$subscription->id}");
                Log::error("Failed to send trial ended notification: " . $e->getMessage());
            }
        }

        $this->info("Completed! Sent {$emailsSent} trial ended notification emails.");
        Log::info("SendTrialEndedNotifications: Sent {$emailsSent} emails");

        return 0;
    }
}
