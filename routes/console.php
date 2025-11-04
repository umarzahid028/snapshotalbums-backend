<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Schedule email notifications with logging
Schedule::command('email:send-upcoming-event-reminders')
    ->dailyAt('09:00')
    ->timezone('America/New_York')
    ->description('Send reminders for events happening in 3 days or 1 day')
    ->before(function () {
        \Log::info('🔔 SCHEDULER: About to run SendUpcomingEventReminders at ' . now()->toDateTimeString());
    })
    ->after(function () {
        \Log::info('✅ SCHEDULER: Completed SendUpcomingEventReminders at ' . now()->toDateTimeString());
    })
    ->onFailure(function () {
        \Log::error('❌ SCHEDULER: SendUpcomingEventReminders FAILED at ' . now()->toDateTimeString());
    });

Schedule::command('email:send-event-ended-notifications')
    ->dailyAt('10:00')
    ->timezone('America/New_York')
    ->description('Send notifications for events that ended yesterday')
    ->before(function () {
        \Log::info('🔔 SCHEDULER: About to run SendEventEndedNotifications at ' . now()->toDateTimeString());
    })
    ->after(function () {
        \Log::info('✅ SCHEDULER: Completed SendEventEndedNotifications at ' . now()->toDateTimeString());
    })
    ->onFailure(function () {
        \Log::error('❌ SCHEDULER: SendEventEndedNotifications FAILED at ' . now()->toDateTimeString());
    });

Schedule::command('email:send-trial-ended-notifications')
    ->dailyAt('11:00')
    ->timezone('America/New_York')
    ->description('Send notifications for trials that ended')
    ->before(function () {
        \Log::info('🔔 SCHEDULER: About to run SendTrialEndedNotifications at ' . now()->toDateTimeString());
    })
    ->after(function () {
        \Log::info('✅ SCHEDULER: Completed SendTrialEndedNotifications at ' . now()->toDateTimeString());
    })
    ->onFailure(function () {
        \Log::error('❌ SCHEDULER: SendTrialEndedNotifications FAILED at ' . now()->toDateTimeString());
    });

Schedule::command('app:activate-albums')
    ->dailyAt('00:10')
    ->timezone('America/New_York')
    ->description('Activate albums on their event date')
    ->before(function () {
        \Log::info('🔔 SCHEDULER: About to run ActivateAlbums at ' . now()->toDateTimeString());
    })
    ->after(function () {
        \Log::info('✅ SCHEDULER: Completed ActivateAlbums at ' . now()->toDateTimeString());
    })
    ->onFailure(function () {
        \Log::error('❌ SCHEDULER: ActivateAlbums FAILED at ' . now()->toDateTimeString());
    });

// New automated email scheduling
// Schedule::command('email:send-welcome')
//     ->dailyAt('06:00')
//     ->timezone('America/New_York')
//     ->description('Send welcome emails to new users')
//     ->before(function () {
//         \Log::info('🔔 SCHEDULER: About to run SendWelcomeEmails at ' . now()->toDateTimeString());
//     })
//     ->after(function () {
//         \Log::info('✅ SCHEDULER: Completed SendWelcomeEmails at ' . now()->toDateTimeString());
//     })
//     ->onFailure(function () {
//         \Log::error('❌ SCHEDULER: SendWelcomeEmails FAILED at ' . now()->toDateTimeString());
//     });




    Schedule::command('email:send-trial-expiring')
    ->dailyAt('07:00')
    ->timezone('America/New_York')
    ->description('Send trial expiring notifications 1 day before trial ends')
    ->before(function () {
        \Log::info('🔔 SCHEDULER: About to run SendTrialExpiringEmails at ' . now()->toDateTimeString());
    })
    ->after(function () {
        \Log::info('✅ SCHEDULER: Completed SendTrialExpiringEmails at ' . now()->toDateTimeString());
    })
    ->onFailure(function () {
        \Log::error('❌ SCHEDULER: SendTrialExpiringEmails FAILED at ' . now()->toDateTimeString());
    });



Schedule::command('email:send-event-reminders')
    ->dailyAt('08:00')
    ->timezone('America/New_York')
    ->description('Send event reminder emails 7 days before the event')
    ->before(function () {
        \Log::info('🔔 SCHEDULER: About to run SendEventReminderEmails at ' . now()->toDateTimeString());
    })
    ->after(function () {
        \Log::info('✅ SCHEDULER: Completed SendEventReminderEmails at ' . now()->toDateTimeString());
    })
    ->onFailure(function () {
        \Log::error('❌ SCHEDULER: SendEventReminderEmails FAILED at ' . now()->toDateTimeString());
    });

Schedule::command('email:send-post-event')
    ->dailyAt('09:30')
    ->timezone('America/New_York')
    ->description('Send post-event emails 1 day after the event')
    ->before(function () {
        \Log::info('🔔 SCHEDULER: About to run SendPostEventEmails at ' . now()->toDateTimeString());
    })
    ->after(function () {
        \Log::info('✅ SCHEDULER: Completed SendPostEventEmails at ' . now()->toDateTimeString());
    })
    ->onFailure(function () {
        \Log::error('❌ SCHEDULER: SendPostEventEmails FAILED at ' . now()->toDateTimeString());
    });

Schedule::command('email:send-subscription-cancelled')
    ->dailyAt('12:00')
    ->timezone('America/New_York')
    ->description('Send subscription cancelled emails to inactive subscriptions')
    ->before(function () {
        \Log::info('🔔 SCHEDULER: About to run SendSubscriptionCancelledEmails at ' . now()->toDateTimeString());
    })
    ->after(function () {
        \Log::info('✅ SCHEDULER: Completed SendSubscriptionCancelledEmails at ' . now()->toDateTimeString());
    })
    ->onFailure(function () {
        \Log::error('❌ SCHEDULER: SendSubscriptionCancelledEmails FAILED at ' . now()->toDateTimeString());
    });

// Log scheduler heartbeat every hour
Schedule::call(function () {
    \Log::info('💓 SCHEDULER HEARTBEAT: Laravel scheduler is running at ' . now()->toDateTimeString());
})->hourly();
