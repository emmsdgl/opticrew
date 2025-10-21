@props([
    'label' => 'Progress',
    'current' => 0,
    'total' => 100,
    'color' => 'blue',
    'showPercentage' => false,
    'animated' => true,
    'size' => 'default', // 'sm', 'default', 'lg'
    'showLabel' => true,
    'showCount' => true
])

@php
    $percentage = $total > 0 ? ($current / $total) * 100 : 0;
    
    $colors = [
        'blue' => 'bg-blue-500 dark:bg-blue-600',
        'green' => 'bg-green-500 dark:bg-green-600',
        'purple' => 'bg-purple-500 dark:bg-purple-600',
        'red' => 'bg-red-500 dark:bg-red-600',
        'yellow' => 'bg-yellow-500 dark:bg-yellow-600',
        'pink' => 'bg-pink-500 dark:bg-pink-600',
        'indigo' => 'bg-indigo-500 dark:bg-indigo-600',
        'gray' => 'bg-gray-500 dark:bg-gray-600',
        'cyan' => 'bg-cyan-500 dark:bg-cyan-600',
        'navy' => 'bg-blue-900 dark:bg-blue-950',
    ];
    
    $sizes = [
        'sm' => 'h-1.5',
        'default' => 'h-2.5',
        'lg' => 'h-3.5',
    ];
    
    $textSizes = [
        'sm' => 'text-xs',
        'default' => 'text-sm',
        'lg' => 'text-base',
    ];
    
    $colorClass = $colors[$color] ?? $colors['blue'];
    $heightClass = $sizes[$size] ?? $sizes['default'];
    $textSizeClass = $textSizes[$size] ?? $textSizes['default'];
@endphp

<div class="w-full">
    <!-- Label and Count -->
    @if($showLabel || $showCount)
        <div class="flex items-center justify-between mb-2">
            @if($showLabel)
                <span class="{{ $textSizeClass }} font-medium text-gray-700 dark:text-gray-300">
                    {{ $label }}
                </span>
            @endif
            
            @if($showCount)
                <span class="{{ $textSizeClass }} font-bold text-gray-900 dark:text-white">
                    @if($showPercentage)
                        {{ number_format($percentage, 0) }}%
                    @else
                        <span class="text-gray-900 dark:text-white">{{ $current }}</span><span class="text-gray-400 dark:text-gray-500">/{{ $total }}</span>
                    @endif
                </span>
            @endif
        </div>
    @endif
    
    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden {{ $heightClass }}">
        <div class="{{ $colorClass }} {{ $heightClass }} rounded-full transition-all duration-500 ease-out {{ $animated ? 'progress-bar-animated' : '' }}"
             style="width: {{ $percentage }}%"
             data-percentage="{{ $percentage }}"
             role="progressbar"
             aria-valuenow="{{ $current }}"
             aria-valuemin="0"
             aria-valuemax="{{ $total }}">
        </div>
    </div>
</div>

<style>
@keyframes progressPulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

.progress-bar-animated {
    animation: progressPulse 2s ease-in-out infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('[role="progressbar"]');
    
    progressBars.forEach(bar => {
        // Animate from 0 to target percentage on load
        const targetPercentage = bar.dataset.percentage;
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.width = targetPercentage + '%';
        }, 100);
    });
});
</script>