<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTermsAccepted
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && is_null(Auth::user()->terms_accepted_at)) {
            return redirect()->route('terms.accept');
        }

        return $next($request);
    }
}
