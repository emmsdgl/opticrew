@props(['tasks' => []])

<div x-data="kanbanBoard(@js($tasks))" x-init="init()"
    class="w-full font-sans h-full flex flex-col bg-gray-50 dark:bg-gray-900 rounded-lg shadow transition-colors duration-300">

    <!-- Kanban Header -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Task Board</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">Drag and drop tasks to update their status</p>
    </div>

    <!-- Board Columns Container -->
    <div class="flex-1 overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 h-full p-4">

            <!-- Column -->
            <template x-for="(column, colIndex) in columns" :key="colIndex">
                <div class="flex flex-col bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

                    <!-- Column Header (Fixed) -->
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100" x-text="column.name"></h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300"
                                x-text="tasks.filter(t => t.status === column.status).length"></span>
                        </div>
                    </div>

                    <!-- Column Content (Scrollable) -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-3" @dragover.prevent @drop="dropTask($event, column.status)">
                        <template x-for="(task, taskIndex) in tasks.filter(t => t.status === column.status)" :key="task.id">
                            <div draggable="true" @dragstart="dragTask($event, task.id)"
                                class="bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-lg hover:border-blue-400 dark:hover:border-blue-600 transition cursor-move">

                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-sans font-medium"
                                :class="task.priorityColor" x-text="task.priority"></span>
                            <button @click="editTask(task)"
                                class="text-gray-500 dark:text-gray-300 hover:text-blue-500">
                                <i class="fa-regular fa-trash-can"></i> </button>
                        </div>

                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="task.client"></p>

                        <div class="mt-1 flex justify-between items-center gap-1 text-sm text-gray-400">
                            <p class="text-base py-1.5 font-sans font-bold text-gray-800 dark:text-gray-100"
                                x-text="task.title"></p>

                            <!-- Team Avatar Component - Using real team members -->
                            <div class="flex -space-x-2">
                                <template x-for="(member, mIndex) in task.teamMembers" :key="member.id">
                                    <div class="relative group">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold border-2 border-white dark:border-gray-800 shadow-sm"
                                            :title="member.name + ' (' + member.role + ')'">
                                            <span x-text="member.name.split(' ').map(n => n[0]).join('').substring(0, 2)"></span>
                                        </div>
                                        <!-- Tooltip -->
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                                            <span x-text="member.name"></span>
                                            <span class="block text-gray-300" x-text="member.role"></span>
                                        </div>
                                    </div>
                                </template>
                                <!-- Show team name if no members -->
                                <template x-if="!task.teamMembers || task.teamMembers.length === 0">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 italic" x-text="task.team"></span>
                                </template>
                            </div>

                        </div>

                        <div class="flex items-center justify-between mt-2 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex text-sm items-center gap-1">
                                <i class="fa-regular fa-calendar mr-2"></i>
                                <span x-text="task.date"></span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-clock mr-2"></i>
                                <span x-text="'Start Time: ' + task.time"></span>
                            </div>
                        </div>

                            </div>
                        </template>

                        <!-- Empty State -->
                        <template x-if="tasks.filter(t => t.status === column.status).length === 0">
                            <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <p class="text-sm">No tasks</p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function kanbanBoard(initialTasks = []) {
        return {
            columns: [
                { name: 'To Do', status: 'todo' },
                { name: 'In Progress', status: 'inprogress' },
                { name: 'Completed', status: 'completed' }
            ],
            tasks: initialTasks,
            draggedTaskId: null,
            showModal: false,
            editForm: { id: null, title: '', priority: '' },

            init() {
                // Tasks are already loaded from database via initialTasks parameter
                console.log('Kanban board loaded with', this.tasks.length, 'tasks from database');
                this.updateTheme();
            },

            dragTask(event, id) {
                this.draggedTaskId = id;
                event.dataTransfer.effectAllowed = 'move';
            },

            async dropTask(event, newStatus) {
                event.preventDefault();
                const task = this.tasks.find(t => t.id === this.draggedTaskId);

                if (!task || task.status === newStatus) {
                    this.draggedTaskId = null;
                    return; // No change needed
                }

                const oldStatus = task.status;

                // Optimistically update UI
                task.status = newStatus;

                // Map Kanban status to database status
                let dbStatus = 'Pending';
                switch (newStatus) {
                    case 'todo':
                        dbStatus = 'Pending';
                        break;
                    case 'inprogress':
                        dbStatus = 'In Progress';
                        break;
                    case 'completed':
                        dbStatus = 'Completed';
                        break;
                }

                // Update in database
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch(`/tasks/${task.id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: dbStatus })
                    });

                    if (!response.ok) {
                        throw new Error('Failed to update task status');
                    }

                    const data = await response.json();
                    console.log('Task status updated:', data);

                } catch (error) {
                    console.error('Error updating task status:', error);
                    // Revert UI on error
                    task.status = oldStatus;
                    alert('Failed to update task status. Please try again.');
                }

                this.draggedTaskId = null;
            },

            editTask(task) {
                this.editForm = { ...task };
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
            },

            updateTask() {
                const task = this.tasks.find(t => t.id === this.editForm.id);
                if (task) {
                    task.title = this.editForm.title;
                    task.priority = this.editForm.priority;
                    task.priorityColor = this.getPriorityColor(task.priority);
                }
                this.showModal = false;
            },

            getPriorityColor(priority) {
                switch (priority) {
                    case 'Urgent': return 'bg-red-100 text-red-800';
                    case 'High': return 'bg-orange-100 text-orange-800';
                    case 'Normal': return 'bg-green-100 text-green-800';
                    case 'Low': return 'bg-yellow-100 text-yellow-800';
                }
            },

            toggleTheme() {
                document.documentElement.classList.toggle('dark');
                this.updateTheme();
            },

            updateTheme() {
                localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            }
        }
    }
</script>