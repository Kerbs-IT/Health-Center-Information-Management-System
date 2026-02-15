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

        /* =============================================
           REUSABLE LOGO HEADER SNIPPET
           Copy this block to any email view.
           Only change: header background gradient to
           match each email's color theme.
        ============================================= */
        .email-logo-header {
            background: #ffffff;
            border-bottom: 3px solid #28a745;
            /* change color per email theme */
            padding: 16px 24px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 0 -30px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .email-logo-header img {
            width: 52px;
            height: 52px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .email-logo-header .logo-text h4 {
            margin: 0;
            font-size: 12px;
            font-weight: 600;
            color: #555;
            line-height: 1.4;
        }

        .email-logo-header .logo-text h4:first-child {
            color: #28a745;
            /* change color per email theme */
            font-size: 13px;
        }

        /* ============================================= */
    </style>
</head>

<body>
    <div class="email-logo-header">
        <img src="https://hugoperezproperbhc.com/images/hugoperez_logo.png" alt="Barangay Hugo Perez Logo">
        <div class="logo-text">
            <h4>Barangay Hugo Perez Proper —</h4>
            <h4>Health Center Information Management System</h4>
        </div>
    </div>
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