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
        $startTime = now();

        $this->info("🚀 [CRON START] SendTrialEndedNotifications - " . $startTime->format('Y-m-d H:i:s'));
        Log::info("===== CRON JOB STARTED: SendTrialEndedNotifications =====", [
            'timestamp' => $startTime->toDateTimeString(),
        ]);

        try {
            // Get subscriptions where trial ended today or yesterday
            $today = Carbon::today()->endOfDay();
            $yesterday = Carbon::yesterday()->startOfDay();

            $this->info("📅 Checking for trials that ended between {$yesterday->format('Y-m-d')} and {$today->format('Y-m-d')}");

            $endedTrials = UserSubscription::whereBetween('trial_ends_at', [$yesterday, $today])
                ->where('status', 'trialing')
                ->with(['user', 'plan'])
                ->get();

            $this->info("📋 Found {$endedTrials->count()} ended trials");
            Log::info("Trials found that ended", [
                'count' => $endedTrials->count(),
                'subscriptions' => $endedTrials->pluck('id', 'user.email')->toArray()
            ]);

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

            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            $this->info("✅ Completed! Sent {$emailsSent} trial ended notification emails");
            $this->info("⏱️  Duration: {$duration} seconds");

            Log::info("===== CRON JOB COMPLETED: SendTrialEndedNotifications =====", [
                'emails_sent' => $emailsSent,
                'duration_seconds' => $duration,
                'start_time' => $startTime->toDateTimeString(),
                'end_time' => $endTime->toDateTimeString(),
                'status' => 'success'
            ]);

            return 0;
        } catch (\Exception $e) {
            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            $this->error("❌ Error sending trial ended notifications: " . $e->getMessage());
            Log::error("===== CRON JOB FAILED: SendTrialEndedNotifications =====", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'duration_seconds' => $duration,
                'start_time' => $startTime->toDateTimeString(),
                'end_time' => $endTime->toDateTimeString(),
                'status' => 'failed'
            ]);

            return 1;
        }
    }
}
