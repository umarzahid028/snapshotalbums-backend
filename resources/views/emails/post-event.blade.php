<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Out Your Event Photos!</title>
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

        .celebration-message {
            background: linear-gradient(135deg, #dcfce7 0%, #dcfce7 100%);
            border-left: 4px solid #10b981;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }

        .celebration-message h3 {
            margin: 0 0 10px 0;
            color: #059669;
            font-size: 22px;
        }

        .celebration-message p {
            margin: 10px 0;
            color: #10b981;
            font-size: 16px;
        }

        .event-recap {
            background-color: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .recap-title {
            color: #10b981;
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 15px 0;
        }

        .recap-item {
            display: flex;
            align-items: start;
            margin: 12px 0;
            padding: 10px;
            background-color: white;
            border-radius: 6px;
        }

        .recap-icon {
            color: #10b981;
            font-size: 20px;
            margin-right: 12px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .recap-text {
            color: #333;
            font-size: 15px;
        }

        .upload-info {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px dashed #10b981;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }

        .upload-info h3 {
            margin: 0 0 10px 0;
            color: #10b981;
            font-size: 18px;
        }

        .upload-info p {
            margin: 8px 0;
            color: #0c4a6e;
            font-size: 15px;
        }

        .qr-section {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }

        .qr-section h4 {
            margin: 0 0 10px 0;
            color: #10b981;
            font-size: 16px;
        }

        .qr-code {
            background: white;
            padding: 15px;
            border-radius: 6px;
            display: inline-block;
            margin: 10px 0;
        }

        .qr-code-text {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
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
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        }

        .secondary-button {
            display: inline-block;
            background-color: #f3f4f6;
            color: #10b981 !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 15px;
            margin-left: 10px;
            border: 2px solid #10b981;
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
            <h1>📸 Your Event is a Hit!</h1>
            <p>All the photos are in</p>
        </div>

        <div class="email-body">
            <p>Hi {{ $album->user->name }},</p>

            <div class="celebration-message">
                <h3>🎉 Thank You!</h3>
                <p>Your event "{{ $album->event_title }}" was a success! Your guests have been uploading photos and creating amazing memories together.</p>
            </div>

            <h2>What's Next?</h2>

            <div class="recap-item">
                <div class="recap-icon">✓</div>
                <div class="recap-text"><strong>Browse all uploaded photos</strong> - View and organize all memories from your event</div>
            </div>
            <div class="recap-item">
                <div class="recap-icon">✓</div>
                <div class="recap-text"><strong>Download photos</strong> - Save your favorite moments to your device</div>
            </div>
            <div class="recap-item">
                <div class="recap-icon">✓</div>
                <div class="recap-text"><strong>Share with guests</strong> - Send the album link to friends and family</div>
            </div>

            <div class="upload-info">
                <h3>📤 Still Accepting Uploads</h3>
                <p>Your guests can continue to upload photos to the album!</p>
                <p>Keep the album open for as long as you'd like, or close it anytime.</p>
            </div>

            <div class="qr-section">
                <h4>🔗 Share Your QR Code</h4>
                <p>Use this code to let guests easily access and upload photos:</p>
                <div class="qr-code">
                    <strong>{{ $qrCode }}</strong>
                </div>
                <div class="qr-code-text">Scan or tap to open the album</div>
            </div>

            <div class="button-container">
                <a href="{{ $eventUrl }}" class="email-button">View Your Album</a>
            </div>

            <p>We hope you loved using Snapshot Albums! Questions or feedback? We'd love to hear from you.</p>

            <p>Cheers,<br>
                <strong>The Snapshot Albums Team</strong>
            </p>
        </div>

        <div class="email-footer">
            <p>&copy; {{ date('Y') }} Snapshot Albums. All rights reserved.</p>
            <p>Questions? <a href="mailto:support@snapshotalbums.net" style="color: #10b981; text-decoration: none;">Contact our support team</a></p>
        </div>
    </div>
</body>

</html>
