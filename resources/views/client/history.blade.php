<x-layouts.general-client :title="'Activity History'">
    <x-skeleton-page :preset="'history'">
    <div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit" x-data="clientHistoryData()">

        {{-- Main Content Area --}}
        <div class="flex-1">
            {{-- Content Grid --}}
            <div class="w-full">

                {{-- Activity List --}}
                <div class="space-y-8">
                    <div class="flex flex-col gap-1 w-full px-8 py-3">
                        <p class="text-base font-bold text-blue-950 dark:text-white">Activity History</p>
                        <p class="text-sm text-gray-700 dark:text-gray-500">View and track your past appointments and activities.</p>
                    </div>

                    {{-- Tabs Navigation & Sort --}}
                    <div class="flex items-center justify-between px-8">
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

                            <button @click="activeTab = 'account'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'account' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Account
                                <span x-show="activeTab === 'account'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>
                        </nav>

                        {{-- Sort Dropdown --}}
                        <x-dropdown
                            :label="'Sort:'"
                            :default="'Most Recent'"
                            :options="['Most Recent', 'Oldest First', 'Price: High to Low', 'Price: Low to High']"
                            :id="'client-history-sort'"
                        />
                    </div>

                    {{-- Activity Cards Container --}}
                    <div class="space-y-3 max-h-[21rem] overflow-y-auto">

                        {{-- All Tab Content --}}
                        <div x-show="activeTab === 'all'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities" :key="index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200 cursor-pointer"
                                    @click="selectActivity(index)">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <img :src="activity.icon" alt="Service Icon" class="w-4 h-4">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs font-semibold text-gray-900 dark:text-white" x-text="activity.title"></h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.date"></p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="text-xs font-bold text-gray-900 dark:text-white" x-text="activity.price"></div>
                                            <div class="text-xs font-medium"
                                                :class="activity.status === 'Completed' ? 'text-green-600 dark:text-green-400' :
                                                        activity.status === 'In Progress' ? 'text-blue-600 dark:text-blue-400' :
                                                        activity.status === 'Cancelled' ? 'text-red-600 dark:text-red-400' :
                                                        'text-orange-600 dark:text-orange-400'"
                                                x-text="activity.status"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="activities.length === 0">
                                <div class="flex flex-col items-center justify-center py-2 px-6 text-center">
                                    <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                        <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                             alt="No activities"
                                             class="w-full h-full object-contain opacity-80 dark:opacity-60">
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                        No activities found
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">
                                        Your activity history will appear here.
                                    </p>
                                </div>
                            </template>
                        </div>

                        {{-- Services Tab Content --}}
                        <div x-show="activeTab === 'services'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities.filter(a => a.type === 'service')" :key="'service-' + index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200 cursor-pointer"
                                    @click="selectActivity(activities.indexOf(activity))">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <img :src="activity.icon" alt="Service Icon" class="w-4 h-4">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs font-semibold text-gray-900 dark:text-white" x-text="activity.title"></h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.date"></p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="text-xs font-bold text-gray-900 dark:text-white" x-text="activity.price"></div>
                                            <div class="text-xs font-medium"
                                                :class="activity.status === 'Completed' ? 'text-green-600 dark:text-green-400' :
                                                        activity.status === 'In Progress' ? 'text-blue-600 dark:text-blue-400' :
                                                        activity.status === 'Cancelled' ? 'text-red-600 dark:text-red-400' :
                                                        'text-orange-600 dark:text-orange-400'"
                                                x-text="activity.status"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="activities.filter(a => a.type === 'service').length === 0">
                                <div class="flex flex-col items-center justify-center py-2 px-6 text-center">
                                    <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                        <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                             alt="No services"
                                             class="w-full h-full object-contain opacity-80 dark:opacity-60">
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                        No services found
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">
                                        Your completed services will appear here.
                                    </p>
                                </div>
                            </template>
                        </div>

                        {{-- To Rate Tab Content --}}
                        <div x-show="activeTab === 'to_rate'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities.filter(a => a.needsRating)" :key="'rate-' + index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200 cursor-pointer"
                                    @click="openRateModal(activities.indexOf(activity))">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <img :src="activity.icon" alt="Service Icon" class="w-4 h-4">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs font-semibold text-gray-900 dark:text-white" x-text="activity.title"></h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.date"></p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="text-xs font-bold text-gray-900 dark:text-white" x-text="activity.price"></div>
                                            <div class="text-xs font-medium text-green-600 dark:text-green-400"
                                                x-text="activity.status"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="activities.filter(a => a.needsRating).length === 0">
                                <div class="flex flex-col items-center justify-center py-2 px-6 text-center">
                                    <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                        <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                             alt="No services to rate"
                                             class="w-full h-full object-contain opacity-80 dark:opacity-60">
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                        No services to rate
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">
                                        You don't have any completed services to rate at the moment.
                                    </p>
                                </div>
                            </template>
                        </div>

                        {{-- Ratings Tab Content --}}
                        <div x-show="activeTab === 'ratings'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(rating, index) in ratings" :key="'rating-' + index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <img :src="rating.icon" alt="Service Icon" class="w-4 h-4">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs font-semibold text-gray-900 dark:text-white" x-text="rating.serviceName + ' - ' + rating.location"></h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="rating.submitted_at"></p>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <template x-for="star in 5" :key="star">
                                                <svg :class="star <= rating.rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
                                                    class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </template>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1" x-text="rating.rating + '/5'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="ratings.length === 0">
                                <div class="flex flex-col items-center justify-center py-2 px-6 text-center">
                                    <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                        <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                             alt="No ratings"
                                             class="w-full h-full object-contain opacity-80 dark:opacity-60">
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                        No ratings submitted yet
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">
                                        Your submitted ratings and feedback will appear here.
                                    </p>
                                </div>
                            </template>
                        </div>

                        {{-- Account Activity Tab Content --}}
                        <div x-show="activeTab === 'account'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(log, index) in accountLogs" :key="'log-' + index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                                    <div class="flex items-center gap-3">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center"
                                             :class="log.type === 'login' ? 'bg-green-100 dark:bg-green-900/30' :
                                                     log.type === 'logout' ? 'bg-red-100 dark:bg-red-900/30' :
                                                     'bg-blue-100 dark:bg-blue-900/30'">
                                            <template x-if="log.type === 'login'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600 dark:text-green-400"><path d="m10 17 5-5-5-5"/><path d="M15 12H3"/><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/></svg>
                                            </template>
                                            <template x-if="log.type === 'logout'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-600 dark:text-red-400"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
                                            </template>
                                            <template x-if="log.type !== 'login' && log.type !== 'logout'">
                                                <i class="fa-solid fa-user-pen text-sm text-blue-600 dark:text-blue-400"></i>
                                            </template>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs font-semibold text-gray-900 dark:text-white"
                                                x-text="log.type === 'login' ? 'Logged In' :
                                                        log.type === 'logout' ? 'Logged Out' :
                                                        'Profile Updated'"></h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="log.description"></p>
                                        </div>

                                        <!-- Timestamp & Status -->
                                        <div class="flex-shrink-0 text-right">
                                            <div class="text-xs font-medium"
                                                 :class="log.type === 'login' ? 'text-green-600 dark:text-green-400' :
                                                         log.type === 'logout' ? 'text-red-600 dark:text-red-400' :
                                                         'text-blue-600 dark:text-blue-400'"
                                                 x-text="log.type === 'login' ? 'Login' :
                                                         log.type === 'logout' ? 'Logout' :
                                                         'Edited'"></div>
                                            <p class="text-xs text-gray-400 dark:text-gray-500" x-text="log.created_at"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="accountLogs.length === 0">
                                <div class="flex flex-col items-center justify-center py-2 px-6 text-center">
                                    <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                        <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                             alt="No account activity"
                                             class="w-full h-full object-contain opacity-80 dark:opacity-60">
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                        No account activity yet
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">
                                        Your account activity logs will appear here.
                                    </p>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Service Details Slide-in Drawer -->
        <x-client-components.shared.appointment-details-drawer
            showVar="showDrawer"
            dataVar="selectedActivity"
            closeMethod="closeDrawer"
            title="Service Details"
            :showTeam="true"
            :showChecklist="true">
            <x-slot name="footer">
                <div class="flex gap-3">
                    <button
                        x-show="getDrawerData('needsRating')"
                        @click="openRateModal(activities.indexOf(selectedActivity)); closeDrawer()"
                        class="flex-1 text-sm px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                        Rate Service
                    </button>
                    <button
                        @click="closeDrawer()"
                        class="flex-1 text-sm px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium">
                        Close
                    </button>
                </div>
            </x-slot>
        </x-client-components.shared.appointment-details-drawer>

        <!-- Feedback Modal -->
        <div x-show="showRateModal" x-cloak @click="closeRateModal()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4"
            style="display: none;">
            <div @click.stop
                class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md border border-gray-100 dark:border-gray-800 overflow-hidden"
                x-show="showRateModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                <!-- Close button -->
                <button type="button" @click="closeRateModal()"
                    class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center bg-gray-900 dark:bg-gray-800 text-white rounded-full hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700 z-10">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Modal Body -->
                <div class="p-5 sm:p-6">
                    <!-- Header -->
                    <div class="text-center flex flex-col gap-1 my-3">
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 tracking-wide">
                            Your feedback matters
                        </p>
                        <h3
                            class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white leading-tight my-2">
                            How would you rate<br class="hidden sm:block">this service?
                        </h3>
                        <p
                            class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 leading-relaxed max-w-sm mx-auto">
                            Your input is valuable in helping us better understand your needs.
                        </p>
                    </div>

                    <!-- Emoji Rating -->
                    <div class="flex justify-center items-end gap-2 mb-6">
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
                                :class="selectedRating === {{ $rating }} ? 'scale-100' : 'scale-100'"
                                class="relative flex flex-col items-center transition-all duration-200 focus:outline-none group"
                                type="button">
                                <div class="rounded-full flex items-center justify-center transition-all duration-200"
                                    :class="selectedRating === {{ $rating }}
                                        ? 'bg-blue-600 dark:bg-blue-500 ring-3 ring-blue-200 dark:ring-blue-900 w-10 h-10 sm:w-12 sm:h-12'
                                        : 'bg-gray-200 dark:bg-gray-800 w-9 h-9 sm:w-10 sm:h-10 group-hover:bg-gray-300 dark:group-hover:bg-gray-700'">
                                    <img src="{{ $emojiSrc }}" alt="Rating {{ $rating }}"
                                        :class="selectedRating === {{ $rating }} ? 'w-6 h-6 sm:w-7 sm:h-7' : 'w-5 h-5 sm:w-6 sm:h-6 grayscale opacity-60'"
                                        class="transition-all duration-200">
                                </div>
                                <span x-show="selectedRating === {{ $rating }}" x-transition
                                    class="absolute -bottom-6 text-[10px] font-semibold text-white bg-blue-600 dark:bg-blue-500 px-2 py-0.5 rounded-full whitespace-nowrap shadow-lg">
                                    {{ $ratingLabels[$rating] }}
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <!-- Keyword Tags -->
                    <div class="mt-8 mb-3">
                        <div class="flex flex-wrap justify-center gap-1.5">
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
                                    class="px-2.5 py-1 text-[10px] font-medium border rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700">
                                    {{ $keyword }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Detailed Review -->
                    <div class="mb-4">
                        <label class="block text-xs text-gray-900 dark:text-white mb-1.5">
                            Detailed Review
                        </label>
                        <textarea x-model="feedbackText" rows="2" placeholder="Add a comment"
                            class="w-full px-3 py-2 text-xs text-gray-900 dark:text-white border-0 bg-gray-50 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 focus:outline-none placeholder-gray-400 dark:placeholder-gray-500 resize-none transition-all"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button @click="submitRating()" :disabled="selectedRating === 0" :class="selectedRating === 0
                ? 'opacity-50 cursor-not-allowed bg-blue-600 dark:bg-blue-800'
                : 'bg-blue-900 dark:bg-blue-700 hover:bg-blue-800 dark:hover:bg-blue-600'" type="button"
                        class="w-full px-4 py-2.5 text-xs font-bold text-white rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 dark:focus:ring-blue-700 disabled:hover:bg-blue-900 dark:disabled:hover:bg-blue-800">
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
            showDrawer: false,
            showRateModal: false,
            selectedRating: 0,
            feedbackText: '',
            selectedKeywords: [],
            ratingActivityIndex: null,
            activities: @json($activities ?? []),
            ratings: @json($ratings ?? []),
            accountLogs: @json($accountLogs ?? []),

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
                snowout_cleaning: [
                    'Remove mud, water, and debris',
                    'Clean door mats',
                    'Mop and dry floors',
                    'Deep vacuum carpets',
                    'Mop with disinfectant solution',
                    'Wipe walls near entrances',
                    'Dry wet surfaces',
                    'Check for water accumulation',
                    'Clean and sanitize affected areas',
                    'Dispose of tracked-in debris',
                    'Replace trash liners',
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
                hotel_cleaning: [
                    'Make bed with fresh linens',
                    'Replace pillowcases and sheets',
                    'Dust all surfaces (tables, headboard, shelves)',
                    'Vacuum carpet / sweep & mop floor',
                    'Clean mirrors and glass surfaces',
                    'Check under bed for trash/items',
                    'Empty trash bins and replace liners',
                    'Clean and disinfect toilet',
                    'Scrub shower walls, tub, and floor',
                    'Clean sink and countertop',
                    'Polish fixtures',
                    'Replace towels, bath mat, tissue, and toiletries',
                    'Mop bathroom floor',
                    'Refill water, coffee, and room amenities',
                    'Replace slippers and hygiene kits',
                    'Check minibar (if applicable)',
                    'Ensure lights, AC, TV working',
                    'Arrange curtains neatly',
                    'Deodorize room',
                ],
            },

            selectActivity(index) {
                // Store the actual activity object, not just the index
                this.selectedActivity = this.activities[index];
                this.showDrawer = true;
                document.body.style.overflow = 'hidden';
            },

            closeDrawer() {
                this.showDrawer = false;
                this.selectedActivity = null;
                document.body.style.overflow = 'auto';
            },

            // Helper methods required by the shared drawer component
            getDrawerStatus() {
                return (this.selectedActivity?.status || '').toLowerCase();
            },

            getDrawerData(key) {
                if (!this.selectedActivity) return null;
                // Map activity keys to drawer expected keys
                const keyMap = {
                    'service_type': 'serviceType',
                    'serviceType': 'serviceType',
                    'service_date': 'serviceDate',
                    'serviceDate': 'serviceDate',
                    'service_time': 'serviceTime',
                    'serviceTime': 'serviceTime',
                    'total_amount': 'price',
                    'totalAmount': 'price',
                    'cabin_name': 'location',
                    'location': 'location',
                };
                // Try direct key first, then mapped key
                if (this.selectedActivity[key] !== undefined) {
                    return this.selectedActivity[key];
                }
                // Try reverse mapping
                for (const [origKey, mappedKey] of Object.entries(keyMap)) {
                    if (key === origKey && this.selectedActivity[mappedKey] !== undefined) {
                        return this.selectedActivity[mappedKey];
                    }
                    if (key === mappedKey && this.selectedActivity[origKey] !== undefined) {
                        return this.selectedActivity[origKey];
                    }
                }
                return null;
            },

            getDrawerChecklistItems() {
                const serviceType = this.selectedActivity?.serviceType || this.selectedActivity?.title || '';
                return this.getChecklistItems(serviceType);
            },

            // Check if a specific checklist item is completed
            isChecklistItemCompleted(itemIndex) {
                if (!this.selectedActivity) return false;
                const completions = this.selectedActivity.checklist_completions || [];
                return completions.includes(itemIndex);
            },

            // Get checklist progress stats
            getDrawerChecklistProgress() {
                if (!this.selectedActivity) return { completed: 0, total: 0, percentage: 0 };

                const checklistItems = this.getDrawerChecklistItems();
                const total = checklistItems.length;
                const completions = this.selectedActivity.checklist_completions || [];
                const completed = completions.length;
                const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;

                return { completed, total, percentage };
            },

            formatDrawerDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;
                return date.toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                });
            },

            formatDrawerTime(timeString) {
                if (!timeString) return '-';
                const parts = timeString.split(':');
                if (parts.length < 2) return timeString;

                let hours = parseInt(parts[0]);
                const minutes = parts[1];
                const ampm = hours >= 12 ? 'PM' : 'AM';

                hours = hours % 12;
                hours = hours ? hours : 12;

                return hours + ':' + minutes + ' ' + ampm;
            },

            getSelectedActivity() {
                return this.selectedActivity;
            },

            getChecklistItems(serviceType) {
                if (!serviceType) return this.checklistTemplates.general_cleaning;

                const type = serviceType.toLowerCase();

                if (type.includes('daily') || type.includes('routine')) {
                    return this.checklistTemplates.daily_cleaning;
                } else if (type.includes('snowout') || type.includes('weather')) {
                    return this.checklistTemplates.snowout_cleaning;
                } else if (type.includes('deep')) {
                    return this.checklistTemplates.deep_cleaning;
                } else if (type.includes('hotel') || type.includes('room turnover')) {
                    return this.checklistTemplates.hotel_cleaning;
                }

                return this.checklistTemplates.general_cleaning;
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
                    window.showErrorDialog('Rating Required', 'Please select a rating before submitting.');
                    return;
                }

                const activity = this.ratingActivityIndex !== null ? this.activities[this.ratingActivityIndex] : null;
                if (!activity) {
                    window.showErrorDialog('No Activity Selected', 'Please select an activity to rate.');
                    return;
                }

                // Show confirmation dialog first (Promise-based)
                try {
                    await window.showConfirmDialog(
                        'Submit Feedback?',
                        'You are about to submit your feedback for this service. This action cannot be undone.',
                        'Submit',
                        'Cancel'
                    );
                } catch (e) {
                    return; // User cancelled
                }

                await this.doSubmitRating(activity);
            },

            async doSubmitRating(activity) {
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

                    if (!response.ok) {
                        const errorText = await response.text();
                        window.showErrorDialog('Server Error', 'Server error ' + response.status + ': ' + errorText.substring(0, 200));
                        return;
                    }

                    const data = await response.json();

                    if (data.success) {
                        this.activities[this.ratingActivityIndex].needsRating = false;
                        this.closeRateModal();
                        window.showSuccessDialog('Feedback Submitted', 'Thank you for your feedback!');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        window.showErrorDialog('Submission Failed', data.message || 'Failed to submit feedback. Please try again.');
                    }
                } catch (error) {
                    console.error('Error submitting feedback:', error);
                    window.showErrorDialog('Error', 'An error occurred while submitting feedback: ' + error.message);
                }
            }
        };
    }
    </script>
    </x-skeleton-page>
</x-layouts.general-client>
