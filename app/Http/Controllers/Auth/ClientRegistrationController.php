<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client; // Make sure you have a Client model
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
            // 1. Validate all the data coming from your multi-step form.
            $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'middle_initial' => ['nullable', 'string', 'max:5'],
                'birthdate' => ['required', 'date_format:m-d-Y'],
                'phone_number' => ['required', 'string', 'min:7'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'street_address' => ['required', 'string', 'max:255'],
                'postal_code' => ['required', 'string', 'size:5'],
                'city' => ['required', 'string', 'max:100'],
                'district' => ['required', 'string', 'max:100'],
                'username' => ['required', 'string', 'max:255', 'unique:users,name'], // Validate username against the 'name' column
                'password' => ['required', 'confirmed', Password::min(8)],
                'security_question' => ['required', 'array', 'size:2'],
                'security_answer_1' => ['required', 'string', 'min:3'],
                'security_answer_2' => ['required', 'string', 'min:3'],
            ], [
                'email.unique' => 'This email is already registered. Please use a different email or log in to your existing account.',
                'username.unique' => 'This username is already taken. Please choose a different username.',
                'password.confirmed' => 'Password confirmation does not match.',
                'postal_code.size' => 'Postal code must be exactly 5 digits.',
                'middle_initial.max' => 'Middle initial must not exceed 5 characters.',
            ]);

            // 2. Use a transaction to ensure everything saves or nothing does.
            DB::transaction(function () use ($request) {
            
            // 3. Create the User record first.
            $user = User::create([
                'name' => $request->username, // The 'name' column in users table stores the username
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'external_client',
            ]);
    
            // 4. Create the associated Client profile record.
            // Build full address from components
            $fullAddress = $request->street_address . ', ' .
                          $request->postal_code . ' ' .
                          $request->city . ', ' .
                          $request->district;

            $user->client()->create([
                'client_type' => 'personal',
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_initial' => $request->middle_initial,
                'birthdate' => Carbon::createFromFormat('m-d-Y', $request->birthdate)->format('Y-m-d'),
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'street_address' => $request->street_address,
                'postal_code' => $request->postal_code,
                'city' => $request->city,
                'district' => $request->district,
                'address' => $fullAddress,
                'billing_address' => $fullAddress,
                'is_active' => true,
                'security_question_1' => $request->security_question[0],
                'security_answer_1' => Hash::make(strtolower($request->security_answer_1)), // Hash the answer
                'security_question_2' => $request->security_question[1],
                'security_answer_2' => Hash::make(strtolower($request->security_answer_2)), // Hash the answer
            ]);
    
                // 5. Log the new user in.
                Auth::login($user);
            });

            // 6. Redirect to the client's dashboard.
            return redirect()->route('client.dashboard')->with('success', 'Registration successful! Welcome to OptiCrew.');

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

}