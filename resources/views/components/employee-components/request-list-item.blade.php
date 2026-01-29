@props([
    'type' => 'Change Shift',
    'date' => '25 December 2025',
    'fromTime' => '8:50 am',
    'toTime' => '9:00 pm',
    'onDate' => null, // For leave requests
    'leaveType' => null, // For leave type (e.g., Emergency Leave, Sick Leave)
    'status' => 'Pending',
    'reason' => 'Personal matters', // Add reason prop
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

<div x-data="{ showModal: false }">
    {{-- Request Card --}}
    <div class="bg-white dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
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
            <button @click="showModal = true; document.body.style.overflow = 'hidden';" 
                    class="inline-flex items-center gap-1 text-sm font-normal text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                View Details
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Modal --}}
    <div x-show="showModal" 
         x-cloak 
         @click="showModal = false; document.body.style.overflow = 'auto';"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/80 p-4"
         style="display: none;">
        <div @click.stop
             class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto"
             x-show="showModal" 
             x-transition>

            {{-- Close Button (Back Arrow) --}}
            <button type="button" 
                    @click="showModal = false; document.body.style.overflow = 'auto';"
                    class="absolute top-12 left-6 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 focus:outline-none z-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            {{-- Modal Content --}}
            <div class="px-6 py-8 sm:px-8">
                {{-- Header --}}
                <div class="text-center mb-8 mt-4">
                    <h3 class="text-md font-bold text-gray-900 dark:text-white mb-2">
                        Request Details
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        View the details of your request
                    </p>
                </div>

                {{-- Request Information --}}
                <div class="space-y-0 mb-3">
                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Request Type</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $type }}</span>
                    </div>

                    @if($leaveType)
                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Leave Type</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $leaveType }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Request Status</span>
                        <span class="text-sm font-semibold {{ $statusColor }}">{{ $status }}</span>
                    </div>

                    @if($type === 'Change Shift' || $type === 'Shift Change')
                        <div class="flex justify-between items-center py-4 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">From Time</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $fromTime }}</span>
                        </div>

                        <div class="flex justify-between items-center py-4 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">To Time</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $toTime }}</span>
                        </div>
                    @else
                        <div class="flex justify-between items-center py-4 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Date of Request</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $fromTime }}</span>
                        </div>
                    @endif

                    <div class="flex flex-row justify-between py-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400 block mb-3">Reason for Request</span>
                        <p class="text-sm text-gray-900 dark:text-white leading-relaxed">{{ $reason }}</p>
                    </div>
                </div>

                {{-- Cancel Button --}}
                <div class="mt-8">
                    <button @click="showModal = false; document.body.style.overflow = 'auto';"
                            class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-full transition-colors duration-200 shadow-lg">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>