<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ClientAppointment;
use App\Models\Task;
use App\Models\OptimizationTeam;
use App\Models\OptimizationRun;
use App\Services\Optimization\OptimizationService;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    protected $optimizationService;
    protected $notificationService;

    public function __construct(OptimizationService $optimizationService, NotificationService $notificationService)
    {
        $this->optimizationService = $optimizationService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display list of all appointments
     */
    public function index()
    {
        $pendingAppointments = ClientAppointment::with(['client', 'assignedTeam', 'recommendedTeam'])
            ->where('is_company_inquiry', false)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $assignedAppointments = ClientAppointment::with(['client', 'assignedTeam', 'recommendedTeam'])
            ->where('is_company_inquiry', false)
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'assigned_page');

        $counts = [
            'pending' => ClientAppointment::where('is_company_inquiry', false)->where('status', 'pending')->count(),
            'approved' => ClientAppointment::where('is_company_inquiry', false)->where('status', 'approved')->count(),
            'rejected' => ClientAppointment::where('is_company_inquiry', false)->where('status', 'rejected')->count(),
            'total' => ClientAppointment::where('is_company_inquiry', false)->count(),
        ];

        return view('admin.appointments.index', compact('pendingAppointments', 'assignedAppointments', 'counts'));
    }

    /**
     * Display appointment details
     */
    public function show(Request $request, $id)
    {
        $appointment = ClientAppointment::with([
            'client.user',
            'assignedTeam.members.employee.user',
            'recommendedTeam.members.employee.user',
            'approvedBy',
            'rejectedBy'
        ])->findOrFail($id);

        // Get available teams for the appointment date if status is pending or approved
        $availableTeams = null;
        if (in_array($appointment->status, ['pending', 'approved'])) {
            $availableTeams = $this->getTeamsForDate($appointment->service_date);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'appointment' => $this->formatAppointmentForDrawer($appointment, $availableTeams)
            ]);
        }

        return view('admin.appointments.show', compact('appointment', 'availableTeams'));
    }

    /**
     * Format appointment data for the drawer JSON response
     */
    protected function formatAppointmentForDrawer(ClientAppointment $appointment, $availableTeams)
    {
        $client = $appointment->client;

        $data = [
            'id' => $appointment->id,
            'status' => $appointment->status,
            'is_company_inquiry' => (bool) $appointment->is_company_inquiry,
            'booking_type' => $appointment->booking_type ?? 'personal',
            'client' => [
                'first_name' => $client->first_name ?? '',
                'last_name' => $client->last_name ?? '',
                'company_name' => $client->company_name ?? '',
                'business_id' => $client->business_id ?? '',
                'email' => $client->email ?? ($client->user->email ?? ''),
                'phone' => $client->phone_number ?? ($client->user->phone ?? ''),
                'einvoice_number' => $client->einvoice_number ?? '',
                'address' => $client->address ?? '',
                'user_email' => $client->user->email ?? '',
                'user_phone' => $client->user->phone ?? '',
            ],
            'service' => [
                'service_type' => $appointment->service_type,
                'number_of_units' => $appointment->number_of_units,
                'service_date' => $appointment->service_date ? Carbon::parse($appointment->service_date)->format('l, F d, Y') : '',
                'service_date_short' => $appointment->service_date ? Carbon::parse($appointment->service_date)->format('M d, Y') : '',
                'service_time' => $appointment->service_time ? Carbon::parse($appointment->service_time)->format('g:i A') : '',
                'is_sunday' => (bool) $appointment->is_sunday,
                'is_holiday' => (bool) $appointment->is_holiday,
                'cabin_name' => $appointment->cabin_name ?? '',
                'unit_size' => $appointment->unit_size ?? '',
                'unit_details' => $appointment->unit_details ?? [],
                'company_service_types' => $this->parseServiceTypes($appointment->company_service_types),
                'other_concerns' => $appointment->other_concerns ?? '',
                'special_requests' => $appointment->special_requests ?? '',
            ],
            'pricing' => [
                'subtotal' => $appointment->quotation ? number_format($appointment->quotation, 2) : '0.00',
                'vat' => $appointment->vat_amount ? number_format($appointment->vat_amount, 2) : '0.00',
                'total' => $appointment->total_amount ? number_format($appointment->total_amount, 2) : '0.00',
            ],
            'checklist' => !$appointment->is_company_inquiry ? $this->getChecklistForServiceType($appointment->service_type) : [],
            'timeline' => [
                'submitted' => $appointment->created_at ? $appointment->created_at->format('M d, Y H:i') : '',
                'approved' => $appointment->approved_at ? Carbon::parse($appointment->approved_at)->format('M d, Y H:i') : null,
                'rejected' => $appointment->rejected_at ? Carbon::parse($appointment->rejected_at)->format('M d, Y H:i') : null,
            ],
            'rejection' => [
                'reason' => $appointment->rejection_reason ?? '',
                'by' => $appointment->rejectedBy->name ?? '',
                'at' => $appointment->rejected_at ? Carbon::parse($appointment->rejected_at)->format('M d, Y H:i') : '',
            ],
            'team' => [
                'assigned' => $appointment->assignedTeam ? [
                    'id' => $appointment->assignedTeam->id,
                    'members' => $appointment->assignedTeam->members
                        ->filter(fn($m) => $m->employee && $m->employee->user)
                        ->map(fn($m) => [
                            'name' => $m->employee->user->name,
                            'role' => $m->role ?? 'member',
                            'is_driver' => ($m->role ?? '') === 'driver',
                        ])->values()->toArray(),
                ] : null,
                'recommended' => $appointment->recommendedTeam ? [
                    'id' => $appointment->recommendedTeam->id,
                    'members' => $appointment->recommendedTeam->members
                        ->filter(fn($m) => $m->employee && $m->employee->user)
                        ->map(fn($m) => [
                            'name' => $m->employee->user->name,
                            'role' => $m->role ?? 'member',
                            'is_driver' => ($m->role ?? '') === 'driver',
                        ])->values()->toArray(),
                ] : null,
                'available' => $availableTeams ? $availableTeams->toArray() : [],
            ],
        ];

        return $data;
    }

    /**
     * Parse company service types from string or array
     */
    protected function parseServiceTypes($serviceTypes)
    {
        if (is_string($serviceTypes)) {
            $serviceTypes = json_decode($serviceTypes, true);
        }
        return is_array($serviceTypes) ? $serviceTypes : [];
    }

    /**
     * Get checklist items based on service type
     */
    protected function getChecklistForServiceType($serviceType)
    {
        $checklistTemplates = [
            'daily_cleaning' => [
                'Sweep and mop floors', 'Vacuum carpets/rugs', 'Dust furniture and surfaces',
                'Wipe tables and countertops', 'Empty trash bins', 'Wipe kitchen counters',
                'Clean sink', 'Wash visible dishes', 'Wipe appliance exteriors',
                'Clean toilet and sink', 'Wipe mirrors', 'Mop floor',
                'Organize cluttered areas', 'Light deodorizing',
            ],
            'snowout_cleaning' => [
                'Remove mud, water, and debris', 'Clean door mats', 'Mop and dry floors',
                'Deep vacuum carpets', 'Mop with disinfectant solution', 'Wipe walls near entrances',
                'Dry wet surfaces', 'Check for water accumulation', 'Clean and sanitize affected areas',
                'Dispose of tracked-in debris', 'Replace trash liners',
            ],
            'deep_cleaning' => [
                'Dust high and low areas (vents, corners, baseboards)', 'Clean behind and under furniture',
                'Wash walls and remove stains', 'Deep vacuum carpets', 'Clean inside microwave',
                'Degrease stove and range hood', 'Clean inside refrigerator (if included)',
                'Scrub tile grout', 'Remove limescale and mold buildup', 'Deep scrub tiles and grout',
                'Sanitize all fixtures thoroughly', 'Clean window interiors',
                'Polish handles and knobs', 'Disinfect frequently touched surfaces',
            ],
            'general_cleaning' => [
                'Dust surfaces', 'Sweep/vacuum floors', 'Mop hard floors',
                'Clean glass and mirrors', 'Wipe countertops', 'Clean sink',
                'Take out trash', 'Clean toilet, sink, and mirror', 'Mop floor',
                'Arrange items neatly', 'Dispose of garbage', 'Light air freshening',
            ],
            'hotel_cleaning' => [
                'Make bed with fresh linens', 'Replace pillowcases and sheets',
                'Dust all surfaces (tables, headboard, shelves)', 'Vacuum carpet / sweep & mop floor',
                'Clean mirrors and glass surfaces', 'Check under bed for trash/items',
                'Empty trash bins and replace liners', 'Clean and disinfect toilet',
                'Scrub shower walls, tub, and floor', 'Clean sink and countertop',
                'Polish fixtures', 'Replace towels, bath mat, tissue, and toiletries',
                'Mop bathroom floor', 'Refill water, coffee, and room amenities',
                'Replace slippers and hygiene kits', 'Check minibar (if applicable)',
                'Ensure lights, AC, TV working', 'Arrange curtains neatly', 'Deodorize room',
            ],
        ];

        $serviceTypeRaw = strtolower($serviceType ?? '');
        $type = 'general_cleaning';

        if (str_contains($serviceTypeRaw, 'daily') || str_contains($serviceTypeRaw, 'routine')) {
            $type = 'daily_cleaning';
        } elseif (str_contains($serviceTypeRaw, 'snowout') || str_contains($serviceTypeRaw, 'weather')) {
            $type = 'snowout_cleaning';
        } elseif (str_contains($serviceTypeRaw, 'deep')) {
            $type = 'deep_cleaning';
        } elseif (str_contains($serviceTypeRaw, 'hotel') || str_contains($serviceTypeRaw, 'room turnover')) {
            $type = 'hotel_cleaning';
        }

        return $checklistTemplates[$type] ?? $checklistTemplates['general_cleaning'];
    }

    /**
     * Approve appointment and automatically assign a team
     */
    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $appointment = ClientAppointment::findOrFail($id);

            if ($appointment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending appointments can be approved.'
                ], 400);
            }

            // Update appointment status to approved
            $appointment->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'client_notified' => false
            ]);

            Log::info('Appointment approved', [
                'appointment_id' => $appointment->id,
                'service_date' => $appointment->service_date,
                'approved_by' => Auth::id()
            ]);

            DB::commit();

            // Notify client that their appointment is approved
            $appointment->load('client.user');
            if ($appointment->client && $appointment->client->user) {
                $this->notificationService->notifyClientAppointmentApproved(
                    $appointment->client->user,
                    $appointment
                );
            }

            // Auto-assign team after approval
            $teamMessage = $this->autoAssignTeam($appointment);

            return response()->json([
                'success' => true,
                'message' => 'Appointment approved! ' . $teamMessage
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to approve appointment', [
                'appointment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Automatically assign a team to an approved appointment
     * Uses existing teams for the date, or runs optimization to create new ones
     */
    protected function autoAssignTeam(ClientAppointment $appointment)
    {
        try {
            // Check for existing teams on this service date
            $availableTeams = $this->getTeamsForDate($appointment->service_date);

            if ($availableTeams && $availableTeams->count() > 0) {
                // Use recommended team if it exists, otherwise pick the first available
                $teamId = $appointment->recommended_team_id;

                if (!$teamId || !$availableTeams->contains(fn($t) => $t['id'] == $teamId)) {
                    $teamId = $availableTeams->first()['id'];
                }

                DB::beginTransaction();

                // Find or create the task
                $task = Task::where('client_id', $appointment->client_id)
                    ->whereDate('scheduled_date', $appointment->service_date)
                    ->where('task_description', 'LIKE', '%' . $appointment->cabin_name . '%')
                    ->first();

                if (!$task) {
                    $task = $this->createTaskFromAppointment($appointment);
                }

                $task->update(['assigned_team_id' => $teamId]);
                $appointment->update(['assigned_team_id' => $teamId]);

                Log::info('Team auto-assigned to appointment', [
                    'appointment_id' => $appointment->id,
                    'team_id' => $teamId,
                    'task_id' => $task->id
                ]);

                DB::commit();

                // Notify client and team members
                $this->notifyTeamAssignment($appointment, $task);

                return 'Team has been automatically assigned.';
            } else {
                // No existing teams - run optimization to create teams
                $task = $this->createTaskFromAppointment($appointment);

                Log::info('Creating teams via optimization for appointment', [
                    'appointment_id' => $appointment->id,
                    'service_date' => $appointment->service_date,
                    'task_id' => $task->id
                ]);

                $result = $this->optimizationService->optimizeSchedule(
                    $appointment->service_date,
                    [],
                    $task->id
                );

                $task = Task::find($task->id);

                if ($task && $task->assigned_team_id) {
                    $appointment->update([
                        'assigned_team_id' => $task->assigned_team_id,
                        'recommended_team_id' => $task->assigned_team_id
                    ]);

                    Log::info('Team created and auto-assigned via optimization', [
                        'appointment_id' => $appointment->id,
                        'team_id' => $task->assigned_team_id,
                        'task_id' => $task->id
                    ]);

                    // Notify client and team members
                    $this->notifyTeamAssignment($appointment, $task);

                    return 'Team has been created and assigned via optimization.';
                } else {
                    Log::warning('Auto-assignment: optimization completed but no team assigned', [
                        'appointment_id' => $appointment->id,
                        'task_id' => $task->id
                    ]);
                    return 'Team could not be automatically assigned. Please assign manually.';
                }
            }
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Auto team assignment failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);

            return 'Team could not be automatically assigned. Please assign manually.';
        }
    }

    /**
     * Notify client and team employees about team assignment
     */
    protected function notifyTeamAssignment(ClientAppointment $appointment, Task $task)
    {
        $appointment->load(['client.user', 'assignedTeam.employees.user']);

        if ($appointment->client && $appointment->client->user && $appointment->assignedTeam) {
            $teamMembers = $appointment->assignedTeam->employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->full_name ?? 'Team Member',
                ];
            })->toArray();

            $this->notificationService->notifyClientAppointmentConfirmed(
                $appointment->client->user,
                $appointment,
                $teamMembers
            );
        }

        if ($appointment->assignedTeam && $task) {
            foreach ($appointment->assignedTeam->employees as $employee) {
                if ($employee->user) {
                    $this->notificationService->notifyEmployeeTaskAssigned(
                        $employee->user,
                        $task,
                        $appointment
                    );
                }
            }
        }
    }

    /**
     * Reject appointment with reason
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $appointment = ClientAppointment::findOrFail($id);

            if ($appointment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending appointments can be rejected.'
                ], 400);
            }

            $appointment->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'client_notified' => false
            ]);

            Log::info('Appointment rejected', [
                'appointment_id' => $appointment->id,
                'rejected_by' => Auth::id(),
                'reason' => $request->rejection_reason
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment rejected successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to reject appointment', [
                'appointment_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign team to appointment
     * If team_id is provided: assign existing team
     * If employee_ids provided: create new team via optimization
     */
    public function assignTeam(Request $request, $id)
    {
        try {
            $appointment = ClientAppointment::findOrFail($id);

            if ($appointment->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved appointments can have teams assigned.'
                ], 400);
            }

            // Check if we're assigning an existing team or creating a new one
            if ($request->has('team_id')) {
                // Scenario 1: Assign existing team (teams already exist for this date)
                DB::beginTransaction();

                $request->validate([
                    'team_id' => 'required|exists:optimization_teams,id'
                ]);

                // Find or create task
                $task = Task::where('client_id', $appointment->client_id)
                    ->whereDate('scheduled_date', $appointment->service_date)
                    ->where('task_description', 'LIKE', '%' . $appointment->cabin_name . '%')
                    ->first();

                if (!$task) {
                    $task = $this->createTaskFromAppointment($appointment);
                }

                // Assign team to task
                $task->update(['assigned_team_id' => $request->team_id]);

                // Update appointment
                $appointment->update([
                    'assigned_team_id' => $request->team_id
                ]);

                Log::info('Existing team assigned to appointment', [
                    'appointment_id' => $appointment->id,
                    'team_id' => $request->team_id,
                    'task_id' => $task->id
                ]);

                DB::commit();
                $message = 'Team assigned successfully!';

            } else {
                // Scenario 2: No teams exist - trigger optimization to create teams
                // Note: Optimization service handles its own transaction

                // Create task from appointment first
                $task = $this->createTaskFromAppointment($appointment);

                Log::info('Creating teams via optimization for appointment', [
                    'appointment_id' => $appointment->id,
                    'service_date' => $appointment->service_date,
                    'task_id' => $task->id
                ]);

                // Trigger optimization (has its own transaction)
                $result = $this->optimizationService->optimizeSchedule(
                    $appointment->service_date,
                    [], // Empty location IDs for client appointments
                    $task->id
                );

                // Reload task from database to get assigned team
                $task = Task::find($task->id);

                if ($task && $task->assigned_team_id) {
                    $appointment->update([
                        'assigned_team_id' => $task->assigned_team_id,
                        'recommended_team_id' => $task->assigned_team_id
                    ]);

                    Log::info('New team created and assigned via optimization', [
                        'appointment_id' => $appointment->id,
                        'team_id' => $task->assigned_team_id,
                        'task_id' => $task->id
                    ]);

                    $message = 'Team created and assigned successfully via optimization!';
                } else {
                    throw new \Exception('Optimization completed but no team was assigned to task');
                }
            }

            // Notify client that team has been assigned and appointment is confirmed
            $appointment->load(['client.user', 'assignedTeam.employees.user']);
            if ($appointment->client && $appointment->client->user && $appointment->assignedTeam) {
                $teamMembers = $appointment->assignedTeam->employees->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->full_name ?? 'Team Member',
                    ];
                })->toArray();

                $this->notificationService->notifyClientAppointmentConfirmed(
                    $appointment->client->user,
                    $appointment,
                    $teamMembers
                );
            }

            // Notify all employees in the assigned team about their new task
            if ($appointment->assignedTeam && $task) {
                foreach ($appointment->assignedTeam->employees as $employee) {
                    if ($employee->user) {
                        $this->notificationService->notifyEmployeeTaskAssigned(
                            $employee->user,
                            $task,
                            $appointment
                        );
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            // Only rollback if we're in a transaction (Scenario 1)
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Failed to assign team', [
                'appointment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign team: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create task from appointment
     */
    protected function createTaskFromAppointment(ClientAppointment $appointment)
    {
        // Calculate estimated duration based on number of units
        $baseDurationPerUnit = 60; // 60 minutes per unit
        $estimatedDuration = $appointment->number_of_units * $baseDurationPerUnit;

        $task = Task::create([
            'client_id' => $appointment->client_id,
            'location_id' => null, // Client appointments don't have location
            'task_description' => $appointment->service_type . ' - ' . $appointment->cabin_name,
            'scheduled_date' => $appointment->service_date,
            'scheduled_time' => $appointment->service_time,
            'duration' => $estimatedDuration,
            'estimated_duration_minutes' => $estimatedDuration,
            'travel_time' => 30, // Default 30 minutes
            'status' => 'Pending',
            'arrival_status' => false,
            'notes' => $appointment->special_requests,
            'assigned_team_id' => null
        ]);

        Log::info('Task created from appointment', [
            'task_id' => $task->id,
            'appointment_id' => $appointment->id
        ]);

        return $task;
    }

    /**
     * Get available teams for a specific date
     */
    protected function getTeamsForDate($date)
    {
        $teams = OptimizationTeam::whereHas('optimizationRun', function($query) use ($date) {
            $query->where('service_date', $date)
                  ->where('is_saved', true);
        })->with(['employees'])->get();

        // Format teams for dropdown
        return $teams->map(function($team) {
            $teamMembers = $team->employees->map(function($emp) {
                return $emp->first_name . ' ' . $emp->last_name .
                       ($emp->has_driving_license ? ' (Driver)' : '');
            })->join(', ');

            return [
                'id' => $team->id,
                'name' => 'Team ' . $team->id . ': ' . $teamMembers,
                'has_driver' => $team->employees->contains(fn($e) => $e->has_driving_license)
            ];
        });
    }
}
