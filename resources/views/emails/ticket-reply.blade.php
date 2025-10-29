<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Ticket Reply</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #16a34a;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
        }
        .ticket-info {
            background-color: #fff;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #16a34a;
        }
        .reply-box {
            background-color: #fff;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #16a34a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Support Ticket Update</h1>
    </div>

    <div class="content">
        <p>Hello {{ $ticket->name ?? 'there' }},</p>

        <p>You have received a new reply on your support ticket.</p>

        <div class="ticket-info">
            <strong>Ticket Number:</strong> {{ $ticket->ticket_number }}<br>
            <strong>Subject:</strong> {{ $ticket->subject }}<br>
            <strong>Status:</strong> {{ ucfirst($ticket->status) }}
        </div>

        <div class="reply-box">
            <h3>Reply from Support Team:</h3>
            <p>{{ $reply->message }}</p>
            <p style="color: #666; font-size: 12px; margin-top: 15px;">
                <em>Replied on {{ $reply->created_at->format('F j, Y \a\t g:i A') }}</em>
            </p>
        </div>

        <p>If you have any further questions, please reply to this email or contact us at support@snapshotalbums.net</p>

        <div class="footer">
            <p>This is an automated message from SnapshotAlbums Support.</p>
            <p>&copy; {{ date('Y') }} SnapshotAlbums. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
