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
    'resources/js/vaccination/vaccinationCase.js'])

    @include('sweetalert::alert')
    <div class="patient-case min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-x-auto">
            @include('layout.header')
            <div class="flex flex-column flex-grow-1">
                <main class="flex-column p-2 shadow-lg m-md-3 m-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center">
                        <h2>View Patient Details</h2>
                        <div class="sequence-links d-md-flex d-none justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('record.vaccination') }}" class="text-decoration-none fs-5 text-muted">Vaccination</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">Patient Case</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-lg-5 px-md-3 px-2">

                        <livewire:vaccination.patient-case :medicalRecordCase="$medical_record_case" />
                        <!-- view case info -->
                        <div class="modal fade" id="viewVaccinationRecordModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Details</h5>
                                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                    </div>

                                    <div class="modal-body">
                                        @include('records.vaccination.viewComponent.viewCase')
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- view update info -->
                        <div class="modal fade" id="viewUpdateVaccinationRecordModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Details</h5>
                                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                    </div>

                                    <div class="modal-body">
                                        @include('records.vaccination.viewComponent.updateCase')
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ADD FORM modal -->
                        <div class="modal fade" id="vaccinationModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column" id="add-vaccination-case-form">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Details</h5>
                                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="inner w-100 rounded">

                                                <div class="mb-2 w-100">
                                                    <label for="patient_name">Patient Name</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Enter the name" id="add-patient-name-vaccination-case" value="{{$medical_record_case->patient->full_name}}">
                                                    <input type="text" name="add_patient_full_name" value="{{$medical_record_case->patient->full_name}}" hidden>
                                                </div>

                                                @if(Auth::user()-> role == 'nurse')
                                                <div class="mb-2 w-100">
                                                    <label for="update_handled_by" class="w-100 form-label">Handled By:</label>
                                                    <select name="dissabled_add_handled_by" id="dissabled_add_handled_by" class="form-select w-100" required>
                                                        <option value="" selected disabled>Select the Health Worker</option>
                                                    </select>
                                                    <small class="text-danger w-100" id="add-health-worker-error"></small>
                                                    <input type="hidden" name="add_handled_by" id="hidden_add_handled_by">
                                                </div>
                                                @elseif(Auth::user()-> role == 'staff')
                                                <div class="mb-2 w-100">
                                                    <label for="administered_by">Handled By:</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Nurse" id="disabled-health-worker-name" value="{{$healthWorkerName}}">
                                                    <input type="hidden" name="add_handled_by" id="hidden_add_handled_by">
                                                </div>
                                                @endif

                                                <div class="mb-2 w-100">
                                                    <label for="date_of_vaccination">Date of Vaccination</label>
                                                    <input type="date" class="form-control" name="add_date_of_vaccination" id="add-date-of-vaccination">
                                                    <small class="text-danger w-100" id="add-date-error"></small>
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="time_of_vaccination">Time</label>
                                                    <input type="time" class="form-control" name="add_time_of_vaccination" id="add-time-of-vaccination">
                                                    <small class="text-danger w-100" id="add-time-error"></small>
                                                </div>
                                                <div class="mb-2 w-100 d-flex gap-2 flex-md-nowrap">
                                                    <div class="mb-2  w-[100%] md:w-[50%]">
                                                        <label for="add_weight">Weight</label>
                                                        <input type="number" class="form-control" name="add_weight" id="add_weight" required placeholder="00.0">
                                                        <small class="text-danger error-text" id="add_weight_error"></small>
                                                    </div>
                                                    <div class="mb-2 w-[100%] md:w-[50%]">
                                                        <label for="time">Height</label>
                                                        <input type="number" class="form-control" name="add_height" id="add_height" required placeholder="00.0">
                                                        <small class="text-danger error-text" id="add_height_error"></small>
                                                    </div>
                                                    <div class="mb-2 w-[100%] md:w-[50%]">
                                                        <label for="add_temperature">Temperature</label>
                                                        <input type="number" class="form-control" name="add_temperature" id="add_temperature" required placeholder="00.0">
                                                        <small class="text-danger error-text" id="add_temperature_error"></small>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="vaccine_type">Vaccine Type:</label>
                                                    <div class="d-flex gap-2">
                                                        <select id="add_vaccine_type" class="form-select w-100">
                                                            <option value="" selected disabled>Select Vaccine</option>
                                                        </select>
                                                        <button type="button" class="btn btn-success" id="add-vaccination-btn">Add</button>
                                                    </div>
                                                </div>
                                                <!-- hidden input -->
                                                <input type="text" name="selected_vaccine_type" id="add-selected-vaccines" hidden>
                                                <!-- vaccine container -->
                                                <div class="mb-2 bg-secondary p-3 d-flex flex-wrap rounded gap-2" id="add-vaccine-container">

                                                </div>
                                                <small class="text-danger w-100" id="selected-vaccine-error"></small>

                                                <div class="mb-2 w-100">
                                                    <label for="dose">Vaccine Dose Number:</label>
                                                    <select id="dose" name="add_record_dose" required class="form-select" id="add_case_dose">
                                                        <option value="" disabled selected>Select Dose</option>
                                                        <option value="1">1st Dose</option>
                                                        <option value="2">2nd Dose</option>
                                                        <option value="3">3rd Dose</option>
                                                    </select>
                                                    <small class="text-danger w-100" id="add-dose-error"></small>
                                                </div>
                                                <div class="mb-2 w-100 ">
                                                    <div class="mb-2 w-100">
                                                        <label for="add_date_of_comeback">Date of Comeback*</label>
                                                        <input type="date" placeholder="20" id="add_date_of_comeback" class="form-control w-100 " name="add_date_of_comeback" required min="1950-01-01" max="{{date('Y-m-d',strtotime('+5 years'))}}">
                                                        <small class="text-danger error-text" id="add-date-of-comeback-error"></small>
                                                    </div>
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="remarks">Remarks</label>
                                                    <input type="text" class="form-control" id="remarks" name="add_case_remarks" id="add_case_remarks">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="add-cancel-btn">Cancel</button>
                                            <button type="submit" class="btn btn-success" id="add_case_save_btn" data-bs-case-id="{{$medical_record_case-> id}}">Save Record</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- EDIT CASE INFO -->
                        <div class="modal fade" id="editVaccinationModal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column" id="edit-vaccination-case-form">
                                        @method('PUT')
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="inner w-100 rounded">

                                                <div class="mb-2 w-100">
                                                    <label for="patient_name">Patient Name</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Jan Louie Salimbago" id="edit-patient-name">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="administered_by">Administered By</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Nurse" value="Nurse Joy">
                                                </div>
                                                @if(Auth::user()-> role == 'nurse')
                                                <div class="mb-2 w-100">
                                                    <label for="update_handled_by" class="w-100 form-label">Handled By:</label>
                                                    <select name="update_handled_by" id="update_handled_by" class="form-select w-100">
                                                        <option value="" selected disabled>Select the Health Worker</option>
                                                    </select>
                                                    <small class="text-danger error-text" id="update_handled_by_error"></small>
                                                </div>
                                                @elseif(Auth::user()-> role == 'staff')
                                                <div class="mb-2 w-100">
                                                    <label for="administered_by">Handled By:</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Nurse" value="Nurse Joy">
                                                </div>
                                                @endif

                                                <div class="mb-2 w-100">
                                                    <label for="date_of_vaccination">Date of Vaccination</label>
                                                    <input type="date" id="edit_date_of_vaccination" class="form-control" name="date_of_vaccination">
                                                    <small class="text-danger error-text" id="date_of_vaccination_error"></small>
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="time_of_vaccination">Time</label>
                                                    <input type="time" class="form-control" name="time_of_vaccination" id="edit-time-of-vaccination">
                                                    <small class="text-danger error-text" id="time_of_vaccination_error"></small>
                                                </div>
                                                <!-- Hidden data -->
                                                <div class="vaccine-administered" hidden id="vaccine-administered"></div>
                                                <div class="mb-2 w-100 d-flex gap-2 flex-md-nowrap">
                                                    <div class="mb-2 w-[100%] md:w-[50%]">
                                                        <label for="weight">Weight</label>
                                                        <input type="number" class="form-control" name="weight" id="edit-weight" required placeholder="00.0">
                                                        <small class="text-danger error-text" id="weight_error"></small>
                                                    </div>
                                                    <div class="mb-2 w-[100%] md:w-[50%]">
                                                        <label for="time">Height</label>
                                                        <input type="number" class="form-control" name="height" id="edit-height" required placeholder="00.0">
                                                        <small class="text-danger error-text" id="height_error"></small>
                                                    </div>
                                                    <div class="mb-2 w-[100%] md:w-[50%]">
                                                        <label for="temperature">Temperature</label>
                                                        <input type="number" class="form-control" name="temperature" id="edit-temperature" required placeholder="00.0">
                                                        <small class="text-danger error-text" id="temperature_error"></small>
                                                    </div>
                                                </div>

                                                <div class="mb-2">
                                                    <label for="vaccine_type">Vaccine Type:</label>
                                                    <div class="d-flex gap-2">
                                                        <select name="vaccine_type" id="update_vaccine_type" class="form-select w-100">
                                                            <option value="">Select Vaccine</option>
                                                        </select>
                                                        <button type="button" class="btn btn-success" id="update-add-vaccine-btn">Add</button>
                                                    </div>
                                                    <small class="text-danger error-text" id="vaccine_type_error"></small>
                                                </div>
                                                <!-- container of the vaccines -->
                                                <div class="mb-2 bg-secondary p-3 d-flex flex-wrap rounded gap-2 update-vaccine-container justify-content-center">


                                                </div>
                                                <!-- hidden inputs -->
                                                <input type="text" name="selected_vaccine" id="update_selected_vaccine" hidden>
                                                <input type="number" name="case_record_id" id="case_record_id" hidden>

                                                <div class="mb-2 w-100">
                                                    <label for="dose">Vaccine Dose Number:</label>
                                                    <select id="edit-dose" name="dose" required class="form-select">
                                                        <option value="" disabled>Select Dose</option>
                                                        <option value="1" selected>1st Dose</option>
                                                        <option value="2">2nd Dose</option>
                                                        <option value="3">3rd Dose</option>
                                                    </select>
                                                    <small class="text-danger error-text" id="dose_error"></small>
                                                </div>
                                                <div class="mb-2 w-100 ">
                                                    <div class="mb-2 w-100">
                                                        <label for="edit-date-of-comeback">Date of Comeback*</label>
                                                        <input type="date" placeholder="20" class="form-control w-100 " id="edit-date-of-comeback" name="date_of_comeback" required min="1950-01-01" max="{{date('Y-m-d',strtotime('+5 years'))}}">
                                                        <small class="text-danger error-text" id="date_of_comeback_error"></small>
                                                    </div>
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="remarks">Remarks</label>
                                                    <input type="text" class="form-control" id="edit-remarks" name="remarks">
                                                    <small class="text-danger error-text" id="remarks_error"></small>
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
                    </div>
                </main>
            </div>
        </div>
    </div>
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('record_vaccination');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>