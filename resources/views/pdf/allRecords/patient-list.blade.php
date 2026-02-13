<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Patient List</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
            background: #fff;
            margin: 0 30px;
        }

        /* ── Page header (logo + title) ──────────────────────────────────── */
        header {
            width: 100%;
            padding: 12px 0 10px 0;
            border-bottom: 2px solid #2c6e49;
            margin-bottom: 10px;
            overflow: hidden;
        }

        header img {
            float: left;
            height: 55px;
            width: auto;
            margin-right: 12px;
        }

        .header-text {
            float: left;
            padding-top: 6px;
        }

        .header-text h4 {
            font-size: 12px;
            font-weight: bold;
            color: #000;
            line-height: 1.5;
        }

        /* ── Report title ────────────────────────────────────────────────── */
        .report-title {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        /* ── Filter summary ──────────────────────────────────────────────── */
        .filter-bar {
            border: 1px solid #ccc;
            padding: 5px 8px;
            margin-bottom: 10px;
            font-size: 8.5px;
            color: #000;
        }

        .filter-bar span {
            margin-right: 14px;
        }

        /* ── Table ───────────────────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background-color: #2c6e49;
            color: #ffffff;
        }

        thead th {
            padding: 6px 7px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 1px solid #1a4d33;
        }

        tbody tr {
            background-color: #fff;
        }

        tbody td {
            padding: 5px 7px;
            border: 1px solid #bbb;
            font-size: 9px;
            vertical-align: middle;
            color: #000;
        }

        .no-data td {
            text-align: center;
            padding: 16px;
            font-style: italic;
        }

        /* ── Total row ───────────────────────────────────────────────────── */
        .total-row td {
            border-top: 2px solid #000;
            font-weight: bold;
            background-color: #fff;
        }

        /* ── Footer ──────────────────────────────────────────────────────── */
        .footer {
            font-size: 8px;
            color: #555;
            text-align: right;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            margin-top: 10px;
        }

        /* ── Page break ──────────────────────────────────────────────────── */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    @php
    $chunks = $rows->chunk(25);
    $total = count($rows);
    $lastChunk = $chunks->count() - 1;
    $counter = 0;
    @endphp

    @foreach($chunks as $pageIndex => $chunk)

    {{-- ─── Logo + Title Header ──────────────────────────────────────── --}}
    <header>
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/hugoperez_logo.png'))) }}" alt="">
        <div class="header-text">
            <h4>Barangay Hugo Perez Proper -</h4>
            <h4>Health Center Information Management System</h4>
        </div>
        <div style="clear: both;"></div>
    </header>

    {{-- ─── Report title + filters (first page only) ────────────────── --}}
    @if($pageIndex === 0)
    <div class="report-title">Patient List Report</div>
    <div class="filter-bar">
        <span><strong>Status:</strong>
            {{ $filters['status'] === 'all' ? 'All' : ucfirst(strtolower($filters['status'])) }}
        </span>
        <span><strong>Purok:</strong>
            {{ $filters['purok'] === 'all' ? 'All Puroks' : $filters['purok'] }}
        </span>
        <span><strong>Type:</strong>
            {{ $filters['type'] === 'all' ? 'All Types' : ucwords(str_replace('-', ' ', $filters['type'])) }}
        </span>
        <span><strong>Date From:</strong>
            {{ $filters['dateFrom'] ? \Carbon\Carbon::parse($filters['dateFrom'])->format('M d, Y') : 'N/A' }}
        </span>
        <span><strong>Date To:</strong>
            {{ $filters['dateTo'] ? \Carbon\Carbon::parse($filters['dateTo'])->format('M d, Y') : 'N/A' }}
        </span>
        <span><strong>Total Records:</strong> {{ $total }}</span>
    </div>
    @endif

    {{-- ─── Table ────────────────────────────────────────────────────── --}}
    <table>
        <thead>
            <tr>
                <th style="width:3%;">#</th>
                <th style="width:18%;">Name</th>
                <th style="width:5%;">Sex</th>
                <th style="width:5%;">Age</th>
                <th style="width:12%;">Contact Number</th>
                <th style="width:16%;">Type of Patient</th>
                <th style="width:14%;">Purok</th>
                <th style="width:8%;">Status</th>
                <th style="width:11%;">Registered</th>
            </tr>
        </thead>
        <tbody>
            @forelse($chunk as $row)
            @php $counter++ @endphp
            <tr>
                <td style="text-align:center;">{{ $counter }}</td>
                <td>{{ $row->first_name }} {{ $row->last_name }}</td>
                <td>{{ $row->sex }}</td>
                <td>{{ $row->ageDisplay }}</td>
                <td>{{ $row->contact_number ?? '—' }}</td>
                <td>{{ $row->type_of_case ? ucwords(str_replace('-', ' ', $row->type_of_case)) : '—' }}</td>
                <td>{{ $row->purok ?? '—' }}</td>
                <td>{{ $row->status }}</td>
                <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('m/d/Y') : '—' }}</td>
            </tr>
            @empty
            <tr class="no-data">
                <td colspan="8">No patients found matching the applied filters.</td>
            </tr>
            @endforelse

            {{-- Total row on last page only --}}
            @if($pageIndex === $lastChunk && $total > 0)
            <tr class="total-row">
                <td colspan="7" style="text-align:right;">Total Records:</td>
                <td colspan="2">{{ $total }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- ─── Footer ───────────────────────────────────────────────────── --}}
    <div class="footer">
        Page {{ $pageIndex + 1 }} of {{ $chunks->count() }} &nbsp;|&nbsp; Generated on {{ $generatedAt }}
    </div>

    {{-- Page break between pages, not after last --}}
    @if($pageIndex < $lastChunk)
        <div class="page-break">
        </div>
        @endif

        @endforeach

</body>

</html>