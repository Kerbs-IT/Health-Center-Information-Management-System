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
    'resources/css/manageUsers/manageUsers.css',
    'resources/js/manageUser/manageUser.js'])
    @include('sweetalert::alert')
    <div class="ms-0 ps-0 d-flex w-100 min-vh-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-flex w-100">
            <aside>
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
            <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class="flex-grow-1 overflow-x-auto">
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


    <div class="modal fade" id="edit-user-profile" tabindex="-1" aria-labelledby="editUserProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Edit User Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" class="p-3" enctype="multipart/form-data" id="profile-form">
                        @csrf
                        <div class="bg-light border-start  border-primary px-3 py-2 mb-4 rounded w-100">
                            <span class="fs-6">
                                <strong>Note:</strong>
                                <span class="text-danger">*</span>
                                <span class="fw-light"> indicates a required field.</span>
                            </span>
                        </div>
                        <div class="row g-3">
                            <!-- Profile Image Section -->
                            <div class="col-12 col-lg-4">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center">
                                        <img src="{{ asset('images/default_profile.png') }}"
                                            alt="profile picture"
                                            class="rounded-circle mb-3 justify-self-center"
                                            id="edit_profile_image"
                                            data-base-url="{{ asset('') }}"
                                            style="width: 150px; height: 150px; object-fit: cover;">

                                        <h5 class="mb-2 text-capitalize" id="full_name">Full Name</h5>
                                        <div class="mt-3">
                                            <label for="fileInput" class="btn btn-success w-100 mb-2">Update Profile</label>
                                            <input type="file" name="profile_image" class="d-none" id="fileInput" onchange="showFileName(this)">
                                            <small class="text-muted d-block" id="fileName">No file chosen</small>
                                            <small class="text-danger d-block" id="image-error"></small>
                                        </div>
                                        <div class="input-fieldxl:w-[50%] flex-fill">

                                            <button type="button" class="btn btn-danger text-white px-2 py-2 flex-grow-1 fs-6" id="reset_password">Reset Password</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Information Section -->
                            <div class="col-12 col-lg-8">
                                <h4 class="mb-3">Personal Info</h4>

                                <!-- Name Fields -->
                                <div class="row g-2 mb-3">
                                    <div class="col-12 col-md-3">
                                        <label for="edit_first_name" class="w-100">First Name<span class="text-danger">*</span></label>
                                        <input type="text" id="edit_first_name" placeholder="Enter First Name" class="form-control" name="first_name" value="">
                                        <small class="text-danger" id="fname-error"></small>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="edit_middle_initial" class="w-100">Middle Name</label>
                                        <input type="text" id="edit_middle_initial" placeholder="Enter Middle Name" class="form-control" name="middle_initial" value="">
                                        <small class="text-danger" id="middle-initial-error"></small>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="edit_last_name" class="w-100">Last Name<span class="text-danger">*</span></label>
                                        <input type="text" id="edit_last_name" placeholder="Enter Last Name" class="form-control" name="last_name" value="">
                                        <small class="text-danger" id="lname-error"></small>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="edit_suffix" class="w-100">Suffix</label>
                                        <select name="edit_suffix" id="edit_suffix" class="form-select responsive-input py-2">
                                            <option value="" selected>Select Suffix</option>
                                            <option value="Jr.">Jr</option>
                                            <option value="Sr.">Sr</option>
                                            <option value="II.">II</option>
                                            <option value="III.">III</option>
                                            <option value="IV.">IV</option>
                                            <option value="V.">V</option>
                                        </select>
                                        <small class="text-danger" id="edit-suffix-error"></small>
                                    </div>
                                </div>

                                <!-- Age, Birthdate, and Sex -->
                                <div class="row g-2 mb-3">
                                    <div class="col-12 col-md-12">
                                        <label for="birthdate" class="form-label">Date of Birth<span class="text-danger">*</span></label>
                                        <input type="date" id="edit_date_of_birth" class="form-control w-100" name="date_of_birth" value="" max="{{date('Y-m-d')}}">
                                        <small class="text-danger" id="birthdate-error"></small>
                                    </div>

                                </div>

                                <!-- Civil Status, Contact, Nationality -->
                                <div class="row g-2 mb-3">

                                    <div class="col-12 col-md-12">
                                        <label for="contact_num" class="form-label">Contact Number<span class="text-danger">*</span></label>
                                        <input type="number" placeholder="Enter the contact number" class="form-control w-100" id="edit_contact_number" name="contact_number" value="">
                                        <small class="text-danger" id="contact-error"></small>
                                    </div>

                                </div>

                                <!-- Username, Email, Password -->
                                <div class="row g-2 mb-3">

                                    <div class="col-12 col-md-12">
                                        <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                                        <input type="email" placeholder="Enter the email" id="edit_email" class="form-control" name="email" value="">
                                        <small class="text-danger" id="email-error"></small>
                                    </div>
                                </div>


                                <!-- Address -->
                                <div class="mb-3">
                                    <label class="form-label">Patient Address</label>
                                    <div class="row g-2">
                                        <div class="col-12 col-md-6">
                                            <label for="edit_blk_n_street" class="form-label text-muted small">Blk & lot, Street<span class="text-danger">*</span></label>
                                            <input type="text" id="edit_blk_n_street" name="blk_n_street" placeholder="Enter blk & lot & street" class="form-control">

                                            <small class="text-danger"></small>

                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="edit_patient_purok_dropdown" class="form-label text-muted small">Puroks<span class="text-danger">*</span></label>
                                            <select id="edit_patient_purok_dropdown" class="form-select" name="patient_purok_dropdown" data-health-worker-assigned-area-id="{{optional(Auth::user())->staff?->assigned_area_id}}" required>
                                                <option value="" selected disabled>Select a purok</option>
                                            </select>

                                            <small class="text-danger"></small>

                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" class="btn btn-danger px-4" id="cancel-btn" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success px-4" id="edit-user-submit-btn">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addModalLabel">Add New Patient Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <form action="" method="POST" class="rounded shadow d-flex flex-column align-items-center p-md-4 p-2  w-sm-25 w-md-50 w-lg-25 bg-white" id="add-patient-form">
                        @csrf
                        <div class="bg-light border-start  border-primary px-3 py-2 mb-4 rounded w-100">
                            <span class="fs-6">
                                <strong>Note:</strong>
                                <span class="text-danger">*</span>
                                <span class="fw-light"> indicates a required field.</span>
                            </span>
                        </div>

                        <!-- full name -->
                        <div class="mb-2 w-100">
                            <label for="" class="mb-1 ">Personal Info:</label>
                            <div class="gap-2 d-flex justify-content-center flex-wrap flex-xl-nowrap">
                                <div class="input-group">
                                    <label for="manage-user-first-name" class="w-100">First Name<span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter First Name" id="manage-user-first-name" name="first_name" class="py-2 px-2 fs-5 bg-light flex-fill xl:w-[50%]" autocomplete="off" style="width:200px;" value="{{old('first_name')}}">
                                </div>
                                <div class="input-group">
                                    <label for="manage-user-middle-name" class="w-100">Middle Name</label>
                                    <input type="text" placeholder="Enter Middle Name" id="manage-user-middle-name" name="middle_initial" class="py-2 px-2 fs-5 bg-light flex-fill xl:w-[50%]" autocomplete="off" style="width:200px;" value="{{old('middle_initial')}}">
                                </div>
                                <div class="input-group">
                                    <label for="manage-user-middle-name" class="w-100">Last Name<span class="text-danger">*</span></label>
                                    <input type=" text" placeholder="Enter Last Name" id="manage-user-last-name" name="last_name" class="py-2 px-2 fs-5 bg-light flex-fill xl:w-[50%]" autocomplete="off" style="width:200px;" value="{{old('last_name')}}">
                                </div>
                                <div class="input-group">
                                    <label for="add_suffix" class="w-100">Suffix</label>
                                    <select name="add_suffix" id="add_suffix" class="form-select responsive-input py-2">
                                        <option value="" selected>Select Suffix</option>
                                        <option value="Jr.">Jr</option>
                                        <option value="Sr.">Sr</option>
                                        <option value="II.">II</option>
                                        <option value="III.">III</option>
                                        <option value="IV.">IV</option>
                                        <option value="V.">V</option>
                                    </select>
                                    <small class="text-danger" id="add-suffix-error"></small>
                                </div>
                            </div>
                            <small class="text-danger error-element fname-error"></small>
                            <small class="text-danger error-element middle-initial-error"></small>
                            <small class="text-danger error-element lname-error"></small>
                        </div>
                        <!-- email -->
                        <div class="mb-2 w-100">
                            <label for="email" class="mb-1 ">Email<span class="text-danger">*</span></label>
                            <input type="email" placeholder="Enter the email" name="email" class="py-2 px-2 w-100 fs-5 bg-light" value="{{old('email')}}">
                            <small class="text-danger error-element email-error"></small>
                        </div>
                        <div class="mb-2 w-100">
                            <label for="date_of_birth" class="mb-1 ">Date of Birth<span class="text-danger">*</span></label>
                            <input type="date"  name="date_of_birth" class=" form-control py-1 px-2 bg-light" value="{{old('date_of_birth')}}" min="1950-01-01" max="{{date('Y-m-d')}}">

                            <small class="text-danger error-element date_of_birth_error"></small>

                        </div>
                        <div class="mb-2 w-100">
                            <label for="contact_number" class="mb-1 ">Contact Number<span class="text-danger">*</span></label>
                            <input type="text" placeholder="Enter the contact number" name="contact_number" class=" form-control py-1 px-2 bg-light w-100" value="{{old('contact_number')}}">

                            <small class="text-danger error-element contact_number_error"></small>

                        </div>
                        <!-- Password -->
                        <div class="mb-3 w-100">
                            <label for="password" class="mb-1 w-100">Password<span class="text-danger">*</span></label>
                            <div class="input-pass d-flex align-items-center">
                                <input type="password" placeholder="Enter the password" name="password" class="py-2 px-2 w-100 fs-5 bg-light" id="password" autocomplete="off" value="{{old('password')}}">
                                <i class="fa-solid fa-eye p-3 bg-primary text-white" id="eye-icon"></i>
                            </div>
                            <small class="text-danger error-element password-error"></small>
                        </div>
                        <!-- retype pass -->
                        <div class="mb-3 w-100">
                            <label for="re-type-pass" class="mb-1 font-weight-normal ">Retype password<span class="text-danger">*</span></label>
                            <div class="input-pass d-flex align-items-center">
                                <input type="password" placeholder="Confirm the password" name="password_confirmation" class="py-2 px-2 w-100 fs-5 bg-light" id="re-type-pass">
                                <i class="fa-solid fa-eye p-3 bg-primary text-white" id="Retype-eye-icon"></i>
                            </div>
                            <small class="text-danger"></small>
                        </div>
                        <!-- hidden input -->
                        <input type="text" name="role" value="patient" hidden>
                        <div class="mb-3 roles w-100">
                            <label for="patient_type" class="">Type of Patient<span class="text-danger">*</span></label>
                            <select name="patient_type" id="patient_type" class="form-select text-center w-100">
                                <option value="" selected disabled>Select the type of patient</option>
                                <option value="vaccination">Vaccination</option>
                                <option value="prenatal">PRE-NATAL</option>
                                <option value="tb-dots">Tb-dots</option>
                                <option value="senior-citizen">Senior Citizen</option>
                                <option value="family-planning">Family Planning</option>
                            </select>
                            <small class="text-danger error-element patient-type-error"></small>
                        </div>
                        <!-- patient address -->
                        <div class="mb-3 w-100 " id="patient_type_con">
                            <label for="patient_type" class="form-label text-nowrap fs-4 fw-bold">Patient Address </label>

                            <div class=" w-100 d-flex gap-2 flex-wrap flex-lg-nowrap">
                                <div class="items w-full lg:w-[50%]">
                                    <label for="patient_street" class="w-100 text-muted">Blk & lot,Street<span class="text-danger">*</span></label>
                                    <input type="text" id="blk_n_street" name="blk_n_street" placeholder="Enter the blk & lot & street seperated by ','" class="w-100 form-control">
                                    <small class="text-danger error-element blk-n-street-error"></small>
                                </div>
                                <div class="items w-full lg:w-[50%]">
                                    <label for="patient_purok_dropdown">Puroks<span class="text-danger">*</span></label>
                                    <select id="patient_purok_dropdown" class="form-select" name="patient_purok_dropdown" data-health-worker-assigned-area-id="{{optional(Auth::user())->staff?->assigned_area_id}}" required>
                                        <option value="" selected disabled>Select a purok</option>
                                    </select>

                                    <small class="text-danger error-element purok-dropdown-error"></small>
                                </div>
                            </div>
                        </div>
                        <!-- RECOVERY QUESTION -->


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
                @if(session('debug'))
                <div class="alert alert-info">
                    <strong>Debug:</strong>
                    <pre>{{ json_encode(session('debug'), JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif

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