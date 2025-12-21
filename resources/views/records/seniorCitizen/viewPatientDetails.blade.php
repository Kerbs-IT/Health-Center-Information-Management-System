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
    <div class="patient-details min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-x-auto">
            @include('layout.header')
            <div class="d-flex flex-grow-1 flex-column">
                <main class="flex-column p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center">
                        <h2>View Patient Details</h2>
                        <div class="sequence-links d-md-flex d-none justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-muted">Senior Citizen</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">View Patient</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-lg-4 px-md-3 px-2 shadow-lg mx-md-3 mx-2">
                        <a href="{{route('record.senior.citizen')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <!-- patient Info -->
                        <div class="info bg-white rounded">
                            <div class="patient-info">
                                <h2 class="fs-3 fw-normal patient-info-header p-3 text-center">Patient's Information Details</h2>
                            </div>
                            <!-- basic info header -->
                            <h3 class="text-start mb-3 fs-2 w-100">Basic Information</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered table-light">
                                    <tbody>
                                        <!-- first row -->
                                        <tr>
                                            <div></div>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Full Name:</h6> <span>{{optional($seniorCitizenRecord -> patient)->full_name??'N/A'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Age:</h6> <span>{{optional($seniorCitizenRecord -> patient)->age??'N/A'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Date of Birth</h6> <span>{{optional($seniorCitizenRecord -> patient)->date_of_birth->format('M d Y')??'N/A'}}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- 2nd row -->
                                        <tr>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Place of Birth:</h6> <span>{{optional($seniorCitizenRecord -> patient)->place_of_birth??'N/A'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Sex:</h6> <span>{{optional($seniorCitizenRecord -> patient)->sex??'N/A'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Nationality</h6> <span>{{optional($seniorCitizenRecord -> patient)->nationality??'N/A'}}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- 3rd row -->
                                        <tr>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Civil Status:</h6> <span>{{optional($seniorCitizenRecord -> patient)->civil_status??'N/A'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Occupation:</h6> <span>{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->occupation??'N/A'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">SSS member:</h6> <span>{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->SSS??'N/A'}}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h3 class="text-start mb-3 fs-2 w-100">Address</h3>
                            <table class="table table-bordered table-light">
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="p-4 text-start">{{$fullAddress}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- vital header  -->
                            <h3 class="text-start mb-3 fs-2 w-100">Vital Sign</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered table-light">
                                    <tbody>
                                        <tr>
                                            <td colspan="7">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Date:</h6> <span>{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->created_at->format('M d Y')??'N/A'}}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- 2nd row -->
                                        <tr>
                                            <td class="w-25 ">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Height(cm):</h6> <span>{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->height??'N/A'}} cm</span>
                                                </div>
                                            </td>
                                            <td class="w-25 ">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Weight(kg):</h6> <span>{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->weight??'N/A'}} kg</span>
                                                </div>
                                            </td>
                                            <td class="w-25 ">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Blood Pressure:</h6> <span class="fw-light">{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->blood_pressure??'N/A'}}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- 3rd -->
                                        <tr>
                                            <td class="w-25 ">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Temperature(C):</h6> <span>{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->temperature??'N/A'}}°C</span>
                                                </div>
                                            </td>
                                            <td class="w-25 ">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Respiratory Rate(breaths/min):</h6> <span>{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->respiratory_rate??'N/A'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25 ">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Pulse Rate:</h6> <span class="fw-light">{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->pulse_rate??'N/A'}}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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