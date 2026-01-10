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

        .date-range-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f0fdf4;
            border-left: 4px solid #10b981;
        }

        .date-range-info p {
            font-size: 11px;
            color: #000;
            margin: 3px 0;
        }

        .date-range-info strong {
            color: #10b981;
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

        .page-break {
            page-break-before: always;
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

        .section-subtitle {
            font-size: 10px;
            text-align: center;
            color: #666;
            margin-top: -8px;
            margin-bottom: 10px;
            font-style: italic;
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

        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }

        @media print {
            body { padding: 10px; }
            .section { page-break-inside: avoid; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <h1>INVENTORY REPORT</h1>
        <p>Generated: {{ $generatedDate }}</p>
    </div>

    <!-- DATE RANGE INFORMATION -->
    <div class="date-range-info">
        <p><strong>Report Period:</strong> {{ $startDate }} - {{ $endDate }}</p>
        <p style="font-size: 9px; color: #666; margin-top: 5px;">
            Medicine Given & Request Trend data filtered by this period
        </p>
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

    <!-- MONTHLY MEDICINE GIVEN - Page 1 -->
    <div class="section">
        <div class="section-title">Medicine Distribution Trend</div>
        <div class="section-subtitle">Period: {{ $startDate }} - {{ $endDate }}</div>
        @if(isset($monthlyGivenData['data']) && array_sum($monthlyGivenData['data']) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Period</th>
                        <th style="width: 50%;" class="text-center">Quantity Dispensed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyGivenData['fullLabels'] as $index => $period)
                    <tr>
                        <td>{{ $period }}</td>
                        <td class="text-center font-bold">{{ $monthlyGivenData['data'][$index] }}</td>
                    </tr>
                    @endforeach
                    <tr style="background: #e6f7f0;">
                        <td class="font-bold">TOTAL</td>
                        <td class="text-center font-bold">{{ array_sum($monthlyGivenData['data']) }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <p class="no-data">No medicine distribution data available for this period.</p>
        @endif
    </div>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- MONTHLY REQUEST TREND - Page 2 -->
    <div class="section">
        <div class="section-title">Medicine Request Trend</div>
        <div class="section-subtitle">Period: {{ $startDate }} - {{ $endDate }}</div>
        @if(isset($requestTrendData['data']) && array_sum($requestTrendData['data']) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Period</th>
                        <th style="width: 50%;" class="text-center">Total Requests</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requestTrendData['fullLabels'] as $index => $period)
                    <tr>
                        <td>{{ $period }}</td>
                        <td class="text-center font-bold">{{ $requestTrendData['data'][$index] }}</td>
                    </tr>
                    @endforeach
                    <tr style="background: #e6f7f0;">
                        <td class="font-bold">TOTAL</td>
                        <td class="text-center font-bold">{{ array_sum($requestTrendData['data']) }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <p class="no-data">No medicine request data available for this period.</p>
        @endif
    </div>

    <!-- TOP 5 MOST DISPENSED MEDICINES -->
    <div class="section">
        <div class="section-title">Top Dispensed Medicines</div>
        <div class="section-subtitle">Period: {{ $startDate }} - {{ $endDate }}</div>
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
            <p class="no-data">No dispensing data available for this period.</p>
        @endif
    </div>

    <!-- STOCK LEVEL DISTRIBUTION -->
    <div class="section">
        <div class="section-title">Stock Level Distribution</div>
        <div class="section-subtitle">Based on medicines dispensed: {{ $pieChartStartDate }} - {{ $pieChartEndDate }}</div>
        @if(isset($pieChartData['data']) && array_sum($pieChartData['data']) > 0)
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
        @else
            <p class="no-data">No stock data available for medicines dispensed in this period.</p>
        @endif
    </div>

    <!-- MEDICINE COUNT BY CATEGORY -->
    <div class="section">
        <div class="section-title">Medicine Distribution by Category</div>
        <div class="section-subtitle">Based on medicines dispensed: {{ $barChartStartDate }} - {{ $barChartEndDate }}</div>
        @if(isset($categoriesData) && count($categoriesData['labels']) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;" class="text-center">No.</th>
                        <th style="width: 60%;">Category Name</th>
                        <th style="width: 30%;" class="text-center">Quantity Dispensed</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalQuantity = array_sum($categoriesData['data']);
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
                        <td class="text-center font-bold">{{ $totalQuantity }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <p class="no-data">No category data available for this period.</p>
        @endif
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>This report was automatically generated by the Inventory Management System</p>
        <p>© {{ date('Y') }} - Confidential Document</p>
    </div>
</body>
</html>