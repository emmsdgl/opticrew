<x-layouts.general-manager :title="'Manager Dashboard'">
    <section role="status" class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">

        <!-- Left Panel - Dashboard Content -->
        <div class="flex flex-col gap-6 flex-1 w-full rounded-lg p-4">

            <!-- Inner Up - Dashboard Header (Hero Card) -->
            <div id="tour-mgr-welcome" class="w-full rounded-lg h-40 sm:h-44 md:h-48 flex items-center">
                <x-herocard :headerName="Auth::user()->name ?? 'Manager'"
                    headerDesc="Welcome back. Here's what's happening with your workforce today."
                    headerIcon="hero-employer" />
            </div>

            <!-- Calendar -->
            <x-labelwithvalue label="My Calendar" count="" />
            <div id="tour-mgr-calendar"
                class="w-full pb-6 rounded-lg h-auto sm:h-72 md:h-80 lg:h-auto bg-white shadow-sm dark:bg-gray-800/40">
                <x-calendar :holidays="$holidays" :taskDates="$taskDates" calendarId="manager-dashboard" />
            </div>

            @php
                $managerDashboardTasks = $allTasks
                    ->map(function ($task) {
                        // task_description may include notes (e.g. "Final Cleaning - extra notes")
                        // Strip everything after the first " - " so the dropdown shows clean service categories
                        $rawDescription = $task->task_description ?? 'General Cleaning';
                        $service = trim(explode(' - ', $rawDescription)[0]);
                        if ($service === '') {
                            $service = 'General Cleaning';
                        }

                        return [
                            'id' => $task->id,
                            'date' => \Carbon\Carbon::parse($task->scheduled_date)->format('Y-m-d'),
                            'location' => $task->location->name ?? 'Unknown Location',
                            'service' => $service,
                            'time' => $task->scheduled_time
                                ? \Carbon\Carbon::parse($task->scheduled_time)->format('H:i')
                                : null,
                            'duration' => $task->duration,
                            'status' => $task->status,
                            'team_count' => $task->assignedEmployees ? $task->assignedEmployees->count() : 0,
                        ];
                    })
                    ->values();
            @endphp

            {{-- Tasks payload + today date for the Alpine component --}}
            <script>
                window.managerDashboardTasksData = @json($managerDashboardTasks);
                window.managerDashboardToday = @json(\Carbon\Carbon::today()->format('Y-m-d'));
            </script>

            <!-- Inner Bottom - Tasks list (Alpine-driven, filtered by calendar) -->
            <div id="tour-mgr-tasks" class="flex flex-col flex-1" x-data="managerDashboardTasks">

                <div class="flex flex-row justify-between items-center">
                    <div class="py-4 px-0">
                        <x-labelwithvalue label="Tasks" count="" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="selectedDateDisplay"></p>
                    </div>


                    {{-- Service Filter Dropdown (replaces "View All") --}}
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-filter text-xs"></i>
                            <span class="text-xs"
                                x-text="selectedService === 'all' ? 'Filter by Service' : selectedService"></span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10 max-h-80 overflow-y-auto">
                            <div class="py-1">
                                <button type="button" @click="selectedService = 'all'; open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    All Services
                                </button>
                                <template x-for="service in availableServices" :key="service">
                                    <button type="button" @click="selectedService = service; open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        x-text="service"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-lg my-6 bg-white shadow-sm dark:bg-gray-800/40 h-auto">
                    <div class="p-4 md:p-5">
                        <template x-if="filteredTasks.length > 0">
                            <div class="space-y-3">
                                <template x-for="task in filteredTasks" :key="task.id">
                                    <div
                                        class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800/60 hover:shadow-md hover:border-blue-200 dark:hover:border-blue-700 transition-all">
                                        <!-- Status dot -->
                                        <div class="flex-shrink-0">
                                            <div class="w-3 h-3 rounded-full" :class="statusDot(task.status)"></div>
                                        </div>

                                        <!-- Task info -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate"
                                                x-text="task.location"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                <i class="fa-regular fa-clock mr-1"></i>
                                                <span x-text="formatTime(task.time)"></span>
                                                <template x-if="task.duration">
                                                    <span><span class="mx-1">•</span><span
                                                            x-text="task.duration + ' min'"></span></span>
                                                </template>
                                            </p>
                                        </div>

                                        <!-- Status badge -->
                                        <div class="flex-shrink-0">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="statusBadgeClass(task.status)"
                                                x-text="statusLabel(task.status)"></span>
                                        </div>

                                        <!-- Team count -->
                                        <div
                                            class="flex-shrink-0 flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                            <i class="fa-solid fa-user-group"></i>
                                            <span x-text="task.team_count"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="filteredTasks.length === 0">
                            <!-- Empty State (matches client dashboard) -->
                            <div class="flex flex-col items-center justify-center py-16 px-6 text-center h-auto">
                                <div class="w-48 h-48 mb-6 flex items-center justify-center">
                                    <img src="{{ asset('images/icons/no-items-found.svg') }}" alt="No tasks"
                                        class="w-full h-full object-contain opacity-80 dark:opacity-60">
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                    <span
                                        x-text="selectedDate === todayDate ? 'No tasks scheduled today' : 'No tasks scheduled for this date'"></span>
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mb-3">
                                    There are no tasks for the selected date. You can create a new task to get started.
                                </p>
                                <a href="{{ route('manager.schedule') }}"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    + Create a new task
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </div>
        <!-- /Left Panel -->

        <!-- Right Panel - Task Overview & Activity -->
        <div id="tour-mgr-activity" class="flex flex-col gap-3 w-full lg:w-1/3 rounded-lg h-auto">
            @php
                $taskOverviewTotal =
                    ($taskOverview['completed'] ?? 0) +
                    ($taskOverview['inProgress'] ?? 0) +
                    ($taskOverview['scheduled'] ?? 0) +
                    ($taskOverview['onHold'] ?? 0);
            @endphp

            <!-- Task Status Overview - Pie Chart -->
            <div class="py-4 px-0">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Task Overview</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">This week's task distribution</p>
            </div>
            <div class="bg-white dark:bg-transparent rounded-xl">
                <div class="p-4 md:p-5">
                    <div class="relative h-56">
                        <canvas id="taskOverviewChart"></canvas>
                        @if ($taskOverviewTotal === 0)
                            {{-- Overlay label for the empty grayscale doughnut --}}
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <span class="text-xs font-medium text-gray-400 dark:text-gray-500">No tasks this
                                    week</span>
                            </div>
                        @endif
                    </div>

                    {{-- Custom legend with values (color-coded regardless of data state) --}}
                    <div class="my-6 grid grid-cols-2 gap-x-3 gap-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-2.5 h-2.5 rounded-full bg-green-500 flex-shrink-0"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-300 truncate">Completed</span>
                            </div>
                            <span
                                class="text-xs font-semibold text-gray-900 dark:text-white">{{ $taskOverview['completed'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-2.5 h-2.5 rounded-full bg-blue-500 flex-shrink-0"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-300 truncate">In Progress</span>
                            </div>
                            <span
                                class="text-xs font-semibold text-gray-900 dark:text-white">{{ $taskOverview['inProgress'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-2.5 h-2.5 rounded-full bg-gray-400 flex-shrink-0"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-300 truncate">Scheduled</span>
                            </div>
                            <span
                                class="text-xs font-semibold text-gray-900 dark:text-white">{{ $taskOverview['scheduled'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-2.5 h-2.5 rounded-full bg-yellow-500 flex-shrink-0"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-300 truncate">On Hold</span>
                            </div>
                            <span
                                class="text-xs font-semibold text-gray-900 dark:text-white">{{ $taskOverview['onHold'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-4 px-0">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Workforce Summary</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Funnel of activity across employees,
                    tasks and locations

                </p>
            </div>
            <!-- Statistics Funnel Chart -->
            <div id="tour-mgr-stats"
                class="bg-white dark:bg-transparent rounded-xl">
                <div class="p-4 md:p-5">
                    @php
                        $workforceFunnel = [
                            ['label' => 'Total Employees', 'value' => $stats['totalEmployees'] ?? 0],
                            ['label' => 'On Duty', 'value' => $stats['onDuty'] ?? 0],
                            ['label' => 'This Week Tasks', 'value' => $stats['weekTasks'] ?? 0],
                            ['label' => "Today's Tasks", 'value' => $stats['todayTasks'] ?? 0],
                            ['label' => 'Completed Today', 'value' => $stats['completedToday'] ?? 0],
                            ['label' => 'Active Locations', 'value' => $stats['locations'] ?? 0],
                        ];
                    @endphp
                    <x-material-ui.funnel-chart :items="$workforceFunnel" />
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div
                    class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h2>
                    <a href="{{ route('manager.activity') }}"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        View All
                    </a>
                </div>
                <div class="p-4 md:p-5">
                    @if (count($recentActivity ?? []) > 0)
                        <div class="space-y-4">
                            @foreach ($recentActivity as $activity)
                                <div class="flex gap-3">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                        <i
                                            class="fa-solid fa-{{ $activity['icon'] ?? 'circle-info' }} text-xs text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $activity['message'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $activity['time'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No recent activity</p>
                    @endif
                </div>
            </div>
        </div>
        </div>
        <!-- /Right Panel -->
    </section>

    {{-- Task Overview Pie Chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('taskOverviewChart');
            if (!canvas || typeof Chart === 'undefined') return;

            const counts = [
                {{ $taskOverview['completed'] ?? 0 }},
                {{ $taskOverview['inProgress'] ?? 0 }},
                {{ $taskOverview['scheduled'] ?? 0 }},
                {{ $taskOverview['onHold'] ?? 0 }}
            ];
            const totalCount = counts.reduce((a, b) => a + b, 0);
            const isEmpty = totalCount === 0;

            const data = isEmpty ? {
                // When there is no data, draw a single full grayscale ring as a placeholder
                labels: ['No tasks'],
                datasets: [{
                    data: [1],
                    backgroundColor: ['rgba(209, 213, 219, 0.6)'], // gray-300 @ 60%
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 0
                }]
            } : {
                labels: ['Completed', 'In Progress', 'Scheduled', 'On Hold'],
                datasets: [{
                    data: counts,
                    backgroundColor: [
                        'rgb(34, 197, 94)', // green-500
                        'rgb(59, 130, 246)', // blue-500
                        'rgb(156, 163, 175)', // gray-400
                        'rgb(234, 179, 8)' // yellow-500
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            };

            new Chart(canvas, {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            display: false
                        }, // we render a custom legend below
                        tooltip: {
                            enabled: !isEmpty, // no tooltips on the empty placeholder
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const value = context.parsed;
                                    const pct = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return context.label + ': ' + value + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

    {{-- Alpine component registration for the manager dashboard tasks list --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('managerDashboardTasks', () => ({
                allTasks: window.managerDashboardTasksData || [],
                todayDate: window.managerDashboardToday,
                selectedDate: window.managerDashboardToday,
                selectedService: 'all',

                init() {
                    document.addEventListener('calendar-date-selected', (e) => {
                        if (e.detail && e.detail.calendarId === 'manager-dashboard') {
                            this.selectedDate = e.detail.date;
                        }
                    });
                },

                get availableServices() {
                    const set = new Set();
                    this.allTasks.forEach(t => {
                        if (t.service) set.add(t.service);
                    });
                    return Array.from(set).sort();
                },

                get filteredTasks() {
                    return this.allTasks
                        .filter(t => t.date === this.selectedDate)
                        .filter(t => this.selectedService === 'all' || t.service === this
                            .selectedService)
                        .sort((a, b) => (a.time || '').localeCompare(b.time || ''));
                },

                get selectedDateLabel() {
                    if (this.selectedDate === this.todayDate) return "Today's Tasks";
                    const d = new Date(this.selectedDate);
                    return d.toLocaleDateString('en-US', {
                        weekday: 'long',
                        month: 'long',
                        day: 'numeric'
                    }) + ' Tasks';
                },

                get selectedDateDisplay() {
                    if (!this.selectedDate) return 'No date selected';
                    // Build the date safely from Y-m-d to avoid timezone shifting
                    const parts = this.selectedDate.split('-');
                    if (parts.length !== 3) return 'No date selected';
                    const d = new Date(
                        parseInt(parts[0], 10),
                        parseInt(parts[1], 10) - 1,
                        parseInt(parts[2], 10)
                    );
                    if (isNaN(d.getTime())) return 'No date selected';
                    const formatted = d.toLocaleDateString('en-US', {
                        weekday: 'long',
                        month: 'long',
                        day: 'numeric',
                        year: 'numeric'
                    });
                    return this.selectedDate === this.todayDate ?
                        formatted + ' (Today)' :
                        formatted;
                },

                statusDot(status) {
                    if (status === 'Completed') return 'bg-green-500';
                    if (status === 'In Progress') return 'bg-blue-500 animate-pulse';
                    if (status === 'On Hold') return 'bg-yellow-500';
                    return 'bg-gray-400';
                },

                statusBadgeClass(status) {
                    if (status === 'Completed')
                        return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
                    if (status === 'In Progress')
                        return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
                    if (status === 'On Hold')
                        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
                    return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                },

                statusLabel(status) {
                    return status || 'Scheduled';
                },

                formatTime(time) {
                    if (!time) return 'No time set';
                    const parts = time.split(':');
                    let hour = parseInt(parts[0], 10);
                    const minutes = parts[1];
                    const ampm = hour >= 12 ? 'PM' : 'AM';
                    hour = hour % 12;
                    if (hour === 0) hour = 12;
                    return hour + ':' + minutes + ' ' + ampm;
                }
            }));
        });
    </script>

    <x-guided-tour tourName="manager-dashboard" :steps="json_encode([
        [
            'title' => 'Welcome to the Manager Dashboard',
            'description' =>
                'This is your command center for managing tasks, employees, and schedules. Let us walk you through the key features.',
            'side' => 'bottom',
            'align' => 'center',
        ],
        [
            'element' => '#sidebar',
            'title' => 'Navigation Menu',
            'description' =>
                'Access Schedule management, Checklists, Employee oversight, Reports, Activity logs, and History from here.',
            'side' => 'right',
            'align' => 'start',
        ],
        [
            'element' => '#tour-mgr-stats',
            'title' => 'Quick Statistics',
            'description' =>
                'Get an at-a-glance view of today\'s tasks, employee count on duty, weekly schedule, and active locations.',
            'side' => 'bottom',
            'align' => 'center',
        ],
        [
            'element' => '#tour-mgr-tasks',
            'title' => 'Today\'s Tasks',
            'description' =>
                'View and manage all tasks scheduled for today. Each task shows its status, location, time, and assigned team size.',
            'side' => 'right',
            'align' => 'start',
        ],
        [
            'element' => '#tour-mgr-activity',
            'title' => 'Task Overview & Activity',
            'description' =>
                'Monitor task completion status and recent activity. Stay updated on what\'s happening across your managed locations.',
            'side' => 'left',
            'align' => 'start',
        ],
    ])" />
</x-layouts.general-manager>
