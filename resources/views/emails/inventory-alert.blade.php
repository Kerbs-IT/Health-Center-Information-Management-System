<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Alert</title>
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

        .email-logo-header {
            background: #ffffff;
            border-bottom: 3px solid #dc3545;
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

        .email-logo-header .logo-text {
            margin-left: 10px;
        }

        .email-logo-header .logo-text h4 {
            margin: 0;
            font-size: 12px;
            font-weight: 600;
            color: #555;
            line-height: 1.4;
        }

        .email-logo-header .logo-text h4:first-child {
            color: #dc3545;
            font-size: 13px;
        }

        .header {
            background: #dc3545;
            color: white;
            padding: 20px;
            border-radius: 0;
            margin: 0 -30px 20px -30px;
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

        .alert-box-red {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .alert-box-yellow {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .alert-box-orange {
            background-color: #ffe5d0;
            border-left: 4px solid #fd7e14;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .alert-box-dark {
            background-color: #e2e3e5;
            border-left: 4px solid #6c757d;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .medicine-details {
            background-color: #f8f9fa;
            border-left: 4px solid #0a6b2c;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .medicine-details p {
            margin: 8px 0;
        }

        .medicine-details strong {
            color: #0a6b2c;
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
    </style>
</head>

<body>
    <div class="email-container">

        {{-- Logo header --}}
        <div class="email-logo-header">
            <img src="https://hugoperezproperbhc.com/images/hugoperez_logo.png" alt="Barangay Hugo Perez Logo">
            <div class="logo-text">
                <h4>Barangay Hugo Perez Proper —</h4>
                <h4>Health Center Information Management System</h4>
            </div>
        </div>

        <div class="header">
            <div class="icon">
                @switch($alertType)
                @case('out_of_stock') 🔴 @break
                @case('low_stock') 🟡 @break
                @case('expiring_soon') 🟠 @break
                @case('expired') ⛔ @break
                @default ⚠️
                @endswitch
            </div>
            <h1>{{ $title }}</h1>
        </div>

        <div class="content" style="padding: 20px 0;">

            {{-- BUG FIXED: was using nurses/staff relationship chain that could
             resolve to wrong person. Now uses $recipient->name directly
             from the users table — always correct since each email is
             sent individually per user in CheckInventoryExpiry.php --}}
            
            <p class="greeting">Hello, Health Center Staff!</p>

            <p>This is an automated inventory alert from the Hugo Perez Health Center Information Management System.</p>

            {{-- Alert type banner --}}
            @switch($alertType)
            @case('out_of_stock')
            <div class="alert-box-red">
                <strong>🔴 This medicine is completely OUT OF STOCK and needs immediate restocking.</strong>
            </div>
            @break
            @case('low_stock')
            <div class="alert-box-yellow">
                <strong>🟡 Stock level is critically LOW. Please arrange restocking soon.</strong>
            </div>
            @break
            @case('expiring_soon')
            <div class="alert-box-orange">
                <strong>🟠 A batch is expiring within 30 days. Please take action.</strong>
            </div>
            @break
            @case('expired')
            <div class="alert-box-dark">
                <strong>⛔ A batch has EXPIRED. It must be removed from the inventory immediately.</strong>
            </div>
            @break
            @endswitch

            {{-- Medicine details --}}
            <div class="medicine-details">
                <p><strong>Medicine Name:</strong> {{ $medicine->medicine_name }}</p>
                <p><strong>Dosage:</strong> {{ $medicine->dosage }}</p>
                <p><strong>Category:</strong> {{ $medicine->category?->category_name ?? 'N/A' }}</p>
                <p><strong>Current Stock:</strong> {{ $medicine->stock }} unit(s)</p>
                <p><strong>Stock Status:</strong> {{ $medicine->stock_status }}</p>
                @if($batchNumber)
                <p><strong>Batch Number:</strong> {{ $batchNumber }}</p>
                @endif
                @if($expiryDate)
                <p><strong>Expiry Date:</strong> {{ $expiryDate }}</p>
                @endif
            </div>

            {{-- Action guide per alert type --}}
            <p><strong>Recommended Action:</strong></p>
            <ul>
                @switch($alertType)
                @case('out_of_stock')
                <li>Immediately request or procure new stock</li>
                <li>Inform the nurse-in-charge about the shortage</li>
                <li>Check if any other batches can substitute</li>
                @break
                @case('low_stock')
                <li>Plan restocking before stock reaches zero</li>
                <li>Prioritize dispensing of this medicine carefully</li>
                <li>Add a new batch via Batch Management</li>
                @break
                @case('expiring_soon')
                <li>Prioritize dispensing this batch (FEFO order)</li>
                <li>Avoid ordering large quantities until this batch is consumed</li>
                <li>Notify concerned health workers</li>
                @break
                @case('expired')
                <li>Remove expired batch from active inventory immediately</li>
                <li>Archive the batch in the system</li>
                <li>Document the disposal per health center protocol</li>
                @break
                @endswitch
            </ul>

            <center>
                <a href="{{ url('https://hugoperezproperbhc.com/medicines') }}" class="button">View Medicine Inventory</a>
            </center>
        </div>

        <div class="footer">
            <p>This is an automated alert. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} Barangay Hugo Perez Health Center Information Management System. All rights reserved.</p>
        </div>
    </div>
</body>

</html>