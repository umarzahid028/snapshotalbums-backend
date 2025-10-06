<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to SnapshotAlbums</title>
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
            background: linear-gradient(135deg, #15803D 0%, #22C55E 100%);
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
            color: #15803D;
            font-size: 24px;
            margin: 0 0 20px 0;
        }

        .email-body p {
            margin: 15px 0;
            font-size: 16px;
            line-height: 1.6;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .email-button {
            display: inline-block;
            background: linear-gradient(135deg, #15803D 0%, #22C55E 100%);
            color: #ffffff !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(21, 128, 61, 0.2);
        }

        .email-button:hover {
            box-shadow: 0 6px 10px rgba(21, 128, 61, 0.3);
        }

        .features {
            background-color: #f0fdf4;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }

        .feature-item {
            display: flex;
            align-items: start;
            margin: 15px 0;
        }

        .feature-icon {
            color: #15803D;
            font-size: 24px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .feature-text {
            flex: 1;
        }

        .feature-text h3 {
            margin: 0 0 5px 0;
            color: #15803D;
            font-size: 18px;
        }

        .feature-text p {
            margin: 0;
            color: #666;
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
            color: #15803D;
            text-decoration: none;
        }

        .social-links {
            margin: 20px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #15803D;
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

            .feature-item {
                flex-direction: column;
            }

            .feature-icon {
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🎉 Welcome to SnapshotAlbums!</h1>
            <p>Your account has been successfully activated</p>
        </div>

        <div class="email-body">
            <h2>Hi {{ $user->name }},</h2>

            <p>Welcome aboard! We're thrilled to have you join the SnapshotAlbums family. Your account has been successfully activated, and you're all set to start creating amazing photo collections for your events.</p>

            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">📸</div>
                    <div class="feature-text">
                        <h3>Create Events</h3>
                        <p>Set up photo collection events for weddings, parties, corporate events, and more.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">📱</div>
                    <div class="feature-text">
                        <h3>QR Code Sharing</h3>
                        <p>Generate unique QR codes for guests to easily upload photos from their phones.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">☁️</div>
                    <div class="feature-text">
                        <h3>Cloud Storage</h3>
                        <p>All photos are automatically backed up to your connected Google Drive.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">🎁</div>
                    <div class="feature-text">
                        <h3>7-Day Free Trial</h3>
                        <p>Explore all premium features with our complimentary 7-day trial period.</p>
                    </div>
                </div>
            </div>

            <div class="button-container">
                <a href="{{ config('app.frontend_url') }}/dashboard" class="email-button">Go to Dashboard</a>
            </div>

            <p>Need help getting started? Check out our <a href="{{ config('app.frontend_url') }}/features" style="color: #15803D; text-decoration: none;">features guide</a> or <a href="{{ config('app.frontend_url') }}/contact" style="color: #15803D; text-decoration: none;">contact our support team</a>.</p>

            <p>Happy collecting!</p>

            <p style="margin-top: 30px;">
                <strong>The SnapshotAlbums Team</strong>
            </p>
        </div>

        <div class="email-footer">
            <p>© {{ date('Y') }} SnapshotAlbums. All rights reserved.</p>
            <div class="social-links">
                <a href="#">Facebook</a> |
                <a href="#">Twitter</a> |
                <a href="#">Instagram</a>
            </div>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                This email was sent to {{ $user->email }}. If you didn't create an account with us, please ignore this email.
            </p>
        </div>
    </div>
</body>

</html>
