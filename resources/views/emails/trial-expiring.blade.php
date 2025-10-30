<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Free Trial Expires Soon!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: white;
            text-align: center;
            padding: 40px 20px;
        }

        .email-header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: bold;
        }

        .email-header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.95;
        }

        .email-body {
            padding: 40px 30px;
        }

        .email-body h2 {
            color: #f59e0b;
            font-size: 24px;
            margin: 0 0 20px 0;
        }

        .email-body p {
            margin: 15px 0;
            font-size: 16px;
            line-height: 1.6;
        }

        .trial-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }

        .trial-warning h3 {
            margin: 0 0 15px 0;
            color: #f59e0b;
            font-size: 20px;
        }

        .countdown {
            font-size: 48px;
            font-weight: bold;
            color: #d97706;
            margin: 15px 0;
        }

        .countdown-text {
            color: #92400e;
            font-size: 16px;
            margin-top: 10px;
        }

        .trial-ends-date {
            background-color: #fff7ed;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
            color: #d97706;
            font-weight: bold;
        }

        .plans-section {
            margin-top: 30px;
        }

        .plans-section h3 {
            color: #f59e0b;
            font-size: 20px;
            margin: 0 0 15px 0;
        }

        .plan-item {
            background-color: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
        }

        .plan-name {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 10px 0;
        }

        .plan-price {
            color: #f59e0b;
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }

        .plan-description {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .email-button {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: #ffffff !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2);
        }

        .email-footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #666;
        }

        .email-footer p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>⏰ Your Free Trial Expires in 1 Day!</h1>
            <p>Time to choose your plan</p>
        </div>

        <div class="email-body">
            <p>Hi {{ $user->name }},</p>

            <div class="trial-warning">
                <h3>Your Free Trial is Expiring</h3>
                <div class="countdown">1 Day</div>
                <div class="countdown-text">Left to explore Snapshot Albums</div>
                <div class="trial-ends-date">
                    Expires: {{ $subscription->trial_ends_at->format('M d, Y') }}
                </div>
            </div>

            <p>We hope you've enjoyed your free trial! To continue using Snapshot Albums and all its amazing features, please choose a plan that works best for you.</p>

            <div class="plans-section">
                <h3>Choose Your Plan</h3>

                @if($availablePlans->count() > 0)
                    @foreach($availablePlans as $plan)
                        <div class="plan-item">
                            <div class="plan-name">{{ $plan->name }}</div>
                            <div class="plan-price">
                                ${{ number_format($plan->price, 2) }}
                                <span style="font-size: 14px; color: #666;">/month</span>
                            </div>
                            <div class="plan-description">
                                {{ $plan->duration_days }} days of access • Up to {{ $plan->no_of_ablums }} albums
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="button-container">
                <a href="{{ $dashboardUrl }}" class="email-button">View All Plans & Subscribe</a>
            </div>

            <p>Questions about our plans? Check out our <a href="{{ config('app.frontend_url') }}/pricing" style="color: #f59e0b; text-decoration: none;">pricing page</a> or contact our support team.</p>

            <p>Don't miss out!<br>
                <strong>The Snapshot Albums Team</strong>
            </p>
        </div>

        <div class="email-footer">
            <p>&copy; {{ date('Y') }} Snapshot Albums. All rights reserved.</p>
            <p>Need help? <a href="mailto:support@snapshotalbums.net" style="color: #f59e0b; text-decoration: none;">Contact support</a></p>
        </div>
    </div>
</body>

</html>
