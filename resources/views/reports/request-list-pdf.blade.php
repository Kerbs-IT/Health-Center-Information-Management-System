<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Medicine Request List Report</title>
    <style>
        @page {
            margin: 15mm 10mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }

        .header h1 {
            margin: 0 0 5px 0;
            font-size: 22px;
            color: #4CAF50;
        }

        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
            font-size: 10px;
        }

        td {
            font-size: 10px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total-box {
            margin-top: 20px;
            padding: 10px;
            background: #f0f0f0;
            font-weight: bold;
            border-radius: 4px;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .bg-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .bg-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .bg-info {
            background-color: #cff4fc;
            color: #055160;
        }

        .bg-danger {
            background-color: #f8d7da;
            color: #842029;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Medicine Request List Report</h1>
        <p>Generated on: {{ $generatedDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 20%">Patient Name</th>
                <th style="width: 18%">Medicine</th>
                <th style="width: 12%">Dosage</th>
                <th style="width: 10%">Quantity</th>
                <th style="width: 12%">Status</th>
                <th style="width: 18%">Date Requested</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $index => $request)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $request->requester_name }}</td>
                <td>{{ $request->medicine_name }}</td>
                <td>{{ $request->dosage }}</td>
                <td>{{ $request->quantity_requested }}</td>
                <td>
                    <span class="badge bg-{{ $request->status == 'completed' ? 'success' : ($request->status == 'pending' ? 'warning' : ($request->status == 'approved' ? 'info' : 'danger')) }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        Total Requests: {{ $total }}
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $size = 9;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>