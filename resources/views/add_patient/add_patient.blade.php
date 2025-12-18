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
    'resources/js/app.js',
    'resources/js/header.js',
    'resources/js/menudropdown.js',
    'resources/css/profile.css',
    'resources/js/patient/add-patient.js',
    'resources/css/patient/add-patient.css',
    'resources/css/patient/record.css',
    'resources/js/prenatal/addPrenatalPatient.js',
    'resources/js/senior_citizen/addPatient.js',
    'resources/js/tb_dots/add_patient.js',
    'resources/js/family_planning/add_patient.js'])
    @include('sweetalert::alert')
    <div class="add-patient d-flex vh-100">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="flex-grow-1 d-flex flex-column" style="min-height: 0;">
            @include('layout.header')
            <main class=" flex-grow-1 py-2 px-md-4 px-2 basic-info" style="overflow-y: auto; min-height: 0;">

                <form action="" method="post" class="d-flex flex-column align-items-center  justify-content-center rounded overflow-hidden" id="add-patient-form">
                    @csrf
                    <h1 class="align-self-start justify-self-start mb-1" id="head-text">Basic Information</h1>

                    <div class="step d-flex flex-column w-100 rounded  px-2" id="step1">
                        <div class="info card shadow  p-3">
                            <div class="mb-2 border-bottom">
                                <div class="user-info w-100">
                                    <div class="d-flex flex-column justify-content-center w-100 align-items-end">
                                        <label for="type-of-patient" class="">Type of Patient</label>
                                        <select name="type_of_patient" id="type-of-patient" class="form-select text-center bg-light w-100 w-md-50 w-lg-25" onchange="showAdditional()">
                                            <option value="" disabled selected>Select type of patient</option>
                                            <option value="vaccination">Vaccination</option>
                                            <option value="prenatal">Prenatal</option>
                                            <option value="tb-dots">TB DOTS</option>
                                            <option value="senior-citizen">Senior Citizen</option>
                                            <option value="family-planning">Family Planning</option>
                                        </select>-
                                    </div>
                                    <h4>Personal Info</h4>
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <input type="text" id="first_name" placeholder="First Name" class="form-control" name="first_name" value="">
                                            <small class="text-danger error-text" id="first_name_error"></small>
                                        </div>
                                        <div class="input-field w-50">
                                            <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="">
                                            <small class="text-danger error-text" id="middle_initial_error"></small>
                                        </div>
                                        <div class="input-field w-50">
                                            <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="">
                                            <small class="text-danger error-text" id="last_name_error"></small>
                                        </div>
                                    </div>
                                    <!-- age -->
                                    <div class="mb-2 d-flex gap-md-1 gap-0">
                                        <!-- date of birth -->
                                        <div class="input-field w-50">
                                            <label for="birthdate">Date of Birth</label>
                                            <input type="date" id="birthdate" placeholder="01-02-25" class="form-control w-100 px-5" name="date_of_birth" value="">
                                            <small class="text-danger error-text" id="date_of_birth_error"></small>
                                        </div>
                                        <!-- place of birth -->
                                        <div class="input-field w-50">
                                            <label for="place_of_birth">Place of Birth</label>
                                            <input type="text" id="place_of_birth" placeholder="trece martires city" class="form-control" name="place_of_birth" value="">
                                            <small class="text-danger error-text" id="place_of_birth_error"></small>
                                        </div>

                                        <!-- age -->
                                        <div class="input-field w-50">
                                            <label for="age">Age</label>
                                            <input type="number" id="age" placeholder="20" class="form-control" name="age" value="">
                                            <small class="text-danger error-text" id="age_error"></small>
                                        </div>
                                    </div>
                                    <!-- civil status, contact number, nationality -->
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <label for="sex">Sex</label>
                                            <div class="input-field d-flex align-items-center p-2">
                                                @php
                                                $selectedSex = optional(Auth::user() -> staff) -> sex ?? optional(Auth::user() -> nurses) -> sex ?? 'none';
                                                @endphp
                                                <div class="sex-input d-flex align-items-center justify-content-center w-100 gap-1">
                                                    <input type="radio" id="male" class="mb-0" name="sex" value="male" class="mb-0">
                                                    <label for="male">Male</label>
                                                    <input type="radio" id="female" class="mb-0" name="sex" value="female" class="mb-0">
                                                    <label for="female">Female</label>
                                                </div>
                                                <small class="text-danger error-text" id="sex_error"></small>
                                            </div>
                                        </div>
                                        <!-- contact -->
                                        <div class="input-field w-50">
                                            <label for="contact_number" class="">Contact Number</label>
                                            <input type="number" placeholder="+63-936-627-8671" class="form-control" name="contact_number" value="">
                                            <small class="text-danger error-text" id="contact_number_error"></small>
                                        </div>
                                        <div class="input-field w-50">
                                            <label for="nationality" class="">Nationality</label>
                                            <input type="text" placeholder="ex. Filipino" class="form-control" name="nationality" value="">
                                            <small class="text-danger error-text" id="nationality_error"></small>
                                        </div>

                                    </div>
                                    <!-- data of registration -->
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field flex-grow-1">
                                            <label for="dateOfRegistration">Date of Registration</label>
                                            <input type="date" id="dateOfRegistration" placeholder="20" class="form-control text-center w-100 px-5 " name="date_of_registration" value="">
                                            <small class="text-danger error-text" id="date_of_registration_error"></small>
                                        </div>
                                        <!-- administered by -->
                                        @if(Auth::user()->role == 'nurse')
                                        <div class="mb-2 w-100 w-md-50">
                                            <label for="brgy">Handled by <span class="text-muted">(healthworker name)</span>*</label>
                                            <select name="handled_by" id="handled_by" class="form-select ">
                                                <option value="" disabled selected>Select a person</option>
                                                @foreach($healthworkers as $worker)
                                                <option value="{{$worker->user_id}}">{{$worker->full_name}}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger error-text" id="health_worker_id_error"></small>
                                        </div>
                                        @elseif(Auth::user()->role == 'staff')


                                        <div class="hidden-handled-by">
                                            <input type="hidden" name="handled_by" id="handled_by" value="{{Auth::user()->id}}" data-health-worker-name="{{$healthWorkerFullName}}">
                                        </div>
                                        @endif

                                        <div class="mb-2 w-50 tb-dots-inputs d-none flex-column">
                                            <label for="">PhilHealth ID No.</label>
                                            <input type="text" placeholder="ex.1234-5678-9012" name="philheath_id" class="form-control">
                                            <small class="text-danger error-text" id="philhealth_id_no_error"></small>
                                        </div>
                                    </div>
                                    <div class="vaccination-inputs mb-2 d-none gap-1">
                                        <div class="input-field w-50">
                                            <label for="motherName">Mother Name</label>
                                            <input type="text" id="mother_name" placeholder="mother name" class="form-control" name="mother_name" value="">
                                            <!-- ERROR HANDLING -->
                                            <small class="text-danger error-text" id="mother_name_error"></small>
                                        </div>
                                        <div class="input-field w-50">
                                            <label for="fatherName">Father Name</label>
                                            <input type="text" id="fatherName" placeholder="Father Name" class="form-control" name="father_name" value="">
                                            <small class="text-danger error-text" id="father_name_error"></small>
                                        </div>
                                    </div>
                                    <div class="prenatal-inputs mb-2 d-none flex-column gap-1">
                                        <div class="mb-2 w-100 d-flex gap-2">
                                            <div class="input-field w-50">
                                                <label for="motherName">Head of the Family</label>
                                                <input type="text" id="head_of_the_family" placeholder="Enter the Name" class="form-control" name="family_head_name" value="">
                                                <small class="text-danger error-text" id="family_head_name_error"></small>

                                            </div>
                                            <div class="input-field w-50">
                                                <label for="pregnancy_civil_status" class="">Civil Status</label>
                                                <select name="civil_status" id="pregnancy_civil_status" class="form-select">
                                                    <option value="Single">Single</option>
                                                    <option value="Married">Married</option>
                                                    <option value="Divorce">Divorce</option>
                                                </select>
                                                <small class="text-danger error-text" id="civil_status_error"></small>
                                            </div>
                                            <div class="input-field w-50">
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
                                        </div>
                                        <div class="mb-3 w-100 d-flex gap-3">

                                            <!-- Religion -->
                                            <div class="input-field w-25">
                                                <label for="religion" class="form-label">Religion</label>
                                                <input type="text" id="religion" placeholder="Enter the Religion" class="form-control" name="religion" value="">
                                                <!-- ERROR HANDLING -->
                                                <small class="text-danger error-text" id="religion_error"></small>

                                            </div>

                                            <!-- PhilHealth -->
                                            <div class="input-field w-50">
                                                <label class="form-label">PhilHealth</label>
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <div class="form-check">
                                                        <input class="" type="radio" name="philhealth_number_radio" id="philhealth_yes" value="yes">
                                                        <label class="form-check-label" for="philhealth_yes">(Yes)</label>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <label class="form-label mb-0">Number:</label>
                                                        <input type="text" class="form-control form-control-sm w-100" name="philHealth_number">
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="" type="radio" name="philhealth_number_radio" id="philhealth_no" value="no">
                                                        <label class="form-check-label" for="philhealth_no">(No)</label>
                                                    </div>
                                                </div>
                                                <small class="text-danger error-text" id="philHealth_number_error"></small>
                                            </div>

                                            <!-- Family Planning -->
                                            <div class="input-field w-50">
                                                <label class="form-label fw-normal">Would you like to use a family planning method?</label>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="" type="radio" name="family_planning" id="planning_yes" value="yes">
                                                        <label class="" for="planning_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="" type="radio" name="family_planning" id="planning_no" value="no">
                                                        <label class="" for="planning_no">No</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="" type="radio" name="family_planning" id="planning_undecided" value="undecided">
                                                        <label class="form-check-label" for="planning_undecided">Undecided</label>
                                                    </div>
                                                </div>
                                                <small class="text-danger error-text" id="family_planning_decision_error"></small>
                                            </div>

                                        </div>
                                        <div class="mb-3">
                                            <div class="input-field">
                                                <label for="family_serial_no" class="form-label w-100">Family Serial No.</label>
                                                <input type="number" name="family_serial_no" placeholder="enter family serial no." class="w-100 form-control">
                                                <small class="text-danger error-text" id="family_serial_no_error"></small>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Senior Citizen inputs -->
                                    <div class="senior-citizen-inputs mb-2 d-none flex-column gap-1">
                                        <div class="mb-2 w-100 d-flex gap-2">
                                            <div class="input-field w-50">
                                                <label for="senior_citizen_civil_status" class="">Civil Status</label>
                                                <select name="civil_status" id="senior_citizen_civil_status" class="form-select">
                                                    <option value="Single">Single</option>
                                                    <option value="Married">Married</option>
                                                    <option value="Divorce">Divorce</option>
                                                </select>
                                                <small class="text-danger error-text" id="civil_status_error"></small>
                                            </div>
                                            <div class="input-field w-50">
                                                <label for="blood_type">Occupation</label>
                                                <input type="text" id="occupation" placeholder="Enter the Occupation" class="form-control" name="occupation">
                                                <small class="text-danger error-text" id="occupation_error"></small>
                                            </div>
                                            <div class="mb-3 w-50 d-flex gap-2">
                                                <div class="input-field w-100">
                                                    <label for="senior_religion">Religion</label>
                                                    <input type="text" id="senior_religion" placeholder="Enter the Religion" class="form-control" name="religion">
                                                    <small class=" text-danger" id="religion_error"></small>
                                                </div>
                                            </div>
                                            <div class="mb-2 w-50  d-flex flex-column">
                                                <label for=""> Member of Social Security System (SSS):</label>
                                                <div class="radio-input d-flex align-items-center justify-content-center w-100 gap-1 py-2">
                                                    <input type="radio" id="male" class="mb-0" name="SSS" value="Yes" class="mb-0">
                                                    <label for="male">Yes</label>
                                                    <input type="radio" id="female" class="mb-0" name="SSS" value="No" class="mb-0">
                                                    <label for="female">No</label>
                                                </div>
                                                <small class="text-danger error-text" id="SSS_error"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="family-planning-inputs d-none gap-1 mb-2">
                                        <div class="input-field w-50">
                                            <label for="family_planning_civil_status" class="">Civil Status</label>
                                            <select name="civil_status" id="family_planning_civil_status" class="form-select">
                                                <option value="Single">Single</option>
                                                <option value="Married">Married</option>
                                                <option value="Divorce">Divorce</option>
                                            </select>
                                            <small class="text-danger error-text" id="civil_status_error"></small>
                                        </div>
                                        <div class="input-field w-25">
                                            <label for="family_plan_religion">Religion</label>
                                            <input type="text" id="family_plan_religion" placeholder="Enter the Religion" class="form-control" name="religion">
                                            <small class="text-danger error-text" id="religion_error"></small>
                                        </div>
                                        <div class="input-field w-25">
                                            <label for="family_plan_occupation">Occupation</label>
                                            <input type="text" id="family_plan_patient_occupation" placeholder="Enter the Occupation" class="form-control" name="family_plan_occupation" value="">
                                            <small class="text-danger error-text" id="occupation_error"></small>
                                        </div>
                                    </div>
                                    <!-- family planning 2nd inputs -->
                                    <div class="family-planning-inputs d-none gap-1">
                                        <div class="input-field w-50">
                                            <label for="client_id">Client ID:</label>
                                            <input type="text" id="client_id" placeholder="Enter the client ID" class="form-control" name="client_id">
                                            <small class="text-danger error-text" id="client_id_error"></small>
                                        </div>
                                        <div class="input-field w-50">
                                            <label for="philhealth_no">Philhealth No:</label>
                                            <input type="text" id="philhealth_no" placeholder="Enter the Religion" class="form-control" name="philhealth_no">
                                            <small class="text-danger error-text" id="philhealth_no_error"></small>
                                        </div>
                                        <div class="input-field w-50 ">
                                            <label for="NHTS" class="">NHTS?:</label>
                                            <div class="inputs d-flex gap-5 w-100 justify-content-center">
                                                <div class="radio-input">
                                                    <input type="radio" name="NHTS" value="Yes" id="nhts_yes">
                                                    <label for="nhts_yes">Yes</label>
                                                </div>
                                                <div class="radio-input">
                                                    <input type="radio" name="NHTS" value="No" id="nhts_no">
                                                    <label for="nhts_no">No</label>
                                                </div>
                                            </div>
                                            <small class="text-danger error-text" id="NHTS_error"></small>
                                        </div>
                                    </div>

                                    <!-- address -->
                                    <div class="mb-md-2 mb-1 d-flex gap-1 flex-column">
                                        <h4>Address</h4>
                                        <div class="input-field d-flex gap-2 align-items-center flex-md-row flex-column">
                                            <div class=" mb-2 w-100 w-md-50">
                                                <label for="street">Street*</label>
                                                <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control py-2" name="street" value="">
                                                <small class="text-danger error-text" id="street_error"></small>
                                            </div>
                                            <div class="mb-2 w-100 w-md-50">
                                                <label for="brgy">Barangay*</label>
                                                @php
                                                $brgy = \App\Models\brgy_unit::orderBy('brgy_unit') -> get();
                                                @endphp
                                                <select name="brgy" id="brgy" class="form-select py-2">
                                                    <option value="" disabled selected>Select a brgy</option>
                                                    @foreach($brgy as $brgy_unit)
                                                    <option value="{{ $brgy_unit -> brgy_unit }}">{{$brgy_unit -> brgy_unit}}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-danger error-text" id="brgy_error"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="vital-sign w-100">
                                        <h5>Vital Sign</h5>
                                        <div class="mb-2 input-field d-flex gap-md-3 gap-0 w-100 first-row flex-wrap flex-md-row flex-column">
                                            <div class="mb-md-2 mb-1 flex-fill">
                                                <label for="BP">Blood Pressure:</label>
                                                <input type="text" class="form-control w-100" placeholder="ex. 120/80" name="blood_pressure">
                                                <small class="text-danger error-text" id="blood_pressure_error"></small>
                                            </div>
                                            <div class="mb-md-2 mb-1 flex-fill">
                                                <label for="BP">Temperature:</label>
                                                <input type="number" class="form-control w-100" placeholder="00 C" name="temperature">
                                                <small class="text-danger error-text" id="temperature_error"></small>
                                            </div>
                                            <div class="mb-md-2 mb-1 flex-fill">
                                                <label for="BP">Pulse Rate(Bpm):</label>
                                                <input type="text" class="form-control w-100" placeholder=" 60-100" name="pulse_rate">
                                                <small class="text-danger error-text" id="pulse_rate_error"></small>
                                            </div>

                                        </div>
                                        <!-- 2nd row -->
                                        <div class="mb-2 input-field d-flex gap-md-3 gap-0 w-100 second-row flex-wrap">
                                            <div class="mb-2 flex-fill">
                                                <label for="BP">Respiratory Rate (breaths/min):</label>
                                                <input type="text" class="form-control w-100" placeholder="ex. 25" name="respiratory_rate">
                                                <small class="text-danger error-text" id="respiratory_rate_error"></small>
                                            </div>
                                            <div class="mb-2 flex-fill">
                                                <label for="BP">Height(cm):</label>
                                                <input type="number" class="form-control w-100" placeholder="00.00" name="height">
                                                <small class="text-danger error-text" id="height_error"></small>
                                            </div>
                                            <div class="mb-2 flex-fill">
                                                <label for="BP">Weight(kg):</label>
                                                <input type="number" class="form-control w-100" placeholder=" 00.00" name="weight">
                                                <small class="text-danger error-text" id="weight_error"></small>
                                            </div>
                                        </div>
                                        <!-- 3rd row -->
                                        <div class="mb-2 input-field d-none gap-md-3 gap-0 w-100 third-row">
                                            <div class="mb-2 w-50">
                                                <label for="BP">Birth Height(cm):</label>
                                                <input type="number" class="form-control w-100" placeholder="00.00" name="vaccination_height">
                                                <small class="text-danger error-text" id="vaccination_height_error"></small>
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Birth Weight(kg):</label>
                                                <input type="text" class="form-control w-100" placeholder=" 00.00" name="vaccination_weight">
                                                <small class="text-danger error-text" id="vaccination_weight_error"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="button align-self-end mt-auto">
                                <button type="button" class="btn btn-success px-5 py-2 fs-5" onclick="nextStep()" id="first_next">Next</button>
                            </div>
                        </div>
                    </div>
                    <!-- step 2 -->
                    <div class="step d-flex flex-column align-self-center w-100 h-100 rounded gap-1  " id="step2">
                        <!-- vaccination -->
                        <div class="vaccination d-none  inner max-w-[960px] align-self-center h-100 rounded mb-2 patient-type" id="vaccination-con">
                            <div class="vaccination-content">
                                <div class="mb-md-2 mb-1 w-100 ">
                                    <div class="mb-md-2 mb-1 w-100">
                                        <label for="patient_name">Patient Name</label>
                                        <input type="text" class="form-control w-100 bg-light" id="vaccination_patient_name_view" disabled placeholder="Enter the Name">
                                    </div>
                                </div>
                                <div class="mb-md-2 mb-1 w-100">
                                    <div class="mb-md-2 mb-1 w-100">
                                        <label for="patient_name">Administered By:</label>
                                        <input type="text" class="form-control w-100 bg-light" disabled placeholder="Nurse">
                                    </div>
                                </div>
                                <div class="mb-md-2 mb-1 w-100">
                                    <div class="mb-md-2 mb-1 w-100">
                                        <label for="patient_name">handled By:</label>
                                        <input type="text" class="form-control w-100 bg-light" disabled placeholder="health worker name" id="handle_by_view_input">
                                    </div>
                                </div>
                                <div class="mb-md-2 mb-1 w-100 ">
                                    <div class="mb-md-2 mb-1 w-100">
                                        <label for="date_of_vaccination">Date of Vaccination</label>
                                        <input type="date" placeholder="20" class="form-control w-100 " name="date_of_vaccination" required>
                                        <small class="text-danger error-text" id="date_of_vaccination_error"></small>
                                    </div>
                                </div>
                                <div class="mb-md-2 mb-1 w-100">
                                    <div class="mb-md-2 mb-1 w-100">
                                        <label for="time">Time</label>
                                        <input type="time" class="form-control" name="time_of_vaccination" required>
                                        <small class="text-danger error-text" id="time_error"></small>
                                    </div>
                                </div>
                                <div class="mb-md-2 mb-1 w-100">
                                    <div class="mb-md-2 mb-1 w-100">
                                        <label for="current_weight">Weight</label>
                                        <input type="number" class="form-control" name="current_weight" id="current_weight" required>
                                        <small class="text-danger error-text" id="current_weight_error"></small>
                                    </div>
                                    <div class="mb-md-2 mb-1 w-100">
                                        <label for="time">Height</label>
                                        <input type="number" class="form-control" name="current_height" id="current_height" required>
                                        <small class="text-danger error-text" id="current_height_error"></small>
                                    </div>
                                    <div class="mb-md-2 mb-1 w-100">
                                        <label for="current_temperature">Temperature</label>
                                        <input type="number" class="form-control" name="current_temperature" id="current_temperature" required>
                                        <small class="text-danger error-text" id="current_temperature_error"></small>
                                    </div>
                                </div>
                                <div class="mb-md-2 mb-1">
                                    <label for="vaccine_type">Vaccine Type:</label>
                                    <div class="mb-md-2 mb-1 d-flex gap-2">
                                        <select name="vaccine_type" id="vaccine_input" class="form-select w-100" required>
                                            <option value="" selected disabled>Select Vaccine</option>
                                            @foreach($vaccines as $vaccine)
                                            <option value="{{$vaccine -> id}}">{{$vaccine->type_of_vaccine}}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-success" id="vaccine-add-btn"> Add</button>
                                    </div>
                                </div>
                                <div class="mb-2 bg-secondary w-100 p-3 d-flex flex-wrap justify-content-center rounded vaccines-container gap-1 ">
                                    <!-- <div class="vaccine d-flex justify-content-between bg-white align-items-center p-1 w-50 rounded">
                                        <p class="mb-0">Penta 1</p>
                                        <div class="delete-icon d-flex align-items-center justify-content-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                                                <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                                            </svg>
                                        </div>
                                    </div> -->
                                </div>
                                <input type="text" name="selected_vaccines" id="selected_vaccines" hidden>
                                <div class="mb-2 w-100">
                                    <label for="dose" class="w-100">Vaccine Dose Number:</label>
                                    <select id="dose" name="dose_number" required class="form-select" required>
                                        <option value="" disabled selected>Select Dose</option>
                                        <option value="1">1st Dose</option>
                                        <option value="2">2nd Dose</option>
                                        <option value="3">3rd Dose</option>
                                        <!-- <option value="booster">Booster</option> -->
                                    </select>
                                    <small class="text-danger error-text" id="dose_number_error"></small>
                                </div>
                                <div class="mb-2 w-100 ">
                                    <div class="mb-2 w-100">
                                        <label for="date_of_vaccination">Date of Comeback</label>
                                        <input type="date" placeholder="20" class="form-control w-100 " name="date_of_comeback" required>
                                        <small class="text-danger error-text" id="date_of_comeback_error"></small>
                                    </div>
                                </div>
                                <div class="mb-2 w-100">
                                    <label for="remarks">Remarks*</label>
                                    <input type="text" class="form-control" id="remarks" name="remarks">
                                    <small class="text-danger error-text" id="remarks_error"></small>
                                </div>

                            </div>
                            <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-5">
                                <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
                                <button type="submit" class="btn btn-success px-5 py-2 fs-5" id="vaccination-submit-btn">Save Record</button>
                            </div>
                        </div>
                        <!-- PRENATAL -->
                        <div class="prenatal d-none patient-type" id="prenatal-con">
                            @include('add_patient.prenatal')
                            <div class="buttons w-75 align-self-center d-flex justify-content-end gap-2 mt-2">
                                <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
                                <button type="button" class="btn btn-success px-5 py-2 fs-5" onclick="nextStep()">Next</button>
                            </div>
                        </div>
                        <!-- TB DOTS -->
                        <div class="tb-dots d-none patient-type w-100 flex-column" id="tb-dots-con">
                            @include('add_patient.tb-dots.tb-dots')

                        </div>
                        <!-- SENIOR CITIZEN -->
                        <div class="senior-citizen patient-type d-none flex-column align-self-center w-75 card shadow" id="senior-citizen-con">
                            @include('add_patient.senior-citizen.senior-citizen')
                        </div>
                        <!-- Family Planning -->
                        <div class="family-planning patient-type flex-grow-1 d-none flex-column align-items-center h-100 w-100" id="family-planning-con">
                            @include('add_patient.familyPlanning.step2')

                        </div>
                    </div>
                    <!-- STEP 3 PRENATAL & family planning -->
                    <div class="step d-none flex-column align-self-center w-100 h-100 rounded gap-1" id="step3">
                        <div id="prenatal-step3" class="d-none">@include('add_patient.prenatalPlanning')</div>
                        <div id="family-planning-step3" class="d-none flex-grow-1 flex-column h-100 w-100">@include('add_patient.familyPlanning.step3')</div>
                    </div>
                    <!-- STEP 4 -->
                    <div class="step d-none flex-column align-self-center w-100 h-100 rounded gap-1" id="step4">
                        <!-- family planning -->
                        @include('add_patient.familyPlanning.step4')
                    </div>
                    <!-- STEP 5 -->
                    <div class="step d-none flex-column align-self-center w-100 h-100 rounded gap-1" id="step5">
                        <!-- family planning -->
                        @include('add_patient.familyPlanning.step5')
                    </div>
                    <!-- STEP 6 -->
                    <div class="step d-none flex-column align-self-center w-100 h-100 rounded gap-1" id="step6">
                        <!-- family planning -->
                        @include('add_patient.familyPlanning.step6')
                    </div>
                    <div class="step d-none flex-column align-self-center w-100 h-100 rounded gap-1" id="step7">
                        <!-- family planning -->
                        @include('add_patient.familyPlanning.side_b.sideB')
                    </div>
                </form>
            </main>

        </div>

    </div>

    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('add-patient');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
    <script>
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('dateOfRegistration').value = today;

        // check if the date of vaccination input is present to avoid error
        if (document.getElementById('date_of_vaccination')) {
            document.getElementById('date_of_vaccination').value = today;
        };
    </script>
</body>

</html>