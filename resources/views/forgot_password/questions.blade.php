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

    <div class="forgot-pass-con bg-white p-3 rounded">
        <form action="{{ route('forgot.pass.verify.answer') }}" method="post" class="w-100">
            @csrf
            <div class="mb-3">
                <h3 class="text-center">Recovery Question</h3 class="text-center">
            </div>
            <div class="mb-3">
                <input type="email" value="{{ session('recovery_email' )}}" class="form-control bg-light text-muted" disabled>
            </div>
            <div class="mb-3">
                <label for="recovery_question" class="fs-4 fw-bold w-100">Recovery Question:</label>
                <select name="recovery_question" id="recovery_question" class="form-select w-100 mb-2" required>
                    <option value="">Select a question</option>
                    <option value="1">What is your nickname? </option>
                    <option value="2">What is the ame of your mother?</option>
                    <option value="3">What is the name of your pet? </option>
                </select>
                <input type="text" name="recovery_answer" placeholder="Enter your answer" class="form-control w-100" required>
            </div>
            @if (session('error'))
                <small class="text-danger text-center d-block mb-3">{{ session('error') }}</small>
            @endif
            <div class="mb-3 w-100">
                <input type="submit" value="Verify Answer" class="btn d-block btn-success mx-auto">
            </div>
        </form>
    </div>
</body>
</html>