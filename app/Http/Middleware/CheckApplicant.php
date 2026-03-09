<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApplicant
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role === 'applicant') {
            return $next($request);
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'employee') {
            return redirect()->route('employee.dashboard');
        }

        if ($user->role === 'external_client') {
            return redirect()->route('client.dashboard');
        }

        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'Invalid user role.');
    }
}
