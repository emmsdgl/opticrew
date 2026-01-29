@props([
    'type' => 'Change Shift',
    'date' => '25 December 2025',
    'fromTime' => '8:50 am',
    'toTime' => '9:00 pm',
    'onDate' => null, // For leave requests
    'status' => 'Pending',
    'detailsUrl' => '#'
])

@php
    // Determine status color
    $statusColors = [
        'Approved' => 'text-green-600 dark:text-green-400',
        'Pending' => 'text-orange-600 dark:text-orange-400',
        'Rejected' => 'text-red-600 dark:text-red-400',
        'Cancelled' => 'text-gray-600 dark:text-gray-400'
    ];
    
    $statusColor = $statusColors[$status] ?? 'text-gray-600 dark:text-gray-400';
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
    {{-- Header: Type and Date --}}
    <div class="flex justify-between items-start mb-3">
        <h3 class="text-base font-bold text-gray-900 dark:text-white">
            {{ $type }}
        </h3>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $date }}
        </span>
    </div>

    {{-- Details Section --}}
    <div class="mb-3">
        @if($type === 'Change Shift' || $type === 'Shift Change')
            {{-- Shift Change Details --}}
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold text-gray-700 dark:text-gray-300">From</span> 
                {{ $fromTime }}, 
                <span class="font-semibold text-gray-700 dark:text-gray-300">To</span> 
                {{ $toTime }}
            </p>
        @else
            {{-- Leave Request Details --}}
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold text-gray-700 dark:text-gray-300">On</span> 
                {{ $onDate ?? $fromTime }}
            </p>
        @endif
    </div>

    {{-- Footer: View Details Link and Status --}}
    <div class="flex justify-between items-center">
        <a href="{{ $detailsUrl }}" 
           class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
            View Details
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        
        <span class="text-sm font-bold {{ $statusColor }}">
            {{ $status }}
        </span>
    </div>
</div>