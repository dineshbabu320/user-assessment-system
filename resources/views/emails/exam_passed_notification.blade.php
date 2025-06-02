<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Passed</title>
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
            background-color: #00bf16;
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
            background-color: #00bf16;
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
            <h1>Exam Notification</h1>
        </div>
        <div class="content">
            <p>Dear Admin/Subdivision Directors,</p>
            <p>The following user has passed an exam:</p>
            <p><strong>Name:</strong> {{ $student->name }}</p>
            <p><strong>CID:</strong> {{ $student->cid }}</p>
            <p><strong>Exam:</strong> {{ $exam->name }}</p>
            <p><strong>Attempt:</strong> {{ $attempt_number }}</p>
            <p><strong>Percentage:</strong> {{ $percentage }}%</p>
            <a href="{{ url('/') }}" class="button">Visit Dashboard</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} SEA-TEST. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
