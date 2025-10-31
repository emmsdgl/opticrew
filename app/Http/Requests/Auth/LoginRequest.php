<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'], // Changed 'email' to 'login' and removed 'email' rule
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginInput = $this->input('login');
        $password = $this->input('password');
        $remember = $this->boolean('remember');

        // 1. If input is an email format, try email first
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            if (Auth::attempt(['email' => $loginInput, 'password' => $password], $remember)) {
                RateLimiter::clear($this->throttleKey());
                return;
            }
        } else {
            // 2. Try username
            if (Auth::attempt(['username' => $loginInput, 'password' => $password], $remember)) {
                RateLimiter::clear($this->throttleKey());
                return;
            }

            // 3. Try name as fallback
            if (Auth::attempt(['name' => $loginInput, 'password' => $password], $remember)) {
                RateLimiter::clear($this->throttleKey());
                return;
            }
        }

        // 4. If all attempts fail, throw validation error
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'login' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        // Use 'login' input for throttling
        return Str::transliterate(Str::lower($this->input('login')).'|'.$this->ip());
    }
}
