<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Subscription Has Been Cancelled</title>
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
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
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
            color: #6366f1;
            font-size: 24px;
            margin: 0 0 20px 0;
        }

        .email-body p {
            margin: 15px 0;
            font-size: 16px;
            line-height: 1.6;
        }

        .confirmation-message {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            border-left: 4px solid #6366f1;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .confirmation-message h3 {
            margin: 0 0 10px 0;
            color: #4f46e5;
            font-size: 20px;
        }

        .confirmation-message p {
            margin: 10px 0;
            color: #3730a3;
        }

        .what-happens {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .what-happens h3 {
            color: #6366f1;
            font-size: 18px;
            margin: 0 0 15px 0;
        }

        .info-item {
            display: flex;
            align-items: start;
            margin: 12px 0;
            padding: 10px;
            background-color: white;
            border-radius: 6px;
        }

        .info-icon {
            color: #6366f1;
            font-size: 20px;
            margin-right: 12px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-text {
            color: #333;
            font-size: 15px;
        }

        .feedback-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .feedback-section h3 {
            margin: 0 0 10px 0;
            color: #d97706;
            font-size: 18px;
        }

        .feedback-section p {
            margin: 10px 0;
            color: #92400e;
        }

        .reactivation-section {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px dashed #10b981;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }

        .reactivation-section h3 {
            margin: 0 0 10px 0;
            color: #059669;
            font-size: 18px;
        }

        .reactivation-section p {
            margin: 10px 0;
            color: #065f46;
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

        .dashboard-link {
            display: inline-block;
            background-color: #f3f4f6;
            color: #6366f1 !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 15px;
            margin-left: 10px;
            border: 2px solid #6366f1;
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
            <h1>Subscription Cancelled</h1>
            <p>We'll miss you!</p>
        </div>

        <div class="email-body">
            <p>Hi {{ $user->name }},</p>

            <div class="confirmation-message">
                <h3>✓ Confirmed</h3>
                <p>Your Snapshot Albums subscription has been successfully cancelled. You'll no longer be charged for the service.</p>
            </div>

            <div class="what-happens">
                <h3>What Happens Now?</h3>

                <div class="info-item">
                    <div class="info-icon">📱</div>
                    <div class="info-text">You can still access your existing albums and view all uploaded photos</div>
                </div>

                <div class="info-item">
                    <div class="info-icon">🚫</div>
                    <div class="info-text">You won't be able to create new events or upload new photos until you reactivate</div>
                </div>

                <div class="info-item">
                    <div class="info-icon">💾</div>
                    <div class="info-text">Your data is safe and will remain accessible if you reactivate your subscription</div>
                </div>

                <div class="info-item">
                    <div class="info-icon">⏱️</div>
                    <div class="info-text">Access expires after {{ env('SUBSCRIPTION_RETENTION_DAYS', 90) }} days of inactivity</div>
                </div>
            </div>

            <div class="feedback-section">
                <h3>We'd Love Your Feedback</h3>
                <p>Your experience matters to us. If there's anything we could have done better, please let us know!</p>
                <p><a href="mailto:support@snapshotalbums.net" style="color: #d97706; text-decoration: none; font-weight: bold;">Send us your feedback</a></p>
            </div>

            <div class="reactivation-section">
                <h3>Ready to Come Back?</h3>
                <p>You can reactivate your subscription anytime you're ready for your next event!</p>
                <p>All your memories and events will be waiting for you.</p>

                <div style="margin-top: 20px;">
                    <a href="{{ $dashboardUrl }}" class="email-button">Reactivate Your Service</a>
                </div>
            </div>

            <p style="margin-top: 30px;">Thank you for using Snapshot Albums. We hope to see you again soon!</p>

            <p>All the best,<br>
                <strong>The Snapshot Albums Team</strong>
            </p>
        </div>

        <div class="email-footer">
            <p>&copy; {{ date('Y') }} Snapshot Albums. All rights reserved.</p>
            <p>Questions about your cancellation? <a href="mailto:support@snapshotalbums.net" style="color: #6366f1; text-decoration: none;">Contact support</a></p>
        </div>
    </div>
</body>

</html>
