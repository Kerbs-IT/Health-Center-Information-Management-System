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
    <div class="patient-details min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column">
            @include('layout.header')
            <div class="d-flex flex-grow-1 flex-column overflow-y-auto">
                <main class="flex-column p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center border-bottom">
                        <h2>Update Patient Details</h2>
                        <div class="sequence-links d-none d-md-flex justify-content-center align-items-center">
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
                    <div class="flex-grow-1 py-3 px-1 px-md-5 bg-white mx-md-3 mx-0 mt-2 rounded">
                        <a href="{{route('record.vaccination')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <form action="" method="post" class="d-flex flex-column align-items-center  justify-content-center rounded overflow-hidden" id="update-form">
                            @method('PUT')
                            @csrf
                            <div class="step d-flex flex-column w-100 rounded  px-2">
                                <div class="info">
                                    <h4>Personal Info</h4>
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-wrap flex-column flex-md-row">
                                        <div class="input-field flex-fill">
                                            <input type="text" id="first_name" placeholder="First Name" class="form-control" name="first_name" value="{{$info->first_name}}">
                                            @error('first_name')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror

                                        </div>
                                        <div class="input-field flex-fill">
                                            <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="{{$info->middle_initial}}">
                                            @error('middle_initial')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror

                                        </div>
                                        <div class="input-field flex-fill">
                                            <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="{{$info->last_name}}">
                                            @error('last_name')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-wrap flex-xl-nowrap flex-column flex-md-row">
                                        <!-- date of birth -->
                                        <div class="input-field xl:w-[50%] flex-fill">
                                            <label for="birthdate">Date of Birth</label>
                                            <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5" name="date_of_birth" value="{{optional($info)->date_of_birth?? ''}}">
                                            @error('date_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <!-- place of birth -->
                                        <div class="input-field xl:w-[50%] flex-fill">
                                            <label for="place_of_birth">Place of Birth</label>
                                            <input type="text" id="place_of_birth" placeholder="20" class="form-control" name="place_of_birth" value="{{optional($info)-> place_of_birth ?? 'none'}}">
                                            @error('place_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>

                                        <!-- age -->
                                        <div class="input-field xl:w-[50%] flex-fill">
                                            <label for="age">Age</label>
                                            <input type="text" id="age" placeholder="20" class="form-control" name="age" value="{{optional($info)-> age ?? 'none'}}">
                                            @error('age')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-wrap  flex-xl-nowrap flex-column flex-md-row">
                                        <div class="input-field xl:w-[50%] flex-fill">
                                            <label for="sex">Sex</label>
                                            <div class="input-field d-flex align-items-center p-2">
                                                <div class="sex-input d-flex align-items-center justify-content-center w-100 gap-1">
                                                    <input type="radio" id="male" class="mb-0" name="sex" value="male" class="mb-0" {{optional($info)-> sex == 'male'?'checked': ''}}>Male</label>
                                                    <input type="radio" id="female" class="mb-0" name="sex" value="female" class="mb-0" {{optional($info)-> sex == 'female'?'checked': ''}}>Female</label>
                                                </div>
                                                @error('sex')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- contact -->
                                        <div class="input-field xl:w-[50%] flex-fill">
                                            <label for="contact_number" class="">Contact Number</label>
                                            <input type="number" placeholder="+63-936-627-8671" class="form-control" name="contact_number" value="{{optional($info)-> contact_number ?? 'none'}}">
                                            @error('contact_number')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <div class="input-field xl:w-[50%] flex-fill">
                                            <label for="nationality" class="">Nationality</label>
                                            <input type="text" placeholder="ex. Filipino" class="form-control" name="nationality" value="{{optional($info)-> nationality ?? 'none'}}">
                                            @error('nationality')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-wrap flex-xl-nowrap flex-column flex-md-row">
                                        <div class="input-field xl:w-[50%] flex-fill">
                                            <label for="dateOfRegistration">Date of Registration</label>
                                            <input type="date" id="dateOfRegistration" placeholder="20" class="form-control text-center w-100 px-5 " name="date_of_registration" value="{{optional($info)-> created_at?->format('Y-m-d') ?? ''}}">
                                            @error('date_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <!-- administered by -->
                                        <div class="mb-md-2 mb-0 xl:w-[50%] flex-fill">
                                            <label for="healthWorkersDropDown">Handled by*</label>
                                            <select name="handled_by" id="healthWorkersDropDown" class="form-select" data-bs-selected-Health-Worker="{{optional($info->medical_record_case[0]->vaccination_medical_record)->health_worker_id ?? 'N/A'}}">
                                                <option value="" selected disabled>Select a person</option>
                                            </select>
                                            @error('brgy')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- mother name and father -->
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-wrap flex-column flex-md-row">
                                        <div class="input-field flex-fill">
                                            <label for="motherName">Mother Name</label>
                                            <input type="text" id="mother_name" placeholder="mother name" class="form-control" name="mother_name" value="{{optional($info->medical_record_case[0]->vaccination_medical_record)->mother_name ?? 'N/A'}}">
                                            @error('mother_name')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror

                                        </div>
                                        <div class="input-field flex-fill">
                                            <label for="fatherName">Father Name</label>
                                            <input type="text" id="fatherName" placeholder="Father Name" class="form-control" name="father_name" value="{{optional($info->medical_record_case[0]->vaccination_medical_record)->father_name ?? 'N/A'}}">
                                            @error('middle_initial')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- address -->
                                    <div class="mb-md-2 mb-0 d-flex gap-1 flex-column">
                                        <h4>Address</h4>
                                        <div class="input-field d-flex gap-2 flex-wrap flex-xl-nowrap flex-column flex-md-row w-100">
                                            <div class=" mb-md-2 mb-0 xl:w-[50%] flex-fill ">
                                                <label for="street">Street*</label>
                                                <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control py-2 text-wrap" name="street" value="{{$street??'none'}}">
                                                @error('street')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                            <div class="mb-md-2 mb-0 xl:w-[50%] flex-fill">
                                                <label for="brgy">Barangay*</label>
                                                <select name="brgy" id="brgy" class="form-select py-2" data-bs-purok="{{optional($address)->purok??'none'}}">
                                                    <option value="" selected disabled>Select a brgy</option>
                                                </select>
                                                @error('brgy')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <!-- vital sign -->
                                    <div class="vital-sign w-100 flex-wrap flex-column flex-md-row">
                                        <div class="mb-md-2 mb-0 input-field d-flex gap-3 w-100 third-row flex-column flex-md-row">
                                            <div class="mb-md-2 mb-0 xl:w-[50%] flex-fill">
                                                <label for="BP">Birth Height(cm):</label>
                                                <input type="number" class="form-control w-100" placeholder="00.00" name="vaccination_height" value="{{optional($info->medical_record_case[0]->vaccination_medical_record)->birth_height ?? 'N/A'}}">
                                            </div>
                                            <div class="mb-md-2 mb-0 xl:w-[50%] flex-fill">
                                                <label for="BP">Birth Weight(kg):</label>
                                                <input type="text" class="form-control w-100" placeholder=" 00.00" name="vaccination_weight" value="{{optional($info->medical_record_case[0]->vaccination_medical_record)->birth_weight ?? 'N/A'}}">
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