<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ClientAppointment;
use App\Models\Task;
use App\Models\OptimizationTeam;
use App\Services\Optimization\OptimizationService;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;

class AdminAppointmentController extends Controller
{
    protected $optimizationService;
    protected $notificationService;

    public function __construct(OptimizationService $optimizationService, NotificationService $notificationService)
    {
        $this->optimizationService = $optimizationService;
        $this->notificationService = $notificationService;
    }

    /**
     * List all appointments with counts. Supports ?status=pending|approved|rejected filter.
     */
    public function index(Request $request)
    {
        try {
            $query = ClientAppointment::with(['client.user', 'assignedTeam', 'recommendedTeam'])
                ->where('is_company_inquiry', false)
                ->orderBy('created_at', 'desc');

            if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
                $query->where('status', $request->status);
            }

            $appointments = $query->paginate(15);

            $counts = [
                'pending' => ClientAppointment::where('is_company_inquiry', false)->where('status', 'pending')->count(),
                'approved' => ClientAppointment::where('is_company_inquiry', false)->where('status', 'approved')->count(),
                'rejected' => ClientAppointment::where('is_company_inquiry', false)->where('status', 'rejected')->count(),
                'total' => ClientAppointment::where('is_company_inquiry', false)->count(),
            ];

            $formattedAppointments = $appointments->getCollection()->map(function ($appointment) {
                $client = $appointment->client;
                return [
                    'id' => $appointment->id,
                    'status' => $appointment->status,
                    'service_type' => $appointment->service_type,
                    'service_date' => $appointment->service_date ? Carbon::parse($appointment->service_date)->format('M d, Y') : null,
                    'service_time' => $appointment->formatted_service_time,
                    'cabin_name' => $appointment->cabin_name,
                    'number_of_units' => $appointment->number_of_units,
                    'total_amount' => $appointment->total_amount ? number_format($appointment->total_amount, 2) : '0.00',
                    'created_at' => $appointment->created_at ? $appointment->created_at->format('M d, Y H:i') : null,
                    'client' => $client ? [
                        'name' => trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')),
                        'email' => $client->email ?? ($client->user->email ?? ''),
                        'phone' => $client->phone_number ?? ($client->user->phone ?? ''),
                    ] : null,
                    'has_assigned_team' => !is_null($appointment->assigned_team_id),
                ];
            });

            return response()->json([
                'success' => true,
                'appointments' => $formattedAppointments,
                'counts' => $counts,
                'pagination' => [
                    'current_page' => $appointments->currentPage(),
                    'last_page' => $appointments->lastPage(),
                    'per_page' => $appointments->perPage(),
                    'total' => $appointments->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('API: Failed to fetch appointments', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch appointments.',
            ], 500);
        }
    }

    /**
     * Get single appointment with full details.
     */
    public function show($id)
    {
        try {
            $appointment = ClientAppointment::with([
                'client.user',
                'assignedTeam.members.employee.user',
                'recommendedTeam.members.employee.user',
                'approvedBy',
                'rejectedBy',
            ])->findOrFail($id);

            $availableTeams = null;
            if (in_array($appointment->status, ['pending', 'approved'])) {
                $availableTeams = $this->getTeamsForDate($appointment->service_date);
            }

            return response()->json([
                'success' => true,
                'appointment' => $this->formatAppointmentDetail($appointment, $availableTeams),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('API: Failed to fetch appointment detail', [
                'appointment_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch appointment details.',
            ], 500);
        }
    }

    /**
     * Approve a pending appointment and auto-assign team.
     */
    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $appointment = ClientAppointment::findOrFail($id);

            if ($appointment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending appointments can be approved.',
                ], 400);
            }

            $appointment->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'client_notified' => false,
            ]);

            Log::info('API: Appointment approved', [
                'appointment_id' => $appointment->id,
                'service_date' => $appointment->service_date,
                'approved_by' => Auth::id(),
            ]);

            DB::commit();

            // Notify client
            $appointment->load('client.user');
            if ($appointment->client && $appointment->client->user) {
                $this->notificationService->notifyClientAppointmentApproved(
                    $appointment->client->user,
                    $appointment
                );
            }

            // Auto-assign team
            $teamMessage = $this->autoAssignTeam($appointment);

            return response()->json([
                'success' => true,
                'message' => 'Appointment approved! ' . $teamMessage,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('API: Failed to approve appointment', [
                'appointment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve appointment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject a pending appointment with a required reason.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $appointment = ClientAppointment::findOrFail($id);

            if ($appointment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending appointments can be rejected.',
                ], 400);
            }

            $appointment->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'client_notified' => false,
            ]);

            Log::info('API: Appointment rejected', [
                'appointment_id' => $appointment->id,
                'rejected_by' => Auth::id(),
                'reason' => $request->rejection_reason,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment rejected successfully.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found.',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('API: Failed to reject appointment', [
                'appointment_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject appointment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format appointment detail for JSON response.
     */
    protected function formatAppointmentDetail(ClientAppointment $appointment, $availableTeams)
    {
        $client = $appointment->client;

        return [
            'id' => $appointment->id,
            'status' => $appointment->status,
            'is_company_inquiry' => (bool) $appointment->is_company_inquiry,
            'booking_type' => $appointment->booking_type ?? 'personal',
            'client' => $client ? [
                'first_name' => $client->first_name ?? '',
                'last_name' => $client->last_name ?? '',
                'company_name' => $client->company_name ?? '',
                'email' => $client->email ?? ($client->user->email ?? ''),
                'phone' => $client->phone_number ?? ($client->user->phone ?? ''),
                'address' => $client->address ?? '',
            ] : null,
            'service' => [
                'service_type' => $appointment->service_type,
                'number_of_units' => $appointment->number_of_units,
                'service_date' => $appointment->service_date ? Carbon::parse($appointment->service_date)->format('Y-m-d') : null,
                'service_date_display' => $appointment->service_date ? Carbon::parse($appointment->service_date)->format('l, F d, Y') : '',
                'service_time' => $appointment->formatted_service_time,
                'is_sunday' => (bool) $appointment->is_sunday,
                'is_holiday' => (bool) $appointment->is_holiday,
                'cabin_name' => $appointment->cabin_name ?? '',
                'unit_size' => $appointment->unit_size ?? '',
                'unit_details' => $appointment->unit_details ?? [],
                'special_requests' => $appointment->special_requests ?? [],
                'other_concerns' => $appointment->other_concerns ?? '',
            ],
            'pricing' => [
                'subtotal' => $appointment->quotation ? number_format($appointment->quotation, 2) : '0.00',
                'vat' => $appointment->vat_amount ? number_format($appointment->vat_amount, 2) : '0.00',
                'total' => $appointment->total_amount ? number_format($appointment->total_amount, 2) : '0.00',
            ],
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
    }

    /**
     * Auto-assign a team to an approved appointment.
     * Reuses the same logic as the web controller.
     */
    protected function autoAssignTeam(ClientAppointment $appointment)
    {
        try {
            $availableTeams = $this->getTeamsForDate($appointment->service_date);

            if ($availableTeams && $availableTeams->count() > 0) {
                $teamId = $appointment->recommended_team_id;
                $estimatedDuration = $appointment->number_of_units * 60;

                // Check recommended team for overlap
                if ($teamId) {
                    $overlap = $this->checkTeamOverlap($teamId, $appointment->service_date, $appointment->service_time, $estimatedDuration);
                    if ($overlap) {
                        $teamId = null;
                    }
                }

                // Find first non-conflicting team
                if (!$teamId || !$availableTeams->contains(fn($t) => $t['id'] == $teamId)) {
                    $teamId = null;
                    foreach ($availableTeams as $team) {
                        $overlap = $this->checkTeamOverlap($team['id'], $appointment->service_date, $appointment->service_time, $estimatedDuration);
                        if (!$overlap) {
                            $teamId = $team['id'];
                            break;
                        }
                    }
                }

                if (!$teamId) {
                    Log::warning('API: Auto-assignment: all teams have overlapping bookings', [
                        'appointment_id' => $appointment->id,
                        'service_date' => $appointment->service_date,
                    ]);
                    return 'All existing teams have overlapping bookings. Please assign manually or adjust the schedule.';
                }

                DB::beginTransaction();

                $task = Task::where('client_id', $appointment->client_id)
                    ->whereDate('scheduled_date', $appointment->service_date)
                    ->where('task_description', 'LIKE', '%' . $appointment->cabin_name . '%')
                    ->first();

                if (!$task) {
                    $task = $this->createTaskFromAppointment($appointment);
                }

                $task->update(['assigned_team_id' => $teamId]);
                $appointment->update(['assigned_team_id' => $teamId]);

                Log::info('API: Team auto-assigned to appointment', [
                    'appointment_id' => $appointment->id,
                    'team_id' => $teamId,
                    'task_id' => $task->id,
                ]);

                DB::commit();

                $this->notifyTeamAssignment($appointment, $task);

                return 'Team has been automatically assigned.';
            } else {
                // No existing teams - run optimization
                $task = $this->createTaskFromAppointment($appointment);

                Log::info('API: Creating teams via optimization for appointment', [
                    'appointment_id' => $appointment->id,
                    'service_date' => $appointment->service_date,
                    'task_id' => $task->id,
                ]);

                $this->optimizationService->optimizeSchedule(
                    $appointment->service_date,
                    [],
                    $task->id
                );

                $task = Task::find($task->id);

                if ($task && $task->assigned_team_id) {
                    $appointment->update([
                        'assigned_team_id' => $task->assigned_team_id,
                        'recommended_team_id' => $task->assigned_team_id,
                    ]);

                    Log::info('API: Team created and auto-assigned via optimization', [
                        'appointment_id' => $appointment->id,
                        'team_id' => $task->assigned_team_id,
                        'task_id' => $task->id,
                    ]);

                    $this->notifyTeamAssignment($appointment, $task);

                    return 'Team has been created and assigned via optimization.';
                } else {
                    Log::warning('API: Auto-assignment: optimization completed but no team assigned', [
                        'appointment_id' => $appointment->id,
                        'task_id' => $task->id ?? null,
                    ]);
                    return 'Team could not be automatically assigned. Please assign manually.';
                }
            }
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('API: Auto team assignment failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);

            return 'Team could not be automatically assigned. Please assign manually.';
        }
    }

    /**
     * Notify client and team employees about team assignment.
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
     * Check for overlapping bookings when assigning a team (Scenario #6).
     */
    protected function checkTeamOverlap(int $teamId, $serviceDate, $serviceTime, ?int $estimatedDurationMinutes = null): ?array
    {
        $existingTasks = Task::where('assigned_team_id', $teamId)
            ->whereDate('scheduled_date', $serviceDate)
            ->whereNotIn('status', ['Cancelled'])
            ->get();

        if ($existingTasks->isEmpty()) {
            return null;
        }

        $newTaskStart = Carbon::parse($serviceDate)->setTimeFromTimeString(
            Carbon::parse($serviceTime)->format('H:i:s')
        );

        foreach ($existingTasks as $task) {
            if (!$task->scheduled_time) continue;

            $taskStart = Carbon::parse($task->scheduled_date)->setTimeFromTimeString(
                Carbon::parse($task->scheduled_time)->format('H:i:s')
            );
            $taskDuration = $task->estimated_duration_minutes ?? $task->duration ?? 60;
            $travelTime = $task->travel_time ?? 30;
            $taskEnd = $taskStart->copy()->addMinutes($taskDuration + $travelTime);

            if ($newTaskStart->lt($taskEnd) && $newTaskStart->gte($taskStart)) {
                return [
                    'conflict' => true,
                    'existing_task_id' => $task->id,
                    'existing_task_description' => $task->task_description,
                    'existing_task_end' => $taskEnd->format('g:i A'),
                    'new_task_start' => $newTaskStart->format('g:i A'),
                ];
            }

            $newDuration = $estimatedDurationMinutes ?? 60;
            $newTaskEnd = $newTaskStart->copy()->addMinutes($newDuration + 30);
            if ($taskStart->lt($newTaskEnd) && $taskStart->gte($newTaskStart)) {
                return [
                    'conflict' => true,
                    'existing_task_id' => $task->id,
                    'existing_task_description' => $task->task_description,
                    'existing_task_start' => $taskStart->format('g:i A'),
                    'new_task_end' => $newTaskEnd->format('g:i A'),
                ];
            }
        }

        return null;
    }

    /**
     * Create a task from an appointment.
     */
    protected function createTaskFromAppointment(ClientAppointment $appointment)
    {
        $baseDurationPerUnit = 60;
        $estimatedDuration = $appointment->number_of_units * $baseDurationPerUnit;

        $task = Task::create([
            'client_id' => $appointment->client_id,
            'location_id' => null,
            'task_description' => $appointment->service_type . ' - ' . $appointment->cabin_name,
            'scheduled_date' => $appointment->service_date,
            'scheduled_time' => $appointment->service_time,
            'duration' => $estimatedDuration,
            'estimated_duration_minutes' => $estimatedDuration,
            'travel_time' => 30,
            'status' => 'Pending',
            'arrival_status' => false,
            'notes' => is_array($appointment->special_requests) ? implode(', ', $appointment->special_requests) : $appointment->special_requests,
            'assigned_team_id' => null,
        ]);

        Log::info('API: Task created from appointment', [
            'task_id' => $task->id,
            'appointment_id' => $appointment->id,
        ]);

        return $task;
    }

    /**
     * Get available teams for a specific date.
     */
    protected function getTeamsForDate($date)
    {
        $teams = OptimizationTeam::whereHas('optimizationRun', function ($query) use ($date) {
            $query->where('service_date', $date)
                  ->where('is_saved', true);
        })->with(['employees'])->get();

        return $teams->map(function ($team) use ($date) {
            $teamMembers = $team->employees->map(function ($emp) {
                return $emp->first_name . ' ' . $emp->last_name .
                       ($emp->has_driving_license ? ' (Driver)' : '');
            })->join(', ');

            $activeTaskCount = Task::where('assigned_team_id', $team->id)
                ->whereDate('scheduled_date', $date)
                ->whereNotIn('status', ['Cancelled'])
                ->count();

            return [
                'id' => $team->id,
                'name' => 'Team ' . $team->id . ': ' . $teamMembers,
                'has_driver' => $team->employees->contains(fn($e) => $e->has_driving_license),
                'active_tasks' => $activeTaskCount,
            ];
        });
    }
}
