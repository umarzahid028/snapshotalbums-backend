<?php

namespace App\Console\Commands;

use App\Mail\WelcomeMail;
use App\Models\User;
use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-welcome';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send welcome emails to new users who haven\'t received one yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();

        $this->info("🚀 [CRON START] SendWelcomeEmails - " . $startTime->format('Y-m-d H:i:s'));
        Log::info("===== CRON JOB STARTED: SendWelcomeEmails =====", [
            'timestamp' => $startTime->toDateTimeString(),
        ]);

        try {
            // Get users who haven't received a welcome email yet
            $users = User::whereNull('last_welcome_email_sent_at')
                ->where('status', true)
                ->limit(100) // Process in batches
                ->get();

            $this->info("📋 Found {$users->count()} users without welcome email");
            Log::info("Users found without welcome email", [
                'count' => $users->count(),
            ]);

            $emailsSent = 0;
            $emailsFailed = 0;

            foreach ($users as $user) {
                try {
                    // Check if user has email notifications enabled
                    if ($user->email_notifications !== false && $user->email) {
                        Mail::to($user->email)->send(new WelcomeMail($user));

                        // Log the sent email
                        EmailLog::create([
                            'user_id' => $user->id,
                            'email_type' => 'welcome',
                            'recipient_email' => $user->email,
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);

                        // Update the user's last welcome email sent timestamp
                        $user->update(['last_welcome_email_sent_at' => now()]);

                        $emailsSent++;
                        $this->info("✓ Sent welcome email to: {$user->email}");
                        Log::info("Sent welcome email for user ID: {$user->id}", [
                            'email' => $user->email,
                        ]);
                    } else {
                        $this->warn("⊘ Skipped user {$user->email} - notifications disabled");
                    }
                } catch (\Exception $e) {
                    $emailsFailed++;
                    $this->error("✗ Failed to send welcome email to: {$user->email}");
                    Log::error("Failed to send welcome email for user ID: {$user->id}", [
                        'email' => $user->email,
                        'error' => $e->getMessage(),
                    ]);

                    // Log failed email attempt
                    EmailLog::create([
                        'user_id' => $user->id,
                        'email_type' => 'welcome',
                        'recipient_email' => $user->email,
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }

            $endTime = now();
            $duration = $endTime->diffInSeconds($startTime);

            $this->info("✓ Complete! Sent: {$emailsSent}, Failed: {$emailsFailed}");
            Log::info("===== CRON JOB COMPLETED: SendWelcomeEmails =====", [
                'sent' => $emailsSent,
                'failed' => $emailsFailed,
                'duration_seconds' => $duration,
                'end_time' => $endTime->toDateTimeString(),
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Fatal error: " . $e->getMessage());
            Log::error("Fatal error in SendWelcomeEmails", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
