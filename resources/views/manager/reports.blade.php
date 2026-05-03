<x-layouts.general-manager :title="'Reports'">
    <div class="flex flex-col gap-6 w-full p-6" x-data="reportsManager()">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-sm font-sans font-bold text-gray-900 dark:text-white">Reports & Analytics</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Track performance and generate billing reports</p>
            </div>
            @php
                $periodOptions = [
                    'week' => 'This Week',
                    'month' => 'This Month',
                    'quarter' => 'This Quarter',
                    'year' => 'This Year',
                ];
                $currentPeriod = $period ?? 'month';
                $currentPeriodLabel = $periodOptions[$currentPeriod] ?? 'This Month';
            @endphp
            <div class="flex gap-3">
                {{-- Period Dropdown --}}
                <div x-data="{ open: false }" class="relative inline-block">
                    <button @click="open = !open" type="button"
                            class="bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-3 inline-flex justify-between items-center gap-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-blue-800 transition-all duration-300">
                        <span class="text-gray-700 dark:text-white text-xs font-normal">Show:</span>
                        <span class="text-gray-700 dark:text-white text-xs font-normal">{{ $currentPeriodLabel }}</span>
                        <svg class="w-2.5 h-2.5 ms-2 transition-transform duration-300 text-gray-600 dark:text-gray-400"
                             :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute right-0 top-full mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-lg min-w-[10rem] dark:bg-gray-700"
                         style="display: none;">
                        <ul class="py-2 text-xs text-gray-700 dark:text-white">
                            @foreach($periodOptions as $value => $label)
                                <li>
                                    <button type="button"
                                            data-url="{{ route('manager.reports', ['period' => $value]) }}"
                                            @click="window.location.href = $el.dataset.url"
                                            class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 {{ $currentPeriod === $value ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                        {{ $label }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Generate Billing Report Button --}}
                <button @click="showBillingModal = true" type="button"
                        class="inline-flex items-center gap-2 px-4 py-3 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fa-solid fa-file-invoice"></i>
                    Generate Billing Report
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-gray-200 dark:bg-gray-700 my-12 rounded-lg overflow-hidden">
            {{-- Total Tasks --}}
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <div class="flex items-center gap-2 mb-2 ml-3">
                    <i class="fa-solid fa-list-check" style="color: #3b82f6"></i>
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Total Tasks</p>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $summary['totalTasks'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">In selected period</p>
            </div>

            {{-- Completion Rate --}}
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <div class="flex items-center gap-2 mb-2 ml-3">
                    <i class="fa-solid fa-check-circle" style="color: #10b981"></i>
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Completion Rate</p>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $summary['completionRate'] ?? 0 }}%</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">Of all tasks</p>
            </div>

            {{-- In Progress --}}
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <div class="flex items-center gap-2 mb-2 ml-3">
                    <i class="fa-solid fa-spinner" style="color: #f59e0b"></i>
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400">In Progress</p>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $summary['inProgress'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">Active right now</p>
            </div>

            {{-- Total Hours --}}
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <div class="flex items-center gap-2 mb-2 ml-3">
                    <i class="fa-solid fa-clock" style="color: #8b5cf6"></i>
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Total Hours</p>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $summary['totalHours'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">Logged this period</p>
            </div>
        </div>

        <!-- Performance Overview (full width) -->
        <div class="flex flex-col gap-3">
            <x-labelwithvalue label="Performance Overview" />
            <div class="bg-white dark:bg-transparent rounded-xl border border-gray-200 dark:border-none">
                <div class="p-4 md:p-5">
                    @if(empty($chartData['labels'] ?? []))
                        <div class="flex flex-col items-center justify-center py-16 px-6 text-center h-auto">
                            <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                     alt="No performance data"
                                     class="w-full h-full object-contain opacity-80 dark:opacity-60">
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">No performance data yet</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">Performance metrics will appear here as tasks are completed in the selected period.</p>
                        </div>
                    @else
                        <div id="performanceChart" class="h-64"></div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tasks by Location + Top Performers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Tasks by Location -->
            <div class="flex flex-col gap-3">
                <x-labelwithvalue label="Tasks by Location" :count="'(' . count($tasksByLocation ?? []) . ')'" />
                <div class="bg-white dark:bg-transparent rounded-xl border border-gray-200 dark:border-none">
                    <div class="p-4 md:p-5 space-y-4">
                        @forelse($tasksByLocation ?? [] as $location)
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $location['name'] }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $location['count'] }} tasks</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $location['percentage'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-16 px-6 text-center h-auto">
                                <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                    <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                         alt="No tasks by location"
                                         class="w-full h-full object-contain opacity-80 dark:opacity-60">
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">No tasks by location yet</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">Locations with tasks will appear here once assignments are made.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Top Performers (StackList style) -->
            <div class="flex flex-col gap-3">
                <x-labelwithvalue label="Top Performers" :count="'(' . count($topPerformers ?? []) . ')'" />
                <div class="bg-white dark:bg-transparent rounded-xl border border-gray-200 dark:border-none">
                    <div class="p-4 md:p-5">
                    @php $performers = collect($topPerformers ?? []); $initialVisible = 3; @endphp
                    @if($performers->count() > 0)
                        <div x-data="{ expanded: false, initialVisible: {{ $initialVisible }} }" class="bg-transparent">
                            <div class="flex flex-col gap-4">
                                @foreach($performers as $index => $performer)
                                    @php
                                        $rankBadgeClass = match($index) {
                                            0 => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
                                            1 => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                            2 => 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400',
                                            default => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                                        };
                                    @endphp
                                    <div x-show="expanded || {{ $index }} < initialVisible"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                         class="flex items-center gap-4 p-4 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-2xl shadow-sm">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 {{ $rankBadgeClass }}">
                                            @if($index < 3)
                                                <i class="fa-solid {{ $index === 0 ? 'fa-trophy' : 'fa-medal' }} text-base"></i>
                                            @else
                                                <span class="font-bold text-sm">{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs xl:text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $performer['name'] }}
                                            </div>
                                            <div class="text-xs xl:text-sm text-gray-500 dark:text-gray-400">
                                                {{ $performer['tasksCompleted'] }} tasks completed
                                            </div>
                                        </div>
                                        <div class="text-xs xl:text-sm font-semibold text-gray-400 flex-shrink-0">
                                            {{ $performer['efficiency'] }}%
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($performers->count() > $initialVisible)
                                <div class="mt-4 flex justify-center">
                                    <button type="button" @click="expanded = !expanded"
                                            class="flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-full text-xs xl:text-sm font-medium text-gray-600 dark:text-gray-300 transition hover:bg-gray-100 dark:hover:bg-neutral-800">
                                        <span x-text="expanded ? 'Hide' : 'Show All'"></span>
                                        <i class="fa-solid fa-chevron-down w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': expanded }"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-16 px-6 text-center h-auto">
                            <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                     alt="No performance data"
                                     class="w-full h-full object-contain opacity-80 dark:opacity-60">
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">No performance data available</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">Top performers will be ranked here once tasks are completed in the selected period.</p>
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </div>

        <!-- Billing Report Modal -->
        <div x-show="showBillingModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showBillingModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Billing Report</h3>
                        <button @click="showBillingModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"><i class="fa-solid fa-xmark text-gray-500"></i></button>
                    </div>

                    <div class="p-4 space-y-4">
                        <!-- Date Range Selection -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                <input type="date" x-model="billingForm.start_date"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                <input type="date" x-model="billingForm.end_date"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <button @click="generateBilling()" :disabled="billingLoading || !billingForm.start_date || !billingForm.end_date"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium disabled:opacity-50">
                            <span x-text="billingLoading ? 'Generating...' : 'Generate Report'"></span>
                        </button>

                        <!-- Billing Results -->
                        <template x-if="billingData">
                            <div class="space-y-4">
                                <!-- Summary -->
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3" x-text="billingData.company + ' - Billing Summary'"></h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3" x-text="billingData.period"></p>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="text-center">
                                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="billingData.total_tasks"></p>
                                            <p class="text-xs text-gray-500">Tasks</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="billingData.total_hours + 'h'"></p>
                                            <p class="text-xs text-gray-500">Hours</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-lg font-bold text-green-600 dark:text-green-400" x-text="billingData.total_amount + ' EUR'"></p>
                                            <p class="text-xs text-gray-500">Total</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tasks by Date -->
                                <template x-for="day in billingData.tasks_by_date" :key="day.date">
                                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="day.date"></span>
                                            <span class="text-sm font-medium text-green-600 dark:text-green-400" x-text="day.subtotal + ' EUR'"></span>
                                        </div>
                                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                            <template x-for="(task, idx) in day.tasks" :key="idx">
                                                <div class="p-3 flex items-center justify-between text-sm">
                                                    <div>
                                                        <p class="text-gray-900 dark:text-white" x-text="task.location"></p>
                                                        <p class="text-xs text-gray-500" x-text="task.description + ' - ' + task.duration + ' min'"></p>
                                                    </div>
                                                    <span class="text-gray-900 dark:text-white font-medium" x-text="task.price + ' EUR'"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Export Button -->
                                <button @click="exportPdf()"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                                    <i class="fa-solid fa-file-pdf"></i>
                                    Export as PDF
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function reportsManager() {
            return {
                showBillingModal: false,
                billingLoading: false,
                billingData: null,
                billingForm: {
                    start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
                    end_date: new Date().toISOString().split('T')[0],
                },

                async generateBilling() {
                    this.billingLoading = true;
                    this.billingData = null;
                    try {
                        const res = await fetch(`/manager/reports/billing?start_date=${this.billingForm.start_date}&end_date=${this.billingForm.end_date}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        if (res.ok) {
                            this.billingData = data.billing;
                        }
                    } catch (e) {
                        console.error('Billing error:', e);
                    }
                    this.billingLoading = false;
                },

                exportPdf() {
                    if (!this.billingData) return;
                    const url = `/manager/reports/billing/pdf?start_date=${this.billingForm.start_date}&end_date=${this.billingForm.end_date}`;
                    window.open(url, '_blank');
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            var performanceOptions = {
                series: [{
                    name: 'Completed',
                    data: @json($chartData['completed'] ?? [])
                }, {
                    name: 'Scheduled',
                    data: @json($chartData['scheduled'] ?? [])
                }],
                chart: {
                    type: 'bar',
                    height: 256,
                    toolbar: { show: false },
                    background: 'transparent'
                },
                colors: ['#22c55e', '#3b82f6'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4
                    },
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: @json($chartData['labels'] ?? []),
                    labels: {
                        style: { colors: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280' }
                    }
                },
                legend: {
                    position: 'top',
                    labels: { colors: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280' }
                },
                grid: {
                    borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                },
                theme: {
                    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                }
            };

            var performanceChartEl = document.querySelector("#performanceChart");
            if (performanceChartEl) {
                var performanceChart = new ApexCharts(performanceChartEl, performanceOptions);
                performanceChart.render();
            }
        });
    </script>
    @endpush
</x-layouts.general-manager>
