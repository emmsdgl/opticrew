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

        UserActivityLog::log(
            Auth::id(),
            UserActivityLog::TYPE_LOGIN,
            'Logged in to account',
            null,
            $request->ip()
        );

        $url = '';
        $role = Auth::user()->role;
            
        if ($role === 'admin') {
            $url = '/admin/dashboard';
        } elseif ($role === 'employee') {
            $url = '/employee/dashboard';
        } elseif ($role === 'external_client') {
            $url = '/client/dashboard';
        }

        return redirect()->intended($url);
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
