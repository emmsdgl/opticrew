<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyChecklist extends Model
{
    protected $fillable = [
        'contracted_client_id',
        'name',
        'important_reminders',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the contracted client that owns this checklist
     */
    public function contractedClient()
    {
        return $this->belongsTo(ContractedClient::class);
    }

    /**
     * Get all categories for this checklist
     */
    public function categories()
    {
        return $this->hasMany(ChecklistCategory::class, 'checklist_id')->orderBy('sort_order');
    }
}
