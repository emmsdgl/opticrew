<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'quantity',
        'sort_order'
    ];

    /**
     * Get the category that owns this item
     */
    public function category()
    {
        return $this->belongsTo(ChecklistCategory::class, 'category_id');
    }
}
