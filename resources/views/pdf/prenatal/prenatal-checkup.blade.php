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
            height: 100%;
            width: 100%;

        }

        table {
            border: 1px solid black;
            border-collapse: collapse;
            width: 100%;
        }

        tr,
        td,
        th {
            border: 1px solid black;
            padding: 10px;
            font-size: 11px;
        }

        th {
            text-align: left;
            padding-left: 10px;
            background-color: lightgray;
        }

        h5 {
            font-size: 20px !important;
            margin: 10px 0;
        }

        /* header */
        header {
            width: 100%;

        }

        header img {
            height: 120px;
            width: 120px;
            float: left;
            margin-right: 10px;
            margin-left: 20px;
            margin-top: 20px;
        }

        .header-text {
            width: 70%;
            text-align: center;
            text-transform: uppercase;
            float: left;
            padding-top: 50px;
            /* Adjust to vertically center text */
        }

        .inner {
            width: 80%;
            /* Adjust this percentage as needed */
            margin: 0 auto;
            /* This centers it horizontally */
            padding: 20px;
            /* if you want background */
        }

        .title-text {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <header>
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/hugoperez_logo.png'))) }}" alt="hugo perez logo">
        <div class="header-text">
            <h4>Barangay Hugo Perez Proper -</h4>
            <h4>Health Center Information Management System</h4>
        </div>
        <div style="clear: both;"></div>
    </header>
    <div class="inner w-100 rounded">
        <h3 class="title-text">PRE-NATAL CHECK-UP</h3>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Patient Name</th>
                    <td id="checkup_patient_name">{{$pregnancy_checkup_info->patient_name??''}}</td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td id="health_worker_name">{{$pregnancy_checkup_info->created_at?->format('m-d-Y')??''}}</td>
                </tr>
                <tr>
                    <th>Time</th>
                    <td id="check_up_time">@if($pregnancy_checkup_info->check_up_time)
                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $pregnancy_checkup_info->check_up_time)->format('h:i A') }}
                        @else
                        N/A
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <h5 class="mt-4">Vital Signs</h5>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Blood Pressure</th>
                    <td id="check_up_blood_pressure">{{$pregnancy_checkup_info->check_up_blood_pressure?$pregnancy_checkup_info->check_up_blood_pressure:'N/A'}}</td>
                    <th>Temperature</th>
                    <td id="check_up_temperature">{{$pregnancy_checkup_info->check_up_temperature?$pregnancy_checkup_info->check_up_temperature . "°C":'N/A'}}</td>
                </tr>
                <tr>
                    <th>Pulse Rate</th>
                    <td id="check_up_pulse_rate">{{$pregnancy_checkup_info->check_up_pulse_rate?$pregnancy_checkup_info->check_up_pulse_rate . "bpm" :'N/A'}} </td>
                    <th>Respiratory Rate</th>
                    <td id="check_up_respiratory_rate">{{$pregnancy_checkup_info->respiratory_rate??'N/A'}}</td>
                </tr>
                <tr>
                    <th>Height</th>
                    <td id="check_up_height">{{$pregnancy_checkup_info->check_up_height? $pregnancy_checkup_info->check_up_height . "cm":'N/A'}} </td>
                    <th>Weight</th>
                    <td id="check_up_weight">{{$pregnancy_checkup_info->weight?$pregnancy_checkup_info->weight . "kg" :'N/A'}}</td>
                </tr>
            </tbody>
        </table>

        <h5 class="mt-4">Prenatal Symptoms and Concerns</h5>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>1. Pain in abdomen/back</th>
                    <td id="abdomen_question">{{$pregnancy_checkup_info->abdomen_question??'N/A'}}</td>
                    <th>Remarks</th>
                    <td id="abdomen_question_remarks">{{$pregnancy_checkup_info->abdomen_question_remarks??'N/A'}}</td>
                </tr>
                <tr>
                    <th>2. Vaginal bleeding/spotting</th>
                    <td id="vaginal_question">{{$pregnancy_checkup_info->vaginal_question??'N/A'}}</td>
                    <th>Remarks</th>
                    <td id="vaginal_question_remarks">{{$pregnancy_checkup_info->vaginal_question_remarks??'N/A'}}</td>
                </tr>
                <tr>
                    <th>3. Swelling hands/feet/face</th>
                    <td id="swelling_question">{{$pregnancy_checkup_info->swelling_question??'N/A'}}</td>
                    <th>Remarks</th>
                    <td id="swelling_question_remarks">{{$pregnancy_checkup_info->swelling_question_remarks??'N/A'}}</td>
                </tr>
                <tr>
                    <th>4. Persistent headache</th>
                    <td id="headache_question">{{$pregnancy_checkup_info->headache_question??'N/A'}}</td>
                    <th>Remarks</th>
                    <td id="headache_question_remarks">{{$pregnancy_checkup_info->headache_question_remarks??'N/A'}}</td>
                </tr>
                <tr>
                    <th>5. Blurry vision</th>
                    <td id="blurry_vission_question">{{$pregnancy_checkup_info->blurry_vission_question??'N/A'}}</td>
                    <th>Remarks</th>
                    <td id="blurry_vission_question_remarks">{{$pregnancy_checkup_info->blurry_vission_question_remarks??'N/A'}}</td>
                </tr>
                <tr>
                    <th>6. Painful/frequent urination</th>
                    <td id="urination_question">{{$pregnancy_checkup_info->urination_question??'N/A'}}</td>
                    <th>Remarks</th>
                    <td id="urination_question_remarks">{{$pregnancy_checkup_info->urination_question_remarks??'N/A'}}</td>
                </tr>
                <tr>
                    <th>7. Felt baby move</th>
                    <td id="baby_move_question">{{$pregnancy_checkup_info->baby_move_question??'N/A'}}</td>
                    <th>Remarks</th>
                    <td id="baby_move_question_remarks">{{$pregnancy_checkup_info->baby_move_question_remarks??'N/A'}}</td>
                </tr>
                <tr>
                    <th>8. Decreased baby movement</th>
                    <td id="decreased_baby_movement">{{$pregnancy_checkup_info->decreased_baby_movement??'N/A'}}</td>
                    <th>Remarks</th>
                    <td id="decreased_baby_movement_remarks">{{$pregnancy_checkup_info->decreased_baby_movement_remarks??'N/A'}}</td>
                </tr>
                <tr>
                    <th>9. Other concerns/symptoms</th>
                    <td id="other_symptoms_question">{{$pregnancy_checkup_info->other_symptoms_question??'N/A'}}</td>
                    <th>Remarks</th>
                    <td id="other_symptoms_question_remarks">{{$pregnancy_checkup_info->other_symptoms_question_remarks??'N/A'}}</td>
                </tr>
                <tr>
                    <th>Overall Remarks</th>
                    <td colspan="3" id="overall_remarks">{{$pregnancy_checkup_info->overall_remarks??'N/A'}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>