<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Schedule</title>
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

        .header {
            background: #0a6b2c;
            color: white;
            padding: 25px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 25px -30px;
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

        .summary-card {
            background: #0a6b2c;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
        }

        .summary-card h2 {
            margin: 0;
            font-size: 48px;
            font-weight: bold;
        }

        .summary-card p {
            margin: 5px 0 0 0;
            font-size: 16px;
            opacity: 0.95;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 5px solid;
            background-color: #f8f9fa;
            transition: transform 0.2s;
        }

        .schedule-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .schedule-icon {
            font-size: 40px;
            margin-right: 20px;
            min-width: 50px;
            text-align: center;
        }

        .schedule-content {
            flex-grow: 1;
        }

        .schedule-label {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 5px;
            color: #333;
        }

        .schedule-count {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .schedule-text {
            font-size: 14px;
            color: #666;
        }

        .no-appointments {
            text-align: center;
            padding: 40px;
            background-color: #f8f9fa;
            border-radius: 8px;
            color: #666;
        }

        .no-appointments .icon {
            font-size: 64px;
            margin-bottom: 15px;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #0a6b2c;
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

        .tips-section {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .tips-section h3 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 16px;
        }

        .tips-section ul {
            margin: 0;
            padding-left: 20px;
        }

        .tips-section li {
            color: #856404;
            margin-bottom: 5px;
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
    <div class="email-container">
        <div class="email-logo-header">
            <img src="https://hugoperezproperbhc.com/images/hugoperez_logo.png" alt="Barangay Hugo Perez Logo">
            <div class="logo-text">
                <h4>Barangay Hugo Perez Proper —</h4>
                <h4>Health Center Information Management System</h4>
            </div>
        </div>
        <div class="header">
            <div class="icon">📋</div>
            <h1>Your Daily Schedule</h1>
            <p>{{ $data['date'] }}</p>
        </div>

        <p style="font-size: 18px; margin-bottom: 20px;">
            Good morning, <strong>{{ $data['staff_name'] }}</strong>! 👋
        </p>

        <p>Here's your schedule for today:</p>

        <!-- Total Appointments Summary -->
        <div class="summary-card">
            <h2>{{ $data['total_appointments'] }}</h2>
            <p>Total {{ $data['total_appointments'] === 1 ? 'Appointment' : 'Appointments' }} Scheduled</p>
        </div>

        <!-- Schedule Breakdown -->
        @if($data['total_appointments'] > 0)
        @foreach($data['schedule_data'] as $schedule)
        @if($schedule['count'] > 0)
        <div class="schedule-item" style="border-left-color: {{ $schedule['color'] }}">
            <div class="schedule-icon">{{ $schedule['icon'] }}</div>
            <div class="schedule-content">
                <div class="schedule-label">{{ $schedule['label'] }}</div>
                <div class="schedule-text">
                    <span class="schedule-count">{{ $schedule['count'] }}</span>
                    {{ $schedule['count'] === 1 ? 'patient' : 'patients' }}
                </div>
            </div>
        </div>
        @endif
        @endforeach

        <!-- Preparation Tips -->
        <div class="tips-section">
            <h3>📌 Preparation Tips:</h3>
            <ul>
                <li>Review patient records before arrival</li>
                <li>Prepare necessary supplies and equipment</li>
                <li>Check for any special requirements or allergies</li>
                <li>Arrive 15 minutes early to set up</li>
            </ul>
        </div>

        <center>
            <a href="{{ url('/') }}" class="button">Go to Health Center Portal</a>
        </center>
        @else
        <div class="no-appointments">
            <div class="icon">🎉</div>
            <h3>No Appointments Today!</h3>
            <p>You have no scheduled appointments for today. Enjoy a lighter day!</p>
        </div>
        @endif

        <div class="footer">
            <p><strong>Important:</strong> This schedule reflects appointments as of 5:00 AM today.</p>
            <p>Please check the portal regularly for any last-minute changes or walk-in patients.</p>
            <p style="margin-top: 15px;">This is an automated notification. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} Health Center Management System. All rights reserved.</p>
        </div>
    </div>
</body>

</html>