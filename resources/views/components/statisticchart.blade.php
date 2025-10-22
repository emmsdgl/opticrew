@props([
    'title' => 'Product Statistic',
    'total' => 0,
    'growthRate' => 0,
    'growthTrend' => 'up',
    'categories' => [],
    'chartId' => null,
    'animateOnLoad' => true,
    'showDropdown' => true,
    'dropdownOptions' => ['Today', 'This Week', 'This Month', 'This Year'],
    'chartSize' => 390,
    'ringThickness' => 17,
    'class' => ''
])

@php
    $chartId = $chartId ?? 'chart-' . uniqid();
    $totalFormatted = number_format($total);
    $growthRateFormatted = number_format($growthRate, 2);
    $padding = 5;
    $viewBoxSize = $chartSize + ($padding * 2);
@endphp

<div {{ $attributes->merge(['class' => 'w-full max-w-sm mx-auto ' . $class]) }}>
    <div class="p-6 transition-colors duration-200">
        <!-- Chart Section -->
        <div class="relative flex justify-center items-center mb-8 h-56 w-full">
            <!-- SVG Chart -->
            <svg class="absolute w-full h-64" viewBox="0 0 {{ $viewBoxSize }} {{ $viewBoxSize }}">
                <!-- Background circles -->
                @php
                    $centerPoint = $viewBoxSize / 2;
                    // Start with a larger radius to accommodate text
                    $maxRadius = ($chartSize / 2) - ($ringThickness / 2);
                    // Minimum inner radius to leave space for text (about 80px radius for text area)
                    $minTextRadius = 85;
                @endphp
                
                @foreach($categories as $index => $category)
                    @php
                        $radius = $maxRadius - ($index * ($ringThickness + 10));
                        // Ensure we don't go smaller than the minimum text radius
                        if ($radius < $minTextRadius) {
                            $radius = $minTextRadius + ($ringThickness * (count($categories) - $index - 1));
                        }
                    @endphp
                    <circle 
                        cx="{{ $centerPoint }}" 
                        cy="{{ $centerPoint }}" 
                        r="{{ $radius }}" 
                        fill="none" 
                        stroke="#f3f4f6" 
                        stroke-width="{{ $ringThickness }}" 
                        class="dark:stroke-gray-700 transition-colors duration-200"
                    />
                @endforeach
                
                <!-- Data circles (will be drawn by JS) -->
                <g id="{{ $chartId }}-rings"></g>
            </svg>
            
            <!-- Center stats -->
            <div class="absolute text-center pointer-events-none max-w-[140px]">
                <div 
                    class="text-4xl font-bold text-gray-800 dark:text-gray-100 transition-all duration-300" 
                    id="{{ $chartId }}-value"
                >
                    {{ $totalFormatted }}
                </div>
                <div class="text-sm text-gray-400 dark:text-gray-500 mt-1">Service Orders</div>
                <div class="inline-block mt-2 px-2 py-0.5 text-xs rounded transition-colors duration-200 {{ $growthTrend === 'up' ? 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300' : 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300' }}">
                    {{ $growthRate > 0 ? '+' : '' }}{{ $growthRateFormatted }}%
                </div>
            </div>
        </div>

        <!-- Category List -->
        <div class="space-y-1">
            @foreach($categories as $index => $category)
                <div 
                    class="flex items-center justify-between p-3 pb-1 rounded-lg transition-all duration-200 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 category-item"
                    data-chart-id="{{ $chartId }}"
                    data-category-index="{{ $index }}"
                    data-value="{{ $category['value'] }}"
                    data-name="{{ $category['name'] }}"
                >
                    <div class="flex items-center gap-3">
                        <!-- Category Name -->
                        <span class="text-gray-700 dark:text-gray-300 text-sm">
                            {{ $category['name'] }}
                        </span>
                    </div>
                    
                    <!-- Values -->
                    <div class="flex items-center gap-3">
                        <span class="text-gray-800 dark:text-gray-200 text-sm">
                            {{ number_format($category['value']) }}
                        </span>
                        
                        @if(isset($category['percentage']))
                            <span class="px-2 py-0.5 text-xs font-semibold rounded transition-colors duration-200 {{ ($category['trend'] ?? 'up') === 'up' ? 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300' : 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300' }}">
                                {{ $category['percentage'] > 0 ? '+' : '' }}{{ number_format($category['percentage'], 1) }}%
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@once
    @push('styles')
    <style>
        .chart-component-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%236B7280' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            padding-right: 2rem;
        }

        @media (prefers-color-scheme: dark) {
            .chart-component-select {
                background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%239CA3AF' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            }
        }
        
        .chart-ring {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    @endpush
@endonce

@once
    @push('scripts')
    <script>
        class ProductStatisticChart {
            constructor(chartId, config) {
                this.chartId = chartId;
                this.config = config;
                this.categories = config.categories;
                this.total = config.total;
                this.animateOnLoad = config.animateOnLoad;
                this.chartSize = config.chartSize;
                this.ringThickness = config.ringThickness;
                this.padding = 30;
                this.minTextRadius = 85;
                
                this.chartRingsContainer = document.getElementById(`${chartId}-rings`);
                this.totalValueEl = document.getElementById(`${chartId}-value`);
                this.categoryElements = document.querySelectorAll(`[data-chart-id="${chartId}"]`);
                
                this.init();
            }
            
            init() {
                this.createRings();
                this.attachEventListeners();
                
                if (this.animateOnLoad) {
                    this.animateValue();
                }
            }
            
            createRings() {
                const viewBoxSize = this.chartSize + (this.padding * 2);
                const centerPoint = viewBoxSize / 2;
                // Match the PHP calculation exactly
                const maxRadius = (this.chartSize / 2) - (this.ringThickness / 2);
                
                this.rings = this.categories.map((cat, index) => {
                    const percentage = (cat.value / this.total) * 100;
                    let radius = maxRadius - (index * (this.ringThickness + 10));
                    
                    // Ensure we don't go smaller than the minimum text radius
                    if (radius < this.minTextRadius) {
                        radius = this.minTextRadius + (this.ringThickness * (this.categories.length - index - 1));
                    }
                    
                    const circumference = 2 * Math.PI * radius;
                    const strokeDashoffset = circumference - (percentage / 100) * circumference;
                    
                    return {
                        ...cat,
                        radius,
                        circumference,
                        strokeDashoffset,
                        percentage: percentage.toFixed(1)
                    };
                });
                
                this.rings.forEach((ring, index) => {
                    const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    circle.setAttribute('cx', centerPoint);
                    circle.setAttribute('cy', centerPoint);
                    circle.setAttribute('r', ring.radius);
                    circle.setAttribute('fill', 'none');
                    circle.setAttribute('stroke', ring.color);
                    circle.setAttribute('stroke-width', this.ringThickness);
                    circle.setAttribute('stroke-linecap', 'round');
                    circle.setAttribute('stroke-dasharray', ring.circumference);
                    circle.setAttribute('transform', `rotate(-90 ${centerPoint} ${centerPoint})`);
                    circle.setAttribute('class', 'chart-ring');
                    circle.setAttribute('data-ring-index', index);
                    circle.style.cursor = 'pointer';
                    
                    if (this.animateOnLoad) {
                        circle.setAttribute('stroke-dashoffset', ring.circumference);
                        setTimeout(() => {
                            circle.style.transition = 'stroke-dashoffset 1s cubic-bezier(0.4, 0, 0.2, 1)';
                            circle.setAttribute('stroke-dashoffset', ring.strokeDashoffset);
                        }, 100 + (index * 200));
                    } else {
                        circle.setAttribute('stroke-dashoffset', ring.strokeDashoffset);
                    }
                    
                    this.chartRingsContainer.appendChild(circle);
                    
                    // Add event listeners to rings
                    circle.addEventListener('mouseenter', () => this.highlightCategory(index));
                    circle.addEventListener('mouseleave', () => this.resetHighlight());
                });
            }
            
            attachEventListeners() {
                this.categoryElements.forEach((el, index) => {
                    el.addEventListener('mouseenter', () => this.highlightCategory(index));
                    el.addEventListener('mouseleave', () => this.resetHighlight());
                });
            }
            
            highlightCategory(index) {
                const category = this.categories[index];
                const rings = this.chartRingsContainer.querySelectorAll('.chart-ring');
                
                // Animate center value
                this.totalValueEl.style.transform = 'scale(0.9)';
                this.totalValueEl.style.opacity = '0.5';
                
                setTimeout(() => {
                    this.totalValueEl.textContent = category.value.toLocaleString();
                    this.totalValueEl.style.transform = 'scale(1.1)';
                    this.totalValueEl.style.opacity = '1';
                    
                    setTimeout(() => {
                        this.totalValueEl.style.transform = 'scale(1)';
                    }, 150);
                }, 150);
                
                // Highlight rings
                rings.forEach((ring, i) => {
                    if (i === index) {
                        ring.style.opacity = '1';
                        ring.style.strokeWidth = this.ringThickness + 2;
                        ring.style.filter = `drop-shadow(0 0 8px ${category.color})`;
                    } else {
                        ring.style.opacity = '0.3';
                        ring.style.filter = 'none';
                    }
                });
                
                // Highlight category
                this.categoryElements.forEach((cat, i) => {
                    if (i === index) {
                        cat.style.transform = 'translateX(4px)';
                    } else {
                        cat.style.opacity = '0.5';
                    }
                });
            }
            
            resetHighlight() {
                const rings = this.chartRingsContainer.querySelectorAll('.chart-ring');
                
                // Reset center value
                this.totalValueEl.style.transform = 'scale(0.9)';
                this.totalValueEl.style.opacity = '0.5';
                
                setTimeout(() => {
                    this.totalValueEl.textContent = this.total.toLocaleString();
                    this.totalValueEl.style.transform = 'scale(1.1)';
                    this.totalValueEl.style.opacity = '1';
                    
                    setTimeout(() => {
                        this.totalValueEl.style.transform = 'scale(1)';
                    }, 150);
                }, 150);
                
                // Reset rings
                rings.forEach(ring => {
                    ring.style.opacity = '1';
                    ring.style.strokeWidth = this.ringThickness;
                    ring.style.filter = 'none';
                });
                
                // Reset categories
                this.categoryElements.forEach(cat => {
                    cat.style.transform = '';
                    cat.style.opacity = '1';
                });
            }
            
            animateValue() {
                let currentValue = 0;
                const targetValue = this.total;
                const duration = 1500;
                const steps = 60;
                const increment = targetValue / steps;
                const stepDuration = duration / steps;
                
                const counter = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= targetValue) {
                        currentValue = targetValue;
                        clearInterval(counter);
                    }
                    this.totalValueEl.textContent = Math.floor(currentValue).toLocaleString();
                }, stepDuration);
            }
        }
        
        // Auto-initialize all charts on page
        document.addEventListener('DOMContentLoaded', function() {
            window.productStatisticCharts = window.productStatisticCharts || {};
        });
    </script>
    @endpush
@endonce

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartId = '{{ $chartId }}';
        
        if (!window.productStatisticCharts[chartId]) {
            window.productStatisticCharts[chartId] = new ProductStatisticChart(chartId, {
                categories: @json($categories),
                total: {{ $total }},
                animateOnLoad: {{ $animateOnLoad ? 'true' : 'false' }},
                chartSize: {{ $chartSize }},
                ringThickness: {{ $ringThickness }}
            });
        }
    });
</script>