<x-layouts.general-employer :title="'Analytics'">
    <div class="flex flex-col lg:flex-row md:gap-4 w-full">
        <!-- Left Panel - Main Content -->
        <div class="flex flex-col gap-4 md:gap-6 w-full rounded-lg p-3 h-fit lg:w-2/3 md:p-3">
            <!-- Card KPI Statistics Summary -->
            <div class="w-full flex flex-row justify-between">
                <x-labelwithvalue label="Analytics Summary" count="" />

                <!-- Export Report Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fa-solid fa-download"></i>
                        Export Report
                        <i class="fa-solid fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                         style="display: none;">
                        <div class="py-1">
                            <!-- View Reports Dashboard -->
                            <a href="{{ route('admin.reports.index') }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fi fi-rr-chart-histogram text-blue-600 dark:text-blue-400"></i>
                                <span>View All Reports</span>
                            </a>

                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <!-- Client Reports -->
                            <a href="{{ route('admin.reports.clients') }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fi fi-rr-users-alt text-purple-600 dark:text-purple-400"></i>
                                <span>Client Revenue Report</span>
                            </a>

                            <a href="{{ route('admin.reports.clients.export', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fi fi-rr-download text-green-600 dark:text-green-400"></i>
                                <span>Export Client Report (CSV)</span>
                            </a>

                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <!-- Payroll Reports -->
                            <a href="{{ route('admin.reports.payroll') }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fi fi-rr-money text-green-600 dark:text-green-400"></i>
                                <span>Employee Payroll Report</span>
                            </a>

                            <a href="{{ route('admin.reports.payroll.export', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fi fi-rr-download text-green-600 dark:text-green-400"></i>
                                <span>Export Payroll Report (CSV)</span>
                            </a>

                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <!-- Service Performance Report -->
                            <a href="{{ route('admin.reports.service') }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fi fi-rr-chart-pie-alt text-orange-600 dark:text-orange-400"></i>
                                <span>Service Performance Report</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full rounded-lg h-fit sm:h-fit md:h-fit lg:h-fit">
                <!-- Inner Up - Performance KPI Cards Summary -->
                <div class="w-full rounded-lg p-3 md:p-4 flex-none h-fit sm:h-fit md:h-fit lg:h-fit">
                    {{-- KPI Cards from Database --}}
                    <x-kpicardcontainer :cards="$kpiCards" columns="3" />
                </div>
            </div>

            <!-- Productivity Rate Section with Filter -->
            <div x-data="{
                selectedPeriod: 'This Year',
                performanceData: {{ Js::from($performanceData) }},
                updateChart(period) {
                    this.selectedPeriod = period;
                    this.$nextTick(() => {
                        window.updateProductivityChart(period);
                    });
                }
            }">
                <div class="flex flex-row justify-between">
                    <x-labelwithvalue label="Productivity Rate" count="" />
                    <div x-data="{ open: false, selected: 'This Year' }" class="relative inline-block">
                        <button @click="open = !open" type="button"
                            class="bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-3 inline-flex justify-between items-center gap-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-blue-800 transition-all duration-300">
                            <span class="text-gray-700 dark:text-white text-xs font-normal">Show:</span>
                            <span class="text-gray-700 dark:text-white text-xs font-normal" x-text="selected"></span>
                            <svg class="w-2.5 h-2.5 ms-2 transition-transform duration-300 text-gray-600 dark:text-gray-400"
                                :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 top-full mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-lg min-w-[10rem] dark:bg-gray-700"
                            style="display: none;">
                            <ul class="py-2 text-xs text-gray-700 dark:text-white">
                                <template x-for="option in ['Today', 'This Week', 'This Month', 'This Year']" :key="option">
                                    <li>
                                        <button @click="selected = option; open = false; $dispatch('period-changed', { period: option, type: 'productivity' })"
                                            type="button"
                                            class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600"
                                            :class="{ 'bg-gray-100 dark:bg-gray-600': selected === option }"
                                            x-text="option"></button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="w-full rounded-lg h-[32rem] sm:h-[32rem] md:h-[32rem] lg:h-[32rem]"
                    id="productivity-chart-container"
                    @period-changed.window="if ($event.detail.type === 'productivity') updateChart($event.detail.period)">
                    {{-- Productivity Rate Line Chart from Database --}}
                    <x-linechart title="Hours Worked"
                        :currentValue="$defaultData['currentValue']"
                        :changeValue="$defaultData['changeValue']"
                        :changePercent="$defaultData['changePercent']"
                        :chartData="$defaultData['values']"
                        :chartLabels="$defaultData['labels']"
                        chartColor="#8b5cf6"
                        gradientStart="rgba(139, 92, 246, 0.2)"
                        gradientEnd="rgba(139, 92, 246, 0)"
                        :dateRange="$defaultData['dateRange']"
                        :allData="$performanceData"
                        chartId="productivityChart" />
                </div>
            </div>
        </div>

        <!-- Right Panel - Service Demand Statistics -->
        <div class="flex flex-col gap-4 md:gap-6 w-1/3 lg:w-1/3 rounded-lg p-3 md:p-3 lg:h-auto lg:overflow-hidden"
            x-data="{
                selectedServicePeriod: 'This Year',
                serviceDemandData: {{ Js::from($serviceDemandData) }},
                updateServiceChart(period) {
                    this.selectedServicePeriod = period;
                    this.$nextTick(() => {
                        window.updateServiceChart(period);
                    });
                }
            }">
            <!-- Header -->
            <div class="flex flex-row justify-between">
                <x-labelwithvalue label="Service Demand Statistics" count="" />
                <div x-data="{ open: false, selected: 'This Year' }" class="relative inline-block">
                    <button @click="open = !open" type="button"
                        class="bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-3 inline-flex justify-between items-center gap-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-blue-800 transition-all duration-300">
                        <span class="text-gray-700 dark:text-white text-xs font-normal">Show:</span>
                        <span class="text-gray-700 dark:text-white text-xs font-normal" x-text="selected"></span>
                        <svg class="w-2.5 h-2.5 ms-2 transition-transform duration-300 text-gray-600 dark:text-gray-400"
                            :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 top-full mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-lg min-w-[10rem] dark:bg-gray-700"
                        style="display: none;">
                        <ul class="py-2 text-xs text-gray-700 dark:text-white">
                            <template x-for="option in ['Today', 'This Week', 'This Month', 'This Year']" :key="option">
                                <li>
                                    <button @click="selected = option; open = false; $dispatch('period-changed', { period: option, type: 'service' })"
                                        type="button"
                                        class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600"
                                        :class="{ 'bg-gray-100 dark:bg-gray-600': selected === option }"
                                        x-text="option"></button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="service-demand-container"
                @period-changed.window="if ($event.detail.type === 'service') updateServiceChart($event.detail.period)">
                {{-- Service Demand Statistics from Database --}}
                <x-statisticchart
                    :total="$totalServiceDemand"
                    :growthRate="$growthRate"
                    :growthTrend="$growthTrend"
                    :categories="$serviceCategories"
                    chartId="serviceDemandChart" />
            </div>
            <!-- Customer Transactions - Scrollable -->
            <x-labelwithvalue label="Client Transactions" count="({{ count($tableData) }})" />

            <div class="rounded-lg h-52 overflow-y-auto">
                {{-- Customer Transactions Table from Database --}}
                <x-datatable :columns="$columns" :data="$tableData" :striped="true" :hoverable="true"
                    :responsive="true" />
            </div>
        </div>
    </div>
