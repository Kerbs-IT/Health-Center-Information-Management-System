<?php

namespace App\Services;

use App\Models\User;
use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Mail;

class VerificationService
{
    const MAX_ATTEMPTS = 5;
    const LOCKOUT_MINUTES = 30;
    const CODE_EXPIRY_MINUTES = 3;

    /**
     * Generate and send verification code
     */
    public function generateAndSendCode(User $user): void
    {
        $code = $this->generateCode();

        $user->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(self::CODE_EXPIRY_MINUTES),
            'verification_attempts' => 0,
            'verification_locked_until' => null,
        ]);

        Mail::to($user->email)->send(new VerificationMail($code, $user->name));
    }

    /**
     * Generate 6-digit code
     */
    private function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if user is locked out
     */
    public function isLockedOut(User $user): bool
    {
        if (!$user->verification_locked_until) {
            return false;
        }

        if (now()->lessThan($user->verification_locked_until)) {
            return true;
        }

        // Lock expired, reset it
        $user->update([
            'verification_locked_until' => null,
            'verification_attempts' => 0,
        ]);

        return false;
    }

    /**
     * Get remaining lockout time in minutes
     */
    public function getRemainingLockoutTime(User $user): int
    {
        if (!$user->verification_locked_until) {
            return 0;
        }

        $remaining = now()->diffInMinutes($user->verification_locked_until, false);
        return max(0, ceil($remaining));
    }

    /**
     * Verify the code
     */
    public function verifyCode(User $user, string $code): array
    {
        // Check if locked out
        if ($this->isLockedOut($user)) {
            return [
                'success' => false,
                'message' => 'Too many failed attempts. Please try again in ' .
                    $this->getRemainingLockoutTime($user) . ' minutes.',
                'locked' => true,
            ];
        }

        // Check if code expired
        if (now()->greaterThan($user->verification_code_expires_at)) {
            return [
                'success' => false,
                'message' => 'Verification code has expired. Please request a new code.',
                'expired' => true,
            ];
        }

        // Check if code matches
        if ($user->verification_code !== $code) {
            $user->increment('verification_attempts');

            // Check if max attempts reached
            if ($user->verification_attempts >= self::MAX_ATTEMPTS) {
                $user->update([
                    'verification_locked_until' => now()->addMinutes(self::LOCKOUT_MINUTES),
                ]);

                return [
                    'success' => false,
                    'message' => 'Maximum attempts exceeded. Your account is locked for ' .
                        self::LOCKOUT_MINUTES . ' minutes.',
                    'locked' => true,
                ];
            }

            $remainingAttempts = self::MAX_ATTEMPTS - $user->verification_attempts;

            return [
                'success' => false,
                'message' => "Invalid verification code. You have {$remainingAttempts} attempt(s) remaining.",
                'remaining_attempts' => $remainingAttempts,
            ];
        }

        // Code is correct, verify user
        $user->update([
            'is_verified' => true,
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
            'verification_attempts' => 0,
            'verification_locked_until' => null,
        ]);

        return [
            'success' => true,
            'message' => 'Email verified successfully!',
        ];
    }

    /**
     * Get remaining attempts
     */
    public function getRemainingAttempts(User $user): int
    {
        return max(0, self::MAX_ATTEMPTS - $user->verification_attempts);
    }
}
