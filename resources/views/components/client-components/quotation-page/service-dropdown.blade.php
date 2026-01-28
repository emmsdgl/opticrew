{{-- resources/views/components/service-dropdown.blade.php --}}
@props([
    'label' => 'Select Service',
    'name' => 'service',
    'options' => [],
    'placeholdericon' => '',
    'required' => false,
    'xModel' => null,
])

<div class="space-y-3" x-data="{ open: false, selected: null }">
    <label class="block text-sm text-gray-700 dark:text-gray-300">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">
        <!-- Dropdown Button -->
        <button
            type="button"
            @click="open = !open"
            class="w-full p-4 border  rounded-xl transition-all duration-300 bg-white dark:bg-gray-900
                   border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500
                   focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-600"
            :class="{ 'border-blue-600 bg-blue-50 dark:bg-blue-900/20': open }"
        >
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-start gap-3 flex-1 text-left">
                    <i class="{{$placeholdericon}} text-[#081032] dark:text-blue-400 text-lg mt-1"
                       x-show="!selected"></i>
                    <i :class="selected?.icon || 'fa-solid fa-triangle-exclamation'"
                       class="text-[#081032] dark:text-blue-400 text-lg mt-1"
                       x-show="selected"></i>
                    <div class="flex-1">
                        <h3 class="text-sm text-gray-900 dark:text-white mb-1"
                            x-text="selected ? selected.title : '{{ $options[0]['title'] ?? 'Select a service' }}'">
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-500"
                           x-text="selected ? selected.description : '{{ $options[0]['description'] ?? 'Choose from available options' }}'">
                        </p>
                    </div>
                </div>
                <div>
                    <i class="fa-solid fa-chevron-down text-gray-600 dark:text-gray-400 transition-transform duration-300"
                       :class="{ 'rotate-180': open }"></i>
                </div>
            </div>
        </button>

        <!-- Hidden Input -->
        <input type="hidden" name="{{ $name }}" :value="selected?.value" {{ $xModel ? "x-model=\"$xModel\"" : '' }}>

        <!-- Dropdown Menu -->
        <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl shadow-lg overflow-hidden"
            style="display: none;"
        >
            <div class="max-h-96 overflow-y-auto">
                @foreach($options as $index => $option)
                    <div
                        @click="selected = {{ json_encode($option) }}; open = false"
                        class="p-4 cursor-pointer transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20
                               border-b border-gray-100 dark:border-gray-600 last:border-b-0"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': selected?.value === '{{ $option['value'] }}' }"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 flex-1">
                                <i class="{{ $option['icon'] ?? '' }} text-[#081032] dark:text-blue-400 text-base mt-1"></i>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                                        {{ $option['title'] }}
                                    </h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ $option['description'] }}
                                    </p>
                                </div>
                            </div>
                            <div x-show="selected?.value === '{{ $option['value'] }}'">
                                <i class="fa-solid fa-circle-check text-[#081032] dark:text-blue-400 text-base"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>