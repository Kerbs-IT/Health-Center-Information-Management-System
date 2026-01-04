<!DOCTYPE html>
<html lang="en">

<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>Barangay Health Center System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugo_perez_logo.png')}}">

    <!-- Laravel Vite Assets -->
    @vite([
    'resources/css/app.css',
    'resources/js/app.js',
    'resources/css/homepage.css',
    'resources/js/homepage.js',
    'resources/css/navbar.css'

    ])
    @vite(['resources/css/app.css', 'resources/js/app.js','resources/css/homepage.css', 'resources/js/homepage.js', 'resources/css/navbar.css', 'resources/js/navbar.js'])
</head>

<body class="d-flex flex-column min-vh-100">

    @include('layout.navbar')

    <main class="d-flex  justify-content-center flex-grow-1" style="background-color:#e8f5e9; margin-top: 5rem;">
        @yield('content')

        <div class="d-flex justify-content-center align-items-center w-100" style="display: flex !important;">
            <div class="row login-container login-card border w-100 mt-5">
                <!-- Left Image -->
                <div class="col-6 p-0">
                    <img src="{{ asset('images/hugo_perez.jpg') }}" alt="Hospital Hallway" class="login-img d-none d-sm-block">
                </div>
                <!-- Right Form -->
                <div class="col-6 login-form-con">
                    <form action="{{route('auth.login')}}" method="POST" class="rounded d-flex flex-column m-0 p-4 bg-white h-100">
                        @csrf
                        <h1 class="text-center fs-1 fw-bold">Login</h1>

                        <div class="form-group  mb-3 w-100">
                            <label for="username" class="mb-1 fw-bold fs-3">Email:</label><br>
                            <input type="text" placeholder="Enter your email" name="email" class="form-control p-2    bg-light" value="{{ old('email', Cookie::get('last_email')) }}">
                        </div>
                        <div class="form-group  mb-2 w-100">
                            <label for="password" class="mb-1 fw-bold fs-3">Password:</label>
                            <div class="input-pass d-flex align-items-center">
                                <input type="password" placeholder="Enter your password" name="password" class=" form-control  p-2    bg-light" id="password" value="{{ old('password', Cookie::get('last_password')) }}">
                                <i class="fa-solid fa-eye p-3 bg-primary text-white" id="eye-icon"></i>
                            </div>
                        </div>
                        <!-- remember me & forgot password -->
                        <div class=" form-group mb-3 w-95 d-flex justify-content-between">
                            <div class="remember-me">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ Cookie::get('remember_me') ? 'checked' : '' }}>
                                <label for="remember" class="mb-0"> Remember me</label>
                            </div>
                            <div class="forgot-pass">
                                <a href="{{ route('forgot.pass') }}">Forgot password?</a>
                            </div>

                        </div>
                        @error('email')
                        <small class="text-danger">{{$message}}</small>
                        @enderror
                        <div class="form-group mb-3 w-95">
                            <input type="submit" value="Sign-in" class="d-block btn btn-success py-1 m-auto  fs-6">
                        </div>

                        <div class="form-group mb-3 w-95">
                            <p class="text-center">Already have an account? <a href="{{route('register')}}">Sign-up</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
<footer class="bg-light text-center  mt-auto">
    @include('layout.footer')
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eye-icon");

        eyeIcon.addEventListener("click", function() {
            const isPassword = passwordInput.type === "password";

            // Toggle input type
            passwordInput.type = isPassword ? "text" : "password";

            // Swap icon
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    });
</script>
</body>

</script>