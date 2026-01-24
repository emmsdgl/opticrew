<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskChecklistCompletion extends Model
{
    protected $fillable = [
        'task_id',
        'checklist_item_id',
        'is_completed',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    public function completedByUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
