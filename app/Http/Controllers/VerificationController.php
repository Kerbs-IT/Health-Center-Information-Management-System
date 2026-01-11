<?php

namespace App\Http\Controllers;

use App\Models\patients;
use App\Models\User;
use App\Models\users_address;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    protected $verificationService;

    public function __construct(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * Show verification page
     */
    public function show()
    {
        $userId = session('verification_user_id');

        if (!$userId) {
            return redirect()->route('register')
                ->with('error', 'Please register first.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('register')
                ->with('error', 'User not found. Please register again.');
        }

        if ($user->is_verified) {
            Auth::login($user);
            return redirect()->route('dashboard.patient')
                ->with('success', 'Your email is already verified.');
        }

        // Check if locked out
        $isLocked = $this->verificationService->isLockedOut($user);
        $lockoutTime = $isLocked ? $this->verificationService->getRemainingLockoutTime($user) : 0;
        $remainingAttempts = $this->verificationService->getRemainingAttempts($user);

        return view('auth.verify-email', [
            'email' => $user->email,
            'expiresAt' => $user->verification_code_expires_at,
            'isLocked' => $isLocked,
            'lockoutTime' => $lockoutTime,
            'remainingAttempts' => $remainingAttempts,
        ]);
    }

    /**
     * Verify the code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $userId = session('verification_user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $result = $this->verificationService->verifyCode($user, $request->code);

        if ($result['success']) {
            // Get pending registration data from session
            $registrationData = session('pending_registration');

            if (!$registrationData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration data not found. Please register again.'
                ], 400);
            }

            // NOW create the full user account with all data
            $user->update([
                'first_name' => ucwords(strtolower($registrationData['first_name'])),
                'middle_initial' => ucwords(strtolower($registrationData['middle_initial'] ?? '')),
                'last_name' => ucwords(strtolower($registrationData['last_name'])),
                'patient_type' => $registrationData['patient_type'],
                'date_of_birth' => $registrationData['date_of_birth'],
                'contact_number' => $registrationData['contact_number'],
                'address' => $registrationData['full_address'],
                'status' => 'active', // Change from Pending to Active
                'is_verified' => true,
                'email_verified_at' => now(),
                'verification_code' => null,
                'verification_code_expires_at' => null,
                'verification_attempts' => 0,
                'verification_locked_until' => null,
                'suffix' => $registrationData['suffix']
            ]);

            // Create users_address record
            $address = users_address::create([
                'user_id' => $user->id,
                'patient_id' => null, // Will be updated after patient creation
                'house_number' => $registrationData['house_number'],
                'street' => $registrationData['street'],
                'purok' => $registrationData['brgy'],
                'postal_code' => '4109',
                'latitude' => null,
                'longitude' => null,
            ]);



            // Log the user in
            Auth::login($user);

            // Clear session data
            session()->forget(['verification_user_id', 'pending_registration']);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'redirect' => route('dashboard.patient')
            ]);
        }

        return response()->json($result, 400);
    }

    /**
     * Resend verification code
     */
    public function resend()
    {
        $userId = session('verification_user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Check if locked out
        if ($this->verificationService->isLockedOut($user)) {
            $lockoutTime = $this->verificationService->getRemainingLockoutTime($user);
            return response()->json([
                'success' => false,
                'message' => "Account is locked. Please try again in {$lockoutTime} minutes.",
                'locked' => true,
                'lockoutTime' => $lockoutTime,
            ], 429);
        }

        // Generate and send new code
        $this->verificationService->generateAndSendCode($user);

        return response()->json([
            'success' => true,
            'message' => 'A new verification code has been sent to your email.',
            'expiresAt' => $user->fresh()->verification_code_expires_at->toIso8601String(),
        ]);
    }
}
