<x-layouts.general-employer :title="'Activity History'">
    <div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit" x-data="adminHistoryData()">

        {{-- Main Content Area --}}
        <div class="flex-1">
            {{-- Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Left Column - Activity List --}}
                <div class="lg:col-span-3 space-y-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i class="fa-regular fa-clock-rotate-left text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                            Activity History
                        </h1>
                    </div>

                    {{-- Tabs Navigation --}}
                    <div class="">
                        <nav class="flex space-x-8">
                            <button @click="activeTab = 'all'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'all' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                All
                                <span x-show="activeTab === 'all'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'services'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'services' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Services
                                <span x-show="activeTab === 'services'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'to_rate'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'to_rate' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Ratings
                                <span x-show="activeTab === 'to_rate'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>
                        </nav>
                    </div>

                    {{-- Sort/Filter Dropdown --}}
                    <div class="flex justify-between items-center">
                        {{-- Filter for Ratings Tab --}}
                        <select x-show="activeTab === 'to_rate'"
                            class="px-4 py-2 bg-transparent dark:bg-transparent rounded-lg text-sm text-gray-900 dark:text-gray-100">
                            <option>All</option>
                            <option>Clients</option>
                            <option>Employees</option>
                        </select>

                        {{-- Sort for Other Tabs --}}
                        <select x-show="activeTab !== 'to_rate'"
                            class="px-4 py-2 bg-transparent dark:bg-transparent rounded-lg text-sm text-gray-900 dark:text-gray-100">
                            <option>Most Recent</option>
                            <option>Oldest First</option>
                            <option>Price: High to Low</option>
                            <option>Price: Low to High</option>
                        </select>
                    </div>

                    {{-- Activity Cards Container --}}
                    <div class="space-y-3">

                        {{-- All Tab Content --}}
                        <div x-show="activeTab === 'all'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities" :key="index">
                                <div class="bg-none dark:bg-none border-b border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-shadow duration-200"
                                    :class="selectedActivity === index ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-l-blue-500' : ''">
                                    <div class="flex items-start gap-4">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <img :src="activity.icon" alt="Service Icon" class="w-6 h-6">
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1" x-text="activity.title"></h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3" x-text="activity.date"></p>

                                            <!-- Actions -->
                                            <div class="flex flex-wrap gap-12">
                                                <a href="#" @click.prevent="selectActivity(index)"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                    Review →
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Meta -->
                                        <div class="flex-shrink-0 text-right">
                                            <div class="text-lg font-bold text-gray-900 dark:text-white mb-1" x-text="activity.price"></div>
                                            <div class="text-sm font-medium"
                                                :class="activity.status === 'Completed' ? 'text-green-600 dark:text-green-400' :
                                                        activity.status === 'In Progress' ? 'text-blue-600 dark:text-blue-400' :
                                                        'text-orange-600 dark:text-orange-400'"
                                                x-text="activity.status"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Services Tab Content --}}
                        <div x-show="activeTab === 'services'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities.filter(a => a.type === 'service')" :key="'service-' + index">
                                <div class="bg-none dark:bg-none border-b border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-shadow duration-200"
                                    :class="selectedActivity === activities.indexOf(activity) ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-l-blue-500' : ''">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <img :src="activity.icon" alt="Service Icon" class="w-6 h-6">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1" x-text="activity.title"></h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3" x-text="activity.date"></p>
                                            <div class="flex flex-wrap gap-12">
                                                <a href="#" @click.prevent="selectActivity(activities.indexOf(activity))"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                    Review →
                                                </a>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <div class="text-lg font-bold text-gray-900 dark:text-white mb-1" x-text="activity.price"></div>
                                            <div class="text-sm font-medium"
                                                :class="activity.status === 'Completed' ? 'text-green-600 dark:text-green-400' :
                                                        activity.status === 'In Progress' ? 'text-blue-600 dark:text-blue-400' :
                                                        'text-orange-600 dark:text-orange-400'"
                                                x-text="activity.status"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Ratings Tab Content --}}
                        <div x-show="activeTab === 'to_rate'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities.filter(a => a.needsRating)" :key="'rate-' + index">
                                <div class="bg-none dark:bg-none border-b border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-shadow duration-200"
                                    :class="selectedActivity === activities.indexOf(activity) ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-l-blue-500' : ''">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <img :src="activity.icon" alt="Service Icon" class="w-6 h-6">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1" x-text="activity.title"></h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3" x-text="activity.date"></p>
                                            <div class="flex flex-wrap gap-12">
                                                <a href="#" @click.prevent="selectActivity(activities.indexOf(activity))"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                    Review →
                                                </a>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <div class="text-lg font-bold text-gray-900 dark:text-white mb-1" x-text="activity.price"></div>
                                            <div class="text-sm font-medium"
                                                :class="activity.status === 'Completed' ? 'text-green-600 dark:text-green-400' :
                                                        activity.status === 'In Progress' ? 'text-blue-600 dark:text-blue-400' :
                                                        'text-orange-600 dark:text-orange-400'"
                                                x-text="activity.status"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

                {{-- Right Column - Service Details Summary --}}
                <div class="lg:col-span-2 h-[calc(100vh-4rem)]">
                    <div class="bg-none dark:bg-none rounded-lg p-8 overflow-y-auto h-full scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-200 dark:scrollbar-track-gray-800">

                        {{-- Empty State - No Selection --}}
                        <div x-show="selectedActivity === null" class="flex flex-col items-center justify-center h-full text-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                <i class="fa-regular fa-hand-pointer text-2xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Select an Activity</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                                Click "Review" on any activity from the list to view its details and task checklist
                            </p>
                        </div>

                        {{-- Activity Details --}}
                        <div x-show="selectedActivity !== null" x-transition>
                            {{-- Service Details Title --}}
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-6">Service Details Summary</h3>

                            {{-- Approval Status Alert --}}
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-6">
                                The service status is
                                <span class="font-semibold"
                                    :class="getSelectedActivity()?.status === 'Completed' ? 'text-green-600 dark:text-green-400' :
                                            getSelectedActivity()?.status === 'In Progress' ? 'text-blue-600 dark:text-blue-400' :
                                            'text-orange-600 dark:text-orange-400'"
                                    x-text="getSelectedActivity()?.status"></span>.
                            </p>

                            {{-- Details List --}}
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Appointment ID</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="getSelectedActivity()?.appointmentId"></span>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Service Date</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="getSelectedActivity()?.serviceDate"></span>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Service Time</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="getSelectedActivity()?.serviceTime"></span>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Service Type</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="getSelectedActivity()?.serviceType"></span>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Service Location</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="getSelectedActivity()?.location"></span>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Client Name</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="getSelectedActivity()?.clientName"></span>
                                </div>
                            </div>

                            {{-- Assigned Members Section --}}
                            <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 mb-3">
                                    <i class="fas fa-users"></i>
                                    Assigned Members
                                </label>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <template x-for="(member, idx) in getSelectedActivity()?.assignedMembers?.slice(0, 3) || []" :key="idx">
                                        <div class="relative group">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold cursor-pointer transition-transform hover:scale-110"
                                                x-text="member.initial"></div>
                                            {{-- Tooltip --}}
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-10"
                                                x-text="member.name">
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="(getSelectedActivity()?.assignedMembers?.length || 0) > 3">
                                        <button class="w-8 h-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-full flex items-center justify-center text-gray-400 hover:border-gray-400 hover:text-gray-600 dark:hover:border-gray-500 dark:hover:text-gray-300 transition-colors text-xs"
                                            x-text="'+' + (getSelectedActivity()?.assignedMembers?.length - 3)"></button>
                                    </template>
                                </div>
                            </div>

                            {{-- Task Checklist Section --}}
                            <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="mb-4">
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-2">
                                        Tasks Checklist
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Track the progress of this task
                                    </p>
                                </div>

                                {{-- Checklist Items (Read-only for admin) --}}
                                <div class="space-y-2 mb-4">
                                    <template x-for="(task, taskIdx) in getSelectedActivity()?.checklist || []" :key="taskIdx">
                                        <div class="flex items-start gap-2 p-2 rounded bg-gray-50 dark:bg-gray-800/50">
                                            <div class="flex items-center h-5 mt-0.5">
                                                <input type="checkbox"
                                                    :checked="task.completed"
                                                    disabled
                                                    class="w-3.5 h-3.5 text-green-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded cursor-not-allowed opacity-70">
                                            </div>
                                            <div class="flex-1">
                                                <span class="text-xs text-gray-700 dark:text-gray-300"
                                                    :class="task.completed ? 'line-through opacity-60' : ''"
                                                    x-text="task.name"></span>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="!getSelectedActivity()?.checklist?.length">
                                        <div class="text-center py-4">
                                            <p class="text-gray-400 dark:text-gray-500 text-xs">No checklist items</p>
                                        </div>
                                    </template>
                                </div>

                                {{-- Add Task Input --}}
                                <div class="mt-4">
                                    <div class="flex gap-2">
                                        <input type="text"
                                            x-model="newTaskName"
                                            @keydown.enter="addTask()"
                                            placeholder="Add a new task..."
                                            class="flex-1 px-3 py-2 text-xs bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                                        <button @click="addTask()"
                                            :disabled="!newTaskName.trim()"
                                            class="px-3 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-xs font-medium rounded-lg transition-colors">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <template x-if="getSelectedActivity()?.checklist?.length > 0">
                                    <div class="mt-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Progress</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                <span x-text="getCompletedCount()"></span> of
                                                <span x-text="getSelectedActivity()?.checklist?.length || 0"></span> completed
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                            <div class="h-1.5 rounded-full transition-all duration-300"
                                                :class="getProgressPercentage() === 100 ? 'bg-green-600' : 'bg-blue-600'"
                                                :style="'width: ' + getProgressPercentage() + '%'"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Pricing --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2 mb-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Amount</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white" x-text="getSelectedActivity()?.totalAmount"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Payable Amount</span>
                                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400" x-text="getSelectedActivity()?.payableAmount"></span>
                                </div>
                            </div>

                            {{-- Status Notice --}}
                            <div class="bg-none dark:bg-none rounded-lg p-4">
                                <p class="text-sm text-center"
                                    :class="getSelectedActivity()?.status === 'Completed' ? 'text-green-500 dark:text-green-400' :
                                            getSelectedActivity()?.status === 'In Progress' ? 'text-blue-500 dark:text-blue-400' :
                                            'text-orange-400 dark:text-orange-500'">
                                    <template x-if="getSelectedActivity()?.status === 'Completed'">
                                        <span>This service has been <span class="font-semibold">completed</span> successfully</span>
                                    </template>
                                    <template x-if="getSelectedActivity()?.status === 'In Progress'">
                                        <span>This service is currently <span class="font-semibold">in progress</span></span>
                                    </template>
                                    <template x-if="getSelectedActivity()?.status === 'Pending'">
                                        <span>This appointment is currently <span class="font-semibold">pending</span> and has not started yet</span>
                                    </template>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
    function adminHistoryData() {
    return {
        activeTab: 'all',
        selectedActivity: null,
        newTaskName: '',
        activities: @json($activities ?? []),

        selectActivity(index) {
            this.selectedActivity = index;
            this.newTaskName = '';
        },

        getSelectedActivity() {
            if (this.selectedActivity === null) return null;
            return this.activities[this.selectedActivity];
        },

        addTask() {
            if (!this.newTaskName.trim() || this.selectedActivity === null) return;

            if (!this.activities[this.selectedActivity].checklist) {
                this.activities[this.selectedActivity].checklist = [];
            }

            this.activities[this.selectedActivity].checklist.push({
                name: this.newTaskName.trim(),
                completed: false
            });

            this.newTaskName = '';
        },

        getCompletedCount() {
            const activity = this.getSelectedActivity();
            if (!activity?.checklist) return 0;
            return activity.checklist.filter(t => t.completed).length;
        },

        getProgressPercentage() {
            const activity = this.getSelectedActivity();
            if (!activity?.checklist?.length) return 0;
            return Math.round((this.getCompletedCount() / activity.checklist.length) * 100);
        }
    };
}
    </script>
</x-layouts.general-employer>
