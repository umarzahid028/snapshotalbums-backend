<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionCancelledMail;
use App\Models\User;
use App\Models\EmailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendSubscriptionCancelledEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-subscription-cancelled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send subscription cancelled emails to users with inactive subscriptions who haven\'t received the notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();

        $this->info("🚀 [CRON START] SendSubscriptionCancelledEmails - " . $startTime->format('Y-m-d H:i:s'));
        Log::info("===== CRON JOB STARTED: SendSubscriptionCancelledEmails =====", [
            'timestamp' => $startTime->toDateTimeString(),
        ]);

        try {
            // Get users with inactive subscriptions who haven't received a cancellation email in the last 24 hours
            $usersWithInactiveSubscriptions = User::whereHas('userSubscriptions', function ($query) {
                $query->where('status', false); // Inactive subscriptions
            })
                ->where('email_notifications', true)
                ->where('status', true)
                ->limit(100) // Process in batches
                ->get();

            // Filter to get those who don't have a recent cancellation email
            $usersToNotify = $usersWithInactiveSubscriptions->filter(function ($user) {
                $recentCancellationEmail = EmailLog::where('user_id', $user->id)
                    ->where('email_type', 'subscription_cancelled')
                    ->where('status', 'sent')
                    ->where('sent_at', '>=', now()->subHours(24))
                    ->exists();

                return !$recentCancellationEmail;
            });

            $this->info("📋 Found {$usersToNotify->count()} users with inactive subscriptions");
            Log::info("Users found with inactive subscriptions", [
                'count' => $usersToNotify->count(),
            ]);

            $emailsSent = 0;
            $emailsFailed = 0;

            foreach ($usersToNotify as $user) {
                try {
                    if ($user->email) {
                        Mail::to($user->email)->send(
                            new SubscriptionCancelledMail($user)
                        );

                        // Log the sent email
                        EmailLog::create([
                            'user_id' => $user->id,
                            'email_type' => 'subscription_cancelled',
                            'recipient_email' => $user->email,
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);

                        $emailsSent++;
                        $this->info("✓ Sent subscription cancelled notification to: {$user->email}");
                        Log::info("Sent subscription cancelled notification for user ID: {$user->id}", [
                            'email' => $user->email,
                        ]);
                    }
                } catch (\Exception $e) {
                    $emailsFailed++;
                    $this->error("✗ Failed to send subscription cancelled notification to: {$user->email}");
                    Log::error("Failed to send subscription cancelled notification for user ID: {$user->id}", [
                        'email' => $user->email,
                        'error' => $e->getMessage(),
                    ]);

                    // Log failed email attempt
                    try {
                        EmailLog::create([
                            'user_id' => $user->id,
                            'email_type' => 'subscription_cancelled',
                            'recipient_email' => $user->email,
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
            Log::info("===== CRON JOB COMPLETED: SendSubscriptionCancelledEmails =====", [
                'sent' => $emailsSent,
                'failed' => $emailsFailed,
                'duration_seconds' => $duration,
                'end_time' => $endTime->toDateTimeString(),
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Fatal error: " . $e->getMessage());
            Log::error("Fatal error in SendSubscriptionCancelledEmails", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
