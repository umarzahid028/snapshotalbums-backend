# 📧 SnapshotAlbums Email Notification System

A complete, production-ready email notification system for SnapshotAlbums with 5 types of automated emails, responsive templates, and scheduled delivery via Laravel's task scheduler.

## 🌟 Features

✅ **5 Professional Email Templates** - Fully responsive and beautifully designed
✅ **Automated Scheduling** - Set it and forget it with Laravel's scheduler
✅ **User Preferences** - Respects user notification settings
✅ **Production Ready** - Includes logging, error handling, and queue support
✅ **Easy Testing** - Interactive test script and manual testing commands
✅ **Complete Documentation** - Step-by-step guides and code examples

## 📋 Email Types

| Email | Purpose | Trigger | Schedule |
|-------|---------|---------|----------|
| **Account Activation** | Welcome new users | Manual (on registration) | Immediate |
| **Upcoming Event** | Remind about events | Automated | Daily @ 9:00 AM |
| **Event Ended** | Summarize completed events | Automated | Daily @ 10:00 AM |
| **Trial Start** | Welcome trial users | Manual (on subscription) | Immediate |
| **Trial Ended** | Encourage conversion | Automated | Daily @ 11:00 AM |

## 🚀 Quick Start

### 1. Set Up Cron Job (Automated)

```bash
cd backend
./setup-cron.sh
```

The script will:
- Detect your PHP path automatically
- Add the cron job to your crontab
- Verify the setup
- Offer to test the scheduler

### 2. Configure Mail Settings

Edit your `.env` file:

```env
# Gmail Example
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@snapshotalbums.com
MAIL_FROM_NAME="SnapshotAlbums"

# Frontend URL (for email links)
APP_FRONTEND_URL=https://snapshotalbums.com
```

### 3. Test Email System

```bash
cd backend
./test-emails.sh
```

This interactive script lets you:
- Test individual email commands
- Run all tests at once
- View scheduled tasks
- Check email logs
- Clear cache

## 📁 What's Included

```
backend/
├── app/
│   ├── Console/Commands/          # Automated email commands
│   │   ├── SendUpcomingEventReminders.php
│   │   ├── SendEventEndedNotifications.php
│   │   └── SendTrialEndedNotifications.php
│   │
│   └── Mail/                      # Mailable classes
│       ├── AccountActivationMail.php
│       ├── UpcomingEventMail.php
│       ├── EventEndedMail.php
│       ├── TrialStartMail.php
│       └── TrialEndedMail.php
│
├── resources/views/emails/        # Email templates
│   ├── account-activation.blade.php
│   ├── upcoming-event.blade.php
│   ├── event-ended.blade.php
│   ├── trial-start.blade.php
│   └── trial-ended.blade.php
│
├── routes/
│   └── console.php                # Scheduler configuration
│
├── EMAIL_SYSTEM_README.md         # This file
├── EMAIL_NOTIFICATIONS_SETUP.md   # Complete setup guide
├── EMAIL_NOTIFICATIONS_SUMMARY.md # Quick reference
├── INTEGRATION_EXAMPLES.md        # Code examples
├── setup-cron.sh                  # Automated cron setup
└── test-emails.sh                 # Email testing script
```

## 🔧 Manual Setup

### Option 1: Automated Script
```bash
cd backend
./setup-cron.sh
```

### Option 2: Manual Cron Entry
```bash
crontab -e
```

Add this line:
```bash
* * * * * cd /Users/umarzahid/Documents/workspace/React/snapshotalbums/backend && php artisan schedule:run >> /dev/null 2>&1
```

## 🧪 Testing

### Test All Commands
```bash
./test-emails.sh
```

### Test Individual Commands
```bash
# Test upcoming event reminders
php artisan email:send-upcoming-event-reminders

# Test event ended notifications
php artisan email:send-event-ended-notifications

# Test trial ended notifications
php artisan email:send-trial-ended-notifications
```

### View Scheduled Tasks
```bash
php artisan schedule:list
```

### Run Scheduler Manually
```bash
php artisan schedule:run --verbose
```

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

## 📚 Documentation

### For Setup & Configuration
👉 **[EMAIL_NOTIFICATIONS_SETUP.md](EMAIL_NOTIFICATIONS_SETUP.md)**
- Complete setup instructions
- Mail configuration guide
- Troubleshooting tips
- Production deployment guide

### For Code Integration
👉 **[INTEGRATION_EXAMPLES.md](INTEGRATION_EXAMPLES.md)**
- How to send Account Activation emails
- How to send Trial Start emails
- How to trigger emails manually
- Queue configuration
- Error handling examples

### Quick Reference
👉 **[EMAIL_NOTIFICATIONS_SUMMARY.md](EMAIL_NOTIFICATIONS_SUMMARY.md)**
- Quick overview
- File structure
- Common issues & solutions

## 💡 Usage Examples

### Send Account Activation Email

