<x-layouts.general-employer :title="'Attendance Summary'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]" x-data="attendanceReport()">
        <!-- Header with Date Filter -->
        <div class="flex flex-col md:items-center md:justify-between gap-4">
            <div class="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Analytics', 'url' => route('admin.analytics')],
                    ['label' => 'Reports', 'url' => route('admin.reports.index')],
                    ['label' => 'Attendance Summary'],
                ]" />
            </div>
            <div class="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Attendance Summary</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Absences and approved leave requests</p>
                </div>

                <!-- Date Range Filter -->
                <form method="GET" action="{{ route('admin.reports.attendance') }}" class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="px-3 py-2 text-xs border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <span class="text-gray-600 dark:text-gray-400">to</span>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="px-3 py-2 text-xs border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <button type="submit"
                        class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="py-6">
            <x-employer-components.stats-cards :stats="[
                ['label' => 'Total Absences', 'value' => $totalAbsences],
                ['label' => 'Approved Leaves', 'value' => $totalLeaves],
                ['label' => 'Total Leave Days', 'value' => $totalLeaveDays],
                ['label' => 'Total Employees', 'value' => $totalEmployees],
                ['label' => 'Flagged Employees', 'value' => count($flaggedEmployees)],
            ]" />
        </div>

        <!-- Search & Sort Controls -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <!-- Search Bar -->
            <div class="relative flex-1 max-w-sm">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" x-model="search" @input="resetPages()" placeholder="Search by employee name..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Employee Filter -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button"
                    class="flex items-center gap-2 px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fa-solid fa-user text-xs text-gray-400"></i>
                    <span x-text="employeeFilter === 'all' ? 'All Employees' : employeeFilter"></span>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                    class="absolute left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 max-h-64 overflow-y-auto">
                    <button @click="employeeFilter = 'all'; resetPages(); open = false"
                        class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        :class="employeeFilter === 'all' ? 'text-blue-600 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                        All Employees
                    </button>
                    @foreach($employeeNames as $name)
                        <button @click="employeeFilter = '{{ addslashes($name) }}'; resetPages(); open = false"
                            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            :class="employeeFilter === '{{ addslashes($name) }}' ? 'text-blue-600 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                            {{ $name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Date Sort -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button"
                    class="flex items-center gap-2 px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fa-solid fa-calendar text-xs text-gray-400"></i>
                    <span x-text="dateSort === 'newest' ? 'Newest First' : dateSort === 'oldest' ? 'Oldest First' : 'Today Only'"></span>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                    class="absolute left-0 mt-1 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                    <button @click="dateSort = 'newest'; resetPages(); open = false"
                        class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        :class="dateSort === 'newest' ? 'text-blue-600 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                        Newest First
                    </button>
                    <button @click="dateSort = 'oldest'; resetPages(); open = false"
                        class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        :class="dateSort === 'oldest' ? 'text-blue-600 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                        Oldest First
                    </button>
                    <button @click="dateSort = 'today'; resetPages(); open = false"
                        class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        :class="dateSort === 'today' ? 'text-blue-600 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                        Today Only
                    </button>
                </div>
            </div>
        </div>

        <!-- Approved Leave Requests Table -->
        <div class="flex flex-col gap-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                Approved Leave Requests
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400" x-text="'(' + filteredLeaves().length + ')'"></span>
            </h2>

            <template x-if="filteredLeaves().length > 0">
                <div>
                    <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Employee</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Leave Type</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Start Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">End Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Duration</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Reason</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(leave, idx) in paginatedLeaves()" :key="idx">
                                    <tr :class="idx % 2 === 1 ? 'bg-gray-50 dark:bg-gray-800/50' : ''">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="leave.name"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="leave.email"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="{
                                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': leave.type === 'Sick',
                                                    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': leave.type === 'Vacation',
                                                    'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400': leave.type === 'Emergency',
                                                    'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': !['Sick','Vacation','Emergency'].includes(leave.type),
                                                }"
                                                x-text="leave.type"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="leave.start_date"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="leave.end_date"></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-teal-600 dark:text-teal-400" x-text="leave.duration + ' day' + (leave.duration > 1 ? 's' : '')"></div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate" x-text="leave.reason"></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Approved</span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white" colspan="4">TOTAL</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-teal-600 dark:text-teal-400" x-text="filteredLeaves().reduce((s, l) => s + l.duration, 0) + ' days'"></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Leaves Pagination -->
                    <template x-if="totalLeavePages() > 1">
                        <div class="flex items-center justify-between mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Showing <span x-text="(leavePage - 1) * perPage + 1"></span>-<span x-text="Math.min(leavePage * perPage, filteredLeaves().length)"></span> of <span x-text="filteredLeaves().length"></span>
                            </p>
                            <div class="flex items-center gap-1">
                                <button @click="leavePage = Math.max(1, leavePage - 1)" :disabled="leavePage === 1"
                                    class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed text-gray-700 dark:text-gray-300">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </button>
                                <template x-for="p in totalLeavePages()" :key="p">
                                    <button @click="leavePage = p"
                                        class="px-3 py-1.5 text-sm rounded-lg transition-colors"
                                        :class="leavePage === p ? 'bg-blue-600 text-white' : 'border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                        x-text="p"></button>
                                </template>
                                <button @click="leavePage = Math.min(totalLeavePages(), leavePage + 1)" :disabled="leavePage === totalLeavePages()"
                                    class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed text-gray-700 dark:text-gray-300">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="filteredLeaves().length === 0">
                <div class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                    <i class="fa-solid fa-calendar-check text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No approved leave requests</p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Approved leave requests will appear here for this period</p>
                </div>
            </template>
        </div>

        <!-- Absences Table -->
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                    Absences
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400" x-text="'(' + filteredAbsences().length + ')'"></span>
                </h2>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-2 px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <i class="fa-solid fa-sort text-xs text-gray-400"></i>
                        <span x-text="absenceSort === 'all' ? 'All' : absenceSort === 'exceeded' ? 'Exceeded' : absenceSort === 'warning' ? 'Warning' : 'No Absences'"></span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                        <button @click="absenceSort = 'all'; absencePage = 1; open = false"
                            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            :class="absenceSort === 'all' ? 'text-blue-600 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                            All
                        </button>
                        <button @click="absenceSort = 'exceeded'; absencePage = 1; open = false"
                            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            :class="absenceSort === 'exceeded' ? 'text-blue-600 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                            <i class="fa-solid fa-circle text-red-500 text-[8px] mr-1.5"></i> Exceeded
                        </button>
                        <button @click="absenceSort = 'warning'; absencePage = 1; open = false"
                            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            :class="absenceSort === 'warning' ? 'text-blue-600 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                            <i class="fa-solid fa-circle text-yellow-500 text-[8px] mr-1.5"></i> Warning
                        </button>
                        <button @click="absenceSort = 'none'; absencePage = 1; open = false"
                            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            :class="absenceSort === 'none' ? 'text-blue-600 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                            <i class="fa-solid fa-circle text-green-500 text-[8px] mr-1.5"></i> No Absences
                        </button>
                    </div>
                </div>
            </div>

            <template x-if="filteredAbsences().length > 0">
                <div>
                    <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Employee</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Day</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(absence, idx) in paginatedAbsences()" :key="idx">
                                    <tr :class="idx % 2 === 1 ? 'bg-gray-50 dark:bg-gray-800/50' : ''">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="absence.name"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="absence.email"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200" x-text="absence.date"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400" x-text="absence.day"></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <template x-if="getAbsenceStatus(absence.name) === 'exceeded'">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                    <i class="fa-solid fa-triangle-exclamation text-[8px]"></i> Exceeded
                                                </span>
                                            </template>
                                            <template x-if="getAbsenceStatus(absence.name) === 'warning'">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                    <i class="fa-solid fa-exclamation text-[8px]"></i> Warning
                                                </span>
                                            </template>
                                            <template x-if="getAbsenceStatus(absence.name) === 'normal'">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Absent</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white" colspan="3">Total Absences</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-red-600 dark:text-red-400" x-text="filteredAbsences().length"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Absences Pagination -->
                    <template x-if="totalAbsencePages() > 1">
                        <div class="flex items-center justify-between mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Showing <span x-text="(absencePage - 1) * perPage + 1"></span>-<span x-text="Math.min(absencePage * perPage, filteredAbsences().length)"></span> of <span x-text="filteredAbsences().length"></span>
                            </p>
                            <div class="flex items-center gap-1">
                                <button @click="absencePage = Math.max(1, absencePage - 1)" :disabled="absencePage === 1"
                                    class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed text-gray-700 dark:text-gray-300">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </button>
                                <template x-for="p in (() => {
                                    const total = totalAbsencePages();
                                    const current = absencePage;
                                    const maxVisible = 5;
                                    if (total <= maxVisible) return Array.from({length: total}, (_, i) => i + 1);
                                    let start = Math.max(1, current - Math.floor(maxVisible / 2));
                                    let end = start + maxVisible - 1;
                                    if (end > total) { end = total; start = end - maxVisible + 1; }
                                    return Array.from({length: end - start + 1}, (_, i) => start + i);
                                })()" :key="p">
                                    <button @click="absencePage = p"
                                        class="px-3 py-1.5 text-sm rounded-lg transition-colors"
                                        :class="absencePage === p ? 'bg-blue-600 text-white' : 'border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                        x-text="p"></button>
                                </template>
                                <button @click="absencePage = Math.min(totalAbsencePages(), absencePage + 1)" :disabled="absencePage === totalAbsencePages()"
                                    class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed text-gray-700 dark:text-gray-300">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="filteredAbsences().length === 0">
                <div class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                    <i class="fa-solid fa-check-circle text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No absences recorded</p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">All employees have been present during this period</p>
                </div>
            </template>
        </div>

    </section>

    @php
        $leavesJson = $leaveRecords->map(function($l) {
            return [
                'name' => $l['employee_name'],
                'email' => $l['employee_email'],
                'type' => $l['type'],
                'start_date' => $l['start_date'],
                'end_date' => $l['end_date'],
                'date_raw' => \Carbon\Carbon::parse($l['start_date'])->toDateString(),
                'duration' => $l['duration'],
                'reason' => $l['reason'],
            ];
        })->values();

        $absencesJson = $absenceRecords->sortBy('date_raw')->map(function($a) {
            return [
                'name' => $a['employee_name'],
                'email' => $a['employee_email'],
                'date' => $a['date'],
                'date_raw' => $a['date_raw'],
                'day' => $a['day'],
            ];
        })->values();
    @endphp

    <script>
        function attendanceReport() {
            return {
                search: '',
                employeeFilter: 'all',
                dateSort: 'newest',
                absenceSort: 'all',
                leavePage: 1,
                absencePage: 1,
                perPage: 5,
                today: new Date().toISOString().split('T')[0],

                leaves: @json($leavesJson),
                absences: @json($absencesJson),

                maxAbsencesAllowed: {{ $maxAbsencesAllowed }},
                flaggedEmployees: @json($flaggedEmployees),
                absenceCounts: @json($absenceCountsByEmployee),

                resetPages() {
                    this.leavePage = 1;
                    this.absencePage = 1;
                },

                getAbsenceStatus(name) {
                    const count = this.absenceCounts[name] || 0;
                    if (count >= this.maxAbsencesAllowed) return 'exceeded';
                    if (count >= this.maxAbsencesAllowed - 2) return 'warning';
                    return 'normal';
                },

                matchesFilter(name, dateRaw) {
                    if (this.search && !name.toLowerCase().includes(this.search.toLowerCase())) return false;
                    if (this.employeeFilter !== 'all' && name !== this.employeeFilter) return false;
                    if (this.dateSort === 'today' && dateRaw !== this.today) return false;
                    return true;
                },

                filteredLeaves() {
                    let result = this.leaves.filter(l => this.matchesFilter(l.name, l.date_raw));
                    if (this.dateSort === 'newest') result.sort((a, b) => b.date_raw.localeCompare(a.date_raw));
                    else if (this.dateSort === 'oldest') result.sort((a, b) => a.date_raw.localeCompare(b.date_raw));
                    return result;
                },

                filteredAbsences() {
                    let result = this.absences.filter(a => this.matchesFilter(a.name, a.date_raw));

                    // Apply absence sort filter
                    if (this.absenceSort === 'exceeded') {
                        result = result.filter(a => this.getAbsenceStatus(a.name) === 'exceeded');
                    } else if (this.absenceSort === 'warning') {
                        result = result.filter(a => this.getAbsenceStatus(a.name) === 'warning');
                    } else if (this.absenceSort === 'none') {
                        result = result.filter(a => this.getAbsenceStatus(a.name) === 'normal');
                    }

                    if (this.dateSort === 'newest') result.sort((a, b) => b.date_raw.localeCompare(a.date_raw));
                    else if (this.dateSort === 'oldest') result.sort((a, b) => a.date_raw.localeCompare(b.date_raw));
                    return result;
                },

                paginatedLeaves() {
                    const start = (this.leavePage - 1) * this.perPage;
                    return this.filteredLeaves().slice(start, start + this.perPage);
                },

                paginatedAbsences() {
                    const start = (this.absencePage - 1) * this.perPage;
                    return this.filteredAbsences().slice(start, start + this.perPage);
                },

                totalLeavePages() {
                    return Math.ceil(this.filteredLeaves().length / this.perPage);
                },

                totalAbsencePages() {
                    return Math.ceil(this.filteredAbsences().length / this.perPage);
                },
            };
        }
    </script>
</x-layouts.general-employer>
