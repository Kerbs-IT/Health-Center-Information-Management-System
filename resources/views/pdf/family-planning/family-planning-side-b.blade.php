<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FP FORM 1 - SIDE B</title>
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
            width: 100%;
            margin-bottom: 10px;
        }

        .header-left {
            float: left;
            width: 50%;
            font-weight: bold;
            font-size: 14px;
        }

        .header-right {
            float: right;
            width: 50%;
            text-align: right;
            font-weight: bold;
            font-size: 14px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .table-title {
            background-color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .patient-info-row {
            height: 300px;
        }

        .patient-info-row td {
            height: 300px;
            min-height: 300px;
        }

        .patient-info-row .medical-findings-cell {
            padding: 10px;
            vertical-align: top;
        }

        .bg-light {
            background-color: #f8f9fa;
        }

        h5 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        h6 {
            font-size: 10px;
            font-weight: normal;
            margin-top: 2px;
        }

        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: monospace;
            margin: 0;
            text-align: left;
        }

        .align-middle {
            vertical-align: middle;
        }

        .question-cell {
            text-align: left;
            padding-left: 10px;
        }
    </style>
</head>

<body>
    <div class="header clearfix">
        <div class="header-left">SIDE B</div>
        <div class="header-right">FP FORM 1</div>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="5" class="table-title">FAMILY PLANNING CLIENT ASSESSMENT RECORD</th>
            </tr>
            <tr>
                <th class="align-middle" style="width: 12%;">
                    <h5>DATE OF VISIT</h5>
                    <h6>(MM/DD/YYYY)</h6>
                </th>
                <th class="align-middle" style="width: 40%;">
                    <h5>MEDICAL FINDINGS</h5>
                    <h6>(Medical observation, complaints/complication, service rendered/procedures, laboratory examination, treatment and referrals)</h6>
                </th>
                <th class="align-middle" style="width: 15%;">
                    METHOD ACCEPTED
                </th>
                <th class="align-middle" style="width: 20%;">
                    NAME AND SIGNATURE OF SERVICE PROVIDER
                </th>
                <th class="align-middle" style="width: 13%;">
                    <h5>DATE OF FOLLOW-UP VISIT</h5>
                    <h6>(MM/DD/YYYY)</h6>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="patient-info-row">
                <td>{{$sideBrecord->date_of_visit??''}}</td>
                <td class="text-left ">
                    <pre>{{$sideBrecord->medical_findings??''}}</pre>
                </td>
                <td>{{$sideBrecord->method_accepted??''}}</td>
                <td>

                    @if($sideBrecord->signature_of_the_provider != null)
                    <img src="{{ storage_path('app/public/' . $sideBrecord->signature_of_the_provider) }}" alt="Signature" style="max-width:150px; height:50px; vertical-align: top;">
                    @else
                    <div>wwww</div>
                    @endif

                </td>
                <td>{{$sideBrecord->date_of_follow_up_visit??''}}</td>
            </tr>
            <tr class="bg-light">
                <td colspan="4" class="text-left align-middle">
                    <h5>How to Reasonably sure a Client is Not Pregnant</h5>
                </td>
                <td><strong>Answer</strong></td>
            </tr>
            <tr>
                <td colspan="4" class="question-cell">1. Did you have a baby less than six (6) months ago, are you fully or nearly-fully breastfeeding, and have you had no menstrual period since then?</td>
                <td>{{$sideBrecord->baby_Less_than_six_months_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="4" class="question-cell">2. Have you abstained from sexual intercourse since your last menstrual period or delivery?</td>
                <td>{{$sideBrecord->sexual_intercouse_or_mesntrual_period_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="4" class="question-cell">3. Have you had a baby in the last four (4) weeks?</td>
                <td>{{$sideBrecord->baby_last_4_weeks_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="4" class="question-cell">4. Did your last menstrual period start within the past seven (7) days?</td>
                <td>{{$sideBrecord->menstrual_period_in_seven_days_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="4" class="question-cell">5. Have you had a miscarriage or abortion in the last seven (7) days?</td>
                <td>{{$sideBrecord->miscarriage_or_abortion_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="4" class="question-cell">6. Have you been using reliable contraceptive method consistently and correctly?</td>
                <td>{{$sideBrecord->contraceptive_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="5" class="text-left">- If the client answered YES to at least one of the questions and she is free of signs or sign or symptoms of pregnancy, provide client with desired method</td>
            </tr>
            <tr>
                <td colspan="5" class="text-left">- If the client answered NO to all the questions, pregnancy cannot be ruled out. The client should await menses or use a pregnancy test</td>
            </tr>
        </tbody>
    </table>
</body>

</html>