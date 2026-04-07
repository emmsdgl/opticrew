<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskCacheObserver
{
    public function created(Task $task): void
    {
        $this->flush($task);
    }

    public function updated(Task $task): void
    {
        $this->flush($task);

        // If scheduled_date changed, also flush the previous date's cache
        if ($task->wasChanged('scheduled_date')) {
            $original = $task->getOriginal('scheduled_date');
            if ($original) {
                $date = \Carbon\Carbon::parse($original)->format('Y-m-d');
                $this->flushDate($date);
            }
        }
    }

    public function deleted(Task $task): void
    {
        $this->flush($task);
    }

    protected function flush(Task $task): void
    {
        if (!$task->scheduled_date) {
            return;
        }

        $date = \Carbon\Carbon::parse($task->scheduled_date)->format('Y-m-d');
        $this->flushDate($date);
    }

    protected function flushDate(string $date): void
    {
        Cache::forget("schedule:{$date}:all");
        Cache::forget("stats:{$date}");
    }
}
