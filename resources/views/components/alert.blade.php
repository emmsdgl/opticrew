@props([
    'type' => 'success', // success, error, info
    'dismissible' => true,
])

@php
    $config = match($type) {
        'success' => [
            'bg' => 'bg-green-50 dark:bg-green-500/10',
            'border' => 'border-green-500 dark:border-green-500',
            'icon' => 'fa-solid fa-circle-check',
            'iconColor' => 'text-green-500 dark:text-green-400',
            'text' => 'text-green-800 dark:text-green-300',
            'close' => 'text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-200',
        ],
        'error' => [
            'bg' => 'bg-red-50 dark:bg-red-500/10',
            'border' => 'border-red-500 dark:border-red-500',
            'icon' => 'fa-solid fa-circle-xmark',
            'iconColor' => 'text-red-500 dark:text-red-400',
            'text' => 'text-red-800 dark:text-red-300',
            'close' => 'text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-200',
        ],
        'info' => [
            'bg' => 'bg-blue-50 dark:bg-blue-500/10',
            'border' => 'border-blue-500 dark:border-blue-500',
            'icon' => 'fa-solid fa-circle-info',
            'iconColor' => 'text-blue-500 dark:text-blue-400',
            'text' => 'text-blue-800 dark:text-blue-300',
            'close' => 'text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-200',
        ],
        default => [
            'bg' => 'bg-green-50 dark:bg-green-500/10',
            'border' => 'border-green-500 dark:border-green-500',
            'icon' => 'fa-solid fa-circle-check',
            'iconColor' => 'text-green-500 dark:text-green-400',
            'text' => 'text-green-800 dark:text-green-300',
            'close' => 'text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-200',
        ],
    };
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     class="mb-4 px-4 py-3 rounded-lg border {{ $config['bg'] }} {{ $config['border'] }} flex items-center gap-3"
     role="alert">
    <i class="{{ $config['icon'] }} {{ $config['iconColor'] }} text-lg flex-shrink-0"></i>
    <span class="flex-1 text-sm font-medium {{ $config['text'] }}">{{ $slot }}</span>
    @if($dismissible)
        <button @click="show = false" class="{{ $config['close'] }} flex-shrink-0 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
