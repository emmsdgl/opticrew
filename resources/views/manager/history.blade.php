<x-layouts.general-manager :title="'Activity History'">
    <x-skeleton-page :preset="'history'">
    <div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit" x-data="managerHistoryData()">

        {{-- Main Content Area --}}
        <div class="flex-1">
            <div class="w-full">

                {{-- Activity List --}}
                <div class="space-y-8">
                    <div class="flex flex-col gap-1 w-full px-8 py-3">
                        <p class="text-base font-bold text-blue-950 dark:text-white">Activity History</p>
                        <p class="text-sm text-gray-700 dark:text-gray-500">View and track past events, tasks, reports, and account activity.</p>
                    </div>

                    {{-- Tabs Navigation & Sort --}}
                    <div class="flex items-center justify-between px-8">
                        <nav class="flex space-x-8">
                            <button @click="activeTab = 'all'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'all' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                <span class="inline-flex items-center gap-2">
                                    <span>All</span>
                                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-blue-100 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                        x-text="activities.length + accountLogs.length"></span>
                                </span>
                                <span x-show="activeTab === 'all'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'tasks'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'tasks' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                <span class="inline-flex items-center gap-2">
                                    <span>Tasks</span>
                                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-blue-100 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                        x-text="activities.filter(a => a.type === 'task').length"></span>
                                </span>
                                <span x-show="activeTab === 'tasks'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'checklist'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'checklist' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                <span class="inline-flex items-center gap-2">
                                    <span>Checklist</span>
                                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-blue-100 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                        x-text="activities.filter(a => a.type === 'checklist').length"></span>
                                </span>
                                <span x-show="activeTab === 'checklist'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'reports'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'reports' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                <span class="inline-flex items-center gap-2">
                                    <span>Reports</span>
                                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-blue-100 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                        x-text="activities.filter(a => a.type === 'report').length"></span>
                                </span>
                                <span x-show="activeTab === 'reports'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'account'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'account' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                <span class="inline-flex items-center gap-2">
                                    <span>Account</span>
                                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-blue-100 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                        x-text="accountLogs.length"></span>
                                </span>
                                <span x-show="activeTab === 'account'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'ratings'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'ratings' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                <span class="inline-flex items-center gap-2">
                                    <span>Ratings</span>
                                    <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-blue-100 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                        x-text="activities.filter(a => a.statusRaw === 'completed').length"></span>
                                </span>
                                <span x-show="activeTab === 'ratings'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>
                        </nav>

                        {{-- Sort Dropdown --}}
                        <x-dropdown
                            :label="'Sort:'"
                            :default="'Most Recent'"
                            :options="['Most Recent', 'Oldest First']"
                            :id="'manager-history-sort'"
                        />
                    </div>

                    {{-- Activity Cards Container --}}
                    <div class="space-y-3 max-h-[21rem] overflow-y-auto px-2 md:px-4">

                        {{-- All Tab --}}
                        <div x-show="activeTab === 'all'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities" :key="index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-6 md:px-8 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200 cursor-pointer"
                                    @click="openDrawer(activity)">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center mt-0.5"
                                            :class="{
                                                'bg-blue-100 dark:bg-blue-900/30': activity.type === 'task',
                                                'bg-green-100 dark:bg-green-900/30': activity.type === 'checklist',
                                                'bg-purple-100 dark:bg-purple-900/30': activity.type === 'report',
                                                'bg-yellow-100 dark:bg-yellow-900/30': activity.type === 'warning',
                                                'bg-gray-100 dark:bg-gray-700': !['task','checklist','report','warning'].includes(activity.type)
                                            }">
                                            <i class="fa-solid text-sm"
                                                :class="{
                                                    'fa-list-check text-blue-600 dark:text-blue-400': activity.type === 'task',
                                                    'fa-clipboard-check text-green-600 dark:text-green-400': activity.type === 'checklist',
                                                    'fa-file-lines text-purple-600 dark:text-purple-400': activity.type === 'report',
                                                    'fa-triangle-exclamation text-yellow-600 dark:text-yellow-400': activity.type === 'warning',
                                                    'fa-circle-info text-gray-500 dark:text-gray-400': !['task','checklist','report','warning'].includes(activity.type)
                                                }"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs font-semibold text-gray-900 dark:text-white" x-text="activity.title"></h3>
                                            <div class="flex items-center gap-1 my-1 text-xs text-gray-500 dark:text-gray-400" x-show="activity.started_at || activity.time">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <span x-text="activity.started_at ? activity.started_at : activity.time"></span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.description"></p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div x-show="activity.status" class="text-xs font-medium"
                                                :class="{
                                                    'text-green-600 dark:text-green-400': activity.status === 'completed' || activity.status === 'added',
                                                    'text-yellow-600 dark:text-yellow-400': activity.status === 'pending',
                                                    'text-blue-600 dark:text-blue-400': activity.status === 'updated',
                                                    'text-gray-500 dark:text-gray-400': !['completed','added','pending','updated'].includes(activity.status)
                                                }"
                                                x-text="activity.status ? activity.status.charAt(0).toUpperCase() + activity.status.slice(1) : ''"></div>
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
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">No activities found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">Your activity history will appear here.</p>
                                </div>
                            </template>
                        </div>

                        {{-- Tasks Tab --}}
                        <div x-show="activeTab === 'tasks'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities.filter(a => a.type === 'task')" :key="'task-' + index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-6 md:px-8 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200 cursor-pointer"
                                    @click="openDrawer(activity)">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mt-0.5">
                                            <i class="fa-solid fa-list-check text-sm text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs font-semibold text-gray-900 dark:text-white" x-text="activity.title"></h3>
                                            <div class="flex items-center gap-1 my-1 text-xs text-gray-500 dark:text-gray-400" x-show="activity.started_at || activity.time">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <span x-text="activity.started_at ? activity.started_at : activity.time"></span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.description"></p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div x-show="activity.status" class="text-xs font-medium"
                                                :class="{
                                                    'text-green-600 dark:text-green-400': activity.status === 'completed' || activity.status === 'added',
                                                    'text-yellow-600 dark:text-yellow-400': activity.status === 'pending',
                                                    'text-blue-600 dark:text-blue-400': activity.status === 'updated'
                                                }"
                                                x-text="activity.status ? activity.status.charAt(0).toUpperCase() + activity.status.slice(1) : ''"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="activities.filter(a => a.type === 'task').length === 0">
                                <div class="flex flex-col items-center justify-center py-2 px-6 text-center">
                                    <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                        <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                             alt="No tasks"
                                             class="w-full h-full object-contain opacity-80 dark:opacity-60">
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">No task activity found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">Task-related activity will appear here.</p>
                                </div>
                            </template>
                        </div>

                        {{-- Checklist Tab --}}
                        <div x-show="activeTab === 'checklist'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities.filter(a => a.type === 'checklist')" :key="'checklist-' + index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-6 md:px-8 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200 cursor-pointer"
                                    @click="openDrawer(activity)">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mt-0.5">
                                            <i class="fa-solid fa-clipboard-check text-sm text-green-600 dark:text-green-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs font-semibold text-gray-900 dark:text-white" x-text="activity.title"></h3>
                                            <div class="flex items-center gap-1 my-1 text-xs text-gray-500 dark:text-gray-400" x-show="activity.started_at || activity.time">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <span x-text="activity.started_at ? activity.started_at : activity.time"></span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.description"></p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div x-show="activity.status" class="text-xs font-medium"
                                                :class="{
                                                    'text-green-600 dark:text-green-400': activity.status === 'completed' || activity.status === 'added',
                                                    'text-yellow-600 dark:text-yellow-400': activity.status === 'pending',
                                                    'text-blue-600 dark:text-blue-400': activity.status === 'updated'
                                                }"
                                                x-text="activity.status ? activity.status.charAt(0).toUpperCase() + activity.status.slice(1) : ''"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="activities.filter(a => a.type === 'checklist').length === 0">
                                <div class="flex flex-col items-center justify-center py-2 px-6 text-center">
                                    <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                        <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                             alt="No checklist activity"
                                             class="w-full h-full object-contain opacity-80 dark:opacity-60">
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">No checklist activity found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">Checklist-related activity will appear here.</p>
                                </div>
                            </template>
                        </div>

                        {{-- Reports Tab --}}
                        <div x-show="activeTab === 'reports'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities.filter(a => a.type === 'report')" :key="'report-' + index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-6 md:px-8 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200 cursor-pointer"
                                    @click="openDrawer(activity)">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mt-0.5">
                                            <i class="fa-solid fa-file-lines text-sm text-purple-600 dark:text-purple-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs font-semibold text-gray-900 dark:text-white" x-text="activity.title"></h3>
                                            <div class="flex items-center gap-1 my-1 text-xs text-gray-500 dark:text-gray-400" x-show="activity.started_at || activity.time">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <span x-text="activity.started_at ? activity.started_at : activity.time"></span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.description"></p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div x-show="activity.status" class="text-xs font-medium"
                                                :class="{
                                                    'text-green-600 dark:text-green-400': activity.status === 'completed' || activity.status === 'added',
                                                    'text-yellow-600 dark:text-yellow-400': activity.status === 'pending',
                                                    'text-blue-600 dark:text-blue-400': activity.status === 'updated'
                                                }"
                                                x-text="activity.status ? activity.status.charAt(0).toUpperCase() + activity.status.slice(1) : ''"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="activities.filter(a => a.type === 'report').length === 0">
                                <div class="flex flex-col items-center justify-center py-2 px-6 text-center">
                                    <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                        <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                             alt="No reports"
                                             class="w-full h-full object-contain opacity-80 dark:opacity-60">
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">No report activity found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">Report-related activity will appear here.</p>
                                </div>
                            </template>
                        </div>

                        {{-- Account Activity Tab --}}
                        <div x-show="activeTab === 'account'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(log, index) in accountLogs" :key="'log-' + index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-6 md:px-8 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200 cursor-pointer"
                                    @click="openDrawer(log)">
                                    <div class="flex items-center gap-3">
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
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs font-semibold text-gray-900 dark:text-white"
                                                x-text="log.type === 'login' ? 'Logged In' :
                                                        log.type === 'logout' ? 'Logged Out' :
                                                        'Profile Updated'"></h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="log.description"></p>
                                        </div>
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
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">No account activity yet</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">Your account activity logs will appear here.</p>
                                </div>
                            </template>
                        </div>

                        {{-- Ratings Tab --}}
                        <div x-show="activeTab === 'ratings'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <template x-for="(activity, index) in activities.filter(a => a.statusRaw === 'completed')" :key="'rating-' + index">
                                <div class="border-b border-gray-200 dark:border-gray-700 px-6 md:px-8 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200 cursor-pointer"
                                    @click="openDrawer(activity)">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center mt-1" :style="`background-color: ${activity.color}33; color: ${activity.color}`">
                                            <i :class="activity.icon" class="text-base"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="text-xs font-semibold text-gray-900 dark:text-white truncate" x-text="activity.room"></h3>
                                                <span :class="activity.reviewed 
                                                    ? 'inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                                    : 'inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'">
                                                    <template x-if="activity.reviewed">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="mr-1"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                                    </template>
                                                    <span x-text="activity.reviewed ? 'Rated' : 'To Rate'"></span>
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1 mb-1 text-xs text-gray-500 dark:text-gray-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4M3 4h18v18H3zM3 10h18"/></svg>
                                                <span x-text="activity.started_at ? activity.started_at : activity.date"></span>
                                            </div>
                                            <template x-if="activity.reviewed && activity.review">
                                                <div class="flex items-center gap-1.5">
                                                    <div class="flex gap-0.5">
                                                        <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" :class="star <= activity.review.rating ? 'fill-yellow-400 text-yellow-400' : 'text-gray-300 dark:text-gray-600'" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                                        </template>
                                                    </div>
                                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300" x-text="activity.review.rating"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="activity.price"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="activities.filter(a => a.statusRaw === 'completed').length === 0">
                                <div class="flex flex-col items-center justify-center py-8 px-6 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 dark:text-gray-600 mb-3"><path d="M9 12h6m-6 4h6M9 8h6M5 20h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No completed tasks yet</p>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    <!-- Details Drawer -->
    <div x-show="showDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
        <div x-show="showDrawer"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="closeDrawer()"
            class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

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

                <div class="h-full flex flex-col bg-white dark:bg-slate-800 shadow-2xl border-l border-gray-200 dark:border-slate-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-slate-800/50">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex-shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center"
                                :style="`background-color: ${selectedItem?.color}33; color: ${selectedItem?.color}`">
                                <i class="fa-solid text-base" :class="selectedItem?.icon || 'fa-circle-info text-blue-600 dark:text-blue-400'"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="selectedItem?.task || selectedItem?.typeLabel || 'Task'"></p>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white truncate" x-text="selectedItem?.room || selectedItem?.title || 'Room'"></h2>
                            </div>
                        </div>
                        <button type="button" @click="closeDrawer()"
                            class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6" x-show="selectedItem">
                        <div class="flex items-center gap-2 mb-6">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</span>
                            <span class="px-3 py-1 text-xs rounded-full font-semibold"
                                :class="selectedItem?.statusBadgeClass || 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                                x-text="selectedItem?.statusLabel || 'N/A'"></span>
                        </div>

                        <div class="space-y-4 text-sm py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Category</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="selectedItem?.category || selectedItem?.task || selectedItem?.typeLabel || '-' "></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Date &amp; Time Started</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="selectedItem?.started_at || '-' "></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Date &amp; Time Ended</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="selectedItem?.ended_at || '-' "></span>
                            </div>
                            <div class="flex justify-between items-center" x-show="selectedItem?.price">
                                <span class="text-gray-500 dark:text-gray-400">Price</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="selectedItem?.price"></span>
                            </div>
                            <div class="flex justify-between items-center" x-show="selectedItem?.statusRaw">
                                <span class="text-gray-500 dark:text-gray-400">Raw Status</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="selectedItem?.statusRaw"></span>
                            </div>
                        </div>

                        <div class="mt-5" x-show="selectedItem?.description">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Description</h4>
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg text-sm text-gray-600 dark:text-gray-300 leading-6"
                                x-text="selectedItem?.description || '-' "></div>
                        </div>

                        {{-- Review Information Section --}}
                        <div class="mt-5" x-show="selectedItem?.review">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Review Information</h4>
                            
                            {{-- Rating --}}
                            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Rating</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="flex gap-0.5">
                                        <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" :class="star <= selectedItem?.review?.rating ? 'fill-yellow-400 text-yellow-400' : 'text-gray-300 dark:text-gray-600'" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        </template>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-900 dark:text-white" x-text="selectedItem?.review?.rating"></span>
                                </div>
                            </div>

                            {{-- Feedback Tags --}}
                            <div class="mb-4" x-show="selectedItem?.review?.feedback_tags && selectedItem?.review?.feedback_tags?.length > 0">
                                <span class="text-xs text-gray-500 dark:text-gray-400 block mb-2">Keywords</span>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="tag in selectedItem?.review?.feedback_tags || []" :key="tag">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300"
                                            x-text="tag"></span>
                                    </template>
                                </div>
                            </div>

                            {{-- Review Text --}}
                            <div x-show="selectedItem?.review?.review_text">
                                <span class="text-xs text-gray-500 dark:text-gray-400 block mb-2">Review Description</span>
                                <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg text-sm text-gray-600 dark:text-gray-300 leading-6"
                                    x-text="selectedItem?.review?.review_text || '-' "></div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex gap-3">
                            <button
                                x-show="selectedItem?.statusRaw === 'completed'"
                                @click="openReviewModal()"
                                :disabled="selectedItem?.reviewed"
                                :class="selectedItem?.reviewed 
                                    ? 'flex-1 text-sm px-4 py-2.5 bg-gray-400 dark:bg-gray-600 text-white rounded-lg font-medium cursor-not-allowed'
                                    : 'flex-1 text-sm px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium'">
                                <span x-text="selectedItem?.reviewed ? 'Reviewed' : 'Review'"></span>
                            </button>
                            <button
                                @click="closeDrawer()"
                                class="flex-1 text-sm px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div x-show="showReviewModal" x-cloak @click="closeReviewModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4"
        style="display: none;">
        <div @click.stop
            class="relative py-6 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100 dark:border-gray-800 overflow-hidden"
            x-show="showReviewModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <button type="button" @click="closeReviewModal()"
                class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center bg-gray-900 dark:bg-gray-800 text-white rounded-full hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700 z-10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="p-8">
                <div class="text-center flex flex-col gap-1 my-3.5">
                    <p class="text-xs text-gray-500 dark:text-gray-400 tracking-wide">Your feedback matters</p>
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white leading-tight my-4">
                        How would you rate<br class="hidden sm:block">this task?
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-sm mx-auto">
                        Share what the team did well and what could be improved.
                    </p>
                </div>

                <div class="flex justify-center items-end gap-2 sm:gap-2.5 mb-6">
                    @php
                        $managerEmojis = [
                            1 => asset('images/icons/emojis/Very-Dissatisfied.svg'),
                            2 => asset('images/icons/emojis/Dissatisfied.svg'),
                            3 => asset('images/icons/emojis/Neutral.svg'),
                            4 => asset('images/icons/emojis/Satisfied.svg'),
                            5 => asset('images/icons/emojis/Very-Satisfied.svg'),
                        ];
                        $managerRatingLabels = [
                            1 => 'Very Dissatisfied',
                            2 => 'Dissatisfied',
                            3 => 'Neutral',
                            4 => 'Satisfied',
                            5 => 'Very Satisfied',
                        ];
                    @endphp
                    @foreach($managerEmojis as $rating => $emojiSrc)
                        <button @click="selectedRating = {{ $rating }}"
                            :class="selectedRating === {{ $rating }} ? 'scale-100 sm:scale-100' : 'scale-100'"
                            class="relative flex flex-col items-center transition-all duration-200 focus:outline-none group"
                            type="button">
                            <div class="rounded-full flex items-center justify-center transition-all duration-200"
                                :class="selectedRating === {{ $rating }}
                                    ? 'bg-blue-600 dark:bg-blue-500 ring-4 ring-blue-200 dark:ring-blue-900 w-12 h-12 sm:w-14 sm:h-14'
                                    : 'bg-gray-200 dark:bg-gray-800 w-10 h-10 sm:w-12 sm:h-12 group-hover:bg-gray-300 dark:group-hover:bg-gray-700'">
                                <img src="{{ $emojiSrc }}" alt="Rating {{ $rating }}"
                                    :class="selectedRating === {{ $rating }} ? 'w-7 h-7 sm:w-8 sm:h-8' : 'w-5 h-5 sm:w-7 sm:h-7 grayscale opacity-60'"
                                    class="transition-all duration-200">
                            </div>
                            <span x-show="selectedRating === {{ $rating }}" x-transition
                                class="absolute -bottom-7 text-[11px] font-semibold text-white bg-blue-600 dark:bg-blue-500 px-2.5 py-0.5 rounded-full whitespace-nowrap shadow-lg">
                                {{ $managerRatingLabels[$rating] }}
                            </span>
                        </button>
                    @endforeach
                </div>

                <div class="mt-7 mb-3">
                    <div class="flex flex-wrap justify-center gap-2">
                        <template x-for="keyword in reviewKeywords" :key="keyword">
                            <button @click="toggleKeyword(keyword)"
                                :class="isKeywordSelected(keyword)
                                        ? 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 border-gray-900 dark:border-gray-100'
                                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-600'"
                                type="button"
                                class="px-2.5 sm:px-3.5 py-1.5 text-xs font-medium border rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700"
                                x-text="keyword"></button>
                        </template>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-gray-900 dark:text-white mb-2">Detailed Review</label>
                    <textarea x-model="feedbackText" rows="2" placeholder="Add a comment"
                        class="w-full px-4 py-3 text-sm text-gray-900 dark:text-white border-0 bg-gray-50 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 focus:outline-none placeholder-gray-400 dark:placeholder-gray-500 resize-none transition-all"></textarea>
                </div>

                <button @click="submitReview()" :disabled="selectedRating === 0" :class="selectedRating === 0
                    ? 'opacity-50 cursor-not-allowed bg-blue-600 dark:bg-blue-800'
                    : 'bg-blue-900 dark:bg-blue-700 hover:bg-blue-800 dark:hover:bg-blue-600'" type="button"
                    class="w-full px-6 py-3 text-sm font-bold text-white rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 dark:focus:ring-blue-700 disabled:hover:bg-blue-900 dark:disabled:hover:bg-blue-800">
                    Submit Feedback
                </button>
            </div>
        </div>
    </div>

    <script type="application/json" id="manager-history-activities-json">@json($activities ?? [])</script>
    <script type="application/json" id="manager-history-account-logs-json">@json($accountLogs ?? [])</script>

    <script>
    function managerHistoryData() {
        const parseJsonNode = (id, fallback = []) => {
            const node = document.getElementById(id);
            if (!node) return fallback;
            try {
                return JSON.parse(node.textContent || 'null') ?? fallback;
            } catch (error) {
                return fallback;
            }
        };

        const initialActivities = parseJsonNode('manager-history-activities-json', []);
        const initialAccountLogs = parseJsonNode('manager-history-account-logs-json', []);

        return {
            activeTab: 'all',
            activities: initialActivities,
            accountLogs: initialAccountLogs,
            showDrawer: false,
            selectedItem: null,
            showReviewModal: false,
            selectedRating: 0,
            feedbackText: '',
            selectedKeywords: [],
            reviewKeywords: [
                'Timely Completion',
                'Professional Staff',
                'Clear Communication',
                'Thorough Work',
                'Good Coordination',
                'Would Recommend',
            ],

            openDrawer(item) {
                const isAccountLog = item && Object.prototype.hasOwnProperty.call(item, 'created_at') && !Object.prototype.hasOwnProperty.call(item, 'price');

                if (isAccountLog) {
                    this.selectedItem = {
                        ...item,
                        title: item.type === 'login' ? 'Logged In' : item.type === 'logout' ? 'Logged Out' : 'Profile Updated',
                        typeLabel: 'Account Activity',
                        statusLabel: item.type === 'login' ? 'Login' : item.type === 'logout' ? 'Logout' : 'Edited',
                        statusRaw: item.type,
                        drawerIcon: item.type === 'login' ? 'fa-right-to-bracket text-green-600 dark:text-green-400' : item.type === 'logout' ? 'fa-right-from-bracket text-red-600 dark:text-red-400' : 'fa-user-pen text-blue-600 dark:text-blue-400',
                        drawerIconBg: item.type === 'login' ? 'bg-green-100 dark:bg-green-900/30' : item.type === 'logout' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-blue-100 dark:bg-blue-900/30',
                        statusBadgeClass: item.type === 'login' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : item.type === 'logout' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                    };
                } else {
                    this.selectedItem = {
                        ...item,
                        typeLabel: item.type ? item.type.charAt(0).toUpperCase() + item.type.slice(1) : 'Activity',
                        category: item.category || item.task || item.typeLabel || item.title,
                        statusLabel: item.status ? item.status.charAt(0).toUpperCase() + item.status.slice(1) : 'N/A',
                        statusRaw: item.status || null,
                        statusBadgeClass: item.status === 'completed' || item.status === 'added'
                            ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                            : item.status === 'pending'
                                ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'
                                : item.status === 'updated'
                                    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
                                    : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                    };
                }

                this.showDrawer = true;
            },

            closeDrawer() {
                this.showDrawer = false;
            },

            openReviewModal() {
                this.showReviewModal = true;
                this.selectedRating = 0;
                this.feedbackText = '';
                this.selectedKeywords = [];
            },

            closeReviewModal() {
                this.showReviewModal = false;
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

            async submitReview() {
                if (this.selectedRating === 0) {
                    window.showErrorDialog('Rating Required', 'Please select a rating before submitting your review.');
                    return;
                }

                if (!this.selectedItem?.id) {
                    window.showErrorDialog('Missing Item', 'Please reopen the drawer and try again.');
                    return;
                }

                try {
                    const response = await fetch(`{{ route('manager.history.review', ['taskId' => '__TASK_ID__']) }}`.replace('__TASK_ID__', this.selectedItem.id), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            rating: this.selectedRating,
                            feedback_tags: this.selectedKeywords,
                            review_text: this.feedbackText,
                        }),
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        window.showErrorDialog('Review Failed', data.message || 'Unable to submit the review.');
                        return;
                    }

                    // Update the activity in real-time
                    const updatedActivity = this.activities.find(a => a.id === this.selectedItem.id);
                    if (updatedActivity) {
                        updatedActivity.reviewed = true;
                        updatedActivity.review = {
                            rating: this.selectedRating,
                            feedback_tags: this.selectedKeywords,
                            review_text: this.feedbackText,
                        };
                        // Update selectedItem so drawer reflects changes
                        this.selectedItem.reviewed = true;
                        this.selectedItem.review = updatedActivity.review;
                    }

                    window.showSuccessDialog('Review Submitted', data.message || 'Your review has been submitted successfully.');
                    this.closeReviewModal();
                    this.closeDrawer();
                } catch (error) {
                    window.showErrorDialog('Review Failed', 'A network error occurred while submitting your review.');
                }
            },
        };
    }
    </script>
    </div>
    </x-skeleton-page>
</x-layouts.general-manager>