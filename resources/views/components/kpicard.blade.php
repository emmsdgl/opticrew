@props([
    'label' => '',
    'labelColor' => null,
    'amount' => '0',
    'description' => '',
    'percentage' => null,
    'percentageColor' => null,
    'bgLight' => 'bg-white',
    'bgDark' => 'dark:bg-gray-800',
])

<div 
    class="rounded-xl p-3 md:p-4 transition-all duration-200 hover:-translate-y-1 hover:shadow-lg {{ $bgLight }} {{ $bgDark }} border border-gray-200 dark:border-gray-700 h-32 flex flex-col justify-between"
>
    {{-- Header (icon + label) --}}
    <div class="flex items-center gap-2 mb-2">
        <span 
            class="text-xs md:text-sm font-medium {{ $labelColor ?? 'text-gray-600 dark:text-gray-300' }} truncate"
        >
            {{ $label }}
        </span>
    </div>

    {{-- Main amount --}}
    <div class="text-xl md:text-2xl lg:text-3xl font-bold mb-2 text-gray-900 dark:text-white">
        {{ $amount }}
    </div>

    {{-- Description and percentage --}}
    <div class="flex justify-between items-center gap-2 mt-auto">
        <span class="text-xs text-gray-500 dark:text-gray-400 truncate flex-1">
            {{ $description }}
        </span>
        @if($percentage)
            @php
                // Auto-determine color based on percentage if not provided
                $autoColor = $percentageColor;
                if (!$autoColor) {
                    $numericValue = (float) str_replace(['%', '+', '-'], '', $percentage);
                    $isNegative = strpos($percentage, '-') !== false;
                    $autoColor = $isNegative ? '#ef4444' : '#10b981';
                }
            @endphp
            <span 
                class="text-xs md:text-sm font-semibold flex-shrink-0"
                style="color: {{ $autoColor }};"
            >
                {{ $percentage }}
            </span>
        @endif
    </div>
</div>