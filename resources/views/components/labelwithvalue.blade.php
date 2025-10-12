@props(['label' => '', 'count' => ''])

<div class="flex flex-row">
    <p class="text-sm font-sans font-bold mr-2">
        {{ $label }}
    </p>
    <sup class="text-xs font-sans font-bold text-gray-600">{{ $count }}</sup>
</div>
