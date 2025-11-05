@props([
    'label' => 'Click Me',     // Text on the button
    'color' => 'blue',         // Tailwind color prefix (blue, green, red, etc.)
    'size' => 'md',            // Size: sm, md, lg
    'icon' => null,            // Optional SVG or icon class
    'type' => 'button',        // HTML button type
    'textcolor' => 'white',    // Text color
])

@php
    $sizes = [
        'sm' => 'px-8 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'back-btn' => 'px-10 py-4 text-sm',
        'submit-btn' => 'px-10 py-4 text-sm',
        'save-edit-profile' => 'px-12 py-4 text-sm',
        'lg' => 'px-8 py-3 text-base'
    ];

    $colors = [
        'blue' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-300 dark:focus:ring-blue-800',
        'green' => 'bg-green-600 hover:bg-green-700 focus:ring-green-300 dark:focus:ring-green-800',
        'red' => 'bg-red-600 hover:bg-red-700 focus:ring-red-300 dark:focus:ring-red-800',
        'purple' => 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-300 dark:focus:ring-purple-800',
        'yellow' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-300 dark:focus:ring-yellow-800',
        'gray' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-300 dark:focus:ring-gray-800',
        'indigo' => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-300 dark:focus:ring-indigo-800',
        'pink' => 'bg-pink-600 hover:bg-pink-700 focus:ring-pink-300 dark:focus:ring-pink-800',
    ];

    $textColors = [
        'white' => 'text-white',
        'black' => 'text-black',
        'gray' => 'text-gray-900 dark:text-white',
    ];

    $colorClass = $colors[$color] ?? $colors['blue'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $textColorClass = $textColors[$textcolor] ?? $textColors['white'];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "inline-flex items-center justify-center gap-2 font-medium rounded-full
                    {$colorClass} {$textColorClass} focus:ring-4 focus:outline-none
                    transition-all duration-300 {$sizeClass}"
    ]) }}
>
    @if($icon)
        <span class="flex items-center">
            {!! $icon !!}
        </span>
    @endif
    <span>{{ $label }}</span>
</button>