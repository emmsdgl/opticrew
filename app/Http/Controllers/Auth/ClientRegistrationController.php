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
            // 1. Determine account type
            $accountType = $request->input('account_type', 'personal');
            $isPersonal = $accountType === 'personal';
            $isCompany = $accountType === 'company';

            // 2. Build validation rules based on account type
            $rules = [
                'account_type' => ['required', 'in:personal,company'],
            ];

            // Personal accounts need security questions + username/password
            if ($isPersonal) {
                $rules = array_merge($rules, [
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
                ]);
            } else {
                // Company accounts don't need security questions or username/password
                // Just need a simple password for the inquiry record
                $rules = array_merge($rules, [
                    'username' => ['nullable'],
                    'password' => ['nullable'],
                ]);
            }

            if ($isPersonal) {
                // Calculate date 18 years ago from today
                $eighteenYearsAgo = Carbon::now()->subYears(18)->format('m-d-Y');

                $rules = array_merge($rules, [
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
                ]);
            } else {
                $rules = array_merge($rules, [
                    'company_name' => ['required', 'string', 'max:255'],
                    'contact_first_name' => ['required', 'string', 'max:255'],
                    'contact_last_name' => ['required', 'string', 'max:255'],
                    'business_id' => ['required', 'string', 'max:20'],
                    'einvoice_number' => ['required', 'string', 'max:100'],
                    'company_phone_number' => ['required', 'string', 'min:7'],
                    'company_email' => ['required', 'string', 'email', 'max:255'], // No unique constraint - companies can submit multiple inquiries
                    'company_street_address' => ['required', 'string', 'max:255'],
                    'company_postal_code' => ['required', 'string', 'size:5'],
                    'company_city' => ['required', 'string', 'max:100'],
                    'company_district' => ['required', 'string', 'max:100'],
                    'service_types' => ['required', 'array', 'min:1'],
                    'other_concerns' => ['nullable', 'string', 'max:2000'],
                ]);
            }

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
                'company_postal_code.size' => 'Postal code must be exactly 5 digits.',
                'middle_initial.max' => 'Middle initial must not exceed 5 characters.',
                'birthdate.before_or_equal' => 'You must be at least 18 years old to create an account.',
            ]);

            // 4. Use a transaction to ensure everything saves or nothing does.
            DB::transaction(function () use ($request, $isPersonal, $isCompany) {
                $user = null;

                // 5. Create User record ONLY for personal accounts
                if ($isPersonal) {
                    $user = User::create([
                        'name' => $request->first_name . ' ' . $request->last_name,
                        'username' => $request->username,
                        'email' => $request->email,
                        'phone' => $request->phone_number,
                        'email_verified_at' => session('email_is_verified') ? now() : null,
                        'password' => Hash::make($request->password),
                        'role' => 'external_client',
                    ]);
                }
                // Note: Companies do NOT get user accounts - they only submit inquiries

                // 6. Create the associated Client profile record.
                if ($isPersonal) {
                    // Personal Account
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
                        'street_address' => $request->street_address,
                        'postal_code' => $request->postal_code,
                        'city' => $request->city,
                        'district' => $request->district,
                        'address' => $fullAddress,
                        'billing_address' => $fullAddress,
                        'is_active' => true, // Personal accounts are active immediately
                        'security_question_1' => $request->security_question[0],
                        'security_answer_1' => Hash::make(strtolower($request->security_answer_1)),
                        'security_question_2' => $request->security_question[1],
                        'security_answer_2' => Hash::make(strtolower($request->security_answer_2)),
                    ]);

                    // 7. Log the new user in immediately for personal accounts
                    Auth::login($user);
                } else {
                    // Company Inquiry (NO USER ACCOUNT)
                    $fullAddress = $request->company_street_address . ', ' .
                                  $request->company_postal_code . ' ' .
                                  $request->company_city . ', ' .
                                  $request->company_district;

                    $client = Client::create([
                        'user_id' => null, // No user account for companies
                        'client_type' => 'company',
                        'company_name' => $request->company_name,
                        'email' => $request->company_email,
                        'phone_number' => $request->company_phone_number,
                        'business_id' => $request->business_id,
                        'first_name' => $request->contact_first_name,
                        'last_name' => $request->contact_last_name,
                        'middle_initial' => null,
                        'birthdate' => null,
                        'street_address' => $request->company_street_address,
                        'postal_code' => $request->company_postal_code,
                        'city' => $request->company_city,
                        'district' => $request->company_district,
                        'address' => $fullAddress,
                        'billing_address' => $fullAddress,
                        'einvoice_number' => $request->einvoice_number,
                        'is_active' => false, // Company inquiries require admin approval
                        'security_question_1' => null,
                        'security_answer_1' => null,
                        'security_question_2' => null,
                        'security_answer_2' => null,
                    ]);

                    // Create service inquiry appointment
                    ClientAppointment::create([
                        'client_id' => $client->id,
                        'is_company_inquiry' => true,
                        'booking_type' => 'company_inquiry',
                        'service_type' => 'Company Service Inquiry',
                        'company_service_types' => json_encode($request->service_types),
                        'other_concerns' => $request->other_concerns,
                        'service_date' => now()->format('Y-m-d'),
                        'service_time' => now()->format('H:i:s'),
                        'number_of_units' => 0,
                        'unit_size' => 'N/A',
                        'cabin_name' => $request->company_name,
                        'special_requests' => 'Company service inquiry - Quotation required',
                        'quotation' => 0.00,
                        'vat_amount' => 0.00,
                        'total_amount' => 0.00,
                        'status' => 'pending',
                    ]);

                    // Company accounts are NOT logged in automatically
                }
            });

            // 8. Redirect based on account type.
            if ($isPersonal) {
                // Personal accounts are logged in and redirected to dashboard
                return redirect()->route('client.dashboard')
                    ->with('success', 'Registration successful! Welcome to OptiCrew.');
            } else {
                // Company inquiries redirect to homepage with thank you message
                return redirect()->route('home')
                    ->with('success', 'Thank you for your inquiry! Our team will review your service requirements and send a detailed quotation to your email within 24-48 hours.')
                    ->with('info', 'We look forward to serving your business needs.');
            }

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