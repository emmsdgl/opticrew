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

<div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col min-w-0">

    <!-- Header: Icon + Trend -->
    <div class="flex items-start justify-between">
        @if($icon)
        <div class="p-2 rounded-xl {{ $iconBg }} flex items-center justify-center flex-shrink-0">
            <i class="{{ $icon }} {{ $iconColor }} text-base"></i>
        </div>
        @endif

        @if($trend && $trendValue)
            <div class="relative" x-data="{ showTip: false }">
                <span @mouseenter="showTip = true" @mouseleave="showTip = false"
                      class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md cursor-help
                    {{ $trend === 'up'
                        ? 'bg-blue-500 text-white dark:bg-gray-800 dark:text-gray-400'
                        : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                    {{ $trendValue }}
                </span>

                <div x-show="showTip"
                     x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute right-0 top-full mt-2 z-50 w-52 p-3 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-300">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <i class="fas fa-chart-line text-[10px] {{ $trend === 'up' ? 'text-blue-500' : 'text-gray-400' }}"></i>
                        <span class="font-bold text-gray-900 dark:text-white">Trend Analysis</span>
                    </div>
                    <p class="leading-relaxed">
                        <span class="font-semibold {{ $trend === 'up' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">{{ $trendValue }}</span>
                        {{ $trend === 'up' ? 'increase' : 'decrease' }} in {{ Str::lower($title) }} {{ $trendLabel }}.
                    </p>
                    <div class="mt-1.5 pt-1.5 border-t border-gray-100 dark:border-gray-700 text-[10px] text-gray-400 dark:text-gray-500">
                        Compared to the previous month
                    </div>
                </div>
            </div>
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
