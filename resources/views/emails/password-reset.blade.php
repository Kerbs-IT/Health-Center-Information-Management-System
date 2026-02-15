<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #198754;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }

        .password-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            border: 2px dashed #dee2e6;
            text-align: center;
            margin: 20px 0;
        }

        .password {
            font-size: 24px;
            color: #e74c3c;
            font-weight: bold;
            letter-spacing: 3px;
            font-family: 'Courier New', monospace;
        }

        .warning {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #999;
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
    <div class="container">
        <div class="email-logo-header">
            <img src="https://hugoperezproperbhc.com/images/hugoperez_logo.png" alt="Barangay Hugo Perez Logo">
            <div class="logo-text">
                <h4>Barangay Hugo Perez Proper —</h4>
                <h4>Health Center Information Management System</h4>
            </div>
        </div>
        <div class="header">
            <h2>Password Reset Notification</h2>
        </div>
        <div class="content">
            <p>Hello {{ $user->name ?? 'User' }},</p>

            <p>Your password has been reset by an administrator. Here is your new temporary password:</p>

            <div class="password-box">
                <div class="password">{{ $newPassword }}</div>
            </div>

            <div class="warning">
                <strong>⚠️ Important Security Notice:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Please change this password immediately after logging in</li>
                    <li>Do not share this password with anyone</li>
                    <li>This email contains sensitive information - please delete it after changing your password</li>
                </ul>
            </div>

            <p>If you did not request this password reset, please contact support immediately.</p>

            <p>Best regards,<br>{{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>