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
    'resources/js/tb_dots/editDetails.js'])
    <div class="patient-details vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-x-auto">
            @include('layout.header')
            <div class="d-flex flex-grow-1 flex-column overflow-auto">
                <main class="flex-column p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center border-bottom mx-md-3 mx-2">
                        <h2>Update Patient Details</h2>
                        <div class="sequence-links d-md-flex d-none justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('record.vaccination') }}" class="text-decoration-none fs-5 text-muted">Tuberculosis</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="" class="text-decoration-none fs-5 text-black">Edit Patient Info</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-lg-5 mx-md-3 mx-2 px-2 shadow-lg">
                        <a href="{{route('record.tb-dots')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <form action="" method="POST" class="d-flex flex-column align-items-center  justify-content-center rounded overflow-hidden bg-white py-2" id="edit-tb-dots-patient-details-form">
                            @csrf
                            <div class="step d-flex flex-column w-100 rounded  px-2">
                                <div class="info">
                                    <h4>Personal Info</h4>
                                    <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <input type="text" id="first_name" placeholder="First Name" class="form-control bg-light" name="first_name" value="{{optional($tbDotsRecord->patient)->first_name??''}}">

                                            <small class="text-danger error-text" id="first_name_error"></small>


                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="{{optional($tbDotsRecord->patient)->middle_initial??''}}">

                                            <small class="text-danger error-text" id="middle_initial_error"></small>


                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="{{optional($tbDotsRecord->patient)->last_name}}">

                                            <small class="text-danger error-text" id="last_name_error"></small>

                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <!-- date of birth -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="birthdate">Date of Birth</label>
                                            <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5" name="date_of_birth" value="{{optional($tbDotsRecord -> patient)->date_of_birth?->format('Y-m-d')??''}}" min="1950-01-01" max="{{date('Y-m-d')}}">

                                            <small class="text-danger error-text" id="date_of_birth_error"></small>

                                        </div>
                                        <!-- place of birth -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="place_of_birth">Place of Birth</label>
                                            <input type="text" id="place_of_birth" placeholder="20" class="form-control" name="place_of_birth" value="{{optional($tbDotsRecord->patient)->place_of_birth??''}}">

                                            <small class="text-danger error-text" id="place_of_birth_error"></small>

                                        </div>

                                        <!-- age -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="age">Age</label>
                                            <input type="text" id="age" placeholder="20" class="form-control" disabled value="{{optional($tbDotsRecord->patient)->age??''}}">
                                            <input type="hidden" id="hiddenAge" name="age">
                                            <small class="text-danger error-text" id="age_error"></small>

                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="sex">Sex</label>
                                            <div class="input-field d-flex align-items-center p-2">

                                                <div class="sex-input d-flex align-items-center justify-content-center w-100 gap-1">
                                                    <input type="radio" id="male" class="mb-0" name="sex" value="Male" class="mb-0" {{ optional($tbDotsRecord->patient)->sex == 'Male' ? 'checked' : '' }}>Male</label>
                                                    <input type="radio" id="female" class="mb-0" name="sex" value="Female" class="mb-0" {{ optional($tbDotsRecord->patient)->sex == 'Female' ? 'checked' : '' }}>Female</label>
                                                </div>

                                                <small class="text-danger error-text" id="sex_error"></small>

                                            </div>
                                        </div>
                                        <!-- contact -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="contact_number" class="">Contact Number</label>
                                            <input type="number" placeholder="+63-936-627-8671" class="form-control" name="contact_number" value="{{optional($tbDotsRecord -> patient)->contact_number??''}}">

                                            <small class="text-danger error-text" id="contact_number_error"></small>

                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="nationality" class="">Nationality</label>
                                            <input type="text" placeholder="ex. Filipino" class="form-control" name="nationality" value="{{optional($tbDotsRecord -> patient)->nationality??''}}">

                                            <small class="text-danger error-text" id="nationality_error"></small>

                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="dateOfRegistration">Date of Registration</label>
                                            <input type="date" id="date_of_registration" placeholder="20" class="form-control text-center w-100 px-5 " name="date_of_registration" min="1950-01-01" max="{{date('Y-m-d')}}" value="{{optional($tbDotsRecord-> patient)->date_of_registration->format('Y-m-d')??''}}">

                                            <small class="text-danger error-text" id="date_of_registration_error"></small>

                                        </div>
                                        <!-- administered by -->
                                        <div class="mb-2 flex-fill xl:w-[50%]">
                                            <label for="brgy">Administered by*</label>
                                            <select name="handled_by" id="handled_by" class="form-select " data-bs-health-worker-id="{{optional($tbDotsRecord-> tb_dots_medical_record)->health_worker_id??''}}">
                                                <option value="">Select a person</option>
                                            </select>

                                            <small class="text-danger error-text" id="handled_by_error"></small>

                                        </div>
                                        <div class="mb-2 flex-fill xl:w-[50%] tb-dots-inputs d-flex flex-column">
                                            <label for="">PhilHealth ID No.</label>
                                            <input type="text" placeholder="ex.1234-5678-9012" name="philheath_id" class="form-control">
                                            <small class="text-danger error-text" id="philheath_id_error"></small>
                                        </div>
                                    </div>

                                    <!-- address -->
                                    <div class="mb-2 d-flex gap-1 flex-column">
                                        <h4>Address</h4>
                                        <div class="input-field d-flex gap-2 align-items-center flex-wrap flex-md-nowrap">
                                            <div class=" mb-2 w-full md:w-[50%]">
                                                <label for="street">Street*</label>
                                                <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control py-2" name="street" value="{{ trim($address->house_number . ' ' . optional($address->street)->name) }}">

                                                <small class="text-danger error-text" id="street_error"></small>

                                            </div>
                                            <div class="mb-2 w-full md:w-[50%]">
                                                <label for="brgy">Barangay*</label>
                                                <select name="brgy" id="brgy" class="form-select py-2" data-bs-selected-brgy="{{$address-> purok}}">
                                                    <option value="">Select a brgy</option>
                                                </select>

                                                <small class="text-danger error-text" id="brgy_error"></small>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- vital sign -->
                                    <div class="vital-sign w-100 border-bottom">
                                        <h5>Vital Sign</h5>
                                        <div class="mb-2 input-field d-flex gap-3 w-100 first-row flex-xl-nowrap flex-wrap">
                                            <div class="mb-2 flex-fill xl:w-[50%]">
                                                <label for="BP">Blood Pressure:</label>
                                                <input type="text" class="form-control w-100" placeholder="Enter the blood pressure" name="blood_pressure" value="{{optional($tbDotsRecord -> tb_dots_medical_record)->blood_pressure??''}}">
                                                <small class="text-danger error-text" id="blood_pressure_error"></small>
                                            </div>
                                            <div class="mb-2 flex-fill xl:w-[50%]">
                                                <label for="BP">Temperature:</label>
                                                <input type="text" class="form-control w-100" placeholder="Enter the temperature" name="temperature" value="{{optional($tbDotsRecord -> tb_dots_medical_record)->temperature??''}}">
                                                <small class="text-danger error-text" id="temperature_error"></small>
                                            </div>
                                            <div class="mb-2 flex-fill xl:w-[50%]">
                                                <label for="BP">Pulse Rate(Bpm):</label>
                                                <input type="text" class="form-control w-100" placeholder="Enter the pulse rate" name="pulse_rate" value="{{optional($tbDotsRecord -> tb_dots_medical_record)->pulse_rate??''}}">
                                                <small class="text-danger error-text" id="pulse_rate_error"></small>
                                            </div>

                                        </div>
                                        <!-- 2nd row -->
                                        <div class="mb-2 input-field d-flex gap-3 w-100 second-row flex-xl-nowrap flex-wrap">
                                            <div class="mb-2 flex-fill xl:w-[50%]">
                                                <label for="BP">Respiratory Rate (breaths/min):</label>
                                                <input type="text" class="form-control w-100" placeholder="Enter the respiratory rate" name="respiratory_rate" value="{{optional($tbDotsRecord -> tb_dots_medical_record)->respiratory_rate??''}}">
                                                <small class="text-danger error-text" id="respiratory_rate_error"></small>
                                            </div>
                                            <div class="mb-2 flex-fill xl:w-[50%]">
                                                <label for="BP">Height(cm):</label>
                                                <input type="text" class="form-control w-100" placeholder="Enter the height" name="height" value="{{optional($tbDotsRecord -> tb_dots_medical_record)->height??''}}">
                                                <small class="text-danger error-text" id="height_error"></small>
                                            </div>
                                            <div class="mb-2 flex-fill xl:w-[50%]">
                                                <label for="BP">Weight(kg):</label>
                                                <input type="text" class="form-control w-100" placeholder="Enter the  weight" name="weight" value="{{optional($tbDotsRecord -> tb_dots_medical_record)->weight??''}}">
                                                <small class="text-danger error-text" id="weight_error"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- save btn -->
                            <div class="save-record align-self-end mt-5">
                                <input type="submit" class="btn btn-success px-4 fs-5" value="Save Record" id="edit-save-btn" data-bs-medical-id="{{$tbDotsRecord-> id}}">
                            </div>
                    </div>
                    </form>
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