<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Inventory Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #198754;
        }

        .header h1 {
            font-size: 22pt;
            color: #198754;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #198754;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 9pt;
            font-weight: 600;
        }

        td {
            padding: 6px 6px;
            border-bottom: 1px solid #ddd;
            font-size: 9pt;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: 600;
            display: inline-block;
        }

        .status-in-stock {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-low-stock {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-out-stock {
            background-color: #f8d7da;
            color: #842029;
        }

        .status-valid {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-expiring {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-expired {
            background-color: #f8d7da;
            color: #842029;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }

        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #198754;
        }

        .summary p {
            margin: 5px 0;
            font-size: 9pt;
        }

        .summary strong {
            color: #198754;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Medicine Inventory Report</h1>
        <p>Generated on {{ date('F d, Y h:i A') }}</p>
    </div>

    <div class="summary">
        <p><strong>Total Medicines:</strong> {{ $medicines->count() }}</p>
        <p><strong>Low Stock Items:</strong> {{ $medicines->where('stock_status', 'Low Stock')->count() }}</p>
        <p><strong>Expiring Soon:</strong> {{ $medicines->where('expiry_status', 'Expiring Soon')->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 20%;">Medicine Name</th>
                <th style="width: 12%;">Category</th>
                <th style="width: 10%;">Dosage</th>
                <th style="width: 13%;">Age Range</th>
                <th style="width: 8%;">Stock</th>
                <th style="width: 12%;">Stock Status</th>
                <th style="width: 12%;">Expiry Status</th>
                <th style="width: 10%;">Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            @php
            function formatAgeRange($minMonths, $maxMonths)
            {
                if (is_null($minMonths) && is_null($maxMonths)) {
                    return 'All ages';
                }

                $minStr = '';
                $maxStr = '';

                if (!is_null($minMonths)) {
                    if ($minMonths < 12) {
                        $minStr = $minMonths . ' mo';
                    } else {
                        $years = floor($minMonths / 12);
                        $months = $minMonths % 12;
                        $minStr = $years . ' yr' . ($years == 1 ? '' : 's');
                        if ($months > 0) {
                            $minStr .= ' ' . $months . ' mo';
                        }
                    }
                }

                if (!is_null($maxMonths)) {
                    if ($maxMonths < 12) {
                        $maxStr = $maxMonths . ' mo';
                    } else {
                        $years = floor($maxMonths / 12);
                        $months = $maxMonths % 12;
                        $maxStr = $years . ' yr' . ($years == 1 ? '' : 's');
                        if ($months > 0) {
                            $maxStr .= ' ' . $months . ' mo';
                        }
                    }
                }

                if (is_null($minMonths)) {
                    return 'Up to ' . $maxStr;
                } elseif (is_null($maxMonths)) {
                    return $minStr . '+';
                } else {
                    return $minStr . ' - ' . $maxStr;
                }
            }
            @endphp

            @foreach($medicines as $index => $medicine)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $medicine->medicine_name }}</td>
                <td>{{ $medicine->category_name }}</td>
                <td>{{ $medicine->dosage }}</td>
                <td>{{ formatAgeRange($medicine->min_age_months, $medicine->max_age_months) }}</td>
                <td>{{ $medicine->stock }}</td>
                <td>
                    <span class="status-badge
                        @if ($medicine->stock_status === 'In Stock') status-in-stock
                        @elseif ($medicine->stock_status === 'Low Stock') status-low-stock
                        @else status-out-stock
                        @endif">
                        {{ $medicine->stock_status }}
                    </span>
                </td>
                <td>
                    <span class="status-badge
                        @if ($medicine->expiry_status === 'Valid') status-valid
                        @elseif ($medicine->expiry_status === 'Expiring Soon') status-expiring
                        @else status-expired
                        @endif">
                        {{ $medicine->expiry_status }}
                    </span>
                </td>
                <td>{{ date('M d, Y', strtotime($medicine->expiry_date)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was automatically generated by the Medicine Inventory Management System</p>
    </div>
</body>
</html>