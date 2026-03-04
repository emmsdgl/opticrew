@props([
    'stats' => [],
    'columns' => null,
])

@php
    $cols = $columns ?? count($stats);
    $gridClass = match(true) {
        $cols <= 3 => 'grid-cols-2 md:grid-cols-3',
        $cols == 4 => 'grid-cols-2 md:grid-cols-4',
        default => 'grid-cols-2 md:grid-cols-5',
    };
@endphp

<div class="grid {{ $gridClass }} gap-px bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
    @foreach($stats as $stat)
        <div class="bg-white dark:bg-slate-900 px-6 py-5">
            <div class="flex items-center gap-2 mb-2 ml-3">
                @if(isset($stat['icon']))
                    <i class="{{ $stat['icon'] }}" style="color: {{ $stat['iconColor'] ?? '#6b7280' }}"></i>
                @endif
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400">{{ $stat['label'] }}</p>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $stat['value'] }}</p>
            @if(isset($stat['subtitle']))
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">{{ $stat['subtitle'] }}</p>
            @endif
        </div>
    @endforeach
</div>
