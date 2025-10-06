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
        $this->info('Starting to send event ended notification emails...');

        // Get events that happened yesterday
        $yesterday = Carbon::yesterday()->startOfDay();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();

        // Find albums that ended yesterday and are still active
        $endedAlbums = Album::whereBetween('event_date', [$yesterday, $yesterdayEnd])
            ->where('status', 'active')
            ->with('user')
            ->get();

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

        $this->info("Completed! Sent {$emailsSent} event ended notification emails.");
        Log::info("SendEventEndedNotifications: Sent {$emailsSent} emails");

        return 0;
    }
}
