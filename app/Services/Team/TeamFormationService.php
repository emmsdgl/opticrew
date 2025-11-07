<?php

namespace App\Services\Team;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TeamFormationService
{
    protected const MIN_TEAM_SIZE = 2;
    protected const MAX_TEAM_SIZE = 3;

    /**
     * Form teams from available employees
     *
     * ✅ RULE 2: Each team MUST have at least 1 driver + 1-2 others (team size: 2-3)
     * ✅ RULE 5: Maximize utilization - create optimal number of teams based on task count
     *
     * Strategy (per FINAL PSEUDOCODE.txt lines 762-826):
     * - Limit teams to ensure all employees are utilized (Rule 5)
     * - If taskCount provided: create min(drivers, taskCount) teams
     * - Otherwise: create teams based on drivers available
     * - Randomly assign 2-3 members per team
     * - Add non-drivers first, then drivers if no non-drivers left
     *
     * @param Collection $employees Available employees
     * @param int|null $taskCount Number of tasks (used to limit teams for Rule 5)
     * @return Collection Teams (each team is a Collection of employees)
     */
    public function formTeams(Collection $employees, ?int $taskCount = null): Collection
    {
        $totalEmployees = $employees->count();

        Log::info("Forming teams", [
            'total_employees' => $totalEmployees,
            'task_count' => $taskCount
        ]);

        if ($totalEmployees < 2) {
            Log::error("Cannot form teams: need at least 2 employees");
            return collect();
        }

        // Separate drivers and non-drivers
        $drivers = $employees->filter(fn($e) => $e->has_driving_license)->shuffle()->values();
        $nonDrivers = $employees->filter(fn($e) => !$e->has_driving_license)->shuffle()->values();

        Log::info("Employee breakdown", [
            'drivers' => $drivers->count(),
            'non_drivers' => $nonDrivers->count()
        ]);

        if ($drivers->isEmpty()) {
            Log::error("Cannot form teams: no drivers available");
            return collect();
        }

        // ✅ RULE 2 & 5: Calculate optimal number of teams
        // Strategy: Balance between employee utilization and task coverage
        // Consider: employees available, tasks to do, team size constraints (2-3)

        // Maximum teams we can create with available employees (each team needs min 2 people)
        $maxTeamsByEmployees = intdiv($totalEmployees, self::MIN_TEAM_SIZE);

        // Maximum teams we can create with available drivers (each team needs 1 driver)
        $maxTeamsByDrivers = $drivers->count();

        // Start with driver constraint
        $maxTeams = min($maxTeamsByDrivers, $maxTeamsByEmployees);

        if ($taskCount !== null && $taskCount > 0) {
            // ✅ Key insight: Balance between task coverage and employee availability
            // Don't create more teams than we have employees for
            // Don't create more teams than we have tasks for
            $maxTeams = min($maxTeams, $taskCount);

            Log::info("Team count calculation", [
                'total_employees' => $totalEmployees,
                'total_drivers' => $drivers->count(),
                'total_tasks' => $taskCount,
                'max_teams_by_employees' => $maxTeamsByEmployees,
                'max_teams_by_drivers' => $maxTeamsByDrivers,
                'teams_to_create' => $maxTeams
            ]);
        } else {
            Log::info("Team count calculation (no task count provided)", [
                'total_employees' => $totalEmployees,
                'total_drivers' => $drivers->count(),
                'max_teams_by_employees' => $maxTeamsByEmployees,
                'teams_to_create' => $maxTeams
            ]);
        }

        $teams = collect();
        $nonDriverIndex = 0;

        // Track which drivers and non-drivers have been used
        $usedEmployeeIds = collect();

        // ✅ RULE 5: Calculate team sizes to use ALL employees
        // If we have odd employees with even teams, we need at least one trio
        $employeesRemaining = $totalEmployees;
        $teamsRemaining = $maxTeams;
        $teamSizes = [];

        for ($i = 0; $i < $maxTeams; $i++) {
            // Calculate optimal size for this team
            $avgSize = $employeesRemaining / $teamsRemaining;

            // If average is closer to 3, make it a trio; otherwise a pair
            if ($avgSize >= 2.5 || $employeesRemaining % 2 === 1) {
                $teamSizes[] = 3;
                $employeesRemaining -= 3;
            } else {
                $teamSizes[] = 2;
                $employeesRemaining -= 2;
            }
            $teamsRemaining--;
        }

        Log::info("Calculated team sizes", [
            'total_employees' => $totalEmployees,
            'total_teams' => $maxTeams,
            'team_sizes' => $teamSizes,
            'employees_used' => array_sum($teamSizes)
        ]);

        // ✅ RULE 2 & 5: Create teams with calculated sizes
        $driverIndex = 0;
        for ($teamIndex = 0; $teamIndex < $maxTeams; $teamIndex++) {
            // Find next unused driver
            $driver = null;
            while ($driverIndex < $drivers->count()) {
                $candidateDriver = $drivers[$driverIndex];
                $driverIndex++;

                if (!$usedEmployeeIds->contains($candidateDriver->id)) {
                    $driver = $candidateDriver;
                    break;
                }
            }

            if ($driver === null) {
                Log::warning("No more unused drivers available for team", [
                    'team_index' => $teamIndex + 1,
                    'max_teams' => $maxTeams
                ]);
                break;
            }

            $team = collect([$driver]); // Start with driver
            $usedEmployeeIds->push($driver->id);

            Log::info("Starting team formation", [
                'team_index' => $teamIndex + 1,
                'driver_id' => $driver->id,
                'used_ids_before' => $usedEmployeeIds->toArray()
            ]);

            // ✅ Use pre-calculated team size (not random!)
            $teamSize = $teamSizes[$teamIndex];

            // Add non-drivers to complete the team
            while ($team->count() < $teamSize && $nonDriverIndex < $nonDrivers->count()) {
                $nonDriver = $nonDrivers[$nonDriverIndex];

                if ($usedEmployeeIds->contains($nonDriver->id)) {
                    Log::warning("⚠️ Non-driver already used - skipping", [
                        'employee_id' => $nonDriver->id,
                        'team_index' => $teamIndex + 1
                    ]);
                    $nonDriverIndex++;
                    continue;
                }

                $team->push($nonDriver);
                $usedEmployeeIds->push($nonDriver->id);
                $nonDriverIndex++;
            }

            // If ran out of non-drivers, try to add another unused driver
            if ($team->count() < $teamSize) {
                foreach ($drivers as $otherDriver) {
                    if ($team->count() >= $teamSize) break;

                    // Skip if this driver is already used in ANY team
                    if (!$usedEmployeeIds->contains($otherDriver->id)) {
                        Log::info("Adding extra driver to team", [
                            'team_index' => $teamIndex + 1,
                            'driver_id' => $otherDriver->id
                        ]);
                        $team->push($otherDriver);
                        $usedEmployeeIds->push($otherDriver->id);
                    }
                }
            }

            Log::info("Team formation complete", [
                'team_index' => $teamIndex + 1,
                'final_size' => $team->count(),
                'target_size' => $teamSize,
                'member_ids' => $team->pluck('id')->toArray(),
                'used_ids_after' => $usedEmployeeIds->toArray()
            ]);

            $teams->push($team);

            Log::info("Team created", [
                'team_index' => $teams->count(),
                'size' => $team->count(),
                'target_size' => $teamSize,
                'has_driver' => $team->contains(fn($e) => $e->has_driving_license),
                'member_ids' => $team->pluck('id')->toArray()
            ]);
        }

        // ✅ Handle remaining non-drivers (edge case - distribute to existing teams)
        while ($nonDriverIndex < $nonDrivers->count()) {
            // Find smallest team that has less than 3 members
            $smallestTeam = $teams->filter(fn($t) => $t->count() < 3)
                                  ->sortBy(fn($t) => $t->count())
                                  ->first();

            if ($smallestTeam) {
                $smallestTeam->push($nonDrivers[$nonDriverIndex]);
                Log::info("Added remaining non-driver to team", [
                    'employee_id' => $nonDrivers[$nonDriverIndex]->id,
                    'team_size_after' => $smallestTeam->count()
                ]);
            } else {
                // All teams are at max size (3), log warning
                Log::warning("Cannot add non-driver: All teams at max size", [
                    'employee_id' => $nonDrivers[$nonDriverIndex]->id
                ]);
            }
            $nonDriverIndex++;
        }

        // ✅ CRITICAL FIX: Ensure NO team has less than 2 members
        // If any team has only 1 member, merge it with the smallest team
        $teamsToRemove = collect();
        foreach ($teams as $index => $team) {
            if ($team->count() < 2) {
                Log::warning("Team has only 1 member - will merge with another team", [
                    'team_index' => $index,
                    'member_id' => $team->first()->id
                ]);

                // Find smallest team (that is not this team)
                $targetTeam = $teams->filter(fn($t, $i) => $i !== $index && $t->count() < 3)
                                    ->sortBy(fn($t) => $t->count())
                                    ->first();

                if ($targetTeam) {
                    // Merge into target team
                    foreach ($team as $member) {
                        $targetTeam->push($member);
                    }
                    Log::info("Merged solo team into another team", [
                        'target_team_size' => $targetTeam->count()
                    ]);
                } else {
                    // No suitable team found, force merge into smallest available team
                    $targetTeam = $teams->filter(fn($t, $i) => $i !== $index)
                                        ->sortBy(fn($t) => $t->count())
                                        ->first();
                    if ($targetTeam) {
                        foreach ($team as $member) {
                            $targetTeam->push($member);
                        }
                        Log::info("Force merged solo team (may exceed size 3)", [
                            'target_team_size' => $targetTeam->count()
                        ]);
                    }
                }

                // Mark this team for removal
                $teamsToRemove->push($index);
            }
        }

        // Remove merged teams
        $teams = $teams->filter(fn($team, $index) => !$teamsToRemove->contains($index))->values();

        // ✅ Final Validation: Ensure NO team has less than 2 members
        $invalidTeams = $teams->filter(fn($t) => $t->count() < 2);
        if ($invalidTeams->isNotEmpty()) {
            Log::error("CRITICAL: Teams with less than 2 members still exist after merging!", [
                'invalid_teams' => $invalidTeams->map(fn($t) => [
                    'size' => $t->count(),
                    'members' => $t->pluck('id')->toArray()
                ])->toArray()
            ]);
        }

        Log::info("Team formation complete", [
            'total_teams' => $teams->count(),
            'team_sizes' => $teams->map(fn($t) => $t->count())->toArray(),
            'average_team_size' => round($teams->avg(fn($t) => $t->count()), 2)
        ]);

        return $teams;
    }


