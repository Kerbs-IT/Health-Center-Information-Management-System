<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>
<body class="vh-100">

    @vite(['resources/css/app.css', 'resources/js/app.js','resources/js/login.js'])

    <!-- <header class="w-100">
        <nav class="w-100 bg-success" style="background-color: green;">
                <div></div>
        </nav>
    </header> -->

    <main class="d-flex align-items-center justify-content-center h-100 ">
        <!-- login form -->
        <form action="{{route('auth.login')}}" method="POST" class="rounded border d-flex flex-column align-items-center p-4 bg-white">
            @csrf
            <h1 class="text-center fs-1 fw-bold">Login</h1>

            <div class="mb-3 w-95">
                <label for="username" class="mb-1 fw-bold fs-3">Username:</label>
                <input type="text" placeholder="Enter your username" name="username" class="p-2 w-100 fs-5">
            </div>
            <div class="mb-3 w-95">
                <label for="password" class="mb-1 fw-bold fs-3">Password:</label>
                <div class="input-pass d-flex align-items-center">
                    <input type="password" placeholder="Enter your password" name="password" class="p-2 w-100 fs-5" id="password">
                    <i class="fa-solid fa-eye p-3 bg-primary text-white" id="eye-icon"></i>
                </div>
            </div>  
            @error('username')
                <small class="text-danger">{{$message}}</small>
            @enderror
            <div class="mb-3 w-95">
                <input type="submit" value="Sign-in" class="d-block btn btn-success py-1 m-auto fw-bold fs-5">
            </div>

            <div class="mb-3 w-95">
                <p class="text-center">Already have an account? <a href="{{route('register')}}">Sign-up</a></p>
            </div>

        </form>
    </main>
</body>
</html>