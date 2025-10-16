<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractedClient extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'contract_start', 'contract_end'];

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