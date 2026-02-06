@props([
    'appointments' => [],
    'showHeader' => true,
])

<div class="w-full min-w-full overflow-visible" x-data="{
    showModal: false,
    selectedAppointment: null,

    viewDetails(appointmentId) {
        this.selectedAppointment = {{ $appointments->toJson() }}.find(apt => apt.id === appointmentId);
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
    },

    formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            month: 'long', 
            day: 'numeric', 
            year: 'numeric' 
        });
    }
}">
    <!-- Table Header -->
    @if($showHeader)
    <div class="hidden md:grid md:grid-cols-[1fr_1.2fr_1.2fr_1fr_1fr_1fr] gap-4 px-6 py-4
                border-b border-gray-200 dark:border-gray-700 rounded-t-lg w-full">
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Status
        </div>
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Service Type
        </div>
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Date & Time
        </div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Units / Cabin</div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Total Amount</div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Action</div>
    </div>
    @endif

    <!-- Table Body -->
    <div class="w-full divide-y divide-gray-200 dark:divide-gray-700">
        @foreach($appointments as $appointment)
        <div class="grid grid-cols-1 md:grid-cols-[1fr_1.2fr_1.2fr_1fr_1fr_1fr] gap-4 px-6 py-4
                    hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors w-full"
             data-appointment-item
             data-status="{{ $appointment->status }}"
             data-service="{{ $appointment->service_type }}"
             data-date="{{ \Carbon\Carbon::parse($appointment->service_date)->format('Y-m-d') }}"
             data-amount="{{ $appointment->total_amount }}"
             data-cabin="{{ $appointment->cabin_name }}">

            <!-- Status Badge -->
            <div class="flex items-center gap-2">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400">Status:</span>
                @if($appointment->status === 'pending')
                    <x-badge
                        label="Pending"
                        colorClass="bg-[#FFA50020] text-[#FFA500]"
                        size="text-xs" />
                @elseif($appointment->status === 'approved')
                    <x-badge
                        label="Approved"
                        colorClass="bg-[#2FBC0020] text-[#2FBC00]"
                        size="text-xs" />
                @elseif($appointment->status === 'confirmed')
                    <x-badge
                        label="Confirmed"
                        colorClass="bg-[#2FBC0020] text-[#2FBC00]"
                        size="text-xs" />
                @elseif($appointment->status === 'completed')
                    <x-badge
                        label="Completed"
                        colorClass="bg-[#00BFFF20] text-[#00BFFF]"
                        size="text-xs" />
                @elseif($appointment->status === 'cancelled')
                    <x-badge
                        label="Cancelled"
                        colorClass="bg-[#FE1E2820] text-[#FE1E28]"
                        size="text-xs" />
                @elseif($appointment->status === 'rejected')
                    <x-badge
                        label="Rejected"
                        colorClass="bg-[#FE1E2820] text-[#FE1E28]"
                        size="text-xs" />
                @endif
            </div>

            <!-- Service Type -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Service:</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $appointment->service_type }}</span>
                @if($appointment->number_of_units > 1)
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->number_of_units }} units</span>
                @endif
            </div>

            <!-- Date & Time -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Date & Time:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ \Carbon\Carbon::parse($appointment->service_date)->format('M d, Y') }}
                </span>
                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                    <i class="fi fi-rr-clock text-xs"></i>
                    {{ \Carbon\Carbon::parse($appointment->service_time)->format('g:i A') }}
                    @if($appointment->is_sunday || $appointment->is_holiday)
                        <span class="ml-1 text-orange-600 dark:text-orange-400 font-semibold">(2x)</span>
                    @endif
                </div>
            </div>

            <!-- Units / Cabin -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Units:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $appointment->cabin_name }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->unit_size }} m²</span>
            </div>

            <!-- Total Amount -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Total:</span>
                <span class="text-sm font-bold text-blue-600 dark:text-blue-400">
                    €{{ number_format($appointment->total_amount, 2) }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">VAT incl.</span>
            </div>

            <!-- Action Button -->
            <div class="flex items-center justify-start md:justify-center">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mr-2">Action:</span>
                <button
                    @click="viewDetails({{ $appointment->id }})"
                    class="px-6 py-3 text-xs font-medium text-gray-700 dark:text-white hover:text-blue-500 hover:bg-blue-500/10 dark:hover:text-blue-500 rounded-full transition-colors duration-200">
                    View Details
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if(count($appointments) === 0)
    <div class="text-center py-12 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 rounded-b-lg">
        <i class="fa-regular fa-calendar-xmark text-4xl mb-4"></i>
        <p class="text-sm font-medium">No appointments found</p>
        <p class="text-xs mt-2">Book a new appointment to get started</p>
        <a href="{{ route('client.appointment.create') }}"
           class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fi fi-rr-plus mr-2"></i>
            Book Appointment
        </a>
    </div>
    @endif

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
                                            <div class="flex justify-between items-center py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
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
                                    <p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-lg"
                                        x-text="selectedAppointment?.special_requests || '-'"></p>
                                </div>

                                <!-- Service Checklist Section -->
                                <div class="mb-5" x-data="serviceChecklist()">
                                    <div class="py-3">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Service Checklist</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                            <template x-if="selectedAppointment?.status === 'completed'">
                                                <span>All tasks completed by our team</span>
                                            </template>
                                            <template x-if="selectedAppointment?.status === 'confirmed' || selectedAppointment?.status === 'approved'">
                                                <span>Tasks to be performed for this service</span>
                                            </template>
                                            <template x-if="selectedAppointment?.status === 'pending'">
                                                <span>Tasks that will be performed once approved</span>
                                            </template>
                                            <template x-if="selectedAppointment?.status === 'cancelled' || selectedAppointment?.status === 'rejected'">
                                                <span>This appointment was cancelled</span>
                                            </template>
                                        </p>
                                    </div>

                                    <!-- Progress indicator for completed appointments -->
                                    <template x-if="selectedAppointment?.status === 'completed'">
                                        <div class="mb-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                                                <span class="text-sm font-medium text-green-700 dark:text-green-300">
                                                    Service completed successfully
                                                </span>
                                            </div>
                                            <p class="text-xs text-green-600 dark:text-green-400 mt-1 ml-6">
                                                All checklist items have been completed by our team
                                            </p>
                                        </div>
                                    </template>

                                    <!-- Checklist items with status-based icons -->
                                    <div class="space-y-2 max-h-48 overflow-y-auto bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                                        <template x-for="(item, index) in getChecklistItems(selectedAppointment?.service_type)" :key="index">
                                            <div class="flex items-center gap-2 py-1.5">
                                                <!-- Show different icons based on appointment status -->
                                                <template x-if="selectedAppointment?.status === 'completed'">
                                                    <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                                </template>
                                                <template x-if="selectedAppointment?.status === 'confirmed' || selectedAppointment?.status === 'approved'">
                                                    <i class="fa-regular fa-circle text-blue-400 text-xs"></i>
                                                </template>
                                                <template x-if="selectedAppointment?.status === 'pending'">
                                                    <i class="fa-regular fa-circle text-gray-400 text-xs"></i>
                                                </template>
                                                <template x-if="selectedAppointment?.status === 'cancelled' || selectedAppointment?.status === 'rejected'">
                                                    <i class="fa-solid fa-times-circle text-gray-400 text-xs"></i>
                                                </template>
                                                <span class="text-sm"
                                                    :class="{
                                                        'text-gray-700 dark:text-gray-300': selectedAppointment?.status !== 'cancelled' && selectedAppointment?.status !== 'rejected',
                                                        'text-gray-400 dark:text-gray-500 line-through': selectedAppointment?.status === 'cancelled' || selectedAppointment?.status === 'rejected'
                                                    }"
                                                    x-text="item"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Progress bar for completed appointments -->
                                    <template x-if="selectedAppointment?.status === 'completed'">
                                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Progress</span>
                                                <span class="text-xs text-green-600 dark:text-green-400 font-semibold">100% Complete</span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                <div class="bg-green-600 h-1.5 rounded-full w-full"></div>
                                            </div>
                                        </div>
                                    </template>
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


