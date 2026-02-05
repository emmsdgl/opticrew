<x-layouts.general-employer :title="'Task Details'">
    @php
        // Determine where the admin came from
        $from = request()->query('from', 'tasks');

        // Map source pages to their labels and routes
        $backNavigation = [
            'tasks' => [
                'label' => 'Tasks',
                'route' => route('admin.tasks')
            ],
        ];

        // Get the back navigation details
        $backNav = $backNavigation[$from] ?? $backNavigation['tasks'];
        $backLabel = $backNav['label'];
        $backUrl = $backNav['route'];
    @endphp

    <section role="status" class="w-full flex flex-col gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ $backUrl }}"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium text-sm">Back to {{ $backLabel }}</span>
            </a>

            {{-- Completed Task Badge --}}
            @if($task->employee_approved === true && $task->status === 'Completed')
                <div class="mt-3 bg-green-100 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg px-4 py-2 inline-flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    <span class="text-sm font-semibold text-green-700 dark:text-green-300">Task Completed</span>
                </div>
            @endif
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column - Task Details -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Task Title and Meta -->
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $task->task_description }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Task #{{ $task->id }} • Created {{ \Carbon\Carbon::parse($task->created_at)->format('M d, Y') }}
                    </p>
                </div>

                <!-- Task Information Card -->
                <div class="p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Task Information</h3>

                    <div class="space-y-4">
                        <!-- Client -->
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Client</span>
                            <span class="text-sm text-gray-900 dark:text-white">
                                @if($task->location && $task->location->contractedClient)
                                    {{ $task->location->contractedClient->name }}
                                @elseif($task->client)
                                    {{ $task->client->first_name }} {{ $task->client->last_name }}
                                @else
                                    Unknown Client
                                @endif
                            </span>
                        </div>

                        <!-- Location -->
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Location</span>
                            <span class="text-sm text-gray-900 dark:text-white">
                                @if($task->location)
                                    {{ $task->location->location_name }}
                                @else
                                    External Client
                                @endif
                            </span>
                        </div>

                        <!-- Scheduled Date -->
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Scheduled Date</span>
                            <span class="text-sm text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($task->scheduled_date)->format('l, F d, Y') }}
                            </span>
                        </div>

                        <!-- Scheduled Time -->
                        @if($task->scheduled_time)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Scheduled Time</span>
                                <span class="text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}
                                </span>
                            </div>
                        @endif

                        <!-- Duration -->
                        @if($task->estimated_duration_minutes)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Estimated Duration</span>
                                <span class="text-sm text-gray-900 dark:text-white">
                                    {{ $task->estimated_duration_minutes }} minutes ({{ number_format($task->estimated_duration_minutes / 60, 1) }} hours)
                                </span>
                            </div>
                        @endif

                        <!-- Status -->
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</span>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($task->status === 'Completed') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                @elseif($task->status === 'In Progress') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400
                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                                @endif">
                                {{ $task->status }}
                            </span>
                        </div>

                        <!-- Employee Approved Status -->
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Employee Approval</span>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($task->employee_approved === true) bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                @elseif($task->employee_approved === false) bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                                @endif">
                                @if($task->employee_approved === true) Approved
                                @elseif($task->employee_approved === false) Declined
                                @else Pending Approval
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Special Requests / Notes -->
                @if($task->notes)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Special Requests / Notes</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $task->notes }}</p>
                    </div>
                @endif

            </div>

            <!-- Right Column - Team & Additional Info -->
            <div class="space-y-6">

                <!-- Assigned Team Card -->
                @if($task->optimizationTeam)
                    <div class="p-6">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Assigned Team</h3>

                        <div class="space-y-3">
                            @if($task->optimizationTeam->car)
                                <div class="flex items-center gap-3 p-3 rounded-lg">
                                    <i class="fas fa-car text-blue-600 dark:text-blue-400"></i>
                                    <div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Vehicle</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->optimizationTeam->car->car_name }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($task->optimizationTeam->members && $task->optimizationTeam->members->isNotEmpty())
                                <div class="space-y-2">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Team Members</p>
                                    @foreach($task->optimizationTeam->members as $member)
                                        @if($member->employee && $member->employee->user)
                                            <div class="flex items-center gap-3 p-2 rounded">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                                    <span class="text-xs font-semibold text-white">
                                                        {{ strtoupper(substr($member->employee->user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <span class="text-sm text-gray-900 dark:text-white">{{ $member->employee->user->name }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Assigned By -->
                @if($task->assignedBy)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Assigned By</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $task->assignedBy->name }}</p>
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Timeline</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Created</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($task->created_at)->format('M d, Y g:i A') }}</p>
                        </div>
                        @if($task->employee_approved_at)
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Employee {{ $task->employee_approved ? 'Approved' : 'Declined' }}</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($task->employee_approved_at)->format('M d, Y g:i A') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </section>
</x-layouts.general-employer>
