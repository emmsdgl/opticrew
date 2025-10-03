<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    
    // It's good practice to add the fillable property here as well
    protected $fillable = [
        'contracted_client_id',
        'location_name',
        'location_type',
        'base_cleaning_duration_minutes',
    ];

    /**
     * Get the contracted client that owns this location.
     */
    public function contractedClient()
    {
        return $this->belongsTo(ContractedClient::class);
    }
}