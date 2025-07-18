<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/css/nurse_dashboard.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/manageInterface.css',
    'resources/css/patient/patient-dashboard.css',
    'resources/css/patient/record.css'
    ])

    <div class="ms-0 ps-0 d-flex w-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-flex w-100">
            <aside>
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
            <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class="flex-grow-1 ">
                @include('layout.header')
                <main class=" mt-4 d-flex align-items-center flex-column justify-content-center flex-grow-1 py-2 px-5">
                    <div class="top-content d-flex w-100  shadow mb-5 rounded b patient-profile">
                        <!-- Left panel -->
                        <div class="edit-profile p-3 d-flex w-25 flex-column align-items-center border-end">
                            <img src="{{ optional(Auth::user()->patient)->profile_image 
    ? asset(Auth::user()->patient->profile_image) 
    : asset('images/default_profile.png') }}"
                                alt="profile_img" class="mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                            <h4 class="mb-3">{{ optional(Auth::user()->patient)->full_name ?? 'none' }}</h4>
                            <h5 class="fw-light">{{ Auth::user()->email ?? 'none' }}</h5>
                            <button type="button" class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#profile_modal">Edit Profile</button>
                        </div>

                        <!-- Right panel -->
                        <div class="personal-info p-4 flex-grow-1">
                            <div class="info">
                                <h4>Personal Information</h4>
                                <div class="mb-3 d-flex px-4">
                                    <div class="box w-50">
                                        <div class="mb-2 d-flex gap-1">
                                            <h5>Sex:</h5>
                                            <p>Female</p>
                                        </div>
                                        <div class="mb-2 d-flex gap-1">
                                            <h5>Age:</h5>
                                            <p>30</p>
                                        </div>
                                        <div class="mb-2 d-flex gap-1">
                                            <h5>Contact No:</h5>
                                            <p>092103104103</p>
                                        </div>
                                        <div class="mb-2 d-flex gap-1">
                                            <h5>Register Date:</h5>
                                            <p>06/20/2025</p>
                                        </div>
                                    </div>
                                    <!-- 2nd box -->
                                    <div class="box w-50 px-4">
                                        <div class="mb-2 d-flex gap-1">
                                            <h5>Nationality:</h5>
                                            <p>Filipino</p>
                                        </div>
                                        <div class="mb-2 d-flex gap-1">
                                            <h5>Date of Birth:</h5>
                                            <p>01/02/1995</p>
                                        </div>
                                        <div class="mb-2 d-flex gap-1">
                                            <h5>Civil Status:</h5>
                                            <p>Single</p>
                                        </div>
                                        <div class="mb-2 d-flex gap-1">
                                            <h5>Patient Type:</h5>
                                            <p>{{Auth::user() -> patient -> patient_type}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="address">
                                <h4>Address</h4>
                                <p class="fs-5 fw-light px-3">Blk 25 Lot 14, Green Forbes, Hugos Perez, Trece Martires City, Cavite, Philippines</p>
                            </div>
                        </div>
                    </div>
                    <div class="record w-100">
                        <h4 class="text-start">Recent History</h4>
                        <table class="w-100">
                            <thead class="table-header">
                                <tr>
                                    <th>Case No.</th>
                                    <th>Type of Record</th>
                                    <th>Administed By</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>C-01</td>
                                    <td>Medical Record</td>
                                    <td>Nurse Joy</td>
                                    <td>05-21-2025</td>
                                    <td>Done</td>
                                    <td>
                                        <button class="btn-success btn">View Details</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                </main>
            </div>
        </div>
    </div>

    <div class="modal fade" id="profile_modal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="simpleModalLabel">Modal Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                </div>
                <div class="moda-body">
                    <div class="pop-up  w-100 h-100 d-flex align-items-center justify-content-center" id="pop-up">
                        <form action="" method="post" class="p-3 gap-3 w-100 d-flex opacity-[1]" enctype="multipart/form-data" id="profile-form">
                            @csrf
                            <!-- profile image section -->
                            <div class="profile-image p-1  mb-3 d-flex flex-column align-items-center" style="min-width:280px;">
                                <img src="{{asset(optional(Auth::user() -> patient ) -> profile_image) ?? asset('images/profile_images/default_img.png')}}" alt="profile picture" class="profile-section-image" id="profile-image">
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
                                        <input type="password" id="password" class="form-control" name="password">
                                        <small class="text-muted">Leave blank if you don't want to change it.</small>
                                        <small class="text-danger"></small>
                                    </div>
                                </div>
                                <!-- address -->
                                <div class="mb-2 d-flex gap-1 flex-column">
                                    <h4>Address</h4>
                                    <div class="input-field d-flex gap-2 w-100">
                                        <div class="mb-3 w-50">
                                            <label for="" class="text-white">te</label>
                                            <input type="text" placeholder="Blk & Lot n Street" class="form-control w-100" name="street" id="blk_n_street" value="">
                                            <small class="text-danger" id="street-error"></small>
                                        </div>
                                        <div class="postal w-50">
                                            <label for="postal">Postal Code</label>
                                            <input type="number" placeholder="0123" name="postal_code" id="postal_code" class="form-control w-100" value="">
                                            <small class="text-danger" id="postal-error"></small>
                                        </div>

                                    </div>
                                    <div class="input-field d-flex gap-2">
                                        <!-- region -->
                                        <div class="mb-2 w-50">
                                            <label for="region">Region*</label>
                                            <select name="region" id="region" class="form-select" data-selected="">
                                                <option value="">Select a region</option>
                                            </select>
                                            <small class="text-danger" id="region-error"></small>
                                        </div>
                                        <!-- province -->
                                        <div class="mb-2 w-50">
                                            <label for="province">Province*</label>
                                            <select name="province" id="province" class="form-select" disabled data-selected="">
                                                <option value="">Select a province</option>
                                            </select>
                                            <small class="text-danger" id="province-error"></small>
                                        </div>
                                    </div>

                                    <!-- city n brgy -->
                                    <div class="input-field d-flex gap-2">
                                        <!-- city -->
                                        <div class="mb-2 w-50">
                                            <label for="city">City*</label>
                                            <select name="city" id="city" class="form-select" disabled data-selected="">
                                                <option value="">Select a city</option>
                                            </select>
                                            <small class="text-danger" id="city-error"></small>
                                        </div>
                                        <!-- brgy -->
                                        <div class="mb-2 w-50">
                                            <label for="brgy">Barangay*</label>
                                            <select name="brgy" id="brgy" class="form-select" disabled data-selected="">
                                                <option value="">Select a brgy</option>
                                            </select>
                                            <small class="text-danger" id="brgy-error"></small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('patient_dashboard');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>