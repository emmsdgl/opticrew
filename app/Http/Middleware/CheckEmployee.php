<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckEmployee
{
    /**
     * Handle an incoming request.
     *
     * Only allow users with 'employee' role to proceed.
     * Redirect others to their appropriate dashboards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has employee role
        if ($user->role === 'employee') {
            return $next($request);
        }

        // Redirect non-employee users to their appropriate dashboards
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Unauthorized: Employee access required.');
        }

        if ($user->role === 'external_client') {
            return redirect()->route('client.dashboard')
                ->with('error', 'Unauthorized: Employee access required.');
        }

        // Fallback for unknown roles
        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'Invalid user role.');
    }
}
