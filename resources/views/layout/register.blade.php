@extends('layouts.app')

@section('content')
<div class="container">
    <div class="register-container register-card d-flex">
        <!-- Left Form -->
        <div class="col-md-6 form-section">
            <h3 class="text-center mb-4">REGISTER</h3>
            <form method="POST" action="#">
                @csrf

                <div class="mb-2">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="name" placeholder="Enter full name" required>
                </div>

                <div class="mb-2">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" placeholder="Enter email address" required>
                </div>

                <div class="mb-2">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Enter username" required>
                </div>

                <div class="mb-2">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                </div>

                <div class="mb-2">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm password" required>
                </div>

                <div class="mb-2">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role" required>
                        <option disabled selected>Choose Role</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                        <!-- Add more roles as needed -->
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label">Department</label>
                    <select class="form-select" name="department" required>
                        <option disabled selected>Choose Department</option>
                        <option value="pediatrics">Pediatrics</option>
                        <option value="surgery">Surgery</option>
                        <option value="dermatology">Dermatology</option>
                        <!-- Add more departments as needed -->
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" required>
                    <label class="form-check-label">I agree with the terms and conditions</label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </form>
        </div>

        <!-- Right Image -->
        <div class="col-md-6 p-0">
            <img src="{{ asset('images/hugo_perez.jpg') }}" alt="Hospital Hallway" class="register-img">
        </div>
    </div>
</div>
@endsection