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

        .event-info {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 4px solid #10b981;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .event-title {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
            margin: 0 0 20px 0;
        }

        .event-details {
            margin: 15px 0;
        }

        .event-detail-row {
            display: flex;
            align-items: start;
            margin: 10px 0;
            padding: 8px;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 4px;
        }

        .event-detail-icon {
            color: #10b981;
            font-size: 18px;
            margin-right: 10px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .event-detail-text {
            color: #065f46;
            font-size: 15px;
        }

        .countdown {
            text-align: center;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            margin: 20px 0;
        }

        .countdown-number {
            font-size: 48px;
            font-weight: bold;
            color: #10b981;
            margin: 0;
        }

        .countdown-text {
            color: #666;
            font-size: 16px;
            margin: 5px 0 0 0;
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
            margin: 10px 0;
            padding: 8px;
        }

        .checklist-icon {
            color: #10b981;
            font-size: 18px;
            margin-right: 10px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .checklist-text {
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
            <h1>📅 Your Event is Coming Up!</h1>
            <p>{{ $daysUntilEvent }} days away</p>
        </div>

        <div class="email-body">
            <p>Hi {{ $album->user->name }},</p>

            <div class="event-info">
                <div class="event-title">{{ $album->event_title }}</div>

                <div class="event-details">
                    <div class="event-detail-row">
                        <div class="event-detail-icon">📅</div>
                        <div class="event-detail-text">
                            <strong>Date:</strong> {{ $album->event_date->format('F d, Y') }}
                        </div>
                    </div>

                    @if($album->event_type)
                        <div class="event-detail-row">
                            <div class="event-detail-icon">🎉</div>
                            <div class="event-detail-text">
                                <strong>Type:</strong> {{ $album->event_type }}
                            </div>
                        </div>
                    @endif

                    @if($album->location)
                        <div class="event-detail-row">
                            <div class="event-detail-icon">📍</div>
                            <div class="event-detail-text">
                                <strong>Location:</strong> {{ $album->location }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="countdown">
                    <p class="countdown-number">{{ $daysUntilEvent }}</p>
                    <p class="countdown-text">Days until your event!</p>
                </div>
            </div>

            <div class="checklist">
                <h3>✓ Preparation Checklist</h3>
                <div class="checklist-item">
                    <div class="checklist-icon">✓</div>
                    <div class="checklist-text">Share your event link or QR code with guests</div>
                </div>
                <div class="checklist-item">
                    <div class="checklist-icon">✓</div>
                    <div class="checklist-text">Review your event settings and customize the welcome message</div>
                </div>
                <div class="checklist-item">
                    <div class="checklist-icon">✓</div>
                    <div class="checklist-text">Test the QR code to ensure it works properly</div>
                </div>
                <div class="checklist-item">
                    <div class="checklist-icon">✓</div>
                    <div class="checklist-text">Send reminders to guests to start uploading their photos</div>
                </div>
            </div>

            <div class="button-container">
                <a href="{{ $eventUrl }}" class="email-button">Go to Your Event</a>
            </div>

            <p>Everything ready? Just relax and let your guests do the uploading. We'll keep all their photos safe and organized in your album!</p>

            <p>Excited for your event!<br>
                <strong>The Snapshot Albums Team</strong>
            </p>
        </div>

        <div class="email-footer">
            <p>&copy; {{ date('Y') }} Snapshot Albums. All rights reserved.</p>
            <p>Need assistance? <a href="mailto:support@snapshotalbums.net" style="color: #10b981; text-decoration: none;">Contact support</a></p>
        </div>
    </div>
</body>

</html>
