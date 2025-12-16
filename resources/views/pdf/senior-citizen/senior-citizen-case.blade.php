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
    <div class="senior-citizen-con">
        <h5 class="mb-0 text-center fw-bold">SENIOR CITIZEN CASE RECORD</h5>
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
                    <span>{{$medicalRecord->senior_citizen_medical_record->religion ?? 'N/A' }}</span>
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
                    <label class="fw-bold">SSS MEMBER:</label>
                    <span>{{ $medicalRecord->senior_citizen_medical_record->SSS ?? 'N/A' }}</span>
                </div>
            </div>

        </div>
        <div class="contents ">
            <table class="table table-bordered  table-light">
                <tbody>
                    <!-- Section Header -->
                    <tr>
                        <td colspan="7" class=" text-uppercase fw-bold ">
                            Medical Information
                        </td>
                    </tr>

                    <!-- Existing Medical Condition -->
                    <tr>
                        <td colspan="2" class="w-25 fw-semibold">Existing Medical Condition:</td>
                        <td colspan="5" class="w-75 bg-white text-center" id="view_existing_medical_condition ">{{$seniorCaseRecord->existing_medical_condition??''}}</td>
                    </tr>

                    <!-- Allergies -->
                    <tr>
                        <td colspan="2" class="w-25 fw-semibold">Allergies:</td>
                        <td colspan="5" class="w-75 bg-white text-center" id="view_alergies">{{$seniorCaseRecord->alergies??''}}</td>
                    </tr>
                </tbody>

            </table>

            <!-- table -->
            <table class="w-100 table table-bordered  table-light">
                <thead>
                    <tr class="table-header">
                        <th>Maintenance Medication</th>
                        <th>Dosage & Frequency</th>
                        <th>Duration</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody id="viewCaseBody">
                    @forelse($seniorCaseRecord->senior_citizen_maintenance_med as $record)
                    <tr>
                        <td>{{$record->maintenance_medication??''}}</td>
                        <td>{{$record->dosage_n_frequency??''}}</td>
                        <td>{{$record->quantity??''}}</td>
                        <td>{{$record->start_date??''}}</td>
                        <td>{{$record->end_date??''}}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center">No record available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- nurse -->
            <table class="table table-bordered">
                <tr>
                    <td colspan="2" class="w-25 fw-semibold bg-light">Prescribe by:</td>
                    <td colspan="5" class="w-75 bg-white" id="view_prescribe_by_nurse">Nurse Joy</td>
                </tr>
                <tr>
                    <td colspan="2" class="bg-light">Remarks*</td>
                    <td colspan="5" id="view_remarks">{{$seniorCaseRecord-> remarks??'none'}}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>