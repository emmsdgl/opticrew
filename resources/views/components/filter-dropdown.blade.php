@props([
    'label' => 'Filter',
    'selected' => '',
    'options' => [],
    'onSelect' => null,
    'buttonClass' => 'bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-3 inline-flex justify-between items-center gap-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-blue-800 transition-all duration-300',
])

@php
    $selectedLabel = $label;
    foreach($options as $key => $value) {
        if($key === $selected) {
            $selectedLabel = $value;
            break;
        }
    }
@endphp

<div class="relative inline-block" x-data="{ open: false }">
    <!-- Dropdown Button -->
    <button
        @click="open = !open"
        type="button"
        class="{{ $buttonClass }}">
        <span class="text-gray-700 dark:text-white text-xs font-normal">{{ $selectedLabel }}</span>
        <svg
            class="w-2.5 h-2.5 ms-2 transition-transform duration-300 text-gray-600 dark:text-gray-400"
            :class="{ 'rotate-180': open }"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 10 6">
            <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 1 4 4 4-4" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 top-full mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-lg min-w-[10rem] dark:bg-gray-700 origin-top"
        style="display: none;">
        <ul class="py-2 text-xs text-gray-700 dark:text-white">
            @foreach($options as $key => $value)
                <li>
                    <button
                        type="button"
                        @click="open = false"
                        onclick="{{ str_replace('{value}', $key, $onSelect) }}"
                        class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors {{ $key === $selected ? 'bg-gray-100 dark:bg-gray-600 font-medium' : '' }}">
                        {{ $value }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>
