<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // This gets the response that Laravel is about to send to the browser.
        $response = $next($request);

        // This adds the necessary headers to the response.
        // These headers tell the browser:
        // - "no-cache": Always check with the server before showing the page.
        // - "no-store": Do not store any copy of this page.
        // - "must-revalidate": You must re-check, you cannot use a stale version.
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        // We return the modified response.
        return $response;
    }
}