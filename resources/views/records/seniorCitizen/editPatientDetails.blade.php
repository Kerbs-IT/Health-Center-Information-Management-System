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
    'resources/js/senior_citizen/editPatientDetails.js'])
    <div class="patient-details vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column ">
            @include('layout.header')
            <div class="d-flex flex-grow-1 flex-column overflow-auto">
                <main class="flex-column p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center border-bottom">
                        <h2>Update Patient Details</h2>
                        <div class="sequence-links d-none d-md-flex justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('record.vaccination') }}" class="text-decoration-none fs-5 text-muted">Senior Citizen</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="/patient-record/senior-citizen/edit-details/{{$seniorCitizenRecord->id}}" class="text-decoration-none fs-5 text-black">Edit Patient Info</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-lg-5 px-md-3 px-1">
                        <a href="{{route('record.senior.citizen')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <form action="" method="post" class="d-flex flex-column align-items-center  justify-content-center rounded overflow-hidden bg-white py-2" id="edit-senior-form">
                            @method('PUT')
                            @csrf
                            <div class="step d-flex flex-column w-100 rounded  px-2">
                                <div class="info">
                                    <h4>Personal Info</h4>
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-wrap fk">
                                        <div class="input-field flex-fill">
                                            <input type="text" id="first_name" placeholder="First Name" class="form-control" name="first_name" value="{{optional($seniorCitizenRecord -> patient)->first_name??''}}">
                                            @error('first_name')
                                            <small class="text-danger" id="first_name_error">{{$message}}</small>
                                            @enderror

                                        </div>
                                        <div class="input-field flex-fill">
                                            <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="{{optional($seniorCitizenRecord -> patient)->middle_initial??''}}">
                                            @error('middle_initial')
                                            <small class="text-danger" id="middle_initial_error">{{$message}}</small>
                                            @enderror

                                        </div>
                                        <div class="input-field flex-fill">
                                            <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="{{optional($seniorCitizenRecord -> patient)->last_name??''}}">
                                            @error('last_name')
                                            <small class="text-danger" id="last_name_error">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-md-row flex-column">
                                        <!-- date of birth -->
                                        <div class="input-field md:w-[50%] w-full">
                                            <label for="birthdate">Date of Birth</label>
                                            <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5" name="date_of_birth" value="{{optional($seniorCitizenRecord -> patient)->date_of_birth?->format('Y-m-d')??''}}">
                                            @error('date_of_birth')
                                            <small class="text-danger" id="date_of_birth_error">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <!-- place of birth -->
                                        <div class="input-field md:w-[50%] w-full">
                                            <label for="place_of_birth">Place of Birth</label>
                                            <input type="text" id="place_of_birth" placeholder="20" class="form-control" name="place_of_birth" value="{{optional($seniorCitizenRecord -> patient)->place_of_birth??''}}">
                                            @error('place_of_birth')
                                            <small class="text-danger" id="place_of_birth_error">{{$message}}</small>
                                            @enderror
                                        </div>

                                        <!-- age -->
                                        <div class="input-field md:w-[50%] w-full">
                                            <label for="age">Age</label>
                                            <input type="number" id="age" placeholder="20" class="form-control" name="age" value="{{optional($seniorCitizenRecord -> patient)->age??''}}">
                                            @error('age')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1 flex-wrap flex-column flex-md-row">
                                        <div class="input-field flex-fill">
                                            <label for="sex">Sex</label>
                                            <div class="input-field d-flex align-items-center p-2">

                                                <div class="sex-input d-flex align-items-center justify-content-center w-100 gap-1">
                                                    <input type="radio" id="male" class="mb-0" name="sex" value="male" class="mb-0" {{ optional($seniorCitizenRecord->patient)->sex == 'male' ? 'checked' : '' }}>Male</label>
                                                    <input type="radio" id="female" class="mb-0" name="sex" value="female" class="mb-0" {{ optional($seniorCitizenRecord->patient)->sex == 'female' ? 'checked' : '' }}>Female</label>
                                                </div>
                                                @error('sex')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- contact -->
                                        <div class="input-field flex-fill">
                                            <label for="contact_number" class="">Contact Number</label>
                                            <input type="number" placeholder="+63-936-627-8671" class="form-control" name="contact_number" value="{{optional($seniorCitizenRecord -> patient)->contact_number??''}}">
                                            @error('contact_number')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <div class="input-field flex-fill">
                                            <label for="nationality" class="">Nationality</label>
                                            <input type="text" placeholder="ex. Filipino" class="form-control" name="nationality" value="{{optional($seniorCitizenRecord -> patient)->nationality??''}}">
                                            @error('nationality')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1 flex-wrap flex-column flex-md-row">
                                        <div class="input-field flex-fill">
                                            <label for="dateOfRegistration">Date of Registration</label>
                                            <input type="date" id="date_of_registration" placeholder="20" class="form-control text-center w-100 px-5 " name="date_of_registration" value="{{optional($seniorCitizenRecord -> patient)->date_of_registration??''}}">
                                            @error('date_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <!-- administered by -->
                                        <div class="mb-2 flex-fill">
                                            <label for="brgy">Administered by*</label>
                                            <select name="handled_by" id="handled_by" class="form-select " data-bs-health-worker-id="{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->health_worker_id??''}}">
                                                <option value="">Select a person</option>
                                            </select>
                                            @error('brgy')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- mother name and father -->
                                    <div class="mb-2 d-flex flex-column flex-md-row gap-1 flex-wrap flex-xl-row">
                                        <div class="mb-2 w-100 d-flex gap-2 flex-wrap flex-xl-row">
                                            <div class="input-field flex-fill">
                                                <label for="civil_status" class="">Civil Status</label>
                                                <select name="civil_status" id="civil_status" class="form-select">
                                                    <option value="Single" {{ optional($seniorCitizenRecord->patient)->civil_status == 'Single' ? 'selected' : '' }}>Single</option>
                                                    <option value="Married" {{ optional($seniorCitizenRecord->patient)->civil_status == 'Married' ? 'selected' : '' }}>Married</option>
                                                    <option value="Divorce" {{ optional($seniorCitizenRecord->patient)->civil_status == 'Divorce' ? 'selected' : '' }}>Divorce</option>
                                                </select>
                                                @error('civil_status')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                            <div class="input-field flex-fill">
                                                <label for="blood_type">Occupation</label>
                                                <input type="text" id="occupation" placeholder="Enter the Occupation" class="form-control" name="occupation" value="{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->occupation??''}}">
                                            </div>
                                            <div class="mb-3 flex-fill d-flex gap-2">
                                                <div class="input-field w-100">
                                                    <label for="motherName">Religion</label>
                                                    <input type="text" id="religion" placeholder="Enter the Religion" class="form-control" name="religion" value="{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->religion??''}}">
                                                    @error('mother_name')
                                                    <small class="text-danger">{{$message}}</small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="mb-2 flex-fill  d-flex flex-column">
                                                <label for=""> Member of Social Security System (SSS):</label>
                                                <div class="radio-input d-flex align-items-center justify-content-center w-100 gap-1 py-2">
                                                    <input type="radio" id="male" class="mb-0" name="SSS" value="Yes" class="mb-0" {{ optional($seniorCitizenRecord->senior_citizen_medical_record)->SSS == 'Yes' ? 'checked' : '' }}>
                                                    <label for="male">Yes</label>
                                                    <input type="radio" id="female" class="mb-0" name="SSS" value="No" class="mb-0" {{ optional($seniorCitizenRecord->senior_citizen_medical_record)->SSS == 'No' ? 'checked' : '' }}>
                                                    <label for="female">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- address -->
                                    <div class="mb-2 d-flex gap-1 flex-column">
                                        <h4>Address</h4>
                                        <div class="input-field d-flex gap-2 flex-column flex-md-row">
                                            <div class=" mb-2 w-full md:w-[50%]">
                                                <label for="street">Street*</label>
                                                <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control py-2" name="street" value="{{ trim($address->house_number . ' ' . optional($address->street)->name) }}">
                                                @error('street')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                            <div class="mb-2 w-full md:w-[50%]">
                                                <label for="brgy">Barangay*</label>
                                                <select name="brgy" id="brgy" class="form-select py-2" data-bs-selected-brgy="{{$address-> purok}}">
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
                                        <div class="mb-md-2 mb-0 input-field d-flex gap-3 w-100 first-row flex-wrap flex-column flex-md-row">
                                            <div class="mb-md-2 mb-0 flex-fill">
                                                <label for="BP">Blood Pressure:</label>
                                                <input type="text" class="form-control w-100" placeholder="ex. 120/80" name="blood_pressure" value="{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->blood_pressure??''}}">
                                            </div>
                                            <div class="mb-md-2 mb-0 flex-fill">
                                                <label for="BP">Temperature:</label>
                                                <input type="number" class="form-control w-100" placeholder="00 C" name="temperature" value="{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->temperature??''}}">
                                            </div>
                                            <div class="mb-md-2 mb-0 flex-fill">
                                                <label for="BP">Pulse Rate(Bpm):</label>
                                                <input type="text" class="form-control w-100" placeholder=" 60-100" name="pulse_rate" value="{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->pulse_rate??''}}">
                                            </div>

                                        </div>
                                        <!-- 2nd row -->
                                        <div class="mb-md-2 mb-0 input-field d-flex gap-3 w-100 second-row flex-wrap flex-column flex-md-row">
                                            <div class="mb-md-2 mb-0 flex-fill">
                                                <label for="BP">Respiratory Rate (breaths/min):</label>
                                                <input type="text" class="form-control w-100" placeholder="ex. 25" name="respiratory_rate" value="{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->respiratory_rate??''}}">
                                            </div>
                                            <div class="mb-md-2 mb-0 flex-fill">
                                                <label for="BP">Height(cm):</label>
                                                <input type="number" class="form-control w-100" placeholder="00.00" name="height" value="{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->height??''}}">
                                            </div>
                                            <div class="mb-md-2 mb-2 flex-fill">
                                                <label for="BP">Weight(kg):</label>
                                                <input type="number" class="form-control w-100" placeholder=" 00.00" name="weight" value="{{optional($seniorCitizenRecord -> senior_citizen_medical_record)->weight??''}}">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- save btn -->
                                <div class="save-record align-self-end mt-lg-5 mt-md-3 mt-2">
                                    <input type="submit" class="btn btn-success px-4 fs-5" value="Save Record" id="edit-save-btn" data-bs-medical-id="{{$seniorCitizenRecord-> id}}">
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
            const con = document.getElementById('record_senior_citizen');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>