```php
use App\Mail\AccountActivationMail;
use Illuminate\Support\Facades\Mail;

// In your registration controller
Mail::to($user->email)->send(new AccountActivationMail($user));
```

### Send Trial Start Email

```php
use App\Mail\TrialStartMail;
use Illuminate\Support\Facades\Mail;

// When user starts trial
Mail::to($subscription->user->email)->send(new TrialStartMail($subscription));
```

### Using Queues (Recommended)

```php
// Send email asynchronously
Mail::to($user->email)->queue(new AccountActivationMail($user));

// Send with delay
Mail::to($user->email)
    ->later(now()->addMinutes(5), new AccountActivationMail($user));
```

More examples in **[INTEGRATION_EXAMPLES.md](INTEGRATION_EXAMPLES.md)**

## ⏰ Email Schedule

All automated emails are scheduled in `routes/console.php`:

| Time | Command | What It Does |
|------|---------|--------------|
| 9:00 AM | `email:send-upcoming-event-reminders` | Sends reminders for events in 3 days and 1 day |
| 10:00 AM | `email:send-event-ended-notifications` | Sends summaries for events that ended yesterday |
| 11:00 AM | `email:send-trial-ended-notifications` | Notifies users when their trial expires |

**Timezone:** America/New_York (configurable)

## 👥 User Preferences

Users can control emails via their settings:

- `email_notifications` - General notifications (event ended, trial ended)
- `event_reminders` - Upcoming event reminders

The system automatically checks these preferences before sending.

## 🎨 Email Templates

All email templates are:
- ✅ Fully responsive (mobile, tablet, desktop)
- ✅ Professionally designed with your brand colors
- ✅ Include CTAs (Call-to-Action buttons)
- ✅ Easy to customize
- ✅ Include unsubscribe information

### Customization

Edit templates in `resources/views/emails/`:

```blade
{{-- Change colors --}}
<div style="background-color: #15803D;">

{{-- Change text --}}
<h1>Your Custom Heading</h1>

{{-- Add/remove sections --}}
<div class="custom-section">
    Your content here
</div>
```

## 🔍 Monitoring & Logging

### View Logs
```bash
# Live tail
tail -f storage/logs/laravel.log

# Search for email logs
grep "email:send" storage/logs/laravel.log

# Last 100 lines
tail -n 100 storage/logs/laravel.log
```

### Check Cron Status
```bash
# Ubuntu/Debian
sudo service cron status

# CentOS/RHEL
sudo systemctl status crond

# View cron logs
grep CRON /var/log/syslog
```

## 🚀 Production Deployment

### Checklist

- [ ] Configure SMTP settings in production `.env`
- [ ] Set up cron job on production server
- [ ] Configure queue for async email sending
- [ ] Set up Supervisor for queue workers (optional)
- [ ] Test email delivery with production SMTP
- [ ] Set correct timezone for your region
- [ ] Enable error logging and monitoring
- [ ] Set up email tracking (optional - Postmark, SendGrid)

### Queue Setup (Recommended)

```bash
# 1. Update .env
QUEUE_CONNECTION=database

# 2. Create jobs table
php artisan queue:table
php artisan migrate

# 3. Start queue worker
php artisan queue:work --daemon --tries=3
```

### Supervisor Configuration

For production, use Supervisor to keep queue workers running:

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

## 🐛 Troubleshooting

### Emails Not Sending

**Check mail configuration:**
```bash
php artisan config:clear
php artisan cache:clear
```

**Test mail connection:**
```bash
php artisan tinker
Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

### Scheduler Not Running

**Verify cron:**
```bash
crontab -l
```

**Check PHP path:**
```bash
which php
```

**Test manually:**
```bash
php artisan schedule:run --verbose
```

### Wrong Timezone

Edit `routes/console.php`:
```php
Schedule::command('email:send-upcoming-event-reminders')
    ->dailyAt('09:00')
    ->timezone('Your/Timezone');  // Change this
```

## 📞 Support

- 📖 **Full Setup Guide:** [EMAIL_NOTIFICATIONS_SETUP.md](EMAIL_NOTIFICATIONS_SETUP.md)
- 💻 **Code Examples:** [INTEGRATION_EXAMPLES.md](INTEGRATION_EXAMPLES.md)
- 📋 **Quick Reference:** [EMAIL_NOTIFICATIONS_SUMMARY.md](EMAIL_NOTIFICATIONS_SUMMARY.md)
- 🌐 **Laravel Docs:** https://laravel.com/docs/mail
- ⏰ **Scheduler Docs:** https://laravel.com/docs/scheduling

## 📝 Notes

- All emails respect user notification preferences
- Failed emails are logged for debugging
- Queue support for better performance
- Timezone configurable per schedule
- Easy to extend with new email types

## ✅ Status

**Version:** 1.0
**Laravel:** 11.x
**Status:** Production Ready
**Last Updated:** January 2025

---

**Need help?** Check the documentation files or review the code examples in `INTEGRATION_EXAMPLES.md`.
