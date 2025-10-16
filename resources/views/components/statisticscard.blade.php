@props([
    'title' => 'Statistic',
    'value' => '0',
    'subtitle' => '',
    'trend' => null,           // 'up', 'down', or null
    'trendValue' => null,      // e.g., '3.4%'
    'trendLabel' => 'vs last month',
    'icon' => null,           
    'iconBg' => 'bg-blue-100', 
    'iconColor' => 'text-blue-600',
    'valuePrefix' => '',       // e.g., '$'
    'valueSuffix' => '',       // e.g., '$', '%'
    'maxTitleChars' => 20,
    'maxValueChars' => 15,
    'maxSubtitleChars' => 25,
])

<div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 
            hover:shadow-lg transition-shadow duration-200 flex flex-col gap-3 min-w-0">
    
    <!-- Header: Icon + Title -->
    <div class="flex items-start justify-between gap-3 min-w-0">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            @if($icon)
            <div class="w-6 h-6 rounded-lg {{ $iconBg }} flex items-center justify-center flex-shrink-0">
                <i class="{{ $icon }} {{ $iconColor }} text-base"></i>
            </div>
            @endif
            
            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate" 
                title="{{ $title }}">
                {{ Str::limit($title, $maxTitleChars, '...') }}
            </h3>
        </div>
    </div>

    <!-- Value -->
    <div class="flex items-baseline gap-1 min-w-0">
        @if($valuePrefix)
        <span class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex-shrink-0">
            {{ $valuePrefix }}
        </span>
        @endif
        
        <span class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-100 truncate" 
              title="{{ $valuePrefix }}{{ $value }}{{ $valueSuffix }}">
            {{ Str::limit($value, $maxValueChars, '...') }}
        </span>
        
        @if($valueSuffix)
        <span class="text-xl font-bold text-gray-900 dark:text-gray-100 flex-shrink-0">
            {{ $valueSuffix }}
        </span>
        @endif
    </div>

    <!-- Subtitle / Trend -->
    <div class="flex items-center gap-2 min-w-0">
        @if($trend && $trendValue)
            <div class="flex items-center gap-1 flex-shrink-0">
                @if($trend === 'up')
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                    {{ $trendValue }}
                </span>
                @elseif($trend === 'down')
                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                </svg>
                <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                    {{ $trendValue }}
                </span>
                @endif
            </div>
            <span class="text-sm text-gray-500 dark:text-gray-400 truncate" 
                  title="{{ $trendLabel }}">
                {{ Str::limit($trendLabel, 20, '...') }}
            </span>
        @elseif($subtitle)
            <span class="text-sm text-gray-500 dark:text-gray-400 truncate" 
                  title="{{ $subtitle }}">
                {{ Str::limit($subtitle, $maxSubtitleChars, '...') }}
            </span>
        @endif
    </div>

    <!-- Custom Slot -->
    @if($slot->isNotEmpty())
    <div class="mt-2 pt-3 border-t border-gray-200 dark:border-gray-700">
        {{ $slot }}
    </div>
    @endif
</div>