<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pre-Natal Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
            /* Reduce from 15px */
            position: relative;
            min-height: 110px;
            /* Reduce from 70px */
        }



        .header-content {
            display: inline-block;
            text-align: center;
            padding-top: 5px;
            /* Reduce from 10px */
        }

        .header h3 {
            font-size: 20px;
            /* Reduce from 13px */
            margin: 0;
            line-height: 1.1;
            /* Tighten from 1.2 */
            font-weight: bold;
        }

        .logo-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 100px;
            /* Reduce from 50px */
            height: 100px;
            /* Reduce from 50px */
        }

        .logo-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 120px;
            /* Reduce from 50px */
            height: 100px;
            /* Reduce from 50px */
        }

        .patient-info {
            margin-bottom: 5px;
            margin-top: 3px;
            font-size: 11px;
        }

        .info-row {
            margin-bottom: 0;
            line-height: 1.5;
            clear: both;
            position: relative;
            /* ADD THIS */
        }

        .right-field {
            position: absolute;
            /* CHANGE FROM float: right */
            right: 0;
            /* ADD THIS */
            top: 0;
            /* ADD THIS */
            border-bottom: 1px solid #000;
            padding: 0 5px;
        }

        .info-row label {
            font-size: 11px;
            font-weight: bold;
            display: inline;
            vertical-align: baseline;
        }

        .info-row span {
            font-size: 11px;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 150px;
            padding: 0 5px;
            vertical-align: baseline;
        }

        .inline-field {
            display: inline-block;
            margin-right: 15px;
            vertical-align: baseline;
        }


        .ob-history {
            margin: 10px 0;
            font-size: 11px;
        }

        .ob-history label {
            font-weight: bold;
        }

        .ob-values {
            display: inline-block;
            margin-left: 10px;
        }

        .ob-item {
            display: inline-block;
            margin-right: 8px;
        }

        .ob-item span {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 20px;
            text-align: center;
            padding: 0 3px;
        }

        .philhealth {
            display: inline-block;
            margin-left: 30px;
            font-size: 11px;
        }

        .philhealth-item {
            display: inline-block;
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 12px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 4px;
        }

        table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }

        .main-table th {
            text-align: center;
            font-size: 12px;
            padding: 3px;
        }

        .main-table td {
            text-align: center;
            font-size: 12px;
            padding: 3px;
        }

        .detail-table th {
            width: 30%;
            text-align: left;
            background-color: #fff;
            font-weight: bold;
        }

        .detail-table td {
            text-align: left;
        }

        .section-header {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            padding: 5px;
        }

        .combined-table {
            margin-bottom: 0;
        }

        .combined-table td {
            vertical-align: top;
        }

        .inner-table {
            width: 100%;
            margin: 0;
        }

        .inner-table th {
            background-color: #fff;
        }

        .date {
            text-align: center;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{$treceLogo}}" class="logo-left" alt="Trece Logo">
        <div class="header-content">
            <h3>CITY HEALTH OFFICE</h3>
            <h3>Trece Martires Cavite</h3>
            <h3>PRE - NATAL</h3>
        </div>
        <img src="{{$DOHlogo}}" class="logo-right" alt="DOH Logo">
    </div>

    <div class="patient-info">
        <div class="info-row">
            <div style="display: inline-block;">
                <label>HEAD OF THE FAMILY:</label>
                <span style="min-width: 250px;">{{ $medicalRecord->prenatal_medical_record->family_head_name ?? '' }}</span>
            </div>
            <div style="display: inline-block; width: 35%; text-align: left;">
                <label>FAMILY SERIAL NO:</label>
                <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 100px;">{{ $medicalRecord->prenatal_medical_record->family_serial_no ?? '' }}</span>
            </div>
        </div>
        <div class="info-row">
            <div style="display: inline-block; ">
                <label>NAME OF PATIENT:</label>
                <span style="min-width: 280px;">{{ $caseInfo->patient_name ?? '' }}</span>
            </div>
            <div style="display: inline-block; width: 29%; text-align: left;">
                <label>AGE:</label>
                <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 40px;">{{ $medicalRecord->patient->age ?? '' }}</span>
            </div>
        </div>
        <div class="info-row">
            <div style="display: inline-block; ">
                <label>BIRTHDAY:</label>
                <span style="min-width: 100px;">{{ $medicalRecord->patient->date_of_birth?->format('m-d-Y') ?? '' }}</span>
                <label style="margin-left: 20px;">RELIGION:</label>
                <span style="min-width: 80px;">{{ $medicalRecord->prenatal_medical_record->religion ?? '' }}</span>
            </div>
            <div style="display: inline-block; width: 29%; text-align: left;">
                <label>CIVIL STATUS:</label>
                <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 60px;">{{ $medicalRecord->patient->civil_status ?? '' }}</span>
            </div>
        </div>
        <div class="info-row">
            <div style="display: inline-block; ">
                <label>COMPLETE ADDRESS:</label>
                <span style="min-width: 300px;">{{ $address ?? '' }}</span>
            </div>
            <div style="display: inline-block; width: 30%; text-align: left;">
                <label>CONTACT NO:</label>
                <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 80px;">{{ $medicalRecord->patient->contact_number ?? '' }}</span>
            </div>
        </div>
    </div>

    <div class="ob-history">
        <label>OB HISTORY:</label>
        <div class="ob-values">
            <span class="ob-item">G <span>{{ $caseInfo->G ?? 0 }}</span></span>
            <span class="ob-item">P <span>{{ $caseInfo->P ?? 0 }}</span></span>
            <span class="ob-item">(T <span>{{ $caseInfo->T ?? 0 }}</span></span>
            <span class="ob-item">P <span>{{ $caseInfo->premature ?? 0 }}</span></span>
            <span class="ob-item">A <span>{{ $caseInfo->abortion ?? 0 }}</span></span>
            <span class="ob-item">L <span>{{ $caseInfo->living_children ?? 0 }}</span>)</span>
        </div>
        <div class="philhealth">
            <label>PHILHEALTH:</label>
            <span class="philhealth-item">Yes ( {{ $medicalRecord->prenatal_medical_record->philHealth_number != null ? '/' : '' }} )</span>
            <span class="philhealth-item">Number: <span style="border-bottom: 1px solid #000; display: inline-block; min-width: 100px; padding: 0 5px;">{{ $medicalRecord->prenatal_medical_record->philHealth_number ?? '' }}</span></span>
            <span class="philhealth-item">No ( {{ $medicalRecord->prenatal_medical_record->philHealth_number == null ? '/' : '' }} )</span>
        </div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th>YEAR OF<br>PREGNANCY</th>
                <th>TYPE OF<br>DELIVERY</th>
                <th>PLACE OF<br>DELIVERY</th>
                <th>BIRTH<br>ATTENDANT</th>
                <th>COMPLICATION</th>
                <th>OUTCOME OF PREGNANCY</th>
            </tr>
        </thead>
        <tbody>
            @php
            $records = $caseInfo->pregnancy_timeline_records ?? collect();
            $recordCount = $records->count();
            $emptyRows = 9 - $recordCount;
            @endphp

            @foreach($records as $record)
            <tr>
                <td>{{ $record->year ?? '' }}</td>
                <td>{{ $record->type_of_delivery ?? '' }}</td>
                <td>{{ $record->place_of_delivery ?? '' }}</td>
                <td>{{ $record->birth_attendant ?? '' }}</td>
                <td>{{ $record->complication ?? '' }}</td>
                <td>{{ $record->outcome ?? '' }}</td>
            </tr>
            @endforeach

            @for($i = 0; $i < $emptyRows; $i++)
                <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                </tr>
                @endfor
        </tbody>
    </table>

    <table class="combined-table">
        <thead>
            <tr>
                <th style="width: 10%;">DATE</th>
                <th style="width: 20%;">SUBJECTIVE</th>
                <th style="width: 20%;">OBJECTIVE</th>
                <th style="width: 25%;">ASSESSMENT</th>
                <th style="width: 25%;">PLANNING</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="20" style="vertical-align: top;" class="date"> {{$caseInfo->created_at ??''}}</td>
                <td>PNCU-1<sup>st</sup> time</td>
                <td>BP: {{$caseInfo -> blood_pressure?? '' }}</td>
                <td>AOG</td>
                <td></td>
            </tr>
            <tr>
                <td>G P (T P A L)</td>
                <td>WT: {{$caseInfo -> weight?$caseInfo -> weight. ' kg': '' }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>HT: {{$caseInfo -> height?$caseInfo -> height. ' cm': '' }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>LMP: {{ $caseInfo->LMP ?? '' }}</td>
                <td>Temp:{{ $caseInfo->temperature?$caseInfo->temperature. '°C' :'' }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>EDC: {{ $caseInfo->expected_delivery ?? '' }}</td>
                <td>RR: {{$caseInfo -> respiratory_rate? $caseInfo -> respiratory_rate: '' }}</td>
                <td>({{$caseInfo->prenatal_assessment->spotting == 'yes'?'/':''}}) spotting</td>
                <td></td>
            </tr>
            <tr>
                <td>Menarche: {{ $caseInfo->menarche ?? ' ' }}</td>
                <td>PR: {{$caseInfo -> pulse_rate?$caseInfo -> pulse_rate : '' }}</td>
                <td>({{$caseInfo->prenatal_assessment->edema == 'yes'?'/':' '}}) edema</td>
                <td></td>
            </tr>
            <tr>
                <td>TT status:</td>
                <td></td>
                <td>({{$caseInfo->prenatal_assessment->severe_headache == 'yes'?'/':' '}}) severe headache</td>
                <td></td>
            </tr>
            <tr>
                <td>TT1: {{ $caseInfo->tetanus_toxoid_1 ?? '' }}</td>
                <td></td>
                <td>({{$caseInfo->prenatal_assessment->blumming_vission == 'yes'?'/':' '}}) blurring of vision</td>
                <td></td>
            </tr>
            <tr>
                <td>TT2: {{ $caseInfo->tetanus_toxoid_2 ?? '' }}</td>
                <td></td>
                <td>({{$caseInfo->prenatal_assessment->water_discharge == 'yes'?'/':' '}}) watery discharge</td>
                <td></td>
            </tr>
            <tr>
                <td>TT3: {{ $caseInfo->tetanus_toxoid_3 ?? '' }}</td>
                <td></td>
                <td>({{$caseInfo->prenatal_assessment->severe_vomitting == 'yes'?'/':' '}}) severe vomiting</td>
                <td></td>
            </tr>
            <tr>
                <td>TT4: {{ $caseInfo->tetanus_toxoid_4 ?? '' }}</td>
                <td></td>
                <td>({{$caseInfo->prenatal_assessment->hx_smoking == 'yes'?'/':' '}}) Hx of smoking</td>
                <td></td>
            </tr>
            <tr>
                <td>TT5: {{ $caseInfo->tetanus_toxoid_5 ?? '' }}</td>
                <td></td>
                <td>({{$caseInfo->prenatal_assessment->alchohol_drinker == 'yes'?'/':' '}}) Alcoholic drinker</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>({{$caseInfo->prenatal_assessment->drug_intake == 'yes'?'/':' '}}) Drug Intake</td>
                <td></td>
            </tr>
            <tr>
                <td>Check up to other health<br>facility</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Date:</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>

</html>