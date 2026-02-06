<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // === ATTENDANCE DATA ===
        $totalEmployees = Employee::count();
        $presentEmployees = Attendance::whereDate('clock_in', Carbon::today())
            ->distinct('employee_id')
            ->count('employee_id');
        $absentEmployees = $totalEmployees - $presentEmployees;
        $attendanceRate = ($totalEmployees > 0) ? ($presentEmployees / $totalEmployees) * 100 : 0;

        // === RECENT ARRIVALS DATA ===
        $recentArrivals = Attendance::with('employee.user')
            ->whereDate('clock_in', Carbon::today())
            ->whereNotNull('clock_in')
            ->orderBy('clock_in', 'desc')
            ->take(4)
            ->get();
            
        // === TASK DATA ===
        $tasksFromDb = Task::with(['location.contractedClient', 'client.user', 'optimizationTeam.members.employee.user', 'checklistCompletions'])
            ->whereBetween('scheduled_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->orderBy('scheduled_date', 'desc')
            ->get();

        $tasks = $tasksFromDb->map(function ($task, $index) {
            $clientName = 'N/A';

            if ($task->location && $task->location->contractedClient) {
                $clientName = $task->location->contractedClient->name;
            } elseif ($task->client) {
                $clientName = $task->client->first_name . ' ' . $task->client->last_name;
            }

            // Get team members
            $teamMembers = [];
            if ($task->optimizationTeam) {
                $teamMembers = $task->optimizationTeam->members()
                    ->with('employee.user')
                    ->get()
                    ->map(function($member) {
                        return [
                            'name' => $member->employee && $member->employee->user ? $member->employee->user->name : 'Unknown',
                            'picture' => $member->employee && $member->employee->user ? $member->employee->user->profile_picture : null
                        ];
                    })
                    ->toArray();
            }

            // Get checklist completions
            $checklistCompletions = $task->checklistCompletions
                ->where('is_completed', true)
                ->pluck('checklist_item_id')
                ->toArray();

            // Calculate due time as started_at + estimated_duration_minutes
            $dueTime = null;
            if ($task->started_at && $task->estimated_duration_minutes) {
                $dueTime = Carbon::parse($task->started_at)->addMinutes($task->estimated_duration_minutes)->format('g:i A');
            } elseif ($task->scheduled_time) {
                $dueTime = $task->scheduled_time;
            }

            return [
                'id' => $index,
                'service' => $task->task_description,
                'status' => $task->status,
                'description' => $clientName,
                'service_date' => $task->scheduled_date ? Carbon::parse($task->scheduled_date)->format('M d, Y') : null,
                'service_time' => $dueTime,
                'action_onclick' => "openTaskModal({$index})",
                'action_label' => 'View Details',
                // Store full task details for modal
                'modal_data' => [
                    'task_id' => $task->id,
                    'client' => $clientName,
                    'service_type' => $task->task_description,
                    'service_date' => $task->scheduled_date ? Carbon::parse($task->scheduled_date)->format('M d, Y') : 'N/A',
                    'service_time' => $dueTime ?? 'N/A',
                    'estimated_duration' => $task->estimated_duration_minutes ? $task->estimated_duration_minutes . ' minutes' : 'N/A',
                    'start_date' => $task->started_at ? Carbon::parse($task->started_at)->format('M d, Y g:i A') : 'N/A',
                    'end_date' => $task->completed_at ? Carbon::parse($task->completed_at)->format('M d, Y g:i A') : 'N/A',
                    'team_name' => $task->optimizationTeam ? $task->optimizationTeam->team_name : 'N/A',
                    'team_members' => $teamMembers,
                    'status' => $task->status,
                    'checklist_completions' => $checklistCompletions
                ]
            ];
        })->values()->toArray();

        $taskCount = count($tasks);
    
        // === ADMIN DATA ===
        $admin = Auth::user()->employee;

        // === HOLIDAYS DATA ===
        $holidays = Holiday::all()->map(function ($holiday) {
            return [
                'date' => $holiday->date->format('Y-m-d'),
                'name' => $holiday->name,
            ];
        });

        // === PASS ALL DATA TO THE VIEW ===
        return view('admin.dashboard', [
            'totalEmployees' => $totalEmployees,
            'presentEmployees' => $presentEmployees,
            'absentEmployees' => $absentEmployees,
            'attendanceRate' => number_format($attendanceRate, 2),
            'tasks' => $tasks,
            'taskCount' => $taskCount,
            'admin' => $admin,
            'recentArrivals' => $recentArrivals,
            'holidays' => $holidays // Pass holidays to the view
        ]);
    }
}