<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\UserActivityLog;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $request->session()->forget('banned');

        UserActivityLog::log(
            Auth::id(),
            UserActivityLog::TYPE_LOGIN,
            'Logged in to account',
            null,
            $request->ip()
        );

        return redirect()->intended($this->dashboardUrl());
    }

    /**
     * Show the terms acceptance page.
     */
    public function showTerms(): View|RedirectResponse
    {
        if (Auth::user()->terms_accepted_at) {
            return redirect()->intended($this->dashboardUrl());
        }

        return view('auth.accept-terms');
    }

    /**
     * Handle terms acceptance.
     */
    public function acceptTerms(Request $request): RedirectResponse
    {
        $request->validate([
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'You must accept the Terms & Conditions and Privacy Policy to continue.',
        ]);

        Auth::user()->update([
            'terms_accepted_at' => Carbon::now(),
        ]);

        return redirect()->intended($this->dashboardUrl());
    }

    /**
     * Get dashboard URL based on user role.
     */
    private function dashboardUrl(): string
    {
        $role = Auth::user()->role;

        if ($role === 'admin') {
            return '/admin/dashboard';
        } elseif ($role === 'employee') {
            return '/employee/dashboard';
        } elseif ($role === 'external_client') {
            return '/client/dashboard';
        } elseif ($role === 'applicant') {
            return '/applicant/dashboard';
        }

        return '/';
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        if ($userId) {
            UserActivityLog::log(
                $userId,
                UserActivityLog::TYPE_LOGOUT,
                'Logged out of account',
                null,
                $request->ip()
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect to login page after logout
        return redirect()->route('login');
    }
}
