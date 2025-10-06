<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Schedule email notifications
Schedule::command('email:send-upcoming-event-reminders')
    ->dailyAt('09:00')
    ->timezone('America/New_York')
    ->description('Send reminders for events happening in 3 days or 1 day');

Schedule::command('email:send-event-ended-notifications')
    ->dailyAt('10:00')
    ->timezone('America/New_York')
    ->description('Send notifications for events that ended yesterday');

Schedule::command('email:send-trial-ended-notifications')
    ->dailyAt('11:00')
    ->timezone('America/New_York')
    ->description('Send notifications for trials that ended');

// Schedule::command('app:activate-albums')->dailyAt('00:10');
