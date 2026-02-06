@props([
    'showVar' => 'showDrawer',
    'dataVar' => 'selectedTask',
    'closeMethod' => 'closeDrawer',
    'title' => 'Task Details',
    'showChecklist' => true,
    'showFooter' => true,
])

<!-- Task Details Slide-in Drawer -->
<div x-show="{{ $showVar }}" x-cloak class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
    <!-- Backdrop -->
    <div x-show="{{ $showVar }}"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="{{ $closeMethod }}()"
         class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

    <!-- Drawer Panel -->
    <div class="fixed inset-y-0 right-0 flex max-w-full">
        <div x-show="{{ $showVar }}"
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
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
                    <button type="button" @click="{{ $closeMethod }}()"
                        class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Drawer Body (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-6" x-show="{{ $dataVar }}">
                    <!-- Status Badge -->
                    <div class="flex items-center gap-2 mb-6">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                        <span x-show="getDrawerStatus() === 'pending'"
                            class="px-3 py-1 text-xs rounded-full bg-[#FFA50020] text-[#FFA500] font-semibold">Pending</span>
                        <span x-show="getDrawerStatus() === 'scheduled'"
                            class="px-3 py-1 text-xs rounded-full bg-[#3B82F620] text-[#3B82F6] font-semibold">Scheduled</span>
                        <span x-show="getDrawerStatus() === 'in progress' || getDrawerStatus() === 'in_progress'"
                            class="px-3 py-1 text-xs rounded-full bg-[#10B98120] text-[#10B981] font-semibold">In Progress</span>
                        <span x-show="getDrawerStatus() === 'completed'"
                            class="px-3 py-1 text-xs rounded-full bg-[#00BFFF20] text-[#00BFFF] font-semibold">Completed</span>
                        <span x-show="getDrawerStatus() === 'on hold'"
                            class="px-3 py-1 text-xs rounded-full bg-[#F59E0B20] text-[#F59E0B] font-semibold">On Hold</span>
                    </div>

                    <!-- Task Details Section -->
                    <div class="mb-5">
                        <div class="py-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Task Information</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">View the details of this assigned task</p>
                        </div>

                        <div class="space-y-4 text-sm py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                            <div class="flex justify-between items-center" x-show="getDrawerData('taskId') || getDrawerData('id')">
                                <span class="text-gray-500 dark:text-gray-400">Task ID</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                    x-text="getDrawerData('taskId') || getDrawerData('id') || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Service Type</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                    x-text="getDrawerData('serviceType') || getDrawerData('title') || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Scheduled Date</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                    x-text="getDrawerData('serviceDate') || getDrawerData('date') || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Scheduled Time</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                    x-text="getDrawerData('serviceTime') || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center" x-show="getDrawerData('location')">
                                <span class="text-gray-500 dark:text-gray-400">Location</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                    x-text="getDrawerData('location') || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center" x-show="getDrawerData('clientName')">
                                <span class="text-gray-500 dark:text-gray-400">Client</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                    x-text="getDrawerData('clientName') || '-'"></span>
                            </div>
                        </div>
                    </div>

                    @if($showChecklist)
                    <!-- Task Checklist Section -->
                    <div class="mb-5">
                        <div class="py-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Task Checklist</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                <template x-if="getDrawerStatus() === 'completed'">
                                    <span>All tasks have been completed</span>
                                </template>
                                <template x-if="getDrawerStatus() === 'in progress' || getDrawerStatus() === 'in_progress'">
                                    <span>Tasks being performed for this assignment</span>
                                </template>
                                <template x-if="getDrawerStatus() === 'pending' || getDrawerStatus() === 'scheduled'">
                                    <span>Tasks to be completed for this assignment</span>
                                </template>
                            </p>
                        </div>

                        <!-- Progress indicator for completed tasks -->
                        <template x-if="getDrawerStatus() === 'completed'">
                            <div class="mb-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                                    <span class="text-sm font-medium text-green-700 dark:text-green-300">
                                        Task completed successfully
                                    </span>
                                </div>
                                <p class="text-xs text-green-600 dark:text-green-400 mt-1 ml-6">
                                    All checklist items have been completed
                                </p>
                            </div>
                        </template>

                        <!-- Progress indicator for in-progress tasks -->
                        <template x-if="(getDrawerStatus() === 'in progress' || getDrawerStatus() === 'in_progress') && getDrawerChecklistProgress().completed > 0">
                            <div class="mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin text-blue-600 dark:text-blue-400"></i>
                                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                                        Task in progress
                                    </span>
                                </div>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 ml-6">
                                    <span x-text="getDrawerChecklistProgress().completed"></span> of <span x-text="getDrawerChecklistProgress().total"></span> items completed
                                </p>
                            </div>
                        </template>

                        <!-- Checklist Items -->
                        <div class="space-y-2 max-h-48 overflow-y-auto bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                            <template x-for="(item, index) in getDrawerChecklistItems()" :key="index">
                                <div class="flex items-center gap-2 py-1.5">
                                    <i class="text-xs"
                                        :class="{
                                            'fa-solid fa-check-circle text-green-500': getDrawerStatus() === 'completed' || isChecklistItemCompleted(index),
                                            'fa-regular fa-circle text-blue-400': getDrawerStatus() !== 'completed' && !isChecklistItemCompleted(index) && (getDrawerStatus() === 'in progress' || getDrawerStatus() === 'in_progress'),
                                            'fa-regular fa-circle text-gray-400': (getDrawerStatus() === 'pending' || getDrawerStatus() === 'scheduled') && !isChecklistItemCompleted(index)
                                        }"></i>
                                    <span class="text-sm"
                                        :class="{
                                            'text-green-700 dark:text-green-300 font-medium': getDrawerStatus() === 'completed' || isChecklistItemCompleted(index),
                                            'text-gray-700 dark:text-gray-300': !isChecklistItemCompleted(index) && getDrawerStatus() !== 'completed'
                                        }"
                                        x-text="item.name || item"></span>
                                </div>
                            </template>

                            <template x-if="!getDrawerChecklistItems()?.length">
                                <div class="text-center py-4">
                                    <p class="text-gray-400 dark:text-gray-500 text-sm">No checklist items assigned</p>
                                </div>
                            </template>
                        </div>

                        <!-- Progress bar -->
                        <template x-if="getDrawerChecklistItems()?.length > 0">
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Progress</span>
                                    <span class="text-xs font-semibold"
                                        :class="{
                                            'text-green-600 dark:text-green-400': getDrawerChecklistProgress().percentage === 100,
                                            'text-blue-600 dark:text-blue-400': getDrawerChecklistProgress().percentage > 0 && getDrawerChecklistProgress().percentage < 100,
                                            'text-gray-600 dark:text-gray-400': getDrawerChecklistProgress().percentage === 0
                                        }">
                                        <span x-text="getDrawerChecklistProgress().percentage"></span>% Complete
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full transition-all duration-300"
                                        :class="{
                                            'bg-green-600': getDrawerChecklistProgress().percentage === 100,
                                            'bg-blue-600': getDrawerChecklistProgress().percentage > 0 && getDrawerChecklistProgress().percentage < 100,
                                            'bg-gray-400': getDrawerChecklistProgress().percentage === 0
                                        }"
                                        :style="'width: ' + getDrawerChecklistProgress().percentage + '%'"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                    @endif

                    <!-- Status Notice -->
                    <div class="rounded-lg p-4 my-6 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-center"
                            :class="{
                                'text-green-500 dark:text-green-400': getDrawerStatus() === 'completed',
                                'text-blue-500 dark:text-blue-400': getDrawerStatus() === 'in progress' || getDrawerStatus() === 'in_progress',
                                'text-orange-400 dark:text-orange-500': getDrawerStatus() === 'pending' || getDrawerStatus() === 'scheduled'
                            }">
                            <template x-if="getDrawerStatus() === 'completed'">
                                <span><i class="fa-solid fa-circle-check mr-2"></i>This task has been <span class="font-semibold">completed</span></span>
                            </template>
                            <template x-if="getDrawerStatus() === 'in progress' || getDrawerStatus() === 'in_progress'">
                                <span><i class="fa-solid fa-spinner mr-2"></i>This task is <span class="font-semibold">in progress</span></span>
                            </template>
                            <template x-if="getDrawerStatus() === 'pending' || getDrawerStatus() === 'scheduled'">
                                <span><i class="fa-solid fa-clock mr-2"></i>This task is <span class="font-semibold">scheduled</span></span>
                            </template>
                        </p>
                    </div>
                </div>

                @if($showFooter)
                <!-- Drawer Footer (Sticky) -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                    {{ $footer ?? '' }}
                    @if(!isset($footer) || empty(trim($footer ?? '')))
                    <div class="flex gap-3">
                        <button
                            @click="{{ $closeMethod }}()"
                            class="flex-1 text-sm px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium">
                            Close
                        </button>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
