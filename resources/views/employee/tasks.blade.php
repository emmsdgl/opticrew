
<x-layouts.general-employee :title="'Tasks'">

    {{-- MOBILE LAYOUT (< 1024px) - Hidden on large screens --}}
    <div class="lg:hidden">
        @include('employee.mobile.tasks')
    </div>

    {{-- DESKTOP LAYOUT (≥ 1024px) - Hidden on small screens --}}
    <section role="status" class="w-full hidden lg:flex flex-col gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">

        <!-- Flash Messages (session based) -->
        @if(session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6" role="alert">
                <p class="font-semibold">Success!</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
                <p class="font-semibold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Compact Clock In/Out Status Indicator -->
        @if($isClockedIn)
            {{-- Compact status indicator when clocked in --}}
            <div class="flex items-center gap-2 p-6 mb-4 mx-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-sm font-medium text-green-800 dark:text-green-300">
                    <i class="fas fa-check-circle"></i> You're Present Today, Clocked in at {{ $clockInTime }}
                </span>
            </div>
        @else
            {{-- Warning banner when not clocked in --}}
            <div class="mb-4 p-4 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 rounded-lg">
                <div class="flex flex-col sm:flex-row items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-xl mt-0.5"></i>
                    <div class="flex-1">
                        <h4 class="font-bold text-orange-800 dark:text-orange-300 text-sm">Clock In Required</h4>
                        <p class="text-orange-700 dark:text-orange-400 text-sm mt-1">
                            You must clock in before starting tasks. All task actions will be disabled until you clock in.
                        </p>
                        <a href="{{ route('employee.dashboard') }}"
                           class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">
                            <i class="fas fa-arrow-left"></i>
                            Go to Dashboard to Clock In
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tasks Calendar Section -->
        <div class="flex flex-col gap-6 flex-1 w-full rounded-lg p-4">
            <x-labelwithvalue label="Task Calendar" count="" />

            @php
                // Transform tasks to calendar events format
                $allTasks = $todayTasks->concat($upcomingTasks);
                $events = $allTasks->map(function($task) {
                    // Color based on status
                    $statusColors = [
                        'Scheduled' => '#3B82F6',      // Blue
                        'In Progress' => '#10B981',    // Green
                        'On Hold' => '#F59E0B',        // Yellow
                        'Completed' => '#6B7280',      // Gray
                        'Pending' => '#FFA500',        // Orange
                    ];

                    $color = $statusColors[$task->status] ?? '#6B7280'; // Default gray

                    // Parse scheduled time
                    $scheduledTime = $task->scheduled_time ? \Carbon\Carbon::parse($task->scheduled_time) : \Carbon\Carbon::parse($task->scheduled_date)->setTime(9, 0);
                    $startTime = $scheduledTime->format('H:i');

                    // Calculate end time based on estimated duration
                    $duration = $task->estimated_duration_minutes ?? 60;
                    $endTime = $scheduledTime->copy()->addMinutes($duration)->format('H:i');

                    // Format time display
                    $timeDisplay = $scheduledTime->format('h:i A') . ' - ' . $scheduledTime->copy()->addMinutes($duration)->format('h:i A');

                    return [
                        'id' => $task->id,
                        'title' => $task->task_description,
                        'date' => \Carbon\Carbon::parse($task->scheduled_date)->format('Y-m-d'),
                        'startTime' => $startTime,
                        'endTime' => $endTime,
                        'time' => $timeDisplay,
                        'description' => ($task->location ? $task->location->location_name : 'External Client') . ' - ' . $duration . ' min',
                        'color' => $color,
                        'status' => $task->status,
                        'position' => 0,
                        'height' => 60
                    ];
                })->toArray();
            @endphp

            <x-employee-components.task-calendar :events="$events" initial-view="month" />
        </div>

        <!-- Divider -->
        <hr class="my-6 border-gray-300 dark:border-gray-700">

        <!-- Sort Script - Define before Alpine initializes -->
        <script>
        // Sort function for all task sections - defined early for Alpine
        window.sortTasks = function(section, field, direction) {
            console.log('Sorting tasks:', section, field, direction);

            // Map section names to their container IDs
            const sectionIds = {
                'today': 'today-tasks-list',
                'pending': 'pending-tasks-list',
                'history': 'history-tasks-list'
            };

            // Get the container for this section
            const listContainer = document.getElementById(sectionIds[section]);

            if (!listContainer) {
                console.error('List container not found for section:', section);
                return;
            }

            // Get all task items
            const taskItems = Array.from(listContainer.querySelectorAll('[data-task-item]'));
            console.log('Found task items:', taskItems.length);

            if (taskItems.length === 0) {
                console.log('No tasks to sort');
                return;
            }

            // Sort the task items
            taskItems.sort((a, b) => {
                let valueA, valueB;

                switch(field) {
                    case 'service':
                        valueA = (a.getAttribute('data-service') || '').toLowerCase();
                        valueB = (b.getAttribute('data-service') || '').toLowerCase();
                        break;
                    case 'date':
                        // Parse dates for proper comparison
                        valueA = a.getAttribute('data-date') || '';
                        valueB = b.getAttribute('data-date') || '';
                        // Convert to Date objects for proper comparison
                        const dateA = new Date(valueA);
                        const dateB = new Date(valueB);
                        return direction === 'asc' ? dateA - dateB : dateB - dateA;
                    case 'time':
                        valueA = a.getAttribute('data-time') || '';
                        valueB = b.getAttribute('data-time') || '';
                        break;
                    case 'client':
                        valueA = (a.getAttribute('data-client') || '').toLowerCase();
                        valueB = (b.getAttribute('data-client') || '').toLowerCase();
                        break;
                    default:
                        return 0;
                }

                // String comparison
                if (direction === 'asc') {
                    return valueA.localeCompare(valueB);
                } else {
                    return valueB.localeCompare(valueA);
                }
            });

            // Get the parent container for the task items
            const taskParent = taskItems[0]?.parentElement;
            if (!taskParent) {
                console.error('Task parent not found');
                return;
            }

            // Re-append items in sorted order
            taskItems.forEach(item => {
                taskParent.appendChild(item);
            });

            console.log('Tasks sorted successfully');
        }

        // Filter tasks by service type
        window.filterTasksByService = function(section, serviceType) {
            console.log('Filtering tasks by service:', section, serviceType);

            // Map section names to their container IDs
            const sectionIds = {
                'today': 'today-tasks-list',
                'pending': 'pending-tasks-list',
                'history': 'history-tasks-list'
            };

            // Get the container for this section
            const listContainer = document.getElementById(sectionIds[section]);

            if (!listContainer) {
                console.error('List container not found for section:', section);
                return;
            }

            // Get all task items
            const taskItems = Array.from(listContainer.querySelectorAll('[data-task-item]'));
            console.log('Found task items:', taskItems.length);

            if (taskItems.length === 0) {
                console.log('No tasks to filter');
                return;
            }

            // Filter or show all tasks
            taskItems.forEach(item => {
                const service = item.getAttribute('data-service') || '';

                if (serviceType === 'all') {
                    // Show all tasks
                    item.style.display = '';
                } else {
                    // Check if service contains the filter text (case-insensitive)
                    if (service.toLowerCase().includes(serviceType.toLowerCase())) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });

            console.log('Tasks filtered successfully');
        }
        </script>

        <!-- Today's Tasks Section -->
        <div class="flex flex-col gap-6 flex-1 w-full rounded-lg p-4">
            <div class="flex items-center justify-between">
                <x-labelwithvalue label="My Tasks for Today" :count="'(' . $todayTasks->count() . ')'" />
                <div class="flex items-center gap-2">
                    <!-- Sort by Service Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-filter text-xs"></i>
                            <span class="text-xs">Filter by Service</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="filterTasksByService('today', 'all'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    All Services
                                </button>
                                <button type="button" @click="filterTasksByService('today', 'Deep Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Deep Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('today', 'Snowout Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Snowout Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('today', 'Daily Room Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Daily Room Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('today', 'Hotel Cleaning Service'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Hotel Cleaning Service
                                </button>
                                <button type="button" @click="filterTasksByService('today', 'Student Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Student Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('today', 'Final Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Final Cleaning
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sort by Order Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-sort-amount-down text-xs"></i>
                            <span class="text-xs">Sort by Order</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="sortTasks('today', 'date', 'desc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-down text-xs w-4"></i>
                                    Newest First
                                </button>
                                <button type="button" @click="sortTasks('today', 'date', 'asc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-up text-xs w-4"></i>
                                    Oldest First
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                // Transform today's tasks to the format expected by task-overview-list component
                $todayTasksFormatted = $todayTasks->map(function($task) use ($isClockedIn) {
                    $scheduledTime = $task->scheduled_time
                        ? \Carbon\Carbon::parse($task->scheduled_time)
                        : \Carbon\Carbon::parse($task->scheduled_date)->setTime(9, 0);

                    $duration = $task->estimated_duration_minutes ?? 60;

                    // Get client name
                    $clientName = 'Unknown Client';
                    if ($task->location && $task->location->contractedClient) {
                        $clientName = $task->location->contractedClient->name;
                    } elseif ($task->client) {
                        $clientName = trim(($task->client->first_name ?? '') . ' ' . ($task->client->last_name ?? ''));
                    }

                    return [
                        'service' => $task->task_description,
                        'status' => $task->status,
                        'service_date' => \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y'),
                        'service_time' => $scheduledTime->format('g:i A') . ' (' . $duration . ' min)',
                        'description' => ($task->location ? $task->location->location_name : 'External Client')
                                       . ($task->assigned_by ? ' • Assigned by: ' . $task->assigned_by->name : ''),
                        'client' => $clientName,
                        'action_url' => route('employee.tasks.show', ['task' => $task->id, 'from' => 'tasks']),
                        'action_label' => 'View Details',
                        'menu_items' => [] // Task start/complete actions will be added later
                    ];
                })->toArray();
            @endphp

            <div id="today-tasks-list" class="h-96 overflow-y-auto border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
                <x-employee-components.task-overview-list
                    :items="$todayTasksFormatted"
                    fixedHeight="24rem"
                    maxHeight="30rem"
                    emptyTitle="No tasks assigned for today"
                    emptyMessage="Check back later or contact your supervisor for task assignments." />
            </div>
        </div>

        <!-- Divider -->
        <hr class="my-6 border-gray-300 dark:border-gray-700">

        <!-- To Be Approved Section -->
        <div class="flex flex-col gap-6 w-full rounded-lg p-4">
            <div class="flex items-center justify-between">
                <x-labelwithvalue label="To Be Approved" :count="'(' . $pendingApprovalTasks->count() . ')'" />
                <div class="flex items-center gap-2">
                    <!-- Sort by Service Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-filter text-xs"></i>
                            <span class="text-xs">Filter by Service</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="filterTasksByService('pending', 'all'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    All Services
                                </button>
                                <button type="button" @click="filterTasksByService('pending', 'Deep Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Deep Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('pending', 'Snowout Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Snowout Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('pending', 'Daily Room Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Daily Room Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('pending', 'Hotel Cleaning Service'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Hotel Cleaning Service
                                </button>
                                <button type="button" @click="filterTasksByService('pending', 'Student Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Student Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('pending', 'Final Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Final Cleaning
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sort by Order Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-sort-amount-down text-xs"></i>
                            <span class="text-xs">Sort by Order</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="sortTasks('pending', 'date', 'desc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-down text-xs w-4"></i>
                                    Newest First
                                </button>
                                <button type="button" @click="sortTasks('pending', 'date', 'asc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-up text-xs w-4"></i>
                                    Oldest First
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                // Transform pending approval tasks to the format expected by task-overview-list component
                $pendingApprovalTasksFormatted = $pendingApprovalTasks->map(function($task) {
                    $scheduledTime = $task->scheduled_time
                        ? \Carbon\Carbon::parse($task->scheduled_time)
                        : \Carbon\Carbon::parse($task->scheduled_date)->setTime(9, 0);

                    $duration = $task->estimated_duration_minutes ?? 60;

                    // Get client name
                    $clientName = 'Unknown Client';
                    if ($task->location && $task->location->contractedClient) {
                        $clientName = $task->location->contractedClient->name;
                    } elseif ($task->client) {
                        $clientName = trim(($task->client->first_name ?? '') . ' ' . ($task->client->last_name ?? ''));
                    }

                    return [
                        'service' => $task->task_description,
                        'status' => 'Pending Approval',
                        'service_date' => \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y'),
                        'service_time' => $scheduledTime->format('g:i A') . ' (' . $duration . ' min)',
                        'description' => ($task->location ? $task->location->location_name : 'External Client')
                                       . ($task->assignedBy ? ' • Assigned by: ' . $task->assignedBy->name : ''),
                        'client' => $clientName,
                        'action_url' => route('employee.tasks.show', ['task' => $task->id, 'from' => 'tasks']),
                        'action_label' => 'View Details',
                        'menu_items' => []
                    ];
                })->toArray();
            @endphp

            <div id="pending-tasks-list" class="border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
                <x-employee-components.task-overview-list
                    :items="$pendingApprovalTasksFormatted"
                    fixedHeight="32rem"
                    maxHeight="32rem"
                    emptyTitle="No tasks pending approval"
                    emptyMessage="All assigned tasks have been reviewed." />
            </div>

            <!-- Hidden forms for approve/decline actions -->
            @foreach($pendingApprovalTasks as $task)
                <form id="approve-form-{{ $task->id }}" action="{{ route('employee.tasks.approve', $task->id) }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <form id="decline-form-{{ $task->id }}" action="{{ route('employee.tasks.decline', $task->id) }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endforeach
        </div>

        <!-- Divider -->
        <hr class="my-6 border-gray-300 dark:border-gray-700">

        <!-- Tasks History Section -->
        <div class="flex flex-col gap-6 w-full rounded-lg p-4">
            <div class="flex items-center justify-between">
                <x-labelwithvalue label="Tasks History" :count="'(' . $completedTasks->count() . ')'" />
                <div class="flex items-center gap-2">
                    <!-- Sort by Service Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-filter text-xs"></i>
                            <span class="text-xs">Filter by Service</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="filterTasksByService('history', 'all'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    All Services
                                </button>
                                <button type="button" @click="filterTasksByService('history', 'Deep Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Deep Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('history', 'Snowout Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Snowout Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('history', 'Daily Room Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Daily Room Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('history', 'Hotel Cleaning Service'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Hotel Cleaning Service
                                </button>
                                <button type="button" @click="filterTasksByService('history', 'Student Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Student Cleaning
                                </button>
                                <button type="button" @click="filterTasksByService('history', 'Final Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Final Cleaning
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sort by Order Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-sort-amount-down text-xs"></i>
                            <span class="text-xs">Sort by Order</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="sortTasks('history', 'date', 'desc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-down text-xs w-4"></i>
                                    Newest First
                                </button>
                                <button type="button" @click="sortTasks('history', 'date', 'asc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-up text-xs w-4"></i>
                                    Oldest First
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                // Transform completed tasks to the format expected by task-overview-list component
                $allTasksFormatted = $completedTasks->map(function($task) use ($isClockedIn) {
                    $scheduledTime = $task->scheduled_time
                        ? \Carbon\Carbon::parse($task->scheduled_time)
                        : \Carbon\Carbon::parse($task->scheduled_date)->setTime(9, 0);

                    $duration = $task->estimated_duration_minutes ?? 60;

                    // Get client name
                    $clientName = 'Unknown Client';
                    if ($task->location && $task->location->contractedClient) {
                        $clientName = $task->location->contractedClient->name;
                    } elseif ($task->client) {
                        $clientName = trim(($task->client->first_name ?? '') . ' ' . ($task->client->last_name ?? ''));
                    }

                    // Build team information if available
                    $teamInfo = '';
                    if ($task->optimizationTeam) {
                        if ($task->optimizationTeam->car) {
                            $teamInfo .= ' • Vehicle: ' . $task->optimizationTeam->car->car_name;
                        }
                        if ($task->optimizationTeam->members->isNotEmpty()) {
                            $teamMembers = $task->optimizationTeam->members->pluck('employee.user.name')->filter()->take(3)->join(', ');
                            if ($task->optimizationTeam->members->count() > 3) {
                                $teamMembers .= ' +' . ($task->optimizationTeam->members->count() - 3) . ' more';
                            }
                            $teamInfo .= ' • Team: ' . $teamMembers;
                        }
                    }

                    return [
                        'service' => $task->task_description,
                        'status' => $task->status,
                        'service_date' => \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y'),
                        'service_time' => $scheduledTime->format('g:i A') . ' (' . $duration . ' min)',
                        'description' => ($task->location ? $task->location->location_name : 'External Client')
                                       . ($task->assigned_by ? ' • Assigned by: ' . $task->assigned_by->name : '')
                                       . $teamInfo,
                        'client' => $clientName,
                        'action_url' => route('employee.tasks.show', ['task' => $task->id, 'from' => 'tasks']),
                        'action_label' => 'View Details',
                        'menu_items' => [] // Task start/complete actions will be added later
                    ];
                })->toArray();
            @endphp

            <div id="history-tasks-list" class="h-96 overflow-y-auto border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
                <x-employee-components.task-overview-list
                    :items="$allTasksFormatted"
                    fixedHeight="24rem"
                    maxHeight="30rem"
                    emptyTitle="No tasks in history"
                    emptyMessage="Your completed and upcoming tasks will appear here once assigned." />
            </div>
        </div>

        <!-- OLD GANTT CHART SECTION - REMOVED, replaced with functional task cards above -->
        {{-- Keep this commented block for reference if frontend developer wants gantt chart back later
        <div class="flex flex-col gap-6 flex-1 w-full rounded-lg p-4">
            <x-labelwithvalue label="My Schedule" count="" />
            @php
                $tasks = [
                    [
                        'label' => 'A',
                        'name' => 'ABC Company',
                        'subtitle' => 'Deep Cleaning',
                        'color' => '#3B82F6', // Blue
                        'start' => '2025-10-28',
                        'end' => '2025-10-31',
                        'percentage' => 55,
                        'due_date' => '2025-10-31',
                        'due_time' => '2:00 pm',
                        'team_name' => 'Team 1',
                        'team_members' => [
                            ['name' => 'John Doe', 'avatar' => ''],
                            ['name' => 'Jane Smith', 'avatar' => ''],
                            ['name' => 'Bob Johnson', 'avatar' => ''],
                        ]
                    ],
                    [
                        'label' => 'B',
                        'name' => 'StratEdge Consulting',
                        'subtitle' => 'Daily Room Cleaning',
                        'color' => '#9333EA', // Purple
                        'start' => '2025-10-29',
                        'end' => '2025-11-05',
                        'percentage' => 80,
                        'due_date' => '2025-11-05',
                        'due_time' => '5:00 pm',
                        'team_name' => 'Team 2',
                        'team_members' => [
                            ['name' => 'Alice Cooper', 'avatar' => ''],
                            ['name' => 'Charlie Brown', 'avatar' => ''],
                        ]
                    ],
                    [
                        'label' => 'C',
                        'name' => 'Noventis Corp',
                        'subtitle' => 'Full Daily Cleaning',
                        'color' => '#EC4899', // Pink
                        'start' => '2025-10-30',
                        'end' => '2025-11-03',
                        'percentage' => 65,
                        'due_date' => '2025-11-03',
                        'due_time' => '3:30 pm',
                        'team_name' => 'Team 3',
                        'team_members' => [
                            ['name' => 'David Lee', 'avatar' => ''],
                            ['name' => 'Eva Green', 'avatar' => ''],
                            ['name' => 'Frank White', 'avatar' => ''],
                            ['name' => 'Grace Hill', 'avatar' => ''],
                        ]
                    ],
                    [
                        'label' => 'D',
                        'name' => 'IntegriCore Partners',
                        'subtitle' => 'Snowout Cleaning',
                        'color' => '#F59E0B', // Orange/Yellow
                        'start' => '2025-11-01',
                        'end' => '2025-11-05',
                        'percentage' => 75,
                        'due_date' => '2025-11-05',
                        'due_time' => '11:00 am',
                        'team_name' => 'Team 1',
                        'team_members' => [
                            ['name' => 'Henry Ford', 'avatar' => ''],
                            ['name' => 'Iris Watson', 'avatar' => ''],
                        ]
                    ],
                ];
            @endphp

        --}}
    </section>
</x-layouts.general-employee>

@push('scripts')
<script>
// Listen for task-updated events and show toast notification
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Sort functions ready');

    let taskUpdateToast = null;

    window.addEventListener('task-updated', function(event) {
        const detail = event.detail;

        // Remove existing toast if any
        if (taskUpdateToast) {
            taskUpdateToast.remove();
        }

        // Create toast notification
        taskUpdateToast = document.createElement('div');
        taskUpdateToast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 flex items-center gap-3';
        taskUpdateToast.innerHTML = `
            <i class="fas fa-check-circle text-lg"></i>
            <span class="font-medium">Task updated successfully!</span>
        `;

        document.body.appendChild(taskUpdateToast);

        // Animate in
        setTimeout(() => {
            taskUpdateToast.style.transform = 'translateY(0)';
            taskUpdateToast.style.opacity = '1';
        }, 10);

        // Auto remove after 3 seconds
        setTimeout(() => {
            taskUpdateToast.style.transform = 'translateY(100px)';
            taskUpdateToast.style.opacity = '0';
            setTimeout(() => {
                if (taskUpdateToast && taskUpdateToast.parentNode) {
                    taskUpdateToast.remove();
                }
            }, 300);
        }, 3000);
    });
});
</script>
@endpush