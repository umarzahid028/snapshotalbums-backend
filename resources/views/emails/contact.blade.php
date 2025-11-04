<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectText ?? 'Message' }}</title>
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
            max-width: 800px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #eaeaea;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .email-header {
            background-color: #10b981;
            /* Green header */
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        .email-body {
            padding: 20px;
        }

        .email-body p {
            margin: 10px 0;
        }

        .email-link {
            display: inline-block;
            background-color: #10b981;
            /* Green button */
            color: #ffffff !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .email-footer {
            background-color: #ffffff;
            /* White footer */
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eaeaea;
        }

        .email-footer a {
            color: #10b981;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .email-container {
                width: 100%;
                border-radius: 0;
            }

            .email-header {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            {{ $data['subject'] }}
        </div>
        <div class="email-body">
            <p><strong>Full Name:</strong> {{ $data['name'] }}</p>
            <p><strong>Email:</strong> {{ $data['email'] }}</p>
            @if (!empty($data['inquiry_type']))
                <p><strong>Inquiry Type:</strong> {{ $data['inquiry_type'] }}</p>
            @endif
            <p><strong>Subject:</strong> {{ $data['subject'] }}</p>
            <hr>
            <p>{{ $data['message'] }}</p>
        </div>
        <div class="email-footer">
            <p>© {{ date('Y') }} {{ $company_name ?? '' }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
