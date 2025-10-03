@props(['active'])

@php
$classes = get_nav_link_classes($active ?? false);
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>