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

        <!-- Task Checklist Templates Section -->
        <div class="flex flex-col gap-4 w-full px-4" x-data="checklistTemplates()">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Task Checklist Templates</h2>
                <button @click="showAddModal = true"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Add Template
                </button>
            </div>

            <!-- Templates Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <template x-for="template in templates" :key="template.id">
                    <div class="bg-gray-800 dark:bg-gray-800 rounded-lg flex border-l-4 hover:bg-gray-750 dark:hover:bg-gray-750 transition-colors cursor-pointer"
                         :style="'border-left-color: ' + template.color">
                        <!-- Color Badge with Initials -->
                        <div class="flex items-center justify-center w-14 h-full min-h-[4rem]"
                             :style="'background-color: ' + template.color">
                            <span class="text-white font-bold text-sm" x-text="template.initials"></span>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 flex items-center justify-between px-4 py-3">
                            <div>
                                <h3 class="text-sm font-semibold text-white" x-text="template.name"></h3>
                                <p class="text-xs text-gray-400" x-text="template.itemCount + ' Items'"></p>
                            </div>

                            <!-- 3-dot Menu -->
                            <div class="relative" x-data="{ menuOpen: false }">
                                <button @click.stop="menuOpen = !menuOpen"
                                        class="p-1.5 text-gray-400 hover:text-white rounded transition-colors">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="menuOpen"
                                     @click.away="menuOpen = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 top-full mt-1 w-36 bg-gray-700 dark:bg-gray-700 rounded-lg shadow-lg border border-gray-600 dark:border-gray-600 z-50">
                                    <button @click="editTemplate(template); menuOpen = false"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-200 hover:bg-gray-600 flex items-center gap-2">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                        Edit
                                    </button>
                                    <button @click="duplicateTemplate(template); menuOpen = false"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-200 hover:bg-gray-600 flex items-center gap-2">
                                        <i class="fa-solid fa-copy text-xs"></i>
                                        Duplicate
                                    </button>
                                    <button @click="deleteTemplate(template.id); menuOpen = false"
                                            class="w-full px-4 py-2 text-left text-sm text-red-400 hover:bg-gray-600 flex items-center gap-2">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div x-show="templates.length === 0"
                     class="col-span-full p-8 text-center text-gray-500 dark:text-gray-400 border border-dashed border-gray-600 rounded-lg">
                    <i class="fa-solid fa-clipboard-list text-3xl mb-3 opacity-50"></i>
                    <p class="font-semibold">No checklist templates</p>
                    <p class="text-sm">Create templates to standardize task checklists.</p>
                </div>
            </div>

            <!-- Add Template Modal (Simple) -->
            <div x-show="showAddModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                 @click.self="closeModal()">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add New Template</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Template Name</label>
                            <input type="text"
                                   x-model="formData.name"
                                   placeholder="e.g., Daily Room Cleaning"
                                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                            <div class="flex gap-2">
                                <template x-for="color in colors" :key="color">
                                    <button type="button"
                                            @click="formData.color = color"
                                            class="w-8 h-8 rounded-lg border-2 transition-all"
                                            :style="'background-color: ' + color"
                                            :class="formData.color === color ? 'border-white scale-110' : 'border-transparent'">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default For Task Type</label>
                            <select x-model="formData.defaultFor"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="">None (Manual Assignment)</option>
                                <template x-for="taskType in taskTypes" :key="taskType.id">
                                    <option :value="taskType.id" x-text="taskType.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="closeModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button @click="saveTemplate()"
                                :disabled="!formData.name.trim()"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Create
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Template Modal (Full with Checklist Items) -->
            <div x-show="showEditModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                 @click.self="closeModal()">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Template</h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fa-solid fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Column: Template Settings & Available Items -->
                            <div class="space-y-6">
                                <!-- Template Settings -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Template Name</label>
                                        <input type="text"
                                               x-model="formData.name"
                                               placeholder="e.g., Daily Room Cleaning"
                                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <div class="flex flex-col gap-4">
                                        <div>
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-3">Default For</label>
                                            <select x-model="formData.defaultFor"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                                <option value="">None</option>
                                                <template x-for="taskType in taskTypes" :key="taskType.id">
                                                    <option :value="taskType.id" x-text="taskType.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-3">Color</label>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="color in colors" :key="color">
                                                    <button type="button"
                                                            @click="formData.color = color"
                                                            class="w-7 h-7 rounded-lg border-2 transition-all"
                                                            :style="'background-color: ' + color"
                                                            :class="formData.color === color ? 'border-white scale-110 ring-2 ring-offset-2 ring-offset-gray-800 ring-white/50' : 'border-transparent'">
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Available Checklist Items -->
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Available Task Items</h4>
                                        <button @click="showNewItemInput = !showNewItemInput"
                                                class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 font-medium">
                                            <i class="fa-solid fa-plus mr-1"></i> Add Item
                                        </button>
                                    </div>

                                    <!-- Add New Item Input -->
                                    <div x-show="showNewItemInput" x-collapse class="mb-3">
                                        <div class="flex gap-2">
                                            <input type="text"
                                                   x-model="newChecklistItem"
                                                   @keydown.enter="addChecklistItem()"
                                                   placeholder="Enter new item name"
                                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            <button @click="addChecklistItem()"
                                                    :disabled="!newChecklistItem.trim()"
                                                    class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                                Add
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Pill-style Items -->
                                    <div class="flex flex-wrap gap-2 max-h-[150px] overflow-y-auto p-1">
                                        <template x-for="item in availableChecklistItems" :key="item.id">
                                            <label class="inline-flex items-center px-3 py-1.5 rounded-full border cursor-pointer transition-all duration-200 select-none"
                                                   :class="formData.enabledItems.includes(item.id)
                                                       ? 'bg-blue-100 border-blue-300 dark:bg-blue-900/40 dark:border-blue-600'
                                                       : 'bg-gray-100 border-gray-200 dark:bg-gray-700/50 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-700'">
                                                <input type="checkbox"
                                                       :checked="formData.enabledItems.includes(item.id)"
                                                       @change="toggleChecklistItem(item.id)"
                                                       class="sr-only">
                                                <i class="fa-solid fa-check text-xs mr-1.5"
                                                   :class="formData.enabledItems.includes(item.id) ? 'text-blue-600 dark:text-blue-400' : 'text-transparent'"></i>
                                                <span class="text-sm"
                                                      :class="formData.enabledItems.includes(item.id)
                                                          ? 'text-blue-700 dark:text-blue-300 font-medium'
                                                          : 'text-gray-600 dark:text-gray-400'"
                                                      x-text="item.name"></span>
                                            </label>
                                        </template>

                                        <div x-show="availableChecklistItems.length === 0"
                                             class="w-full text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                                            No items available. Add some items above.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Enabled Items (Reorderable) -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 min-h-[200px] max-h-[440px] overflow-hidden">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                        Checklist Order
                                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-full"
                                              x-text="formData.enabledItems.length"></span>
                                    </h4>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Drag to reorder</span>
                                </div>

                                <!-- Sortable List -->
                                <div x-ref="editSortableList" class="space-y-2 min-h-[200px] max-h-[350px] overflow-y-auto">
                                    <template x-for="(itemId, index) in formData.enabledItems" :key="itemId">
                                        <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 cursor-move hover:shadow-md transition-shadow group"
                                             :data-id="itemId">
                                            <i class="fa-solid fa-grip-vertical text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300"></i>
                                            <span class="text-xs text-gray-400 w-5" x-text="index + 1 + '.'"></span>
                                            <span class="flex-1 text-sm text-gray-700 dark:text-gray-300" x-text="getItemName(itemId)"></span>
                                            <button @click="removeChecklistItem(itemId)"
                                                    class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors opacity-0 group-hover:opacity-100">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                    </template>

                                    <!-- Empty State -->
                                    <div x-show="formData.enabledItems.length === 0"
                                         class="flex flex-col items-center justify-center py-12 text-gray-500 dark:text-gray-400">
                                        <i class="fa-solid fa-list-check text-3xl mb-3 opacity-50"></i>
                                        <p class="text-sm font-medium">No items selected</p>
                                        <p class="text-xs">Select items from the left to add them here</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            <span x-text="formData.enabledItems.length"></span> items in checklist
                        </span>
                        <div class="flex gap-3">
                            <button @click="closeModal()"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                Cancel
                            </button>
                            <button @click="saveTemplate()"
                                    :disabled="!formData.name.trim()"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
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
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
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
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function checklistTemplates() {
            return {
                showAddModal: false,
                showEditModal: false,
                showNewItemInput: false,
                editingId: null,
                newChecklistItem: '',
                editSortableInstance: null,
                formData: {
                    name: '',
                    color: '#E91E63',
                    defaultFor: '',
                    enabledItems: []
                },
                colors: [
                    '#E91E63', // Pink
                    '#9C27B0', // Purple
                    '#FF9800', // Orange
                    '#4CAF50', // Green
                    '#2196F3', // Blue
                    '#F44336', // Red
                    '#00BCD4', // Cyan
                ],
                taskTypes: [
                    { id: 'daily_cleaning', name: 'Daily Cleaning Service' },
                    { id: 'snowout_cleaning', name: 'Snowout Cleaning Service' },
                    { id: 'deep_cleaning', name: 'Deep Cleaning Service' },
                    { id: 'general_cleaning', name: 'General Cleaning Service' },
                    { id: 'hotel_cleaning', name: 'Hotel Cleaning Service' },
                ],
                availableChecklistItems: [
                    // ========== DAILY CLEANING SERVICE (Routine Maintenance) ==========
                    // General Areas
                    { id: 1, name: 'Sweep and mop floors' },
                    { id: 2, name: 'Vacuum carpets/rugs' },
                    { id: 3, name: 'Dust furniture and surfaces' },
                    { id: 4, name: 'Wipe tables and countertops' },
                    { id: 5, name: 'Empty trash bins' },
                    // Kitchen (Daily)
                    { id: 6, name: 'Wipe kitchen counters' },
                    { id: 7, name: 'Clean sink' },
                    { id: 8, name: 'Wash visible dishes' },
                    { id: 9, name: 'Wipe appliance exteriors' },
                    // Bathroom (Daily)
                    { id: 10, name: 'Clean toilet and sink' },
                    { id: 11, name: 'Wipe mirrors' },
                    { id: 12, name: 'Mop floor' },
                    // Finishing (Daily)
                    { id: 13, name: 'Organize cluttered areas' },
                    { id: 14, name: 'Light deodorizing' },

                    // ========== SNOWOUT CLEANING SERVICE (Post-Weather Cleanup) ==========
                    // Entryway
                    { id: 15, name: 'Remove mud, water, and debris' },
                    { id: 16, name: 'Clean door mats' },
                    { id: 17, name: 'Mop and dry floors' },
                    // Floors & Surfaces (Snowout)
                    { id: 18, name: 'Deep vacuum carpets' },
                    { id: 19, name: 'Mop with disinfectant solution' },
                    { id: 20, name: 'Wipe walls near entrances' },
                    // Moisture Control
                    { id: 21, name: 'Dry wet surfaces' },
                    { id: 22, name: 'Check for water accumulation' },
                    { id: 23, name: 'Clean and sanitize affected areas' },
                    // Waste Removal (Snowout)
                    { id: 24, name: 'Dispose of tracked-in debris' },
                    { id: 25, name: 'Replace trash liners' },

                    // ========== DEEP CLEANING SERVICE (Detailed Intensive) ==========
                    // All Rooms (Deep)
                    { id: 26, name: 'Dust high and low areas (vents, corners, baseboards)' },
                    { id: 27, name: 'Clean behind and under furniture' },
                    { id: 28, name: 'Wash walls and remove stains' },
                    { id: 29, name: 'Deep vacuum carpets' },
                    // Kitchen (Deep)
                    { id: 30, name: 'Clean inside microwave' },
                    { id: 31, name: 'Degrease stove and range hood' },
                    { id: 32, name: 'Clean inside refrigerator (if included)' },
                    { id: 33, name: 'Scrub tile grout' },
                    // Bathroom (Deep)
                    { id: 34, name: 'Remove limescale and mold buildup' },
                    { id: 35, name: 'Deep scrub tiles and grout' },
                    { id: 36, name: 'Sanitize all fixtures thoroughly' },
                    // Detail Work (Deep)
                    { id: 37, name: 'Clean window interiors' },
                    { id: 38, name: 'Polish handles and knobs' },
                    { id: 39, name: 'Disinfect frequently touched surfaces' },

                    // ========== GENERAL CLEANING SERVICE (Standard Whole-Area) ==========
                    // Living Areas (General)
                    { id: 40, name: 'Dust surfaces' },
                    { id: 41, name: 'Sweep/vacuum floors' },
                    { id: 42, name: 'Mop hard floors' },
                    { id: 43, name: 'Clean glass and mirrors' },
                    // Kitchen (General)
                    { id: 44, name: 'Wipe countertops' },
                    { id: 45, name: 'Clean sink' },
                    { id: 46, name: 'Take out trash' },
                    // Bathroom (General)
                    { id: 47, name: 'Clean toilet, sink, and mirror' },
                    { id: 48, name: 'Mop floor' },
                    // Final Tasks (General)
                    { id: 49, name: 'Arrange items neatly' },
                    { id: 50, name: 'Dispose of garbage' },
                    { id: 51, name: 'Light air freshening' },

                    // ========== HOTEL CLEANING SERVICE (Room Turnover) ==========
                    // Room Area
                    { id: 52, name: 'Make bed with fresh linens' },
                    { id: 53, name: 'Replace pillowcases and sheets' },
                    { id: 54, name: 'Dust all surfaces (tables, headboard, shelves)' },
                    { id: 55, name: 'Vacuum carpet / sweep & mop floor' },
                    { id: 56, name: 'Clean mirrors and glass surfaces' },
                    { id: 57, name: 'Check under bed for trash/items' },
                    { id: 58, name: 'Empty trash bins and replace liners' },
                    // Bathroom (Hotel)
                    { id: 59, name: 'Clean and disinfect toilet' },
                    { id: 60, name: 'Scrub shower walls, tub, and floor' },
                    { id: 61, name: 'Clean sink and countertop' },
                    { id: 62, name: 'Polish fixtures' },
                    { id: 63, name: 'Replace towels, bath mat, tissue, and toiletries' },
                    { id: 64, name: 'Mop bathroom floor' },
                    // Restocking (Hotel)
                    { id: 65, name: 'Refill water, coffee, and room amenities' },
                    { id: 66, name: 'Replace slippers and hygiene kits' },
                    { id: 67, name: 'Check minibar (if applicable)' },
                    // Final Check (Hotel)
                    { id: 68, name: 'Ensure lights, AC, TV working' },
                    { id: 69, name: 'Arrange curtains neatly' },
                    { id: 70, name: 'Deodorize room' },
                ],
                templates: [
                    { id: 1, name: 'Daily Cleaning Service', initials: 'DC', color: '#E91E63', itemCount: 14, defaultFor: 'daily_cleaning', enabledItems: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14] },
                    { id: 2, name: 'Snowout Cleaning Service', initials: 'SC', color: '#2196F3', itemCount: 11, defaultFor: 'snowout_cleaning', enabledItems: [15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25] },
                    { id: 3, name: 'Deep Cleaning Service', initials: 'DP', color: '#FF9800', itemCount: 14, defaultFor: 'deep_cleaning', enabledItems: [26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39] },
                    { id: 4, name: 'General Cleaning Service', initials: 'GC', color: '#4CAF50', itemCount: 12, defaultFor: 'general_cleaning', enabledItems: [40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51] },
                    { id: 5, name: 'Hotel Cleaning Service', initials: 'HC', color: '#9C27B0', itemCount: 19, defaultFor: 'hotel_cleaning', enabledItems: [52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70] },
                ],

                init() {
                    this.$watch('showEditModal', (value) => {
                        if (value) {
                            this.$nextTick(() => {
                                this.initEditSortable();
                            });
                        } else {
                            if (this.editSortableInstance) {
                                this.editSortableInstance.destroy();
                                this.editSortableInstance = null;
                            }
                        }
                    });

                    // Auto-fill template name when "Default For" is selected
                    this.$watch('formData.defaultFor', (value) => {
                        if (value) {
                            const taskType = this.taskTypes.find(t => t.id === value);
                            if (taskType) {
                                this.formData.name = taskType.name;
                            }
                        }
                    });
                },

                initEditSortable() {
                    if (this.editSortableInstance) {
                        this.editSortableInstance.destroy();
                    }

                    const el = this.$refs.editSortableList;
                    if (el) {
                        this.editSortableInstance = new Sortable(el, {
                            animation: 150,
                            ghostClass: 'opacity-50',
                            handle: '.fa-grip-vertical',
                            onEnd: (evt) => {
                                const items = [...this.formData.enabledItems];
                                const [removed] = items.splice(evt.oldIndex, 1);
                                items.splice(evt.newIndex, 0, removed);
                                this.formData.enabledItems = items;
                            }
                        });
                    }
                },

                getInitials(name) {
                    return name.split(' ')
                        .map(word => word.charAt(0).toUpperCase())
                        .slice(0, 2)
                        .join('');
                },

                getItemName(itemId) {
                    const item = this.availableChecklistItems.find(i => i.id === itemId);
                    return item ? item.name : 'Unknown';
                },

                editTemplate(template) {
                    this.editingId = template.id;
                    this.formData.name = template.name;
                    this.formData.color = template.color;
                    this.formData.defaultFor = template.defaultFor || '';
                    this.formData.enabledItems = [...(template.enabledItems || [])];
                    this.showNewItemInput = false;
                    this.newChecklistItem = '';
                    this.showEditModal = true;
                },

                duplicateTemplate(template) {
                    const newId = Math.max(...this.templates.map(t => t.id)) + 1;
                    this.templates.push({
                        id: newId,
                        name: template.name + ' (Copy)',
                        initials: template.initials,
                        color: template.color,
                        itemCount: template.itemCount,
                        defaultFor: '',
                        enabledItems: [...(template.enabledItems || [])]
                    });
                },

                deleteTemplate(id) {
                    if (confirm('Are you sure you want to delete this template?')) {
                        this.templates = this.templates.filter(t => t.id !== id);
                    }
                },

                toggleChecklistItem(itemId) {
                    const index = this.formData.enabledItems.indexOf(itemId);
                    if (index > -1) {
                        this.formData.enabledItems.splice(index, 1);
                    } else {
                        this.formData.enabledItems.push(itemId);
                    }
                    this.$nextTick(() => this.initEditSortable());
                },

                removeChecklistItem(itemId) {
                    const index = this.formData.enabledItems.indexOf(itemId);
                    if (index > -1) {
                        this.formData.enabledItems.splice(index, 1);
                    }
                },

                addChecklistItem() {
                    if (!this.newChecklistItem.trim()) return;

                    const newId = this.availableChecklistItems.length > 0
                        ? Math.max(...this.availableChecklistItems.map(i => i.id)) + 1
                        : 1;

                    this.availableChecklistItems.push({
                        id: newId,
                        name: this.newChecklistItem.trim()
                    });

                    this.newChecklistItem = '';
                },

                saveTemplate() {
                    if (!this.formData.name.trim()) return;

                    if (this.showEditModal && this.editingId) {
                        // Update existing template
                        const index = this.templates.findIndex(t => t.id === this.editingId);
                        if (index !== -1) {
                            this.templates[index].name = this.formData.name;
                            this.templates[index].initials = this.getInitials(this.formData.name);
                            this.templates[index].color = this.formData.color;
                            this.templates[index].defaultFor = this.formData.defaultFor;
                            this.templates[index].enabledItems = [...this.formData.enabledItems];
                            this.templates[index].itemCount = this.formData.enabledItems.length;
                        }
                    } else {
                        // Add new template
                        const newId = this.templates.length > 0
                            ? Math.max(...this.templates.map(t => t.id)) + 1
                            : 1;
                        this.templates.push({
                            id: newId,
                            name: this.formData.name,
                            initials: this.getInitials(this.formData.name),
                            color: this.formData.color,
                            defaultFor: this.formData.defaultFor,
                            enabledItems: [],
                            itemCount: 0
                        });
                    }

                    this.closeModal();
                },

                closeModal() {
                    this.showAddModal = false;
                    this.showEditModal = false;
                    this.showNewItemInput = false;
                    this.editingId = null;
                    this.newChecklistItem = '';
                    this.formData = { name: '', color: '#E91E63', defaultFor: '', enabledItems: [] };
                }
            }
        }
    </script>
    @endpush
</x-layouts.general-employer>
