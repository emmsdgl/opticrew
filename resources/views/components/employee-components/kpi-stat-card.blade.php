@props([
    'label' => '',
    'value' => '0',
    'icon' => 'fas fa-chart-bar',
    'trend' => '',
    'trendUp' => false,
])

<div class="bg-blue-300/20 dark:bg-gray-800/30 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-md transition-all duration-300">
    <div class="flex items-start justify-between">
        <div class="p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
            <i class="{{ $icon }} text-base text-blue-600"></i>
        </div>
        @if($trend)
            <span class="text-[10px] px-1.5 py-0.5 rounded-md
                {{ $trendUp
                    ? 'bg-blue-500 font-semiboldtext-white dark:bg-gray-800 dark:text-gray-400'
                    : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                {{ $trend }}
            </span>
        @endif
    </div>
    <div class="mt-4">
        <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">{{ $label }}</p>
        <p class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $value }}</p>
    </div>
</div>
