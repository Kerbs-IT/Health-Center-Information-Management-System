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
            <div class="d-flex flex-grow-1 flex-column shadow-lg m-3">
                <main class="flex-column p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center">
                        <h2>View Patient Details</h2>
                        <div class="equence-links d-md-flex d-none justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-muted">Vaccination</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">View Patient</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-2 px-md-3 px-lg-5 ">
                        <a href="{{route('record.vaccination')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <div class="info bg-white rounded overflow-hidden">
                            <!-- patient Info -->
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
                                            <td class="w-25 ">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Full Name:</h6> <span>{{optional($info)?->full_name??'none'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25 ">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Age:</h6> <span>{{optional($info)-> age ?? 'none'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25 ">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Date of Birth: </h6> <span>{{optional($info->date_of_birth ? Carbon\Carbon::parse($info->date_of_birth) : null)?->format('M j, Y') ?? 'none' }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- 2nd row -->
                                        <tr>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Place of Birth:</h6> <span>{{optional($info)->place_of_birth ?? 'none'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Sex:</h6> <span>{{optional($info)->sex ?? 'none'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25" rowspan="2">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Nationality</h6> <span>{{optional($info)->nationality ?? 'none'}}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- 3rd row -->
                                        <tr>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Mother Name:</h6> <span>{{optional($info->medical_record_case[0]->vaccination_medical_record)->mother_name ?? 'N/A'}}</span>
                                                </div>
                                            </td>
                                            <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                    <h6 class="mb-0">Father's Name:</h6> <span>{{optional($info->medical_record_case[0]->vaccination_medical_record)->father_name ?? 'N/A'}}</span>
                                                </div>
                                            </td>
                                            <!-- <td class="w-25">
                                                <div class="info d-flex gap-2 align-items-center">
                                                    <h6 class="mb-0">Nationality</h6> <span>Filipino</span>
                                                </div>
                                            </td> -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h3 class="text-start mb-3 fs-2 w-100">Address</h3>
                            <table class="table table-bordered table-light">
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="p-4 text-start">{{$fullAddress?? 'none'}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- vital header  -->
                            <h3 class="text-start mb-3 fs-2 w-100">Vital Sign</h3>
                            <table class="table table-bordered table-light">
                                <tbody>
                                    <tr>
                                        <td colspan="7">
                                            <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                <h6 class="mb-0">Date:</h6> <span>{{optional($info->medical_record_case[0]->vaccination_medical_record)->created_at?->format('M j, Y') ?? 'N/A'}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 2nd row -->
                                    <tr>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                <h6 class="mb-0">Birth Height(cm):</h6> <span>{{optional($info->medical_record_case[0]->vaccination_medical_record)->birth_height ?? 'N/A'}} cm</span>
                                            </div>
                                        </td>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center flex-wrap">
                                                <h6 class="mb-0">Birth Weight(kg):</h6> <span>{{optional($info->medical_record_case[0]->vaccination_medical_record)->birth_height ?? 'N/A'}} kg</span>
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
            const con = document.getElementById('record_vaccination');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>