@push('scripts')
<script>
function cancelAppointment(appointmentId) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        // TODO: Implement cancellation API call
        console.log('Cancelling appointment:', appointmentId);
        alert('Appointment #' + appointmentId + ' has been cancelled');
        // Reload page to reflect changes
        // window.location.reload();
    }
}

function serviceChecklist() {
    return {
        checklistTemplates: {
            daily_cleaning: [
                'Sweep and mop floors',
                'Vacuum carpets/rugs',
                'Dust furniture and surfaces',
                'Wipe tables and countertops',
                'Empty trash bins',
                'Wipe kitchen counters',
                'Clean sink',
                'Wash visible dishes',
                'Wipe appliance exteriors',
                'Clean toilet and sink',
                'Wipe mirrors',
                'Mop floor',
                'Organize cluttered areas',
                'Light deodorizing',
            ],
            snowout_cleaning: [
                'Remove mud, water, and debris',
                'Clean door mats',
                'Mop and dry floors',
                'Deep vacuum carpets',
                'Mop with disinfectant solution',
                'Wipe walls near entrances',
                'Dry wet surfaces',
                'Check for water accumulation',
                'Clean and sanitize affected areas',
                'Dispose of tracked-in debris',
                'Replace trash liners',
            ],
            deep_cleaning: [
                'Dust high and low areas (vents, corners, baseboards)',
                'Clean behind and under furniture',
                'Wash walls and remove stains',
                'Deep vacuum carpets',
                'Clean inside microwave',
                'Degrease stove and range hood',
                'Clean inside refrigerator (if included)',
                'Scrub tile grout',
                'Remove limescale and mold buildup',
                'Deep scrub tiles and grout',
                'Sanitize all fixtures thoroughly',
                'Clean window interiors',
                'Polish handles and knobs',
                'Disinfect frequently touched surfaces',
            ],
            general_cleaning: [
                'Dust surfaces',
                'Sweep/vacuum floors',
                'Mop hard floors',
                'Clean glass and mirrors',
                'Wipe countertops',
                'Clean sink',
                'Take out trash',
                'Clean toilet, sink, and mirror',
                'Mop floor',
                'Arrange items neatly',
                'Dispose of garbage',
                'Light air freshening',
            ],
            hotel_cleaning: [
                'Make bed with fresh linens',
                'Replace pillowcases and sheets',
                'Dust all surfaces (tables, headboard, shelves)',
                'Vacuum carpet / sweep & mop floor',
                'Clean mirrors and glass surfaces',
                'Check under bed for trash/items',
                'Empty trash bins and replace liners',
                'Clean and disinfect toilet',
                'Scrub shower walls, tub, and floor',
                'Clean sink and countertop',
                'Polish fixtures',
                'Replace towels, bath mat, tissue, and toiletries',
                'Mop bathroom floor',
                'Refill water, coffee, and room amenities',
                'Replace slippers and hygiene kits',
                'Check minibar (if applicable)',
                'Ensure lights, AC, TV working',
                'Arrange curtains neatly',
                'Deodorize room',
            ],
        },

        getChecklistItems(serviceType) {
            if (!serviceType) return this.checklistTemplates.general_cleaning;

            const type = serviceType.toLowerCase();

            if (type.includes('daily') || type.includes('routine')) {
                return this.checklistTemplates.daily_cleaning;
            } else if (type.includes('snowout') || type.includes('weather')) {
                return this.checklistTemplates.snowout_cleaning;
            } else if (type.includes('deep')) {
                return this.checklistTemplates.deep_cleaning;
            } else if (type.includes('hotel') || type.includes('room turnover')) {
                return this.checklistTemplates.hotel_cleaning;
            }

            return this.checklistTemplates.general_cleaning;
        }
    }
}
</script>
@endpush