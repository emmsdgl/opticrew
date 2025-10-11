<div>
    <header class="bg-white shadow-sm">
        <div class="px-8 py-4">
            <h2 class="text-2xl font-bold text-gray-800">Scheduling Process Log</h2>
        </div>
    </header>

    <div class="p-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
                <label for="date-filter" class="font-semibold">Select a Date to View Log:</label>
                <input type="date" wire:model="selectedDate" class="border p-2 rounded ml-2">
            </div>

            @if($logData && isset($logData['steps']))
                <div class="space-y-6">
                    @foreach($logData['steps'] as $index => $step)
                        <div class="p-4 border rounded-lg bg-gray-50">
                            <!-- Step Header -->
                            <h3 class="font-bold text-lg text-blue-700">
                                Step {{ $index + 1 }}: {{ $step['title'] ?? 'Unknown Step' }}
                                @if(isset($step['count']))
                                    ({{ $step['count'] }} {{ str_contains($step['title'], 'Employee') ? 'Found' : 'Formed' }})
                                @endif
                            </h3>

                            <!-- Available Employees -->
                            @if(str_contains($step['title'], 'Available Employees'))
                                <p class="text-sm text-gray-600 mt-2">The system identified the following employees as available to work on this date:</p>
                                <p class="mt-2 text-sm bg-white p-3 rounded border">
                                    {{ is_array($step['data']) ? collect($step['data'])->join(', ') : $step['data'] }}
                                </p>
                            @endif

                            <!-- Employee Allocation -->
                            @if(str_contains($step['title'], 'Employee Allocation'))
                                <p class="text-sm text-gray-600 mt-2">Employees allocated based on workload proportion:</p>
                                <p class="mt-2 text-sm bg-white p-3 rounded border">
                                    {{ is_array($step['data']) ? collect($step['data'])->join(', ') : $step['data'] }}
                                </p>
                            @endif

                            <!-- Team Formation -->
                            @if(str_contains($step['title'], 'Team Formation'))
                                <p class="text-sm text-gray-600 mt-2">Based on the "pair-first" and "driver-required" rules, the following teams were created:</p>
                                <div class="mt-2 space-y-2">
                                    @if(isset($step['data']) && is_array($step['data']))
                                        @foreach($step['data'] as $teamIndex => $team)
                                            <div class="bg-white p-3 rounded border">
                                                <p class="text-sm font-semibold text-gray-700">Team {{ $teamIndex + 1 }}:</p>
                                                <p class="text-sm">{{ is_array($team) ? collect($team)->join(', ') : $team }}</p>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif

                            <!-- Team Formation Failed -->
                            @if(isset($step['error']))
                                <div class="mt-2 bg-red-50 border border-red-200 p-3 rounded">
                                    <p class="text-sm text-red-700 font-semibold">⚠️ {{ $step['error'] }}</p>
                                    <p class="text-sm text-gray-600 mt-1">Employees: {{ is_array($step['employees']) ? collect($step['employees'])->join(', ') : $step['employees'] }}</p>
                                </div>
                            @endif

                            <!-- Greedy Algorithm Result -->
                            @if(str_contains($step['title'], 'Greedy Algorithm'))
                                <p class="text-sm text-gray-600 mt-2">The Greedy Algorithm assigned tasks by giving the longest tasks to teams with the least workload:</p>
                                <div class="mt-2 space-y-3">
                                    @if(isset($step['data']) && is_array($step['data']))
                                        @foreach($step['data'] as $teamSchedule)
                                            <!-- This is the NEW, corrected block -->
                                            <div class="bg-white p-3 rounded border border-green-200">
                                                <p class="font-semibold text-gray-800">
                                                    Team: {{ collect($teamSchedule['team_members'])->pluck('name')->join(', ') }}
                                                    <span class="font-normal text-gray-500 text-sm">
                                                        (Avg. Efficiency: {{ $teamSchedule['team_efficiency'] ?? 'N/A' }})

                                                        <!-- START: Efficiency Breakdown -->
                                                        <details class="relative inline-block ml-2 text-xs align-middle">
                                                            <summary class="cursor-pointer text-indigo-600 hover:underline focus:outline-none">Show Calculation</summary>
                                                            <div class="absolute z-10 mt-2 p-3 border rounded bg-white text-gray-700 text-left shadow-lg">
                                                                <ul class="list-disc list-inside whitespace-nowrap">
                                                                    @foreach($teamSchedule['team_members'] as $member)
                                                                        <li>{{ $member['name'] }}: <span class="font-semibold">{{ number_format($member['efficiency'] * 100, 1) }}%</span></li>
                                                                    @endforeach
                                                                </ul>
                                                                <hr class="my-2">
                                                                @php
                                                                    $memberEfficiencies = collect($teamSchedule['team_members'])->pluck('efficiency');
                                                                    $efficiencySum = $memberEfficiencies->sum();
                                                                    $memberCount = $memberEfficiencies->count();
                                                                    $avgEfficiency = $memberCount > 0 ? ($efficiencySum / $memberCount) * 100 : 0;
                                                                @endphp
                                                                <p class="font-semibold text-xs">
                                                                    Sum ({{ $memberEfficiencies->map(fn($e) => number_format($e, 2))->join(' + ') }}) / {{ $memberCount }} Members
                                                                    = <b>{{ number_format($avgEfficiency, 0) }}%</b>
                                                                </p>
                                                            </div>
                                                        </details>
                                                        <!-- END: Efficiency Breakdown -->
                                                    </span>
                                                </p>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    Assigned Tasks ({{ $teamSchedule['total_tasks'] ?? 0 }}):
                                                    <span class="text-gray-500">
                                                        {{ collect($teamSchedule['assigned_tasks'])->take(5)->join(', ') }}
                                                        @if(($teamSchedule['total_tasks'] ?? 0) > 5)
                                                            ... and {{ $teamSchedule['total_tasks'] - 5 }} more
                                                        @endif
                                                    </span>
                                                </p>
                                                <p class="text-sm font-bold text-green-700 mt-1">
                                                    Total Workload: {{ $teamSchedule['estimated_duration'] ?? 0 }} minutes
                                                </p>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif

                            <!-- Genetic Algorithm Result -->
                            @if(str_contains($step['title'], 'Genetic Algorithm'))
                                @php
                                    $fitnessScore = $step['fitness_score'] ?? null;
                                    $scheduleData = $step['data'] ?? [];
                                    
                                    // Perform the fitness calculation again here for display purposes
                                    $predictedWorkloads = collect($scheduleData)->pluck('predicted_workload')->all();
                                    $mean = 0;
                                    $variance = 0;
                                    $stdDev = 0;
                                    if (count($predictedWorkloads) > 0) {
                                        $mean = array_sum($predictedWorkloads) / count($predictedWorkloads);
                                        $variance = array_sum(array_map(fn($x) => ($x - $mean) ** 2, $predictedWorkloads)) / count($predictedWorkloads);
                                        $stdDev = sqrt($variance);
                                    }
                                @endphp
                                
                                <p class="text-sm text-gray-600 mt-2">The Genetic Algorithm refined the schedule for optimal workload balance (Final Fitness: {{ number_format($fitnessScore, 4) }}):</p>
                                
                                <div class="mt-2 space-y-4">
                                @foreach($scheduleData as $teamSchedule)
                                    <div class="bg-white p-3 rounded border border-blue-200">
                                        <p class="font-semibold text-gray-800">
                                            Team: {{ collect($teamSchedule['team_members'])->pluck('name')->join(', ') }}
                                            <span class="font-normal text-gray-500 text-sm">
                                                (Avg. Efficiency: {{ $teamSchedule['team_efficiency'] ?? 'N/A' }})

                                                <!-- START: Efficiency Breakdown -->
                                                <details class="relative inline-block ml-2 text-xs align-middle">
                                                    <summary class="cursor-pointer text-indigo-600 hover:underline focus:outline-none">Show Calculation</summary>
                                                    <div class="absolute z-10 mt-2 p-3 border rounded bg-white text-gray-700 text-left shadow-lg">
                                                        <ul class="list-disc list-inside whitespace-nowrap">
                                                            @foreach($teamSchedule['team_members'] as $member)
                                                                <li>{{ $member['name'] }}: <span class="font-semibold">{{ number_format($member['efficiency'] * 100, 1) }}%</span></li>
                                                            @endforeach
                                                        </ul>
                                                        <hr class="my-2">
                                                        @php
                                                            $memberEfficiencies = collect($teamSchedule['team_members'])->pluck('efficiency');
                                                            $efficiencySum = $memberEfficiencies->sum();
                                                            $memberCount = $memberEfficiencies->count();
                                                            $avgEfficiency = $memberCount > 0 ? ($efficiencySum / $memberCount) * 100 : 0;
                                                        @endphp
                                                        <p class="font-semibold text-xs">
                                                            Sum ({{ $memberEfficiencies->map(fn($e) => number_format($e, 2))->join(' + ') }}) / {{ $memberCount }} Members
                                                            = <b>{{ number_format($avgEfficiency, 0) }}%</b>
                                                        </p>
                                                    </div>
                                                </details>
                                                <!-- END: Efficiency Breakdown -->
                                            </span>
                                        </p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Assigned Tasks ({{ $teamSchedule['total_tasks'] ?? 0 }}):
                                            <span class="text-gray-500">
                                                {{ collect($teamSchedule['assigned_tasks'])->take(3)->join(', ') }}
                                                @if(($teamSchedule['total_tasks'] ?? 0) > 3)
                                                    ... and {{ $teamSchedule['total_tasks'] - 3 }} more
                                                @endif
                                            </span>
                                        </p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Total Estimated Workload: {{ $teamSchedule['estimated_duration'] ?? 0 }} minutes
                                        </p>
                                        <p class="text-sm font-bold text-blue-700 mt-1">
                                            Predicted Workload Duration: {{ $teamSchedule['predicted_workload'] ?? 0 }} minutes
                                        </p>
                                    </div>
                                @endforeach

                                </div>

                                <!-- NEW: Fitness Calculation Breakdown -->
                                <div class="mt-4 p-4 border rounded-lg bg-indigo-50 border-indigo-200">
                                    <h4 class="font-semibold text-indigo-800">Fitness Calculation Breakdown</h4>
                                    <div class="text-sm mt-2 space-y-1">
                                        @if(!empty($predictedWorkloads))
                                            <p><b>1. Predicted Workloads per Team (in minutes):</b> <br> <code class="bg-indigo-100 p-1 rounded">[{{ implode(', ', $predictedWorkloads) }}]</code></p>
                                            <p><b>2. Calculate Mean (Average Workload):</b> <br> <code class="bg-indigo-100 p-1 rounded">{{ array_sum($predictedWorkloads) }} / {{ count($predictedWorkloads) }} = {{ number_format($mean, 2) }}</code></p>
                                            <p><b>3. Calculate Standard Deviation (A measure of imbalance):</b> <br> <code class="bg-indigo-100 p-1 rounded">sqrt( variance ) = {{ number_format($stdDev, 2) }}</code></p>
                                            <p><b>4. Calculate Final Fitness Score:</b> <br> <code class="bg-indigo-100 p-1 rounded">1 / (1 + {{ number_format($stdDev, 2) }}) = {{ number_format($fitnessScore, 4) }}</code></p>
                                        @else
                                            <p class="text-gray-500 italic">Detailed workload data for this breakdown is not available in this log entry. Please re-run optimization to generate it.</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Summary -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-bold text-blue-800">Optimization Summary</h4>
                    <p class="text-sm text-gray-700 mt-2">
                        Service Date: <strong>{{ $logData['service_date'] }}</strong>
                    </p>
                    <p class="text-sm text-gray-700">
                        Total Steps: <strong>{{ count($logData['steps']) }}</strong>
                    </p>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="mt-2 text-gray-500">No scheduling log found for the selected date.</p>
                    <p class="text-sm text-gray-400 mt-1">Try running the "Optimize & Assign" function first.</p>
                </div>
            @endif
        </div>
    </div>
</div>