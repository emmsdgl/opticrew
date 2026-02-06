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
                showTaskDrawer: false,
                selectedTask: null,
                taskDetails: @js($tasks),

                // Checklist templates for different service types
                checklistTemplates: {
                    daily_cleaning: [
                        'Sweep and mop floors',
                        'Vacuum carpets/rugs',
                        'Dust furniture and surfaces',
                        'Wipe tables and countertops',
                        'Empty trash bins',
                        'Wipe kitchen counters',
                        'Clean sink',
                        'Wash visible dishes',
                        'Wipe appliance exteriors',
                        'Clean toilet and sink',
                        'Wipe mirrors',
                        'Mop floor',
                        'Organize cluttered areas',
                        'Light deodorizing',
                    ],
                    deep_cleaning: [
                        'Dust high and low areas (vents, corners, baseboards)',
                        'Clean behind and under furniture',
                        'Wash walls and remove stains',
                        'Deep vacuum carpets',
                        'Clean inside microwave',
                        'Degrease stove and range hood',
                        'Clean inside refrigerator (if included)',
                        'Scrub tile grout',
                        'Remove limescale and mold buildup',
                        'Deep scrub tiles and grout',
                        'Sanitize all fixtures thoroughly',
                        'Clean window interiors',
                        'Polish handles and knobs',
                        'Disinfect frequently touched surfaces',
                    ],
                    final_cleaning: [
                        'Deep clean all rooms',
                        'Clean windows inside and out',
                        'Clean all kitchen appliances',
                        'Degrease oven and stovetop',
                        'Clean inside cabinets',
                        'Scrub bathroom tiles and grout',
                        'Clean behind furniture',
                        'Dust and clean light fixtures',
                        'Clean baseboards',
                        'Vacuum and mop all floors',
                        'Clean door frames and handles',
                        'Remove all trash',
                    ],
                    general_cleaning: [
                        'Dust surfaces',
                        'Sweep/vacuum floors',
                        'Mop hard floors',
                        'Clean glass and mirrors',
                        'Wipe countertops',
                        'Clean sink',
                        'Take out trash',
                        'Clean toilet, sink, and mirror',
                        'Mop floor',
                        'Arrange items neatly',
                        'Dispose of garbage',
                        'Light air freshening',
                    ],
                },

                openTaskDrawer(taskId) {
                    this.selectedTask = this.taskDetails[taskId];
                    this.showTaskDrawer = true;
                    document.body.style.overflow = 'hidden';
                },

                closeTaskDrawer() {
                    this.showTaskDrawer = false;
                    document.body.style.overflow = 'auto';
                },

                // Alias for compatibility with task-overview-list component
                openTaskModal(taskId) {
                    this.openTaskDrawer(taskId);
                },

                closeTaskModal() {
                    this.closeTaskDrawer();
                },

                getTaskStatus() {
                    return this.selectedTask?.modal_data?.status || '';
                },

                // Get checklist items based on service type
                getChecklistItems() {
                    const serviceType = this.selectedTask?.modal_data?.service_type || '';
                    const type = serviceType.toLowerCase();

                    if (type.includes('daily') || type.includes('routine')) {
                        return this.checklistTemplates.daily_cleaning;
                    } else if (type.includes('deep')) {
                        return this.checklistTemplates.deep_cleaning;
                    } else if (type.includes('final')) {
                        return this.checklistTemplates.final_cleaning;
                    }

                    return this.checklistTemplates.general_cleaning;
                },

                // Check if a specific checklist item is completed
                isChecklistItemCompleted(itemIndex) {
                    if (!this.selectedTask?.modal_data?.checklist_completions) return false;
                    const completions = this.selectedTask.modal_data.checklist_completions || [];
                    return completions.includes(itemIndex);
                },

                // Get checklist progress stats
                getChecklistProgress() {
                    const checklistItems = this.getChecklistItems();
                    const total = checklistItems.length;
                    const completions = this.selectedTask?.modal_data?.checklist_completions || [];
                    const completed = completions.length;
                    const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;

                    return { completed, total, percentage };
                }
            }">
                <x-labelwithvalue label="Task Overview" :count="'(' . $taskCount . ')'" />

                <div
                    class="h-72 overflow-y-auto w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
                    <x-employee-components.task-overview-list :items="$tasks" fixedHeight="18rem" maxHeight="24rem"
                        emptyTitle="No tasks this month" emptyMessage="There are no tasks scheduled for this month." />
                </div>

                <!-- Task Details Slide-in Drawer -->
                <div x-show="showTaskDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
                    <!-- Backdrop -->
                    <div x-show="showTaskDrawer"
                         x-transition:enter="transition-opacity ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click="closeTaskDrawer()"
                         class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

                    <!-- Drawer Panel -->
                    <div class="fixed inset-y-0 right-0 flex max-w-full">
                        <div x-show="showTaskDrawer"
                             x-transition:enter="transform transition ease-in-out duration-300"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-200"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             @click.stop
                             class="relative w-screen max-w-md sm:max-w-lg">

                            <!-- Drawer Content -->
                            <div class="h-full flex flex-col bg-white dark:bg-slate-800 shadow-2xl border-l border-gray-200 dark:border-slate-700">
                                <!-- Drawer Header -->
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-slate-800/50">
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Task Details</h2>
                                    <button type="button" @click="closeTaskDrawer()"
                                        class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Drawer Body (Scrollable) -->
                                <div class="flex-1 overflow-y-auto p-6" x-show="selectedTask">
                                    <!-- Status Badge -->
                                    <div class="flex items-center gap-2 mb-6">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                        <span class="px-3 py-1 text-xs rounded-full font-semibold"
                                            :class="{
                                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': getTaskStatus() === 'Completed',
                                                'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': getTaskStatus() === 'In Progress',
                                                'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': getTaskStatus() === 'Pending' || getTaskStatus() === 'Scheduled',
                                                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': !['Completed', 'In Progress', 'Pending', 'Scheduled'].includes(getTaskStatus())
                                            }"
                                            x-text="getTaskStatus()">
                                        </span>
                                    </div>

                                    <!-- Service Details Section -->
                                    <div class="mb-5">
                                        <div class="py-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Service Details</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">View the details of the service for this task</p>
                                        </div>

                                        <div class="space-y-4 text-sm py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <!-- Client -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Client</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                                    x-text="selectedTask?.modal_data?.client || '-'"></span>
                                            </div>

                                            <!-- Service Type -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Service Type</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                                    x-text="selectedTask?.modal_data?.service_type || '-'"></span>
                                            </div>

                                            <!-- Service Date -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Service Date</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                                    x-text="selectedTask?.modal_data?.service_date || '-'"></span>
                                            </div>

                                            <!-- Service Time -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Service Time (Due at)</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                                    x-text="selectedTask?.modal_data?.service_time || '-'"></span>
                                            </div>

                                            <!-- Location -->
                                            <div class="flex justify-between items-center" x-show="selectedTask?.modal_data?.location">
                                                <span class="text-gray-500 dark:text-gray-400">Location</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                                    x-text="selectedTask?.modal_data?.location || '-'"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assigned Team Section -->
                                    <div class="mb-5" x-show="selectedTask?.modal_data?.team_members && selectedTask.modal_data.team_members.length > 0">
                                        <div class="py-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Assigned Team</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3" x-text="selectedTask?.modal_data?.team_name || 'Team members assigned to this task'"></p>
                                        </div>

                                        <div class="flex items-center gap-2 flex-wrap px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <template x-for="(member, idx) in (selectedTask?.modal_data?.team_members || []).slice(0, 5)" :key="idx">
                                                <div class="relative group">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold cursor-pointer transition-transform hover:scale-110"
                                                        x-text="member.name.split(' ').map(n => n[0]).join('').substring(0, 2)"></div>
                                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-10"
                                                        x-text="member.name"></div>
                                                </div>
                                            </template>
                                            <template x-if="(selectedTask?.modal_data?.team_members?.length || 0) > 5">
                                                <button class="w-10 h-10 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-full flex items-center justify-center text-gray-400 text-sm"
                                                    x-text="'+' + (selectedTask.modal_data.team_members.length - 5)"></button>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Task Duration Section -->
                                    <div class="mb-5">
                                        <div class="py-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Task Duration</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Actual start and end time of the task</p>
                                        </div>

                                        <div class="space-y-4 text-sm py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <!-- Task Start -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Start</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                                    x-text="selectedTask?.modal_data?.start_date || 'Not started'"></span>
                                            </div>

                                            <!-- Task End -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">End</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                                    x-text="selectedTask?.modal_data?.end_date || 'Not completed'"></span>
                                            </div>

                                            <!-- Estimated Duration -->
                                            <div class="flex justify-between items-center" x-show="selectedTask?.modal_data?.estimated_duration">
                                                <span class="text-gray-500 dark:text-gray-400">Estimated Duration</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                                    x-text="selectedTask?.modal_data?.estimated_duration || '-'"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Task Checklist Section -->
                                    <div class="mb-5">
                                        <div class="py-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Task Checklist</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                                <template x-if="getTaskStatus() === 'Completed'">
                                                    <span>All tasks completed by the team</span>
                                                </template>
                                                <template x-if="getTaskStatus() === 'In Progress'">
                                                    <span>Tasks being performed for this service</span>
                                                </template>
                                                <template x-if="getTaskStatus() !== 'Completed' && getTaskStatus() !== 'In Progress'">
                                                    <span>Tasks to be performed for this service</span>
                                                </template>
                                            </p>
                                        </div>

                                        <!-- Progress indicator for in-progress tasks -->
                                        <template x-if="getTaskStatus() === 'In Progress' && getChecklistProgress().completed > 0">
                                            <div class="mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-spinner fa-spin text-blue-600 dark:text-blue-400"></i>
                                                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                                                        Task in progress
                                                    </span>
                                                </div>
                                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 ml-6">
                                                    <span x-text="getChecklistProgress().completed"></span> of <span x-text="getChecklistProgress().total"></span> checklist items completed
                                                </p>
                                            </div>
                                        </template>

                                        <!-- Completed task indicator -->
                                        <template x-if="getTaskStatus() === 'Completed'">
                                            <div class="mb-3 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
                                                    <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">
                                                        Task completed successfully
                                                    </span>
                                                </div>
                                                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 ml-6">
                                                    All checklist items have been completed
                                                </p>
                                            </div>
                                        </template>

                                        <!-- Checklist Items -->
                                        <div class="space-y-2 max-h-48 overflow-y-auto bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                                            <template x-for="(item, index) in getChecklistItems()" :key="index">
                                                <div class="flex items-center gap-2 py-1.5">
                                                    <!-- Single icon based on status -->
                                                    <i class="text-xs"
                                                        :class="{
                                                            'fa-solid fa-check-circle text-emerald-500': getTaskStatus() === 'Completed' || (getTaskStatus() === 'In Progress' && isChecklistItemCompleted(index)),
                                                            'fa-regular fa-circle text-blue-400': getTaskStatus() === 'In Progress' && !isChecklistItemCompleted(index),
                                                            'fa-regular fa-circle text-gray-400': getTaskStatus() !== 'Completed' && getTaskStatus() !== 'In Progress'
                                                        }"></i>
                                                    <span class="text-sm"
                                                        :class="{
                                                            'text-emerald-700 dark:text-emerald-300 font-medium': getTaskStatus() === 'Completed' || (getTaskStatus() === 'In Progress' && isChecklistItemCompleted(index)),
                                                            'text-gray-700 dark:text-gray-300': (getTaskStatus() === 'In Progress' && !isChecklistItemCompleted(index)) || (getTaskStatus() !== 'Completed' && getTaskStatus() !== 'In Progress')
                                                        }"
                                                        x-text="item"></span>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Progress bar -->
                                        <template x-if="getTaskStatus() === 'In Progress' || getTaskStatus() === 'Completed'">
                                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Progress</span>
                                                    <span class="text-xs font-semibold"
                                                        :class="{
                                                            'text-emerald-600 dark:text-emerald-400': getChecklistProgress().percentage === 100 || getTaskStatus() === 'Completed',
                                                            'text-blue-600 dark:text-blue-400': getChecklistProgress().percentage > 0 && getChecklistProgress().percentage < 100 && getTaskStatus() !== 'Completed',
                                                            'text-gray-600 dark:text-gray-400': getChecklistProgress().percentage === 0 && getTaskStatus() !== 'Completed'
                                                        }">
                                                        <span x-text="getTaskStatus() === 'Completed' ? '100' : getChecklistProgress().percentage"></span>% Complete
                                                    </span>
                                                </div>
                                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                    <div class="h-1.5 rounded-full transition-all duration-300"
                                                        :class="{
                                                            'bg-emerald-600': getChecklistProgress().percentage === 100 || getTaskStatus() === 'Completed',
                                                            'bg-blue-600': getChecklistProgress().percentage > 0 && getChecklistProgress().percentage < 100 && getTaskStatus() !== 'Completed',
                                                            'bg-gray-400': getChecklistProgress().percentage === 0 && getTaskStatus() !== 'Completed'
                                                        }"
                                                        :style="'width: ' + (getTaskStatus() === 'Completed' ? '100' : getChecklistProgress().percentage) + '%'"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    
                                </div>

                                <!-- Drawer Footer -->
                                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                                    <div class="flex gap-3">
                                        <button
                                            @click="closeTaskDrawer()"
                                            class="flex-1 text-sm px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
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