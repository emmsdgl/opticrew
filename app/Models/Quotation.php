<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Step 1: Service Information
        'booking_type',
        'cleaning_services',
        'date_of_service',
        'duration_of_service',
        'type_of_urgency',

        // Step 2: Property Information
        'property_type',
        'floors',
        'rooms',
        'people_per_room',
        'floor_area',
        'area_unit',

        // Property Location
        'location_type',
        'street_address',
        'postal_code',
        'city',
        'district',
        'latitude',
        'longitude',

        // Step 3: Contact Information
        'company_name',
        'client_name',
        'phone_number',
        'email',

        // Pricing
        'estimated_price',
        'vat_amount',
        'total_price',
        'pricing_notes',

        // Status
        'status',

        // Admin Actions
        'reviewed_by',
        'reviewed_at',
        'quoted_by',
        'quoted_at',
        'admin_notes',
        'rejection_reason',

        // Conversion
        'appointment_id',
        'converted_by',
        'converted_at',

        // Client Response
        'client_responded_at',
        'client_message',
    ];

    protected $casts = [
        'cleaning_services' => 'array',
        'date_of_service' => 'date',
        'reviewed_at' => 'datetime',
        'quoted_at' => 'datetime',
        'converted_at' => 'datetime',
        'client_responded_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'floor_area' => 'decimal:2',
        'estimated_price' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function quotedBy()
    {
        return $this->belongsTo(User::class, 'quoted_by');
    }

    public function convertedBy()
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    public function appointment()
    {
        return $this->belongsTo(ClientAppointment::class, 'appointment_id');
    }

    // Scopes for filtering
    public function scopePending($query)
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeQuoted($query)
    {
        return $query->where('status', 'quoted');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopePersonal($query)
    {
        return $query->where('booking_type', 'personal');
    }

    public function scopeCompany($query)
    {
        return $query->where('booking_type', 'company');
    }
}
