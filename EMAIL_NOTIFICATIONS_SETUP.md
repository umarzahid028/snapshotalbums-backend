# Email Notifications Setup Guide

This guide explains how to set up and use the automated email notification system in SnapshotAlbums.

## Overview

The system includes 5 types of automated email notifications:

1. **Account Activation** - Sent when a user's account is activated
2. **Upcoming Event Reminder** - Sent 3 days and 1 day before an event
3. **Event Ended** - Sent 1 day after an event concludes
4. **Trial Start** - Sent when a user starts their free trial
5. **Trial Ended** - Sent when a user's trial period ends

## Email Templates

All email templates are responsive and located in:
```
resources/views/emails/
├── account-activation.blade.php
├── upcoming-event.blade.php
├── event-ended.blade.php
├── trial-start.blade.php
└── trial-ended.blade.php
```

## Mailable Classes

Mailable classes are located in:
```
app/Mail/
├── AccountActivationMail.php
├── UpcomingEventMail.php
├── EventEndedMail.php
├── TrialStartMail.php
└── TrialEndedMail.php
```

## Console Commands

Three scheduled commands handle automated email sending:

### 1. Send Upcoming Event Reminders
```bash
php artisan email:send-upcoming-event-reminders
```
- Sends reminders for events happening in 3 days
- Sends reminders for events happening in 1 day (tomorrow)
- Only sends to users who have `event_reminders` enabled
- Scheduled to run daily at 9:00 AM

### 2. Send Event Ended Notifications
```bash
php artisan email:send-event-ended-notifications
```
- Sends notifications for events that ended yesterday
- Only sends to users who have `email_notifications` enabled
- Scheduled to run daily at 10:00 AM

### 3. Send Trial Ended Notifications
```bash
php artisan email:send-trial-ended-notifications
```
- Sends notifications for trials that ended today
- Includes user statistics (events created, photos collected)
- Shows available subscription plans
- Automatically updates subscription status to 'expired'
- Scheduled to run daily at 11:00 AM

## Cron Job Setup

### Step 1: Configure Laravel Scheduler

The Laravel scheduler is already configured in `routes/console.php`:

```php
// Send reminders for events happening in 3 days or 1 day
Schedule::command('email:send-upcoming-event-reminders')
    ->dailyAt('09:00')
    ->timezone('America/New_York');

// Send notifications for events that ended yesterday
Schedule::command('email:send-event-ended-notifications')
    ->dailyAt('10:00')
    ->timezone('America/New_York');

// Send notifications for trials that ended
Schedule::command('email:send-trial-ended-notifications')
    ->dailyAt('11:00')
    ->timezone('America/New_York');
```

### Step 2: Set Up Server Cron Job

Add this single cron entry to your server's crontab:

```bash
# Edit crontab
crontab -e

# Add this line (replace /path/to/backend with your actual path)
* * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1
```

**For this project:**
```bash
* * * * * cd /Users/umarzahid/Documents/workspace/React/snapshotalbums/backend && php artisan schedule:run >> /dev/null 2>&1
```

This cron job runs every minute and Laravel's scheduler determines which tasks should actually run.

### Step 3: Verify Cron Job

Check if the cron job is running:
```bash
# View scheduled tasks
php artisan schedule:list

# Test the scheduler (run immediately)
php artisan schedule:run
```

## Manual Testing

### Test Individual Commands

```bash
# Test upcoming event reminders
php artisan email:send-upcoming-event-reminders

# Test event ended notifications
php artisan email:send-event-ended-notifications

# Test trial ended notifications
php artisan email:send-trial-ended-notifications
```

### Test Email Sending in Code

You can manually send emails in your controllers:

#### Account Activation Email
```php
use App\Mail\AccountActivationMail;
use Illuminate\Support\Facades\Mail;

// In your registration/activation logic
Mail::to($user->email)->send(new AccountActivationMail($user));
```

#### Trial Start Email
```php
use App\Mail\TrialStartMail;
use Illuminate\Support\Facades\Mail;

// When user starts a trial subscription
Mail::to($subscription->user->email)->send(new TrialStartMail($subscription));
```

## Mail Configuration

Ensure your `.env` file has the correct mail settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@snapshotalbums.com
MAIL_FROM_NAME="SnapshotAlbums"

