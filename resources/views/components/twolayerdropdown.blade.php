@props([
    'label' => 'Label',
    'description' => 'Description text here',
    'options' => [],
    'default' => null,
    'id' => null,
])

@php
    $uniqueId = $id ?? uniqid('infodropdown_');
@endphp

<div 
    x-data="{ open: false, selected: '{{ $default ?? $label }}' }"
    class="relative w-full"
>
    <!-- Button -->
    <button 
        type="button"
        @click="open = !open"
        class="w-full flex flex-col items-start justify-between
               bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
               rounded-xl px-4 py-4 text-left shadow-sm hover:shadow-md 
               transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
        <div class="flex w-full justify-between items-center ml-3">
            <p class="font-semibold text-base text-[#081032] dark:text-white" x-text="selected"></p>
            <i :class="open ? 'fa-solid fa-chevron-up text-gray-400 mr-6' : 'fa-solid fa-chevron-down text-gray-400 mr-6'"></i>
        </div>
        <p class="text-xs italic text-gray-500 mt-1 ml-3">{{ $description }}</p>
    </button>

    <!-- Dropdown Menu -->
    <div 
        x-show="open"
        @click.away="open = false"
        x-transition.origin.top
        class="absolute left-0 right-0 mt-2 bg-white dark:bg-gray-800 
               border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg 
               z-[9999] overflow-hidden"
    >
        <ul class="max-h-60 overflow-y-auto">
            @foreach ($options as $option)
                <li>
                    <button 
                        type="button"
                        @click="selected = '{{ $option['label'] ?? $option }}'; open = false"
                        class="w-full text-left px-4 py-3 text-sm 
                               hover:bg-gray-100 dark:hover:bg-gray-700 
                               text-gray-700 dark:text-gray-200 transition-all duration-150"
                    >
                        <p class="font-medium text-[#071957] dark:text-white">
                            {{ $option['label'] ?? $option }}
                        </p>
                        @if(isset($option['description']))
                            <p class="text-xs italic text-gray-500 mt-1">
                                {{ $option['description'] }}
                            </p>
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>
