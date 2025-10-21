<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Client;
use App\Models\Task;
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

        return view('client-appointment-form', [
            'currentStep' => 1,
            'client' => $client,
            'user' => $user
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
            'service_type' => 'required|string|in:annual,monthly,weekly',
            'service_date' => 'required|date|after_or_equal:today',
            'service_time' => 'required|date_format:H:i',
            'units' => 'required|integer|min:1|max:10',
            'unit_length' => 'nullable|numeric',
            'unit_width' => 'nullable|numeric',
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

            // Calculate estimated duration based on units and service type
            $baseDurationPerUnit = 60; // 60 minutes per unit
            switch ($request->service_type) {
                case 'annual':
                    $baseDurationPerUnit = 120; // Deep cleaning
                    break;
                case 'monthly':
                    $baseDurationPerUnit = 90;
                    break;
                case 'weekly':
                    $baseDurationPerUnit = 60;
                    break;
            }

            $estimatedDuration = $request->units * $baseDurationPerUnit;

            // Create task from appointment
            $task = Task::create([
                'client_id' => $client->id,
                'location_id' => null, // Will be null for client-submitted appointments
                'task_description' => ucfirst($request->service_type) . ' Cleaning - ' . $request->room_identifier,
                'scheduled_date' => $request->service_date,
                'scheduled_time' => $request->service_time,
                'duration' => $estimatedDuration,
                'estimated_duration_minutes' => $estimatedDuration,
                'travel_time' => 30, // Default 30 minutes travel time
                'status' => 'Pending',
                'arrival_status' => false, // Client bookings are not urgent by default
                'notes' => $request->special_requests,
                'assigned_team_id' => null // Will be assigned during optimization
            ]);

            Log::info('Task created from client appointment', [
                'task_id' => $task->id,
                'client_id' => $client->id,
                'service_date' => $request->service_date,
                'units' => $request->units
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment booked successfully!',
                'task_id' => $task->id,
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
