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
    'resources/js/family_planning/sideB.js'])
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
                    <div class="flex-grow-1 py-3 px-5">
                        <div class="tables d-flex flex-column p-3">
                            <div class="add-btn mb-3 d-flex justify-content-between">
                                <a href="{{route('record.family.planning')}}" class="btn btn-danger px-4 fs-5 ">Back</a>
                                <div class="add-btn">
                                    <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#side-a-add-record" data-patient-info='@json($patientInfo)' data-medical-case-record-id="{{$patientInfo->id}}" id="side-a-add-record-btn">Add Side A Record</button>
                                    <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#side-b-add-record" data-patient-info='@json($patientInfo)' id="side-b-add-record-btn">Add Side B Record</button>
                                </div>
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
                                    @foreach($familyPlanningCases as $case)
                                    <tr class="px-">
                                        <td>{{$case->id}}</td>
                                        <td>{{$case->type_of_record}}</td>
                                        <td>Nurse Joy</td>
                                        <td>{{$case->created_at->format('M d, Y')}}</td>
                                        <td>{{$case->status}}</td>

                                        <td>
                                            <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#viewdetailsModal" id="view-family-plan-info" data-case-id="{{$case->id}}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn btn-info text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#editfamilyPlanningCaseModal" id="edit-family-plan-info" data-case-id="{{$case->id}}">Edit</button>
                                                <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3">Archive</button>
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                                </svg>
                                                <!-- <p class="mb-0">None</p> -->
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @foreach($familyPlanningSideB as $sideB)
                                    <tr class="px-">
                                        <td>{{$sideB->id}}</td>
                                        <td>{{$sideB->type_of_record}}</td>
                                        <td>Nurse Joy</td>
                                        <td>{{$sideB->created_at->format('M d, Y')}}</td>
                                        <td>{{$sideB->status}}</td>

                                        <td>
                                            <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#viewSideBmodal" id="view-side-b-record" data-case-id="{{$sideB->id}}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn btn-info text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#editSideBcaseModal" id="edit-side-b-record" data-case-id="{{$sideB->id}}">Edit</button>
                                                <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3">Archive</button>
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                                </svg>
                                                <!-- <p class="mb-0">None</p> -->
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <!-- check if both array is empty -->
                                    <!-- {{-- Show "No Records" only if BOTH are empty --}} -->
                                    @if($familyPlanningCases->isEmpty() && $familyPlanningSideB->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No records available.
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>

                            </table>
                        </div>
                        <!-- VIEW FORM -->
                        <div class="modal fade" id="viewdetailsModal" tabindex="-1" aria-labelledby="seniorCitizenModalLabel" aria-hidden="true">
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