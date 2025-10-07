# 📊 Cron Job Logging Guide

This guide explains how to monitor and verify that your Laravel scheduled tasks (cron jobs) are running correctly.

## 🎯 What Was Added

Enhanced logging has been added to all scheduled tasks with the following features:

### 1. **Command-Level Logging**
Each command now logs:
- ✅ Start time with timestamp
- 📋 Details of what's being processed (counts, IDs, etc.)
- ✅ Success/failure status
- ⏱️ Execution duration
- ❌ Full error details if something fails

### 2. **Scheduler-Level Logging**
The scheduler itself logs:
- 🔔 Before each job runs
- ✅ After each job completes
- ❌ When a job fails
- 💓 Hourly heartbeat to confirm scheduler is alive

## 📅 Scheduled Tasks

| Command | Schedule | Time (EST) | Description |
|---------|----------|------------|-------------|
| `email:send-upcoming-event-reminders` | Daily | 9:00 AM | Sends reminders for events in 3 days and 1 day |
| `email:send-event-ended-notifications` | Daily | 10:00 AM | Notifies about events that ended yesterday |
| `email:send-trial-ended-notifications` | Daily | 11:00 AM | Notifies about expired trial periods |
| `app:activate-albums` | Daily | 12:10 AM | Activates albums on their event date |
| Scheduler Heartbeat | Hourly | Every hour | Confirms scheduler is running |

## 🔍 How to Check Logs

### Method 1: View Laravel Log File
```bash
# View last 50 lines
tail -50 storage/logs/laravel.log

# View last 100 lines
tail -100 storage/logs/laravel.log

# Follow logs in real-time
tail -f storage/logs/laravel.log

# Search for specific cron job
grep "ActivateAlbums" storage/logs/laravel.log

# Search for failures
grep "FAILED" storage/logs/laravel.log

# View today's logs
grep "$(date +%Y-%m-%d)" storage/logs/laravel.log
```

### Method 2: View by Date
```bash
# View logs from specific date
grep "2025-10-07" storage/logs/laravel.log

# View logs from last 24 hours
find storage/logs -name "*.log" -mtime -1 -exec cat {} \;
```

## 📝 Log Format Examples

### Successful Execution
```
[2025-10-07 09:00:01] local.INFO: 🔔 SCHEDULER: About to run SendUpcomingEventReminders at 2025-10-07 09:00:01
[2025-10-07 09:00:01] local.INFO: ===== CRON JOB STARTED: SendUpcomingEventReminders ===== {"timestamp":"2025-10-07 09:00:01"}
[2025-10-07 09:00:01] local.INFO: Events found for reminders {"3_day_events":2,"1_day_events":1,...}
[2025-10-07 09:00:05] local.INFO: ===== CRON JOB COMPLETED: SendUpcomingEventReminders ===== {"emails_sent":3,"duration_seconds":4,"start_time":"2025-10-07 09:00:01","end_time":"2025-10-07 09:00:05","status":"success"}
[2025-10-07 09:00:05] local.INFO: ✅ SCHEDULER: Completed SendUpcomingEventReminders at 2025-10-07 09:00:05
```

### Failed Execution
```
[2025-10-07 10:00:01] local.INFO: 🔔 SCHEDULER: About to run SendEventEndedNotifications at 2025-10-07 10:00:01
[2025-10-07 10:00:01] local.INFO: ===== CRON JOB STARTED: SendEventEndedNotifications =====
[2025-10-07 10:00:02] local.ERROR: ===== CRON JOB FAILED: SendEventEndedNotifications ===== {"error":"SQLSTATE[HY000]...","trace":"...","status":"failed"}
[2025-10-07 10:00:02] local.ERROR: ❌ SCHEDULER: SendEventEndedNotifications FAILED at 2025-10-07 10:00:02
```

