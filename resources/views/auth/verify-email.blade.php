<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Your Email - Health Center IMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {

            font-size: 16px;
        }

        .verification-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .card-header i {
            font-size: 48px;
            margin-bottom: 15px;
            color: white;
        }

        .card-header h4 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: white;
        }

        .card-body {
            padding: 40px !important;
        }

        .email-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .code-input {
            font-size: 28px !important;
            letter-spacing: 10px;
            text-align: center;
            font-weight: bold;
            height: 70px;
            border: 3px solid #e0e0e0;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .code-input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        .btn-verify {
            height: 55px;
            font-size: 18px;
            font-weight: 600;
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            border: none;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-verify:hover {

            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }

        .countdown-timer {
            font-size: 20px;
            font-weight: bold;
            color: #4CAF50;
        }

        .attempts-badge {
            display: inline-block;
            padding: 8px 15px;
            background: #fff3cd;
            color: #856404;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
        }

        .locked-message {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .info-text {
            font-size: 15px;
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    @vite(['resources/css/header.css',
    ])
    @include('layout.navbar')

    <main style="padding-top: 110px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="verification-card">
                        <div class="card-header">
                            <i class="fas fa-envelope-open-text"></i>
                            <h4>Verify Your Email</h4>
                        </div>
                        <div class="card-body">
                            @if($isLocked)
                            <div class="locked-message">
                                <i class="fas fa-lock fa-2x mb-3"></i>
                                <h5>Account Temporarily Locked</h5>
                                <p class="mb-0">Too many failed attempts. Please try again in <strong id="lockoutTimer">{{ $lockoutTime }}</strong> minute(s).</p>
                            </div>
                            @else
                            <p class="text-center info-text">We've sent a 6-digit verification code to:</p>
                            <div class="email-display">
                                <i class="fas fa-envelope text-primary"></i>
                                <strong class="ms-2">{{ $email }}</strong>
                            </div>

                            <form id="verificationForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="code" class="form-label fw-bold">Enter Verification Code</label>
                                    <input
                                        type="text"
                                        class="form-control code-input"
                                        id="code"
                                        name="code"
                                        maxlength="6"
                                        placeholder="000000"
                                        required
                                        autofocus
                                        inputmode="numeric"
                                        pattern="[0-9]{6}">
                                    <div id="error-message" class="alert alert-danger mt-3" style="display: none;"></div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-verify w-100">
                                    <i class="fas fa-check-circle me-2"></i>Verify Email
                                </button>
                            </form>

                            <div class="text-center mt-4">
                                <p class="info-text mb-2">Didn't receive the code?</p>
                                <button id="resendBtn" class="btn btn-link text-decoration-none" disabled>
                                    <i class="fas fa-redo me-1"></i>
                                    Resend Code (<span class="countdown-timer" id="countdown">180</span>s)
                                </button>
                            </div>

                            <div class="text-center mt-3">
                                <span class="attempts-badge">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <span id="attemptsRemaining">{{ $remainingAttempts }}</span> attempt(s) remaining
                                </span>
                            </div>

                            <div class="alert alert-info mt-4" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>
                                    <strong>Note:</strong> Code expires in 3 minutes. After 5 failed attempts,
                                    you'll be locked out for 30 minutes.
                                </small>
                            </div>
                            @endif
                            <div id="verificationData"
                                data-expires-at="{{ $expiresAt }}"
                                data-is-locked="{{ $isLocked ? '1' : '0' }}"
                                data-lockout-time="{{ $lockoutTime }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const verificationData = document.getElementById('verificationData');
        let countdownInterval;
        let lockoutInterval;
        let expiresAt = new Date(verificationData.dataset.expiresAt);
        const isLocked = verificationData.dataset.isLocked === '1';
        const initialLockoutTime = parseInt(verificationData.dataset.lockoutTime) || 0;

        // Countdown timer for code expiry
        function startCountdown() {
            const resendBtn = document.getElementById('resendBtn');
            const countdownEl = document.getElementById('countdown');

            if (!resendBtn || !countdownEl) return;

            resendBtn.disabled = true;

            clearInterval(countdownInterval);

            countdownInterval = setInterval(() => {
                const now = new Date();
                const timeLeft = Math.floor((expiresAt - now) / 1000);

                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = '<i class="fas fa-redo me-1"></i>Resend Code';
                } else {
                    countdownEl.textContent = timeLeft;
                }
            }, 1000);
        }

        // Lockout countdown timer
        function startLockoutTimer() {
            const lockoutTimerEl = document.getElementById('lockoutTimer');

            if (!lockoutTimerEl) return;

            let remainingMinutes = initialLockoutTime;

            lockoutInterval = setInterval(() => {
                remainingMinutes--;

                if (remainingMinutes <= 0) {
                    clearInterval(lockoutInterval);
                    location.reload();
                } else {
                    lockoutTimerEl.textContent = remainingMinutes;
                }
            }, 60000);
        }

        // Initialize timers
        if (isLocked) {
            startLockoutTimer();
        } else {
            startCountdown();
        }

        // Only add input restriction for numeric only
        const codeInput = document.getElementById('code');
        if (codeInput) {
            codeInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        // Handle form submission
        const verificationForm = document.getElementById('verificationForm');
        if (verificationForm) {
            verificationForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const code = document.getElementById('code').value;
                const errorMsg = document.getElementById('error-message');
                const submitBtn = e.target.querySelector('button[type="submit"]');

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';

                try {
                    const response = await fetch("{{ route('verification.verify') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            code
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Congratulations!',
                            text: 'Your email has been verified successfully!',
                            confirmButtonText: 'Continue to Dashboard',
                            confirmButtonColor: '#4CAF50',
                            allowOutsideClick: false,
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        if (data.remaining_attempts !== undefined) {
                            document.getElementById('attemptsRemaining').textContent = data.remaining_attempts;
                        }

                        errorMsg.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + data.message;
                        errorMsg.style.display = 'block';

                        if (data.locked) {
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }

                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Verify Email';
                    }
                } catch (error) {
                    errorMsg.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>An error occurred. Please try again.';
                    errorMsg.style.display = 'block';

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Verify Email';
                }
            });
        }

        // Handle resend code
        const resendBtn = document.getElementById('resendBtn');
        if (resendBtn) {
            resendBtn.addEventListener('click', async () => {
                const errorMsg = document.getElementById('error-message');

                try {
                    const response = await fetch("{{ route('verification.resend') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        expiresAt = new Date(data.expiresAt);
                        clearInterval(countdownInterval);
                        startCountdown();

                        Swal.fire({
                            icon: 'success',
                            title: 'Code Resent!',
                            text: 'A new verification code has been sent to your email.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        errorMsg.style.display = 'none';
                    } else {
                        if (data.locked) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Account Locked',
                                text: data.message,
                                confirmButtonColor: '#d33'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to resend code. Please try again.',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    </script>
</body>

</html>