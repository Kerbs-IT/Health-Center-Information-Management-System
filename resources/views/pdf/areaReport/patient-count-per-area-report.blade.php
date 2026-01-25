<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Patient Count Per Area Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 18px;
            color: #34495e;
            font-weight: normal;
        }

        .report-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #2E8B57;
        }

        .report-info p {
            margin: 5px 0;
            font-size: 14px;
        }

        .report-info strong {
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px 8px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background-color: #2E8B57;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e8f4f8;
        }

        .area-column {
            text-align: left;
            font-weight: bold;
            background-color: #ecf0f1;
        }

        .total-row {
            background-color: #34495e !important;
            color: white;
            font-weight: bold;
        }

        .total-row td {
            border-color: #2c3e50;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #7f8c8d;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }

        header {
            width: 100%;
        }

        header img {
            height: 120px;
            width: 120px;
            float: left;
            margin-right: 20px;
            margin-top: 0px;
        }

        .header-text {
            width: 70%;
            text-align: center;
            text-transform: uppercase;
            float: left;
            padding-top: 10px;
            /* Adjust to vertically center text */
        }
    </style>
</head>

<body>
    <header>
        <img src="{{public_path('images/hugoperez_logo.png')}}" alt="">
        <div class="header-text">
            <h4>Barangay Hugo Perez Proper -</h4>
            <h4>Health Center Information Management System</h4>
        </div>
        <div style="clear: both;"></div>
    </header>

    <div class="report-info">
        <p><strong>Date Generated:</strong> {{ $generatedDate }}</p>
        @if($startDate && $endDate)
        <p><strong>Date Range:</strong>{{$startDate}} - {{$endDate}}</p>
        @else
        <p><strong>Date Range:</strong> All Records</p>
        @endif
    </div>

    @if(count($data) > 0)
    <table>
        <thead>
            <tr>
                <th>Area/Purok</th>
                <th>Vaccination</th>
                <th>Prenatal</th>
                <th>TB-DOTS</th>
                <th>Senior Citizen</th>
                <th>Family Planning</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalVaccination = 0;
            $totalPrenatal = 0;
            $totalTbDots = 0;
            $totalSenior = 0;
            $totalFamilyPlanning = 0;
            $grandTotal = 0;
            @endphp

            @foreach($data as $area => $counts)
            @php
            $rowTotal = ($counts['vaccination'] ?? 0) +
            ($counts['prenatal'] ?? 0) +
            ($counts['tb-dots'] ?? 0) +
            ($counts['senior-citizen'] ?? 0) +
            ($counts['family-planning'] ?? 0);

            $totalVaccination += $counts['vaccination'] ?? 0;
            $totalPrenatal += $counts['prenatal'] ?? 0;
            $totalTbDots += $counts['tb-dots'] ?? 0;
            $totalSenior += $counts['senior-citizen'] ?? 0;
            $totalFamilyPlanning += $counts['family-planning'] ?? 0;
            $grandTotal += $rowTotal;
            @endphp
            <tr>
                <td class="area-column">{{ $area }}</td>
                <td>{{ $counts['vaccination'] ?? 0 }}</td>
                <td>{{ $counts['prenatal'] ?? 0 }}</td>
                <td>{{ $counts['tb-dots'] ?? 0 }}</td>
                <td>{{ $counts['senior-citizen'] ?? 0 }}</td>
                <td>{{ $counts['family-planning'] ?? 0 }}</td>
                <td><strong>{{ $rowTotal }}</strong></td>
            </tr>
            @endforeach

            <tr class="total-row">
                <td>TOTAL</td>
                <td>{{ $totalVaccination }}</td>
                <td>{{ $totalPrenatal }}</td>
                <td>{{ $totalTbDots }}</td>
                <td>{{ $totalSenior }}</td>
                <td>{{ $totalFamilyPlanning }}</td>
                <td>{{ $grandTotal }}</td>
            </tr>
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>No data available for the selected date range.</p>
    </div>
    @endif

    <div class="footer">
        <p>Generated by Hugo Perez Barangay Health Center Management System</p>
        <p>This report is confidential and for authorized personnel only.</p>
    </div>
</body>

</html>