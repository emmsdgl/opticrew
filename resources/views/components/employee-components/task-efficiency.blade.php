@props([
    'items' => [],
])

<div class="bg-white dark:bg-gray-800/30 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col justify-evenly h-full">
    {{-- Progress Items --}}
    <div class="space-y-8">
        @foreach($items as $item)
            @php
                $percentage = $item['total'] > 0 ? round(($item['current'] / $item['total']) * 100) : 0;

                $barColor = 'bg-blue-600';
            @endphp
            <div class="space-y-2">
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ $item['label'] }}</span>
                    <span class="text-gray-900 dark:text-gray-100">{{ $percentage }}%</span>
                </div>
                <div class="w-full h-2.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="{{ $barColor }} h-full rounded-full transition-all duration-700 ease-out"
                         style="width: {{ $percentage }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
