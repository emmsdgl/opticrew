@props([
    'items' => [],
])

<nav class="flex items-center gap-1.5 text-sm">
    @foreach($items as $index => $item)
        @if($index > 0)
            <i class="fi fi-rr-angle-small-right text-xs text-gray-400 dark:text-gray-500 mt-0.5"></i>
        @endif

        @if($index === count($items) - 1)
            <span class="font-medium text-gray-900 dark:text-white">{{ $item['label'] }}</span>
        @else
            <a href="{{ $item['url'] }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                {{ $item['label'] }}
            </a>
        @endif
    @endforeach
</nav>
