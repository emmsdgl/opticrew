{{--
    Funnel Chart Component
    Displays a list of metrics as horizontal bars with the percentage drop
    shown between consecutive rows.

    Usage:
    <x-funnel-chart :items="[
        ['label' => 'Impressions', 'value' => 45000],
        ['label' => 'Clicks',      'value' => 12000],
        ['label' => 'Signups',     'value' => 3000],
        ['label' => 'Purchases',   'value' => 850],
    ]" />

    Optional props:
        :showDrop (bool) — render the percent drop between rows (default true)
        :format   (string) — 'number' (default), shows raw integer with thousand separators
--}}
@props([
    'items' => [],
    'showDrop' => true,
    'format' => 'number',
])

@php
    $items = collect($items)->values();
    $maxValue = $items->max('value') ?: 1;

    $formatValue = function ($value) use ($format) {
        if ($format === 'number') {
            return number_format((float) $value);
        }
        return $value;
    };
@endphp

<div class="w-full">
    @foreach ($items as $index => $item)
        @php
            $value = (float) ($item['value'] ?? 0);
            $width = $maxValue > 0 ? max(2, ($value / $maxValue) * 100) : 0;
            $prev = $index > 0 ? (float) ($items[$index - 1]['value'] ?? 0) : null;
            $drop = null;
            if ($prev !== null && $prev > 0) {
                $drop = round((($prev - $value) / $prev) * 100, 1);
            }
        @endphp

        {{-- Row --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $item['label'] ?? '' }}
                </span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">
                    {{ $formatValue($value) }}
                </span>
            </div>
            <div class="w-full h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                <div class="h-full rounded-full bg-gray-900 dark:bg-white transition-all duration-500"
                     style="width: {{ $width }}%;"></div>
            </div>
        </div>

        {{-- Drop indicator (between rows) --}}
        @if ($showDrop && $drop !== null && !$loop->last)
            <div class="text-center my-3">
                <span class="text-xs font-semibold text-gray-400 dark:text-gray-500">
                    {{ $drop > 0 ? $drop . '% drop' : ($drop < 0 ? abs($drop) . '% gain' : 'no change') }}
                </span>
            </div>
        @elseif ($showDrop && !$loop->last)
            <div class="my-3"></div>
        @endif
    @endforeach
</div>
