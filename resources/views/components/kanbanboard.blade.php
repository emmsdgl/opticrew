<div x-data="kanbanBoard()" x-init="init()"
    class="w-full font-sans h-full bg-gray-50 dark:bg-gray-900 rounded-lg shadow p-6 transition-colors duration-300">

    <!-- Board Columns -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

        <!-- Column -->
        <template x-for="(column, colIndex) in columns" :key="colIndex">
            <div class="flex flex-col bg-transparent rounded-xl p-4" @dragover.prevent
                @drop="dropTask($event, column.status)">

                <h3 class="text-base mb-4 font-bold text-gray-800 dark:text-gray-100" x-text="column.name"></h3>

                <template x-for="(task, taskIndex) in tasks.filter(t => t.status === column.status)" :key="task.id">
                    <div draggable="true" @dragstart="dragTask($event, task.id)"
                        class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg mb-4 border border-gray-200 dark:border-gray-600 hover:shadow-md transition">

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
                            <x-teamavatarcols :teamName="'Team 2'" :members="['member-1', 'member-2', 'member-3', 'member-4', 'member-5', 'member6', 'member7', 'member8']" />

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
            </div>
        </template>
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
                    { id: 1, client: 'Client 1', title: 'Deep Cleaning', team: 'Team 1', date: 'July 9, 2025', time: '2:00 pm', priority: 'Urgent', status: 'todo', priorityColor: 'bg-[#FE1E2820] text-[#FE1E28]' },
                    { id: 2, client: 'Client 2', title: 'Deep Cleaning', team: 'Team 1', date: 'July 10, 2025', time: '2:00 pm', priority: 'Low', status: 'todo', priorityColor: 'bg-[#FFB70020] text-[#FFB700]' },
                    { id: 3, client: 'Client 3', title: 'Daily Room Cleaning', team: 'Team 1', date: 'July 9, 2025', time: '2:00 pm', priority: 'Normal', status: 'inprogress', priorityColor: 'bg-[#2FBC0020] text-[#2FBC00]' },
                    { id: 4, client: 'Client 1', title: 'Light Daily Cleaning', team: 'Team 1', date: 'July 22, 2025', time: '2:00 pm', priority: 'High', status: 'completed', priorityColor: 'bg-[#FF7F0020] text-[#FF7F00]' }
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