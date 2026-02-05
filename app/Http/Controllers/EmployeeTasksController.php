<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\Attendance;
use Carbon\Carbon;

class EmployeeTasksController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $today = Carbon::today()->toDateString();

        // Get tasks pending employee approval
        $pendingApprovalTasks = Task::whereHas('optimizationTeam.members', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->whereNull('employee_approved')  // Tasks that haven't been approved/declined yet
            ->with(['location', 'optimizationTeam.members.employee.user', 'optimizationTeam.car', 'assignedBy'])
            ->orderBy('scheduled_date', 'desc')
            ->get();

        // Get all approved active tasks (not completed) - "My Tasks for Today"
        $todayTasks = Task::whereHas('optimizationTeam.members', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->where('employee_approved', true)  // Only show approved tasks
            ->whereNotIn('status', ['Completed'])  // Exclude completed tasks
            ->with(['location', 'optimizationTeam.members.employee.user', 'optimizationTeam.car', 'assignedBy'])
            ->orderBy('scheduled_date', 'desc')
            ->orderBy('scheduled_time', 'desc')
            ->get();

        // Get upcoming tasks (only approved tasks)
        $upcomingTasks = Task::where('scheduled_date', '>', $today)
            ->whereHas('optimizationTeam.members', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->where('employee_approved', true)  // Only show approved tasks
            ->with(['location', 'optimizationTeam.members.employee.user', 'optimizationTeam.car'])
            ->orderBy('scheduled_date', 'desc')
            ->limit(10)
            ->get();

        // Get completed tasks (all completed tasks, ordered by most recent first)
        $completedTasks = Task::whereHas('optimizationTeam.members', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->where('employee_approved', true)  // Only show approved tasks
            ->where('status', 'Completed')
            ->with(['location', 'optimizationTeam.members.employee.user', 'optimizationTeam.car', 'assignedBy'])
            ->orderBy('scheduled_date', 'desc')  // Most recent first
            ->get();

        // Check if employee is currently clocked in
        $currentAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', $today)
            ->whereNull('clock_out')
            ->first();

        $isClockedIn = $currentAttendance !== null;
        $clockInTime = $currentAttendance ? Carbon::parse($currentAttendance->clock_in)->format('g:i A') : null;

        return view('employee.tasks', [
            'employee' => $employee,
            'pendingApprovalTasks' => $pendingApprovalTasks,
            'todayTasks' => $todayTasks,
            'upcomingTasks' => $upcomingTasks,
            'completedTasks' => $completedTasks,
            'isClockedIn' => $isClockedIn,
            'clockInTime' => $clockInTime,
        ]);
    }

    public function show(Task $task)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Verify employee has access to this task (security check)
        $hasAccess = $task->optimizationTeam &&
            $task->optimizationTeam->members()
                ->where('employee_id', $employee->id)
                ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this task.');
        }

        // Load task with relationships
        $task->load([
            'location',
            'optimizationTeam.members.employee.user',
            'optimizationTeam.car',
            'startedBy',
            'completedBy'
        ]);

        return view('employee.tasks.show', [
            'employee' => $employee,
            'task' => $task,
        ]);
    }
    public function feedback(Task $task)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Verify employee has access to this task (security check)
        $hasAccess = $task->optimizationTeam &&
            $task->optimizationTeam->members()
                ->where('employee_id', $employee->id)
                ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this task.');
        }

        // Load task with relationships
        $task->load([
            'location',
            'optimizationTeam.members.employee.user',
            'optimizationTeam.car',
            'startedBy',
            'completedBy'
        ]);

        return view('employee.tasks.feedback', [
            'employee' => $employee,
            'task' => $task,
        ]);
    }
    public function storeFeedback(Request $request, Task $task)
{
    $user = Auth::user();
    $employee = $user->employee;

    // Verify employee has access to this task
    $hasAccess = $task->optimizationTeam &&
                 $task->optimizationTeam->members()
                      ->where('employee_id', $employee->id)
                      ->exists();

    if (!$hasAccess) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Validate the request
    $validated = $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'keywords' => 'nullable|array',
        'keywords.*' => 'string',
        'comment' => 'nullable|string|max:1000'
    ]);

    // Store the feedback (adjust based on your database structure)
    // You'll need to create a TaskFeedback model and migration
    $feedback = $task->feedback()->create([
        'employee_id' => $employee->id,
        'rating' => $validated['rating'],
        'keywords' => json_encode($validated['keywords'] ?? []),
        'comment' => $validated['comment'] ?? null,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Feedback submitted successfully'
    ]);
}

    /**
     * Approve a task
     */
    public function approve(Task $task)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Verify employee has access to this task
        $hasAccess = $task->optimizationTeam &&
                     $task->optimizationTeam->members()
                          ->where('employee_id', $employee->id)
                          ->exists();

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'You do not have access to this task.');
        }

        // Update task approval status
        $task->update([
            'employee_approved' => true,
            'employee_approved_at' => Carbon::now(),
        ]);

        return redirect()->route('employee.tasks')->with('success', 'Task approved successfully.');
    }

    /**
     * Decline a task
     */
    public function decline(Task $task)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Verify employee has access to this task
        $hasAccess = $task->optimizationTeam &&
                     $task->optimizationTeam->members()
                          ->where('employee_id', $employee->id)
                          ->exists();

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'You do not have access to this task.');
        }

        // Update task approval status
        $task->update([
            'employee_approved' => false,
            'employee_approved_at' => Carbon::now(),
        ]);

        return redirect()->route('employee.tasks')->with('success', 'Task declined successfully.');
    }

    /**
     * Start a task
     */
    public function start(Task $task)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Verify employee has access to this task
        $hasAccess = $task->optimizationTeam &&
                     $task->optimizationTeam->members()
                          ->where('employee_id', $employee->id)
                          ->exists();

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'You do not have access to this task.');
        }

        // Check if task is approved
        if (!$task->employee_approved) {
            return redirect()->back()->with('error', 'Task must be approved before starting.');
        }

        // Check if task is already started or completed
        if (in_array($task->status, ['In Progress', 'Completed'])) {
            return redirect()->back()->with('error', 'Task is already started or completed.');
        }

        // Update task status to In Progress and record who started it
        $task->update([
            'status' => 'In Progress',
            'started_by' => $user->id,
            'started_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Task started successfully.');
    }

    /**
     * Mark task as complete
     */
    public function complete(Task $task)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Verify employee has access to this task
        $hasAccess = $task->optimizationTeam &&
                     $task->optimizationTeam->members()
                          ->where('employee_id', $employee->id)
                          ->exists();

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'You do not have access to this task.');
        }

        // Check if task is approved
        if (!$task->employee_approved) {
            return redirect()->back()->with('error', 'Task must be approved before completing.');
        }

        // Check if task is already completed
        if ($task->status === 'Completed') {
            return redirect()->back()->with('error', 'Task is already completed.');
        }

        // Update task status to Completed and record who completed it
        $task->update([
            'status' => 'Completed',
            'completed_by' => $user->id,
            'completed_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Task completed successfully.');
    }
}
