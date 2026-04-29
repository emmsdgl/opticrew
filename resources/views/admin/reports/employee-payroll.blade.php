<x-layouts.general-employer :title="'Employee Payroll Reports'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header with Date Filter -->
        <div class="flex flex-col md:items-center md:justify-between gap-4">
            <div class ="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Analytics', 'url' => route('admin.analytics')],
                    ['label' => 'Reports', 'url' => route('admin.reports.index')],
                    ['label' => 'Employee Payroll'],
                ]" />
            </div>
            <div class ="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Employee Payroll Reports</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Salary calculations and attendance tracking</p>
                </div>

                <!-- Date Range Filter -->
                <form method="GET" action="{{ route('admin.reports.payroll') }}" class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="px-3 py-2 text-xs border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <span class="text-gray-600 dark:text-gray-400">to</span>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="px-3 py-2 text-xs border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <button type="submit"
                        class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="{{ route('admin.reports.payroll.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                        class="px-4 py-2 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                        <i class="fi fi-rr-download"></i> Export CSV
                    </a>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="py-6">
            <x-employer-components.stats-cards :stats="[
                ['label' => 'Total Payroll', 'value' => '€' . number_format($totalSalary, 2)],
                ['label' => 'Regular Hours', 'value' => number_format($totalRegularHours, 2)],
                ['label' => 'Sunday/Holiday Hours', 'value' => number_format($totalPremiumHours, 2), 'subtitle' => '2x Rate'],
                ['label' => 'Total Employees', 'value' => $totalEmployees],
                ['label' => 'Avg Hours/Employee', 'value' => $averageHoursPerEmployee],
            ]" />
        </div>

        <!-- Employee Payroll Table -->
        <div class="flex flex-col gap-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Employee Payroll Details</h2>

            @if($employees->count() > 0)
                <div x-data="{ page: 1, perPage: 5, total: {{ $employees->count() }}, get totalPages() { return Math.ceil(this.total / this.perPage); } }">
                <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Employee</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Hourly Rate</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Days</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Regular Hrs</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Sun/Holiday Hrs</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Lunch Break</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Dinner Break</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Total Hrs</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Gross Salary</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr class="even:bg-gray-50 dark:even:bg-gray-800/50" x-show="{{ $loop->index }} >= (page - 1) * perPage && {{ $loop->index }} < page * perPage">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $employee->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-200">€{{ number_format($employee->salary_per_hour, 2) }}/hr</div>
                                        @if($employee->premium_hours > 0)
                                            <div class="text-xs text-orange-600 dark:text-orange-400">€{{ number_format($employee->salary_per_hour * 2, 2) }}/hr (2x)</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-200">{{ $employee->days_worked ?: 0 }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-blue-600 dark:text-blue-400">{{ number_format($employee->regular_hours, 2) }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">€{{ number_format($employee->regular_pay, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-orange-600 dark:text-orange-400">{{ number_format($employee->premium_hours, 2) }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">€{{ number_format($employee->premium_pay, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-200">
                                            {{ intdiv($employee->lunch_break_minutes, 60) }}h {{ $employee->lunch_break_minutes % 60 }}m
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->lunch_break_minutes }} min</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-200">
                                            {{ intdiv($employee->dinner_break_minutes, 60) }}h {{ $employee->dinner_break_minutes % 60 }}m
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->dinner_break_minutes }} min</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($employee->total_hours, 2) }} hrs</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-green-600 dark:text-green-400">€{{ number_format($employee->gross_salary, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('admin.reports.employee-detail', ['employeeId' => $employee->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                           class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                            <i class="fa-regular fa-eye mr-1 text-xs"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white" colspan="3">TOTAL</td>
                                <td class="px-6 py-4 text-sm font-semibold text-blue-600 dark:text-blue-400">{{ number_format($totalRegularHours, 2) }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-orange-600 dark:text-orange-400">{{ number_format($totalPremiumHours, 2) }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ intdiv($totalLunchBreakMinutes, 60) }}h {{ $totalLunchBreakMinutes % 60 }}m
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ intdiv($totalDinnerBreakMinutes, 60) }}h {{ $totalDinnerBreakMinutes % 60 }}m
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($totalHours, 2) }} hrs</td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600 dark:text-green-400">€{{ number_format($totalSalary, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @include('components.report-pagination')
                </div>
            @else
                <div class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                    <i class="fa-solid fa-inbox text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No employee data available</p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Employee payroll details will appear here once data is available for this period</p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.general-employer>
