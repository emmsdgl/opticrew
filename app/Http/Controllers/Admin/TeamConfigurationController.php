<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OptimizationTeam;
use App\Models\OptimizationTeamMember;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TeamConfigurationController extends Controller
{
    /**
     * Fetch teams for a given date (AJAX).
     */
    public function getTeams(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $date = Carbon::parse($request->input('date'))->toDateString();

        $teams = OptimizationTeam::where('service_date', $date)
            ->with(['members.employee.user', 'tasks.location', 'car'])
            ->orderBy('team_index')
            ->get()
            ->map(function ($team) {
                return [
                    'id' => $team->id,
                    'team_index' => $team->team_index,
                    'team_name' => $team->team_name,
                    'car' => $team->car ? [
                        'id' => $team->car->id,
                        'name' => $team->car->name ?? $team->car->plate_number ?? 'Car #' . $team->car->id,
                    ] : null,
                    'members' => $team->members->map(function ($member) {
                        $employee = $member->employee;
                        $user = $employee ? $employee->user : null;
                        $profilePic = null;
                        if ($user && $user->profile_picture) {
                            $profilePic = str_starts_with($user->profile_picture, 'profile_pictures/')
                                ? asset('storage/' . $user->profile_picture)
                                : asset($user->profile_picture);
                        }
                        return [
                            'member_id' => $member->id,
                            'employee_id' => $employee ? $employee->id : null,
                            'name' => $employee ? $employee->full_name : 'Unknown',
                            'has_driving_license' => $employee ? $employee->has_driving_license : false,
                            'profile_picture' => $profilePic,
                        ];
                    }),
                    'tasks' => $team->tasks->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'description' => $task->task_description,
                            'status' => $task->status,
                            'location' => $task->location ? $task->location->address : null,
                        ];
                    }),
                ];
            });

        return response()->json(['teams' => $teams]);
    }

    /**
     * Get all active employees (for the replacement dropdown).
     */
    public function getEmployees()
    {
        $employees = Employee::where('is_active', true)
            ->with('user')
            ->get()
            ->map(function ($employee) {
                $user = $employee->user;
                $profilePic = null;
                if ($user && $user->profile_picture) {
                    $profilePic = str_starts_with($user->profile_picture, 'profile_pictures/')
                        ? asset('storage/' . $user->profile_picture)
                        : asset($user->profile_picture);
                }
                return [
                    'employee_id' => $employee->id,
                    'name' => $employee->full_name,
                    'has_driving_license' => $employee->has_driving_license,
                    'profile_picture' => $profilePic,
                ];
            })
            ->sortBy('name')
            ->values();

        return response()->json(['employees' => $employees]);
    }

    /**
     * Replace a team member with another employee.
     * Only updates the optimization_team_members pivot — does NOT touch
     * task_checklist_completions, started_by, completed_by, etc.
     */
    public function replaceMember(Request $request)
    {
        $request->validate([
            'member_id' => 'required|integer|exists:optimization_team_members,id',
            'new_employee_id' => 'required|integer|exists:employees,id',
        ]);

        $member = OptimizationTeamMember::findOrFail($request->input('member_id'));
        $newEmployeeId = $request->input('new_employee_id');

        // Check if the new employee is already in this team
        $alreadyInTeam = OptimizationTeamMember::where('optimization_team_id', $member->optimization_team_id)
            ->where('employee_id', $newEmployeeId)
            ->where('id', '!=', $member->id)
            ->exists();

        if ($alreadyInTeam) {
            return response()->json([
                'success' => false,
                'message' => 'This employee is already a member of this team.',
            ], 422);
        }

        $oldEmployeeId = $member->employee_id;

        // Only update the pivot record — preserves all task-level data
        $member->employee_id = $newEmployeeId;
        $member->save();

        $newEmployee = Employee::with('user')->find($newEmployeeId);

        Log::info('Team member replaced by admin', [
            'team_id' => $member->optimization_team_id,
            'old_employee_id' => $oldEmployeeId,
            'new_employee_id' => $newEmployeeId,
            'replaced_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Team member replaced successfully.',
            'member' => [
                'member_id' => $member->id,
                'employee_id' => $newEmployee->id,
                'name' => $newEmployee->full_name,
                'has_driving_license' => $newEmployee->has_driving_license,
                'profile_picture' => $newEmployee->user && $newEmployee->user->profile_picture
                    ? (str_starts_with($newEmployee->user->profile_picture, 'profile_pictures/')
                        ? asset('storage/' . $newEmployee->user->profile_picture)
                        : asset($newEmployee->user->profile_picture))
                    : null,
            ],
        ]);
    }
}
