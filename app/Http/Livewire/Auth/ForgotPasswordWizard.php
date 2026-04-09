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

    /** Realtime email existence check: null = unknown, true = exists, false = not found. */
    public ?bool $emailExists = null;

    protected $listeners = ['fp-prev-step' => 'previousStep'];

    public function previousStep(): void
    {
        $this->resetMessages();
        if ($this->step > 1) {
            $this->step = 1;
        }
    }

    public function updatedEmail(): void
    {
        $this->checkEmailExists();
    }

    public function checkEmailExists(): void
    {
        $email = trim($this->email);
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->emailExists = null;
            return;
        }

        $this->emailExists = User::where('email', $email)
            ->orWhere('alternative_email', $email)
            ->exists();
    }

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
            $this->dispatchBrowserEvent('fp-error', ['title' => 'Account Not Found', 'message' => "We couldn't find an account with that email address. Please check and try again."]);
            return;
        }

        if (!$user->is_active) {
            $this->dispatchBrowserEvent('fp-error', ['title' => 'Account Deactivated', 'message' => 'This account has been deactivated. Please contact support.']);
            return;
        }

        if (empty($user->google_id)) {
            $this->dispatchBrowserEvent('fp-error', ['title' => 'Google Account Required', 'message' => 'Password reset requires a linked Google account. Please contact your administrator to reset your password.']);
            return;
        }

        $this->step = 2;
    }

    /** Step 3: validate OTP from cache and issue a reset_token for the final step. */
    public function verifyOtp(): void
    {
        $this->resetMessages();

        // Silently ignore empty submissions (e.g. triggered by clearing inputs after a resend).
        if ($this->otp === '' || $this->otp === null) {
            return;
        }

        if (strlen($this->otp) !== 6 || !ctype_digit($this->otp)) {
            $this->dispatchBrowserEvent('fp-error', ['title' => 'Incomplete Code', 'message' => 'Please enter the complete 6-digit verification code.']);
            $this->dispatchBrowserEvent('web-fp-otp-clear');
            return;
        }

        $user = User::where('email', $this->email)
            ->orWhere('alternative_email', $this->email)
            ->first();

        if (!$user) {
            $this->dispatchBrowserEvent('fp-error', ['title' => 'Session Expired', 'message' => "We couldn't find your account. Please start over."]);
            return;
        }

        $cacheKey = "web_pwd_reset_otp:{$user->id}";
        $stored = Cache::get($cacheKey);

        if (!$stored) {
            $this->dispatchBrowserEvent('fp-error', ['title' => 'Code Expired', 'message' => 'Your verification code has expired. Please request a new one.']);
            $this->dispatchBrowserEvent('web-fp-otp-clear');
            return;
        }

        if ((string) $this->otp !== (string) $stored) {
            $this->dispatchBrowserEvent('fp-error', ['title' => 'Incorrect Code', 'message' => 'The verification code you entered is incorrect. Please try again.']);
            $this->dispatchBrowserEvent('web-fp-otp-clear');
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

        if (!$user) {
            $this->errorMessage = 'Unable to resend the code. Please start over.';
            return;
        }

        // Throttle: refuse if a code was sent in the last 60s (matches OTP TTL and
        // keeps us under upstream mail-provider rate limits like Mailtrap testing).
        $throttleKey = "web_pwd_reset_otp_throttle:{$user->id}";
        if (Cache::has($throttleKey)) {
            $this->dispatchBrowserEvent('fp-error', [
                'title' => 'Please Wait',
                'message' => 'A verification code was just sent. Please wait a moment before requesting another.',
            ]);
            return;
        }

        $recoveryEmail = ! empty($user->alternative_email) ? $user->alternative_email : $user->email;

        $otp = random_int(100000, 999999);
        Cache::put("web_pwd_reset_otp:{$user->id}", $otp, now()->addSeconds(60));
        Cache::put($throttleKey, true, now()->addSeconds(60));

        try {
            Mail::to($recoveryEmail)->send(new EmailVerificationOtp($otp));
        } catch (\Throwable $e) {
            Cache::forget($throttleKey);
            \Log::error('Forgot-password OTP mail failed: ' . $e->getMessage());
            $this->dispatchBrowserEvent('fp-error', [
                'title' => 'Email Delivery Failed',
                'message' => 'We were unable to send the verification code right now. Please try again in a moment.',
            ]);
            return;
        }

        $this->otp = '';
        $this->dispatchBrowserEvent('web-fp-resent');
        $this->dispatchBrowserEvent('fp-success', [
            'title' => 'Verification Code Sent',
            'message' => 'A new verification code has been sent to your email.',
        ]);
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
        $this->dispatchBrowserEvent('fp-step-changed', ['step' => $this->step]);
        return view('livewire.auth.forgot-password-wizard');
    }
}
