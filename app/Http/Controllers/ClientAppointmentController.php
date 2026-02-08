<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Client;
use App\Models\ClientAppointment;
use App\Models\Holiday;
use App\Models\Task;
use App\Models\Feedback;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;

class ClientAppointmentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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
                ],
                'serviceBreakdown' => [
                    'completed_total' => 0,
                    'final_cleaning' => 0,
                    'deep_cleaning' => 0,
                    'other' => 0,
                    'month_change' => 0,
                ],
            ]);
        }

        // Fetch ongoing appointments (pending and confirmed) for dashboard
        $appointments = ClientAppointment::where('client_id', $client->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('service_date', 'asc')
            ->orderBy('service_time', 'asc')
            ->get()
            ->map(function ($appointment) {
                // Load related task with checklist completions for progress tracking
                $relatedTask = Task::with('checklistCompletions')
                    ->where('client_id', $appointment->client_id)
                    ->whereDate('scheduled_date', $appointment->service_date)
                    ->where('task_description', 'like', '%' . $appointment->service_type . '%')
                    ->first();

                if ($relatedTask) {
                    $completedItems = $relatedTask->checklistCompletions
                        ->where('is_completed', true)
                        ->pluck('checklist_item_id')
                        ->toArray();

                    $appointment->checklist_completions = $completedItems;
                    $appointment->task_id = $relatedTask->id;
                    $appointment->task_status = $relatedTask->status;
                } else {
                    $appointment->checklist_completions = [];
                    $appointment->task_id = null;
                    $appointment->task_status = null;
                }

                return $appointment;
            });

        // Calculate statistics
        $allAppointments = ClientAppointment::where('client_id', $client->id)->get();
        $stats = [
            'total' => $allAppointments->count(),
            'ongoing' => $allAppointments->whereIn('status', ['pending', 'confirmed'])->count(),
            'completed' => $allAppointments->where('status', 'completed')->count(),
            'cancelled' => $allAppointments->where('status', 'cancelled')->count(),
        ];

        // Service breakdown for "Services Availed" card
        $completedAppointments = $allAppointments->where('status', 'completed');
        $finalCleaningCount = $completedAppointments->where('service_type', 'Final Cleaning')->count();
        $deepCleaningCount = $completedAppointments->where('service_type', 'Deep Cleaning')->count();
        $otherCount = $completedAppointments->whereNotIn('service_type', ['Final Cleaning', 'Deep Cleaning'])->count();

        // Calculate month-over-month change
        $thisMonthCompleted = $completedAppointments->filter(function ($a) {
            return Carbon::parse($a->service_date)->isCurrentMonth();
        })->count();
        $lastMonthCompleted = $completedAppointments->filter(function ($a) {
            return Carbon::parse($a->service_date)->month === now()->subMonth()->month
                && Carbon::parse($a->service_date)->year === now()->subMonth()->year;
        })->count();
        $monthChange = $lastMonthCompleted > 0
            ? round((($thisMonthCompleted - $lastMonthCompleted) / $lastMonthCompleted) * 100)
            : ($thisMonthCompleted > 0 ? 100 : 0);

        $serviceBreakdown = [
            'completed_total' => $completedAppointments->count(),
            'final_cleaning' => $finalCleaningCount,
            'deep_cleaning' => $deepCleaningCount,
            'other' => $otherCount,
            'month_change' => $monthChange,
        ];

        return view('client.dashboard', compact('client', 'holidays', 'appointments', 'stats', 'serviceBreakdown'));
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

        // Fetch all appointments for this client with assigned team members
        $appointments = ClientAppointment::where('client_id', $client->id)
            ->with(['assignedTeam.employees.user'])
            ->orderBy('service_date', 'desc')
            ->orderBy('service_time', 'desc')
            ->get()
            ->map(function ($appointment) {
                // Format assigned team members for the drawer component
                if ($appointment->assignedTeam && $appointment->assignedTeam->employees->count() > 0) {
                    $appointment->assigned_members = $appointment->assignedTeam->employees->map(function ($employee) {
                        $fullName = $employee->full_name ?? 'Team Member';
                        // Extract initials from full name (e.g., "John Doe" -> "JD")
                        $nameParts = explode(' ', $fullName);
                        $initial = '';
                        foreach ($nameParts as $part) {
                            if (!empty($part)) {
                                $initial .= strtoupper(substr($part, 0, 1));
                            }
                        }
                        $initial = substr($initial, 0, 2); // Max 2 characters

                        return [
                            'id' => $employee->id,
                            'name' => $fullName,
                            'initial' => $initial ?: 'TM',
                            'avatar' => $employee->user->profile_photo_path ?? null,
                        ];
                    })->toArray();
                } else {
                    $appointment->assigned_members = [];
                }

                // Load related task with checklist completions for progress tracking
                $relatedTask = Task::with('checklistCompletions')
                    ->where('client_id', $appointment->client_id)
                    ->whereDate('scheduled_date', $appointment->service_date)
                    ->where('task_description', 'like', '%' . $appointment->service_type . '%')
                    ->first();

                if ($relatedTask) {
                    $completedItems = $relatedTask->checklistCompletions
                        ->where('is_completed', true)
                        ->pluck('checklist_item_id')
                        ->toArray();

                    $appointment->checklist_completions = $completedItems;
                    $appointment->task_id = $relatedTask->id;
                    $appointment->task_status = $relatedTask->status;
                } else {
                    $appointment->checklist_completions = [];
                    $appointment->task_id = null;
                    $appointment->task_status = null;
                }

                return $appointment;
            });

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

        // Fetch completed appointments for rating (exclude already rated ones)
        $completedAppointments = ClientAppointment::where('client_id', $client->id)
            ->where('status', 'completed')
            ->whereDoesntHave('clientFeedback', function ($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->orderBy('service_date', 'desc')
            ->orderBy('service_time', 'desc')
            ->get();

        return view('client.appointments', compact('appointments', 'stats', 'completedAppointments'));
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

            // Notify all admins about the new appointment
            $clientName = $client->first_name . ' ' . $client->last_name;
            $this->notificationService->notifyAdminsNewAppointment($appointment, $clientName);

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

    /**
     * Cancel an appointment
     */
    public function cancel($id)
    {
        try {
            $user = Auth::user();
            $client = $user ? $user->client : null;

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            $appointment = ClientAppointment::where('id', $id)
                ->where('client_id', $client->id)
                ->first();

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found'
                ], 404);
            }

            if ($appointment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending appointments can be cancelled'
                ], 400);
            }

            $appointment->update(['status' => 'cancelled']);

            Log::info('Appointment cancelled by client', [
                'appointment_id' => $appointment->id,
                'client_id' => $client->id
            ]);

            // Notify all admins about the cancellation
            $clientName = $client->first_name . ' ' . $client->last_name;
            $this->notificationService->notifyAdminsAppointmentCancelled($appointment, $clientName);

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cancel appointment', [
                'appointment_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel appointment'
            ], 500);
        }
    }

    /**
     * Store feedback for a completed appointment
     */
    public function storeFeedback(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $client = $user ? $user->client : null;

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            $appointment = ClientAppointment::where('id', $id)
                ->where('client_id', $client->id)
                ->where('status', 'completed')
                ->first();

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Completed appointment not found'
                ], 404);
            }

            // Check if feedback already exists for this appointment
            $existingFeedback = Feedback::where('appointment_id', $id)
                ->where('client_id', $client->id)
                ->first();

            if ($existingFeedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already submitted feedback for this appointment'
                ], 400);
            }

            // Validate the request
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'keywords' => 'nullable|array',
                'keywords.*' => 'string',
                'feedback_text' => 'nullable|string|max:1000'
            ]);

            // Create the feedback
            $feedback = Feedback::create([
                'client_id' => $client->id,
                'appointment_id' => $appointment->id,
                'service_type' => $appointment->service_type,
                'user_type' => 'client',
                'rating' => $validated['rating'],
                'overall_rating' => $validated['rating'],
                'keywords' => $validated['keywords'] ?? [],
                'feedback_text' => $validated['feedback_text'] ?? null,
                'comments' => $validated['feedback_text'] ?? null,
            ]);

            Log::info('Client feedback submitted', [
                'feedback_id' => $feedback->id,
                'appointment_id' => $appointment->id,
                'client_id' => $client->id,
                'rating' => $validated['rating']
            ]);

            // Send notification to client about feedback submission
            $this->notificationService->notifyClientFeedbackSubmitted($user, $feedback, $appointment);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback!'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to submit feedback', [
                'appointment_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback. Please try again.'
            ], 500);
        }
    }
}
