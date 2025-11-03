<x-layouts.general-client :title="'Client Dashboard'">
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
                    <div class="flex flex-row gap-3">
                        <x-dropdown label="Filter by:" default="All" :options="['All', 'Active', 'Inactive', 'Pending']"
                            id="status-filter" />
                        <x-dropdown label="Sort by:" default="Latest" :options="[
                            'latest' => 'Latest',
                            'oldest' => 'Oldest',
                            'name_asc' => 'Name (A-Z)',
                            'name_desc' => 'Name (Z-A)'
                        ]" />
                    </div>
                </div>
                <!-- Remove overflow-y-auto from here and add a wrapper inside the component -->
                <div class="h-20 overflow-y-auto">
                    <x-client-components.appointment-page.appointment-list-item :items="$appointments->map(function ($appointment) {
        return [
            'id' => $appointment->id,
            'service' => $appointment->service_type ?? 'N/A',
            'status' => ucfirst($appointment->status),
            'service_date' => $appointment->service_date ? \Carbon\Carbon::parse($appointment->service_date)->format('F j, Y') : 'N/A',
            'service_time' => $appointment->service_time ? \Carbon\Carbon::parse($appointment->service_time)->format('g:i A') : 'N/A',
            'action_label' => 'View Details',
            'action_onclick' => 'viewDetails(' . $appointment->id . ')',
            'menu_items' => [
                ['label' => 'Reschedule', 'action' => 'reschedAppointment(' . $appointment->id . ')'],
                ['label' => 'Cancel Appointment', 'action' => 'cancelAppointment(' . $appointment->id . ')'],
            ]
        ];
    })->toArray()" :maxHeight="'30rem'" />
                </div>
            </div>
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

        <!-- Right Panel - Attendance Overview -->
        <div
            class="flex flex-col gap-6 w-full lg:w-1/3 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-auto p-6 mt-3">
            <!-- Inner Up - Recommendation Service List -->
            <x-labelwithvalue label="Recommended Services For You" count="(2)" />
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