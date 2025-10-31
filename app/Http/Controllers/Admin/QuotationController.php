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
    public function show($id)
    {
        $quotation = Quotation::with(['reviewedBy', 'quotedBy', 'convertedBy', 'appointment'])->findOrFail($id);

        return view('admin.quotations.show', compact('quotation'));
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
