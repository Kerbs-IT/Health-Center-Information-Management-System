<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body class="bg-white">
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/js/patient/add-patient.js',
    'resources/css/patient/add-patient.css',
    'resources/css/patient/record.css',
    'resources/js/record/record.js'])
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
                                <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#vaccinationModal">Add Record</button>
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
                                <tbody>
                                    <tr class="px-">
                                        <td>C-01</td>
                                        <td>Medical Record</td>
                                        <td>Nurse Joy</td>
                                        <td>05-22-2025</td>
                                        <td>Done</td>

                                        <td>
                                            <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#viewPrenatalMedicalRecordModal">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn btn-info text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#editVaccinationModal">Edit</button>
                                                <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3">Archive</button>
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                                </svg>
                                                <!-- <p class="mb-0">None</p> -->
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>C-02</td>
                                        <td>Pregnancy Plan</td>
                                        <td>Nurse Joy</td>
                                        <td>05-22-2025</td>
                                        <td>Done</td>
                                        <td>
                                            <!-- <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                <button class="btn btn-success text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#updateVaccinationModal">Update</button>
                                            </div> -->
                                            <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#viewPregnancyPlanRecordModal">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn btn-info text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#case2PrenatalModal">Edit</button>
                                                <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3">Archive</button>
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                                </svg>
                                                <!-- <p class="mb-0">None</p> -->
                                            </div>
                                        </td>
                                    </tr>
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
                                                <!-- <p class="mb-0">None</p> -->
                                            </div>
                                        </td>
                                    </tr>

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
                        <!-- EDIT CASE INFO -->
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
                        <!-- view medical record -->
                        <div class="modal fade" id="viewPrenatalMedicalRecordModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="vaccinationModalLabel">Prenatal Medical Record Details</h5>
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
                                    <div class="modal-header bg-success">
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
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="inner w-100 rounded">

                                                <div class="mb-2 w-100">
                                                    <label for="patient_name">Patient Name</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Jan Louie Salimbago">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="administered_by">Administered By</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Nurse">
                                                </div>
                                                <div class="mb-2 w-100">
                                                    <label for="time_of_vaccination">Time</label>
                                                    <input type="time" class="form-control" name="time_of_vaccination">
                                                </div>

                                                <div class="vital-sign w-100 border-bottom">
                                                    <h5>Vital Sign</h5>
                                                    <div class="mb-2 input-field d-flex gap-3 w-100 first-row">
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Blood Pressure:</label>
                                                            <input type="text" class="form-control w-100" placeholder="ex. 120/80">
                                                        </div>
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Temperature:</label>
                                                            <input type="number" class="form-control w-100" placeholder="00 C">
                                                        </div>
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Pulse Rate(Bpm):</label>
                                                            <input type="text" class="form-control w-100" placeholder=" 60-100">
                                                        </div>

                                                    </div>
                                                    <!-- 2nd row -->
                                                    <div class="mb-2 input-field d-flex gap-3 w-100 second-row">
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Respiratory Rate (breaths/min):</label>
                                                            <input type="text" class="form-control w-100" placeholder="ex. 25">
                                                        </div>
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Height(cm):</label>
                                                            <input type="number" class="form-control w-100" placeholder="00.00" name="height">
                                                        </div>
                                                        <div class="mb-2 w-50">
                                                            <label for="BP">Weight(kg):</label>
                                                            <input type="number" class="form-control w-100" placeholder=" 00.00" name="weight">
                                                        </div>
                                                    </div>
                                                    <!-- 3rd row -->
                                                </div>
                                                <!-- QUESTIONS -->
                                                <div class="my-4">
                                                    <h5 class="mb-4">Prenatal Symptoms and Concerns</h5>
                                                    <form>
                                                        <!-- Question 1 -->
                                                        <div class="mb-3">
                                                            <label class="form-label">1. Do you have any pain in your lower abdomen or back?</label>
                                                            <div class="d-flex gap-3 flex-wrap">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q1" value="Yes" id="q1-yes">
                                                                    <label class="form-check-label" for="q1-yes">Yes</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q1" value="No" id="q1-no">
                                                                    <label class="form-check-label" for="q1-no">No</label>
                                                                </div>
                                                                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="q1_remarks">
                                                            </div>
                                                        </div>

                                                        <!-- Question 2 -->
                                                        <div class="mb-3">
                                                            <label class="form-label">2. Have you experienced any vaginal bleeding or spotting?</label>
                                                            <div class="d-flex gap-3 flex-wrap">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q2" value="Yes" id="q2-yes">
                                                                    <label class="form-check-label" for="q2-yes">Yes</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q2" value="No" id="q2-no">
                                                                    <label class="form-check-label" for="q2-no">No</label>
                                                                </div>
                                                                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="q2_remarks">
                                                            </div>
                                                        </div>

                                                        <!-- Question 3 -->
                                                        <div class="mb-3">
                                                            <label class="form-label">3. Do you have swelling in your hands, feet, or face?</label>
                                                            <div class="d-flex gap-3 flex-wrap">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q3" value="Yes" id="q3-yes">
                                                                    <label class="form-check-label" for="q3-yes">Yes</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q3" value="No" id="q3-no">
                                                                    <label class="form-check-label" for="q3-no">No</label>
                                                                </div>
                                                                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="q3_remarks">
                                                            </div>
                                                        </div>

                                                        <!-- Question 4 -->
                                                        <div class="mb-3">
                                                            <label class="form-label">4. Do you have persistent headache?</label>
                                                            <div class="d-flex gap-3 flex-wrap">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q4" value="Yes" id="q4-yes">
                                                                    <label class="form-check-label" for="q4-yes">Yes</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q4" value="No" id="q4-no">
                                                                    <label class="form-check-label" for="q4-no">No</label>
                                                                </div>
                                                                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="q3_remarks">
                                                            </div>
                                                        </div>
                                                        <!-- Question 5 -->
                                                        <div class="mb-3">
                                                            <label class="form-label">5. Do you have Blurry vision or flashing lights??</label>
                                                            <div class="d-flex gap-3 flex-wrap">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q5" value="Yes" id="q5-yes">
                                                                    <label class="form-check-label" for="q5-yes">Yes</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q5" value="No" id="q5-no">
                                                                    <label class="form-check-label" for="q5-no">No</label>
                                                                </div>
                                                                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="q3_remarks">
                                                            </div>
                                                        </div>
                                                        <!-- Question 6 -->
                                                        <div class="mb-3">
                                                            <label class="form-label">6. Do you have painful or frequent urination?</label>
                                                            <div class="d-flex gap-3 flex-wrap">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q6" value="Yes" id="q6-yes">
                                                                    <label class="form-check-label" for="q6-yes">Yes</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q6" value="No" id="q6-no">
                                                                    <label class="form-check-label" for="q6-no">No</label>
                                                                </div>
                                                                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="q3_remarks">
                                                            </div>
                                                        </div>
                                                        <!-- Question 7 -->
                                                        <div class="mb-3">
                                                            <label class="form-label">7. Do you have Felt baby move? (if after 20 weeks)?</label>
                                                            <div class="d-flex gap-3 flex-wrap">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q7" value="Yes" id="q7-yes">
                                                                    <label class="form-check-label" for="q7-yes">Yes</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q7" value="No" id="q7-no">
                                                                    <label class="form-check-label" for="q7-no">No</label>
                                                                </div>
                                                                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="q3_remarks">
                                                            </div>
                                                        </div>
                                                        <!-- Question 8 -->
                                                        <div class="mb-3">
                                                            <label class="form-label">8. Do you have Felt baby move? (if after 20 weeks)?</label>
                                                            <div class="d-flex gap-3 flex-wrap">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q8" value="Yes" id="q8-yes">
                                                                    <label class="form-check-label" for="q8-yes">Yes</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q8" value="No" id="q8-no">
                                                                    <label class="form-check-label" for="q8-no">No</label>
                                                                </div>
                                                                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="q3_remarks">
                                                            </div>
                                                        </div>
                                                        <!-- Question 9 -->
                                                        <div class="mb-3">
                                                            <label class="form-label">9. Do you feel decreased baby movement?</label>
                                                            <div class="d-flex gap-3 flex-wrap">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q9" value="Yes" id="q9-yes">
                                                                    <label class="form-check-label" for="q9-yes">Yes</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q9" value="No" id="q9-no">
                                                                    <label class="form-check-label" for="q9-no">No</label>
                                                                </div>
                                                                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="q3_remarks">
                                                            </div>
                                                        </div>
                                                        <!-- Question 10 -->
                                                        <div class="mb-3">
                                                            <label class="form-label">10. Do you have feel Other concerns or symptoms?</label>
                                                            <div class="d-flex gap-3 flex-wrap">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q10" value="Yes" id="q10-yes">
                                                                    <label class="form-check-label" for="q10-yes">Yes</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="q10" value="No" id="q10-no">
                                                                    <label class="form-check-label" for="q10-no">No</label>
                                                                </div>
                                                                <input type="text" class="form-control ms-3 mt-2 mt-sm-0 flex-grow-1" placeholder="Remarks (if any)" name="q3_remarks">
                                                            </div>
                                                        </div>


                                                    </form>
                                                </div>


                                                <div class="mb-2 w-100">
                                                    <label for="remarks">Remarks*</label>
                                                    <input type="text" class="form-control" id="remarks" name="remarks">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Save Record</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- EDIT CASE INFO -->
                        <div class="modal fade" id="editVaccinationModal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Prenatal Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-2 w-100">
                                                <label for="date_of_tetanous">Petsa ng Injeksyon ng Tetanus Toxiod</label>
                                                <div class="dates d-flex gap-1 ">
                                                    <!-- 1 -->
                                                    <div class="mb-2 w-25 d-flex">
                                                        <span class="px-3 bg-secondary text-center mb-0 text-white d-flex align-items-center justify-content-center" style="height: 38px;">1</span>
                                                        <input type="date" id="date_of_vaccination" placeholder="20" class="form-control flex-grow-1 " name="date_of_vaccination" value="">
                                                    </div>
                                                    <!-- 2 -->
                                                    <div class="mb-2 w-25 d-flex">
                                                        <span class="px-3 bg-secondary text-center mb-0 text-white d-flex align-items-center justify-content-center" style="height: 38px;">2</span>
                                                        <input type="date" id="date_of_vaccination" placeholder="20" class="form-control flex-grow-1 " name="date_of_vaccination" value="">
                                                    </div>
                                                    <!-- 3 -->
                                                    <div class="mb-2 w-25 d-flex">
                                                        <span class="px-3 bg-secondary text-center mb-0 text-white d-flex align-items-center justify-content-center" style="height: 38px;">3</span>
                                                        <input type="date" id="date_of_vaccination" placeholder="20" class="form-control flex-grow-1 " name="date_of_vaccination" value="">
                                                    </div>
                                                    <!-- 4 -->
                                                    <div class="mb-2 w-25 d-flex">
                                                        <span class="px-3 bg-secondary text-center mb-0 text-white d-flex align-items-center justify-content-center" style="height: 38px;">4</span>
                                                        <input type="date" id="date_of_vaccination" placeholder="20" class="form-control flex-grow-1 " name="date_of_vaccination" value="">
                                                    </div>
                                                    <!-- 5 -->
                                                    <div class="mb-2 w-25 d-flex">
                                                        <span class="px-3 bg-secondary text-center mb-0 text-white d-flex align-items-center justify-content-center" style="height: 38px;">5</span>
                                                        <input type="date" id="date_of_vaccination" placeholder="20" class="form-control flex-grow-1 " name="date_of_vaccination" value="">
                                                    </div>
                                                </div>
                                                <div class="ob-history mb-2">
                                                    <h3>OB HISTORY</h3>
                                                    <div class="type-of-pregnancy d-flex w-100 gap-1">
                                                        <div class="item">
                                                            <label for="G">G</label>
                                                            <input type="number" name="G" class="form-control w-100" placeholder="0">
                                                        </div>
                                                        <div class="item">
                                                            <label for="G">P</label>
                                                            <input type="number" name="G" class="form-control w-100" placeholder="0">
                                                        </div>
                                                        <div class="item">
                                                            <label for="G">T</label>
                                                            <input type="number" name="G" class="form-control w-100" placeholder="0">
                                                        </div>
                                                        <div class="item">
                                                            <label for="G">Premature</label>
                                                            <input type="number" name="G" class="form-control w-100" placeholder="0">
                                                        </div>
                                                        <div class="item">
                                                            <label for="G">Abortion</label>
                                                            <input type="number" name="G" class="form-control w-100" placeholder="0">
                                                        </div>
                                                        <div class="item">
                                                            <label for="G">Living Children</label>
                                                            <input type="number" name="G" class="form-control w-100" placeholder="0">
                                                        </div>
                                                    </div>
                                                </div>
                                                <h3>Records</h3>
                                                <div class="previous-record mb-3 d-flex gap-1">
                                                    <div class="item">
                                                        <label for="year_of_pregnancy">Year of Pregnancy</label>
                                                        <input type="date" name="year_of_pregranancy" class="form-control w-100">
                                                    </div>
                                                    <div class="item">
                                                        <label for="type_of_delivery">Type of Delivery</label>
                                                        <select name="type_of_delivery" id="type_of_delivery" class="form-select" required>
                                                            <option value="" disabled selected>Select Type of Delivery</option>
                                                            <option value="normal_spontaneous_delivery">Normal Spontaneous Delivery (NSD)</option>
                                                            <option value="cesarean_section">Cesarean Section (CS)</option>
                                                            <option value="assisted_vaginal_delivery">Assisted Vaginal Delivery</option>
                                                            <option value="breech_delivery">Breech Delivery</option>
                                                            <option value="forceps_delivery">Forceps Delivery</option>
                                                            <option value="vacuum_extraction">Vacuum Extraction</option>
                                                            <option value="water_birth">Water Birth</option>
                                                            <option value="home_birth">Home Birth</option>
                                                            <option value="emergency_cesarean">Emergency Cesarean</option>
                                                        </select>
                                                    </div>
                                                    <div class="item">
                                                        <label for="place_of_delivery">Place of Delivery</label>
                                                        <input type="text" name="place_of_delivery" class="form-control w-100" placeholder="trece">
                                                    </div>
                                                    <div class="item">
                                                        <label for="birth_attendant">Birth Attendant</label>
                                                        <input type="text" name="birth_attendant" class="form-control w-100" placeholder="Nurse joy">
                                                    </div>
                                                    <div class="item">
                                                        <label for="Complication">Complication</label>
                                                        <input type="text" name="Complication" class="form-control w-100" placeholder="">
                                                    </div>
                                                    <div class="item">
                                                        <label for="G">Outcome</label>
                                                        <input type="text" name="G" class="form-control w-100" placeholder="">
                                                    </div>
                                                    <div class="d-flex align-self-end mb-0">
                                                        <button type="button" class="btn btn-success"> Add</button>
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
                                                        <tbody id="previous-records-body">
                                                            <tr class="text-center">
                                                                <td>2022-08-15</td>
                                                                <td>Normal Spontaneous Delivery (NSD)</td>
                                                                <td>Trece</td>
                                                                <td>Nurse Joy</td>
                                                                <td>None</td>
                                                                <td>Live birth</td>
                                                                <td>
                                                                    <button class="btn btn-danger btn-sm">Remove</button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- subjective -->
                                                <h3>Subjective</h3>
                                                <div class="subjective-info mb-3 border-bottom">
                                                    <div class="mb-2 d-flex w-100 gap-2">
                                                        <div class="mb-2 w-100 ">
                                                            <label for="place_of_delivery">LMP</label>
                                                            <input type="date" name="place_of_delivery" class="form-control w-100" placeholder="trece">
                                                        </div>
                                                        <div class="mb-2 w-100">
                                                            <label for="expected_delivery">Expected Delivery</label>
                                                            <input type="date" name="expected_delivery" class="form-control w-100" placeholder="trece">
                                                        </div>
                                                        <div class="mb-2 w-100">
                                                            <label for="expected_delivery">Menarche</label>
                                                            <input type="text" name="expected_delivery" class="form-control w-100" placeholder="trece">
                                                        </div>
                                                    </div>
                                                    <!-- next row -->
                                                    <div class="mb-2 d-flex w-100 gap-2">
                                                        <div class="mb-2 w-100 ">
                                                            <label for="place_of_delivery">TT1</label>
                                                            <input type="text" name="place_of_delivery" class="form-control w-100" placeholder="2021">
                                                        </div>
                                                        <div class="mb-2 w-100">
                                                            <label for="expected_delivery">TT2</label>
                                                            <input type="text" name="expected_delivery" class="form-control w-100" placeholder="2021">
                                                        </div>
                                                        <div class="mb-2 w-100">
                                                            <label for="expected_delivery">TT3</label>
                                                            <input type="text" name="expected_delivery" class="form-control w-100" placeholder="2021">
                                                        </div>
                                                    </div>
                                                    <!-- last row -->
                                                    <div class="mb-2 d-flex w-100 gap-2">
                                                        <div class="mb-2 w-100 ">
                                                            <label for="place_of_delivery">TT4</label>
                                                            <input type="text" name="place_of_delivery" class="form-control w-100" placeholder="2021">
                                                        </div>
                                                        <div class="mb-2 w-100">
                                                            <label for="expected_delivery">TT5</label>
                                                            <input type="text" name="expected_delivery" class="form-control w-100" placeholder="2021">
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- ASSESSMENT -->
                                                <div class="assessment-con mb-3 border-bottom">
                                                    <h4>ASSESSMENT <small class="text-muted fs-5">(put check if yes)</small></h4>
                                                    <div class="checkboxes d-flex gap-2 mb-2 flex-wrap">
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" name="spotting" class="p-4">
                                                            <label for="spotting" class="w-100 fs-5">Spotting</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" name="spotting" class="p-4">
                                                            <label for="spotting" class="w-100 fs-5">Edema</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" name="spotting" class="p-4">
                                                            <label for="spotting" class="w-100 fs-5">severe headache</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" name="spotting" class="p-4">
                                                            <label for="spotting" class="w-100 fs-5">blumming of vision</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" name="spotting" class="p-4">
                                                            <label for="spotting" class="w-100 fs-5">Watery discharge</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" name="spotting" class="p-4">
                                                            <label for="spotting" class="w-100 fs-5">severe vomiting</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" name="spotting" class="p-4">
                                                            <label for="spotting" class="w-100 fs-5">Hx of smoking </label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" name="spotting" class="p-4">
                                                            <label for="spotting" class="w-100 fs-5">alcohol drinker</label>
                                                        </div>
                                                        <div class="mb-1 d-flex align-items-center gap-1">
                                                            <input type="checkbox" name="spotting" class="p-4">
                                                            <label for="spotting" class="w-100 fs-5">Drug intake</label>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- main info about pregnancy -->
                                                <div class="survey-questionare w-100 ">
                                                    <div class="hatol">
                                                        <label for="" class="fw-bold fs-5">Decision</label>
                                                        <div class="options px-5 py-2">
                                                            <div class="mb-2">
                                                                <input type="radio" name="nurse_decision" id="nurse_f1_option">
                                                                <label for="">Papuntahin sa Doktor/RHU Alamin? Sundan ang kalagayan</label>
                                                            </div>
                                                            <div class="mb-2">
                                                                <input type="radio" name="nurse_decision" id="nurse_f2_option">
                                                                <label for="">Masusing pagsusuri at aksyon ng kumadrona / Nurse</label>
                                                            </div>
                                                            <div class="mb-2">
                                                                <input type="radio" name="nurse_decision" id="nurse_f3_option">
                                                                <label for="">Ipinayong manganak sa Ospital</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Save Record</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- UPDATE NEW DATA -->
                        <div class="modal fade" id="case2PrenatalModal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column">
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
                                                            <input type="text" class="flex-grow-1 form-control" name="midwife" placeholder="(pangalan ng doctor/nars/midwife, atbp.)">
                                                        </div>
                                                    </div>
                                                    <!-- plano ko manganak -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex align-items-center gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Plano kong manganak sa:</label>
                                                            <input type="text" class="flex-grow-1 form-control" name="place_of_birth" placeholder="(pangalan ng hospital/lying-in center/ maternity clinic)">
                                                        </div>
                                                    </div>
                                                    <!-- authorized by philheath -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="midwife" class="fs-5 fw-medium text-nowrap">Ito ay pasilid na otorisado ng Philheath:</label>
                                                            <div class="authorize-radio d-flex gap-3 align-items-center">
                                                                <label for="yes" class="fs-5"> Yes:</label>
                                                                <input type="radio" name="authorized">
                                                                <label for="no" class="fs-5">Hindi:</label>
                                                                <input type="radio" name="authorized" class="mb-0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- cost of pregnancy -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium w-100 text-nowrap ">Ang tinatayang gagastusin ng panganganak sa pasilidad ay (P):</label>
                                                            <input type="number" class="flex-grow-1 form-control" name="place_of_birth">
                                                        </div>
                                                    </div>
                                                    <!-- payment method -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Ang Paraan ng pagbabayad ay:</label>
                                                            <select name="payment_method" id="payment_method" class="form-select flex-grow-1">
                                                                <option value="" disabled selected>Select Payment Method</option>
                                                                <option value="philhealth">PhilHealth</option>
                                                                <option value="cash">Cash / Out-of-Pocket</option>
                                                                <option value="private_insurance">Private Insurance</option>
                                                                <option value="hmo">HMO</option>
                                                                <option value="ngo">NGO / Charity Assistance</option>
                                                                <option value="gov_program">Government Health Program</option>
                                                                <option value="installment">Installment Plan</option>
                                                                <option value="employer">Employer / Company Benefit</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <!-- mode of transportation -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Paraan ng pagbiyahe patungo sa pasilidad ay:</label>
                                                            <select name="transportation_mode" id="transportation_mode" class="form-select flex-grow-1" required>
                                                                <option value="" disabled selected>Select Mode of Transportation</option>
                                                                <option value="walking">Walking</option>
                                                                <option value="tricycle">Tricycle</option>
                                                                <option value="jeepney">Jeepney</option>
                                                                <option value="motorcycle">Motorcycle</option>
                                                                <option value="private_vehicle">Private Vehicle</option>
                                                                <option value="ambulance">Ambulance</option>
                                                                <option value="taxi">Taxi / Grab</option>
                                                                <option value="others">Others</option>
                                                            </select>
                                                        </div>
                                                        <div class="low-box w-100 d-flex justify-content-center">
                                                            <small>(mode of transportation)</small>
                                                        </div>
                                                    </div>
                                                    <!-- person who will bring me to hospital -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Taong magdadala sakin sa hospital: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="place_of_birth" placeholder="Ilagay ang pangalan">
                                                        </div>
                                                    </div>
                                                    <!-- guardian -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Pangalan ng taong sasamahan ako sa panganganak: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="place_of_birth" placeholder="Ilagay ang pangalan">
                                                        </div>
                                                    </div>
                                                    <!-- mag-alalaga -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Pangalan ng taong mag-aalaga sa akin sa panganganak: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="place_of_birth" placeholder="Ilagay ang pangalan">
                                                        </div>
                                                    </div>
                                                    <!-- magbibigay ng dugo -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1 mb-2">
                                                            <label for="place_of_birth" class="fs-5 fw-medium text-nowrap">Maaring magbigay ng dugo, kung sakaling mangailangan: </label>
                                                            <div class="blood-donation d-flex w-100">
                                                                <input type="text" class="w-50 px-2 form-control flex-grow-1" name="place_of_birth" placeholder="Ilagay ang pangalan">
                                                                <button type="button" class="btn btn-success">Add</button>
                                                            </div>
                                                            <!-- hidden input since madami to -->
                                                        </div>
                                                        <div class="lower-box p-3 bg-secondary w-75 justify-self-center">
                                                            <div class="box vaccine d-flex justify-content-between bg-white align-items-center p-1 w-50 rounded">
                                                                <h5 class="mb-0">Jan Loiue Salimbago</h5>
                                                                <div class="delete-icon d-flex align-items-center justify-content-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                                                                        <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h5 class="mb-3">Kung magkaroon ng komplikasyon, kailangan sabihan kaagad si:</h5>
                                                    <!-- persons info -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1 align-items-center">
                                                            <label for="place_of_birth" class="fs-5 text-nowrap">Pangalan: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="place_of_birth" placeholder="Ilagay ang pangalan">
                                                        </div>
                                                    </div>
                                                    <!-- contact info -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1 align-items-center">
                                                            <label for="place_of_birth" class="fs-5">Tirahan: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="place_of_birth" placeholder="address">
                                                        </div>
                                                    </div>
                                                    <!-- contact -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1 align-items-center">
                                                            <label for="place_of_birth" class="fs-5"> Telepono: </label>
                                                            <input type="number" class="flex-grow-1 form-control" name="place_of_birth" placeholder="0936627872">
                                                        </div>
                                                    </div>
                                                    <!-- patient name -->
                                                    <div class="mb-3">
                                                        <div class="upper-box d-flex gap-1 align-items-center">
                                                            <label for="place_of_birth" class="fs-5 text-nowrap">Pangalan ng pasyente: </label>
                                                            <input type="text" class="flex-grow-1 form-control" name="place_of_birth" placeholder="Ilagay ang pangalan">
                                                        </div>
                                                    </div>
                                                    <!-- signature -->
                                                    <div class="mb-3 w-100 d-flex flex-column border-bottom">
                                                        <label for="signature_image">Upload Signature</label>
                                                        <input type="file" name="signature_image" id="signature_image" class="form-control" accept="image/*" required>
                                                        <small class="text-muted text-center">Upload a clear photo or scanned image of the signature.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Save Record</button>
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