<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistCategory extends Model
{
    protected $fillable = [
        'checklist_id',
        'name',
        'sort_order'
    ];

    /**
     * Get the checklist that owns this category
     */
    public function checklist()
    {
        return $this->belongsTo(CompanyChecklist::class, 'checklist_id');
    }

    /**
     * Get all items in this category
     */
    public function items()
    {
        return $this->hasMany(ChecklistItem::class, 'category_id')->orderBy('sort_order');
    }
}
