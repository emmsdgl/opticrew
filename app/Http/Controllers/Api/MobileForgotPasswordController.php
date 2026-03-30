<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class MobileForgotPasswordController extends Controller
{
    /**
     * Step 1: Request password reset.
     * Validates the email exists, checks if Google-linked, and sends OTP.
     *
     * 3FA Flow:
     *   Factor 1 - Email identification (this step)
     *   Factor 2 - Google account verification (step 2, if linked)
     *   Factor 3 - OTP sent to email (step 2 or after Google verify)
     */
    public function request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Find user by email or alternative_email
        $user = User::where('email', $request->email)
            ->orWhere('alternative_email', $request->email)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'We couldn\'t find an account with that email address. Please check and try again.',
            ], 422);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'This account has been deactivated. Please contact support.',
            ], 422);
        }

        // Check if user has a linked Google account
        $hasGoogle = !empty($user->google_id);

        if (!$hasGoogle) {
            // No Google linked — cannot perform self-service reset.
            // The 3FA flow requires: (1) email, (2) Google identity, (3) OTP.
            // Without a linked Google account, factor 2 cannot be verified.
            // Admin must manually reset the password after physically verifying identity.
            return response()->json([
                'message' => 'Password reset requires a linked Google account. Please contact your administrator to reset your password.',
            ], 422);
        }

        // No real inbox for @finnoys.com — verify the employee has a real email on file
        if (empty($user->alternative_email)) {
            return response()->json([
                'message' => 'No recovery email has been set up for this account. Please contact your administrator for help.',
            ], 422);
        }

        // Store a temporary token so the Google verify step knows which user
        $verifyToken = bin2hex(random_bytes(32));
        Cache::put("pwd_reset_google_verify:{$verifyToken}", $user->id, now()->addMinutes(15));

        return response()->json([
            'requires_google_verify' => true,
            'verify_token' => $verifyToken,
            'message' => 'Your account is linked to Google. Please verify your identity.',
        ]);
    }

    /**
     * Step 2: Verify OTP code.
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $user = User::where('email', $request->email)
            ->orWhere('alternative_email', $request->email)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'We couldn\'t find an account with that email address. Please check and try again.'], 422);
        }

        $cacheKey = "pwd_reset_otp:{$user->id}";
        $storedOtp = Cache::get($cacheKey);

        if (!$storedOtp) {
            return response()->json([
                'message' => 'Your verification code has expired. Please request a new one.',
            ], 408);
        }

        if ($request->otp != $storedOtp) {
            return response()->json([
                'message' => 'The verification code you entered is incorrect. Please try again.',
            ], 422);
        }

        // OTP is valid — mark as verified and remove OTP
        Cache::forget($cacheKey);
        $resetToken = bin2hex(random_bytes(32));
        Cache::put("pwd_reset_verified:{$resetToken}", $user->id, now()->addMinutes(10));

        return response()->json([
            'message' => 'Verification successful.',
            'reset_token' => $resetToken,
        ]);
    }

    /**
     * Step 3: Reset password with verified token.
     * Requires the reset_token issued by verifyOtp() — not the raw OTP.
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reset_token' => 'required|string',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'password.confirmed' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Validate the reset token issued after OTP verification
        $userId = Cache::get("pwd_reset_verified:{$request->reset_token}");

        if (!$userId) {
            return response()->json([
                'message' => 'Your password reset link has expired. Please start over from the beginning.',
            ], 422);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'We couldn\'t find your account. Please try again or contact your administrator.'], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Revoke all existing tokens (force re-login)
        $user->tokens()->delete();

        // Clean up cache
        Cache::forget("pwd_reset_verified:{$request->reset_token}");

        return response()->json([
            'message' => 'Your password has been reset successfully. Please log in with your new password.',
        ]);
    }

    /**
     * Generate a 6-digit OTP, store in cache, and send via email.
     */
    private function generateAndSendOTP(User $user)
    {
        $otp = random_int(100000, 999999);

        // Store OTP in cache for 5 minutes (keyed by user ID)
        Cache::put("pwd_reset_otp:{$user->id}", $otp, now()->addMinutes(5));

        // Always send OTP to the employee's verified Google account email.
        // @finnoys.com addresses have no inbox — never send there.
        Mail::to($user->alternative_email)->send(new EmailVerificationOtp($otp));

        return $otp;
    }
}
