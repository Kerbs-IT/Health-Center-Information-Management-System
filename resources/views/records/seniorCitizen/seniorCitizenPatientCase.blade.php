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
    <div class="patient-case min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-x-auto">
            @include('layout.header')
            <div class="d-flex flex-column flex-grow-1">
                <main class="flex-column p-md-2 p-0">
                    <div class="top-part d-flex justify-content-lg-between justify-content-end px-md-2 px-1">
                        <h2>View Patient Details</h2>
                        <div class="sequence-links d-none d-md-flex justify-content-center align-items-center">
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
                    <div class="flex-grow-1 py-3 px-lg-5 px-md-3 px-0">

                        <div class="tables d-flex flex-column p-3 table-responsive">
                            <div class="add-btn mb-3 d-flex justify-content-between">
                                <a href="{{route('record.senior.citizen')}}" class="btn btn-danger px-4 fs-5 ">Back</a>
                                <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#vaccinationModal" id="add_record_btn">Add Record</button>
                            </div>
                            <div class="table-responsive">
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
                                        @forelse($seniorCaseRecords as $record)
                                        <tr class="px-">
                                            <td>{{$record->id}}</td>
                                            <td>{{$record->type_of_record}}</td>
                                            <td>Nurse Joy</td>
                                            <td>{{$record->created_at->format('M d Y')}}</td>
                                            <td>{{$record->status}}</td>

                                            <td>

                                                <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                    <button type="button" data-bs-toggle="modal" data-bs-target="#viewdetailsModal" class="viewCaseBtn" data-bs-case-id="{{$record->id}}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                            <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                        </svg>
                                                    </button>
                                                    <button type="button" class="btn btn-info text-white fw-bold px-3 editCaseBtn" data-bs-toggle="modal" data-bs-target="#editVaccinationModal" data-bs-case-id="{{$record->id}}">Edit</button>
                                                    <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3">Archive</button>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="print-icon" style="width: 30px; height:30px;" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                                    </svg>
                                                    <!-- <p class="mb-0">None</p> -->
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                No records available.
                                            </td>
                                        </tr>
                                        @endforelse

                                    </tbody>

                                </table>
                            </div>
                        </div>
                        <!-- VIEW CASE -->
                        <div class="modal fade" id="viewdetailsModal" tabindex="-1" aria-labelledby="seniorCitizenModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
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
                                            <div class="d-flex p-lg-4 p-md-2 p-2 flex-column">
                                                <div class="contents bottom-border">
                                                    <div class="mb-md-3 mb-1">
                                                        <label for="">Patient Name:</label>
                                                        <input type="text" disabled class="form-control" id="dissabled_patient_name" value="{{$patient_name}}">
                                                        <input type="hidden" name="new_patient_name" id="new_patient_name" value="{{$patient_name}}">
                                                        <input type="hidden" name="add_health_worker_id" value="{{$healthWorkerId}}">
                                                    </div>
                                                    <h5>MEDICAL INFORMATION</h5>
                                                    <div class="mb-md-3 mb-1">
                                                        <label for="">Existing Medical Condition</label>
                                                        <input type="text" class="form-control" name="add_existing_medical_condition" id="new_existing_medical_condition">
                                                    </div>
                                                    <div class="mb-md-3 mb-1">
                                                        <label for="">Alergies</label>
                                                        <input type="text" class="form-control" name="add_alergies" id="new_alergies">
                                                    </div>
                                                    <div class="maintenance-con d-flex gap-2 flex-wrap flex-xl-nowrap align-items-center">
                                                        <div class="mb-md-3 mb-0 flex-fill">
                                                            <label for="" class="text-nowrap">Maintenance Medication</label>
                                                            <input type="text" class="form-control" id="add_maintenance_medication">
                                                        </div>
                                                        <div class="mb-md-3 mb-0 flex-fill">
                                                            <label for="">Dosage & Frequency</label>
                                                            <input type="text" class="form-control" id="add_dosage_n_frequency">
                                                        </div>
                                                        <div class="mb-md-3 mb-0 flex-fill">
                                                            <label for="">Quantity</label>
                                                            <input type="number" class="form-control" id="add_maintenance_quantity">
                                                        </div>
                                                        <div class="mb-md-3 mb-0 flex-fill">
                                                            <label for="">Start Date</label>
                                                            <input type="date" class="form-control" id="add_maintenance_start_date">
                                                        </div>
                                                        <div class="mb-md-3 mb-0 flex-fill">
                                                            <label for="">End Date</label>
                                                            <input type="date" class="form-control" id="add_maintenance_end_date">
                                                        </div>
                                                        <div class="mb-md-3 mb-1 flex-fill d-flex flex-column flex-md-none">
                                                            <label for="" class="text-white text-nowrap">End Date</label>
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
                                                    <div class="mb-3">
                                                        <label for="">Prescribing Nurse</label>
                                                        <input type="text" class="form-control" name="add_prescribe_by_nurse" id="add_prescribe_by_nurse">
                                                    </div>
                                                    <div class="mb-3 border-bottom">
                                                        <label for="" class="text-nowrap">Remarks *</label>
                                                        <input type="text" class="form-control p-3" name="add_medication_maintenance_remarks">
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
                        <div class="modal fade" id="editVaccinationModal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column" id="edit-senior-citizen-form">
                                        @method('PUT')
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Senior Citizen Medicine Maintenance Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="d-flex p-4 flex-column">
                                                <div class="contents bottom-border">
                                                    <div class="mb-md-3 mb-1">
                                                        <label for="">Patient Name:</label>
                                                        <input type="text" class="form-control" disabled id="edit_patient_name">
                                                    </div>
                                                    <h5>MEDICAL INFORMATION</h5>
                                                    <div class="mb-md-3 mb-1">
                                                        <label for="">Existing Medical Condition</label>
                                                        <input type="text" class="form-control" name="edit_existing_medical_condition" id="edit_existing_medical_condition">
                                                    </div>
                                                    <div class="mb-md-3 mb-1">
                                                        <label for="">Alergies</label>
                                                        <input type="text" class="form-control" id="edit_alergies" name="edit_alergies">
                                                    </div>
                                                    <div class="maintenance-con d-flex gap-2 align-items-center flex-wrap flex-xl-nowrap">
                                                        <div class="mb-md-3 mb-1 flex-fill">
                                                            <label for="" class="text-nowrap">Maintenance Medication</label>
                                                            <input type="text" class="form-control" id="edit_maintenance_medication">
                                                        </div>
                                                        <div class="mb-md-3 mb-1 flex-fill">
                                                            <label for="">Dosage & Frequency</label>
                                                            <input type="text" class="form-control" id="edit_dosage_n_frequency">
                                                        </div>
                                                        <div class="mb-md-3 mb-1 flex-fill">
                                                            <label for="">Quantity</label>
                                                            <input type="number" class="form-control" value="90 days" id="edit_maintenance_quantity">
                                                        </div>
                                                        <div class="mb-md-3 mb-1 flex-fill">
                                                            <label for="">Start Date</label>
                                                            <input type="date" class="form-control" id="edit_maintenance_start_date">
                                                        </div>
                                                        <div class="mb-md-3 mb-1 flex-fill">
                                                            <label for="">End Date</label>
                                                            <input type="date" class="form-control" id="edit_maintenance_end_date">
                                                        </div>
                                                        <div class="mb-md-3 mb-1 flex-fill d-flex flex-column flex-md-none">
                                                            <label for="" class="text-white text-nowrap">End Date</label>
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
                                                    </div>
                                                    <div class="mb-3 border-bottom">
                                                        <label for="" class="text-nowrap">Remarks*</label>
                                                        <input type="text" class="form-control p-3" name="edit_medication_maintenance_remarks" id="edit_remarks">
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