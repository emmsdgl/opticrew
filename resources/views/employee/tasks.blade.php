
<x-layouts.general-employee :title="'Tasks'">

    {{-- MOBILE LAYOUT (< 1024px) - Hidden on large screens --}}
    <div class="lg:hidden">
        @include('employee.mobile.tasks')
    </div>

    {{-- DESKTOP LAYOUT (≥ 1024px) - Hidden on small screens --}}
    <section role="status" class="hidden lg:flex flex-col gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">

        <!-- Flash Messages (session based) -->
        @if(session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6" role="alert">
                <p class="font-semibold">Success!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
                <p class="font-semibold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Compact Clock In/Out Status Indicator -->
        @if($isClockedIn)
            {{-- Compact status indicator when clocked in --}}
            <div class="flex items-center gap-2 p-3 mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-sm font-medium text-green-800 dark:text-green-300">
                    <i class="fas fa-check-circle"></i> Clocked in at {{ $clockInTime }}
                </span>
            </div>
        @else
            {{-- Warning banner when not clocked in --}}
            <div class="mb-4 p-4 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 rounded-lg">
                <div class="flex flex-col sm:flex-row items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-xl mt-0.5"></i>
                    <div class="flex-1">
                        <h4 class="font-bold text-orange-800 dark:text-orange-300 text-sm">Clock In Required</h4>
                        <p class="text-orange-700 dark:text-orange-400 text-sm mt-1">
                            You must clock in before starting tasks. All task actions will be disabled until you clock in.
                        </p>
                        <a href="{{ route('employee.dashboard') }}"
                           class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">
                            <i class="fas fa-arrow-left"></i>
                            Go to Dashboard to Clock In
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Today's Tasks Section -->
        <div class="flex flex-col gap-6 flex-1 w-full rounded-lg p-4" x-data="{ filter: 'all' }">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <x-labelwithvalue label="My Tasks for Today" :count="'(' . $todayTasks->count() . ')'" />

                {{-- Filter Buttons --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <button @click="filter = 'all'"
                            :class="filter === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:shadow-md">
                        All ({{ $todayTasks->count() }})
                    </button>
                    <button @click="filter = 'scheduled'"
                            :class="filter === 'scheduled' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:shadow-md">
                        Scheduled ({{ $todayTasks->where('status', 'Scheduled')->count() }})
                    </button>
                    <button @click="filter = 'in-progress'"
                            :class="filter === 'in-progress' ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:shadow-md">
                        In Progress ({{ $todayTasks->where('status', 'In Progress')->count() }})
                    </button>
                    <button @click="filter = 'on-hold'"
                            :class="filter === 'on-hold' ? 'bg-yellow-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:shadow-md">
                        On Hold ({{ $todayTasks->where('status', 'On Hold')->count() }})
                    </button>
                    <button @click="filter = 'completed'"
                            :class="filter === 'completed' ? 'bg-gray-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors hover:shadow-md">
                        Completed ({{ $todayTasks->where('status', 'Completed')->count() }})
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($todayTasks as $task)
                    <div x-show="filter === 'all' ||
                                (filter === 'scheduled' && '{{ $task->status }}' === 'Scheduled') ||
                                (filter === 'in-progress' && '{{ $task->status }}' === 'In Progress') ||
                                (filter === 'on-hold' && '{{ $task->status }}' === 'On Hold') ||
                                (filter === 'completed' && '{{ $task->status }}' === 'Completed')"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        <x-task-action-card :task="$task" :isClockedIn="$isClockedIn" />
                    </div>
                @empty
                    <div class="flex flex-col w-full rounded-xl p-12 text-center">
                        <i class="fa-solid fa-magnifying-glass text-3xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">No tasks assigned for today</p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Check back later or contact your supervisor</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Divider -->
        <hr class="my-6 border-gray-300 dark:border-gray-700">

        <!-- Upcoming Tasks Section -->
        <div class="flex flex-col gap-6 w-full rounded-lg p-4">
            <x-labelwithvalue label="Upcoming Tasks" :count="'(' . $upcomingTasks->count() . ')'" />

            {{-- Simple list view for upcoming tasks (no action buttons needed) --}}
            <div class="space-y-4">
                @forelse($upcomingTasks as $task)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border-l-4 border-gray-400">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-semibold rounded-full">
                                {{ $task->status }}
                            </span>
                            <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}
                                @if($task->scheduled_time)
                                    · {{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}
                                @endif
                            </span>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $task->task_description }}</h4>
                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                            <span class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                                {{ $task->location->location_name ?? 'External Client Task' }}
                            </span>
                            @if($task->estimated_duration_minutes)
                                <span class="flex items-center">
                                    <i class="fas fa-clock mr-2 text-orange-500"></i>
                                    {{ $task->estimated_duration_minutes }} min
                                </span>
                            @endif
                            @if($task->optimizationTeam && $task->optimizationTeam->car)
                                <span class="flex items-center">
                                    <i class="fas fa-car mr-2 text-purple-500"></i>
                                    {{ $task->optimizationTeam->car->car_name }}
                                </span>
                            @endif
                        </div>
                        @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                        <div class="mt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Team:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($task->optimizationTeam->members as $member)
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">
                                        {{ $member->employee->user->name ?? 'Unknown' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-xl p-8 text-center">
                        <i class="fa-solid fa-calendar-week text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No upcoming tasks scheduled</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- OLD GANTT CHART SECTION - REMOVED, replaced with functional task cards above -->
        {{-- Keep this commented block for reference if frontend developer wants gantt chart back later
        <div class="flex flex-col gap-6 flex-1 w-full rounded-lg p-4">
            <x-labelwithvalue label="My Schedule" count="" />
            @php
                $tasks = [
                    [
                        'label' => 'A',
                        'name' => 'ABC Company',
                        'subtitle' => 'Deep Cleaning',
                        'color' => '#3B82F6', // Blue
                        'start' => '2025-10-28',
                        'end' => '2025-10-31',
                        'percentage' => 55,
                        'due_date' => '2025-10-31',
                        'due_time' => '2:00 pm',
                        'team_name' => 'Team 1',
                        'team_members' => [
                            ['name' => 'John Doe', 'avatar' => ''],
                            ['name' => 'Jane Smith', 'avatar' => ''],
                            ['name' => 'Bob Johnson', 'avatar' => ''],
                        ]
                    ],
                    [
                        'label' => 'B',
                        'name' => 'StratEdge Consulting',
                        'subtitle' => 'Daily Room Cleaning',
                        'color' => '#9333EA', // Purple
                        'start' => '2025-10-29',
                        'end' => '2025-11-05',
                        'percentage' => 80,
                        'due_date' => '2025-11-05',
                        'due_time' => '5:00 pm',
                        'team_name' => 'Team 2',
                        'team_members' => [
                            ['name' => 'Alice Cooper', 'avatar' => ''],
                            ['name' => 'Charlie Brown', 'avatar' => ''],
                        ]
                    ],
                    [
                        'label' => 'C',
                        'name' => 'Noventis Corp',
                        'subtitle' => 'Full Daily Cleaning',
                        'color' => '#EC4899', // Pink
                        'start' => '2025-10-30',
                        'end' => '2025-11-03',
                        'percentage' => 65,
                        'due_date' => '2025-11-03',
                        'due_time' => '3:30 pm',
                        'team_name' => 'Team 3',
                        'team_members' => [
                            ['name' => 'David Lee', 'avatar' => ''],
                            ['name' => 'Eva Green', 'avatar' => ''],
                            ['name' => 'Frank White', 'avatar' => ''],
                            ['name' => 'Grace Hill', 'avatar' => ''],
                        ]
                    ],
                    [
                        'label' => 'D',
                        'name' => 'IntegriCore Partners',
                        'subtitle' => 'Snowout Cleaning',
                        'color' => '#F59E0B', // Orange/Yellow
                        'start' => '2025-11-01',
                        'end' => '2025-11-05',
                        'percentage' => 75,
                        'due_date' => '2025-11-05',
                        'due_time' => '11:00 am',
                        'team_name' => 'Team 1',
                        'team_members' => [
                            ['name' => 'Henry Ford', 'avatar' => ''],
                            ['name' => 'Iris Watson', 'avatar' => ''],
                        ]
                    ],
                ];
            @endphp

        --}}
    </section>
</x-layouts.general-employee>

@push('scripts')
<script>
// Listen for task-updated events and show toast notification
document.addEventListener('DOMContentLoaded', function() {
    let taskUpdateToast = null;

    window.addEventListener('task-updated', function(event) {
        const detail = event.detail;

        // Remove existing toast if any
        if (taskUpdateToast) {
            taskUpdateToast.remove();
        }

        // Create toast notification
        taskUpdateToast = document.createElement('div');
        taskUpdateToast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 flex items-center gap-3';
        taskUpdateToast.innerHTML = `
            <i class="fas fa-check-circle text-lg"></i>
            <span class="font-medium">Task updated successfully!</span>
        `;

        document.body.appendChild(taskUpdateToast);

        // Animate in
        setTimeout(() => {
            taskUpdateToast.style.transform = 'translateY(0)';
            taskUpdateToast.style.opacity = '1';
        }, 10);

        // Auto remove after 3 seconds
        setTimeout(() => {
            taskUpdateToast.style.transform = 'translateY(100px)';
            taskUpdateToast.style.opacity = '0';
            setTimeout(() => {
                if (taskUpdateToast && taskUpdateToast.parentNode) {
                    taskUpdateToast.remove();
                }
            }, 300);
        }, 3000);
    });
});
</script>
@endpush