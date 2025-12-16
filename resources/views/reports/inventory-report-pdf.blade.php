<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report - {{ date('Y-m-d') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 3px solid #10b981;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #000;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .summary {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .summary-row {
            display: table-row;
        }

        .summary-cell {
            display: table-cell;
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }

        .summary-cell strong {
            display: block;
            font-size: 20px;
            color: #10b981;
            margin-bottom: 5px;
        }

        .summary-cell span {
            font-size: 9px;
            color: #000;
            text-transform: uppercase;
            font-weight: bold;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding: 8px;
            background: #10b981;
            color: white;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th {
            background: #10b981;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #0ea572;
        }

        table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
            color: #000;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 2px solid #10b981;
            font-size: 9px;
            color: #666;
        }

        @media print {
            body { padding: 10px; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <h1>INVENTORY REPORT</h1>
        <p>Generated: {{ $generatedDate }}</p>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="summary">
        <div class="summary-row">
            <div class="summary-cell">
                <strong>{{ $totalMedicines }}</strong>
                <span>Total Medicines</span>
            </div>
            <div class="summary-cell">
                <strong>{{ $totalRequests }}</strong>
                <span>Total Requests</span>
            </div>
            <div class="summary-cell">
                <strong>{{ $totalDispensed }}</strong>
                <span>Total Distributed</span>
            </div>
            <div class="summary-cell">
                <strong>{{ $lowStock }}</strong>
                <span>Low Stock</span>
            </div>
            <div class="summary-cell">
                <strong>{{ $expiringSoon }}</strong>
                <span>Expiring Soon</span>
            </div>
        </div>
    </div>

    <!-- MONTHLY MEDICINE GIVEN -->
    <div class="section">
        <div class="section-title">Monthly Medicine Given ({{ date('Y') }})</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Month</th>
                    <th style="width: 50%;" class="text-center">Quantity Dispensed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyGivenData['labels'] as $index => $month)
                <tr>
                    <td>{{ $month }}</td>
                    <td class="text-center font-bold">{{ $monthlyGivenData['data'][$index] }}</td>
                </tr>
                @endforeach
                <tr style="background: #e6f7f0;">
                    <td class="font-bold">TOTAL</td>
                    <td class="text-center font-bold">{{ array_sum($monthlyGivenData['data']) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- MONTHLY REQUEST TREND -->
    <div class="section">
        <div class="section-title">Monthly Request Trend ({{ date('Y') }})</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Month</th>
                    <th style="width: 50%;" class="text-center">Total Requests</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requestTrendData['labels'] as $index => $month)
                <tr>
                    <td>{{ $month }}</td>
                    <td class="text-center font-bold">{{ $requestTrendData['data'][$index] }}</td>
                </tr>
                @endforeach
                <tr style="background: #e6f7f0;">
                    <td class="font-bold">TOTAL</td>
                    <td class="text-center font-bold">{{ array_sum($requestTrendData['data']) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- TOP 5 MOST DISPENSED MEDICINES -->
    <div class="section">
        <div class="section-title">Top 5 Most Dispensed Medicines</div>
        @if($topDispensedTable && $topDispensedTable->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;" class="text-center">Rank</th>
                        <th style="width: 60%;">Medicine Name</th>
                        <th style="width: 30%;" class="text-center">Total Dispensed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topDispensedTable->take(5) as $index => $item)
                    <tr>
                        <td class="text-center font-bold">{{ $index + 1 }}</td>
                        <td>{{ $item->medicine_name }}</td>
                        <td class="text-center font-bold">{{ $item->total_dispensed }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; padding: 20px; color: #999;">No dispensing data available.</p>
        @endif
    </div>

    <!-- STOCK LEVEL DISTRIBUTION -->
    <div class="section">
        <div class="section-title">Stock Level Distribution</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Stock Status</th>
                    <th style="width: 25%;" class="text-center">Count</th>
                    <th style="width: 25%;" class="text-center">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = array_sum($pieChartData['data']);
                @endphp
                @foreach($pieChartData['labels'] as $index => $label)
                <tr>
                    <td class="font-bold">{{ $label }}</td>
                    <td class="text-center font-bold">{{ $pieChartData['data'][$index] }}</td>
                    <td class="text-center">{{ $total > 0 ? round(($pieChartData['data'][$index] / $total) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
                <tr style="background: #e6f7f0;">
                    <td class="font-bold">TOTAL</td>
                    <td class="text-center font-bold">{{ $total }}</td>
                    <td class="text-center font-bold">100%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- MEDICINE COUNT BY CATEGORY -->
    <div class="section">
        <div class="section-title">Medicine Count by Category</div>
        @if(isset($categoriesData) && count($categoriesData['labels']) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;" class="text-center">No.</th>
                        <th style="width: 60%;">Category Name</th>
                        <th style="width: 30%;" class="text-center">Medicine Count</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalMedicines = array_sum($categoriesData['data']);
                    @endphp
                    @foreach($categoriesData['labels'] as $index => $category)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $category }}</td>
                        <td class="text-center font-bold">{{ $categoriesData['data'][$index] }}</td>
                    </tr>
                    @endforeach
                    <tr style="background: #e6f7f0;">
                        <td colspan="2" class="font-bold">TOTAL</td>
                        <td class="text-center font-bold">{{ $totalMedicines }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <p style="text-align: center; padding: 20px; color: #999;">No category data available.</p>
        @endif
    </div>
    <!-- FOOTER -->
    <div class="footer">
        <p>This report was automatically generated by the Inventory Management System</p>
        <p>© {{ date('Y') }} - Confidential Document</p>
    </div>
</body>
</html>