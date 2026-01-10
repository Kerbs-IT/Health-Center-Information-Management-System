<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/css/nurse_dashboard.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/manageInterface.css',
    'resources/css/patient/patient-dashboard.css',
    'resources/css/patient/record.css',
    'resources/js/manageUser/userProfile.js',
    'resources/css/profile.css'])

    <div class="ms-0 ps-0 d-flex w-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-flex w-100">
            <aside>
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
            <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class="flex-grow-1 ">
                @include('layout.header')
                <main class=" mt-md-4 mt-2 d-flex align-items-center flex-column justify-content-center flex-grow-1 py-2 px-lg-5 px-md-3 px-2 overflow-x-auto">
                    <div class="top-content d-flex w-100  shadow mb-5 rounded b patient-profile flex-lg-row flex-column">
                        <!-- Left panel -->
                        <div class="edit-profile p-3 d-flex w-[100%] lg:w-[25%] flex-column align-items-center border-lg-end border-bottom">
                            <img src="{{
    asset(
        optional(Auth::user())->profile_image ??
        optional(Auth::user()->patient)->profile_image
        ?? 'images/default_profile.png'
    )
}}"
                                alt="profile_img" class="mb-3 profile-section-image" style="width: 100px; height: 100px; object-fit: cover;">
                            <h4 class="mb-3">{{ optional(Auth::user())->username ?? 'none' }}</h4>
                            <h5 class="fw-light">{{ Auth::user()->email ?? 'none' }}</h5>
                            <button type="button" class="btn btn-success mt-2" id="patient_profile_edit" data-bs-toggle="modal" data-bs-target="#profile_modal" data-id="{{Auth::user()->id}}">Edit Profile</button>
                        </div>

                        <!-- Right panel -->
                        <div class="personal-info p-4 flex-grow-1">
                            <!-- Personal Information Section -->
                            <div class="info mb-4">
                                <div class="d-flex w-100 justify-content-between mb-3 flex-wrap">
                                    <h4 class="mb-0 pb-2 border-bottom fw-bold no-wrap flex-1">Personal Information</h4>
                                    <div class="change-pass-button w-100 d-flex justify-content-end px-4 flex-1 mt-md-0 mt-1">
                                        <div>
                                          <a href="{{ route('change-pass') }}" class="btn btn-success text-nowrap">Change Password</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row px-md-3 px-1">
                                    <!-- Left Column -->
                                    <div class="col-sm-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Name:</label>
                                            <p class="mb-0 fw-medium">{{ optional(Auth::user()->patient)->full_name ?? trim((Auth::user()->first_name ?? '') . ' ' . (Auth::user()->middle_initial ?? '') . ' ' . (Auth::user()->last_name ?? '')) ?: 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Sex</label>
                                            <p class="mb-0 fw-medium">{{ optional(Auth::user()->patient)->sex ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Age</label>
                                            <p class="mb-0 fw-medium">{{ optional(Auth::user()->patient)->age ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Contact Number</label>
                                            <p class="mb-0 fw-medium">{{ optional(Auth::user()->patient)->contact_number ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Register Date</label>
                                            <p class="mb-0 fw-medium">{{ optional(Auth::user())->created_at->format('m/d/Y') ?? 'Not specified' }}</p>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="col-sm-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Nationality</label>
                                            <p class="mb-0 fw-medium">{{ optional(Auth::user()->patient)->nationality ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Date of Birth</label>
                                            <p class="mb-0 fw-medium">{{ optional(Auth::user()->patient)->date_of_birth ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Place of Birth</label>
                                            <p class="mb-0 fw-medium">{{ optional(Auth::user()->patient)->place_of_birth ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Civil Status</label>
                                            <p class="mb-0 fw-medium">{{ optional(Auth::user()->patient)->civil_status ?? 'Not specified' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Section -->
                            <div class="address mb-3">
                                <h4 class="mb-3 pb-2 border-bottom fw-bold">Address</h4>
                                <div class="px-3">
                                    <p class="mb-0 fw-normal">{{ $fullAddress }}</p>
                                </div>
                            </div>
                            <div class="other-info">
                                <h4 class="mb-3 pb-2 border-bottom fw-bold">{{ucwords($typeOfPatient)}} Medical Record Information</h4>
                                @if(($typeOfPatient ?? null) === 'vaccination')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Mother Name</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->mother_name ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Birth Weight</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->birth_weight?? 'Not specified' }}</p>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Father Name</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->father_name?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Birth Height</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->birth_height ?? 'Not specified' }}</p>
                                        </div>
                                    </div>
                                </div>
                                @elseif(($typeOfPatient ?? null) === 'prenatal')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Head of the Family</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->family_head_name ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Blood Type</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->blood_type?? 'Not specified' }}</p>
                                        </div>


                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Religion</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->religion?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Philhealth No.</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->philHealth_number ?? 'Not specified' }}</p>
                                        </div>

                                    </div>
                                </div>
                                @elseif(($typeOfPatient ?? null) === 'tb-dots')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Philhealth ID No.</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->philhealth_id_no ?? 'Not specified' }}</p>
                                        </div>

                                    </div>

                                </div>
                                @elseif(($typeOfPatient ?? null) === 'senior-citizen')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Occupation</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->occupation ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Religion</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->religion?? 'Not specified' }}</p>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Member of Social Security System (SSS)</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->SSS?? 'Not specified' }}</p>
                                        </div>

                                    </div>
                                </div>
                                @elseif(($typeOfPatient ?? null) === 'family-planning')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Religion</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->religion ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Occupation</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->occupation ?? 'Not specified' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item mb-3">
                                            <label class="text-muted small mb-1">Philhealth No.</label>
                                            <p class="mb-0 fw-medium">{{ optional($medicalRecord)->philhealth_no?? 'Not specified' }}</p>
                                        </div>
                                    </div>
                                </div>
                                @else
                                @endif
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <div class="modal fade" id="profile_modal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="">
        <div class="modal-dialog modal-xl">
            <form action="" method="post" class="w-100 " enctype="multipart/form-data" id="profile-form">
                <div class="modal-content">

                    @method('PUT')
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="simpleModalLabel">Edit User Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                    </div>
                    <div class="modal-body">
                        <div class="pop-up  w-100 h-100 d-flex align-items-start justify-content-center px-3 gap-3 mt-2 flex-lg-nowrap flex-wrap" id="pop-up">
                            <!-- type of patient if available -->
                            <input type="hidden" name="typeOfPatient" value="{{$typeOfPatient??null}}">

                            <!-- profile image section -->
                            <div class="profile-image p-1  mb-3 d-flex flex-column align-items-center h-100" style="min-width:280px;">
                                <img src="" alt="profile picture" class="profile-section-image" id="profile-image" data-base-url="{{ asset('') }}">
                                <h3 class=""></h3>
                                <h5 class="mb-3 text-muted text-capitalize fw-normal" id="full_name"></h5>
                                <div class="upload-image d-flex flex-column">
                                    <label for="fileInput" class="btn mb-2 btn-success justify-self-center ">Update Profile</label>
                                    <input type="file" name="profile_image" class="d-none w-100" id="fileInput" onchange="showFileName(this)">
                                    <span id="fileName" class="text-center text-muted">No file choosen</span>
                                    <small class="text-danger" id="image-error"></small>
                                </div>
                            </div>
                            <!-- USER INFORMATION -->
                            <div class="user-info flex-grow-1 ">
                                <h4 class="fw-bold">Personal Info</h4>
                                <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                    <div class="input-field flex-fill xl: w-[50%]">
                                        <input type="text" id="first_name" placeholder="First Name" class="form-control" name="first_name" value="">
                                        <small class="text-danger" id="fname-error"></small>
                                    </div>
                                    <div class="input-field flex-fill xl: w-[50%]">
                                        <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="">
                                        <small class="text-danger" id="middle-initial-error"></small>
                                    </div>
                                    <div class="input-field flex-fill xl: w-[50%]">
                                        <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="">
                                        <small class="text-danger" id="lname-error"></small>
                                    </div>
                                </div>
                                <!-- age -->
                                <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap ">
                                    @if($typeOfPatient)
                                    <div class="input-field flex-1 flex-fill xl:w-[50%]">
                                        <label for="age">Age</label>
                                        <input type="text" id="age" placeholder="20" class="form-control" name="age" value="">
                                        <small class="text-danger" id="age-error"></small>
                                    </div>
                                    @endif
                                    <div class="input-field flex-1 flex-fill xl:w-[50%]">
                                        <label for="birthdate">Date of Birth</label>
                                        <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5" name="date_of_birth" value="">
                                        <small class="text-danger" id="birthdate-error"></small>
                                    </div>
                                    @if($typeOfPatient)
                                    <div class="input-field flex-1 flex-fill xl:w-[50%]">
                                        <label for="sex">Sex</label>
                                        <div class="input-field d-flex align-items-center p-2">
                                            @php
                                            $selectedSex = optional(Auth::user() -> staff) -> sex ?? optional(Auth::user() -> nurses) -> sex ?? 'none';
                                            @endphp
                                            <div class="sex-input d-flex align-items-center gap-1">
                                                <input type="radio" id="male" class="mb-0" name="sex" value="male" {{ $selectedSex === 'male'? 'checked' : '' }}>
                                                <label for="male" class="mb-0">Male</label>
                                                <input type="radio" id="female" class="mb-0" name="sex" value="female" {{ $selectedSex === 'female'? 'checked' : '' }}>
                                                <label for="female" class="mb-0">Female</label>
                                            </div>
                                            <small class="text-danger" id="sex-error"></small>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <!-- civil status, contact number, nationality -->
                                <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                    @if($typeOfPatient)
                                    <div class="input-field flex-1 flex-fill xl:w-[50%]">
                                        <label for="civil_status" class="">Civil Status</label>
                                        <!-- to display the current status -->

                                        <select name="civil_status" id="civil_status" class="form-select">
                                            <option value="single">Single</option>
                                            <option value="married">Married</option>
                                            <option value="divorce">Divorce</option>
                                        </select>
                                        <small class="text-danger" id="civil-status-error"></small>
                                    </div>
                                    @endif
                                    <!-- contact -->
                                    <div class="input-field flex-1 flex-fill xl:w-[50%]">
                                        <label for="contact_number" class="">Contact Number</label>
                                        <input type="number" placeholder="+63-936-627-8671" class="form-control" id="contact_num" name="contact_number" value="">
                                        <small class="text-danger" id="contact-error"></small>
                                    </div>
                                    @if($typeOfPatient)
                                    <div class="input-field flex-1 flex-fill xl:w-[50%]">
                                        <label for="nationality" class="">Nationality</label>
                                        <input type="text" placeholder="ex. Filipino" class="form-control" id="nationality" name="nationality" value="">
                                        <small class="text-danger" id="nationality-error"></small>
                                    </div>
                                    @endif
                                </div>
                                <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                    <!-- username -->
                                    <div class="input-field flex-fill xl: w-[50%]">
                                        <label for="username" class="">Username</label>
                                        <input type="text" placeholder="ex. yato" id="username" class="form-control" name="username" value="">
                                        <small class="text-danger" id="username-error"></small>
                                    </div>
                                    <!-- email -->
                                    <div class="input-field flex-fill xl: w-[50%]">
                                        <label for="email" class="">Email</label>
                                        <input type="email" placeholder="ex. yato" id="email" class="form-control" name="email" value="">
                                        <small class="text-danger" id="email-error"></small>
                                    </div>
                                    <!-- password -->
                                    <div class="input-field flex-fill xl: w-[50%]">
                                        <label for="password" class="">Password</label>
                                        <input type="password" id="edit_password" class="form-control" name="password">
                                        <small class="text-muted">Leave blank if you don't want to change it.</small>
                                        <small class="text-danger"></small>
                                    </div>
                                </div>
                                <!-- address -->
                                <div class="mb-3 w-100" id="patient_type_con">
                                    <label for="patient_type" class="form-label text-nowrap ">Patient Address </label>

                                    <div class=" w-100 d-flex gap-2 flex-xl-row flex-column flex-xl-nowrap flex-wrap">
                                        <div class="items w-[100%] xl:[50%]">
                                            <label for="patient_street" class="w-100 text-muted">Blk & lot,Street*</label>
                                            <input type="text" id="update_blk_n_street" name="blk_n_street" placeholder="enter the blk & lot & street seperated by ','" class="w-100 form-control">
                                            @error('blk_n_street')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <div class="items w-[100%] xl:[50%]">
                                            <label for="patient_purok_dropdown">Puroks*</label>
                                            <select id="update_patient_purok_dropdown" class="form-select w-100" name="patient_purok_dropdown" required>
                                                <option value="" selected disabled>Select a purok</option>
                                            </select>
                                            @error('patient_purok_dropdown')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                                <!-- ADDITIONAL INFORMATION -->
                                <div class="mb-3 w-100">
                                    @if($typeOfPatient )
                                    <h4 class="fw-bold">{{ucwords($typeOfPatient)}} Medical Record Information</h4>
                                    @endif
                                    @if(($typeOfPatient ?? null) === 'vaccination')
                                    <div class="d-flex gap-2">
                                        <div class="input-field flex-1">
                                            <label for="motherName">Mother Name</label>
                                            <input type="text" id="mother_name" placeholder="mother name" class="form-control" name="mother_name" value="">
                                            <!-- ERROR HANDLING -->
                                            <small class="text-danger error-text" id="mother_name_error"></small>
                                        </div>
                                        <div class="input-field flex-1">
                                            <label for="fatherName">Father Name</label>
                                            <input type="text" id="father_name" placeholder="Father Name" class="form-control" name="father_name" value="">
                                            <small class="text-danger error-text" id="father_name_error"></small>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <div class="mb-2 flex-1">
                                            <label for="BP">Birth Height(cm):</label>
                                            <input type="number" class="form-control w-100" id="vaccination_height" placeholder="00.00" name="vaccination_height">
                                            <small class="text-danger error-text" id="vaccination_height_error"></small>
                                        </div>
                                        <div class="mb-2 flex-1">
                                            <label for="BP">Birth Weight(kg):</label>
                                            <input type="text" class="form-control w-100" id="vaccination_weight" placeholder=" 00.00" name="vaccination_weight">
                                            <small class="text-danger error-text" id="vaccination_weight_error"></small>
                                        </div>
                                    </div>
                                    @elseif(($typeOfPatient ?? null) === 'prenatal')
                                    <div class="mb-2 d-flex gap-2 flex-wrap flex-xl-nowrap">
                                        <div class="input-field flex-1 flex-fill flex-xl:w-[50%]">
                                            <label for="motherName">Head of the Family</label>
                                            <input type="text" id="head_of_the_family" placeholder="Enter the Name" class="form-control" name="family_head_name" value="">
                                            <small class="text-danger error-text" id="family_head_name_error"></small>

                                        </div>
                                        <div class="input-field flex-1 flex-fill flex-xl:w-[50%]">
                                            <label for="blood_type">Blood Type</label>
                                            <select name="blood_type" id="blood_type" class="form-select" required>
                                                <option value="" disabled selected>Select Blood Type</option>
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="AB+">AB+</option>
                                                <option value="AB-">AB-</option>
                                                <option value="O+">O+</option>
                                                <option value="O-">O-</option>
                                            </select>
                                            <small class="text-danger error-text" id="blood_type_error"></small>
                                        </div>
                                        <div class="input-field flex-1 flex-fill flex-xl:w-[50%]">
                                            <label for="religion" class="form-label">Religion</label>
                                            <input type="text" id="religion" placeholder="Enter the Religion" class="form-control" name="religion" value="">
                                            <!-- ERROR HANDLING -->
                                            <small class="text-danger error-text" id="religion_error"></small>

                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-2">
                                        <div class="input-field w-50">
                                            <label class="form-label">PhilHealth</label>
                                            <div class="d-flex align-items-center  gap-2">
                                                <div class="form-check d-flex align-items-center">
                                                    <input class="" type="radio" name="philhealth_number_radio" id="philhealth_yes" value="yes">
                                                    <label class="form-check-label" for="philhealth_yes">(Yes)</label>
                                                </div>
                                                <div class="d-flex align-items-center gap-1">
                                                    <label class="form-label mb-0">Number:</label>
                                                    <input type="text" class="form-control form-control-sm w-100" name="philHealth_number" id="philhealth_number">
                                                </div>
                                                <div class="form-check d-flex align-items-center">
                                                    <input class="" type="radio" name="philhealth_number_radio" id="philhealth_no" value="no">
                                                    <label class="form-check-label" for="philhealth_no">(No)</label>
                                                </div>
                                            </div>
                                            <small class="text-danger error-text" id="philHealth_number_error"></small>
                                        </div>



                                    </div>
                                    @elseif(($typeOfPatient ?? null) === 'tb-dots')
                                    <div class="mb-2 flex-1 tb-dots-inputs d-flex flex-column">
                                        <label for="">PhilHealth ID No.</label>
                                        <input type="text" placeholder="ex.1234-5678-9012" name="philhealth_id" class="form-control" id="philheath_id">
                                        <small class="text-danger error-text" id="philhealth_id_no_error"></small>
                                    </div>
                                    @elseif(($typeOfPatient ?? null) === 'senior-citizen')
                                    <div class="mb-2 d-flex gap-2">
                                        <div class="input-field flex-1">
                                            <label for="blood_type">Occupation</label>
                                            <input type="text" id="occupation" placeholder="Enter the Occupation" class="form-control" name="occupation">
                                            <small class="text-danger error-text" id="occupation_error"></small>
                                        </div>
                                        <div class="mb-3 flex-1 d-flex gap-2">
                                            <div class="input-field w-100">
                                                <label for="senior_religion">Religion</label>
                                                <input type="text" id="religion" placeholder="Enter the Religion" class="form-control" name="religion">
                                                <small class=" text-danger" id="religion_error"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="mb-2 flex-1  d-flex  justify-content-center">
                                            <label for="" class="flex-1"> Member of Social Security System (SSS):</label>
                                            <div class="radio-input d-flex align-items-center justify-content-center flex-1 gap-1 py-2">
                                                <input type="radio" id="sss_yes" class="mb-0" name="SSS" value="Yes" class="mb-0">
                                                <label for="sss_yes">Yes</label>
                                                <input type="radio" id="sss_no" class="mb-0" name="SSS" value="No" class="mb-0">
                                                <label for="sss_no">No</label>
                                            </div>
                                            <small class="text-danger error-text" id="SSS_error"></small>
                                        </div>
                                    </div>
                                    @elseif(($typeOfPatient ?? null) === 'family-planning')
                                    <div class="mb-2 d-flex gap-2">
                                        <div class="input-field flex-1">
                                            <label for="blood_type">Occupation</label>
                                            <input type="text" id="occupation" placeholder="Enter the Occupation" class="form-control" name="occupation">
                                            <small class="text-danger error-text" id="occupation_error"></small>
                                        </div>
                                        <div class="mb-3 flex-1 d-flex gap-2">
                                            <div class="input-field w-100">
                                                <label for="senior_religion">Religion</label>
                                                <input type="text" id="religion" placeholder="Enter the Religion" class="form-control" name="religion">
                                                <small class=" text-danger" id="religion_error"></small>
                                            </div>
                                        </div>
                                        <div class="input-field flex-1">
                                            <label for="philhealth_no">Philhealth No:</label>
                                            <input type="text" id="philhealth_no" placeholder="Enter the philhealth no." class="form-control" name="philhealth_no">
                                            <small class="text-danger error-text" id="philhealth_no_error"></small>
                                        </div>
                                    </div>
                                    @endif

                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Cancel</button>
                        <input type="submit" value="Save" class="btn btn-success px-4" id="submit-btn" data-user>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('patient_dashboard');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>