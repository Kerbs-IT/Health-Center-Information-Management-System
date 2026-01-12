<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Request List - {{ date('Y-m-d') }}</title>
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
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MEDICINE REQUESTS LIST</h1>
        <p>Generated: {{ $generatedDate }}</p>
        <p>Total Requests: {{ $total }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 22%;">Patient Name</th>
                <th style="width: 25%;">Medicine</th>
                <th style="width: 10%;">Dosage</th>
                <th style="width: 10%;">Quantity</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 13%;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
            <tr>
                <td>#{{ $request->id }}</td>
                <td>{{ $request->requester_name }}</td>
                <td>{{ $request->medicine_display }}</td>
                <td>{{ $request->dosage_display }}</td>
                <td>{{ $request->quantity_requested }}</td>
                <td>
                    <span class="badge badge-{{
                        $request->status === 'pending' ? 'warning' :
                        ($request->status === 'completed' ? 'success' : 'danger')
                    }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
                <td>{{ $request->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #999;">
                    No requests found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>