<div>
    <header class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="px-8 py-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Optimization Analytics & Evaluation Metrics</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Performance metrics for Genetic Algorithm optimization runs</p>
        </div>
    </header>

    <div class="p-8">
        <!-- Workforce Calculation Methodology -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-users-cog text-purple-600 mr-2"></i>
                        5-Step Workforce Calculation Methodology
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">How optimal workforce size is determined for each optimization run</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Step (a) -->
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-5 border border-purple-200 dark:border-purple-800">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold">a</div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">Calculate Individual Task Durations</h4>
                            <div class="bg-white dark:bg-gray-800 rounded p-3 mb-2">
                                <code class="text-sm text-purple-700 dark:text-purple-300">D<sub>i</sub> = A<sub>i</sub> / S<sub>i</sub></code>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Where:</strong> D<sub>i</sub> = Duration for task i, A<sub>i</sub> = Area to clean (m²), S<sub>i</sub> = Cleaning speed (m²/hour)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step (b) -->
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-5 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">b</div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">Calculate Total Required Work Hours</h4>
                            <div class="bg-white dark:bg-gray-800 rounded p-3 mb-2">
                                <code class="text-sm text-blue-700 dark:text-blue-300">T<sub>req</sub> = Σ(D<sub>i</sub>) + One-time Travel per Client</code>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Travel Time:</strong> 30 min for Kakslauttanen, 15 min for Aikamatkat (one-time per team per client, NOT per task)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step (c) -->
                <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-5 border border-green-200 dark:border-green-800">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center font-bold">c</div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">Calculate Minimum Workforce Required</h4>
                            <div class="bg-white dark:bg-gray-800 rounded p-3 mb-2">
                                <code class="text-sm text-green-700 dark:text-green-300">N<sub>base</sub> = Ceiling(T<sub>req</sub> / (H<sub>avail</sub> × R))</code>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Where:</strong> H<sub>avail</sub> = Available hours per employee (default: 8h), R = Target utilization rate (default: 0.85 = 85%)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step (d) -->
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-lg p-5 border border-orange-200 dark:border-orange-800">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-orange-600 text-white rounded-full flex items-center justify-center font-bold">d</div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">Calculate Maximum Affordable Workforce (Budget Constraint)</h4>
                            <div class="bg-white dark:bg-gray-800 rounded p-3 mb-2">
                                <code class="text-sm text-orange-700 dark:text-orange-300">N<sub>cost-max</sub> = Floor(C<sub>limit</sub> / (W × H<sub>avail</sub> + B))</code>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Where:</strong> C<sub>limit</sub> = Budget limit, W = Hourly wage, B = Benefits cost per employee
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step (e) -->
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-lg p-5 border border-indigo-200 dark:border-indigo-800">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold">e</div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">Determine Final Workforce Size</h4>
                            <div class="bg-white dark:bg-gray-800 rounded p-3 mb-2">
                                <code class="text-sm text-indigo-700 dark:text-indigo-300">N<sub>final</sub> = max(N<sub>base</sub>, min(N<sub>set</sub>, N<sub>cost-max</sub>))</code>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                <strong>Where:</strong> N<sub>set</sub> = Number of available employees
                            </p>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded p-3 border border-yellow-200 dark:border-yellow-800">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <strong>Constraints Applied:</strong>
                                </p>
                                <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-300 ml-2 mt-1">
                                    <li>N<sub>final</sub> ≥ N<sub>base</sub> (enough employees to complete work)</li>
                                    <li>N<sub>final</sub> ≤ N<sub>set</sub> (doesn't exceed available employees)</li>
                                    <li>N<sub>final</sub> ≤ N<sub>cost-max</sub> (doesn't exceed budget)</li>
                                    <li>Minimum 2 employees (1 pair) per client</li>
                                    <li>Even number preferred for pairing (add +1 for trio if needed)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall Schedule Fitness Score -->
        @if($overallFitness && $selectedRun)
        <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/30 dark:via-indigo-900/30 dark:to-purple-900/30 rounded-lg shadow-2xl p-8 mb-8 border-4 border-indigo-400 dark:border-indigo-600">
            <div class="mb-6">
                <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                    <i class="fas fa-chart-line text-indigo-600 mr-3"></i>
                    Overall Schedule Fitness Score
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Quality evaluation for the entire day's schedule across all clients</p>
            </div>

            <!-- Hero Fitness Score -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-8 mb-6 shadow-lg text-center">
                <div class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Overall Fitness</div>
                <div class="text-7xl font-black mb-4 {{ $overallFitness['overall']['final_fitness'] >= 0.9 ? 'text-green-600' : ($overallFitness['overall']['final_fitness'] >= 0.7 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $overallFitness['overall']['final_fitness'] }}
                </div>
                <div class="flex justify-center items-center gap-8 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Teams:</span>
                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ $overallFitness['overall']['total_teams'] }}</span>
                    </div>
                    <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Tasks:</span>
                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ $overallFitness['overall']['total_tasks'] }}</span>
                    </div>
                    <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Penalties:</span>
                        <span class="font-bold text-red-600">{{ $overallFitness['overall']['total_penalties'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Fitness Calculation Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-5 border border-green-200 dark:border-green-700">
                    <div class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Mean Workload</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $overallFitness['overall']['mean_workload'] }} min</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Average per team</div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-5 border border-blue-200 dark:border-blue-700">
                    <div class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Standard Deviation</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $overallFitness['overall']['std_dev'] }} min</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lower is better (more balanced)</div>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-5 border border-purple-200 dark:border-purple-700">
                    <div class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Base Fitness</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $overallFitness['overall']['base_fitness'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Before penalties</div>
                </div>
            </div>

            <!-- Per-Client Fitness Comparison -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                    <i class="fas fa-building text-gray-600 mr-2"></i>
                    Per-Client Fitness Breakdown
                </h4>
                <div class="space-y-3">
                    @foreach($overallFitness['per_client'] as $clientId => $clientFitness)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-bold text-gray-900 dark:text-gray-100">{{ $clientFitness['client_name'] }}</div>
                            <div class="text-2xl font-bold {{ $clientFitness['final_fitness'] >= 0.9 ? 'text-green-600' : ($clientFitness['final_fitness'] >= 0.7 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $clientFitness['final_fitness'] }}
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-3 text-xs">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Teams:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $clientFitness['team_count'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Tasks:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $clientFitness['task_count'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">StdDev:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $clientFitness['std_dev'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Penalties:</span>
                                <span class="font-semibold text-red-600">{{ $clientFitness['penalties'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Explanation -->
            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-5 mt-6 border border-indigo-200 dark:border-indigo-700">
                <h5 class="font-bold text-gray-900 dark:text-gray-100 mb-2 flex items-center">
                    <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                    How Overall Fitness is Calculated
                </h5>
                <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    <div><strong>Formula:</strong> <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded text-indigo-700 dark:text-indigo-300">{{ $overallFitness['explanation']['overall_formula'] }}</code></div>
                    <div><strong>Calculation Basis:</strong> {{ $overallFitness['explanation']['calculation_basis'] }}</div>
                    <div><strong>Interpretation:</strong> {{ $overallFitness['explanation']['interpretation'] }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Overall Workforce Allocation Summary -->
        @if($overallWorkforceAllocation && $selectedRun)
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-lg shadow-lg p-6 mb-8 border-2 border-emerald-300 dark:border-emerald-700">
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    <i class="fas fa-users text-emerald-600 mr-2"></i>
                    Overall Workforce Allocation for {{ \Carbon\Carbon::parse($selectedRun->service_date)->format('M d, Y') }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">How all {{ $overallWorkforceAllocation['totals']['employees'] }} employees were distributed across all clients</p>
            </div>

            <!-- Overall Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total Tasks</div>
                    <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $overallWorkforceAllocation['totals']['tasks'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total Employees</div>
                    <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $overallWorkforceAllocation['totals']['employees'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total Work Hours</div>
                    <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $overallWorkforceAllocation['totals']['task_hours'] }}h</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total with Travel</div>
                    <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $overallWorkforceAllocation['totals']['required_hours'] }}h</div>
                </div>
            </div>

            <!-- Allocation by Client -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-5 mb-6">
                <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-4">Distribution Across Clients</h4>
                <div class="space-y-4">
                    @foreach($overallWorkforceAllocation['client_summaries'] as $clientId => $summary)
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h5 class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ $summary['client_name'] }}</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $summary['task_count'] }} tasks • {{ $summary['travel_minutes'] }} min travel
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $summary['employee_count'] }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">employees</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Teams Formed</div>
                                <div class="font-bold text-gray-900 dark:text-gray-100">{{ $summary['team_count'] }} teams</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Work Hours</div>
                                <div class="font-bold text-gray-900 dark:text-gray-100">{{ round($summary['total_hours'], 2) }}h</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">With Travel</div>
                                <div class="font-bold text-gray-900 dark:text-gray-100">{{ round($summary['total_hours'] + ($summary['travel_minutes'] / 60), 2) }}h</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Overall Workforce Calculation -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-5">
                <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-4">Overall Workforce Calculation</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Step C -->
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                        <div class="flex items-center mb-2">
                            <span class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-2">c</span>
                            <h5 class="font-bold text-gray-900 dark:text-gray-100">Minimum Required</h5>
                        </div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-2">{{ $overallWorkforceAllocation['calculations']['minimum_workforce'] }} employees</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $overallWorkforceAllocation['totals']['required_hours'] }}h ÷ {{ $overallWorkforceAllocation['calculations']['productive_hours'] }}h = {{ $overallWorkforceAllocation['calculations']['minimum_workforce'] }}
                        </div>
                    </div>

                    <!-- Step D -->
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 border border-orange-200 dark:border-orange-800">
                        <div class="flex items-center mb-2">
                            <span class="w-8 h-8 bg-orange-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-2">d</span>
                            <h5 class="font-bold text-gray-900 dark:text-gray-100">Max Affordable</h5>
                        </div>
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400 mb-2">
                            {{ $overallWorkforceAllocation['calculations']['max_affordable'] ?? 'Unlimited' }}
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            @if($overallWorkforceAllocation['calculations']['max_affordable'])
                                Budget constraint applied
                            @else
                                No budget limit set
                            @endif
                        </div>
                    </div>

                    <!-- Step E -->
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-800">
                        <div class="flex items-center mb-2">
                            <span class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-2">e</span>
                            <h5 class="font-bold text-gray-900 dark:text-gray-100">Final Allocated</h5>
                        </div>
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">{{ $overallWorkforceAllocation['calculations']['final_workforce'] }} employees</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            100% utilization achieved
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Actual Workforce Calculation Results (Per Client) -->
        @if($workforceBreakdown && $selectedRun)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8 border border-gray-200 dark:border-gray-700">
            <div class="mb-6">
                <div class="flex items-center justify-between flex-wrap gap-4 mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            <i class="fas fa-calculator text-green-600 mr-2"></i>
                            Workforce Calculation Results for {{ \Carbon\Carbon::parse($selectedRun->service_date)->format('M d, Y') }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Step-by-step calculation showing how employees were assigned to each client</p>
                    </div>

                    <!-- Client Dropdown -->
                    @if(count($availableClients) > 1)
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Select Client:</label>
                        <select wire:model.live="selectedClientId"
                                class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach($availableClients as $clientId => $clientName)
                                <option value="{{ $clientId }}">{{ $clientName }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </div>

            @foreach($workforceBreakdown as $clientId => $client)
            <div class="mb-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-lg p-6 border-2 border-gray-300 dark:border-gray-600">
                <!-- Client Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $client['client_name'] }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $client['total_tasks'] }} tasks • {{ $client['total_employees'] }} employees assigned • {{ count($client['teams']) }} team(s)
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $client['total_employees'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Final Workforce</div>
                    </div>
                </div>

                <!-- Step A & B: Task Durations and Total Hours -->
                <div class="bg-white dark:bg-gray-900 rounded-lg p-5 mb-4">
                    <h5 class="font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <span class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm mr-2">a+b</span>
                        Task Durations & Total Required Hours
                    </h5>

                    <!-- Task List -->
                    <div class="mb-4 max-h-60 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-800 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Task</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Location</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-400">Duration</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($client['calculations']['step_a_b']['task_durations'] as $task)
                                <tr>
                                    <td class="px-3 py-2 text-gray-800 dark:text-gray-300">{{ $task['description'] }}</td>
                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $task['location'] }}</td>
                                    <td class="px-3 py-2 text-right font-mono text-gray-800 dark:text-gray-300">
                                        {{ $task['duration_hours'] }}h ({{ $task['duration_minutes'] }} min)
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Calculation Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-purple-50 dark:bg-purple-900/20 rounded p-4 border border-purple-200 dark:border-purple-800">
                        <div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total Task Hours</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $client['calculations']['step_a_b']['total_task_hours'] }}h</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Travel Time</div>
                            <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                +{{ $client['calculations']['step_a_b']['travel_hours'] }}h ({{ $client['calculations']['step_a_b']['travel_minutes'] }} min)
                            </div>
                        </div>
                        <div class="col-span-2">
                            <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total Required Hours (T<sub>req</sub>)</div>
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ $client['calculations']['step_a_b']['total_required_hours'] }}h
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step C: Minimum Workforce -->
                <div class="bg-white dark:bg-gray-900 rounded-lg p-5 mb-4">
                    <h5 class="font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <span class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm mr-2">c</span>
                        Minimum Workforce Calculation
                    </h5>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Available Hours</div>
                            <div class="font-bold text-gray-900 dark:text-gray-100">{{ $client['calculations']['step_c']['available_hours_per_employee'] }}h</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Utilization Rate</div>
                            <div class="font-bold text-gray-900 dark:text-gray-100">{{ $client['calculations']['step_c']['utilization_rate'] * 100 }}%</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Productive Hours</div>
                            <div class="font-bold text-gray-900 dark:text-gray-100">{{ $client['calculations']['step_c']['productive_hours_per_employee'] }}h</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Min Workforce</div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $client['calculations']['step_c']['minimum_workforce'] }}</div>
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 rounded p-3 border border-green-200 dark:border-green-800">
                        <code class="text-sm text-green-700 dark:text-green-300">{{ $client['calculations']['step_c']['formula'] }}</code>
                    </div>
                </div>

                <!-- Step D: Max Affordable -->
                <div class="bg-white dark:bg-gray-900 rounded-lg p-5 mb-4">
                    <h5 class="font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <span class="w-8 h-8 bg-orange-600 text-white rounded-full flex items-center justify-center text-sm mr-2">d</span>
                        Budget Constraint
                    </h5>

                    @if($client['calculations']['step_d']['budget_limit'])
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">
                        <div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Budget Limit</div>
                            <div class="font-bold text-gray-900 dark:text-gray-100">€{{ $client['calculations']['step_d']['budget_limit'] }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Hourly Wage</div>
                            <div class="font-bold text-gray-900 dark:text-gray-100">€{{ $client['calculations']['step_d']['hourly_wage'] }}/h</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Benefits</div>
                            <div class="font-bold text-gray-900 dark:text-gray-100">€{{ $client['calculations']['step_d']['benefits_cost'] }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Cost per Employee</div>
                            <div class="font-bold text-gray-900 dark:text-gray-100">€{{ $client['calculations']['step_d']['cost_per_employee'] }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Max Affordable</div>
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $client['calculations']['step_d']['max_affordable'] }}</div>
                        </div>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded p-3 border border-orange-200 dark:border-orange-800">
                        <code class="text-sm text-orange-700 dark:text-orange-300">{{ $client['calculations']['step_d']['formula'] }}</code>
                    </div>
                    @else
                    <div class="bg-gray-100 dark:bg-gray-800 rounded p-4 text-center">
                        <p class="text-gray-600 dark:text-gray-400">No budget limit configured - unlimited workforce allowed</p>
                    </div>
                    @endif
                </div>

                <!-- Step E: Final Decision -->
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-lg p-5 border-2 border-indigo-300 dark:border-indigo-700">
                    <h5 class="font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                        <span class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm mr-2">e</span>
                        Final Workforce Decision
                    </h5>

                    <div class="space-y-2 mb-4">
                        @foreach($client['calculations']['step_e']['explanation'] as $line)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-check-circle text-indigo-600 mr-2"></i>
                            <span class="text-gray-800 dark:text-gray-200">{{ $line }}</span>
                        </div>
                        @endforeach
                    </div>

                    <!-- Assigned Teams -->
                    <div class="bg-white dark:bg-gray-900 rounded p-4">
                        <h6 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Assigned Teams:</h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($client['teams'] as $team)
                            <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded p-3 border border-indigo-200 dark:border-indigo-800">
                                <div class="font-semibold text-indigo-900 dark:text-indigo-300 mb-1">Team {{ $team['team_index'] }}</div>
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ implode(', ', $team['members']) }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $team['task_count'] }} tasks</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Latest Optimization Run - Featured Card -->
        @if($latestRun)
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-xl shadow-lg p-8 mb-8 border border-indigo-100 dark:border-gray-600">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Latest Optimization Run</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $latestRun['service_date'] }} • Created: {{ $latestRun['created_at'] }}</p>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $latestRun['is_saved'] ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' }}">
                    {{ $latestRun['is_saved'] ? 'Saved' : 'Unsaved' }}
                </span>
            </div>

            <!-- Evaluation Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Fitness Rate -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase">Fitness Rate</h4>
                        @if($latestRun['is_optimal'])
                            <span class="text-green-500 dark:text-green-400"><i class="fas fa-check-circle"></i></span>
                        @endif
                    </div>
                    <p class="text-4xl font-bold {{ $latestRun['is_optimal'] ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400' }}">
                        {{ $latestRun['fitness_rate'] }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Evaluates schedule quality by calculating 1/(1.0×Conflicts+1)
                    </p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-4">
                        <div class="h-2 rounded-full {{ $latestRun['is_optimal'] ? 'bg-green-500 dark:bg-green-400' : 'bg-blue-500 dark:bg-blue-400' }}"
                             style="width: {{ $latestRun['fitness_rate'] * 100 }}%"></div>
                    </div>
                    @if($latestRun['is_optimal'])
                        <p class="text-sm text-green-600 dark:text-green-400 font-semibold mt-2">✓ Optimal (Conflict-free)</p>
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ round((1 - $latestRun['fitness_rate']) * 100, 1) }}% conflicts remaining</p>
                    @endif
                </div>

                <!-- Convergence Rate -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase">Convergence Rate</h4>
                        <span class="text-indigo-500 dark:text-indigo-400"><i class="fas fa-chart-line"></i></span>
                    </div>
                    <p class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">
                        {{ $latestRun['convergence_rate'] }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Total generations required to reach best fitness
                    </p>
                    <div class="mt-4 flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="h-2 rounded-full bg-indigo-500 dark:bg-indigo-400"
                                 style="width: {{ min(100, ($latestRun['convergence_rate'] / 100) * 100) }}%"></div>
                        </div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">of 100 max</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        {{ $latestRun['convergence_rate'] < 50 ? 'Fast convergence ⚡' : ($latestRun['convergence_rate'] < 80 ? 'Normal convergence' : 'Slow convergence') }}
                    </p>
                </div>

                <!-- Runtime -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase">Runtime</h4>
                        <span class="text-orange-500 dark:text-orange-400"><i class="fas fa-clock"></i></span>
                    </div>
                    <p class="text-4xl font-bold text-orange-600 dark:text-orange-400">
                        {{ $latestRun['runtime_formatted'] }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Total time to complete optimization process
                    </p>
                    <div class="mt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Tasks processed:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $latestRun['total_tasks'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Teams formed:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $latestRun['total_teams'] }}</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        ≈ {{ round($latestRun['runtime'] / $latestRun['total_tasks'], 2) }}s per task
                    </p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-12 text-center border border-gray-200 dark:border-gray-700">
            <i class="fas fa-chart-bar text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400">No optimization runs found. Create tasks and run optimization to see metrics.</p>
        </div>
        @endif

        <!-- Fitness Calculation Breakdown -->
        @if($fitnessBreakdown)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-8 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-calculator text-indigo-600 mr-2"></i>
                        Fitness Calculation Breakdown
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Detailed fitness scoring for selected client</p>
                </div>

                <!-- Client Dropdown for Fitness -->
                @if(count($availableFitnessClients) > 1)
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Filter by Client:</label>
                    <select wire:model.live="selectedFitnessClientId"
                            class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach($availableFitnessClients as $clientName)
                            <option value="{{ $clientName }}">{{ $clientName }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <!-- Fitness Formula Explanation -->
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-gray-700 dark:to-gray-700 rounded-lg p-6 mb-6">
                <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-4">How Fitness is Calculated</h4>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 font-mono text-center text-lg mb-4">
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">Fitness</span> =
                    <span class="text-green-600 dark:text-green-400">(1 / (1 + StdDev))</span> -
                    <span class="text-red-600 dark:text-red-400">Penalties</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-green-50 dark:bg-green-900/20 rounded p-3 border border-green-200 dark:border-green-800">
                        <div class="font-bold text-green-800 dark:text-green-300 mb-1">Base Fitness: {{ $fitnessBreakdown['base_fitness'] }}</div>
                        <div class="text-gray-700 dark:text-gray-300">Balance Score (StdDev: {{ $fitnessBreakdown['std_dev'] }})</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Lower StdDev = Better task balance</div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded p-3 border border-red-200 dark:border-red-800">
                        <div class="font-bold text-red-800 dark:text-red-300 mb-1">Total Penalty: {{ $fitnessBreakdown['total_penalty'] }}</div>
                        <div class="text-gray-700 dark:text-gray-300">Constraint Violations</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">12-hour limit & 3PM deadline</div>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded p-3 border border-blue-200 dark:border-blue-800">
                        <div class="font-bold text-blue-800 dark:text-blue-300 mb-1">Final Fitness: {{ $fitnessBreakdown['final_fitness'] }}</div>
                        <div class="text-gray-700 dark:text-gray-300">Optimization Score</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $fitnessBreakdown['final_fitness'] >= 0.999 ? 'Perfect!' : ($fitnessBreakdown['final_fitness'] >= 0.8 ? 'Good' : 'Needs improvement') }}</div>
                    </div>
                </div>
            </div>

            <!-- Team Breakdown -->
            <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-4">Team-by-Team Analysis</h4>
            <div class="space-y-4">
                @foreach($fitnessBreakdown['teams'] as $team)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h5 class="font-bold text-gray-900 dark:text-gray-100">{{ $team['team_name'] }}</h5>
                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded text-xs font-semibold">
                                    {{ $team['client_name'] }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ implode(', ', $team['members']) }}
                            </p>
                        </div>
                        @if(empty($team['penalties']))
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold">
                                <i class="fas fa-check-circle mr-1"></i> No Penalties
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-full text-sm font-semibold">
                                <i class="fas fa-exclamation-triangle mr-1"></i> -{{ $team['team_penalty'] }} Penalty
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-3">
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Tasks</div>
                            <div class="font-bold text-gray-900 dark:text-gray-100">{{ $team['task_count'] }} total</div>
                            @if($team['arrival_task_count'] > 0)
                                <div class="text-xs text-orange-600 dark:text-orange-400">{{ $team['arrival_task_count'] }} urgent</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Workload</div>
                            <div class="font-bold text-gray-900 dark:text-gray-100">{{ $team['workload_hours'] }}h</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ $team['workload_minutes'] }} min</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Travel Time</div>
                            <div class="font-bold text-blue-600 dark:text-blue-400">{{ $team['travel_time_minutes'] }} min</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">One-time</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Efficiency</div>
                            <div class="font-bold text-gray-900 dark:text-gray-100">{{ $team['efficiency'] }}×</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Team rating</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Total Hours</div>
                            <div class="font-bold {{ $team['total_hours'] > 12 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                {{ $team['total_hours'] }}h
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">w/ travel</div>
                        </div>
                    </div>

                    <!-- Penalties (if any) -->
                    @if(!empty($team['penalties']))
                        <div class="bg-red-50 dark:bg-red-900/20 rounded p-3 border border-red-200 dark:border-red-800">
                            <div class="font-semibold text-red-800 dark:text-red-300 mb-2">⚠️ Penalties Applied:</div>
                            <ul class="space-y-1">
                                @foreach($team['penalties'] as $penalty)
                                    <li class="text-sm text-gray-700 dark:text-gray-300">
                                        <strong>{{ $penalty['type'] }}:</strong> -{{ $penalty['value'] }}
                                        <span class="text-gray-600 dark:text-gray-400">({{ $penalty['details'] }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Penalty Rules Explanation -->
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6 border border-yellow-200 dark:border-yellow-800">
                <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                    Penalty Rules
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100 mb-1">🚫 12-Hour Daily Limit (All Tasks)</div>
                        <p class="text-gray-700 dark:text-gray-300">Heavy penalty (×10) if any team's total working hours (including travel) exceeds 12 hours in one day.</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1"><strong>Formula:</strong> Penalty = Overtime Hours × 10</p>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100 mb-1">⏰ 3PM Deadline (Arrival Tasks Only)</div>
                        <p class="text-gray-700 dark:text-gray-300">Moderate penalty if <strong>arrival tasks</strong> (urgent for arriving guests) cannot be finished before 3PM (900 minutes).</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1"><strong>Formula:</strong> Penalty = Over-deadline Minutes × 0.005</p>
                    </div>
                </div>
                <div class="mt-4 bg-white dark:bg-gray-800 rounded p-3">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <strong>Note:</strong> Regular cleaning tasks (non-arrival) are NOT subject to the 3PM deadline. They only need to stay within the 12-hour working limit.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Optimization History Table -->
        @if($optimizationRuns->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Optimization History</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Recent optimization runs with evaluation metrics</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Service Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Clients</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Fitness Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Generations</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Runtime</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Tasks/Teams/Employees</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($optimizationRuns as $run)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $run['service_date'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $run['is_saved'] ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' }}">
                                    {{ $run['is_saved'] ? 'Saved' : 'Unsaved' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $run['client_count'] }} {{ $run['client_count'] > 1 ? 'Clients' : 'Client' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold {{ $run['is_optimal'] ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-gray-100' }}">
                                        {{ $run['fitness_rate'] }}
                                    </span>
                                    @if($run['is_optimal'])
                                        <i class="fas fa-check-circle text-green-500 dark:text-green-400 text-xs"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $run['convergence_rate'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $run['runtime_formatted'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $run['total_tasks'] }} / {{ $run['total_teams'] }} / {{ $run['total_employees'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $run['created_at'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Evaluation Metrics Legend -->
        <div class="mt-8 bg-blue-50 dark:bg-gray-800 rounded-lg p-6 border border-blue-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📊 Evaluation Metrics Explanation</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">Fitness Rate</h4>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Evaluates the quality of the best schedule in a generation by calculating <code class="bg-blue-100 dark:bg-gray-700 px-1 rounded">1/(1.0×Conflicts+1)</code>.
                        The maximum rate of <strong>1.0</strong> confirms a conflict-free, optimal schedule.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-indigo-900 dark:text-indigo-300 mb-2">Convergence Rate</h4>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Assesses how quickly the algorithm finds the optimal solution. Measured by the total number of generations (iterations) required for the Fitness Rate to reach 1.0.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-orange-900 dark:text-orange-300 mb-2">Runtime</h4>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Measures the practical speed of the algorithm. Recorded as the total time in seconds required to complete the process and achieve a Fitness Rate of 1.0.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>