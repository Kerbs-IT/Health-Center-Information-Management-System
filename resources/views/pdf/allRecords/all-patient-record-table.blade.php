<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Patient Records Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 15px;
        }

        header {
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            overflow: hidden;
        }

        header img {
            float: left;
            width: 60px;
            height: 60px;
            margin-right: 15px;
        }

        .header-text {
            float: left;
            margin-top: 10px;
        }

        .header-text h4 {
            margin: 2px 0;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 3px 0;
            font-size: 12px;
            font-weight: normal;
        }

        .filters-info {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
        }

        .filters-info h3 {
            margin: 0 0 8px 0;
            font-size: 12px;
            font-weight: bold;
        }

        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
            font-size: 10px;
        }

        .filter-label {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table thead {
            background-color: #28a745;
            color: white;
        }

        table th {
            padding: 8px 6px;
            text-align: left;
            font-size: 11px;
            border: 1px solid #dee2e6;
        }

        table td {
            padding: 6px 5px;
            border: 1px solid #dee2e6;
            font-size: 11px;
            vertical-align: top;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .page-break {
            page-break-after: always;
        }

        .no-page-break {
            page-break-inside: avoid;
        }

        .footer {
            position: fixed;
            bottom: 15px;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }

        .page-number:before {
            content: "Page " counter(page);
        }

        .text-nowrap {
            white-space: nowrap;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            margin-top: 10px;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <header>
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/hugoperez_logo.png'))) }}" alt="Logo">
        <div class="header-text">
            <h4>Barangay Hugo Perez Proper -</h4>
            <h4>Health Center Information Management System</h4>
        </div>
        <div style="clear: both;"></div>
    </header>

    <div class="header">
        <h1>Patient Records Report</h1>
    </div>

    <div class="filters-info">
        <h3>Report Details:</h3>
        <div class="filter-item">
            <span class="filter-label">Date Range:</span>
            {{ \Carbon\Carbon::parse($filters['start_date'])->format('M j, Y') }} -
            {{ \Carbon\Carbon::parse($filters['end_date'])->format('M j, Y') }}
        </div>

        @if(!empty($filters['search']))
        <div class="filter-item">
            <span class="filter-label">Search:</span> {{ $filters['search'] }}
        </div>
        @endif

        @if(!empty($filters['type_of_patient']))
        <div class="filter-item">
            <span class="filter-label">Type of Patient:</span>
            {{ Str::title(str_replace("-", " ", $filters['type_of_patient'])) }}
        </div>
        @endif

        @if(!empty($filters['purok']))
        <div class="filter-item">
            <span class="filter-label">Purok:</span> {{ $filters['purok'] }}
        </div>
        @endif

        <div class="filter-item">
            <span class="filter-label">Generated:</span> {{ $filters['date_generated'] }}
        </div>

        <div class="filter-item">
            <span class="filter-label">Total Records:</span> {{ $records->count() }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No.</th>
                <th style="width: 18%;">Full Name</th>
                <th style="width: 5%;">Age</th>
                <th style="width: 5%;">Sex</th>
                <th style="width: 12%;">Contact No.</th>
                <th style="width: 18%;">Type of Patient</th>
                <th style="width: 12%;">Purok</th>
                <th style="width: 12%;">Date Registered</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp
            @forelse($records as $record)
            @forelse($record->medical_record_case as $case)
            <tr class="{{ $counter % 20 == 0 ? 'page-break' : '' }}">
                <td class="text-center">{{ $counter }}</td>
                <td class="text-nowrap">{{ $record->full_name ?? 'N/A' }}</td>
                <td class="text-center">{{ $record->age_display ?? 'N/A' }}</td>
                <td class="text-center">{{ $record->sex ?? 'N/A' }}</td>
                <td>{{ $record->contact_number ?? 'N/A' }}</td>
                <td>{{ Str::title(str_replace("-", " ", $case->type_of_case ?? 'N/A')) }}</td>
                <td>{{ $record->address->purok ?? 'N/A' }}</td>
                <td class="text-center">{{ $case->created_at ? $case->created_at->format('M j, Y') : 'N/A' }}</td>
            </tr>
            @php $counter++; @endphp
            @empty
            <tr class="{{ $counter % 20 == 0 ? 'page-break' : '' }}">
                <td class="text-center">{{ $counter }}</td>
                <td class="text-nowrap">{{ $record->full_name ?? 'N/A' }}</td>
                <td class="text-center">{{ $record->age_display ?? 'N/A' }}</td>
                <td class="text-center">{{ $record->sex ?? 'N/A' }}</td>
                <td>{{ $record->contact_number ?? 'N/A' }}</td>
                <td>N/A</td>
                <td>{{ $record->address->purok ?? 'N/A' }}</td>
                <td class="text-center">{{ $record->created_at ? $record->created_at->format('M j, Y') : 'N/A' }}</td>
            </tr>
            @php $counter++; @endphp
            @endforelse
            @empty
            <tr>
                <td colspan="8" class="text-center">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($records->count() > 0)
    <div class="summary">
        Total Patients: {{ $records->count() }}
    </div>
    @endif

    <div class="footer">
        <div class="page-number"></div>
        <div>This is a system-generated report. No signature is required.</div>
    </div>
</body>

</html>