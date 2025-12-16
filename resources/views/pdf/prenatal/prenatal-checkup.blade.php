<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    @vite([
    'resources/css/app.css',
    'resources/css/pdfTableTemplate.css'])
    <div class="inner w-100 rounded">
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
                    <td id="check_up_time">{{ \Carbon\Carbon::createFromFormat('H:i:s', $pregnancy_checkup_info->check_up_time)->format('h:i') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <h5 class="mt-4">Vital Signs</h5>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Blood Pressure</th>
                    <td id="check_up_blood_pressure">{{$pregnancy_checkup_info->check_up_blood_pressure??''}}</td>
                    <th>Temperature</th>
                    <td id="check_up_temperature">{{$pregnancy_checkup_info->check_up_temperature??''}}°C</td>
                </tr>
                <tr>
                    <th>Pulse Rate</th>
                    <td id="check_up_pulse_rate">{{$pregnancy_checkup_info->check_up_pulse_rate??''}} bpm</td>
                    <th>Respiratory Rate</th>
                    <td id="check_up_respiratory_rate">{{$pregnancy_checkup_info->respiratory_rate??''}}</td>
                </tr>
                <tr>
                    <th>Height</th>
                    <td id="check_up_height">{{$pregnancy_checkup_info->check_up_height??''}} cm</td>
                    <th>Weight</th>
                    <td id="check_up_weight">{{$pregnancy_checkup_info->weight??''}} kg</td>
                </tr>
            </tbody>
        </table>

        <h5 class="mt-4">Prenatal Symptoms and Concerns</h5>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>1. Pain in abdomen/back</th>
                    <td id="abdomen_question">{{$pregnancy_checkup_info->abdomen_question??''}}</td>
                    <th>Remarks</th>
                    <td id="abdomen_question_remarks">{{$pregnancy_checkup_info->abdomen_question_remarks??''}}</td>
                </tr>
                <tr>
                    <th>2. Vaginal bleeding/spotting</th>
                    <td id="vaginal_question">{{$pregnancy_checkup_info->vaginal_question??''}}</td>
                    <th>Remarks</th>
                    <td id="vaginal_question_remarks">{{$pregnancy_checkup_info->vaginal_question_remarks??''}}</td>
                </tr>
                <tr>
                    <th>3. Swelling hands/feet/face</th>
                    <td id="swelling_question">{{$pregnancy_checkup_info->swelling_question??''}}</td>
                    <th>Remarks</th>
                    <td id="swelling_question_remarks">{{$pregnancy_checkup_info->swelling_question_remarks??''}}</td>
                </tr>
                <tr>
                    <th>4. Persistent headache</th>
                    <td id="headache_question">{{$pregnancy_checkup_info->headache_question??''}}</td>
                    <th>Remarks</th>
                    <td id="headache_question_remarks">{{$pregnancy_checkup_info->headache_question_remarks??''}}</td>
                </tr>
                <tr>
                    <th>5. Blurry vision</th>
                    <td id="blurry_vission_question">{{$pregnancy_checkup_info->blurry_vission_question??''}}</td>
                    <th>Remarks</th>
                    <td id="blurry_vission_question_remarks">{{$pregnancy_checkup_info->blurry_vission_question_remarks??''}}</td>
                </tr>
                <tr>
                    <th>6. Painful/frequent urination</th>
                    <td id="urination_question">{{$pregnancy_checkup_info->urination_question??''}}</td>
                    <th>Remarks</th>
                    <td id="urination_question_remarks">{{$pregnancy_checkup_info->urination_question_remarks??''}}</td>
                </tr>
                <tr>
                    <th>7. Felt baby move</th>
                    <td id="baby_move_question">{{$pregnancy_checkup_info->baby_move_question??''}}</td>
                    <th>Remarks</th>
                    <td id="baby_move_question_remarks">{{$pregnancy_checkup_info->baby_move_question_remarks??''}}</td>
                </tr>
                <tr>
                    <th>8. Decreased baby movement</th>
                    <td id="decreased_baby_movement">{{$pregnancy_checkup_info->decreased_baby_movement??''}}</td>
                    <th>Remarks</th>
                    <td id="decreased_baby_movement_remarks">{{$pregnancy_checkup_info->decreased_baby_movement_remarks??''}}</td>
                </tr>
                <tr>
                    <th>9. Other concerns/symptoms</th>
                    <td id="other_symptoms_question">{{$pregnancy_checkup_info->other_symptoms_question??''}}</td>
                    <th>Remarks</th>
                    <td id="other_symptoms_question_remarks">{{$pregnancy_checkup_info->other_symptoms_question_remarks??''}}</td>
                </tr>
                <tr>
                    <th>Overall Remarks</th>
                    <td colspan="3" id="overall_remarks">{{$pregnancy_checkup_info->overall_remarks??''}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>