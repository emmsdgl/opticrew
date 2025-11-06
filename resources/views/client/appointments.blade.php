<x-layouts.general-dashboard :title="'Appointments'">
    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => route('client.dashboard')],
            ['label' => 'Appointments', 'icon' => 'fa-calendar', 'href' => route('client.appointments')],
            ['label' => 'Pricing', 'icon' => 'fa-file-lines', 'href' => route('client.pricing')],
            ['label' => 'Feedbacks', 'icon' => 'fa-chart-line', 'href' => route('client.feedback')]
        ];

        $teams = []; // No teams for client sidebar
    @endphp
    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <style>
        /* Prevent horizontal scroll on body/main */
        body,
        main {
            overflow-x: hidden;
        }

        /* Custom scrollbar for service cards - thinner and transparent track */
        .service-scroll::-webkit-scrollbar {
            height: 1px;
            /* thinner scrollbar */
        }

        .service-scroll::-webkit-scrollbar-track {
            background: transparent;
            /* transparent track */
        }

        .service-scroll::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.1);
            /* primary blue with opacity */
            border-radius: 2px;
        }

        .service-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(37, 99, 235, 0.1);
            /* darker blue on hover */
        }

        /* Dark mode scrollbar */
        .dark .service-scroll::-webkit-scrollbar-track {
            background: transparent;
            /* keep transparent in dark mode */
        }

        .dark .service-scroll::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.1);
            /* indigo thumb */
        }

        .dark .service-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(129, 140, 248, 0.1);
            /* lighter on hover */
        }

        /* Firefox scrollbar styling */
        .service-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(59, 130, 246, 0.1) transparent;
        }

        .dark .service-scroll {
            scrollbar-color: rgba(99, 102, 241, 0.1) transparent;
        }
    </style>

    <section role="status" class="flex flex-col lg:flex-col gap-1 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Inner Panel - Summary Cards Container -->
        <div
            class="flex flex-col gap-6 w-full rounded-lg px-8">

            @php
                $statCards = [
                    [
                        'title' => 'Total Appointments',
                        'value' => $stats['total'],
                        'subtitle' => 'All your service requests',
                        'icon' => 'fa-solid fa-calendar-check',
                        'iconBg' => '',
                        'iconColor' => 'text-blue-600',
                    ],
                    [
                        'title' => 'Ongoing Appointments',
                        'value' => $stats['ongoing'],
                        'subtitle' => 'Pending or confirmed services',
                        'icon' => 'fa-solid fa-broom',
                        'iconBg' => '',
                        'iconColor' => 'text-blue-600',
                    ],
                    [
                        'title' => 'Completed Services',
                        'value' => $stats['completed'],
                        'subtitle' => 'Successfully finished services',
                        'icon' => 'fa-solid fa-check-circle',
                        'iconBg' => '',
                        'iconColor' => 'text-blue-600',
                    ],
                ];
            @endphp
            <x-labelwithvalue label="Availed Services Overview" count="" />
            <div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                @foreach($statCards as $stat)
                    <x-statisticscard :title="$stat['title']" :value="$stat['value']" :subtitle="$stat['subtitle'] ?? ''"
                        :trend="$stat['trend'] ?? null" :trend-value="$stat['trendValue'] ?? null"
                        :trend-label="$stat['trendLabel'] ?? 'vs last month'" :icon="$stat['icon'] ?? null"
                        :icon-bg="$stat['iconBg'] ?? 'bg-gray-100'" :icon-color="$stat['iconColor'] ?? 'text-gray-600'"
                        :value-suffix="$stat['valueSuffix'] ?? ''" :value-prefix="$stat['valuePrefix'] ?? ''" />
                @endforeach
            </div>
        </div>
        <div class="flex flex-col gap-6 w-full rounded-lg p-8 my-8">
            <x-labelwithvalue label="Appointments Calendar" count="" />
            <div class="flex flex-row justify-between w-full items-center">
                @php
                    $events = [
                        [
                            'id' => 1,
                            'title' => 'Check Health',
                            'date' => '2025-11-07',  // Changed from 2024-08-07
                            'startTime' => '09:00',
                            'endTime' => '10:00',
                            'time' => '09 AM - 10 AM',
                            'description' => 'Annual checkup',
                            'color' => '#EC4899',
                            'position' => 0,
                            'height' => 60
                        ],
                        [
                            'id' => 2,
                            'title' => 'Check Health',
                            'date' => '2025-11-05',  // Changed from 2024-08-05
                            'startTime' => '09:00',
                            'endTime' => '10:00',
                            'time' => '09 AM - 10 AM',
                            'description' => 'Annual checkup',
                            'color' => '#EC4899',
                            'position' => 0,
                            'height' => 60
                        ],
                        [
                            'id' => 3,
                            'title' => 'Lunch Meeting',
                            'date' => '2025-11-05',  // Changed from 2024-08-05
                            'startTime' => '12:00',
                            'endTime' => '13:00',  // Changed from '1:00' to '13:00'
                            'time' => '12 PM - 1 PM',
                            'description' => 'Lunch meeting',
                            'color' => '#EC4899',
                            'position' => 0,
                            'height' => 60
                        ],
                        [
                            'id' => 4,
                            'title' => 'Team Meeting',
                            'date' => '2025-11-07',  // Changed from 2024-08-07
                            'startTime' => '14:00',
                            'endTime' => '15:30',
                            'time' => '02 PM - 03:30 PM',
                            'description' => 'Weekly sync',
                            'color' => '#3B82F6',
                            'position' => 0,
                            'height' => 90
                        ],
                        [
                            'id' => 5,
                            'title' => 'Lunch Break',
                            'date' => '2025-11-07',  // Changed from 2024-08-07
                            'startTime' => '12:00',
                            'endTime' => '13:00',
                            'time' => '12 PM - 01 PM',
                            'description' => 'Lunch time',
                            'color' => '#10B981',
                            'position' => 0,
                            'height' => 60
                        ],
                    ];
                @endphp

                <x-client-components.appointment-page.appointment-calendar :events="$events" initial-view="month" />
            </div>
        </div>

        <!-- Inner Panel - Appointments History -->
        <div class="flex flex-col gap-6 w-full rounded-lg px-8">
            <div class="flex flex-row justify-between w-full items-center">
                <x-labelwithvalue label="My Appointments" count="({{ $appointments->count() }})" />

                <div class="flex flex-row gap-2">
                    <x-dropdown label="Filter by:" default="All" :options="['All', 'Active', 'Inactive', 'Pending']"
                        id="status-filter" />
                    <x-dropdown label="Sort by:" default="Latest" :options="[
        'latest' => 'Latest',
        'oldest' => 'Oldest',
        'name_asc' => 'Name (A-Z)',
        'name_desc' => 'Name (Z-A)'
    ]" />
                    <a href="{{ route('client.appointment.create') }}"
                        class="px-4 py-2.5 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors text-sm">
                        <i class="fi fi-rr-plus mr-2"></i>
                        Book New Appointment
                    </a>
                </div>
            </div>

            <x-client-components.appointment-page.client-appointment-list :appointments="$appointments"
                :show-header="true" />
        </div>

        <!-- Inner Panel - Appointments to Rate -->
        <div class="flex flex-col gap-6 rounded-lg overflow-hidden p-8 my-8">
            <x-labelwithvalue label="Appointments To Rate" count="(2)" />
            <div class="h-48">

                @php
                    $services = [
                        [
                            'service' => 'Deep Cleaning',
                            'status' => 'Complete',
                            'service_date' => 'Nov 15, 2024',
                            'service_time' => '2:00 PM',
                            'description' => 'Full house deep cleaning including kitchen, bathrooms, and living areas.',
                        ],
                        [
                            'service' => 'Window Cleaning',
                            'status' => 'In progress',
                            'service_date' => 'Nov 20, 2024',
                            'service_time' => '10:00 AM',
                            'description' => 'Professional window cleaning for all windows.',
                        ],
                        [
                            'service' => 'Carpet Cleaning',
                            'status' => 'Completed',
                            'service_date' => 'Nov 10, 2024',
                            'service_time' => '1:00 PM',
                            'description' => 'Steam cleaning for living room and bedroom carpets.',
                        ],
                        [
                            'service' => 'Kitchen Deep Clean',
                            'status' => 'Archived',
                            'service_date' => 'Oct 28, 2024',
                            'service_time' => '3:30 PM',
                            'description' => 'Detailed kitchen cleaning including appliances and cabinets.',
                        ],
                    ];
                @endphp

                <x-client-components.appointment-page.appointment-rate-list :items="$services" maxHeight="30rem" />
            </div>
        </div>
    </section>
</x-layouts.general-dashboard>