<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Expiring Soon Medicine Report</title>
    <style>
        @page { margin: 15mm 10mm; }

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
        .header h1 { margin: 0 0 5px 0; font-size: 22px; color: #4CAF50; }
        .header p  { margin: 5px 0; color: #666; font-size: 10px; }

        .alert-box {
            background-color: #d2ffcd;
            border-left: 4px solid #4CAF50;
            padding: 10px;
            margin-bottom: 15px;
            color: black;
            font-weight: 600;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 7px; text-align: left; }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
            font-size: 10px;
        }
        td { font-size: 10px; }
        tr:nth-child(even) { background-color: #f9f9f9; }

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
        .badge-success   { background-color: #d1e7dd; color: #0f5132; }
        .badge-warning   { background-color: #fff3cd; color: #856404; }
        .badge-danger    { background-color: #f8d7da; color: #842029; }
        .badge-secondary { background-color: #e2e3e5; color: #41464b; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Expiring Soon Medicine Report</h1>
        <p>Generated on: {{ $generatedDate }}</p>
    </div>

    @if($total > 0)
    <div class="alert-box">
        ⚠ Alert: {{ $total }} batch(es) will expire within 30 days. Please use or dispose of them accordingly!
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width:4%"  class="text-center">#</th>
                <th style="width:22%">Medicine Name</th>
                <th style="width:12%">Dosage</th>
                <th style="width:14%" class="text-center">Batch No.</th>
                <th style="width:12%" class="text-center">Avail. Stock</th>
                <th style="width:14%" class="text-center">Stock Status</th>
                <th style="width:14%" class="text-center">Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batches as $index => $batch)
            @php
                $stockStatus = $batch->computed_stock_status ?? 'N/A';
                $stockBadge  = match($stockStatus) {
                    'In Stock'     => 'badge-success',
                    'Low Stock'    => 'badge-warning',
                    'Out of Stock' => 'badge-danger',
                    default        => 'badge-secondary',
                };
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $batch->medicine?->medicine_name ?? 'N/A' }}</td>
                <td>{{ $batch->medicine?->dosage ?? 'N/A' }}</td>
                <td class="text-center">
                    <span class="text-dark">{{ $batch->batch_number ?? '—' }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-warning">{{ $batch->available_stock }}</span>
                </td>
                <td class="text-center">
                    <span class="badge {{ $stockBadge }}">{{ $stockStatus }}</span>
                </td>
                <td class="text-center">
                    {{ \Carbon\Carbon::parse($batch->expiry_date)->format('M d, Y') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#666;">
                    No expiring batches found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box">
        Total Expiring Soon Batches: {{ $total }}
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