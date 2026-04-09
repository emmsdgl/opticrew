<?php

namespace App\Providers;

use App\Models\JobPosting;
use App\Models\Task;
use App\Models\User;
use App\Observers\JobPostingCacheObserver;
use App\Observers\TaskApprovalObserver;
use App\Observers\TaskCacheObserver;
use App\Observers\TaskCompletionObserver;
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
        Task::observe(TaskApprovalObserver::class); // ✅ Stage 2.5: strict timeline recompute
        Task::observe(TaskCompletionObserver::class); // ✅ Stage 3: employee efficiency recalc on completion
        JobPosting::observe(JobPostingCacheObserver::class);
        User::observe(UserCacheObserver::class);
    }
}
