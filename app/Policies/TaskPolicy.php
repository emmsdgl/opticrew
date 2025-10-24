<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given task can be viewed by the user.
     */
    public function view(User $user, Task $task): bool
    {
        // Admins can view all tasks
        if ($user->role === 'admin') {
            return true;
        }

        // Employees can view tasks assigned to their team
        if ($user->role === 'employee' && $user->employee) {
            // Check if the employee is part of the team assigned to this task
            if ($task->assigned_team_id) {
                $teamMemberIds = $task->optimizationTeam?->members->pluck('employee_id')->toArray() ?? [];
                return in_array($user->employee->id, $teamMemberIds);
            }
        }

        return false;
    }

    /**
     * Determine if the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        // Admins and employees can view tasks
        return in_array($user->role, ['admin', 'employee']);
    }

    /**
     * Determine if the user can create tasks.
     */
    public function create(User $user): bool
    {
        // Only admins can create tasks
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        // Admins can update all tasks
        if ($user->role === 'admin') {
            return true;
        }

        // Employees can only update status fields of tasks assigned to them
        if ($user->role === 'employee' && $user->employee) {
            if ($task->assigned_team_id) {
                $teamMemberIds = $task->optimizationTeam?->members->pluck('employee_id')->toArray() ?? [];
                return in_array($user->employee->id, $teamMemberIds);
            }
        }

        return false;
    }

    /**
     * Determine if the user can update task status specifically.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }

    /**
     * Determine if the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        // Only admins can delete tasks
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can restore the task.
     */
    public function restore(User $user, Task $task): bool
    {
        // Only admins can restore tasks
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can permanently delete the task.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        // Only admins can force delete tasks
        return $user->role === 'admin';
    }
}
