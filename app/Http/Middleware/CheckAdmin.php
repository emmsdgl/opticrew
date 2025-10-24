<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * Only allow users with 'admin' role to proceed.
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

        // Check if user has admin role
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Redirect non-admin users to their appropriate dashboards
        if ($user->role === 'employee') {
            return redirect()->route('employee.dashboard')
                ->with('error', 'Unauthorized: Admin access required.');
        }

        if ($user->role === 'external_client') {
            return redirect()->route('client.dashboard')
                ->with('error', 'Unauthorized: Admin access required.');
        }

        // Fallback for unknown roles
        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'Invalid user role.');
    }
}
