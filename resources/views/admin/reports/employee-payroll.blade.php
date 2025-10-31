<x-layouts.general-employer :title="'Employee Payroll Reports'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header with Date Filter -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        <i class="fi fi-rr-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Employee Payroll Reports</h1>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Salary calculations and attendance tracking</p>
            </div>

            <!-- Date Range Filter -->
            <form method="GET" action="{{ route('admin.reports.payroll') }}" class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <span class="text-gray-600 dark:text-gray-400">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Filter
                </button>
                <a href="{{ route('admin.reports.payroll.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                   class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                    <i class="fi fi-rr-download"></i> Export CSV
                </a>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Payroll</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">€{{ number_format($totalSalary, 2) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                        <i class="fi fi-rr-money text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Regular Hours</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($totalRegularHours, 2) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                        <i class="fi fi-rr-clock text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Sunday/Holiday Hours</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1">{{ number_format($totalPremiumHours, 2) }}</p>
                        <p class="text-xs text-orange-500 dark:text-orange-400 mt-1">2x Rate</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-full">
                        <i class="fi fi-rr-calendar text-orange-600 dark:text-orange-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Employees</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $totalEmployees }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                        <i class="fi fi-rr-users text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Hours/Employee</p>
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $averageHoursPerEmployee }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/20 rounded-full">
                        <i class="fi fi-rr-chart-histogram text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Payroll Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Employee Payroll Details</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hourly Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Days</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Regular Hrs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sun/Holiday Hrs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Hrs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gross Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($employees as $employee)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $employee->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $employee->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        €{{ number_format($employee->salary_per_hour, 2) }}/hr
                                    </div>
                                    @if($employee->premium_hours > 0)
                                        <div class="text-xs text-orange-600 dark:text-orange-400">
                                            €{{ number_format($employee->salary_per_hour * 2, 2) }}/hr (2x)
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $employee->days_worked ?: 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                        {{ number_format($employee->regular_hours, 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        €{{ number_format($employee->regular_pay, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-orange-600 dark:text-orange-400">
                                        {{ number_format($employee->premium_hours, 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        €{{ number_format($employee->premium_pay, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($employee->total_hours, 2) }} hrs
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 dark:text-green-400">
                                    €{{ number_format($employee->gross_salary, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.reports.employee-detail', ['employeeId' => $employee->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <i class="fi fi-rr-inbox text-4xl mb-2"></i>
                                        <p>No employee data available for this period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($employees->count() > 0)
                        <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="font-bold">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white" colspan="3">TOTAL</td>
                                <td class="px-6 py-4 text-sm text-blue-600 dark:text-blue-400">{{ number_format($totalRegularHours, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-orange-600 dark:text-orange-400">{{ number_format($totalPremiumHours, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ number_format($totalHours, 2) }} hrs</td>
                                <td class="px-6 py-4 text-sm text-green-600 dark:text-green-400">€{{ number_format($totalSalary, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </section>
</x-layouts.general-employer>
