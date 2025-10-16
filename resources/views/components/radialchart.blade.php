@props([
    'chartData' => [
        'done' => '',
        'inProgress' => '',
        'toDo' => ''
    ],
    'chartId' => 'radial-chart',
    'title' => 'Last 7 days',
    'showDropdown' => true,
    'labels' => [
        'done' => 'Done',
        'inProgress' => 'In progress',
        'toDo' => 'To do'
    ],
    'colors' => [
        'done' => '#2A6DFA',     
        'inProgress' => '#2AC9FA', 
        'toDo' => '#0028B3'       
    ]
])

<div class="relative w-full h-full flex flex-col">
    <!-- Chart Container with Gradient Background -->
    <div class="relative flex-1 min-h-0">
        <!-- Subtle gradient background circle -->
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-full h-full max-w-xs max-h-xs rounded-full bg-gradient-to-br from-indigo-50 via-pink-50 to-orange-50 
                        dark:from-indigo-950/20 dark:via-pink-950/20 dark:to-orange-950/20 opacity-40"></div>
        </div>

        <!-- ApexCharts Radial Chart -->
        <div class="relative w-full h-full" id="{{ $chartId }}"></div>

        <!-- Custom Center Label (overlays the chart) -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100" 
                     id="{{ $chartId }}-total">0</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Tasks</div>
            </div>
        </div>
    </div>

    <!-- Floating Info Card with a unique ID -->
    <div class="absolute top-4 left-4 bg-gray-900 dark:bg-gray-800 text-white rounded-lg px-3 py-2 
                shadow-lg backdrop-blur-sm bg-opacity-90 dark:bg-opacity-95 z-5 flex items-center gap-2"
         id="{{ $chartId }}-info-card">
        <div class="flex items-center gap-1.5">
            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
            <span class="text-xs font-medium">Goal Sessions</span>
        </div>
        <div class="pl-2 flex items-baseline gap-2">
            <span class="text-sm font-bold" id="{{ $chartId }}-done-value">0</span>
            <span class="text-xs text-gray-400">/</span>
            <span class="text-xs text-gray-400" id="{{ $chartId }}-done-percent">0%</span>
        </div>
    </div>

    <!-- Legend -->
    <div class="flex flex-row justify-center gap-4 items-center border-gray-200
                dark:border-gray-700 mb-4 w-full flex-shrink-0">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $colors['done'] }}"></span>
            <span class="text-xs text-gray-700 dark:text-gray-300">{{ $labels['done'] }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $colors['inProgress'] }}"></span>
            <span class="text-xs text-gray-700 dark:text-gray-300">{{ $labels['inProgress'] }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $colors['toDo'] }}"></span>
            <span class="text-xs text-gray-700 dark:text-gray-300">{{ $labels['toDo'] }}</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData);
        const chartId = @json($chartId);
        const colors = @json($colors);
        const labels = @json($labels);
        
        const chartEl = document.getElementById(chartId);
        if (!chartEl) return;

        if (typeof ApexCharts !== 'undefined') {
            if (chartEl.__apexchart__) {
                chartEl.__apexchart__.destroy();
            }

            const series = [
                chartData.done || 0, 
                chartData.inProgress || 0, 
                chartData.toDo || 0
            ];
            const total = series.reduce((a, b) => a + b, 0);

            const options = {
                series: series,
                chart: {
                    height: '100%',
                    width: '100%',
                    type: 'radialBar',
                    animations: { enabled: true, speed: 800 },
                    events: {
                        dataPointMouseEnter: function(event, chartContext, config) {
                            const seriesIndex = config.seriesIndex;
                            const value = series[seriesIndex];
                            const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                            const labelNames = ['done', 'inProgress', 'toDo'];
                            const labelKey = labelNames[seriesIndex];
                            const labelText = labels[labelKey];
                            
                            const card = document.getElementById(`${chartId}-info-card`);
                            if (!card) return;
                            
                            card.querySelector('.text-xs.font-medium').textContent = labelText;
                            card.querySelector('.text-sm.font-bold').textContent = Math.round(value);
                            card.querySelector('.text-xs.text-gray-400:last-child').textContent = percent + '%';
                            card.style.transform = 'scale(1.05)';
                            card.style.transition = 'transform 0.2s ease';
                        },
                        dataPointMouseLeave: function(event, chartContext, config) {
                            const card = document.getElementById(`${chartId}-info-card`);
                            if (!card) return;

                            const doneValue = series[0];
                            const donePercent = total > 0 ? Math.round((doneValue / total) * 100) : 0;
                            
                            card.querySelector('.text-xs.font-medium').textContent = 'Goal Sessions';
                            card.querySelector('.text-sm.font-bold').textContent = Math.round(doneValue);
                            card.querySelector('.text-xs.text-gray-400:last-child').textContent = donePercent + '%';
                            card.style.transform = 'scale(1)';
                        }
                    }
                },
                plotOptions: {
                    radialBar: {
                        offsetY: 0,
                        startAngle: 0,
                        endAngle: 270,
                        hollow: { margin: 5, size: '40%', background: 'transparent' },
                        track: { background: '#f3f4f6', strokeWidth: '97%', margin: 5 },
                        dataLabels: { show: false }
                    }
                },
                colors: [colors.done, colors.inProgress, colors.toDo],
                labels: [labels.done, labels.inProgress, labels.toDo],
                legend: { show: false },
                stroke: { lineCap: 'round' }
            };

            const chart = new ApexCharts(chartEl, options);
            chart.render();

            document.getElementById(chartId + '-total').textContent = total;
            document.getElementById(chartId + '-done-value').textContent = Math.round(series[0]);
            
            const donePercent = total > 0 ? Math.round((series[0] / total) * 100) : 0;
            document.getElementById(chartId + '-done-percent').textContent = donePercent + '%';

            chartEl.__apexchart__ = chart;
        } else {
            console.error('ApexCharts is not loaded');
        }
    });
</script>
@endpush