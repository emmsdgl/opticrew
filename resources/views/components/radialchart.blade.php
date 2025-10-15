@props([
    'chartData' => [
        'done' => 70,
        'inProgress' => 20,
        'toDo' => 10
    ],
    'chartId' => 'radial-chart',
    'title' => 'Last 7 days',
    'showDropdown' => true,
    'labels' => [
        'done' => 'Done',
        'inProgress' => 'In progress',
        'toDo' => 'To do'
    ]
])

<div class="mx-auto p-6 flex flex-col items-center space-y-10">

    <!-- Radial Chart -->
    <div class="py-6" id="{{ $chartId }}"></div>

    <!-- Legend -->
    <div class="flex flex-row justify-center gap-6 items-center border-gray-200 border-t dark:border-gray-700 pt-5 w-full">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-[#2A6DFA]"></span>
            <span class="text-xs text-gray-700 dark:text-gray-300">{{ $labels['done'] }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-[#2AC9FA]"></span>
            <span class="text-xs text-gray-700 dark:text-gray-300">{{ $labels['inProgress'] }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-[#002ABC]"></span>
            <span class="text-xs text-gray-700 dark:text-gray-300">{{ $labels['toDo'] }}</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData);
        const chartId = @json($chartId);
        
        if (typeof ApexCharts !== 'undefined') {
            const options = {
                series: [chartData.done || 0, chartData.inProgress || 0, chartData.toDo || 0],
                chart: {
                    height: 320,
                    type: 'radialBar',
                },
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: {
                                fontSize: '16px',
                            },
                            value: {
                                fontSize: '14px',
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        },
                        hollow: {
                            size: '60%',
                        }
                    }
                },
                labels: ['Done', 'In progress', 'To do'],
                colors: ['#3B82F6', '#2DD4BF', '#FDB462'],
                legend: {
                    show: false
                }
            };

            const chart = new ApexCharts(document.querySelector('#' + chartId), options);
            chart.render();
        } else {
            console.error('ApexCharts is not loaded');
        }
    });
</script>
@endpush