@props([
    'label' => 'Actions',
    'icon' => '<i class="fas fa-ellipsis-v"></i>',
    'buttonClass' => 'inline-flex items-center justify-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition duration-150 ease-in-out shadow-sm',
    'align' => 'right', // 'left' or 'right'
])

<div class="relative" x-data="{ open: false }">
    <!-- Dropdown Button -->
    <button
        @click="open = !open"
        @click.away="open = false"
        type="button"
        class="{{ $buttonClass }}">
        {!! $icon !!}
        <span>{{ $label }}</span>
        <svg class="ml-2 -mr-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute {{ $align === 'left' ? 'left-0' : 'right-0' }} mt-2 w-48 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 py-1 z-10"
        style="display: none;">
        {{ $slot }}
    </div>
</div>