### Scheduler Heartbeat
```
[2025-10-07 11:00:00] local.INFO: 💓 SCHEDULER HEARTBEAT: Laravel scheduler is running at 2025-10-07 11:00:00
[2025-10-07 12:00:00] local.INFO: 💓 SCHEDULER HEARTBEAT: Laravel scheduler is running at 2025-10-07 12:00:00
```

## 🧪 Testing Commands

### Test Logging System
```bash
# Run test command
php artisan cron:test-logging

# Expected output:
🚀 [TEST CRON] Starting test at 2025-10-07 14:30:15
⏳ Processing...
✅ Test completed successfully!
⏱️  Duration: 2 seconds
📝 Check your logs at: storage/logs/laravel.log
```

### Manually Run Individual Jobs
```bash
# Test upcoming event reminders
php artisan email:send-upcoming-event-reminders

# Test event ended notifications
php artisan email:send-event-ended-notifications

# Test trial ended notifications
php artisan email:send-trial-ended-notifications

# Test album activation
php artisan app:activate-albums
```

### View Scheduled Tasks List
```bash
# List all scheduled tasks
php artisan schedule:list

# Run scheduler manually (for testing)
php artisan schedule:run

# Run specific command manually
php artisan schedule:test
```

## 🚨 Troubleshooting

### Problem: No Logs Appearing

**Check 1: Is the cron configured?**
```bash
# Check crontab
crontab -l

# Should see something like:
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**Check 2: Is the scheduler running?**
```bash
# Look for heartbeat logs (should appear hourly)
grep "SCHEDULER HEARTBEAT" storage/logs/laravel.log | tail -5
```

**Check 3: Check permissions**
```bash
# Make sure log directory is writable
chmod -R 775 storage/logs
chown -R www-data:www-data storage/logs  # Adjust user as needed
```

### Problem: Jobs Not Running at Scheduled Time

**Check timezone configuration:**
```bash
# In .env
APP_TIMEZONE=America/New_York

# Restart after changing
php artisan config:cache
```

**Check server time:**
```bash
date
# Should show correct timezone
```

### Problem: Jobs Failing

**View error details:**
```bash
# See full error traces
grep -A 10 "CRON JOB FAILED" storage/logs/laravel.log | tail -20
```

**Common issues:**
- Database connection errors
- SMTP/mail configuration issues
- Missing environment variables
- Permission issues

## 📊 Monitoring Commands

### Create Custom Monitoring Script
```bash
#!/bin/bash
# monitor-cron.sh

echo "=== Cron Job Status (Last 24 Hours) ==="
echo ""

echo "📋 ActivateAlbums:"
grep "ActivateAlbums" storage/logs/laravel.log | grep "$(date +%Y-%m-%d)" | tail -5

echo ""
echo "📧 Email Reminders:"
grep "SendUpcomingEventReminders" storage/logs/laravel.log | grep "$(date +%Y-%m-%d)" | tail -5

echo ""
echo "💓 Scheduler Heartbeat (Last 5):"
grep "SCHEDULER HEARTBEAT" storage/logs/laravel.log | tail -5

echo ""
echo "❌ Failures (if any):"
grep "FAILED" storage/logs/laravel.log | grep "$(date +%Y-%m-%d)"
```

## 📈 Success Indicators

Your cron jobs are working correctly if you see:

1. ✅ **Hourly heartbeats** - Confirms scheduler is running
2. ✅ **Start/Complete logs** - Each job logs when it starts and finishes
3. ✅ **No FAILED messages** - All jobs complete successfully
4. ✅ **Execution times match schedule** - Jobs run at their scheduled times
5. ✅ **Duration is reasonable** - Jobs complete in expected timeframe

## 🔗 Additional Resources

- **Laravel Docs**: https://laravel.com/docs/scheduling
- **Log Location**: `storage/logs/laravel.log`
- **Cron Expression Tester**: https://crontab.guru/

## 📞 Support

If cron jobs are not working:
1. Check this guide's troubleshooting section
2. Review the log file for error messages
3. Run commands manually to test
4. Verify server cron is configured correctly
