<div wire:poll.5s class="w-full flex flex-col gap-3">

    @if($recentArrivals->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <i class="fa-regular fa-clock text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500">No arrivals yet for today</p>
            <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-1">Employee clock-ins will appear here</p>
        </div>
    @else
        <x-applicant-components.stack-list maxHeight="max-h-64">
            @foreach ($recentArrivals as $index => $arrival)
                @if($arrival->employee)
                    <x-applicant-components.stack-item
                        :colorIndex="$index"
                        :initials="strtoupper(substr($arrival->employee->full_name, 0, 1))"
                        :subtitle="\Carbon\Carbon::parse($arrival->clock_in)->format('h:i A')"
                        :title="$arrival->employee->full_name"
                        :detail="$arrival->employee->user->email ?? 'N/A'"
                        badge="Timed In"
                        badgeClass="bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300"
                    />
                @endif
            @endforeach
        </x-applicant-components.stack-list>
    @endif

    {{-- Last updated --}}
    <div class="text-[10px] text-gray-400 dark:text-gray-600 text-right">
        Last updated: {{ now()->format('h:i:s A') }}
    </div>
</div>
