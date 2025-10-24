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

        return view('client-appointment-form', [
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
            'einvoice_number' => 'nullable|string|max:100',

            // Step 2: Service Details
            'service_type' => 'required|string',
            'service_date' => 'required|date|after_or_equal:today',
            'service_time' => 'required',
            'is_sunday' => 'required|boolean',
            'is_holiday' => 'nullable|boolean',
            'units' => 'required|integer|min:1|max:10',
            'unit_size' => 'required|string',
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
            $pricingRates = [
                '40-60' => ['normal' => 68.25, 'sunday' => 102.50],
                '60-90' => ['normal' => 84.00, 'sunday' => 126.00],
                '90-120' => ['normal' => 99.75, 'sunday' => 149.50],
                '120-150' => ['normal' => 115.50, 'sunday' => 173.25],
                '150-180' => ['normal' => 131.25, 'sunday' => 196.75],
                '180-220' => ['normal' => 157.50, 'sunday' => 236.25],
            ];

            $unitSize = $request->unit_size;
            $isSunday = $request->is_sunday ?? false;
            $isHoliday = $request->is_holiday ?? false;

            // External client rates: Sunday/Holiday use the same premium rate
            $ratePerUnit = ($isSunday || $isHoliday)
                ? $pricingRates[$unitSize]['sunday']
                : $pricingRates[$unitSize]['normal'];

            $quotation = $request->units * $ratePerUnit;
            $vatAmount = $quotation * 0.24; // 24% VAT
            $totalAmount = $quotation + $vatAmount;

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
                'unit_size' => $unitSize,
                'cabin_name' => $request->room_identifier,
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
                'quotation' => $quotation,
                'total_amount' => $totalAmount
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
