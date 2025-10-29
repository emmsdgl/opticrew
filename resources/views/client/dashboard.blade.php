<x-layouts.general-client :title="'Client Dashboard'">
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
                <x-calendar :holidays="$holidays" />
            </div>

            <!-- Inner Bottom - Recent Orders -->
            <!-- Appointments List with Filters -->
            <div x-data="{
                timeFilter: 'All Time',
                serviceFilter: 'Service Type',
                timeDropdownOpen: false,
                serviceDropdownOpen: false,
                showModal: false,
                selectedAppointment: null,
                allAppointments: {{ $appointments->toJson() }},

                selectTimeFilter(value) {
                    this.timeFilter = value;
                    this.timeDropdownOpen = false;
                    console.log('Time filter set to:', value);
                },

                selectServiceFilter(value) {
                    this.serviceFilter = value;
                    this.serviceDropdownOpen = false;
                    console.log('Service filter set to:', value);
                },

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

                getStatusBadgeText(status) {
                    const texts = {
                        'pending': 'Pending',
                        'confirmed': 'Confirmed',
                        'completed': 'Completed',
                        'cancelled': 'Cancelled'
                    };
                    return texts[status] || status;
                },

                formatTime(timeString) {
                    if (!timeString) return '-';
                    // Handle different time formats (HH:MM:SS or HH:MM)
                    const parts = timeString.split(':');
                    if (parts.length < 2) return timeString;

                    let hours = parseInt(parts[0]);
                    const minutes = parts[1];
                    const ampm = hours >= 12 ? 'PM' : 'AM';

                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'

                    return hours + ':' + minutes + ' ' + ampm;
                },

                get filteredAppointments() {
                    let filtered = [...this.allAppointments];
                    console.log('Filtering appointments. Time:', this.timeFilter, 'Service:', this.serviceFilter);

                    // Filter by service type
                    if (this.serviceFilter !== 'Service Type') {
                        filtered = filtered.filter(apt => apt.service_type === this.serviceFilter);
                    }

                    // Filter by time
                    if (this.timeFilter !== 'All Time') {
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);

                        if (this.timeFilter === 'This Day') {
                            filtered = filtered.filter(apt => {
                                const aptDate = new Date(apt.service_date);
                                aptDate.setHours(0, 0, 0, 0);
                                return aptDate.getTime() === today.getTime();
                            });
                        } else if (this.timeFilter === 'This Week') {
                            const weekStart = new Date(today);
                            weekStart.setDate(today.getDate() - today.getDay());
                            const weekEnd = new Date(weekStart);
                            weekEnd.setDate(weekStart.getDate() + 6);
                            weekEnd.setHours(23, 59, 59, 999);

                            filtered = filtered.filter(apt => {
                                const aptDate = new Date(apt.service_date);
                                return aptDate >= weekStart && aptDate <= weekEnd;
                            });
                        } else if (this.timeFilter === 'This Month') {
                            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                            const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                            monthEnd.setHours(23, 59, 59, 999);

                            filtered = filtered.filter(apt => {
                                const aptDate = new Date(apt.service_date);
                                return aptDate >= monthStart && aptDate <= monthEnd;
                            });
                        }
                    }

                    console.log('Filtered results:', filtered.length, 'appointments');
                    return filtered;
                }
            }">
                <!-- Filter Header -->
                <div class="flex flex-row justify-between w-full items-center mb-2">
                    <x-labelwithvalue label="Ongoing Appointments" :count="'(' . $stats['ongoing'] . ')'" />
                    <div class="flex flex-row gap-3">
                        @php
                            $timeOptions = ['This Day', 'This Week', 'This Month', 'All Time'];
                            $serviceOptions = ['Final Cleaning', 'Deep Cleaning'];
                        @endphp

                        <!-- Time Filter Dropdown -->
                        <div class="relative inline-block">
                            <button @click="timeDropdownOpen = !timeDropdownOpen" type="button"
                                class="bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-3 inline-flex justify-between items-center gap-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-blue-800 transition-all duration-300">
                                <span class="text-gray-700 dark:text-white text-xs font-normal" x-text="timeFilter"></span>
                                <svg class="w-2.5 h-2.5 ms-2 transition-transform duration-300 text-gray-600 dark:text-gray-400"
                                    :class="{ 'rotate-180': timeDropdownOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </button>
                            <div x-show="timeDropdownOpen" @click.away="timeDropdownOpen = false" x-transition
                                class="absolute right-0 top-full mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-lg min-w-[10rem] dark:bg-gray-700"
                                style="display: none;">
                                <ul class="py-2 text-xs text-gray-700 dark:text-white">
                                    @foreach($timeOptions as $option)
                                    <li>
                                        <button @click="selectTimeFilter('{{ $option }}')" type="button"
                                            class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                            :class="{ 'bg-gray-100 dark:bg-gray-600': timeFilter === '{{ $option }}' }">
                                            {{ $option }}
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Service Type Filter Dropdown -->
                        <div class="relative inline-block">
                            <button @click="serviceDropdownOpen = !serviceDropdownOpen" type="button"
                                class="bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-3 inline-flex justify-between items-center gap-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-blue-800 transition-all duration-300">
                                <span class="text-gray-700 dark:text-white text-xs font-normal" x-text="serviceFilter"></span>
                                <svg class="w-2.5 h-2.5 ms-2 transition-transform duration-300 text-gray-600 dark:text-gray-400"
                                    :class="{ 'rotate-180': serviceDropdownOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </button>
                            <div x-show="serviceDropdownOpen" @click.away="serviceDropdownOpen = false" x-transition
                                class="absolute right-0 top-full mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-lg min-w-[10rem] dark:bg-gray-700"
                                style="display: none;">
                                <ul class="py-2 text-xs text-gray-700 dark:text-white">
                                    <li>
                                        <button @click="selectServiceFilter('Service Type')" type="button"
                                            class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                            :class="{ 'bg-gray-100 dark:bg-gray-600': serviceFilter === 'Service Type' }">
                                            All Services
                                        </button>
                                    </li>
                                    @foreach($serviceOptions as $option)
                                    <li>
                                        <button @click="selectServiceFilter('{{ $option }}')" type="button"
                                            class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                            :class="{ 'bg-gray-100 dark:bg-gray-600': serviceFilter === '{{ $option }}' }">
                                            {{ $option }}
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <a href="{{ route('client.appointment.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Book a Service
                        </a>
                    </div>
                </div>
                <div class="w-full overflow-x-auto">
                    <!-- Table Header -->
                    <div class="hidden md:grid grid-cols-6 gap-4 px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 rounded-t-lg">
                        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Status</div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Service Type</div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Date & Time</div>
                        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Units / Cabin</div>
                        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Total Amount</div>
                        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Action</div>
                    </div>

                    <!-- Table Body -->
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="appointment in filteredAppointments" :key="appointment.id">
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 px-6 py-4 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <!-- Status Badge -->
                                <div class="flex items-center gap-2">
                                    <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400">Status:</span>
                                    <span x-show="appointment.status === 'pending'" class="px-2 py-1 text-xs rounded-full bg-[#FFA50020] text-[#FFA500]">Pending</span>
                                    <span x-show="appointment.status === 'confirmed'" class="px-2 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00]">Confirmed</span>
                                    <span x-show="appointment.status === 'completed'" class="px-2 py-1 text-xs rounded-full bg-[#00BFFF20] text-[#00BFFF]">Completed</span>
                                    <span x-show="appointment.status === 'cancelled'" class="px-2 py-1 text-xs rounded-full bg-[#FE1E2820] text-[#FE1E28]">Cancelled</span>
                                </div>

                                <!-- Service Type -->
                                <div class="flex flex-col">
                                    <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Service:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="appointment.service_type"></span>
                                    <span x-show="appointment.number_of_units > 1" class="text-xs text-gray-500 dark:text-gray-400" x-text="appointment.number_of_units + ' units'"></span>
                                </div>

                                <!-- Date & Time -->
                                <div class="flex flex-col">
                                    <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Date & Time:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="new Date(appointment.service_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></span>
                                    <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                        <i class="fi fi-rr-clock text-xs"></i>
                                        <span x-text="formatTime(appointment.service_time)"></span>
                                        <span x-show="appointment.is_sunday || appointment.is_holiday" class="ml-1 text-orange-600 dark:text-orange-400 font-semibold">(2x)</span>
                                    </div>
                                </div>

                                <!-- Units / Cabin -->
                                <div class="flex flex-col">
                                    <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Units:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="appointment.cabin_name"></span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="appointment.unit_size + ' m²'"></span>
                                </div>

                                <!-- Total Amount -->
                                <div class="flex flex-col">
                                    <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Total:</span>
                                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400" x-text="'€' + parseFloat(appointment.total_amount).toFixed(2)"></span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">VAT incl.</span>
                                </div>

                                <!-- Action Button -->
                                <div class="flex items-start gap-2">
                                    <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Action:</span>
                                    <button @click="viewDetails(appointment.id)"
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                                        <i class="fi fi-rr-eye text-xs mr-1"></i>View
                                    </button>
                                    <button x-show="appointment.status === 'pending'" @click="cancelAppointment(appointment.id)"
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40">
                                        <i class="fi fi-rr-cross-circle text-xs mr-1"></i>Cancel
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="filteredAppointments.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 rounded-b-lg">
                        <i class="fa-regular fa-calendar-xmark text-4xl mb-4"></i>
                        <p class="text-sm font-medium">No appointments found for the selected filters</p>
                        <p class="text-xs mt-2">Try adjusting your filters or book a new appointment</p>
                    </div>
                </div>

                <!-- Appointment Details Modal -->
                <div x-show="showModal" x-cloak @click="closeModal()"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
                    style="display: none;">
                    <div @click.stop class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
                        x-show="showModal" x-transition>

                        <!-- Modal Header -->
                        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-between items-center z-10">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Appointment Details</h2>
                            <button @click="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6" x-show="selectedAppointment">
                            <!-- Status Badge -->
                            <div class="mb-6 flex items-center gap-3">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Status:</span>
                                <span x-show="selectedAppointment && selectedAppointment.status === 'pending'" class="px-3 py-1 text-sm rounded-full bg-[#FFA50020] text-[#FFA500] font-semibold">Pending</span>
                                <span x-show="selectedAppointment && selectedAppointment.status === 'confirmed'" class="px-3 py-1 text-sm rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">Confirmed</span>
                                <span x-show="selectedAppointment && selectedAppointment.status === 'completed'" class="px-3 py-1 text-sm rounded-full bg-[#00BFFF20] text-[#00BFFF] font-semibold">Completed</span>
                                <span x-show="selectedAppointment && selectedAppointment.status === 'cancelled'" class="px-3 py-1 text-sm rounded-full bg-[#FE1E2820] text-[#FE1E28] font-semibold">Cancelled</span>
                            </div>

                            <!-- Appointment Details -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg">
                                <!-- Service Details -->
                                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Service Details</h4>
                                    <div class="space-y-3 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">Service Type</span>
                                            <span class="font-medium text-gray-900 dark:text-white" x-text="selectedAppointment?.service_type || '-'"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">Service Date</span>
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                <span x-text="selectedAppointment ? new Date(selectedAppointment.service_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-'"></span>
                                                <span x-show="selectedAppointment && (selectedAppointment.is_sunday || selectedAppointment.is_holiday)" class="ml-2 text-xs text-orange-600 dark:text-orange-400 font-semibold">(2x)</span>
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">Service Time</span>
                                            <span class="font-medium text-gray-900 dark:text-white" x-text="selectedAppointment ? formatTime(selectedAppointment.service_time) : '-'"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Unit Details -->
                                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Unit Details</h4>
                                    <div class="space-y-3">
                                        <!-- Check if unit_details exists and has data -->
                                        <template x-if="selectedAppointment && selectedAppointment.unit_details && Array.isArray(selectedAppointment.unit_details) && selectedAppointment.unit_details.length > 0">
                                            <div class="space-y-3">
                                                <template x-for="(unit, index) in selectedAppointment.unit_details" :key="index">
                                                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                                        <div class="flex justify-between items-start mb-3">
                                                            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                                                <span x-text="'Unit ' + (index + 1)"></span>
                                                            </div>
                                                            <div class="text-right" x-show="unit.price">
                                                                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                                                    €<span x-text="parseFloat(unit.price).toFixed(2)"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                                            <div>
                                                                <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                                                <span class="font-medium text-gray-900 dark:text-white ml-1" x-text="unit.name || '-'"></span>
                                                            </div>
                                                            <div>
                                                                <span class="text-gray-600 dark:text-gray-400">Size:</span>
                                                                <span class="font-medium text-gray-900 dark:text-white ml-1">
                                                                    <span x-text="unit.size || '-'"></span> m²
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- Fallback to old single unit display -->
                                        <template x-if="!selectedAppointment || !selectedAppointment.unit_details || !Array.isArray(selectedAppointment.unit_details) || selectedAppointment.unit_details.length === 0">
                                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                                <div class="flex justify-between items-start mb-3">
                                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                                        <span x-text="selectedAppointment && selectedAppointment.number_of_units > 1 ? selectedAppointment.number_of_units + ' Units' : 'Unit 1'"></span>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-3 text-sm">
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                                        <span class="font-medium text-gray-900 dark:text-white ml-1" x-text="selectedAppointment?.cabin_name || '-'"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">Size:</span>
                                                        <span class="font-medium text-gray-900 dark:text-white ml-1" x-text="selectedAppointment ? selectedAppointment.unit_size + ' m²' : '-'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Special Requests -->
                                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700" x-show="selectedAppointment && selectedAppointment.special_requests">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Special Requests</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="selectedAppointment?.special_requests || '-'"></p>
                                </div>

                                <!-- Pricing Notice -->
                                <div class="mb-6 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                    <p class="text-xs text-yellow-800 dark:text-yellow-200">
                                        <i class="fi fi-rr-info mr-1"></i>
                                        Sundays and holidays are charged double the price. All rates are inclusive of VAT.
                                    </p>
                                </div>

                                <!-- Total -->
                                <div class="flex justify-between items-center pt-4 border-t-2 border-gray-300 dark:border-gray-600 mb-2">
                                    <div>
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">Total Amount</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">VAT Inclusive</div>
                                    </div>
                                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        €<span x-text="selectedAppointment ? parseFloat(selectedAppointment.total_amount).toFixed(2) : '0.00'"></span>
                                    </span>
                                </div>

                                <div class="text-sm font-semibold text-blue-600 dark:text-blue-400 mb-6 text-right">
                                    Payment Amount: €<span x-text="selectedAppointment ? parseFloat(selectedAppointment.total_amount).toFixed(2) : '0.00'"></span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3 justify-end">
                                    <button @click="closeModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                        Close
                                    </button>
                                    <button x-show="selectedAppointment && selectedAppointment.status === 'pending'"
                                        @click="cancelAppointment(selectedAppointment.id); closeModal()"
                                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                        Cancel Appointment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Attendance Overview -->
        <div
            class="flex flex-col gap-6 w-full lg:w-1/3 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-auto p-6">

            <!-- Inner Up - Recommendation Service List -->
            <x-labelwithvalue label="Recommended Services For You" count="(2)" />
            <div class="w-full flex flex-col overflow-y-auto rounded-lg h-full sm:h-full md:h-full">
                <div
                    class="flex flex-col gap-6 p-2 overflow-y-auto snap-x snap-mandatory scroll-smooth scrollbar-custom w-full">
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

                    @foreach($services as $service)
                        <div class="snap-start shrink-0 w-full">
                            <x-servicecard :service="$service" />
                        </div>
                    @endforeach
                </div>
            </div>
    </section>
</x-layouts.general-client>