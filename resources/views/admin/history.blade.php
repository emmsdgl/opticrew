<x-layouts.general-employer :title="'Activity History'">
    <x-skeleton-page :preset="'history'">
    <div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit" x-data="adminHistoryData()">

        {{-- Main Content Area --}}
        <div class="flex-1">
            {{-- Content Grid --}}
            <div class="w-full">

                {{-- Activity List --}}
                <div class="space-y-8">
                    <div class="flex flex-col gap-1 w-full px-8 py-3">
                        <p class="text-base font-bold text-blue-950 dark:text-white">Activity History</p>
                        <p class="text-sm text-gray-700 dark:text-gray-500">View and track past appointments and activities.</p>
                    </div>

                    {{-- Tabs Navigation & Sort --}}
                    <div class="flex items-center justify-between px-8">
                        <nav class="flex space-x-8">
                            <button @click="activeTab = 'all'; currentPage = 1"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'all' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                All
                                <span x-show="activeTab === 'all'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'services'; currentPage = 1"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'services' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Services
                                <span x-show="activeTab === 'services'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'to_rate'; currentPage = 1"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'to_rate' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Ratings
                                <span x-show="activeTab === 'to_rate'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>
                        </nav>

                        {{-- Sort Dropdown --}}
                        <x-dropdown
                            :label="'Sort:'"
                            :default="'Most Recent'"
                            :options="['Most Recent', 'Oldest First', 'Price: High to Low', 'Price: Low to High']"
                            :id="'admin-history-sort'"
                        />
                    </div>

                    {{-- Unified Paginated Activity Cards --}}
                    <div class="space-y-3 max-h-[21rem] overflow-y-auto">
                        <div x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in paginatedActivities()" :key="activeTab + '-' + index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <img :src="activity.icon" alt="Service Icon" class="w-4 h-4">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs font-semibold text-gray-900 dark:text-white" x-text="activity.title"></h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.date"></p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <template x-if="activity.status === 'Completed' || activity.needsRating">
                                                <div class="flex gap-2">
                                                    <a href="#" @click.prevent="
                                                        const fb = activity.employeeRating?.feedbacks?.[0];
                                                        openFeedbackViewer({
                                                            type: 'employee',
                                                            rating: fb?.rating || activity.employeeRating?.averageRating || 0,
                                                            tags: fb?.tags || activity.employeeRating?.tags || [],
                                                            comment: fb?.comment || '',
                                                            submittedBy: fb?.employeeName || 'Employee',
                                                            submittedAt: fb?.submittedAt || 'N/A'
                                                        });"
                                                        class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                                        Employee
                                                    </a>
                                                    <a href="#" @click.prevent="
                                                        openFeedbackViewer({
                                                            type: 'client',
                                                            rating: activity.clientRating?.rating || 0,
                                                            tags: activity.clientRating?.tags || [],
                                                            comment: activity.clientRating?.comment || '',
                                                            submittedBy: activity.clientRating?.clientName || 'Client',
                                                            submittedAt: activity.clientRating?.submittedAt || 'N/A'
                                                        });"
                                                        class="text-xs text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium">
                                                        Client
                                                    </a>
                                                </div>
                                            </template>
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

                            <template x-if="filteredActivities().length === 0">
                                <div class="flex flex-col items-center justify-center py-2 px-6 text-center">
                                    <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                        <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                             alt="No activities found"
                                             class="w-full h-full object-contain opacity-80 dark:opacity-60">
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                        No activities found
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">
                                        Activity history will appear here.
                                    </p>
                                </div>
                            </template>
                        </div>

                    </div>

                        {{-- Pagination --}}
                        <div x-show="totalPages > 1" class="mt-4">
                            <nav role="navigation" aria-label="Pagination" class="mx-auto flex w-full justify-center">
                                <ul class="flex flex-row items-center gap-1">
                                    {{-- Previous --}}
                                    <li>
                                        <button @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1"
                                            :class="currentPage <= 1 ? 'text-gray-400 dark:text-gray-600 cursor-not-allowed' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800'"
                                            class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                            <span>Previous</span>
                                        </button>
                                    </li>

                                    {{-- Page Numbers --}}
                                    <template x-for="page in pageNumbers" :key="'page-'+page">
                                        <li>
                                            <template x-if="page === '...'">
                                                <span class="flex h-9 w-9 items-center justify-center text-gray-400 dark:text-gray-500" aria-hidden="true">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                                                </span>
                                            </template>
                                            <template x-if="page !== '...' && page === currentPage">
                                                <span aria-current="page"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-100 shadow-sm"
                                                    x-text="page"></span>
                                            </template>
                                            <template x-if="page !== '...' && page !== currentPage">
                                                <button @click="goToPage(page)"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                                    x-text="page"></button>
                                            </template>
                                        </li>
                                    </template>

                                    {{-- Next --}}
                                    <li>
                                        <button @click="goToPage(currentPage + 1)" :disabled="currentPage >= totalPages"
                                            :class="currentPage >= totalPages ? 'text-gray-400 dark:text-gray-600 cursor-not-allowed' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800'"
                                            class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                                            <span>Next</span>
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                </div>

            </div>
        </div>


        {{-- Feedback Viewer Modal --}}
        <x-dialogs.feedback-viewer
            show="showFeedbackViewer"
            onClose="showFeedbackViewer = false"
        />

    </div>
    </x-skeleton-page>

    <script>
    function adminHistoryData() {
        return {
            activeTab: 'all',
            currentPage: 1,
            perPage: 5,
            showFeedbackViewer: false,
            viewingFeedback: null,
            activities: @json($activities ?? []),

            filteredActivities() {
                if (this.activeTab === 'all') return this.activities;
                if (this.activeTab === 'services') return this.activities.filter(a => a.type === 'service');
                if (this.activeTab === 'to_rate') return this.activities.filter(a => a.needsRating);
                return this.activities;
            },

            paginatedActivities() {
                const filtered = this.filteredActivities();
                const start = (this.currentPage - 1) * this.perPage;
                return filtered.slice(start, start + this.perPage);
            },

            get totalPages() {
                return Math.ceil(this.filteredActivities().length / this.perPage);
            },

            get pageNumbers() {
                const pages = [];
                const total = this.totalPages;
                const current = this.currentPage;
                pages.push(1);
                const rangeStart = Math.max(2, current - 1);
                const rangeEnd = Math.min(total - 1, current + 1);
                if (rangeStart > 2) pages.push('...');
                for (let i = rangeStart; i <= rangeEnd; i++) pages.push(i);
                if (rangeEnd < total - 1) pages.push('...');
                if (total > 1) pages.push(total);
                return pages;
            },

            goToPage(p) {
                if (p >= 1 && p <= this.totalPages) this.currentPage = p;
            },

            openFeedbackViewer(feedback) {
                this.viewingFeedback = feedback;
                this.showFeedbackViewer = true;
            }
        };
    }
    </script>
</x-layouts.general-employer>
