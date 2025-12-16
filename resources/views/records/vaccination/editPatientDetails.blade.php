<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- important for the js and server communication -->
    <!-- to avoid the invalid response -->
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
    'resources/js/patient/editPatientDetails.js'])
    @include('sweetalert::alert')
    <div class="patient-details vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column">
            @include('layout.header')
            <div class="d-flex flex-grow-1 flex-column overflow-y-auto">
                <main class="flex-column p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center border-bottom">
                        <h2>Update Patient Details</h2>
                        <div class="sequence-links d-flex justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('record.vaccination') }}" class="text-decoration-none fs-5 text-muted">Vaccination</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">Edit Patient Info</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-5 bg-white mx-3 mt-2 rounded">
                        <a href="{{route('record.vaccination')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <form action="" method="post" class="d-flex flex-column align-items-center  justify-content-center rounded overflow-hidden" id="update-form">
                            @method('PUT')
                            @csrf
                            <div class="step d-flex flex-column w-100 rounded  px-2">
                                <div class="info">
                                    <h4>Personal Info</h4>
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <input type="text" id="first_name" placeholder="First Name" class="form-control" name="first_name" value="{{$info->first_name}}">
                                            <small class="text-danger error-text" id="first_name_error"></small>
                                        </div>
                                        <div class="input-field w-50">
                                            <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="{{$info->middle_initial}}">
                                            <small class="text-danger error-text" id="middle_initial_error"></small>
                                        </div>
                                        <div class="input-field w-50">
                                            <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="{{$info->last_name}}">
                                            <small class="text-danger error-text" id="last_name_error"></small>

                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1">
                                        <!-- date of birth -->
                                        <div class="input-field w-50">
                                            <label for="birthdate">Date of Birth</label>
                                            <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5" name="date_of_birth" value="{{optional($info)->date_of_birth?? ''}}">
                                            <small class="text-danger error-text" id="date_of_birth_error"></small>
                                        </div>
                                        <!-- place of birth -->
                                        <div class="input-field w-50">
                                            <label for="place_of_birth">Place of Birth</label>
                                            <input type="text" id="place_of_birth" placeholder="20" class="form-control" name="place_of_birth" value="{{optional($info)-> place_of_birth ?? 'none'}}">
                                            <small class="text-danger error-text" id="place_of_birth_error"></small>
                                        </div>

                                        <!-- age -->
                                        <div class="input-field w-50">
                                            <label for="age">Age</label>
                                            <input type="text" id="age" placeholder="20" class="form-control" name="age" value="{{optional($info)-> age ?? 'none'}}">

                                            <small class="text-danger error-text" id="age_error"></small>
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <label for="sex">Sex</label>
                                            <div class="input-field d-flex align-items-center p-2">
                                                <div class="sex-input d-flex align-items-center justify-content-center w-100 gap-1">
                                                    <input type="radio" id="male" class="mb-0" name="sex" value="Male" class="mb-0" {{optional($info)-> sex == 'Male'?'checked': ''}}>Male</label>
                                                    <input type="radio" id="female" class="mb-0" name="sex" value="Female" class="mb-0" {{optional($info)-> sex == 'Female'?'checked': ''}}>Female</label>
                                                </div>

                                                <small class="text-danger error-text" id="sex_error"></small>

                                            </div>
                                        </div>
                                        <!-- contact -->
                                        <div class="input-field w-50">
                                            <label for="contact_number" class="">Contact Number</label>
                                            <input type="number" placeholder="+63-936-627-8671" class="form-control" name="contact_number" value="{{optional($info)-> contact_number ?? 'none'}}">

                                            <small class="text-danger error-text" id="contact_number_error"></small>

                                        </div>
                                        <div class="input-field w-50">
                                            <label for="nationality" class="">Nationality</label>
                                            <input type="text" placeholder="ex. Filipino" class="form-control" name="nationality" value="{{optional($info)-> nationality ?? 'none'}}">

                                            <small class="text-danger error-text" id="nationality_error"></small>

                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <label for="dateOfRegistration">Date of Registration</label>
                                            <input type="date" id="dateOfRegistration" placeholder="20" class="form-control text-center w-100 px-5 " name="date_of_registration" value="{{optional($info)-> created_at?->format('Y-m-d') ?? ''}}">

                                            <small class="text-danger error-text" id="date_of_registration_error"></small>

                                        </div>
                                        <!-- administered by -->
                                        <div class="mb-2 w-50">
                                            <label for="healthWorkersDropDown">Handled by*</label>
                                            <select name="handled_by" id="healthWorkersDropDown" class="form-select" data-bs-selected-Health-Worker="{{optional($info->medical_record_case[0]->vaccination_medical_record)->health_worker_id ?? 'N/A'}}">
                                                <option value="" selected disabled>Select a person</option>
                                            </select>

                                            <small class="text-danger error-text" id="handled_by_error"></small>

                                        </div>
                                    </div>
                                    <!-- mother name and father -->
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <label for="motherName">Mother Name</label>
                                            <input type="text" id="mother_name" placeholder="mother name" class="form-control" name="mother_name" value="{{optional($info->medical_record_case[0]->vaccination_medical_record)->mother_name ?? 'N/A'}}">

                                            <small class="text-danger error-text" id="mother_name_error"></small>


                                        </div>
                                        <div class="input-field w-50">
                                            <label for="fatherName">Father Name</label>
                                            <input type="text" id="fatherName" placeholder="Father Name" class="form-control" name="father_name" value="{{optional($info->medical_record_case[0]->vaccination_medical_record)->father_name ?? 'N/A'}}">

                                            <small class="text-danger error-text" id="father_name_error"></small>

                                        </div>
                                    </div>
                                    <!-- address -->
                                    <div class="mb-2 d-flex gap-1 flex-column">
                                        <h4>Address</h4>
                                        <div class="input-field d-flex gap-2 align-items-center">
                                            <div class=" mb-2 w-50">
                                                <label for="street">Street*</label>
                                                <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control py-2" name="street" value="{{$street??'none'}}">

                                                <small class="text-danger error-text" id="street_error"></small>

                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="brgy">Barangay*</label>
                                                <select name="brgy" id="brgy" class="form-select py-2" data-bs-purok="{{optional($address)->purok??'none'}}">
                                                    <option value="" selected disabled>Select a brgy</option>
                                                </select>

                                                <small class="text-danger error-text" id="brgy_error"></small>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- vital sign -->
                                    <div class="vital-sign w-100">
                                        <div class="mb-2 input-field d-flex gap-3 w-100 third-row">
                                            <div class="mb-2 w-50">
                                                <label for="BP">Birth Height(cm):</label>
                                                <input type="number" class="form-control w-100" placeholder="00.00" name="vaccination_height" value="{{optional($info->medical_record_case[0]->vaccination_medical_record)->birth_height ?? 'N/A'}}">
                                                <small class="text-danger error-text" id="vaccination_height_error"></small>
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Birth Weight(kg):</label>
                                                <input type="text" class="form-control w-100" placeholder=" 00.00" name="vaccination_weight" value="{{optional($info->medical_record_case[0]->vaccination_medical_record)->birth_weight ?? 'N/A'}}">
                                                <small class="text-danger error-text" id="vaccination_weight_error"></small>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- save btn -->
                                <div class="save-record align-self-end mt-5">
                                    <input type="submit" class="btn btn-success px-4 fs-5" value="Save Record" id="update-record-btn" data-bs-patient-id="{{$info->id}}">
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
            const con = document.getElementById('record_vaccination');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>