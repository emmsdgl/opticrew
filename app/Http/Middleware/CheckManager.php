<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckManager
{
    /**
     * Handle an incoming request.
     *
     * Only allow users with 'contracted_client' role to proceed.
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

        // Check if user has company role (manager/contracted client)
        if ($user->role === 'company') {
            return $next($request);
        }

        // Redirect non-manager users to their appropriate dashboards
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Unauthorized: Manager access required.');
        }

        if ($user->role === 'employee') {
            return redirect()->route('employee.dashboard')
                ->with('error', 'Unauthorized: Manager access required.');
        }

        if ($user->role === 'external_client') {
            return redirect()->route('client.dashboard')
                ->with('error', 'Unauthorized: Manager access required.');
        }

        // Fallback for unknown roles
        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'Invalid user role.');
    }
}
