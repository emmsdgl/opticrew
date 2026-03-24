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
                'topRatedServices' => collect([]),
            ]);
        }

        // Fetch today's appointments (pending, approved, confirmed) for dashboard
        $appointments = ClientAppointment::where('client_id', $client->id)
            ->whereDate('service_date', now()->toDateString())
            ->whereIn('status', ['pending', 'approved', 'confirmed'])
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
        $todayAppointments = $allAppointments->filter(fn($a) => Carbon::parse($a->service_date)->isToday());
        $stats = [
            'total' => $allAppointments->count(),
            'ongoing' => $todayAppointments->whereIn('status', ['pending', 'approved', 'confirmed'])->count(),
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

        // Top 3 rated service categories for recommendation card
        // Group by base service category (e.g. "Final Cleaning - Demo Room" → "Final Cleaning")
        $baseCategories = ['Final Cleaning', 'Deep Cleaning', 'Daily Cleaning', 'Daily Room Cleaning', 'Snowout Cleaning', 'General Cleaning', 'Hotel Cleaning'];
        $topRatedServices = \App\Models\Feedback::whereNotNull('service_type')
            ->where('service_type', '!=', '')
            ->get()
            ->groupBy(function ($feedback) use ($baseCategories) {
                foreach ($baseCategories as $cat) {
                    if (str_starts_with($feedback->service_type, $cat)) {
                        return $cat;
                    }
                }
                return $feedback->service_type;
            })
            ->map(function ($group, $category) {
                return (object) [
                    'service_type' => $category,
                    'avg_rating' => $group->avg('rating'),
                    'review_count' => $group->count(),
                ];
            })
            ->sortByDesc('avg_rating')
            ->take(3)
            ->values();

        return view('client.dashboard', compact('client', 'holidays', 'appointments', 'stats', 'serviceBreakdown', 'topRatedServices'));
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
                    'trends' => [
                        'total' => ['value' => '0%', 'direction' => null, 'current' => 0, 'previous' => 0],
                        'ongoing' => ['value' => '0%', 'direction' => null, 'current' => 0, 'previous' => 0],
                        'completed' => ['value' => '0%', 'direction' => null, 'current' => 0, 'previous' => 0],
                    ],
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

        // Calculate month-over-month trends
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $thisMonthAppointments = $appointments->filter(fn($a) => Carbon::parse($a->service_date)->gte($thisMonth));
        $lastMonthAppointments = $appointments->filter(fn($a) => Carbon::parse($a->service_date)->gte($lastMonth) && Carbon::parse($a->service_date)->lte($lastMonthEnd));

        $thisMonthTotal = $thisMonthAppointments->count();
        $lastMonthTotal = $lastMonthAppointments->count();
        $thisMonthOngoing = $thisMonthAppointments->whereIn('status', ['pending', 'confirmed'])->count();
        $lastMonthOngoing = $lastMonthAppointments->whereIn('status', ['pending', 'confirmed'])->count();
        $thisMonthCompleted = $thisMonthAppointments->where('status', 'completed')->count();
        $lastMonthCompleted = $lastMonthAppointments->where('status', 'completed')->count();

        $calcTrend = function ($current, $previous) {
            if ($previous == 0) {
                return $current > 0
                    ? ['value' => '+100%', 'direction' => 'up', 'current' => $current, 'previous' => $previous]
                    : ['value' => '0%', 'direction' => null, 'current' => $current, 'previous' => $previous];
            }
            $change = round((($current - $previous) / $previous) * 100);
            return [
                'value' => ($change >= 0 ? '+' : '') . $change . '%',
                'direction' => $change >= 0 ? 'up' : 'down',
                'current' => $current,
                'previous' => $previous,
            ];
        };

        $stats['trends'] = [
            'total' => $calcTrend($thisMonthTotal, $lastMonthTotal),
            'ongoing' => $calcTrend($thisMonthOngoing, $lastMonthOngoing),
            'completed' => $calcTrend($thisMonthCompleted, $lastMonthCompleted),
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
            'holidays' => $holidays,
            'cscApiKey' => env('CSC_API_KEY', ''),
        ]);
    }

    /**
     * Get booked/unavailable time slots for a given date.
     * Takes into account the duration of existing bookings and the
     * estimated duration of the service being booked.
     */
    public function bookedSlots(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $date = $request->date;
        $newServiceType = $request->input('service_type', '');
        $newEstimatedHours = (float) $request->input('estimated_hours', 0);

        // Duration estimates per service type (in hours, default for medium-sized unit)
        $defaultDurations = [
            'Final Cleaning' => 3,
            'Deep Cleaning' => 4,
            'Daily Cleaning' => 2,
            'Snowout Cleaning' => 3,
            'General Cleaning' => 2,
            'Hotel Cleaning' => 3,
        ];

        // Get all active appointments on this date
        $appointments = ClientAppointment::whereDate('service_date', $date)
            ->whereNotIn('status', ['cancelled', 'rejected', 'completed'])
            ->get();

        // Build occupied time ranges from existing appointments
        $occupiedRanges = [];
        foreach ($appointments as $apt) {
            if (!$apt->service_time) continue;

            try {
                // Extract just the time (H:i:s) from service_time and combine with the correct date
                $timeOnly = \Carbon\Carbon::parse($apt->service_time)->format('H:i:s');
                $start = \Carbon\Carbon::parse($date . ' ' . $timeOnly);
            } catch (\Exception $e) {
                continue;
            }

            // Get actual duration from unit_details or fall back to service type default
            $duration = $defaultDurations[$apt->service_type] ?? 2;
            if ($apt->unit_details && is_array($apt->unit_details)) {
                $totalHours = 0;
                foreach ($apt->unit_details as $unit) {
                    $totalHours += $unit['hours'] ?? 0;
                }
                if ($totalHours > 0) {
                    $duration = $totalHours;
                }
            }

            $end = $start->copy()->addMinutes((int) ceil($duration * 60));
            $occupiedRanges[] = ['start' => $start, 'end' => $end];
        }

        // Determine duration of the new service being booked (in minutes)
        $newDurationMinutes = 120; // default 2 hours
        if ($newEstimatedHours > 0) {
            $newDurationMinutes = (int) ceil($newEstimatedHours * 60);
        } elseif (isset($defaultDurations[$newServiceType])) {
            $newDurationMinutes = $defaultDurations[$newServiceType] * 60;
        }

        // Check each 15-min slot: would booking at this time overlap with any existing appointment?
        $unavailable = [];
        for ($h = 6; $h <= 21; $h++) {
            for ($m = 0; $m < 60; $m += 15) {
                $slotStart = \Carbon\Carbon::parse($date)->setTime($h, $m, 0);
                $slotEnd = $slotStart->copy()->addMinutes($newDurationMinutes);

                // Check overlap with each occupied range: start1 < end2 AND end1 > start2
                foreach ($occupiedRanges as $range) {
                    if ($slotStart->lt($range['end']) && $slotEnd->gt($range['start'])) {
                        $unavailable[] = sprintf('%02d:%02d', $h, $m);
                        break;
                    }
                }
            }
        }

        return response()->json(['booked' => array_values(array_unique($unavailable))]);
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
            'service_type' => 'required|string|in:Final Cleaning,Deep Cleaning,Daily Cleaning,Snowout Cleaning,General Cleaning,Hotel Cleaning',
            'service_date' => 'required|date|after_or_equal:today',
            'service_time' => 'required',
            'is_sunday' => 'required|boolean',
            'is_holiday' => 'nullable|boolean',
            'units' => 'required|integer|min:1|max:20',
            'unit_size' => 'required|string|in:20-50,51-70,71-90,91-120,121-140,141-160,161-180,181-220',
            'room_identifier' => 'required|string|max:255',
            'special_requests' => 'nullable|string|max:1000'
        ]);

        // SCENARIO #1: Minimum booking notice requirement
        // - Standard booking: 3 days advance notice
        // - Priority Clean: 2 days advance notice (client opts in)
        // - Admin/Manager: no restriction
        // - Tomorrow (1 day): always blocked for non-privileged users
        $serviceDate = Carbon::parse($request->service_date);
        $daysUntilService = Carbon::today()->diffInDays($serviceDate, false);
        $user = Auth::user();
        $isPrivileged = $user && in_array($user->role, ['admin', 'manager']);
        $isPriority = $request->boolean('is_priority');

        if (!$isPrivileged) {
            if ($daysUntilService < 2) {
                // Tomorrow or today — always blocked
                return response()->json([
                    'success' => false,
                    'message' => 'This date is too soon. Bookings require at least 2 days advance notice.',
                    'error_code' => 'DATE_TOO_SOON',
                    'minimum_date' => Carbon::today()->addDays(2)->format('Y-m-d'),
                ], 422);
            } elseif ($daysUntilService < 3 && !$isPriority) {
                // 2 days out but Priority Clean not selected
                return response()->json([
                    'success' => false,
                    'message' => 'This date requires Priority Clean. Please enable "Priority Clean" to book within 3 days.',
                    'error_code' => 'PRIORITY_REQUIRED',
                    'minimum_date' => Carbon::today()->addDays(3)->format('Y-m-d'),
                ], 422);
            }
        }

        // SCENARIO #5: Calendar Conflict - Prevent double-booking on the same date
        // Check if this client already has a pending/approved appointment on the same date
        // Check for time slot overlap (allows multiple bookings per day if no overlap)
        $client = $user ? $user->client : null;
        if ($client && $request->service_time) {
            $defaultDurations = [
                'Final Cleaning' => 3, 'Deep Cleaning' => 4, 'Daily Cleaning' => 2,
                'Snowout Cleaning' => 3, 'General Cleaning' => 2, 'Hotel Cleaning' => 3,
            ];

            // Calculate new booking's time range
            $newStart = \Carbon\Carbon::parse($request->service_date . ' ' . \Carbon\Carbon::parse($request->service_time)->format('H:i:s'));
            $newDuration = $defaultDurations[$request->service_type] ?? 2;
            if ($request->unit_details && is_array($request->unit_details)) {
                $totalHours = 0;
                foreach ($request->unit_details as $unit) {
                    $totalHours += $unit['hours'] ?? 0;
                }
                if ($totalHours > 0) $newDuration = $totalHours;
            }
            $newEnd = $newStart->copy()->addMinutes((int) ceil($newDuration * 60));

            // Check overlap with existing appointments
            $existingAppointments = ClientAppointment::where('client_id', $client->id)
                ->whereDate('service_date', $request->service_date)
                ->whereIn('status', ['pending', 'approved', 'confirmed'])
                ->get();

            foreach ($existingAppointments as $existing) {
                if (!$existing->service_time) continue;
                $existTimeOnly = \Carbon\Carbon::parse($existing->service_time)->format('H:i:s');
                $existStart = \Carbon\Carbon::parse($request->service_date . ' ' . $existTimeOnly);
                $existDuration = $defaultDurations[$existing->service_type] ?? 2;
                if ($existing->unit_details && is_array($existing->unit_details)) {
                    $th = 0;
                    foreach ($existing->unit_details as $u) { $th += $u['hours'] ?? 0; }
                    if ($th > 0) $existDuration = $th;
                }
                $existEnd = $existStart->copy()->addMinutes((int) ceil($existDuration * 60));

                // Overlap check: start1 < end2 AND end1 > start2
                if ($newStart->lt($existEnd) && $newEnd->gt($existStart)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This time slot overlaps with an existing booking (' . $existStart->format('h:i A') . ' - ' . $existEnd->format('h:i A') . '). Please choose a different time.',
                        'error_code' => 'TIME_CONFLICT',
                    ], 422);
                }
            }
        }

        try {
            DB::beginTransaction();

            // Refresh client record (may have been loaded above for conflict check)
            if (!$client) {
                $client = $user ? $user->client : null;
            }

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

            if (!in_array($appointment->status, ['pending', 'approved', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This appointment cannot be cancelled in its current state.'
                ], 400);
            }

            // SCENARIO #7: Cancellation Policy
            // - Same-day cancellations (within Grace Period of 24 hours) trigger "Late Cancellation" flag with penalty fee
            // - Cancel button locked for confirmed appointments → must use "Request Cancellation (Fee Applies)"
            // SCENARIO #8: Urgent/Priority flags bypass the 3-day cancellation rule (admin/manager only)
            $isPrivileged = $user && in_array($user->role, ['admin', 'manager']);
            $serviceDate = Carbon::parse($appointment->service_date);
            $hoursUntilService = Carbon::now()->diffInHours($serviceDate, false);
            $daysUntilService = Carbon::now()->diffInDays($serviceDate, false);

            $cancellationType = 'standard'; // default
            $cancellationFee = 0;
            $feeApplies = false;

            if ($daysUntilService < 0) {
                // Past service date - cannot cancel
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel past appointments.'
                ], 400);
            }

            if ($hoursUntilService <= 24 && !$isPrivileged) {
                // SCENARIO #7: Same-day / Grace Period cancellation (< 24 hours)
                // Late Cancellation with penalty fee
                $cancellationType = 'late_cancellation';
                $feeApplies = true;
                $cancellationFee = round($appointment->total_amount * 0.50, 2); // 50% penalty fee

                Log::info('Late cancellation detected (Grace Period)', [
                    'appointment_id' => $appointment->id,
                    'hours_until_service' => $hoursUntilService,
                    'cancellation_fee' => $cancellationFee,
                ]);
            } elseif ($daysUntilService < 3 && !$isPrivileged) {
                // Within 3 days but more than 24 hours - Request Cancellation with reduced fee
                $cancellationType = 'request_cancellation';
                $feeApplies = true;
                $cancellationFee = round($appointment->total_amount * 0.25, 2); // 25% fee

                Log::info('Short-notice cancellation (Fee Applies)', [
                    'appointment_id' => $appointment->id,
                    'days_until_service' => $daysUntilService,
                    'cancellation_fee' => $cancellationFee,
                ]);
            }

            // Update appointment
            $appointment->update([
                'status' => 'cancelled',
                'cancellation_type' => $cancellationType,
                'cancellation_fee' => $cancellationFee,
                'cancelled_at' => now(),
                'cancelled_by' => $user->id,
            ]);

            Log::info('Appointment cancelled by client', [
                'appointment_id' => $appointment->id,
                'client_id' => $client->id,
                'cancellation_type' => $cancellationType,
                'fee_applies' => $feeApplies,
                'cancellation_fee' => $cancellationFee,
            ]);

            // Notify all admins about the cancellation
            $clientName = $client->first_name . ' ' . $client->last_name;
            $this->notificationService->notifyAdminsAppointmentCancelled($appointment, $clientName);

            $message = 'Appointment cancelled successfully.';
            if ($feeApplies) {
                $message = "Appointment cancelled. A cancellation fee of €{$cancellationFee} applies due to {$cancellationType}.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'cancellation_type' => $cancellationType,
                'fee_applies' => $feeApplies,
                'cancellation_fee' => $cancellationFee,
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
     * SCENARIO #7: Get cancellation policy info for an appointment
     * Returns whether cancel button should be locked and what fees apply
     */
    public function getCancellationPolicy($id)
    {
        $user = Auth::user();
        $client = $user ? $user->client : null;

        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Client not found'], 404);
        }

        $appointment = ClientAppointment::where('id', $id)
            ->where('client_id', $client->id)
            ->first();

        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found'], 404);
        }

        $isPrivileged = $user && in_array($user->role, ['admin', 'manager']);
        $serviceDate = Carbon::parse($appointment->service_date);
        $hoursUntilService = Carbon::now()->diffInHours($serviceDate, false);
        $daysUntilService = Carbon::now()->diffInDays($serviceDate, false);

        $canCancel = in_array($appointment->status, ['pending', 'approved', 'confirmed']);
        $cancelButtonLocked = false;
        $feeApplies = false;
        $feePercentage = 0;
        $estimatedFee = 0;
        $policyMessage = 'Free cancellation available.';

        if ($daysUntilService < 0) {
            $canCancel = false;
            $policyMessage = 'Cannot cancel past appointments.';
        } elseif ($hoursUntilService <= 24 && !$isPrivileged) {
            $cancelButtonLocked = true;
            $feeApplies = true;
            $feePercentage = 50;
            $estimatedFee = round($appointment->total_amount * 0.50, 2);
            $policyMessage = "Late cancellation: 50% fee (€{$estimatedFee}) applies. Contact admin for assistance.";
        } elseif ($daysUntilService < 3 && !$isPrivileged) {
            $feeApplies = true;
            $feePercentage = 25;
            $estimatedFee = round($appointment->total_amount * 0.25, 2);
            $policyMessage = "Short-notice cancellation: 25% fee (€{$estimatedFee}) applies.";
        }

        return response()->json([
            'success' => true,
            'can_cancel' => $canCancel,
            'cancel_button_locked' => $cancelButtonLocked,
            'fee_applies' => $feeApplies,
            'fee_percentage' => $feePercentage,
            'estimated_fee' => $estimatedFee,
            'policy_message' => $policyMessage,
            'is_privileged' => $isPrivileged,
            'hours_until_service' => max(0, $hoursUntilService),
            'days_until_service' => max(0, $daysUntilService),
        ]);
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
