<html>
<head>
    <meta charset="utf-8">
    <title>Medicine List - {{ date('Y-m-d') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #10b981;
        }
        .header h1 {
            font-size: 24px;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #10b981;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MEDICINE INVENTORY LIST</h1>
        <p>Generated: {{ $generatedDate }}</p>
        <p>Total Medicines: {{ $total }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Medicine Name</th>
                <th style="width: 20%;">Category</th>
                <th style="width: 15%;">Dosage</th>
                <th style="width: 10%;">Stock</th>
                <th style="width: 15%;">Stock Status</th>
                <th style="width: 10%;">Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($medicines as $medicine)
            <tr>
                <td>{{ $medicine->medicine_name }}</td>
                <td>{{ $medicine->category_display }}</td>
                <td>{{ $medicine->dosage }}</td>
                <td>{{ $medicine->stock }}</td>
                <td>
                    <span class="badge badge-{{
                        $medicine->stock_status === 'In Stock' ? 'success' :
                        ($medicine->stock_status === 'Low Stock' ? 'warning' : 'danger')
                    }}">
                        {{ $medicine->stock_status }}
                    </span>
                </td>
                <td>{{ $medicine->expiry_date ? $medicine->expiry_date->format('M d, Y') : 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px; color: #999;">
                    No medicines found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>