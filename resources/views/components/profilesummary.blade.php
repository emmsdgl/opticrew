@props([
    'title' => null,                 // Optional title of the dashboard section
    'cards' => [],                   // Array of dashboard cards
    'maxWidth' => 'md',              // For container width (sm, md, lg, xl)
])

<div class="w-full mx-auto justify-center align-items-center max-w-{{ $maxWidth }}">
    <div class="rounded-3xl p-6 transition-colors duration-300">

        {{-- Title --}}
        @if($title)
            <div class="flex items-center justify-between mb-6 mt-12">
                <h2 class="text-base font-sans font-semibold text-gray-800 dark:text-white">
                    {{ $title }}
                </h2>
            </div>
        @endif

        {{-- Cards --}}
        @if(!empty($cards))
            <div class="flex flex-col gap-4">
                @foreach($cards as $card)
                    <div 
                        class="rounded-2xl p-4 transition-all duration-200 hover:-translate-y-1 card-item bg-white dark:bg-gray-800"
                        @class([
                            'dark:bg-gray-700' => empty($card['bgColorDark']),
                        ])
                    >
                        {{-- Header (icon + label) --}}
                        <div class="flex items-center gap-2 mb-2">
                            <span 
                                class="text-base font-semibold"
                                style="color: {{ $card['iconColor'] ?? '#666' }};"
                            >
                                {!! $card['icon'] ?? '●' !!}
                            </span>
                            <span 
                                class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                style="color: {{ $card['labelColor'] ?? '' }};"
                            >
                                {{ $card['label'] ?? '' }}
                            </span>
                        </div>

                        {{-- Main amount --}}
                        <div class="text-3xl font-bold mb-1 text-gray-800 dark:text-white">
                            {{ $card['amount'] ?? '0' }}
                        </div>

                        {{-- Description and percentage --}}
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $card['description'] ?? '' }}
                            </span>
                            @if(isset($card['percentage']))
                                <span 
                                    class="text-sm font-semibold"
                                    style="color: {{ $card['percentageColor'] ?? '#10b981' }};"
                                >
                                    {{ $card['percentage'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
