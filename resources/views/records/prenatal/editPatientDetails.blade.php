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
    'resources/js/prenatal/editPrenatalDetails.js',
    'resources/js/vitalSign.js'])

    @include('sweetalert::alert')
    <div class="patient-details vh-100 d-flex ">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-x-auto">
            @include('layout.header')
            <div class="d-flex flex-grow-1 flex-column overflow-auto">
                <main class="flex-column p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center border-bottom">
                        <h2>Update Patient Details</h2>
                        <div class="sequence-links d-md-flex d-none justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('records.prenatal') }}" class="text-decoration-none fs-5 text-muted">Prenatal</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">Edit Patient Info</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-lg-5 mx-md-3 mx-2 px-2 shadow-lg">
                        <a href="{{route('records.prenatal')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <form action="" method="post" id="update-prenatal-patient-details-form" class="d-flex flex-column align-items-center  justify-content-center rounded overflow-hidden bg-white py-2" enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                            <div class="step d-flex flex-column w-100 rounded  px-2">
                                <div class="info">
                                    <div class="bg-light border-start  border-primary px-3 py-2 mb-4 rounded w-100">
                                        <span class="fs-6">
                                            <strong>Note:</strong>
                                            <span class="text-danger">*</span>
                                            <span class="fw-light"> indicates a required field.</span>
                                        </span>
                                    </div>
                                    <h4>Personal Info</h4>
                                    <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="first_name" class="">First Name<span class="text-danger">*</span></label>
                                            <input type="text" id="first_name" placeholder="Enter your first name" class="form-control bg-light border-dark" name="first_name" value="{{optional($prenatalRecord)->patient?->first_name??''}}">
                                            <small class="text-danger error-text" id="first_name_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="middle_initial" class="">Middle Name</label>
                                            <input type="text" id="middle_initial" placeholder="Enter your middle name" class="form-control bg-light border-dark " name="middle_initial" value="{{optional($prenatalRecord)->patient?->middle_initial??''}}">

                                            <small class="text-danger error-text" id="middle_initial_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="last_name" class="">Last Name<span class="text-danger">*</span></label>
                                            <input type="text" id="last_name" placeholder="Enter your last name" class="form-control bg-light border-dark" name="last_name" value="{{optional($prenatalRecord)->patient?->last_name??''}}">
                                            <small class="text-danger error-text" id="last_name_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="suffix" class="">Suffix</label>
                                            <select name="suffix" id="suffix" class="form-select py-2 bg-light border-dark">
                                                <option value="" disabled {{ !optional($prenatalRecord)->patient?->suffix? 'selected' : '' }}>Select Suffix</option>
                                                <option value="Jr." {{ optional($prenatalRecord)->patient?->suffix== 'Jr.' ? 'selected' : '' }}>Jr</option>
                                                <option value="Sr." {{ optional($prenatalRecord)->patient?->suffix== 'Sr.' ? 'selected' : '' }}>Sr</option>
                                                <option value="II." {{ optional($prenatalRecord)->patient?->suffix== 'II.' ? 'selected' : '' }}>II</option>
                                                <option value="III." {{ optional($prenatalRecord)->patient?->suffix== 'III.' ? 'selected' : '' }}>III</option>
                                                <option value="IV." {{ optional($prenatalRecord)->patient?->suffix== 'IV.' ? 'selected' : '' }}>IV</option>
                                                <option value="V." {{ optional($prenatalRecord)->patient?->suffix== 'V.' ? 'selected' : '' }}>V</option>
                                            </select>
                                            <small class="text-danger" id="suffix_error"></small>
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <!-- date of birth -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="birthdate">Date of Birth<span class="text-danger">*</span></label>
                                            <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5 bg-light border-dark" name="date_of_birth" value="{{ optional($prenatalRecord?->patient?->date_of_birth)->format('Y-m-d') ?? '' }}" min="1950-01-01" max="{{date('Y-m-d')}}">

                                            <small class="text-danger error-text" id="date_of_birth_error"></small>

                                        </div>
                                        <!-- place of birth -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="place_of_birth">Place of Birth</label>
                                            <input type="text" id="place_of_birth" placeholder="Enter your the place of birth" class="form-control bg-light border-dark" name="place_of_birth" value="{{optional($prenatalRecord)->patient?->place_of_birth??''}}">

                                            <small class="text-danger error-text" id="birth_place_error"></small>

                                        </div>

                                        <!-- age -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="age">Age</label>
                                            <input type="text" id="age" placeholder="20" class="form-control bg-light border-dark" disabled value="{{optional($prenatalRecord)->patient?->age??''}}">
                                            <input type="hidden" id="hiddenAge" name="age" value="{{optional($prenatalRecord)->patient?->age??''}}">
                                            <small class="text-danger error-text" id="age_error"></small>

                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="sex">Sex</label>
                                            <div class="input-field d-flex align-items-center p-2 bg-light border-dark">
                                                @php
                                                $selectedSex = optional(Auth::user() -> staff) -> sex ?? optional(Auth::user() -> nurses) -> sex ?? 'none';
                                                @endphp
                                                <div class="sex-input d-flex align-items-center justify-content-center w-100 gap-1">
                                                    <input type="radio" id="male" class="mb-0" name="sex" value="Male" class="mb-0" disabled {{optional($prenatalRecord)->patient?->sex == 'Male'?'checked':''}}>Male</label>
                                                    <input type="radio" id="female" class="mb-0" name="sex" value="Female" class="mb-0" {{optional($prenatalRecord)->patient?->sex == 'Female'?'checked':''}}>Female</label>
                                                </div>

                                                <small class="text-danger error-text" id="sex_error"></small>

                                            </div>
                                        </div>
                                        <!-- contact -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="contact_number" class="">Contact Number</span><span class="text-danger">*</span></label>
                                            <input type="number" placeholder="Enter your phone number" class="form-control bg-light border-dark" name="contact_number" value="{{optional($prenatalRecord)->patient?->contact_number??''}}">

                                            <small class="text-danger error-text" id="contact_number_error"></small>

                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="nationality" class="">Nationality</label>
                                            <input type="text" placeholder="Enter your nationality" class="form-control bg-light border-dark" name="nationality" value="{{optional($prenatalRecord)->patient?->nationality??''}}">

                                            <small class="text-danger error-text" id="nationality_error"></small>

                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1 flex-wrap flex-md-nowrap">
                                        <div class="input-field w-full md:w-[50%]">
                                            <label for="dateOfRegistration">Date of Registration<span class="text-danger">*</span></label>
                                            <input type="date" id="dateOfRegistration" placeholder="20" class="form-control text-center w-100 px-5 bg-light border-dark" name="date_of_registration" value="{{optional($prenatalRecord)->patient?->date_of_registration->format('Y-m-d')??''}}" min="1950-01-01" max="{{date('Y-m-d')}}">

                                            <small class="text-danger error-text" id="date_of_registration_error"></small>

                                        </div>
                                        <!-- administered by -->
                                        <div class="mb-2 w-full md:w-[50%]">
                                            <label for="edit_handled_by">Administered by<span class="text-danger">*</span></label>
                                            <select name="handled_by" id="edit_handled_by" class="form-select bg-light border-dark " data-bs-health-worker-id="{{optional($prenatalRecord)->prenatal_case_record[0]->health_worker_id??''}}" data-staff-id="{{Auth::user()->role == 'staff'?Auth::user()->id:null}}">
                                                <option value="" disabled>Select a person</option>
                                            </select>

                                            <small class="text-danger error-text" id="handled_by_error"></small>

                                        </div>
                                    </div>

                                    <!-- mother name and father -->
                                    <div class="prenatal-inputs mb-2 d-flex flex-column gap-1">
                                        <div class="mb-2 w-100 d-flex gap-2 flex-xl-nowrap flex-wrap">
                                            <div class="input-field flex-fill xl:w-[50%]">
                                                <label for="motherName">Head of the Family</label>
                                                <input type="text" id="head_of_the_family" placeholder="Enter the Name" class="form-control bg-light border-dark" name="family_head" value="{{optional($prenatalRecord)->prenatal_medical_record->family_head_name??''}}">

                                                <small class="text-danger error-text" id="head_of_family_error"></small>


                                            </div>
                                            <div class="input-field flex-fill xl:w-[50%]">
                                                <label for="civil_status" class="">Civil Status</label>
                                                <select name="civil_status" id="civil_status" class="form-select bg-light border-dark">
                                                    <option value="" disabled>Select a Civil Status</option>
                                                    <option value="Single" {{optional($prenatalRecord)->patient == 'Single'?'selected':''}}>Single</option>
                                                    <option value="Married" {{optional($prenatalRecord)->patient == 'Married'?'selected':''}}>Married</option>
                                                    <option value="Divorce" {{optional($prenatalRecord)->patient == 'Divorce'?'selected':''}}>Divorce</option>
                                                </select>

                                                <small class="text-danger error-text" id="civil_status_error"></small>

                                            </div>
                                            <div class="input-field flex-fill xl:w-[50%]">
                                                <label for="blood_type">Blood Type</label>
                                                <select name="blood_type" id="blood_type" class="form-select bg-light border-dark" required data-bs-blood-type="{{optional($prenatalRecord)->prenatal_medical_record -> blood_type??''}}">
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
                                        <div class="mb-3 w-100 d-flex gap-3 flex-wrap flex-md-nowrap">

                                            <!-- Religion -->
                                            <div class="input-field flex-fill md:w-[50%] xl:w-[25%]">
                                                <label for="religion" class="form-label">Religion</label>
                                                <input type="text" id="religion" placeholder="Enter the Religion" class="form-control bg-light border-dark" name="religion" value="{{optional($prenatalRecord)->prenatal_medical_record -> religion??''}}">

                                                <small class="text-danger error-text" id="religion_error"></small>

                                            </div>

                                            <!-- PhilHealth -->
                                            <div class="input-field flex-fill xl:w-[50%]">
                                                <label class="form-label">PhilHealth</label>
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <div class="form-check">
                                                        <input class=" bg-light border-dark" type="radio" name="philhealth" id="philhealth_yes" value="yes" {{optional($prenatalRecord)->prenatal_medical_record -> philHealth_number?'checked':''}}>
                                                        <label class="form-check-label" for="philhealth_yes">(Yes)</label>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <label class="form-label mb-0">Number:</label>
                                                        <input type="text" class="form-control form-control-sm bg-light border-dark" name="philhealth_number" id="philhealth_number" style="width: 120px;" value="{{optional($prenatalRecord)->prenatal_medical_record -> philHealth_number??''}}">
                                                    </div>
                                                    <div class="form-check">
                                                        <input class=" bg-light border-dark" type="radio" name="philhealth" id="philhealth_no" value="no" {{ optional(optional($prenatalRecord)->prenatal_medical_record)->philHealth_number ? '' : 'checked' }}>
                                                        <label class="form-check-label" for="philhealth_no">(No)</label>
                                                    </div>
                                                </div>
                                                <small class="text-danger error-text" id="philhealth_error"></small>
                                            </div>

                                            <!-- Family Planning -->
                                            <div class="input-field flex-fill xl:w-[50%]">
                                                <label class="form-label fw-normal">Would you like to use a family planning method?</label>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class=" bg-light border-dark" type="radio" name="family_planning" id="planning_yes" value="yes" {{ optional($prenatalRecord)->prenatal_medical_record->family_planning_decision == 'yes'? 'checked' : '' }}>
                                                        <label class="form-check-label" for="planning_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class=" bg-light border-dark" type="radio" name="family_planning" id="planning_no" value="no" {{ optional($prenatalRecord)->prenatal_medical_record->family_planning_decision == 'no'? 'checked' : '' }}>
                                                        <label class="form-check-label" for="planning_no">No</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class=" bg-light border-dark" type="radio" name="family_planning" id="planning_undecided" value="undecided" {{ optional($prenatalRecord)->prenatal_medical_record->family_planning_decision == 'undecided'? 'checked' : '' }}>
                                                        <label class="form-check-label" for="planning_undecided">Undecided</label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="input-field">
                                            <label for="family_serial_no" class="form-label w-100">Family Serial No.</label>
                                            <input type="text" name="family_serial_no" placeholder="enter family serial no." class="w-100 form-control bg-light border-dark" value="{{optional($prenatalRecord)->prenatal_medical_record->family_serial_no ??''}}">
                                        </div>
                                        <small class="text-danger error-text" id="family_serial_no_error"></small>
                                    </div>
                                    <!-- address -->
                                    <div class="mb-2 d-flex gap-1 flex-column border-top border-bottom">
                                        <h4>Address</h4>
                                        <div class="input-field d-flex gap-2 align-items-center flex-wrap flex-md-nowrap">
                                            <div class=" mb-2 w-full md:w-[50%]">
                                                <label for="street">Street<span class="text-danger">*</span></label>
                                                <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control py-2 bg-light border-dark" name="street" value="{{ trim(($address->house_number ?: '') . ', ' . ($address->street ?: '')) }}">

                                                <small class="text-danger error-text" id="street_error"></small>

                                            </div>
                                            <div class="mb-2 w-full md:w-[50%]">
                                                <label for="brgy">Barangay<span class="text-danger">*</span></label>
                                                <select name="brgy" id="brgy" class="form-select py-2 bg-light border-dark" data-bs-selected-brgy="{{$address-> purok}}" data-health-worker-assigned-area-id="{{optional(Auth::user())->staff?->assigned_area_id}}">
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
                                                <input type="text" class="form-control w-100 bg-light border-dark" name="blood_pressure" id="blood_pressure" placeholder="Enter the blood pressure" value="{{ optional($caseRecord)-> blood_pressure?? '' }}">
                                                <small class="text-danger error-text" id="blood_pressure_error"></small>
                                            </div>
                                            <div class="mb-2 flex-fill xl:w-[50%]">
                                                <label for="BP">Temperature(°C):</label>
                                                <input type="text" class="form-control w-100 bg-light border-dark" name="temperature" id="temperature" placeholder="Enter the temperature" value="{{ optional($caseRecord)-> temperature?? '' }}">
                                                <small class="text-danger error-text" id="temperature_error"></small>
                                            </div>
                                            <div class="mb-2 flex-fill xl:w-[50%]">
                                                <label for="BP">Pulse Rate(Bpm):</label>
                                                <input type="text" class="form-control w-100 bg-light border-dark" name="pulse_rate" id="pulse_rate" placeholder="Enter the pulse rate" value="{{ optional($caseRecord)-> pulse_rate?? '' }}">
                                                <small class="text-danger error-text" id="pulse_rate_error"></small>
                                            </div>

                                        </div>
                                        <!-- 2nd row -->
                                        <div class="mb-2 input-field d-flex gap-3 w-100 second-row flex-xl-nowrap flex-wrap">
                                            <div class="mb-2 flex-fill xl:w-[50%]">
                                                <label for="BP">Respiratory Rate (breaths/min):</label>
                                                <input type="text" class="form-control w-100 bg-light border-dark" name="respiratory_rate" id="respiratory_rate" placeholder="Enter the respiratory rate" value="{{ optional($caseRecord)-> respiratory_rate?? '' }}">
                                                <small class="text-danger error-text" id="respiratory_rate_error"></small>
                                            </div>
                                            <div class="mb-2 flex-fill xl:w-[50%]">
                                                <label for="BP">Height(cm):</label>
                                                <input type="text" class="form-control w-100 bg-light border-dark" placeholder="Enter the height" name="height" id="height" value="{{ optional($caseRecord)-> height?? '' }}">
                                                <small class="text-danger error-text" id="height_error"></small>
                                            </div>
                                            <div class="mb-2 flex-fill xl:w-[50%]">
                                                <label for="BP">Weight(kg):</label>
                                                <input type="text" class="form-control w-100 bg-light border-dark" placeholder="Enter the weight" name="weight" id="weight" value="{{ optional($caseRecord)-> weight?? '' }}">
                                                <small class="text-danger error-text" id="weight_error"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="survey-questionare w-100 ">
                                        <div class="current-prenancy w-100 d-flex gap-3 mb-3 border-bottom  flex-lg-row flex-column">

                                            <div class="questions w-100 " style="border:1px solid black;">
                                                <h3 class="w-100 bg-success text-white text-center">Kasaysayan ng Pagbubuntis</h3>
                                                <div class="mb-4 px-2 d-flex">
                                                    <label for="number_of" class="w-100 fs-5" class="w-50">Bilang ng Pagbubuntis:</label>
                                                    <select name="number_of_children" id="number_of_children" class="form-select w-50 text-center">
                                                        <option value="" disabled selected>Select the number</option>
                                                        <option value="1" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> number_of_children == 1?'selected': '' }}>1</option>
                                                        <option value="2" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> number_of_children == 2?'selected': '' }}>2</option>
                                                        <option value="3" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> number_of_children == 3?'selected': '' }}>3</option>
                                                        <option value="4+" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> number_of_children == 4?'selected': '' }}>4+</option>
                                                    </select>
                                                </div>
                                                <div class="mb-4 px-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Nanganak ng sasarin:</label>
                                                    <div class="radio-input w-50 d-flex align-items-center justify-content-center gap-2">
                                                        <input type="radio" name="answer_1" value="yes" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> answer_1 == 'yes'?'checked': '' }}>
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer_1" value="no" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> answer_1 == 'no'?'checked': '' }}>
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 2nd -->
                                                <div class="mb-4 px-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">3 beses nakuhanan magkasunod:</label>
                                                    <div class="radio-input w-50 d-flex align-items-center justify-content-center gap-2">
                                                        <input type="radio" name="answer_2" value="yes" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> answer_2 == 'yes'?'checked': '' }}>
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer_2" value="no" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> answer_2 == 'no'?'checked': '' }}>
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 3rd -->
                                                <div class="mb-4 px-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Ipinanganak ng patay:</label>
                                                    <div class="radio-input w-50 d-flex align-items-center justify-content-center gap-2">
                                                        <input type="radio" name="answer_3" value="yes" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> answer_3 == 'yes'?'checked': '' }}>
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer_3" value="no" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> answer_3 == 'no'?'checked': '' }}>
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 4th -->
                                                <div class="mb-2 px-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Labis na pagdurogo matapos manganak:</label>
                                                    <div class="radio-input w-50 d-flex align-items-center justify-content-center gap-2">
                                                        <input type="radio" name="answer_4" value="yes" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> answer_4 == 'yes'?'checked': '' }}>
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer_4" value="no" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> answer_4 == 'no'?'checked': '' }}>
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- 2nd question-->
                                            <div class="questions w-100" style="border:1px solid black;">
                                                <h3 class="w-100 bg-success text-white text-center">Kasalukuyang Problemang Pang Kalusugan</h3>
                                                <div class="mb-4 px-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Tuberculosis(ubong labis 14 araaw):</label>
                                                    <div class="radio-input w-50 d-flex align-items-center justify-content-center gap-2">
                                                        <input type="radio" name="q2_answer1" value="yes" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> q2_answer1 == 'yes'?'checked': '' }}>
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="q2_answer1" value="no" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> q2_answer1 == 'no'?'checked': '' }}>
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 2nd -->
                                                <div class="mb-4 px-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Sakit sa Puso:</label>
                                                    <div class="radio-input w-50 d-flex align-items-center justify-content-center gap-2">
                                                        <input type="radio" name="q2_answer2" value="yes" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> q2_answer2 == 'yes'?'checked': '' }}>
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="q2_answer2" value="no" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> q2_answer2 == 'no'?'checked': '' }}>
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 3rd -->
                                                <div class="mb-4 px-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Diabetis:</label>
                                                    <div class="radio-input w-50 d-flex align-items-center justify-content-center gap-2">
                                                        <input type="radio" name="q2_answer3" value="yes" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> q2_answer3 == 'yes'?'checked': '' }}>
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="q2_answer3" value="no" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> q2_answer3 == 'no'?'checked': '' }}>
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 4th -->
                                                <div class="mb-4 px-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Hika:</label>
                                                    <div class="radio-input w-50 d-flex align-items-center justify-content-center gap-2">
                                                        <input type="radio" name="q2_answer4" value="yes" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> q2_answer4 == 'yes'?'checked': '' }}>
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="q2_answer4" value="no" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> q2_answer4 == 'no'?'checked': '' }}>
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 4th -->
                                                <div class="mb-4 px-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Bisyo:</label>
                                                    <div class="radio-input w-50 d-flex align-items-center justify-content-center gap-2">
                                                        <input type="radio" name="q2_answer5" value="yes" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> q2_answer5 == 'yes'?'checked': '' }}>
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="q2_answer5" value="no" {{ optional($prenatalRecord)->prenatal_case_record[0]-> pregnancy_history_questions[0] -> q2_answer5 == 'no'?'checked': '' }}>
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="survey-questionare w-100 ">
                                            <div class="hatol">
                                                <label for="" class="fw-bold fs-5">Decision</label>
                                                <div class="options px-5 py-2">
                                                    <div class="mb-2">
                                                        <input type="radio" name="nurse_decision" id="nurse_f1_option" value="1" {{optional($caseRecord)-> decision == 1 ?'checked':''}}>
                                                        <label for="nurse_f1_option">Papuntahin sa Doktor/RHU Alamin? Sundan ang kalagayan</label>
                                                    </div>
                                                    <div class="mb-2">
                                                        <input type="radio" name="nurse_decision" id="nurse_f2_option" value="2" {{optional($caseRecord)-> decision == 2 ?'checked':''}}>
                                                        <label for="nurse_f2_option">Masusing pagsusuri at aksyon ng kumadrona / Nurse</label>
                                                    </div>
                                                    <div class="mb-2">
                                                        <input type="radio" name="nurse_decision" id="nurse_f3_option" value="3" {{optional($caseRecord)-> decision == 3 ?'checked':''}}>
                                                        <label for="nurse_f3_option">Ipinayong manganak sa Ospital</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- save btn -->
                                    <div class="save-record d-flex justify-content-end  w-100">
                                        <input type="submit" class="btn btn-success px-4 fs-5" value="Save Record" id="update-patient-detail-BTN" data-bs-id="{{$prenatalRecord->id}}">
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
            const con = document.getElementById('record_prenatal');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>