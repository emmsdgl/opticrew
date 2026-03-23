@props([
    'title' => 'Attendance Summary',
    'subtitle' => 'Monthly attendance breakdown',
    'items' => [],
])

@php
    // Calculate max value for relative bar widths
    $maxValue = collect($items)->max('current') ?: 1;
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
    {{-- Header --}}
    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-6">{{ $subtitle }}</p>

    {{-- Funnel Items --}}
    <div class="space-y-5">
        @foreach($items as $index => $item)
            @php
                $barWidth = $maxValue > 0 ? max(5, ($item['current'] / $maxValue) * 100) : 5;

                $barColor = match($item['color'] ?? 'blue') {
                    'blue' => 'bg-blue-600',
                    'navy' => 'bg-indigo-700',
                    'cyan' => 'bg-cyan-500',
                    'yellow' => 'bg-amber-500',
                    'green' => 'bg-green-500',
                    'red' => 'bg-red-500',
                    default => 'bg-blue-600',
                };

                // Calculate drop percentage from previous item
                $dropPercent = null;
                if ($index > 0 && isset($items[$index - 1]) && $items[$index - 1]['current'] > 0) {
                    $prev = $items[$index - 1]['current'];
                    $diff = $prev - $item['current'];
                    if ($diff > 0) {
                        $dropPercent = round(($diff / $prev) * 100, 1);
                    }
                }
            @endphp

            {{-- Drop indicator --}}
            @if($dropPercent !== null)
                <div class="flex items-center gap-2 -mt-2 -mb-2">
                    <span class="text-[11px] text-gray-400 dark:text-gray-500 pl-1">
                        {{ $dropPercent }}% drop
                    </span>
                </div>
            @endif

            {{-- Item row --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item['label'] }}</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                        {{ number_format($item['current']) }}<span class="text-xs font-normal text-gray-400 dark:text-gray-500">/{{ number_format($item['total']) }}</span>
                    </span>
                </div>
                <div class="w-full h-2.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="{{ $barColor }} h-full rounded-full transition-all duration-700 ease-out"
                         style="width: {{ $barWidth }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
