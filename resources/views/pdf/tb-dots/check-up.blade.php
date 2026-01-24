<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            width: 100%;
            padding: 10px;
        }

        /* Header */
        header {
            width: 80%;
            margin: 0 auto 10px auto;
            overflow: hidden;
        }

        header img {
            height: 120px;
            width: 120px;
            float: left;
            margin-right: 10px;
        }

        .header-text {
            float: left;
            padding-top: 35px;
            width: calc(100% - 200px);
            text-align: center;
            text-transform: uppercase;
        }

        .header-text h4 {
            margin: 5px 0;
            font-size: 14px;
        }

        /* Main Container */
        .tb-dots-checkup-con {
            width: 80%;
            margin: 0 auto;
            padding: 0px 10px;
        }

        h5 {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            text-transform: uppercase;
        }

        h6 {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0 5px 0;
        }

        /* Patient Info Section */
        .patient-info-con {
            margin-bottom: 20px;
            width: 100%;
            overflow: hidden;
        }

        .info-column {
            width: 49%;
            float: left;
            vertical-align: top;
        }

        .info-column:first-child {
            margin-right: 2%;
        }

        .info-field {
            margin-bottom: 10px;
            font-size: 11px;
            line-height: 1.8;
        }

        .info-field label {
            font-weight: bold;
        }

        /* Tables */
        table {
            border: 1px solid black;
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 15px;
        }

        tr,
        td,
        th {
            border: 1px solid black;
            padding: 8px;
            font-size: 11px;
        }

        th {
            text-align: left;
            background-color: #d3d3d3;
            font-weight: bold;
        }

        td {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .fw-bold {
            font-weight: bold;
        }

        .fw-semibold {
            font-weight: 600;
        }

        .bg-light {
            background-color: #d3d3d3;
        }

        .bg-white {
            background-color: #ffffff;
        }

        .w-25 {
            width: 25%;
        }

        .w-75 {
            width: 75%;
        }

        /* Clear floats */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .text-decoration-underline {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <header class="clearfix">
        <img src="{{public_path('images/hugoperez_logo.png')}}" alt="hugo perez logo">
        <div class="header-text">
            <h4>Barangay Hugo Perez Proper -</h4>
            <h4>Health Center Information Management System</h4>
        </div>
    </header>
    <div class="tb-dots-checkup-con">
        <h5 class="fw-bold fs-4">Tuberculosis Patient Check-Up Record</h5>
        <div class="patient-info-con clearfix">
            <div class="info-column">
                <div class="info-field">
                    <label class="fw-bold">NAME: </label>
                    <span class="text-decoration-underline ">{{ $medicalRecord->patient->full_name ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">DATE OF BIRTH:</label>
                    <span class="text-decoration-underline ">
                        {{
                            $medicalRecord->patient->date_of_birth
                                ? \Carbon\Carbon::parse($medicalRecord->patient->date_of_birth)->format('Y-m-d H:i:s')
                                : 'N/A'
                        }}
                    </span>

                </div>
                <div class="info-field">
                    <label class="fw-bold">PLACE OF BIRTH: </label>
                    <span class="text-decoration-underline ">{{$medicalRecord->patient->place_of_birth ?? 'N/A' }}</span>

                </div>
                <div class="info-field">
                    <label class="fw-bold">RELIGION: </label>
                    <span class="text-decoration-underline ">{{$medicalRecord->tb_dots_medical_record->religion ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">COMPLETE ADDRESS: </label>
                    <span class="text-decoration-underline ">{{ $address?? 'N/A' }}</span>
                </div>
            </div>
            <div class="info-column">
                <div class="info-field">
                    <label class="fw-bold">AGE: </label>
                    <span class="text-decoration-underline ">{{ $medicalRecord->patient->age?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">SEX:</label>
                    <span class="text-decoration-underline ">{{ $medicalRecord->patient->sex?? 'N/A'}}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">CIVIL STATUS:</label>
                    <span class="text-decoration-underline ">{{$medicalRecord->patient->civil_status?? 'N/A'}}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">PHILHEALTH ID NO:</label>
                    <span class="text-decoration-underline ">{{ $medicalRecord->tb_dots_medical_record->philhealth_id_no ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        <!-- content -->
        <div class="card-body">

            <table class="table table-bordered mb-4">
                <tbody>
                    <tr>
                        <th>Date of Visit</th>
                        <td id="view_checkup_date_of_visit">{{$checkUpRecord -> date_of_visit??''}}</td>
                    </tr>
                </tbody>
            </table>

            <h6 class="fw-bold">Vital Signs</h6>
            <table class="table table-bordered mb-4">
                <tbody>
                    <tr>
                        <th>Blood Pressure</th>
                        <td id="view_checkup_blood_pressure">{{$checkUpRecord -> blood_pressure??''}}</td>
                        <th>Temperature (°C)</th>
                        <td id="view_checkup_temperature">{{$checkUpRecord -> temperature? $checkUpRecord->temperature. '°C' :'N/A' }}</td>
                        <th>Pulse Rate (BPM)</th>
                        <td id="view_checkup_pulse_rate">{{$checkUpRecord -> pulse_rate?$checkUpRecord-> pulse_rate : 'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Respiratory Rate</th>
                        <td id="view_checkup_respiratory_rate">{{$checkUpRecord -> respiratory_rate?$checkUpRecord -> respiratory_rate :'N/A'}}</td>
                        <th>Height (cm)</th>
                        <td id="view_checkup_height">{{$checkUpRecord -> height? $checkUpRecord -> height . ' cm': 'N/A'}}</td>
                        <th>Weight (kg)</th>
                        <td id="view_checkup_weight">{{$checkUpRecord -> weight? $checkUpRecord -> weight . ' kg': 'N/A'}}</td>
                    </tr>
                </tbody>
            </table>
            <table class="table table-bordered mb-4">
                <tbody>
                    <tr>
                        <th colspan="2" class="fw-bold text-center fs-3 bg-light">TREATMENT INFORMATION</th>
                    </tr>
                    <tr>
                        <th>Adherence to Treatment</th>
                        <td id="view_checkup_adherence_of_treatment">{{$checkUpRecord ->adherence_of_treatment??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Side Effects</th>
                        <td id="view_checkup_side_effect">{{$checkUpRecord -> side_effect??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Progress Note</th>
                        <td id="view_checkup_progress_note">{{$checkUpRecord -> progress_note??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Sputum Test Result</th>
                        <td id="view_checkup_sputum_test_result">{{$checkUpRecord -> sputum_test_result??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Treatment Phase</th>
                        <td id="view_checkup_treatment_phase">{{$checkUpRecord -> treatment_phase??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Outcome Update</th>
                        <td id="view_checkup_outcome">{{$checkUpRecord -> outcome??'N/A'}}</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</body>

</html>