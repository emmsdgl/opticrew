<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any employees.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all employees
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can view the employee.
     */
    public function view(User $user, Employee $employee): bool
    {
        // Admins can view all employees
        if ($user->role === 'admin') {
            return true;
        }

        // Employees can view their own profile
        if ($user->role === 'employee' && $user->employee) {
            return $user->employee->id === $employee->id;
        }

        return false;
    }

    /**
     * Determine if the user can create employees.
     */
    public function create(User $user): bool
    {
        // Only admins can create employees
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can update the employee.
     */
    public function update(User $user, Employee $employee): bool
    {
        // Only admins can update employee records
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can delete the employee.
     */
    public function delete(User $user, Employee $employee): bool
    {
        // Only admins can delete employees
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can restore the employee.
     */
    public function restore(User $user, Employee $employee): bool
    {
        // Only admins can restore employees
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can permanently delete the employee.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        // Only admins can force delete employees
        return $user->role === 'admin';
    }
}
