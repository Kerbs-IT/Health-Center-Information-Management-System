<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            background-color: #4CAF50;
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

        .code {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
            text-align: center;
            letter-spacing: 8px;
            padding: 20px;
            background-color: #fff;
            border: 2px dashed #4CAF50;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Email Verification</h2>
    </div>
    <div class="content">
        <p>Hello {{ $userName }},</p>
        <p>Welcome to our Health Center Information Management System!</p>
        <p>Your verification code is:</p>

        <div class="code">{{ $code }}</div>

        <p><strong>Important:</strong></p>
        <ul>
            <li>This code will expire in <strong>3 minutes</strong></li>
            <li>Enter this code on the verification page</li>
            <li>You have <strong>5 attempts</strong> to enter the correct code</li>
        </ul>

        <p>If you didn't request this code, please ignore this email.</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} Health Center IMS. All rights reserved.</p>
    </div>
</body>

</html>