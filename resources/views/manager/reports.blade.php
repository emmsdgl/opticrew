<x-layouts.general-manager :title="'Reports'">
    <div class="flex flex-col gap-6 w-full" x-data="reportsManager()">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Reports & Analytics</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Track performance and generate billing reports</p>
            </div>
            <div class="flex gap-3">
                <select onchange="window.location.href = '{{ route('manager.reports') }}?period=' + this.value"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="week" {{ ($period ?? 'month') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ ($period ?? 'month') === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="quarter" {{ ($period ?? 'month') === 'quarter' ? 'selected' : '' }}>This Quarter</option>
                    <option value="year" {{ ($period ?? 'month') === 'year' ? 'selected' : '' }}>This Year</option>
                </select>
                <button @click="showBillingModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                    <i class="fa-solid fa-file-invoice"></i>
                    Generate Billing Report
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-5 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-list-check text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Tasks</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $summary['totalTasks'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-5 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Completion Rate</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $summary['completionRate'] ?? 0 }}%</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-5 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-spinner text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">In Progress</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $summary['inProgress'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-5 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-clock text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Hours</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $summary['totalHours'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Performance Overview</h2>
                </div>
                <div class="p-4 md:p-5">
                    <div id="performanceChart" class="h-64"></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tasks by Location</h2>
                </div>
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
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Top Performers</h2>
            </div>
            <div class="p-4 md:p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @forelse($topPerformers ?? [] as $index => $performer)
                        <div class="flex items-center gap-4 p-4 rounded-lg {{ $index === 0 ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' : 'bg-gray-50 dark:bg-gray-700/50' }}">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold
                                {{ $index === 0 ? 'bg-yellow-400 text-yellow-900' : ($index === 1 ? 'bg-gray-300 text-gray-700' : 'bg-orange-300 text-orange-900') }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold">
                                {{ strtoupper(substr($performer['name'], 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $performer['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $performer['tasksCompleted'] }} tasks completed</p>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $performer['efficiency'] }}%</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">efficiency</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">No performance data available</p>
                        </div>
                    @endforelse
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

            var performanceChart = new ApexCharts(document.querySelector("#performanceChart"), performanceOptions);
            performanceChart.render();
        });
    </script>
    @endpush
</x-layouts.general-manager>
