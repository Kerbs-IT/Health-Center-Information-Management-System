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
    <div class="prenatal-con">
        <div class="content d-flex justify-content-between w-100 align-items-center mb-3">
            <img src="{{$treceLogo}}" class="pdf-logo">
            <div class="text-content">
                <h5 class="text-center fw-bold">CITH HEALTH OFFICE</h5>
                <h5 class="text-center fw-bold">Trece Martires Cavite</h5>
                <h5 class="text-center fw-bold">PRE-NATAL</h5>
            </div>
            <img src="{{ $DOHlogo }}" alt="logo" class="pdf-logo">
        </div>
        <div class="patient-info-con d-flex justify-content-between mb-2 gap-2">
            <div class="w-50">
                <div class="info-field">
                    <label class="fw-bold">HEAD OF THE FAMILY: </label>
                    <span>{{ $medicalRecord->prenatal_medical_record->family_head_name ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">NAME OF PATIENT:</label>
                    <span>{{ $caseInfo-> patient_name ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">BIRTH DAY: </label>
                    <span>{{$medicalRecord->patient->date_of_birth?->format('m-d-Y') ?? 'N/A' }}</span>
                    <label class="fw-bold">RELIGION: </label>
                    <span>{{$medicalRecord->prenatal_medical_record->religion ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">COMPLETE ADDRESS: </label>
                    <span>{{ $address?? 'N/A' }}</span>
                </div>
            </div>
            <div class="w-50">
                <div class="info-field">
                    <label class="fw-bold">FAMILY SERIAL NO: </label>
                    <span>{{ $medicalRecord->prenatal_medical_record->family_serial_no?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">AGE:</label>
                    <span>{{ $medicalRecord->patient->age?? 'N/A'}}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">CIVIL STATUS:</label>
                    <span>{{$medicalRecord->patient->civil_status?? 'N/A'}}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">CONTACT NO:</label>
                    <span>{{ $medicalRecord->patient->contact_no?? 'N/A' }}</span>
                </div>
            </div>

        </div>
        <div class="mb-2 w-100 d-flex gap-3">
            <!-- OB HISTORY Section -->
            <div class="info-field w-50">
                <label class="fw-bold mb-2">OB HISTORY:</label>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="d-flex align-items-center">
                        <label class="mb-0 fw-bold me-1" style="min-width: 15px;">G: </label>
                        <span class="border-bottom border-dark px-2" style="min-width: 30px;">{{$caseInfo->G??0}}</span>
                    </div>

                    <div class="d-flex align-items-center">
                        <label class="mb-0 fw-bold me-1" style="min-width: 15px;">P: </label>
                        <span class="border-bottom border-dark px-2" style="min-width: 30px;">{{$caseInfo->P??0}}</span>
                    </div>

                    <div class="d-flex align-items-center">
                        <label class="mb-0 fw-bold me-1" style="min-width: 15px;">T: </label>
                        <span class="border-bottom border-dark px-2" style="min-width: 30px;">{{$caseInfo->T??0}}</span>
                    </div>

                    <div class="d-flex align-items-center">
                        <label class="mb-0 fw-bold me-1" style="min-width: 15px;">P: </label>
                        <span class="border-bottom border-dark px-2" style="min-width: 30px;">{{$caseInfo->premature??0}}</span>
                    </div>

                    <div class="d-flex align-items-center">
                        <label class="mb-0 fw-bold me-1" style="min-width: 15px;">A: </label>
                        <span class="border-bottom border-dark px-2" style="min-width: 30px;">{{$caseInfo->abortion??0}}</span>
                    </div>

                    <div class="d-flex align-items-center">
                        <label class="mb-0 fw-bold me-1" style="min-width: 15px;">L: </label>
                        <span class="border-bottom border-dark px-2" style="min-width: 30px;">{{$caseInfo->living_children??0}}</span>
                    </div>
                </div>
            </div>

            <!-- PHILHEALTH Section -->
            <div class="info-field w-50">
                <label class="fw-bold mb-2">PHILHEALTH:</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <span>Yes</span>
                        <span class="ms-1">({{ $medicalRecord->prenatal_medical_record->philHealth_number != null ? '✓' : '' }})</span>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <label class="mb-0">Number:</label>
                        <span class="border-bottom border-dark px-2" style="min-width: 120px;">
                            {{ $medicalRecord->prenatal_medical_record->philHealth_number ?? '' }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center">
                        <span>No</span>
                        <span class="ms-1">({{ $medicalRecord->prenatal_medical_record->philHealth_number == null ? '✓' : '' }})</span>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-bordered">
            <thead class="table-secondary text-center table-header">
                <tr>
                    <th>Year of Pregnancy</th>
                    <th>Type of Delivery</th>
                    <th>Place</th>
                    <th>Attendant</th>
                    <th>Complication</th>
                    <th>Outcome</th>
                </tr>
            </thead>
            <tbody id="pregnancy_history_body">
                @forelse($caseInfo->pregnancy_timeline_records as $record)
                <tr class="text-center">
                    <td>{{$record->year??''}}</td>
                    <td>{{$record->type_of_delivery??''}}</td>
                    <td>{{$record->place_of_delivery??''}}</td>
                    <td>{{$record->birth_attendant??''}}</td>
                    <td>{{$record->complication??''}}</td>
                    <td>{{$record->outcome??''}}</td>
                </tr>
                @empty
                <td colspan="12" class="text-center">No record available</td>
                @endforelse
            </tbody>
        </table>
        <table class="table table-bordered">
            <thead class="table-secondary text-center table-header">
                <tr>
                    <th colspan="2">Subjective Info</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>LMP</th>
                    <td id="lmp_value">{{$caseInfo->LMP??''}}</td>
                </tr>
                <tr>
                    <th>Expected Delivery</th>
                    <td id="expected_delivery_value">{{$caseInfo->expected_delivery??''}}</td>
                </tr>
                <tr>
                    <th>Menarche</th>
                    <td id="menarche_value">{{$caseInfo->menarche??''}}</td>
                </tr>
                <tr>
                    <th>TT1</th>
                    <td id="tt1_value">{{$caseInfo->tetanus_toxoid_1??''}}</td>
                </tr>
                <tr>
                    <th>TT2</th>
                    <td id="tt2_value">{{$caseInfo->tetanus_toxoid_2??''}}</td>
                </tr>
                <tr>
                    <th>TT3</th>
                    <td id="tt3_value">{{$caseInfo->tetanus_toxoid_3??''}}</td>
                </tr>
                <tr>
                    <th>TT4</th>
                    <td id="tt4_value">{{$caseInfo->tetanus_toxoid_4??''}}</td>
                </tr>
                <tr>
                    <th>TT5</th>
                    <td id="tt5_value">{{$caseInfo->tetanus_toxoid_5??''}}</td>
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered">
            <thead class="table-secondary text-center table-header">
                <tr>
                    <th colspan="2">Assessment</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Spotting</th>
                    <td id="spotting_value">{{$caseInfo->prenatal_assessment->spotting??''}}</td>
                </tr>
                <tr>
                    <th>Edema</th>
                    <td id="edema_value">{{$caseInfo->prenatal_assessment->edema??''}}</td>
                </tr>
                <tr>
                    <th>Severe Headache</th>
                    <td id="severe_headache_value">{{$caseInfo->prenatal_assessment->severe_headache??''}}</td>
                </tr>
                <tr>
                    <th>Blurring of Vision</th>
                    <td id="blurring_of_vission_value">{{$caseInfo->prenatal_assessment->blumming_vission??''}}</td>
                </tr>
                <tr>
                    <th>Watery Discharge</th>
                    <td id="water_discharge_value">{{$caseInfo->prenatal_assessment->water_discharge??''}}</td>
                </tr>
                <tr>
                    <th>Severe Vomiting</th>
                    <td id="severe_vomiting_value">{{$caseInfo->prenatal_assessment->severe_vomitting??''}}</td>
                </tr>
                <tr>
                    <th>Hx of Smoking</th>
                    <td id="smoking_value">{{$caseInfo->prenatal_assessment->hx_smoking??''}}</td>
                </tr>
                <tr>
                    <th>Alcohol Drinker</th>
                    <td id="alcohol_drinker_value">{{$caseInfo->prenatal_assessment->alchohol_drinker??''}}</td>
                </tr>
                <tr>
                    <th>Drug Intake</th>
                    <td id="drug_intake_value">{{$caseInfo->prenatal_assessment->drug_intake??''}}</td>
                </tr>
            </tbody>
        </table>

    </div>
</body>

</html>