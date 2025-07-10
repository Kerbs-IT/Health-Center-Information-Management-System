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
                            <a href="{{ route('record.family.planning') }}" class="text-decoration-none fs-5 text-muted">Family Planning</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('record.family.planning.case')}}" class="text-decoration-none fs-5 text-black">Patient Case</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-5">
                        <div class="tables d-flex flex-column p-3">
                            <div class="add-btn mb-3 d-flex justify-content-between">
                                <a href="{{route('record.family.planning')}}" class="btn btn-danger px-4 fs-5 ">Back</a>
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
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#viewdetailsModal">
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

                                </tbody>

                            </table>
                        </div>
                        <!-- VIEW FORM -->
                        <div class="modal fade" id="viewdetailsModal" tabindex="-1" aria-labelledby="seniorCitizenModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                @include('records.familyPlanning.viewCase')
                            </div>
                        </div>
                        <!-- ADD FORM modal -->
                        <div class="modal fade" id="vaccinationModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Family Plan Details</h5>
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
                        <!-- EDIT CASE INFO -->
                        <div class="modal fade" id="editVaccinationModal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
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
            const con = document.getElementById('record_family_planning');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>