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
        <h1>Medicine List Report</h1>
        <p>Generated on: {{ $generatedDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 25%">Patient Name</th>
                <th style="width: 15%">Medicine</th>
                <th style="width: 15%">Dosage</th>
                <th style="width: 10%">Quantity</th>
                <th style="width: 15%">Status</th>
                <th style="width: 15%">Date Requested</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $index => $request)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $request->patients->full_name }}</td>
                <td>{{ $request->medicine_name}}</td>
                <td>{{ $request->dosage }}</td>
                <td>{{ $request->quantity_requested }}</td>
                <td>
                    <span class="badge bg-{{ $request->status == 'completed' ? 'success' : ($request->status == 'pending' ? 'warning' : ($request->status == 'approved' ? 'info' : 'danger')) }}">
                        {{ $request->status }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        Total Medicines: {{ $total }}
    </div>
</body>
</html>