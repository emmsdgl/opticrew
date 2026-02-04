<x-layouts.general-employee :title="'Activity History'">
    <div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit" x-data="employeeHistoryData()">

        {{-- Main Content Area --}}
        <div class="flex-1">
            {{-- Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Left Column - Activity List --}}
                <div class="lg:col-span-3 space-y-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock-rotate-left text-blue-600 dark:text-blue-400 text-xl"></i>
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

                            <button @click="activeTab === 'services'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'services' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Assigned Tasks
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
                            class="px-4 py-2 bg-white dark:bg-gray-900 rounded-lg text-sm text-gray-900 dark:text-gray-100">
                            <option>Most Recent</option>
                            <option>Oldest First</option>
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
                                                <a href="#" @click.prevent="openRateModal(index)"
                                                    x-show="activity.status === 'Completed'"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                    Rate →
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Meta -->
                                        <div class="flex-shrink-0 text-right">
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

                        {{-- Assigned Tasks Tab Content --}}
                        <div x-show="activeTab === 'services'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities.filter(a => a.type === 'task')" :key="'task-' + index">
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
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
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
                                            <div class="text-sm font-medium text-green-600 dark:text-green-400"
                                                x-text="activity.status"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

                {{-- Right Column - Task Details Summary --}}
                <div class="lg:col-span-2 h-[calc(100vh-4rem)]">
                    <div class="bg-none dark:bg-none rounded-lg p-8 overflow-y-auto h-full scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-200 dark:scrollbar-track-gray-800">

                        {{-- Empty State - No Selection --}}
                        <div x-show="selectedActivity === null" class="flex flex-col items-center justify-center h-full text-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                <i class="fa-regular fa-hand-pointer text-2xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Select a Task</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                                Click "Review" on any task from the list to view details and update your progress
                            </p>
                        </div>

                        {{-- Activity Details --}}
                        <div x-show="selectedActivity !== null" x-transition>
                            {{-- Task Details Title --}}
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-6">Task Details</h3>

                            {{-- Status Alert --}}
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-6">
                                Task status:
                                <span class="font-semibold"
                                    :class="getSelectedActivity()?.status === 'Completed' ? 'text-green-600 dark:text-green-400' :
                                            getSelectedActivity()?.status === 'In Progress' ? 'text-blue-600 dark:text-blue-400' :
                                            'text-orange-600 dark:text-orange-400'"
                                    x-text="getSelectedActivity()?.status"></span>
                            </p>

                            {{-- Details List --}}
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Task ID</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="getSelectedActivity()?.taskId"></span>
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
                                    <span class="text-gray-600 dark:text-gray-400">Location</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="getSelectedActivity()?.location"></span>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Client</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="getSelectedActivity()?.clientName"></span>
                                </div>
                            </div>

                            {{-- Task Checklist Section (Editable for Employee) --}}
                            <div class="mb-6 pb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                                <div class="mb-4">
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-2">
                                        Tasks Checklist
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Update your progress by checking completed tasks
                                    </p>
                                </div>

                                {{-- Checklist Items (Editable) --}}
                                <div class="space-y-2 mb-4">
                                    <template x-for="(task, taskIdx) in getSelectedActivity()?.checklist || []" :key="taskIdx">
                                        <label class="flex items-start gap-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer group">
                                            <div class="flex items-center h-5 mt-0.5">
                                                <input type="checkbox"
                                                    :checked="task.completed"
                                                    @change="toggleChecklistItem(taskIdx)"
                                                    class="w-4 h-4 text-green-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-green-500 dark:focus:ring-green-600 focus:ring-2 cursor-pointer">
                                            </div>
                                            <div class="flex-1">
                                                <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors"
                                                    :class="task.completed ? 'line-through opacity-60' : ''"
                                                    x-text="task.name"></span>
                                            </div>
                                        </label>
                                    </template>

                                    <template x-if="!getSelectedActivity()?.checklist?.length">
                                        <div class="text-center py-4">
                                            <p class="text-gray-400 dark:text-gray-500 text-sm">No checklist items assigned</p>
                                        </div>
                                    </template>
                                </div>

                                {{-- Progress Bar --}}
                                <template x-if="getSelectedActivity()?.checklist?.length > 0">
                                    <div class="mt-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                <span x-text="getCompletedCount()"></span> of
                                                <span x-text="getSelectedActivity()?.checklist?.length || 0"></span> completed
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-300"
                                                :class="getProgressPercentage() === 100 ? 'bg-green-600' : 'bg-blue-600'"
                                                :style="'width: ' + getProgressPercentage() + '%'"></div>
                                        </div>
                                        <p class="text-xs text-center mt-2"
                                            :class="getProgressPercentage() === 100 ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'"
                                            x-text="getProgressPercentage() === 100 ? 'All tasks completed!' : getProgressPercentage() + '% complete'"></p>
                                    </div>
                                </template>
                            </div>

                            {{-- Status Notice --}}
                            <div class="rounded-lg p-4">
                                <p class="text-sm text-center"
                                    :class="getSelectedActivity()?.status === 'Completed' ? 'text-green-500 dark:text-green-400' :
                                            getSelectedActivity()?.status === 'In Progress' ? 'text-blue-500 dark:text-blue-400' :
                                            'text-orange-400 dark:text-orange-500'">
                                    <template x-if="getSelectedActivity()?.status === 'Completed'">
                                        <span><i class="fa-solid fa-circle-check mr-2"></i>This task has been <span class="font-semibold">completed</span></span>
                                    </template>
                                    <template x-if="getSelectedActivity()?.status === 'In Progress'">
                                        <span><i class="fa-solid fa-spinner mr-2"></i>This task is <span class="font-semibold">in progress</span></span>
                                    </template>
                                    <template x-if="getSelectedActivity()?.status === 'Pending'">
                                        <span><i class="fa-solid fa-clock mr-2"></i>This task is <span class="font-semibold">pending</span></span>
                                    </template>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Feedback Modal -->
        <div x-show="showFeedbackModal" x-cloak @click="closeFeedbackModal()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4"
            style="display: none;">
            <div @click.stop
                class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100 dark:border-gray-800 overflow-hidden"
                x-show="showFeedbackModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                <!-- Close button -->
                <button type="button" @click="closeFeedbackModal()"
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
                            How would you rate<br class="hidden sm:block">this task?
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
                                    'Well-Scheduled',
                                    'Clear Instructions',
                                    'Professional Standards',
                                    'Hygiene-Compliant',
                                    'Time-Efficient',
                                    'Rushed Timeline',
                                    'Well-Defined Steps',
                                    'Skill-Appropriate'
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
                    <button @click="submitFeedback()" :disabled="selectedRating === 0" :class="selectedRating === 0
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
    function employeeHistoryData() {
        return {
            activeTab: 'all',
            selectedActivity: null,
            showFeedbackModal: false,
            selectedRating: 0,
            feedbackText: '',
            selectedKeywords: [],
            ratingActivityIndex: null,
            activities: [
                {
                    id: 1,
                    icon: '🧹',
                    title: 'Deep Cleaning Service - Unit 204',
                    date: '14 Dec 2025, 8:50 pm',
                    status: 'Completed',
                    type: 'task',
                    needsRating: true,
                    taskId: 'TASK-2025-001',
                    serviceDate: '2025-12-14',
                    serviceTime: '8:50 PM',
                    serviceType: 'Deep Cleaning',
                    location: '101 S from, Helsinki, Finland',
                    clientName: 'Maria Johnson',
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
                    status: 'In Progress',
                    type: 'task',
                    needsRating: false,
                    taskId: 'TASK-2025-002',
                    serviceDate: '2025-12-10',
                    serviceTime: '2:30 PM',
                    serviceType: 'Move-Out Cleaning',
                    location: '45 Oak Street, Espoo, Finland',
                    clientName: 'Peter Anderson',
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
                    status: 'Pending',
                    type: 'task',
                    needsRating: false,
                    taskId: 'TASK-2025-003',
                    serviceDate: '2025-12-15',
                    serviceTime: '9:00 AM',
                    serviceType: 'Regular Maintenance',
                    location: '88 Business Park, Vantaa, Finland',
                    clientName: 'Nordic Corp Ltd.',
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
                    title: 'Post-Construction Cleaning - New Build',
                    date: '5 Dec 2025, 7:00 am',
                    status: 'Completed',
                    type: 'task',
                    needsRating: true,
                    taskId: 'TASK-2025-004',
                    serviceDate: '2025-12-05',
                    serviceTime: '7:00 AM',
                    serviceType: 'Post-Construction',
                    location: '22 New Development, Tampere, Finland',
                    clientName: 'BuildRight Construction',
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

            toggleChecklistItem(taskIdx) {
                if (this.selectedActivity !== null && this.activities[this.selectedActivity]?.checklist) {
                    this.activities[this.selectedActivity].checklist[taskIdx].completed =
                        !this.activities[this.selectedActivity].checklist[taskIdx].completed;

                    // Auto-update status based on progress
                    this.updateActivityStatus();
                }
            },

            updateActivityStatus() {
                if (this.selectedActivity === null) return;

                const activity = this.activities[this.selectedActivity];
                if (!activity?.checklist?.length) return;

                const completedCount = activity.checklist.filter(t => t.completed).length;
                const totalCount = activity.checklist.length;

                if (completedCount === totalCount) {
                    activity.status = 'Completed';
                    activity.needsRating = true;
                } else if (completedCount > 0) {
                    activity.status = 'In Progress';
                } else {
                    activity.status = 'Pending';
                }
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
                this.showFeedbackModal = true;
            },

            closeFeedbackModal() {
                this.showFeedbackModal = false;
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

            async submitFeedback() {
                if (this.selectedRating === 0) return;

                // Here you would typically send this to your backend
                console.log('Submitting feedback:', {
                    activityId: this.ratingActivityIndex !== null ? this.activities[this.ratingActivityIndex].id : null,
                    rating: this.selectedRating,
                    keywords: this.selectedKeywords,
                    comment: this.feedbackText
                });

                // Mark as rated (remove from needsRating)
                if (this.ratingActivityIndex !== null) {
                    this.activities[this.ratingActivityIndex].needsRating = false;
                }

                this.closeFeedbackModal();
                alert('Thank you for your feedback!');
            }
        };
    }
    </script>
</x-layouts.general-employee>
