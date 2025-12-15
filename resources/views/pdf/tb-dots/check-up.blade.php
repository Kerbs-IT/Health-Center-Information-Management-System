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
    <div class="tb-dots-checkup-con">
        <h5 class="fw-bold fs-4">Tuberculosis Patient Case Record</h5>
        <div class="patient-info-con d-flex justify-content-between mb-2 gap-2">
            <div class="w-50">
                <div class="info-field">
                    <label class="fw-bold">NAME: </label>
                    <span>{{ $medicalRecord->patient->full_name ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">DATE OF BIRTH:</label>
                    <span>
                        {{
                            $medicalRecord->patient->date_of_birth
                                ? \Carbon\Carbon::parse($medicalRecord->patient->date_of_birth)
                                : 'N/A'
                        }}
                    </span>

                </div>
                <div class="info-field">
                    <label class="fw-bold">PLACE OF BIRTH: </label>
                    <span>{{$medicalRecord->patient->place_of_birth ?? 'N/A' }}</span>
                    <label class="fw-bold">RELIGION: </label>
                    <span>{{$medicalRecord->tb_dots_medical_record->religion ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">COMPLETE ADDRESS: </label>
                    <span>{{ $address?? 'N/A' }}</span>
                </div>
            </div>
            <div class="w-50">
                <div class="info-field">
                    <label class="fw-bold">AGE: </label>
                    <span>{{ $medicalRecord->patient->age?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">SEX:</label>
                    <span>{{ $medicalRecord->patient->sex?? 'N/A'}}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">CIVIL STATUS:</label>
                    <span>{{$medicalRecord->patient->civil_status?? 'N/A'}}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">PHILHEALTH ID NO:</label>
                    <span>{{ $medicalRecord->tb_Dots_medical_record->philhealth_id_no ?? 'N/A' }}</span>
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
                        <td id="view_checkup_temperature">{{$checkUpRecord -> temperature??''}}</td>
                        <th>Pulse Rate (BPM)</th>
                        <td id="view_checkup_pulse_rate">{{$checkUpRecord -> pulse_rate??''}}</td>
                    </tr>
                    <tr>
                        <th>Respiratory Rate</th>
                        <td id="view_checkup_respiratory_rate">{{$checkUpRecord -> respiratory_rate??''}}</td>
                        <th>Height (cm)</th>
                        <td id="view_checkup_height">{{$checkUpRecord -> height??''}}</td>
                        <th>Weight (kg)</th>
                        <td id="view_checkup_weight">{{$checkUpRecord -> weight??''}}</td>
                    </tr>
                </tbody>
            </table>
            <table class="table table-bordered mb-4">
                <tbody>
                    <tr>
                        <th colspan="12" class="fw-bold text-center fs-3 bg-light">TREATMENT INFORMATION</th>
                    </tr>
                    <tr>
                        <th>Adherence to Treatment</th>
                        <td id="view_checkup_adherence_of_treatment">{{$checkUpRecord ->adherence_of_treatment??''}}</td>
                    </tr>
                    <tr>
                        <th>Side Effects</th>
                        <td id="view_checkup_side_effect">{{$checkUpRecord -> side_effect??''}}</td>
                    </tr>
                    <tr>
                        <th>Progress Note</th>
                        <td id="view_checkup_progress_note">{{$checkUpRecord -> progress_note??''}}</td>
                    </tr>
                    <tr>
                        <th>Sputum Test Result</th>
                        <td id="view_checkup_sputum_test_result">{{$checkUpRecord -> sputum_test_result??''}}</td>
                    </tr>
                    <tr>
                        <th>Treatment Phase</th>
                        <td id="view_checkup_treatment_phase">{{$checkUpRecord -> treatment_phase??''}}</td>
                    </tr>
                    <tr>
                        <th>Outcome Update</th>
                        <td id="view_checkup_outcome">{{$checkUpRecord -> outcome??''}}</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</body>

</html>