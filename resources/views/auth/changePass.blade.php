<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/js/changePassword.js',
    'resources/css/changePass.css'])
    <!-- always include the sweetalert -->
    @include('sweetalert::alert')
    <div class="ms-0 ps-0 d-flex w-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-flex w-100">
            <aside>
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
            <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class="flex-grow-1">
                @include('layout.header')
                <main>
                    <div class="change-pass-button w-100 d-flex justify-content-start p-4">
                        <a href="{{ Auth::user()->role == 'patient' ? route('dashboard.patient') : route('page.profile') }}" class="btn btn-danger">Back</a>
                    </div>

                    <div class="card shadow mx-auto mt-5" style="max-width: 450px;">
                        <!-- Header with success theme -->
                        <div class="card-header bg-success text-white text-center">
                            <h5 class="mb-0">Change Password</h5>
                        </div>

                        <!-- Form body -->
                        <form action="" method="POST" class="card-body flex-column" id="change-pass-form">
                            @csrf

                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="inputs d-flex align-items-center">
                                    <input type="password" name="current_password" id="current_password" class="form-control change-pass-input" required>
                                    <div class="eye-icon bg-light text-success px-2 align-self-stretch d-flex align-items-center" id="current_password_eye_icon">
                                        <i class="fa-solid fa-eye fs-4"></i>
                                    </div>
                                </div>
                                <small class="text-danger" id="current_password_error"></small>
                            </div>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="inputs d-flex align-items-center">
                                    <input type="password" name="new_password" id="new_password" class="form-control change-pass-input" required>
                                    <div class="eye-icon bg-light text-success px-2 align-self-stretch d-flex align-items-center" id="new_password_eye_icon">
                                        <i class="fa-solid fa-eye  fs-4"></i>
                                    </div>
                                </div>

                                <small class="text-danger" id="new_password_error"></small>
                            </div>

                            <!-- Confirm New Password -->
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="inputs d-flex align-items-center">
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control change-pass-input" required>
                                    <div class="eye-icon bg-light text-success px-2 align-self-stretch d-flex align-items-center" id="new_password_confirmation_eye_icon">
                                        <i class="fa-solid fa-eye fs-4"></i>
                                    </div>
                                </div>

                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-success" id="change-pass-submit-btn">Update Password</button>
                            </div>
                        </form>
                    </div>

                </main>

            </div>
        </div>
    </div>

    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- slide-in from right -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Vaccination Notification -->
                    <div class="alert alert-info mb-3 d-flex justify-content-between align-items-center">
                        <span>💉 <strong>Scheduled vaccination list is updated.</strong></span>
                        <a href="#" class="btn btn-sm btn-primary">Visit</a>
                    </div>

                    <!-- Prenatal Notification -->
                    <div class="alert alert-warning d-flex justify-content-between align-items-center">
                        <span>🤰 <strong>New prenatal checkup list is available.</strong></span>
                        <a href="#" class="btn btn-sm btn-warning text-white">Visit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($isActive && Auth::user()-> role !='patient')
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const profileCon = document.getElementById('profile');

            if (profileCon) {
                profileCon.classList.add('active');
            }
        })
    </script>
    @else
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const profileCon = document.getElementById('patient_dashboard');

            if (profileCon) {
                profileCon.classList.add('active');
            }
        })
    </script>
    @endif

    <script>
        function showFileName(input) {
            const fileName = input.files.length ? input.files[0].name : "No file chosen";
            document.getElementById("fileName").textContent = fileName;
        }
    </script>
</body>

</html>