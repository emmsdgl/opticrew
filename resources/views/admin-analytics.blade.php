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
            <div class="w-full rounded-lg h-[32rem] sm:h-[32rem] md:h-[32rem] lg:h-[32rem]">
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

            <x-statisticchart :total="9829" :growthRate="5.39" growthTrend="up" :categories="[
        ['name' => 'Deep Cleaning', 'value' => 4000, 'color' => '#275BED'],
        ['name' => 'Snowout Cleaning', 'value' => 3000, 'color' => '#779FF4'],
        ['name' => 'Daily Room Cleaning', 'value' => 2829, 'color' => '#CAD9F8']
    ]" />
            <!-- Customer Transactions - Scrollable -->
            <x-labelwithvalue label="Customer Transactions" count="(10)" />

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
                                'contracted' => [
                                    'label' => 'Contracted',
                                    'bgColor' => 'bg-blue-50 dark:bg-blue-900/20',
                                    'textColor' => 'text-blue-700 dark:text-blue-400',
                                    'borderColor' => 'border-blue-200 dark:border-blue-800'
                                ],
                                'external' => [
                                    'label' => 'External',
                                    'bgColor' => 'bg-indigo-50 dark:bg-indigo-900/20',
                                    'textColor' => 'text-indigo-700 dark:text-indigo-400',
                                    'borderColor' => 'border-indigo-200 dark:border-indigo-800'
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
                            'status' => 'contracted',
                            'orders' => 2
                        ],
                        [
                            'name' => 'Cesar Homenick',
                            'status' => 'external',
                            'orders' => 4
                        ],
                        [
                            'name' => 'Bernice Fadel IV',
                            'status' => 'contracted',
                            'orders' => 3
                        ],
                        [
                            'name' => 'Norma Tromp',
                            'status' => 'contracted',
                            'orders' => 2
                        ],
                        [
                            'name' => 'Cesar Homenick',
                            'status' => 'external',
                            'orders' => 4
                        ],
                        [
                            'name' => 'Bernice Fadel IV',
                            'status' => 'external',
                            'orders' => 3
                        ]
                    ];
                @endphp

                <x-datatable :columns="$columns" :data="$tableData" :striped="true" :hoverable="true"
                    :responsive="true" />
            </div>
        </div>
    </div>
</x-layouts.general-employer>
@stack('scripts')