<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Album;
use Carbon\Carbon;

class ActivateAlbums extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:activate-albums';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();
        $today = Carbon::today()->toDateString();

        $this->info("🚀 [CRON START] ActivateAlbums - " . $startTime->format('Y-m-d H:i:s'));
        \Log::info("===== CRON JOB STARTED: ActivateAlbums =====", [
            'timestamp' => $startTime->toDateTimeString(),
            'date' => $today,
        ]);

        try {
            // Find albums that need to be activated
            $albumsToActivate = Album::whereDate('event_date', $today)
                ->where('status', '!=', 'active')
                ->get();

            $this->info("📋 Found {$albumsToActivate->count()} albums to activate");
            \Log::info("Albums to activate", [
                'count' => $albumsToActivate->count(),
                'albums' => $albumsToActivate->pluck('id', 'event_title')->toArray()
            ]);

            // Update the albums
            $updated = Album::whereDate('event_date', $today)
                ->where('status', '!=', 'active')
                ->update(['status' => 'active']);

            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            $this->info("✅ Successfully activated $updated albums for today ($today)");
            $this->info("⏱️  Duration: {$duration} seconds");

            \Log::info("===== CRON JOB COMPLETED: ActivateAlbums =====", [
                'albums_activated' => $updated,
                'date' => $today,
                'duration_seconds' => $duration,
                'start_time' => $startTime->toDateTimeString(),
                'end_time' => $endTime->toDateTimeString(),
                'status' => 'success'
            ]);

            return 0;
        } catch (\Exception $e) {
            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            $this->error("❌ Error activating albums: " . $e->getMessage());
            \Log::error("===== CRON JOB FAILED: ActivateAlbums =====", [
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
