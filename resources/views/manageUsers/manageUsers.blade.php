<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/css/patient/record.css',
    'resources/js/login.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/healthWorker.css',
    'resources/css/profile.css',
    'resources/js/manageUser/manageUser'])
    @include('sweetalert::alert')
    <div class="ms-0 ps-0 d-flex w-100" style="height: 100vh;">
        <!-- aside contains the sidebar menu -->
        <div class="d-flex w-100">
            <aside>
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
            <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class="flex-grow-1">
                <header class=" d-flex align-items-center px-3 ">
                    <nav class="d-flex justify-content-between align-items-center w-100 ">
                        <div class="left-side d-flex gap-2">
                            <button class="btn hamburger d-lg-block fs-6 mx-1" id="toggleSidebar">
                                <i class="fa-solid fa-bars fs-2"></i>
                            </button>
                            <h1 class="mb-0"> {{$page ?? Welcome}}</span></h1>
                        </div>
                        <div class="profile-con position-relative justify-content-space d-flex align-items-center gap-2" style="min-width: 150px;">
                            <img src="{{ optional(Auth::user()->nurses)->profile_image 
                        ? asset(optional(Auth::user()->nurses)->profile_image) 
                        : (optional(Auth::user()->staff)->profile_image 
                            ? asset(optional(Auth::user()->staff)->profile_image) 
                            : asset('images/default_profile.png')) }}" alt="profile picture" class="profile-img" id="profile_img">
                            <div class="username-n-role">
                                <h5 class="mb-0">{{ optional(Auth::user()->nurses)->full_name
                                                    ?? optional(Auth::user()->staff)->full_name
                                                    ?? 'none'  }}</h5>
                                <h6 class="mb-0 text-muted fw-light">{{Auth::user() -> role ?? 'none';}}</h6>
                            </div>
                            <div class="links position-absolute flex-column top-17 w-100 bg-white" id="links">
                                <a href="{{ route('page.profile') }}" class="text-decoration-none text-black">view profile</a>
                                <a href="{{route('logout')}}" class="text-decoration-none text-black">Logout</a>
                            </div>
                        </div>
                    </nav>
                </header>
                <main class=" overflow-y-auto max-h-[calc(100vh-100px)] px-3">
                  

                    <div class="records">
                        <livewire:patient-account-binding />
                    </div>
                </main>

            </div>
        </div>
    </div>

    <div class="pop-up  w-100 h-100 d-none align-items-center justify-content-center" id="pop-up">
        <form action="" method="post" class="p-3 gap-3 w-50 d-flex opacity-[1]" enctype="multipart/form-data" id="profile-form">
            @csrf
            <!-- profile image section -->
            <div class="profile-image p-1  mb-3 d-flex flex-column align-items-center" style="min-width:280px;">
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
                <h4>Personal Info</h4>
                <div class="mb-2 d-flex gap-1">
                    <div class="input-field w-50">
                        <input type="text" id="first_name" placeholder="First Name" class="form-control" name="first_name" value="">
                        <small class="text-danger" id="fname-error"></small>
                    </div>
                    <div class="input-field w-50">
                        <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="">
                        <small class="text-danger" id="middle-initial-error"></small>
                    </div>
                    <div class="input-field w-50">
                        <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="">
                        <small class="text-danger" id="lname-error"></small>
                    </div>
                </div>
                <!-- age -->
                <div class="mb-2 d-flex gap-1">
                    <div class="input-field w-50">
                        <label for="age">Age</label>
                        <input type="text" id="age" placeholder="20" class="form-control" name="age" value="">
                        <small class="text-danger" id="age-error"></small>
                    </div>
                    <div class="input-field w-50">
                        <label for="birthdate">Date of Birth</label>
                        <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5" name="date_of_birth" value="">
                        <small class="text-danger" id="birthdate-error"></small>
                    </div>
                    <div class="input-field w-25">
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
                </div>
                <!-- civil status, contact number, nationality -->
                <div class="mb-2 d-flex gap-1">
                    <div class="input-field w-50">
                        <label for="civil_status" class="">Civil Status</label>
                        <!-- to display the current status -->

                        <select name="civil_status" id="civil_status" class="form-select">
                            <option value="single">Single</option>
                            <option value="married">Married</option>
                            <option value="divorce">Divorce</option>
                        </select>
                        <small class="text-danger" id="civil-status-error"></small>
                    </div>
                    <!-- contact -->
                    <div class="input-field w-50">
                        <label for="contact_number" class="">Contact Number</label>
                        <input type="number" placeholder="+63-936-627-8671" class="form-control" id="contact_num" name="contact_number" value="">
                        <small class="text-danger" id="contact-error"></small>
                    </div>
                    <div class="input-field w-50">
                        <label for="nationality" class="">Nationality</label>
                        <input type="text" placeholder="ex. Filipino" class="form-control" id="nationality" name="nationality" value="">
                        <small class="text-danger" id="nationality-error"></small>
                    </div>
                </div>
                <div class="mb-2 d-flex gap-1">
                    <!-- username -->
                    <div class="input-field w-50">
                        <label for="username" class="">Username</label>
                        <input type="text" placeholder="ex. yato" id="username" class="form-control" name="username" value="">
                        <small class="text-danger" id="username-error"></small>
                    </div>
                    <!-- email -->
                    <div class="input-field w-50">
                        <label for="email" class="">Email</label>
                        <input type="email" placeholder="ex. yato" id="email" class="form-control" name="email" value="">
                        <small class="text-danger" id="email-error"></small>
                    </div>
                    <!-- password -->
                    <div class="input-field w-50">
                        <label for="password" class="">Password</label>
                        <input type="password" id="edit_password" class="form-control" name="password">
                        <small class="text-muted">Leave blank if you don't want to change it.</small>
                        <small class="text-danger"></small>
                    </div>
                </div>
                <!-- address -->
                <div class="mb-3 w-100" id="patient_type_con">
                    <label for="patient_type" class="form-label text-nowrap ">Patient Address </label>

                    <div class=" w-100 d-flex gap-2">
                        <div class="items w-50">
                            <label for="patient_street" class="w-100 text-muted">Blk & lot,Street*</label>
                            <input type="text" id="update_blk_n_street" name="blk_n_street" placeholder="enter the blk & lot & street seperated by ','" class="w-100 form-control">
                            @error('blk_n_street')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="items w-50">
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
                <!-- save button -->
                <div class="mb-2 d-flex justify-content-end gap-2">
                    <button class="btn btn-danger px-4" id="cancel-btn">Cancel</button>
                    <input type="submit" value="Save" class="btn btn-success px-4" id="submit-btn" data-user>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addModalLabel">Add New Health Worker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <form action="" method="POST" class="rounded shadow d-flex flex-column align-items-center p-4  w-sm-25 w-md-50 w-lg-25 bg-white" id="add-patient-form">
                        @csrf
                        <!-- username -->
                        <div class="mb-2 w-100">
                            <label for="username" class="mb-1 h4 fs-4">Username:</label>
                            <input type="text" placeholder="Enter your username" name="username" class="py-2 px-2 w-100 fs-5 bg-light" autocomplete="off" value="{{old('username')}}">
                            <small class="text-danger username-error"></small>
                        </div>
                        <!-- full name -->
                        <div class="mb-2 w-100">
                            <label for="" class="mb-1 h4 fs-4">Personal Info:</label>
                            <div class="gap-2 d-flex justify-content-center">
                                <input type="text" placeholder="First Name" name="first_name" class="py-2 px-2 fs-5 bg-light w-50" autocomplete="off" style="width:200px;" value="{{old('first_name')}}">
                                <input type="text" placeholder="Middle Initial" name="middle_initial" class="py-2 px-2 fs-5 bg-light w-50" autocomplete="off" style="width:200px;" value="{{old('middle_initial')}}">
                                <input type=" text" placeholder="Last Name" name="last_name" class="py-2 px-2 fs-5 bg-light w-50" autocomplete="off" style="width:200px;" value="{{old('last_name')}}">
                            </div>
                            <small class="text-danger fname-error"></small>
                            <small class="text-danger middle-initial-error"></small>
                            <small class="text-danger lname-error"></small>
                        </div>
                        <!-- email -->
                        <div class="mb-2 w-100">
                            <label for="email" class="mb-1 h4 fs-4">Email:</label>
                            <input type="email" placeholder="Enter your email" name="email" class="py-2 px-2 w-100 fs-5 bg-light" value="{{old('email')}}">
                            <small class="text-danger email-error"></small>
                        </div>
                        <!-- Password -->
                        <div class="mb-3 w-100">
                            <label for="password" class="mb-1 h4 fs-4 w-100">Password:</label>
                            <div class="input-pass d-flex align-items-center">
                                <input type="password" placeholder="Enter your password" name="password" class="py-2 px-2 w-100 fs-5 bg-light" id="password" autocomplete="off" value="{{old('password')}}">
                                <i class="fa-solid fa-eye p-3 bg-primary text-white" id="eye-icon"></i>
                            </div>
                            <small class="text-danger password-error"></small>
                        </div>
                        <!-- retype pass -->
                        <div class="mb-3 w-100">
                            <label for="re-type-pass" class="mb-1 font-weight-normal h4">Retype password:</label>
                            <div class="input-pass d-flex align-items-center">
                                <input type="password" placeholder="Re-type-pass" name="password_confirmation" class="py-2 px-2 w-100 fs-5 bg-light" id="re-type-pass">
                                <i class="fa-solid fa-eye p-3 bg-primary text-white" id="Retype-eye-icon"></i>
                            </div>
                            <small class="text-danger"></small>
                        </div>
                        <!-- hidden input -->
                        <input type="text" name="role" value="patient" hidden>
                        <!-- patient address -->
                        <div class="mb-3 w-100 " id="patient_type_con">
                            <label for="patient_type" class="form-label text-nowrap fs-4 fw-bold">Patient Address </label>

                            <div class=" w-100 d-flex gap-2">
                                <div class="items w-50">
                                    <label for="patient_street" class="w-100 text-muted">Blk & lot,Street*</label>
                                    <input type="text" id="blk_n_street" name="blk_n_street" placeholder="enter the blk & lot & street seperated by ','" class="w-100 form-control">
                                    <small class="text-danger blk-n-street-error"></small>
                                </div>
                                <div class="items w-50">
                                    <label for="patient_purok_dropdown">Puroks*</label>
                                    <select id="patient_purok_dropdown" class="form-select" name="patient_purok_dropdown" required>
                                        <option value="" selected disabled>Select a purok</option>
                                    </select>

                                    <small class="text-danger purok-dropdown-error"></small>
                                </div>
                            </div>
                        </div>
                        <!-- RECOVERY QUESTION -->
                        <div class="mb-3 w-100">
                            <div class="input-group w-100">
                                <label for="recovery_question" class="fs-4 fw-bold w-100">Recovery Question:</label>
                                <select name="recovery_question" id="recovery_question" class="form-select mb-2 w-100" required>
                                    <option value="">Select a question</option>
                                    <option value="1">What is your nickname? </option>
                                    <option value="2">What is the ame of your mother?</option>
                                    <option value="3">What is the name of your pet? </option>
                                </select>
                                <small class="text-danger recovery-question-error"></small>
                                <input type="text" name="recovery_answer" placeholder="Enter your answer" class="form-control w-100" required>
                                <small class="text-danger recovery-answer-error"></small>
                            </div>
                        </div>


                        <div class="mb-3 w-95">
                            <input type="submit" value="Register Patient Account" class="d-block btn btn-success py-1 m-auto fw-bold fs-5" id="add-patient-submit-btn">
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
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('manage_user');

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


</body>

</html>