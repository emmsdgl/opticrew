@props([
    'showVar' => 'showDrawer',
    'dataVar' => 'selectedAppointment',
    'closeMethod' => 'closeDrawer',
    'title' => 'Appointment Details',
    'showChecklist' => true,
    'showTeam' => true,
    'showFooter' => true,
])

<!-- Appointment Details Slide-in Drawer -->
<div x-show="{{ $showVar }}" x-cloak class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
    <!-- Backdrop -->
    <div x-show="{{ $showVar }}"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="{{ $closeMethod }}()"
         class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

    <!-- Drawer Panel -->
    <div class="fixed inset-y-0 right-0 flex max-w-full">
        <div x-show="{{ $showVar }}"
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
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
                    <button type="button" @click="{{ $closeMethod }}()"
                        class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Drawer Body (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-6" x-show="{{ $dataVar }}">
                    <!-- Status Badge -->
                    <div class="flex items-center gap-2 mb-6">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                        <span x-show="getDrawerStatus() === 'pending'"
                            class="px-3 py-1 text-xs rounded-full bg-[#FFA50020] text-[#FFA500] font-semibold">Pending</span>
                        <span x-show="getDrawerStatus() === 'approved'"
                            class="px-3 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">Approved</span>
                        <span x-show="getDrawerStatus() === 'confirmed'"
                            class="px-3 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">Confirmed</span>
                        <span x-show="getDrawerStatus() === 'in progress' || getDrawerStatus() === 'in_progress'"
                            class="px-3 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">In Progress</span>
                        <span x-show="getDrawerStatus() === 'completed'"
                            class="px-3 py-1 text-xs rounded-full bg-[#00BFFF20] text-[#00BFFF] font-semibold">Completed</span>
                        <span x-show="getDrawerStatus() === 'cancelled'"
                            class="px-3 py-1 text-xs rounded-full bg-[#FE1E2820] text-[#FE1E28] font-semibold">Cancelled</span>
                        <span x-show="getDrawerStatus() === 'rejected'"
                            class="px-3 py-1 text-xs rounded-full bg-[#FE1E2820] text-[#FE1E28] font-semibold">Rejected</span>
                    </div>

                    <!-- Service Details Section -->
                    <div class="mb-5">
                        <div class="py-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Service Details</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">View the details of the service availed for this appointment</p>
                        </div>

                        <div class="space-y-4 text-sm py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                            <div class="flex justify-between items-center" x-show="getDrawerData('appointmentId') || getDrawerData('id')">
                                <span class="text-gray-500 dark:text-gray-400">Appointment ID</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                    x-text="getDrawerData('appointmentId') || getDrawerData('id') || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Service Type</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                    x-text="getDrawerData('serviceType') || getDrawerData('service_type') || '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Service Date</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right">
                                    <span x-text="formatDrawerDate(getDrawerData('serviceDate') || getDrawerData('service_date'))"></span>
                                    <span x-show="getDrawerData('is_sunday') || getDrawerData('is_holiday')"
                                        class="ml-1 text-xs text-orange-600 dark:text-orange-400 font-semibold">(2x)</span>
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Service Time</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                    x-text="formatDrawerTime(getDrawerData('serviceTime') || getDrawerData('service_time'))"></span>
                            </div>
                            <div class="flex justify-between items-center" x-show="getDrawerData('location') || getDrawerData('cabin_name')">
                                <span class="text-gray-500 dark:text-gray-400">Service Location</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right"
                                    x-text="getDrawerData('location') || getDrawerData('cabin_name') || '-'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Unit Details Section -->
                    <div class="mb-5" x-show="getDrawerData('unit_details') || getDrawerData('cabin_name')">
                        <div class="py-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Unit Details</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">View the details of the units included for this appointment</p>
                        </div>

                        <div class="space-y-1 py-3">
                            <template x-if="getDrawerData('unit_details') && Array.isArray(getDrawerData('unit_details')) && getDrawerData('unit_details').length > 0">
                                <div class="">
                                    <template x-for="(unit, index) in getDrawerData('unit_details')" :key="index">
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

                            <template x-if="!getDrawerData('unit_details') || !Array.isArray(getDrawerData('unit_details')) || getDrawerData('unit_details').length === 0">
                                <div class="flex justify-between items-center py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 flex-shrink-0">Unit 1</span>
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            <span class="font-medium text-gray-900 dark:text-white text-sm truncate"
                                                x-text="getDrawerData('cabin_name') || '-'"></span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0" x-show="getDrawerData('unit_size')">
                                                Size: <span x-text="getDrawerData('unit_size') || '-'"></span> m²
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Special Requests Section -->
                    <div class="mb-5" x-show="getDrawerData('special_requests')">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Special Requests</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-lg"
                            x-text="getDrawerData('special_requests') || '-'"></p>
                    </div>

                    @if($showTeam)
                    <!-- Assigned Team Section -->
                    <div class="mb-5" x-show="getDrawerData('assignedMembers')?.length > 0">
                        <div class="py-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Assigned Team</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Team members assigned to this service</p>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                            <template x-for="(member, idx) in (getDrawerData('assignedMembers') || []).slice(0, 5)" :key="idx">
                                <div class="relative group">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold cursor-pointer transition-transform hover:scale-110"
                                        x-text="member.initial"></div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-10"
                                        x-text="member.name"></div>
                                </div>
                            </template>
                            <template x-if="(getDrawerData('assignedMembers')?.length || 0) > 5">
                                <button class="w-10 h-10 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-full flex items-center justify-center text-gray-400 text-sm"
                                    x-text="'+' + (getDrawerData('assignedMembers').length - 5)"></button>
                            </template>
                        </div>
                    </div>
                    @endif

                    @if($showChecklist)
                    <!-- Service Checklist Section -->
                    <div class="mb-5">
                        <div class="py-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Service Progress</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                <template x-if="getDrawerStatus() === 'completed'">
                                    <span>All tasks completed by our team</span>
                                </template>
                                <template x-if="getDrawerStatus() === 'confirmed' || getDrawerStatus() === 'approved' || getDrawerStatus() === 'in progress' || getDrawerStatus() === 'in_progress'">
                                    <span>Tasks being performed for this service</span>
                                </template>
                                <template x-if="getDrawerStatus() === 'pending'">
                                    <span>Tasks that will be performed once approved</span>
                                </template>
                                <template x-if="getDrawerStatus() === 'cancelled' || getDrawerStatus() === 'rejected'">
                                    <span>This appointment was cancelled</span>
                                </template>
                            </p>
                        </div>

                        <!-- Progress indicator for completed appointments -->
                        <template x-if="getDrawerStatus() === 'completed'">
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

                        <!-- Progress indicator for in-progress appointments with actual progress -->
                        <template x-if="(getDrawerStatus() === 'confirmed' || getDrawerStatus() === 'approved' || getDrawerStatus() === 'in progress' || getDrawerStatus() === 'in_progress') && getDrawerChecklistProgress().completed > 0">
                            <div class="mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin text-blue-600 dark:text-blue-400"></i>
                                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                                        Service in progress
                                    </span>
                                </div>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 ml-6">
                                    <span x-text="getDrawerChecklistProgress().completed"></span> of <span x-text="getDrawerChecklistProgress().total"></span> tasks completed
                                </p>
                            </div>
                        </template>

                        <!-- Checklist Items with actual completion status -->
                        <div class="space-y-2 max-h-48 overflow-y-auto bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                            <template x-for="(item, index) in getDrawerChecklistItems()" :key="index">
                                <div class="flex items-center gap-2 py-1.5">
                                    <!-- Single icon based on status - mutually exclusive conditions -->
                                    <i class="text-xs"
                                        :class="{
                                            'fa-solid fa-check-circle text-green-500': getDrawerStatus() === 'completed' || (getDrawerStatus() !== 'cancelled' && getDrawerStatus() !== 'rejected' && getDrawerStatus() !== 'pending' && isChecklistItemCompleted(index)),
                                            'fa-regular fa-circle text-blue-400': getDrawerStatus() !== 'completed' && getDrawerStatus() !== 'cancelled' && getDrawerStatus() !== 'rejected' && getDrawerStatus() !== 'pending' && !isChecklistItemCompleted(index),
                                            'fa-regular fa-circle text-gray-400': getDrawerStatus() === 'pending',
                                            'fa-solid fa-times-circle text-gray-400': getDrawerStatus() === 'cancelled' || getDrawerStatus() === 'rejected'
                                        }"></i>
                                    <span class="text-sm"
                                        :class="{
                                            'text-green-700 dark:text-green-300 font-medium': getDrawerStatus() === 'completed' || (getDrawerStatus() !== 'cancelled' && getDrawerStatus() !== 'rejected' && isChecklistItemCompleted(index)),
                                            'text-gray-700 dark:text-gray-300': !isChecklistItemCompleted(index) && getDrawerStatus() !== 'cancelled' && getDrawerStatus() !== 'rejected' && getDrawerStatus() !== 'completed',
                                            'text-gray-400 dark:text-gray-500 line-through': getDrawerStatus() === 'cancelled' || getDrawerStatus() === 'rejected'
                                        }"
                                        x-text="item"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Dynamic progress bar showing actual completion -->
                        <template x-if="getDrawerStatus() !== 'pending' && getDrawerStatus() !== 'cancelled' && getDrawerStatus() !== 'rejected'">
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Progress</span>
                                    <span class="text-xs font-semibold"
                                        :class="{
                                            'text-green-600 dark:text-green-400': getDrawerChecklistProgress().percentage === 100,
                                            'text-blue-600 dark:text-blue-400': getDrawerChecklistProgress().percentage > 0 && getDrawerChecklistProgress().percentage < 100,
                                            'text-gray-600 dark:text-gray-400': getDrawerChecklistProgress().percentage === 0
                                        }">
                                        <span x-text="getDrawerChecklistProgress().percentage"></span>% Complete
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full transition-all duration-300"
                                        :class="{
                                            'bg-green-600': getDrawerChecklistProgress().percentage === 100,
                                            'bg-blue-600': getDrawerChecklistProgress().percentage > 0 && getDrawerChecklistProgress().percentage < 100,
                                            'bg-gray-400': getDrawerChecklistProgress().percentage === 0
                                        }"
                                        :style="'width: ' + getDrawerChecklistProgress().percentage + '%'"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                    @endif

                    <!-- Total Amount -->
                    <div class="my-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-2" x-show="getDrawerData('totalAmount') || getDrawerData('total_amount')">
                            <div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Amount</div>
                            </div>
                            <span class="text-base font-bold text-gray-900 dark:text-white">
                                <template x-if="getDrawerData('totalAmount')">
                                    <span x-text="getDrawerData('totalAmount')"></span>
                                </template>
                                <template x-if="!getDrawerData('totalAmount') && getDrawerData('total_amount')">
                                    <span>€<span x-text="parseFloat(getDrawerData('total_amount')).toFixed(2)"></span></span>
                                </template>
                            </span>
                        </div>
                        <div class="flex justify-between items-center" x-show="getDrawerData('payableAmount') || getDrawerData('total_amount')">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Payable Amount</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">VAT Inclusive</div>
                            </div>
                            <span class="text-base font-bold text-blue-600 dark:text-blue-400">
                                <template x-if="getDrawerData('payableAmount')">
                                    <span x-text="getDrawerData('payableAmount')"></span>
                                </template>
                                <template x-if="!getDrawerData('payableAmount') && getDrawerData('total_amount')">
                                    <span>€<span x-text="parseFloat(getDrawerData('total_amount')).toFixed(2)"></span></span>
                                </template>
                            </span>
                        </div>
                    </div>
                </div>

                @if($showFooter)
                <!-- Drawer Footer (Sticky) -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                    {{ $footer ?? '' }}
                    @if(!isset($footer) || empty(trim($footer ?? '')))
                    <div class="flex gap-3">
                        <button
                            @click="{{ $closeMethod }}()"
                            class="flex-1 text-sm px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium">
                            Close
                        </button>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@pushOnce('scripts')
