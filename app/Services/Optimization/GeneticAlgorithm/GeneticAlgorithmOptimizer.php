<?php

namespace App\Services\Optimization\GeneticAlgorithm;
use App\Services\Team\TeamFormationService;
use App\Services\Team\TeamEfficiencyCalculator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GeneticAlgorithmOptimizer
{
    // Read GA parameters from config (aligned with simulation model)
    protected int $populationSize;
    protected int $patience;

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

        // Load GA parameters from config
        $this->populationSize = config('optimization.genetic_algorithm.population_size', 50);
        $this->patience = config('optimization.genetic_algorithm.patience', 15);
    }

    public function optimize(
        Collection $validTasks,
        array $employeeAllocations,
        int $maxGenerations = 100,
        ?Collection $lockedTeams = null // ✅ RULE 9: Pass locked teams
    ): array {
        $allOptimalSchedules = [];
    
        foreach ($employeeAllocations as $clientId => $employees) {
            Log::info("Processing client", [
                'client_id' => $clientId,
                'employee_count' => count($employees)
            ]);
    
            // ✅ RULE 9: Filter out locked team members if provided
            $availableEmployees = collect($employees);
            if ($lockedTeams && $lockedTeams->isNotEmpty()) {
                $lockedEmployeeIds = $lockedTeams->flatten(1)->pluck('id')->unique();
                $availableEmployees = $availableEmployees->filter(function($emp) use ($lockedEmployeeIds) {
                    return !$lockedEmployeeIds->contains($emp->id);
                });

                Log::info("Filtered locked employees", [
                    'original_count' => count($employees),
                    'available_count' => $availableEmployees->count(),
                    'locked_count' => $lockedEmployeeIds->count()
                ]);
            }

            // 2. Get tasks for this client FIRST (needed to calculate team count)
            $clientTasks = $validTasks->filter(function($task) use ($clientId) {
                if (str_starts_with($clientId, 'contracted_')) {
                    $contractedClientId = str_replace('contracted_', '', $clientId);
                    return $task->location 
                        && $task->location->contracted_client_id == $contractedClientId;
                }
                
                if (str_starts_with($clientId, 'client_')) {
                    $externalClientId = str_replace('client_', '', $clientId);
                    return $task->client_id == $externalClientId;
                }
                
                if ($clientId === 'unassigned') {
                    return $task->client_id === null 
                        && (!$task->location || !$task->location->contracted_client_id);
                }
                
                return false;
            });

            Log::info("Tasks for client", [
                'client_id' => $clientId,
                'task_count' => $clientTasks->count(),
                'task_ids' => $clientTasks->pluck('id')->toArray()
            ]);

            if ($clientTasks->isEmpty()) {
                Log::warning("No tasks found for client", ['client_id' => $clientId]);
                continue;
            }

            // 1. Form teams (after getting tasks, so we can limit team count)
            // ✅ RULE 5: Pass task count to limit teams and ensure all employees utilized
            $teams = $this->teamFormation->formTeams($availableEmployees, $clientTasks->count());

            if ($teams->isEmpty()) {
                Log::warning("No teams formed for client", ['client_id' => $clientId]);
                continue;
            }

            $teamEfficiencies = $this->efficiencyCalculator->calculate($teams);

            // ✅ CRITICAL: Store fixed teams - they should NOT change during evolution
            $fixedTeams = $teams;

            // ✅ RULE 6 & 7: Use FAIR greedy schedule (even distribution + time limits)
            $greedySchedule = $this->generateFairGreedySchedule(
                $fixedTeams,
                $clientTasks,
                $teamEfficiencies
            );

            // 3. Initialize population with BALANCED schedules
            $population = new Population($this->populationSize);
            $population->addIndividual($greedySchedule);

            // ✅ FIX: Create MORE balanced schedules instead of random ones
            // Generate 70% balanced + 30% random for diversity
            $balancedCount = (int) ($this->populationSize * 0.7);
            $randomCount = $this->populationSize - $balancedCount;

            // Create variations of the greedy schedule (balanced)
            for ($i = 1; $i < $balancedCount; $i++) {
                $balancedSchedule = $this->generateFairGreedySchedule($fixedTeams, $clientTasks, $teamEfficiencies);
                // Add slight randomness for diversity
                if (rand(0, 100) < 30) {
                    $balancedSchedule = $this->mutation->mutate($balancedSchedule);
                }
                // ✅ Restore original teams (mutation might have corrupted them)
                $balancedSchedule = $this->ensureTeamsAreFixed($balancedSchedule, $fixedTeams);
                $population->addIndividual($balancedSchedule);
            }

            // Add some random schedules for diversity
            for ($i = 0; $i < $randomCount; $i++) {
                $randomSchedule = $this->generateRandomSchedule($fixedTeams, $clientTasks);
                // ✅ Restore original teams
                $randomSchedule = $this->ensureTeamsAreFixed($randomSchedule, $fixedTeams);
                $population->addIndividual($randomSchedule);
            }

            // 4. Evolutionary loop
            $bestFitness = 0;
            $generationsWithoutImprovement = 0;
            $generation = 0;

            for ($generation = 1; $generation <= $maxGenerations; $generation++) {
                $population->evaluateFitness($this->fitnessCalculator, $teamEfficiencies, $clientTasks->count());

                $currentBest = $population->getBest();
                if ($currentBest->getFitness() > $bestFitness) {
                    $bestFitness = $currentBest->getFitness();
                    $generationsWithoutImprovement = 0;
                } else {
                    $generationsWithoutImprovement++;
                }

                if ($generationsWithoutImprovement >= $this->patience) {
                    break;
                }

                $newPopulation = new Population($this->populationSize);

                // ✅ ELITISM: Preserve best solution from previous generation
                $newPopulation->addIndividual($currentBest);

                while ($newPopulation->size() < $this->populationSize) {
                    $parentA = $this->tournamentSelection($population);
                    $parentB = $this->tournamentSelection($population);

                    $child = $this->crossover->crossover($parentA, $parentB);

                    if (rand(0, 100) / 100 < 0.1) {
                        $child = $this->mutation->mutate($child);
                    }

                    // ✅ CRITICAL: Restore original teams after crossover/mutation
                    $child = $this->ensureTeamsAreFixed($child, $fixedTeams);

                    $newPopulation->addIndividual($child);
                }

                $population = $newPopulation;
            }

            $bestSchedule = $population->getBest();

            $bestSchedule->setMetadata([
                'generations_run' => $generation,
                'final_fitness' => $bestSchedule->getFitness()
            ]);
            
            Log::info("Optimization complete for client", [
                'client_id' => $clientId,
                'final_fitness' => $bestSchedule->getFitness(),
                'generations' => $generation
            ]);

            $allOptimalSchedules[$clientId] = $bestSchedule;
        }

        return $allOptimalSchedules;
    }

    //         // 1. Form teams
    //         $teams = $this->teamFormation->formTeams(collect($employees));
    //         $teamEfficiencies = $this->efficiencyCalculator->calculate($teams);
    
    //         // 2. Get tasks for this client
    //         // ✅ FIX: Handle 'unassigned' client grouping
    //         $clientTasks = $validTasks->filter(function($task) use ($clientId) {
    //             // Handle 'contracted_X' format
    //             if (str_starts_with($clientId, 'contracted_')) {
    //                 $contractedClientId = str_replace('contracted_', '', $clientId);
    //                 return $task->location 
    //                     && $task->location->contracted_client_id == $contractedClientId;
    //             }
                
    //             // Handle 'client_X' format (external clients)
    //             if (str_starts_with($clientId, 'client_')) {
    //                 $externalClientId = str_replace('client_', '', $clientId);
    //                 return $task->client_id == $externalClientId;
    //             }
                
    //             // Handle 'unassigned'
    //             if ($clientId === 'unassigned') {
    //                 return $task->client_id === null 
    //                     && (!$task->location || !$task->location->contracted_client_id);
    //             }
                
    //             return false;
    //         });
    
    //         \Log::info("Tasks for client", [
    //             'client_id' => $clientId,
    //             'task_count' => $clientTasks->count(),
    //             'task_ids' => $clientTasks->pluck('id')->toArray()
    //         ]);
    
    //         // ✅ SAFETY CHECK
    //         if ($clientTasks->isEmpty()) {
    //             \Log::warning("No tasks found for client", ['client_id' => $clientId]);
    //             continue;
    //         }
    
    //         if ($teams->isEmpty()) {
    //             \Log::warning("No teams formed for client", ['client_id' => $clientId]);
    //             continue;
    //         }
    
    //         // 3. Generate greedy seed schedule
    //         $greedySchedule = $this->generateGreedySchedule($teams, $clientTasks, $teamEfficiencies);
    
    //         // 4. Initialize population
    //         $population = new Population($this->populationSize);
    //         $population->addIndividual($greedySchedule); // Seed with greedy solution
    
    //         // Fill rest with random schedules
    //         for ($i = 1; $i < $this->populationSize; $i++) {
    //             $randomSchedule = $this->generateRandomSchedule($teams, $clientTasks);
    //             $population->addIndividual($randomSchedule);
    //         }
    
    //         // 5. Evolutionary loop
    //         $bestFitness = 0;
    //         $generationsWithoutImprovement = 0;
    //         $generation = 0; // ✅ Initialize outside loop

    //         for ($generation = 1; $generation <= $maxGenerations; $generation++) {
    //             // Evaluate fitness
    //             $population->evaluateFitness($this->fitnessCalculator, $teamEfficiencies);
    
    //             // Check for improvement
    //             $currentBest = $population->getBest();
    //             if ($currentBest->getFitness() > $bestFitness) {
    //                 $bestFitness = $currentBest->getFitness();
    //                 $generationsWithoutImprovement = 0;
    //             } else {
    //                 $generationsWithoutImprovement++;
    //             }
    
    //             // Early stopping
    //             if ($generationsWithoutImprovement >= $this->patience) {
    //                 break;
    //             }
    
    //             // Create new population
    //             $newPopulation = new Population($this->populationSize);
                
    //             // Elitism: preserve best
    //             $newPopulation->addIndividual($currentBest);
    
    //             // Generate offspring
    //             while ($newPopulation->size() < $this->populationSize) {
    //                 $parentA = $this->tournamentSelection($population);
    //                 $parentB = $this->tournamentSelection($population);
    
    //                 $child = $this->crossover->crossover($parentA, $parentB);
    
    //                 // Mutation
    //                 if (rand(0, 100) / 100 < 0.1) { // 10% mutation rate
    //                     $child = $this->mutation->mutate($child);
    //                 }
    
    //                 $newPopulation->addIndividual($child);
    //             }
    
    //             $population = $newPopulation;
    //         }
    
    //         $bestSchedule = $population->getBest();

    //         // ✅ Store generation count in the Individual
    //         $bestSchedule->setMetadata([
    //             'generations_run' => $generation,
    //             'final_fitness' => $bestSchedule->getFitness()
    //         ]);
            
    //         \Log::info("Optimization complete for client", [
    //             'client_id' => $clientId,
    //             'final_fitness' => $bestSchedule->getFitness(),
    //             'generations' => $generation
    //         ]);
    
    //         $allOptimalSchedules[$clientId] = $bestSchedule;
    //     }
    
    //     return $allOptimalSchedules;
    // }

    protected function tournamentSelection(Population $population, int $tournamentSize = 5): Individual
    {
        $tournament = $population->getRandomIndividuals($tournamentSize);
        return $tournament->sortByDesc(fn($ind) => $ind->getFitness())->first();
    }

    /**
     * ✅ RULE 5, 6, 7: Generate Fair Greedy Schedule
     * - RULE 5: Max utilization (ensures all teams get tasks)
     * - RULE 6: Fair distribution (assigns to least loaded team)
     * - RULE 7: 12-hour limit (respects maximum working hours)
     */
    protected function generateFairGreedySchedule(Collection $teams, Collection $tasks, array $efficiencies): Individual
    {
        $schedule = [];

        foreach ($teams as $index => $team) {
            $schedule[$index] = [
                'team' => $team, 
                'tasks' => collect(),
                'total_hours' => 0 // ✅ Track working hours
            ];
        }
    
        // ✅ RULE 3: Tasks already sorted by arrival_status in preprocessor
        $sortedTasks = $tasks->sortByDesc('arrival_status')->values();
    
        foreach ($sortedTasks as $task) {
            // ✅ RULE 6: Find team with LEAST tasks (primary) and LEAST workload (secondary)
            $selectedTeam = null;
            $minTaskCount = PHP_INT_MAX;
            $minWorkload = PHP_INT_MAX;

            foreach ($schedule as $index => $teamSchedule) {
                $taskDurationHours = ($task->duration + $task->travel_time) / 60;
                $projectedHours = $teamSchedule['total_hours'] + $taskDurationHours;

                // ✅ RULE 7: Skip if exceeds 12 hours
                if ($projectedHours > 12) {
                    continue;
                }

                $currentTaskCount = $teamSchedule['tasks']->count();
                $workload = $this->calculateTeamWorkload(
                    $teamSchedule['tasks'],
                    $efficiencies[$index]
                );

                // ✅ Primary: Prefer team with fewer tasks (ensures even distribution)
                // ✅ Secondary: Among teams with same task count, prefer lower workload
                if ($currentTaskCount < $minTaskCount ||
                    ($currentTaskCount === $minTaskCount && $workload < $minWorkload)) {
                    $minTaskCount = $currentTaskCount;
                    $minWorkload = $workload;
                    $selectedTeam = $index;
                }
            }
    
            // If no team can take it (all at 12 hours), assign to least loaded anyway
            if ($selectedTeam === null) {
                Log::warning("All teams at 12-hour limit, assigning to least loaded", [
                    'task_id' => $task->id
                ]);
                $selectedTeam = collect($schedule)
                    ->sortBy(fn($ts) => $ts['total_hours'])
                    ->keys()
                    ->first();
            }
    
            $schedule[$selectedTeam]['tasks']->push($task);
            $schedule[$selectedTeam]['total_hours'] += ($task->duration + $task->travel_time) / 60;

            Log::info("Task assigned by fair greedy", [
                'task_id' => $task->id,
                'arrival_status' => $task->arrival_status,
                'selected_team' => $selectedTeam + 1,
                'team_task_count_after' => $schedule[$selectedTeam]['tasks']->count(),
                'all_teams_task_counts' => collect($schedule)->map(fn($ts) => $ts['tasks']->count())->toArray()
            ]);
        }
    
        // ✅ RULE 5: Log if any team has 0 tasks
        foreach ($schedule as $index => $teamSchedule) {
            if ($teamSchedule['tasks']->isEmpty()) {
                Log::warning("Team has no tasks assigned", [
                    'team_index' => $index,
                    'team_size' => $teamSchedule['team']->count()
                ]);
            }
        }
    
        return new Individual($schedule);
    }

    //     foreach ($teams as $index => $team) {
    //         $schedule[$index] = ['team' => $team, 'tasks' => collect()];
    //     }

    //     // Sort tasks by duration (longest first)
    //     $sortedTasks = $tasks->sortByDesc('duration');

    //     foreach ($sortedTasks as $task) {
    //         // Assign to team with least workload
    //         $minWorkload = PHP_INT_MAX;
    //         $selectedTeam = 0;

    //         foreach ($schedule as $index => $teamSchedule) {
    //             $workload = $this->calculateTeamWorkload($teamSchedule['tasks'], $efficiencies[$index]);
    //             if ($workload < $minWorkload) {
    //                 $minWorkload = $workload;
    //                 $selectedTeam = $index;
    //             }
    //         }

    //         $schedule[$selectedTeam]['tasks']->push($task);
    //     }

    //     return new Individual($schedule);
    // }

    protected function generateRandomSchedule(Collection $teams, Collection $tasks): Individual
    {
        $schedule = [];
        foreach ($teams as $index => $team) {
            $schedule[$index] = [
                'team' => $team, 
                'tasks' => collect(),
                'total_hours' => 0
            ];
        }
    
        foreach ($tasks as $task) {
            $randomTeam = rand(0, count($teams) - 1);
            $schedule[$randomTeam]['tasks']->push($task);
            $schedule[$randomTeam]['total_hours'] += ($task->duration + $task->travel_time) / 60;
        }
    
        return new Individual($schedule);
    }

    // protected function generateRandomSchedule(Collection $teams, Collection $tasks): Individual
    // {
    //     $schedule = [];
    //     foreach ($teams as $index => $team) {
    //         $schedule[$index] = ['team' => $team, 'tasks' => collect()];
    //     }

    //     foreach ($tasks as $task) {
    //         $randomTeam = rand(0, count($teams) - 1);
    //         $schedule[$randomTeam]['tasks']->push($task);
    //     }

    //     return new Individual($schedule);
    // }

    protected function calculateTeamWorkload(Collection $tasks, float $efficiency): float
    {
        return $tasks->sum(fn($task) => $task->duration / $efficiency);
    }

    /**
     * ✅ CRITICAL FIX: Ensure teams remain fixed across all individuals
     * Teams should NOT change during evolution - only task assignments change
     * This prevents employees from appearing in multiple teams
     */
    protected function ensureTeamsAreFixed(Individual $individual, Collection $fixedTeams): Individual
    {
        $schedule = $individual->getSchedule();
        $correctedSchedule = [];

        // Replace team composition with fixed teams
        foreach ($schedule as $teamIndex => $teamSchedule) {
            if (isset($fixedTeams[$teamIndex])) {
                $correctedSchedule[$teamIndex] = [
                    'team' => $fixedTeams[$teamIndex], // Use FIXED team
                    'tasks' => $teamSchedule['tasks'], // Keep task assignments
                    'total_hours' => $teamSchedule['total_hours'] ?? 0
                ];
            }
        }

        // ✅ VALIDATION: Check for duplicate employee assignments
        $allEmployeeIds = collect();
        $duplicateEmployees = collect();

        foreach ($correctedSchedule as $teamIndex => $teamSchedule) {
            $teamEmployeeIds = collect($teamSchedule['team'])->pluck('id');

            foreach ($teamEmployeeIds as $empId) {
                if ($allEmployeeIds->contains($empId)) {
                    $duplicateEmployees->push($empId);
                    Log::error("❌ CRITICAL: Employee appears in multiple teams!", [
                        'employee_id' => $empId,
                        'team_index' => $teamIndex + 1,
                        'all_teams' => collect($correctedSchedule)->map(fn($ts, $idx) => [
                            'team' => $idx + 1,
                            'members' => collect($ts['team'])->pluck('id')->toArray()
                        ])->values()->toArray()
                    ]);
                }
                $allEmployeeIds->push($empId);
            }
        }

        // ✅ VALIDATION: Check for duplicate task assignments
        $allTaskIds = collect();
        foreach ($correctedSchedule as $teamIndex => $teamSchedule) {
            foreach ($teamSchedule['tasks'] as $task) {
                if ($allTaskIds->contains($task->id)) {
                    Log::warning("⚠️ Duplicate task detected in schedule", [
                        'task_id' => $task->id,
                        'team_index' => $teamIndex
                    ]);
                }
                $allTaskIds->push($task->id);
            }
        }

        $correctedIndividual = new Individual($correctedSchedule);

        // ✅ Only set fitness if it exists (not null)
        $fitness = $individual->getFitness();
        if ($fitness !== null) {
            $correctedIndividual->setFitness($fitness);
        }

        $correctedIndividual->setMetadata($individual->getMetadata());

        return $correctedIndividual;
    }
}