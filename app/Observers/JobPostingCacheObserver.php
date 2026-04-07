<?php

namespace App\Observers;

use App\Models\JobPosting;
use Illuminate\Support\Facades\Cache;

class JobPostingCacheObserver
{
    public function created(JobPosting $jobPosting): void
    {
        $this->flush();
    }

    public function updated(JobPosting $jobPosting): void
    {
        $this->flush();
    }

    public function deleted(JobPosting $jobPosting): void
    {
        $this->flush();
    }

    protected function flush(): void
    {
        Cache::forget('job_postings:active');
    }
}
