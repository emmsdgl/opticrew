<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from session, default to 'en'
        $locale = Session::get('locale', config('app.locale', 'en'));

        // Validate locale
        if (!in_array($locale, ['en', 'fi'])) {
            $locale = 'en';
        }

        // Set application locale
        App::setLocale($locale);

        return $next($request);
    }
}
