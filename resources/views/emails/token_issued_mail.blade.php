<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Token Issued</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        .header {
            background-color: #0ea5e9;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #ffffff;
        }
        .content {
            padding: 20px;
        }
        .content p {
            margin: 10px 0;
            line-height: 1.6;
            color: #333333;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #0ea5e9;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }
        .button:hover {
            background-color: #0284c7;
        }
        .footer {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0f172a;
                color: #ffffff;
            }
            .container {
                background-color: #1e293b;
                border: 1px solid #334155;
            }
            .content p {
                color: #e2e8f0;
            }
            .footer {
                background-color: #0f172a;
                color: #94a3b8;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Token Issued</h1>
        </div>
        <div class="content">
            <p>Dear {{ $token->user->name }},</p>
            <p>We are pleased to inform you that a token has been issued for the <strong>{{ $token->exam->name }}</strong> exam.</p>
            <p>The token is valid until <strong>{{ $token->expires_at->format('F j, Y') }}</strong>. Please ensure that you complete the exam before the expiry date.</p>
            <p>If you have any questions or need assistance, feel free to contact us.</p>
            <a href="{{ route('exam.take', $token->exam->name) }}" class="button">Start Exam</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} SEA-TEST. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
