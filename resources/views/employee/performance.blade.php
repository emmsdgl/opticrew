<x-layouts.general-employee :title="'Performance'">

    {{-- MOBILE LAYOUT (< 1024px) - Hidden on large screens --}}
    <div class="lg:hidden">
        @include('employee.mobile.performance')
    </div>

    {{-- DESKTOP LAYOUT (≥ 1024px) - Hidden on small screens --}}
    <div class="hidden lg:flex lg:flex-row gap-6 w-full">
    <!-- Left Panel - Charts -->
    <div
        class="flex flex-col gap-6 flex-1 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
        <!-- Inner Up - Performance Line Graph -->
        <div class="flex flex-row justify-between w-full">
            <x-labelwithvalue label="My Performance" count="" />
            @php
                $timeOptions = ['All', 'Today', 'Yesterday', 'Last 7 days', 'Last 30 days'];
            @endphp
            <x-dropdown :options="$timeOptions" id="dropdown-time" :default="$period" />
        </div>


        <div
            class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-80 sm:h-56 md:h-64 lg:h-80">
            <!-- Line Chart Component -->
            <x-linechart title="Hours Worked" :currentValue="$performanceData['currentValue']"
                :changeValue="$performanceData['changeValue']" :changePercent="$performanceData['changePercent']"
                :chartData="$performanceData['values']" :chartLabels="$performanceData['labels']" chartColor="#8b5cf6"
                gradientStart="rgba(139, 92, 246, 0.2)" gradientEnd="rgba(139, 92, 246, 0)"
                :dateRange="$performanceData['dateRange']" />
        </div>

        <!-- Inner Bottom - Recently Completed Task List -->
        <x-labelwithvalue label="Recently Completed Tasks" :count="'(' . $recentlyCompletedTasks->count() . ')'" />

        @php
            // Transform recently completed tasks to the format expected by task-overview-list component
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

        <div class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
            <x-employee-components.task-overview-list
                :items="$completedTasksFormatted"
                fixedHeight="20rem"
                maxHeight="24rem"
                emptyTitle="No completed tasks yet"
                emptyMessage="Your completed tasks will appear here once you finish them." />
        </div>
    </div>

    <!-- Right Panel - Performance Summary -->
    <div
        class="flex flex-col gap-6 w-full lg:w-1/3 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">

        <!-- Inner Up - Performance KPI Cards Summary -->
        <div
            class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-fit sm:h-fit md:h-fit">
            <x-profilesummary title="" :cards="[
        [
            'label' => 'Total Tasks Completed',
            'amount' => (string)$totalTasksCompleted,
            'description' => 'Great job on completing tasks!',
            'icon' => '<i class=&quot;fas fa-check-circle&quot;></i>',
            'percentage' => '',
            'percentageColor' => '#10b981',
            'bgColor' => '#fef3c7',
        ],
        [
            'label' => 'Incomplete Tasks',
            'amount' => (string)$incompleteTasks,
            'description' => 'Tasks in progress',
            'icon' => '<i class=&quot;fas fa-spinner&quot;></i>',
            'percentage' => '',
            'percentageColor' => '#f59e0b',
        ],
        [
            'label' => 'Pending Tasks',
            'amount' => (string)$pendingTasks,
            'description' => 'Scheduled tasks awaiting',
            'icon' => '<i class=&quot;fas fa-hourglass-half&quot;></i>',
            'percentage' => '',
            'percentageColor' => '#3b82f6',
        ],
    ]" />
        </div>

        <!-- Inner Down - Attendance Summary -->
        <div
            class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-full sm:h-full md:h-full overflow-y-auto">

            <div class="w-full flex flex-col h-full justify-center align-items-center">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg justify-center align-items-center h-fit p-6 mr-4 ml-4">
                    <div class="space-y-6 pr-6 pl-6">
                        @foreach($attendanceData as $data)
                            <x-progressdetails :label="$data['label']" :current="$data['current']" :total="$data['total']"
                                :color="$data['color']" />
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
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