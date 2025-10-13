<div x-data="kanbanBoard()" x-init="init()"
    class="w-full font-sans h-full bg-gray-50 dark:bg-gray-900 rounded-lg shadow p-6 transition-colors duration-300">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-base font-sans font-bold text-gray-800 dark:text-gray-100">Task Board</h2>
    </div>

    <!-- Board Columns -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- Column -->
        <template x-for="(column, colIndex) in columns" :key="colIndex">
            <div class="flex flex-col bg-white dark:bg-gray-800 rounded-xl p-4"
                @dragover.prevent @drop="dropTask($event, column.status)">

                <h3 class="text-base mb-4 text-gray-800 dark:text-gray-100" x-text="column.name"></h3>

                <template x-for="(task, taskIndex) in tasks.filter(t => t.status === column.status)" :key="task.id">
                    <div draggable="true" @dragstart="dragTask($event, task.id)"
                        class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-4 border border-gray-200 dark:border-gray-600 hover:shadow-md transition">

                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="task.priorityColor"
                                x-text="task.priority"></span>
                            <button @click="editTask(task)"
                                class="text-gray-500 dark:text-gray-300 hover:text-blue-500">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </div>

                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="task.client"></p>
                        
                        <div class="mt-1 flex justify-between items-center gap-1 text-sm text-gray-400">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100" x-text="task.title"></p>
                            <span x-text="task.team"></span>
                        </div>

                        <div class="flex items-center justify-between mt-2 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-1">
                                <i class="fa-regular fa-clock"></i>
                                <span x-text="'Start Time: ' + task.time"></span>
                            </div>
                            <div class="flex text-sm items-center gap-1">
                                <i class="fa-solid fa-calendar"></i>
                                <span x-text="task.date"></span>
                            </div>
                        </div>

                    </div>
                </template>
            </div>
        </template>
    </div>

    <!-- Modal for Editing -->
    <div x-show="showModal" x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md relative shadow-lg">
            <button @click="closeModal"
                class="absolute top-2 right-2 text-gray-600 dark:text-gray-300 hover:text-red-500">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Edit Task</h3>

            <form @submit.prevent="updateTask">
                <input type="text" x-model="editForm.title" placeholder="Task title"
                    class="w-full mb-3 p-2 border border-gray-300 dark:border-gray-600 rounded text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                    required>

                <select x-model="editForm.priority"
                    class="w-full mb-3 p-2 border border-gray-300 dark:border-gray-600 rounded text-sm bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <option value="Urgent">Urgent</option>
                    <option value="High">High</option>
                    <option value="Normal">Normal</option>
                    <option value="Low">Low</option>
                </select>

                <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function kanbanBoard() {
        return {
            columns: [
                { name: 'To Do', status: 'todo' },
                { name: 'In Progress', status: 'inprogress' },
                { name: 'Completed', status: 'completed' }
            ],
            tasks: [],
            draggedTaskId: null,
            showModal: false,
            editForm: { id: null, title: '', priority: '' },

            init() {
                // Sample data
                this.tasks = [
                    { id: 1, client: 'Client 1', title: 'Deep Cleaning', team: 'Team 1', date: 'July 9, 2025', time: '2:00 pm', priority: 'Urgent', status: 'todo', priorityColor: 'bg-red-100 text-red-800' },
                    { id: 2, client: 'Client 2', title: 'Deep Cleaning', team: 'Team 1', date: 'July 10, 2025', time: '2:00 pm', priority: 'Low', status: 'todo', priorityColor: 'bg-yellow-100 text-yellow-800' },
                    { id: 3, client: 'Client 3', title: 'Daily Room Cleaning', team: 'Team 1', date: 'July 9, 2025', time: '2:00 pm', priority: 'Normal', status: 'inprogress', priorityColor: 'bg-green-100 text-green-800' },
                    { id: 4, client: 'Client 1', title: 'Light Daily Cleaning', team: 'Team 1', date: 'July 22, 2025', time: '2:00 pm', priority: 'High', status: 'completed', priorityColor: 'bg-orange-100 text-orange-800' }
                ];
                this.updateTheme();
            },

            dragTask(event, id) {
                this.draggedTaskId = id;
            },

            dropTask(event, newStatus) {
                const task = this.tasks.find(t => t.id === this.draggedTaskId);
                if (task) task.status = newStatus;
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