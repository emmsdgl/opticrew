<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\JobApplication;
use App\Models\UserActivityLog;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        return $this->handleLoginCallback($googleUser);
    }

    /**
     * Handle Google callback for Login (Client accounts).
     */
    private function handleLoginCallback($googleUser)
    {
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
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

        if (!$user) {
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
                ]);
            }
        }

        if ($googleUser->getAvatar() && $user->profile_picture !== $googleUser->getAvatar()) {
            $user->update(['profile_picture' => $googleUser->getAvatar()]);
        }

        // Create the job application
        $application = JobApplication::create([
            'job_title' => $recruitmentData['job_title'],
            'job_type' => $recruitmentData['job_type'] ?? null,
            'email' => $googleUser->getEmail(),
            'status' => 'pending',
        ]);

        // Notify all admins
        app(NotificationService::class)->notifyAdminsNewJobApplication($application);

        // Log in the applicant
        Auth::login($user, true);
        session()->regenerate();

        // Clear session data
        session()->forget(['google_auth_purpose', 'recruitment_data']);

        return redirect()->route('applicant.dashboard')->with('success', 'Your application has been submitted successfully!');
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
