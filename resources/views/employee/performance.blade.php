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
                    'action_onclick' => "openTaskModal({$index})",
                    'action_label' => 'View Details',
                    // Store full task details for modal
                    'modal_data' => [
                        'client' => $task['name'],
                        'service_type' => $task['subtitle'],
                        'service_date' => $task['due_date'] ? \Carbon\Carbon::parse($task['due_date'])->format('M d, Y') : 'N/A',
                        'service_time' => $task['due_time'] ?? 'N/A',
                        'start_date' => $task['start'] ? \Carbon\Carbon::parse($task['start'])->format('M d, Y') : 'N/A',
                        'end_date' => $task['end'] ? \Carbon\Carbon::parse($task['end'])->format('M d, Y') : 'N/A',
                        'team_name' => $task['team_name'] ?? 'N/A',
                        'team_members' => $task['team_members'] ?? []
                    ]
                ];
            })->toArray();
        @endphp

        <div class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg" x-data="{
            showTaskModal: false,
            selectedTask: null,
            taskDetails: @js($completedTasksFormatted),

            openTaskModal(taskId) {
                this.selectedTask = this.taskDetails[taskId];
                this.showTaskModal = true;
                document.body.style.overflow = 'hidden';
            },

            closeTaskModal() {
                this.showTaskModal = false;
                document.body.style.overflow = 'auto';
            }
        }">
            <x-employee-components.task-overview-list
                :items="$completedTasksFormatted"
                fixedHeight="20rem"
                maxHeight="24rem"
                emptyTitle="No completed tasks yet"
                emptyMessage="Your completed tasks will appear here once you finish them." />

            <!-- Task Details Modal -->
            <div x-show="showTaskModal" x-cloak @click="closeTaskModal()"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 dark:bg-black/80 p-4 sm:p-8"
                style="display: none;">
                <div @click.stop
                    class="relative bg-white w-1/3 dark:bg-slate-800 rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-700"
                    x-show="showTaskModal" x-transition>

                    <!-- Close button -->
                    <button type="button" @click="closeTaskModal()"
                        class="absolute top-4 right-4 sm:top-5 sm:right-5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-800 rounded-lg p-1 z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Modal Content -->
                    <div class="p-6 sm:p-8">
                        <!-- Header -->
                        <div class="my-6 text-center w-full">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Task Details</h2>
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                                <span class="px-3 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 font-semibold">
                                    Completed
                                </span>
                            </div>
                        </div>

                        <!-- Task Info Card -->
                        <template x-if="selectedTask">
                            <div class="space-y-6 px-12 py-8 pt-0">
                                <!-- Service Details Section -->
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Service Details</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">View the details of the service availed for this appointment</p>

                                    <div class="space-y-3">
                                        <!-- Client -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Client</span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="selectedTask?.modal_data?.client"></span>
                                        </div>

                                        <!-- Service Type -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Service Type</span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="selectedTask?.modal_data?.service_type"></span>
                                        </div>

                                        <!-- Service Date -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Service Date</span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="selectedTask?.modal_data?.service_date"></span>
                                        </div>

                                        <!-- Service Time -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Service Time (Due at)</span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="selectedTask?.modal_data?.service_time"></span>
                                        </div>
                                        <!-- Team Assigned -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Team Assigned</span>
                                    <p class="text-sm text-white dark:text-gray-400" x-text="selectedTask?.modal_data?.team_name"></p>

                                    <!-- Team Members Avatars -->
                                    <template x-if="selectedTask?.modal_data?.team_members && selectedTask.modal_data.team_members.length > 0">
                                        <div class="flex -space-x-2">
                                            <template x-for="(member, index) in selectedTask.modal_data.team_members.slice(0, 4)" :key="member.name">
                                                <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 border-2 border-white dark:border-gray-800 flex items-center justify-center text-sm font-semibold text-gray-700 dark:text-gray-200"
                                                     :title="member.name"
                                                     x-text="member.name.split(' ').map(n => n[0]).join('').substring(0, 2)">
                                                </div>
                                            </template>
                                            <template x-if="selectedTask.modal_data.team_members.length > 4">
                                                <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-semibold text-gray-700 dark:text-gray-300"
                                                     x-text="'+' + (selectedTask.modal_data.team_members.length - 4)">
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cleaning Duration Section -->
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Cleaning Duration</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Actual start and end time of cleaning</p>

                                    <div class="space-y-3">
                                        <!-- Cleaning Start -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Start</span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="selectedTask?.modal_data?.start_date"></span>
                                        </div>

                                        <!-- Cleaning End -->
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">End</span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="selectedTask?.modal_data?.end_date"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
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