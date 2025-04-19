<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Passed</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9fafb; /* Light background for light mode */
            color: #333333; /* Dark text for readability */
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff; /* White background for light mode */
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb; /* Light border for structure */
        }
        .header {
            background-color: #0ea5e9; /* Cyan color */
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #ffffff; /* White text for contrast */
        }
        .content {
            padding: 20px;
        }
        .content p {
            margin: 10px 0;
            line-height: 1.6;
            color: #333333; /* Dark text for readability */
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #0ea5e9; /* Cyan color */
            color: #ffffff !important; /* White text */
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }
        .button:hover {
            background-color: #0284c7; /* Darker cyan on hover */
        }
        .footer {
            background-color: #f3f4f6; /* Light gray background for footer */
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #6b7280; /* Gray text */
        }

        /* Dark Mode Styles */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0f172a; /* Dark background for dark mode */
                color: #ffffff; /* Light text for readability */
            }
            .container {
                background-color: #1e293b; /* Darker background for dark mode */
                border: 1px solid #334155; /* Dark border for structure */
            }
            .content p {
                color: #e2e8f0; /* Light text for readability */
            }
            .footer {
                background-color: #0f172a; /* Dark footer background */
                color: #94a3b8; /* Light gray text */
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
            <p>Dear Admin/Subdivision Directors</p>
            <p>The following user has successfully passed an exam:</p>
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>CID:</strong> {{ $user->cid }}</p>
            <p><strong>Exam:</strong> {{ $exam->name }}</p>
            <p><strong>Score:</strong> {{ $score }}</p>
            <a href="{{ url('/') }}" class="button">Visit Dashboard</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} SEA-TEST. All rights reserved.</p>
        </div>
    </div>
</body>
</html>