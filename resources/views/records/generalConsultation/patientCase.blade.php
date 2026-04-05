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
    'resources/js/vitalSign.js',
    'resources/js/general_consultation/gcCase.js'])

    @include('sweetalert::alert')
    <div class="patient-case min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-x-auto">
            @include('layout.header')
            <div class="flex flex-column flex-grow-1">
                <main class="flex-column p-2 m-md-3 m-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center">
                        <h2>View Patient Details</h2>
                        <div class="sequence-links d-md-flex d-none justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512">
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('record.general.consultation') }}" class="text-decoration-none fs-5 text-muted">General Consultation</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512">
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">Patient Case</a>
                        </div>
                    </div>

                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-lg-5 px-md-3 px-2">

                        <livewire:general-consultation.patient-case :medicalRecordCase="$medical_record_case" />

                        <!-- VIEW CASE MODAL -->
                        <div class="modal fade" id="viewGcRecordModal" tabindex="-1" aria-labelledby="viewGcModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="viewGcModalLabel">General Consultation Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                    </div>
                                    <div class="modal-body">
                                        @include('records.generalConsultation.viewComponent.viewCase')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- VIEW UPDATE CASE MODAL -->
                        <div class="modal fade" id="viewUpdateGcRecordModal" tabindex="-1" aria-labelledby="viewUpdateGcModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="viewUpdateGcModalLabel">General Consultation Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                    </div>
                                    <div class="modal-body">
                                        @include('records.generalConsultation.viewComponent.updateCase')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ADD CASE MODAL -->
                        <div class="modal fade" id="gcModal" tabindex="-1" aria-labelledby="gcModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column" id="add-gc-case-form">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title" id="gcModalLabel">Add Consultation Record</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="inner w-100 rounded">
                                                <div class="bg-light border-start border-primary px-3 py-2 mb-2 rounded w-100">
                                                    <span class="fs-6">
                                                        <strong>Note:</strong>
                                                        <span class="text-danger">*</span>
                                                        <span class="fw-light"> indicates a required field.</span>
                                                    </span>
                                                </div>

                                                <!-- Patient Name -->
                                                <div class="mb-2 w-100">
                                                    <label for="add_patient_name">Patient Name<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control bg-light" disabled id="add_patient_name" value="{{ $medical_record_case->patient->full_name }}">
                                                    <input type="hidden" name="add_patient_full_name" value="{{ $medical_record_case->patient->full_name }}">
                                                </div>

                                                <!-- Handled By -->
                                                @if(Auth::user()->role == 'nurse')
                                                <div class="mb-2 w-100">
                                                    <label for="dissabled_add_handled_by" class="w-100 form-label">Handled By<span class="text-danger">*</span></label>
                                                    <select name="dissabled_add_handled_by" id="dissabled_add_handled_by" class="form-select w-100">
                                                        <option value="" selected disabled>Select the Health Worker</option>
                                                    </select>
                                                    <small class="text-danger w-100" id="add-health-worker-error"></small>
                                                    <input type="hidden" name="add_handled_by" id="hidden_add_handled_by">
                                                </div>
                                                @elseif(Auth::user()->role == 'staff')
                                                <div class="mb-2 w-100">
                                                    <label for="administered_by">Handled By:</label>
                                                    <input type="text" class="form-control bg-light" disabled id="disabled-health-worker-name" value="{{ $healthWorkerName }}">
                                                    <input type="hidden" name="add_handled_by" id="hidden_add_handled_by">
                                                </div>
                                                @endif

                                                <!-- Date of Consultation -->
                                                <div class="mb-2 w-100">
                                                    <label for="add_date_of_consultation">Date of Consultation<span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="add_date_of_consultation" id="add_date_of_consultation" min="1950-01-01" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                                    <small class="text-danger w-100 add_gc_case_record_errors" id="add_date_of_consultation_error"></small>
                                                </div>

                                                <!-- S - Symptoms / Chief Complaint -->
                                                <div class="mb-2 w-100">
                                                    <label for="add_symptoms">S(Symptoms / Chief Complaint)<span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="add_symptoms" id="add_symptoms" rows="3" placeholder="Describe the patient's symptoms or chief complaint..."></textarea>
                                                    <small class="text-danger w-100 add_gc_case_record_errors" id="add_symptoms_error"></small>
                                                </div>

                                                <!-- O - Vital Signs (PE) -->
                                                <div class="mb-2 w-100">
                                                    <label class="fw-semibold">O(P.E) — Vital Signs</label>
                                                    <div class="d-flex gap-2 flex-wrap flex-md-nowrap mt-2">
                                                        <div class="mb-2 flex-fill">
                                                            <label for="blood_pressure">Blood Pressure</label>
                                                            <input type="text" class="form-control" name="add_blood_pressure" id="blood_pressure" placeholder="e.g. 120/80">
                                                            <small class="text-danger add_gc_case_record_errors" id="add_blood_pressure_error"></small>
                                                        </div>
                                                        <div class="mb-2 flex-fill">
                                                            <label for="temperature">Temperature(°C)</label>
                                                            <input type="text" class="form-control" name="add_temperature" id="temperature" placeholder="e.g. 36.5">
                                                            <small class="text-danger add_gc_case_record_errors" id="add_temperature_error"></small>
                                                        </div>
                                                        <div class="mb-2 flex-fill">
                                                            <label for="pulse_rate">Pulse Rate(Bpm)</label>
                                                            <input type="text" class="form-control" name="add_pulse_rate" id="pulse_rate" placeholder="e.g. 72">
                                                            <small class="text-danger add_gc_case_record_errors" id="add_pulse_rate_error"></small>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex gap-2 flex-wrap flex-md-nowrap">
                                                        <div class="mb-2 flex-fill">
                                                            <label for="respiratory_rate">Respiratory Rate(breaths/min)</label>
                                                            <input type="text" class="form-control" name="add_respiratory_rate" id="respiratory_rate" placeholder="e.g. 16">
                                                            <small class="text-danger add_gc_case_record_errors" id="add_respiratory_rate_error"></small>
                                                        </div>
                                                        <div class="mb-2 flex-fill">
                                                            <label for="height">Height(cm)</label>
                                                            <input type="text" class="form-control" name="add_height" id="height" placeholder="e.g. 165">
                                                            <small class="text-danger add_gc_case_record_errors" id="add_height_error"></small>
                                                        </div>
                                                        <div class="mb-2 flex-fill">
                                                            <label for="weight">Weight(kg)</label>
                                                            <input type="text" class="form-control" name="add_weight" id="weight" placeholder="e.g. 65">
                                                            <small class="text-danger add_gc_case_record_errors" id="add_weight_error"></small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- A - Diagnosis / Assessment -->
                                                <div class="mb-2 w-100">
                                                    <label for="add_diagnosis">A(Diagnosis / Assessment)<span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="add_diagnosis" id="add_diagnosis" rows="3" placeholder="Enter the diagnosis or clinical assessment..."></textarea>
                                                    <small class="text-danger w-100 add_gc_case_record_errors" id="add_diagnosis_error"></small>
                                                </div>

                                                <!-- P - Treatment / Plan -->
                                                <div class="mb-2 w-100">
                                                    <label for="add_treatment_plan">P(Treatment / Plan)<span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="add_treatment_plan" id="add_treatment_plan" rows="3" placeholder="Describe the treatment plan, medications, or instructions..."></textarea>
                                                    <small class="text-danger w-100 add_gc_case_record_errors" id="add_treatment_plan_error"></small>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="add-cancel-btn">Cancel</button>
                                            <button type="submit" class="btn btn-success" id="add_gc_case_save_btn" data-bs-case-id="{{ $medical_record_case->id }}">Save Record</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- EDIT CASE MODAL -->
                        <div class="modal fade" id="editGcModal" tabindex="-1" aria-labelledby="editGcModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column" id="edit-gc-case-form">
                                        @method('PUT')
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editGcModalLabel">Edit Consultation Record</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="inner w-100 rounded">
                                                <div class="bg-light border-start border-primary px-3 py-2 mb-2 rounded w-100">
                                                    <span class="fs-6">
                                                        <strong>Note:</strong>
                                                        <span class="text-danger">*</span>
                                                        <span class="fw-light"> indicates a required field.</span>
                                                    </span>
                                                </div>

                                                <!-- Patient Name -->
                                                <div class="mb-2 w-100">
                                                    <label for="edit_patient_name">Patient Name<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control bg-light" disabled id="edit_patient_name">
                                                </div>

                                                <!-- Handled By -->
                                                @if(Auth::user()->role == 'nurse')
                                                <div class="mb-2 w-100">
                                                    <label for="update_handled_by" class="w-100 form-label">Handled By<span class="text-danger">*</span></label>
                                                    <select name="update_handled_by" id="update_handled_by" class="form-select w-100">
                                                        <option value="" selected disabled>Select the Health Worker</option>
                                                    </select>
                                                    <small class="text-danger error-text" id="update_handled_by_error"></small>
                                                </div>
                                                @elseif(Auth::user()->role == 'staff')
                                                <div class="mb-2 w-100">
                                                    <label for="administered_by">Handled By</label>
                                                    <select id="update_handled_by" class="form-select w-100">
                                                        <option value="" selected disabled>Select the Health Worker</option>
                                                    </select>
                                                    <input type="hidden" name="update_handled_by" value="{{ auth()->id() }}">
                                                    <small class="text-danger error-text" id="update_handled_by_error"></small>
                                                </div>
                                                @endif

                                                <!-- Date of Consultation -->
                                                <div class="mb-2 w-100">
                                                    <label for="edit_date_of_consultation">Date of Consultation<span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="date_of_consultation" id="edit_date_of_consultation" min="1950-01-01" max="{{ date('Y-m-d') }}">
                                                    <small class="text-danger error-text" id="update_date_of_consultation_error"></small>
                                                </div>

                                                <!-- S - Symptoms / Chief Complaint -->
                                                <div class="mb-2 w-100">
                                                    <label for="edit_symptoms">S(Symptoms / Chief Complaint)<span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="symptoms" id="edit_symptoms" rows="3" placeholder="Describe the patient's symptoms or chief complaint..."></textarea>
                                                    <small class="text-danger error-text" id="update_symptoms_error"></small>
                                                </div>

                                                <!-- O - Vital Signs (PE) -->
                                                <div class="mb-2 w-100">
                                                    <label class="fw-semibold">O(P.E) — Vital Signs</label>
                                                    <div class="d-flex gap-2 flex-wrap flex-md-nowrap mt-2">
                                                        <div class="mb-2 flex-fill">
                                                            <label for="edit_blood_pressure">Blood Pressure</label>
                                                            <input type="text" class="form-control" name="blood_pressure" id="edit_blood_pressure" placeholder="e.g. 120/80">
                                                            <small class="text-danger error-text" id="update_blood_pressure_error"></small>
                                                        </div>
                                                        <div class="mb-2 flex-fill">
                                                            <label for="edit_temperature">Temperature(°C)</label>
                                                            <input type="text" class="form-control" name="temperature" id="edit_temperature" placeholder="e.g. 36.5">
                                                            <small class="text-danger error-text" id="update_temperature_error"></small>
                                                        </div>
                                                        <div class="mb-2 flex-fill">
                                                            <label for="edit_pulse_rate">Pulse Rate(Bpm)</label>
                                                            <input type="text" class="form-control" name="pulse_rate" id="edit_pulse_rate" placeholder="e.g. 72">
                                                            <small class="text-danger error-text" id="update_pulse_rate_error"></small>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex gap-2 flex-wrap flex-md-nowrap">
                                                        <div class="mb-2 flex-fill">
                                                            <label for="edit_respiratory_rate">Respiratory Rate(breaths/min)</label>
                                                            <input type="text" class="form-control" name="respiratory_rate" id="edit_respiratory_rate" placeholder="e.g. 16">
                                                            <small class="text-danger error-text" id="update_respiratory_rate_error"></small>
                                                        </div>
                                                        <div class="mb-2 flex-fill">
                                                            <label for="edit_height">Height(cm)</label>
                                                            <input type="text" class="form-control" name="height" id="edit_height" placeholder="e.g. 165">
                                                            <small class="text-danger error-text" id="update_height_error"></small>
                                                        </div>
                                                        <div class="mb-2 flex-fill">
                                                            <label for="edit_weight">Weight(kg)</label>
                                                            <input type="text" class="form-control" name="weight" id="edit_weight" placeholder="e.g. 65">
                                                            <small class="text-danger error-text" id="update_weight_error"></small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- A - Diagnosis / Assessment -->
                                                <div class="mb-2 w-100">
                                                    <label for="edit_diagnosis">A(Diagnosis / Assessment)<span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="diagnosis" id="edit_diagnosis" rows="3" placeholder="Enter the diagnosis or clinical assessment..."></textarea>
                                                    <small class="text-danger error-text" id="update_diagnosis_error"></small>
                                                </div>

                                                <!-- P - Treatment / Plan -->
                                                <div class="mb-2 w-100">
                                                    <label for="edit_treatment_plan">P(Treatment / Plan)<span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="treatment_plan" id="edit_treatment_plan" rows="3" placeholder="Describe the treatment plan, medications, or instructions..."></textarea>
                                                    <small class="text-danger error-text" id="update_treatment_plan_error"></small>
                                                </div>

                                                <!-- hidden case record id -->
                                                <input type="number" name="case_record_id" id="gc_case_record_id" hidden>

                                            </div>
                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success" id="update_gc_case_save_btn">Save Record</button>
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
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('record_general_consultation');
            if (con) con.classList.add('active');
        })
    </script>
    @endif
</body>

</html>