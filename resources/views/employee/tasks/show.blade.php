<x-layouts.general-employee :title="'Task Details'">

    {{-- MOBILE LAYOUT (< 1024px) - Hidden on large screens --}}
    <div class="lg:hidden p-4">
        <div class="mb-4">
            <a href="{{ route('employee.tasks') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Tasks</span>
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $task->task_description }}</h1>

            <div class="space-y-4">
                <!-- Status Badge -->
                <div>
                    @php
                        $statusColors = [
                            'Scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                            'In Progress' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            'On Hold' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                            'Completed' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300',
                            'Pending' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                        ];
                        $statusClass = $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                        {{ $task->status }}
                    </span>
                </div>

                <!-- Location -->
                <div class="flex items-start gap-3">
                    <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Location</p>
                        <p class="text-gray-900 dark:text-white font-medium">
                            {{ $task->location ? $task->location->location_name : 'External Client' }}
                        </p>
                    </div>
                </div>

                <!-- Date & Time -->
                <div class="flex items-start gap-3">
                    <i class="fas fa-calendar text-gray-400 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Scheduled</p>
                        <p class="text-gray-900 dark:text-white font-medium">
                            {{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}
                            @if($task->scheduled_time)
                                at {{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Duration -->
                @if($task->estimated_duration_minutes)
                <div class="flex items-start gap-3">
                    <i class="fas fa-clock text-gray-400 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Duration</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $task->estimated_duration_minutes }} minutes</p>
                    </div>
                </div>
                @endif

                <!-- Team Members -->
                @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                <div class="flex items-start gap-3">
                    <i class="fas fa-users text-gray-400 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Team Members</p>
                        <div class="space-y-1 mt-1">
                            @foreach($task->optimizationTeam->members as $member)
                                <p class="text-gray-900 dark:text-white">
                                    {{ $member->employee->user->name }}
                                    @if($member->role === 'driver')
                                        <span class="ml-2 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 px-2 py-0.5 rounded">Driver</span>
                                    @endif
                                </p>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Vehicle -->
                @if($task->optimizationTeam && $task->optimizationTeam->car)
                <div class="flex items-start gap-3">
                    <i class="fas fa-car text-gray-400 mt-1"></i>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Vehicle</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $task->optimizationTeam->car->car_name }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- DESKTOP LAYOUT (≥ 1024px) - Hidden on small screens --}}
    <section role="status" class="w-full hidden lg:flex flex-col gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('employee.tasks') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Back to Tasks</span>
            </a>
        </div>

        <!-- Task Detail Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <!-- Header with Status -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-700 dark:to-blue-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-white">{{ $task->task_description }}</h1>
                    @php
                        $statusColors = [
                            'Scheduled' => 'bg-blue-100 text-blue-800',
                            'In Progress' => 'bg-green-100 text-green-800',
                            'On Hold' => 'bg-yellow-100 text-yellow-800',
                            'Completed' => 'bg-gray-100 text-gray-800',
                            'Pending' => 'bg-orange-100 text-orange-800',
                        ];
                        $statusClass = $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $statusClass }}">
                        {{ $task->status }}
                    </span>
                </div>
            </div>

            <!-- Task Details -->
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Left Column -->
                    <div class="space-y-4">
                        <!-- Location -->
                        <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</p>
                                <p class="text-gray-900 dark:text-white font-semibold text-lg mt-1">
                                    {{ $task->location ? $task->location->location_name : 'External Client' }}
                                </p>
                                @if($task->location && $task->location->address)
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $task->location->address }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Scheduled Date & Time</p>
                                <p class="text-gray-900 dark:text-white font-semibold text-lg mt-1">
                                    {{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}
                                </p>
                                @if($task->scheduled_time)
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Duration -->
                        @if($task->estimated_duration_minutes)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Estimated Duration</p>
                                <p class="text-gray-900 dark:text-white font-semibold text-lg mt-1">
                                    {{ $task->estimated_duration_minutes }} minutes
                                    <span class="text-sm text-gray-600 dark:text-gray-300">
                                        ({{ number_format($task->estimated_duration_minutes / 60, 1) }} hours)
                                    </span>
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <!-- Team Members -->
                        @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Team Members</p>
                            </div>
                            <div class="space-y-2">
                                @foreach($task->optimizationTeam->members as $member)
                                    <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-600 rounded">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-semibold">
                                                {{ strtoupper(substr($member->employee->user->name, 0, 1)) }}
                                            </div>
                                            <span class="text-gray-900 dark:text-white font-medium">
                                                {{ $member->employee->user->name }}
                                            </span>
                                        </div>
                                        @if($member->role === 'driver')
                                            <span class="text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 px-2 py-1 rounded font-medium">
                                                Driver
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Vehicle -->
                        @if($task->optimizationTeam && $task->optimizationTeam->car)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="fas fa-car text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Vehicle</p>
                                <p class="text-gray-900 dark:text-white font-semibold text-lg mt-1">
                                    {{ $task->optimizationTeam->car->car_name }}
                                </p>
                            </div>
                        </div>
                        @endif

                        <!-- Assigned By -->
                        @if($task->assigned_by)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-tie text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned By</p>
                                <p class="text-gray-900 dark:text-white font-semibold text-lg mt-1">
                                    {{ $task->assigned_by->name }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons (placeholder for future functionality) -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <div class="flex flex-wrap gap-3">
                        <button class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <i class="fas fa-play mr-2"></i>
                            Start Task
                        </button>
                        <button class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <i class="fas fa-check mr-2"></i>
                            Mark as Complete
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Task actions will be enabled once the task management feature is fully implemented.
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.general-employee>
