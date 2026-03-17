@props([
    'icon'     => 'fa-solid fa-circle',
    'title'    => '',
    'subtitle' => '',
    'end'      => '',
])

<div {{ $attributes->merge([
    'class' => 'flex items-center gap-3 p-3 bg-white dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm
                transition-all duration-300 ease-out'
]) }}
    x-show="true"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
>
    {{-- Icon --}}
    <div class="w-9 h-9 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
        <i class="{{ $icon }} text-xs text-gray-600 dark:text-gray-300"></i>
    </div>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        <div class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ $title }}</div>
        @if($subtitle)
        <div class="text-[10px] text-gray-500 dark:text-gray-400 truncate">{{ $subtitle }}</div>
        @endif
    </div>

    {{-- End slot (date, count badge, etc.) --}}
    @if($end)
    <div class="text-[10px] text-gray-400 dark:text-gray-500 flex-shrink-0">{{ $end }}</div>
    @endif
</div>
