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
    'resources/css/patient/record.css',
    'resources/js/senior_citizen/caseRecord.js',
    'resources/js/senior_citizen/addCase.js'])
    <div class="patient-details min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-y-auto">
            @include('layout.header')
            <div class="d-flex flex-column flex-grow-1 ">
                <main class="flex-column p-2 overflow-x-auto">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center">
                        <h2>View Patient Details</h2>
                        <div class="sequence-links d-md-flex d-none justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('record.senior.citizen') }}" class="text-decoration-none fs-5 text-muted">Senior Citizen</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">Patient Case</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-lg-4 px-md-3 px-2 shadow-lg min-h-[75vh]">

                        <!---------------------------- LIVEWIRE HERE ----------------------------------->
                        <livewire:senior-citizen.patient-case-table :caseId="$medicalRecordId">
                            <!---------------------------- END OF LIVEWIRE --------------------------------->



                            <!-- VIEW CASE -->
                            <div class="modal fade" id="viewdetailsModal" tabindex="-1" aria-labelledby="seniorCitizenModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-fullscreen-lg-down modal-dialog-centered">
                                    @include('records.seniorCitizen.viewCase')
                                </div>
                            </div>

                            <!-- ADD FORM modal -->
                            <div class="modal fade" id="vaccinationModal" tabindex="-1" aria-labelledby="vaccinationModalLabel">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="#" class="flex-column" id="add-new-record-form">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="vaccinationModalLabel">Senior Citizen Medicine Maintenance Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="d-flex p-md-4 px-2 flex-column">
                                                    <div class="contents bottom-border">
                                                        <div class="mb-md-3 mb-0">
                                                            <label for="">Patient Name:</label>
                                                            <input type="text" disabled class="form-control" id="dissabled_patient_name" value="{{$patient_name}}">
                                                            <input type="hidden" name="new_patient_name" id="new_patient_name" value="{{$patient_name}}">
                                                            <input type="hidden" name="add_health_worker_id" value="{{$healthWorkerId}}">
                                                        </div>
                                                        <h5>MEDICAL INFORMATION</h5>
                                                        <div class="mb-md-3 mb-0">
                                                            <label for="new_existing_medical_condition">Existing Medical Condition</label>
                                                            <input type="text" class="form-control" name="add_existing_medical_condition" id="new_existing_medical_condition">
                                                            <small class="text-danger error-text" id="add_existing_medical_condition_error"></small>
                                                        </div>
                                                        <div class="mb-md-3 mb-0">
                                                            <label for="new_alergies">Alergies</label>
                                                            <input type="text" class="form-control" name="add_alergies" id="new_alergies">
                                                            <small class="text-danger error-text" id="add_alergies_error"></small>
                                                        </div>
                                                        <div class="maintenance-con d-flex gap-2 flex-wrap flex-xl-nowrap flex-md-row flex-column">
                                                            <div class="mb-3 flex-fill">
                                                                <label for="" class="text-nowrap">Maintenance Medication</label>
                                                                <input type="text" class="form-control" id="add_maintenance_medication">
                                                            </div>
                                                            <div class="mb-3 flex-fill">
                                                                <label for="">Dosage & Frequency</label>
                                                                <input type="text" class="form-control" id="add_dosage_n_frequency">
                                                            </div>
                                                            <div class="mb-3 flex-fill">
                                                                <label for="">Quantity</label>
                                                                <input type="number" class="form-control" id="add_maintenance_quantity">
                                                            </div>
                                                            <div class="mb-3 flex-fill">
                                                                <label for="">Start Date</label>
                                                                <input type="date" class="form-control" id="add_maintenance_start_date">
                                                            </div>
                                                            <div class="mb-3 flex-fill">
                                                                <label for="">End Date</label>
                                                                <input type="date" class="form-control" id="add_maintenance_end_date">
                                                            </div>
                                                            <div class="mb-3 flex-fill  d-flex flex-column">
                                                                <label for="" class="text-white">End Date</label>
                                                                <button type="button" class="btn btn-success" id="add-record-btn">Add</button>
                                                            </div>
                                                        </div>
                                                        <!-- table -->
                                                        <div class="table-responsive">
                                                            <table class="w-100 table">
                                                                <thead>
                                                                    <tr class="table-header">
                                                                        <th>Maintenance Medication</th>
                                                                        <th>Dosage & Frequency</th>
                                                                        <th>Duration</th>
                                                                        <th>Start Date</th>
                                                                        <th>End Date</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="add-record-body">
                                                                    <!-- <tr>
                                                                    <td>Amlodipine 5mg</td>
                                                                    <td>1x/day</td>
                                                                    <td>90 days</td>
                                                                    <td>2025-01-01</td>
                                                                    <td>2025-02-01</td>
                                                                    <td class=" align-middle text-center">
                                                                        <div class="delete-icon d-flex align-items-center justify-self-center w-100 h-100">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" width="20" height="20" viewBox="0 0 448 512">
                                                                                <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                                                                            </svg>
                                                                        </div>
                                                                    </td>
                                                                </tr> -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <!-- prescribing  -->
                                                        <div class="mb-md-3 mb-0">
                                                            <label for="">Prescribing Nurse</label>
                                                            <input type="text" class="form-control" name="add_prescribe_by_nurse" id="add_prescribe_by_nurse">
                                                            <small class="text-danger error-text" id="add_prescribe_by_nurse_error"></small>
                                                        </div>
                                                        <div class="mb-md-3 mb-0 ">
                                                            <label for="" class="text-nowrap">Remarks *</label>
                                                            <input type="text" class="form-control p-2 border" name="add_medication_maintenance_remarks">
                                                            <small class="text-danger error-text" id="add_medication_maintenance_remarks_error"></small>
                                                        </div>
                                                        <div class="mb-md-3 mb-0 ">
                                                            <label for="add_date_of_comeback">Date of Comeback</label>
                                                            <input type="date" class="form-control border" name="add_date_of_comeback" id="add_date_of_comeback">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success" data-bs-medical-id="{{$medicalRecordId}}" id="add-new-record-save-btn">Save Record</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- EDIT CASE INFO -->
                            <div class="modal fade" id="editSeniorCitizenModal" tabindex="-1" aria-labelledby="editSeniorCitizenModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl  modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="#" class="flex-column" id="edit-senior-citizen-form">
                                            @method('PUT')
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="vaccinationModalLabel">Senior Citizen Medicine Maintenance Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="d-flex p-md-4 p-2 flex-column">
                                                    <div class="contents bottom-border">
                                                        <div class="mb-3">
                                                            <label for="">Patient Name:</label>
                                                            <input type="text" class="form-control" disabled id="edit_patient_name">
                                                        </div>
                                                        <h5>MEDICAL INFORMATION</h5>
                                                        <div class="mb-3">
                                                            <label for="">Existing Medical Condition</label>
                                                            <input type="text" class="form-control" name="edit_existing_medical_condition" id="edit_existing_medical_condition">
                                                            <small class="text-danger error-text" id="edit_existing_medical_condition_error"></small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="">Alergies</label>
                                                            <input type="text" class="form-control" id="edit_alergies" name="edit_alergies">
                                                            <small class="text-danger error-text" id="edit_alergies_error"></small>
                                                        </div>
                                                        <div class="maintenance-con d-flex gap-2 flex-wrap flex-xl-nowrap flex-md-row flex-column">
                                                            <div class="mb-3 flex-fill">
                                                                <label for="" class="text-nowrap">Maintenance Medication</label>
                                                                <input type="text" class="form-control" id="edit_maintenance_medication">
                                                            </div>
                                                            <div class="mb-3 flex-fill">
                                                                <label for="">Dosage & Frequency</label>
                                                                <input type="text" class="form-control" id="edit_dosage_n_frequency">
                                                            </div>
                                                            <div class="mb-3 flex-fill">
                                                                <label for="">Quantity</label>
                                                                <input type="number" class="form-control" value="90 days" id="edit_maintenance_quantity">
                                                            </div>
                                                            <div class="mb-3 flex-fill">
                                                                <label for="">Start Date</label>
                                                                <input type="date" class="form-control" id="edit_maintenance_start_date">
                                                            </div>
                                                            <div class="mb-3 flex-fill">
                                                                <label for="">End Date</label>
                                                                <input type="date" class="form-control" id="edit_maintenance_end_date">
                                                            </div>
                                                            <div class="mb-3 flex-fill d-flex flex-column">
                                                                <label for="" class="text-white">End</label>
                                                                <button type="button" class="btn btn-success" id="edit-add-btn">Add</button>
                                                            </div>
                                                        </div>
                                                        <!-- table -->
                                                         <div class="table-responsive">
                                                            <table class="w-100 table">
                                                                <thead>
                                                                    <tr class="table-header">
                                                                        <th>Maintenance Medication</th>
                                                                        <th>Dosage & Frequency</th>
                                                                        <th>Duration</th>
                                                                        <th>Start Date</th>
                                                                        <th>End Date</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="edit-tbody" id="edit-tbody">
                                                                    <!-- <tr>
                                                                    <td>Amlodipine 5mg</td>
                                                                    <td>1x/day</td>
                                                                    <td>90 days</td>
                                                                    <td>2025-01-01</td>
                                                                    <td>2025-02-01</td>
                                                                    <td class=" align-middle text-center">
                                                                        <div class="delete-icon d-flex align-items-center justify-self-center w-100 h-100">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" width="20" height="20" viewBox="0 0 448 512">
                                                                                <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                                                                            </svg>
                                                                        </div>
                                                                    </td>
                                                                </tr> -->
                                                                </tbody>
                                                            </table>
                                                         </div>
                                                        <!-- prescribing  -->
                                                        <div class="mb-3">
                                                            <label for="">Prescribing Nurse</label>
                                                            <input type="text" class="form-control p-3" name="edit_prescribe_by_nurse" id="edit_prescribe_by_nurse">
                                                            <small class="text-danger error-text" id="edit_prescribe_by_nurse_error"></small>
                                                        </div>
                                                        <div class="mb-3 ">
                                                            <label for="" class="text-nowrap">Remarks*</label>
                                                            <input type="text" class="form-control p-2 border" name="edit_medication_maintenance_remarks" id="edit_remarks">
                                                            <small class="text-danger error-text" id="edit_medication_maintenance_remarks_error"></small>
                                                        </div>

                                                        <!-- date of comeback -->
                                                        <div class="mb-3 ">
                                                            <label for="edit_date_of_comeback">Date of Comeback</label>
                                                            <input type="date" class="form-control bg-light border" name="edit_date_of_comeback" id="edit_date_of_comeback">
                                                            <small class="text-danger error-text" id="edit_date_of_comeback_error"></small>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success" id="edit-save-btn">Save Record</button>
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
            const con = document.getElementById('record_senior_citizen');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>