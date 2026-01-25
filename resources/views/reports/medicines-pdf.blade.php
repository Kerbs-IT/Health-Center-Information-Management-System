<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Medicine Inventory Report</title>
    <style>
        @page {
            margin: 20mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 20mm 15mm;
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #198754;
        }

        .header h1 {
            font-size: 20pt;
            color: #198754;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 9pt;
            color: #666;
        }

        .summary {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f8f9fa;
            border-left: 4px solid #198754;
        }

        .summary p {
            margin: 3px 0;
            font-size: 9pt;
        }

        .summary strong {
            color: #198754;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            font-family: 'Arial';
            background-color: #198754;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-size: 8pt;
            font-weight: 600;
        }

        td {
            padding: 5px 4px;
            border-bottom: 1px solid #ddd;
            font-size: 8pt;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
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
            margin-top: 20px;
            padding-top: 8px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 7pt;
            color: #666;
        }

        /* Column widths */
        .col-no { width: 4%; }
        .col-name { width: 18%; }
        .col-category { width: 12%; }
        .col-dosage { width: 10%; }
        .col-age { width: 13%; }
        .col-stock { width: 7%; }
        .col-stock-status { width: 11%; }
        .col-expiry-status { width: 11%; }
        .col-expiry-date { width: 10%; }


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
        <p><strong>Out of Stock Items:</strong> {{ $medicines->where('stock_status', 'Out of Stock')->count() }}</p>
        <p><strong>Expiring Soon:</strong> {{ $medicines->where('expiry_status', 'Expiring Soon')->count() }}</p>
        <p><strong>Expired Items:</strong> {{ $medicines->where('expiry_status', 'Expired')->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th class="col-name">Medicine Name</th>
                <th class="col-category">Category</th>
                <th class="col-dosage">Dosage</th>
                <th class="col-age">Age Range</th>
                <th class="col-stock">Stock</th>
                <th class="col-stock-status">Stock Status</th>
                <th class="col-expiry-status">Expiry Status</th>
                <th class="col-expiry-date">Expiry Date</th>
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
                <td class="col-no">{{ $index + 1 }}</td>
                <td class="col-name">{{ $medicine->medicine_name }}</td>
                <td class="col-category">{{ $medicine->category->category_name ?? 'N/A' }}</td>
                <td class="col-dosage">{{ $medicine->dosage }}</td>
                <td class="col-age">{{ formatAgeRange($medicine->min_age_months, $medicine->max_age_months) }}</td>
                <td class="col-stock">{{ $medicine->stock }}</td>
                <td class="col-stock-status">
                    <span class="status-badge
                        @if ($medicine->stock_status === 'In Stock') status-in-stock
                        @elseif ($medicine->stock_status === 'Low Stock') status-low-stock
                        @else status-out-stock
                        @endif">
                        {{ $medicine->stock_status }}
                    </span>
                </td>
                <td class="col-expiry-status">
                    <span class="status-badge
                        @if ($medicine->expiry_status === 'Valid') status-valid
                        @elseif ($medicine->expiry_status === 'Expiring Soon') status-expiring
                        @else status-expired
                        @endif">
                        {{ $medicine->expiry_status }}
                    </span>
                </td>
                <td class="col-expiry-date">{{ date('M d, Y', strtotime($medicine->expiry_date)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was automatically generated by the Health Center Information Management System</p>
    </div>
</body>
</html>