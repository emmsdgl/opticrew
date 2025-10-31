<x-layouts.general-employer :title="'Employee Payroll Detail'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col gap-2">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.payroll') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <i class="fi fi-rr-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $employee->user->name }}
                </h1>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Employee payroll and attendance details
            </p>
        </div>

        <!-- Employee Info & Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Employee Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Employee Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $employee->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Phone</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $employee->user->phone ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Hourly Rate</p>
                        <p class="text-sm font-bold text-green-600 dark:text-green-400">€{{ number_format($employee->salary_per_hour, 2) }}/hr</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Experience</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $employee->years_of_experience }} years</p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/10 rounded-lg p-4">
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Days Worked</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">{{ $stats['total_days'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        R:{{ $stats['regular_days'] }} / P:{{ $stats['premium_days'] }}
                    </p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/10 rounded-lg p-4">
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Regular Hours</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">{{ number_format($stats['regular_hours'], 2) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">€{{ number_format($stats['regular_pay'], 2) }}</p>
                </div>
                <div class="bg-orange-50 dark:bg-orange-900/10 rounded-lg p-4">
                    <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">Sun/Holiday Hrs</p>
                    <p class="text-2xl font-bold text-orange-700 dark:text-orange-300 mt-1">{{ number_format($stats['premium_hours'], 2) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">€{{ number_format($stats['premium_pay'], 2) }} (2x)</p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/10 rounded-lg p-4">
                    <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Avg Hours/Day</p>
                    <p class="text-2xl font-bold text-purple-700 dark:text-purple-300 mt-1">{{ number_format($stats['average_hours_per_day'], 2) }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/10 rounded-lg p-4">
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Total Salary</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">€{{ number_format($stats['total_salary'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Daily Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daily Breakdown</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Day Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hourly Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Shifts</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Daily Pay</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($dailyBreakdown as $date => $day)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($day['date'])->format('D, M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($day['is_premium_day'])
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400">
                                            {{ $day['day_type'] }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                            {{ $day['day_type'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="{{ $day['is_premium_day'] ? 'text-orange-600 dark:text-orange-400 font-semibold' : 'text-gray-900 dark:text-white' }}">
                                        €{{ number_format($day['hourly_rate'], 2) }}/hr
                                    </span>
                                    @if($day['is_premium_day'])
                                        <span class="text-xs text-orange-500">(2x)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $day['shifts'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600 dark:text-blue-400">
                                    {{ number_format($day['total_hours'], 2) }} hrs
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 dark:text-green-400">
                                    €{{ number_format($day['daily_pay'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <i class="fi fi-rr-inbox text-4xl mb-2"></i>
                                        <p>No attendance records found for this period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($dailyBreakdown->count() > 0)
                        <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-bold">
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white" colspan="3">TOTAL</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $stats['total_days'] }}</td>
                                <td class="px-6 py-4 text-sm text-blue-600 dark:text-blue-400">{{ number_format($stats['total_hours'], 2) }} hrs</td>
                                <td class="px-6 py-4 text-sm text-green-600 dark:text-green-400">€{{ number_format($stats['total_salary'], 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Attendance Records -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Detailed Attendance Records</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Day Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clock In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clock Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pay</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($attendances as $attendance)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($attendance->clock_in)->format('D, M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($attendance->is_premium_day)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400">
                                            {{ $attendance->day_type }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                            {{ $attendance->day_type }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($attendance->clock_in)->format('h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('h:i A') : 'Not clocked out' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600 dark:text-blue-400">
                                    {{ number_format($attendance->hours_worked, 2) }} hrs
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="{{ $attendance->is_premium_day ? 'text-orange-600 dark:text-orange-400 font-semibold' : 'text-gray-900 dark:text-white' }}">
                                        €{{ number_format($attendance->hourly_rate, 2) }}/hr
                                    </span>
                                    @if($attendance->is_premium_day)
                                        <span class="text-xs text-orange-500">(2x)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 dark:text-green-400">
                                    €{{ number_format($attendance->daily_pay, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <i class="fi fi-rr-inbox text-4xl mb-2"></i>
                                        <p>No attendance records found for this period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.general-employer>
