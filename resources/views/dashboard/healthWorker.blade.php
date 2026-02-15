<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
    <style>
        .swap-icon-con {
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .swap-icon-con:hover {
            background-color: rgba(23, 162, 184, 0.1);
        }

        .action-icon {
            width: 18px;
            height: 18px;
        }
    </style>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/css/patient/record.css',
    'resources/js/header.js',
    'resources/css/healthWorker.css',
    'resources/js/healthWorker.js',
    'resources/css/profile.css',
    'resources/js/login.js'])
    @include('sweetalert::alert')
    <div class="ms-0 ps-0 d-flex w-100  min-vh-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-lg-flex w-100">
            <aside>
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
            <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class="flex-grow-1">
                @include('layout.header')
                <main class="m-3 overflow-auto max-h-[calc(100vh-100px)] p-0 p-md-3">
                    <div class="header-text d-flex justify-content-between align-items-center">
                        <h2>Manage Health Workers</h2>
                        <div class="end-button d-flex flex-column flex-md-row gap-2">
                            <button type="button" class="btn btn-success text-nowrap" id="add-health-worker-modal" data-bs-toggle="modal" data-bs-target="#addModal">
                                Add Health Worker
                            </button>
                        </div>
                    </div>

                    <livewire:manage-health-worker />
                </main>

            </div>
        </div>
    </div>

    <!-- Modal page -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white d-flex justify-content-between">
                    <h5>Edit Profile</h5>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fs-5 fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="bg-light border-start  border-primary px-3 py-2 mb-4 rounded">
                        <span class="fs-6">
                            <strong>Note:</strong>
                            <span class="text-danger">*</span>
                            <span class="fw-light"> indicates a required field.</span>
                        </span>
                    </div>
                    <form action="" method="post" class="d-flex gap-3 p-3 flex-wrap" enctype="multipart/form-data" id="profile-form">
                        @method('PUT')
                        @csrf

                        <!-- profile image section -->
                        <div class="d-flex flex-column align-items-center border-end p-2 flex-fill">
                            <img src="" alt="profile picture" class="profile-section-image rounded-circle" id="profile-image" data-base-url="{{ asset('') }}">
                            <h3 class=""></h3>
                            <h5 class="mb-3 text-muted text-capitalize fw-normal" id="full_name"></h5>
                            <div class="upload-image d-flex flex-column">
                                <label for="fileInput" class="btn mb-2 btn-success justify-self-center ">Update Profile</label>
                                <input type="file" name="profile_image" class="d-none w-100" id="fileInput" onchange="showFileName(this)">
                                <span id="fileName" class="text-center text-muted">No file choosen</span>
                                <small class="text-danger edit-healthworker-info" id="image-error"></small>
                            </div>
                        </div>
                        <!-- USER INFORMATION -->
                        <div class="flex-fill">
                            <h6>Personal Info</h6>
                            <div class="mb-2 d-flex gap-1 flex-wrap">
                                <div class="input-field flex-fill">
                                    <label for="first_name" class="">First Name<span class="text-danger">*</span></label>
                                    <input type="text" id="first_name" placeholder="Enter First Name" class="form-control" name="first_name" value="">
                                    <small class="text-danger edit-healthworker-info" id="fname-error"></small>
                                </div>
                                <div class="input-field flex-fill">
                                    <label for="middle_initial" class="">Middle Name</label>
                                    <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="">
                                    <small class="text-danger edit-healthworker-info" id="middle-initial-error"></small>
                                </div>
                                <div class="input-field flex-fill">
                                    <label for="last_name" class="">Last Name<span class="text-danger">*</span></label>
                                    <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="">
                                    <small class="text-danger edit-healthworker-info" id="lname-error"></small>
                                </div>
                                <div class="input-field flex-fill">
                                    <label for="edit_suffix" class="">Suffix</label>
                                    <select name="edit_suffix" id="edit_suffix" class="form-select responsive-input py-2">
                                        <option value="" selected>Select Suffix</option>
                                        <option value="Jr.">Jr</option>
                                        <option value="Sr.">Sr</option>
                                        <option value="II.">II</option>
                                        <option value="III.">III</option>
                                        <option value="IV.">IV</option>
                                        <option value="V.">V</option>
                                    </select>
                                    <small class="text-danger edit-healthworker-info" id="edit-suffix-error"></small>
                                </div>
                            </div>
                            <!-- age -->
                            <div class="mb-2 d-flex gap-1 flex-wrap flex-xl-nowrap flex-md-row flex-column">
                                <div class="input-field xl:w-[50%] flex-fill">
                                    <label for="age">Age</label>
                                    <input type="text" id="age" placeholder="Enter the age" class="form-control" disabled value="">
                                    <input type="hidden" id="hiddenAge" name="age">
                                    <small class="text-danger edit-healthworker-info" id="age-error"></small>
                                </div>
                                <div class="input-field xl:w-[50%] flex-fill">
                                    <label for="birthdate">Date of Birth<span class="text-danger">*</span></label>
                                    <input type="date" id="birthdate" class="form-control w-100 px-5" name="date_of_birth" value="" min="1950-01-01" max="{{date('Y-m-d',strtotime('-18 years'))}}">
                                    <small class="text-danger edit-healthworker-info" id="birthdate-error"></small>
                                </div>
                                <div class="input-field xl:w-[50%] flex-fill">
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
                                        <small class="text-danger edit-healthworker-info" id="sex-error"></small>
                                    </div>
                                </div>
                            </div>
                            <!-- civil status, contact number, nationality -->
                            <div class="mb-2 d-flex gap-1 flex-wrap flex-xl-nowrap">
                                <div class="input-field xl:w-[50%] flex-fill">
                                    <label for="civil_status" class="">Civil Status</label>
                                    <!-- to display the current status -->

                                    <select name="civil_status" id="civil_status" class="form-select">
                                        <option value="single">Single</option>
                                        <option value="married">Married</option>
                                        <option value="divorce">Divorce</option>
                                    </select>
                                    <small class="text-danger edit-healthworker-info" id="civil-status-error"></small>
                                </div>
                                <!-- contact -->
                                <div class="input-field xl:w-[50%] flex-fill">
                                    <label for="contact_number" class="">Contact Number<span class="text-danger">*</span></label>
                                    <input type="number" placeholder="Enter the contact number" class="form-control" id="contact_num" name="contact_number" value="">
                                    <small class="text-danger edit-healthworker-info" id="contact-error"></small>
                                </div>
                                <div class="input-field xl:w-[50%] flex-fill">
                                    <label for="nationality" class="">Nationality</label>
                                    <input type="text" placeholder="Enter the nationality" class="form-control" id="nationality" name="nationality" value="">
                                    <small class="text-danger edit-healthworker-info" id="nationality-error"></small>
                                </div>
                            </div>
                            <div class="mb-2 d-flex gap-1 flex-wrap flex-nowrap">

                                <!-- email -->
                                <div class="input-field xl:w-[50%] flex-fill">
                                    <label for="email" class="w-100">Email<span class="text-danger">*</span></label>
                                    <input type="email" placeholder="Enter the email" id="email" class="form-control" name="email" value="">
                                    <small class="text-danger edit-healthworker-info" id="email-error"></small>
                                </div>
                                <div class="input-fieldxl:w-[50%] flex-fill">
                                    <label for="reset_password" class="w-100">Password</label>
                                    <button type="button" class="btn btn-danger text-white px-2 py-2 flex-grow-1 fs-6" id="reset_password">Reset Password</button>
                                </div>

                            </div>

                            <!-- address -->
                            <div class="mb-2 d-flex gap-1 flex-column flex-wrap w-100">
                                <label>Address</label>
                                <div class="input-field d-flex gap-2 flex-column flex-md-row">
                                    <input type="text" placeholder="Blk & Lot n Street" class="form-control" name="street" id="blk_n_street" value="">
                                    <small class="text-danger edit-healthworker-info" id="street-error"></small>
                                    <div class="postal xl:w-[50%] flex-fill">
                                        <label for="postal">Postal Code</label>
                                        <input type="number" placeholder="Enter postal code" name="postal_code" id="postal_code" class="form-control" value="">
                                        <small class="text-danger" id="postal-error"></small>
                                    </div>

                                </div>
                                <div class="input-field d-flex gap-2 flex-wrap flex-column flex-md-row">
                                    <!-- region -->
                                    <div class="mb-2 xl:w-[50%] flex-fill">
                                        <label for="region">Region</label>
                                        <select name="region" id="region" class="form-select" data-selected="">
                                            <option value="" dissabled hidden>Select a region</option>
                                        </select>
                                        <small class="text-danger edit-healthworker-info" id="region-error"></small>
                                    </div>
                                    <!-- province -->
                                    <div class="mb-2 xl:w-[50%] flex-fill">
                                        <label for="province">Province</label>
                                        <select name="province" id="province" class="form-select" disabled data-selected="">
                                            <option value="">Select a province</option>
                                        </select>
                                        <small class="text-danger edit-healthworker-info" id="province-error"></small>
                                    </div>
                                </div>

                                <!-- city n brgy -->
                                <div class="input-field d-flex gap-2 flex-wrap flex-column flex-md-row">
                                    <!-- city -->
                                    <div class="mb-2 flex-fill">
                                        <label for="city">City</label>
                                        <select name="city" id="city" class="form-select" disabled data-selected="">
                                            <option value="">Select a city</option>
                                        </select>
                                        <small class="text-danger edit-healthworker-info" id="city-error"></small>
                                    </div>
                                    <!-- brgy -->
                                    <div class="mb-2 flex-fill">
                                        <label for="brgy">Barangay</label>
                                        <select name="brgy" id="brgy" class="form-select" disabled data-selected="">
                                            <option value="">Select a brgy</option>
                                        </select>
                                        <small class="text-danger edit-healthworker-info" id="brgy-error"></small>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <!-- save button -->
                            <div class="mb-2 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal" id="cancel-btn">Cancel</button>
                                <input type="submit" value="Save" class="btn btn-success px-4" id="submit-btn" data-user>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <!-- add health worker -->

    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-success text-white d-flex justify-content-between">
                    <h5 class="modal-title" id="addModalLabel">Add New Health Worker</h5>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fs-5 fa-xmark"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <form action="" method="POST" id="add-health-worker-form" class="rounded shadow-none shadow-md-flex d-flex flex-column flex-wrap align-items-start align-items-md-center  p-0 p-md-4  w-sm-25 w-md-50 w-lg-25 bg-white">
                        @csrf
                        <!-- full name -->
                        <div class="mb-2 w-100">
                            <div class="bg-light border-start  border-primary px-3 py-2 mb-4 rounded">
                                <span class="fs-6">
                                    <strong>Note:</strong>
                                    <span class="text-danger">*</span>
                                    <span class="fw-light"> indicates a required field.</span>
                                </span>
                            </div>
                            <div class="row g-2">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <label for="add_first_name_healthworker" class="">First Name<span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter First Name" id="add_first_name_healthworker" name="first_name" class="py-2 px-2 responsive-input rounded form-control" autocomplete="off" value="{{old('first_name')}}">
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <label for="add_middle_name_healthworker" class="">Middle Name</label>
                                    <input type="text" placeholder="Enter Middle Name" id="add_middle_name_healthworker" name="middle_initial" class="py-2 px-2 responsive-input rounded form-control" autocomplete="off" value="{{old('middle_initial')}}">
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <label for="add_last_name_healthworker" class="">Last Name<span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter Last Name" id="add_last_name_healthworker" name="last_name" class="py-2 px-2 responsive-input rounded form-control" autocomplete="off" value="{{old('last_name')}}">
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <label for="add_suffix" class="">Suffix</label>
                                    <select name="add_suffix" id="add_suffix" class="form-select responsive-input py-2">
                                        <option value="" selected>Select Suffix</option>
                                        <option value="Jr.">Jr</option>
                                        <option value="Sr.">Sr</option>
                                        <option value="II.">II</option>
                                        <option value="III.">III</option>
                                        <option value="IV.">IV</option>
                                        <option value="V.">V</option>
                                    </select>
                                    <small class="text-danger add-healthworker-error" id="add_suffix_error"></small>
                                </div>
                            </div>
                            <small class="text-danger add-healthworker-error" id="first_name_error"></small>
                            <small class="text-danger middle-initial-error add-healthworker-error" id="middle_initial_error"></small>
                            <small class="text-danger lname-error add-healthworker-error" id="last_name_error"></small>
                        </div>
                        <!-- email -->
                        <div class="mb-2 w-100">
                            <label for="email" class="mb-1 responsive-label">Email<span class="text-danger">*</span></label>
                            <input type="email" placeholder="Enter the email" name="email" class="py-2 px-2 w-100 responsive-input rounded form-control" value="{{old('email')}}">
                            <small class="text-danger email-error add-healthworker-error" id="email_error"></small>
                        </div>
                        <!-- date of birth -->
                        <div class="mb-2 w-100">
                            <label for="add_date_of_birth" class="mb-1 responsive-label">Date of Birth<span class="text-danger">*</span></label>
                            <input type="date" placeholder="Enter the date_of_birth" id="add_date_of_birth" name="add_date_of_birth" class="py-2 px-2 w-100 responsive-input rounded form-control" value="{{old('date_of_birth')}}" min="1950-01-01" max="{{date('Y-m-d',strtotime('-18 years'))}}">
                            <small class="text-danger add_date_of_birth-error add-healthworker-error" id="add_date_of_birth_error"></small>
                        </div>
                        <!-- contact number -->
                        <div class="mb-2 w-100">
                            <label for="add_contact_number" class="mb-1 responsive-label">Contact Number<span class="text-danger">*</span></label>
                            <input type="number" placeholder="Enter the contact number" id="add_contact_number" name="add_contact_number" class="py-2 px-2 w-100 responsive-input rounded form-control" value="{{old('contact_number')}}" min="1950-01-01" max="{{date('Y-m-d',strtotime('-18 years'))}}">
                            <small class="text-danger add_contact_number-error add-healthworker-error" id="add_contact_number_error"></small>
                        </div>
                        <!-- Password -->
                        <div class="mb-3 w-100">
                            <label for="password" class="mb-1  w-100">Password<span class="text-danger">*</span></label>
                            <div class="input-pass d-flex align-items-center">
                                <input type="password" placeholder="Enter the password" name="password" class="py-2 px-2 w-100 fs-5 bg-light" id="add_password" autocomplete="off" value="{{old('password')}}">
                                <i class="fa-solid fa-eye p-3 bg-primary text-white transition transform hover:scale-110 duration-200 rounded hover:shadow-lg" id="eye-icon"></i>
                            </div>
                            <small class="text-danger password-error add-healthworker-error" id="password_error"></small>
                        </div>
                        <!-- retype pass -->
                        <div class="mb-3 w-100">
                            <label for="re-type-pass" class="mb-1 font-weight-normal">Confirm password<span class="text-danger">*</span></label>
                            <div class="input-pass d-flex align-items-center">
                                <input type="password" placeholder="Confirm the password" name="password_confirmation" class="py-2 px-2 w-100 fs-5 bg-light" id="add_re-type-pass">
                                <i class="fa-solid fa-eye p-3 bg-primary text-white transition transform hover:scale-110 duration-200 rounded hover:shadow-lg" id="Retype-eye-icon"></i>
                            </div>
                            <small class="text-danger add-healthworker-error" class="password-confirmation-error" id="password-confirmation-error"></small>
                        </div>
                        <input type="hidden" name="role" value="staff">
                        <!-- STAFF -->
                        <div class="mb-3 w-100 staff-fields " id="staff-fields">
                            <div class="mb-3 w-100 d-block gap-2">
                                <div class="input-group w-100 d-block gap-2 align-items-center">
                                    <label for="role" class="responsive-label m-0">Assigned Area<span class="text-danger">*</span></label>
                                    <select name="assigned_area" id="assigned_area" class="border py-2 px-3 form-select w-100" data-occupied-areas='@json($occupied_assigned_areas)'>
                                        <option value="">Select an Area</option>
                                    </select>
                                    <small class="text-danger assigned-area-error add-healthworker-error" id="assigned_area_error"></small>
                                </div>
                            </div>
                        </div>
                        <!-- RECOVERY QUESTION -->


                        <div class="mb-3 w-95">
                            <input type="submit" value="Register Healthworker" class=" fs-5 d-block btn btn-success py-1 responsive-btn m-auto" id="add-Health-worker">
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div> -->

            </div>
        </div>
    </div>

    <!-- swap -->
    @include('healthWorkerSwapArea.swap-area')
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('healthWorker');

            if (con) {
                con.classList.add('active');
            }
        })

        function showFileName(input) {
            const fileName = input.files.length ? input.files[0].name : "No file chosen";
            document.getElementById("fileName").textContent = fileName;
        }
    </script>
    @endif
    <!-- eye icon -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const passwordInput = document.getElementById("add_password");
            const retypeInput = document.getElementById("add_re-type-pass");
            const eyeIcon = document.getElementById("eye-icon");
            const retypeEyeIcon = document.getElementById("Retype-eye-icon");
            const passwordEditInput = document.getElementById('password');

            function togglePassword(input, icon) {
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    input.type = "password";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            }

            eyeIcon.addEventListener("click", function() {
                togglePassword(passwordInput, eyeIcon);
            });

            retypeEyeIcon.addEventListener("click", function() {
                togglePassword(retypeInput, retypeEyeIcon);
            });


        });
    </script>
</body>

</html>