<script>
// Shared drawer helper functions - define globally
window.drawerChecklistTemplates = {
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
    final_cleaning: [
        'Deep clean all rooms',
        'Clean windows inside and out',
        'Clean all kitchen appliances',
        'Degrease oven and stovetop',
        'Clean inside cabinets',
        'Scrub bathroom tiles and grout',
        'Clean behind furniture',
        'Dust and clean light fixtures',
        'Clean baseboards',
        'Vacuum and mop all floors',
        'Clean door frames and handles',
        'Remove all trash',
    ],
};

window.getChecklistByServiceType = function(serviceType) {
    if (!serviceType) return window.drawerChecklistTemplates.general_cleaning;

    const type = serviceType.toLowerCase();

    if (type.includes('daily') || type.includes('routine')) {
        return window.drawerChecklistTemplates.daily_cleaning;
    } else if (type.includes('snowout') || type.includes('weather')) {
        return window.drawerChecklistTemplates.snowout_cleaning;
    } else if (type.includes('deep')) {
        return window.drawerChecklistTemplates.deep_cleaning;
    } else if (type.includes('hotel') || type.includes('room turnover')) {
        return window.drawerChecklistTemplates.hotel_cleaning;
    } else if (type.includes('final')) {
        return window.drawerChecklistTemplates.final_cleaning;
    }

    return window.drawerChecklistTemplates.general_cleaning;
};

// Helper function to get checklist completions from appointment data
window.getChecklistCompletions = function(appointmentData) {
    if (!appointmentData) return [];
    return appointmentData.checklist_completions || appointmentData.checklistCompletions || [];
};

// Helper function to check if a checklist item is completed
window.isItemCompleted = function(appointmentData, itemIndex) {
    const completions = window.getChecklistCompletions(appointmentData);
    // checklist_item_id stores the item index (0-based or 1-based depending on implementation)
    return completions.includes(itemIndex) || completions.includes(itemIndex + 1);
};

// Helper function to calculate checklist progress
window.calculateChecklistProgress = function(appointmentData, totalItems) {
    const completions = window.getChecklistCompletions(appointmentData);
    const completed = completions.length;
    const total = totalItems || 1;
    const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;

    return {
        completed: completed,
        total: total,
        percentage: percentage
    };
};
</script>
@endPushOnce
