<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Free Trial Has Started!</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
            color: #10b981;
            font-size: 24px;
            margin: 0 0 20px 0;
        }

        .email-body p {
            margin: 15px 0;
            font-size: 16px;
            line-height: 1.6;
        }

        .trial-info {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 4px solid #10b981;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }

        .trial-info h3 {
            margin: 0 0 15px 0;
            color: #10b981;
            font-size: 20px;
        }

        .trial-days {
            font-size: 56px;
            font-weight: bold;
            color: #10b981;
            margin: 15px 0;
        }

        .trial-expires {
            color: #059669;
            font-size: 16px;
            margin-top: 10px;
        }

        .plan-details {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .plan-details h3 {
            margin: 0 0 20px 0;
            color: #10b981;
            font-size: 18px;
            text-align: center;
        }

        .plan-name {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        .plan-price {
            text-align: center;
            margin-bottom: 20px;
        }

        .plan-price .amount {
            font-size: 42px;
            font-weight: bold;
            color: #10b981;
        }

        .plan-price .period {
            font-size: 18px;
            color: #666;
        }

        .features-list {
            margin-top: 20px;
        }

        .feature-item {
            display: flex;
            align-items: start;
            margin: 12px 0;
            padding: 10px;
            background-color: white;
            border-radius: 6px;
        }

        .feature-icon {
            color: #10b981;
            font-size: 20px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .feature-text {
            color: #333;
            font-size: 15px;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .email-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }

        .tips-section {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 4px solid #10b981;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .tips-section h3 {
            margin: 0 0 15px 0;
            color: #059669;
            font-size: 18px;
        }

        .tip-item {
            display: flex;
            align-items: start;
            margin: 15px 0;
        }

        .tip-icon {
            color: #10b981;
            font-size: 24px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .tip-content {
            flex: 1;
        }

        .tip-content strong {
            color: #059669;
            display: block;
            margin-bottom: 5px;
        }

        .tip-content p {
            margin: 0;
            color: #047857;
            font-size: 14px;
        }

        .important-note {
            background-color: #f0fdf4;
            border: 2px solid #fbbf24;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }

        .important-note strong {
            color: #b45309;
            font-size: 16px;
            display: block;
            margin-bottom: 10px;
        }

        .important-note p {
            margin: 0;
            color: #78350f;
            font-size: 14px;
        }

        .email-footer {
            background-color: #f9fafb;
            text-align: center;
            padding: 30px 20px;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer a {
            color: #10b981;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .email-container {
                width: 100%;
                border-radius: 0;
                margin: 0;
            }

            .email-header h1 {
                font-size: 24px;
            }

            .email-body {
                padding: 30px 20px;
            }

            .trial-days {
                font-size: 42px;
            }

            .plan-price .amount {
                font-size: 36px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🎁 Your Free Trial Has Started!</h1>
            <p>Explore all premium features</p>
        </div>

        <div class="email-body">
            <h2>Hi {{ $subscription->user->name }},</h2>

            <p>Congratulations! Your 7-day free trial has officially begun. You now have full access to all premium features of SnapshotAlbums.</p>

            <div class="trial-info">
                <h3>⏰ Trial Period</h3>
                <div class="trial-days">7 Days</div>
                <p style="margin: 10px 0; color: #6b21a8; font-size: 16px;">Full access to all premium features</p>
                <div class="trial-expires">
                    Trial ends on: <strong>{{ \Carbon\Carbon::parse($subscription->trial_ends_at)->format('F j, Y') }}</strong>
                </div>
            </div>

            <div class="plan-details">
                <h3>📦 Your Selected Plan</h3>
                <div class="plan-name">{{ $subscription->plan->name }}</div>
                <div class="plan-price">
                    <span class="amount">${{ number_format($subscription->plan->price, 2) }}</span>
                    <span class="period">/month</span>
                </div>

                <div class="features-list">
                    @foreach($subscription->plan->features as $feature)
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <div class="feature-text">{{ $feature }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="tips-section">
                <h3>💡 Quick Start Tips</h3>

                <div class="tip-item">
                    <div class="tip-icon">1️⃣</div>
                    <div class="tip-content">
                        <strong>Connect Your Google Drive</strong>
                        <p>Link your Google Drive account to automatically backup all photos.</p>
                    </div>
                </div>

                <div class="tip-item">
                    <div class="tip-icon">2️⃣</div>
                    <div class="tip-content">
                        <strong>Create Your First Event</strong>
                        <p>Set up an event and generate a QR code for easy photo sharing.</p>
                    </div>
                </div>

                <div class="tip-item">
                    <div class="tip-icon">3️⃣</div>
                    <div class="tip-content">
                        <strong>Share with Guests</strong>
                        <p>Distribute your event QR code via email, social media, or print it out.</p>
                    </div>
                </div>

                <div class="tip-item">
                    <div class="tip-icon">4️⃣</div>
                    <div class="tip-content">
                        <strong>Monitor Uploads</strong>
                        <p>Track photo uploads in real-time through your event dashboard.</p>
                    </div>
                </div>
            </div>

            <div class="button-container">
                <a href="{{ config('app.frontend_url') }}/dashboard" class="email-button">Get Started Now</a>
            </div>

            <div class="important-note">
                <strong>📌 Important Information</strong>
                <p>Your trial is completely free with no credit card required. After 7 days, you can choose to continue with a paid subscription or downgrade to our free plan. We'll send you a reminder before your trial ends.</p>
            </div>

            <p>If you have any questions during your trial period, our support team is here to help!</p>

            <p style="margin-top: 30px;">
                <strong>Enjoy your trial!</strong><br>
                The SnapshotAlbums Team
            </p>
        </div>

        <div class="email-footer">
            <p>© {{ date('Y') }} SnapshotAlbums. All rights reserved.</p>
            <p style="margin-top: 15px;">
                <a href="{{ config('app.frontend_url') }}/settings">Manage Subscription</a> |
                <a href="{{ config('app.frontend_url') }}/contact">Contact Support</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                This email was sent to {{ $subscription->user->email }}
            </p>
        </div>
    </div>
</body>

</html>
