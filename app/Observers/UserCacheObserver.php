<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserCacheObserver
{
    public function updated(User $user): void
    {
        Cache::forget("user_profile:{$user->id}");
    }

    public function deleted(User $user): void
    {
        Cache::forget("user_profile:{$user->id}");
    }
}
