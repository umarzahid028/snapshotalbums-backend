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
        $today = Carbon::today()->toDateString();

        $updated = Album::whereDate('event_date', $today)
            ->where('status', '!=', 'active')
            ->update(['status' => 'active']);

        $this->info("✅ $updated albums activated for today ($today).");
    }
}
