<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    //

    public function reset($id)
    {
        try {
            $user = User::findOrFail($id);
            $newPassword = $this->generateSecurePassword();

            $hashPassword = Hash::make($newPassword);

            $user->update([
                'password' => $hashPassword
            ]);

            // Send password via email
            try {
                Mail::to($user->email)->send(new PasswordResetMail($user, $newPassword));

                return response()->json([
                    'success' => true,
                    'message' => 'Password reset successfully. New password has been sent to user\'s email.'
                ], 200);
            } catch (\Exception $mailException) {
                // Log the mail error but still return success since password was updated
                // \Log::error('Failed to send password reset email: ' . $mailException->getMessage());

                return response()->json([
                    'success' => true,
                    'message' => 'Password reset successfully, but failed to send email. Please contact user directly.',
                    'email_sent' => false
                ], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password'
            ], 400);
        }
    }

    private function generateSecurePassword($length = 12){
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%&*';

        $password = '';
        $password .= $uppercase[random_int(0,strlen($uppercase)-1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        $allChars = $uppercase . $lowercase . $numbers . $special;

        for($i = 4; $i < $length;$i++){
            $password .= $allChars[random_int(0,strlen($allChars)-1)];
        }

        return str_shuffle($password);
    }
}
