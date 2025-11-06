<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client; // Make sure you have a Client model
use App\Models\ClientAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationOtp;
use Carbon\Carbon;

class ClientRegistrationController extends Controller
{
    /**
     * Handle an incoming client registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Account type is always personal (company signup removed)
            $accountType = 'personal';

            // Calculate date 18 years ago from today
            $eighteenYearsAgo = Carbon::now()->subYears(18)->format('m-d-Y');

            // Validation rules for personal account registration
            $rules = [
                'account_type' => ['required', 'in:personal'],
                'username' => ['required', 'string', 'min:6', 'max:8', 'unique:users,username'],
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                ],
                'security_question' => ['required', 'array', 'size:2'],
                'security_answer_1' => ['required', 'string', 'min:3'],
                'security_answer_2' => ['required', 'string', 'min:3'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'middle_initial' => ['nullable', 'string', 'max:5'],
                'birthdate' => ['required', 'date_format:m-d-Y', 'before_or_equal:' . $eighteenYearsAgo],
                'phone_number' => ['required', 'string', 'min:7'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'street_address' => ['required', 'string', 'max:255'],
                'postal_code' => ['required', 'string', 'size:5'],
                'city' => ['required', 'string', 'max:100'],
                'district' => ['required', 'string', 'max:100'],
            ];

            // 3. Validate the data
            $request->validate($rules, [
                'email.unique' => 'This email is already registered. Please use a different email or log in to your existing account.',
                'username.unique' => 'This username is already taken. Please choose a different username.',
                'username.min' => 'Username must be at least 6 characters.',
                'username.max' => 'Username must not exceed 8 characters.',
                'password.confirmed' => 'Password confirmation does not match.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.letters' => 'Password must contain at least one letter.',
                'password.mixed_case' => 'Password must contain at least one uppercase and one lowercase letter.',
                'password.numbers' => 'Password must contain at least one number.',
                'password.symbols' => 'Password must contain at least one special character (!@#$%^&*).',
                'postal_code.size' => 'Postal code must be exactly 5 digits.',
                'middle_initial.max' => 'Middle initial must not exceed 5 characters.',
                'birthdate.before_or_equal' => 'You must be at least 18 years old to create an account.',
            ]);

            // Use a transaction to ensure everything saves or nothing does
            DB::transaction(function () use ($request) {
                // Apply Title Case formatting to name fields
                $firstName = $this->formatTitleCase($request->first_name);
                $lastName = $this->formatTitleCase($request->last_name);
                $middleInitial = $request->middle_initial ? strtoupper($request->middle_initial) : null;

                // Apply Title Case to address fields
                $streetAddress = $this->formatTitleCase($request->street_address);
                $city = $this->formatTitleCase($request->city);
                $district = $this->formatTitleCase($request->district);

                // Create User record (always personal account)
                // Email is already verified via OTP in Step 2, so always set email_verified_at
                $user = User::create([
                    'name' => $firstName . ' ' . $lastName,
                    'username' => $request->username,
                    'email' => strtolower($request->email), // Always lowercase for emails
                    'phone' => $request->phone_number,
                    'email_verified_at' => now(), // Always set - OTP verification confirms email ownership
                    'password' => Hash::make($request->password),
                    'role' => 'external_client',
                ]);

                // Create the associated Client profile record
                $fullAddress = $streetAddress . ', ' .
                              $request->postal_code . ' ' .
                              $city . ', ' .
                              $district;

                $user->client()->create([
                    'client_type' => 'personal',
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'middle_initial' => $middleInitial,
                    'birthdate' => Carbon::createFromFormat('m-d-Y', $request->birthdate)->format('Y-m-d'),
                    'street_address' => $streetAddress,
                    'postal_code' => $request->postal_code,
                    'city' => $city,
                    'district' => $district,
                    'address' => $fullAddress,
                    'billing_address' => $fullAddress,
                    'is_active' => true,
                    'security_question_1' => $request->security_question[0],
                    'security_answer_1' => Hash::make(strtolower($request->security_answer_1)),
                    'security_question_2' => $request->security_question[1],
                    'security_answer_2' => Hash::make(strtolower($request->security_answer_2)),
                ]);

                // Log the new user in immediately
                Auth::login($user);
            });

            // Redirect to dashboard with success message
            return redirect()->route('client.dashboard')
                ->with('success', 'Registration successful! Welcome to OptiCrew.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to show validation errors
            throw $e;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Client registration error: ' . $e->getMessage(), [
                'exception' => $e,
                'user_data' => $request->except(['password', 'password_confirmation'])
            ]);

            // Redirect back with error message
            return redirect()->back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['error' => 'An error occurred during registration. Please try again. If the problem persists, contact support.']);
        }
    }

    /**
     * Send OTP to the user's email.
     */
    public function sendOtp(Request $request)
    {
        $request->validate(
            ['email' => 'required|email|unique:users,email'],
            [
                'email.required' => 'Email address is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already registered. Please use a different email or log in to your existing account.'
            ]
        );

        $otp = random_int(100000, 999999); // Generate a 6-digit OTP

        // Store OTP and email in the session for verification later
        session(['otp' => $otp, 'registration_email' => $request->email]);

        // Send the email
        Mail::to($request->email)->send(new EmailVerificationOtp($otp));

        return response()->json(['message' => 'OTP sent successfully.']);
    }

    /**
     * Verify the OTP entered by the user.
     */
    public function verifyOtp(Request $request)
    {
        // Get the OTP from the request. It could be an array or a string.
        $userOtp = $request->input('otp');
    
        // If the JavaScript sent an array of digits, implode it into a single string.
        if (is_array($userOtp)) {
            $userOtp = implode('', $userOtp);
        }
    
        // Now that we are sure $userOtp is a string, we can validate it.
        // We manually create a validator for this.
        $validator = \Illuminate\Support\Facades\Validator::make(['otp' => $userOtp], [
            'otp' => 'required|string|digits:6'
        ]);
    
        // If the validation fails (e.g., OTP is not 6 digits), return an error.
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
    
        // Now, compare the validated user OTP with the one in the session.
        $sessionOtp = session('otp');
    
        if ($userOtp == $sessionOtp) {
            // OTP is correct, mark email as verified in the session.
            session(['email_is_verified' => true]);
            return response()->json(['message' => 'OTP verified successfully.']);
        }
    
        return response()->json(['message' => 'The provided OTP is invalid.'], 422);
    }

    /**
     * Format a string to Title Case (first letter of each word capitalized)
     * Handles multi-word strings properly
     *
     * @param string $text
     * @return string
     */
    private function formatTitleCase($text)
    {
        if (!$text) {
            return $text;
        }

        // Convert to lowercase first, then capitalize first letter of each word
        // mb_convert_case handles UTF-8 properly for international characters
        return mb_convert_case(trim($text), MB_CASE_TITLE, 'UTF-8');
    }

}