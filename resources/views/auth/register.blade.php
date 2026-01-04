<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png') }}">
    <title>Health Center Information Management System</title>

    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/css/homepage.css',
    'resources/js/homepage.js',
    'resources/css/navbar.css',
    'resources/js/navbar.js',
    'resources/js/register.js',
    'resources/css/auth/registration.css'])
</head>

<body class="">
    @include('sweetalert::alert')

    @include('layout.navbar')

    <main class="d-flex align-items-center justify-content-center mt-5 pt-5">
        <div class="container">
            <div class="row shadow rounded border overflow-hidden">
                <!-- Left Card/Image -->
                <div class="col-lg-6 col-md-12 d-none d-md-block p-0">
                    <img src="{{ asset('images/hugo_perez.jpg') }}" alt="Hospital Hallway" class="img-fluid object-fit-cover w-100 h-100">
                </div>
                <!-- Right card/Form -->
                <div class="col-lg-6 col-md-12  p-3">
                    <form action="{{ route('user.store') }}" method="POST" class="rounded d-flex flex-column bg-white">
                        @csrf
                        <h1 class="text-center fs-2 fw-bold">Register</h1>

                        <!-- username -->
                        <div class="mb-3">
                            <label for="username" class="mb-1 h6 ">Username:</label>
                            <input type="text" placeholder="Enter your username" name="username" class="form-control bg-light" autocomplete="off" value="{{old('username')}}">
                            @error('username')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <!-- full name -->
                        <div class="mb-3">
                            <label for="" class="mb-1 h6 ">Personal Info:</label>
                            <div class="row g-2">
                                <div class="col-lg-4 col-md-6">
                                    <input type="text" placeholder="First Name" name="first_name" class=" form-control py-1 px-2 bg-light" autocomplete="off" value="{{old('first_name')}}">
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <input type="text" placeholder="Middle Initial" name="middle_initial" class="form-control py-1 px-2 bg-light" autocomplete="off" value="{{old('middle_initial')}}">
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <input type=" text" placeholder="Last Name" name="last_name" class="form-control py-1 px-2 bg-light" autocomplete="off" value="{{old('last_name')}}">
                                </div>

                            </div>
                            @error('first_name')
                            <small class=" text-danger">{{$message}}</small>
                            @enderror
                            @error('middle_initial')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                            @error('last_name')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <!-- email -->
                        <div class="mb-2">
                            <label for="email" class="mb-1 h6 ">Email:</label>
                            <input type="email" placeholder="Enter your email" name="email" class=" form-control py-1 px-2 bg-light" value="{{old('email')}}">
                            @error('email')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <!-- date of birth -->
                        <div class="mb-2">
                            <label for="date_of_birth" class="mb-1 h6 ">Date of Birth:</label>
                            <input type="date" placeholder="Enter your email" name="date_of_birth" class=" form-control py-1 px-2 bg-light" value="{{old('date_of_birth')}}">
                            @error('date_of_birth')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="contact_number" class="mb-1 h6 ">Contact Number:</label>
                            <input type="text" placeholder="Enter your email" name="contact_number" class=" form-control py-1 px-2 bg-light" value="{{old('contact_number')}}">
                            @error('contact_number')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="mb-1 h6">Password:</label>
                            <div class="input-pass d-flex align-items-center">
                                <input type="password" placeholder="Enter your password" name="password" class="form-control py-1 px-2 bg-light" id="password" autocomplete="off" value="{{old('password')}}">
                                <i class="fa-solid fa-eye p-2 bg-primary text-white" id="eye-icon"></i>
                            </div>
                            @error('password')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <!-- retype pass -->
                        <div class="mb-3">
                            <label for="re-type-pass" class="mb-1  h6">Retype password:</label>
                            <div class="input-pass d-flex align-items-center">
                                <input type="password" placeholder="Re-type-pass" name="password_confirmation" class="form-control py-1 px-2 bg-light" id="re-type-pass">
                                <i class="fa-solid fa-eye p-2 bg-primary text-white" id="Retype-eye-icon"></i>
                            </div>

                            @error('password_confirmation')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <!-- Roles -->
                        <div class="mb-3 roles">
                            <label for="patient_type" class="">Type of User</label>
                            <select name="patient_type" id="patient_type" class="form-select text-center">
                                <option value="" selected disabled>Select the type of patient</option>
                                <option value="vaccination">Vaccination</option>
                                <option value="prenatal">PRE-NATAL</option>
                                <option value="tb-dots">Tb-dots</option>
                                <option value="senior-citizen">Senior Citizen</option>
                                <option value="family-planning">Family Planning</option>
                            </select>
                        </div>
                        <!-- patient type -->
                        <div class="mb-3" id="patient_type_con">
                            <label for="patient_type" class="form-label text-nowrap ">Patient Address </label>
                            <div class="row d-flex">
                                <div class="col-lg-6">
                                    <div class="items">
                                        <label for="patient_street" class="w-100 text-muted">Blk & lot,Street*</label>
                                        <input type="text" id="blk_n_street" name="blk_n_street" placeholder="Enter the blk & lot" class="form-control">
                                        @error('blk_n_street')
                                        <small class="text-danger">{{$message}}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="items">
                                        <label for="brgy" class="text-muted">Puroks*</label>
                                        <select id="brgy" class="form-select" name="brgy" required>
                                            <option value="" selected disabled>Select a purok</option>
                                        </select>
                                        @error('brgy')
                                        <small class="text-danger">{{$message}}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="mb-3 w-100">
                            <input type="submit" value="Register" class="d-block btn btn-success py-1 m-auto fw-bold fs-5">
                        </div>

                        <div class="w-100">
                            <p class="text-center">Already have an account? <a href="{{route('login')}}">Sign-in</a></p>
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
                <!-- register form -->
            </div>
        </div>
    </main>
    @if(session('reg_success'))
    <script>
        setTimeout(() => {
            window.location.href = "{{ route('login') }}";
        }, 2000);
    </script>
    @endif

    <script>
        const eyeIcon = document.getElementById('eye-icon');
        const password = document.getElementById('password');

        // retype
        const RetypeeyeIcon = document.getElementById('Retype-eye-icon');
        const Retypepassword = document.getElementById('re-type-pass');


        function passwordToggle(eyeIcon, passwordInput) {
            eyeIcon.addEventListener('mousedown', () => {
                passwordInput.type = 'text';
            })
            eyeIcon.addEventListener('mouseup', () => {
                passwordInput.type = 'password';
            })

            eyeIcon.addEventListener('mouseout', () => {
                passwordInput.type = 'password';
            })
        }

        passwordToggle(eyeIcon, password);
        passwordToggle(RetypeeyeIcon, Retypepassword);
    </script>
</body>

</html>