    //     // ✅ STRATEGY: Pair each driver with a non-driver when possible
    //     $driverIndex = 0;
    //     $nonDriverIndex = 0;
        
    //     while ($driverIndex < $drivers->count()) {
    //         $team = collect();
            
    //         // Add driver first
    //         $team->push($drivers[$driverIndex]);
    //         $driverIndex++;
            
    //         // Add non-driver if available
    //         if ($nonDriverIndex < $nonDrivers->count()) {
    //             $team->push($nonDrivers[$nonDriverIndex]);
    //             $nonDriverIndex++;
    //         }
    //         // If no non-drivers left, pair with another driver
    //         elseif ($driverIndex < $drivers->count()) {
    //             $team->push($drivers[$driverIndex]);
    //             $driverIndex++;
    //         }
            
    //         $teams->push($team);
    //     }
        
    //     // Handle remaining non-drivers (shouldn't happen in ideal case)
    //     // These would need to be added to existing teams as trios
    //     while ($nonDriverIndex < $nonDrivers->count()) {
    //         if ($teams->isEmpty()) {
    //             \Log::error("Cannot form team: No drivers available for remaining non-drivers");
    //             break;
    //         }
            
    //         // Add to smallest team (convert pair to trio)
    //         $smallestTeam = $teams->sortBy(fn($team) => $team->count())->first();
            
