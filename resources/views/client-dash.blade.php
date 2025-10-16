<x-layouts.general-dashboard :title="'Client Dashboard'">

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

    <section role="status" class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Left Panel - Dashboard Content -->
        <div
            class="flex flex-col gap-6 flex-1 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <!-- Inner Up - Dashboard Header -->
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-48 sm:h-56 md:h-64 lg:h-1/3">
                <x-herocard :headerName="$client->first_name ?? 'Client'" :headerDesc="'Welcome to the dashboard. What needs cleaning today?'" :headerIcon="'hero-client'" />
            </div>
            <!-- Inner Middle - Calendar -->
            <x-labelwithvalue label="My Calendar" count="" />
            <div
                class="w-full mb-6 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-auto sm:h-72 md:h-80 lg:h-auto">
                <x-calendar />
            </div>

            <!-- Inner Bottom - Recent Orders -->
            <div class="flex flex-row justify-between w-full">
                <x-labelwithvalue label="Ongoing Appointments" count="(5)" />
                <div class="flex flex-row gap-3">
                    @php
                        $timeOptions = ['This Day', 'This Week', 'This Month'];
                        $serviceOptions = ['Deep Cleaning', 'Daily Full Cleaning', 'Daily Room Cleaning', 'Full Room Cleaning'];
                    @endphp

                    <x-dropdown :options="$timeOptions" default="This Day" id="dropdown-time" />
                    <x-dropdown :options="$serviceOptions" default="Service Type" id="dropdown-service-type" />
                    <x-button label="Book a Service" color="blue" size="sm" icon='<i class="fa-solid fa-plus"></i>' />
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
                        'status' => 'incomplete ',
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

        <!-- Right Panel - Attendance Overview -->
        <div
            class="flex flex-col gap-6 w-full lg:w-1/3 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-auto p-6">

            <!-- Inner Up - Recommendation Service List -->
            <div class="w-full flex flex-col overflow-y-auto rounded-lg h-full sm:h-full md:h-full">
                <x-labelwithvalue label="Recommended Services For You" count="(4)" />
                @php
                    $services = [
                        [
                            'title' => 'Hotel Cleaning',
                            'badge' => 'Top Choice',
                            'ratingCount' => 4,
                            'description' => 'Room Cleaning, Linen Cleaning, Window Cleaning, Rug Cleaning',
                        ],
                        [
                            'title' => 'Office Cleaning',
                            'badge' => 'Popular',
                            'ratingCount' => 5,
                            'description' => 'Desk Sanitization, Floor Cleaning, Trash Removal',
                        ],
                        [
                            'title' => 'Residential Cleaning',
                            'badge' => 'New',
                            'ratingCount' => 6,
                            'description' => 'General Housekeeping, Kitchen Cleaning, Bathroom Disinfection',
                        ],
                    ];
                @endphp

                <!-- Just pass the array -->
                <x-servicecard :services="$services" />
            </div>

        </div>
    </section>
</x-layouts.general-dashboard>