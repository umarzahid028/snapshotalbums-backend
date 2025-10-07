<?php

namespace App\Console\Commands;

use App\Mail\EventEndedMail;
use App\Models\Album;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEventEndedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-event-ended-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification emails for events that ended 1 day ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();

        $this->info("🚀 [CRON START] SendEventEndedNotifications - " . $startTime->format('Y-m-d H:i:s'));
        Log::info("===== CRON JOB STARTED: SendEventEndedNotifications =====", [
            'timestamp' => $startTime->toDateTimeString(),
        ]);

        try {
            // Get events that happened yesterday
            $yesterday = Carbon::yesterday()->startOfDay();
            $yesterdayEnd = Carbon::yesterday()->endOfDay();

            $this->info("📅 Checking for events that ended yesterday ({$yesterday->format('Y-m-d')})");

            // Find albums that ended yesterday and are still active
            $endedAlbums = Album::whereBetween('event_date', [$yesterday, $yesterdayEnd])
                ->where('status', 'active')
                ->with('user')
                ->get();

            $this->info("📋 Found {$endedAlbums->count()} events that ended yesterday");
            Log::info("Events found that ended", [
                'count' => $endedAlbums->count(),
                'events' => $endedAlbums->pluck('id', 'event_title')->toArray()
            ]);

            $emailsSent = 0;

        foreach ($endedAlbums as $album) {
            try {
                if ($album->user && $album->user->email && $album->user->email_notifications) {
                    Mail::to($album->user->email)->send(new EventEndedMail($album));
                    $emailsSent++;
                    $this->info("Sent event ended notification for: {$album->event_title} to {$album->user->email}");
                    Log::info("Sent event ended notification for event ID: {$album->id}");

                    // Optionally mark the event as completed
                    // $album->update(['status' => 'completed']);
                }
            } catch (\Exception $e) {
                $this->error("Failed to send email for event ID: {$album->id}");
                Log::error("Failed to send event ended notification: " . $e->getMessage());
            }
        }

            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            $this->info("✅ Completed! Sent {$emailsSent} event ended notification emails");
            $this->info("⏱️  Duration: {$duration} seconds");

            Log::info("===== CRON JOB COMPLETED: SendEventEndedNotifications =====", [
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

            $this->error("❌ Error sending event ended notifications: " . $e->getMessage());
            Log::error("===== CRON JOB FAILED: SendEventEndedNotifications =====", [
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
