<?php

namespace App\Providers;

use App\Models\JobPosting;
use App\Models\Task;
use App\Models\User;
use App\Observers\JobPostingCacheObserver;
use App\Observers\TaskCacheObserver;
use App\Observers\UserCacheObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Task::observe(TaskCacheObserver::class);
        JobPosting::observe(JobPostingCacheObserver::class);
        User::observe(UserCacheObserver::class);
    }
}
