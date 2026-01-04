<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Medicine List Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .total-box { margin-top: 20px; padding: 10px; background: #f0f0f0; font-weight: bold; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Expiring Soon Medicine List Report</h1>
        <p>Generated on: {{ $generatedDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 20%">Medicine Name</th>
                <th style="width: 15%">Dosage</th>
                <th style="width: 15%">Stock</th>
                <th style="width: 10%">Expiry Date</th>
                <th style="width: 10%">Expiry Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicines as $index => $medicine)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $medicine->medicine_name }}</td>
                <td>{{ $medicine->dosage }}</td>
                <td>{{ $medicine->stock }}</td>
                <td>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if ($medicine->stock_status === 'In Stock') bg-success bg-opacity-25 text-success
                        @elseif ($medicine->stock_status === 'Low Stock') bg-warning bg-opacity-25 text-yellow-800
                        @elseif ($medicine->stock_status === 'Out of Stock') bg-danger bg-opacity-25 text-danger
                        @endif">
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
</body>
</html>