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
            color: #ffffff !important; /* Ensure text color is white */
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            text-decoration: none; /* Remove underline */
        }
        .button a {
            color: inherit !important; /* Inherit color from parent */
            text-decoration: none; /* Remove underline */
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
            <p>Dear Admin,</p>
            <p>A <span class="highlight">Rating Upgrade Request</span> has been initiated for the following user:</p>
            <p><strong>Name:</strong> <span class="highlight">{{ $token->user->name }}</span></p>
            <p><strong>CID:</strong> <span class="highlight">{{ $token->user->cid }}</span></p>
            <p><strong>Requested Rating:</strong> <span class="highlight">{{ substr($token->id, 4) }}</span></p>
            <p>Please log in to the system to review and process this request.</p>
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