<x-layouts.general-employee :title="'Performance'">


    <!-- Left Panel - Charts -->
    <div
        class="flex flex-col gap-6 flex-1 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
        <!-- Inner Up - Performance Line Graph -->
        <div class="flex flex-row justify-between w-full">
            <x-labelwithvalue label="My Performance" count="" />
            @php
                $timeOptions = ['All', 'Today', 'Yesterday', 'Last 7 days', 'Last 30 days'];
            @endphp
            <x-dropdown :options="$timeOptions" id="dropdown-time" />
        </div>


        <div
            class="w-full mt-6 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-48 sm:h-56 md:h-64 lg:h-1/3">
        </div>

        <!-- Inner Bottom - Recently Completed Task List -->
        <x-labelwithvalue label="Recently Completed Tasks" count="(5)" />
        <div
            class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-fit sm:h-fit md:h-fit lg:h-fit">
            <div class="w-full flex flex-col">

                <div class="space-y-4 h-auto">
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

                    @forelse($tasks as $task)
                        <x-gantttaskitem :label="$task['label']" :name="$task['name']" :subtitle="$task['subtitle']"
                            :color="$task['color']" :percentage="$task['percentage']" :dueDate="$task['due_date'] ?? null"
                            :dueTime="$task['due_time'] ?? null" :teamName="$task['team_name'] ?? 'Team 1'"
                            :teamMembers="$task['team_members'] ?? []" />
                    @empty
                        <div
                            class="text-center py-12 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-xl">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400 dark:text-gray-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-lg font-medium">No tasks assigned</p>
                            <p class="text-sm">Check back later for new assignments</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel - Performance Summary -->
    <div
        class="flex flex-col gap-6 w-full lg:w-1/3 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">

        <!-- Inner Up - Performance KPI Cards Summary -->
        <div
            class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-fit sm:h-fit md:h-fit">
            <x-profilesummary title="" :cards="[
        [
            'label' => 'Total Tasks Completed',
            'amount' => '30',
            'description' => 'Boost your productivity today',
            'icon' => '<i class=&quot;fas fa-check-circle&quot;></i>',
            'percentage' => '+12%',
            'percentageColor' => '#10b981',
            'bgColor' => '#fef3c7',
        ],
        [
            'label' => 'Incomplete Tasks',
            'amount' => '1,240',
            'description' => 'Check out your list',
            'icon' => '<i class=&quot;fas fa-times-circle&quot;></i>',
            'percentage' => '+8%',
            'percentageColor' => '#3b82f6',
        ],
        [
            'label' => 'Pending Tasks',
            'amount' => '1,240',
            'description' => 'Your tasks await',
            'icon' => '<i class=&quot;fas fa-hourglass-half&quot;></i>',
            'percentage' => '+8%',
            'percentageColor' => '#3b82f6',
        ],
    ]" />
        </div>

        <!-- Inner Down - Attendance Summary -->
        <div
            class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-auto sm:h-56 md:h-auto overflow-y-auto">

            <div class="w-full flex flex-col">

            </div>
        </div>
    </div>
</x-layouts.general-employee>
@stack('scripts')