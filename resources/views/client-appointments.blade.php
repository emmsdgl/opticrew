<x-layouts.general-dashboard :title="'Appointments'">
    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => route('client.dashboard')],
            ['label' => 'Appointments', 'icon' => 'fa-calendar', 'href' => route('client.appointments')],
            ['label' => 'Pricing', 'icon' => 'fa-file-lines', 'href' => '/reports'],
            ['label' => 'Feedbacks', 'icon' => 'fa-chart-line', 'href' => '/analytics']
        ];


        $teams = ['', ''];
    @endphp
    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section role="status" class="flex flex-col lg:flex-col gap-1 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Inner Panel - Summary Cards Container -->
        <div
            class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <x-labelwithvalue label="Availed Services Overview" count="" />

            @php
                $stats = [
                    [
                        'title' => 'Total Services Availed',
                        'value' => '50',
                        'trend' => 'up',
                        'trendValue' => '3.4%',
                        'trendLabel' => 'vs last month',
                        'icon' => 'fa-solid fa-money-check-dollar',
                        'iconBg' => '',
                        'iconColor' => 'text-blue-600',
                    ],
                    [
                        'title' => 'Ongoing Services',
                        'value' => '30',
                        'subtitle' => 'We are currently working on it',
                        'icon' => 'fa-solid fa-broom',
                        'iconBg' => '',
                        'iconColor' => 'text-blue-600',
                    ],
                    [
                        'title' => 'Cancelled Services',
                        'value' => '20',
                        'trend' => 'up',
                        'subtitle' => 'Share with us your feedback on our services',
                        'icon' => 'fa-solid fa-ban',
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


        <!-- Inner Panel - Appointments History -->
        <div
            class="flex flex-col gap-6 p-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">

            <div class="flex flex-row justify-between w-full">
                <x-labelwithvalue label="All Appointments" count="(5)" />
                <div class="flex flex-row gap-3">
                    @php
                        $timeOptions = ['Completed', 'In Progress', 'Incomplete'];
                        $serviceOptions = ['Deep Cleaning', 'Daily Full Cleaning', 'Daily Room Cleaning', 'Full Room Cleaning'];
                    @endphp

                    <x-dropdown :options="$timeOptions" id="dropdown-time" />
                    <x-dropdown :options="$serviceOptions" default="Service Type" id="dropdown-service-type" />
                </div>
            </div>
            @php
                $appointments = [
                    [
                        'id' => 1,
                        'title' => 'Deep Cleaning',
                        'location' => 'Cabin 1',
                        'status' => 'in_progress',
                        'duration' => '02 h 30 m',
                        'progress' => 30,
                        'date' => '2025-07-07',
                        'time' => '10:00 AM'
                    ],
                    [
                        'id' => 2, // Changed to 2 (you had duplicate id: 1)
                        'title' => 'Deep Cleaning',
                        'location' => 'Cabin 1',
                        'status' => 'incomplete',
                        'duration' => '02 h 30 m',
                        'progress' => 50,
                        'date' => '2025-07-07',
                        'time' => '10:00 AM'
                    ],
                ];
            @endphp

            <x-appointmentlistitem :appointments="$appointments" :editable="false" :show-progress="true"
                :show-duration="true" on-item-click="handleAppointmentClick">
            </x-appointmentlistitem>
        </div>
        <!-- Inner Panel - Services Container -->
        <x-labelwithvalue label="Explore Our Services" count="(5)" />
        <div
            class="flex flex-row gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-2 overflow-x-auto">
            @php
                $services = [
                    [
                        'title' => 'Hotel Cleaning',
                        'badge' => 'Top Choice',
                        'rating' => 4.4,
                        'description' => 'Room Cleaning, Linen Cleaning, Window Cleaning, Rug Cleaning',
                    ],
                    [
                        'title' => 'Office Cleaning',
                        'badge' => 'Popular',
                        'rating' => 4.7,
                        'description' => 'Desk Sanitization, Floor Cleaning, Trash Removal',
                    ],
                    [
                        'title' => 'Office Cleaning',
                        'badge' => 'Popular',
                        'rating' => 4.7,
                        'description' => 'Desk Sanitization, Floor Cleaning, Trash Removal',
                    ],
                    [
                        'title' => 'Office Cleaning',
                        'badge' => 'Popular',
                        'rating' => 4.7,
                        'description' => 'Desk Sanitization, Floor Cleaning, Trash Removal',
                    ],
                    [
                        'title' => 'Carpet Deep Cleaning',
                        'badge' => 'Recommended',
                        'rating' => 4.9,
                        'description' => 'Carpet Shampooing, Vacuuming, and Odor Removal',
                    ],
                ];
            @endphp

<div class="flex gap-6 p-6 overflow-x-auto max-w-full">
    @foreach($services as $service)
        <x-servicecard :service="$service" onBook="handleBook" onFavorite="handleFavorite" />
    @endforeach
</div>
        </div>
    </section>
</x-layouts.general-dashboard>
@stack('scripts')