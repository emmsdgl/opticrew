@props([
    'appointments' => [],
    'showHeader' => true,
])

<div class="w-full min-w-full overflow-visible" x-data="{
    showDrawer: false,
    selectedAppointment: null,
    allAppointments: {{ $appointments->toJson() }},

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

    // Helper methods required by the shared drawer component
    getDrawerStatus() {
        return (this.selectedAppointment?.status || '').toLowerCase();
    },

    getDrawerData(key) {
        if (key === 'assignedMembers') {
            return this.selectedAppointment?.assigned_members || [];
        }
        return this.selectedAppointment?.[key];
    },

    getDrawerChecklistItems() {
        const serviceType = this.selectedAppointment?.service_type;
        return window.getChecklistByServiceType ? window.getChecklistByServiceType(serviceType) : [];
    },

    formatDrawerDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
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

    <!-- Appointment Details Slide-in Drawer (Reusable Component) -->
    <x-client-components.shared.appointment-details-drawer
        showVar="showDrawer"
        dataVar="selectedAppointment"
        closeMethod="closeDrawer"
        title="Appointment Details"
        :showTeam="true"
        :showChecklist="true">
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