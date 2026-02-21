<x-layouts.general-employer :title="'Reports'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Generate and view business reports</p>
        </div>

        <!-- Report Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Client Revenue Report -->
            <a href="{{ route('admin.reports.clients') }}" class="group bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-all p-6 border-2 border-transparent hover:border-blue-500">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <i class="fi fi-rr-users-alt text-blue-600 dark:text-blue-400 text-2xl"></i>
                    </div>
                    <i class="fi fi-rr-arrow-right text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Client Reports
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    View client revenue, appointment history, and service breakdown
                </p>

                <div class="mt-4 flex items-center gap-2 text-blue-600 dark:text-blue-400 font-medium text-sm">
                    View Report
                    <i class="fi fi-rr-angle-right text-xs"></i>
                </div>
            </a>

            <!-- Employee Payroll Report -->
            <a href="{{ route('admin.reports.payroll') }}" class="group bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-all p-6 border-2 border-transparent hover:border-green-500">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <i class="fi fi-rr-money text-green-600 dark:text-green-400 text-2xl"></i>
                    </div>
                    <i class="fi fi-rr-arrow-right text-gray-400 group-hover:text-green-600 transition-colors"></i>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Employee Payroll
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Calculate salaries, view attendance, and track work hours
                </p>

                <div class="mt-4 flex items-center gap-2 text-green-600 dark:text-green-400 font-medium text-sm">
                    View Report
                    <i class="fi fi-rr-angle-right text-xs"></i>
                </div>
            </a>

            <!-- Future: Task Completion Report (Placeholder) -->
            <div class="bg-gray-100 dark:bg-gray-700/50 rounded-lg shadow p-6 border-2 border-dashed border-gray-300 dark:border-gray-600">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-gray-200 dark:bg-gray-600 rounded-lg opacity-50">
                        <i class="fi fi-rr-tasks text-gray-500 text-2xl"></i>
                    </div>
                    <span class="text-xs bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 px-2 py-1 rounded">Coming Soon</span>
                </div>

                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Task Analytics
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Track task completion rates and team performance
                </p>
            </div>

            <!-- Future: Revenue Trends (Placeholder) -->
            <div class="bg-gray-100 dark:bg-gray-700/50 rounded-lg shadow p-6 border-2 border-dashed border-gray-300 dark:border-gray-600">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-gray-200 dark:bg-gray-600 rounded-lg opacity-50">
                        <i class="fi fi-rr-chart-line-up text-gray-500 text-2xl"></i>
                    </div>
                    <span class="text-xs bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 px-2 py-1 rounded">Coming Soon</span>
                </div>

                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Revenue Trends
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Analyze revenue trends and forecast future income
                </p>
            </div>

            <!-- Service Performance Report -->
            <a href="{{ route('admin.reports.service') }}" class="group bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-all p-6 border-2 border-transparent hover:border-orange-500">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                        <i class="fi fi-rr-chart-pie-alt text-orange-600 dark:text-orange-400 text-2xl"></i>
                    </div>
                    <i class="fi fi-rr-arrow-right text-gray-400 group-hover:text-orange-600 transition-colors"></i>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Service Performance
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    View client and employee feedback, ratings, and service quality
                </p>

                <div class="mt-4 flex items-center gap-2 text-orange-600 dark:text-orange-400 font-medium text-sm">
                    View Report
                    <i class="fi fi-rr-angle-right text-xs"></i>
                </div>
            </a>

            <!-- Future: Attendance Summary (Placeholder) -->
            <div class="bg-gray-100 dark:bg-gray-700/50 rounded-lg shadow p-6 border-2 border-dashed border-gray-300 dark:border-gray-600">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-gray-200 dark:bg-gray-600 rounded-lg opacity-50">
                        <i class="fi fi-rr-calendar-check text-gray-500 text-2xl"></i>
                    </div>
                    <span class="text-xs bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 px-2 py-1 rounded">Coming Soon</span>
                </div>

                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Attendance Summary
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    View attendance patterns and identify trends
                </p>
            </div>
        </div>

        <!-- Quick Stats Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Overview (Current Month)</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- These would be dynamically loaded in a real implementation -->
                <div class="p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg">
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">---</p>
                    <p class="text-xs text-blue-500 dark:text-blue-400 mt-1">Use Client Reports for details</p>
                </div>

                <div class="p-4 bg-green-50 dark:bg-green-900/10 rounded-lg">
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Total Payroll</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">---</p>
                    <p class="text-xs text-green-500 dark:text-green-400 mt-1">Use Payroll Reports for details</p>
                </div>

                <div class="p-4 bg-purple-50 dark:bg-purple-900/10 rounded-lg">
                    <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Active Clients</p>
                    <p class="text-2xl font-bold text-purple-700 dark:text-purple-300 mt-1">---</p>
                    <p class="text-xs text-purple-500 dark:text-purple-400 mt-1">View in Client Reports</p>
                </div>

                <div class="p-4 bg-orange-50 dark:bg-orange-900/10 rounded-lg">
                    <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">Total Work Hours</p>
                    <p class="text-2xl font-bold text-orange-700 dark:text-orange-300 mt-1">---</p>
                    <p class="text-xs text-orange-500 dark:text-orange-400 mt-1">View in Payroll Reports</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.general-employer>
