<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body class="bg-white">
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/js/patient/add-patient.js',
    'resources/css/patient/add-patient.css',
    'resources/css/patient/record.css'])
    @include('sweetalert::alert')
    <div class="add-patient d-flex vh-100">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="flex-grow-1 d-flex flex-column" style="min-height: 0;">
            @include('layout.header')
            <main class=" flex-grow-1 py-2 px-4 basic-info" style="overflow-y: auto; min-height: 0;">

                <form action="" method="post" class="d-flex flex-column align-items-center  justify-content-center rounded overflow-hidden">
                    <h1 class="align-self-start justify-self-start mb-1" id="head-text">Basic Information</h1>

                    <div class="step d-flex flex-column w-100 rounded  px-2" id="step1">
                        <div class="info card shadow  p-3">
                            <div class="mb-2 border-bottom">
                                <div class="user-info w-100">
                                    <div class="d-flex flex-column justify-content-center w-100 align-items-end">
                                        <label for="type-of-patient" class="">Type of Patient</label>
                                        <select name="type-of-patient" id="type-of-patient" class="form-select text-center bg-light w-25" onchange="showAdditional()">
                                            <option value="" disabled selected>Select type of patient</option>
                                            <option value="vaccination">Vaccination</option>
                                            <option value="prenatal">Prenatal</option>
                                            <option value="tb-dots">TB DOTS</option>
                                            <option value="senior-citizen">Senior Citizen</option>
                                            <option value="family-planning">Family Planning</option>
                                        </select>
                                    </div>
                                    <h4>Personal Info</h4>
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <input type="text" id="first_name" placeholder="First Name" class="form-control" name="first_name" value="">
                                            @error('first_name')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror

                                        </div>
                                        <div class="input-field w-50">
                                            <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="">
                                            @error('middle_initial')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror

                                        </div>
                                        <div class="input-field w-50">
                                            <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="">
                                            @error('last_name')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- age -->
                                    <div class="mb-2 d-flex gap-1">
                                        <!-- date of birth -->
                                        <div class="input-field w-50">
                                            <label for="birthdate">Date of Birth</label>
                                            <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5" name="date_of_birth" value="">
                                            @error('date_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <!-- place of birth -->
                                        <div class="input-field w-50">
                                            <label for="place_of_birth">Place of Birth</label>
                                            <input type="text" id="place_of_birth" placeholder="20" class="form-control" name="place_of_birth" value="">
                                            @error('place_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>

                                        <!-- age -->
                                        <div class="input-field w-50">
                                            <label for="age">Age</label>
                                            <input type="text" id="age" placeholder="20" class="form-control" name="age" value="">
                                            @error('age')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
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
                                                    <input type="radio" id="male" class="mb-0" name="sex" value="" class="mb-0">Male</label>
                                                    <input type="radio" id="female" class="mb-0" name="sex" value="" class="mb-0">Female</label>
                                                </div>
                                                @error('sex')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- contact -->
                                        <div class="input-field w-50">
                                            <label for="contact_number" class="">Contact Number</label>
                                            <input type="number" placeholder="+63-936-627-8671" class="form-control" name="contact_number" value="">
                                            @error('contact_number')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <div class="input-field w-50">
                                            <label for="nationality" class="">Nationality</label>
                                            <input type="text" placeholder="ex. Filipino" class="form-control" name="nationality" value="">
                                            @error('nationality')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>

                                    </div>
                                    <!-- data of registration -->
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <label for="dateOfRegistration">Date of Registration</label>
                                            <input type="date" id="dateOfRegistration" placeholder="20" class="form-control text-center w-100 px-5 " name="date_of_registration" value="">
                                            @error('date_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <!-- administered by -->
                                        <div class="mb-2 w-50">
                                            <label for="brgy">Administered by*</label>
                                            <select name="brgy" id="brgy" class="form-select ">
                                                <option value="">Select a person</option>
                                            </select>
                                            @error('brgy')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="vaccination-inputs mb-2 d-none gap-1">
                                        <div class="input-field w-50">
                                            <label for="motherName">Mother Name</label>
                                            <input type="text" id="mother_name" placeholder="mother name" class="form-control" name="mother_name" value="">
                                            @error('mother_name')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror

                                        </div>
                                        <div class="input-field w-50">
                                            <label for="fatherName">Father Name</label>
                                            <input type="text" id="fatherName" placeholder="Father Name" class="form-control" name="father_name" value="">
                                            @error('middle_initial')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror

                                        </div>
                                    </div>
                                    <div class="prenatal-inputs mb-2 d-none flex-column gap-1">
                                        <div class="mb-2 w-100 d-flex gap-2">
                                            <div class="input-field w-50">
                                                <label for="motherName">Head of the Family</label>
                                                <input type="text" id="head_of_the_family" placeholder="Enter the Name" class="form-control" name="mother_name" value="">
                                                @error('mother_name')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror

                                            </div>
                                            <div class="input-field w-50">
                                                <label for="civil_status" class="">Civil Status</label>
                                                <select name="civil_status" id="civil_status" class="form-select">
                                                    <option value="Single">Single</option>
                                                    <option value="Married">Married</option>
                                                    <option value="Divorce">Divorce</option>
                                                </select>
                                                @error('civil_status')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
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
                                            </div>

                                        </div>
                                        <div class="mb-3 w-100 d-flex gap-2">
                                            <div class="input-field w-25">
                                                <label for="motherName">Religion</label>
                                                <input type="text" id="head_of_the_family" placeholder="Enter the Religion" class="form-control" name="mother_name" value="">
                                                @error('mother_name')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                            <div class="input-field w-25">
                                                <div class="header">
                                                    <label for="" class="text-white">a</label>
                                                </div>
                                                <div class="philhealth-content d-flex align-items-center w-100 gap-2 mt-2">
                                                    <label for="" class="fs-5">Philheath</label>


                                                    <label>(Yes)</label>
                                                    <input type="radio" name="philhealth" class="custom-radio w-25" value="yes">
                                                    <div class="phil-num d-flex gap-1 ">
                                                        <label for="" class="fs-5">Number:</label>
                                                        <input type="text" class="">
                                                    </div>
                                                    <h6 class="mb-0">(No)</h6>
                                                    <input type="radio" name="philhealth" class="custom-radio w-25" value="no">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="family-planning-inputs d-none gap-1">
                                        <div class="input-field w-50">
                                            <label for="civil_status" class="">Civil Status</label>
                                            <select name="civil_status" id="civil_status" class="form-select">
                                                <option value="Single">Single</option>
                                                <option value="Married">Married</option>
                                                <option value="Divorce">Divorce</option>
                                            </select>
                                            @error('civil_status')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <div class="input-field w-25">
                                            <label for="religion">Religion</label>
                                            <input type="text" id="head_of_the_family" placeholder="Enter the Religion" class="form-control" name="mother_name" value="">
                                            @error('religion')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <div class="input-field w-25">
                                            <label for="patient_occupation">Occupation</label>
                                            <input type="text" id="patient_occupation" placeholder="Enter the Occupation" class="form-control" name="patient_occupation" value="">
                                            @error('occupation')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- address -->
                                    <div class="mb-2 d-flex gap-1 flex-column">
                                        <h4>Address</h4>
                                        <div class="input-field d-flex gap-2 align-items-center">
                                            <div class=" mb-2 w-50">
                                                <label for="street">Street*</label>
                                                <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control py-2" name="street" value="">
                                                @error('street')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="brgy">Barangay*</label>
                                                <select name="brgy" id="brgy" class="form-select py-2">
                                                    <option value="">Select a brgy</option>
                                                </select>
                                                @error('brgy')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="vital-sign w-100">
                                        <h5>Vital Sign</h5>
                                        <div class="mb-2 input-field d-flex gap-3 w-100 first-row">
                                            <div class="mb-2 w-50">
                                                <label for="BP">Blood Pressure:</label>
                                                <input type="text" class="form-control w-100" placeholder="ex. 120/80">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Temperature:</label>
                                                <input type="number" class="form-control w-100" placeholder="00 C">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Pulse Rate(Bpm):</label>
                                                <input type="text" class="form-control w-100" placeholder=" 60-100">
                                            </div>

                                        </div>
                                        <!-- 2nd row -->
                                        <div class="mb-2 input-field d-flex gap-3 w-100 second-row">
                                            <div class="mb-2 w-50">
                                                <label for="BP">Respiratory Rate (breaths/min):</label>
                                                <input type="text" class="form-control w-100" placeholder="ex. 25">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Height(cm):</label>
                                                <input type="number" class="form-control w-100" placeholder="00.00" name="height">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Weight(kg):</label>
                                                <input type="number" class="form-control w-100" placeholder=" 00.00" name="weight">
                                            </div>
                                        </div>
                                        <!-- 3rd row -->
                                        <div class="mb-2 input-field d-none gap-3 w-100 third-row">
                                            <div class="mb-2 w-50">
                                                <label for="BP">Birth Height(cm):</label>
                                                <input type="number" class="form-control w-100" placeholder="00.00" name="height">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Birth Weight(kg):</label>
                                                <input type="text" class="form-control w-100" placeholder=" 00.00" name="weight">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="button align-self-end mt-auto">
                                <button type="button" class="btn btn-success px-5 py-2 fs-5" onclick="nextStep()">Next</button>
                            </div>
                        </div>
                    </div>
                    <!-- step 2 -->
                    <div class="step d-flex flex-column align-self-center w-100 h-100 rounded gap-1  " id="step2">
                        <!-- vaccination -->
                        <div class="vaccination d-none  inner w-50 align-self-center h-100 rounded mb-2 patient-type" id="vaccination-con">
                            <div class="vaccination-content">
                                <div class="mb-2 w-100 ">
                                    <div class="mb-2 w-100">
                                        <label for="patient_name">Patient Name</label>
                                        <input type="text" class="form-control w-100 bg-light" disabled placeholder="Jan Louie Salimbago">
                                    </div>
                                </div>
                                <div class="mb-2 w-100">
                                    <div class="mb-2 w-100">
                                        <label for="patient_name">Administered By:</label>
                                        <input type="text" class="form-control w-100 bg-light" disabled placeholder="Nurse">
                                    </div>
                                </div>
                                <div class="mb-2 w-100 ">
                                    <div class="mb-2 w-100">
                                        <label for="date_of_vaccination">Date of Vaccination</label>
                                        <input type="date" id="date_of_vaccination" placeholder="20" class="form-control w-100 " name="date_of_vaccination" value="">
                                    </div>
                                </div>
                                <div class="mb-2 w-100">
                                    <div class="mb-2 w-100">
                                        <label for="time">Time</label>
                                        <input type="time" class="form-control" name="time_of_vaccination">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label for="vaccine_type">Vaccine Type:</label>
                                    <div class="mb-2 d-flex gap-2">
                                        <select name="vaccine_type" id="vaccine_type" class="form-select w-100">
                                            <option value="">Select Vaccine</option>
                                        </select>
                                        <button type="button" class="btn btn-success"> Add</button>
                                    </div>
                                </div>
                                <div class="mb-2 bg-secondary w-100 p-3 d-flex  flex-wrap rounded">
                                    <div class="vaccine d-flex justify-content-between bg-white align-items-center p-1 w-50 rounded">
                                        <p class="mb-0">Penta 1</p>
                                        <div class="delete-icon d-flex align-items-center justify-content-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                                                <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2 w-100">
                                    <label for="dose" class="w-100">Vaccine Dose Number:</label>
                                    <select id="dose" name="dose" required class="form-select">
                                        <option value="" disabled selected>Select Dose</option>
                                        <option value="1">1st Dose</option>
                                        <option value="2">2nd Dose</option>
                                        <option value="3">3rd Dose</option>
                                        <!-- <option value="booster">Booster</option> -->
                                    </select>
                                </div>
                                <div class="mb-2 w-100">
                                    <label for="remarks">Remarks*</label>
                                    <input type="text" class="form-control" id="remarks" name="remarks">
                                </div>

                            </div>
                            <div class="buttons w-100 align-self-center d-flex justify-content-end gap-2 mt-5">
                                <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
                                <button type="submit" class="btn btn-success px-5 py-2 fs-5">Save Record</button>
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
                        <div class="tb-dots d-none patient-type" id="tb-dots-con">
                            <h1>working tb</h1>
                            <div class="buttons w-75 align-self-center d-flex justify-content-end gap-2 mt-2">
                                <button type="button" class="btn btn-danger px-5 py-2 fs-5" onclick="prevStep()">Back</button>
                                <button type="button" class="btn btn-success px-5 py-2 fs-5" onclick="nextStep()">Next</button>
                            </div>
                        </div>
                        <div class="senior-citizen patient-type d-none flex-column align-self-center w-75 card shadow" id="senior-citizen-con">
                            @include('add_patient.senior-citizen.senior-citizen')
                        </div>
                        <!-- Family Planning -->
                        <div class="family-planning patient-type flex-grow-1 d-none flex-column align-items-center h-100 w-100" id="family-planning-con">
                            @include('add_patient.familyPlanning.step2')

                        </div>
                    </div>
                    <!-- STEP 3 PRENATAL -->
                    <div class="step d-none flex-column align-self-center w-100 h-100 rounded gap-1" id="step3">
                        <div id="prenatal-step3" class="d-none">@include('add_patient.prenatalPlanning')</div>
                        <div id="family-planning-step3" class="d-none flex-grow-1 d-none flex-column h-100 w-100"> @include('add_patient.familyPlanning.step3')</div>
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
        document.getElementById('date_of_vaccination').value = today;
    </script>
</body>

</html>