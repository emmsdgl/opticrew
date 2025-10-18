<?php

namespace App\Services\Optimization\ScenarioManager\Scenarios;

use Illuminate\Support\Collection;

class VehicleBreakdownScenario
{
    /**
     * Handle vehicle breakdown scenario
     */
    public function handle(array $originalSchedule, array $parameters): array
    {
        $unavailableVehicleId = $parameters['vehicle_id'];
        
        // 1. Find teams using the broken vehicle
        $affectedTeams = $this->findTeamsUsingVehicle($originalSchedule, $unavailableVehicleId);
        
        if (empty($affectedTeams)) {
            return [
                'schedule' => $originalSchedule,
                'fitness' => $this->calculateFitness($originalSchedule),
                'is_feasible' => true,
                'affected_teams' => 0,
                'reassignments' => 0,
            ];
        }
        
        // 2. Check if alternative vehicles are available
        $availableVehicles = $this->getAvailableVehicles($parameters);
        
        if (empty($availableVehicles)) {
            // No vehicles available - need to reassign tasks
            return $this->handleNoVehicleAvailable($originalSchedule, $affectedTeams, $parameters);
        }
        
        // 3. Reassign vehicle to affected teams
        $modifiedSchedule = $this->reassignVehicles($originalSchedule, $affectedTeams, $availableVehicles);
        
        return [
            'schedule' => $modifiedSchedule,
            'fitness' => $this->calculateFitness($modifiedSchedule),
            'is_feasible' => true,
            'affected_teams' => count($affectedTeams),
            'reassignments' => 0,
            'action' => 'vehicle_reassigned',
            'new_vehicles' => array_map(fn($v) => $v['id'], $availableVehicles),
        ];
    }

    protected function findTeamsUsingVehicle(array $schedule, int $vehicleId): array
    {
        $affected = [];
        
        foreach ($schedule as $clientId => $clientSchedule) {
            if (is_array($clientSchedule) && isset($clientSchedule['schedule'])) {
                foreach ($clientSchedule['schedule'] as $teamIndex => $teamSchedule) {
                    if (isset($teamSchedule['vehicle_id']) && $teamSchedule['vehicle_id'] === $vehicleId) {
                        $affected[] = [
                            'client_id' => $clientId,
                            'team_index' => $teamIndex,
                            'tasks' => $teamSchedule['tasks'] ?? collect(),
                        ];
                    }
                }
            }
        }
        
        return $affected;
    }

    protected function getAvailableVehicles(array $parameters): array
    {
        return $parameters['available_vehicles'] ?? [];
    }

    protected function reassignVehicles(array $schedule, array $affectedTeams, array $availableVehicles): array
    {
        $modifiedSchedule = $schedule;
        $vehicleIndex = 0;
        
        foreach ($affectedTeams as $affected) {
            $clientId = $affected['client_id'];
            $teamIndex = $affected['team_index'];
            
            // Assign next available vehicle
            if ($vehicleIndex < count($availableVehicles)) {
                $newVehicle = $availableVehicles[$vehicleIndex];
                $modifiedSchedule[$clientId]['schedule'][$teamIndex]['vehicle_id'] = $newVehicle['id'];
                $vehicleIndex++;
            }
        }
        
        return $modifiedSchedule;
    }

    protected function handleNoVehicleAvailable(array $schedule, array $affectedTeams, array $parameters): array
    {
        // Redistribute tasks from affected teams to other teams
        $modifiedSchedule = $schedule;
        $orphanedTasks = collect();
        
        foreach ($affectedTeams as $affected) {
            $orphanedTasks = $orphanedTasks->merge($affected['tasks']);
            
            // Clear tasks from affected team
            $clientId = $affected['client_id'];
            $teamIndex = $affected['team_index'];
            $modifiedSchedule[$clientId]['schedule'][$teamIndex]['tasks'] = collect();
        }
        
        // Redistribute tasks to teams with vehicles
        foreach ($orphanedTasks as $task) {
            $targetTeam = $this->findTeamWithVehicle($modifiedSchedule);
            if ($targetTeam) {
                $modifiedSchedule[$targetTeam['client_id']]['schedule'][$targetTeam['team_index']]['tasks']->push($task);
            }
        }
        
        return [
            'schedule' => $modifiedSchedule,
            'fitness' => $this->calculateFitness($modifiedSchedule),
            'is_feasible' => true,
            'affected_teams' => count($affectedTeams),
            'reassignments' => $orphanedTasks->count(),
            'action' => 'tasks_redistributed',
        ];
    }

    protected function findTeamWithVehicle(array $schedule): ?array
    {
        foreach ($schedule as $clientId => $clientSchedule) {
            if (is_array($clientSchedule) && isset($clientSchedule['schedule'])) {
                foreach ($clientSchedule['schedule'] as $teamIndex => $teamSchedule) {
                    if (isset($teamSchedule['vehicle_id']) && $teamSchedule['vehicle_id'] > 0) {
                        return [
                            'client_id' => $clientId,
                            'team_index' => $teamIndex,
                        ];
                    }
                }
            }
        }
        return null;
    }

    protected function calculateFitness(array $schedule): float
    {
        $totalFitness = 0;
        $count = 0;
        
        foreach ($schedule as $clientSchedule) {
            if (is_object($clientSchedule) && method_exists($clientSchedule, 'getFitness')) {
                $totalFitness += $clientSchedule->getFitness() ?? 0;
                $count++;
            }
        }
        
        return $count > 0 ? $totalFitness / $count : 0;
    }
}