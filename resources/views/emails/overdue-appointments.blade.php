<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overdue Appointments Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* =============================================
           REUSABLE LOGO HEADER SNIPPET
           Copy this block to any email view.
           Only change: header background gradient to
           match each email's color theme.
        ============================================= */
        .email-logo-header {
            background: #ffffff;
            border-bottom: 3px solid #d32f2f;
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
            color: #c62828;
            /* change color per email theme */
            font-size: 13px;
        }

        /* ============================================= */

        .header {
            background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
            color: white;
            padding: 25px;
            margin: 0 -30px 25px -30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }

        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .alert-card {
            background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
        }

        .alert-card h2 {
            margin: 0;
            font-size: 48px;
            font-weight: bold;
        }

        .alert-card p {
            margin: 5px 0 0 0;
            font-size: 16px;
            opacity: 0.95;
        }

        .overdue-item {
            display: flex;
            align-items: center;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 5px solid #d32f2f;
            background-color: #ffebee;
            transition: transform 0.2s;
        }

        .overdue-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(211, 47, 47, 0.2);
        }

        .overdue-icon {
            font-size: 40px;
            margin-right: 20px;
            min-width: 50px;
            text-align: center;
        }

        .overdue-content {
            flex-grow: 1;
        }

        .overdue-label {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 5px;
            color: #c62828;
        }

        .overdue-count {
            font-size: 32px;
            font-weight: bold;
            color: #d32f2f;
        }

        .overdue-text {
            font-size: 14px;
            color: #b71c1c;
        }

        .warning-badge {
            display: inline-block;
            background-color: #ff9800;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .no-overdue {
            text-align: center;
            padding: 40px;
            background-color: #e8f5e9;
            border-radius: 8px;
            color: #2e7d32;
        }

        .no-overdue .icon {
            font-size: 64px;
            margin-bottom: 15px;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
            text-align: center;
        }

        .urgent-section {
            background-color: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .urgent-section h3 {
            margin: 0 0 10px 0;
            color: #e65100;
            font-size: 16px;
        }

        .urgent-section ul {
            margin: 0;
            padding-left: 20px;
        }

        .urgent-section li {
            color: #e65100;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="email-container">

        <!-- LOGO HEADER — copy this block to other email views -->
        <div class="email-logo-header">
            <img src="https://hugoperezproperbhc.com/images/hugoperez_logo.png" alt="Barangay Hugo Perez Logo">
            <div class="logo-text">
                <h4>Barangay Hugo Perez Proper —</h4>
                <h4>Health Center Information Management System</h4>
            </div>
        </div>
        <!-- END LOGO HEADER -->

        <div class="header">
            <div class="icon">⚠️</div>
            <h1>Overdue Appointments Alert</h1>
            <p>Action Required</p>
        </div>

        <p style="font-size: 18px; margin-bottom: 20px;">
            Hello <strong>{{ $data['staff_name'] }}</strong>, 👋
        </p>

        <p style="color: #d32f2f; font-weight: 600;">
            You have patients with overdue appointments that need immediate attention.
        </p>

        <!-- Total Overdue Summary -->
        <div class="alert-card">
            <h2>{{ $data['total_overdue'] }}</h2>
            <p>Total Overdue {{ $data['total_overdue'] === 1 ? 'Appointment' : 'Appointments' }}</p>
        </div>

        <!-- Overdue Breakdown -->
        @if($data['total_overdue'] > 0)
        @foreach($data['overdue_data'] as $overdue)
        @if($overdue['count'] > 0)
        <div class="overdue-item">
            <div class="overdue-icon">{{ $overdue['icon'] }}</div>
            <div class="overdue-content">
                <div class="overdue-label">
                    {{ $overdue['label'] }}
                    <span class="warning-badge">OVERDUE</span>
                </div>
                <div class="overdue-text">
                    <span class="overdue-count">{{ $overdue['count'] }}</span>
                    {{ $overdue['count'] === 1 ? 'patient has' : 'patients have' }} missed appointments
                </div>
            </div>
        </div>
        @endif
        @endforeach

        <!-- Urgent Action Items -->
        <div class="urgent-section">
            <h3>🔴 Immediate Action Required:</h3>
            <ul>
                <li>Contact overdue patients as soon as possible</li>
                <li>Reschedule missed appointments</li>
                <li>Document reasons for missed appointments</li>
                <li>Report to supervisor if unable to reach patients</li>
                <li>Update patient records with follow-up actions</li>
            </ul>
        </div>

        <center>
            <a href="{{ url('/') }}" class="button">View Overdue Patients Now</a>
        </center>
        @else
        <div class="no-overdue">
            <div class="icon">✅</div>
            <h3>All Clear!</h3>
            <p>No overdue appointments at this time.</p>
        </div>
        @endif

        <div class="footer">
            <p><strong>Note:</strong> Overdue patients may be at risk. Please prioritize follow-up.</p>
            <p>Regular updates help ensure continuity of care and patient safety.</p>
            <p style="margin-top: 15px;">This is an automated alert. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} Health Center Management System. All rights reserved.</p>
        </div>
    </div>
</body>

</html>