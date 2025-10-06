# Email Integration Examples

This document shows how to integrate the email notifications into your application controllers.

## 1. Account Activation Email

Send when a user successfully registers or activates their account.

### In Registration Controller

```php
<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\User;
use App\Mail\AccountActivationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'status' => 'active',
        ]);

        // Send Account Activation Email
        Mail::to($user->email)->send(new AccountActivationMail($user));

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id'   => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
            ],
            'token' => $token,
            'message' => 'Registration successful! Check your email for welcome message.'
        ], 201);
    }
}
```

### Using Queue (Recommended for Production)

```php
use Illuminate\Support\Facades\Mail;

// Send email in queue (non-blocking)
Mail::to($user->email)->queue(new AccountActivationMail($user));

// OR with delay
Mail::to($user->email)
    ->later(now()->addMinutes(5), new AccountActivationMail($user));
```

## 2. Trial Start Email

Send when a user subscribes to a plan with a trial period.

### In Subscription Controller

```php
<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\UserSubscription;
use App\Mail\TrialStartMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StripeSubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|string',
        ]);

        $user = auth()->user();
        $plan = \App\Models\SubscriptionPlan::findOrFail($request->plan_id);

        // Create subscription
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'plan_price' => $plan->price,
            'plan_duration' => $plan->duration_days,
            'plan_no_of_ablums' => $plan->no_of_ablums,
            'status' => 'trialing',
            'trial_ends_at' => Carbon::now()->addDays(7),
            'ends_at' => Carbon::now()->addDays($plan->duration_days),
            'payment_token' => $request->payment_method,
        ]);

        // Send Trial Start Email
        Mail::to($user->email)->send(new TrialStartMail($subscription));

        return response()->json([
            'message' => 'Subscription created successfully! Your 7-day trial has started.',
            'subscription' => $subscription
        ], 201);
    }
}
```

## 3. Manual Event Reminder

Trigger a reminder email manually for any event.

```php
<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\Album;
use App\Mail\UpcomingEventMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AlbumController extends Controller
{
    public function sendReminder($id)
    {
        $album = Album::with('user')->findOrFail($id);

        if ($album->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $eventDate = Carbon::parse($album->event_date);
        $daysUntil = now()->diffInDays($eventDate, false);

        if ($daysUntil < 0) {
            return response()->json(['error' => 'Event has passed'], 400);
        }

        Mail::to($album->user->email)
            ->send(new UpcomingEventMail($album, $daysUntil));

        return response()->json(['message' => 'Reminder sent successfully!']);
    }
}
```

## 4. Event Ended Email

Send when an event is manually closed or automatically after it ends.

```php
<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\Album;
use App\Mail\EventEndedMail;
use Illuminate\Support\Facades\Mail;

class AlbumController extends Controller
{
    public function closeEvent($id)
    {
        $album = Album::with('user')->findOrFail($id);

        if ($album->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $album->update(['status' => 'completed']);

        // Send event ended email if user has notifications enabled
        if ($album->user->email_notifications) {
            Mail::to($album->user->email)
                ->send(new EventEndedMail($album));
        }

        return response()->json([
            'message' => 'Event closed successfully!',
            'album' => $album
        ]);
    }
}
```

## Best Practices

### 1. Always Use Queues in Production

```php
// Bad (blocks HTTP request)
Mail::to($user->email)->send(new AccountActivationMail($user));

// Good (non-blocking)
Mail::to($user->email)->queue(new AccountActivationMail($user));
```

### 2. Respect User Preferences

```php
// Check notification preferences
if ($user->email_notifications) {
    Mail::to($user->email)->queue(new EventEndedMail($album));
}

if ($user->event_reminders) {
    Mail::to($user->email)->queue(new UpcomingEventMail($album, $days));
}
```

### 3. Handle Errors Gracefully

```php
use Illuminate\Support\Facades\Log;

try {
    Mail::to($user->email)->send(new AccountActivationMail($user));
    Log::info("Email sent to: {$user->email}");
} catch (\Exception $e) {
    Log::error("Failed to send email: " . $e->getMessage());
}
```

### 4. Test Locally First

```env
# Use Mailtrap for testing
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password

# Or use log driver
MAIL_MAILER=log
```

## Testing Examples

### Test in Tinker

```bash
php artisan tinker
```

```php
// Test Account Activation
$user = User::first();
Mail::to($user->email)->send(new \App\Mail\AccountActivationMail($user));

// Test Trial Start
$subscription = UserSubscription::first();
Mail::to($subscription->user->email)->send(new \App\Mail\TrialStartMail($subscription));

// Test Event Reminder
$album = Album::first();
Mail::to($album->user->email)->send(new \App\Mail\UpcomingEventMail($album, 3));
```

### Test Commands

```bash
# Test upcoming event reminders
php artisan email:send-upcoming-event-reminders

# Test event ended notifications
php artisan email:send-event-ended-notifications

# Test trial ended notifications
php artisan email:send-trial-ended-notifications
```

## Complete Integration Example

Here's a complete example showing how to integrate all emails:

```php
<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Album;
use App\Mail\AccountActivationMail;
use App\Mail\TrialStartMail;
use App\Mail\UpcomingEventMail;
use App\Mail\EventEndedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Send account activation email
     */
    public function sendActivationEmail(User $user)
    {
        Mail::to($user->email)->queue(new AccountActivationMail($user));

        return response()->json([
            'message' => 'Activation email sent successfully'
        ]);
    }

    /**
     * Send trial start email
     */
    public function sendTrialStartEmail(UserSubscription $subscription)
    {
        Mail::to($subscription->user->email)
            ->queue(new TrialStartMail($subscription));

        return response()->json([
            'message' => 'Trial start email sent successfully'
        ]);
    }

    /**
     * Send event reminder
     */
    public function sendEventReminder(Album $album)
    {
        if (!$album->user->event_reminders) {
            return response()->json([
                'message' => 'User has disabled event reminders'
            ], 400);
        }

        $daysUntil = now()->diffInDays($album->event_date, false);

        Mail::to($album->user->email)
            ->queue(new UpcomingEventMail($album, $daysUntil));

        return response()->json([
            'message' => 'Event reminder sent successfully'
        ]);
    }

    /**
     * Send event ended notification
     */
    public function sendEventEndedNotification(Album $album)
    {
        if (!$album->user->email_notifications) {
            return response()->json([
                'message' => 'User has disabled email notifications'
            ], 400);
        }

        Mail::to($album->user->email)
            ->queue(new EventEndedMail($album));

        return response()->json([
            'message' => 'Event ended notification sent successfully'
        ]);
    }
}
```

## Queue Configuration

### Setup Database Queue

```bash
# Create jobs table
php artisan queue:table
php artisan migrate
```

### Update .env

```env
QUEUE_CONNECTION=database
```

### Start Queue Worker

```bash
# Development
php artisan queue:work

# Production with Supervisor
php artisan queue:work --daemon --tries=3 --timeout=60
```

### Monitor Queue

```bash
# Check queue status
php artisan queue:listen --verbose

# Failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

For more information, see [EMAIL_NOTIFICATIONS_SETUP.md](EMAIL_NOTIFICATIONS_SETUP.md)
