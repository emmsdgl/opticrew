<?php

namespace App\Services\Team;

use Illuminate\Support\Collection;

class TeamFormationService
{
    protected const MIN_TEAM_SIZE = 2;
    protected const MAX_TEAM_SIZE = 3;

    /**
     * Form teams from available employees
     * CONSTRAINT: Each team MUST have at least 1 driver
     * Teams are pairs (2) or trios (3) if odd number
     */
    public function formTeams(Collection $employees): Collection
    {
        $teams = collect();
        
        // Separate drivers and non-drivers
        $drivers = $employees->filter(fn($e) => $e->has_driving_license == 1)->values();
        $nonDrivers = $employees->filter(fn($e) => $e->has_driving_license != 1)->values();
        
        \Log::info("Team formation started", [
            'total_employees' => $employees->count(),
            'drivers' => $drivers->count(),
            'non_drivers' => $nonDrivers->count()
        ]);
        
        // ✅ STRATEGY: Pair each driver with a non-driver when possible
        $driverIndex = 0;
        $nonDriverIndex = 0;
        
        while ($driverIndex < $drivers->count()) {
            $team = collect();
            
            // Add driver first
            $team->push($drivers[$driverIndex]);
            $driverIndex++;
            
            // Add non-driver if available
            if ($nonDriverIndex < $nonDrivers->count()) {
                $team->push($nonDrivers[$nonDriverIndex]);
                $nonDriverIndex++;
            }
            // If no non-drivers left, pair with another driver
            elseif ($driverIndex < $drivers->count()) {
                $team->push($drivers[$driverIndex]);
                $driverIndex++;
            }
            
            $teams->push($team);
        }
        
        // Handle remaining non-drivers (shouldn't happen in ideal case)
        // These would need to be added to existing teams as trios
        while ($nonDriverIndex < $nonDrivers->count()) {
            if ($teams->isEmpty()) {
                \Log::error("Cannot form team: No drivers available for remaining non-drivers");
                break;
            }
            
            // Add to smallest team (convert pair to trio)
            $smallestTeam = $teams->sortBy(fn($team) => $team->count())->first();
            
            if ($smallestTeam->count() < self::MAX_TEAM_SIZE) {
                $smallestTeam->push($nonDrivers[$nonDriverIndex]);
                $nonDriverIndex++;
            } else {
                \Log::warning("Cannot add non-driver: All teams at max size");
                break;
            }
        }
        
        // ✅ HANDLE ODD NUMBER: If last team is solo driver, make it a trio
        $lastTeam = $teams->last();
        if ($lastTeam && $lastTeam->count() === 1 && $teams->count() > 1) {
            \Log::info("Handling odd number: Converting solo driver to trio");
            
            // Remove solo driver
            $soloDriver = $teams->pop()->first();
            
            // Add to smallest team
            $smallestTeam = $teams->sortBy(fn($team) => $team->count())->first();
            $smallestTeam->push($soloDriver);
        }
        
        // Log final team composition
        foreach ($teams as $index => $team) {
            $driversInTeam = $team->filter(fn($e) => $e->has_driving_license == 1)->count();
            \Log::info("Team formed", [
                'team_index' => $index,
                'team_size' => $team->count(),
                'drivers' => $driversInTeam,
                'employee_ids' => $team->pluck('id')->toArray()
            ]);
            
            // ✅ VALIDATION: Every team MUST have at least 1 driver
            if ($driversInTeam === 0) {
                \Log::error("INVALID TEAM: No driver!", [
                    'team_index' => $index,
                    'employee_ids' => $team->pluck('id')->toArray()
                ]);
            }
        }
        
        return $teams;
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