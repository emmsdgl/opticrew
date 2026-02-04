@props([
    'type' => 'Change Shift',
    'date' => '25 December 2025',
    'fromTime' => '8:50 am',
    'toTime' => '9:00 pm',
    'onDate' => null, // For leave requests
    'leaveType' => null, // For leave type (e.g., Emergency Leave, Sick Leave)
    'status' => 'Pending',
    'reason' => 'Personal matters',
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

{{-- Request Card - No internal modal, parent handles the modal --}}
<div class="bg-white dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200 cursor-pointer">
    {{-- Header: Type and Date --}}
    <div class="flex justify-between items-start mb-3">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
            {{ $type }}
        </h3>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $date }}
        </span>
    </div>

    {{-- Details Section --}}
    <div class="mb-3 flex justify-between gap-12">
        @if($type === 'Change Shift' || $type === 'Shift Change')
            {{-- Shift Change Details --}}
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-normal text-gray-700 dark:text-gray-300">From</span>
                {{ $fromTime }},
                <span class="font-normal text-gray-700 dark:text-gray-300">To</span>
                {{ $toTime }}
            </p>
        @else
            {{-- Leave Request Details --}}
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-normal text-gray-700 dark:text-gray-300">On</span>
                {{ $onDate ?? $fromTime }}
            </p>
        @endif

        <span class="text-sm font-medium {{ $statusColor }}">
            {{ $status }}
        </span>
    </div>

    <div class="flex justify-between items-center">
        <span class="inline-flex items-center gap-1 text-sm font-normal text-blue-600 dark:text-blue-400">
            View Details
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </span>
    </div>
</div>
