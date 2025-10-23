
<x-layouts.general-employee :title="'Tasks'">

    <section role="status" class="flex flex-col lg:flex-col gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">

        <!-- Flash Messages (session based) -->
        @if(session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6" role="alert">
                <p class="font-semibold">Success!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Today's Tasks Section -->
        <div class="flex flex-col gap-6 flex-1 w-full rounded-lg p-4">
            <x-labelwithvalue label="My Tasks for Today" :count="'(' . $todayTasks->count() . ')'" />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($todayTasks as $task)
                    <x-task-action-card :task="$task" />
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
                            </span>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">{{ $task->task_description }}</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-sm flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            {{ $task->location->location_name ?? 'External Client Task' }}
                        </p>
                        @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                        <div class="mt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Team:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($task->optimizationTeam->members as $member)
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">
                                        {{ $member->employee->full_name }}
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