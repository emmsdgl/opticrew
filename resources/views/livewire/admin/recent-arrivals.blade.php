<div wire:poll.5s class="w-full flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-gray-800">
    <x-labelwithvalue label="Recent Arrivals" :count="'(' . $recentArrivals->count() . ')'" />
    
    <div class="overflow-y-auto h-56 pr-2">
        <div class="flex flex-col gap-3 w-full">
            @forelse ($recentArrivals as $arrival)
                @if($arrival->employee)
                    <x-attendanceparticulars 
                        :empName="$arrival->employee->full_name"
                        :empNum="$arrival->employee->user->email ?? 'N/A'"
                        :attendanceStatus="'Timed In at'"
                        :attendanceDuration="\Carbon\Carbon::parse($arrival->clock_in)->format('h:i A')" 
                    />
                @endif
            @empty
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray-500 dark:text-gray-400">No arrivals yet for today.</p>
                </div>
            @endforelse
        </div>
    </div>
    
    {{-- Optional: Show last updated time --}}
    <div class="text-xs text-gray-400 text-right">
        Last updated: {{ now()->format('h:i:s A') }}
    </div>
</div>