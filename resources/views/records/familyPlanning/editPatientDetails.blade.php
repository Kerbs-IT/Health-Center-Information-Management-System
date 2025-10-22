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
        <div class="d-flex flex-grow-1 flex-column">
            @include('layout.header')
            <div class="d-flex flex-grow-1 flex-column overflow-auto">
                <main class="flex-column p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center border-bottom">
                        <h2>Update Patient Details</h2>
                        <div class="sequence-links d-flex justify-content-center align-items-center">
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
                    <div class="flex-grow-1 py-3 px-5">
                        <a href="{{route('record.family.planning')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <form action="" method="post" class="d-flex flex-column align-items-center  justify-content-center rounded overflow-hidden bg-white py-2" id="edit-family-planning-form">
                            @method('PUT')
                            @csrf
                            <div class="step d-flex flex-column w-100 rounded  px-2">
                                <div class="info">
                                    <h4>Personal Info</h4>
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <input type="text" id="first_name" placeholder="First Name" class="form-control bg-light" name="first_name" value="{{optional($familyPlanningRecord->patient)->first_name??''}}">
                                            <small class="text-danger"></small>

                                        </div>
                                        <div class="input-field w-50">
                                            <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control bg-light" name="middle_initial" value="{{optional($familyPlanningRecord->patient)->middle_initial??''}}">
                                            @error('middle_initial')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror

                                        </div>
                                        <div class="input-field w-50">
                                            <input type="text" id="last_name" placeholder="Last Name" class="form-control bg-light" name="last_name" value="{{optional($familyPlanningRecord->patient)->last_name??''}}">
                                            @error('last_name')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1">
                                        <!-- date of birth -->
                                        <div class="input-field w-50">
                                            <label for="birthdate">Date of Birth</label>
                                            <input type="date" id="birthdate" placeholder="20" class="form-control bg-light w-100 px-5" name="date_of_birth" value="{{optional($familyPlanningRecord->patient)->date_of_birth?->format('Y-m-d')??''}}">
                                            @error('date_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <!-- place of birth -->
                                        <div class="input-field w-50">
                                            <label for="place_of_birth">Place of Birth</label>
                                            <input type="text" id="place_of_birth" placeholder="20" class="form-control bg-light" name="place_of_birth" value="{{optional($familyPlanningRecord->patient)->place_of_birth??''}}">
                                            @error('place_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>

                                        <!-- age -->
                                        <div class="input-field w-50">
                                            <label for="age">Age</label>
                                            <input type="number" id="age" placeholder="20" class="form-control bg-light" name="age" value="{{optional($familyPlanningRecord->patient)->age??''}}">
                                            @error('age')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <label for="sex">Sex</label>
                                            <div class="input-field d-flex align-items-center p-2">
                                                <div class="sex-input d-flex align-items-center justify-content-center w-100 gap-1">
                                                    <input type="radio" id="male" class="mb-0" name="sex" value="male" {{ optional($familyPlanningRecord->patient)->sex == 'male'?'checked':'' }} class="mb-0">Male</label>
                                                    <input type="radio" id="female" class="mb-0" name="sex" value="female" class="mb-0" {{optional($familyPlanningRecord->patient)->sex == 'female'?'checked':''}}>Female</label>
                                                </div>
                                                @error('sex')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- contact -->
                                        <div class="input-field w-50">
                                            <label for="contact_number" class="">Contact Number</label>
                                            <input type="number" placeholder="+63-936-627-8671" class="form-control bg-light" name="contact_number" value="{{optional($familyPlanningRecord->patient)->contact_number??''}}">
                                            @error('contact_number')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <div class="input-field w-50">
                                            <label for="nationality" class="">Nationality</label>
                                            <input type="text" placeholder="ex. Filipino" class="form-control bg-light" name="nationality" value="{{optional($familyPlanningRecord->patient)->nationality??''}}">
                                            @error('nationality')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <label for="dateOfRegistration">Date of Registration</label>
                                            <input type="date" id="dateOfRegistration" placeholder="20" class="form-control bg-light text-center w-100 px-5 " name="date_of_registration" value="{{optional($familyPlanningRecord->patient)->date_of_registration?->format('Y-m-d')??''}}">
                                            @error('date_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <!-- administered by -->
                                        <div class="mb-2 w-50">
                                            <label for="brgy">Administered by*</label>
                                            <select name="handled_by" id="handled_by" class="form-select bg-light " data-bs-health-worker-id="{{optional($familyPlanningRecord->family_planning_medical_record)->health_worker_id??''}}">
                                                <option value="">Select a person</option>
                                            </select>
                                            @error('brgy')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>

                                    </div>
                                    <!-- civil status -->
                                    <div class="mb-2 w-100 d-flex gap-2">

                                        <div class="input-field w-50">
                                            <label for="civil_status" class="">Civil Status</label>
                                            <select name="civil_status" id="civil_status" class="form-select bg-light">
                                                <option value="Single" {{ optional($familyPlanningRecord->patient)->civil_status == 'Single'?'selected':'' }}>Single</option>
                                                <option value="Married" {{ optional($familyPlanningRecord->patient)->civil_status == 'Married'?'selected':'' }}>Married</option>
                                                <option value="Divorce" {{ optional($familyPlanningRecord->patient)->civil_status == 'Divorce'?'selected':'' }}>Divorce</option>
                                            </select>
                                            @error('civil_status')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <div class="mb-3 w-50 d-flex gap-2">
                                            <div class="input-field w-100">
                                                <label for="motherName">Religion</label>
                                                <input type="text" id="religion" placeholder="Enter the Religion" class="form-control bg-light" name="religion" value="{{optional($familyPlanningRecord->family_planning_medical_record)->religion??''}}">
                                                @error('mother_name')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3 w-50 d-flex gap-2">
                                            <div class="input-field w-100">
                                                <label for="">Occupation</label>
                                                <input type="text" id="head_of_the_family" placeholder="Enter the occupation" class="form-control bg-light" name="occupation" value="{{optional($familyPlanningRecord->family_planning_medical_record)->occupation??''}}">
                                                @error('mother_name')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                    <div class="mb-2 d-flex gap-2">
                                        <div class="input-field w-50">
                                            <label for="client_id">Client ID:</label>
                                            <input type="text" id="client_id" placeholder="Enter the client ID" class="form-control" name="client_id" value="{{optional($familyPlanningRecord->family_planning_case_record)->client_id??''}}">
                                            <small class="text-danger"></small>
                                        </div>
                                        <div class="input-field w-50">
                                            <label for="philhealth_no">Philhealth No:</label>
                                            <input type="text" class="form-control" name="philhealth_no" value="{{optional($familyPlanningRecord->family_planning_medical_record)->philhealth_no??''}}">
                                            <small class="text-danger"></small>
                                        </div>
                                        <div class="input-field w-50 ">
                                            <label for="NHTS" class="">NHTS?:</label>
                                            <div class="inputs d-flex gap-5 w-100 justify-content-center">
                                                <div class="radio-input">
                                                    <input type="radio" name="NHTS" value="Yes" id="nhts_yes" {{optional($familyPlanningRecord->family_planning_case_record)->NHTS == 'Yes'?'checked':''}}>
                                                    <label for="nhts_yes">Yes</label>
                                                </div>
                                                <div class="radio-input">
                                                    <input type="radio" name="NHTS" value="No" id="nhts_no" {{optional($familyPlanningRecord->family_planning_case_record)->NHTS == 'No'?'checked':''}}>
                                                    <label for="nhts_no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- address -->
                                    <div class="mb-2 d-flex gap-1 flex-column border-bottom">
                                        <h4>Address</h4>
                                        <div class="input-field d-flex gap-2 align-items-center">
                                            <div class=" mb-2 w-50">
                                                <label for="street">Street*</label>
                                                <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control bg-light py-2" name="street" value="{{trim($address->house_number . ' '. $address->street)}}">
                                                @error('street')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="brgy">Barangay*</label>
                                                <select name="brgy" id="brgy" class="form-select bg-light py-2" data-bs-selected-brgy="{{$address->purok}}">
                                                    <option value="">Select a brgy</option>
                                                </select>
                                                @error('brgy')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <!-- vital sign -->
                                    <div class="vital-sign w-100 border-bottom">
                                        <h5>Vital Sign</h5>
                                        <div class="mb-2 input-field d-flex gap-3 w-100 first-row">
                                            <div class="mb-2 w-50">
                                                <label for="BP">Blood Pressure:</label>
                                                <input type="text" class="form-control w-100" name="blood_pressure" placeholder="ex. 120/80" value="{{optional($familyPlanningRecord->family_planning_medical_record)->blood_pressure??''}}">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Temperature:</label>
                                                <input type="number" class="form-control w-100" name="temperature" placeholder="00 C" value="{{optional($familyPlanningRecord->family_planning_medical_record)->temperature??''}}">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Pulse Rate(Bpm):</label>
                                                <input type="text" class="form-control w-100" name="pulse_rate" placeholder=" 60-100" value="{{optional($familyPlanningRecord->family_planning_medical_record)->pulse_rate??''}}">
                                            </div>

                                        </div>
                                        <!-- 2nd row -->
                                        <div class="mb-2 input-field d-flex gap-3 w-100 second-row">
                                            <div class="mb-2 w-50">
                                                <label for="BP">Respiratory Rate (breaths/min):</label>
                                                <input type="text" class="form-control w-100" name="respiratory_rate" placeholder="ex. 25" value="{{optional($familyPlanningRecord->family_planning_medical_record)->respiratory_rate??''}}">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Height(cm):</label>
                                                <input type="number" class="form-control w-100" placeholder="00.00" name="height" value="{{optional($familyPlanningRecord->family_planning_medical_record)->height??''}}">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Weight(kg):</label>
                                                <input type="number" class="form-control w-100" placeholder=" 00.00" name="weight" value="{{optional($familyPlanningRecord->family_planning_medical_record)->weight??''}}">
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