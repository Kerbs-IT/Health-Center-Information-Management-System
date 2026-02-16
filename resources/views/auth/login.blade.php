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
                                <input type="password" placeholder="Enter your password" name="password" class="form-control p-2 bg-light !rounded-r-none" id="password" value="{{ old('password', Cookie::get('last_password')) }}">

                                <!-- eye icon -->
                                <svg class="eye-icon p-3 bg-primary text-white h-12 w-13 !rounded-r-lg cursor-pointer" id="eye-open" fill="white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                    <path d="M320 96C239.2 96 174.5 132.8 127.4 176.6C80.6 220.1 49.3 272 34.4 307.7C31.1 315.6 31.1 324.4 34.4 332.3C49.3 368 80.6 420 127.4 463.4C174.5 507.1 239.2 544 320 544C400.8 544 465.5 507.2 512.6 463.4C559.4 419.9 590.7 368 605.6 332.3C608.9 324.4 608.9 315.6 605.6 307.7C590.7 272 559.4 220 512.6 176.6C465.5 132.9 400.8 96 320 96zM176 320C176 240.5 240.5 176 320 176C399.5 176 464 240.5 464 320C464 399.5 399.5 464 320 464C240.5 464 176 399.5 176 320zM320 256C320 291.3 291.3 320 256 320C244.5 320 233.7 317 224.3 311.6C223.3 322.5 224.2 333.7 227.2 344.8C240.9 396 293.6 426.4 344.8 412.7C396 399 426.4 346.3 412.7 295.1C400.5 249.4 357.2 220.3 311.6 224.3C316.9 233.6 320 244.4 320 256z" />
                                </svg>
                                <!-- eye slash -->
                                <svg class="eye-icon p-3 bg-primary text-white h-12 w-13 d-none !rounded-r-lg cursor-pointer" id="eye-closed" fill="white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                    <path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zM223.1 149.5C248.6 126.2 282.7 112 320 112c79.5 0 144 64.5 144 144c0 24.9-6.3 48.3-17.4 68.7L408 294.5c8.4-19.3 10.6-41.4 4.8-63.3c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3c0 10.2-2.4 19.8-6.6 28.3l-90.3-70.8zM373 389.9c-16.4 6.5-34.3 10.1-53 10.1c-79.5 0-144-64.5-144-144c0-6.9 .5-13.6 1.4-20.2L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5L373 389.9z" />
                                </svg>
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
                            <p class="text-center fs-6">Don’t have an account? <a href="{{route('register')}}">Sign-up</a></p>
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
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');


        function togglePassword(){
            const isPassword = passwordInput.type === "password";

            passwordInput.type = isPassword ? "text" : "password";

            eyeOpen.classList.toggle('d-none');
            eyeClosed.classList.toggle('d-none')
        }

        eyeOpen.addEventListener('click', togglePassword);
        eyeClosed.addEventListener('click', togglePassword);
    });
</script>
</body>

</script>