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
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png')}}">

    <!-- Laravel Vite Assets -->
    @vite([
    'resources/css/app.css',
    'resources/js/app.js',
    'resources/css/homepage.css',
    'resources/js/homepage.js',
    'resources/css/navbar.css',
    'resources/js/navbar.js'])

</head>

@include('layout.navbar')

<body class="d-flex flex-column min-vh-100" style="background-color: #e8f5e9;">



    <main class="d-flex align-items-center justify-content-center flex-grow-1" style="background-color:#e8f5e9; min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">
                    <div class="card border-0 shadow-lg" style="border-radius: 20px;">
                        <div class="card-body p-5">

                            <div class="text-center mb-4">
                                <h2 class="fw-bold mb-1" style="color:#1b5e20;">Forgot Password</h2>
                                <p class="text-muted small mb-0">
                                    Enter your email and we'll send you a reset link.
                                </p>
                            </div>

                            @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold small text-uppercase"
                                        style="letter-spacing:1px; color:#2e7d32;">
                                        Email Address
                                    </label>
                                    <input type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email"
                                        value="{{ old('email') }}"
                                        placeholder="Enter your email"
                                        style="border-radius:10px; padding: 12px 16px; border-color:#ced4da;"
                                        required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn py-2 fw-semibold text-white"
                                        style="border-radius:10px; background-color:#2e7d32; border:none; font-size:15px;">
                                        Send Reset Link
                                    </button>
                                </div>

                                <hr class="my-3">

                                <div class="text-center">
                                    <a href="{{ route('login') }}"
                                        class="text-decoration-none small fw-semibold text-center"
                                        style="color:#2e7d32;">
                                         Back to Login
                                    </a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
<footer class="bg-light text-center  mt-auto">
    @include('layout.footer')
</footer>

</html>