<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    // Display list of quotations in admin panel
    public function index(Request $request)
    {
        $query = Quotation::query()->latest();

        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by booking type if provided
        if ($request->has('booking_type') && $request->booking_type != '') {
            $query->where('booking_type', $request->booking_type);
        }

        // Search by name or email
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $quotations = $query->paginate(15);

        return view('admin.quotations.index', compact('quotations'));
    }

    // Show quotation details
    public function show(Request $request, $id)
    {
        $quotation = Quotation::with(['reviewedBy', 'quotedBy', 'convertedBy', 'appointment'])->findOrFail($id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'quotation' => $this->formatQuotationForDrawer($quotation),
            ]);
        }

        return view('admin.quotations.show', compact('quotation'));
    }

    private function formatQuotationForDrawer($quotation)
    {
        return [
            'id' => $quotation->id,
            'status' => $quotation->status,
            'status_label' => str_replace('_', ' ', ucwords($quotation->status)),
            'created_at' => $quotation->created_at->format('M d, Y \a\t g:i A'),

            'contact' => [
                'company_name' => $quotation->company_name,
                'client_name' => $quotation->client_name,
                'email' => $quotation->email,
                'phone_number' => $quotation->phone_number,
                'booking_type' => $quotation->booking_type,
            ],

            'service' => [
                'cleaning_services' => $quotation->cleaning_services ?? [],
                'date_of_service' => $quotation->date_of_service ? $quotation->date_of_service->format('M d, Y') : null,
                'duration_of_service' => $quotation->duration_of_service,
                'type_of_urgency' => $quotation->type_of_urgency ? str_replace('_', ' ', ucwords($quotation->type_of_urgency)) : null,
            ],

            'property' => [
                'property_type' => $quotation->property_type,
                'floors' => $quotation->floors,
                'rooms' => $quotation->rooms,
                'people_per_room' => $quotation->people_per_room,
                'floor_area' => $quotation->floor_area,
                'area_unit' => $quotation->area_unit,
            ],

            'location' => [
                'street_address' => $quotation->street_address,
                'postal_code' => $quotation->postal_code,
                'city' => $quotation->city,
                'district' => $quotation->district,
                'latitude' => $quotation->latitude,
                'longitude' => $quotation->longitude,
            ],

            'pricing' => [
                'estimated_price' => $quotation->estimated_price ? number_format($quotation->estimated_price, 2) : null,
                'vat_amount' => $quotation->vat_amount ? number_format($quotation->vat_amount, 2) : null,
                'total_price' => $quotation->total_price ? number_format($quotation->total_price, 2) : null,
                'pricing_notes' => $quotation->pricing_notes,
            ],

            'activity' => [
                'reviewed_by' => $quotation->reviewedBy ? $quotation->reviewedBy->name : null,
                'reviewed_at' => $quotation->reviewed_at ? $quotation->reviewed_at->format('M d, Y g:i A') : null,
                'quoted_by' => $quotation->quotedBy ? $quotation->quotedBy->name : null,
                'quoted_at' => $quotation->quoted_at ? $quotation->quoted_at->format('M d, Y g:i A') : null,
                'converted_by' => $quotation->convertedBy ? $quotation->convertedBy->name : null,
                'converted_at' => $quotation->converted_at ? $quotation->converted_at->format('M d, Y g:i A') : null,
            ],

            'notes' => [
                'admin_notes' => $quotation->admin_notes,
                'rejection_reason' => $quotation->rejection_reason,
            ],
        ];
    }

    // Handle form submission from landing page
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Step 1: Service Information
            'bookingType' => 'required|in:personal,company',
            'cleaningServices' => 'nullable|array',
            'dateOfService' => 'nullable|date',
            'durationOfService' => 'nullable|integer',
            'typeOfUrgency' => 'nullable|string',

            // Step 2: Property Information
            'propertyType' => 'required|string',
            'floors' => 'required|integer|min:1',
            'rooms' => 'required|integer|min:1',
            'peoplePerRoom' => 'nullable|integer',
            'floorArea' => 'nullable|numeric',
            'areaUnit' => 'nullable|string',

            // Property Location
            'locationType' => 'nullable|string',
            'streetAddress' => 'nullable|string',
            'postalCode' => 'nullable|string|max:10',
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',

            // Step 3: Contact Information
            'companyName' => 'nullable|string',
            'clientName' => 'required|string',
            'phoneNumber' => 'required|string',
            'email' => 'required|email',
        ]);

        // Create quotation
        $quotation = Quotation::create([
            'booking_type' => $validated['bookingType'],
            'cleaning_services' => $validated['cleaningServices'] ?? null,
            'date_of_service' => $validated['dateOfService'] ?? null,
            'duration_of_service' => $validated['durationOfService'] ?? null,
            'type_of_urgency' => $validated['typeOfUrgency'] ?? null,

            'property_type' => $validated['propertyType'],
            'floors' => $validated['floors'],
            'rooms' => $validated['rooms'],
            'people_per_room' => $validated['peoplePerRoom'] ?? null,
            'floor_area' => $validated['floorArea'] ?? null,
            'area_unit' => $validated['areaUnit'] ?? null,

            'location_type' => $validated['locationType'] ?? null,
            'street_address' => $validated['streetAddress'] ?? null,
            'postal_code' => $validated['postalCode'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $validated['district'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,

            'company_name' => $validated['companyName'] ?? null,
            'client_name' => $validated['clientName'],
            'phone_number' => $validated['phoneNumber'],
            'email' => $validated['email'],

            'status' => 'pending_review',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your quotation request has been submitted successfully! We will contact you soon.',
            'quotation_id' => $quotation->id
        ], 201);
    }
}
