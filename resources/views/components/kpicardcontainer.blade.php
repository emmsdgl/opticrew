@props([
    'title' => null,
    'cards' => [],
    'columns' => 'auto', // 'auto', '1', '2', '3', '4'
])

@php
    $gridClasses = match($columns) {
        '1' => 'grid-cols-1',
        '2' => 'grid-cols-1 sm:grid-cols-2',
        '3' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        '4' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
        default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
    };
@endphp

<div class="w-full h-full flex flex-col">
    {{-- Title --}}
    @if($title)
        <div class="mb-3 md:mb-4 flex-shrink-0">
            <h2 class="text-base md:text-lg font-semibold text-gray-800 dark:text-white">
                {{ $title }}
            </h2>
        </div>
    @endif

    {{-- Cards Grid - Takes full remaining height --}}
    @if(!empty($cards))
        <div class="grid {{ $gridClasses }} gap-3 md:gap-4 flex-1">
            @foreach($cards as $card)
                <x-kpicard
                    :icon="$card['icon'] ?? null"
                    :iconColor="$card['iconColor'] ?? '#6366f1'"
                    :label="$card['label'] ?? ''"
                    :labelColor="$card['labelColor'] ?? null"
                    :amount="$card['amount'] ?? '0'"
                    :description="$card['description'] ?? ''"
                    :percentage="$card['percentage'] ?? null"
                    :percentageColor="$card['percentageColor'] ?? null"
                    :bgLight="$card['bgLight'] ?? 'bg-white'"
                    :bgDark="$card['bgDark'] ?? 'dark:bg-gray-800'"
                />
            @endforeach
        </div>
    @endif
</div>