@props([
    'label' => 'Label',
    'colorClass' => 'bg-gray-200 text-gray-700',
    'size' => 'text-xs',
])

<span 
    {{ $attributes->merge([
        'class' => "px-2 py-0.5 rounded-full font-sans font-medium {$size} {$colorClass}"
    ]) }}
>
    {{ $label }}
</span>
