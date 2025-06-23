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
                            <a href="{{ route('record.vaccination') }}" class="text-decoration-none fs-5 text-muted">Vaccination</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">Patient Case</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-5">
                        <a href="{{route('record.vaccination')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <div class="tables d-flex flex-column p-3">
                            <div class="add-btn mb-3 align-self-end">
                                <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#vaccinationModal">Add Record</button>
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
                                    <tr class="px-">
                                        <td>C-01</td>
                                        <td>Penta 1, OPV, PCV 1</td>
                                        <td>1st Dose</td>
                                        <td>Nurse Joy</td>
                                        <td>05-22-2025</td>
                                        <td>Done</td>

                                        <td>
                                            <div class="actions d-flex gap-2 justify-content-center align-items-center">

                                                <button type="button" class="btn btn-info text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#editVaccinationModal">Edit</button>
                                                <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3">Archive</button>
                                                <!-- <p class="mb-0">None</p> -->
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>C-02</td>
                                        <td>None</td>
                                        <td>2nd Dose</td>
                                        <td>Nurse Joy</td>
                                        <td>none</td>
                                        <td>pending</td>
                                        <td>
                                            <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                <button class="btn btn-success text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#updateVaccinationModal">Update</button>
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>

                            </table>
                        </div>
                        <!-- ADD FORM modal -->
                        <div class="modal fade" id="vaccinationModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="inner w-100 rounded">

                                                <div class="mb-2 w-100">
                                                    <label for="patient_name">Patient Name</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Jan Louie Salimbago">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="administered_by">Administered By</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Nurse">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="date_of_vaccination">Date of Vaccination</label>
                                                    <input type="date" id="date_of_vaccination" class="form-control" name="date_of_vaccination">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="time_of_vaccination">Time</label>
                                                    <input type="time" class="form-control" name="time_of_vaccination">
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

                                                <div class="mb-2 bg-secondary p-3 d-flex flex-wrap rounded">
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
                                                        <option value="" disabled selected>Select Dose</option>
                                                        <option value="1">1st Dose</option>
                                                        <option value="2">2nd Dose</option>
                                                        <option value="3">3rd Dose</option>
                                                    </select>
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="remarks">Remarks*</label>
                                                    <input type="text" class="form-control" id="remarks" name="remarks">
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
                        <!-- EDIT CASE INFO -->
                        <div class="modal fade" id="editVaccinationModal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="inner w-100 rounded">

                                                <div class="mb-2 w-100">
                                                    <label for="patient_name">Patient Name</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Jan Louie Salimbago">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="administered_by">Administered By</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Nurse">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="date_of_vaccination">Date of Vaccination</label>
                                                    <input type="date" id="date_of_vaccination" class="form-control" name="date_of_vaccination">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="time_of_vaccination">Time</label>
                                                    <input type="time" class="form-control" name="time_of_vaccination">
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
                                                    <div class="vaccine d-flex justify-content-between bg-white align-items-center p-2 w-25 rounded">
                                                        <p class="mb-0">OPV</p>
                                                        <div class="delete-icon d-flex align-items-center justify-content-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                                                                <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="vaccine d-flex justify-content-between bg-white align-items-center p-2 w-25 rounded">
                                                        <p class="mb-0">PCV 1</p>
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
                        <div class="modal fade" id="updateVaccinationModal" tabindex="-1" aria-labelledby="editVaccinationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="#" class="flex-column">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="inner w-100 rounded">

                                                <div class="mb-2 w-100">
                                                    <label for="patient_name">Patient Name</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Jan Louie Salimbago">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="administered_by">Administered By</label>
                                                    <input type="text" class="form-control bg-light" disabled placeholder="Nurse">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="date_of_vaccination">Date of Vaccination</label>
                                                    <input type="date" id="date_of_vaccination" class="form-control" name="date_of_vaccination">
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="time_of_vaccination">Time</label>
                                                    <input type="time" class="form-control" name="time_of_vaccination">
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
                                                    <!-- <div class="vaccine d-flex justify-content-between bg-white align-items-center p-2 w-25 rounded">
                                                        <p class="mb-0">Penta 1</p>
                                                        <div class="delete-icon d-flex align-items-center justify-content-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                                                                <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/>
                                                            </svg>
                                                        </div>
                                                    </div> -->


                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="dose">Vaccine Dose Number:</label>
                                                    <select id="dose" name="dose" required class="form-select">
                                                        <option value="" disabled>Select Dose</option>
                                                        <option value="1">1st Dose</option>
                                                        <option value="2" selected>2nd Dose</option>
                                                        <option value="3">3rd Dose</option>
                                                    </select>
                                                </div>

                                                <div class="mb-2 w-100">
                                                    <label for="remarks">Remarks*</label>
                                                    <input type="text" class="form-control" id="remarks" name="remarks">
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
            const con = document.getElementById('record_vaccination');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>