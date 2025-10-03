<div>
    <header class="bg-white shadow-sm">
        <div class="px-8 py-4">
            <h2 class="text-2xl font-bold text-gray-800">Algorithm Performance Simulation</h2>
        </div>
    </header>

    <div class="p-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-6">
                <button wire:click="runSimulation" wire:loading.attr="disabled" class="bg-indigo-600 text-white font-bold px-8 py-3 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                    <span wire:loading.remove>Run 10 Simulations</span>
                    <span wire:loading>Running...</span>
                </button>
                <p class="text-sm text-gray-500 mt-2">This will run the optimization algorithm 10 times on a random set of 20 tasks to measure its performance and consistency.</p>
            </div>

            @if (!empty($simulationResults))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Fitness Value -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-bold text-gray-600">Fitness Value</h4>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($simulationResults['fitness_value'], 4) }}</p>
                    <p class="text-sm text-gray-500">Highest fitness score achieved across all runs (closer to 1 is better).</p>
                </div>
                <!-- Convergence Rate -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-bold text-gray-600">Convergence Rate</h4>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($simulationResults['convergence_rate'], 4) }}</p>
                    <p class="text-sm text-gray-500">Average fitness score. Shows the algorithm's typical performance.</p>
                </div>
                <!-- Robustness -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-bold text-gray-600">Robustness (Std. Dev)</h4>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($simulationResults['robustness'], 4) }}</p>
                    <p class="text-sm text-gray-500">Standard deviation of fitness. A low value means the algorithm is consistent.</p>
                </div>
                <!-- Solution Quality -->
                <div class="md:col-span-2 lg:col-span-3 bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-bold text-gray-600 mb-2">Solution Quality (Sample Run)</h4>
                    <dl class="grid grid-cols-3 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Workforce Cost</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $simulationResults['solution_quality']['total_workforce_cost'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Task Completion Rate</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $simulationResults['solution_quality']['task_completion_rate'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Workload Balance (Std. Dev)</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $simulationResults['solution_quality']['workload_balance'] }}</dd>
                        </div>
                    </dl>
                </div>
                <!-- Prediction Accuracy -->
                <div class="md:col-span-2 lg:col-span-3 bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-bold text-gray-600 mb-2">Prediction Accuracy (Future Work)</h4>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Mean Absolute Error (MAE)</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $simulationResults['prediction_accuracy']['mae'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Root Mean Square Error (RMSE)</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $simulationResults['prediction_accuracy']['rmse'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>