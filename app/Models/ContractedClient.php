<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractedClient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contract_start',
        'contract_end',
        'user_id',
        'latitude',
        'longitude',
        'business_id'
    ];

    /**
     * Get the user account for this company
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all locations for this contracted client
     */
    public function locations()
    {
        return $this->hasMany(Location::class, 'contracted_client_id');
    }

    /**
     * Get all tasks for this contracted client
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'contracted_client_id');
    }
}