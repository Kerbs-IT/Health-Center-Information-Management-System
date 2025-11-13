<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/css/patient/add-patient.css',
    'resources/css/patient/record.css',
    'resources/js/record/record.js',
    'resources/js/prenatal/prenatalCase.js',
    'resources/js/prenatal/pregnancyCheckup.js'])
    <div class="patient-case vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column">
            @include('layout.header')
            <div class="flex flex-column flex-grow-1">
                <main class="flex-column p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center">
                        <h2>View Patient Details</h2>
                        <div class="sequence-links d-flex justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('record.vaccination') }}" class="text-decoration-none fs-5 text-muted">Prenatal</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">Patient Case</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-5">

                        <div class="tables d-flex flex-column p-3">
                            <div class="add-btn mb-3 d-flex justify-content-between">
                                <a href="{{route('records.prenatal')}}" class="btn btn-danger px-4 fs-5 ">Back</a>
                                <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#vaccinationModal" data-bs-medical-record-id="{{$prenatalCaseRecords->id}}" id="prenatal_check_up_add_btn">Add Record</button>
                            </div>
                            <table class="w-100 table ">
                                <thead class="table-header">
                                    <tr>
                                        <th>Case No.</th>
                                        <th>Type of Record</th>
                                        <th>Nurse</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!-- data of patient -->
                                <div>{{ $prenatalCaseRecords -> pregnancy_checkups }}</div>
                                <tbody>
                                    @foreach($prenatalCaseRecords -> prenatal_case_record as $record)

                                    <tr class="px-">
                                        <td>{{$record-> id}}</td>
                                        <td>{{$record-> type_of_record}}</td>
                                        <td>Nurse Joy</td>
                                        <td>{{ optional($record->created_at)->format('M j, Y') }}</td>
                                        <td>{{$record->status}}</td>

                                        <td>
                                            <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#viewPrenatalMedicalRecordModal" id="viewCaseBtn" data-bs-medical-id="{{$record->id}}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn btn-info text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#editVaccinationModal" id="case-edit-icon" data-bs-medical-id="{{$record->id}}">Edit</button>
                                                <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3">Archive</button>
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                                </svg>
                                                <!-- <p class="mb-0">None</p> -->
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @if($prenatalCaseRecords -> pregnancy_plan)
                                    <tr>

                                        <td>{{$prenatalCaseRecords -> pregnancy_plan->id}}</td>
                                        <td>{{$prenatalCaseRecords -> pregnancy_plan->type_of_record}}</td>
                                        <td>Nurse Joy</td>
                                        <td>{{$prenatalCaseRecords -> pregnancy_plan->created_at->format('M j, Y')}}</td>
                                        <td>Done</td>
                                        <td>
                                            <!-- <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                        <button class="btn btn-success text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#updateVaccinationModal">Update</button>
                                                    </div> -->
                                            <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#viewPregnancyPlanRecordModal" id="pregnancy-plan-view-btn" data-bs-id="{{$prenatalCaseRecords -> pregnancy_plan->id}}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn btn-info text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#case2PrenatalModal" id="pregnancy_plan_edit_btn" data-bs-id="{{$prenatalCaseRecords -> pregnancy_plan->id}}">Edit</button>
                                                <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3">Archive</button>
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                                </svg>
                                                <!-- <p class="mb-0">None</p> -->
                                            </div>
                                        </td>
                                    </tr>
                                    @endif

                                    <tr class="px-">
                                        <td>C-01</td>
                                        <td>Family Planning</td>
                                        <td>Nurse Joy</td>
                                        <td>05-22-2025</td>
                                        <td>Done</td>

                                        <td>
                                            <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#viewdetailsModal">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn btn-info text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#editFamilyPlanningModal">Edit</button>
                                                <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3">Archive</button>
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                                </svg>

                                            </div>
                                        </td>
                                    </tr>
                                    @foreach($prenatalCaseRecords -> pregnancy_checkup as $checkup)

                                    <tr class="px-">
                                        <td>{{$checkup-> id}}</td>
                                        <td>{{$checkup-> type_of_record}}</td>
                                        <td>Nurse Joy</td>
                                        <td>{{ optional($checkup->created_at)->format('M j, Y') }}</td>
                                        <td>{{$checkup->status}}</td>

                                        <td>
                                            <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#pregnancyCheckUpModal" class="viewPregnancyCheckupBtn" data-checkup-id="{{$checkup->id}}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn btn-info text-white fw-bold px-3 editPregnancyCheckupBtn" data-bs-toggle="modal" data-bs-target="#checkUpModal" data-checkup-id="{{$checkup->id}}">Edit</button>
                                                <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3">Archive</button>
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                                </svg>
                                                <!-- <p class="mb-0">None</p> -->
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>

                            </table>
                        </div>
                        <!-- view family planning -->
                        <div class="modal fade" id="viewdetailsModal" tabindex="-1" aria-labelledby="seniorCitizenModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                @include('records.familyPlanning.viewCase')
                            </div>
                        </div>
                        <!-- edit family planning -->
                        <!-- EDIT FAMILY CASE RECORD -->
                        <div class="modal fade" id="editFamilyPlanningModal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Edit Family Plan Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            @include('records.familyPlanning.familyPlanningCaseStep.step1')
                                            <!-- step 2 -->
                                            @include('records.familyPlanning.familyPlanningCaseStep.step2')
                                            <!-- step 3 -->
                                            @include('records.familyPlanning.familyPlanningCaseStep.step3')
                                            <!-- step4 -->
                                            @include('records.familyPlanning.familyPlanningCaseStep.step4')
                                            <!-- step5 -->
                                            @include('records.familyPlanning.familyPlanningCaseStep.step5')
                                        </div>


                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Save Record</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- view case record -->
                        <div class="modal fade" id="viewPrenatalMedicalRecordModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="vaccinationModalLabel">Prenatal Case Record Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
                                    </div>

                                    <div class="modal-body">
                                        @include('records.prenatal.viewComponent.viewMedicalRecord')
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- view pregnancy plan -->
                        <div class="modal fade" id="viewPregnancyPlanRecordModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="vaccinationModalLabel">Prenatal Medical Record Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
                                    </div>

                                    <div class="modal-body">
                                        @include('records.prenatal.viewComponent.viewPregnancyPlan')
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ADD FORM modal -->
                        <div class="modal fade" id="vaccinationModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column" id="check-up-form">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Prenatal Check-Up Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="inner w-100 rounded">

                                                <div class="mb-2 w-100">
                                                    <label for="patient_name">Patient Name</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Enter the name" id="check_up_patient_name">
                                                    <input type="hidden" class="form-control bg-light" name="check_up_full_name" id="hidden_check_up_patient_name">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="administered_by">Administered By</label>
                                                    <input type="text" class="form-control bg-light" name="check_up_handled_by" disabled placeholder="Nurse" id="check_up_handled_by">
                                                    <input type="hidden" class="form-control bg-light" name="health_worker_id" placeholder="Nurse" id="health_worker_id">
                                                </div>
                                                <div class="mb-2 w-100">
                                                    <label for="time_of_vaccination">Time</label>
                                                    <input type="time" class="form-control" name="check_up_time">
                                                    <small class="text-danger error-text" id="check_up_time_error"></small>
                                                </div>

                                                <div class="vital-sign w-100 border-bottom">
                                                    <h5>Vital Sign</h5>
                                                    <div class="mb-2 input-field d-flex gap-3 w-100 first-row">
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Blood Pressure:</label>
                                                            <input type="text" class="form-control w-100" placeholder="ex. 120/80" name="check_up_blood_pressure">
                                                            <small class="text-danger error-text" id="check_up_blood_pressure_error"></small>
                                                        </div>
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Temperature:</label>
                                                            <input type="number" class="form-control w-100" placeholder="00 C" name="check_up_temperature">
                                                            <small class="text-danger error-text" id="check_up_temperature_error"></small>
                                                        </div>
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Pulse Rate(Bpm):</label>
                                                            <input type="text" class="form-control w-100" placeholder=" 60-100" name="check_up_pulse_rate">
                                                            <small class="text-danger error-text" id="check_up_pulse_rate_error"></small>
                                                        </div>

                                                    </div>
                                                    <!-- 2nd row -->
                                                    <div class="mb-2 input-field d-flex gap-3 w-100 second-row">
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Respiratory Rate (breaths/min):</label>
                                                            <input type="text" class="form-control w-100" placeholder="ex. 25" name="check_up_respiratory_rate">
                                                            <small class="text-danger error-text" id="check_up_respiratory_rate_error"></small>
                                                        </div>
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Height(cm):</label>
                                                            <input type="number" class="form-control w-100" placeholder="00.00" name="check_up_height">
                                                            <small class="text-danger error-text" id="check_up_height_error"></small>
                                                        </div>
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Weight(kg):</label>
                                                            <input type="number" class="form-control w-100" placeholder=" 00.00" name="check_up_weight">
                                                            <small class="text-danger error-text" id="check_up_weight_error"></small>
                                                        </div>
                                                    </div>
                                                    <!-- 3rd row -->
                                                </div>
                                                <!-- QUESTIONS -->
                                                <div class="my-4">
                                                    <h5 class="mb-4">Prenatal Symptoms and Concerns</h5>
                                                    <!-- Question 1 -->
                                                    <div class="mb-3">
                                                        <label class="form-label">1. Do you have any pain in your lower abdomen or back?</label>
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="abdomen_question" value="Yes" id="q1-yes">
                                                                <label class="form-check-label" for="q1-yes">Yes</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="abdomen_question" value="No" id="q1-no">
                                                                <label class="form-check-label" for="q1-no">No</label>
                                                            </div>
                                                            <small class="text-danger error-text" id="abdomen_question_error"></small>
                                                            <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="abdomen_question_remarks">
                                                            <small class="text-danger error-text" id="abdomen_question_remarks_error"></small>
                                                        </div>
                                                    </div>

                                                    <!-- Question 2 -->
                                                    <div class="mb-3">
                                                        <label class="form-label">2. Have you experienced any vaginal bleeding or spotting?</label>
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="vaginal_question" value="Yes" id="q2-yes">
                                                                <label class="form-check-label" for="q2-yes">Yes</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="vaginal_question" value="No" id="q2-no">
                                                                <label class="form-check-label" for="q2-no">No</label>
                                                            </div>
                                                            <small class="text-danger error-text" id="vaginal_question_error"></small>
                                                            <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="vaginal_question_remarks">
                                                            <small class="text-danger error-text" id="vaginal_question_remarks_error"></small>
                                                        </div>
                                                    </div>

                                                    <!-- Question 3 -->
                                                    <div class="mb-3">
                                                        <label class="form-label">3. Do you have swelling in your hands, feet, or face?</label>
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="swelling_question" value="Yes" id="q3-yes">
                                                                <label class="form-check-label" for="q3-yes">Yes</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="swelling_question" value="No" id="q3-no">
                                                                <label class="form-check-label" for="q3-no">No</label>
                                                            </div>
                                                            <small class="text-danger error-text" id="swelling_question_error"></small>
                                                            <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="swelling_question_remarks">
                                                            <small class="text-danger error-text" id="swelling_question_remarks_error"></small>
                                                        </div>
                                                    </div>

                                                    <!-- Question 4 -->
                                                    <div class="mb-3">
                                                        <label class="form-label">4. Do you have persistent headache?</label>
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="headache_question" value="Yes" id="q4-yes">
                                                                <label class="form-check-label" for="q4-yes">Yes</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="headache_question" value="No" id="q4-no">
                                                                <label class="form-check-label" for="q4-no">No</label>
                                                            </div>
                                                            <small class="text-danger error-text" id="headache_question_error"></small>
                                                            <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="headache_question_remarks">
                                                            <small class="text-danger error-text" id="headache_question_remarks_error"></small>
                                                        </div>
                                                    </div>
                                                    <!-- Question 5 -->
                                                    <div class="mb-3">
                                                        <label class="form-label">5. Do you have Blurry vision or flashing lights??</label>
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="blurry_vission_question" value="Yes" id="q5-yes">
                                                                <label class="form-check-label" for="q5-yes">Yes</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="blurry_vission_question" value="No" id="q5-no">
                                                                <label class="form-check-label" for="q5-no">No</label>
                                                            </div>
                                                            <small class="text-danger error-text" id="blurry_vission_question_error"></small>
                                                            <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="blurry_vission_question_remarks">
                                                            <small class="text-danger error-text" id="blurry_vission_question_remarks_error"></small>
                                                        </div>
                                                    </div>
                                                    <!-- Question 6 -->
                                                    <div class="mb-3">
                                                        <label class="form-label">6. Do you have painful or frequent urination?</label>
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="urination_question" value="Yes" id="q6-yes">
                                                                <label class="form-check-label" for="q6-yes">Yes</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="urination_question" value="No" id="q6-no">
                                                                <label class="form-check-label" for="q6-no">No</label>
                                                            </div>
                                                            <small class="text-danger error-text" id="urination_question_error"></small>
                                                            <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="urination_question_remarks">
                                                            <small class="text-danger error-text" id="urination_question_remarks_error"></small>
                                                        </div>
                                                    </div>
                                                    <!-- Question 7 -->
                                                    <div class="mb-3">
                                                        <label class="form-label">7. Do you have Felt baby move? (if after 20 weeks)?</label>
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="baby_move_question" value="Yes" id="q7-yes">
                                                                <label class="form-check-label" for="q7-yes">Yes</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="baby_move_question" value="No" id="q7-no">
                                                                <label class="form-check-label" for="q7-no">No</label>
                                                            </div>
                                                            <small class="text-danger error-text" id="baby_move_question_error"></small>
                                                            <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="baby_move_question_remarks">
                                                            <small class="text-danger error-text" id="baby_move_question_remarks_error"></small>
                                                        </div>
                                                    </div>

                                                    <!-- Question 9 -->
                                                    <div class="mb-3">
                                                        <label class="form-label">8. Do you feel decreased baby movement?</label>
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="decreased_baby_movement" value="Yes" id="q9-yes">
                                                                <label class="form-check-label" for="q9-yes">Yes</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="decreased_baby_movement" value="No" id="q9-no">
                                                                <label class="form-check-label" for="q9-no">No</label>
                                                            </div>
                                                            <small class="text-danger error-text" id="decreased_baby_movement_error"></small>
                                                            <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="decreased_baby_movement_remarks">
                                                            <small class="text-danger error-text" id="decreased_baby_movement_remarks_error"></small>
                                                        </div>
                                                    </div>
                                                    <!-- Question 10 -->
                                                    <div class="mb-3">
                                                        <label class="form-label">9. Do you have feel Other concerns or symptoms?</label>
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="other_symptoms_question" value="Yes" id="q10-yes">
                                                                <label class="form-check-label" for="q10-yes">Yes</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="" type="radio" name="other_symptoms_question" value="No" id="q10-no">
                                                                <label class="form-check-label" for="q10-no">No</label>
                                                            </div>
                                                            <small class="text-danger error-text" id="other_symptoms_question_error"></small>
                                                            <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="other_symptoms_question_remarks">
                                                            <small class="text-danger error-text" id="other_symptoms_question_remarks_error"></small>
                                                        </div>
                                                    </div>
                                                    <!-- overall remarks -->
                                                    <div class="mb-2 w-100">
                                                        <label for="remarks">Remarks*</label>
                                                        <input type="text" class="form-control" name="overall_remarks">
                                                        <small class="text-danger error-text" id="overall_remarks_error"></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success" id="check-up-save-btn">Save Record</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- EDIT prenatal CASE INFO -->
                        <div class="modal fade" id="editVaccinationModal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="" class="flex-column" id="update-prenatal-case-record-form">
                                        @method('PUT')
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Prenatal Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-2 w-100">
                                                <div class="ob-history mb-2">
                                                    <h3>OB HISTORY</h3>
                                                    <div class="type-of-pregnancy d-flex w-100 gap-1">
                                                        <div class="item">
                                                            <label for="G">G</label>
                                                            <input type="number" name="G" class="form-control w-100" placeholder="0" id="grada_input">
                                                            <small class="text-danger error-text error-text" id="G_error"></small>
                                                        </div>
                                                        <div class="item">
                                                            <label for="G">P</label>
                                                            <input type="number" name="P" class="form-control w-100" placeholder="0" id="para_input">
                                                            <small class="text-danger error-text error-text" id="P_error"></small>
                                                        </div>
                                                        <div class="item">
                                                            <label for="T">T</label>
                                                            <input type="number" name="T" class="form-control w-100" placeholder="0" id="term_input">
                                                            <small class="text-danger error-text error-text" id="T_error"></small>
                                                        </div>
                                                        <div class="item">
                                                            <label for="premature">Premature</label>
                                                            <input type="number" name="premature" class="form-control w-100" placeholder="0" id="premature_input">
                                                            <small class="text-danger error-text error-text" id="premature_error"></small>
                                                        </div>
                                                        <div class="item">
                                                            <label for="abortion">Abortion</label>
                                                            <input type="number" name="abortion" class="form-control w-100" placeholder="0" id="abortion_input">
                                                            <small class="text-danger error-text error-text" id="abortion_error"></small>
                                                        </div>
                                                        <div class="item">
                                                            <label for="living_children">Living Children</label>
                                                            <input type="number" name="living_children" class="form-control w-100" placeholder="0" id="living_children_input">
                                                            <small class="text-danger error-text error-text" id="living_children_error"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h3>Records</h3>
                                                <div class="previous-record mb-3 d-flex gap-1">
                                                    <div class="item">
                                                        <label for="year_of_pregnancy">Year of Pregnancy</label>
                                                        <input type="number" name="pregnancy_year" class="form-control w-100" id="pregnancy_year">
                                                        <span class="text-danger error-text" id="pregnancy_year_error"></span>
                                                    </div>
                                                    <div class="item">
                                                        <label for="type_of_delivery">Type of Delivery</label>
                                                        <select name="type_of_delivery" id="type_of_delivery" class="form-select" required>
                                                            <option value="" disabled selected>Select Type of Delivery</option>
                                                            <option value="Normal Spontaneous Delivery">Normal Spontaneous Delivery (NSD)</option>
                                                            <option value="Cesarean Section">Cesarean Section (CS)</option>
                                                            <option value="Assisted Vaginal Delivery">Assisted Vaginal Delivery</option>
                                                            <option value="Breech delivery">Breech Delivery</option>
                                                            <option value="Forceps Delivery">Forceps Delivery</option>
                                                            <option value="Vacuum Extraction">Vacuum Extraction</option>
                                                            <option value="Water Birth">Water Birth</option>
                                                            <option value="Home Birth">Home Birth</option>
                                                            <option value="Emergency Cesarean">Emergency Cesarean</option>
                                                        </select>
                                                        <span class="text-danger error-text" id="type_of_delivery_error"></span>
                                                    </div>
                                                    <div class="item">
                                                        <label for="place_of_delivery">Place of Delivery</label>
                                                        <input type="text" name="place_of_delivery" class="form-control w-100" placeholder="trece" id="place_of_delivery">
                                                        <span class="text-danger error-text" id="place_of_delivery_error"></span>
                                                    </div>
                                                    <div class="item">
                                                        <label for="birth_attendant">Birth Attendant</label>
                                                        <input type="text" name="birth_attendant" class="form-control w-100" placeholder="Nurse joy" id="birth_attendant">
                                                        <span class="text-danger error-text" id="birth_attendant_error"></span>
                                                    </div>
                                                    <div class="item">
                                                        <label for="Complication">Complication</label>
                                                        <input type="text" name="Complication" class="form-control w-100" placeholder="" id="complication" value="None">
                                                    </div>
                                                    <div class="item">
                                                        <label for="G">Outcome</label>
                                                        <select id="outcome" name="outcome" required class="form-select">
                                                            <option value="" disabled selected>Select Outcome</option>
                                                            <option value="term">Term Delivery</option>
                                                            <option value="preterm">Preterm Delivery</option>
                                                            <option value="abortion">Abortion (Spontaneous/Induced)</option>
                                                            <option value="ectopic">Ectopic Pregnancy</option>
                                                            <option value="stillbirth">Stillbirth (IUFD)</option>
                                                            <option value="living">Living Child</option>
                                                        </select>
                                                        <span class="w-100 text-danger error-text" id="outcome_error"></span>
                                                    </div>
                                                    <div class="d-flex align-self-end mb-0">
                                                        <button type="button" class="btn btn-success" id="add-pregnancy-history-btn"> Add</button>
                                                    </div>
                                                </div>
                                                <!-- results -->
                                                <div class="mb-2">
                                                    <table class="table table-bordered mt-4">
                                                        <thead class="table-secondary text-center">
                                                            <tr>
                                                                <th>Year of Pregnancy</th>
                                                                <th>Type of Delivery</th>
                                                                <th>Place of Delivery</th>
                                                                <th>Birth Attendant</th>
                                                                <th>Complication</th>
                                                                <th>Outcome</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="edit-previous-records-body">

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- subjective -->
                                                <h3>Subjective</h3>
                                                <div class="subjective-info mb-3 border-bottom">
                                                    <div class="mb-2 d-flex w-100 gap-2">
                                                        <div class="mb-2 w-100 ">
                                                            <label for="place_of_delivery">LMP</label>
                                                            <input type="date" name="LMP" class="form-control w-100" placeholder="trece" id="LMP_input">
                                                            <small class="text-danger error-text error-text" id="LMP_error"></small>
                                                        </div>
                                                        <div class="mb-2 w-100">
                                                            <label for="expected_delivery">Expected Delivery</label>
                                                            <input type="date" name="expected_delivery" class="form-control w-100" placeholder="trece" id="expected_delivery_input">
                                                            <small class="text-danger error-text error-text" id="expected_delivery_error"></small>
                                                        </div>
                                                        <div class="mb-2 w-100">
                                                            <label for="expected_delivery">Menarche</label>
                                                            <input type="text" name="menarche" class="form-control w-100" placeholder="trece" id="menarche_input">
                                                            <small class="text-danger error-text error-text" id="menarche_error"></small>
                                                        </div>
                                                    </div>
                                                    <!-- next row -->
                                                    <div class="mb-2 d-flex w-100 gap-2">
                                                        <div class="mb-2 w-100 ">
                                                            <label for="place_of_delivery">TT1</label>
                                                            <input type="text" name="tt1" class="form-control w-100" placeholder="YYYY" id="tt1_input">
                                                            <small class="text-danger error-text error-text" id="tt1_error"></small>
                                                        </div>
                                                        <div class="mb-2 w-100">
                                                            <label for="expected_delivery">TT2</label>
                                                            <input type="text" name="tt2" class="form-control w-100" placeholder="YYYY" id="tt2_input">
                                                            <small class="text-danger error-text error-text" id="tt2_error"></small>
                                                        </div>
                                                        <div class="mb-2 w-100">
                                                            <label for="expected_delivery">TT3</label>
                                                            <input type="text" name="tt3" class="form-control w-100" placeholder="YYYY" id="tt3_input">
                                                            <small class="text-danger error-text error-text" id="tt3_error"></small>
                                                        </div>
                                                    </div>
                                                    <!-- last row -->
                                                    <div class="mb-2 d-flex w-100 gap-2">
                                                        <div class="mb-2 w-100 ">
                                                            <label for="place_of_delivery">TT4</label>
                                                            <input type="text" name="tt4" class="form-control w-100" placeholder="YYYY" id="tt4_input">
                                                            <small class="text-danger error-text error-text" id="tt4_error"></small>
                                                        </div>
                                                        <div class="mb-2 w-100">
                                                            <label for="expected_delivery">TT5</label>
                                                            <input type="text" name="tt5" class="form-control w-100" placeholder="YYYY" id="tt5_input">
                                                            <small class="text-danger error-text error-text" id="tt5_error"></small>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- ASSESSMENT -->
                                                <div class="assessment-con mb-3">
                                                    <h4>ASSESSMENT <small class="text-muted fs-5">(put check if yes)</small></h4>
                                                    <div class="checkboxes d-flex gap-2 mb-2 flex-wrap">
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" name="spotting" class="p-4" id="spotting_input" value="yes">
                                                            <label for="spotting_input" class="w-100 fs-5">Spotting</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" value="yes" name="edema" class="p-4" id="edema_input">
                                                            <label for="edema_input" class="w-100 fs-5">Edema</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" value="yes" name="severe_headache" class="p-4" id="severe_headache_input">
                                                            <label for="severe_headache_input" class="w-100 fs-5">severe headache</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" value="yes" name="blurring_of_vission" class="p-4" id="blurring_of_vission_input">
                                                            <label for="blurring_of_vission_input" class="w-100 fs-5">blumming of vision</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" value="yes" name="watery_discharge" class="p-4" id="watery_discharge_input">
                                                            <label for="watery_discharge_input" class="w-100 fs-5">Watery discharge</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" value="yes" name="severe_vomiting" class="p-4" id="severe_vomiting_input">
                                                            <label for="severe_vomiting_input" class="w-100 fs-5">severe vomiting</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" value="yes" name="hx_smoking" class="p-4" id="hx_smoking_input">
                                                            <label for="hx_smoking_input" class="w-100 fs-5">Hx of smoking </label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" value="yes" name="alcohol_drinker" class="p-4" id="alcohol_drinker_input">
                                                            <label for="alcohol_drinker_input" class="w-100 fs-5">alcohol drinker</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" value="yes" name="drug_intake" class="p-4" id="drug_intake_input">
                                                            <label for="drug_intake_input" class="w-100 fs-5">Drug intake</label>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success" id="update-save-btn">Save Record</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- EDIT PREGNANCY PLAN DETAILS -->
                        <div class="modal fade" id="case2PrenatalModal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="" class="flex-column" id="pregnancy_plan_update_form">
                                        @method('PUT')
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Pregnancy Planning Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="prenatal-planning d-flex flex-column align-items-center">
                                                <h1 class="text-center mb-2 planning-header">Plano SA ORAS NG PANGANGANAK AT KAGIPITAN</h1>

                                                <div class="prenatal-planning-body d-flex flex-column w-100 p-4 shadow card">
                                                    <h4>Mahahalagang Impormasyon:</h4>
                                                    <div class="mb-3 w-100">
                                                        <div class="upper-box d-flex align-items-center gap-1">
                                                            <label for="midwife" class="fs-5 fw-medium text-nowrap">Ako ay papaanakin ni:</label>
                                                            <input type="text" class="flex-grow-1 form-control" name="midwife_name" placeholder="(pangalan ng doctor/nars/midwife, atbp.)" id="midwife_name">
                                                        </div>
                                                        <small id="midwife_name_error" class="text-danger error-text"></small>
                                                    </div>
                                                    <!-- plano ko manganak -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex align-items-center gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Plano kong manganak sa:</label>
                                                            <input type="text" class="flex-grow-1 form-control" name="place_of_pregnancy" placeholder="(pangalan ng hospital/lying-in center/ maternity clinic)" id="place_of_pregnancy">
                                                        </div>
                                                        <small id="place_of_pregnancy_error" class="text-danger error-text"></small>
                                                    </div>
                                                    <!-- authorized by philheath -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="midwife" class="fs-5 fw-medium text-nowrap">Ito ay pasilid na otorisado ng Philheath:</label>
                                                            <div class="authorize-radio d-flex gap-3 align-items-center">
                                                                <label for="yes" class="fs-5"> Yes:</label>
                                                                <input type="radio" name="authorized_by_philhealth" value="yes" id="authorized_by_philhealth_yes">
                                                                <label for="no" class="fs-5">Hindi:</label>
                                                                <input type="radio" name="authorized_by_philhealth" class="mb-0" value="no" id="authorized_by_philhealth_no">
                                                            </div>
                                                            <small id="authorized_by_philhealth_error" class="text-danger error-text"></small>
                                                        </div>
                                                    </div>
                                                    <!-- cost of pregnancy -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium w-100 text-nowrap ">Ang tinatayang gagastusin ng panganganak sa pasilidad ay (P):</label>
                                                            <input type="number" class="flex-grow-1 form-control" name="cost_of_pregnancy" id="cost_of_pregnancy">
                                                        </div>
                                                        <small id="cost_of_pregnancy_error" class="text-danger error-text"></small>
                                                    </div>
                                                    <!-- payment method -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Ang Paraan ng pagbabayad ay:</label>
                                                            <select name="payment_method" id="payment_method" class="form-select flex-grow-1">
                                                                <option value="" disabled selected>Select Payment Method</option>
                                                                <option value="PhilHealth">PhilHealth</option>
                                                                <option value="Cash">Cash / Out-of-Pocket</option>
                                                                <option value="private insurance">Private Insurance</option>
                                                                <option value="HMO">HMO</option>
                                                                <option value="NGO">NGO / Charity Assistance</option>
                                                                <option value="Government Health Program">Government Health Program</option>
                                                                <option value="installment">Installment Plan</option>
                                                                <option value="Employer / Company Benefit">Employer / Company Benefit</option>
                                                            </select>
                                                        </div>
                                                        <small id="payment_method_error" class="text-danger error-text"></small>
                                                    </div>
                                                    <!-- mode of transportation -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Paraan ng pagbiyahe patungo sa pasilidad ay:</label>
                                                            <select name="transportation_mode" id="transportation_mode" class="form-select flex-grow-1" required>
                                                                <option value="" disabled selected>Select Mode of Transportation</option>
                                                                <option value="Walking">Walking</option>
                                                                <option value="Tricycle">Tricycle</option>
                                                                <option value="Jeepney">Jeepney</option>
                                                                <option value="Motorcycle">Motorcycle</option>
                                                                <option value="Private Vehicle">Private Vehicle</option>
                                                                <option value="Ambulance">Ambulance</option>
                                                                <option value="Taxi / Grab">Taxi / Grab</option>
                                                                <option value="Others">Others</option>
                                                            </select>

                                                        </div>
                                                        <div class="low-box w-100 d-flex justify-content-center">
                                                            <small>(mode of transportation)</small>
                                                        </div>
                                                        <small id="transportation_mode_error" class="text-danger error-text"></small>
                                                    </div>
                                                    <!-- person who will bring me to hospital -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Taong magdadala sakin sa hospital: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="accompany_person_to_hospital" placeholder="Ilagay ang pangalan" id="accompany_person_to_hospital">
                                                        </div>
                                                        <small id="accompany_person_to_hospital_error" class="text-danger error-text"></small>
                                                    </div>
                                                    <!-- guardian -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Pangalan ng taong sasamahan ako sa panganganak: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="accompany_through_pregnancy" placeholder="Ilagay ang pangalan" id="accompany_through_pregnancy">
                                                        </div>
                                                        <small id="accompany_through_pregnancy_error" class="text-danger error-text"></small>
                                                    </div>
                                                    <!-- mag-alalaga -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Pangalan ng taong mag-aalaga sa akin sa panganganak: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="care_person" placeholder="Ilagay ang pangalan" id="care_person">
                                                        </div>
                                                        <small id="care_person_error" class="text-danger error-text"></small>
                                                    </div>
                                                    <!-- magbibigay ng dugo -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1 mb-2">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Maaring magbigay ng dugo, kung sakaling mangailangan: </label>
                                                            <div class="blood-donation d-flex w-100">
                                                                <input type="text" class="w-50 px-2 form-control flex-grow-1" name="name_of_donor" id="name_of_donor" placeholder="Ilagay ang pangalan">
                                                                <button type="button" class="btn btn-success" id="donor_name_add_btn">Add</button>
                                                            </div>
                                                            <!-- hidden input since madami to -->
                                                        </div>
                                                        <div class="lower-box p-3 bg-secondary w-100 justify-self-center d-flex gap-2" id="donor_names_con">
                                                            <!-- <div class="box vaccine d-flex justify-content-between bg-white align-items-center p-1 w-50 rounded">
                                                                <h5 class="mb-0">Jan Loiue Salimbago</h5>
                                                                <div class="delete-icon d-flex align-items-center justify-content-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                                                                        <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                                                                    </svg>
                                                                </div>
                                                                <input type="text" name="donor_names[]" value="">
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                    <h5 class="mb-3">Kung magkaroon ng komplikasyon, kailangan sabihan kaagad si:</h5>
                                                    <!-- persons info -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1 align-items-center">
                                                            <label for="place_of_birth" class="fs-5 text-nowrap">Pangalan: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="emergency_person_name" placeholder="Ilagay ang pangalan" id="emergency_person_name">
                                                        </div>
                                                        <small id="emergency_person_name_error" class="text-danger error-text"></small>
                                                    </div>
                                                    <!-- contact info -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1 align-items-center">
                                                            <label for="place_of_birth" class="fs-5">Tirahan: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="emergency_person_residency" placeholder="address" id="emergency_person_residency">
                                                        </div>
                                                        <small id="emergency_person_residency_error" class="text-danger error-text"></small>
                                                    </div>
                                                    <!-- contact -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1 align-items-center">
                                                            <label for="place_of_birth" class="fs-5"> Telepono: </label>
                                                            <input type="number" class="flex-grow-1 form-control" name="emergency_person_contact_number" placeholder="ex. 0936627872" id="emergency_person_contact_number">
                                                        </div>
                                                        <small id="emergency_person_contact_number_error" class="text-danger error-text"></small>
                                                    </div>
                                                    <!-- patient name -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1 align-items-center">
                                                            <label for="place_of_birth" class="fs-5 text-nowrap">Pangalan ng pasyente: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="patient_name" id="patient_name" placeholder="Ilagay ang pangalan" disabled>
                                                        </div>

                                                    </div>
                                                    <!-- signature -->
                                                    <div class="mb-3 w-100 d-flex flex-column border-bottom">
                                                        <label for="signature_image">Upload Signature</label>
                                                        <input type="file" name="signature_image" id="signature_image" class="form-control" accept="image/*" required>
                                                        <small class="text-muted text-center">Upload a clear photo or scanned image of the signature.</small>
                                                    </div>
                                                    <small id="signature_image_error" class="text-danger error-text"></small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success" id="pregnancy_plan_update_btn">Save Record</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- view pregnnacy check up -->
                        <div class="modal fade" id="pregnancyCheckUpModal" tabindex="-1" aria-labelledby="pregnancyCheckUpModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="vaccinationModalLabel">Prenatal Check-Up Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <!-- use include to lessen the code lines -->
                                    <div class="modal-body">
                                        @include('records.prenatal.viewComponent.viewPregnancyCheckup')
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- EDIT CHECK UP modal -->
                        <div class="modal fade" id="checkUpModal" tabindex="-1" aria-labelledby="checkUpModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column" id="edit-check-up-form">
                                        @method('PUT')
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Prenatal Check-Up Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            @include('records.prenatal.editComponent.editPregnancyCheckup')
                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success" id="edit-check-up-save-btn">Save Record</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </main>
            </div>
        </div>
    </div>
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('record_prenatal');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>