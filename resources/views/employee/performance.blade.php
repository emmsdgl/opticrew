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
            <x-dropdown :options="$timeOptions" id="dropdown-time" />
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
        <div
            class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-full sm:h-full md:h-full lg:h-full">
            <div class="w-full flex flex-col h-full">

                <div class="scrollbar space-y-4 h-80 overflow-auto">
                    @forelse($recentlyCompletedTasks as $task)
                        <x-gantttaskitem :label="$task['label']" :name="$task['name']" :subtitle="$task['subtitle']"
                            :color="$task['color']" :percentage="$task['percentage']" :dueDate="$task['due_date'] ?? null"
                            :dueTime="$task['due_time'] ?? null" :teamName="$task['team_name'] ?? 'Team 1'"
                            :teamMembers="$task['team_members'] ?? []" />
                    @empty
                        <div
                            class="text-center py-12 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-xl">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400 dark:text-gray-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-lg font-medium">No tasks assigned</p>
                            <p class="text-sm">Check back later for new assignments</p>
                        </div>
                    @endforelse
                </div>
            </div>
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