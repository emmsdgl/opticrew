<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\QuotationSetting;
use App\Models\UserActivityLog;
use App\Mail\QuotationConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google's OAuth page (Login - Client role).
     */
    public function redirect()
    {
        session(['google_auth_purpose' => 'login']);
        return Socialite::driver('google')->redirect();
    }

    /**
     * Store resume and job info, then redirect to Google OAuth (Recruitment - Applicant role).
     */
    public function recruitmentApply(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string|max:255',
            'job_type' => 'nullable|string|max:50',
        ]);

        // Verify terms and policy acceptance
        if (!$request->cookie('finnoys_terms_accepted') || !$request->cookie('finnoys_policy_accepted')) {
            return redirect()->route('recruitment')
                ->with('error', 'Please accept both Terms and Conditions and Privacy Policy before applying.');
        }

        // Store job application data in session
        session([
            'google_auth_purpose' => 'recruitment',
            'recruitment_data' => [
                'job_title' => $request->job_title,
                'job_type' => $request->job_type,
                'required_docs' => json_decode($request->required_docs, true) ?? [],
            ],
        ]);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(Request $request)
    {
        try {
            // Build the Google callback URL based on the current host
            $host = $request->getHost();
            if (str_contains($host, 'finnoys.com')) {
                $callbackUrl = 'https://finnoys.com/auth/google/callback';
            } elseif (str_contains($host, 'ngrok')) {
                $callbackUrl = 'https://' . $host . '/opticrew/public/auth/google/callback';
            } else {
                $callbackUrl = 'http://127.0.0.1:8000/auth/google/callback';
            }
            $googleUser = Socialite::driver('google')
                ->redirectUrl($callbackUrl)
                ->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth callback failed', [
                'message' => $e->getMessage(),
                'host' => $request->getHost(),
                'callbackUrl' => $callbackUrl ?? 'not set',
                'purpose' => session('google_auth_purpose', 'unknown'),
            ]);

            $redirectRoute = session('google_auth_purpose') === 'recruitment' ? 'recruitment' : 'login';
            return redirect()->route($redirectRoute)->with('error', 'Google authentication failed. Please try again.');
        }

        $purpose = session('google_auth_purpose', 'login');

        if ($purpose === 'recruitment') {
            return $this->handleRecruitmentCallback($googleUser);
        }

        if ($purpose === 'quotation') {
            return $this->handleQuotationCallback($googleUser);
        }

        if ($purpose === 'link_account') {
            session()->forget('google_auth_purpose');
            return $this->handleLinkAccountCallback($googleUser);
        }

        if ($purpose === 'mobile_login') {
            return $this->handleMobileLoginCallback($googleUser);
        }

        if ($purpose === 'mobile_link') {
            return $this->handleMobileLinkCallback($googleUser);
        }

        if ($purpose === 'mobile_verify') {
            return $this->handleMobileVerifyCallback($googleUser);
        }

        if ($purpose === 'web_forgot_password_verify') {
            return $this->handleWebForgotPasswordVerifyCallback($googleUser);
        }

        return $this->handleLoginCallback($googleUser);
    }

    /**
     * Handle Google callback for Login (all roles).
     */
    private function handleLoginCallback($googleUser)
    {
        // 0. Check if account was deleted by admin (soft-deleted) — by google_id or email
        $trashedUser = User::onlyTrashed()
            ->where(fn($q) => $q->where('google_id', $googleUser->getId())->orWhere('email', $googleUser->getEmail()))
            ->first();
        if ($trashedUser) {
            return redirect()->route('login')->with('error',
                'This account has been removed by the administrator. Please contact the admin for more information regarding your account termination.');
        }

        // 1. Try to find user by google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            // 2. Try primary email match
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Link Google ID to existing account
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
                // 3. Try alternative_email match (employees have Gmail stored here after conversion)
                $user = User::where('alternative_email', $googleUser->getEmail())->first();

                if ($user) {
                    // Link Google ID to the employee account found by alternative email
                    $user->update(['google_id' => $googleUser->getId()]);
                } else {
                    // 4. No match — create new external_client account (or find if race condition)
                    $user = DB::transaction(function () use ($googleUser) {
                        // Re-check inside transaction to handle race conditions
                        $existing = User::where('email', $googleUser->getEmail())->first();
                        if ($existing) {
                            $existing->update(['google_id' => $googleUser->getId()]);
                            return $existing;
                        }

                        $nameParts = explode(' ', $googleUser->getName(), 2);
                        $firstName = $nameParts[0];
                        $lastName = $nameParts[1] ?? '';

                        $user = User::create([
                            'name' => $googleUser->getName(),
                            'email' => $googleUser->getEmail(),
                            'google_id' => $googleUser->getId(),
                            'profile_picture' => $googleUser->getAvatar(),
                            'email_verified_at' => now(),
                            'role' => 'external_client',
                            'terms_accepted_at' => now(),
                            'is_active' => true,
                        ]);

                        Client::create([
                            'user_id' => $user->id,
                            'client_type' => 'personal',
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'is_active' => true,
                        ]);

                        return $user;
                    });
                }
            }
        }

        // Block banned users
        if (!$user->is_active) {
            return redirect()->route('login')->with('banned', true);
        }

        if ($googleUser->getAvatar() && $user->profile_picture !== $googleUser->getAvatar()) {
            $user->update(['profile_picture' => $googleUser->getAvatar()]);
        }

        Auth::login($user, true);
        session()->regenerate();
        session()->forget('banned');

        UserActivityLog::log($user->id, UserActivityLog::TYPE_LOGIN, 'Logged in via Google', null, request()->ip());

        return redirect($this->dashboardUrl($user->role));
    }

    /**
     * Handle Google callback for Recruitment (Applicant accounts).
     */
    private function handleRecruitmentCallback($googleUser)
    {
        $recruitmentData = session('recruitment_data');

        if (!$recruitmentData) {
            // If no recruitment data in session, clean up and redirect
            return redirect()->route('recruitment')->with('error', 'Application data was lost. Please try again.');
        }

        // Check if account was deleted by admin (soft-deleted)
        $trashedUser = User::onlyTrashed()
            ->where(fn($q) => $q->where('google_id', $googleUser->getId())->orWhere('email', $googleUser->getEmail()))
            ->first();
        if ($trashedUser) {
            session()->forget(['google_auth_purpose', 'recruitment_data']);
            return redirect()->route('recruitment')->with('error',
                'This account has been removed by the administrator. Please contact the admin for more information regarding your account termination.');
        }

        // Find or create user with applicant role
        $user = User::where('google_id', $googleUser->getId())->first();

        // Block if google_id exists but belongs to a non-applicant role (wrong flow)
        if ($user && $user->role !== 'applicant') {
            session()->forget(['google_auth_purpose', 'recruitment_data']);
            return redirect()->route('recruitment')->with('error',
                'This Google account is already registered in the system as a ' . str_replace('_', ' ', $user->role) . '. Please use a different Google account for job applications.');
        }

        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Block if this email is registered with a non-applicant role
                if ($user->role !== 'applicant') {
                    session()->forget(['google_auth_purpose', 'recruitment_data']);
                    return redirect()->route('recruitment')->with('error',
                        'This Google account is already registered in the system as a ' . str_replace('_', ' ', $user->role) . '. Please use a different Google account for job applications.');
                }
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
                // Re-check to handle race conditions
                $user = User::where('email', $googleUser->getEmail())->first();
                if ($user) {
                    $user->update(['google_id' => $googleUser->getId()]);
                } else {
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'profile_picture' => $googleUser->getAvatar(),
                        'email_verified_at' => now(),
                        'role' => 'applicant',
                        'terms_accepted_at' => now(),
                        'is_active' => true,
                    ]);
                }
            }
        }

        // Block banned users
        if (!$user->is_active) {
            session()->forget(['google_auth_purpose', 'recruitment_data']);
            return redirect()->route('login')->with('banned', true);
        }

        if ($googleUser->getAvatar() && $user->profile_picture !== $googleUser->getAvatar()) {
            $user->update(['profile_picture' => $googleUser->getAvatar()]);
        }

        // Log in the applicant
        Auth::login($user, true);
        session()->regenerate();

        // Keep recruitment data in session so the dashboard can open the apply modal
        session()->forget(['google_auth_purpose', 'banned']);
        // recruitment_data is kept intentionally — the dashboard will consume it

        return redirect()->route('applicant.dashboard')->with('open_apply_modal', true);
    }

    /**
     * Initiate Google OAuth for account linking (authenticated employees).
     */
    public function linkGoogle()
    {
        if (auth()->user()->google_id) {
            return redirect()->route('employee.dashboard')
                ->with('info', 'Your Google account is already linked.');
        }

        session(['google_auth_purpose' => 'link_account']);
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback for account linking.
     */
    private function handleLinkAccountCallback($googleUser)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first to link your Google account.');
        }

        // Check if this Google ID is already linked to another account
        $existingUser = User::where('google_id', $googleUser->getId())
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingUser) {
            return redirect($this->dashboardUrl($user->role))
                ->with('error', 'This Google account is already linked to another user. Please use a different Google account.');
        }

        // 2FA: Generate OTP and send to the Google account email to verify it is active
        // and owned by the user before completing the link.
        $otp = random_int(100000, 999999);
        Cache::put("google_link_otp:{$user->id}", [
            'otp' => (string) $otp,
            'google_id' => $googleUser->getId(),
            'google_email' => $googleUser->getEmail(),
            'google_avatar' => $googleUser->getAvatar(),
        ], now()->addMinutes(5));

        try {
            Mail::to($googleUser->getEmail())
                ->send(new \App\Mail\EmailVerificationOtp($otp));
        } catch (\Exception $e) {
            Log::error('Failed to send Google link OTP: ' . $e->getMessage());
            Cache::forget("google_link_otp:{$user->id}");
            return redirect($this->dashboardUrl($user->role))
                ->with('error', 'Failed to send verification code to your Google account. Please try again.');
        }

        return redirect()->route('google.link.otp.show');
    }

    /**
     * Show the OTP verification form for Google account linking (2FA step).
     */
    public function showLinkOtp()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $pending = Cache::get("google_link_otp:{$user->id}");
        if (!$pending) {
            return redirect($this->dashboardUrl($user->role))
                ->with('error', 'Your verification session has expired. Please try linking your Google account again.');
        }

        return view('auth.link-google-otp', [
            'googleEmail' => $pending['google_email'],
        ]);
    }

    /**
     * Verify the OTP and finalize Google account linking.
     */
    public function verifyLinkOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $pending = Cache::get("google_link_otp:{$user->id}");
        if (!$pending) {
            return redirect($this->dashboardUrl($user->role))
                ->with('error', 'Your verification session has expired. Please try linking your Google account again.');
        }

        if ((string) $request->otp !== (string) $pending['otp']) {
            return back()->with('error', 'The verification code is incorrect. Please try again.');
        }

        // Re-check that the Google ID has not been claimed by another account in the meantime
        $existingUser = User::where('google_id', $pending['google_id'])
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingUser) {
            Cache::forget("google_link_otp:{$user->id}");
            return redirect($this->dashboardUrl($user->role))
                ->with('error', 'This Google account is already linked to another user. Please use a different Google account.');
        }

        // Finalize the link
        $user->update([
            'google_id' => $pending['google_id'],
            'alternative_email' => $user->alternative_email ?: $pending['google_email'],
        ]);

        if (!empty($pending['google_avatar']) && !$user->profile_picture) {
            $user->update(['profile_picture' => $pending['google_avatar']]);
        }

        Cache::forget("google_link_otp:{$user->id}");

        UserActivityLog::log($user->id, UserActivityLog::TYPE_LOGIN, 'Linked Google account (OTP verified)', null, $request->ip());

        return redirect($this->dashboardUrl($user->role))
            ->with('success', 'Your Google account has been linked successfully. You can now sign in with Google.');
    }

    /**
     * Resend the OTP to the pending Google account email.
     */
    public function resendLinkOtp()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $pending = Cache::get("google_link_otp:{$user->id}");
        if (!$pending) {
            return redirect($this->dashboardUrl($user->role))
                ->with('error', 'Your verification session has expired. Please try linking your Google account again.');
        }

        $otp = random_int(100000, 999999);
        $pending['otp'] = (string) $otp;
        Cache::put("google_link_otp:{$user->id}", $pending, now()->addMinutes(5));

        try {
            Mail::to($pending['google_email'])
                ->send(new \App\Mail\EmailVerificationOtp($otp));
        } catch (\Exception $e) {
            Log::error('Failed to resend Google link OTP: ' . $e->getMessage());
            return back()->with('error', 'Failed to resend verification code. Please try again.');
        }

        return back()->with('success', 'A new verification code has been sent to your Google account.');
    }

    /**
     * Store quotation form data in session and redirect to Google OAuth.
     */
    public function quotationAuth(Request $request)
    {
        session([
            'google_auth_purpose' => 'quotation',
            'quotation_data' => $request->only([
                'bookingType', 'serviceType', 'serviceDate', 'urgency',
                'propertyType', 'floors', 'rooms', 'floorArea',
                'region', 'city', 'postalCode', 'district',
                'specialRequests', 'companyName',
            ]),
        ]);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Fetch phone number from Google People API using the access token.
     */
    private function fetchGooglePhoneNumber($accessToken): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->get('https://people.googleapis.com/v1/people/me', [
                    'personFields' => 'phoneNumbers',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $phones = $data['phoneNumbers'] ?? [];
                if (!empty($phones)) {
                    return $phones[0]['value'] ?? null;
                }
            }
        } catch (\Exception $e) {
            // Silent fail — phone is optional
        }

        return null;
    }

    /**
     * Handle Google callback for Quotation submission.
     */
    private function handleQuotationCallback($googleUser)
    {
        $quotationData = session('quotation_data');
        session()->forget(['google_auth_purpose', 'quotation_data']);

        if (!$quotationData) {
            return redirect()->route('quotation')
                ->with('error', 'Quotation data was lost. Please try again.');
        }

        try {
            // Fetch phone number from Google People API
            $phoneNumber = $this->fetchGooglePhoneNumber($googleUser->token);

            // Fallback: try existing user record in the system
            if (!$phoneNumber) {
                $existingUser = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

                if ($existingUser) {
                    $client = Client::where('user_id', $existingUser->id)->first();
                    if ($client && $client->phone_number) {
                        $phoneNumber = $client->phone_number;
                    }
                }
            }

            // Map service type to its key for PDF lookup
            $serviceType = $quotationData['serviceType'] ?? '';
            $serviceKeyMap = [
                'Deep Cleaning' => 'deep_cleaning',
                'Final Cleaning' => 'final_cleaning',
                'Daily Cleaning' => 'daily_cleaning',
                'Snowout Cleaning' => 'snowout_cleaning',
                'General Cleaning' => 'general_cleaning',
                'Hotel Cleaning' => 'hotel_cleaning',
            ];

            $quotation = Quotation::create([
                'booking_type'       => $quotationData['bookingType'] ?? 'personal',
                'cleaning_services'  => $serviceType ? [$serviceType] : null,
                'date_of_service'    => !empty($quotationData['serviceDate']) ? $quotationData['serviceDate'] : null,
                'type_of_urgency'    => $quotationData['urgency'] ?? null,

                'property_type'      => $quotationData['propertyType'] ?? null,
                'floors'             => $quotationData['floors'] ?? 1,
                'rooms'              => $quotationData['rooms'] ?? 1,
                'floor_area'         => !empty($quotationData['floorArea']) ? $quotationData['floorArea'] : null,
                'area_unit'          => 'sqm',

                'postal_code'        => $quotationData['postalCode'] ?? null,
                'city'               => $quotationData['city'] ?? null,
                'district'           => $quotationData['district'] ?? null,

                'company_name'       => !empty($quotationData['companyName']) ? $quotationData['companyName'] : null,
                'client_name'        => $googleUser->getName(),
                'phone_number'       => $phoneNumber ?: '—',
                'email'              => $googleUser->getEmail(),

                'status'             => 'pending_review',
            ]);

            // Send confirmation email with PDF attachment if auto-send is enabled
            $autoSendEnabled = QuotationSetting::isAutoSendEnabled();
            \Log::info('Quotation email check', [
                'auto_send_enabled' => $autoSendEnabled,
                'service_type' => $serviceType,
                'email' => $googleUser->getEmail(),
            ]);

            if ($autoSendEnabled) {
                $serviceKey = $serviceKeyMap[$serviceType] ?? null;
                $pdfPath = $serviceKey ? QuotationSetting::getPdfPath($serviceKey) : null;

                \Log::info('Sending quotation email', [
                    'service_key' => $serviceKey,
                    'pdf_path' => $pdfPath,
                    'to' => $googleUser->getEmail(),
                ]);

                try {
                    Mail::to($googleUser->getEmail())
                        ->send(new QuotationConfirmation($quotation, $pdfPath));
                    \Log::info('Quotation email sent successfully to ' . $googleUser->getEmail());
                } catch (\Exception $mailError) {
                    \Log::error('Failed to send quotation confirmation email: ' . $mailError->getMessage());
                }
            }

            return redirect()->route('quotation')
                ->with('success', 'Your quotation request has been submitted successfully! We will contact you at ' . $googleUser->getEmail() . '.');
        } catch (\Exception $e) {
            return redirect()->route('quotation')
                ->with('error', 'Failed to submit quotation. Please try again. ' . $e->getMessage());
        }
    }

    /**
     * Redirect to Google OAuth for mobile app login.
     */
    public function mobileRedirect(Request $request)
    {
        session([
            'google_auth_purpose' => 'mobile_login',
            'mobile_callback' => $request->query('callback', 'opticrew://auth'),
        ]);

        // Build the Google callback URL based on the current host
        $host = $request->getHost();
        if (str_contains($host, 'finnoys.com')) {
            $callbackUrl = 'https://finnoys.com/auth/google/callback';
        } elseif (str_contains($host, 'ngrok')) {
            $callbackUrl = 'https://' . $host . '/opticrew/public/auth/google/callback';
        } else {
            $callbackUrl = 'http://127.0.0.1:8000/auth/google/callback';
        }

        return Socialite::driver('google')
            ->redirectUrl($callbackUrl)
            ->redirect();
    }

    /**
     * Handle Google callback for mobile login — creates Sanctum token and redirects to app.
     */
    private function handleMobileLoginCallback($googleUser)
    {
        $callback = session('mobile_callback', 'opticrew://auth');
        session()->forget(['google_auth_purpose', 'mobile_callback']);

        // Check if account was deleted by admin (soft-deleted)
        $trashedUser = User::onlyTrashed()
            ->where(fn($q) => $q->where('google_id', $googleUser->getId())->orWhere('email', $googleUser->getEmail()))
            ->first();
        if ($trashedUser) {
            return redirect($callback . '?error=' . urlencode('This account has been removed by the administrator. Please contact the admin for more information regarding your account termination.'));
        }

        // Find user: by google_id, then email, then alternative_email
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
                $user = User::where('alternative_email', $googleUser->getEmail())->first();

                if ($user) {
                    $user->update(['google_id' => $googleUser->getId()]);
                } else {
                    $user = DB::transaction(function () use ($googleUser) {
                        // Re-check inside transaction to handle race conditions
                        $existing = User::where('email', $googleUser->getEmail())->first();
                        if ($existing) {
                            $existing->update(['google_id' => $googleUser->getId()]);
                            return $existing;
                        }

                        $nameParts = explode(' ', $googleUser->getName(), 2);

                        $user = User::create([
                            'name' => $googleUser->getName(),
                            'email' => $googleUser->getEmail(),
                            'google_id' => $googleUser->getId(),
                            'profile_picture' => $googleUser->getAvatar(),
                            'email_verified_at' => now(),
                            'role' => 'external_client',
                            'terms_accepted_at' => now(),
                            'is_active' => true,
                        ]);

                        Client::create([
                            'user_id' => $user->id,
                            'client_type' => 'personal',
                            'first_name' => $nameParts[0],
                            'last_name' => $nameParts[1] ?? '',
                            'is_active' => true,
                        ]);

                        return $user;
                    });
                }
            }
        }

        // Block applicants and external clients — mobile is only for admin, employee, company
        if (in_array($user->role, ['applicant', 'external_client'])) {
            return redirect($callback . '?error=' . urlencode('This account type can only access the website. Please log in at finnoys.com.'));
        }

        if (!$user->is_active) {
            return redirect($callback . '?error=' . urlencode('Account is deactivated'));
        }

        if ($googleUser->getAvatar() && $user->profile_picture !== $googleUser->getAvatar()) {
            $user->update(['profile_picture' => $googleUser->getAvatar()]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;
        $user->load('employee');

        UserActivityLog::log($user->id, UserActivityLog::TYPE_LOGIN, 'Logged in via Google (mobile)', null, request()->ip());

        $userData = json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'phone' => $user->phone,
            'profile_picture' => $user->profile_picture,
            'employee_id' => $user->employee?->id,
            'google_linked' => !empty($user->google_id),
        ]);

        return redirect($callback . '?token=' . urlencode($token) . '&user=' . urlencode($userData));
    }

    /**
     * Redirect to Google OAuth for mobile app account linking.
     * Requires user_id and token so we know which account to link.
     */
    public function mobileLinkRedirect(Request $request)
    {
        $userId = $request->query('user_id');
        $token = $request->query('token');
        $callback = $request->query('callback', 'opticrew://google-link');

        if (!$userId || !$token) {
            return redirect($callback . '?error=' . urlencode('Missing authentication parameters.'));
        }

        // Verify the Sanctum token belongs to this user
        $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        if (!$personalAccessToken || $personalAccessToken->tokenable_id != $userId) {
            return redirect($callback . '?error=' . urlencode('Invalid authentication. Please log in again.'));
        }

        session([
            'google_auth_purpose' => 'mobile_link',
            'mobile_callback' => $callback,
            'mobile_link_user_id' => (int) $userId,
        ]);

        $host = $request->getHost();
        if (str_contains($host, 'finnoys.com')) {
            $callbackUrl = 'https://finnoys.com/auth/google/callback';
        } elseif (str_contains($host, 'ngrok')) {
            $callbackUrl = 'https://' . $host . '/opticrew/public/auth/google/callback';
        } else {
            $callbackUrl = 'http://127.0.0.1:8000/auth/google/callback';
        }

        return Socialite::driver('google')
            ->redirectUrl($callbackUrl)
            ->redirect();
    }

    /**
     * Handle Google callback for mobile account linking.
     */
    private function handleMobileLinkCallback($googleUser)
    {
        $callback = session('mobile_callback', 'opticrew://google-link');
        $userId = session('mobile_link_user_id');
        session()->forget(['google_auth_purpose', 'mobile_callback', 'mobile_link_user_id']);

        if (!$userId) {
            return redirect($callback . '?error=' . urlencode('Session expired. Please try again.'));
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect($callback . '?error=' . urlencode('User not found.'));
        }

        // Check if already linked
        if ($user->google_id) {
            return redirect($callback . '?success=' . urlencode('Your Google account is already linked.'));
        }

        // Check if this Google ID is already linked to another account
        $existingUser = User::where('google_id', $googleUser->getId())
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingUser) {
            return redirect($callback . '?error=' . urlencode('This Google account is already linked to another user. Please use a different Google account.'));
        }

        // 2FA: Generate OTP and send to the Google account email to verify it is active
        // and owned by the user before completing the link.
        $otp = random_int(100000, 999999);
        Cache::put("google_link_otp:{$user->id}", [
            'otp' => (string) $otp,
            'google_id' => $googleUser->getId(),
            'google_email' => $googleUser->getEmail(),
            'google_avatar' => $googleUser->getAvatar(),
        ], now()->addMinutes(5));

        try {
            Mail::to($googleUser->getEmail())
                ->send(new \App\Mail\EmailVerificationOtp($otp));
        } catch (\Exception $e) {
            Log::error('Failed to send Google link OTP (mobile): ' . $e->getMessage());
            Cache::forget("google_link_otp:{$user->id}");
            return redirect($callback . '?error=' . urlencode('Failed to send verification code to your Google account. Please try again.'));
        }

        return redirect($callback . '?otp_required=true&user_id=' . $user->id . '&google_email=' . urlencode($googleUser->getEmail()));
    }

    /**
     * Mobile API: Verify OTP and finalize Google account linking.
     */
    public function mobileVerifyLinkOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'otp' => 'required|digits:6',
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $pending = Cache::get("google_link_otp:{$user->id}");
        if (!$pending) {
            return response()->json(['success' => false, 'message' => 'Verification session expired. Please try linking again.'], 410);
        }

        if ((string) $request->otp !== (string) $pending['otp']) {
            return response()->json(['success' => false, 'message' => 'The verification code is incorrect.'], 422);
        }

        $existingUser = User::where('google_id', $pending['google_id'])
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingUser) {
            Cache::forget("google_link_otp:{$user->id}");
            return response()->json(['success' => false, 'message' => 'This Google account is already linked to another user.'], 409);
        }

        $user->update([
            'google_id' => $pending['google_id'],
            'alternative_email' => $user->alternative_email ?: $pending['google_email'],
        ]);

        if (!empty($pending['google_avatar']) && !$user->profile_picture) {
            $user->update(['profile_picture' => $pending['google_avatar']]);
        }

        Cache::forget("google_link_otp:{$user->id}");

        UserActivityLog::log($user->id, UserActivityLog::TYPE_LOGIN, 'Linked Google account (mobile, OTP verified)', null, $request->ip());

        return response()->json(['success' => true, 'message' => 'Your Google account has been linked successfully.']);
    }

    /**
     * Mobile API: Resend OTP for Google account linking.
     */
    public function mobileResendLinkOtp(Request $request)
    {
        $request->validate(['user_id' => 'required|integer']);

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $pending = Cache::get("google_link_otp:{$user->id}");
        if (!$pending) {
            return response()->json(['success' => false, 'message' => 'Verification session expired. Please try linking again.'], 410);
        }

        $otp = random_int(100000, 999999);
        $pending['otp'] = (string) $otp;
        Cache::put("google_link_otp:{$user->id}", $pending, now()->addMinutes(5));

        try {
            Mail::to($pending['google_email'])
                ->send(new \App\Mail\EmailVerificationOtp($otp));
        } catch (\Exception $e) {
            Log::error('Failed to resend Google link OTP (mobile): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to resend verification code.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'A new verification code has been sent.']);
    }

    /**
     * Mobile: Initiate Google OAuth for password reset identity verification (3FA Step 2).
     * Stores the email and callback, then redirects to Google.
     */
    public function mobileVerifyRedirect(Request $request)
    {
        $email = $request->query('email');
        $callback = $request->query('callback', 'opticrew://forgot-password-verify');

        session([
            'google_auth_purpose' => 'mobile_verify',
            'mobile_callback' => $callback,
            'mobile_verify_email' => $email,
        ]);

        $host = $request->getHost();
        if (str_contains($host, 'finnoys.com')) {
            $callbackUrl = 'https://finnoys.com/auth/google/callback';
        } elseif (str_contains($host, 'ngrok')) {
            $callbackUrl = 'https://' . $host . '/opticrew/public/auth/google/callback';
        } else {
            $callbackUrl = 'http://127.0.0.1:8000/auth/google/callback';
        }

        return Socialite::driver('google')
            ->redirectUrl($callbackUrl)
            ->redirect();
    }

    /**
     * Handle Google callback for mobile password reset verification.
     * Verifies that the Google account matches the reset email, then sends OTP.
     */
    private function handleMobileVerifyCallback($googleUser)
    {
        $callback = session('mobile_callback', 'opticrew://forgot-password-verify');
        $resetEmail = session('mobile_verify_email');
        session()->forget(['google_auth_purpose', 'mobile_callback', 'mobile_verify_email']);

        if (!$resetEmail) {
            return redirect($callback . '?error=' . urlencode('Your verification link has expired. Please go back and start the process again.'));
        }

        // Find user by the reset email
        $user = User::where('email', $resetEmail)
            ->orWhere('alternative_email', $resetEmail)
            ->first();

        if (!$user) {
            return redirect($callback . '?error=' . urlencode('We could not find an account with that email address. Please check and try again.'));
        }

        // Verify the Google account matches (by google_id, email, or alternative_email)
        $googleEmail = $googleUser->getEmail();
        $googleId = $googleUser->getId();

        $matches = false;
        if ($user->google_id && $user->google_id === $googleId) {
            $matches = true;
        } elseif ($user->email === $googleEmail || $user->alternative_email === $googleEmail) {
            $matches = true;
        }

        if (!$matches) {
            return redirect($callback . '?error=' . urlencode('The Google account you used does not match the one linked to your account. Please sign in with the correct Google account.'));
        }

        // Google verification passed — generate and send OTP (3FA Step 3)
        // Always send OTP to the employee's verified Google account email.
        // @finnoys.com addresses have no inbox — never send there.
        $otp = random_int(100000, 999999);
        Cache::put("pwd_reset_otp:{$user->id}", $otp, now()->addMinutes(5));

        \Illuminate\Support\Facades\Mail::to($user->alternative_email)
            ->send(new \App\Mail\EmailVerificationOtp($otp));

        return redirect($callback . '?verified=true');
    }

    /**
     * Web (Livewire) forgot-password 3FA Step 2: redirect to Google OAuth.
     * Mirrors mobileVerifyRedirect() but stores web-specific session keys and redirects
     * back to the website (not a deep link).
     */
    public function webForgotPasswordVerifyRedirect(Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('forgot.password.new')
                ->with('web_forgot_password_error', 'Missing email. Please start over from the beginning.');
        }

        session([
            'google_auth_purpose' => 'web_forgot_password_verify',
            'web_forgot_password_email' => $email,
        ]);

        $host = $request->getHost();
        if (str_contains($host, 'finnoys.com')) {
            $callbackUrl = 'https://finnoys.com/auth/google/callback';
        } elseif (str_contains($host, 'ngrok')) {
            $callbackUrl = 'https://' . $host . '/opticrew/public/auth/google/callback';
        } else {
            $callbackUrl = 'http://127.0.0.1:8000/auth/google/callback';
        }

        return Socialite::driver('google')
            ->redirectUrl($callbackUrl)
            ->redirect();
    }

    /**
     * Handle Google callback for the web (Livewire) forgot-password verification.
     * Verifies the Google account matches the reset email, sends an OTP, and redirects
     * the user back to the Livewire page with a session flash so the wizard can advance
     * to the OTP step automatically.
     */
    private function handleWebForgotPasswordVerifyCallback($googleUser)
    {
        $resetEmail = session('web_forgot_password_email');
        session()->forget(['google_auth_purpose', 'web_forgot_password_email']);

        if (!$resetEmail) {
            return redirect()->route('forgot.password.new')
                ->with('web_forgot_password_error', 'Your verification link has expired. Please go back and start the process again.');
        }

        $user = User::where('email', $resetEmail)
            ->orWhere('alternative_email', $resetEmail)
            ->first();

        if (!$user) {
            return redirect()->route('forgot.password.new')
                ->with('web_forgot_password_error', 'We could not find an account with that email address. Please check and try again.')
                ->with('web_forgot_password_email', $resetEmail);
        }

        // Verify the Google account matches (by google_id, email, or alternative_email)
        $googleEmail = $googleUser->getEmail();
        $googleId = $googleUser->getId();

        $matches = false;
        if ($user->google_id && $user->google_id === $googleId) {
            $matches = true;
        } elseif ($user->email === $googleEmail || $user->alternative_email === $googleEmail) {
            $matches = true;
        }

        if (!$matches) {
            return redirect()->route('forgot.password.new')
                ->with('web_forgot_password_error', 'The Google account you used does not match the one linked to your account. Please sign in with the correct Google account.')
                ->with('web_forgot_password_email', $resetEmail);
        }

        // Google verification passed — generate and send OTP (3FA Step 3).
        // Always send OTP to the employee's verified Google account email; @finnoys.com
        // addresses have no inbox so we never send there.
        $otp = random_int(100000, 999999);
        Cache::put("web_pwd_reset_otp:{$user->id}", $otp, now()->addMinutes(5));

        Mail::to($user->alternative_email)
            ->send(new \App\Mail\EmailVerificationOtp($otp));

        return redirect()->route('forgot.password.new')
            ->with('web_forgot_password_verified_email', $resetEmail);
    }

    private function dashboardUrl(string $role): string
    {
        return match ($role) {
            'admin' => '/admin/dashboard',
            'employee' => '/employee/dashboard',
            'external_client' => '/client/dashboard',
            'company' => '/manager/dashboard',
            'applicant' => '/applicant/dashboard',
            default => '/',
        };
    }
}
