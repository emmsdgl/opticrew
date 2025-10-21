<div>
    <header class="bg-white shadow-sm">
        <div class="px-8 py-4">
            <h2 class="text-2xl font-bold text-gray-800">Optimization Analytics & Evaluation Metrics</h2>
            <p class="text-sm text-gray-600 mt-1">Performance metrics for Genetic Algorithm optimization runs</p>
        </div>
    </header>

    <div class="p-8">
        <!-- Latest Optimization Run - Featured Card -->
        @if($latestRun)
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-lg p-8 mb-8 border border-indigo-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Latest Optimization Run</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $latestRun['service_date'] }} • Created: {{ $latestRun['created_at'] }}</p>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $latestRun['is_saved'] ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $latestRun['is_saved'] ? 'Saved' : 'Unsaved' }}
                </span>
            </div>

            <!-- Evaluation Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Fitness Rate -->
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-600 uppercase">Fitness Rate</h4>
                        @if($latestRun['is_optimal'])
                            <span class="text-green-500"><i class="fas fa-check-circle"></i></span>
                        @endif
                    </div>
                    <p class="text-4xl font-bold {{ $latestRun['is_optimal'] ? 'text-green-600' : 'text-blue-600' }}">
                        {{ $latestRun['fitness_rate'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        Evaluates schedule quality by calculating 1/(1.0×Conflicts+1)
                    </p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                        <div class="h-2 rounded-full {{ $latestRun['is_optimal'] ? 'bg-green-500' : 'bg-blue-500' }}"
                             style="width: {{ $latestRun['fitness_rate'] * 100 }}%;"></div>
                    </div>
                    @if($latestRun['is_optimal'])
                        <p class="text-sm text-green-600 font-semibold mt-2">✓ Optimal (Conflict-free)</p>
                    @else
                        <p class="text-sm text-gray-600 mt-2">{{ round((1 - $latestRun['fitness_rate']) * 100, 1) }}% conflicts remaining</p>
                    @endif
                </div>

                <!-- Convergence Rate -->
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-600 uppercase">Convergence Rate</h4>
                        <span class="text-indigo-500"><i class="fas fa-chart-line"></i></span>
                    </div>
                    <p class="text-4xl font-bold text-indigo-600">
                        {{ $latestRun['convergence_rate'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        Total generations required to reach best fitness
                    </p>
                    <div class="mt-4 flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full bg-indigo-500"
                                 style="width: {{ min(100, ($latestRun['convergence_rate'] / 100) * 100) }}%;"></div>
                        </div>
                        <span class="text-xs text-gray-600">of 100 max</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        {{ $latestRun['convergence_rate'] < 50 ? 'Fast convergence ⚡' : ($latestRun['convergence_rate'] < 80 ? 'Normal convergence' : 'Slow convergence') }}
                    </p>
                </div>

                <!-- Runtime -->
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-600 uppercase">Runtime</h4>
                        <span class="text-orange-500"><i class="fas fa-clock"></i></span>
                    </div>
                    <p class="text-4xl font-bold text-orange-600">
                        {{ $latestRun['runtime_formatted'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        Total time to complete optimization process
                    </p>
                    <div class="mt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tasks processed:</span>
                            <span class="font-semibold">{{ $latestRun['total_tasks'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Teams formed:</span>
                            <span class="font-semibold">{{ $latestRun['total_teams'] }}</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        ≈ {{ round($latestRun['runtime'] / $latestRun['total_tasks'], 2) }}s per task
                    </p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-gray-50 rounded-lg p-12 text-center">
            <i class="fas fa-chart-bar text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">No optimization runs found. Create tasks and run optimization to see metrics.</p>
        </div>
        @endif

        <!-- Optimization History Table -->
        @if($optimizationRuns->isNotEmpty())
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Optimization History</h3>
                <p class="text-sm text-gray-600">Recent optimization runs with evaluation metrics</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Fitness Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Generations</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Runtime</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tasks/Teams</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($optimizationRuns as $run)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $run['service_date'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $run['is_saved'] ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $run['is_saved'] ? 'Saved' : 'Unsaved' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold {{ $run['is_optimal'] ? 'text-green-600' : 'text-gray-900' }}">
                                        {{ $run['fitness_rate'] }}
                                    </span>
                                    @if($run['is_optimal'])
                                        <i class="fas fa-check-circle text-green-500 text-xs"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $run['convergence_rate'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $run['runtime_formatted'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $run['total_tasks'] }} / {{ $run['total_teams'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $run['created_at'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Evaluation Metrics Legend -->
        <div class="mt-8 bg-blue-50 rounded-lg p-6 border border-blue-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Evaluation Metrics Explanation</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">Fitness Rate</h4>
                    <p class="text-sm text-gray-700">
                        Evaluates the quality of the best schedule in a generation by calculating <code class="bg-blue-100 px-1 rounded">1/(1.0×Conflicts+1)</code>.
                        The maximum rate of <strong>1.0</strong> confirms a conflict-free, optimal schedule.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-indigo-900 mb-2">Convergence Rate</h4>
                    <p class="text-sm text-gray-700">
                        Assesses how quickly the algorithm finds the optimal solution. Measured by the total number of generations (iterations) required for the Fitness Rate to reach 1.0.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-orange-900 mb-2">Runtime</h4>
                    <p class="text-sm text-gray-700">
                        Measures the practical speed of the algorithm. Recorded as the total time in seconds required to complete the process and achieve a Fitness Rate of 1.0.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>