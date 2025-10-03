<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    // public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // ADD THIS NEW LOGIC FOR REDIRECTION
        $this->app['router']->matched(function ($route) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role === 'admin' && !request()->is('admin/*')) {
                    config(['fortify.home' => route('admin.dashboard')]);
                } elseif ($user->role === 'employee' && !request()->is('employee/*')) {
                    config(['fortify.home' => route('employee.dashboard')]);
                }
                // Add client dashboard logic here later
                // elseif ($user->role === 'external_client' && !request()->is('client/*')) {
                //     config(['fortify.home' => route('client.dashboard')]);
                // }
            }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
