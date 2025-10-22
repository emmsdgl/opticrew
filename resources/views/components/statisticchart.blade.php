@props([
    'total' => 0,
    'growthRate' => 0,
    'growthTrend' => 'up',
    'categories' => [],
    'chartId' => null,
    'animateOnLoad' => true,
    'chartSize' => 270,
    'ringThickness' => 14,
    'class' => ''
])

@php
    $chartId = $chartId ?? 'chart-' . uniqid();
    
    // Default colors if not provided
    $defaultColors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
    
    // Ensure each category has a color
    foreach($categories as $index => &$category) {
        if (!isset($category['color']) || empty($category['color'])) {
            $category['color'] = $defaultColors[$index % count($defaultColors)];
        }
    }
    unset($category);
@endphp

<div {{ $attributes->merge(['class' => 'w-full ' . $class]) }}>
    <div class="bg-white dark:bg-transparent rounded-xl p-6 transition-colors duration-200">
        <!-- Chart Container -->
        <div class="relative flex justify-center items-center mb-6" style="height: {{ $chartSize }}px;">
            <svg 
                id="{{ $chartId }}-svg" 
                class="transform -rotate-90" 
                width="{{ $chartSize }}" 
                height="{{ $chartSize }}" 
                viewBox="0 0 {{ $chartSize }} {{ $chartSize }}"
            >
                <!-- Background rings -->
                <g id="{{ $chartId }}-bg-rings"></g>
                
                <!-- Animated data rings -->
                <g id="{{ $chartId }}-data-rings"></g>
            </svg>
            
            <!-- Center Content -->
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <div 
                    id="{{ $chartId }}-center-value" 
                    class="text-3xl font-bold text-gray-900 dark:text-white transition-all duration-300"
                >
                    0
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Service Orders
                </div>
                <div class="mt-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium transition-colors duration-200
                        {{ $growthTrend === 'up' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                        {{ $growthRate > 0 ? '+' : '' }}{{ number_format($growthRate, 1) }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Category List -->
        <div class="space-y-1">
            @foreach($categories as $index => $category)
                <div 
                    class="flex items-center justify-between p-2 rounded-lg transition-all duration-200 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 group"
                    data-chart-id="{{ $chartId }}"
                    data-category-index="{{ $index }}"
                >
                    <div class="flex items-center">
                        <!-- Color Indicator -->
                        <div 
                            class="w-3 h-3 mr-3 rounded-full flex-shrink-0 transition-transform duration-200 group-hover:scale-125" 
                            style="background-color: {{ $category['color'] }}"
                        ></div>
                        
                        <!-- Category Name -->
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">
                            {{ $category['name'] }}
                        </span>
                    </div>
                    
                    <!-- Value -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ number_format($category['value']) }}
                        </span>
                        
                        @if(isset($category['percentage']))
                            <span class="px-2 py-1 text-xs font-medium rounded transition-colors duration-200
                                {{ ($category['trend'] ?? 'up') === 'up' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
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
        .chart-ring-bg {
            transition: all 0.3s ease;
        }
        
        .chart-ring-data {
            transition: all 0.3s ease;
            stroke-dashoffset: 0;
        }
    </style>
    @endpush
@endonce

@once
    @push('scripts')
    <script>
        class StatisticChart {
            constructor(chartId, config) {
                this.chartId = chartId;
                this.total = config.total;
                this.categories = config.categories;
                this.chartSize = config.chartSize;
                this.ringThickness = config.ringThickness;
                this.animateOnLoad = config.animateOnLoad;
                
                this.centerX = this.chartSize / 2;
                this.centerY = this.chartSize / 2;
                this.baseRadius = (this.chartSize / 2) - (this.ringThickness / 2) - 10;
                
                this.bgRingsContainer = document.getElementById(`${chartId}-bg-rings`);
                this.dataRingsContainer = document.getElementById(`${chartId}-data-rings`);
                this.centerValueEl = document.getElementById(`${chartId}-center-value`);
                this.categoryItems = document.querySelectorAll(`[data-chart-id="${chartId}"]`);
                
                if (!this.bgRingsContainer || !this.dataRingsContainer || !this.centerValueEl) {
                    console.error('Chart elements not found');
                    return;
                }
                
                this.init();
            }
            
            init() {
                this.calculateRings();
                this.renderBackgroundRings();
                this.renderDataRings();
                this.attachEventListeners();
                
                if (this.animateOnLoad) {
                    this.animate();
                }
            }
            
            calculateRings() {
                const ringSpacing = 8;
                const numCategories = this.categories.length;
                
                this.rings = this.categories.map((category, index) => {
                    const radius = this.baseRadius - (index * (this.ringThickness + ringSpacing));
                    const circumference = 2 * Math.PI * radius;
                    const percentage = (category.value / this.total) * 100;
                    const offset = circumference - (circumference * percentage / 100);
                    
                    return {
                        ...category,
                        radius,
                        circumference,
                        offset,
                        percentage
                    };
                });
            }
            
            renderBackgroundRings() {
                this.rings.forEach((ring, index) => {
                    const circle = this.createCircle(ring.radius, '#E5E7EB', this.ringThickness);
                    circle.classList.add('chart-ring-bg', 'dark:stroke-gray-700');
                    this.bgRingsContainer.appendChild(circle);
                });
            }
            
            renderDataRings() {
                this.rings.forEach((ring, index) => {
                    const circle = this.createCircle(ring.radius, ring.color, this.ringThickness);
                    circle.classList.add('chart-ring-data');
                    circle.setAttribute('stroke-linecap', 'round');
                    circle.setAttribute('stroke-dasharray', ring.circumference);
                    circle.setAttribute('stroke-dashoffset', ring.circumference);
                    circle.setAttribute('data-ring-index', index);
                    circle.style.cursor = 'pointer';
                    
                    this.dataRingsContainer.appendChild(circle);
                });
            }
            
            createCircle(radius, color, strokeWidth) {
                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('cx', this.centerX);
                circle.setAttribute('cy', this.centerY);
                circle.setAttribute('r', radius);
                circle.setAttribute('fill', 'none');
                circle.setAttribute('stroke', color);
                circle.setAttribute('stroke-width', strokeWidth);
                return circle;
            }
            
            animate() {
                // Animate center value
                this.animateValue(0, this.total, 1500);
                
                // Animate rings with stagger
                const dataRings = this.dataRingsContainer.querySelectorAll('.chart-ring-data');
                dataRings.forEach((ring, index) => {
                    const targetOffset = this.rings[index].offset;
                    
                    setTimeout(() => {
                        ring.style.transition = 'stroke-dashoffset 1s cubic-bezier(0.4, 0, 0.2, 1)';
                        ring.setAttribute('stroke-dashoffset', targetOffset);
                    }, 100 + (index * 150));
                });
            }
            
            animateValue(start, end, duration) {
                const startTime = performance.now();
                
                const updateValue = (currentTime) => {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // Easing function
                    const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                    const currentValue = Math.floor(start + (end - start) * easeOutQuart);
                    
                    this.centerValueEl.textContent = currentValue.toLocaleString();
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateValue);
                    }
                };
                
                requestAnimationFrame(updateValue);
            }
            
            attachEventListeners() {
                const dataRings = this.dataRingsContainer.querySelectorAll('.chart-ring-data');
                
                // Ring hover
                dataRings.forEach((ring, index) => {
                    ring.addEventListener('mouseenter', () => this.highlightCategory(index));
                    ring.addEventListener('mouseleave', () => this.resetHighlight());
                });
                
                // Category item hover
                this.categoryItems.forEach((item, index) => {
                    item.addEventListener('mouseenter', () => this.highlightCategory(index));
                    item.addEventListener('mouseleave', () => this.resetHighlight());
                });
            }
            
            highlightCategory(index) {
                const category = this.categories[index];
                const dataRings = this.dataRingsContainer.querySelectorAll('.chart-ring-data');
                
                // Update center value
                this.centerValueEl.style.transform = 'scale(0.95)';
                this.centerValueEl.style.opacity = '0.7';
                
                setTimeout(() => {
                    this.centerValueEl.textContent = category.value.toLocaleString();
                    this.centerValueEl.style.transform = 'scale(1.05)';
                    this.centerValueEl.style.opacity = '1';
                    
                    setTimeout(() => {
                        this.centerValueEl.style.transform = 'scale(1)';
                    }, 200);
                }, 150);
                
                // Highlight rings
                dataRings.forEach((ring, i) => {
                    if (i === index) {
                        ring.style.opacity = '1';
                        ring.style.strokeWidth = this.ringThickness;
                        ring.style.filter = `drop-shadow(0 0 10px ${category.color})`;
                    } else {
                        ring.style.opacity = '0.3';
                    }
                });
                
                // Highlight category item
                this.categoryItems.forEach((item, i) => {
                    if (i === index) {
                        item.style.transform = 'translateX(4px)';
                        item.style.backgroundColor = 'rgba(0, 0, 0, 0.05)';
                    } else {
                        item.style.opacity = '0.5';
                    }
                });
            }
            
            resetHighlight() {
                const dataRings = this.dataRingsContainer.querySelectorAll('.chart-ring-data');
                
                // Reset center value
                this.centerValueEl.style.transform = 'scale(0.95)';
                this.centerValueEl.style.opacity = '0.7';
                
                setTimeout(() => {
                    this.centerValueEl.textContent = this.total.toLocaleString();
                    this.centerValueEl.style.transform = 'scale(1.05)';
                    this.centerValueEl.style.opacity = '1';
                    
                    setTimeout(() => {
                        this.centerValueEl.style.transform = 'scale(1)';
                    }, 200);
                }, 150);
                
                // Reset rings
                dataRings.forEach(ring => {
                    ring.style.opacity = '1';
                    ring.style.strokeWidth = this.ringThickness;
                    ring.style.filter = 'none';
                });
                
                // Reset category items
                this.categoryItems.forEach(item => {
                    item.style.transform = '';
                    item.style.opacity = '1';
                    item.style.backgroundColor = '';
                });
            }
        }
        
        if (typeof window.statisticCharts === 'undefined') {
            window.statisticCharts = {};
        }
    </script>
    @endpush
@endonce

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartId = '{{ $chartId }}';
        
        if (!window.statisticCharts[chartId]) {
            window.statisticCharts[chartId] = new StatisticChart(chartId, {
                total: {{ $total }},
                categories: @json($categories),
                chartSize: {{ $chartSize }},
                ringThickness: {{ $ringThickness }},
                animateOnLoad: {{ $animateOnLoad ? 'true' : 'false' }}
            });
        }
    });
</script>