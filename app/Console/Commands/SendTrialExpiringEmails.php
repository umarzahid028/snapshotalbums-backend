<?php

namespace App\Console\Commands;

use App\Mail\TrialExpiringMail;
use App\Models\UserSubscription;
use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTrialExpiringEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-trial-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send trial expiring notifications 1 day before trial ends';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();

        $this->info("🚀 [CRON START] SendTrialExpiringEmails - " . $startTime->format('Y-m-d H:i:s'));
        Log::info("===== CRON JOB STARTED: SendTrialExpiringEmails =====", [
            'timestamp' => $startTime->toDateTimeString(),
        ]);

        try {
            // Get subscriptions where trial expires in 1 day (24 hours)
            $tomorrow = Carbon::tomorrow()->startOfDay();
            $tomorrowEnd = Carbon::tomorrow()->endOfDay();

            $this->info("📅 Checking for trials expiring between {$tomorrow->format('Y-m-d H:i:s')} and {$tomorrowEnd->format('Y-m-d H:i:s')}");

            $expiringTrials = UserSubscription::whereBetween('trial_ends_at', [$tomorrow, $tomorrowEnd])
                ->where('status', true) // Active subscriptions
                ->with(['user'])
                ->get();

            $this->info("📋 Found {$expiringTrials->count()} trials expiring in 1 day");
            Log::info("Trials found expiring in 1 day", [
                'count' => $expiringTrials->count(),
            ]);

            $emailsSent = 0;
            $emailsFailed = 0;

            foreach ($expiringTrials as $subscription) {
                try {
                    // Check if user exists and has email notifications enabled
                    if ($subscription->user && $subscription->user->email && $subscription->user->email_notifications) {
                        Mail::to($subscription->user->email)->send(
                            new TrialExpiringMail($subscription)
                        );

                        // Log the sent email
                        EmailLog::create([
                            'user_id' => $subscription->user_id,
                            'email_type' => 'trial_expiring',
                            'recipient_email' => $subscription->user->email,
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);

                        $emailsSent++;
                        $this->info("✓ Sent trial expiring notification to: {$subscription->user->email}");
                        Log::info("Sent trial expiring notification for subscription ID: {$subscription->id}", [
                            'user_email' => $subscription->user->email,
                            'trial_ends_at' => $subscription->trial_ends_at,
                        ]);
                    } else {
                        $this->warn("⊘ Skipped subscription {$subscription->id} - user invalid or notifications disabled");
                    }
                } catch (\Exception $e) {
                    $emailsFailed++;
                    $this->error("✗ Failed to send trial expiring notification for subscription ID: {$subscription->id}");
                    Log::error("Failed to send trial expiring notification for subscription ID: {$subscription->id}", [
                        'error' => $e->getMessage(),
                    ]);

                    // Log failed email attempt
                    try {
                        EmailLog::create([
                            'user_id' => $subscription->user_id,
                            'email_type' => 'trial_expiring',
                            'recipient_email' => $subscription->user->email ?? 'unknown',
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ]);
                    } catch (\Exception $logError) {
                        Log::error("Failed to log email error", ['error' => $logError->getMessage()]);
                    }
                }
            }

            $endTime = now();
            $duration = $endTime->diffInSeconds($startTime);

            $this->info("✓ Complete! Sent: {$emailsSent}, Failed: {$emailsFailed}");
            Log::info("===== CRON JOB COMPLETED: SendTrialExpiringEmails =====", [
                'sent' => $emailsSent,
                'failed' => $emailsFailed,
                'duration_seconds' => $duration,
                'end_time' => $endTime->toDateTimeString(),
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Fatal error: " . $e->getMessage());
            Log::error("Fatal error in SendTrialExpiringEmails", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
