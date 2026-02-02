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
    'resources/css/navbar.css',
    'resources/js/navbar.js'])

</head>

@include('layout.navbar')

<body class="d-flex flex-column min-vh-100" style="background-color: #e8f5e9;">



    <main class="d-flex align-items-center justify-content-center flex-grow-1" style="background-color:#e8f5e9; min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h2 class="text-center mb-4">Forgot Password</h2>
                            <p class="text-muted text-center mb-4 small">
                                Enter your email address and we'll send you a link to reset your password.
                            </p>

                            @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input
                                        type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="Enter your email"
                                        required>
                                    @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary py-2">
                                        Send Password Reset Link
                                    </button>
                                </div>

                                <div class="text-center">
                                    <a href="{{ route('login') }}" class="text-decoration-none">
                                        <i class="bi bi-arrow-left me-1"></i>Back to Login
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