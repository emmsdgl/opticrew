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
use Illuminate\Support\Facades\DB;
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
            return redirect()->route('login')->with('error', 'Google authentication failed. Please try again.');
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

        return $this->handleLoginCallback($googleUser);
    }

    /**
     * Handle Google callback for Login (all roles).
     */
    private function handleLoginCallback($googleUser)
    {
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
                    // 4. No match — create new external_client account
                    $user = DB::transaction(function () use ($googleUser) {
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

        // Link the Google account
        $user->update([
            'google_id' => $googleUser->getId(),
            'alternative_email' => $user->alternative_email ?: $googleUser->getEmail(),
        ]);

        if ($googleUser->getAvatar() && !$user->profile_picture) {
            $user->update(['profile_picture' => $googleUser->getAvatar()]);
        }

        UserActivityLog::log($user->id, UserActivityLog::TYPE_LOGIN, 'Linked Google account', null, request()->ip());

        return redirect($this->dashboardUrl($user->role))
            ->with('success', 'Your Google account has been linked successfully. You can now sign in with Google.');
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
        ]);

        return redirect($callback . '?token=' . urlencode($token) . '&user=' . urlencode($userData));
    }

    /**
     * Get dashboard URL based on user role.
     */
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
