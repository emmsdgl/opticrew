<x-layouts.general-manager :title="'Activity'">
    <div class="flex flex-col gap-6 w-full">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Activity</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Stay updated with recent events</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-2">
            <div class="flex gap-2 overflow-x-auto">
                <button class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white">
                    All
                </button>
                <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    Tasks
                </button>
                <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    Checklist
                </button>
                <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    Reports
                </button>
            </div>
        </div>

        <!-- Activity List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">Most Recent</span>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($activities ?? [] as $activity)
                    <div class="p-4 md:p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    @switch($activity['type'] ?? 'info')
                                        @case('task')
                                            bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                                            @break
                                        @case('checklist')
                                            bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400
                                            @break
                                        @case('report')
                                            bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400
                                            @break
                                        @case('warning')
                                            bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400
                                            @break
                                        @default
                                            bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                                    @endswitch
                                ">
                                    <i class="fa-solid fa-{{ $activity['icon'] ?? 'circle-info' }}"></i>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $activity['title'] ?? 'Activity' }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                    {{ $activity['description'] ?? '' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    {{ $activity['time'] ?? '' }}
                                </p>
                            </div>

                            <!-- Status Label -->
                            @if(isset($activity['status']))
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @switch($activity['status'])
                                            @case('completed')
                                                bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                                @break
                                            @case('pending')
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                        @endswitch
                                    ">
                                        {{ ucfirst($activity['status']) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-bell-slash text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">No recent activity</p>
                    </div>
                @endforelse
            </div>

            @if(count($activities ?? []) > 0)
                <!-- Load More -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 text-center">
                    <button class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                        Load More
                    </button>
                </div>
            @endif
        </div>
    </div>
</x-layouts.general-manager>
