<?php

namespace App\Services\Optimization\PreProcessing;

use Illuminate\Support\Collection;

class EmployeeFilter
{
    public function filter(Collection $employees): Collection
    {
        // Return all active employees, sorted with drivers first
        return $employees->sortByDesc(function($employee) {
            return $employee->has_driving_license ? 1000 : $employee->efficiency;
        });
    }

    protected function hasRequiredSkills($employee): bool
    {
        // Example: Check if employee has driving license
        return $employee->has_driving_license === true || $employee->has_driving_license === 1;
    }
}