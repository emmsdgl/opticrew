@props([
    'initialVisible' => 1,
    'class'          => '',
    'maxHeight'      => 'max-h-64',
])

<style>
.stack-scroll::-webkit-scrollbar { width: 4px; }
.stack-scroll::-webkit-scrollbar-track { background: transparent; }
.stack-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.dark .stack-scroll::-webkit-scrollbar-thumb { background: #475569; }
</style>

<div
    x-data="{
        expanded: false,
        atTop: true,
        atBottom: false,
        onScroll(e) {
            const el = e.target;
            this.atTop = el.scrollTop <= 8;
            this.atBottom = el.scrollHeight - el.scrollTop - el.clientHeight <= 8;
        }
    }"
    class="relative bg-transparent overflow-hidden {{ $class }}"
>
    {{-- Top gradient fade --}}
    <div
        class="absolute top-0 left-0 right-0 h-8 z-10 pointer-events-none transition-opacity duration-300"
        style="background: linear-gradient(to bottom, var(--tw-gradient-from, rgb(255 255 255)) 0%, transparent 100%);"
        :class="atTop ? 'opacity-0' : 'opacity-100'"
        x-cloak
    >
        <div class="w-full h-full bg-gradient-to-b from-white dark:from-gray-900 to-transparent"></div>
    </div>

    {{-- Scrollable content --}}
    <div
        class="stack-scroll {{ $maxHeight }} overflow-y-auto px-0.5 py-1"
        @scroll="onScroll($event)"
        x-ref="stackScroll"
    >
        <div class="flex flex-col gap-2.5 relative">
            {{ $slot }}
        </div>
    </div>

    {{-- Bottom gradient fade --}}
    <div
        class="absolute bottom-0 left-0 right-0 h-12 z-10 pointer-events-none transition-opacity duration-300"
        :class="atBottom ? 'opacity-0' : 'opacity-100'"
        x-cloak
    >
        <div class="w-full h-full bg-gradient-to-t from-white dark:from-gray-900 to-transparent"></div>
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
