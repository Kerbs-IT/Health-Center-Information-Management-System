<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Health Center Report</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 25px;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .fw-light {
            font-weight: 300;
        }

        .text-muted {
            color: #6c757d;
        }

        h3 {
            font-size: 15px;
            margin-bottom: 8px;
            margin-top: 12px;
        }

        h5 {
            font-size: 12px;
            margin-bottom: 15px;
        }

        .section {
            margin-bottom: 20px;
        }

        .table-wrapper {
            width: 100%;
            text-align: center;
        }

        table {
            width: 100%;
            margin: 0 auto 10px auto;
            border-collapse: collapse;
            display: inline-table;
        }

        table th,
        table td {
            border: 1px solid #dee2e6;
            padding: 6px 10px;
            text-align: left;
        }

        table th {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .table-secondary {
            background-color: #e9ecef;
        }

        .page-break {
            page-break-before: always;
        }

        /* Header */
        header {
            width: 85%;
            margin: 0 auto 20px auto;
            text-align: center;
        }

        header table {
            width: 100%;
            border: none;
            margin-bottom: 0;
        }

        header table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        header img {
            height: 100px;
            width: 100px;
        }

        .header-text {
            text-align: center;
            text-transform: uppercase;
        }

        .date-footer {
            margin-top: 10px;
            font-size: 9px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <table>
            <tr>
                <td style="width: 120px; text-align: center;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/hugoperez_logo.png'))) }}" alt="hugo perez logo">
                </td>
                <td>
                    <div class="header-text">
                        <h4>Barangay Hugo Perez Proper</h4>
                        <h4>Health Center Information Management System</h4>
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <!-- Patient Overall Count -->
    <div class="section">
        <h3 class="fw-bold">Patient Overall Count</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr class="table-secondary">
                        <th>Type of Patient</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Prenatal</td>
                        <td>{{$patientCount['prenatalCount']??'0'}}</td>
                    </tr>
                    <tr>
                        <td>Senior Citizen</td>
                        <td>{{$patientCount['seniorCitizenCount']??'0'}}</td>
                    </tr>
                    <tr>
                        <td>TB Dots</td>
                        <td>{{$patientCount['tbDotsCount']??'0'}}</td>
                    </tr>
                    <tr>
                        <td>Vaccination</td>
                        <td>{{$patientCount['vaccinationCount']??'0'}}</td>
                    </tr>
                    <tr>
                        <td>Family Planning</td>
                        <td>{{$patientCount['familyPlanningCount']??'0'}}</td>
                    </tr>
                    <tr class="table-secondary">
                        <td class="fw-bold">Overall Patient</td>
                        <td class="fw-bold">{{$patientCount['overallPatients']??'0'}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Patient Added Today -->
    <div class="section">
        <h3 class="fw-bold">Patient Added Today</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr class="table-secondary">
                        <th>Type of Patient</th>
                        <th>Total Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Prenatal</td>
                        <td>{{$patientPerDay['prenatalCount']??'0'}}</td>
                    </tr>
                    <tr>
                        <td>Senior Citizen</td>
                        <td>{{$patientPerDay['seniorCitizenCount']??'0'}}</td>
                    </tr>
                    <tr>
                        <td>TB Dots</td>
                        <td>{{$patientPerDay['tbDotsCount']??'0'}}</td>
                    </tr>
                    <tr>
                        <td>Vaccination</td>
                        <td>{{$patientPerDay['vaccinationCount']??'0'}}</td>
                    </tr>
                    <tr>
                        <td>Family Planning</td>
                        <td>{{$patientPerDay['familyPlanningCount']??'0'}}</td>
                    </tr>
                    <tr class="table-secondary">
                        <td class="fw-bold">Overall Patient</td>
                        <td class="fw-bold">{{$patientPerDay['overallPatients']??'0'}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Page Break -->
    <div class="page-break"></div>
    <header>
        <table>
            <tr>
                <td style="width: 120px; text-align: center;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/hugoperez_logo.png'))) }}" alt="hugo perez logo">
                </td>
                <td>
                    <div class="header-text">
                        <h4>Barangay Hugo Perez Proper</h4>
                        <h4>Health Center Information Management System</h4>
                    </div>
                </td>
            </tr>
        </table>
    </header>
    <!-- Age Distribution Table -->
    <div class="date">
        <p> <span style="font-weight: bold;">Date range:</span> {{$startDate?? now()->format('Y-m-d') }} - {{$endDate?? now()->format('Y-m-d')}}</p>
    </div>
    <div class="section">
        <h3 class="fw-bold">Age Distribution by Patient Type</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr class="table-secondary">
                        <th>Patient Type</th>
                        <th>0-11 Months</th>
                        <th>1-5 Years</th>
                        <th>6-17 Years</th>
                        <th>18-59 Years</th>
                        <th>60+ Years</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Vaccination</td>
                        <td class="text-center">{{ $ageData['distribution']['vaccination']['0-11'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['vaccination']['1-5'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['vaccination']['6-17'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['vaccination']['18-59'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['vaccination']['60+'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Prenatal</td>
                        <td class="text-center">{{ $ageData['distribution']['prenatal']['0-11'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['prenatal']['1-5'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['prenatal']['6-17'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['prenatal']['18-59'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['prenatal']['60+'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Senior Citizen</td>
                        <td class="text-center">{{ $ageData['distribution']['seniorCitizen']['0-11'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['seniorCitizen']['1-5'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['seniorCitizen']['6-17'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['seniorCitizen']['18-59'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['seniorCitizen']['60+'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>TB DOTS</td>
                        <td class="text-center">{{ $ageData['distribution']['tbDots']['0-11'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['tbDots']['1-5'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['tbDots']['6-17'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['tbDots']['18-59'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['tbDots']['60+'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Family Planning</td>
                        <td class="text-center">{{ $ageData['distribution']['familyPlanning']['0-11'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['familyPlanning']['1-5'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['familyPlanning']['6-17'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['familyPlanning']['18-59'] ?? 0 }}</td>
                        <td class="text-center">{{ $ageData['distribution']['familyPlanning']['60+'] ?? 0 }}</td>
                    </tr>
                    <tr class="table-secondary">
                        <td class="fw-bold">TOTAL</td>
                        <td class="fw-bold text-center">{{ $ageData['totals']['0-11'] ?? 0 }}</td>
                        <td class="fw-bold text-center">{{ $ageData['totals']['1-5'] ?? 0 }}</td>
                        <td class="fw-bold text-center">{{ $ageData['totals']['6-17'] ?? 0 }}</td>
                        <td class="fw-bold text-center">{{ $ageData['totals']['18-59'] ?? 0 }}</td>
                        <td class="fw-bold text-center">{{ $ageData['totals']['60+'] ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sex Distribution Table -->
    <div class="section">
        <h3 class="fw-bold">Sex Distribution by Patient Type</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr class="table-secondary">
                        <th>Patient Type</th>
                        <th>Male</th>
                        <th>Female</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Vaccination</td>
                        <td class="text-center">{{ $sexData['distribution']['vaccination']['Male'] ?? 0 }}</td>
                        <td class="text-center">{{ $sexData['distribution']['vaccination']['Female'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Prenatal</td>
                        <td class="text-center">{{ $sexData['distribution']['prenatal']['Male'] ?? 0 }}</td>
                        <td class="text-center">{{ $sexData['distribution']['prenatal']['Female'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Senior Citizen</td>
                        <td class="text-center">{{ $sexData['distribution']['seniorCitizen']['Male'] ?? 0 }}</td>
                        <td class="text-center">{{ $sexData['distribution']['seniorCitizen']['Female'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>TB DOTS</td>
                        <td class="text-center">{{ $sexData['distribution']['tbDots']['Male'] ?? 0 }}</td>
                        <td class="text-center">{{ $sexData['distribution']['tbDots']['Female'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Family Planning</td>
                        <td class="text-center">{{ $sexData['distribution']['familyPlanning']['Male'] ?? 0 }}</td>
                        <td class="text-center">{{ $sexData['distribution']['familyPlanning']['Female'] ?? 0 }}</td>
                    </tr>
                    <tr class="table-secondary">
                        <td class="fw-bold">TOTAL</td>
                        <td class="fw-bold text-center">{{ $sexData['totals']['Male'] ?? 0 }}</td>
                        <td class="fw-bold text-center">{{ $sexData['totals']['Female'] ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="date-footer text-end">
            <span class="text-muted">Generated Date: {{ $generatedDate }}</span>
        </div>
    </div>

</body>

</html>