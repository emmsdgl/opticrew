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
            <x-dropdown :options="$timeOptions" id="dropdown-time" :default="$period" />
        </div>

        <div class="w-full rounded-lg h-80">
            <x-linechart title="Hours Worked" :currentValue="$performanceData['currentValue']"
                :changeValue="$performanceData['changeValue']" :changePercent="$performanceData['changePercent']"
                :chartData="$performanceData['values']" :chartLabels="$performanceData['labels']" chartColor="#8b5cf6"
                gradientStart="rgba(139, 92, 246, 0.2)" gradientEnd="rgba(139, 92, 246, 0)"
                :dateRange="$performanceData['dateRange']" />
        </div>

        <!-- Bottom Section: Recently Completed Tasks + Task Efficiency -->
        <div class="flex flex-row gap-6">
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
            <div class="flex flex-col gap-6 w-full lg:w-1/3">
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
    </div>
    </x-skeleton-page>
</x-layouts.general-employee>

@push('scripts')
<script>
    // Handle time period dropdown changes
    document.addEventListener('alpine:init', () => {
        // Watch for dropdown selection changes
        const dropdownElement = document.querySelector('#dropdown-time');
        if (dropdownElement) {
            dropdownElement.addEventListener('click', function(e) {
                const button = e.target.closest('button[type="button"]');
                if (button && button.hasAttribute('@click') && button.textContent.trim()) {
                    // Extract the period from button text
                    const period = button.textContent.trim();
                    // Ignore the dropdown toggle button
                    if (!button.querySelector('svg')) {
                        // Reload page with new period parameter
                        setTimeout(() => {
                            window.location.href = '{{ route("employee.performance") }}?period=' + encodeURIComponent(period);
                        }, 100);
                    }
                }
            });
        }
    });
</script>
@endpush