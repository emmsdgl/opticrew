@props([
    'title' => 'Course Title',
    'progress' => 0,
    'buttonUrl' => '#',
    'duration' => '',
])

@php
    $progressBarColor = match(true) {
        $progress >= 100 => 'bg-green-500',
        $progress >= 50 => 'bg-blue-600',
        default => 'bg-blue-600 dark:bg-white'
    };

    $statusLabel = match(true) {
        $progress >= 100 => 'Completed',
        default => 'In Progress'
    };

    $statusColor = match(true) {
        $progress >= 100 => 'text-green-600 dark:text-green-400',
        default => 'text-orange-600 dark:text-orange-400'
    };
@endphp

<a href="{{ $buttonUrl }}" class="block p-4 px-6 bg-white border border-gray-200 shadow-sm dark:bg-gray-800/40 dark:border-transparent dark:backdrop-blur-none hover:bg-gray-50 dark:hover:bg-gray-700/40 rounded-lg transition-colors duration-200 cursor-pointer">
    {{-- Header: Title and Percentage --}}
    <div class="flex items-center justify-between mb-0.5">
        <p class="text-sm font-medium text-gray-900 dark:text-white truncate pr-3">{{ $title }}</p>
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex-shrink-0">{{ round($progress) }}%</span>
    </div>

    {{-- Duration --}}
    @if($duration)
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $duration }}</p>
    @endif

    {{-- Progress Bar --}}
    <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
        <div class="{{ $progressBarColor }} h-full rounded-full transition-all duration-500 ease-out"
             style="width: {{ $progress }}%"></div>
    </div>
</a>
