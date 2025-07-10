@extends('layouts.app')


@section('content')
<div class="container">
    <div class="login-container login-card">
        <!-- Left Image -->
        <div class="col-md-6 p-0">
            <img src="{{ asset('images/hugo_perez.jpg') }}" alt="Hospital Hallway" class="login-img">
        </div>

        <!-- Right Form -->
        <div class="col-md-6 form-section">
            <h3 class="text-center mb-4">LOGIN</h3>
            <form method="POST" action="#">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email or Username</label>
                    <input type="text" class="form-control" name="email" placeholder="Enter email or username" required>
                </div>

                <div class="mb-2">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember">
                        <label class="form-check-label">Remember me</label>
                    </div>
                    <a href="#" class="small">Forgot Password?</a>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-login">Login</button>
                </div>

                <div class="text-center mt-3">
                    <small>Don't have an account? <a href="{{ route('register') }}">Register here</a></small>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection