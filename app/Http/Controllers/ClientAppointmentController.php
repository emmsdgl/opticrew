<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Client;
use App\Models\ClientAppointment;
use App\Models\Holiday;
use Carbon\Carbon;

class ClientAppointmentController extends Controller
{
    /**
     * Display client dashboard with appointments
     */
    public function dashboard()
    {
        $user = Auth::user();
        $client = $user ? $user->client : null;

        // Fetch holidays
        $holidays = Holiday::all()->map(function ($holiday) {
            return [
                'date' => $holiday->date->format('Y-m-d'),
                'name' => $holiday->name,
            ];
        });

        if (!$client) {
            return view('client.dashboard', [
                'client' => null,
                'holidays' => $holidays,
                'appointments' => collect([]),
                'stats' => [
                    'total' => 0,
                    'ongoing' => 0,
                    'completed' => 0,
                    'cancelled' => 0,
                ]
            ]);
        }

        // Fetch ongoing appointments (pending and confirmed) for dashboard
        $appointments = ClientAppointment::where('client_id', $client->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('service_date', 'asc')
            ->orderBy('service_time', 'asc')
            ->get();

        // Calculate statistics
        $allAppointments = ClientAppointment::where('client_id', $client->id)->get();
        $stats = [
            'total' => $allAppointments->count(),
            'ongoing' => $allAppointments->whereIn('status', ['pending', 'confirmed'])->count(),
            'completed' => $allAppointments->where('status', 'completed')->count(),
            'cancelled' => $allAppointments->where('status', 'cancelled')->count(),
        ];

        return view('client.dashboard', compact('client', 'holidays', 'appointments', 'stats'));
    }

    /**
     * Display client appointments list
     */
    public function index()
    {
        $user = Auth::user();
        $client = $user ? $user->client : null;

        Log::info('Client Appointments Index', [
            'user_id' => $user ? $user->id : null,
            'client_id' => $client ? $client->id : null,
        ]);

        if (!$client) {
            Log::warning('No client record found for user', ['user_id' => $user ? $user->id : null]);
            return view('client.appointments', [
                'appointments' => collect([]),
                'stats' => [
                    'total' => 0,
                    'ongoing' => 0,
                    'completed' => 0,
                    'cancelled' => 0,
                ]
            ]);
        }

        // Fetch all appointments for this client
        $appointments = ClientAppointment::where('client_id', $client->id)
            ->orderBy('service_date', 'desc')
            ->orderBy('service_time', 'desc')
            ->get();

        Log::info('Appointments fetched', [
            'client_id' => $client->id,
            'count' => $appointments->count(),
        ]);

        // Calculate statistics
        $stats = [
            'total' => $appointments->count(),
            'ongoing' => $appointments->whereIn('status', ['pending', 'confirmed'])->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
        ];

        return view('client.appointments', compact('appointments', 'stats'));
    }

    /**
     * Show the appointment booking form
     */
    public function create()
    {
        // Get authenticated user and their client record
        $user = Auth::user();
        $client = $user ? $user->client : null;

        // Fetch holidays
        $holidays = Holiday::all()->map(function ($holiday) {
            return [
                'date' => $holiday->date->format('Y-m-d'),
                'name' => $holiday->name,
            ];
        });

        return view('client.appointment-form', [
            'currentStep' => 1,
            'client' => $client,
            'user' => $user,
            'holidays' => $holidays
        ]);
    }

    /**
     * Store the appointment and create a task
     */
    public function store(Request $request)
    {
        // Validate the appointment data
        $request->validate([
            // Step 1: Client Details
            'booking_type' => 'required|string|in:personal,company',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'country_code' => 'required|string',
            'mobile_number' => 'required|string|max:20',
            'street_address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'billing_address' => 'nullable|string|max:500',

            // Step 2: Service Details
            'service_type' => 'required|string|in:Final Cleaning,Deep Cleaning',
            'service_date' => 'required|date|after_or_equal:today',
            'service_time' => 'required',
            'is_sunday' => 'required|boolean',
            'is_holiday' => 'nullable|boolean',
            'units' => 'required|integer|min:1|max:20',
            'unit_size' => 'required|string|in:20-50,51-70,71-90,91-120,121-140,141-160,161-180,181-220',
            'room_identifier' => 'required|string|max:255',
            'special_requests' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Get authenticated user and their client record
            $user = Auth::user();
            $client = $user ? $user->client : null;

            if (!$client) {
                // Create new client if not authenticated
                $fullAddress = $request->street_address . ', ' .
                              $request->postal_code . ' ' .
                              $request->city . ', ' .
                              $request->district;

                $client = Client::create([
                    'user_id' => $user ? $user->id : null,
                    'client_type' => $request->booking_type,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone_number' => $request->country_code . $request->mobile_number,
                    'street_address' => $request->street_address,
                    'postal_code' => $request->postal_code,
                    'city' => $request->city,
                    'district' => $request->district,
                    'address' => $fullAddress,
                    'billing_address' => $request->billing_address ?? $fullAddress,
                    'einvoice_number' => $request->einvoice_number,
                    'is_active' => true
                ]);

                Log::info('New client created for appointment', [
                    'client_id' => $client->id,
                    'email' => $client->email
                ]);
            } else {
                // Update existing client with new address info if changed
                $fullAddress = $request->street_address . ', ' .
                              $request->postal_code . ' ' .
                              $request->city . ', ' .
                              $request->district;

                $client->update([
                    'street_address' => $request->street_address,
                    'postal_code' => $request->postal_code,
                    'city' => $request->city,
                    'district' => $request->district,
                    'address' => $fullAddress,
                    'billing_address' => $request->billing_address ?? $fullAddress,
                ]);
            }

            // Calculate pricing for external clients
            // Final Cleaning rates (VAT inclusive)
            $finalCleaningRates = [
                '20-50' => ['normal' => 70.00, 'sunday' => 140.00],
                '51-70' => ['normal' => 105.00, 'sunday' => 210.00],
                '71-90' => ['normal' => 140.00, 'sunday' => 280.00],
                '91-120' => ['normal' => 175.00, 'sunday' => 350.00],
                '121-140' => ['normal' => 210.00, 'sunday' => 420.00],
                '141-160' => ['normal' => 245.00, 'sunday' => 490.00],
                '161-180' => ['normal' => 280.00, 'sunday' => 560.00],
                '181-220' => ['normal' => 315.00, 'sunday' => 630.00],
            ];

            // Deep Cleaning rates
            $deepCleaningEstimates = [
                '20-50' => 2.5,
                '51-70' => 3.5,
                '71-90' => 4.5,
                '91-120' => 5.5,
                '121-140' => 6.5,
                '141-160' => 7.5,
                '161-180' => 8.5,
                '181-220' => 10,
            ];
            $deepCleaningHourlyRate = 48.00;

            $isSunday = $request->is_sunday ?? false;
            $isHoliday = $request->is_holiday ?? false;
            $serviceType = $request->service_type;

            // Calculate total by summing individual unit prices
            $quotation = 0;
            $unitDetails = $request->unit_details;

            if ($unitDetails && is_array($unitDetails) && count($unitDetails) > 0) {
                // New bookings: Calculate from unit_details array (each unit can have different size)
                foreach ($unitDetails as $unit) {
                    $unitSize = $unit['size'] ?? null;

                    if (!$unitSize) {
                        continue;
                    }

                    if ($serviceType === 'Final Cleaning') {
                        // Final Cleaning: fixed rate based on unit size
                        $rates = $finalCleaningRates[$unitSize] ?? ['normal' => 0, 'sunday' => 0];
                        $ratePerUnit = ($isSunday || $isHoliday) ? $rates['sunday'] : $rates['normal'];
                        $quotation += $ratePerUnit;
                    } else if ($serviceType === 'Deep Cleaning') {
                        // Deep Cleaning: hourly rate
                        $hours = $deepCleaningEstimates[$unitSize] ?? 0;
                        $basePrice = $hours * $deepCleaningHourlyRate;
                        $ratePerUnit = ($isSunday || $isHoliday) ? ($basePrice * 2) : $basePrice;
                        $quotation += $ratePerUnit;
                    }
                }
            } else {
                // Fallback for old bookings: Use single unit_size × number of units
                $unitSize = $request->unit_size;

                if ($serviceType === 'Final Cleaning') {
                    $rates = $finalCleaningRates[$unitSize] ?? ['normal' => 0, 'sunday' => 0];
                    $ratePerUnit = ($isSunday || $isHoliday) ? $rates['sunday'] : $rates['normal'];
                    $quotation = $request->units * $ratePerUnit;
                } else if ($serviceType === 'Deep Cleaning') {
                    $hours = $deepCleaningEstimates[$unitSize] ?? 0;
                    $basePrice = $hours * $deepCleaningHourlyRate;
                    $ratePerUnit = ($isSunday || $isHoliday) ? ($basePrice * 2) : $basePrice;
                    $quotation = $request->units * $ratePerUnit;
                }
            }

            // Prices are already VAT inclusive
            $totalAmount = $quotation;
            $vatAmount = 0;  // VAT already included in pricing

            // Get first unit's size and name for backward compatibility
            $firstUnitSize = $request->unit_size;
            $firstUnitName = $request->room_identifier;

            if ($unitDetails && is_array($unitDetails) && count($unitDetails) > 0) {
                $firstUnitSize = $unitDetails[0]['size'] ?? $request->unit_size;
                $firstUnitName = $unitDetails[0]['name'] ?? $request->room_identifier;
            }

            // Create appointment (NOT task yet - waits for admin approval)
            $appointment = ClientAppointment::create([
                'client_id' => $client->id,
                'booking_type' => $request->booking_type,
                'service_type' => $request->service_type,
                'service_date' => $request->service_date,
                'service_time' => $request->service_time,
                'is_sunday' => $isSunday,
                'is_holiday' => $isHoliday,
                'number_of_units' => $request->units,
                'unit_size' => $firstUnitSize,
                'cabin_name' => $firstUnitName,
                'unit_details' => $request->unit_details ?? null,  // Store all unit details as JSON
                'special_requests' => $request->special_requests,
                'quotation' => $quotation,
                'vat_amount' => $vatAmount,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            Log::info('Client appointment created (pending approval)', [
                'appointment_id' => $appointment->id,
                'client_id' => $client->id,
                'service_date' => $request->service_date,
                'number_of_units' => $request->units,
                'unit_details' => $unitDetails,
                'quotation' => $quotation,
                'total_amount' => $totalAmount,
                'calculation_method' => $unitDetails ? 'unit_details_array' : 'legacy_single_unit'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment request submitted successfully! Waiting for admin approval.',
                'appointment_id' => $appointment->id,
                'client_id' => $client->id,
                'redirect_url' => route('client.dashboard')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create appointment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to book appointment. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
