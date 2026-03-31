<x-layouts.general-client :title="'Client Dashboard'">
    <x-skeleton-page :preset="'client-dashboard'">
    <!-- Filter/Sort Script - Define before Alpine initializes -->
    <script>
    // Sort function for appointments on dashboard
    window.sortDashboardAppointments = function(field, direction) {
        console.log('Sorting dashboard appointments:', field, direction);

        const listContainer = document.getElementById('dashboard-appointments-list');
        if (!listContainer) {
            console.error('List container not found');
            return;
        }

        const appointmentItems = Array.from(listContainer.querySelectorAll('[data-appointment-item]'));
        console.log('Found appointment items:', appointmentItems.length);

        if (appointmentItems.length === 0) {
            console.log('No appointments to sort');
            return;
        }

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
                default:
                    return 0;
            }

            if (direction === 'asc') {
                return valueA.localeCompare(valueB);
            } else {
                return valueB.localeCompare(valueA);
            }
        });

        const appointmentParent = appointmentItems[0]?.parentElement;
        if (!appointmentParent) {
            console.error('Appointment parent not found');
            return;
        }

        appointmentItems.forEach(item => {
            appointmentParent.appendChild(item);
        });

        console.log('Dashboard appointments sorted successfully');
    }

    // Filter dashboard appointments by status
    window.filterDashboardByStatus = function(statusType) {
        console.log('Filtering dashboard appointments by status:', statusType);

        const listContainer = document.getElementById('dashboard-appointments-list');
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

        console.log('Dashboard appointments filtered by status successfully');
    }

    // Filter dashboard appointments by service type
    window.filterDashboardByService = function(serviceType) {
        console.log('Filtering dashboard appointments by service:', serviceType);

        const listContainer = document.getElementById('dashboard-appointments-list');
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

        console.log('Dashboard appointments filtered by service successfully');
    }
    </script>

    <section role="status" class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Left Panel - Dashboard Content -->
        <div class="flex flex-col gap-6 flex-1 w-full rounded-lg p-4"
            x-data="{
                showDrawer: false,
                selectedAppointment: null,
                allAppointments: {{ collect($appointments)->toJson() }},

                viewDetails(appointmentId) {
                    this.selectedAppointment = this.allAppointments.find(apt => apt.id === appointmentId);
                    if (this.selectedAppointment) {
                        this.showDrawer = true;
                        document.body.style.overflow = 'hidden';
                    }
                },

                closeDrawer() {
                    this.showDrawer = false;
                    this.selectedAppointment = null;
                    document.body.style.overflow = 'auto';
                },

                // Helper methods for drawer component
                getDrawerStatus() {
                    return (this.selectedAppointment?.status || '').toLowerCase();
                },

                getDrawerData(key) {
                    return this.selectedAppointment?.[key];
                },

                getDrawerChecklistItems() {
                    const serviceType = this.selectedAppointment?.service_type || '';
                    return window.getChecklistByServiceType ? window.getChecklistByServiceType(serviceType) : [];
                },

                // Check if a specific checklist item is completed
                isChecklistItemCompleted(itemIndex) {
                    if (!this.selectedAppointment) return false;
                    const completions = this.selectedAppointment.checklist_completions || [];
                    return completions.includes(itemIndex);
                },

                // Get checklist progress stats
                getDrawerChecklistProgress() {
                    if (!this.selectedAppointment) return { completed: 0, total: 0, percentage: 0 };

                    const checklistItems = this.getDrawerChecklistItems();
                    const total = checklistItems.length;
                    const completions = this.selectedAppointment.checklist_completions || [];
                    const completed = completions.length;
                    const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;

                    return { completed, total, percentage };
                },

                formatDrawerDate(dateString) {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                },

                formatDrawerTime(timeString) {
                    if (!timeString) return '-';
                    const parts = timeString.split(':');
                    if (parts.length < 2) return timeString;

                    let hours = parseInt(parts[0]);
                    const minutes = parts[1];
                    const ampm = hours >= 12 ? 'PM' : 'AM';

                    hours = hours % 12;
                    hours = hours ? hours : 12;

                    return hours + ':' + minutes + ' ' + ampm;
                }
            }">

            <!-- Inner Up - Dashboard Header -->
            <div id="tour-client-welcome"
                class="w-full rounded-lg h-40 sm:h-44 md:h-48 flex items-center">
                    <x-herocard :headerName="$client->first_name ?? 'Client'" :headerDesc="'Welcome to the dashboard. What needs cleaning today?'" :headerIcon="'hero-client'" />
            </div>

            <!-- Inner Middle - Calendar -->
            <x-labelwithvalue label="My Calendar" count="" />
            <div id="tour-client-calendar" class="w-full pb-6 rounded-lg h-auto sm:h-72 md:h-80 lg:h-auto bg-white shadow-sm dark:bg-gray-800/40">
                <x-calendar :holidays="$holidays" />
            </div>

            <!-- Inner Bottom - Appointments List -->
            <div id="tour-client-appointments" class="flex flex-col flex-1">
                <div class="flex flex-row justify-between">
                    <x-labelwithvalue label="Appointments Today" :count="'(' . $stats['ongoing'] . ')'" />
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
                                    <button type="button" @click="filterDashboardByStatus('all'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        All Statuses
                                    </button>
                                    <button type="button" @click="filterDashboardByStatus('pending'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Pending
                                    </button>
                                    <button type="button" @click="filterDashboardByStatus('approved'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Approved
                                    </button>
                                    <button type="button" @click="filterDashboardByStatus('confirmed'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Confirmed
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
                                    <button type="button" @click="filterDashboardByService('all'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        All Services
                                    </button>
                                    <button type="button" @click="filterDashboardByService('Deep Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Deep Cleaning
                                    </button>
                                    <button type="button" @click="filterDashboardByService('Final Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
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
                                    <button type="button" @click="sortDashboardAppointments('date', 'desc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                        <i class="fas fa-arrow-down text-xs w-4"></i>
                                        Newest First
                                    </button>
                                    <button type="button" @click="sortDashboardAppointments('date', 'asc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                        <i class="fas fa-arrow-up text-xs w-4"></i>
                                        Oldest First
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fixed height when items exist, auto height for empty state -->
                <div id="dashboard-appointments-list" class="rounded-lg my-6 bg-white shadow-sm dark:bg-gray-800/40 flex-1 min-h-[24rem] {{ $appointments->count() > 0 ? 'overflow-y-auto' : '' }}">
                    <x-client-components.appointment-page.appointment-overview-list :items="$appointments->map(function ($appointment) {
                        return [
                            'id' => $appointment->id,
                            'service' => $appointment->service_type ?? 'N/A',
                            'status' => strtolower($appointment->status),
                            'status_display' => ucfirst($appointment->status),
                            'service_date' => $appointment->service_date ? \Carbon\Carbon::parse($appointment->service_date)->format('F j, Y') : 'N/A',
                            'service_date_raw' => $appointment->service_date ? \Carbon\Carbon::parse($appointment->service_date)->format('Y-m-d') : '',
                            'service_time' => $appointment->formatted_service_time ?? 'N/A',
                            'action_label' => 'View Details',
                            'action_onclick' => 'viewDetails(' . $appointment->id . ')',
                            'menu_items' => []
                        ];
                    })->toArray()" :maxHeight="'30rem'" />
                </div>
            </div>
            <!-- Appointment Details Slide-in Drawer -->
            <x-client-components.shared.appointment-details-drawer
                showVar="showDrawer"
                dataVar="selectedAppointment"
                closeMethod="closeDrawer"
                title="Appointment Details"
                :showTeam="false">
                <x-slot name="footer">
                    <div class="flex gap-3">
                        <button
                            x-show="selectedAppointment && selectedAppointment.status === 'pending'"
                            @click="cancelAppointment(selectedAppointment.id)"
                            class="flex-1 text-sm px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors font-medium">
                            Cancel Appointment
                        </button>
                        <button
                            @click="closeDrawer()"
                            class="flex-1 text-sm px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium">
                            Close
                        </button>
                    </div>
                </x-slot>
            </x-client-components.shared.appointment-details-drawer>
        </div>

        <!-- Right Panel - Attendance Overview -->
        <div id="tour-client-right-panel" class="flex flex-col gap-3 w-full lg:w-1/3 rounded-lg h-auto">
            <!-- Ready To Book Card - NEW -->
            <div id="ready-card" class="snap-start shrink-0 w-full relative overflow-hidden rounded-xl shadow-sm">
                <!-- Background Image for Light Mode -->
                <div class="absolute inset-0 bg-cover bg-center block dark:hidden"
                    style="background-image: url('{{ asset('images/backgrounds/ready-to-book-bg.svg') }}');">
                </div>

                <!-- Background Image for Dark Mode -->
                <div class="absolute inset-0 bg-cover bg-center hidden dark:block"
                    style="background-image: url('{{ asset('images/backgrounds/ready-to-book-bg-dark.svg') }}');">
                </div>

                <!-- Content -->
                <div class="relative p-6 h-full">
                    <div class="flex flex-col lg:flex-col items-center lg:items-start">
                        <!-- Text Content -->
                        <div class="flex flex-row w-full">
                            <h3 class="text-xl lg:text-xl font-black text-gray-900 dark:text-white mb-2 mt-3">
                                Ready To Book <br>Your Cleaning?
                            </h3>
                        </div>

                        <div class="mb-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Currently
                                <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $stats['ongoing'] ?? 0 }} Active<br>
                                    {{ Str::plural('Service', $stats['ongoing'] ?? 0) }}</span>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                                Choose your preferred date, select your unit size, and we'll take care of the
                                rest.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Inner Up - Recommendation Service List -->
            <div class="mt-3 w-full flex flex-col rounded-lg">
                <div
                    class="flex flex-col gap-6 snap-x snap-mandatory scroll-smooth scrollbar-custom w-full">

                    @php
                        $serviceMeta = [
                            'Final Cleaning' => ['badge' => 'Most Popular', 'price' => 'Fixed Price', 'description' => 'Complete cleaning service covering kitchen, living room, bedrooms, bathroom and sauna.'],
                            'Deep Cleaning' => ['badge' => 'Thorough', 'price' => 'From €48/hr', 'description' => 'Intensive deep clean with extra attention to hard-to-reach areas, detailed scrubbing, and full sanitization.'],
                            'Daily Cleaning' => ['badge' => 'Everyday', 'price' => 'From €35/hr', 'description' => 'Regular daily maintenance cleaning to keep your space fresh, tidy, and comfortable every day.'],
                            'Daily Room Cleaning' => ['badge' => 'Room Care', 'price' => 'From €35/hr', 'description' => 'Focused daily room upkeep including dusting, vacuuming, and surface cleaning for a consistently clean space.'],
                            'Snowout Cleaning' => ['badge' => 'Seasonal', 'price' => 'From €55/hr', 'description' => 'Specialized post-winter cleaning to remove salt, dirt, and debris brought in during snowy conditions.'],
                            'General Cleaning' => ['badge' => 'Essential', 'price' => 'From €40/hr', 'description' => 'Standard all-around cleaning covering floors, surfaces, kitchen, and bathrooms for a fresh environment.'],
                            'Hotel Cleaning' => ['badge' => 'Hospitality', 'price' => 'From €42/hr', 'description' => 'Professional hotel-standard cleaning for guest rooms, lobbies, and common areas with turnover-ready results.'],
                        ];

                        $topRated = $topRatedServices ?? collect([]);

                        if ($topRated->isNotEmpty()) {
                            // Use top-rated services from feedback data
                            $services = $topRated->take(3)->map(function ($svc) use ($serviceMeta) {
                                $baseType = $svc->service_type;
                                $matchedMeta = $serviceMeta[$baseType] ?? null;
                                if (!$matchedMeta) {
                                    foreach ($serviceMeta as $key => $meta) {
                                        if (str_starts_with($baseType, $key)) {
                                            $matchedMeta = $meta;
                                            break;
                                        }
                                    }
                                }
                                $matchedMeta = $matchedMeta ?? ['badge' => 'Service', 'price' => 'Contact Us', 'description' => 'Professional cleaning service tailored to your needs.'];

                                return [
                                    'title' => $svc->service_type,
                                    'badge' => $matchedMeta['badge'],
                                    'rating' => number_format($svc->avg_rating, 1),
                                    'price' => $matchedMeta['price'],
                                    'description' => $matchedMeta['description'],
                                ];
                            })->toArray();
                        } else {
                            // No feedback yet — show default services with "Must Try" badge
                            $defaultOrder = ['Final Cleaning', 'Deep Cleaning', 'Daily Room Cleaning'];
                            $services = collect($defaultOrder)->map(function ($name) use ($serviceMeta) {
                                $meta = $serviceMeta[$name];
                                return [
                                    'title' => $name,
                                    'badge' => 'Must Try',
                                    'rating' => null,
                                    'price' => $meta['price'],
                                    'description' => $meta['description'],
                                ];
                            })->toArray();
                        }
                    @endphp

                    @foreach($services as $service)
                        <div class="snap-start shrink-0 w-full">
                            <x-client-components.dashboard-page.servicecard :service="$service" />
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </section>

    @push('scripts')
    <script>
    async function cancelAppointment(appointmentId) {
        try {
            await window.showConfirmDialog(
                'Cancel Appointment?',
                'Are you sure you want to cancel this appointment? This action cannot be undone.',
                'Yes, Cancel',
                'No, Keep It'
            );
        } catch (e) {
            return;
        }

        try {
            const response = await fetch(`/client/appointments/${appointmentId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                window.showSuccessDialog('Appointment Cancelled', data.message || 'Your appointment has been cancelled successfully.');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                window.showErrorDialog('Cancellation Failed', data.message || 'Failed to cancel the appointment. Please try again.');
            }
        } catch (error) {
            console.error('Error cancelling appointment:', error);
            window.showErrorDialog('Error', 'An unexpected error occurred. Please try again.');
        }
    }
    </script>
    @endpush

    <x-guided-tour tourName="client-dashboard" :steps="json_encode([
        [
            'title' => 'Welcome to Your Dashboard',
            'description' => 'This is your personal space to manage appointments and book cleaning services. Let us show you around!',
            'side' => 'bottom',
            'align' => 'center',
        ],
        [
            'element' => '#sidebar',
            'title' => 'Navigation Menu',
            'description' => 'Quickly navigate to your Appointments, Pricing information, and service History from here.',
            'side' => 'right',
            'align' => 'start',
        ],
        [
            'element' => '#tour-client-calendar',
            'title' => 'Your Calendar',
            'description' => 'View your scheduled appointments and important dates on this calendar.',
            'side' => 'top',
            'align' => 'center',
        ],
        [
            'element' => '#tour-client-appointments',
            'title' => 'Today\'s Appointments',
            'description' => 'See all your appointments for today. Use the filter and sort options to find specific bookings. Click any appointment for full details.',
            'side' => 'top',
            'align' => 'center',
        ],
        [
            'element' => '#tour-client-right-panel',
            'title' => 'Book & Track',
            'description' => 'Book new cleaning services and view your upcoming appointments from this panel.',
            'side' => 'left',
            'align' => 'start',
        ],
    ])" />
    </x-skeleton-page>
</x-layouts.general-client>