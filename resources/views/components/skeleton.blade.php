{{-- Base Skeleton Element --}}
@props([
    'variant' => 'default',   // default, circle, rounded, square
    'animation' => 'shimmer', // shimmer, pulse, none
    'width' => null,
    'height' => null,
    'class' => '',
])

@php
    $variantClasses = [
        'default' => 'rounded-md',
        'circle' => 'rounded-full',
        'rounded' => 'rounded-xl',
        'square' => 'rounded-none',
    ];

    $animationClasses = [
        'shimmer' => 'skeleton-shimmer',
        'pulse' => 'animate-pulse',
        'none' => '',
    ];

    $style = '';
    if ($width) $style .= 'width: ' . (is_numeric($width) ? $width . 'px' : $width) . ';';
    if ($height) $style .= 'height: ' . (is_numeric($height) ? $height . 'px' : $height) . ';';
@endphp

<div
    {{ $attributes->merge([
        'class' => 'bg-gray-200 dark:bg-gray-700/60 relative overflow-hidden '
            . ($variantClasses[$variant] ?? $variantClasses['default']) . ' '
            . ($animationClasses[$animation] ?? $animationClasses['shimmer']) . ' '
            . $class,
        'style' => $style,
    ]) }}
></div>
