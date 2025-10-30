<?php

namespace App\Console\Commands;

use App\Mail\EventReminderMail;
use App\Models\Album;
use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEventReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send event reminder emails 7 days before the event';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();

        $this->info("🚀 [CRON START] SendEventReminderEmails - " . $startTime->format('Y-m-d H:i:s'));
        Log::info("===== CRON JOB STARTED: SendEventReminderEmails =====", [
            'timestamp' => $startTime->toDateTimeString(),
        ]);

        try {
            // Get albums where event is 7 days away
            $in7Days = Carbon::now()->addDays(7)->startOfDay();
            $in7DaysEnd = Carbon::now()->addDays(7)->endOfDay();

            $this->info("📅 Checking for events between {$in7Days->format('Y-m-d')} and {$in7DaysEnd->format('Y-m-d')}");

            $upcomingAlbums = Album::whereBetween('event_date', [$in7Days, $in7DaysEnd])
                ->where('status', true)
                ->with(['user'])
                ->get();

            $this->info("📋 Found {$upcomingAlbums->count()} events in 7 days");
            Log::info("Events found in 7 days", [
                'count' => $upcomingAlbums->count(),
            ]);

            $emailsSent = 0;
            $emailsFailed = 0;

            foreach ($upcomingAlbums as $album) {
                try {
                    // Check if album hasn't already sent a reminder
                    if (!$album->last_reminder_email_sent_at && $album->user && $album->user->email) {
                        // Check if user has email notifications enabled
                        if ($album->user->email_notifications) {
                            $daysUntil = now()->diffInDays($album->event_date);

                            Mail::to($album->user->email)->send(
                                new EventReminderMail($album, $daysUntil)
                            );

                            // Log the sent email
                            EmailLog::create([
                                'user_id' => $album->user_id,
                                'album_id' => $album->id,
                                'email_type' => 'event_reminder',
                                'recipient_email' => $album->user->email,
                                'status' => 'sent',
                                'sent_at' => now(),
                            ]);

                            // Update the album's last reminder email sent timestamp
                            $album->update(['last_reminder_email_sent_at' => now()]);

                            $emailsSent++;
                            $this->info("✓ Sent event reminder to: {$album->user->email} for event: {$album->event_title}");
                            Log::info("Sent event reminder for album ID: {$album->id}", [
                                'user_email' => $album->user->email,
                                'event_title' => $album->event_title,
                                'event_date' => $album->event_date,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    $emailsFailed++;
                    $this->error("✗ Failed to send event reminder for album ID: {$album->id}");
                    Log::error("Failed to send event reminder for album ID: {$album->id}", [
                        'error' => $e->getMessage(),
                    ]);

                    // Log failed email attempt
                    try {
                        EmailLog::create([
                            'user_id' => $album->user_id,
                            'album_id' => $album->id,
                            'email_type' => 'event_reminder',
                            'recipient_email' => $album->user->email ?? 'unknown',
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
            Log::info("===== CRON JOB COMPLETED: SendEventReminderEmails =====", [
                'sent' => $emailsSent,
                'failed' => $emailsFailed,
                'duration_seconds' => $duration,
                'end_time' => $endTime->toDateTimeString(),
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Fatal error: " . $e->getMessage());
            Log::error("Fatal error in SendEventReminderEmails", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
