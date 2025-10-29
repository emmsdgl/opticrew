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
use Carbon\Carbon;

class AppointmentController extends Controller
{
    protected $optimizationService;

    public function __construct(OptimizationService $optimizationService)
    {
        $this->optimizationService = $optimizationService;
    }

    /**
     * Display list of all appointments
     */
    public function index()
    {
        $appointments = ClientAppointment::with(['client', 'assignedTeam', 'recommendedTeam'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Display appointment details
     */
    public function show($id)
    {
        $appointment = ClientAppointment::with([
            'client',
            'assignedTeam.employees',
            'recommendedTeam.employees',
            'approvedBy',
            'rejectedBy'
        ])->findOrFail($id);

        // Get available teams for the appointment date if status is pending or approved
        $availableTeams = null;
        if (in_array($appointment->status, ['pending', 'approved'])) {
            $availableTeams = $this->getTeamsForDate($appointment->service_date);
        }

        return view('admin.appointments.show', compact('appointment', 'availableTeams'));
    }

    /**
     * Approve appointment (without triggering optimization yet)
     * Optimization happens when admin confirms team assignment
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

            Log::info('Appointment approved - awaiting team assignment', [
                'appointment_id' => $appointment->id,
                'service_date' => $appointment->service_date,
                'approved_by' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment approved! Please assign a team to complete the process.'
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
