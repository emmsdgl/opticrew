@props(['tasks' => []])

<div x-data="kanbanBoard(@js($tasks))" x-init="init()" class="w-full h-full flex flex-col bg-gray-50 dark:bg-gray-900 rounded-xl">

    <!-- Kanban Header -->
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
        <p class="text-base font-bold font-sans mt-1">Tasks Board</p>
    </div>

    <!-- Board Columns Container -->
    <div class="flex-1 overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-full p-6">

            <!-- Column -->
            <template x-for="(column, colIndex) in columns" :key="colIndex">
                <div class="flex flex-col bg-transparent overflow-hidden">

                    <!-- Column Header -->
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h3 class="text-base font-sans font-bold text-gray-900 dark:text-gray-100" x-text="column.name"></h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400"
                                x-text="tasks.filter(t => t.status === column.status).length"></span>
                            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Column Content (Scrollable) -->
                    <div class="flex-1 overflow-y-auto space-y-3 pr-1" @dragover.prevent
                        @drop="dropTask($event, column.status)"
                        style="scrollbar-width: thin; scrollbar-color: #CBD5E0 transparent;">

                        <template x-for="(task, taskIndex) in tasks.filter(t => t.status === column.status)"
                            :key="task.id">
                            <div draggable="true" @dragstart="dragTask($event, task.id)"
                                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200 cursor-move group">

                                <div class="p-5">
                                    <!-- Footer: Client tag & Icons -->
                                    <div
                                        class="flex items-center justify-between pt-3 pb-4 border-gray-100 dark:border-gray-700">
                                        <template x-if="task.client">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300"
                                                x-text="task.client"></span>
                                        </template>

                                        <div class="flex items-center gap-3 text-gray-400 dark:text-gray-500 ml-auto">

                                            <button @click="editTask(task)"
                                                class="hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex flex-row justify-between w-full">
                                        <!-- Task Title -->
                                        <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-3 leading-snug pr-8"
                                            x-text="task.title"></h4>
    
                                        <!-- Avatar -->
                                        <div class="mb-4">
                                            <template x-if="task.teamMembers && task.teamMembers.length > 0">
                                                <div class="flex -space-x-2">
                                                    <template x-for="(member, mIndex) in task.teamMembers.slice(0, 3)"
                                                        :key="member.id">
                                                        <div class="relative group/avatar">
                                                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center text-white text-xs font-semibold border-2 border-white dark:border-gray-800 shadow-sm ring-2 ring-gray-100 dark:ring-gray-700"
                                                                :title="member.name">
                                                                <span
                                                                    x-text="member.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()"></span>
                                                            </div>
                                                            <!-- Tooltip -->
                                                            <div
                                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1.5 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover/avatar:opacity-100 transition-opacity pointer-events-none z-10 shadow-lg">
                                                                <span x-text="member.name"></span>
                                                                <div
                                                                    class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                                                    <div
                                                                        class="border-4 border-transparent">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template x-if="task.teamMembers.length > 3">
                                                        <div
                                                            class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 text-xs font-semibold border-2 border-white dark:border-gray-800 shadow-sm ring-2 ring-gray-100 dark:ring-gray-700">
                                                            <span x-text="'+' + (task.teamMembers.length - 3)"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Date & Time -->
                                    <div
                                        class="flex items-center justify-between gap-4 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span x-text="task.date"></span>
                                        </div>
                                        <template x-if="task.time">
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span x-text="task.time"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                            </div>
                        </template>

                        <!-- Empty State -->
                        <template x-if="tasks.filter(t => t.status === column.status).length === 0">
                            <div class="text-center py-12 px-4">
                                <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-sm text-gray-400 dark:text-gray-500">No tasks</p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<style>
    /* Custom scrollbar styling */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: transparent;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #CBD5E0;
        border-radius: 3px;
    }

    .dark .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #4A5568;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #A0AEC0;
    }
</style>

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
                    return;
                }

                const oldStatus = task.status;
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