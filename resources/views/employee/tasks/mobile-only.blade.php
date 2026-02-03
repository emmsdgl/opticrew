{{-- MOBILE LAYOUT (< 1024px) - Hidden on large screens --}}
    <div class="lg:hidden p-4">
        <div class="text-right w-full">
            <a href="{{ route('employee.tasks') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                <i class="fas fa-arrow-left"></i>
                <span class ="font-medium text-sm">Back to Tasks</span>
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