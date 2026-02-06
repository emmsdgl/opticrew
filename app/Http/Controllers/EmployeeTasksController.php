<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\TaskChecklistCompletion;
use App\Models\ClientAppointment;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;

class EmployeeTasksController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
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
            'completedBy',
            'checklistCompletions'
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

        // Notify all admins that the employee approved the task
        $this->notificationService->notifyAdminsTaskApproved($task, $user->name);

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

        // Notify all admins that the employee declined the task
        $this->notificationService->notifyAdminsTaskDeclined($task, $user->name);

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

        // Notify client that their service has started
        if ($task->client && $task->client->user) {
            $this->notificationService->notifyClientTaskStarted(
                $task->client->user,
                $task,
                $user->name
            );
        }

        // Notify all admins that the task has started
        $this->notificationService->notifyAdminsTaskStarted($task, $user->name);

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

        // Also update the related ClientAppointment status to 'completed'
        if ($task->client_id && $task->scheduled_date) {
            $relatedAppointment = ClientAppointment::where('client_id', $task->client_id)
                ->whereDate('service_date', $task->scheduled_date)
                ->where(function ($query) use ($task) {
                    // Match by service type from task description
                    $query->where('service_type', 'like', '%' . $task->task_description . '%')
                          ->orWhere(DB::raw('LOWER(service_type)'), 'like', '%' . strtolower($task->task_description) . '%');
                })
                ->whereIn('status', ['pending', 'approved', 'confirmed', 'in progress', 'in_progress'])
                ->first();

            // If no match found by service type, try matching by date only
            if (!$relatedAppointment) {
                $relatedAppointment = ClientAppointment::where('client_id', $task->client_id)
                    ->whereDate('service_date', $task->scheduled_date)
                    ->whereIn('status', ['pending', 'approved', 'confirmed', 'in progress', 'in_progress'])
                    ->first();
            }

            if ($relatedAppointment) {
                $relatedAppointment->update([
                    'status' => 'completed',
                ]);
            }
        }

        // Notify client that their service is completed
        if ($task->client && $task->client->user) {
            $this->notificationService->notifyClientTaskCompleted(
                $task->client->user,
                $task,
                $user->name
            );
        }

        // Notify all admins that the task is completed
        $this->notificationService->notifyAdminsTaskCompleted($task, $user->name);

        return redirect()->back()->with('success', 'Task completed successfully.');
    }

    /**
     * Toggle checklist item completion
     */
    public function toggleChecklistItem(Request $request, Task $task)
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

        // Check if task is approved and started (In Progress)
        if (!$task->employee_approved) {
            return response()->json(['error' => 'Task must be approved first'], 400);
        }

        if ($task->status !== 'In Progress') {
            return response()->json(['error' => 'Task must be started before updating checklist'], 400);
        }

        // Validate the request
        $validated = $request->validate([
            'item_index' => 'required|integer|min:0',
            'item_name' => 'required|string',
            'is_completed' => 'required|boolean'
        ]);

        // Find or create checklist completion record
        $completion = TaskChecklistCompletion::updateOrCreate(
            [
                'task_id' => $task->id,
                'checklist_item_id' => $validated['item_index'], // Using index as ID since we don't have actual checklist_items table populated
            ],
            [
                'is_completed' => $validated['is_completed'],
                'completed_by' => $validated['is_completed'] ? $user->id : null,
                'completed_at' => $validated['is_completed'] ? Carbon::now() : null,
            ]
        );

        // Get all completions for this task
        $completions = TaskChecklistCompletion::where('task_id', $task->id)
            ->where('is_completed', true)
            ->count();

        // Notify at progress milestones (only when completing items, not unchecking)
        if ($validated['is_completed']) {
            $totalItems = $request->input('total_items', 10); // Default to 10 if not provided
            $percentage = $totalItems > 0 ? round(($completions / $totalItems) * 100) : 0;

            // Notify client at 50% milestone
            if ($task->client && $task->client->user && $percentage >= 50 && $percentage < 60) {
                $this->notificationService->notifyClientChecklistProgress(
                    $task->client->user,
                    $task,
                    $completions,
                    $totalItems,
                    $validated['item_name']
                );
            }

            // Notify admins at 50% and 100% milestones
            if (($percentage >= 50 && $percentage < 60) || $percentage === 100) {
                $this->notificationService->notifyAdminsTaskProgress(
                    $task,
                    $user->name,
                    $completions,
                    $totalItems
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => $validated['is_completed'] ? 'Item marked as completed' : 'Item marked as incomplete',
            'completed_count' => $completions
        ]);
    }

    /**
     * Get checklist completion status for a task
     */
    public function getChecklistStatus(Task $task)
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

        // Get all completions for this task
        $completions = TaskChecklistCompletion::where('task_id', $task->id)
            ->get()
            ->keyBy('checklist_item_id')
            ->map(function ($item) {
                return [
                    'is_completed' => $item->is_completed,
                    'completed_at' => $item->completed_at?->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'completions' => $completions
        ]);
    }
}