# Frontend URL for email links
APP_FRONTEND_URL=https://snapshotalbums.com
```

## User Preferences

Users can control email notifications through their settings:

- `email_notifications` - Controls general email notifications (event ended, trial ended)
- `event_reminders` - Controls upcoming event reminder emails

Update these fields in the `users` table or via the settings API.

## Logging

All email sending activities are logged:

```bash
# View logs
tail -f storage/logs/laravel.log

# Search for email logs
grep "email:send" storage/logs/laravel.log
```

## Troubleshooting

### Emails Not Sending

1. **Check mail configuration:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Test mail connection:**
   ```bash
   php artisan tinker
   Mail::raw('Test email', function($msg) {
       $msg->to('test@example.com')->subject('Test');
   });
   ```

3. **Check queue:**
   If using queues, make sure queue worker is running:
   ```bash
   php artisan queue:work
   ```

### Scheduler Not Running

1. **Verify cron is running:**
   ```bash
   sudo service cron status  # Ubuntu/Debian
   sudo systemctl status crond  # CentOS/RHEL
   ```

2. **Check cron logs:**
   ```bash
   grep CRON /var/log/syslog  # Ubuntu/Debian
   tail -f /var/log/cron  # CentOS/RHEL
   ```

3. **Test scheduler manually:**
   ```bash
   php artisan schedule:run --verbose
   ```

## Production Deployment

### Using Laravel Forge

If using Laravel Forge, the cron job is automatically set up. You just need to ensure your scheduled tasks are defined in `routes/console.php`.

### Using Supervisor (Queue Workers)

For better performance with queued emails, set up Supervisor:

1. Install Supervisor:
   ```bash
   sudo apt-get install supervisor
   ```

2. Create configuration file:
   ```bash
   sudo nano /etc/supervisor/conf.d/snapshotalbums-worker.conf
   ```

3. Add configuration:
   ```ini
   [program:snapshotalbums-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /path/to/backend/artisan queue:work --sleep=3 --tries=3
   autostart=true
   autorestart=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/path/to/backend/storage/logs/worker.log
   ```

4. Start Supervisor:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start snapshotalbums-worker:*
   ```

## Email Queue Configuration

To send emails asynchronously (recommended for production):

1. Update `.env`:
   ```env
   QUEUE_CONNECTION=database
   ```

2. Run migrations:
   ```bash
   php artisan queue:table
   php artisan migrate
   ```

3. Update Mailable classes to use `ShouldQueue`:
   ```php
   use Illuminate\Contracts\Queue\ShouldQueue;

   class AccountActivationMail extends Mailable implements ShouldQueue
   {
       // ...
   }
   ```

4. Start queue worker:
   ```bash
   php artisan queue:work
   ```

## Customization

### Change Email Schedule Times

Edit `routes/console.php`:

```php
// Change from 9:00 AM to 8:00 AM
Schedule::command('email:send-upcoming-event-reminders')
    ->dailyAt('08:00')
    ->timezone('America/New_York');
```

### Change Timezone

Update the timezone in the schedule:

```php
Schedule::command('email:send-upcoming-event-reminders')
    ->dailyAt('09:00')
    ->timezone('Asia/Karachi');  // Your timezone
```

### Customize Email Templates

Edit the Blade templates in `resources/views/emails/` to match your branding and messaging.

### Add More Reminder Days

Edit `app/Console/Commands/SendUpcomingEventReminders.php` to add more reminder intervals:

```php
// Add 7-day reminder
$sevenDaysFromNow = Carbon::now()->addDays(7)->startOfDay();
$sevenDaysEnd = Carbon::now()->addDays(7)->endOfDay();

$albumsIn7Days = Album::whereBetween('event_date', [$sevenDaysFromNow, $sevenDaysEnd])
    ->where('status', 'active')
    ->with('user')
    ->get();

foreach ($albumsIn7Days as $album) {
    Mail::to($album->user->email)->send(new UpcomingEventMail($album, 7));
}
```

## Support

For issues or questions:
- Check Laravel documentation: https://laravel.com/docs/mail
- Check Laravel scheduler docs: https://laravel.com/docs/scheduling
- Review application logs in `storage/logs/`
