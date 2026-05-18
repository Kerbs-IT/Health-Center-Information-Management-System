<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Daily Inventory Report</title>
    <style>
        /* ── Reset ── */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            display: block;
            outline: none;
            -ms-interpolation-mode: bicubic;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            width: 100%;
        }

        /* ── Outer wrapper ── */
        .email-wrapper {
            width: 100%;
            background-color: #f4f4f4;
            padding: 20px 10px;
        }

        /* ── Container ── */
        .email-container {
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            max-width: 640px;
            width: 100%;
            margin: 0 auto;
        }

        /* ── Logo header ── */
        .logo-header {
            background: #ffffff;
            border-bottom: 3px solid #0a6b2c;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-header img {
            width: 52px;
            height: 52px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .logo-text {
            margin-left: 10px;
        }

        .logo-text h4 {
            margin: 0;
            font-size: 12px;
            font-weight: 600;
            color: #555555;
            line-height: 1.5;
        }

        .logo-text h4:first-child {
            color: #0a6b2c;
            font-size: 13px;
        }

        /* ── Banner ── */
        .banner {
            background: linear-gradient(135deg, #0a6b2c 0%, #1a8c40 100%);
            color: #ffffff;
            padding: 24px 20px;
            text-align: center;
        }

        .banner .icon {
            font-size: 44px;
            margin-bottom: 8px;
            line-height: 1;
        }

        .banner h1 {
            font-size: 22px;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .banner p {
            font-size: 13px;
            opacity: 0.85;
        }

        /* ── Body ── */
        .body {
            padding: 24px;
        }

        .greeting {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .intro {
            font-size: 14px;
            color: #555555;
            margin-bottom: 24px;
            border-left: 4px solid #0a6b2c;
            background: #f0faf4;
            padding: 12px 12px 12px 16px;
            border-radius: 4px;
        }

        /* ── Reporting window badge ── */
        .window-badge {
            background: #e8f5e9;
            border: 1px solid #a5d6a7;
            border-radius: 6px;
            padding: 10px 16px;
            margin: 0 0 24px;
            font-size: 13px;
            color: #2e7d32;
            text-align: center;
            word-break: break-word;
        }

        .window-badge strong {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #388e3c;
            margin-bottom: 4px;
        }

        /* ── Summary chips — TABLE-BASED for full email client compatibility ── */
        .chip-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 20px;
        }

        .chip-table td {
            width: 25%;
            border-radius: 8px;
            padding: 16px 8px;
            text-align: center;
            vertical-align: middle;
        }

        .chip-count {
            font-size: 30px;
            font-weight: bold;
            line-height: 1;
            display: block;
        }

        .chip-label {
            font-size: 11px;
            margin-top: 6px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
        }

        .chip-red {
            background: #fde8e8;
            color: #c0392b;
        }

        .chip-yellow {
            background: #fff8e1;
            color: #b7860b;
        }

        .chip-orange {
            background: #fff3e0;
            color: #c0540a;
        }

        .chip-gray {
            background: #e8e8e8;
            color: #555555;
        }

        /* ── NEW badge ── */
        .badge-new {
            display: inline-block;
            background: #0a6b2c;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 2px 6px;
            border-radius: 10px;
            vertical-align: middle;
            margin-left: 5px;
        }

        /* ── Section ── */
        .section {
            margin-bottom: 28px;
        }

        .section-header {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            padding: 10px 14px;
            border-radius: 6px 6px 0 0;
            font-weight: bold;
            font-size: 14px;
            line-height: 1.4;
        }

        .section-header span {
            font-weight: normal;
            font-size: 12px;
        }

        .section-header-red {
            background: #fde8e8;
            color: #c0392b;
            border-left: 4px solid #dc3545;
        }

        .section-header-yellow {
            background: #fff8e1;
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .section-header-orange {
            background: #fff3e0;
            color: #c0540a;
            border-left: 4px solid #fd7e14;
        }

        .section-header-gray {
            background: #e8e8e8;
            color: #555555;
            border-left: 4px solid #6c757d;
        }

        /* ── Responsive table wrapper ── */
        .table-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .medicine-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            min-width: 400px;
        }

        .medicine-table th {
            background: #f8f9fa;
            padding: 8px 12px;
            text-align: left;
            font-weight: 600;
            color: #555555;
            border-bottom: 2px solid #dee2e6;
            white-space: nowrap;
        }

        .medicine-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
            word-break: break-word;
        }

        .medicine-table tr:last-child td {
            border-bottom: none;
        }

        /* ── Inline status badges ── */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-red {
            background: #fde8e8;
            color: #c0392b;
        }

        .badge-yellow {
            background: #fff8e1;
            color: #856404;
        }

        .badge-orange {
            background: #fff3e0;
            color: #c0540a;
        }

        .badge-gray {
            background: #e8e8e8;
            color: #555555;
        }

        /* ── See-more & empty state ── */
        .see-more {
            text-align: center;
            padding: 10px 14px;
            font-size: 12px;
            color: #777777;
            background: #fafafa;
            border-top: 1px dashed #e0e0e0;
            border-radius: 0 0 6px 6px;
        }

        .see-more a {
            color: #0a6b2c;
            font-weight: 600;
            text-decoration: none;
        }

        .empty-state {
            text-align: center;
            padding: 14px;
            color: #aaaaaa;
            font-size: 13px;
            background: #fafafa;
            border-radius: 0 0 6px 6px;
        }

        /* ── All-clear box ── */
        .all-clear-box {
            text-align: center;
            padding: 20px;
            background: #f0faf4;
            border: 1px solid #a5d6a7;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .all-clear-box .all-clear-icon {
            font-size: 36px;
            margin-bottom: 8px;
        }

        .all-clear-box .all-clear-title {
            color: #2e7d32;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .all-clear-box .all-clear-sub {
            color: #555555;
            font-size: 13px;
        }

        /* ── CTA button ── */
        .cta {
            text-align: center;
            margin: 28px 0 16px;
        }

        .cta a {
            display: inline-block;
            padding: 13px 36px;
            background: #0a6b2c;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 15px;
        }

        /* ── Footer ── */
        .footer {
            padding: 16px 24px;
            border-top: 1px solid #eeeeee;
            font-size: 11px;
            color: #999999;
            text-align: center;
            line-height: 1.6;
        }

        .footer p {
            margin: 3px 0;
        }

        /* ══════════════════════════════════
           MOBILE BREAKPOINT  (≤ 480px)
        ══════════════════════════════════ */
        @media only screen and (max-width: 480px) {

            .email-wrapper {
                padding: 0;
            }

            .email-container {
                border-radius: 0;
                box-shadow: none;
            }

            .logo-header {
                padding: 12px 14px;
                gap: 10px;
                flex-wrap: wrap;
            }

            .logo-header img {
                width: 40px;
                height: 40px;
            }

            .logo-text h4 {
                font-size: 11px;
            }

            .logo-text h4:first-child {
                font-size: 12px;
            }

            .banner {
                padding: 20px 14px;
            }

            .banner .icon {
                font-size: 34px;
            }

            .banner h1 {
                font-size: 18px;
            }

            .banner p {
                font-size: 12px;
            }

            .body {
                padding: 16px 14px;
            }

            .greeting {
                font-size: 15px;
            }

            .intro {
                font-size: 13px;
                padding: 10px 10px 10px 14px;
            }

            .window-badge {
                font-size: 12px;
                padding: 10px 12px;
            }

            /* Chips — shrink font/padding on small screens, still 1 row */
            .chip-table {
                border-spacing: 5px;
            }

            .chip-table td {
                padding: 12px 4px;
            }

            .chip-count {
                font-size: 20px;
            }

            .chip-label {
                font-size: 9px;
                letter-spacing: 0;
                margin-top: 4px;
            }

            .section-header {
                font-size: 13px;
            }

            .table-wrap {
                border-radius: 0 0 6px 6px;
                box-shadow: inset -6px 0 8px -6px rgba(0, 0, 0, 0.12);
            }

            .medicine-table th,
            .medicine-table td {
                padding: 8px 10px;
                font-size: 12px;
            }

            .cta a {
                display: block;
                width: 100%;
                text-align: center;
                padding: 14px 16px;
                font-size: 14px;
            }

            .footer {
                padding: 14px;
            }
        }

        /* Extra-small screens (≤ 360px) */
        @media only screen and (max-width: 360px) {
            .chip-count {
                font-size: 16px;
            }

            .chip-label {
                font-size: 8px;
            }

            .chip-table td {
                padding: 10px 2px;
            }

            .banner h1 {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>

    @php
    $totalIssues = $outOfStock->count() + $lowStock->count() + $expiringSoon->count() + $expired->count();
    $limit = 5;
    @endphp

    <div class="email-wrapper">
        <div class="email-container">

            {{-- ── Logo header ── --}}
            <div class="logo-header">
                <img src="https://hugoperezproperbhc.com/images/hugoperez_logo.png"
                    alt="Barangay Hugo Perez Logo" width="52" height="52">
                <div class="logo-text">
                    <h4>Barangay Hugo Perez Proper —</h4>
                    <h4>Health Center Information Management System</h4>
                </div>
            </div>

            {{-- ── Banner ── --}}
            <div class="banner">
                <div class="icon">{{ $totalIssues > 0 ? '📋' : '✅' }}</div>
                <h1>Daily Inventory Report</h1>
                <p>{{ now()->format('l, F d, Y') }} · Generated at 8:00 AM</p>
            </div>

            <div class="body">

                {{-- Greeting --}}
                <p class="greeting">
                    Good morning, {{ $recipient->full_name }}!
                </p>

                <div class="intro">
                    This is your automated daily inventory summary from the Hugo Perez Health Center Information Management System.
                    Please review and take action on the items listed below.
                </div>

                {{-- Reporting window --}}
                <div class="window-badge">
                    <strong style="color:white;"> Reporting Window</strong>
                    {{ $windowStart->format('M d, Y — h:i A') }} &nbsp;<br>&nbsp; {{ $windowEnd->format('M d, Y — h:i A') }}
                    &nbsp;<br>&nbsp; Items newly flagged in this period are marked <span class="badge-new">new</span>
                </div>

                {{-- ══ Summary chips — HTML table (works in Gmail, Outlook, Apple Mail) ══ --}}
                <table class="chip-table" role="presentation" cellpadding="0" cellspacing="8" width="100%">
                    <tr>
                        <td class="chip-red">
                            <span class="chip-count">{{ $outOfStock->count() }}</span>
                            <span class="chip-label">Out of Stock</span>
                        </td>
                        <td class="chip-yellow">
                            <span class="chip-count">{{ $lowStock->count() }}</span>
                            <span class="chip-label">Low Stock</span>
                        </td>
                        <td class="chip-orange">
                            <span class="chip-count">{{ $expiringSoon->count() }}</span>
                            <span class="chip-label">Expiring Soon</span>
                        </td>
                        <td class="chip-gray">
                            <span class="chip-count">{{ $expired->count() }}</span>
                            <span class="chip-label">Expired</span>
                        </td>
                    </tr>
                </table>

                {{-- All-clear box — only shown when nothing was flagged --}}
                @if($totalIssues === 0)
                <div class="all-clear-box">
                    <div class="all-clear-icon">✅</div>
                    <p class="all-clear-title">All Clear!</p>
                    <p class="all-clear-sub">No inventory issues were flagged in the last 24 hours. Keep it up!</p>
                </div>
                @endif

                {{-- ══ SECTION 1: OUT OF STOCK ══ --}}
                <div class="section">
                    <div class="section-header section-header-red">
                        🔴 Out of Stock
                        <span>({{ $outOfStock->count() }} medicine{{ $outOfStock->count() != 1 ? 's' : '' }} newly flagged)</span>
                    </div>
                    @if($outOfStock->isNotEmpty())
                    <div class="table-wrap">
                        <table class="medicine-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Medicine Name</th>
                                    <th>Dosage</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outOfStock->take($limit) as $i => $med)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><strong>{{ $med->medicine_name }}</strong> <span class="badge-new">new</span></td>
                                    <td>{{ $med->dosage }}</td>
                                    <td>{{ $med->category?->category_name ?? '—' }}</td>
                                    <td><span class="badge badge-red">0 units</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($outOfStock->count() > $limit)
                    <div class="see-more">
                        ⚠️ Showing {{ $limit }} of {{ $outOfStock->count() }} out-of-stock medicines.
                        <a href="https://hugoperezproperbhc.com/medicines">Log in to see the full list →</a>
                    </div>
                    @endif
                    @else
                    <div class="empty-state">✅ No newly out-of-stock medicines in this period.</div>
                    @endif
                </div>

                {{-- ══ SECTION 2: LOW STOCK ══ --}}
                <div class="section">
                    <div class="section-header section-header-yellow">
                        🟡 Low Stock
                        <span>({{ $lowStock->count() }} medicine{{ $lowStock->count() != 1 ? 's' : '' }} newly flagged)</span>
                    </div>
                    @if($lowStock->isNotEmpty())
                    <div class="table-wrap">
                        <table class="medicine-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Medicine Name</th>
                                    <th>Dosage</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStock->take($limit) as $i => $med)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><strong>{{ $med->medicine_name }}</strong> <span class="badge-new">new</span></td>
                                    <td>{{ $med->dosage }}</td>
                                    <td>{{ $med->category?->category_name ?? '—' }}</td>
                                    <td><span class="badge badge-yellow">{{ $med->stock }} unit{{ $med->stock != 1 ? 's' : '' }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($lowStock->count() > $limit)
                    <div class="see-more">
                        ⚠️ Showing {{ $limit }} of {{ $lowStock->count() }} low-stock medicines.
                        <a href="https://hugoperezproperbhc.com/medicines">Log in to see the full list →</a>
                    </div>
                    @endif
                    @else
                    <div class="empty-state">✅ No newly low-stock medicines in this period.</div>
                    @endif
                </div>

                {{-- ══ SECTION 3: EXPIRING SOON ══ --}}
                <div class="section">
                    <div class="section-header section-header-orange">
                        🟠 Expiring Soon (within 30 days)
                        <span>({{ $expiringSoon->count() }} batch{{ $expiringSoon->count() != 1 ? 'es' : '' }} newly flagged)</span>
                    </div>
                    @if($expiringSoon->isNotEmpty())
                    <div class="table-wrap">
                        <table class="medicine-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Medicine Name</th>
                                    <th>Dosage</th>
                                    <th>Batch No.</th>
                                    <th>Expiry Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expiringSoon->take($limit) as $i => $batch)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><strong>{{ $batch->medicine?->medicine_name ?? '—' }}</strong> <span class="badge-new">new</span></td>
                                    <td>{{ $batch->medicine?->dosage ?? '—' }}</td>
                                    <td>{{ $batch->batch_number }}</td>
                                    <td><span class="badge badge-orange">{{ $batch->expiry_date->format('M d, Y') }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($expiringSoon->count() > $limit)
                    <div class="see-more">
                        ⚠️ Showing {{ $limit }} of {{ $expiringSoon->count() }} expiring batches.
                        <a href="https://hugoperezproperbhc.com/medicines">Log in to see the full list →</a>
                    </div>
                    @endif
                    @else
                    <div class="empty-state">✅ No batches newly flagged as expiring soon in this period.</div>
                    @endif
                </div>

                {{-- ══ SECTION 4: EXPIRED ══ --}}
                <div class="section">
                    <div class="section-header section-header-gray">
                        ⛔ Expired Medicines
                        <span>({{ $expired->count() }} batch{{ $expired->count() != 1 ? 'es' : '' }} newly flagged)</span>
                    </div>
                    @if($expired->isNotEmpty())
                    <div class="table-wrap">
                        <table class="medicine-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Medicine Name</th>
                                    <th>Dosage</th>
                                    <th>Batch No.</th>
                                    <th>Expired On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expired->take($limit) as $i => $batch)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><strong>{{ $batch->medicine?->medicine_name ?? '—' }}</strong> <span class="badge-new">new</span></td>
                                    <td>{{ $batch->medicine?->dosage ?? '—' }}</td>
                                    <td>{{ $batch->batch_number }}</td>
                                    <td><span class="badge badge-gray">{{ $batch->expiry_date->format('M d, Y') }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($expired->count() > $limit)
                    <div class="see-more">
                        ⚠️ Showing {{ $limit }} of {{ $expired->count() }} expired batches.
                        <a href="https://hugoperezproperbhc.com/medicines">Log in to see the full list →</a>
                    </div>
                    @endif
                    @else
                    <div class="empty-state">✅ No newly expired batches in this period.</div>
                    @endif
                </div>

                {{-- CTA --}}
                <div class="cta">
                    <a href="https://hugoperezproperbhc.com/medicines">📦 Go to Medicine Inventory</a>
                </div>

            </div>{{-- /body --}}

            <div class="footer">
                <p>This is an automated daily report. Please do not reply to this email.</p>
                <p>© {{ date('Y') }} Barangay Hugo Perez Health Center Information Management System. All rights reserved.</p>
            </div>

        </div>
    </div>
</body>

</html>