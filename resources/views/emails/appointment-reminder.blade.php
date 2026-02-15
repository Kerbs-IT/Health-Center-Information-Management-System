<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Reminder</title>
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
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 20px -30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .content {
            padding: 20px 0;
        }

        .appointment-details {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .appointment-details p {
            margin: 8px 0;
        }

        .appointment-details strong {
            color: #667eea;
        }

        .alert-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .success-box {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #0a6b2c;
            color: white !important;
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

        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
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
        <!-- LOGO HEADER — copy this block to other email views -->
        <div class="email-logo-header">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/hugoperez_logo.png'))) }}"
                alt="Barangay Hugo Perez Logo">
            <div class="logo-text">
                <h4>Barangay Hugo Perez Proper —</h4>
                <h4>Health Center Information Management System</h4>
            </div>
        </div>
        <!-- END LOGO HEADER -->
        <div class="header">
            <div class="icon">
                @if($data['type'] === 'vaccination_completion')
                ✅
                @else
                📅
                @endif
            </div>
            <h1>Appointment Reminder</h1>
        </div>

        <div class="content">
            <p class="greeting">Hello, {{ $data['patient_name'] }}!</p>

            @if($data['type'] === 'vaccination_completion')
            <p>Congratulations! You have completed your vaccination series.</p>

            <div class="success-box">
                <strong>🎉 Vaccination Completion Checkup - TOMORROW</strong>
            </div>

            <div class="appointment-details">
                <p><strong>Type:</strong> {{ $data['appointment_type'] }}</p>
                <p><strong>Date:</strong> {{ $data['appointment_date'] }}</p>
                <p><strong>Completed Vaccines:</strong> {{ $data['completed_vaccines'] }}</p>
            </div>

            <p><strong>Purpose of This Checkup:</strong></p>
            <ul>
                <li>Verify all vaccinations are complete and properly recorded</li>
                <li>Discuss any side effects or concerns you may have</li>
                <li>Update your official vaccination records</li>
                <li>Determine if additional vaccinations are needed</li>
                <li>Receive your completed vaccination certificate</li>
            </ul>

            <p><strong>Please Bring:</strong></p>
            <ul>
                <li>Your vaccination card</li>
                <li>Valid ID</li>
                <li>Any medical records related to the vaccinations</li>
            </ul>

            @else
            <p>This is a friendly reminder about your upcoming appointment.</p>

            <div class="alert-box">
                <strong>⏰ Your appointment is scheduled for TOMORROW</strong>
            </div>

            <div class="appointment-details">
                <p><strong>Type:</strong> {{ $data['appointment_type'] }}</p>
                <p><strong>Date:</strong> {{ $data['appointment_date'] }}</p>

                @if($data['type'] === 'vaccination')
                <p><strong>Vaccine:</strong> {{ $data['vaccine_type'] }}</p>
                <p><strong>Dose:</strong> Dose {{ $data['dose_number'] }}</p>
                @endif
            </div>

            <p><strong>Important Reminders:</strong></p>
            <ul>
                <li>Please arrive 10-15 minutes before your appointment</li>
                <li>Bring your health records and valid ID</li>
                @if($data['type'] === 'vaccination')
                <li>Bring your vaccination card</li>
                @endif
                @if($data['type'] === 'prenatal')
                <li>Bring your prenatal booklet</li>
                @endif
                <li>Wear a face mask and observe health protocols</li>
            </ul>
            @endif

            <p>If you need to reschedule or have any questions, please contact the health center as soon as possible.</p>

            <center>
                <a href="{{ url('/') }}" class="button btn-success text-white">Visit Health Center Portal</a>
            </center>
        </div>

        <div class="footer">
            <p>This is an automated reminder. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} Health Center Management System. All rights reserved.</p>
        </div>
    </div>
</body>

</html>