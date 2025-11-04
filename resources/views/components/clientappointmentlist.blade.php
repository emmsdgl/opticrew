@props([
    'appointments' => [],
    'showHeader' => true,
])

<div class="w-full overflow-x-auto" x-data="{
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
    <!-- Table Header -->
    @if($showHeader)
    <div class="hidden md:grid grid-cols-6 gap-4 px-6 py-4 bg-gray-50 dark:bg-gray-800
                border-b border-gray-200 dark:border-gray-700 rounded-t-lg">
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
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Action</div>
    </div>
    @endif

    <!-- Table Body -->
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach($appointments as $appointment)
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 px-6 py-4 bg-white dark:bg-gray-900
                    hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">

            <!-- Status Badge -->
            <div class="flex items-center gap-2">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400">Status:</span>
                @if($appointment->status === 'pending')
                    <x-badge
                        label="Pending"
                        colorClass="bg-[#FFA50020] text-[#FFA500]"
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
            <div class="flex items-start gap-2">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Action:</span>
                <button
                    @click="viewDetails({{ $appointment->id }})"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                           bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    <i class="fi fi-rr-eye text-xs mr-1"></i>
                    View
                </button>
                @if($appointment->status === 'pending')
                <button
                    @click="cancelAppointment({{ $appointment->id }})"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                           bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40">
                    <i class="fi fi-rr-cross-circle text-xs mr-1"></i>
                    Cancel
                </button>
                @endif
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

    <!-- Appointment Details Modal -->
    <div x-show="showModal" x-cloak @click="closeModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 dark:bg-black/80 p-8"
        style="display: none;">
        <div @click.stop
            class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-3xl w-2/5 max-h-[90vh] overflow-y-auto"
            x-show="showModal" x-transition>

            <!-- Close button -->
            <button type="button" @click="closeModal()"
                class="absolute top-4 right-4 sm:top-5 sm:right-5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-800 rounded-lg p-1 z-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Body -->
            <div class="p-12 sm:p-12" x-show="selectedAppointment">
                <!-- Header -->
                <div class="py-6">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white text-center mb-3">
                        Appointment Details
                    </h3>
                    <!-- Status Badge - Centered -->
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                        <span x-show="selectedAppointment && selectedAppointment.status === 'pending'"
                            class="px-3 py-1 text-xs rounded-full bg-[#FFA50020] text-[#FFA500] font-semibold">Pending</span>
                        <span x-show="selectedAppointment && selectedAppointment.status === 'confirmed'"
                            class="px-3 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">Confirmed</span>
                        <span x-show="selectedAppointment && selectedAppointment.status === 'completed'"
                            class="px-3 py-1 text-xs rounded-full bg-[#00BFFF20] text-[#00BFFF] font-semibold">Completed</span>
                        <span x-show="selectedAppointment && selectedAppointment.status === 'cancelled'"
                            class="px-3 py-1 text-xs rounded-full bg-[#FE1E2820] text-[#FE1E28] font-semibold">Cancelled</span>
                    </div>
                </div>

                <!-- Service Details Section -->
                <div class="mb-5">

                    <div class="py-3">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Service Details
                        </h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">View the details of the service
                            availed for this appointment</p>
                    </div>

                    <div class="space-y-4 text-sm py-2.5 px-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Service Type</span>
                            <span class="font-medium text-gray-900 dark:text-white text-right"
                                x-text="selectedAppointment?.service_type || '-'"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Service Date</span>
                            <span class="font-medium text-gray-900 dark:text-white text-right">
                                <span
                                    x-text="selectedAppointment ? formatDate(selectedAppointment.service_date) : '-'"></span>
                                <span
                                    x-show="selectedAppointment && (selectedAppointment.is_sunday || selectedAppointment.is_holiday)"
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
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">View the details of the units
                            included for this appointment</p>
                    </div>

                    <div class="space-y-1 py-3">
                        <template
                            x-if="selectedAppointment && selectedAppointment.unit_details && Array.isArray(selectedAppointment.unit_details) && selectedAppointment.unit_details.length > 0">
                            <div class="">
                                <template x-for="(unit, index) in selectedAppointment.unit_details"
                                    :key="index">
                                    <div
                                        class="flex justify-between items-center py-2 px-3 bg-transparent rounded-lg">
                                        <div class="flex items-center gap-1 flex-1 min-w-0">
                                            <span
                                                class="text-sm font-semibold text-gray-500 dark:text-gray-400 flex-shrink-0"
                                                x-text="'Unit ' + (index + 1)"></span>
                                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                                <span
                                                    class="font-medium text-gray-900 dark:text-white text-sm truncate"
                                                    x-text="unit.name || '-'"></span>
                                                <span
                                                    class="text-sm text-gray-500 dark:text-gray-400 flex-shrink-0">
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

                        <template
                            x-if="!selectedAppointment || !selectedAppointment.unit_details || !Array.isArray(selectedAppointment.unit_details) || selectedAppointment.unit_details.length === 0">
                            <div
                                class="flex justify-between items-center py-2.5 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <span
                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 flex-shrink-0">Unit
                                        1</span>
                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                        <span class="font-medium text-gray-900 dark:text-white text-sm truncate"
                                            x-text="selectedAppointment?.cabin_name || '-'"></span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
                                            Size: <span x-text="selectedAppointment?.unit_size || '-'"></span>
                                            m²
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
                            €<span
                                x-text="selectedAppointment ? parseFloat(selectedAppointment.total_amount).toFixed(2) : '0.00'"></span>
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col items-center justify-center w-full gap-3">
                    <button @click="closeModal()"
                        class="w-1/2 text-sm px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors font-medium my-3">
                        Cancel Appointment
                    </button>
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
</script>
@endpush
