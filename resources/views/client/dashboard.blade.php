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
                showModal: false,
                selectedAppointment: null,
                allAppointments: {{ collect($appointments)->toJson() }},

                viewDetails(appointmentId) {
                    this.selectedAppointment = this.allAppointments.find(apt => apt.id === appointmentId);
                    if (this.selectedAppointment) {
                        this.showModal = true;
                        document.body.style.overflow = 'hidden';
                    }
                },

                closeModal() {
                    this.showModal = false;
                    this.selectedAppointment = null;
                    document.body.style.overflow = 'auto';
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                },

                formatTime(timeString) {
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
                    <x-labelwithvalue label="Ongoing Appointments" :count="'(' . $stats['ongoing'] . ')'" />
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
                <!-- Remove overflow-y-auto from here and add a wrapper inside the component -->
                <div id="dashboard-appointments-list" class="h-48 overflow-y-auto">
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
            <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
                <!-- Backdrop -->
                <div x-show="showModal"
                     x-transition:enter="transition-opacity ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeModal()"
                     class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

                <!-- Drawer Panel -->
                <div class="fixed inset-y-0 right-0 flex max-w-full">
                    <div x-show="showModal"
                         x-transition:enter="transform transition ease-in-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-200"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         @click.stop
                         class="relative w-screen max-w-md sm:max-w-lg">

                        <!-- Drawer Content -->
                        <div class="h-full flex flex-col bg-white dark:bg-slate-800 shadow-2xl border-l border-gray-200 dark:border-slate-700">
                            <!-- Drawer Header -->
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-slate-800/50">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Appointment Details</h2>
                                <button type="button" @click="closeModal()"
                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Drawer Body (Scrollable) -->
                            <div class="flex-1 overflow-y-auto p-6" x-show="selectedAppointment">
                                <!-- Status Badge -->
                                <div class="flex items-center gap-2 mb-6">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                    <span x-show="selectedAppointment && selectedAppointment.status === 'pending'"
                                        class="px-3 py-1 text-xs rounded-full bg-[#FFA50020] text-[#FFA500] font-semibold">Pending</span>
                                    <span x-show="selectedAppointment && selectedAppointment.status === 'approved'"
                                        class="px-3 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">Approved</span>
                                    <span x-show="selectedAppointment && selectedAppointment.status === 'confirmed'"
                                        class="px-3 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">Confirmed</span>
                                    <span x-show="selectedAppointment && selectedAppointment.status === 'completed'"
                                        class="px-3 py-1 text-xs rounded-full bg-[#00BFFF20] text-[#00BFFF] font-semibold">Completed</span>
                                    <span x-show="selectedAppointment && selectedAppointment.status === 'cancelled'"
                                        class="px-3 py-1 text-xs rounded-full bg-[#FE1E2820] text-[#FE1E28] font-semibold">Cancelled</span>
                                    <span x-show="selectedAppointment && selectedAppointment.status === 'rejected'"
                                        class="px-3 py-1 text-xs rounded-full bg-[#FE1E2820] text-[#FE1E28] font-semibold">Rejected</span>
                                </div>

                                <!-- Service Details Section -->
                                <div class="mb-5">
                                    <div class="py-3">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Service Details</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">View the details of the service availed for this appointment</p>
                                    </div>

                                    <div class="space-y-4 text-sm py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500 dark:text-gray-400">Service Type</span>
                                            <span class="font-medium text-gray-900 dark:text-white text-right"
                                                x-text="selectedAppointment?.service_type || '-'"></span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500 dark:text-gray-400">Service Date</span>
                                            <span class="font-medium text-gray-900 dark:text-white text-right">
                                                <span x-text="selectedAppointment ? formatDate(selectedAppointment.service_date) : '-'"></span>
                                                <span x-show="selectedAppointment && (selectedAppointment.is_sunday || selectedAppointment.is_holiday)"
                                                    class="ml-1 text-xs text-orange-600 dark:text-orange-400 font-semibold">(2x)</span>
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500 dark:text-gray-400">Service Time</span>
                                            <span class="font-medium text-gray-900 dark:text-white text-right"
                                                x-text="selectedAppointment ? formatTime(selectedAppointment.service_time) : '-'"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Unit Details Section -->
                                <div class="mb-5">
                                    <div class="py-3">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Unit Details</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">View the details of the units included for this appointment</p>
                                    </div>

                                    <div class="space-y-1 py-3">
                                        <template x-if="selectedAppointment && selectedAppointment.unit_details && Array.isArray(selectedAppointment.unit_details) && selectedAppointment.unit_details.length > 0">
                                            <div class="">
                                                <template x-for="(unit, index) in selectedAppointment.unit_details" :key="index">
                                                    <div class="flex justify-between items-center py-2 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg mb-2">
                                                        <div class="flex items-center gap-1 flex-1 min-w-0">
                                                            <span class="text-sm font-semibold text-gray-500 dark:text-gray-400 flex-shrink-0"
                                                                x-text="'Unit ' + (index + 1)"></span>
                                                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                                                <span class="font-medium text-gray-900 dark:text-white text-sm truncate"
                                                                    x-text="unit.name || '-'"></span>
                                                                <span class="text-sm text-gray-500 dark:text-gray-400 flex-shrink-0">
                                                                    Size: <span x-text="unit.size || '-'"></span> m²
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="text-right flex-shrink-0 ml-3" x-show="unit.price">
                                                            <span class="text-base font-bold text-gray-900 dark:text-white">
                                                                €<span x-text="parseFloat(unit.price).toFixed(2)"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!selectedAppointment || !selectedAppointment.unit_details || !Array.isArray(selectedAppointment.unit_details) || selectedAppointment.unit_details.length === 0">
                                            <div class="flex justify-between items-center py-2.5 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 flex-shrink-0">Unit 1</span>
                                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                                        <span class="font-medium text-gray-900 dark:text-white text-sm truncate"
                                                            x-text="selectedAppointment?.cabin_name || '-'"></span>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
                                                            Size: <span x-text="selectedAppointment?.unit_size || '-'"></span> m²
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Special Requests Section -->
                                <div class="mb-5" x-show="selectedAppointment && selectedAppointment.special_requests">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Special Requests</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 p-3 rounded-lg"
                                        x-text="selectedAppointment?.special_requests || '-'"></p>
                                </div>

                                <!-- Total Amount -->
                                <div class="my-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="text-base font-bold text-gray-900 dark:text-white">Total Amount</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">VAT Inclusive</div>
                                        </div>
                                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                            €<span x-text="selectedAppointment ? parseFloat(selectedAppointment.total_amount).toFixed(2) : '0.00'"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Drawer Footer (Sticky) -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                                <div class="flex gap-3">
                                    <button
                                        x-show="selectedAppointment && selectedAppointment.status === 'pending'"
                                        @click="cancelAppointment(selectedAppointment.id); closeModal()"
                                        class="flex-1 text-sm px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors font-medium">
                                        Cancel Appointment
                                    </button>
                                    <button
                                        @click="closeModal()"
                                        class="flex-1 text-sm px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                <span class="font-bold text-gray-900 dark:text-white text-sm">2 Cleaning<br>
                                    Services</span>
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
            <div class="mt-3 w-full flex flex-col overflow-y-auto rounded-lg h-full sm:h-full md:h-full">
                <div
                    class="flex flex-col gap-6 overflow-y-auto snap-x snap-mandatory scroll-smooth scrollbar-custom w-full">

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
        </div>
    </section>
</x-layouts.general-client>