<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Event Has Ended</title>
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

        .stats-container {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            gap: 15px;
        }

        .stat-box {
            flex: 1;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .stat-box .number {
            font-size: 36px;
            font-weight: bold;
            color: #15803D;
            margin: 10px 0;
        }

        .stat-box .label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .event-summary {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .event-summary h3 {
            margin: 0 0 15px 0;
            color: #15803D;
            font-size: 18px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: #666;
            font-size: 14px;
        }

        .summary-value {
            color: #333;
            font-weight: 600;
            font-size: 14px;
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
            margin: 5px;
        }

        .secondary-button {
            display: inline-block;
            background-color: #ffffff;
            color: #15803D !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            border: 2px solid #15803D;
            margin: 5px;
        }

        .next-steps {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .next-steps h3 {
            margin: 0 0 15px 0;
            color: #1e40af;
            font-size: 18px;
        }

        .next-step-item {
            display: flex;
            align-items: start;
            margin: 15px 0;
        }

        .step-number {
            background-color: #3b82f6;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .step-content {
            flex: 1;
        }

        .step-content strong {
            color: #1e40af;
            display: block;
            margin-bottom: 5px;
        }

        .step-content p {
            margin: 0;
            color: #64748b;
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

            .stats-container {
                flex-direction: column;
            }

            .stat-box {
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🎊 Your Event Has Concluded!</h1>
            <p>All photos collected successfully</p>
        </div>

        <div class="email-body">
            <h2>Hi {{ $album->user->name }},</h2>

            <p>We hope your event <strong>{{ $album->event_title }}</strong> was a tremendous success! Here's a summary of all the wonderful memories captured during your event.</p>

            <div class="stats-container">
                <div class="stat-box">
                    <div class="label">Total Photos</div>
                    <div class="number">{{ $album->total_files ?? 0 }}</div>
                </div>
                <div class="stat-box">
                    <div class="label">Contributors</div>
                    <div class="number">{{ $album->total_guests ?? 0 }}</div>
                </div>
            </div>

            <div class="event-summary">
                <h3>📋 Event Summary</h3>
                <div class="summary-row">
                    <span class="summary-label">Event Name</span>
                    <span class="summary-value">{{ $album->event_title }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Event Type</span>
                    <span class="summary-value">{{ ucfirst($album->event_type ?? 'Event') }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Event Date</span>
                    <span class="summary-value">{{ \Carbon\Carbon::parse($album->event_date)->format('F j, Y') }}</span>
                </div>
                @if($album->location)
                <div class="summary-row">
                    <span class="summary-label">Location</span>
                    <span class="summary-value">{{ $album->location }}</span>
                </div>
                @endif
                <div class="summary-row">
                    <span class="summary-label">Event Code</span>
                    <span class="summary-value">{{ $album->qrCode }}</span>
                </div>
            </div>

            <div class="next-steps">
                <h3>🚀 What's Next?</h3>

                <div class="next-step-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <strong>Access Your Photos</strong>
                        <p>All photos have been automatically saved to your Google Drive folder.</p>
                    </div>
                </div>

                <div class="next-step-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <strong>Review & Organize</strong>
                        <p>Browse through your collection and organize photos as you like.</p>
                    </div>
                </div>

                <div class="next-step-item">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <strong>Share with Guests</strong>
                        <p>Download and share the collection with all your event attendees.</p>
                    </div>
                </div>

                <div class="next-step-item">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <strong>Create Another Event</strong>
                        <p>Planning another event? Set up a new photo collection anytime!</p>
                    </div>
                </div>
            </div>

            <div class="button-container">
                <a href="{{ config('app.frontend_url') }}/events/{{ $album->id }}" class="email-button">View Event Details</a>
                <a href="{{ config('app.frontend_url') }}/create-event" class="secondary-button">Create New Event</a>
            </div>

            <p>Thank you for using SnapshotAlbums! We'd love to hear about your experience. If you have a moment, please share your feedback with us.</p>

            <p style="margin-top: 30px;">
                <strong>Warm regards,</strong><br>
                The SnapshotAlbums Team
            </p>
        </div>

        <div class="email-footer">
            <p>© {{ date('Y') }} SnapshotAlbums. All rights reserved.</p>
            <p style="margin-top: 15px;">
                <a href="{{ config('app.frontend_url') }}/contact">Contact Support</a> |
                <a href="{{ config('app.frontend_url') }}/faq">FAQ</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                This email was sent to {{ $album->user->email }}
            </p>
        </div>
    </div>
</body>

</html>
