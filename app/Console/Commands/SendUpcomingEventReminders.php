<?php

namespace App\Console\Commands;

use App\Mail\UpcomingEventMail;
use App\Models\Album;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendUpcomingEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-upcoming-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for events happening in 3 days or 1 day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();

        $this->info("🚀 [CRON START] SendUpcomingEventReminders - " . $startTime->format('Y-m-d H:i:s'));
        Log::info("===== CRON JOB STARTED: SendUpcomingEventReminders =====", [
            'timestamp' => $startTime->toDateTimeString(),
        ]);

        try {
            // Get events happening in 3 days
            $threeDaysFromNow = Carbon::now()->addDays(3)->startOfDay();
            $threeDaysEnd = Carbon::now()->addDays(3)->endOfDay();

            // Get events happening in 1 day (tomorrow)
            $oneDayFromNow = Carbon::now()->addDays(1)->startOfDay();
            $oneDayEnd = Carbon::now()->addDays(1)->endOfDay();

            $this->info("📅 Checking for events in 3 days ({$threeDaysFromNow->format('Y-m-d')}) and 1 day ({$oneDayFromNow->format('Y-m-d')})");

            // Find albums for 3-day reminder
            $albumsIn3Days = Album::whereBetween('event_date', [$threeDaysFromNow, $threeDaysEnd])
                ->where('status', 'active')
                ->with('user')
                ->get();

            // Find albums for 1-day reminder
            $albumsIn1Day = Album::whereBetween('event_date', [$oneDayFromNow, $oneDayEnd])
                ->where('status', 'active')
                ->with('user')
                ->get();

            $this->info("📋 Found {$albumsIn3Days->count()} events in 3 days and {$albumsIn1Day->count()} events in 1 day");
            Log::info("Events found for reminders", [
                '3_day_events' => $albumsIn3Days->count(),
                '1_day_events' => $albumsIn1Day->count(),
                '3_day_list' => $albumsIn3Days->pluck('id', 'event_title')->toArray(),
                '1_day_list' => $albumsIn1Day->pluck('id', 'event_title')->toArray(),
            ]);

            $emailsSent = 0;

        // Send 3-day reminders
        foreach ($albumsIn3Days as $album) {
            try {
                if ($album->user && $album->user->email && $album->user->event_reminders) {
                    Mail::to($album->user->email)->send(new UpcomingEventMail($album, 3));
                    $emailsSent++;
                    $this->info("Sent 3-day reminder for event: {$album->event_title} to {$album->user->email}");
                    Log::info("Sent 3-day reminder for event ID: {$album->id}");
                }
            } catch (\Exception $e) {
                $this->error("Failed to send email for event ID: {$album->id}");
                Log::error("Failed to send 3-day reminder: " . $e->getMessage());
            }
        }

        // Send 1-day reminders
        foreach ($albumsIn1Day as $album) {
            try {
                if ($album->user && $album->user->email && $album->user->event_reminders) {
                    Mail::to($album->user->email)->send(new UpcomingEventMail($album, 1));
                    $emailsSent++;
                    $this->info("Sent 1-day reminder for event: {$album->event_title} to {$album->user->email}");
                    Log::info("Sent 1-day reminder for event ID: {$album->id}");
                }
            } catch (\Exception $e) {
                $this->error("Failed to send email for event ID: {$album->id}");
                Log::error("Failed to send 1-day reminder: " . $e->getMessage());
            }
        }

            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            $this->info("✅ Completed! Sent {$emailsSent} reminder emails");
            $this->info("⏱️  Duration: {$duration} seconds");

            Log::info("===== CRON JOB COMPLETED: SendUpcomingEventReminders =====", [
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

            $this->error("❌ Error sending reminders: " . $e->getMessage());
            Log::error("===== CRON JOB FAILED: SendUpcomingEventReminders =====", [
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
