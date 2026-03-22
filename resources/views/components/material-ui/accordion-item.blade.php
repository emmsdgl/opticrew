{{--
    Accordion Item Component

    Usage:
    <x-material-ui.accordion-item value="item-1" title="Section Title">
        Content...
    </x-material-ui.accordion-item>
--}}
@props([
    'value' => '',
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'iconBg' => 'bg-blue-50 dark:bg-blue-900/30',
    'iconColor' => 'text-blue-500 dark:text-blue-400',
    'disabled' => false,
    'badge' => null,
    'badgeColor' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
])

<div class="border-b border-gray-200 dark:border-gray-700/50"
     :data-state="isOpen('{{ $value }}') ? 'open' : 'closed'">

    {{-- Trigger --}}
    <button type="button"
        @click="toggle('{{ $value }}')"
        @if($disabled) disabled @endif
        class="w-full flex items-center justify-between py-3.5 px-1 text-left transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50 rounded-lg group disabled:opacity-50 disabled:cursor-not-allowed">

        <span class="flex items-center gap-3 min-w-0">
            @if($icon)
                <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ $iconBg }}">
                    <i class="{{ $icon }} text-xs {{ $iconColor }}"></i>
                </span>
            @endif
            <span class="min-w-0">
                <span class="block text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $title }}</span>
                @if($subtitle)
                    <span class="block text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $subtitle }}</span>
                @endif
            </span>
            @if($badge)
                <span class="ml-2 px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $badgeColor }}">{{ $badge }}</span>
            @endif
        </span>

        {{-- Morphing +/- icon --}}
        <span class="relative w-5 h-5 flex-shrink-0 ml-2">
            {{-- Plus --}}
            <svg class="w-5 h-5 absolute accordion-icon-plus text-gray-400 dark:text-gray-500"
                 :class="isOpen('{{ $value }}') ? 'is-open' : 'is-closed'"
                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/><path d="M12 5v14"/>
            </svg>
            {{-- Minus --}}
            <svg class="w-5 h-5 absolute accordion-icon-minus text-gray-400 dark:text-gray-500"
                 :class="isOpen('{{ $value }}') ? 'is-open' : 'is-closed'"
                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/>
            </svg>
        </span>
    </button>

    {{-- Content with height animation --}}
    <div x-ref="content_{{ $value }}"
         x-show="isOpen('{{ $value }}')"
         x-collapse
         class="overflow-hidden">
        <div class="pb-4 pt-1 px-1 text-sm text-gray-700 dark:text-gray-300">
            {{ $slot }}
        </div>
    </div>
</div>