    //         if ($smallestTeam->count() < self::MAX_TEAM_SIZE) {
    //             $smallestTeam->push($nonDrivers[$nonDriverIndex]);
    //             $nonDriverIndex++;
    //         } else {
    //             \Log::warning("Cannot add non-driver: All teams at max size");
    //             break;
    //         }
    //     }
        
    //     // ✅ HANDLE ODD NUMBER: If last team is solo driver, make it a trio
    //     $lastTeam = $teams->last();
    //     if ($lastTeam && $lastTeam->count() === 1 && $teams->count() > 1) {
    //         \Log::info("Handling odd number: Converting solo driver to trio");
            
    //         // Remove solo driver
    //         $soloDriver = $teams->pop()->first();
            
    //         // Add to smallest team
    //         $smallestTeam = $teams->sortBy(fn($team) => $team->count())->first();
    //         $smallestTeam->push($soloDriver);
    //     }
        
    //     // Log final team composition
    //     foreach ($teams as $index => $team) {
    //         $driversInTeam = $team->filter(fn($e) => $e->has_driving_license == 1)->count();
    //         \Log::info("Team formed", [
    //             'team_index' => $index,
    //             'team_size' => $team->count(),
    //             'drivers' => $driversInTeam,
    //             'employee_ids' => $team->pluck('id')->toArray()
    //         ]);
            
