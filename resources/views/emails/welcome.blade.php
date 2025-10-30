<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Snapshot Albums!</title>
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
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
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
            color: #7c3aed;
            font-size: 24px;
            margin: 0 0 20px 0;
        }

        .email-body p {
            margin: 15px 0;
            font-size: 16px;
            line-height: 1.6;
        }

        .welcome-message {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            border-left: 4px solid #7c3aed;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .welcome-message p {
            margin: 10px 0;
        }

        .features-list {
            margin-top: 20px;
        }

        .feature-item {
            display: flex;
            align-items: start;
            margin: 12px 0;
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 6px;
        }

        .feature-icon {
            color: #7c3aed;
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
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: #ffffff !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(124, 58, 237, 0.2);
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
            <h1>🎉 Welcome to Snapshot Albums!</h1>
            <p>Thank you for signing up</p>
        </div>

        <div class="email-body">
            <p>Hi {{ $user->name }},</p>

            <div class="welcome-message">
                <p><strong>Welcome to the easiest way to collect and share photos from your events!</strong></p>
                <p>We're thrilled to have you on board. Snapshot Albums makes it simple to create a shared photo album for any occasion, invite your guests, and watch as they upload their favorite memories.</p>
            </div>

            <h2>Getting Started</h2>
            <p>Here's what you can do now:</p>

            <div class="features-list">
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text"><strong>Create Your First Event</strong> - Set up an album for your upcoming event or celebration</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text"><strong>Invite Guests</strong> - Send invitations to your friends and family to start uploading photos</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text"><strong>Organize Memories</strong> - Automatically collect and organize all photos in one place</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text"><strong>Generate QR Code</strong> - Create a unique QR code for easy guest access</div>
                </div>
            </div>

            <div class="button-container">
                <a href="{{ $dashboardUrl }}" class="email-button">Go to Your Dashboard</a>
            </div>

            <p>If you have any questions or need help getting started, don't hesitate to reach out to our support team.</p>

            <p>Happy album creating!<br>
                <strong>The Snapshot Albums Team</strong>
            </p>
        </div>

        <div class="email-footer">
            <p>&copy; {{ date('Y') }} Snapshot Albums. All rights reserved.</p>
            <p>Have questions? <a href="mailto:support@snapshotalbums.net" style="color: #7c3aed; text-decoration: none;">Contact our support team</a></p>
        </div>
    </div>
</body>

</html>
