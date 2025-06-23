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
    @include('sweetalert::alert')
     @vite(['resources/css/app.css', 'resources/js/app.js','resources/css/forgot_pass.css'])

    <div class="forgot-pass-con bg-white p-3 rounded">
        <form action="{{ route('change.pass') }}" method="post" class="w-100">
            @csrf
            <div class="mb-3">
                <h3 class="text-center">Change Password</h3 class="text-center">
            </div>
            <div class="mb-3">
                <input type="email" value="{{ session('recovery_email' )}}" class="form-control bg-light text-muted" disabled>
            </div>
            <div class="mb-3">
                <label for="new_pass">New Password</label>
                <input type="password" name="password" class="form-control bg-light" placeholder="Enter new password">
            </div>
            @error('password')
                <small class="text-danger text-center d-block mb-3">{{$message}}</small>
            @enderror
            <div class="mb-3">
                <label for="new_pass">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control bg-light" placeholder="Enter new password">
            </div>
            @error('password_confirmation')
                <small class="text-danger text-center d-block mb-3">{{ $message }}</small>
            @enderror
            <div class="mb-3 w-100">
                <input type="submit" value="Verify Answer" class="btn d-block btn-success mx-auto">
            </div>
        </form>
    </div>

    @if(session('success'))
        <script>
            setTimeout(() =>{
                window.location.href = "{{ route('login') }}";
            },2000);
        </script>
    @endif
</body>
</html>