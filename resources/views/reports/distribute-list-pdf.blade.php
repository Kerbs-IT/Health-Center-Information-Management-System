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
        <h1>Total Distributed Medicine List Report</h1>
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
                <th style="width: 15%">Date Distributed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distributed as $index => $dist)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $dist->patient_name }}</td>
                <td>{{ $dist->medicine_name }}</td>
                <td>{{ $dist->dosage }}</td>
                <td>{{ $dist->quantity }}</td>
                <td>{{ \Carbon\Carbon::parse($dist->performed_at)->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        Total Medicines: {{ $total }}
    </div>
</body>
</html>