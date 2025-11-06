@props([
    'records' => [],
    'showHeader' => true,
    'isClockedIn' => false,
    'hasAttendanceToday' => false,
])

<div class="w-full overflow-x-auto" x-data="{
    showAttendanceModal: false,
    selectedRecord: null,

    openAttendanceModal(record) {
        this.selectedRecord = record;
        this.showAttendanceModal = true;
        document.body.style.overflow = 'hidden';
    },

    closeAttendanceModal() {
        this.showAttendanceModal = false;
        document.body.style.overflow = 'auto';
    }
}">
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
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Hours Worked</div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Action</div>
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

            <!-- Hours Worked -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Hours Worked:</span>
                <span class="text-sm font-bold text-blue-600 dark:text-blue-400" x-text="hoursWorked || '-'"></span>
            </div>

            <!-- Action Button -->
            <div class="flex items-center justify-center">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 mr-2">Action:</span>
                <button
                    @click="$dispatch('open-modal', { date: date, timedIn: timedIn, isTimedOut: isTimedOut })"
                    :disabled="isTimedOut"
                    class="w-full px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
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

    <!-- Attendance Details Modal -->
    <div x-show="showAttendanceModal" x-cloak @click="closeAttendanceModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 dark:bg-black/80 p-4 sm:p-8"
        style="display: none;"
        @open-modal.window="openAttendanceModal($event.detail)">
        <div @click.stop
            class="relative bg-white w-1/3 dark:bg-slate-800 rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-700"
            x-show="showAttendanceModal" x-transition>

            <!-- Close button -->
            <button type="button" @click="closeAttendanceModal()"
                class="absolute top-4 right-4 sm:top-5 sm:right-5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-800 rounded-lg p-1 z-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Content -->
            <div class="py-6 sm:p-8">
                <!-- Header -->
                <div class="my-6 text-center w-full">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Attendance Details</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <template x-if="selectedRecord">
                            <span>Manage your attendance for <span class="font-semibold" x-text="selectedRecord?.date"></span></span>
                        </template>
                    </p>
                </div>

                <!-- Attendance Info Card -->
                <template x-if="selectedRecord">
                    <div class="rounded-xl mb-6">

                        <!-- Status Badge - Centered -->
                        <div class="flex items-center justify-center gap-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</span>
                            <template x-if="selectedRecord.timedIn">
                                <span class="px-3 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">Clocked In</span>
                            </template>
                            <template x-if="!selectedRecord.timedIn && !selectedRecord.isTimedOut">
                                <span class="px-3 py-1 text-xs rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold">Not Clocked In</span>
                            </template>
                            <template x-if="selectedRecord.isTimedOut">
                                <span class="px-3 py-1 text-xs rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold">Clocked Out</span>
                            </template>
                        </div>

                        <!-- Attendance Details Grid -->
                        <div class="space-y-3 mt-6 border-t border-gray-200 dark:border-slate-600 pt-6 p-8">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Date</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right" x-text="selectedRecord?.date"></span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Clock In Time</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right">
                                    <template x-if="selectedRecord.timedIn">
                                        <span x-text="selectedRecord?.timeIn || 'N/A'"></span>
                                    </template>
                                    <template x-if="!selectedRecord.timedIn">
                                        <span class="text-gray-400 dark:text-gray-500">Not clocked in</span>
                                    </template>
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Clock Out Time</span>
                                <span class="font-medium text-gray-900 dark:text-white text-right">
                                    <template x-if="selectedRecord.timedIn && !selectedRecord.isTimedOut">
                                        <span class="text-blue-500 dark:text-blue-400">Still working...</span>
                                    </template>
                                    <template x-if="selectedRecord.isTimedOut">
                                        <span x-text="selectedRecord?.timeOut || 'N/A'"></span>
                                    </template>
                                    <template x-if="!selectedRecord.timedIn">
                                        <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                    </template>
                                </span>
                            </div>

                            <template x-if="selectedRecord.isTimedOut && selectedRecord.hoursWorked">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 dark:text-gray-400">Total Hours</span>
                                    <span class="font-medium text-gray-900 dark:text-white text-right" x-text="selectedRecord?.hoursWorked"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Action Buttons -->
                <div class="mt-3 flex flex-col sm:flex-row gap-3">
                    <template x-if="selectedRecord && !selectedRecord.timedIn">
                        <form action="{{ route('employee.attendance.clockin') }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit"
                                class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-full transition-colors duration-200 flex items-center justify-center gap-2">
                                <i class="fi fi-rr-play text-sm"></i>
                                Clock In
                            </button>
                        </form>
                    </template>
                    <template x-if="selectedRecord && selectedRecord.timedIn && !selectedRecord.isTimedOut">
                        <form action="{{ route('employee.attendance.clockout') }}" method="POST" class="flex-1 flex justify-center">
                            @csrf
                            <button type="submit"
                                class="w-1/2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                <i class="fi fi-rr-stop text-sm"></i>
                                Clock Out
                            </button>
                        </form>
                    </template>

                </div>
            </div>
        </div>
    </div>
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
        hoursWorked: record.hoursWorked || null,
        timedIn: record.timedIn || false,
        isTimedOut: record.isTimedOut || false,
        isHighlighted: false,
        
        get buttonText() {
            if (this.isTimedOut) return 'Timed Out';
            return this.timedIn ? 'View Details' : 'Time In';
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