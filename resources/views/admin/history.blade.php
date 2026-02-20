<x-layouts.general-employer :title="'Activity History'">
    <div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit" x-data="adminHistoryData()">

        {{-- Main Content Area --}}
        <div class="flex-1">
            {{-- Content Grid --}}
            <div class="w-full">

                {{-- Activity List --}}
                <div class="space-y-8">
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

                    {{-- Sort Dropdown --}}
                    <div class="flex justify-between items-center">
                        <select
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
                                <div class="bg-none dark:bg-none border-b border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-shadow duration-200 hover:bg-gray-50 dark:hover:bg-gray-800/50">
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
                                            <div class="flex flex-wrap gap-6">
                                                <template x-if="activity.status === 'Completed'">
                                                    <div class="flex gap-4">
                                                        <a href="#" @click.prevent="selectActivity(index, 'employee')"
                                                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                            Employee Rate →
                                                        </a>
                                                        <a href="#" @click.prevent="selectActivity(index, 'client')"
                                                            class="text-sm text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium">
                                                            Client Rate →
                                                        </a>
                                                    </div>
                                                </template>
                                                <template x-if="activity.status !== 'Completed'">
                                                    <a href="#" @click.prevent="selectActivity(index, 'details')"
                                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                        Review →
                                                    </a>
                                                </template>
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
                                <div class="bg-none dark:bg-none border-b border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-shadow duration-200 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <img :src="activity.icon" alt="Service Icon" class="w-6 h-6">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1" x-text="activity.title"></h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3" x-text="activity.date"></p>
                                            <div class="flex flex-wrap gap-6">
                                                <template x-if="activity.status === 'Completed'">
                                                    <div class="flex gap-4">
                                                        <a href="#" @click.prevent="selectActivity(activities.indexOf(activity), 'employee')"
                                                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                            Employee Rate →
                                                        </a>
                                                        <a href="#" @click.prevent="selectActivity(activities.indexOf(activity), 'client')"
                                                            class="text-sm text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium">
                                                            Client Rate →
                                                        </a>
                                                    </div>
                                                </template>
                                                <template x-if="activity.status !== 'Completed'">
                                                    <a href="#" @click.prevent="selectActivity(activities.indexOf(activity), 'details')"
                                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                        Review →
                                                    </a>
                                                </template>
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
                                <div class="bg-none dark:bg-none border-b border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-shadow duration-200 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <img :src="activity.icon" alt="Service Icon" class="w-6 h-6">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1" x-text="activity.title"></h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3" x-text="activity.date"></p>
                                            <div class="flex flex-wrap gap-6">
                                                <div class="flex gap-4">
                                                    <a href="#" @click.prevent="selectActivity(activities.indexOf(activity), 'employee')"
                                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                        Employee Rate →
                                                    </a>
                                                    <a href="#" @click.prevent="selectActivity(activities.indexOf(activity), 'client')"
                                                        class="text-sm text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium">
                                                        Client Rate →
                                                    </a>
                                                </div>
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

                            <template x-if="activities.filter(a => a.needsRating).length === 0">
                                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                                    <i class="fa-regular fa-star text-4xl mb-3"></i>
                                    <p>No activities to rate</p>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Service Details Slide-in Drawer -->
        <div x-show="showDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
            <!-- Backdrop -->
            <div x-show="showDrawer"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="closeDrawer()"
                 class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

            <!-- Drawer Panel -->
            <div class="fixed inset-y-0 right-0 flex max-w-full">
                <div x-show="showDrawer"
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
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <span x-show="rateType === 'details'">Service Details</span>
                                <span x-show="rateType === 'employee'">Employee Feedback</span>
                                <span x-show="rateType === 'client'">Client Feedback</span>
                            </h2>
                            <button type="button" @click="closeDrawer()"
                                class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Drawer Body (Scrollable) -->
                        <template x-if="getSelectedActivity()">
                            <div class="flex-1 overflow-y-auto p-6">

                                <!-- Service Details View -->
                                <div x-show="rateType === 'details'">
                                    <!-- Status Badge -->
                                    <div class="flex items-center gap-2 mb-6">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                        <span class="px-3 py-1 text-xs rounded-full font-semibold"
                                            :class="{
                                                'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400': getSelectedActivity()?.status === 'Completed',
                                                'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400': getSelectedActivity()?.status === 'In Progress',
                                                'bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400': getSelectedActivity()?.status === 'Pending' || getSelectedActivity()?.status === 'Approved',
                                            }"
                                            x-text="getSelectedActivity()?.status"></span>
                                    </div>

                                    <!-- Service Details Section -->
                                    <div class="mb-5">
                                        <div class="py-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Service Details</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">View the details of the selected activity</p>
                                        </div>

                                        <div class="space-y-4 text-sm py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Appointment ID</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="getSelectedActivity()?.appointmentId"></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Service Type</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="getSelectedActivity()?.serviceType"></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Service Date</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="getSelectedActivity()?.serviceDate"></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Service Time</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="getSelectedActivity()?.serviceTime"></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Service Location</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="getSelectedActivity()?.location"></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Client Name</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="getSelectedActivity()?.clientName"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assigned Team Section -->
                                    <div class="mb-5" x-show="getSelectedActivity()?.assignedMembers?.length > 0">
                                        <div class="py-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Assigned Team</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Team members assigned to this service</p>
                                        </div>

                                        <div class="flex items-center gap-2 flex-wrap py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <template x-for="(member, idx) in (getSelectedActivity()?.assignedMembers || []).slice(0, 5)" :key="idx">
                                                <div class="relative group">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold cursor-pointer transition-transform hover:scale-110"
                                                        x-text="member.initial"></div>
                                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-10"
                                                        x-text="member.name"></div>
                                                </div>
                                            </template>
                                            <template x-if="(getSelectedActivity()?.assignedMembers?.length || 0) > 5">
                                                <button class="w-10 h-10 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-full flex items-center justify-center text-gray-400 text-sm"
                                                    x-text="'+' + (getSelectedActivity()?.assignedMembers?.length - 5)"></button>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Task Checklist Section -->
                                    <div class="mb-5">
                                        <div class="py-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Tasks Checklist</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Track the progress of this task</p>
                                        </div>

                                        <!-- Checklist Items (Read-only for admin) -->
                                        <div class="space-y-2 mb-4 max-h-48 overflow-y-auto bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                                            <template x-for="(task, taskIdx) in getSelectedActivity()?.checklist || []" :key="taskIdx">
                                                <div class="flex items-center gap-2 py-1.5">
                                                    <i class="text-xs"
                                                        :class="task.completed ? 'fa-solid fa-check-circle text-green-500' : 'fa-regular fa-circle text-gray-400'"></i>
                                                    <span class="text-sm"
                                                        :class="task.completed ? 'text-green-700 dark:text-green-300 font-medium' : 'text-gray-700 dark:text-gray-300'"
                                                        x-text="task.name"></span>
                                                </div>
                                            </template>

                                            <template x-if="!getSelectedActivity()?.checklist?.length">
                                                <div class="text-center py-4">
                                                    <p class="text-gray-400 dark:text-gray-500 text-xs">No checklist items</p>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Progress Bar -->
                                        <template x-if="getSelectedActivity()?.checklist?.length > 0">
                                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Progress</span>
                                                    <span class="text-xs font-semibold"
                                                        :class="getProgressPercentage() === 100 ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400'">
                                                        <span x-text="getProgressPercentage()"></span>% Complete
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

                                    <!-- Total Amount -->
                                    <div class="my-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Amount</div>
                                            <span class="text-base font-bold text-gray-900 dark:text-white" x-text="getSelectedActivity()?.totalAmount"></span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">Payable Amount</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">VAT Inclusive</div>
                                            </div>
                                            <span class="text-base font-bold text-blue-600 dark:text-blue-400" x-text="getSelectedActivity()?.payableAmount"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Employee Feedback View -->
                                <div x-show="rateType === 'employee'">
                                    <!-- Task Info -->
                                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2" x-text="getSelectedActivity()?.title"></h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="getSelectedActivity()?.date"></p>
                                    </div>

                                    <!-- Employee Rating Section -->
                                    <div class="mb-6">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Overall Rating</h4>
                                        <div class="flex items-center gap-2 mb-2">
                                            <template x-for="star in 5" :key="'emp-star-' + star">
                                                <i class="text-xl"
                                                    :class="star <= (getSelectedActivity()?.employeeRating?.rating || 0)
                                                        ? 'fa-solid fa-star text-yellow-400'
                                                        : 'fa-regular fa-star text-gray-300 dark:text-gray-600'"></i>
                                            </template>
                                            <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300"
                                                x-text="(getSelectedActivity()?.employeeRating?.rating || 0) + '/5'"></span>
                                        </div>
                                    </div>

                                    <!-- Employee Feedback Tags -->
                                    <div class="mb-6" x-show="getSelectedActivity()?.employeeRating?.tags?.length > 0">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Feedback Tags</h4>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="(tag, idx) in (getSelectedActivity()?.employeeRating?.tags || [])" :key="'emp-tag-' + idx">
                                                <span class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm rounded-full font-medium"
                                                    x-text="tag"></span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Employee Comments -->
                                    <div class="mb-6">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Employee Comments</h4>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 italic"
                                                x-text="getSelectedActivity()?.employeeRating?.comment || 'No comments provided'"></p>
                                        </div>
                                    </div>

                                    <!-- Submitted By -->
                                    <div class="mb-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold"
                                                x-text="(getSelectedActivity()?.employeeRating?.employeeName || 'E').charAt(0).toUpperCase()"></div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white"
                                                    x-text="getSelectedActivity()?.employeeRating?.employeeName || 'Employee'"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400"
                                                    x-text="'Submitted: ' + (getSelectedActivity()?.employeeRating?.submittedAt || 'N/A')"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- No Rating Message -->
                                    <template x-if="!getSelectedActivity()?.employeeRating">
                                        <div class="text-center py-12">
                                            <i class="fa-regular fa-comment-dots text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                            <p class="text-gray-500 dark:text-gray-400">No employee feedback submitted yet</p>
                                        </div>
                                    </template>
                                </div>

                                <!-- Client Feedback View -->
                                <div x-show="rateType === 'client'">
                                    <!-- Task Info -->
                                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2" x-text="getSelectedActivity()?.title"></h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="getSelectedActivity()?.date"></p>
                                    </div>

                                    <!-- Client Rating Section -->
                                    <div class="mb-6">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Overall Rating</h4>
                                        <div class="flex items-center gap-2 mb-2">
                                            <template x-for="star in 5" :key="'client-star-' + star">
                                                <i class="text-xl"
                                                    :class="star <= (getSelectedActivity()?.clientRating?.rating || 0)
                                                        ? 'fa-solid fa-star text-yellow-400'
                                                        : 'fa-regular fa-star text-gray-300 dark:text-gray-600'"></i>
                                            </template>
                                            <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300"
                                                x-text="(getSelectedActivity()?.clientRating?.rating || 0) + '/5'"></span>
                                        </div>
                                    </div>

                                    <!-- Client Feedback Tags -->
                                    <div class="mb-6" x-show="getSelectedActivity()?.clientRating?.tags?.length > 0">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Feedback Tags</h4>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="(tag, idx) in (getSelectedActivity()?.clientRating?.tags || [])" :key="'client-tag-' + idx">
                                                <span class="px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-sm rounded-full font-medium"
                                                    x-text="tag"></span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Client Comments -->
                                    <div class="mb-6">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Client Comments</h4>
                                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 italic"
                                                x-text="getSelectedActivity()?.clientRating?.comment || 'No comments provided'"></p>
                                        </div>
                                    </div>

                                    <!-- Submitted By -->
                                    <div class="mb-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-sm font-semibold"
                                                x-text="(getSelectedActivity()?.clientRating?.clientName || 'C').charAt(0).toUpperCase()"></div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white"
                                                    x-text="getSelectedActivity()?.clientRating?.clientName || 'Client'"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400"
                                                    x-text="'Submitted: ' + (getSelectedActivity()?.clientRating?.submittedAt || 'N/A')"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- No Rating Message -->
                                    <template x-if="!getSelectedActivity()?.clientRating">
                                        <div class="text-center py-12">
                                            <i class="fa-regular fa-comment-dots text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                            <p class="text-gray-500 dark:text-gray-400">No client feedback submitted yet</p>
                                        </div>
                                    </template>
                                </div>

                            </div>
                        </template>

                        <!-- Drawer Footer -->
                        <template x-if="getSelectedActivity()">
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                                <button @click="closeDrawer()"
                                    class="w-full px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                                    Close
                                </button>
                            </div>
                        </template>
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
            showDrawer: false,
            rateType: 'details', // 'details', 'employee', or 'client'
            activities: @json($activities ?? []),

            selectActivity(index, type = 'details') {
                this.selectedActivity = index;
                this.rateType = type;
                this.showDrawer = true;
                document.body.style.overflow = 'hidden';
            },

            closeDrawer() {
                this.showDrawer = false;
                this.rateType = 'details';
                document.body.style.overflow = 'auto';
            },

            getSelectedActivity() {
                if (this.selectedActivity === null) return null;
                return this.activities[this.selectedActivity];
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
