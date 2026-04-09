<x-layouts.general-employee :title="'Performance'">
    <x-skeleton-page :preset="'performance'">

    {{-- MOBILE LAYOUT (< 1024px) - Hidden on large screens --}}
    <div class="lg:hidden">
        @include('employee.mobile.performance')
    </div>

    {{-- DESKTOP LAYOUT (≥ 1024px) - Hidden on small screens --}}
    <div class="hidden lg:block space-y-6 p-16">

        @php
            $totalTasks = $totalTasksCompleted + $incompleteTasks + $pendingTasks;
            $completionRate = $totalTasks > 0 ? round(($totalTasksCompleted / $totalTasks) * 100) : 0;
        @endphp

        <!-- KPI Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-employee-components.kpi-stat-card
                label="Tasks Completed"
                :value="(string)$totalTasksCompleted"
                icon="fas fa-check-circle"
                :trend="$completionRate . '%'"
                :trendUp="$completionRate > 50"
            />
            <x-employee-components.kpi-stat-card
                label="In Progress"
                :value="(string)$incompleteTasks"
                icon="fas fa-spinner"
                trend="Active"
            />
            <x-employee-components.kpi-stat-card
                label="Pending Tasks"
                :value="(string)$pendingTasks"
                icon="fas fa-hourglass-half"
                trend="Queued"
            />
            <x-employee-components.kpi-stat-card
                label="Attendance Rate"
                :value="collect($attendanceData)->firstWhere('label', 'Present')['current'] . '/' . collect($attendanceData)->firstWhere('label', 'Present')['total']"
                icon="fas fa-calendar-check"
                :trend="(collect($attendanceData)->firstWhere('label', 'Present')['total'] > 0 ? round((collect($attendanceData)->firstWhere('label', 'Present')['current'] / collect($attendanceData)->firstWhere('label', 'Present')['total']) * 100) : 0) . '%'"
                :trendUp="true"
            />
        </div>

        <!-- Performance Line Graph (Full Width) -->
        <div class="flex flex-row justify-between w-full items-start">
            <div>
                <x-labelwithvalue label="My Performance" count="" />
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Track your work hours and productivity over time</p>
            </div>
            @php
                $timeOptions = ['All', 'Today', 'Yesterday', 'Last 7 days', 'Last 30 days'];
            @endphp
            <div x-data="{ open: false, selected: '{{ $period }}' }" class="relative inline-block">
                <button @click="open = !open" type="button"
                    class="bg-white border border-gray-200 hover:bg-blue-100 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-3 inline-flex justify-between items-center gap-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-blue-800 transition-all duration-300">
                    <span class="text-gray-700 dark:text-white text-xs font-normal">Show:</span>
                    <span class="text-gray-700 dark:text-white text-xs font-normal" x-text="selected"></span>
                    <svg class="w-2.5 h-2.5 ms-2 transition-transform duration-300 text-gray-700 dark:text-white" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 top-full mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-lg min-w-[10rem] dark:bg-gray-700 origin-top" style="display: none;">
                    <ul class="py-2 text-xs text-gray-700 dark:text-white">
                        @foreach ($timeOptions as $option)
                            <li>
                                <button @click="window.location.href='{{ route('employee.performance') }}?period={{ urlencode($option) }}'" type="button"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                    :class="{ 'bg-gray-100 dark:bg-gray-600': selected === '{{ $option }}' }">
                                    {{ $option }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="w-full rounded-lg h-80">
            <x-linechart title="Hours Worked" :currentValue="$performanceData['currentValue']"
                :changeValue="$performanceData['changeValue']" :changePercent="$performanceData['changePercent']"
                :chartData="$performanceData['values']" :chartLabels="$performanceData['labels']" chartColor="#8b5cf6"
                gradientStart="rgba(139, 92, 246, 0.2)" gradientEnd="rgba(139, 92, 246, 0)"
                :dateRange="$performanceData['dateRange']" />
        </div>

        <!-- Bottom Section: Recently Completed Tasks + Task Efficiency -->
        <div class="flex flex-row gap-6 items-stretch">
            <!-- Left: Recently Completed Task List -->
            <div class="flex flex-col gap-6 flex-1">
                <div>
                    <x-labelwithvalue label="Recently Completed Tasks" :count="'(' . $recentlyCompletedTasks->count() . ')'" />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tasks you have finished recently</p>
                </div>

                @php
                    $completedTasksFormatted = $recentlyCompletedTasks->map(function($task, $index) {
                        return [
                            'id' => $index,
                            'service' => $task['subtitle'],
                            'status' => 'Completed',
                            'description' => $task['name'],
                            'service_date' => $task['due_date'] ? \Carbon\Carbon::parse($task['due_date'])->format('M d, Y') : null,
                            'service_time' => $task['due_time'] ?? null,
                            'action_onclick' => "window.location.href='" . route('employee.tasks.show', ['task' => $task['task_id'], 'from' => 'performance']) . "'",
                            'action_label' => 'View Details',
                        ];
                    })->toArray();
                @endphp

                    <x-employee-components.task-overview-list
                        :items="$completedTasksFormatted"
                        fixedHeight="auto"
                        maxHeight="17.5rem"
                        emptyTitle="No completed tasks yet"
                        emptyMessage="Your completed tasks will appear here once you finish them." />
            </div>

            <!-- Right: Task Efficiency -->
            <div class="flex flex-col gap-6 w-full lg:w-1/3 [&>div:last-child]:flex-1">
                {{-- ✅ STAGE 3: Employee Efficiency Score --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Your Efficiency Score</p>
                    @php
                        $effPercent = round(($employee->efficiency ?? 1.0) * 100);
                        $effColor = $effPercent >= 90 ? 'text-green-500' : ($effPercent >= 75 ? 'text-yellow-500' : 'text-red-500');
                        $effBg = $effPercent >= 90 ? 'bg-green-500' : ($effPercent >= 75 ? 'bg-yellow-500' : 'bg-red-500');
                    @endphp
                    <div class="flex items-end gap-2 mb-3">
                        <span class="text-3xl font-bold {{ $effColor }}">{{ $effPercent }}%</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500 mb-1">
                            @if($effPercent >= 90) On track @elseif($effPercent >= 75) Needs improvement @else Below standard @endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="{{ $effBg }} h-2 rounded-full transition-all duration-500" style="width: {{ $effPercent }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2">
                        Updated automatically after each completed task based on your checklist performance.
                    </p>
                </div>

                <div>
                    <x-labelwithvalue label="Task Efficiency" count="" />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Overall task performance metrics</p>
                </div>
                <x-employee-components.task-efficiency
                    :items="[
                        ['label' => 'Completion Rate', 'current' => $totalTasksCompleted, 'total' => $totalTasks ?: 1, 'color' => 'green'],
                        ['label' => 'In Progress', 'current' => $incompleteTasks, 'total' => $totalTasks ?: 1, 'color' => 'yellow'],
                        ['label' => 'Pending', 'current' => $pendingTasks, 'total' => $totalTasks ?: 1, 'color' => 'blue'],
                        ['label' => 'Attendance', 'current' => collect($attendanceData)->firstWhere('label', 'Present')['current'], 'total' => collect($attendanceData)->firstWhere('label', 'Present')['total'] ?: 1, 'color' => 'indigo'],
                    ]"
                />
            </div>
        </div>

        {{-- Performance Evaluation & PIPs Section --}}
        @if ($latestEvaluation || ($activePips && $activePips->count() > 0))
            <div class="mt-6 space-y-6">
                {{-- Active PIPs --}}
                @if ($activePips && $activePips->count() > 0)
                    @foreach ($activePips as $pip)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-red-500">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Performance Improvement Plan</h3>
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                    {{ $pip->isOverdue() ? 'OVERDUE' : 'ACTIVE' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $pip->description }}</p>

                            {{-- Timeline --}}
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-4">
                                <span>{{ $pip->start_date->format('M d') }} - {{ $pip->end_date->format('M d, Y') }}</span>
                                <span>{{ $pip->end_date->isPast() ? 'Overdue by ' . $pip->end_date->diffInDays(now()) . ' days' : $pip->end_date->diffInDays(now()) . ' days remaining' }}</span>
                            </div>

                            {{-- Progress --}}
                            <div class="mb-4">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-500 dark:text-gray-400">Progress</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $pip->getProgressPercentage() }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pip->getProgressPercentage() }}%"></div>
                                </div>
                            </div>

                            {{-- Areas --}}
                            <div class="mb-4">
                                <p class="text-xs font-semibold text-red-600 dark:text-red-400 mb-2">Areas to Improve</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($pip->areas_to_improve ?? [] as $area)
                                        <span class="px-2 py-1 text-xs bg-red-50 dark:bg-red-900/10 text-red-600 dark:text-red-400 rounded">{{ $area['area'] }}</span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Action Items --}}
                            <div>
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Action Items</p>
                                <div class="space-y-2">
                                    @foreach ($pip->action_items ?? [] as $item)
                                        <div class="flex items-start gap-2">
                                            <div class="mt-0.5">
                                                @if (($item['status'] ?? 'pending') === 'completed')
                                                    <i class="fi fi-sr-check-circle text-green-500 text-xs"></i>
                                                @else
                                                    <i class="fi fi-rr-circle text-gray-400 text-xs"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-700 dark:text-gray-300 {{ ($item['status'] ?? '') === 'completed' ? 'line-through opacity-60' : '' }}">
                                                    {{ $item['description'] }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-0.5">Target: {{ \Carbon\Carbon::parse($item['target_date'])->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Latest Evaluation Summary --}}
                @if ($latestEvaluation)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Latest Evaluation</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $latestEvaluation->evaluation_period_start->format('F Y') }}</span>
                        </div>

                        <div class="flex items-baseline gap-2 mb-4">
                            @php $color = $latestEvaluation->getRatingColor(); @endphp
                            <span class="text-3xl font-bold text-{{ $color }}-600 dark:text-{{ $color }}-400">{{ number_format($latestEvaluation->overall_rating, 1) }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">/ 5.0</span>
                            <span class="text-xs font-medium text-{{ $color }}-600 dark:text-{{ $color }}-400 ml-2">{{ $latestEvaluation->getRatingLabel() }}</span>
                        </div>

                        {{-- Score bars --}}
                        <div class="space-y-3">
                            @foreach (\App\Models\PerformanceEvaluation::CRITERIA as $field => $label)
                                @php $score = $latestEvaluation->$field ?? 0; @endphp
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 w-28">{{ $label }}</span>
                                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $score >= 4 ? 'bg-green-500' : ($score >= 3 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                            style="width: {{ ($score / 5) * 100 }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 w-5 text-right">{{ $score }}</span>
                                </div>
                            @endforeach
                        </div>

                        @if ($latestEvaluation->strengths)
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs font-semibold text-green-600 dark:text-green-400 mb-1">Strengths</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $latestEvaluation->strengths }}</p>
                            </div>
                        @endif

                        @if ($latestEvaluation->goals_for_next_period)
                            <div class="mt-3">
                                <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 mb-1">Goals for Next Period</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $latestEvaluation->goals_for_next_period }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
    </x-skeleton-page>

</x-layouts.general-employee>