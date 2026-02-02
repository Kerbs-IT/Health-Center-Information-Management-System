<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png') }}">
    <title>Health Center Information Management System</title>

    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/css/homepage.css',
    'resources/js/homepage.js',
    'resources/css/navbar.css',
    'resources/js/navbar.js',
    'resources/js/register.js',
    'resources/css/auth/registration.css'])
</head>

<body class="">
    @include('sweetalert::alert')

    @include('layout.navbar')

    <main class="d-flex align-items-center justify-content-center mt-5 pt-5">
        <div class="container">
            <div class="row shadow rounded border overflow-hidden">
                <!-- Left Card/Image -->
                <div class="col-lg-6 col-md-12 d-none d-md-block p-0">
                    <img src="{{ asset('images/hugo_perez.jpg') }}" alt="Hospital Hallway" class="img-fluid object-fit-cover w-100 h-100">
                </div>
                <!-- Right card/Form -->
                <div class="col-lg-6 col-md-12  p-3">
                    <form action="{{ route('user.store') }}" method="POST" class="rounded d-flex flex-column bg-white" id="registrationForm">
                        @csrf
                        <h1 class="text-center fs-2 fw-bold">Register</h1>
                        <!-- full name -->
                        <div class="mb-3">
                            <div class="bg-light border-start  border-primary px-3 py-2 mb-4 rounded">
                                <span class="fs-6">
                                    <strong>Note:</strong>
                                    <span class="text-danger">*</span>
                                    <span class="fw-light"> indicates a required field.</span>
                                </span>
                            </div>

                            <div class="row g-2">
                                <div class="col-lg-6 col-md-6">
                                    <label for="" class="">First Name<span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter First Name" name="first_name" class="form-control py-1 px-2 bg-light" autocomplete="off" value="{{old('first_name')}}">
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <label for="" class="">Middle Name</label>
                                    <input type="text" placeholder="Enter Middle Name" name="middle_initial" class="form-control py-1 px-2 bg-light" autocomplete="off" value="{{old('middle_initial')}}">
                                </div>
                            </div>

                            <div class="row g-2 mt-2">
                                <div class="col-lg-6 col-md-6">
                                    <label for="" class="">Last Name<span class="text-danger">*</span></label>
                                    <input type="text" placeholder="Enter Last Name" name="last_name" class="form-control py-1 px-2 bg-light" autocomplete="off" value="{{old('last_name')}}">
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <label for="suffix" class="">Suffix</label>
                                    <select name="suffix" id="suffix" class="form-select responsive-input py-2">
                                        <option value="" disabled selected>Select Suffix</option>
                                        <option value="Jr.">Jr</option>
                                        <option value="Sr.">Sr</option>
                                        <option value="II.">II</option>
                                        <option value="III.">III</option>
                                        <option value="IV.">IV</option>
                                        <option value="V.">V</option>
                                    </select>
                                    <small class="text-danger" id="edit-suffix-error"></small>
                                </div>
                            </div>
                            @error('first_name')
                            <small class=" text-danger">{{$message}}</small>
                            @enderror
                            @error('middle_initial')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                            @error('last_name')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <!-- email -->
                        <div class="mb-2">
                            <label for="email" class="mb-1 h6 ">Email<span class="text-danger">*</span></label>
                            <input type="email" placeholder="Enter your email" name="email" class=" form-control py-1 px-2 bg-light" value="{{old('email')}}">
                            @error('email')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <!-- date of birth -->
                        <div class="mb-2">
                            <label for="date_of_birth" class="mb-1 h6 ">Date of Birth<span class="text-danger">*</span></label>
                            <input type="date" placeholder="Enter your date of birth" name="date_of_birth" class=" form-control py-1 px-2 bg-light" value="{{old('date_of_birth')}}" min="1950-01-01" max="{{date('Y-m-d')}}">
                            @error('date_of_birth')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="contact_number" class="mb-1 h6 ">Contact Number<span class="text-danger">*</span></label>
                            <input type="text" placeholder="Enter your phone number" name="contact_number" class=" form-control py-1 px-2 bg-light" value="{{old('contact_number')}}">
                            @error('contact_number')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="mb-1 h6">Password<span class="text-danger">*</span></label>
                            <div class="input-pass d-flex align-items-center">
                                <input type="password" placeholder="Enter your password" name="password" class="form-control py-1 px-2 bg-light" id="password" autocomplete="off" value="{{old('password')}}">

                                <svg class="fa-solid fa-eye p-2 bg-primary text-white h-10 w-10" fill="white" id="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                    <path d="M320 96C239.2 96 174.5 132.8 127.4 176.6C80.6 220.1 49.3 272 34.4 307.7C31.1 315.6 31.1 324.4 34.4 332.3C49.3 368 80.6 420 127.4 463.4C174.5 507.1 239.2 544 320 544C400.8 544 465.5 507.2 512.6 463.4C559.4 419.9 590.7 368 605.6 332.3C608.9 324.4 608.9 315.6 605.6 307.7C590.7 272 559.4 220 512.6 176.6C465.5 132.9 400.8 96 320 96zM176 320C176 240.5 240.5 176 320 176C399.5 176 464 240.5 464 320C464 399.5 399.5 464 320 464C240.5 464 176 399.5 176 320zM320 256C320 291.3 291.3 320 256 320C244.5 320 233.7 317 224.3 311.6C223.3 322.5 224.2 333.7 227.2 344.8C240.9 396 293.6 426.4 344.8 412.7C396 399 426.4 346.3 412.7 295.1C400.5 249.4 357.2 220.3 311.6 224.3C316.9 233.6 320 244.4 320 256z" />
                                </svg>
                            </div>
                            @error('password')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <!-- retype pass -->
                        <div class="mb-3">
                            <label for="re-type-pass" class="mb-1  h6">Retype password<span class="text-danger">*</span></label>
                            <div class="input-pass d-flex align-items-center">
                                <input type="password" placeholder="Re-type-pass" name="password_confirmation" class="form-control py-1 px-2 bg-light" id="re-type-pass">
                                <svg class="fa-solid fa-eye p-2 bg-primary text-white h-10 w-10" id="Retype-eye-icon" fill="white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                    <path d="M320 96C239.2 96 174.5 132.8 127.4 176.6C80.6 220.1 49.3 272 34.4 307.7C31.1 315.6 31.1 324.4 34.4 332.3C49.3 368 80.6 420 127.4 463.4C174.5 507.1 239.2 544 320 544C400.8 544 465.5 507.2 512.6 463.4C559.4 419.9 590.7 368 605.6 332.3C608.9 324.4 608.9 315.6 605.6 307.7C590.7 272 559.4 220 512.6 176.6C465.5 132.9 400.8 96 320 96zM176 320C176 240.5 240.5 176 320 176C399.5 176 464 240.5 464 320C464 399.5 399.5 464 320 464C240.5 464 176 399.5 176 320zM320 256C320 291.3 291.3 320 256 320C244.5 320 233.7 317 224.3 311.6C223.3 322.5 224.2 333.7 227.2 344.8C240.9 396 293.6 426.4 344.8 412.7C396 399 426.4 346.3 412.7 295.1C400.5 249.4 357.2 220.3 311.6 224.3C316.9 233.6 320 244.4 320 256z" />
                                </svg>
                            </div>

                            @error('password_confirmation')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <!-- Roles -->
                        <div class="mb-3 roles">
                            <label for="patient_type" class="">Type of User<span class="text-danger">*</span></label>
                            <select name="patient_type" id="patient_type" class="form-select text-center">
                                <option value="" selected disabled>Select the type of patient</option>
                                <option value="vaccination">Vaccination</option>
                                <option value="prenatal">PRE-NATAL</option>
                                <option value="tb-dots">Tb-dots</option>
                                <option value="senior-citizen">Senior Citizen</option>
                                <option value="family-planning">Family Planning</option>
                            </select>
                        </div>
                        <!-- patient type -->
                        <div class="mb-3" id="patient_type_con">
                            <label for="patient_type" class="form-label text-nowrap ">Patient Address </label>
                            <div class="row d-flex">
                                <div class="col-lg-6">
                                    <div class="items">
                                        <label for="patient_street" class="w-100 text-muted">Blk & lot,Street<span class="text-danger">*</span></label>
                                        <input type="text" id="blk_n_street" name="blk_n_street" placeholder="Enter the blk & lot" class="form-control">
                                        @error('blk_n_street')
                                        <small class="text-danger">{{$message}}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="items">
                                        <label for="brgy" class="text-muted">Purok<span class="text-danger">*</span></label>
                                        <select id="brgy" class="form-select" name="brgy" required>
                                            <option value="" selected disabled>Select a purok</option>
                                        </select>
                                        @error('brgy')
                                        <small class="text-danger">{{$message}}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions Checkbox -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="termsCheckbox" required>
                                <label class="form-check-label" for="termsCheckbox">
                                    I agree to the <a href="#" id="termsLink" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a><span class="text-danger">*</span>
                                </label>
                            </div>
                            <small class="text-danger" id="termsError" style="display: none;">You must accept the terms and conditions to register.</small>
                        </div>

                        <div class="mb-3 w-100">
                            <input type="submit" value="Register" id="submitBtn" class="d-block btn btn-success py-1 m-auto fw-bold fs-5">
                        </div>

                        <div class="w-100">
                            <p class="text-center">Already have an account? <a href="{{route('login')}}">Sign-in</a></p>
                        </div>
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </form>
                </div>
                <!-- register form -->
            </div>
        </div>
    </main>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header  text-white">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="terms-content">
                        <h3>1. Acceptance of Terms</h3>
                        <p>By registering for the Health Center Information Management System, you agree to comply with and be bound by these Terms and Conditions. If you do not agree to these terms, please do not register or use our services.</p>

                        <h3>2. User Registration</h3>
                        <p>To access our services, you must:</p>
                        <ul class="text-justify">
                            <li class="text-justify">Provide accurate, current, and complete information during registration, maintain and update your information to keep it accurate and current, Maintain the security and confidentiality of your password. Notify us immediately of any unauthorized use of your account
                            </li>
                        </ul>

                        <h3>3. Privacy and Data Protection</h3>
                        <p>We are committed to protecting:</p>
                        <ul>
                            <li>the privacy and personal data of its users in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173) and its implementing rules and regulations.
                                All personal and medical information collected through the system is used solely for healthcare documentation, record-keeping, and administrative purposes of the health center. Data collected includes, but is not limited to, personal identification details, medical records, and vaccination information.
                                Access to personal and medical data is restricted to authorized personnel only, such as healthcare workers and system administrators, who are required to handle information with strict confidentiality.
                                The system implements reasonable organizational, technical, and physical security measures to protect personal data against unauthorized access, alteration, disclosure, loss, or destruction.
                                Personal data shall not be shared with third parties without the data subject’s consent, except when required by law or authorized by relevant government agencies.
                                By using this system, users acknowledge that they understand and consent to the collection, processing, and storage of their personal and medical information for the stated purposes.</li>
                        </ul>

                        <h3>4. Medical Information Disclaimer</h3>
                        <p>The Health Center Information Management System is intended solely:</p>
                        <ul>
                            <li>for record-keeping, documentation, and administrative purposes. The information stored and displayed in this system, including patient records, vaccination history, and medical data, is not intended to replace professional medical advice, diagnosis, or treatment.
                                All medical decisions, diagnoses, and treatments remain the responsibility of licensed healthcare professionals. Users are advised not to rely solely on the information provided by this system for medical decision-making.</li>
                            <li>By using this system, users acknowledge and agree that the platform functions as a digital record management tool only and does not provide medical services.</li>
                        </ul>

                        <h3>5. User Responsibilities</h3>
                        <p>As a user, you agree to:</p>
                        <ul>
                            <li>
                                Provide accurate, truthful, and complete personal information when your data is collected or recorded by authorized health center personnel. <br>
                                Inform the health center of any changes to your personal or medical information when applicable. <br>
                                Respect the confidentiality and integrity of the system and refrain from attempting to access restricted areas or data. <br>
                                Use any system access granted to you (if applicable) only for lawful and authorized purposes. <br>
                                Refrain from submitting false, misleading, or unauthorized information that may affect the accuracy of medical records. <br>
                                Acknowledge that medical records are managed and updated by authorized health workers, and that patients do not directly modify official health records within the system.
                            </li>
                        </ul>

                        <h3>6. Service Availability</h3>
                        <p>We strive to maintain continuous service availability, however:</p>
                        <ul>
                            <li>The system may be temporarily unavailable due to maintenance or technical issues</li>
                            <li>We are not liable for service interruptions beyond our control</li>
                            <li>Emergency medical situations should be directed to appropriate emergency services</li>
                        </ul>

                        <h3>7. Modifications to Terms</h3>
                        <ul>
                            <li>We reserve the right to modify these terms at any time. Continued use of the system after changes constitutes acceptance of the modified terms.</li>
                        </ul>

                        <h3>8. Termination</h3>
                        <p>We reserve the right to terminate or suspend your account if:</p>
                        <ul>
                            <li>You provide false, inaccurate, or misleading information that affects the integrity of health records.  <br>
                                You misuse the system or attempt to access restricted or unauthorized information.  <br>
                                You violate any part of these Terms and Conditions, including privacy and data protection provisions.  <br>
                                Your actions compromise the security, confidentiality, or proper operation of the system.  <br>
                                Account access is no longer necessary for the intended purpose of the system, such as record maintenance or administrative requirements.</li>
                        </ul>

                        <h3>9. Contact Information</h3>
                        <ul>
                            <li>If you have questions about these Terms and Conditions, please contact the Health Center administration.</li>
                        </ul>

                        <p class="mt-4"><strong>By checking the box and registering, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="acceptTermsBtn">Accept Terms</button>
                </div>
            </div>
        </div>
    </div>

    @if(session('reg_success'))
    <script>
        setTimeout(() => {
            window.location.href = "{{ route('login') }}";
        }, 2000);
    </script>
    @endif

    <script>
        // Password toggle functionality
        const eyeIcon = document.getElementById('eye-icon');
        const password = document.getElementById('password');
        const RetypeeyeIcon = document.getElementById('Retype-eye-icon');
        const Retypepassword = document.getElementById('re-type-pass');

        function passwordToggle(eyeIcon, passwordInput) {
            eyeIcon.addEventListener('mousedown', () => {
                passwordInput.type = 'text';
            })
            eyeIcon.addEventListener('mouseup', () => {
                passwordInput.type = 'password';
            })
            eyeIcon.addEventListener('mouseout', () => {
                passwordInput.type = 'password';
            })
        }

        passwordToggle(eyeIcon, password);
        passwordToggle(RetypeeyeIcon, Retypepassword);

        // Terms and Conditions functionality
        const termsCheckbox = document.getElementById('termsCheckbox');
        const termsError = document.getElementById('termsError');
        const registrationForm = document.getElementById('registrationForm');
        const acceptTermsBtn = document.getElementById('acceptTermsBtn');
        const termsModal = document.getElementById('termsModal');

        // Prevent link from navigating
        document.getElementById('termsLink').addEventListener('click', (e) => {
            e.preventDefault();
        });

        // Accept terms button in modal
        acceptTermsBtn.addEventListener('click', () => {
            termsCheckbox.checked = true;
            termsError.style.display = 'none';
            // Close modal using Bootstrap's modal instance
            const modalInstance = bootstrap.Modal.getInstance(termsModal);
            modalInstance.hide();
        });

        // Form validation
        registrationForm.addEventListener('submit', (e) => {
            if (!termsCheckbox.checked) {
                e.preventDefault();
                termsError.style.display = 'block';
                termsCheckbox.focus();

                // Scroll to the checkbox
                termsCheckbox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Hide error when checkbox is checked
        termsCheckbox.addEventListener('change', () => {
            if (termsCheckbox.checked) {
                termsError.style.display = 'none';
            }
        });
    </script>
</body>

</html>