</x-layouts.general-employer>

@push('scripts')
<script>
// Analytics Filter Functionality
(function() {
    // Store all data for dynamic updates
    const performanceData = @json($performanceData);
    const serviceDemandData = @json($serviceDemandData);

    // Helper function to format time from hours to HH:MM:SS
    function formatTime(hours) {
        const h = Math.floor(hours);
        const m = Math.floor((hours - h) * 60);
        const s = Math.floor(((hours - h) * 60 - m) * 60);
        return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }

    // Update Productivity Chart
    window.updateProductivityChart = function(period) {
        console.log('Updating productivity chart for period:', period);
        const data = performanceData[period];
        if (!data) {
            console.error('No data found for period:', period);
            return;
        }

        // Find the Chart.js instance
        const chartCanvas = document.getElementById('productivityChart');
        if (chartCanvas && window.Chart) {
            const chart = Chart.getChart(chartCanvas);
            if (chart) {
                console.log('Chart found, updating with data:', data);

                // Update chart data
                chart.data.labels = data.labels;
                chart.data.datasets[0].data = data.values;
                chart.update('active'); // Update with animation

                // Update display values
                updateProductivityStats(data);
            } else {
                console.error('Chart instance not found');
            }
        } else {
            console.error('Chart canvas or Chart.js not found');
        }
    };

    // Update Service Demand Chart (Custom SVG Chart)
    window.updateServiceChart = function(period) {
        console.log('Updating service chart for period:', period);
        const data = serviceDemandData[period];
        if (!data) {
            console.error('No data found for period:', period);
            return;
        }

        console.log('Service data:', data);

        // Find the StatisticChart instance
        const chartId = 'serviceDemandChart';
        const chartInstance = window.statisticCharts ? window.statisticCharts[chartId] : null;

        if (chartInstance) {
            console.log('StatisticChart instance found, updating...');

            // Update the chart instance
            chartInstance.total = data.total;
            chartInstance.categories = data.categories;

            // Recalculate and re-render
            chartInstance.calculateRings();

            // Clear existing rings
            chartInstance.bgRingsContainer.innerHTML = '';
            chartInstance.dataRingsContainer.innerHTML = '';

            // Re-render
            chartInstance.renderBackgroundRings();
            chartInstance.renderDataRings();
            chartInstance.attachEventListeners();
            chartInstance.animate();

            // Update category list in DOM
            updateServiceCategoryList(data);

            // Update stats
            updateServiceStats(data);
        } else {
            console.error('StatisticChart instance not found. Available charts:', Object.keys(window.statisticCharts || {}));

            // Fallback: Just update the numbers
            updateServiceStats(data);
            updateServiceCategoryList(data);
        }
    };

    // Update productivity display stats
    function updateProductivityStats(data) {
        // Update current value
        const valueEl = document.querySelector('[data-current-value] .value-text');
        if (valueEl) {
            valueEl.textContent = formatTime(data.currentValue);
            console.log('Updated current value:', formatTime(data.currentValue));
        }

        // Update change indicator
        const changeEl = document.querySelector('[data-change-indicator] .change-value');
        const changePercentEl = document.querySelector('[data-change-indicator] .change-percent');

        if (changeEl) {
            const arrow = data.changeValue >= 0 ? '↑' : '↓';
            changeEl.textContent = `${arrow} ${formatTime(Math.abs(data.changeValue))}`;
            // Update color based on positive/negative
            changeEl.className = data.changeValue >= 0 ?
                'text-blue-500 dark:text-blue-400 text-sm font-medium change-value' :
                'text-red-500 dark:text-red-400 text-sm font-medium change-value';
        }

        if (changePercentEl) {
            const sign = data.changePercent >= 0 ? '+' : '';
            changePercentEl.textContent = `(${sign}${data.changePercent.toFixed(2)}%)`;
            // Update color based on positive/negative
            changePercentEl.className = data.changePercent >= 0 ?
                'text-blue-500 dark:text-blue-400 text-sm change-percent' :
                'text-red-500 dark:text-red-400 text-sm change-percent';
        }

        // Update total hours
        const totalEl = document.querySelector('.total-value');
        if (totalEl) {
            totalEl.textContent = formatTime(data.currentValue + data.changeValue);
        }

        // Update date range
        const dateRangeEl = document.querySelector('[data-date-range]');
        if (dateRangeEl) {
            const dates = data.dateRange.split(' - ');
            dateRangeEl.innerHTML = dates.map(date => `<span>${date}</span>`).join('');
        }
    }

    // Update service demand display stats
    function updateServiceStats(data) {
        // Update center value (total)
        const centerValueEl = document.getElementById('serviceDemandChart-center-value');
        if (centerValueEl) {
            centerValueEl.textContent = data.total.toLocaleString();
            console.log('Updated service total:', data.total);
        }

        // Update growth rate badge
        const container = document.querySelector('#service-demand-container');
        if (container) {
            const growthBadge = container.querySelector('.inline-flex.items-center.px-2\\.5.py-1');
            if (growthBadge) {
                growthBadge.textContent = (data.growthRate > 0 ? '+' : '') + data.growthRate + '%';
                console.log('Updated growth rate:', data.growthRate + '%');
            }
        }
    }

    // Update service category list
    function updateServiceCategoryList(data) {
        const container = document.querySelector('#service-demand-container');
        if (!container) return;

        // Find the category list container
        const categoryList = container.querySelector('.space-y-1');
        if (categoryList) {
            // Clear existing categories
            categoryList.innerHTML = '';

            // Add new categories
            data.categories.forEach((category, index) => {
                const categoryItem = document.createElement('div');
                categoryItem.className = 'flex items-center justify-between p-2 rounded-lg transition-all duration-200 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 group';
                categoryItem.setAttribute('data-chart-id', 'serviceDemandChart');
                categoryItem.setAttribute('data-category-index', index);

                categoryItem.innerHTML = `
                    <div class="flex items-center">
                        <div class="w-3 h-3 mr-3 rounded-full flex-shrink-0 transition-transform duration-200 group-hover:scale-125"
                             style="background-color: ${category.color}"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">
                            ${category.name}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            ${category.value.toLocaleString()}
                        </span>
                    </div>
                `;

                categoryList.appendChild(categoryItem);
            });

            console.log('Updated category list with', data.categories.length, 'categories');
        }
    }

    // Log that the script is loaded
    console.log('Analytics filter script loaded');
    console.log('Performance data available for periods:', Object.keys(performanceData));
    console.log('Service data available for periods:', Object.keys(serviceDemandData));
})();
</script>
@endpush

@stack('scripts')