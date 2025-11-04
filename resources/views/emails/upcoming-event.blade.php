<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Event is Coming Up!</title>
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

        .event-details {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 4px solid #10b981;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .event-detail-row {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }

        .event-icon {
            color: #10b981;
            font-size: 24px;
            margin-right: 15px;
            width: 30px;
            text-align: center;
        }

        .event-detail-text {
            flex: 1;
        }

        .event-detail-text strong {
            color: #10b981;
            font-size: 14px;
            display: block;
            margin-bottom: 3px;
        }

        .event-detail-text span {
            color: #333;
            font-size: 16px;
        }

        .countdown-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }

        .countdown-box h3 {
            margin: 0 0 10px 0;
            color: #92400e;
            font-size: 20px;
        }

        .countdown-box .days {
            font-size: 48px;
            font-weight: bold;
            color: #b45309;
            margin: 10px 0;
        }

        .countdown-box p {
            margin: 0;
            color: #78350f;
            font-size: 16px;
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
            box-shadow: 0 4px 6px rgba(21, 128, 61, 0.2);
            margin: 5px;
        }

        .checklist {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }

        .checklist h3 {
            margin: 0 0 15px 0;
            color: #10b981;
            font-size: 18px;
        }

        .checklist-item {
            display: flex;
            align-items: start;
            margin: 12px 0;
        }

        .checklist-item input[type="checkbox"] {
            margin-right: 10px;
            margin-top: 3px;
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

            .event-details {
                padding: 20px 15px;
            }

            .countdown-box .days {
                font-size: 36px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>📅 Your Event is Coming Up!</h1>
            <p>Time to get ready</p>
        </div>

        <div class="email-body">
            <h2>Hi {{ $album->user->name }},</h2>

            <p>This is a friendly reminder that your event <strong>{{ $album->event_title }}</strong> is approaching soon!</p>

            <div class="countdown-box">
                <h3>⏰ Event Countdown</h3>
                <div class="days">{{ $daysUntil }}</div>
                <p>{{ $daysUntil == 1 ? 'Day' : 'Days' }} Until Your Event</p>
            </div>

            <div class="event-details">
                <div class="event-detail-row">
                    <div class="event-icon">🎉</div>
                    <div class="event-detail-text">
                        <strong>Event Name</strong>
                        <span>{{ $album->event_title }}</span>
                    </div>
                </div>

                <div class="event-detail-row">
                    <div class="event-icon">📆</div>
                    <div class="event-detail-text">
                        <strong>Date</strong>
                        <span>{{ \Carbon\Carbon::parse($album->event_date)->format('l, F j, Y') }}</span>
                    </div>
                </div>

                @if($album->location)
                <div class="event-detail-row">
                    <div class="event-icon">📍</div>
                    <div class="event-detail-text">
                        <strong>Location</strong>
                        <span>{{ $album->location }}</span>
                    </div>
                </div>
                @endif

                <div class="event-detail-row">
                    <div class="event-icon">🎫</div>
                    <div class="event-detail-text">
                        <strong>Event Code</strong>
                        <span>{{ $album->qrCode }}</span>
                    </div>
                </div>
            </div>

            <div class="checklist">
                <h3>✅ Pre-Event Checklist</h3>
                <div class="checklist-item">
                    <span>📱</span>
                    <span style="margin-left: 10px;">Share the QR code with your guests via email or social media</span>
                </div>
                <div class="checklist-item">
                    <span>🖨️</span>
                    <span style="margin-left: 10px;">Print QR codes for display at your event venue</span>
                </div>
                <div class="checklist-item">
                    <span>💾</span>
                    <span style="margin-left: 10px;">Ensure your Google Drive has sufficient storage space</span>
                </div>
                <div class="checklist-item">
                    <span>📧</span>
                    <span style="margin-left: 10px;">Test the upload link to ensure everything works smoothly</span>
                </div>
            </div>

            <div class="button-container">
                <a href="{{ config('app.frontend_url') }}/events/{{ $album->id }}" class="email-button">View Event Details</a>
                <a href="{{ config('app.frontend_url') }}/upload/{{ $album->qrCode }}" class="email-button">Test Upload Link</a>
            </div>

            <p>If you need any assistance or have questions, our support team is here to help!</p>

            <p style="margin-top: 30px;">
                <strong>Best wishes for your event!</strong><br>
                The SnapshotAlbums Team
            </p>
        </div>

        <div class="email-footer">
            <p>© {{ date('Y') }} SnapshotAlbums. All rights reserved.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                This email was sent to {{ $album->user->email }}
            </p>
        </div>
    </div>
</body>

</html>
