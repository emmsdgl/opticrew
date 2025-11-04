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
            class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <x-labelwithvalue label="Availed Services Overview" count="" />

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
                        'iconColor' => 'text-orange-600',
                    ],
                    [
                        'title' => 'Completed Services',
                        'value' => $stats['completed'],
                        'subtitle' => 'Successfully finished services',
                        'icon' => 'fa-solid fa-check-circle',
                        'iconBg' => '',
                        'iconColor' => 'text-green-600',
                    ],
                ];
            @endphp
            <div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 p-6">
                @foreach($statCards as $stat)
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
            class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <div class="flex flex-row justify-between w-full items-center">
                <x-labelwithvalue label="My Appointments" count="({{ $appointments->count() }})" />
                <a href="{{ route('client.appointment.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fi fi-rr-plus mr-2"></i>
                    Book New Appointment    
                </a>
            </div>

            <x-client-components.appointment-page.clientappointmentlist :appointments="$appointments" :show-header="true" />
        </div>

        <!-- Inner Panel - Services Container with Horizontal Scroll -->
        <div
            class="flex flex-col gap-6 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4 overflow-hidden">
            <x-labelwithvalue label="Explore Our Services" count="(2)" />

            @php
                // Get average ratings from real feedback data
                $finalCleaningRating = \App\Models\Feedback::averageRatingForService('Final Cleaning');
                $deepCleaningRating = \App\Models\Feedback::averageRatingForService('Deep Cleaning');

                $services = [
                    [
                        'title' => 'Final Cleaning',
                        'badge' => 'Most Popular',
                        'rating' => $finalCleaningRating > 0 ? number_format($finalCleaningRating, 1) : 'New',
                        'description' => 'Complete cleaning service covering kitchen, living room, bedrooms, bathroom and sauna.',
                    ],
                    [
                        'title' => 'Deep Cleaning',
                        'badge' => 'Thorough',
                        'rating' => $deepCleaningRating > 0 ? number_format($deepCleaningRating, 1) : 'New',
                        'description' => 'Intensive cleaning service for a deeper clean. Includes all Final Cleaning tasks plus extra attention to hard-to-reach areas, detailed scrubbing, and sanitization of all surfaces for a spotless finish.',
                    ],
                ];
            @endphp

            <!-- Horizontal Scrollable Container -->
            <div class="-mx-4 px-4">
                <div class="service-scroll flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory"
                    style="scrollbar-width: thin;">
                    @foreach($services as $service)
                        <div class="flex-none snap-start" style="width: calc((100% - 2rem) / 3); min-width: 280px;">
                            <x-client-components.dashboard-page.servicecard :service="$service" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-layouts.general-dashboard>