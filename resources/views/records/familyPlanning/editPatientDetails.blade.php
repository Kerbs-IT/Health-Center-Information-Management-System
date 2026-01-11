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
    'resources/js/family_planning/editPatientDetails.js'])
    <div class="patient-details vh-100 d-flex">
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
                            <a href="{{ route('records.prenatal') }}" class="text-decoration-none fs-5 text-muted">Family Planning</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">Edit Patient Info</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-lg-5 mx-md-3 mx-2 px-2 shadow-lg">
                        <a href="{{route('record.family.planning')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <form action="" method="post" class="d-flex flex-column align-items-center  justify-content-center rounded overflow-hidden bg-white py-2" id="edit-family-planning-form">
                            @method('PUT')
                            @csrf
                            <div class="step d-flex flex-column w-100 rounded px-2">
                                <div class="info">
                                    <div class="bg-light border-start  border-primary px-3 py-2 mb-4 rounded w-100">
                                        <span class="fs-6">
                                            <strong>Note:</strong>
                                            <span class="text-danger">*</span>
                                            <span class="fw-light"> indicates a required field.</span>
                                        </span>
                                    </div>
                                    <h4>Personal Info</h4>
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="first_name" class="">First Name<span class="text-danger">*</span></label>
                                            <input type="text" id="first_name" placeholder="First Name" class="form-control bg-light" name="first_name" value="{{optional($familyPlanningRecord->patient)->first_name??''}}">
                                            <small class="text-danger error-text" id="first_name_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="middle_initial" class="">Middle Name</label>
                                            <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control bg-light" name="middle_initial" value="{{optional($familyPlanningRecord->patient)->middle_initial??''}}">
                                            <small class="text-danger error-text" id="middle_initial_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="last_name" class="">Last Name<span class="text-danger">*</span></label>
                                            <input type="text" id="last_name" placeholder="Last Name" class="form-control bg-light" name="last_name" value="{{optional($familyPlanningRecord->patient)->last_name??''}}">
                                            <small class="text-danger error-text" id="last_name_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="suffix" class="">Suffix</label>
                                            <select name="suffix" id="suffix" class="form-select py-2 ">
                                                <option value="" disabled {{ !optional($familyPlanningRecord)->patient?->suffix? 'selected' : '' }}>Select Suffix</option>
                                                <option value="Jr." {{ optional($familyPlanningRecord)->patient?->suffix== 'Jr.' ? 'selected' : '' }}>Jr</option>
                                                <option value="Sr." {{ optional($familyPlanningRecord)->patient?->suffix== 'Sr.' ? 'selected' : '' }}>Sr</option>
                                                <option value="II." {{ optional($familyPlanningRecord)->patient?->suffix== 'II.' ? 'selected' : '' }}>II</option>
                                                <option value="III." {{ optional($familyPlanningRecord)->patient?->suffix== 'III.' ? 'selected' : '' }}>III</option>
                                                <option value="IV." {{ optional($familyPlanningRecord)->patient?->suffix== 'IV.' ? 'selected' : '' }}>IV</option>
                                                <option value="V." {{ optional($familyPlanningRecord)->patient?->suffix== 'V.' ? 'selected' : '' }}>V</option>
                                            </select>
                                            <small class="text-danger" id="suffix_error"></small>
                                        </div>
                                    </div>
                                    <div class="mb-md-2 mb-0 d-flex gap-1  flex-xl-nowrap flex-wrap">
                                        <!-- date of birth -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="birthdate">Date of Birth<span class="text-danger">*</span></label>
                                            <input type="date" id="birthdate" placeholder="20" class="form-control bg-light w-100 px-5" min="1950-01-01" max="{{date('Y-m-d')}}" name="date_of_birth" value="{{optional($familyPlanningRecord->patient)->date_of_birth?->format('Y-m-d')??''}}">

                                            <small class="text-danger error-text" id="data_of_birth_error"></small>

                                        </div>
                                        <!-- place of birth -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="place_of_birth">Place of Birth</label>
                                            <input type="text" id="place_of_birth" placeholder="20" class="form-control bg-light" name="place_of_birth" value="{{optional($familyPlanningRecord->patient)->place_of_birth??''}}">

                                            <small class="text-danger error-text" id="place_of_birth_error"></small>
                                        </div>

                                        <!-- age -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="age">Age<span class="text-danger">*</span></label>
                                            <input type="number" id="age" placeholder="20" class="form-control bg-light" disabled value="{{optional($familyPlanningRecord->patient)->age??''}}">
                                            <input type="hidden" id="hiddenAge" name="age">
                                            <small class="text-danger error-text" id="age_error"></small>

                                        </div>
                                    </div>
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="sex">Sex</label>
                                            <div class="input-field d-flex align-items-center p-2">
                                                <div class="sex-input d-flex align-items-center justify-content-center w-100 gap-1">
                                                    <input type="radio" id="male" class="mb-0" name="sex" value="Male" {{ optional($familyPlanningRecord->patient)->sex == 'Male'?'checked':'' }} class="mb-0">Male</label>
                                                    <input type="radio" id="female" class="mb-0" name="sex" value="Female" class="mb-0" {{optional($familyPlanningRecord->patient)->sex == 'Female'?'checked':''}}>Female</label>
                                                </div>

                                                <small class="text-danger error-text" id="sex_error"></small>

                                            </div>
                                        </div>
                                        <!-- contact -->
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="contact_number" class="">Contact Number</label>
                                            <input type="number" placeholder="+63-936-627-8671" class="form-control bg-light" name="contact_number" value="{{optional($familyPlanningRecord->patient)->contact_number??''}}">

                                            <small class="text-danger error-text" id="contact_number_error"></small>

                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="nationality" class="">Nationality</label>
                                            <input type="text" placeholder="ex. Filipino" class="form-control bg-light" name="nationality" value="{{optional($familyPlanningRecord->patient)->nationality??''}}">

                                            <small class="text-danger error-text" id="nationality_error"></small>

                                        </div>
                                    </div>
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="dateOfRegistration">Date of Registration<span class="text-danger">*</span></label>
                                            <input type="date" id="dateOfRegistration" placeholder="20" class="form-control bg-light text-center w-100 px-5 " min="1950-01-01" max="{{date('Y-m-d')}}" name="date_of_registration" value="{{optional($familyPlanningRecord->patient)->date_of_registration?->format('Y-m-d')??''}}">

                                            <small class="text-danger error-text" id="date_of_registration_error"></small>

                                        </div>
                                        <!-- administered by -->
                                        <div class="mb-md-2 mb-0 flex-fill xl:w-[50%]">
                                            <label for="brgy">Administered by<span class="text-danger">*</span></label>
                                            <select name="handled_by" id="handled_by" class="form-select bg-light " data-bs-health-worker-id="{{optional($familyPlanningRecord->family_planning_medical_record)->health_worker_id??''}}">
                                                <option value="">Select a person</option>
                                            </select>

                                            <small class="text-danger error-text" id="handled_by_error"></small>

                                        </div>

                                    </div>
                                    <!-- civil status -->
                                    <div class="mb-md-2 mb-0 w-100 d-flex gap-2 flex-xl-nowrap flex-wrap">

                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="civil_status" class="">Civil Status</label>
                                            <select name="civil_status" id="civil_status" class="form-select bg-light">
                                                <option value="Single" {{ optional($familyPlanningRecord->patient)->civil_status == 'Single'?'selected':'' }}>Single</option>
                                                <option value="Married" {{ optional($familyPlanningRecord->patient)->civil_status == 'Married'?'selected':'' }}>Married</option>
                                                <option value="Divorce" {{ optional($familyPlanningRecord->patient)->civil_status == 'Divorce'?'selected':'' }}>Divorce</option>
                                            </select>

                                            <small class="text-danger error-text" id="civil_status_error"></small>

                                        </div>
                                        <div class="mb-3 flex-fill xl:w-[50%] d-flex gap-2">
                                            <div class="input-field w-100">
                                                <label for="motherName">Religion</label>
                                                <input type="text" id="religion" placeholder="Enter the Religion" class="form-control bg-light" name="religion" value="{{optional($familyPlanningRecord->family_planning_medical_record)->religion??''}}">

                                                <small class="text-danger error-text" id="religion_error"></small>

                                            </div>
                                        </div>
                                        <div class="mb-3 flex-fill xl:w-[50%] d-flex gap-2">
                                            <div class="input-field w-100">
                                                <label for="">Occupation</label>
                                                <input type="text" id="occupation" placeholder="Enter the occupation" class="form-control bg-light" name="occupation" value="{{optional($familyPlanningRecord->family_planning_medical_record)->occupation??''}}">

                                                <small class="text-danger error-text" id="occupation_error"></small>

                                            </div>
                                        </div>

                                    </div>
                                    <div class="mb-md-2 mb-0 d-flex gap-2 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="client_id">Client ID:</label>
                                            <input type="text" id="client_id" placeholder="Enter the client ID" class="form-control" name="client_id" value="{{optional($familyPlanningRecord->family_planning_case_record->first())->client_id??''}}">
                                            <small class="text-danger error-text" id="client_id_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="philhealth_no">Philhealth No:</label>
                                            <input type="text" class="form-control" name="philhealth_no" value="{{optional($familyPlanningRecord->family_planning_medical_record->first())->philhealth_no??''}}">
                                            <small class="text-danger error-text" id="philhealth_no_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%] ">
                                            <label for="NHTS" class="">NHTS?:</label>
                                            <div class="inputs d-flex gap-5 w-100 justify-content-center">
                                                <div class="radio-input">
                                                    <input type="radio" name="NHTS" value="Yes" id="nhts_yes" {{optional($familyPlanningRecord->family_planning_case_record->first())->NHTS == 'Yes'?'checked':''}}>
                                                    <label for="nhts_yes">Yes</label>
                                                </div>
                                                <div class="radio-input">
                                                    <input type="radio" name="NHTS" value="No" id="nhts_no" {{optional($familyPlanningRecord->family_planning_case_record->first())->NHTS == 'No'?'checked':''}}>
                                                    <label for="nhts_no">No</label>
                                                </div>
                                            </div>
                                            <small class="text-danger error-text" id="NHTS_error"></small>
                                        </div>
                                    </div>

                                    <!-- address -->
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-column border-bottom">
                                        <h4>Address</h4>
                                        <div class="input-field d-flex gap-2 align-items-center flex-wrap flex-md-nowrap flex-md-row flex-column">
                                            <div class=" mb-md-2 mb-0  w-full md:w-[50%]">
                                                <label for="street">Street<span class="text-danger">*</span></label>
                                                <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control bg-light py-2" name="street" value="{{ trim(($address->house_number ?: '') . ', ' . ($address->street ?: '')) }}">

                                                <small class="text-danger error-text" id="street_error"></small>

                                            </div>
                                            <div class="mb-md-2 mb-0  w-full md:w-[50%]">
                                                <label for="brgy">Barangay<span class="text-danger">*</span></label>
                                                <select name="brgy" id="brgy" class="form-select bg-light py-2" data-bs-selected-brgy="{{$address->purok}}">
                                                    <option value="">Select a brgy</option>
                                                </select>

                                                <small class="text-danger error-text" id="brgy_error"></small>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- vital sign -->
                                    <div class="vital-sign w-100 border-bottom">
                                        <h5>Vital Sign</h5>
                                        <div class="mb-md-2 mb-0 input-field d-flex gap-3 w-100 first-row flex-wrap flex-md-nowrap flex-md-row flex-column">
                                            <div class="mb-md-2 mb-0 flex-fill xl:w-[50%]">
                                                <label for="BP">Blood Pressure:</label>
                                                <input type="text" class="form-control w-100" name="blood_pressure" placeholder="Enter the blood pressure" value="{{optional($familyPlanningRecord->family_planning_medical_record)->blood_pressure??''}}">
                                                <small class="text-danger error-text" id="blood_pressure_error"></small>
                                            </div>
                                            <div class="mb-md-2 mb-0 flex-fill xl:w-[50%]">
                                                <label for="BP">Temperature:</label>
                                                <input type="text" class="form-control w-100" name="temperature" placeholder="Enter the value temperature" {{optional($familyPlanningRecord->family_planning_medical_record)->temperature??''}}">
                                                <small class="text-danger error-text" id="temperature_error"></small>
                                            </div>
                                            <div class="mb-md-2 mb-0 flex-fill xl:w-[50%]">
                                                <label for="BP">Pulse Rate(Bpm):</label>
                                                <input type="text" class="form-control w-100" name="pulse_rate" placeholder="Enter the pulse rate" value="{{optional($familyPlanningRecord->family_planning_medical_record)->pulse_rate??''}}">
                                                <small class="text-danger error-text" id="pulse_rate_error"></small>
                                            </div>

                                        </div>
                                        <!-- 2nd row -->
                                        <div class="mb-md-2 mb-0 input-field d-flex gap-3 w-100 second-row flex-wrap flex-md-nowrap flex-md-row flex-column">
                                            <div class="mb-md-2 mb-0 flex-fill xl:w-[50%]">
                                                <label for="BP">Respiratory Rate (breaths/min):</label>
                                                <input type="text" class="form-control w-100" name="respiratory_rate" placeholder="Enter the respiratory rate" value="{{optional($familyPlanningRecord->family_planning_medical_record)->respiratory_rate??''}}">
                                                <small class="text-danger error-text" id="respiratory_rate_error"></small>
                                            </div>
                                            <div class="mb-md-2 mb-0 flex-fill xl:w-[50%]">
                                                <label for="BP">Height(cm):</label>
                                                <input type="text" class="form-control w-100" placeholder="Enter the height" name="height" value="{{optional($familyPlanningRecord->family_planning_medical_record)->height??''}}">
                                                <small class="text-danger error-text" id="height_error"></small>
                                            </div>
                                            <div class="mb-md-2 mb-0 flex-fill xl:w-[50%]">
                                                <label for="BP">Weight(kg):</label>
                                                <input type="text" class="form-control w-100" placeholder="Enter the weight" name="weight" value="{{optional($familyPlanningRecord->family_planning_medical_record)->weight??''}}">
                                                <small class="text-danger error-text" id="weight_error"></small>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- save btn -->
                                    <div class="save-record d-flex justify-content-end  w-100">
                                        <input type="submit" class="btn btn-success px-4 fs-5" id="edit-save-btn" value="Save Record" data-bs-medical-id="{{$familyPlanningRecord->id}}">
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
            const con = document.getElementById('record_family_planning');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>