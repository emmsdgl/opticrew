<x-layouts.general-dashboard :title="'Attendance'">
    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => '/employee-dash'],
            ['label' => 'Tasks', 'icon' => 'fa-file-lines', 'href' => '/employee-tasks'],
            ['label' => 'Attendance', 'icon' => 'fa-calendar', 'href' => '/employee-attendance'],
            ['label' => 'Performance', 'icon' => 'fa-chart-line', 'href' => '/employee-performance']
        ];

        $teams = ['', ''];
    @endphp
    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section role="status" class="flex flex-col lg:flex-col gap-1 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Inner Panel - Summary Cards Container -->
        <div
            class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <x-labelwithvalue label="Summary" count="" />

            @php
                $stats = [
                    [
                        'title' => 'Required Working Hours',
                        'value' => '90 h',
                        'subtitle' => 'As a requisite, the hours is set to 90 hrs of work',
                        'icon' => 'fa-solid fa-business-time',
                        'iconBg' => '',
                        'iconColor' => 'text-blue-600',
                    ],
                    [
                        'title' => 'Worked Hours',
                        'value' => '30 h 10 m',
                        'trend' => 'up',
                        'trendValue' => '3.4%',
                        'trendLabel' => 'vs last month',
                        'icon' => 'fa-solid fa-hourglass-start',
                        'iconBg' => '',
                        'iconColor' => 'text-blue-600',
                    ],
                    [
                        'title' => 'Idle Time',
                        'value' => '20 m',
                        'trend' => 'up',
                        'trendValue' => '3.4%',
                        'trendLabel' => 'vs last month',
                        'icon' => 'fa-regular fa-hourglass',
                        'iconBg' => '',
                        'iconColor' => 'text-blue-600',
                    ],
                ];
            @endphp
            <div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 p-6">

                @foreach($stats as $stat)
                    <x-statisticscard :title="$stat['title']" :value="$stat['value']" :subtitle="$stat['subtitle'] ?? ''"
                        :trend="$stat['trend'] ?? null" :trend-value="$stat['trendValue'] ?? null"
                        :trend-label="$stat['trendLabel'] ?? 'vs last month'" :icon="$stat['icon'] ?? null"
                        :icon-bg="$stat['iconBg'] ?? 'bg-gray-100'" :icon-color="$stat['iconColor'] ?? 'text-gray-600'"
                        :value-suffix="$stat['valueSuffix'] ?? ''" :value-prefix="$stat['valuePrefix'] ?? ''" />
                @endforeach

            </div>

        </div>

        <!-- Inner Panel - Attendance Records List -->
        <div
            class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">

            <x-labelwithvalue label="Attendance Records" count="" />
            <!-- TRANSFER THIS TO CONTROLLER FOR DATA IN THE DATABASE -->
            @php
                $attendanceRecords = [
                    [
                        'status' => 'present',
                        'date' => 'August 24',
                        'dayOfWeek' => 'Monday',
                        'timeIn' => '11:00 am',
                        'timeInNote' => '2 m early',
                        'timeOut' => null,
                        'timeOutNote' => '',
                        'mealBreak' => '1:00 pm',
                        'mealBreakDuration' => '30 mins',
                        'timedIn' => true,
                        'isTimedOut' => false
                    ],
                    [
                        'status' => 'late',
                        'date' => 'August 24',
                        'dayOfWeek' => 'Monday',
                        'timeIn' => null,
                        'timeInNote' => '',
                        'timeOut' => null,
                        'timeOutNote' => '',
                        'mealBreak' => '1:00 pm',
                        'mealBreakDuration' => '30 mins',
                        'timedIn' => false,
                        'isTimedOut' => false
                    ],
                ];
            @endphp
            <x-attendancelistitem :records="$attendanceRecords" :show-header="true" />
        </div>

    </section>
</x-layouts.general-dashboard>
@stack('scripts')