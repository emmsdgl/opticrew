<?php

namespace App\Http\Livewire\Auth;

use App\Mail\EmailVerificationOtp;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

/**
 * Web (Livewire) implementation of the mobile-style 4-step forgot-password flow.
 *
 * Adapted from the React Native ForgotPasswordScreen and MobileForgotPasswordController.
 * Kept fully separate from the mobile API: cache keys use the `web_pwd_reset_*` prefix
 * so a user could (in theory) have a mobile reset and a web reset in flight simultaneously
 * without collision. The Google verification HTTP step lives in GoogleAuthController under
 * the `web_forgot_password_verify` purpose.
 *
 * Steps:
 *   1 = Email
 *   2 = Google Verify (full-page redirect, returns via session flash)
 *   3 = OTP
 *   4 = New Password
 */
class ForgotPasswordWizard extends Component
{
    public int $step = 1;
    public string $email = '';
    public string $otp = '';
    public string $newPassword = '';
    public string $confirmPassword = '';
    public string $resetToken = '';

    public ?string $errorMessage = null;
    public ?string $successMessage = null;

    public function mount(): void
    {
        // Returning from a successful Google verify — jump straight to the OTP step.
        if (session()->has('web_forgot_password_verified_email')) {
            $this->email = (string) session('web_forgot_password_verified_email');
            $this->step = 3;
            session()->forget('web_forgot_password_verified_email');
            return;
        }

        // Returning from a failed Google verify — show the error and stay on step 2 if
        // we have the email, otherwise drop the user back on step 1.
        if (session()->has('web_forgot_password_error')) {
            $this->errorMessage = (string) session('web_forgot_password_error');
            session()->forget('web_forgot_password_error');

            if (session()->has('web_forgot_password_email')) {
                $this->email = (string) session('web_forgot_password_email');
                $this->step = 2;
                session()->forget('web_forgot_password_email');
            }
        }
    }

    public function backTo(int $step): void
    {
        $this->resetMessages();
        if ($step >= 1 && $step < $this->step) {
            $this->step = $step;
        }
    }

    /** Step 1 → 2: validate email, confirm Google linking, then advance to Google verify. */
    public function submitEmail(): void
    {
        $this->resetMessages();
        $this->validate(['email' => 'required|email']);

        $user = User::where('email', $this->email)
            ->orWhere('alternative_email', $this->email)
            ->first();

        if (!$user) {
            $this->errorMessage = "We couldn't find an account with that email address. Please check and try again.";
            return;
        }

        if (!$user->is_active) {
            $this->errorMessage = 'This account has been deactivated. Please contact support.';
            return;
        }

        if (empty($user->google_id)) {
            $this->errorMessage = 'Password reset requires a linked Google account. Please contact your administrator to reset your password.';
            return;
        }

        if (empty($user->alternative_email)) {
            $this->errorMessage = 'No recovery email has been set up for this account. Please contact your administrator for help.';
            return;
        }

        $this->step = 2;
    }

    /** Step 3: validate OTP from cache and issue a reset_token for the final step. */
    public function verifyOtp(): void
    {
        $this->resetMessages();

        if (strlen($this->otp) !== 6 || !ctype_digit($this->otp)) {
            $this->errorMessage = 'Please enter the complete 6-digit verification code.';
            return;
        }

        $user = User::where('email', $this->email)
            ->orWhere('alternative_email', $this->email)
            ->first();

        if (!$user) {
            $this->errorMessage = "We couldn't find your account. Please start over.";
            return;
        }

        $cacheKey = "web_pwd_reset_otp:{$user->id}";
        $stored = Cache::get($cacheKey);

        if (!$stored) {
            $this->errorMessage = 'Your verification code has expired. Please request a new one.';
            return;
        }

        if ((string) $this->otp !== (string) $stored) {
            $this->errorMessage = 'The verification code you entered is incorrect. Please try again.';
            return;
        }

        Cache::forget($cacheKey);

        $resetToken = bin2hex(random_bytes(32));
        Cache::put("web_pwd_reset_verified:{$resetToken}", $user->id, now()->addMinutes(10));

        $this->resetToken = $resetToken;
        $this->step = 4;
    }

    /** Step 3 helper: regenerate and re-send the OTP. */
    public function resendOtp(): void
    {
        $this->resetMessages();

        $user = User::where('email', $this->email)
            ->orWhere('alternative_email', $this->email)
            ->first();

        if (!$user || empty($user->alternative_email)) {
            $this->errorMessage = 'Unable to resend the code. Please start over.';
            return;
        }

        $otp = random_int(100000, 999999);
        Cache::put("web_pwd_reset_otp:{$user->id}", $otp, now()->addMinutes(5));

        Mail::to($user->alternative_email)->send(new EmailVerificationOtp($otp));

        $this->otp = '';
        $this->successMessage = 'A new verification code has been sent to your email.';
        $this->dispatchBrowserEvent('web-fp-resent');
    }

    /** Step 4: validate the new password and persist it. */
    public function resetPassword(): void
    {
        $this->resetMessages();

        $this->validate([
            'newPassword' => [
                'required',
                'string',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
                'same:confirmPassword',
            ],
        ], [
            'newPassword.required' => 'Please enter a new password.',
            'newPassword.same' => 'Passwords do not match.',
            'newPassword.min' => 'Password must be at least 8 characters.',
            'newPassword.letters' => 'Password must contain at least one letter.',
            'newPassword.mixed_case' => 'Password must contain both uppercase and lowercase letters.',
            'newPassword.numbers' => 'Password must contain at least one number.',
            'newPassword.symbols' => 'Password must contain at least one special character.',
        ]);

        $userId = Cache::get("web_pwd_reset_verified:{$this->resetToken}");

        if (!$userId) {
            $this->errorMessage = 'Your password reset session has expired. Please start over from the beginning.';
            return;
        }

        $user = User::find($userId);

        if (!$user) {
            $this->errorMessage = "We couldn't find your account. Please start over.";
            return;
        }

        $user->password = Hash::make($this->newPassword);
        $user->save();

        Cache::forget("web_pwd_reset_verified:{$this->resetToken}");

        $this->successMessage = 'Your password has been reset successfully. Redirecting to login...';
        $this->dispatchBrowserEvent('web-fp-success');
    }

    private function resetMessages(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;
    }

    public function render()
    {
        return view('livewire.auth.forgot-password-wizard');
    }
}
