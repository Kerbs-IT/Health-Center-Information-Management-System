<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png') }}">
    <title>Health Center Information Management System</title>
    <style>
        .records-table thead th {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #3a3b3cff;
            background: #f8f9fb;
            border-bottom: 2px solid #eef0f4;
            padding: 14px 18px;
            white-space: nowrap;
        }

        .records-table tbody td {
            padding: 15px 18px;
            vertical-align: middle;
            border-bottom: 1px solid #f2f4f7;
            font-size: 0.9rem;
        }

        .records-table tbody tr:last-child td {
            border-bottom: none;
        }

        .records-table tbody tr {
            transition: background 0.15s ease;
        }

        .records-table tbody tr:hover {
            background-color: #f8f9fb;
        }

        .row-num {
            font-size: 0.8rem;
            font-weight: 700;
            color: #ced2da;
            text-align: center;
            width: 48px;
        }

        .patient-name-cell {
            font-weight: 600;
            color: #1a202c;
            font-size: 0.9rem;
        }

        .patient-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.72rem;
            font-weight: 700;
            flex-shrink: 0;
            letter-spacing: 0.02em;
        }

        .case-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.775rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .case-badge .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .badge-vaccination {
            background: #ecfdf5;
            color: #059669;
        }

        .badge-vaccination .badge-dot {
            background: #059669;
        }

        .badge-prenatal {
            background: #fdf2f8;
            color: #c026d3;
        }

        .badge-prenatal .badge-dot {
            background: #c026d3;
        }

        .badge-tb-dots {
            background: #fffbeb;
            color: #d97706;
        }

        .badge-tb-dots .badge-dot {
            background: #d97706;
        }

        .badge-senior-citizen {
            background: #eff6ff;
            color: #2563eb;
        }

        .badge-senior-citizen .badge-dot {
            background: #2563eb;
        }

        .badge-family-planning {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .badge-family-planning .badge-dot {
            background: #7c3aed;
        }

        .badge-default {
            background: #f3f4f6;
            color: #6b7280;
        }

        .badge-default .badge-dot {
            background: #6b7280;
        }

        .date-cell {
            color: #6b7280;
            font-size: 0.865rem;
            white-space: nowrap;
        }

        .btn-view-record {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #374151;
            background: #fff;
            border: 1px solid #d1d5db;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-view-record:hover {
            background: #1a202c;
            color: #fff;
            border-color: #1a202c;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26, 32, 44, 0.15);
        }

        .table-card {
            border-radius: 14px;
            border: 1px solid #eef0f4;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04), 0 0 1px rgba(0, 0, 0, 0.06);
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #9ca3af;
        }

        .empty-icon {
            width: 52px;
            height: 52px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
        }

        .page-label {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        .guardian-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            vertical-align: middle;
            margin-left: 8px;
        }

        @keyframes rowFadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .records-table tbody tr {
            animation: rowFadeIn 0.2s ease both;
        }

        .records-table tbody tr:nth-child(1) {
            animation-delay: 0.04s;
        }

        .records-table tbody tr:nth-child(2) {
            animation-delay: 0.08s;
        }

        .records-table tbody tr:nth-child(3) {
            animation-delay: 0.12s;
        }

        .records-table tbody tr:nth-child(4) {
            animation-delay: 0.16s;
        }

        .records-table tbody tr:nth-child(5) {
            animation-delay: 0.20s;
        }

        .records-table tbody tr:nth-child(6) {
            animation-delay: 0.24s;
        }

        .records-table tbody tr:nth-child(7) {
            animation-delay: 0.28s;
        }

        .records-table tbody tr:nth-child(8) {
            animation-delay: 0.32s;
        }

        .records-table tbody tr:nth-child(9) {
            animation-delay: 0.36s;
        }

        .records-table tbody tr:nth-child(10) {
            animation-delay: 0.40s;
        }
    </style>
</head>

<body class="bg-white">
    @vite([
    'resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/css/patient/record.css',
    ])

    <div class="vaccination vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-x-auto">
            @include('layout.header')
            <main class="flex-column p-md-3 p-2 overflow-y-auto">

                {{-- Page heading --}}
                <div class="px-lg-5 px-md-3 px-2 mb-4">
                    <!-- <p class="page-label mb-1">Patient Dashboard</p> -->
                    <h1 class="fw-bold mb-0 fs-3 text-dark">
                        Patient Record
                        @if($isGuardian)
                        <span class="guardian-pill">
                            <i class="fa-solid fa-shield-halved" style="font-size:0.65rem;"></i>
                            Guardian
                        </span>
                        @endif
                    </h1>
                    @if(!$rows->isEmpty())
                    <p class="text-muted mt-1 mb-0" style="font-size:0.875rem;">
                        {{ $rows->count() }} {{ Str::plural('record', $rows->count()) }} found
                    </p>
                    @endif
                </div>

                {{-- Table --}}
                <div class="w-100 px-lg-5 px-md-3 px-2">
                    @if($rows->isEmpty())
                    <div class="table-card bg-white">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fa-regular fa-folder-open text-secondary fs-5"></i>
                            </div>
                            <p class="fw-semibold text-secondary mb-1">No records found</p>
                            <p class="text-muted small mb-0">No patient case records are available at this time.</p>
                        </div>
                    </div>
                    @else
                    <div class="table-card bg-white">
                        <table class="table records-table mb-0">
                            <thead>
                                <tr>
                                    <th class="row-num text-center">#</th>
                                    <th>Patient Name</th>
                                    <th>Type of Case</th>
                                    <th>Date Registered</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $row)
                                @php
                                $initials = collect(explode(' ', $row['patient_name']))
                                ->filter()
                                ->take(2)
                                ->map(fn($w) => strtoupper($w[0]))
                                ->implode('');

                                $badgeClass = match($row['type_of_case']) {
                                'vaccination' => 'badge-vaccination',
                                'prenatal' => 'badge-prenatal',
                                'tb-dots' => 'badge-tb-dots',
                                'senior-citizen' => 'badge-senior-citizen',
                                'family-planning' => 'badge-family-planning',
                                default => 'badge-default',
                                };

                                $caseLabel = match($row['type_of_case']) {
                                'vaccination' => 'Vaccination',
                                'prenatal' => 'Prenatal',
                                'tb-dots' => 'TB-DOTS',
                                'senior-citizen' => 'Senior Citizen',
                                'family-planning' => 'Family Planning',
                                default => ucfirst($row['type_of_case']),
                                };
                                @endphp
                                <tr>
                                    {{-- Row number --}}
                                    <td class="row-num">{{ $loop->iteration }}</td>

                                    {{-- Patient name with avatar --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="patient-name-cell">{{ $row['patient_name'] }}</span>
                                        </div>
                                    </td>

                                    {{-- Case type badge --}}
                                    <td>
                                        <span class="case-badge {{ $badgeClass }}">
                                            <span class="badge-dot"></span>
                                            {{ $caseLabel }}
                                        </span>
                                    </td>

                                    {{-- Date --}}
                                    <td class="date-cell">
                                        <i class="fa-regular fa-calendar me-1 opacity-50"></i>
                                        {{ \Carbon\Carbon::parse($row['date_registered'])->format('M d, Y') }}
                                    </td>

                                    {{-- Action --}}
                                    <td class="text-center">
                                        <a href="{{ route('patient.record.case', [
                                                    'patientId' => $row['patient_id'],
                                                    'caseType'  => $row['type_of_case'],
                                                ]) }}"
                                            class="btn-view-record">
                                            <i class="fa-solid fa-folder-open" style="font-size:0.75rem;"></i>
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

            </main>
        </div>
    </div>

    @if($isActive)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('patient_medical_record');
            if (con) con.classList.add('active');
        });
    </script>
    @endif
</body>

</html>