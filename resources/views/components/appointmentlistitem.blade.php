@props([
    'appointments' => [],       // Array of appointments
    'editable' => false,        // Enable inline editing
    'showProgress' => true,     // Show progress bar
    'showDuration' => true,     // Show estimated duration
    'onItemClick' => '',        // JS function name for click handler
])

<div x-data="appointmentList()" class="w-full space-y-5">
    <template x-for="(appointment, index) in appointments" :key="appointment.id">
        <div
            @click="handleClick(appointment)"
            class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border border-gray-200 
                   dark:border-gray-700 rounded-lg hover:shadow-md transition-shadow cursor-pointer group">
            
            <!-- Left Section: Status Badge + Title + Duration -->
            <div class="flex items-center gap-4 flex-1 min-w-0">
                
                <!-- Status Badge -->
                <div class="flex-shrink-0">
                    <template x-if="appointment.status === 'in_progress'">
                        <x-badge 
                            label="In Progress" 
                            colorClass="bg-[#FF7F0020] text-[#FF7F00]" 
                            size="text-xs" />
                    </template>
                    <template x-if="appointment.status === 'complete'">
                        <x-badge 
                            label="Completed" 
                            colorClass="bg-[#2FBC0020] text-[#2FBC00]" 
                            size="text-xs" />
                    </template>
                    <template x-if="appointment.status === 'incomplete'">
                        <x-badge 
                            label="Incomplete" 
                            colorClass="bg-[#FE1E2820] text-[#FE1E28]" 
                            size="text-xs" />
                    </template>
                </div>

                <!-- Title and Location -->
                <div class="flex-1 min-w-0">
                    <template x-if="{{ $editable ? 'true' : 'false' }}">
                        <input 
                            type="text"
                            x-model="appointment.title"
                            class="w-full bg-transparent border-b border-gray-300 focus:border-blue-500 
                                   focus:outline-none text-gray-900 dark:text-gray-100 font-medium"
                            @click.stop>
                    </template>
                    
                    <template x-if="{{ $editable ? 'false' : 'true' }}">
                        <h3 class="text-gray-900 dark:text-gray-100 font-semibold text-base"
                            x-text="appointment.title"></h3>
                    </template>
                    
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5" 
                       x-text="appointment.location"></p>
                </div>

                <!-- Estimated Duration -->
                @if ($showDuration)
                <div class="flex-shrink-0 text-left" x-show="appointment.duration">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                        Estimated Duration
                    </p>
                    <p class="text-sm text-gray-900 dark:text-gray-100 font-semibold mt-0.5"
                       x-text="appointment.duration"></p>
                </div>
                @endif
            </div>

            <!-- Right Section: Progress + Date/Time -->
            <div class="flex items-center gap-6 flex-shrink-0 ml-6">
                
                <!-- Progress Bar -->
                @if ($showProgress)
                <div class="flex items-center gap-3" x-show="appointment.progress !== undefined">
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 min-w-[3rem]"
                          x-text="appointment.progress + '%'"></span>
                    <div class="w-32 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div 
                            class="h-full bg-blue-500 rounded-full transition-all duration-300"
                            :style="`width: ${appointment.progress}%`">
                        </div>
                    </div>
                </div>
                @endif

                <!-- Date and Time -->
                <div class="text-right">
                    <p class="text-sm text-gray-900 dark:text-gray-100 font-medium"
                       x-text="formatDate(appointment.date)"></p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"
                       x-text="appointment.time"></p>
                </div>

                <!-- Custom Slot -->
                <div class="flex items-center gap-2">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>

    <!-- Empty State -->
    <div x-show="appointments.length === 0" 
         class="text-center py-12 text-gray-500 dark:text-gray-400">
        <i class="fa-regular fa-calendar-xmark text-4xl mb-3"></i>
        <p>No appointments scheduled</p>
    </div>
</div>

<script>
function appointmentList() {
    return {
        appointments: @js($appointments),
        
        formatStatus(status) {
            const statusMap = {
                'in_progress': 'In Progress',
                'complete': 'Complete',
                'pending': 'Pending',
                'scheduled': 'Scheduled'
            };
            return statusMap[status] || status;
        },
        
        formatDate(date) {
            if (!date) return '';
            const d = new Date(date);
            return d.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric' 
            });
        },
        
        handleClick(appointment) {
            @if($onItemClick)
                if (typeof window['{{ $onItemClick }}'] === 'function') {
                    window['{{ $onItemClick }}'](appointment);
                }
            @endif
            
            // Emit Alpine event
            this.$dispatch('appointment-clicked', appointment);
        }
    }
}
</script>