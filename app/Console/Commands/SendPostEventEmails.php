<?php

namespace App\Console\Commands;

use App\Mail\PostEventMail;
use App\Models\Album;
use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPostEventEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-post-event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send post-event emails 1 day after the event';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();

        $this->info("🚀 [CRON START] SendPostEventEmails - " . $startTime->format('Y-m-d H:i:s'));
        Log::info("===== CRON JOB STARTED: SendPostEventEmails =====", [
            'timestamp' => $startTime->toDateTimeString(),
        ]);

        try {
            // Get albums where event was 1 day ago
            $yesterday = Carbon::yesterday()->startOfDay();
            $yesterdayEnd = Carbon::yesterday()->endOfDay();

            $this->info("📅 Checking for events that happened between {$yesterday->format('Y-m-d')} and {$yesterdayEnd->format('Y-m-d')}");

            $pastAlbums = Album::whereBetween('event_date', [$yesterday, $yesterdayEnd])
                ->where('status', true)
                ->with(['user'])
                ->get();

            $this->info("📋 Found {$pastAlbums->count()} events from 1 day ago");
            Log::info("Events found from 1 day ago", [
                'count' => $pastAlbums->count(),
            ]);

            $emailsSent = 0;
            $emailsFailed = 0;

            foreach ($pastAlbums as $album) {
                try {
                    // Check if post-event email hasn't already been sent
                    if (!$album->last_post_event_email_sent_at && $album->user && $album->user->email) {
                        // Check if user has email notifications enabled
                        if ($album->user->email_notifications) {
                            Mail::to($album->user->email)->send(
                                new PostEventMail($album)
                            );

                            // Log the sent email
                            EmailLog::create([
                                'user_id' => $album->user_id,
                                'album_id' => $album->id,
                                'email_type' => 'post_event',
                                'recipient_email' => $album->user->email,
                                'status' => 'sent',
                                'sent_at' => now(),
                            ]);

                            // Update the album's last post-event email sent timestamp
                            $album->update(['last_post_event_email_sent_at' => now()]);

                            $emailsSent++;
                            $this->info("✓ Sent post-event email to: {$album->user->email} for event: {$album->event_title}");
                            Log::info("Sent post-event email for album ID: {$album->id}", [
                                'user_email' => $album->user->email,
                                'event_title' => $album->event_title,
                                'event_date' => $album->event_date,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    $emailsFailed++;
                    $this->error("✗ Failed to send post-event email for album ID: {$album->id}");
                    Log::error("Failed to send post-event email for album ID: {$album->id}", [
                        'error' => $e->getMessage(),
                    ]);

                    // Log failed email attempt
                    try {
                        EmailLog::create([
                            'user_id' => $album->user_id,
                            'album_id' => $album->id,
                            'email_type' => 'post_event',
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
            Log::info("===== CRON JOB COMPLETED: SendPostEventEmails =====", [
                'sent' => $emailsSent,
                'failed' => $emailsFailed,
                'duration_seconds' => $duration,
                'end_time' => $endTime->toDateTimeString(),
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Fatal error: " . $e->getMessage());
            Log::error("Fatal error in SendPostEventEmails", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
