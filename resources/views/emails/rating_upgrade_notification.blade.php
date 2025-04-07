<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rating Upgrade Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background-color: #4f46e5;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
        }
        .email-body p {
            margin: 0 0 15px;
            line-height: 1.6;
        }
        .email-body .highlight {
            color: #4f46e5;
            font-weight: bold;
        }
        .email-footer {
            text-align: center;
            padding: 15px;
            background-color: #f4f4f9;
            font-size: 14px;
            color: #777;
        }
        .email-footer a {
            color: #4f46e5;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #3b3abf;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>Rating Upgrade Notification</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>Dear <span class="highlight">{{ $token->user->name }}</span>,</p>
            <p>We are pleased to inform you that a <span class="highlight">Rating Upgrade Request</span> has been initiated for you.</p>
            <p>The requested rating is: <span class="highlight">{{ substr($token->id, 4) }}</span>.</p>
            <p>Please wait while the request is being processed. You will be notified once the upgrade is completed.</p>
            <p>If you have any questions, feel free to contact your subdivision director or admin.</p>
            <a href="{{ url('/') }}" class="button">Visit Dashboard</a>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>Thank you for your dedication and hard work!</p>
            <p><a href="{{ url('/') }}">Visit Our Website</a></p>
        </div>
    </div>
</body>
</html>