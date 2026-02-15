<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Account Created</title>
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
            border-top: none;
        }

        .credentials {
            background-color: #fff;
            border: 2px solid #4CAF50;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }

        .credential-item {
            margin: 10px 0;
            font-size: 16px;
        }

        .credential-label {
            font-weight: bold;
            color: #555;
        }

        .credential-value {
            color: #000;
            font-family: 'Courier New', monospace;
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-radius: 3px;
            display: inline-block;
            margin-left: 10px;
        }

        .password {
            font-size: 18px;
            font-weight: bold;
            color: #d32f2f;
        }

        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
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
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/hugoperez_logo.png'))) }}"
            alt="Barangay Hugo Perez Logo">
        <div class="logo-text">
            <h4>Barangay Hugo Perez Proper —</h4>
            <h4>Health Center Information Management System</h4>
        </div>
    </div>
    <div class="header">
        <h1>Welcome to Our Healthcare System</h1>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $user->full_name }}</strong>,</p>

        <p>Your patient account has been successfully created. You can now access our system using the credentials below:</p>

        <div class="credentials">
            <div class="credential-item">
                <span class="credential-label">Email:</span>
                <span class="credential-value">{{ $user->email }}</span>
            </div>
            <div class="credential-item">
                <span class="credential-label">Temporary Password:</span>
                <span class="credential-value password">{{ $temporaryPassword }}</span>
            </div>
        </div>

        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong>
            <ul>
                <li>This is a temporary password. Please change it after your first login.</li>
                <li>Do not share your password with anyone.</li>
                <li>Keep this email in a secure location or delete it after changing your password.</li>
            </ul>
        </div>

        <center>
            <a href="{{ config('app.url') }}/login" class="button">Login to Your Account</a>
        </center>

        <p>If you have any questions or need assistance, please contact our support team.</p>

        <p>Best regards,<br>
            <strong>Healthcare System Team</strong>
        </p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Healthcare System. All rights reserved.</p>
    </div>
</body>

</html>