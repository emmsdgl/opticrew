<x-layouts.general-employer :title="'Task Management'">
    <section role="status" class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">

        <!-- Task Calendar Section -->
        <div class="flex flex-col gap-6 flex-1 w-full rounded-lg p-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Task Calendar</h2>

            <!-- Pass data to the calendar -->
            <x-taskcalendar :clients="$clients" :events="$events" :booked-locations-by-date="$bookedLocationsByDate" :holidays="$holidays" />
        </div>

        <!-- Divider -->
        <hr class="my-6 border-gray-300 dark:border-gray-700">

        <!-- Tasks List Sections -->
        <div class="flex flex-col gap-6 w-full rounded-lg p-4">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">All Tasks</h2>

            @php
                // Group tasks by status and sort by most recent first
                $todoTasks = $tasks->where('status', 'todo')->sortByDesc('scheduled_date')->values();
                $inProgressTasks = $tasks->where('status', 'inprogress')->sortByDesc('scheduled_date')->values();
                $completedTasks = $tasks->where('status', 'completed')->sortByDesc('scheduled_date')->values();
            @endphp

            <!-- To Do Tasks Section -->
            <div class="flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                        To Do
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $todoTasks->count() }})</span>
                    </h3>
                </div>

                @php
                    // Transform to-do tasks for list display
                    $todoTasksFormatted = $todoTasks->map(function($task) {
                        return [
                            'service' => $task['title'],
                            'status' => 'Pending',
                            'service_date' => $task['date'],
                            'service_time' => $task['time'],
                            'description' => 'Client: ' . $task['client'] . ' • Priority: ' . $task['priority'],
                            'priority_color' => $task['priorityColor'],
                            'action_url' => route('admin.tasks.show', ['id' => $task['id'], 'from' => 'tasks']),
                            'action_label' => 'View Details',
                            'menu_items' => []
                        ];
                    })->toArray();
                @endphp

                <div class="max-h-96 overflow-y-auto border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
                    @if(count($todoTasksFormatted) > 0)
                        <x-employee-components.task-overview-list
                            :items="$todoTasksFormatted"
                            fixedHeight="24rem"
                            maxHeight="30rem"
                            emptyTitle="No pending tasks"
                            emptyMessage="All tasks are either in progress or completed." />
                    @else
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-check-circle text-3xl mb-3 opacity-50"></i>
                            <p class="font-semibold">No pending tasks</p>
                            <p class="text-sm">All tasks are either in progress or completed.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Divider -->
            <hr class="my-4 border-gray-300 dark:border-gray-700">

            <!-- In Progress Tasks Section -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        In Progress
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $inProgressTasks->count() }})</span>
                    </h3>
                </div>

                @php
                    // Transform in-progress tasks for list display
                    $inProgressTasksFormatted = $inProgressTasks->map(function($task) {
                        return [
                            'service' => $task['title'],
                            'status' => 'In Progress',
                            'service_date' => $task['date'],
                            'service_time' => $task['time'],
                            'description' => 'Client: ' . $task['client'] . ' • Priority: ' . $task['priority'],
                            'priority_color' => $task['priorityColor'],
                            'action_url' => route('admin.tasks.show', ['id' => $task['id'], 'from' => 'tasks']),
                            'action_label' => 'View Details',
                            'menu_items' => []
                        ];
                    })->toArray();
                @endphp

                <div class="max-h-96 overflow-y-auto border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
                    @if(count($inProgressTasksFormatted) > 0)
                        <x-employee-components.task-overview-list
                            :items="$inProgressTasksFormatted"
                            fixedHeight="24rem"
                            maxHeight="30rem"
                            emptyTitle="No tasks in progress"
                            emptyMessage="Start working on pending tasks to see them here." />
                    @else
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-tasks text-4xl mb-3 opacity-50"></i>
                            <p class="font-semibold">No tasks in progress</p>
                            <p class="text-sm">Start working on pending tasks to see them here.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Divider -->
            <hr class="my-4 border-gray-300 dark:border-gray-700">

            <!-- Completed Tasks Section -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        Completed
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $completedTasks->count() }})</span>
                    </h3>
                </div>

                @php
                    // Transform completed tasks for list display
                    $completedTasksFormatted = $completedTasks->map(function($task) {
                        return [
                            'service' => $task['title'],
                            'status' => 'Completed',
                            'service_date' => $task['date'],
                            'service_time' => $task['time'],
                            'description' => 'Client: ' . $task['client'] . ' • Priority: ' . $task['priority'],
                            'priority_color' => $task['priorityColor'],
                            'action_url' => route('admin.tasks.show', ['id' => $task['id'], 'from' => 'tasks']),
                            'action_label' => 'View Details',
                            'menu_items' => []
                        ];
                    })->toArray();
                @endphp

                <div class="max-h-96 overflow-y-auto border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
                    @if(count($completedTasksFormatted) > 0)
                        <x-employee-components.task-overview-list
                            :items="$completedTasksFormatted"
                            fixedHeight="24rem"
                            maxHeight="30rem"
                            emptyTitle="No completed tasks"
                            emptyMessage="Completed tasks will appear here." />
                    @else
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-clipboard-check text-4xl mb-3 opacity-50"></i>
                            <p class="font-semibold">No completed tasks</p>
                            <p class="text-sm">Completed tasks will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-layouts.general-employer>
