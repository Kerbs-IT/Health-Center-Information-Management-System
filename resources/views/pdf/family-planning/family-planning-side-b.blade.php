<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        .patient-info-con{
            height: 700px;
        }
    </style>
</head>

<body>
    @vite(['resources/css/app.css',

    'resources/css/profile.css',
    'resources/css/patient/add-patient.css',
    'resources/css/patient/record.css',
    'resources/js/family_planning/editPatientCase.js'])
    <div class="d-flex justify-content-between">
        <h4 class="fw-bold">SIDE B</h4>
        <h4 class="fw-bold">FP FORM 1</h4>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th colspan="12" class="text-center"> FAMILY PLANNING CLIENT ASSESSMENT RECORD</th>
            </tr>
            <tr>
                <th class="align-middle">
                    <h5>DATE OF VISIT</h5>
                    <h6>(MM/DD/YYYY)</h6>
                </th>
                <th class="align-middle">
                    <h5 class="">MEDICAL FINDINGS</h5>
                    <h6>(Medical observation, complaints/complication, service rendered/procedures,laboratory examination, treatment and referrals)</h6>
                </th>
                <th class="align-middle">
                    METHOD ACCEPTED
                </th>
                <th class="align-middle">
                    NAME AND SIGNATURE OF SERVICE PROVIDER
                </th>
                <th class="align-middle">
                    <h5>DATE OF FOLLOW-UP VISIT</h5>
                    <h6>(MM/DD/YYYY)</h6>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="patient-info-con">
                <td id="view_date_of_visit">{{$sideBrecord->date_of_visit??''}}</td>
                <td class="text-start">
                    <pre id="view_medical_findings" style="white-space: pre; font-family: monospace;">{{$sideBrecord->medical_findings??''}}</pre>
                </td>
                <td id="view_method_accepted">{{$sideBrecord->method_accepted??''}}</td>
                <td id="view_signature_of_the_provider">{{$sideBrecord->signature_of_the_provider??''}}</td>
                <td id="view_date_of_follow_up_visit">{{$sideBrecord->date_of_follow_up_visit??''}}</td>

            </tr>
            <tr class="bg-light">
                <td colspan="4" class="text-start align-middle">
                    <h5 class="mb-0">How to Reasonable sure a Client is Not Pregnant</h5>
                </td>
                <td>Answer</td>
            </tr>
            <tr>
                <td colspan="4" class="text-start">1. Did you have a baby less than six (6) months ago, are you fully or nearly-fully breastfeeding, and have you had no menstrual period since then?</td>
                <td id="view_baby_Less_than_six_months_question">{{$sideBrecord->baby_Less_than_six_months_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-start">2. Have you abstained from sexual intercourse since your last menstrual period or delivery?</td>
                <td id="view_sexual_intercouse_or_mesntrual_period_question">{{$sideBrecord->sexual_intercouse_or_mesntrual_period_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-start">3. Have you had a baby in the last four (4) weeks</td>
                <td id="view_baby_last_4_weeks_question">{{$sideBrecord->baby_last_4_weeks_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-start">4. Did your last menstrual period start within the past seven (7) days</td>
                <td id="view_menstrual_period_in_seven_days_question">{{$sideBrecord->menstrual_period_in_seven_days_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-start">5. Have you had a miscarriage or abortion in the last seven (7) days?</td>
                <td id="view_miscarriage_or_abortion_question">{{$sideBrecord->miscarriage_or_abortion_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-start">6. Have you been using reliable contraceptive method consistenly and correctly?</td>
                <td id="view_contraceptive_question">{{$sideBrecord->contraceptive_question??'N/A'}}</td>
            </tr>
            <tr>
                <td colspan="6">- If the client answered YES to at least one of the questions and she is free of signs or sign or symptoma of pregnancy, provide client with desired method</td>

            </tr>
            <tr>
                <td colspan="6">- If the client answered NO to all the question, pregnancy cannot be ruled out. the client should await menses or use a pregnancy test</td>
            </tr>

        </tbody>
    </table>
</body>

</html>