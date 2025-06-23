<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    @vite(['resources/css/app.css', 'resources/js/app.js','resources/css/forgot_pass.css'])

    <div class="forgot-pass-con rounded bg-white p-3">
        <form action="{{ route('forgot.pass.verify.email') }}" method="post" class="w-100">
            @csrf
            <h3 class="text-center">Forgot your password?</h3>
            <p class="text-muted">No problem. To reset your password, simply provide your email address and answer questions. This helps us verify your identity and keep your account secure.</p>
            <div class="input-group mb-3">
                <label for="email" class="w-100 mb-1">Email</label>
                <input type="email" placeholder="Enter your email" name="email" class="form-control rounded bg-light" required >
            </div>
            @if (session('error'))
                <small class="text-danger text-center d-block mb-3">{{ session('error') }}</small>
            @endif
            <div class="input-group w-100">
                <input type="submit" value="Verify Email" class="btn btn-success mx-auto">
            </div>
        </form>
    </div>
</body>
</html>