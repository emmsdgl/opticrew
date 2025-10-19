<?php

namespace App\Services\Optimization\GeneticAlgorithm;

use App\Services\Team\TeamFormationService;
use App\Services\Team\TeamEfficiencyCalculator;
use Illuminate\Support\Collection;

class GeneticAlgorithmOptimizer
{
    protected const POPULATION_SIZE = 20;
    protected const PATIENCE = 15; // Generations without improvement

    protected TeamFormationService $teamFormation;
    protected TeamEfficiencyCalculator $efficiencyCalculator;
    protected FitnessCalculator $fitnessCalculator;
    protected CrossoverOperator $crossover;
    protected MutationOperator $mutation;

    public function __construct(
        TeamFormationService $teamFormation,
        TeamEfficiencyCalculator $efficiencyCalculator,
        FitnessCalculator $fitnessCalculator,
        CrossoverOperator $crossover,
        MutationOperator $mutation
    ) {
        $this->teamFormation = $teamFormation;
        $this->efficiencyCalculator = $efficiencyCalculator;
        $this->fitnessCalculator = $fitnessCalculator;
        $this->crossover = $crossover;
        $this->mutation = $mutation;
    }

    public function optimize(
        Collection $validTasks,
        array $employeeAllocations,
        int $maxGenerations = 100
    ): array {
        $allOptimalSchedules = [];
    
        foreach ($employeeAllocations as $clientId => $employees) {
            \Log::info("Processing client", [
                'client_id' => $clientId,
                'employee_count' => count($employees)
            ]);
    
            // 1. Form teams
            $teams = $this->teamFormation->formTeams(collect($employees));
            $teamEfficiencies = $this->efficiencyCalculator->calculate($teams);
    
            // 2. Get tasks for this client
            // ✅ FIX: Handle 'unassigned' client grouping
            $clientTasks = $validTasks->filter(function($task) use ($clientId) {
                // Handle 'contracted_X' format
                if (str_starts_with($clientId, 'contracted_')) {
                    $contractedClientId = str_replace('contracted_', '', $clientId);
                    return $task->location 
                        && $task->location->contracted_client_id == $contractedClientId;
                }
                
                // Handle 'client_X' format (external clients)
                if (str_starts_with($clientId, 'client_')) {
                    $externalClientId = str_replace('client_', '', $clientId);
                    return $task->client_id == $externalClientId;
                }
                
                // Handle 'unassigned'
                if ($clientId === 'unassigned') {
                    return $task->client_id === null 
                        && (!$task->location || !$task->location->contracted_client_id);
                }
                
                return false;
            });
    
            \Log::info("Tasks for client", [
                'client_id' => $clientId,
                'task_count' => $clientTasks->count(),
                'task_ids' => $clientTasks->pluck('id')->toArray()
            ]);
    
            // ✅ SAFETY CHECK
            if ($clientTasks->isEmpty()) {
                \Log::warning("No tasks found for client", ['client_id' => $clientId]);
                continue;
            }
    
            if ($teams->isEmpty()) {
                \Log::warning("No teams formed for client", ['client_id' => $clientId]);
                continue;
            }
    
            // 3. Generate greedy seed schedule
            $greedySchedule = $this->generateGreedySchedule($teams, $clientTasks, $teamEfficiencies);
    
            // 4. Initialize population
            $population = new Population(self::POPULATION_SIZE);
            $population->addIndividual($greedySchedule); // Seed with greedy solution
    
            // Fill rest with random schedules
            for ($i = 1; $i < self::POPULATION_SIZE; $i++) {
                $randomSchedule = $this->generateRandomSchedule($teams, $clientTasks);
                $population->addIndividual($randomSchedule);
            }
    
            // 5. Evolutionary loop
            $bestFitness = 0;
            $generationsWithoutImprovement = 0;
            $generation = 0; // ✅ Initialize outside loop

            for ($generation = 1; $generation <= $maxGenerations; $generation++) {
                // Evaluate fitness
                $population->evaluateFitness($this->fitnessCalculator, $teamEfficiencies);
    
                // Check for improvement
                $currentBest = $population->getBest();
                if ($currentBest->getFitness() > $bestFitness) {
                    $bestFitness = $currentBest->getFitness();
                    $generationsWithoutImprovement = 0;
                } else {
                    $generationsWithoutImprovement++;
                }
    
                // Early stopping
                if ($generationsWithoutImprovement >= self::PATIENCE) {
                    break;
                }
    
                // Create new population
                $newPopulation = new Population(self::POPULATION_SIZE);
                
                // Elitism: preserve best
                $newPopulation->addIndividual($currentBest);
    
                // Generate offspring
                while ($newPopulation->size() < self::POPULATION_SIZE) {
                    $parentA = $this->tournamentSelection($population);
                    $parentB = $this->tournamentSelection($population);
    
                    $child = $this->crossover->crossover($parentA, $parentB);
    
                    // Mutation
                    if (rand(0, 100) / 100 < 0.1) { // 10% mutation rate
                        $child = $this->mutation->mutate($child);
                    }
    
                    $newPopulation->addIndividual($child);
                }
    
                $population = $newPopulation;
            }
    
            $bestSchedule = $population->getBest();

            // ✅ Store generation count in the Individual
            $bestSchedule->setMetadata([
                'generations_run' => $generation,
                'final_fitness' => $bestSchedule->getFitness()
            ]);
            
            \Log::info("Optimization complete for client", [
                'client_id' => $clientId,
                'final_fitness' => $bestSchedule->getFitness(),
                'generations' => $generation
            ]);
    
            $allOptimalSchedules[$clientId] = $bestSchedule;
        }
    
        return $allOptimalSchedules;
    }

    protected function tournamentSelection(Population $population, int $tournamentSize = 5): Individual
    {
        $tournament = $population->getRandomIndividuals($tournamentSize);
        return $tournament->sortByDesc(fn($ind) => $ind->getFitness())->first();
    }

    protected function generateGreedySchedule(Collection $teams, Collection $tasks, array $efficiencies): Individual
    {
        $schedule = [];
        foreach ($teams as $index => $team) {
            $schedule[$index] = ['team' => $team, 'tasks' => collect()];
        }

        // Sort tasks by duration (longest first)
        $sortedTasks = $tasks->sortByDesc('duration');

        foreach ($sortedTasks as $task) {
            // Assign to team with least workload
            $minWorkload = PHP_INT_MAX;
            $selectedTeam = 0;

            foreach ($schedule as $index => $teamSchedule) {
                $workload = $this->calculateTeamWorkload($teamSchedule['tasks'], $efficiencies[$index]);
                if ($workload < $minWorkload) {
                    $minWorkload = $workload;
                    $selectedTeam = $index;
                }
            }

            $schedule[$selectedTeam]['tasks']->push($task);
        }

        return new Individual($schedule);
    }

    protected function generateRandomSchedule(Collection $teams, Collection $tasks): Individual
    {
        $schedule = [];
        foreach ($teams as $index => $team) {
            $schedule[$index] = ['team' => $team, 'tasks' => collect()];
        }

        foreach ($tasks as $task) {
            $randomTeam = rand(0, count($teams) - 1);
            $schedule[$randomTeam]['tasks']->push($task);
        }

        return new Individual($schedule);
    }

    protected function calculateTeamWorkload(Collection $tasks, float $efficiency): float
    {
        return $tasks->sum(fn($task) => $task->duration / $efficiency);
    }
}