@props([
    'label' => '',
    'value' => '0',
    'icon' => 'fas fa-chart-bar',
    'trend' => '',
    'trendUp' => false,
    'trendLabel' => 'vs last month',
    'currentCount' => null,
    'previousCount' => null,
])

<div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md transition-all duration-300">
    <div class="flex items-start justify-between">
        <div class="p-2 rounded-xl bg-blue-200/30 dark:bg-gray-600/20 text-gray-900 dark:text-gray-100">
            <i class="{{ $icon }} text-base text-blue-600"></i>
        </div>
        @if($trend)
            <div class="relative" x-data="{ showTip: false }">
                <span @mouseenter="showTip = true" @mouseleave="showTip = false"
                      class="text-[10px] px-1.5 py-0.5 rounded-md cursor-help
                    {{ $trendUp
                        ? 'bg-blue-500 font-semibold text-white bg-blue-200/30 dark:bg-gray-600/20 dark:text-gray-400'
                        : 'bg-gray-100 font-semibold text-gray-600 bg-blue-200/30 dark:bg-gray-600/20 dark:text-gray-400' }}">
                    {{ $trend }}
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
                        <i class="fas fa-chart-line text-[10px] {{ $trendUp ? 'text-blue-500' : 'text-gray-400' }}"></i>
                        <span class="font-bold text-gray-900 dark:text-white">Trend Analysis</span>
                    </div>
                    <p class="leading-relaxed">
                        <span class="font-semibold {{ $trendUp ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">{{ $trend }}</span>
                        {{ $trendUp ? 'increase' : 'decrease' }} in {{ Str::lower($label) }} {{ $trendLabel }}.
                    </p>
                    @if(!is_null($currentCount) && !is_null($previousCount))
                        <div class="mt-2 flex items-center justify-between gap-2 text-[11px]">
                            <div class="flex flex-col items-center flex-1 p-1.5 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <span class="text-[10px] text-gray-400 dark:text-gray-500">Last month</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $previousCount }}</span>
                            </div>
                            <i class="fas fa-arrow-right text-[8px] text-gray-300 dark:text-gray-600"></i>
                            <div class="flex flex-col items-center flex-1 p-1.5 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <span class="text-[10px] text-gray-400 dark:text-gray-500">This month</span>
                                <span class="font-bold {{ $trendUp ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white' }}">{{ $currentCount }}</span>
                            </div>
                        </div>
                    @endif
                    <div class="mt-1.5 pt-1.5 border-t border-gray-100 dark:border-gray-700 text-[10px] text-gray-400 dark:text-gray-500">
                        Compared to the previous month
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="mt-4">
        <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">{{ $label }}</p>
        <p class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $value }}</p>
    </div>
</div>
