<x-layouts.general-employer :title="'Admin Dashboard'">
    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">

        <!-- Left Panel -->
        <div class="flex flex-col gap-6 flex-1 w-full">
            <div>
                <x-herocard :headerName="$admin->full_name ?? 'Admin'" :headerDesc="'Welcome to the admin dashboard. Track tasks and manage them in the dashboard'" :headerIcon="'hero-employer'" />
            </div>

            <p class="text-sm font-sans font-bold text-gray-800 dark:text-gray-200">
                My Calendar
            </p>
            <div class="w-full border border-dashed rounded-lg border-gray-400 dark:border-gray-700">
                <x-calendar :holidays="$holidays" />
            </div>

            <!-- Task Overview Section -->
            <div class="flex flex-col gap-4" x-data="{
                showTaskModal: false,
                selectedTask: null,
                taskDetails: @js($tasks),

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
                <x-labelwithvalue label="Task Overview" :count="'(' . $taskCount . ')'" />

                <div
                    class="h-72 overflow-y-auto w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
                    <x-employee-components.task-overview-list :items="$tasks" fixedHeight="18rem" maxHeight="24rem"
                        emptyTitle="No tasks this month" emptyMessage="There are no tasks scheduled for this month." />
                </div>

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
                                    <span class="px-3 py-1 text-xs rounded-full font-semibold" :class="{
                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': selectedTask?.modal_data?.status === 'Completed',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': selectedTask?.modal_data?.status === 'In Progress',
                                            'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': selectedTask?.modal_data?.status === 'Pending' || selectedTask?.modal_data?.status === 'Scheduled',
                                            'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': !['Completed', 'In Progress', 'Pending', 'Scheduled'].includes(selectedTask?.modal_data?.status)
                                        }" x-text="selectedTask?.modal_data?.status">
                                    </span>
                                </div>
                            </div>

                            <!-- Task Info Card -->
                            <template x-if="selectedTask">
                                <div class="space-y-6 px-12 py-8 pt-0">
                                    <!-- Service Details Section -->
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Service
                                            Details</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">View the details of the
                                            service for this task</p>

                                        <div class="space-y-3">
                                            <!-- Client -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Client</span>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white"
                                                    x-text="selectedTask?.modal_data?.client"></span>
                                            </div>

                                            <!-- Service Type -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Service
                                                    Type</span>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white"
                                                    x-text="selectedTask?.modal_data?.service_type"></span>
                                            </div>

                                            <!-- Service Date -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Service
                                                    Date</span>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white"
                                                    x-text="selectedTask?.modal_data?.service_date"></span>
                                            </div>

                                            <!-- Service Time -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Service Time (Due
                                                    at)</span>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white"
                                                    x-text="selectedTask?.modal_data?.service_time"></span>
                                            </div>

                                            <!-- Team Assigned -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Team
                                                    Assigned</span>
                                                <p class="text-sm text-white dark:text-gray-400"
                                                    x-text="selectedTask?.modal_data?.team_name"></p>

                                                <!-- Team Members Avatars -->
                                                <template
                                                    x-if="selectedTask?.modal_data?.team_members && selectedTask.modal_data.team_members.length > 0">
                                                    <div class="flex -space-x-2">
                                                        <template
                                                            x-for="(member, index) in selectedTask.modal_data.team_members.slice(0, 4)"
                                                            :key="member.name">
                                                            <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 border-2 border-white dark:border-gray-800 flex items-center justify-center text-sm font-semibold text-gray-700 dark:text-gray-200"
                                                                :title="member.name"
                                                                x-text="member.name.split(' ').map(n => n[0]).join('').substring(0, 2)">
                                                            </div>
                                                        </template>
                                                        <template
                                                            x-if="selectedTask.modal_data.team_members.length > 4">
                                                            <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-semibold text-gray-700 dark:text-gray-300"
                                                                x-text="'+' + (selectedTask.modal_data.team_members.length - 4)">
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Task Duration Section -->
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Task Duration
                                        </h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Actual start and end
                                            time of the task</p>

                                        <div class="space-y-3">
                                            <!-- Task Start -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Start</span>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white"
                                                    x-text="selectedTask?.modal_data?.start_date"></span>
                                            </div>

                                            <!-- Task End -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">End</span>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white"
                                                    x-text="selectedTask?.modal_data?.end_date"></span>
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

        <!-- Right Panel -->
        <div class="flex flex-col gap-6 w-full lg:w-1/3">
            {{-- LIVEWIRE ATTENDANCE CHART - AUTO REFRESHES --}}
            @livewire('admin.attendance-chart')

            {{-- LIVEWIRE RECENT ARRIVALS - AUTO REFRESHES --}}
            <x-labelwithvalue label="Recent Arrivals" :count="'(' . $recentArrivals->count() . ')'" />
            @livewire('admin.recent-arrivals')
        </div>
    </section>
</x-layouts.general-employer>