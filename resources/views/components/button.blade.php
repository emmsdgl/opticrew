@props([
    'label' => 'Click Me',     // Text on the button
    'color' => 'blue',         // Tailwind color prefix (blue, green, red, etc.)
    'size' => 'sm',            // Size: sm, md, lg
    'icon' => null,            // Optional SVG or icon class
    'type' => 'button',        // HTML button type
    'textcolor' => 'white',        // HTML button type
])

@php
    $sizes = [
        'sm' => 'px-8 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'back-btn' => 'px-10 py-4 text-sm',
        'submit-btn' => 'px-10 py-4 text-sm',
        'lg' => 'px-8 py-3 text-base'
    ];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "inline-flex items-center justify-center gap-2 font-medium rounded-full
                    bg-{$color}-600 hover:bg-{$color}-700 focus:ring-4 focus:outline-none focus:ring-{$color}-300
                    text-{$textcolor} transition-all duration-300 {$sizes[$size]}"
    ]) }}
>
    @if($icon)
        <span class="flex items-center">
            {!! $icon !!}
        </span>
    @endif
    <span>{{ $label }}</span>
</button>
