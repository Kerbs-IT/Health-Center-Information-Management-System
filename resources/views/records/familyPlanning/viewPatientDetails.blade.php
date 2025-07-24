<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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
    'resources/js/patient/add-patient.js',
    'resources/css/patient/record.css'])
    <div class="patient-details vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column">
            @include('layout.header')
            <div class="d-flex flex-grow-1 flex-column overflow-y-auto">
                <main class="flex-column p-2 ">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center">
                        <h2>View Patient Details</h2>
                        <div class="sequence-links d-flex justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-muted">Family Planning</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">View Patient</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-5">
                        <a href="{{route('record.family.planning')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <div class="info bg-white overflow-hidden">
                            <!-- patient Info -->
                            <div class="patient-info">
                                <h2 class="fs-3 fw-normal patient-info-header p-3 text-center">Patient's Information Details</h2>
                            </div>
                            <!-- basic info header -->
                            <h3 class="text-start mb-3 fs-2 w-100">Basic Information</h3>
                            <table class="table table-bordered table-light">
                                <tbody>
                                    <!-- first row -->
                                    <tr>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Name of Client:</h6> <span class="fw-light">Josephine Batumbakal</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Client ID:</h6> <span class="fw-light">1010</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">PHILHEALTH NO:</h6> <span class="fw-light">1011</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 2nd row -->
                                    <tr>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0"> Date of Birth:</h6> <span class="fw-light">01-22-1990</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Sex:</h6> <span class="fw-light">Female</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Age:</h6> <span class="fw-light">28</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 3rd row -->

                                    <tr>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Contact No.:</h6> <span class="fw-light">0932647102</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Religion:</h6> <span class="fw-light">Catholic</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Civil Status:</h6> <span class="fw-light">Married</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 4th -->
                                    <tr>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Occupation:</h6> <span class="fw-light">Call Center Agent</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Blood Type:</h6> <span class="fw-light">O+</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Nationality:</h6> <span class="fw-light">Filipino</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 5th -->
                                </tbody>
                            </table>
                            <!-- spouse -->
                            <h3 class="text-start mb-3 fs-2 w-100">Address</h3>
                            <table class="table table-bordered table-light">
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="p-4 text-start">Blk 25 lot 11, Green Forbes, Hugo Perez, Trece Martires City, Cavite, Philippines</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- vital header  -->
                            <h3 class="text-start mb-3 fs-2 w-100">Vital Sign</h3>
                            <table class="table table-bordered table-light">
                                <tbody>
                                    <tr>
                                        <td colspan="7">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Date:</h6> <span>06-24-2025</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 2nd row -->
                                    <tr>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Height(cm):</h6> <span>172 cm</span>
                                            </div>
                                        </td>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Weight(kg):</h6> <span>75 kg</span>
                                            </div>
                                        </td>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Blood Pressure:</h6> <span class="fw-light">120/80</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 3rd -->
                                    <tr>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Temperature(C):</h6> <span>30°C</span>
                                            </div>
                                        </td>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Respiratory Rate(breaths/min):</h6> <span>25</span>
                                            </div>
                                        </td>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Pulse Rate:</h6> <span class="fw-light">120/80</span>
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