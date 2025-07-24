<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body >
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/js/patient/add-patient.js',
    'resources/css/patient/record.css',
    'resources/js/record/record.js'])

    <div class="vaccination vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column">
            @include('layout.header')
            <main class="flex-column p-2">
                <h1>ALL PATIENT CASE</h1>
                <!-- body part -->
                <div class="mb-3 w-100 px-5">
                    <a href="{{route('all.record')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                    <div class="filters d-flex justify-content-between">
                        <div class="mb-3 w-25 ">
                            <small>Show Entries</small>
                            <input type="number" class="form-control bg-light" value="10">
                        </div>
                        <div class="mb-3 w-25">
                            <small>Search</small>
                            <input type="text" class="form-control bg-light" value="Search the record num">
                        </div>
                        <div class="mb-3 w-25">
                            <small>Filter</small>
                            <select name="filter_option" id="" class="form-select bg-light">
                                <option value="" disabled selected>Filter by date</option>
                                <option value="">0-10 weeks</option>
                            </select>
                        </div>
                        <div class="button-con d-flex align-items-center mt-1">
                            <button type="button" class="btn btn-success d-flex  justify-content-center align-items-center gap-2 px-3 py-2" style="height: auto;">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px" viewBox="0 0 512 512">
                                    <path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" fill="white" />
                                </svg>
                                <p class="mb-0" style="font-size: 0.875rem;">Download</p>
                            </button>
                        </div>
                        <div class="print d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="print-icon" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                            </svg>
                        </div>
                    </div>
                    <div class="tables">
                        <table class="w-100 table ">
                            <thead class="table-header">
                                <tr>
                                    <th>Patient No.</th>
                                    <th>Service Type</th>
                                    <th>Type of Record</th>
                                    <th>Date Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <!-- data of patient -->
                            <tbody>
                                <tr>
                                    <td>P-01</td>
                                    <td>Vaccination</td>
                                    <td>Medical Record</td>
                                    <td>Date Registered</td>
                                    <td>
                                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#patient_detail_modal">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                </svg>
                                            </button>
                                            <a href="" class="delete-record-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 448 512">
                                                    <path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z" fill='red' />
                                                </svg>
                                            </a>
                                            <button type="button" class="btn btn-info text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#patient_edit_detail_modal">Edit</button>
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                            </svg>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>P-01</td>
                                    <td>Prenatal</td>
                                    <td>Pregnancy Plan</td>
                                    <td>Date Registered</td>
                                    <td>
                                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#patient_detail_modal">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                </svg>
                                            </button>
                                            <a href="#" class="delete-record-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 448 512">
                                                    <path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z" fill='red' />
                                                </svg>
                                            </a>
                                            <button type="button" class="btn btn-info text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="patient_edit_detail_modal">Edit</button>
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                            </svg>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>
    <!-- modal -->
    <div class="modal fade" id="patient_detail_modal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="simpleModalLabel">VIEW PATIENT MEDICAL RECORD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                </div>
                <div class="moda-body p-5">
                    <div class="">
                        <div class="card-body">

                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Patient Name</th>
                                        <td>Maria L. Montalban</td>
                                    </tr>
                                    <tr>
                                        <th>Administered By</th>
                                        <td>Nurse Joy</td>
                                    </tr>
                                    <tr>
                                        <th>Date of Vaccination</th>
                                        <td>2025-06-25</td> <!-- Replace with dynamic value -->
                                    </tr>
                                    <tr>
                                        <th>Time of Vaccination</th>
                                        <td>09:00 AM</td> <!-- Replace with dynamic value -->
                                    </tr>
                                    <tr>
                                        <th>Vaccine Type</th>
                                        <td>
                                            <ul class="list-unstyled mb-0">
                                                <li>Penta 1</li>
                                                <!-- Add more vaccines here if needed -->
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Vaccine Dose Number</th>
                                        <td>1st Dose</td>
                                    </tr>
                                    <tr>
                                        <th>Remarks</th>
                                        <td>No complications after administration</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save changes</button>
                </div> -->
            </div>
        </div>
    </div>
    <!-- EDIT MODAL -->
    <div class="modal fade" id="patient_edit_detail_modal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="simpleModalLabel">EDIT PATIENT MEDICAL RECORD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                </div>
                <div class="moda-body p-5">
                    <form action="" class="flex-column">
                        <div class="inner w-100 rounded">

                            <div class="mb-2 w-100">
                                <label for="patient_name">Patient Name</label>
                                <input type="text" class="form-control bg-light" disabled placeholder="Maria L. Montalban" value="Maria L. Montalban">
                            </div>

                            <div class="mb-2 w-100">
                                <label for="administered_by">Administered By</label>
                                <input type="text" class="form-control bg-light" disabled placeholder="Nurse" value="Nurse Joy">
                            </div>

                            <div class="mb-2 w-100">
                                <label for="date_of_vaccination">Date of Vaccination</label>
                                <input type="date" id="date_of_vaccination" class="form-control" name="date_of_vaccination" value="2025-06-25">
                            </div>

                            <div class="mb-2 w-100">
                                <label for="time_of_vaccination">Time</label>
                                <input type="time" class="form-control" name="time_of_vaccination" value="09:00">
                            </div>

                            <div class="mb-2">
                                <label for="vaccine_type">Vaccine Type:</label>
                                <div class="d-flex gap-2">
                                    <select name="vaccine_type" id="vaccine_type" class="form-select w-100">
                                        <option value="">Select Vaccine</option>
                                    </select>
                                    <button type="button" class="btn btn-success">Add</button>
                                </div>
                            </div>

                            <div class="mb-2 bg-secondary p-3 d-flex flex-wrap rounded gap-2">
                                <div class="vaccine d-flex justify-content-between bg-white align-items-center p-2 w-25 rounded">
                                    <p class="mb-0">Penta 1</p>
                                    <div class="delete-icon d-flex align-items-center justify-content-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                                            <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                                        </svg>
                                    </div>
                                </div>


                            </div>

                            <div class="mb-2 w-100">
                                <label for="dose">Vaccine Dose Number:</label>
                                <select id="dose" name="dose" required class="form-select">
                                    <option value="" disabled>Select Dose</option>
                                    <option value="1" selected>1st Dose</option>
                                    <option value="2">2nd Dose</option>
                                    <option value="3">3rd Dose</option>
                                </select>
                            </div>

                            <div class="mb-2 w-100">
                                <label for="remarks">Remarks*</label>
                                <input type="text" class="form-control" id="remarks" name="remarks">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('record_all_records');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif

</body>

</html>