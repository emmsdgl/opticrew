{{--
    Select Dropdown Component (Alpine.js)
    Usage:
    <x-material-ui.select-dropdown
        model="formData.service_type"
        placeholder="Select a Service"
        placeholderDesc="Choose from available options"
        placeholderIcon="fas fa-broom"
        :options="[...]"
    />
--}}
@props([
    'model' => '',
    'placeholder' => 'Select...',
    'placeholderDesc' => 'Choose from available options',
    'placeholderIcon' => null,
    'options' => [],
    'searchable' => false,
    'disabled' => false,
    'onChange' => null,
])

@php
    $uid = 'seldrop_' . uniqid();
@endphp

<div class="relative" x-data="{ sdOpen_{{ $uid }}: false, sdSearch_{{ $uid }}: '' }">
    {{-- Trigger Button --}}
    <button type="button"
        @click="sdOpen_{{ $uid }} = !sdOpen_{{ $uid }}"
        @if($disabled) disabled @endif
        class="w-full p-4 border rounded-xl transition-all duration-300 bg-white dark:bg-gray-800
               border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500
               focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-600
               disabled:opacity-50 disabled:cursor-not-allowed"
        :class="{ 'border-blue-600 bg-blue-50 dark:bg-blue-900/20': sdOpen_{{ $uid }} }">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-start gap-3 flex-1 text-left">
                {{-- Dynamic icon per option (server-rendered, toggled by Alpine) --}}
                @foreach($options as $opt)
                    <span x-show="{{ $model }} === '{{ $opt['value'] }}'"
                          class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 {{ $opt['iconBg'] ?? 'bg-gray-100 dark:bg-gray-700' }}">
                        <i class="{{ $opt['icon'] ?? '' }} text-sm {{ $opt['iconColor'] ?? 'text-gray-400' }}"></i>
                    </span>
                @endforeach

                {{-- Placeholder icon --}}
                @if($placeholderIcon)
                <span x-show="!{{ $model }}" class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 bg-gray-100 dark:bg-gray-700">
                    <i class="{{ $placeholderIcon }} text-sm text-gray-400 dark:text-gray-500"></i>
                </span>
                @endif

                {{-- Title & Description --}}
                <div class="flex-1 min-w-0">
                    @foreach($options as $opt)
                        <template x-if="{{ $model }} === '{{ $opt['value'] }}'">
                            <div>
                                <h3 class="text-sm font-medium mb-0.5 text-gray-900 dark:text-white">{{ $opt['label'] }}</h3>
                                @if(isset($opt['description']))
                                    <p class="text-xs text-gray-500 dark:text-gray-500">{{ $opt['description'] }}</p>
                                @endif
                            </div>
                        </template>
                    @endforeach
                    <div x-show="!{{ $model }}">
                        <h3 class="text-sm font-medium mb-0.5 text-gray-500 dark:text-gray-400">{{ $placeholder }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-500">{{ $placeholderDesc }}</p>
                    </div>
                </div>
            </div>
            <i class="fa-solid fa-chevron-down text-gray-600 dark:text-gray-400 transition-transform duration-300"
               :class="{ 'rotate-180': sdOpen_{{ $uid }} }"></i>
        </div>
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="sdOpen_{{ $uid }}" @click.away="sdOpen_{{ $uid }} = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute z-20 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden"
         style="display: none;">

        @if($searchable)
        <div class="p-3 border-b border-gray-100 dark:border-gray-700">
            <input type="text" x-model="sdSearch_{{ $uid }}" placeholder="Search..."
                class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        @endif

        <div class="max-h-80 overflow-y-auto seldrop-scroll">
            @foreach($options as $opt)
                @php
                    $val = $opt['value'];
                    $label = $opt['label'];
                    $desc = $opt['description'] ?? null;
                    $optIcon = $opt['icon'] ?? null;
                    $optIconBg = $opt['iconBg'] ?? 'bg-gray-100 dark:bg-gray-700';
                    $optIconColor = $opt['iconColor'] ?? 'text-gray-400 dark:text-gray-500';
                @endphp
                <div @click="{{ $model }} = '{{ $val }}'; sdOpen_{{ $uid }} = false; sdSearch_{{ $uid }} = ''; {{ $onChange ? $onChange . '()' : '' }}"
                    @if($searchable)
                        x-show="'{{ strtolower($label . ' ' . ($desc ?? '')) }}'.includes(sdSearch_{{ $uid }}.toLowerCase())"
                    @endif
                    class="p-4 cursor-pointer transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20
                           border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                    :class="{ 'bg-blue-50 dark:bg-blue-900/20': {{ $model }} === '{{ $val }}' }">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1">
                            @if($optIcon)
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 {{ $optIconBg }}">
                                    <i class="{{ $optIcon }} text-sm {{ $optIconColor }}"></i>
                                </span>
                            @endif
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-0.5">{{ $label }}</h3>
                                @if($desc)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                                @endif
                            </div>
                        </div>
                        {{-- Check indicator --}}
                        <div x-show="{{ $model }} === '{{ $val }}'" class="flex-shrink-0 mt-1">
                            <i class="fa-solid fa-circle-check text-blue-600 dark:text-blue-400 text-sm"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@once
<style>
.seldrop-scroll::-webkit-scrollbar { width: 6px; }
.seldrop-scroll::-webkit-scrollbar-track { background: transparent; }
.seldrop-scroll::-webkit-scrollbar-thumb { background: rgba(156,163,175,0.3); border-radius: 3px; }
.seldrop-scroll::-webkit-scrollbar-thumb:hover { background: rgba(156,163,175,0.5); }
.seldrop-scroll { scrollbar-width: thin; scrollbar-color: rgba(156,163,175,0.3) transparent; }
</style>
@endonce
