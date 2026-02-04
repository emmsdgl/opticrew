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
                                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-2xl"
                                            x-text="activity.icon"></div>

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
                                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-2xl"
                                            x-text="activity.icon"></div>
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
                                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-2xl"
                                            x-text="activity.icon"></div>
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

        <!-- Rating Modal -->
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
                    class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-900 dark:bg-gray-800 text-white rounded-full hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight my-3">
                            How would you rate this service?
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
                            Your input helps us improve our services.
                        </p>
                    </div>

                    <!-- Star Rating -->
                    <div class="flex justify-center items-center gap-2 mb-8">
                        <template x-for="star in 5" :key="star">
                            <button @click="selectedRating = star" type="button"
                                class="text-3xl transition-all duration-200 focus:outline-none"
                                :class="star <= selectedRating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600 hover:text-yellow-200'">
                                <i class="fa-solid fa-star"></i>
                            </button>
                        </template>
                    </div>

                    <!-- Detailed Review -->
                    <div class="mb-6">
                        <label class="block text-sm text-gray-900 dark:text-white mb-2">
                            Comments (Optional)
                        </label>
                        <textarea x-model="feedbackText" rows="3" placeholder="Share your experience..."
                            class="w-full px-4 py-3 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 focus:outline-none placeholder-gray-400 dark:placeholder-gray-500 resize-none transition-all"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button @click="submitRating()" :disabled="selectedRating === 0"
                        :class="selectedRating === 0 ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'"
                        type="button"
                        class="w-full px-6 py-3 text-sm font-bold text-white rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600">
                        Submit Rating
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
            ratingActivityIndex: null,
            activities: [
                {
                    id: 1,
                    icon: '🧹',
                    title: 'Deep Cleaning Service - Unit 204',
                    date: '14 Dec 2025, 8:50 pm',
                    price: '€ 280',
                    status: 'Completed',
                    type: 'service',
                    needsRating: true,
                    appointmentId: 'APT-2025-001',
                    serviceDate: '2025-12-14',
                    serviceTime: '8:50 PM',
                    serviceType: 'Deep Cleaning',
                    location: '101 S from, Helsinki, Finland',
                    totalAmount: '€280.00',
                    payableAmount: '€120.00',
                    assignedMembers: [
                        { name: 'John Doe', initial: 'J' },
                        { name: 'Jane Smith', initial: 'J' },
                        { name: 'Bob Johnson', initial: 'B' }
                    ],
                    checklist: [
                        { name: 'Remove clutter and movable items', completed: true },
                        { name: 'Wipe walls, doors, door frames, and switches', completed: true },
                        { name: 'Vacuum sofas, chairs, and cushions', completed: true },
                        { name: 'Deep vacuum carpets / mop hard floors', completed: true },
                        { name: 'Clean shower area (tiles, glass, fixtures)', completed: true },
                        { name: 'Dust and Sanitize furniture surfaces', completed: true },
                        { name: 'Report damages or issues (if any)', completed: true }
                    ]
                },
                {
                    id: 2,
                    icon: '🏠',
                    title: 'Move-Out Cleaning - Villa 15',
                    date: '10 Dec 2025, 2:30 pm',
                    price: '€ 450',
                    status: 'In Progress',
                    type: 'service',
                    needsRating: false,
                    appointmentId: 'APT-2025-002',
                    serviceDate: '2025-12-10',
                    serviceTime: '2:30 PM',
                    serviceType: 'Move-Out Cleaning',
                    location: '45 Oak Street, Espoo, Finland',
                    totalAmount: '€450.00',
                    payableAmount: '€200.00',
                    assignedMembers: [
                        { name: 'Sarah Wilson', initial: 'S' },
                        { name: 'Mike Brown', initial: 'M' },
                        { name: 'Lisa Davis', initial: 'L' }
                    ],
                    checklist: [
                        { name: 'Empty all rooms and storage areas', completed: true },
                        { name: 'Clean all windows inside and out', completed: true },
                        { name: 'Deep clean kitchen appliances', completed: false },
                        { name: 'Sanitize all bathroom fixtures', completed: false },
                        { name: 'Clean and polish all floors', completed: false },
                        { name: 'Remove all wall marks and scuffs', completed: false },
                        { name: 'Final walkthrough inspection', completed: false }
                    ]
                },
                {
                    id: 3,
                    icon: '✨',
                    title: 'Regular Maintenance - Office Block A',
                    date: '8 Dec 2025, 9:00 am',
                    price: '€ 180',
                    status: 'Pending',
                    type: 'service',
                    needsRating: false,
                    appointmentId: 'APT-2025-003',
                    serviceDate: '2025-12-15',
                    serviceTime: '9:00 AM',
                    serviceType: 'Regular Maintenance',
                    location: '88 Business Park, Vantaa, Finland',
                    totalAmount: '€180.00',
                    payableAmount: '€180.00',
                    assignedMembers: [
                        { name: 'Tom White', initial: 'T' },
                        { name: 'Emma Green', initial: 'E' }
                    ],
                    checklist: [
                        { name: 'Dust all surfaces and desks', completed: false },
                        { name: 'Empty all trash bins', completed: false },
                        { name: 'Vacuum carpeted areas', completed: false },
                        { name: 'Mop hard floor areas', completed: false },
                        { name: 'Clean break room and kitchen', completed: false },
                        { name: 'Restock bathroom supplies', completed: false }
                    ]
                },
                {
                    id: 4,
                    icon: '🧼',
                    title: 'Post-Construction Cleaning',
                    date: '5 Dec 2025, 7:00 am',
                    price: '€ 650',
                    status: 'Completed',
                    type: 'service',
                    needsRating: true,
                    appointmentId: 'APT-2025-004',
                    serviceDate: '2025-12-05',
                    serviceTime: '7:00 AM',
                    serviceType: 'Post-Construction',
                    location: '22 New Development, Tampere, Finland',
                    totalAmount: '€650.00',
                    payableAmount: '€350.00',
                    assignedMembers: [
                        { name: 'John Doe', initial: 'J' },
                        { name: 'Sarah Wilson', initial: 'S' },
                        { name: 'Mike Brown', initial: 'M' },
                        { name: 'Lisa Davis', initial: 'L' }
                    ],
                    checklist: [
                        { name: 'Remove all construction debris', completed: true },
                        { name: 'Clean and polish all windows', completed: true },
                        { name: 'Remove paint splatters and stickers', completed: true },
                        { name: 'Deep clean all surfaces', completed: true },
                        { name: 'Sanitize bathroom installations', completed: true },
                        { name: 'Clean ventilation and ducts', completed: true },
                        { name: 'Final quality inspection', completed: true }
                    ]
                }
            ],

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
                this.ratingActivityIndex = null;
            },

            async submitRating() {
                if (this.selectedRating === 0) return;

                console.log('Submitting rating:', {
                    activityId: this.ratingActivityIndex !== null ? this.activities[this.ratingActivityIndex].id : null,
                    rating: this.selectedRating,
                    comment: this.feedbackText
                });

                if (this.ratingActivityIndex !== null) {
                    this.activities[this.ratingActivityIndex].needsRating = false;
                }

                this.closeRateModal();
                alert('Thank you for your feedback!');
            }
        };
    }
    </script>
</x-layouts.general-client>
