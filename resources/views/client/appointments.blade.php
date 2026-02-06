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

        <!-- Saved Services Section (Only shows favorited services from dashboard) -->
        @php
            $finalCleaningRating = \App\Models\Feedback::averageRatingForService('Final Cleaning');
            $deepCleaningRating = \App\Models\Feedback::averageRatingForService('Deep Cleaning');

            $allServices = [
                [
                    'title' => 'Final Cleaning',
                    'initial' => 'FC',
                    'color' => 'bg-gradient-to-br from-blue-500 to-blue-600',
                    'rating' => $finalCleaningRating > 0 ? number_format($finalCleaningRating, 1) : 'New',
                    'description' => 'Complete move-out cleaning',
                    'bookings' => $appointments->where('service_type', 'Final Cleaning')->count(),
                ],
                [
                    'title' => 'Deep Cleaning',
                    'initial' => 'DC',
                    'color' => 'bg-gradient-to-br from-purple-500 to-indigo-600',
                    'rating' => $deepCleaningRating > 0 ? number_format($deepCleaningRating, 1) : 'New',
                    'description' => 'Intensive thorough cleaning',
                    'bookings' => $appointments->where('service_type', 'Deep Cleaning')->count(),
                ],
            ];
        @endphp

        <div x-data="{
            favorites: [],
            allServices: {{ json_encode($allServices) }},
            init() {
                this.favorites = JSON.parse(localStorage.getItem('favoriteServices') || '[]');
                // Listen for favorites updates from other pages
                window.addEventListener('favorites-updated', (e) => {
                    this.favorites = e.detail.favorites;
                });
            },
            isFavorite(title) {
                return this.favorites.includes(title);
            },
            removeFavorite(title) {
                this.favorites = this.favorites.filter(s => s !== title);
                localStorage.setItem('favoriteServices', JSON.stringify(this.favorites));
            }
        }"
        x-show="favorites.length > 0"
        x-cloak
        class="flex flex-col gap-6 w-full rounded-lg px-8">
            <x-labelwithvalue label="Saved Services" count="" />

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($allServices as $service)
                <a x-show="isFavorite('{{ $service['title'] }}')"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 scale-95"
                   x-transition:enter-end="opacity-100 scale-100"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="opacity-100 scale-100"
                   x-transition:leave-end="opacity-0 scale-95"
                   href="{{ route('client.appointment.create') }}?service={{ urlencode($service['title']) }}"
                   class="group flex items-center gap-4 p-4 bg-gray-900 dark:bg-gray-800 rounded-xl border border-gray-700 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all duration-200 hover:shadow-lg hover:shadow-blue-500/10">
                    <!-- Service Initial Badge -->
                    <div class="flex-shrink-0 w-12 h-12 {{ $service['color'] }} rounded-lg flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-sm">{{ $service['initial'] }}</span>
                    </div>

                    <!-- Service Info -->
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-white truncate group-hover:text-blue-400 transition-colors">
                            {{ $service['title'] }}
                        </h4>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $service['bookings'] }} {{ $service['bookings'] == 1 ? 'Booking' : 'Bookings' }}
                        </p>
                    </div>

                    <!-- Rating & Remove -->
                    <div class="flex items-center gap-2">
                        @if($service['rating'] !== 'New')
                        <div class="flex items-center gap-1 px-2 py-1 bg-yellow-500/20 rounded-full">
                            <i class="fas fa-star text-yellow-500 text-xs"></i>
                            <span class="text-xs font-medium text-yellow-500">{{ $service['rating'] }}</span>
                        </div>
                        @else
                        @endif

                        <button @click.prevent="removeFavorite('{{ $service['title'] }}')"
                                class="p-1.5 text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors opacity-0 group-hover:opacity-100"
                                title="Remove from saved">
                            <i class="fas fa-heart text-sm"></i>
                        </button>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Filter/Sort Script - Define before Alpine initializes -->
        <script>
        // Sort function for appointments
        window.sortAppointments = function(field, direction) {
            console.log('Sorting appointments:', field, direction);

            const listContainer = document.getElementById('appointments-list');
            if (!listContainer) {
                console.error('List container not found');
                return;
            }

            // Get all appointment items
            const appointmentItems = Array.from(listContainer.querySelectorAll('[data-appointment-item]'));
            console.log('Found appointment items:', appointmentItems.length);

            if (appointmentItems.length === 0) {
                console.log('No appointments to sort');
                return;
            }

            // Sort the appointment items
            appointmentItems.sort((a, b) => {
                let valueA, valueB;

                switch(field) {
                    case 'service':
                        valueA = (a.getAttribute('data-service') || '').toLowerCase();
                        valueB = (b.getAttribute('data-service') || '').toLowerCase();
                        break;
                    case 'date':
                        valueA = a.getAttribute('data-date') || '';
                        valueB = b.getAttribute('data-date') || '';
                        const dateA = new Date(valueA);
                        const dateB = new Date(valueB);
                        return direction === 'asc' ? dateA - dateB : dateB - dateA;
                    case 'amount':
                        valueA = parseFloat(a.getAttribute('data-amount') || '0');
                        valueB = parseFloat(b.getAttribute('data-amount') || '0');
                        return direction === 'asc' ? valueA - valueB : valueB - valueA;
                    default:
                        return 0;
                }

                // String comparison
                if (direction === 'asc') {
                    return valueA.localeCompare(valueB);
                } else {
                    return valueB.localeCompare(valueA);
                }
            });

            // Get the parent container for the appointment items
            const appointmentParent = appointmentItems[0]?.parentElement;
            if (!appointmentParent) {
                console.error('Appointment parent not found');
                return;
            }

            // Re-append items in sorted order
            appointmentItems.forEach(item => {
                appointmentParent.appendChild(item);
            });

            console.log('Appointments sorted successfully');
        }

        // Filter appointments by status
        window.filterAppointmentsByStatus = function(statusType) {
            console.log('Filtering appointments by status:', statusType);

            const listContainer = document.getElementById('appointments-list');
            if (!listContainer) {
                console.error('List container not found');
                return;
            }

            const appointmentItems = Array.from(listContainer.querySelectorAll('[data-appointment-item]'));
            console.log('Found appointment items:', appointmentItems.length);

            if (appointmentItems.length === 0) {
                console.log('No appointments to filter');
                return;
            }

            appointmentItems.forEach(item => {
                const status = item.getAttribute('data-status') || '';

                if (statusType === 'all') {
                    item.style.display = '';
                } else {
                    if (status.toLowerCase() === statusType.toLowerCase()) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });

            console.log('Appointments filtered by status successfully');
        }

        // Filter appointments by service type
        window.filterAppointmentsByService = function(serviceType) {
            console.log('Filtering appointments by service:', serviceType);

            const listContainer = document.getElementById('appointments-list');
            if (!listContainer) {
                console.error('List container not found');
                return;
            }

            const appointmentItems = Array.from(listContainer.querySelectorAll('[data-appointment-item]'));
            console.log('Found appointment items:', appointmentItems.length);

            if (appointmentItems.length === 0) {
                console.log('No appointments to filter');
                return;
            }

            appointmentItems.forEach(item => {
                const service = item.getAttribute('data-service') || '';

                if (serviceType === 'all') {
                    item.style.display = '';
                } else {
                    if (service.toLowerCase().includes(serviceType.toLowerCase())) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });

            console.log('Appointments filtered by service successfully');
        }

        // Filter rate list by service type
        window.filterRateListByService = function(serviceType) {
            console.log('Filtering rate list by service:', serviceType);

            const listContainer = document.getElementById('rate-list-container');
            if (!listContainer) {
                console.error('Rate list container not found');
                return;
            }

            const rateItems = Array.from(listContainer.querySelectorAll('[data-rate-item]'));
            console.log('Found rate items:', rateItems.length);

            if (rateItems.length === 0) {
                console.log('No rate items to filter');
                return;
            }

            rateItems.forEach(item => {
                const service = item.getAttribute('data-service') || '';

                if (serviceType === 'all') {
                    item.style.display = '';
                } else {
                    if (service.toLowerCase().includes(serviceType.toLowerCase())) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });

            console.log('Rate list filtered by service successfully');
        }

        // Sort rate list
        window.sortRateList = function(field, direction) {
            console.log('Sorting rate list:', field, direction);

            const listContainer = document.getElementById('rate-list-container');
            if (!listContainer) {
                console.error('Rate list container not found');
                return;
            }

            const rateItems = Array.from(listContainer.querySelectorAll('[data-rate-item]'));
            console.log('Found rate items:', rateItems.length);

            if (rateItems.length === 0) {
                console.log('No rate items to sort');
                return;
            }

            rateItems.sort((a, b) => {
                let valueA, valueB;

                switch(field) {
                    case 'service':
                        valueA = (a.getAttribute('data-service') || '').toLowerCase();
                        valueB = (b.getAttribute('data-service') || '').toLowerCase();
                        break;
                    case 'date':
                        valueA = a.getAttribute('data-date') || '';
                        valueB = b.getAttribute('data-date') || '';
                        const dateA = new Date(valueA);
                        const dateB = new Date(valueB);
                        return direction === 'asc' ? dateA - dateB : dateB - dateA;
                    default:
                        return 0;
                }

                // String comparison
                if (direction === 'asc') {
                    return valueA.localeCompare(valueB);
                } else {
                    return valueB.localeCompare(valueA);
                }
            });

            // Get the parent container for the rate items
            const rateParent = rateItems[0]?.parentElement;
            if (!rateParent) {
                console.error('Rate parent not found');
                return;
            }

            // Re-append items in sorted order
            rateItems.forEach(item => {
                rateParent.appendChild(item);
            });

            console.log('Rate list sorted successfully');
        }
        </script>

        <!-- Inner Panel - Appointments History -->
        <div class="flex flex-col gap-6 w-full rounded-lg px-8 my-12">
            <div class="flex flex-row justify-between w-full items-center">
                <x-labelwithvalue label="My Appointments" count="({{ $appointments->count() }})" />

                <div class="flex flex-row gap-2">
                    <!-- Filter by Status Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-filter text-xs"></i>
                            <span class="text-xs">Filter by Status</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="filterAppointmentsByStatus('all'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    All Statuses
                                </button>
                                <button type="button" @click="filterAppointmentsByStatus('pending'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Pending
                                </button>
                                <button type="button" @click="filterAppointmentsByStatus('approved'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Approved
                                </button>
                                <button type="button" @click="filterAppointmentsByStatus('confirmed'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Confirmed
                                </button>
                                <button type="button" @click="filterAppointmentsByStatus('completed'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Completed
                                </button>
                                <button type="button" @click="filterAppointmentsByStatus('cancelled'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Cancelled
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Filter by Service Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-broom text-xs"></i>
                            <span class="text-xs">Filter by Service</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="filterAppointmentsByService('all'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    All Services
                                </button>
                                <button type="button" @click="filterAppointmentsByService('Deep Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Deep Cleaning
                                </button>
                                <button type="button" @click="filterAppointmentsByService('Snowout Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Snowout Cleaning
                                </button>
                                <button type="button" @click="filterAppointmentsByService('Daily Room Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Daily Room Cleaning
                                </button>
                                <button type="button" @click="filterAppointmentsByService('Hotel Cleaning Service'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Hotel Cleaning Service
                                </button>
                                <button type="button" @click="filterAppointmentsByService('Student Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Student Cleaning
                                </button>
                                <button type="button" @click="filterAppointmentsByService('Final Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Final Cleaning
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sort by Order Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-sort-amount-down text-xs"></i>
                            <span class="text-xs">Sort by Order</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="sortAppointments('date', 'desc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-down text-xs w-4"></i>
                                    Newest First
                                </button>
                                <button type="button" @click="sortAppointments('date', 'asc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-up text-xs w-4"></i>
                                    Oldest First
                                </button>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('client.appointment.create') }}"
                        class="px-4 py-2.5 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors text-sm">
                        <i class="fi fi-rr-plus mr-2"></i>
                        Book New Appointment
                    </a>
                </div>
            </div>

            <div id="appointments-list" class="h-64 overflow-y-auto">
                <x-client-components.appointment-page.client-appointment-list :appointments="$appointments"
                    :show-header="true" />
            </div>
        </div>

        <!-- Inner Panel - Appointments to Rate -->
        <div class="flex flex-col gap-6 rounded-lg p-8 my-8">
            <div class="flex flex-row justify-between w-full items-center">
                <x-labelwithvalue label="Appointments To Rate" count="({{ $completedAppointments->count() ?? 0 }})" />

                <div class="flex flex-row gap-2">
                    <!-- Filter by Service Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-broom text-xs"></i>
                            <span class="text-xs">Filter by Service</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="filterRateListByService('all'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    All Services
                                </button>
                                <button type="button" @click="filterRateListByService('Deep Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Deep Cleaning
                                </button>
                                <button type="button" @click="filterRateListByService('Snowout Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Snowout Cleaning
                                </button>
                                <button type="button" @click="filterRateListByService('Daily Room Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Daily Room Cleaning
                                </button>
                                <button type="button" @click="filterRateListByService('Hotel Cleaning Service'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Hotel Cleaning Service
                                </button>
                                <button type="button" @click="filterRateListByService('Student Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Student Cleaning
                                </button>
                                <button type="button" @click="filterRateListByService('Final Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Final Cleaning
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sort by Order Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-sort-amount-down text-xs"></i>
                            <span class="text-xs">Sort by Order</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="sortRateList('date', 'desc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-down text-xs w-4"></i>
                                    Newest First
                                </button>
                                <button type="button" @click="sortRateList('date', 'asc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-up text-xs w-4"></i>
                                    Oldest First
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div id="rate-list-container" class="h-64 overflow-y-auto">
                @php
                    // Transform completed appointments to the format expected by the component
                    $services = $completedAppointments->map(function($appointment) {
                        return [
                            'id' => $appointment->id,
                            'service' => $appointment->service_type,
                            'status' => 'Completed',
                            'service_date' => $appointment->service_date->format('M d, Y'),
                            'service_time' => \Carbon\Carbon::parse($appointment->service_time)->format('g:i A'),
                            'raw_date' => $appointment->service_date->format('Y-m-d'),
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