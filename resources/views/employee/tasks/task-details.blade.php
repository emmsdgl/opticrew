<x-layouts.general-employee :title="'Task Details'">
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800">
        
        {{-- MOBILE LAYOUT --}}
        <div class="lg:hidden">
            <!-- Header with Back Button -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-10">
                <div class="px-4 py-3 flex items-center">
                    <a href="{{ route('employee.tasks') }}" class="mr-3 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Hero Illustration Section -->
            <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-8">
                <div class="flex justify-center mb-4">
                    <div class="w-48 h-48 relative">
                        <!-- Illustration placeholder - replace with actual illustration -->
                        <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-2xl flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-tasks text-6xl text-blue-600 dark:text-blue-400 mb-2"></i>
                                <div class="flex justify-center gap-2 mt-4">
                                    <div class="w-12 h-12 bg-blue-600 rounded-full"></div>
                                    <div class="w-12 h-12 bg-cyan-500 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-2">Task Details</h1>
                
                <!-- Assignment Info -->
                <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                    <p>The service is availed by <span class="font-semibold text-gray-900 dark:text-white">{{ $task->location ? $task->location->location_name : 'External Client' }}</span>. This task has been</p>
                    <p>assigned to you and should be started in 
                        <span class="font-bold text-green-600 dark:text-green-400">
                            @php
                                $scheduledDate = \Carbon\Carbon::parse($task->scheduled_date);
                                if ($task->scheduled_time) {
                                    $scheduledTime = \Carbon\Carbon::parse($task->scheduled_time);
                                    $scheduledDateTime = $scheduledDate->setTimeFromTimeString($scheduledTime->format('H:i:s'));
                                } else {
                                    $scheduledDateTime = $scheduledDate;
                                }
                                $now = \Carbon\Carbon::now();
                                $diff = $now->diff($scheduledDateTime);
                                $timeRemaining = '';
                                if ($diff->d > 0) {
                                    $timeRemaining = $diff->d . ' days and ' . $diff->h . ' hours';
                                } elseif ($diff->h > 0) {
                                    $timeRemaining = $diff->h . ' hrs and ' . $diff->i . ' mins';
                                } else {
                                    $timeRemaining = $diff->i . ' mins';
                                }
                            @endphp
                            {{ $timeRemaining }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Task Details Summary -->
            <div class="px-6 py-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Tasks Details Summary</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">View the details for this task</p>

                <!-- Details List -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm divide-y divide-gray-100 dark:divide-gray-700">
                    
                    <!-- Task ID -->
                    <div class="flex justify-between items-center py-4 px-5">
                        <span class="text-gray-600 dark:text-gray-400 text-sm">Task ID</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $task->id }}</span>
                    </div>

                    <!-- Task Date -->
                    <div class="flex justify-between items-center py-4 px-5">
                        <span class="text-gray-600 dark:text-gray-400 text-sm">Task Date</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($task->scheduled_date)->format('Y-m-d') }}
                        </span>
                    </div>

                    <!-- Task Starting Time -->
                    @if($task->scheduled_time)
                    <div class="flex justify-between items-center py-4 px-5">
                        <span class="text-gray-600 dark:text-gray-400 text-sm">Task Starting Time</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}
                        </span>
                    </div>
                    @endif

                    <!-- Task Type -->
                    <div class="flex justify-between items-center py-4 px-5">
                        <span class="text-gray-600 dark:text-gray-400 text-sm">Task Type</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $task->task_description }}</span>
                    </div>

                    <!-- Task Location -->
                    <div class="flex justify-between items-center py-4 px-5">
                        <span class="text-gray-600 dark:text-gray-400 text-sm">Task Location</span>
                        <span class="font-semibold text-gray-900 dark:text-white text-right">
                            {{ $task->location ? $task->location->address : 'N/A' }}
                        </span>
                    </div>

                    <!-- Task Members -->
                    @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                    <div class="flex justify-between items-center py-4 px-5">
                        <span class="text-gray-600 dark:text-gray-400 text-sm">Task Members</span>
                        <div class="flex -space-x-2">
                            @foreach($task->optimizationTeam->members->take(3) as $member)
                                <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-semibold text-gray-700 dark:text-gray-200">
                                    {{ strtoupper(substr($member->employee->user->name, 0, 1)) }}
                                </div>
                            @endforeach
                            @if($task->optimizationTeam->members->count() > 3)
                                <div class="w-8 h-8 rounded-full bg-gray-400 dark:bg-gray-500 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-semibold text-white">
                                    +{{ $task->optimizationTeam->members->count() - 3 }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Special Requests -->
                    <div class="py-4 px-5">
                        <span class="text-gray-600 dark:text-gray-400 text-sm block mb-2">Special Requests</span>
                        <p class="text-sm text-gray-900 dark:text-white leading-relaxed">
                            {{ $task->notes ?? 'No special requests for this task.' }}
                        </p>
                    </div>
                </div>

                <!-- Pending Notice -->
                @if($task->status === 'Pending' || $task->status === 'Scheduled')
                <div class="mt-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-4">
                    <p class="text-sm text-orange-800 dark:text-orange-300 text-center">
                        <i class="fas fa-clock mr-2"></i>
                        This task is currently pending and should be accepted by you, if preferred
                    </p>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="mt-6 flex gap-3">
                    <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                        Accept
                    </button>
                    <button class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                        Reject
                    </button>
                </div>
            </div>
        </div>

        {{-- DESKTOP LAYOUT --}}
        <div class="hidden lg:block">
            <section role="status" class="max-w-7xl mx-auto p-6 min-h-[calc(100vh-4rem)]">
                
                <!-- Back Button -->
                <div class="my-6">
                    <a href="{{ route('employee.tasks') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors group">
                        <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="font-medium text-sm">Back to Tasks</span>
                    </a>
                </div>

                <div class="grid lg:grid-cols-5 gap-8">
                    
                    <!-- Left Column - Illustration & Summary (2 columns) -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Illustration Card -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                            <div class="flex justify-center mb-6">
                                <div class="w-64 h-64 relative">
                                    <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-2xl flex items-center justify-center">
                                        <div class="text-center">
                                            <i class="fas fa-tasks text-8xl text-blue-600 dark:text-blue-400 mb-4"></i>
                                            <div class="flex justify-center gap-3 mt-6">
                                                <div class="w-16 h-16 bg-blue-600 rounded-full"></div>
                                                <div class="w-16 h-16 bg-cyan-500 rounded-full"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white text-center mb-3">Task Details</h1>
                            
                            <div class="text-center text-gray-600 dark:text-gray-400 space-y-1">
                                <p>The service is availed by</p>
                                <p class="font-semibold text-gray-900 dark:text-white text-lg">
                                    {{ $task->location ? $task->location->location_name : 'External Client' }}
                                </p>
                                <p class="mt-3">This task has been assigned to you and should be started in</p>
                                <p class="font-bold text-green-600 dark:text-green-400 text-xl">
                                    @php
                                        $scheduledDate = \Carbon\Carbon::parse($task->scheduled_date);
                                        if ($task->scheduled_time) {
                                            $scheduledTime = \Carbon\Carbon::parse($task->scheduled_time);
                                            $scheduledDateTime = $scheduledDate->setTimeFromTimeString($scheduledTime->format('H:i:s'));
                                        } else {
                                            $scheduledDateTime = $scheduledDate;
                                        }
                                        $now = \Carbon\Carbon::now();
                                        $diff = $now->diff($scheduledDateTime);
                                        $timeRemaining = '';
                                        if ($diff->d > 0) {
                                            $timeRemaining = $diff->d . ' days and ' . $diff->h . ' hours';
                                        } elseif ($diff->h > 0) {
                                            $timeRemaining = $diff->h . ' hrs and ' . $diff->i . ' mins';
                                        } else {
                                            $timeRemaining = $diff->i . ' mins';
                                        }
                                    @endphp
                                    {{ $timeRemaining }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Current Status</span>
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
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold {{ $statusClass }}">
                                    {{ $task->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Task Details (3 columns) -->
                    <div class="lg:col-span-3">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                            
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 px-8 py-6">
                                <h2 class="text-2xl font-bold text-white">Tasks Details Summary</h2>
                                <p class="text-blue-100 mt-1">View the details for this task</p>
                            </div>

                            <!-- Details Grid -->
                            <div class="p-8">
                                <div class="grid md:grid-cols-2 gap-6">
                                    
                                    <!-- Task ID -->
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Task ID</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $task->id }}</p>
                                    </div>

                                    <!-- Task Date -->
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Task Date</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($task->scheduled_date)->format('Y-m-d') }}
                                        </p>
                                    </div>

                                    <!-- Task Starting Time -->
                                    @if($task->scheduled_time)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Task Starting Time</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}
                                        </p>
                                    </div>
                                    @endif

                                    <!-- Task Type -->
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Task Type</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $task->task_description }}</p>
                                    </div>

                                    <!-- Task Location -->
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5 md:col-span-2">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Task Location</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ $task->location ? $task->location->address : 'N/A' }}
                                        </p>
                                    </div>

                                    <!-- Task Members -->
                                    @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Task Members</p>
                                        <div class="flex items-center gap-3">
                                            <div class="flex -space-x-3">
                                                @foreach($task->optimizationTeam->members->take(3) as $member)
                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-white dark:border-gray-800 flex items-center justify-center text-sm font-bold text-white shadow-lg">
                                                        {{ strtoupper(substr($member->employee->user->name, 0, 1)) }}
                                                    </div>
                                                @endforeach
                                                @if($task->optimizationTeam->members->count() > 3)
                                                    <div class="w-10 h-10 rounded-full bg-gray-500 border-2 border-white dark:border-gray-800 flex items-center justify-center text-sm font-bold text-white shadow-lg">
                                                        +{{ $task->optimizationTeam->members->count() - 3 }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $task->optimizationTeam->members->count() }} member{{ $task->optimizationTeam->members->count() > 1 ? 's' : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Vehicle -->
                                    @if($task->optimizationTeam && $task->optimizationTeam->car)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Assigned Vehicle</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ $task->optimizationTeam->car->car_name }}
                                        </p>
                                    </div>
                                    @endif

                                    <!-- Special Requests -->
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5 md:col-span-2">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Special Requests</p>
                                        <p class="text-gray-900 dark:text-white leading-relaxed">
                                            {{ $task->notes ?? 'No special requests for this task.' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Pending Notice -->
                                @if($task->status === 'Pending' || $task->status === 'Scheduled')
                                <div class="mt-6 bg-orange-50 dark:bg-orange-900/20 border-2 border-orange-200 dark:border-orange-800 rounded-xl p-5">
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-xl mt-0.5"></i>
                                        <p class="text-orange-800 dark:text-orange-300 font-medium">
                                            This task is currently pending and should be accepted by you, if preferred
                                        </p>
                                    </div>
                                </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="mt-8 flex gap-4">
                                    <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                                        <i class="fas fa-check mr-2"></i>
                                        Accept Task
                                    </button>
                                    <button class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                                        <i class="fas fa-times mr-2"></i>
                                        Reject Task
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layouts.general-employee>