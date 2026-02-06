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
            <div class="row login-container login-card border w-100 mt-5 mx-2">
                <!-- Left Image -->
                <div class="col-12 col-lg-6 p-0">
                    <img src="{{ asset('images/hugo_perez.jpg') }}" alt="Hospital Hallway" class="login-img d-none d-sm-block">
                </div>
                <!-- Right Form -->
                <div class="col-12 col-lg-6 login-form-con">
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

                                <svg class="fa-solid fa-eye p-3 bg-primary text-white h-13 w-13" fill="white" id="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M320 96C239.2 96 174.5 132.8 127.4 176.6C80.6 220.1 49.3 272 34.4 307.7C31.1 315.6 31.1 324.4 34.4 332.3C49.3 368 80.6 420 127.4 463.4C174.5 507.1 239.2 544 320 544C400.8 544 465.5 507.2 512.6 463.4C559.4 419.9 590.7 368 605.6 332.3C608.9 324.4 608.9 315.6 605.6 307.7C590.7 272 559.4 220 512.6 176.6C465.5 132.9 400.8 96 320 96zM176 320C176 240.5 240.5 176 320 176C399.5 176 464 240.5 464 320C464 399.5 399.5 464 320 464C240.5 464 176 399.5 176 320zM320 256C320 291.3 291.3 320 256 320C244.5 320 233.7 317 224.3 311.6C223.3 322.5 224.2 333.7 227.2 344.8C240.9 396 293.6 426.4 344.8 412.7C396 399 426.4 346.3 412.7 295.1C400.5 249.4 357.2 220.3 311.6 224.3C316.9 233.6 320 244.4 320 256z"/></svg>
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
                            <p class="text-center fs-5">Already have an account? <a href="{{route('register')}}">Sign-up</a></p>
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