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
                            <a href="{{ route('record.vaccination') }}" class="text-decoration-none fs-5 text-muted">Vaccination</a>
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
                                <a href="{{route('record.vaccination')}}" class="btn btn-danger px-4 fs-5 ">Back</a>
                                <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#vaccinationModal" id="add-vaccination-case-record-btn">Add Record</button>
                                <!-- <div>{{$medical_record_case ->id}}</div> -->
                            </div>
                            <table class="w-100 table ">
                                <thead class="table-header">
                                    <tr>
                                        <th>Case No.</th>
                                        <th>Vaccine Type/s</th>
                                        <th>Dosage</th>
                                        <th>Nurse</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!-- data of patient -->
                                <tbody>
                                    @foreach($vaccination_case_record as $record)
                                    <tr class="px-">
                                        <!-- <div>{{$record}}</div> -->

                                        <td>{{$record->id}}</td>
                                        <td>{{$record->vaccine_type}}</td>
                                        <td>{{$record->dose_number}}{{$record->dose_number == 1 ? 'st':'th'}} Dose</td>
                                        <td>Nurse Joy</td>
                                        <td>{{ \Carbon\Carbon::parse($record->date_of_vaccination)->format('M j, Y') }}</td>
                                        <td>Done</td>

                                        <td>
                                            <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#viewVaccinationRecordModal" class="view-case-info" data-bs-case-id="{{$record->id}}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn btn-info text-white fw-bold px-3 case-edit-btn" data-bs-toggle="modal" data-bs-target="#editVaccinationModal" data-bs-case-id="{{$record->id}}">Edit</button>
                                                <button type="button" class="btn btn-danger archive-record-icon text-white fw-bold px-3" data-bs-case-id="{{$record->id}}">Archive</button>
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
                                                    <select name="add_handled_by" id="add_handled_by" class="form-select w-100" required>
                                                        <option value="" selected disabled>Select the Health Worker</option>
                                                    </select>
                                                    <small class="text-danger w-100" id="add-health-worker-error"></small>
                                                </div>
                                                @elseif(Auth::user()-> role == 'staff')
                                                <div class="mb-2 w-100">
                                                    <label for="administered_by">Handled By:</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Nurse" value="Nurse Joy">
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

                                                <div class="mb-2">
                                                    <label for="vaccine_type">Vaccine Type:</label>
                                                    <div class="d-flex gap-2">
                                                        <select id="add_vaccine_type" class="form-select w-100">
                                                            <option value="" selected dissabled>Select Vaccine</option>
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

                                                <div class="mb-2 w-100">
                                                    <label for="remarks">Remarks*</label>
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
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="time_of_vaccination">Time</label>
                                                    <input type="time" class="form-control" name="time_of_vaccination" id="edit-time-of-vaccination">
                                                </div>
                                                <!-- Hidden data -->
                                                <div class="vaccine-administered" hidden id="vaccine-administered"></div>

                                                <div class="mb-2">
                                                    <label for="vaccine_type">Vaccine Type:</label>
                                                    <div class="d-flex gap-2">
                                                        <select name="vaccine_type" id="update_vaccine_type" class="form-select w-100">
                                                            <option value="">Select Vaccine</option>
                                                        </select>
                                                        <button type="button" class="btn btn-success" id="update-add-vaccine-btn">Add</button>
                                                    </div>
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
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="remarks">Remarks*</label>
                                                    <input type="text" class="form-control" id="edit-remarks" name="remarks">
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