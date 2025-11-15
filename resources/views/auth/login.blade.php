<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png') }}">
    <link href="{{ asset('homepage.css') }}" rel="stylesheet"> {{-- Optional Custom Styling --}}
    <title>Health Center Information Management System</title>
</head>

<body class="d-flex flex-column min-vh-100">

    @vite(['resources/css/app.css', 'resources/js/app.js','resources/js/login.js','resources/css/auth/login.css'])
     @include('layout.navbar')

    <main class="d-flex align-items-center justify-content-center flex-grow-1 ">

        <div class="container">
            <div class="login-container login-card border">
                <!-- Left Image -->
                <div class=" p-0">
                    <img src="{{ asset('images/hugo_perez.jpg') }}" alt="Hospital Hallway" class="login-img d-none d-sm-block">
                </div>

                <!-- Right Form -->
                <div class="login-form-con">
                    <form action="{{route('auth.login')}}" method="POST" class="rounded d-flex flex-column align-items-center p-4 bg-white h-100">
                        @csrf
                        <h1 class="text-center fs-1 fw-bold">Login</h1>

                        <div class="mb-3 w-100">
                            <label for="username" class="mb-1 fw-bold fs-3">Email:</label>
                            <input type="text" placeholder="Enter your email" name="email" class="p-2 w-100 bg-light" value="{{ old('email', Cookie::get('last_email')) }}">
                        </div>
                        <div class="mb-2 w-100">
                            <label for="password" class="mb-1 fw-bold fs-3">Password:</label>
                            <div class="input-pass d-flex">
                                <input type="password" placeholder="Enter your password" name="password" class="p-2 w-100 bg-light" id="password" value="{{ old('password', Cookie::get('last_password')) }}">
                                <i class="fa-solid fa-eye p-3 bg-primary text-white" id="eye-icon"></i>
                            </div>
                        </div>
                        <!-- remember me & forgot password -->
                        <div class="mb-3 w-100 d-flex justify-content-between">
                            <div class="remember-me">
                                <input type="checkbox" name="remember" id="remember" {{ Cookie::get('remember_me') ? 'checked' : '' }}>
                                <label for="remember" class="mb-0"> Remember me</label>
                            </div>
                            <div class="forgot-pass">
                                <a href="{{ route('forgot.pass') }}">Forgot password?</a>
                            </div>

                        </div>
                        @error('email')
                        <small class="text-danger">{{$message}}</small>
                        @enderror
                        <div class="mb-3 w-100">
                            <input type="submit" value="Sign-in" class="d-block btn btn-success py-1 m-auto fw-bold fs-5">
                        </div>

                        <div class="mb-3 w-95">
                            <p class="text-center fs-6 fs-md-5">Already have an account? <a href="{{route('register')}}">Sign-up</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eye-icon");

        eyeIcon.addEventListener("click", function () {
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

</html>