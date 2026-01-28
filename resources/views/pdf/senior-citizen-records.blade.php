<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Vaccination Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        th.sorted {
            background-color: #d4e6f1;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-info {
            margin-bottom: 15px;
            font-size: 11px;
        }

        .sort-indicator {
            font-size: 10px;
            color: #3498db;
        }

        .page-break {
            page-break-after: always;
        }

        .page-number {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            color: #666;
        }

        /* header section */

        header {
            width: 100%;
        }

        header img {
            height: 120px;
            width: 120px;
            float: left;
            margin-right: 20px;
            margin-top: 20px;
        }

        .header-text {
            width: 70%;
            text-align: center;
            text-transform: uppercase;
            float: left;
            padding-top: 20px;
            /* Adjust to vertically center text */
        }

        .header-text h4 {
            font-size: 15px;
        }
    </style>
</head>

<body>
    @forelse($recordPages as $pageIndex => $records)
    <div class="{{ !$loop->last ? 'page-break' : '' }}">
        <header>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/hugoperez_logo.png'))) }}" alt="">
            <div class="header-text">
                <h4>Barangay Hugo Perez Proper -</h4>
                <h4>Health Center Information Management System</h4>
            </div>
            <div style="clear: both;"></div>
        </header>
        <h1>Senior Citizen Records</h1>

        <div class="header-info">
            <p><strong>Generated:</strong> {{ now()->format('F d, Y h:i A') }}</p>
            <p><strong>Total Records:</strong> {{ $totalRecords }}</p>
            <p><strong>Records Per Page:</strong> {{ $entriesPerPage }}</p>
            @if($search)
            <p><strong>Search Filter:</strong> "{{ $search }}"</p>
            @endif
            <p><strong>Sorted By:</strong> {{ ucfirst(str_replace('_', ' ', str_replace('patients.', '', $sortField))) }} ({{ strtoupper($sortDirection) }})</p>
            <p><strong>Date Range: </strong>{{$startDate}} - {{$endDate}}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="{{ $sortField == 'patients.full_name' || $sortField == 'full_name' ? 'sorted' : '' }}">
                        Full Name
                        @if($sortField == 'patients.full_name' || $sortField == 'full_name')
                        <span class="sort-indicator">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="{{ $sortField == 'patients.age' || $sortField == 'age' ? 'sorted' : '' }}">
                        Age
                        @if($sortField == 'patients.age' || $sortField == 'age')
                        <span class="sort-indicator">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="{{ $sortField == 'patients.sex' || $sortField == 'sex' ? 'sorted' : '' }}">
                        Sex
                        @if($sortField == 'patients.sex' || $sortField == 'sex')
                        <span class="sort-indicator">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="{{ $sortField == 'patients.contact_number' || $sortField == 'contact_number' ? 'sorted' : '' }}">
                        Contact Number
                        @if($sortField == 'patients.contact_number' || $sortField == 'contact_number')
                        <span class="sort-indicator">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="{{ $sortField == 'created_at' ? 'sorted' : '' }}">
                        Date
                        @if($sortField == 'created_at')
                        <span class="sort-indicator">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td>{{ $record->full_name }}</td>
                    <td>{{ $record->age }}</td>
                    <td>{{ $record->sex }}</td>
                    <td>{{ $record->contact_number }}</td>
                    <td>{{ $record->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No records found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="page-number">
            Page {{ $pageIndex + 1 }} of {{ $recordPages->count() }}
        </div>
    </div>
    @empty
    <header>
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/hugoperez_logo.png'))) }}" alt="">
        <div class="header-text">
            <h4>Barangay Hugo Perez Proper -</h4>
            <h4>Health Center Information Management System</h4>
        </div>
        <div style="clear: both;"></div>
    </header>
    <h1>Senior Citizen Records</h1>

    <div class="header-info">
        <p><strong>Generated:</strong> {{ now()->format('F d, Y h:i A') }}</p>
        <p><strong>Total Records:</strong> {{ $totalRecords }}</p>
        <p><strong>Records Per Page:</strong> {{ $entriesPerPage }}</p>
        @if($search)
        <p><strong>Search Filter:</strong> "{{ $search }}"</p>
        @endif
        <p><strong>Sorted By:</strong> {{ ucfirst(str_replace('_', ' ', str_replace('patients.', '', $sortField))) }} ({{ strtoupper($sortDirection) }})</p>
        <p><strong>Date Range: </strong>{{$startDate}} - {{$endDate}}</p>
    </div>
    <table>

        <thead>
            <tr>
                <th class="{{ $sortField == 'patients.full_name' || $sortField == 'full_name' ? 'sorted' : '' }}">
                    Full Name
                    @if($sortField == 'patients.full_name' || $sortField == 'full_name')
                    <span class="sort-indicator">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </th>
                <th class="{{ $sortField == 'patients.age' || $sortField == 'age' ? 'sorted' : '' }}">
                    Age
                    @if($sortField == 'patients.age' || $sortField == 'age')
                    <span class="sort-indicator">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </th>
                <th class="{{ $sortField == 'patients.sex' || $sortField == 'sex' ? 'sorted' : '' }}">
                    Sex
                    @if($sortField == 'patients.sex' || $sortField == 'sex')
                    <span class="sort-indicator">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </th>
                <th class="{{ $sortField == 'patients.contact_number' || $sortField == 'contact_number' ? 'sorted' : '' }}">
                    Contact Number
                    @if($sortField == 'patients.contact_number' || $sortField == 'contact_number')
                    <span class="sort-indicator">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </th>
                <th class="{{ $sortField == 'created_at' ? 'sorted' : '' }}">
                    Date
                    @if($sortField == 'created_at')
                    <span class="sort-indicator">{{ $sortDirection == 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="12" style="text-align:center">No record available.</td>
            </tr>
        </tbody>
    </table>
    @endforelse
</body>

</html>