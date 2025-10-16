<div class="w-full flex flex-col p-4 rounded-lg bg-white dark:bg-gray-800">
    <div class="flex items-center justify-between mb-2">
        <p class="text-sm font-sans font-bold w-full text-left dark:text-gray-200">
            Attendance Chart
        </p>
        {{-- Manual refresh button with loading indicator --}}
        <button wire:click="loadAttendanceData" class="text-xs text-blue-500 hover:text-blue-700 transition">
            <i class="fa-solid fa-rotate-right" wire:loading.class="fa-spin" wire:target="loadAttendanceData"></i>
        </button>
    </div>
    
    <x-attendancechart 
        :totalEmployees="$totalEmployees" 
        :presentEmployees="$presentEmployees"
        :absentEmployees="$absentEmployees" 
        :attendanceRate="$attendanceRate" 
    />
    
    {{-- Subtle loading indicator (non-blocking) --}}
    <div class="text-xs text-right mt-2">
        <span wire:loading wire:target="loadAttendanceData" class="text-blue-500">
            <i class="fa-solid fa-circle-notch fa-spin"></i> Updating...
        </span>
        <span wire:loading.remove wire:target="loadAttendanceData" class="text-gray-400">
            Last updated: {{ now()->format('h:i:s A') }}
        </span>
    </div>
    
    {{-- Hidden polling trigger - updates every 10 seconds without blocking --}}
    <div wire:poll.10s="loadAttendanceData" class="hidden"></div>
</div>