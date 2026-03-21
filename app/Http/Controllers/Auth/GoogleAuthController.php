<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\UserActivityLog;
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
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
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
        session()->forget(['google_auth_purpose']);
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

        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/user.phonenumbers.read'])
            ->redirect();
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

            $quotation = Quotation::create([
                'booking_type'       => $quotationData['bookingType'] ?? 'personal',
                'cleaning_services'  => $quotationData['serviceType'] ? [$quotationData['serviceType']] : null,
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

            return redirect()->route('quotation')
                ->with('success', 'Your quotation request has been submitted successfully! We will contact you at ' . $googleUser->getEmail() . '.');
        } catch (\Exception $e) {
            return redirect()->route('quotation')
                ->with('error', 'Failed to submit quotation. Please try again. ' . $e->getMessage());
        }
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
