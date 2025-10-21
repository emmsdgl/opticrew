{{-- resources/views/components/linechart.blade.php --}}

@props([
    'title' => 'Chart Title',
    'currentValue' => 0,
    'changeValue' => 0,
    'changePercent' => 0,
    'chartId' => 'chart-' . uniqid(),
    'chartData' => [],
    'chartLabels' => [],
    'chartColor' => '#60a5fa',
    'gradientStart' => 'rgba(96, 165, 250, 0.2)',
    'gradientEnd' => 'rgba(96, 165, 250, 0)',
    'height' => '100%',
    'dateRange' => 'Jan - Dec',
    'showTotal' => true,
    'showHeader' => true,
    'dropdownId' => null,
    'allData' => null,
])

<div class="w-full h-full flex flex-col" data-chart-container="{{ $chartId }}">
    @if($showHeader)
    <div class="p-4 sm:p-6 flex flex-row justify-between">
        <div class="flex flex-col">

            {{-- Current Value --}}
            <div class="mb-2">
                                <div class="text-xs text-slate-400 dark:text-slate-300 mb-1">Total Hours Worked</div>
    
                <span class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white" data-current-value>
                    <span class="value-text">{{ gmdate('H:i:s', $currentValue) }}</span>
                </span>
            </div>
    
            {{-- Change Indicator --}}
            <div class="flex items-center gap-2 mb-4" data-change-indicator>
                
                @php $isPositive = $changeValue >= 0; @endphp
                <span class="{{ $isPositive ? 'text-blue-500 dark:text-blue-400' : 'text-red-500 dark:text-red-400' }} text-sm font-medium change-value">
                    {{ $isPositive ? '↑' : '↓' }} {{ gmdate('H:i:s', abs($changeValue)) }}
                </span>
    
                <span class="{{ $isPositive ? 'text-blue-500 dark:text-blue-400' : 'text-red-500 dark:text-red-400' }} text-sm change-percent">
                    ({{ $isPositive ? '+' : '' }}{{ number_format($changePercent, 2) }}%)
                </span>
            </div>
        </div>

        @if($showTotal)
        {{-- Total Badge --}}
        <div class="flex flex-col text-center items-center text-white dark:text-gray-900 rounded-xl">
            <div class="text-left">
                <div class="text-xs text-slate-400 dark:text-slate-300 mb-1 text-right">Idle Hours</div>

                <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white total-value mb-2 text-right">
                    {{ gmdate('H:i', $currentValue + $changeValue) }}
                </div>
                <div class="text-xs text-slate-400 dark:text-slate-300">As of {{ now()->format('F') }} (Unproductive Hours)</div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="flex-1 px-4 sm:px-6 pb-4 sm:pb-6 min-h-0">
        <div class="relative w-full h-full">
            <canvas id="{{ $chartId }}"></canvas>
        </div>

        <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500 mt-2 px-2" data-date-range>
            @foreach(explode(' - ', $dateRange) as $date)
                <span>{{ $date }}</span>
            @endforeach
        </div>
    </div>
</div>

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush
@endonce

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $chartId }}');
    if (!ctx) return;

    const container = document.querySelector('[data-chart-container="{{ $chartId }}"]');
    let chartData = @json($chartData);
    let chartLabels = @json($chartLabels);
    const allDataSets = @json($allData);

    // Convert seconds → hh:mm:ss
    function formatTime(seconds) {
        const hrs = Math.floor(seconds / 3600);
        const mins = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${hrs.toString().padStart(2,'0')}:${mins.toString().padStart(2,'0')}:${secs.toString().padStart(2,'0')}`;
    }

    const isDarkMode = () => document.documentElement.classList.contains('dark');
    const getGridColor = () => isDarkMode() ? 'rgba(75, 85, 99, 0.2)' : 'rgba(229, 231, 235, 0.5)';
    const getTextColor = () => isDarkMode() ? 'rgba(156, 163, 175, 1)' : 'rgba(107, 114, 128, 1)';

    const createGradient = () => {
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, ctx.height || 300);
        gradient.addColorStop(0, '{{ $gradientStart }}');
        gradient.addColorStop(1, '{{ $gradientEnd }}');
        return gradient;
    };

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartData,
                borderColor: '{{ $chartColor }}',
                backgroundColor: createGradient(),
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '{{ $chartColor }}',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2,
                spanGaps: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: isDarkMode() ? 'rgba(31, 41, 55, 0.95)' : 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    displayColors: false,
                    callbacks: {
                        label: ctx => formatTime(ctx.parsed.y)
                    }
                }
            },
            scales: {
                x: {
                    display: false,
                    grid: { display: false, color: getGridColor() },
                    ticks: { color: getTextColor() }
                },
                y: {
                    display: false,
                    grid: { display: false, color: getGridColor() },
                    ticks: {
                        color: getTextColor(),
                        callback: value => formatTime(value)
                    }
                }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });

    // Function to update chart dynamically
    window.updateChartData_{{ str_replace('-', '_', $chartId) }} = function(period) {
        if (!allDataSets || !allDataSets[period]) return;
        const data = allDataSets[period];

        chart.data.labels = data.labels;
        chart.data.datasets[0].data = data.values;
        chart.update('active');

        if (container) {
            const valueText = container.querySelector('.value-text');
            const changeValue = container.querySelector('.change-value');
            const changePercent = container.querySelector('.change-percent');
            const totalValue = container.querySelector('.total-value');
            const dateRange = container.querySelector('[data-date-range]');

            if (valueText) valueText.textContent = formatTime(data.currentValue);
            if (changeValue) {
                const isPositive = data.changeValue >= 0;
                changeValue.className = `text-sm font-medium change-value ${isPositive ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400'}`;
                changeValue.textContent = `${isPositive ? '↑' : '↓'} ${formatTime(Math.abs(data.changeValue))}`;
            }
            if (changePercent) {
                const isPositive = data.changePercent >= 0;
                changePercent.className = `text-sm change-percent ${isPositive ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400'}`;
                changePercent.textContent = `(${isPositive ? '+' : ''}${data.changePercent.toFixed(2)}%)`;
            }
            if (totalValue) totalValue.textContent = formatTime(data.currentValue + data.changeValue);
            if (dateRange && data.dateRange) {
                const dates = data.dateRange.split(' - ');
                dateRange.innerHTML = dates.map(date => `<span>${date}</span>`).join('');
            }
        }
    };

    @if($dropdownId)
    const dropdown = document.getElementById('{{ $dropdownId }}');
    if (dropdown) {
        dropdown.addEventListener('change', e => {
            const period = e.target.value;
            window.updateChartData_{{ str_replace('-', '_', $chartId) }}(period);
        });
    }
    @endif

    const observer = new MutationObserver(() => {
        chart.options.scales.x.grid.color = getGridColor();
        chart.options.scales.y.grid.color = getGridColor();
        chart.options.scales.x.ticks.color = getTextColor();
        chart.options.scales.y.ticks.color = getTextColor();
        chart.options.plugins.tooltip.backgroundColor = isDarkMode()
            ? 'rgba(31, 41, 55, 0.95)'
            : 'rgba(0, 0, 0, 0.8)';
        chart.data.datasets[0].backgroundColor = createGradient();
        chart.update();
    });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});
</script>
@endpush
