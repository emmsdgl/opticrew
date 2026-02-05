<x-layouts.general-client :title="'Activity History'">
    <div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit" x-data="clientHistoryData()">

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
                                To Rate
                                <span x-show="activeTab === 'to_rate'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'ratings'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'ratings' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Ratings
                                <span x-show="activeTab === 'ratings'" x-transition
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
                                            <div class="flex flex-wrap gap-6">
                                                <a href="#" @click.prevent="selectActivity(index)"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                    Review →
                                                </a>
                                                <a href="#" x-show="activity.status === 'Completed'"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                    Rebook →
                                                </a>
                                                <a href="#" @click.prevent="openRateModal(index)"
                                                    x-show="activity.needsRating"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                    Rate →
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
                                            <div class="flex flex-wrap gap-6">
                                                <a href="#" @click.prevent="selectActivity(activities.indexOf(activity))"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                    Review →
                                                </a>
                                                <a href="#" x-show="activity.status === 'Completed'"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                    Rebook →
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

                        {{-- To Rate Tab Content --}}
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
                                            <div class="flex flex-wrap gap-6">
                                                <a href="#" @click.prevent="selectActivity(activities.indexOf(activity))"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                    Review →
                                                </a>
                                                <a href="#" @click.prevent="openRateModal(activities.indexOf(activity))"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                    Rate →
                                                </a>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <div class="text-lg font-bold text-gray-900 dark:text-white mb-1" x-text="activity.price"></div>
                                            <div class="text-sm font-medium text-green-600 dark:text-green-400"
                                                x-text="activity.status"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="activities.filter(a => a.needsRating).length === 0">
                                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                                    <i class="fa-regular fa-star text-4xl mb-3"></i>
                                    <p>No services to rate</p>
                                </div>
                            </template>
                        </div>

                        {{-- Ratings Tab Content --}}
                        <div x-show="activeTab === 'ratings'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(rating, index) in ratings" :key="'rating-' + index">
                                <div class="rounded-lg p-3 hover:shadow-lg dark:hover:shadow-gray-900/50 transition-shadow duration-200">
                                    <div class="flex items-start gap-4">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <img :src="rating.icon" alt="Service Icon" class="w-6 h-6">
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1" x-text="rating.serviceName"></h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2" x-text="rating.location"></p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3" x-text="rating.submitted_at"></p>

                                            <!-- Rating Stars -->
                                            <div class="flex items-center gap-1 mb-3">
                                                <template x-for="star in 5" :key="star">
                                                    <svg :class="star <= rating.rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
                                                        class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                </template>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2" x-text="rating.rating + '/5'"></span>
                                            </div>

                                            <!-- Keywords -->
                                            <template x-if="rating.keywords && rating.keywords.length > 0">
                                                <div class="flex flex-wrap gap-1 mb-3">
                                                    <template x-for="keyword in rating.keywords" :key="keyword">
                                                        <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full"
                                                            x-text="keyword"></span>
                                                    </template>
                                                </div>
                                            </template>

                                            <!-- Feedback Text -->
                                            <template x-if="rating.feedback_text">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 italic" x-text="rating.feedback_text"></p>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="ratings.length === 0">
                                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                                    <i class="fa-regular fa-star text-4xl mb-3"></i>
                                    <p>No ratings submitted yet</p>
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
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Select a Service</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                                Click "Review" on any service from the list to view its details and progress
                            </p>
                        </div>

                        {{-- Activity Details --}}
                        <div x-show="selectedActivity !== null" x-transition>
                            {{-- Service Details Title --}}
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-6">Service Details Summary</h3>

                            {{-- Status Alert --}}
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-6">
                                Your service is
                                <span class="font-semibold"
                                    :class="getSelectedActivity()?.status === 'Completed' ? 'text-green-600 dark:text-green-400' :
                                            getSelectedActivity()?.status === 'In Progress' ? 'text-blue-600 dark:text-blue-400' :
                                            'text-orange-600 dark:text-orange-400'"
                                    x-text="getSelectedActivity()?.status?.toLowerCase()"></span>.
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
                            </div>

                            {{-- Assigned Team Section --}}
                            <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 mb-3">
                                    <i class="fas fa-users"></i>
                                    Assigned Team
                                </label>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <template x-for="(member, idx) in getSelectedActivity()?.assignedMembers?.slice(0, 3) || []" :key="idx">
                                        <div class="relative group">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold cursor-pointer transition-transform hover:scale-110"
                                                x-text="member.initial"></div>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-10"
                                                x-text="member.name"></div>
                                        </div>
                                    </template>
                                    <template x-if="(getSelectedActivity()?.assignedMembers?.length || 0) > 3">
                                        <button class="w-8 h-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-full flex items-center justify-center text-gray-400 text-xs"
                                            x-text="'+' + (getSelectedActivity()?.assignedMembers?.length - 3)"></button>
                                    </template>
                                </div>
                            </div>

                            {{-- Task Checklist Section (Read-only for Client) --}}
                            <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="mb-4">
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-2">
                                        Service Progress
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Track the progress of your service
                                    </p>
                                </div>

                                {{-- Checklist Items (Read-only) --}}
                                <div class="space-y-2 mb-4">
                                    <template x-for="(task, taskIdx) in getSelectedActivity()?.checklist || []" :key="taskIdx">
                                        <div class="flex items-start gap-2 p-2 rounded bg-gray-50 dark:bg-gray-800/50">
                                            <div class="flex items-center h-5 mt-0.5">
                                                <template x-if="task.completed">
                                                    <i class="fa-solid fa-circle-check text-green-500 text-sm"></i>
                                                </template>
                                                <template x-if="!task.completed">
                                                    <i class="fa-regular fa-circle text-gray-400 text-sm"></i>
                                                </template>
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
                                            <p class="text-gray-400 dark:text-gray-500 text-xs">No tasks listed</p>
                                        </div>
                                    </template>
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
                            <div class="rounded-lg p-4">
                                <p class="text-sm text-center"
                                    :class="getSelectedActivity()?.status === 'Completed' ? 'text-green-500 dark:text-green-400' :
                                            getSelectedActivity()?.status === 'In Progress' ? 'text-blue-500 dark:text-blue-400' :
                                            'text-orange-400 dark:text-orange-500'">
                                    <template x-if="getSelectedActivity()?.status === 'Completed'">
                                        <span><i class="fa-solid fa-circle-check mr-2"></i>Your service has been <span class="font-semibold">completed</span></span>
                                    </template>
                                    <template x-if="getSelectedActivity()?.status === 'In Progress'">
                                        <span><i class="fa-solid fa-spinner mr-2"></i>Your service is <span class="font-semibold">in progress</span></span>
                                    </template>
                                    <template x-if="getSelectedActivity()?.status === 'Pending'">
                                        <span><i class="fa-solid fa-clock mr-2"></i>Your appointment is <span class="font-semibold">pending</span></span>
                                    </template>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Feedback Modal -->
        <div x-show="showRateModal" x-cloak @click="closeRateModal()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4"
            style="display: none;">
            <div @click.stop
                class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100 dark:border-gray-800 overflow-hidden"
                x-show="showRateModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                <!-- Close button -->
                <button type="button" @click="closeRateModal()"
                    class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-900 dark:bg-gray-800 text-white rounded-full hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700 z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Modal Body -->
                <div class="p-8 sm:p-10">
                    <!-- Header -->
                    <div class="text-center flex flex-col gap-2 my-6">
                        <p class="text-xs text-gray-500 dark:text-gray-400 tracking-wide">
                            Your feedback matters
                        </p>
                        <h3
                            class="text-3xl sm:text-3xl font-bold text-gray-900 dark:text-white leading-tight my-3">
                            How would you rate<br class="hidden sm:block">this service?
                        </h3>
                        <p
                            class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
                            Your input is valuable in helping us better understand your needs.
                        </p>
                    </div>

                    <!-- Emoji Rating -->
                    <div class="flex justify-center items-end gap-2 sm:gap-3 mb-10">
                        @php
                            $emojis = [
                                1 => asset('images/icons/emojis/Very-Dissatisfied.svg'),
                                2 => asset('images/icons/emojis/Dissatisfied.svg'),
                                3 => asset('images/icons/emojis/Neutral.svg'),
                                4 => asset('images/icons/emojis/Satisfied.svg'),
                                5 => asset('images/icons/emojis/Very-Satisfied.svg')
                            ];
                            $ratingLabels = [
                                1 => 'Very Dissatisfied',
                                2 => 'Dissatisfied',
                                3 => 'Neutral',
                                4 => 'Satisfied',
                                5 => 'Very Satisfied'
                            ];
                        @endphp
                        @foreach($emojis as $rating => $emojiSrc)
                            <button @click="selectedRating = {{ $rating }}"
                                :class="selectedRating === {{ $rating }} ? 'scale-100 sm:scale-100' : 'scale-100'"
                                class="relative flex flex-col items-center transition-all duration-200 focus:outline-none group"
                                type="button">
                                <div class="rounded-full flex items-center justify-center transition-all duration-200"
                                    :class="selectedRating === {{ $rating }}
                                        ? 'bg-blue-600 dark:bg-blue-500 ring-4 ring-blue-200 dark:ring-blue-900 w-14 h-14 sm:w-16 sm:h-16'
                                        : 'bg-gray-200 dark:bg-gray-800 w-12 h-12 sm:w-14 sm:h-14 group-hover:bg-gray-300 dark:group-hover:bg-gray-700'">
                                    <img src="{{ $emojiSrc }}" alt="Rating {{ $rating }}"
                                        :class="selectedRating === {{ $rating }} ? 'w-8 h-8 sm:w-10 sm:h-10' : 'w-6 h-6 sm:w-8 sm:h-8 grayscale opacity-60'"
                                        class="transition-all duration-200">
                                </div>
                                <span x-show="selectedRating === {{ $rating }}" x-transition
                                    class="absolute -bottom-8 text-xs font-semibold text-white bg-blue-600 dark:bg-blue-500 px-3 py-1 rounded-full whitespace-nowrap shadow-lg">
                                    {{ $ratingLabels[$rating] }}
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <!-- Keyword Tags -->
                    <div class="mt-12 mb-4">
                        <div class="flex flex-wrap justify-center gap-2">
                            @php
                                $keywords = [
                                    'Punctual Service',
                                    'Professional Staff',
                                    'Thorough Cleaning',
                                    'Good Communication',
                                    'Value for Money',
                                    'Friendly Team',
                                    'Met Expectations',
                                    'Would Recommend'
                                ];
                            @endphp
                            @foreach($keywords as $keyword)
                                <button @click="toggleKeyword('{{ $keyword }}')"
                                    :class="isKeywordSelected('{{ $keyword }}')
                                            ? 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 border-gray-900 dark:border-gray-100'
                                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-600'"
                                    type="button"
                                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs font-medium border rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700">
                                    {{ $keyword }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Detailed Review -->
                    <div class="mb-6">
                        <label class="block text-sm text-gray-900 dark:text-white mb-2">
                            Detailed Review
                        </label>
                        <textarea x-model="feedbackText" rows="3" placeholder="Add a comment"
                            class="w-full px-4 py-3 text-sm text-gray-900 dark:text-white border-0 bg-gray-50 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 focus:outline-none placeholder-gray-400 dark:placeholder-gray-500 resize-none transition-all"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button @click="submitRating()" :disabled="selectedRating === 0" :class="selectedRating === 0
                ? 'opacity-50 cursor-not-allowed bg-blue-600 dark:bg-blue-800'
                : 'bg-blue-900 dark:bg-blue-700 hover:bg-blue-800 dark:hover:bg-blue-600'" type="button"
                        class="w-full px-6 py-3.5 sm:py-4 text-sm font-bold text-white rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 dark:focus:ring-blue-700 disabled:hover:bg-blue-900 dark:disabled:hover:bg-blue-800">
                        Submit Feedback
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
    function clientHistoryData() {
        return {
            activeTab: 'all',
            selectedActivity: null,
            showRateModal: false,
            selectedRating: 0,
            feedbackText: '',
            selectedKeywords: [],
            ratingActivityIndex: null,
            activities: @json($activities ?? []),
            ratings: @json($ratings ?? []),

            selectActivity(index) {
                this.selectedActivity = index;
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
            },

            openRateModal(index) {
                this.ratingActivityIndex = index;
                this.showRateModal = true;
            },

            closeRateModal() {
                this.showRateModal = false;
                this.selectedRating = 0;
                this.feedbackText = '';
                this.selectedKeywords = [];
                this.ratingActivityIndex = null;
            },

            toggleKeyword(keyword) {
                const index = this.selectedKeywords.indexOf(keyword);
                if (index === -1) {
                    this.selectedKeywords.push(keyword);
                } else {
                    this.selectedKeywords.splice(index, 1);
                }
            },

            isKeywordSelected(keyword) {
                return this.selectedKeywords.includes(keyword);
            },

            async submitRating() {
                if (this.selectedRating === 0) {
                    alert('Please select a rating');
                    return;
                }

                const activity = this.ratingActivityIndex !== null ? this.activities[this.ratingActivityIndex] : null;
                if (!activity) {
                    alert('No activity selected');
                    return;
                }

                console.log('Submitting feedback for activity:', activity);

                try {
                    const response = await fetch('{{ route("client.history.feedback") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            appointment_id: activity.id,
                            rating: this.selectedRating,
                            keywords: this.selectedKeywords,
                            feedback_text: this.feedbackText
                        })
                    });

                    console.log('Response status:', response.status);

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Error response:', errorText);
                        alert('Server error: ' + response.status + '\n' + errorText.substring(0, 200));
                        return;
                    }

                    const data = await response.json();
                    console.log('Response data:', data);

                    if (data.success) {
                        // Mark as rated (remove from needsRating)
                        this.activities[this.ratingActivityIndex].needsRating = false;

                        this.closeRateModal();
                        alert('Thank you for your feedback!');

                        // Reload the page to refresh the ratings list
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to submit feedback');
                    }
                } catch (error) {
                    console.error('Error submitting feedback:', error);
                    alert('An error occurred while submitting feedback: ' + error.message);
                }
            }
        };
    }
    </script>
</x-layouts.general-client>
