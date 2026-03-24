@props([
    'title' => 'Statistic',
    'value' => '0',
    'subtitle' => '',
    'trend' => null,           // 'up', 'down', or null
    'trendValue' => null,      // e.g., '3.4%'
    'trendLabel' => 'vs last month',
    'icon' => null,
    'iconBg' => 'bg-gray-100 dark:bg-gray-800',
    'iconColor' => 'text-blue-600',
    'valuePrefix' => '',       // e.g., '$'
    'valueSuffix' => '',       // e.g., '$', '%'
    'maxTitleChars' => 20,
    'maxValueChars' => 15,
    'maxSubtitleChars' => 25,
])

<div class="bg-white dark:bg-gray-800/30 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col min-w-0">

    <!-- Header: Icon + Trend -->
    <div class="flex items-start justify-between">
        @if($icon)
        <div class="p-2 rounded-xl {{ $iconBg }} flex items-center justify-center flex-shrink-0">
            <i class="{{ $icon }} {{ $iconColor }} text-base"></i>
        </div>
        @endif

        @if($trend && $trendValue)
            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md
                {{ $trend === 'up'
                    ? 'bg-blue-500 text-white dark:bg-gray-800 dark:text-gray-400'
                    : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                {{ $trendValue }}
            </span>
        @endif
    </div>

    <!-- Label + Value -->
    <div class="mt-4">
        <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold truncate" title="{{ $title }}">
            {{ Str::limit($title, $maxTitleChars, '...') }}
        </p>
        <p class="text-2xl font-black text-gray-900 dark:text-gray-100 truncate"
           title="{{ $valuePrefix }}{{ $value }}{{ $valueSuffix }}">
            {{ $valuePrefix }}{{ Str::limit($value, $maxValueChars, '...') }}{{ $valueSuffix }}
        </p>
    </div>

    <!-- Subtitle -->
    @if($subtitle)
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 truncate" title="{{ $subtitle }}">
            {{ Str::limit($subtitle, $maxSubtitleChars, '...') }}
        </p>
    @endif

    <!-- Custom Slot -->
    @if($slot->isNotEmpty())
    <div class="mt-2 pt-3 border-t border-gray-200 dark:border-gray-700">
        {{ $slot }}
    </div>
    @endif
</div>
