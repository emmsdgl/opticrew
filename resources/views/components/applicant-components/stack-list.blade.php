@props([
    'initialVisible' => 1,
    'class'          => '',
])

<div
    x-data="{ expanded: false }"
    class="bg-transparent overflow-hidden {{ $class }}"
>
    <div class="flex flex-col gap-2.5 relative">
        {{ $slot }}
    </div>

    {{-- Show All / Hide toggle --}}
    @isset($toggle)
    <div x-show="true">
        <button
            @click="expanded = !expanded"
            class="mt-3 mx-auto flex items-center justify-center gap-1.5 px-3 py-1.5
                   border border-gray-200 dark:border-gray-700 rounded-full
                   text-[10px] font-medium text-gray-500 dark:text-gray-400 transition-colors
                   hover:bg-gray-50 dark:hover:bg-gray-800"
        >
            <span x-text="expanded ? 'Hide' : 'Show All'"></span>
            <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-300"
               :class="expanded && 'rotate-180'"></i>
        </button>
    </div>
    @endisset
</div>
