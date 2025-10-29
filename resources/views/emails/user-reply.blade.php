<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Ticket Reply</title>
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
            border-top: none;
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
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .label {
            font-weight: bold;
            color: #16a34a;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-open { background-color: #dbeafe; color: #1e40af; }
        .status-in_progress { background-color: #fef3c7; color: #92400e; }
        .status-resolved { background-color: #d1fae5; color: #065f46; }
        .status-closed { background-color: #e5e7eb; color: #374151; }
        .priority-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .priority-low { background-color: #e5e7eb; color: #374151; }
        .priority-medium { background-color: #dbeafe; color: #1e40af; }
        .priority-high { background-color: #fed7aa; color: #92400e; }
        .priority-urgent { background-color: #fecaca; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h2>📬 New Ticket Reply Received</h2>
    </div>

    <div class="content">
        <p>Hello Support Team,</p>

        <p>A user has replied to their support ticket. Here are the details:</p>

        <div class="ticket-info">
            <p><span class="label">Ticket Number:</span> {{ $ticket->ticket_number }}</p>
            <p><span class="label">Subject:</span> {{ $ticket->subject }}</p>
            <p><span class="label">Customer Name:</span> {{ $ticket->name ?? 'Not provided' }}</p>
            <p><span class="label">Customer Email:</span> {{ $ticket->email }}</p>
            <p>
                <span class="label">Status:</span>
                <span class="status-badge status-{{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span>
            </p>
            <p>
                <span class="label">Priority:</span>
                <span class="priority-badge priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
            </p>
            @if($ticket->category)
            <p><span class="label">Category:</span> {{ ucfirst($ticket->category) }}</p>
            @endif
        </div>

        <h3>User's Reply:</h3>
        <div class="reply-box">
            <p>{{ $reply->message }}</p>
            <p style="color: #666; font-size: 12px; margin-top: 15px;">
                Sent at: {{ $reply->created_at->format('F j, Y, g:i a') }}
            </p>
        </div>

        <div style="background-color: #fef3c7; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <p style="margin: 0;">
                <strong>Action Required:</strong> Please log in to the admin dashboard to view the full conversation and respond to this ticket.
            </p>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated notification from Snapshot Albums Support System</p>
        <p>&copy; {{ date('Y') }} Snapshot Albums. All rights reserved.</p>
    </div>
</body>
</html>
