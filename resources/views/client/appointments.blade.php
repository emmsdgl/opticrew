<x-layouts.general-client :title="'Appointments'">

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

    <section role="status" class="w-full flex flex-col lg:flex-col gap-1 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
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
                    // Transform appointments to calendar events format
                    $events = $appointments->map(function($appointment) {
                        // Color based on status
                        $statusColors = [
                            'pending' => '#FFA500',    // Orange
                            'confirmed' => '#2FBC00',  // Green
                            'completed' => '#00BFFF',  // Blue
                            'cancelled' => '#FE1E28',  // Red
                        ];

                        $color = $statusColors[$appointment->status] ?? '#6B7280'; // Default gray

                        // Parse service time
                        $serviceTime = \Carbon\Carbon::parse($appointment->service_time);
                        $startTime = $serviceTime->format('H:i');

                        // Estimate end time (assuming 2 hours for cleaning)
                        $endTime = $serviceTime->addHours(2)->format('H:i');

                        // Format time display
                        $timeDisplay = \Carbon\Carbon::parse($appointment->service_time)->format('h:i A') . ' - ' .
                                       \Carbon\Carbon::parse($appointment->service_time)->addHours(2)->format('h:i A');

                        return [
                            'id' => $appointment->id,
                            'title' => $appointment->service_type,
                            'date' => $appointment->service_date->format('Y-m-d'),
                            'startTime' => $startTime,
                            'endTime' => $endTime,
                            'time' => $timeDisplay,
                            'description' => $appointment->cabin_name . ' - ' . $appointment->number_of_units . ' unit(s)',
                            'color' => $color,
                            'position' => 0,
                            'height' => 60
                        ];
                    })->toArray();
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

            <div class="h-64 overflow-y-auto">
                <x-client-components.appointment-page.client-appointment-list :appointments="$appointments"
                    :show-header="true" />
            </div>
        </div>

        <!-- Inner Panel - Appointments to Rate -->
        <div class="flex flex-col gap-6 rounded-lg p-8 my-8">
            <x-labelwithvalue label="Appointments To Rate" count="({{ $completedAppointments->count() ?? 0 }})" />
    
            <div class="h-64 overflow-y-auto">
                @php
                    // Transform completed appointments to the format expected by the component
                    $services = $completedAppointments->map(function($appointment) {
                        return [
                            'id' => $appointment->id,
                            'service' => $appointment->service_type,
                            'status' => 'Completed',
                            'service_date' => $appointment->service_date->format('M d, Y'),
                            'service_time' => \Carbon\Carbon::parse($appointment->service_time)->format('g:i A'),
                            'description' => $appointment->special_requests
                                ? $appointment->special_requests
                                : 'Cleaning service for ' . $appointment->number_of_units . ' unit(s) at ' . $appointment->cabin_name . ' (' . $appointment->unit_size . ' m²)',
                            'cabin_name' => $appointment->cabin_name,
                            'unit_size' => $appointment->unit_size,
                            'number_of_units' => $appointment->number_of_units,
                        ];
                    })->toArray();
                @endphp

                <x-client-components.appointment-page.appointment-rate-list
                    :items="$services"
                    maxHeight="30rem"
                    emptyTitle="No completed appointments yet"
                    emptyMessage="Once you complete appointments, they will appear here for you to rate and provide feedback." />
            </div>
        </div>
    </section>
</x-layouts.general-client>