<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['location_name', 'contracted_client_id', 'address', 'description'];

    /**
     * Get the contracted client that owns this location
     */
    public function contractedClient()
    {
        return $this->belongsTo(ContractedClient::class, 'contracted_client_id');
    }

    /**
     * Get all tasks for this location
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'location_id');
    }
}