    //         // ✅ VALIDATION: Every team MUST have at least 1 driver
    //         if ($driversInTeam === 0) {
    //             \Log::error("INVALID TEAM: No driver!", [
    //                 'team_index' => $index,
    //                 'employee_ids' => $team->pluck('id')->toArray()
    //             ]);
    //         }
    //     }
        
    //     return $teams;
    // }

    /**
     * ✅ Re-form teams excluding locked teams (for real-time additions)
     */
    public function formTeamsExcludingLocked(
        Collection $allEmployees,
        Collection $lockedTeamEmployees
    ): Collection {
        // Get available (unlocked) employees
        $availableEmployees = $allEmployees->filter(function($employee) use ($lockedTeamEmployees) {
            return !$lockedTeamEmployees->contains('id', $employee->id);
        });

        Log::info("Forming teams with locked exclusions", [
            'total_employees' => $allEmployees->count(),
            'locked_employees' => $lockedTeamEmployees->count(),
            'available_employees' => $availableEmployees->count()
        ]);

        return $this->formTeams($availableEmployees);
    }




    /**
     * Calculate optimal team size based on available employees
     */
    protected function calculateOptimalTeamSize(int $availableCount): int
    {
        // Prefer pairs (2), use trio (3) only if needed
        if ($availableCount % 2 === 0) {
            return self::MIN_TEAM_SIZE; // Pairs
        }
        
        // Odd number: last team should be trio
        return self::MIN_TEAM_SIZE;
    }

    /**
     * Validate if team meets driver requirement
     */
    public function validateTeam(Collection $team): bool
    {
        $driverCount = $team->filter(fn($e) => $e->has_driving_license == 1)->count();
        
        return $driverCount >= 1 && 
               $team->count() >= self::MIN_TEAM_SIZE && 
               $team->count() <= self::MAX_TEAM_SIZE;
    }

    /**
     * Validate if a team can handle specific task requirements
     */
    public function canHandleTask(Collection $team, $task): bool
    {
        // First check: Must have a driver
        if (!$this->validateTeam($team)) {
            return false;
        }
        
        // Check if team has all required skills
        $requiredSkills = $task->required_skills ?? [];
        if (empty($requiredSkills)) {
            return true;
        }
        
        $teamSkills = $team->flatMap(fn($member) => $member->skills ?? [])->unique();
        
        foreach ($requiredSkills as $skill) {
            if (!$teamSkills->contains($skill)) {
                return false;
            }
        }
        
        return true;
    }
}