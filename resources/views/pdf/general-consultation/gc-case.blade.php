<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Consultation Record</title>
</head>

<style>
    * {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        padding: 10px;
    }

    header {
        width: 100%;
        margin-bottom: 10px;
    }

    header img {
        height: 100px;
        width: 100px;
        float: left;
        margin-right: 20px;
        margin-top: 10px;
    }

    .header-text {
        width: 70%;
        text-align: center;
        text-transform: uppercase;
        float: left;
        padding-top: 15px;
    }

    .header-text h4 {
        font-size: 14px;
        margin-bottom: 4px;
    }

    .clearfix::after {
        content: "";
        display: table;
        clear: both;
    }

    .record-title {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        margin: 12px 0 16px 0;
        letter-spacing: 1px;
    }

    /* ── Patient Info ── */
    .patient-info {
        width: 100%;
        margin-bottom: 12px;
        border-collapse: collapse;
    }

    .patient-info th,
    .patient-info td {
        font-size: 13px;
        padding: 7px 10px;
        border: 1px solid #000;
    }

    .patient-info th {
        background-color: lightgray;
        font-weight: bold;
        width: 18%;
        text-align: left;
    }

    .patient-info td {
        width: 32%;
    }

    .date-consultation {
        font-size: 13px;
        margin-bottom: 14px;
        padding: 4px 6px;
    }

    .date-consultation span {
        font-weight: bold;
    }

    .date-value {
        display: inline-block;
        border-bottom: 1px solid #000;
        min-width: 180px;
        padding-bottom: 1px;
    }

    /* ── SOAP Section ── */
    .soap-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4px;
    }

    .soap-table th,
    .soap-table td {
        border: 1px solid #000;
        padding: 8px 10px;
        font-size: 13px;
        vertical-align: top;
    }

    .soap-table th {
        background-color: lightgray;
        width: 22%;
        font-weight: bold;
    }

    /* ── Vitals two-column layout inside td ── */
    .vitals-grid {
        width: 100%;
        border-collapse: collapse;
    }

    .vitals-grid td {
        border: none;
        padding: 3px 6px;
        font-size: 13px;
        width: 50%;
        vertical-align: top;
    }

    .vitals-grid .v-label {
        font-weight: bold;
        white-space: nowrap;
    }

    /* ── Footer / Signature ── */
    .signature-section {
        margin-top: 40px;
        width: 100%;
    }

    .signature-section table {
        width: 100%;
        border-collapse: collapse;
    }

    .signature-section td {
        font-size: 13px;
        padding: 4px 8px;
        width: 50%;
        vertical-align: top;
    }

    .sig-line {
        border-top: 1px solid #000;
        margin-top: 40px;
        padding-top: 4px;
        text-align: center;
        font-size: 12px;
    }
</style>

<body>

    {{-- ── HEADER ── --}}
    <header class="clearfix">
        <img src="{{ $logoSrc }}" alt="Logo">
        <div class="header-text">
            <h4>Barangay Hugo Perez Proper —</h4>
            <h4>Health Center Information Management System</h4>
        </div>
    </header>

    <div class="record-title">General Consultation Record</div>

    {{-- ── PATIENT INFO ── --}}
    <table class="patient-info">
        <tr>
            <th>Name</th>
            <td>{{ $patient->full_name }}</td>
            <th>Birthdate</th>
            <td>{{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('F d, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Complete Address</th>
            <td>{{ $address }}</td>
            <th>Age</th>
            <td>
                @php
                $dob = \Carbon\Carbon::parse($patient->date_of_birth);
                $age = $dob->age;
                $months = (int) $dob->diffInMonths(now());
                $days = (int) $dob->diffInDays(now());
                @endphp

                @if($age === 0)
                @if($months === 0)
                {{ $days }} Days old
                @else
                {{ $months }} Months old
                @endif
                @else
                {{ $age }} years old
                @endif
            </td>
        </tr>
    </table>

    <div class="date-consultation">
        <span>Date of Consultation:</span>&nbsp;
        <span class="date-value">
            {{ $caseInfo->date_of_consultation
                ? \Carbon\Carbon::parse($caseInfo->date_of_consultation)->format('F d, Y')
                : 'N/A' }}
        </span>
    </div>

    {{-- ── SOAP TABLE ── --}}
    <table class="soap-table">

        {{-- S — Symptoms / Chief Complaint --}}
        <tr>
            <th>S<br><small>(Symptoms / Chief Complaint)</small></th>
            <td colspan="2">{{ $caseInfo->symptoms ?? 'N/A' }}</td>
        </tr>

        {{-- O — Physical Exam / Vital Signs (two-column) --}}
        <tr>
            <th>O<br><small>(P.E / Vital Signs)</small></th>
            <td colspan="2">
                <table class="vitals-grid">
                    <tr>
                        <td>
                            <span class="v-label">Blood Pressure:</span><br>
                            {{ $caseInfo->blood_pressure ?? 'N/A' }} mmHg
                        </td>
                        <td>
                            <span class="v-label">Temperature:</span><br>
                            {{ $caseInfo->temperature ?? 'N/A' }} °C
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="v-label">Pulse Rate:</span><br>
                            {{ $caseInfo->pulse_rate ?? 'N/A' }} bpm
                        </td>
                        <td>
                            <span class="v-label">Respiratory Rate:</span><br>
                            {{ $caseInfo->respiratory_rate ?? 'N/A' }} breaths/min
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="v-label">Height:</span><br>
                            {{ $caseInfo->height ?? 'N/A' }} cm
                        </td>
                        <td>
                            <span class="v-label">Weight:</span><br>
                            {{ $caseInfo->weight ?? 'N/A' }} kg
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- A — Diagnosis / Assessment --}}
        <tr>
            <th>A<br><small>(Diagnosis / Assessment)</small></th>
            <td colspan="2">{{ $caseInfo->diagnosis ?? 'N/A' }}</td>
        </tr>

        {{-- P — Treatment / Plan --}}
        <tr>
            <th>P<br><small>(Treatment / Plan)</small></th>
            <td colspan="2">{{ $caseInfo->treatment_plan ?? 'N/A' }}</td>
        </tr>

    </table>



</body>

</html>