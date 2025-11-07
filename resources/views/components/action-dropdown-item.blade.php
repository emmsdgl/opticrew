@props([
    'href' => '#',
    'icon' => null,
])

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => 'flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white transition-colors']) }}>
    @if($icon)
        <i class="{{ $icon }} w-4 text-center"></i>
    @endif
    <span>{{ $slot }}</span>
</a>
