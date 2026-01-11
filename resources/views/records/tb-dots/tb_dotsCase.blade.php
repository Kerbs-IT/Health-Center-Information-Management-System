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
    'resources/js/tb_dots/caseRecord.js',
    'resources/js/tb_dots/checkup.js',
    'resources/js/tb_dots/addCaseRecord.js'])
    <div class="patient-details min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-y-auto">
            @include('layout.header')
            <div class="flex flex-column flex-grow-1">
                <main class="flex-column p-md-4 p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center">
                        <h2>View Patient Details</h2>
                        <div class="sequence-links d-md-flex d-none justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('record.tb-dots') }}" class="text-decoration-none fs-5 text-muted">Tuberculosis</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="" class="text-decoration-none fs-5 text-black">Patient Case</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-lg-4 px-md-3 px-2  min-h-[75vh]">
                        <!-- livewire here -->
                        <livewire:tb-dots.patient-case-table :medicalRecordCaseId="$medicalRecordId">
                            <!-- livewire end here -->


                            <!-- VIEW FORM -->
                            <div class="modal fade" id="viewdetailsModal" tabindex="-1" aria-labelledby="seniorCitizenModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-fullscreen-md-down modal-dialog-centered">
                                    @include('records.tb-dots.viewComponent.viewCaseInfo')
                                </div>
                            </div>
                            <!-- VIEW CHECK UP -->
                            <div class="modal fade" id="viewCheckUpModal" tabindex="-1" aria-labelledby="seniorCitizenModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    @include('records.tb-dots.viewComponent.viewFollowUpCheckUp')
                                </div>
                            </div>
                            <!-- ADD FORM modal -->
                            <div class="modal fade" id="tbDotsAddCheckUpModal" tabindex="-1" aria-labelledby="tbDotsAddCheckUpModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="#" class="flex-column" id="add-check-up-form">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title" id="vaccinationModalLabel">Tuberculosis Follow-up Check-up Details</h5>
                                                <button type="button" class="btn-close btn-white" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="bg-light border-start  border-primary px-3 py-2 mb-4 rounded w-100">
                                                    <span class="fs-6">
                                                        <strong>Note:</strong>
                                                        <span class="text-danger">*</span>
                                                        <span class="fw-light"> indicates a required field.</span>
                                                    </span>
                                                </div>
                                                @include('records.tb-dots.addCase.checkUp')

                                            </div>

                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success" data-medical-id="{{$medicalRecordId}}" id="add-check-up-save-btn">Save Record</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Add case modal -->
                            <div class="modal fade" id="tbDotsCaseRecordModal" tabindex="-1" aria-labelledby="tbDotsCaseRecordModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-fullscreen-md-down modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="#" class="flex-column" id="add_tb_dots_case_record_form">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="tbDotsCaseRecordModal">Tuberculosis Medical Record Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="bg-light border-start  border-primary px-3 py-2 mb-4 rounded w-100">
                                                    <span class="fs-6">
                                                        <strong>Note:</strong>
                                                        <span class="text-danger">*</span>
                                                        <span class="fw-light"> indicates a required field.</span>
                                                    </span>
                                                </div>
                                                @include('records.tb-dots.addCase.addCaseRecord')

                                            </div>


                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success" id="add_case_save_btn">Save Record</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- EDIT CASE MEDICAL RECORD INFO -->
                            <div class="modal fade" id="edit_Tb_dots_Record_Modal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl  modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="#" class="flex-column" id="edit_case_info">
                                            @method('PUT')
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="vaccinationModalLabel">Tuberculosis Medical Record Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="bg-light border-start  border-primary px-3 py-2 mb-4 rounded w-100">
                                                    <span class="fs-6">
                                                        <strong>Note:</strong>
                                                        <span class="text-danger">*</span>
                                                        <span class="fw-light"> indicates a required field.</span>
                                                    </span>
                                                </div>
                                                @include('records.tb-dots.addCase.updateMedicalRecord')

                                            </div>


                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success" id="edit_save_btn">Save Record</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- EDIT CASE INFO -->
                            <div class="modal fade" id="edit_tb_dots_checkup_Modal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="#" class="flex-column" id="edit-checkup-form">
                                            @method('PUT')
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="vaccinationModalLabel">Tuberculosis Follow-up Check-up Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="bg-light border-start  border-primary px-3 py-2 mb-4 rounded w-100">
                                                    <span class="fs-6">
                                                        <strong>Note:</strong>
                                                        <span class="text-danger">*</span>
                                                        <span class="fw-light"> indicates a required field.</span>
                                                    </span>
                                                </div>
                                                @include('records.tb-dots.addCase.editCheckUp')

                                            </div>


                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success" id="edit-checkup-save-btn">Save Record</button>
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
            const con = document.getElementById('record_tb_dots');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>