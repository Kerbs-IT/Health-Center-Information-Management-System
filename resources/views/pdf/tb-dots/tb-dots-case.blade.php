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
    <div class="tb-dots-con">
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
        <div class="tb-dots card shadow p-4 w-100">
            <table class="table table-bordered">
                <tbody>

                    <tr>
                        <th>Type of Tuberculosis (TB)</th>
                        <td id="view_type_of_tuberculosis">{{$caseRecord->type_of_tuberculosis??''}}</td>
                    </tr>
                    <tr>
                        <th>Type of TB Case</th>
                        <td id="view_type_of_tb_case">{{$caseRecord->type_of_tb_case??''}}</td>
                    </tr>
                    <tr>
                        <th>Date of Diagnosis</th>
                        <td id="view_date_of_diagnosis">{{$caseRecord->date_of_diagnosis??''}}</td>
                    </tr>
                    <tr>
                        <th>Name of Physician</th>
                        <td id="view_name_of_physician">{{$caseRecord->name_of_physician??''}}</td>
                    </tr>
                    <tr>
                        <th>Sputum Test Results</th>
                        <td id="view_sputum_test_results">{{$caseRecord->sputum_test_results??''}}</td>
                    </tr>

                </tbody>
            </table>

            <h4 class="border-bottom mt-4">Medication List</h4>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr class="table-header">
                        <th>Medicine Name</th>
                        <th>Dosage & Frequency</th>
                        <th>Quality</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody id="view-table-body">
                    @forelse($caseRecord->tb_dots_maintenance_med as $maintenance)
                    <tr>
                        <td>{{$maintenance->medicine_name??''}}</td>
                        <td>{{$maintenance->dosage_n_frequency??''}}</td>
                        <td>{{$maintenance->quantity??''}}</td>
                        <td>{{$maintenance->start_date??''}}</td>
                        <td>{{$maintenance->end_date??''}}</td>
                    </tr>
                    @empty
                    <td colspan="12" class="text-center">No record is available</td>
                    @endforelse
                </tbody>
            </table>

            <table class="table table-bordered mt-4">
                <tbody>
                    <tr>
                        <th>Treatment Category</th>
                        <td id="view_treatment_category">{{$caseRecord->treatment_category??''}}</td>
                    </tr>
                    <tr>
                        <th>Assigned Health Worker</th>
                        <td id="view_assigned_health_worker">{{$healthWorker->full_name??''}}</td>
                    </tr>
                </tbody>
            </table>

            <h4 class="border-bottom mt-4">Monitoring & Progress</h4>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Date of Diagnosis</th>
                        <td id="view_date_administered">{{$caseRecord->date_administered??''}}</td>
                    </tr>
                    <tr>
                        <th>Side Effects</th>
                        <td id="view_side_effect">{{$caseRecord->side_effect??''}}</td>
                    </tr>
                    <tr>
                        <th>Remarks</th>
                        <td id="view_remarks">{{$caseRecord->remarks??''}}</td>
                    </tr>
                    <tr>
                        <th>Outcome</th>
                        <td id="view_outcome">{{$caseRecord->outcome??''}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>