<div>
    <header class="bg-white shadow-sm">
        <div class="px-8 py-4">
            <h2 class="text-2xl font-bold text-gray-800">Employee Performance Analytics</h2>
        </div>
    </header>

    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($employees as $employee)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-800">{{ $employee->full_name }}</h3>

                    <!-- Skills -->
                    <div class="mt-2">
                        <span class="text-sm font-semibold text-gray-600">Skills:</span>
                        <span class="text-sm text-gray-800">{{ collect(json_decode($employee->skills))->join(', ') }}</span>
                    </div>

                    <!-- Days Off -->
                    <div class="mt-1">
                        <span class="text-sm font-semibold text-gray-600">Days Off (This Month):</span>
                        @if($employee->schedules->isNotEmpty())
                            <span class="text-sm text-gray-800">{{ $employee->schedules->pluck('work_date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M d'))->join(', ') }}</span>
                        @else
                            <span class="text-sm text-gray-500">None</span>
                        @endif
                    </div>

                    <hr class="my-4">

                    <!-- Work Efficiency Calculation -->
                    @php
                        $completedTasks = $employee->completedTasks;
                        $totalTasks = $completedTasks->count(); // Count all completed tasks
                        $avgDifference = 0;
                        $efficiencyScore = 100;

                        if ($totalTasks > 0) {
                            $totalDifference = 0;
                            $totalEfficiencyRatio = 0;
                            $tasksWithHistory = 0;

                            foreach($completedTasks as $task) {
                                if ($task->performanceHistory) {
                                    $history = $task->performanceHistory;
                                    $totalDifference += ($history->estimated_duration_minutes - $history->actual_duration_minutes);
                                    if ($history->actual_duration_minutes > 0) {
                                        $totalEfficiencyRatio += ($history->estimated_duration_minutes / $history->actual_duration_minutes);
                                    } else {
                                        $totalEfficiencyRatio += 1;
                                    }
                                    $tasksWithHistory++;
                                }
                            }

                            if ($tasksWithHistory > 0) {
                                $avgDifference = $totalDifference / $tasksWithHistory;
                                $efficiencyScore = ($totalEfficiencyRatio / $tasksWithHistory) * 100;
                            }
                        }
                    @endphp

                    <h4 class="font-semibold text-gray-700 mb-2">Work Efficiency</h4>
                    @if ($totalTasks > 0)
                        <div class="text-center bg-gray-50 p-4 rounded-lg">
                            <p class="text-2xl font-bold {{ $avgDifference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ round(abs($avgDifference)) }} min {{ $avgDifference >= 0 ? 'early' : 'late' }}
                            </p>
                            <p class="text-sm text-gray-600">on average over {{ $totalTasks }} tasks</p>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-3">
                                <div class="h-2.5 rounded-full {{ $efficiencyScore >= 100 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min(100, $efficiencyScore) }}%"></div>
                            </div>
                            <p class="text-lg font-bold text-gray-800 mt-1">{{ round($efficiencyScore) }}%</p>
                            <p class="text-xs text-gray-500">Efficiency Score</p>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center">No historical task data available.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>