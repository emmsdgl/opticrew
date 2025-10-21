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
     *
     * Strategy:
     * - Each driver gets assigned to a team
     * - Add 1-2 non-drivers to complete the team
     * - If no non-drivers available, pair drivers together
     *
     * @param Collection $employees Available employees
     * @return Collection Teams (each team is a Collection of employees)
     */
    public function formTeams(Collection $employees): Collection
    {
        $totalEmployees = $employees->count();

        Log::info("Forming teams", [
            'total_employees' => $totalEmployees
        ]);

        if ($totalEmployees < 2) {
            Log::error("Cannot form teams: need at least 2 employees");
            return collect();
        }

        // Separate drivers and non-drivers
        $drivers = $employees->filter(fn($e) => $e->has_driving_license)->values();
        $nonDrivers = $employees->filter(fn($e) => !$e->has_driving_license)->values();

        Log::info("Employee breakdown", [
            'drivers' => $drivers->count(),
            'non_drivers' => $nonDrivers->count()
        ]);

        // ✅ Calculate optimal team distribution (PAIRS first, TRIOS only if odd)
        $numDrivers = $drivers->count();

        if ($numDrivers === 0) {
            Log::error("Cannot form teams: no drivers available");
            return collect();
        }

        // Maximum teams we can create = number of drivers (each team needs 1 driver)
        $maxTeams = $numDrivers;

        // Calculate how many pairs and trios we need
        // Strategy: ALL PAIRS first, convert last team to TRIO if odd number of employees
        $teamsNeeded = min($maxTeams, intdiv($totalEmployees, 2)); // Max teams possible with pairs

        if ($totalEmployees % 2 === 1) {
            // Odd number: last team will be a trio
            $numPairs = $teamsNeeded - 1;
            $numTrios = 1;
        } else {
            // Even number: all pairs
            $numPairs = $teamsNeeded;
            $numTrios = 0;
        }

        Log::info("Team distribution plan", [
            'total_employees' => $totalEmployees,
            'teams_needed' => $teamsNeeded,
            'pairs' => $numPairs,
            'trios' => $numTrios
        ]);

        $teams = collect();
        $driverIndex = 0;
        $nonDriverIndex = 0;

        // ✅ Create PAIRS first
        for ($i = 0; $i < $numPairs; $i++) {
            $team = collect();

            // Add 1 driver
            if ($driverIndex < $drivers->count()) {
                $team->push($drivers[$driverIndex++]);
            }

            // Add 1 non-driver (or another driver if no non-drivers left)
            if ($nonDriverIndex < $nonDrivers->count()) {
                $team->push($nonDrivers[$nonDriverIndex++]);
            } elseif ($driverIndex < $drivers->count()) {
                $team->push($drivers[$driverIndex++]);
            }

            $teams->push($team);

            Log::info("Pair created", [
                'team_index' => $teams->count(),
                'size' => $team->count(),
                'has_driver' => $team->contains(fn($e) => $e->has_driving_license),
                'member_ids' => $team->pluck('id')->toArray()
            ]);
        }

        // ✅ Create TRIO if needed (for odd number of employees)
        if ($numTrios > 0) {
            $team = collect();

            // Add 1 driver
            if ($driverIndex < $drivers->count()) {
                $team->push($drivers[$driverIndex++]);
            }

            // Add 2 more employees (prefer non-drivers)
            for ($j = 0; $j < 2; $j++) {
                if ($nonDriverIndex < $nonDrivers->count()) {
                    $team->push($nonDrivers[$nonDriverIndex++]);
                } elseif ($driverIndex < $drivers->count()) {
                    $team->push($drivers[$driverIndex++]);
                }
            }

            $teams->push($team);

            Log::info("Trio created", [
                'team_index' => $teams->count(),
                'size' => $team->count(),
                'has_driver' => $team->contains(fn($e) => $e->has_driving_license),
                'member_ids' => $team->pluck('id')->toArray()
            ]);
        }

        // ✅ Validation: Ensure NO team has less than 2 members
        $invalidTeams = $teams->filter(fn($t) => $t->count() < 2);
        if ($invalidTeams->isNotEmpty()) {
            Log::error("CRITICAL: Teams with less than 2 members detected!", [
                'invalid_teams' => $invalidTeams->map(fn($t) => [
                    'size' => $t->count(),
                    'members' => $t->pluck('id')->toArray()
                ])->toArray()
            ]);
        }

        Log::info("Team formation complete", [
            'total_teams' => $teams->count(),
            'team_sizes' => $teams->map(fn($t) => $t->count())->toArray()
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