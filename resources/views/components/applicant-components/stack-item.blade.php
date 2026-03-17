@props([
    'colorIndex' => 0,
    'initials'   => '?',
    'subtitle'   => '',
    'title'      => '',
    'detail'     => '',
    'badge'      => '',
    'badgeClass' => 'bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300',
    'iconClass'  => '',
])

@php
    $colorClasses = [
        ['card' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800/40', 'icon' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-500', 'accent' => 'text-blue-600 dark:text-blue-400'],
        ['card' => 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800/40', 'icon' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-500', 'accent' => 'text-purple-600 dark:text-purple-400'],
        ['card' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800/40', 'icon' => 'bg-green-100 dark:bg-green-900/30 text-green-500', 'accent' => 'text-green-600 dark:text-green-400'],
        ['card' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800/40', 'icon' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-500', 'accent' => 'text-amber-600 dark:text-amber-400'],
        ['card' => 'bg-cyan-50 dark:bg-cyan-900/20 border-cyan-200 dark:border-cyan-800/40', 'icon' => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-500', 'accent' => 'text-cyan-600 dark:text-cyan-400'],
        ['card' => 'bg-pink-50 dark:bg-pink-900/20 border-pink-200 dark:border-pink-800/40', 'icon' => 'bg-pink-100 dark:bg-pink-900/30 text-pink-500', 'accent' => 'text-pink-600 dark:text-pink-400'],
    ];
    $color = $colorClasses[$colorIndex % count($colorClasses)];
@endphp

<div class="flex items-start gap-3 p-3 rounded-xl border transition-all hover:shadow-sm {{ $color['card'] }}">
    {{-- Icon / Avatar badge --}}
    <div class="flex-shrink-0 flex flex-col items-center">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $color['icon'] }}">
            @if($iconClass)
                <i class="{{ $iconClass }} text-sm"></i>
            @else
                <span class="text-sm font-bold">{{ $initials }}</span>
            @endif
        </div>
        {{-- @if($subtitle)
            <span class="text-[9px] font-bold mt-1 {{ $color['accent'] }}">{{ $subtitle }}</span>
        @endif --}}
    </div>

    {{-- Details --}}
    <div class="flex-1 min-w-0">
        <p class="text-xs font-bold truncate text-gray-800 dark:text-gray-100">{{ $title }}</p>
        @if($detail)
            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $detail }}</p>
        @endif
        @isset($extra)
            {{ $extra }}
        @endisset
    </div>

    {{-- Badge --}}
    @if($badge)
        <div class="flex-shrink-0">
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeClass }}">{{ $badge }}</span>
        </div>
    @endif
</div>
