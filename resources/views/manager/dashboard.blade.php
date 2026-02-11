<x-layouts.general-manager :title="'Manager Dashboard'">
    <div class="flex flex-col gap-6 w-full">
        <!-- Welcome Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                    Welcome back, {{ Auth::user()->name }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ now()->format('l, F j, Y') }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('manager.schedule') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fa-solid fa-plus"></i>
                    New Task
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total Tasks Today -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-5 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-500 dark:text-gray-400">Today's Tasks</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['todayTasks'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-list-check text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs">
                    <span class="text-green-600 dark:text-green-400 font-medium">{{ $stats['completedToday'] ?? 0 }} completed</span>
                </div>
            </div>

            <!-- Employees on Duty -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-5 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-500 dark:text-gray-400">On Duty</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['onDuty'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-users text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs">
                    <span class="text-gray-500 dark:text-gray-400">of {{ $stats['totalEmployees'] ?? 0 }} employees</span>
                </div>
            </div>

            <!-- This Week Tasks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-5 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-500 dark:text-gray-400">This Week</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['weekTasks'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-calendar-week text-purple-600 dark:text-purple-400"></i>
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs">
                    <span class="text-gray-500 dark:text-gray-400">scheduled tasks</span>
                </div>
            </div>

            <!-- Locations -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-5 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-500 dark:text-gray-400">Locations</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['locations'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-location-dot text-orange-600 dark:text-orange-400"></i>
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs">
                    <span class="text-gray-500 dark:text-gray-400">active locations</span>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Today's Tasks (Left Column - 2/3 width) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Today's Tasks</h2>
                    <a href="{{ route('manager.schedule') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        View All
                    </a>
                </div>
                <div class="p-4 md:p-5">
                    @if(count($todayTasks ?? []) > 0)
                        <div class="space-y-3">
                            @foreach($todayTasks as $task)
                                <div class="flex items-center gap-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <!-- Status Indicator -->
                                    <div class="flex-shrink-0">
                                        @switch($task->status)
                                            @case('Completed')
                                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                                @break
                                            @case('In Progress')
                                                <div class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></div>
                                                @break
                                            @case('On Hold')
                                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                                @break
                                            @default
                                                <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                                        @endswitch
                                    </div>

                                    <!-- Task Info -->
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $task->location->name ?? 'Unknown Location' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $task->scheduled_time ? \Carbon\Carbon::parse($task->scheduled_time)->format('H:i') : 'No time set' }}
                                            @if($task->duration)
                                                <span class="mx-1">•</span>
                                                {{ $task->duration }} min
                                            @endif
                                        </p>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="flex-shrink-0">
                                        @switch($task->status)
                                            @case('Completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                    Completed
                                                </span>
                                                @break
                                            @case('In Progress')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                    In Progress
                                                </span>
                                                @break
                                            @case('On Hold')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                    On Hold
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                    Scheduled
                                                </span>
                                        @endswitch
                                    </div>

                                    <!-- Team Count -->
                                    <div class="flex-shrink-0 flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                        <i class="fa-solid fa-user-group"></i>
                                        <span>{{ $task->assignedEmployees->count() ?? 0 }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                <i class="fa-solid fa-calendar-check text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">No tasks scheduled for today</p>
                            <a href="{{ route('manager.schedule') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-2 inline-block">
                                Create a new task
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Quick Stats & Activity -->
            <div class="space-y-6">
                <!-- Task Status Overview -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Task Overview</h2>
                    </div>
                    <div class="p-4 md:p-5 space-y-4">
                        <!-- Completed -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Completed</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $taskOverview['completed'] ?? 0 }}</span>
                        </div>
                        <!-- In Progress -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-300">In Progress</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $taskOverview['inProgress'] ?? 0 }}</span>
                        </div>
                        <!-- Scheduled -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Scheduled</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $taskOverview['scheduled'] ?? 0 }}</span>
                        </div>
                        <!-- On Hold -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-300">On Hold</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $taskOverview['onHold'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h2>
                        <a href="{{ route('manager.activity') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            View All
                        </a>
                    </div>
                    <div class="p-4 md:p-5">
                        @if(count($recentActivity ?? []) > 0)
                            <div class="space-y-4">
                                @foreach($recentActivity as $activity)
                                    <div class="flex gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <i class="fa-solid fa-{{ $activity['icon'] ?? 'circle-info' }} text-xs text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $activity['message'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $activity['time'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent activity</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.general-manager>
