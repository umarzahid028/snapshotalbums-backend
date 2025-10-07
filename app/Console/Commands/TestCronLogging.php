<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TestCronLogging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:test-logging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test cron job logging functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();

        $this->info("🚀 [TEST CRON] Starting test at " . $startTime->format('Y-m-d H:i:s'));
        Log::info("===== TEST CRON JOB STARTED =====", [
            'timestamp' => $startTime->toDateTimeString(),
            'timezone' => config('app.timezone'),
        ]);

        try {
            // Simulate some work
            $this->info("⏳ Processing...");
            sleep(2);

            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            $this->info("✅ Test completed successfully!");
            $this->info("⏱️  Duration: {$duration} seconds");

            Log::info("===== TEST CRON JOB COMPLETED =====", [
                'duration_seconds' => $duration,
                'start_time' => $startTime->toDateTimeString(),
                'end_time' => $endTime->toDateTimeString(),
                'status' => 'success'
            ]);

            $this->newLine();
            $this->info("📝 Check your logs at: storage/logs/laravel.log");

            return 0;
        } catch (\Exception $e) {
            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            $this->error("❌ Test failed: " . $e->getMessage());
            Log::error("===== TEST CRON JOB FAILED =====", [
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
