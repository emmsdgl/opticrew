<x-layouts.general-client :title="'Client Dashboard'">
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
        <div class="flex flex-col gap-6 flex-1 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4"
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
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-48 sm:h-56 md:h-64 lg:h-1/3">
                <x-herocard :headerName="$client->first_name ?? 'Client'" :headerDesc="'Welcome to the dashboard. What needs cleaning today?'" :headerIcon="'hero-client'" />
            </div>

            <!-- Inner Middle - Calendar -->
            <x-labelwithvalue label="My Calendar" count="" />
            <div class="w-full pb-6 rounded-lg h-auto sm:h-72 md:h-80 lg:h-auto">
                <x-calendar :holidays="$holidays" />
            </div>

            <!-- Inner Bottom - Appointments List -->
            <div class="flex flex-col">
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
                <div id="dashboard-appointments-list" class="{{ $appointments->count() > 0 ? 'h-48 overflow-y-auto' : '' }}">
                    <x-client-components.appointment-page.appointment-overview-list :items="$appointments->map(function ($appointment) {
                        return [
                            'id' => $appointment->id,
                            'service' => $appointment->service_type ?? 'N/A',
                            'status' => strtolower($appointment->status),
                            'status_display' => ucfirst($appointment->status),
                            'service_date' => $appointment->service_date ? \Carbon\Carbon::parse($appointment->service_date)->format('F j, Y') : 'N/A',
                            'service_date_raw' => $appointment->service_date ? \Carbon\Carbon::parse($appointment->service_date)->format('Y-m-d') : '',
                            'service_time' => $appointment->service_time ? \Carbon\Carbon::parse($appointment->service_time)->format('g:i A') : 'N/A',
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
                            @click="cancelAppointment(selectedAppointment.id); closeDrawer()"
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
        <div class="flex flex-col gap-3 w-full lg:w-1/3 rounded-lg h-auto">
            <!-- Ready To Book Card - NEW -->
            <div id="ready-card" class="snap-start shrink-0 w-full relative overflow-hidden rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
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
            <div class="mt-3 w-full flex flex-col rounded-lg {{ $appointments->count() > 0 ? 'max-h-96 overflow-y-auto' : '' }}">
                <div
                    class="flex flex-col gap-6 snap-x snap-mandatory scroll-smooth scrollbar-custom w-full">

                    @php
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

                    @foreach($services as $service)
                        <div class="snap-start shrink-0 w-full">
                            <x-client-components.dashboard-page.servicecard :service="$service" />
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Services Availed Card -->
            @php
                $breakdown = $serviceBreakdown ?? ['completed_total' => 0, 'final_cleaning' => 0, 'deep_cleaning' => 0, 'other' => 0, 'month_change' => 0];
                $completedTotal = $breakdown['completed_total'];
                $allTotal = $stats['total'] ?? 0;
                $completionRate = $allTotal > 0 ? round(($completedTotal / $allTotal) * 100) : 0;
                $finalPct = $completedTotal > 0 ? round(($breakdown['final_cleaning'] / $completedTotal) * 100) : 0;
                $deepPct = $completedTotal > 0 ? round(($breakdown['deep_cleaning'] / $completedTotal) * 100) : 0;
                $otherPct = $completedTotal > 0 ? (100 - $finalPct - $deepPct) : 0;
                $monthChange = $breakdown['month_change'];
            @endphp
            <div class="mt-3 w-full relative overflow-hidden rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Services Availed</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Represents completion rate of your booked services.</p>

                    <!-- Percentage + Change -->
                    <div class="flex items-end gap-3 mt-4">
                        <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $completionRate }}%</span>
                        <div class="flex items-center gap-1 mb-1.5">
                            @if($monthChange > 0)
                                <span class="text-xs font-semibold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30 px-1.5 py-0.5 rounded">
                                    <i class="fa-solid fa-arrow-up text-[10px]"></i> {{ $monthChange }}%
                                </span>
                            @elseif($monthChange < 0)
                                <span class="text-xs font-semibold text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 px-1.5 py-0.5 rounded">
                                    <i class="fa-solid fa-arrow-down text-[10px]"></i> {{ abs($monthChange) }}%
                                </span>
                            @else
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">
                                    0%
                                </span>
                            @endif
                            <span class="text-sm text-gray-500 dark:text-gray-400">since last month</span>
                        </div>
                    </div>

                    <!-- Multi-segment Progress Bar -->
                    <div class="flex w-full h-2.5 rounded-full overflow-hidden mt-4 bg-gray-200 dark:bg-gray-700">
                        @if($completedTotal > 0)
                            <div class="bg-blue-500 h-full" style="width: {{ $finalPct }}%"></div>
                            <div class="bg-emerald-500 h-full" style="width: {{ $deepPct }}%"></div>
                            @if($otherPct > 0)
                                <div class="bg-amber-400 h-full" style="width: {{ $otherPct }}%"></div>
                            @endif
                        @endif
                    </div>

                    <!-- Legend -->
                    <div class="flex flex-wrap justify-between items-center gap-x-4 gap-y-1 mt-3">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-sm bg-blue-500 inline-block"></span>
                            <span class="text-xs text-gray-600 dark:text-gray-400">Final Cleaning</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-sm bg-emerald-500 inline-block"></span>
                            <span class="text-xs text-gray-600 dark:text-gray-400">Deep Cleaning</span>
                        </div>
                        @if($breakdown['other'] > 0)
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-sm bg-amber-400 inline-block"></span>
                            <span class="text-xs text-gray-600 dark:text-gray-400">Other</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
    async function cancelAppointment(appointmentId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            try {
                const response = await fetch(`/client/appointments/${appointmentId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Appointment cancelled successfully');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to cancel appointment');
                }
            } catch (error) {
                console.error('Error cancelling appointment:', error);
                alert('An error occurred while cancelling the appointment');
            }
        }
    }
    </script>
    @endpush
</x-layouts.general-client>