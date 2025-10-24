<?php

namespace App\Policies;

use App\Models\OptimizationRun;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OptimizationRunPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any optimization runs.
     */
    public function viewAny(User $user): bool
    {
        // Only admins can view optimization runs
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can view the optimization run.
     */
    public function view(User $user, OptimizationRun $optimizationRun): bool
    {
        // Only admins can view optimization runs
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can create optimization runs.
     */
    public function create(User $user): bool
    {
        // Only admins can create optimization runs
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can update the optimization run.
     */
    public function update(User $user, OptimizationRun $optimizationRun): bool
    {
        // Only admins can update optimization runs
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can save (finalize) the optimization run.
     */
    public function save(User $user, OptimizationRun $optimizationRun): bool
    {
        // Only admins can save optimization runs
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can re-optimize.
     */
    public function reOptimize(User $user, OptimizationRun $optimizationRun): bool
    {
        // Only admins can re-optimize
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can delete the optimization run.
     */
    public function delete(User $user, OptimizationRun $optimizationRun): bool
    {
        // Only admins can delete optimization runs
        return $user->role === 'admin';
    }
}
