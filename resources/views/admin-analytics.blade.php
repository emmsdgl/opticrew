<x-layouts.general-employer :title="'Analytics'">
    <div class="flex flex-col lg:flex-row md:gap-4 w-full">
        <!-- Left Panel - Main Content -->
        <div class="flex flex-col gap-4 md:gap-6 w-full rounded-lg p-3 h-fit lg:w-2/3 md:p-3">
            <!-- Card KPI Statistics Summary -->
            <x-labelwithvalue label="Summary" count="" />

            <div class="w-full rounded-lg h-fit sm:h-fit md:h-fit lg:h-fit">
                <!-- Inner Up - Performance KPI Cards Summary -->
                <div class="w-full rounded-lg p-3 md:p-4 flex-none h-fit sm:h-fit md:h-fit lg:h-fit">
                    @php
                        $kpiCards = [
                            [
                                'icon' => '<i class="fi fi-rr-users-alt"></i>',
                                'iconColor' => '#3b82f6',
                                'label' => 'Active Users',
                                'amount' => '2,345',
                                'description' => 'This month',
                                'percentage' => '+5.2%',
                            ],
                            [
                                'icon' => '<i class="fi fi-rr-shopping-cart"></i>',
                                'iconColor' => '#f59e0b',
                                'label' => 'Total Orders',
                                'amount' => '892',
                                'description' => 'This week',
                                'percentage' => '-2.4%',
                                'percentageColor' => '#ef4444',
                            ],
                            [
                                'icon' => '<i class="fi fi-rr-star"></i>',
                                'iconColor' => '#8b5cf6',
                                'label' => 'Customer Rating',
                                'amount' => '4.8',
                                'description' => 'Average score',
                                'percentage' => '+0.3',
                            ],
                        ];
                    @endphp

                    <x-kpicardcontainer :cards="$kpiCards" columns="3" />
                </div>
            </div>

            <!-- Customer Feedbacks Section -->
            <div class="flex flex-row justify-between">
                <x-labelwithvalue label="Productivity Rate" count="" />
                @php
                    $timeOptions = ['Today', 'This Week', 'This Month', 'This Year'];
                @endphp
                <x-dropdown :options="$timeOptions" id="dropdown-time" />
            </div>
            <div class="w-full rounded-lg h-[28rem] sm:h-[28rem] md:h-[28rem] lg:h-[28rem]">
                @php
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
        </div>

        <!-- Right Panel - Service Demand Statistics -->
        <div class="flex flex-col gap-4 md:gap-6 w-1/3 lg:w-1/3 rounded-lg p-3 md:p-3 lg:h-auto lg:overflow-hidden">
            <!-- Header -->
            <div class="flex flex-row justify-between">
                <x-labelwithvalue label="Service Demand Statistics" count="" />
                @php
                    $timeOptions = ['Today', 'This Week', 'This Month', 'This Year'];
                @endphp
                <x-dropdown :options="$timeOptions" id="dropdown-time" />
            </div>

            <!-- Chart Container with flex-shrink-0 to prevent squishing -->
            <div class="flex-shrink-0 ">
                <x-statisticchart title="Sales Performance" :total="18500" :growthRate="6.7" :animateOnLoad="true"
                    :categories="[
        ['name' => 'Deep Cleaning', 'value' => 7200, 'percentage' => 8.5, 'trend' => 'up', 'color' => '#3b82f6'],
        ['name' => 'Snowout Cleaning', 'value' => 6100, 'percentage' => 4.2, 'trend' => 'up', 'color' => '#10b981'],
        ['name' => 'Daily Room Cleaning', 'value' => 5200, 'percentage' => 1.5, 'trend' => 'up', 'color' => '#f59e0b']
    ]" />
            </div>

            <!-- Customer Transactions - Scrollable -->
            <div class="rounded-lg h-52 overflow-y-auto">
                @php
                    // Define table columns
                    $columns = [
                        [
                            'label' => 'Name',
                            'key' => 'name',
                            'headerClass' => '',
                            'cellClass' => 'font-medium'
                        ],
                        [
                            'label' => 'Status',
                            'key' => 'status',
                            'type' => 'status',
                            'statusConfig' => [
                                'subscribed' => [
                                    'label' => 'Subscribed',
                                    'bgColor' => 'bg-green-50 dark:bg-green-900/20',
                                    'textColor' => 'text-green-700 dark:text-green-400',
                                    'borderColor' => 'border-green-200 dark:border-green-800'
                                ],
                                'not_subscribed' => [
                                    'label' => 'Not Subscribed',
                                    'bgColor' => 'bg-red-50 dark:bg-red-900/20',
                                    'textColor' => 'text-red-700 dark:text-red-400',
                                    'borderColor' => 'border-red-200 dark:border-red-800'
                                ]
                            ]
                        ],
                        [
                            'label' => 'Orders',
                            'key' => 'orders',
                            'headerClass' => '',
                            'cellClass' => 'font-semibold'
                        ]
                    ];

                    // Define table data
                    $tableData = [
                        [
                            'name' => 'Norma Tromp',
                            'status' => 'subscribed',
                            'orders' => 2
                        ],
                        [
                            'name' => 'Cesar Homenick',
                            'status' => 'not_subscribed',
                            'orders' => 4
                        ],
                        [
                            'name' => 'Bernice Fadel IV',
                            'status' => 'subscribed',
                            'orders' => 3
                        ],
                        [
                            'name' => 'Norma Tromp',
                            'status' => 'subscribed',
                            'orders' => 2
                        ],
                        [
                            'name' => 'Cesar Homenick',
                            'status' => 'not_subscribed',
                            'orders' => 4
                        ],
                        [
                            'name' => 'Bernice Fadel IV',
                            'status' => 'subscribed',
                            'orders' => 3
                        ]
                    ];
                @endphp

                <x-datatable :columns="$columns" :data="$tableData" :striped="true" :hoverable="true"
                    :responsive="true" />
            </div>
        </div>
    </div>
    @stack('scripts')
</x-layouts.general-employer>