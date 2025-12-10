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
    'resources/js/family_planning/case.js',
    'resources/js/family_planning/sideB.js',
    'resources/js/family_planning/editPatientCase.js'])
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
                            <a href="{{ route('record.family.planning') }}" class="text-decoration-none fs-5 text-muted">Family Planning</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="" class="text-decoration-none fs-5 text-black">Patient Case</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-5 ">
                        <!-- LIVEWIRE HERE -->
                        <livewire:family-planning.patient-case-table :medicalRecordCaseId="$medicalRecordCaseId">

                            <!-- END OF LIVEWIRE HERE -->

                            <!-- VIEW FORM -->
                            <div class="modal fade" id="viewdetailsModal" tabindex="-1" aria-labelledby="familyPlanModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    @include('records.familyPlanning.viewCase')
                                </div>
                            </div>
                            <!-- ADD SIDE A FORM modal -->
                            <div class="modal fade" id="side-a-add-record" tabindex="-1" aria-labelledby="addFamilyPlanningSideAmodalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="#" class="flex-column" id="side-a-add-form">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="add-family-planning-side-b-label">FAMILY PLANNING CLIENT ASSESSMENT RECORD-SIDE A</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <!-- Add side a content here -->
                                                <!-- STEP 1 -->
                                                @include('records.familyPlanning.sideRecord.sideAtemplate.sideAstep1')
                                                <!-- STEP 2 -->
                                                @include('records.familyPlanning.sideRecord.sideAtemplate.sideAstep2')
                                                <!-- STEP 3 -->
                                                @include('records.familyPlanning.sideRecord.sideAtemplate.sideAstep3')
                                                <!-- STEP 4 -->
                                                @include('records.familyPlanning.sideRecord.sideAtemplate.sideAstep4')
                                                <!-- STEP 5 -->
                                                @include('records.familyPlanning.sideRecord.sideAtemplate.sideAstep5')

                                            </div>

                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success" id="side-a-save-record-btn">Upload Record</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- ADD SIDE B FORM modal -->
                            <div class="modal fade" id="side-b-add-record" tabindex="-1" aria-labelledby="addFamilyPlanningModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="#" class="flex-column" id="side-b-add-form">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="add-family-planning-side-b-label">FAMILY PLANNING CLIENT ASSESSMENT RECORD</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <!-- Add side b content here -->
                                                @include('records.familyPlanning.sideRecord.sideB')
                                            </div>

                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success" id="side-b-save-record-btn">Save Record</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- EDIT CASE INFO -->
                            <div class="modal fade" id="editfamilyPlanningCaseModal" tabindex="-1" aria-labelledby="editfamilyPlanningCaseModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered" tabindex="-1">
                                    <div class="modal-content">
                                        <form method="POST" action="#" class="flex-column" id="edit-family-plan-form">
                                            @method('PUT')
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title table-header" id="edit-family-planning-side-a">Edit Family Plan Details</h5>
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
                                                <button type="submit" class="btn btn-success" id="edit-family-planning-case-btn">Save Record</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- VIEW SIDE B RECORD -->
                            <!-- VIEW FORM -->
                            <div class="modal fade" id="viewSideBmodal" tabindex="-1" aria-labelledby="viewFamilyPlanningSideBrecord" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered ">
                                    <div class="modal-content">
                                        <div class="modal-header table-header">
                                            <h5 class="mb-0">SIDE B</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('records.familyPlanning.sideRecord.viewSideBrecord')
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- EDIT SIDE B RECORD -->
                            <div class="modal fade" id="editSideBcaseModal" tabindex="-1" aria-labelledby="editSideBcaseModal" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered" tabindex="-1">
                                    <div class="modal-content">
                                        <form method="POST" action="#" class="flex-column" id="edit-side-b-family-plan-form">
                                            @method('PUT')
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title table-header" id="edit-side-b-family-planning-side-b">EDIT FAMILY PLANNING CLIENT ASSESSMENT RECORD</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                @include('records.familyPlanning.sideRecord.editSideB')
                                            </div>
                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success" id="edit-side-b-family-planning-assessment-btn">Save Record</button>
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
            const con = document.getElementById('record_family_planning');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>