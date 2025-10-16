@props([
    'records' => [],
    'showHeader' => true,
])

<div class="w-full overflow-x-auto">
    <!-- Table Header -->
    @if($showHeader)
    <div class="hidden md:grid grid-cols-6 gap-4 px-6 py-4 bg-gray-50 dark:bg-gray-800 
                border-b border-gray-200 dark:border-gray-700 rounded-lg">
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Status
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
            </svg>
        </div>
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Date
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
            </svg>
        </div>
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Time In
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
            </svg>
        </div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Time Out</div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Meal Break</div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Action</div>
    </div>
    @endif

    <!-- Table Body -->
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach($records as $index => $record)
        <div x-data="attendanceRow({{ $index }}, @js($record))" 
             class="grid grid-cols-1 md:grid-cols-6 gap-4 px-6 py-4 bg-white dark:bg-gray-900 
                    hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
             :class="{ 'border-2 border-blue-500': isHighlighted }">
            
            <!-- Status Badge -->
            <div class="flex items-center gap-2">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400">Status:</span>
                <template x-if="status === 'present'">
                    <x-badge 
                        label="Present" 
                        colorClass="bg-[#2FBC0020] text-[#2FBC00]" 
                        size="text-xs" />
                </template>
                <template x-if="status === 'late'">
                        <x-badge 
                            label="Late" 
                            colorClass="bg-[#FF7F0020] text-[#FF7F00]" 
                            size="text-xs" />
                </template>
                <template x-if="status === 'absent'">
                        <x-badge 
                            label="Absent" 
                            colorClass="bg-[#FE1E2820] text-[#FE1E28]" 
                            size="text-xs" />
                </template>
            </div>

            <!-- Date -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Date:</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="date"></span>
                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="dayOfWeek"></span>
            </div>

            <!-- Time In -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Time In:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="timeIn || '-'"></span>
                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="timeInNote"></span>
            </div>

            <!-- Time Out -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Time Out:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="timeOut || '-'"></span>
                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="timeOutNote"></span>
            </div>

            <!-- Meal Break -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Meal Break:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="mealBreak || '-'"></span>
                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="mealBreakDuration"></span>
            </div>

            <!-- Action Button -->
            <div class="flex items-start">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Action:</span>
                <button
                    @click="handleAction()"
                    :disabled="isTimedOut"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                    :class="isTimedOut 
                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600' 
                        : (timedIn 
                            ? 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' 
                            : 'bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600')"
                    x-text="buttonText">
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if(count($records) === 0)
    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
        <i class="fa-regular fa-calendar-xmark text-4xl mb-3"></i>
        <p>No attendance records found</p>
    </div>
    @endif
</div>
@push('scripts')
<script>
function attendanceRow(index, record) {
    return {
        status: record.status || 'absent',
        date: record.date || '',
        dayOfWeek: record.dayOfWeek || '',
        timeIn: record.timeIn || null,
        timeInNote: record.timeInNote || '',
        timeOut: record.timeOut || null,
        timeOutNote: record.timeOutNote || '',
        mealBreak: record.mealBreak || null,
        mealBreakDuration: record.mealBreakDuration || '',
        timedIn: record.timedIn || false,
        isTimedOut: record.isTimedOut || false,
        isHighlighted: false,
        
        get buttonText() {
            if (this.isTimedOut) return 'Timed Out';
            return this.timedIn ? 'Time Out' : 'Time In';
        },
        
        handleAction() {
            if (this.isTimedOut) return;
            
            const now = new Date();
            const currentTime = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: true 
            });
            
            if (!this.timedIn) {
                // Time In action
                this.timeIn = currentTime;
                this.timedIn = true;
                this.status = 'present';
                this.isHighlighted = true;
                
                // Calculate if early or late (example: expected time is 9:00 AM)
                const hour = now.getHours();
                if (hour < 9) {
                    this.timeInNote = Math.abs(9 - hour) + ' h early';
                } else if (hour > 9) {
                    this.timeInNote = (hour - 9) + ' h late';
                    this.status = 'late';
                } else {
                    this.timeInNote = 'On time';
                }
                
                // Emit event for parent component
                this.$dispatch('time-in', { index, time: currentTime });
                
                // Remove highlight after animation
                setTimeout(() => {
                    this.isHighlighted = false;
                }, 2000);
            } else {
                // Time Out action
                this.timeOut = currentTime;
                this.isTimedOut = true;
                this.timeOutNote = '';
                
                // Emit event for parent component
                this.$dispatch('time-out', { index, time: currentTime });
            }
        }
    }
}
</script>
@endpush
@stack('scripts')