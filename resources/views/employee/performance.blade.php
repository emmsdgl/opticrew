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
            class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-80 sm:h-56 md:h-64 lg:h-80">
            @php
                // Change Amounts to the Number of Worked Hours
                $performanceData = [
                    'All' => [
                        'currentValue' => 892450,
                        'changeValue' => 45200,
                        'changePercent' => 5.34,
                        'values' => [750000, 780000, 810000, 840000, 870000, 892450, 920000, 950000, 980000, 1000000, 1020000, 1050000],
                        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        'dateRange' => 'Jan - Dec'
                    ],
                    'Today' => [
                        'currentValue' => 45800,
                        'changeValue' => 2300,
                        'changePercent' => 5.28,
                        'values' => [38000, 39500, 41000, 42500, 43800, 45800],
                        'labels' => ['12 AM', '4 AM', '8 AM', '12 PM', '4 PM', '8 PM'],
                        'dateRange' => '12 AM - 8 PM'
                    ],
                    'Yesterday' => [
                        'currentValue' => 43500,
                        'changeValue' => 1800,
                        'changePercent' => 4.32,
                        'values' => [38000, 39000, 40500, 41500, 42800, 43500],
                        'labels' => ['12 AM', '4 AM', '8 AM', '12 PM', '4 PM', '8 PM'],
                        'dateRange' => '12 AM - 8 PM'
                    ],
                    'Last 7 days' => [
                        'currentValue' => 312500,
                        'changeValue' => 18200,
                        'changePercent' => 6.18,
                        'values' => [40000, 42000, 45000, 48000, 50000, 44000, 43500],
                        'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        'dateRange' => 'Mon - Sun'
                    ],
                    'Last 30 days' => [
                        'currentValue' => 892450,
                        'changeValue' => 45200,
                        'changePercent' => 5.34,
                        'values' => [750000, 780000, 810000, 840000, 870000, 892450],
                        'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
                        'dateRange' => 'Week 1 - Week 6'
                    ],
                ];

                // Default data (All)
                $defaultData = $performanceData['All'];
            @endphp

            <!-- Line Chart Component -->
            <x-linechart title="Hours Worked" :currentValue="$defaultData['currentValue']"
                :changeValue="$defaultData['changeValue']" :changePercent="$defaultData['changePercent']"
                :chartData="$defaultData['values']" :chartLabels="$defaultData['labels']" chartColor="#8b5cf6"
                gradientStart="rgba(139, 92, 246, 0.2)" gradientEnd="rgba(139, 92, 246, 0)"
                :dateRange="$defaultData['dateRange']" />
        </div>

        <!-- Inner Bottom - Recently Completed Task List -->
        <x-labelwithvalue label="Recently Completed Tasks" count="(5)" />
        <div
            class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-full sm:h-full md:h-full lg:h-full">
            <div class="w-full flex flex-col h-full">

                <div class="scrollbar space-y-4 h-80 overflow-auto">
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
            class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-full sm:h-full md:h-full overflow-y-auto">

            <div class="w-full flex flex-col h-full justify-center align-items-center">
                @php
                    $attendanceData = [
                        ['label' => 'Present', 'current' => 10, 'total' => 50, 'color' => 'blue'],
                        ['label' => 'Days Off', 'current' => 50, 'total' => 50, 'color' => 'navy'],
                        ['label' => 'Absent', 'current' => 40, 'total' => 50, 'color' => 'cyan'],
                        ['label' => 'On Leave', 'current' => 5, 'total' => 50, 'color' => 'yellow'],
                    ];
                @endphp

                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg justify-center align-items-center h-fit p-6 mr-4 ml-4">
                    <div class="space-y-6 pr-6 pl-6">
                        @foreach($attendanceData as $data)
                            <x-progressdetails :label="$data['label']" :current="$data['current']" :total="$data['total']"
                                :color="$data['color']" />
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.general-employee>