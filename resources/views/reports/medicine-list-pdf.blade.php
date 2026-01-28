<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Medicine List Report</title>
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

        .badge-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #666;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Medicine List Report</h1>
        <p>Generated on: {{ $generatedDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 25%">Medicine Name</th>
                <th style="width: 15%">Category</th>
                <th style="width: 15%">Dosage</th>
                <th style="width: 10%">Stock</th>
                <th style="width: 15%">Status</th>
                <th style="width: 15%">Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicines as $index => $medicine)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $medicine->medicine_name }}</td>
                <td>{{ $medicine->category_name }}</td>
                <td>{{ $medicine->dosage }}</td>
                <td>{{ $medicine->stock }}</td>
                <td>
                    <span class="badge badge-{{ $medicine->stock_status == 'In Stock' ? 'success' : ($medicine->stock_status == 'Low Stock' ? 'warning' : 'danger') }}">
                        {{ $medicine->stock_status }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($medicine->expiry_date)->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        Total Medicines: {{ $total }}
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