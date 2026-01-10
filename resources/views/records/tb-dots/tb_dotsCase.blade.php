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
            const con = document.getElementById('record_tb_dots');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>