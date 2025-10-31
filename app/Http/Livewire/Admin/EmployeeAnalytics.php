<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\OptimizationRun;
use App\Models\OptimizationTeam;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EmployeeAnalytics extends Component
{
    public $selectedRunId = null;
    public $selectedClientId = null; // For filtering workforce breakdown by client
    public $selectedFitnessClientId = null; // For filtering fitness breakdown by client

    public function render()
    {
        // Get all optimization runs ordered by most recent first, GROUPED BY SERVICE DATE
        $allRuns = OptimizationRun::with(['teams.members.employee', 'teams.tasks'])
            ->orderBy('created_at', 'desc')
            ->limit(30) // Get more runs to show last 10 unique dates
            ->get();

        // Group runs by service_date and create summary for each date
        $groupedByDate = $allRuns->groupBy('service_date')->take(10); // Show last 10 unique dates

        $optimizationRuns = $groupedByDate->map(function($runsForDate, $serviceDate) {
            // Get all runs for this date
            $runIds = $runsForDate->pluck('id')->toArray();
            $firstRun = $runsForDate->first();
            $isSaved = $runsForDate->contains('is_saved', true);

            // Calculate combined metrics across all clients
            $totalTeams = $runsForDate->sum(fn($r) => $r->teams->count());
            $totalTasks = $runsForDate->sum('total_tasks');
            $totalEmployees = $runsForDate->sum('total_employees');
            $avgGenerations = round($runsForDate->avg('generations_run'));
            $avgRuntime = round($runsForDate->avg(fn($r) => $r->created_at->diffInSeconds($r->updated_at)));

            // Calculate overall fitness across all clients for this date
            $allTeamsForDate = $runsForDate->flatMap(fn($r) => $r->teams);
            $overallFitness = $this->calculateFitnessFromTeams($allTeamsForDate);

            // Update each run's fitness
            foreach ($runsForDate as $run) {
                $actualFitness = $this->calculateActualFitnessForRun($run);
                if ($run->final_fitness_score != $actualFitness) {
                    Log::info("Updating stored fitness score", [
                        'run_id' => $run->id,
                        'old_fitness' => $run->final_fitness_score,
                        'new_fitness' => $actualFitness,
                        'reason' => 'Displaying current calculated fitness'
                    ]);
                    $run->final_fitness_score = $actualFitness;
                    $run->save();
                }
            }

            // Check if overall fitness is optimal
            $isOptimal = $overallFitness >= 0.999;

            return [
                'id' => $firstRun->id, // Use first run ID for selection
                'run_ids' => $runIds, // All run IDs for this date
                'client_count' => $runsForDate->count(), // Number of clients
                'service_date' => Carbon::parse($serviceDate)->format('M d, Y'),
                'is_saved' => $isSaved,
                'fitness_rate' => round($overallFitness, 4),
                'convergence_rate' => $avgGenerations,
                'runtime' => $avgRuntime,
                'runtime_formatted' => $this->formatRuntime($avgRuntime),
                'is_optimal' => $isOptimal,
                'total_teams' => $totalTeams,
                'total_tasks' => $totalTasks,
                'total_employees' => $totalEmployees,
                'created_at' => $firstRun->created_at->format('M d, Y H:i:s'),
            ];
        })->values();

        // Get latest optimization run for detailed view
        $latestRun = $optimizationRuns->first();

        // Get the selected run or latest run to determine the service date
        $primaryRun = $this->selectedRunId
            ? OptimizationRun::find($this->selectedRunId)
            : OptimizationRun::latest()->first();

        // Get ALL optimization runs for the same service date (multiple clients)
        $selectedRuns = [];
        if ($primaryRun) {
            $selectedRuns = OptimizationRun::with(['teams.members.employee', 'teams.tasks'])
                ->where('service_date', $primaryRun->service_date)
                ->orderBy('id', 'asc')
                ->get();
        }

        // For backward compatibility, set selectedRun to primary run
        $selectedRun = $primaryRun;

        // Calculate breakdowns using ALL runs for the service date
        $fullFitnessBreakdown = count($selectedRuns) > 0 ? $this->calculateFitnessBreakdown($selectedRuns) : null;
        $workforceBreakdown = count($selectedRuns) > 0 ? $this->calculateWorkforceBreakdown($selectedRuns) : null;
        $overallWorkforceAllocation = count($selectedRuns) > 0 ? $this->calculateOverallWorkforceAllocation($selectedRuns) : null;
        $overallFitness = count($selectedRuns) > 0 ? $this->calculateOverallFitness($selectedRuns) : null;

        // Get list of clients from fitness breakdown for dropdown
        $availableFitnessClients = [];
        if ($fullFitnessBreakdown) {
            $clientTeamMap = [];
            foreach ($fullFitnessBreakdown['teams'] as $team) {
                $clientName = $team['client_name'];
                $clientTeamMap[$clientName] = $clientName;
            }
            $availableFitnessClients = $clientTeamMap;
        }

        // Auto-select first client for fitness if none selected
        if ($fullFitnessBreakdown && !$this->selectedFitnessClientId && count($availableFitnessClients) > 0) {
            $this->selectedFitnessClientId = array_key_first($availableFitnessClients);
        }

        // Filter fitness breakdown by selected client
        $fitnessBreakdown = null;
        if ($fullFitnessBreakdown) {
            if ($this->selectedFitnessClientId) {
                // Filter teams by selected client
                $filteredTeams = array_filter($fullFitnessBreakdown['teams'], function($team) {
                    return $team['client_name'] === $this->selectedFitnessClientId;
                });

                $fitnessBreakdown = $fullFitnessBreakdown;
                $fitnessBreakdown['teams'] = array_values($filteredTeams); // Re-index array
            } else {
                $fitnessBreakdown = $fullFitnessBreakdown;
            }
        }

        // Get list of clients from workforce breakdown for dropdown
        $availableClients = [];
        if ($workforceBreakdown) {
            foreach ($workforceBreakdown as $clientId => $client) {
                $availableClients[$clientId] = $client['client_name'];
            }
        }

        // Auto-select first client if none selected
        if ($workforceBreakdown && !$this->selectedClientId && count($availableClients) > 0) {
            $this->selectedClientId = array_key_first($workforceBreakdown);
        }

        // Filter workforce breakdown by selected client
        $filteredWorkforceBreakdown = null;
        if ($workforceBreakdown && $this->selectedClientId && isset($workforceBreakdown[$this->selectedClientId])) {
            $filteredWorkforceBreakdown = [$this->selectedClientId => $workforceBreakdown[$this->selectedClientId]];
        }

        return view('livewire.admin.employee-analytics', [
            'optimizationRuns' => $optimizationRuns,
            'latestRun' => $latestRun,
            'fitnessBreakdown' => $fitnessBreakdown,
            'workforceBreakdown' => $filteredWorkforceBreakdown,
            'overallWorkforceAllocation' => $overallWorkforceAllocation,
            'overallFitness' => $overallFitness,
            'availableClients' => $availableClients,
            'availableFitnessClients' => $availableFitnessClients,
            'selectedRun' => $selectedRun,
        ])->layout('components.layouts.general-employer', [
            'title' => 'Employee Analytics Dashboard',
        ]);
    }

    public function selectRun($runId)
    {
        $this->selectedRunId = $runId;
        $this->selectedClientId = null; // Reset workforce client selection when run changes
        $this->selectedFitnessClientId = null; // Reset fitness client selection when run changes
    }

    public function selectClient($clientId)
    {
        $this->selectedClientId = $clientId;
    }

    public function selectFitnessClient($clientId)
    {
        $this->selectedFitnessClientId = $clientId;
    }

    private function calculateFitnessBreakdown($runs)
    {
        // Accept both single run and collection of runs
        if (!$runs) {
            return null;
        }

        // Convert single run to collection
        if (!is_array($runs) && !($runs instanceof \Illuminate\Support\Collection)) {
            $runs = collect([$runs]);
        } elseif (is_array($runs)) {
            $runs = collect($runs);
        }

        $teamBreakdown = [];
        $workloads = [];
        $totalPenalty = 0;

        // Process ALL teams from ALL runs
        foreach ($runs as $run) {
            if (!$run || !$run->teams) {
                continue;
            }

            foreach ($run->teams as $team) {
            $tasks = Task::where('assigned_team_id', $team->id)->get();
            $taskCount = $tasks->count();
            $arrivalTaskCount = $tasks->where('arrival_status', true)->count();

            // Calculate team efficiency (simplified - use average employee efficiency)
            $avgEfficiency = $team->members->avg(fn($m) => $m->employee->efficiency ?? 1.0) ?: 1.0;

            // ✅ Determine which client this team is serving
            $clientName = 'Unknown';
            $travelTimeMinutes = 0;
            if ($taskCount > 0) {
                $firstTask = $tasks->first();
                // Get contracted client from first task's location
                if ($firstTask->location && $firstTask->location->contractedClient) {
                    $clientName = $firstTask->location->contractedClient->name;
                    $clientId = $firstTask->location->contracted_client_id;
                    // Kakslauttanen (ID=1): 30 min, Aikamatkat (ID=2): 15 min
                    $travelTimeMinutes = ($clientId == 1) ? 30 : 15;
                } elseif ($firstTask->client) {
                    // External client
                    $clientName = $firstTask->client->name ?? 'External Client';
                }
            }

            // Calculate workloads
            $totalWorkload = 0;
            $arrivalWorkload = 0;
            $totalHours = 0;

            foreach ($tasks as $task) {
                // ✅ Use 'duration' field (same as FitnessCalculator) for consistency
                $taskDuration = $task->duration ?? $task->estimated_duration_minutes ?? 60;
                $predictedDuration = $taskDuration / $avgEfficiency;
                $totalWorkload += $predictedDuration;

                if ($task->arrival_status) {
                    $arrivalWorkload += $predictedDuration;
                }

                // Total hours (duration only - travel added once below)
                $totalHours += $taskDuration / 60;
            }

            // Add one-time travel to total hours
            $totalHours += $travelTimeMinutes / 60;

            $workloads[] = $totalWorkload;

            // Calculate penalties
            $penalties = [];
            $teamPenalty = 0;

            // 12-hour limit penalty
            if ($totalHours > 12) {
                $overtime = $totalHours - 12;
                $overtimePenalty = $overtime * 10;
                $teamPenalty += $overtimePenalty;
                $totalPenalty += $overtimePenalty;
                $penalties[] = [
                    'type' => '12-hour Limit Exceeded',
                    'value' => round($overtimePenalty, 4),
                    'details' => round($overtime, 2) . ' hours overtime',
                ];
            }

            // 3PM deadline penalty (only for arrival tasks)
            if ($arrivalTaskCount > 0 && $arrivalWorkload > 900) {
                $overDeadline = $arrivalWorkload - 900;
                $deadlinePenalty = $overDeadline * 0.005;
                $teamPenalty += $deadlinePenalty;
                $totalPenalty += $deadlinePenalty;
                $penalties[] = [
                    'type' => '3PM Deadline (Arrival Tasks)',
                    'value' => round($deadlinePenalty, 4),
                    'details' => round($overDeadline, 2) . ' min over deadline',
                ];
            }

            $teamBreakdown[] = [
                'team_name' => 'Team ' . $team->team_index,
                'client_name' => $clientName,
                'members' => $team->members->pluck('employee.user.name')->toArray(),
                'efficiency' => round($avgEfficiency, 2),
                'task_count' => $taskCount,
                'arrival_task_count' => $arrivalTaskCount,
                'workload_minutes' => round($totalWorkload, 2),
                'workload_hours' => round($totalWorkload / 60, 2),
                'arrival_workload' => round($arrivalWorkload, 2),
                'travel_time_minutes' => $travelTimeMinutes,
                'total_hours' => round($totalHours, 2),
                'penalties' => $penalties,
                'team_penalty' => round($teamPenalty, 4),
            ];
            } // End foreach team
        } // End foreach run

        // Calculate standard deviation
        $mean = count($workloads) > 0 ? array_sum($workloads) / count($workloads) : 0;
        $variance = 0;
        foreach ($workloads as $workload) {
            $variance += pow($workload - $mean, 2);
        }
        $stdDev = count($workloads) > 0 ? sqrt($variance / count($workloads)) : 0;

        // Calculate fitness components
        $baseFitness = $stdDev > 0 ? 1 / (1 + $stdDev) : 1.0;
        $finalFitness = max(0.001, $baseFitness - $totalPenalty);

        return [
            'teams' => $teamBreakdown,
            'mean_workload' => round($mean, 2),
            'std_dev' => round($stdDev, 2),
            'base_fitness' => round($baseFitness, 4),
            'total_penalty' => round($totalPenalty, 4),
            'final_fitness' => round($finalFitness, 4),
            'explanation' => [
                'formula' => 'Fitness = (1 / (1 + StdDev)) - Penalties',
                'balance' => 'Lower StdDev = Better task balance across teams',
                'penalty1' => '12-Hour Limit: Heavy penalty (×10) if team works over 12 hours',
                'penalty2' => '3PM Deadline: Only for ARRIVAL tasks (urgent guests)',
            ],
        ];
    }

    private function calculateWorkforceBreakdown($runs)
    {
        // Accept both single run and collection of runs
        if (!$runs) {
            return null;
        }

        // Convert single run to collection
        if (!is_array($runs) && !($runs instanceof \Illuminate\Support\Collection)) {
            $runs = collect([$runs]);
        } elseif (is_array($runs)) {
            $runs = collect($runs);
        }

        // Group teams by client
        $clientBreakdown = [];

        // Process ALL teams from ALL runs
        foreach ($runs as $run) {
            if (!$run || !$run->teams) {
                continue;
            }

            foreach ($run->teams as $team) {
            $tasks = Task::where('assigned_team_id', $team->id)->get();

            if ($tasks->isEmpty()) continue;

            $firstTask = $tasks->first();
            $clientId = null;
            $clientName = 'Unknown';

            // Determine client
            if ($firstTask->location && $firstTask->location->contractedClient) {
                $clientId = 'contracted_' . $firstTask->location->contracted_client_id;
                $clientName = $firstTask->location->contractedClient->name;
            } elseif ($firstTask->client) {
                $clientId = 'client_' . $firstTask->client->id;
                $clientName = $firstTask->client->name ?? 'External Client';
            }

            if (!$clientId) continue;

            // Initialize client entry if not exists
            if (!isset($clientBreakdown[$clientId])) {
                $clientBreakdown[$clientId] = [
                    'client_name' => $clientName,
                    'teams' => [],
                    'total_employees' => 0,
                    'total_tasks' => 0,
                    'task_details' => [],
                ];
            }

            // Add team to client
            $clientBreakdown[$clientId]['teams'][] = [
                'team_index' => $team->team_index,
                'members' => $team->members->pluck('employee.user.name')->toArray(),
                'task_count' => $tasks->count(),
            ];

            $clientBreakdown[$clientId]['total_employees'] += $team->members->count();
            $clientBreakdown[$clientId]['total_tasks'] += $tasks->count();

            // Collect task details for this client (avoid duplicates)
            foreach ($tasks as $task) {
                $taskId = $task->id;
                if (!isset($clientBreakdown[$clientId]['task_details'][$taskId])) {
                    $clientBreakdown[$clientId]['task_details'][$taskId] = [
                        'description' => $task->task_description,
                        'duration_minutes' => $task->duration ?? $task->estimated_duration_minutes,
                        'location' => $task->location ? $task->location->location_name : 'N/A',
                    ];
                }
            } // End foreach task
            } // End foreach team
        } // End foreach run

        // Calculate workforce steps for each client
        foreach ($clientBreakdown as $clientId => &$client) {
            // Step (a) & (b): Calculate task durations and total required hours
            $taskDurations = [];
            $totalTaskHours = 0;

            foreach ($client['task_details'] as $taskId => $taskDetail) {
                $durationHours = $taskDetail['duration_minutes'] / 60;
                $taskDurations[] = [
                    'description' => $taskDetail['description'],
                    'location' => $taskDetail['location'],
                    'duration_minutes' => $taskDetail['duration_minutes'],
                    'duration_hours' => round($durationHours, 2),
                ];
                $totalTaskHours += $durationHours;
            }

            // Add one-time travel based on client
            $travelMinutes = 0;
            if (strpos($clientId, 'contracted_1') !== false) {
                $travelMinutes = 30; // Kakslauttanen
            } elseif (strpos($clientId, 'contracted_2') !== false) {
                $travelMinutes = 15; // Aikamatkat
            }
            $travelHours = $travelMinutes / 60;

            // Total required hours (Step b)
            $totalRequiredHours = $totalTaskHours + $travelHours;

            // Step (c): Calculate minimum workforce
            $availableHours = config('optimization.workforce.available_hours', 8.0);
            $utilizationRate = config('optimization.workforce.target_utilization', 0.85);
            $productiveHours = $availableHours * $utilizationRate;
            $minimumWorkforce = (int) ceil($totalRequiredHours / $productiveHours);

            // Step (d): Calculate max affordable (if budget set)
            $budgetLimit = config('optimization.workforce.budget_limit');
            $hourlyWage = config('optimization.workforce.hourly_wage', 13.0);
            $benefitsCost = config('optimization.workforce.benefits_cost', 5.0);
            $costPerEmployee = ($hourlyWage * $availableHours) + $benefitsCost;

            $maxAffordable = null;
            if ($budgetLimit) {
                $maxAffordable = (int) floor($budgetLimit / $costPerEmployee);
            }

            // Step (e): Final workforce (actual assigned)
            $finalWorkforce = $client['total_employees'];

            // Explanation of why this number
            $explanation = [];
            $explanation[] = "Minimum required: {$minimumWorkforce} employees";
            if ($maxAffordable) {
                $explanation[] = "Budget allows: {$maxAffordable} employees";
            }
            $explanation[] = "Actually assigned: {$finalWorkforce} employees";

            // Team formation - count actual team sizes
            $pairCount = 0;
            $trioCount = 0;
            foreach ($client['teams'] as $team) {
                $memberCount = count($team['members']);
                if ($memberCount == 2) {
                    $pairCount++;
                } elseif ($memberCount == 3) {
                    $trioCount++;
                }
            }

            if ($trioCount > 0 && $pairCount > 0) {
                $explanation[] = "Formed {$pairCount} pair(s) and {$trioCount} trio(s)";
            } elseif ($trioCount > 0) {
                $explanation[] = "Formed {$trioCount} trio(s) only";
            } elseif ($pairCount > 0) {
                $explanation[] = "Formed {$pairCount} pair(s) only";
            }

            $client['calculations'] = [
                'step_a_b' => [
                    'task_durations' => $taskDurations,
                    'total_task_hours' => round($totalTaskHours, 2),
                    'travel_minutes' => $travelMinutes,
                    'travel_hours' => round($travelHours, 2),
                    'total_required_hours' => round($totalRequiredHours, 2),
                ],
                'step_c' => [
                    'available_hours_per_employee' => $availableHours,
                    'utilization_rate' => $utilizationRate,
                    'productive_hours_per_employee' => round($productiveHours, 2),
                    'minimum_workforce' => $minimumWorkforce,
                    'formula' => "Ceiling({$totalRequiredHours} / {$productiveHours}) = {$minimumWorkforce}",
                ],
                'step_d' => [
                    'budget_limit' => $budgetLimit,
                    'hourly_wage' => $hourlyWage,
                    'benefits_cost' => $benefitsCost,
                    'cost_per_employee' => round($costPerEmployee, 2),
                    'max_affordable' => $maxAffordable,
                    'formula' => $maxAffordable ? "Floor({$budgetLimit} / {$costPerEmployee}) = {$maxAffordable}" : 'No budget limit set',
                ],
                'step_e' => [
                    'final_workforce' => $finalWorkforce,
                    'explanation' => $explanation,
                ],
            ];
        }

        return $clientBreakdown;
    }

    private function calculateOverallWorkforceAllocation($runs)
    {
        // Accept both single run and collection of runs
        if (!$runs) {
            return null;
        }

        // Convert single run to collection
        if (!is_array($runs) && !($runs instanceof \Illuminate\Support\Collection)) {
            $runs = collect([$runs]);
        } elseif (is_array($runs)) {
            $runs = collect($runs);
        }

        // Group all tasks and teams by client
        $clientSummaries = [];
        $allTasks = [];
        $totalEmployees = 0;

        // Process ALL teams from ALL runs
        foreach ($runs as $run) {
            if (!$run || !$run->teams) {
                continue;
            }

            foreach ($run->teams as $team) {
            $tasks = Task::where('assigned_team_id', $team->id)->get();

            if ($tasks->isEmpty()) continue;

            $firstTask = $tasks->first();
            $clientId = null;
            $clientName = 'Unknown';

            if ($firstTask->location && $firstTask->location->contractedClient) {
                $clientId = 'contracted_' . $firstTask->location->contracted_client_id;
                $clientName = $firstTask->location->contractedClient->name;
            } elseif ($firstTask->client) {
                $clientId = 'client_' . $firstTask->client->id;
                $clientName = $firstTask->client->name ?? 'External Client';
            }

            if (!$clientId) continue;

            // Initialize client summary if not exists
            if (!isset($clientSummaries[$clientId])) {
                $clientSummaries[$clientId] = [
                    'client_name' => $clientName,
                    'task_count' => 0,
                    'employee_count' => 0,
                    'team_count' => 0,
                    'total_hours' => 0,
                    'travel_minutes' => 0,
                ];
            }

            // Add to client summary
            $clientSummaries[$clientId]['team_count']++;
            $clientSummaries[$clientId]['employee_count'] += $team->members->count();
            $clientSummaries[$clientId]['task_count'] += $tasks->count();

            // Calculate hours for this team
            foreach ($tasks as $task) {
                $durationHours = ($task->duration ?? $task->estimated_duration_minutes) / 60;
                $clientSummaries[$clientId]['total_hours'] += $durationHours;

                // Collect all tasks for overall calculation
                $allTasks[] = $task;
            }

            // Set travel time (one-time per client, not per team)
            if ($clientSummaries[$clientId]['travel_minutes'] == 0) {
                if (strpos($clientId, 'contracted_1') !== false) {
                    $clientSummaries[$clientId]['travel_minutes'] = 30; // Kakslauttanen
                } elseif (strpos($clientId, 'contracted_2') !== false) {
                    $clientSummaries[$clientId]['travel_minutes'] = 15; // Aikamatkat
                }
            }
            } // End foreach team
        } // End foreach run

        // Calculate overall totals
        $totalTasks = count($allTasks);
        $totalHours = 0;
        $totalTravelHours = 0;

        foreach ($clientSummaries as $summary) {
            $totalEmployees += $summary['employee_count'];
            $totalHours += $summary['total_hours'];
            $totalTravelHours += $summary['travel_minutes'] / 60;
        }

        $totalRequiredHours = $totalHours + $totalTravelHours;

        // Step (c): Minimum workforce
        $availableHours = config('optimization.workforce.available_hours', 8.0);
        $utilizationRate = config('optimization.workforce.target_utilization', 0.85);
        $productiveHours = $availableHours * $utilizationRate;
        $minimumWorkforce = (int) ceil($totalRequiredHours / $productiveHours);

        // Step (d): Max affordable
        $budgetLimit = config('optimization.workforce.budget_limit');
        $hourlyWage = config('optimization.workforce.hourly_wage', 13.0);
        $benefitsCost = config('optimization.workforce.benefits_cost', 5.0);
        $costPerEmployee = ($hourlyWage * $availableHours) + $benefitsCost;

        $maxAffordable = null;
        if ($budgetLimit) {
            $maxAffordable = (int) floor($budgetLimit / $costPerEmployee);
        }

        return [
            'client_summaries' => $clientSummaries,
            'totals' => [
                'tasks' => $totalTasks,
                'employees' => $totalEmployees,
                'task_hours' => round($totalHours, 2),
                'travel_hours' => round($totalTravelHours, 2),
                'required_hours' => round($totalRequiredHours, 2),
            ],
            'calculations' => [
                'available_hours' => $availableHours,
                'utilization_rate' => $utilizationRate,
                'productive_hours' => round($productiveHours, 2),
                'minimum_workforce' => $minimumWorkforce,
                'max_affordable' => $maxAffordable,
                'final_workforce' => $totalEmployees,
            ],
        ];
    }

    /**
     * Calculate Overall Fitness for the entire schedule across all clients
     * Also provides per-client fitness breakdown for comparison
     */
    private function calculateOverallFitness($runs)
    {
        // Accept both single run and collection of runs
        if (!$runs) {
            return null;
        }

        // Convert single run to collection
        if (!is_array($runs) && !($runs instanceof \Illuminate\Support\Collection)) {
            $runs = collect([$runs]);
        } elseif (is_array($runs)) {
            $runs = collect($runs);
        }

        // Calculate OVERALL fitness (all teams from all clients)
        $allWorkloads = [];
        $allPenalties = 0;
        $totalTeams = 0;
        $totalTasks = 0;
        $clientData = [];
        $runByClientId = []; // Map client IDs to their OptimizationRun records

        // Process ALL teams from ALL runs
        foreach ($runs as $run) {
            if (!$run || !$run->teams) {
                continue;
            }

            foreach ($run->teams as $team) {
                $tasks = Task::where('assigned_team_id', $team->id)->get();
                $taskCount = $tasks->count();
                $arrivalTaskCount = $tasks->where('arrival_status', true)->count();

                if ($taskCount == 0) continue;

                $totalTeams++;
                $totalTasks += $taskCount;

                // Determine client
                $firstTask = $tasks->first();
                $clientId = 'unknown';
                $clientName = 'Unknown';
                $travelTimeMinutes = 0;

                if ($firstTask->location && $firstTask->location->contractedClient) {
                    $clientId = 'contracted_' . $firstTask->location->contracted_client_id;
                    $clientName = $firstTask->location->contractedClient->name;
                    $contractedClientId = $firstTask->location->contracted_client_id;
                    // Kakslauttanen (ID=1): 30 min, Aikamatkat (ID=2): 15 min
                    $travelTimeMinutes = ($contractedClientId == 1) ? 30 : 15;
                } elseif ($firstTask->client) {
                    $clientId = 'client_' . $firstTask->client->id;
                    $clientName = $firstTask->client->name ?? 'External Client';
                }

                // Initialize client data if not exists
                if (!isset($clientData[$clientId])) {
                    $clientData[$clientId] = [
                        'client_name' => $clientName,
                        'workloads' => [],
                        'penalties' => 0,
                        'team_count' => 0,
                        'task_count' => 0,
                    ];
                    // Store the run associated with this client
                    $runByClientId[$clientId] = $run;
                }

                // Calculate team efficiency
                $avgEfficiency = $team->members->avg(fn($m) => $m->employee->efficiency ?? 1.0) ?: 1.0;

                // Calculate workload and hours
                $totalWorkload = 0;
                $arrivalWorkload = 0;
                $totalHours = 0;

                foreach ($tasks as $task) {
                    // ✅ Use 'duration' field (same as FitnessCalculator) for consistency
                    $taskDuration = $task->duration ?? $task->estimated_duration_minutes ?? 60;
                    $predictedDuration = $taskDuration / $avgEfficiency;
                    $totalWorkload += $predictedDuration;

                    if ($task->arrival_status) {
                        $arrivalWorkload += $predictedDuration;
                    }

                    $totalHours += $taskDuration / 60;
                }

                // Add one-time travel to total hours
                $totalHours += $travelTimeMinutes / 60;

                // Add to overall workloads
                $allWorkloads[] = $totalWorkload;

                // Add to client-specific workloads
                $clientData[$clientId]['workloads'][] = $totalWorkload;
                $clientData[$clientId]['team_count']++;
                $clientData[$clientId]['task_count'] += $taskCount;

                // Calculate penalties
                $teamPenalty = 0;

                // 12-hour limit penalty
                if ($totalHours > 12) {
                    $overtime = $totalHours - 12;
                    $overtimePenalty = $overtime * 10;
                    $teamPenalty += $overtimePenalty;
                    $allPenalties += $overtimePenalty;
                    $clientData[$clientId]['penalties'] += $overtimePenalty;
                }

                // 3PM deadline penalty (only for arrival tasks)
                if ($arrivalTaskCount > 0 && $arrivalWorkload > 900) {
                    $overDeadline = $arrivalWorkload - 900;
                    $deadlinePenalty = $overDeadline * 0.005;
                    $teamPenalty += $deadlinePenalty;
                    $allPenalties += $deadlinePenalty;
                    $clientData[$clientId]['penalties'] += $deadlinePenalty;
                }
            } // End foreach team
        } // End foreach run

        // Calculate OVERALL fitness
        $overallMean = count($allWorkloads) > 0 ? array_sum($allWorkloads) / count($allWorkloads) : 0;
        $overallVariance = 0;
        foreach ($allWorkloads as $workload) {
            $overallVariance += pow($workload - $overallMean, 2);
        }
        $overallStdDev = count($allWorkloads) > 0 ? sqrt($overallVariance / count($allWorkloads)) : 0;
        $overallBaseFitness = $overallStdDev > 0 ? 1 / (1 + $overallStdDev) : 1.0;
        $overallFinalFitness = max(0.001, $overallBaseFitness - $allPenalties);

        // Calculate per-client fitness from CURRENT task data (not stored historical values)
        $clientFitnessScores = [];
        foreach ($clientData as $clientId => $data) {
            if (count($data['workloads']) == 0) continue;

            // Calculate mean workload and stddev from CURRENT data
            $mean = array_sum($data['workloads']) / count($data['workloads']);
            $variance = 0;
            foreach ($data['workloads'] as $workload) {
                $variance += pow($workload - $mean, 2);
            }
            $stdDev = sqrt($variance / count($data['workloads']));

            // Calculate fitness from CURRENT data (not stored value!)
            $baseFitness = $stdDev > 0 ? 1 / (1 + $stdDev) : 1.0;
            $currentFitness = max(0.001, $baseFitness - $data['penalties']);

            // Get stored fitness for comparison
            $run = $runByClientId[$clientId] ?? null;
            $storedFitness = $run ? round($run->final_fitness_score, 4) : null;

            $clientFitnessScores[$clientId] = [
                'client_name' => $data['client_name'],
                'team_count' => $data['team_count'],
                'task_count' => $data['task_count'],
                'mean_workload' => round($mean, 2),
                'std_dev' => round($stdDev, 2),
                'base_fitness' => round($baseFitness, 4),
                'penalties' => round($data['penalties'], 4),
                'final_fitness' => round($currentFitness, 4), // CURRENT calculated fitness
                'stored_fitness' => $storedFitness, // Historical fitness from GA run (for comparison)
            ];

            // ✅ UPDATE the stored fitness score in the database to match current reality
            if ($run && $storedFitness !== round($currentFitness, 4)) {
                $run->final_fitness_score = round($currentFitness, 4);
                $run->save();

                Log::info("Updated stored fitness score for OptimizationRun", [
                    'run_id' => $run->id,
                    'client_id' => $clientId,
                    'old_fitness' => $storedFitness,
                    'new_fitness' => round($currentFitness, 4),
                    'reason' => 'Tasks added/modified after optimization'
                ]);
            }
        }

        return [
            'overall' => [
                'total_teams' => $totalTeams,
                'total_tasks' => $totalTasks,
                'mean_workload' => round($overallMean, 2),
                'std_dev' => round($overallStdDev, 2),
                'base_fitness' => round($overallBaseFitness, 4),
                'total_penalties' => round($allPenalties, 4),
                'final_fitness' => round($overallFinalFitness, 4),
            ],
            'per_client' => $clientFitnessScores,
            'explanation' => [
                'overall_formula' => 'Overall Fitness = (1 / (1 + StdDev_all_teams)) - Total_Penalties',
                'calculation_basis' => 'Calculated across ALL teams from ALL clients',
                'per_client_comparison' => 'Individual client fitness shown for comparison',
                'interpretation' => 'Higher fitness (closer to 1.0) = Better balanced schedule with fewer penalties',
            ],
        ];
    }

    /**
     * Calculate actual current fitness for a single OptimizationRun
     * based on current task assignments and data
     */
    private function calculateActualFitnessForRun($run): float
    {
        if (!$run || !$run->teams || $run->teams->isEmpty()) {
            Log::warning("calculateActualFitnessForRun: No teams found", [
                'run_id' => $run->id ?? 'null'
            ]);
            return 0.001;
        }

        $workloads = [];
        $totalPenalty = 0;
        $teamDetails = [];

        foreach ($run->teams as $team) {
            $tasks = Task::where('assigned_team_id', $team->id)->get();
            $taskCount = $tasks->count();

            if ($taskCount == 0) {
                Log::debug("calculateActualFitnessForRun: Team has no tasks", [
                    'run_id' => $run->id,
                    'team_id' => $team->id
                ]);
                continue;
            }

            $arrivalTaskCount = $tasks->where('arrival_status', true)->count();
            $avgEfficiency = $team->members->avg(fn($m) => $m->employee->efficiency ?? 1.0) ?: 1.0;

            // Determine travel time based on client
            $travelTimeMinutes = 0;
            $firstTask = $tasks->first();
            if ($firstTask->location && $firstTask->location->contracted_client_id) {
                $clientId = $firstTask->location->contracted_client_id;
                $travelTimeMinutes = ($clientId == 1) ? 30 : 15;
            }

            // Calculate workload and hours
            $totalWorkload = 0;
            $arrivalWorkload = 0;
            $totalHours = 0;
            $taskDurations = [];

            foreach ($tasks as $task) {
                $taskDuration = $task->duration ?? $task->estimated_duration_minutes ?? 60;
                $predictedDuration = $taskDuration / $avgEfficiency;
                $totalWorkload += $predictedDuration;
                $taskDurations[] = $taskDuration;

                if ($task->arrival_status) {
                    $arrivalWorkload += $predictedDuration;
                }

                $totalHours += $taskDuration / 60;
            }

            $totalHours += $travelTimeMinutes / 60;
            $workloads[] = $totalWorkload;

            // Calculate penalties
            $overtimePenalty = 0;
            $deadlinePenalty = 0;

            if ($totalHours > 12) {
                $overtime = $totalHours - 12;
                $overtimePenalty = $overtime * 10;
                $totalPenalty += $overtimePenalty;
            }

            if ($arrivalTaskCount > 0 && $arrivalWorkload > 900) {
                $overDeadline = $arrivalWorkload - 900;
                $deadlinePenalty = $overDeadline * 0.005;
                $totalPenalty += $deadlinePenalty;
            }

            $teamDetails[] = [
                'team_id' => $team->id,
                'task_count' => $taskCount,
                'arrival_count' => $arrivalTaskCount,
                'task_durations' => $taskDurations,
                'total_hours' => round($totalHours, 2),
                'total_workload' => round($totalWorkload, 2),
                'arrival_workload' => round($arrivalWorkload, 2),
                'travel_time_min' => $travelTimeMinutes,
                'overtime_penalty' => round($overtimePenalty, 4),
                'deadline_penalty' => round($deadlinePenalty, 4)
            ];
        }

        if (count($workloads) == 0) {
            Log::warning("calculateActualFitnessForRun: No workloads calculated", [
                'run_id' => $run->id
            ]);
            return 0.001;
        }

        // Calculate fitness
        $mean = array_sum($workloads) / count($workloads);
        $variance = 0;
        foreach ($workloads as $workload) {
            $variance += pow($workload - $mean, 2);
        }
        $stdDev = sqrt($variance / count($workloads));
        $baseFitness = $stdDev > 0 ? 1 / (1 + $stdDev) : 1.0;
        $finalFitness = max(0.001, $baseFitness - $totalPenalty);

        Log::info("calculateActualFitnessForRun: Detailed calculation", [
            'run_id' => $run->id,
            'team_count' => count($workloads),
            'team_details' => $teamDetails,
            'workloads' => array_map(fn($w) => round($w, 2), $workloads),
            'mean_workload' => round($mean, 2),
            'std_dev' => round($stdDev, 4),
            'base_fitness' => round($baseFitness, 4),
            'total_penalty' => round($totalPenalty, 4),
            'final_fitness' => round($finalFitness, 4)
        ]);

        return $finalFitness;
    }

    /**
     * Calculate fitness from a collection of teams (across multiple optimization runs)
     * Used for calculating overall fitness when multiple clients are optimized for same date
     */
    private function calculateFitnessFromTeams($teams): float
    {
        if (!$teams || $teams->isEmpty()) {
            return 0.001;
        }

        $workloads = [];
        $totalPenalty = 0;

        foreach ($teams as $team) {
            $tasks = Task::where('assigned_team_id', $team->id)->get();
            $taskCount = $tasks->count();

            if ($taskCount == 0) {
                continue;
            }

            $arrivalTaskCount = $tasks->where('arrival_status', true)->count();
            $avgEfficiency = $team->members->avg(fn($m) => $m->employee->efficiency ?? 1.0) ?: 1.0;

            // Determine travel time based on client
            $travelTimeMinutes = 0;
            $firstTask = $tasks->first();
            if ($firstTask && $firstTask->location && $firstTask->location->contracted_client_id) {
                $clientId = $firstTask->location->contracted_client_id;
                $travelTimeMinutes = ($clientId == 1) ? 30 : 15;
            }

            // Calculate workload and hours
            $totalWorkload = 0;
            $arrivalWorkload = 0;
            $totalHours = 0;

            foreach ($tasks as $task) {
                $taskDuration = $task->duration ?? $task->estimated_duration_minutes ?? 60;
                $predictedDuration = $taskDuration / $avgEfficiency;
                $totalWorkload += $predictedDuration;

                if ($task->arrival_status) {
                    $arrivalWorkload += $predictedDuration;
                }

                $totalHours += $taskDuration / 60;
            }

            $totalHours += $travelTimeMinutes / 60;
            $workloads[] = $totalWorkload;

            // Calculate penalties
            if ($totalHours > 12) {
                $overtime = $totalHours - 12;
                $totalPenalty += $overtime * 10;
            }

            if ($arrivalTaskCount > 0 && $arrivalWorkload > 900) {
                $overDeadline = $arrivalWorkload - 900;
                $totalPenalty += $overDeadline * 0.005;
            }
        }

        if (count($workloads) == 0) {
            return 0.001;
        }

        // Calculate fitness
        $mean = array_sum($workloads) / count($workloads);
        $variance = 0;
        foreach ($workloads as $workload) {
            $variance += pow($workload - $mean, 2);
        }
        $stdDev = sqrt($variance / count($workloads));
        $baseFitness = $stdDev > 0 ? 1 / (1 + $stdDev) : 1.0;
        $finalFitness = max(0.001, $baseFitness - $totalPenalty);

        return $finalFitness;
    }

    private function formatRuntime($seconds)
    {
        if ($seconds < 60) {
            return round($seconds, 2) . 's';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $secs = $seconds % 60;
            return "{$minutes}m " . round($secs) . "s";
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return "{$hours}h {$minutes}m";
        }
    }
}
