<x-layouts.general-manager :title="'Schedule'">
    <div class="flex flex-col gap-6 w-full" x-data="scheduleManager()" x-init="init()" @keydown.escape.window="showCreateModal = false">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Schedule</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your tasks and assignments</p>
            </div>
            <div class="flex gap-3">
                <button @click="openCreateModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fa-solid fa-plus"></i>
                    New Task
                </button>
            </div>
        </div>

        <!-- Date Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-4">
                <button @click="previousWeek()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-chevron-left text-gray-600 dark:text-gray-400"></i>
                </button>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="currentWeekLabel"></h2>
                <button @click="nextWeek()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fa-solid fa-chevron-right text-gray-600 dark:text-gray-400"></i>
                </button>
            </div>

            <!-- Week Days -->
            <div class="grid grid-cols-7 gap-2">
                <template x-for="day in weekDays" :key="day.date">
                    <button @click="selectDate(day.date)"
                            :class="{
                                'bg-blue-600 text-white': selectedDate === day.date,
                                'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600': selectedDate !== day.date && day.isToday,
                                'hover:bg-gray-100 dark:hover:bg-gray-700': selectedDate !== day.date && !day.isToday
                            }"
                            class="flex flex-col items-center p-2 md:p-3 rounded-lg transition-colors">
                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="day.dayName"></span>
                        <span class="text-lg font-semibold" x-text="day.dayNumber"></span>
                        <div class="flex gap-1 mt-1" x-show="day.taskCount > 0">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- Tasks for Selected Date -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Tasks for <span x-text="formatDate(selectedDate)"></span>
                </h2>
                <span class="text-sm text-gray-500 dark:text-gray-400" x-text="tasks.length + ' tasks'"></span>
            </div>
            <div class="p-4 md:p-5">
                <!-- Loading State -->
                <div x-show="loading" class="flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>

                <!-- Tasks List -->
                <div x-show="!loading && tasks.length > 0" class="space-y-3">
                    <template x-for="task in tasks" :key="task.id">
                        <div class="flex items-center gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                             @click="openTaskDetails(task)">
                            <!-- Status Indicator -->
                            <div class="flex-shrink-0">
                                <div class="w-3 h-3 rounded-full"
                                     :class="{
                                         'bg-green-500': task.status === 'Completed',
                                         'bg-blue-500 animate-pulse': task.status === 'In Progress',
                                         'bg-yellow-500': task.status === 'On Hold',
                                         'bg-gray-400': task.status === 'Scheduled' || task.status === 'Pending'
                                     }"></div>
                            </div>

                            <!-- Task Info -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="task.location_name"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <span x-text="task.scheduled_time"></span>
                                    <span class="mx-1" x-show="task.duration">•</span>
                                    <span x-show="task.duration" x-text="task.duration + ' min'"></span>
                                </p>
                            </div>

                            <!-- Status Badge -->
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': task.status === 'Completed',
                                          'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': task.status === 'In Progress',
                                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': task.status === 'On Hold',
                                          'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': task.status === 'Scheduled' || task.status === 'Pending'
                                      }"
                                      x-text="task.status"></span>
                            </div>

                            <!-- Team Count -->
                            <div class="flex-shrink-0 flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-user-group"></i>
                                <span x-text="task.employee_count"></span>
                            </div>

                            <!-- Actions -->
                            <div class="flex-shrink-0">
                                <button class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                    <i class="fa-solid fa-ellipsis-vertical text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="!loading && tasks.length === 0" class="text-center py-8">
                    <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-solid fa-calendar-xmark text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">No tasks scheduled for this date</p>
                    <button @click="openCreateModal()" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-2">
                        Create a new task
                    </button>
                </div>
            </div>
        </div>

        <!-- Location Groups -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Locations by Type</h2>
            </div>
            <div class="p-4 md:p-5">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($locationTypes ?? [] as $type => $count)
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 text-center hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                            <div class="text-2xl mb-1">
                                @switch($type)
                                    @case('Small')
                                        <i class="fa-solid fa-house text-blue-500"></i>
                                        @break
                                    @case('Medium')
                                        <i class="fa-solid fa-house-chimney text-green-500"></i>
                                        @break
                                    @case('Big')
                                        <i class="fa-solid fa-building text-purple-500"></i>
                                        @break
                                    @case('Queen')
                                        <i class="fa-solid fa-crown text-yellow-500"></i>
                                        @break
                                    @case('Igloo')
                                        <i class="fa-solid fa-igloo text-cyan-500"></i>
                                        @break
                                    @default
                                        <i class="fa-solid fa-location-dot text-gray-500"></i>
                                @endswitch
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $type }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $count }} locations</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Create Task Modal -->
        <div x-show="showCreateModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showCreateModal = false"></div>

            <!-- Modal Content -->
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div x-show="showCreateModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg"
                     @click.stop>

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New Task</h3>
                        <button @click="showCreateModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fa-solid fa-xmark text-gray-500"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-4 space-y-4">
                        <!-- Location Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                            <select x-model="taskForm.location_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select a location</option>
                                <template x-for="location in locations" :key="location.id">
                                    <option :value="location.id" x-text="location.name + ' (' + location.type + ')'"></option>
                                </template>
                            </select>
                            <p x-show="formErrors.location_id" class="mt-1 text-sm text-red-500" x-text="formErrors.location_id"></p>
                        </div>

                        <!-- Date and Time Row -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Scheduled Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                <input type="date"
                                       x-model="taskForm.scheduled_date"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p x-show="formErrors.scheduled_date" class="mt-1 text-sm text-red-500" x-text="formErrors.scheduled_date"></p>
                            </div>

                            <!-- Scheduled Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
                                <input type="time"
                                       x-model="taskForm.scheduled_time"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p x-show="formErrors.scheduled_time" class="mt-1 text-sm text-red-500" x-text="formErrors.scheduled_time"></p>
                            </div>
                        </div>

                        <!-- Rate Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rate Type</label>
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" x-model="taskForm.rate_type" value="Normal" class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Normal Rate</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="taskForm.rate_type" value="Student" class="mr-2 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Student Rate</span>
                                </label>
                            </div>
                        </div>

                        <!-- Service Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service Type</label>
                            <select x-model="taskForm.service_type"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="Regular Cleaning">Regular Cleaning</option>
                                <option value="Deep Cleaning">Deep Cleaning</option>
                                <option value="Light Deep Cleaning">Light Deep Cleaning</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (Optional)</label>
                            <textarea x-model="taskForm.task_description"
                                      rows="3"
                                      placeholder="Add any special instructions or notes..."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        </div>

                        <!-- Error Message -->
                        <div x-show="submitError" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-sm text-red-600 dark:text-red-400" x-text="submitError"></p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="showCreateModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button @click="submitTask()"
                                :disabled="submitting"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <i x-show="submitting" class="fa-solid fa-spinner animate-spin"></i>
                            <span x-text="submitting ? 'Creating...' : 'Create Task'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function scheduleManager() {
            return {
                selectedDate: '{{ now()->format("Y-m-d") }}',
                currentWeekStart: new Date(),
                weekDays: [],
                tasks: [],
                loading: false,

                // Modal state
                showCreateModal: false,
                locations: [],
                locationsLoaded: false,
                submitting: false,
                submitError: '',
                formErrors: {},

                // Task form data
                taskForm: {
                    location_id: '',
                    scheduled_date: '{{ now()->format("Y-m-d") }}',
                    scheduled_time: '09:00',
                    rate_type: 'Normal',
                    service_type: 'Regular Cleaning',
                    task_description: ''
                },

                init() {
                    this.currentWeekStart = this.getStartOfWeek(new Date());
                    this.generateWeekDays();
                    this.fetchTasks();
                },

                get currentWeekLabel() {
                    const start = new Date(this.currentWeekStart);
                    const end = new Date(start);
                    end.setDate(end.getDate() + 6);
                    const options = { month: 'short', day: 'numeric' };
                    return `${start.toLocaleDateString('en-US', options)} - ${end.toLocaleDateString('en-US', options)}, ${end.getFullYear()}`;
                },

                getStartOfWeek(date) {
                    const d = new Date(date);
                    const day = d.getDay();
                    const diff = d.getDate() - day + (day === 0 ? -6 : 1);
                    return new Date(d.setDate(diff));
                },

                generateWeekDays() {
                    this.weekDays = [];
                    const today = new Date().toISOString().split('T')[0];
                    for (let i = 0; i < 7; i++) {
                        const date = new Date(this.currentWeekStart);
                        date.setDate(date.getDate() + i);
                        const dateStr = date.toISOString().split('T')[0];
                        this.weekDays.push({
                            date: dateStr,
                            dayName: date.toLocaleDateString('en-US', { weekday: 'short' }),
                            dayNumber: date.getDate(),
                            isToday: dateStr === today,
                            taskCount: 0
                        });
                    }
                },

                previousWeek() {
                    this.currentWeekStart.setDate(this.currentWeekStart.getDate() - 7);
                    this.generateWeekDays();
                },

                nextWeek() {
                    this.currentWeekStart.setDate(this.currentWeekStart.getDate() + 7);
                    this.generateWeekDays();
                },

                selectDate(date) {
                    this.selectedDate = date;
                    this.fetchTasks();
                },

                formatDate(dateStr) {
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
                },

                async fetchTasks() {
                    this.loading = true;
                    try {
                        const response = await fetch(`/manager/schedule/tasks?date=${this.selectedDate}`);
                        const data = await response.json();
                        this.tasks = data.tasks || [];
                    } catch (error) {
                        console.error('Error fetching tasks:', error);
                        this.tasks = [];
                    }
                    this.loading = false;
                },

                async fetchLocations() {
                    if (this.locationsLoaded) return;
                    try {
                        const response = await fetch('/manager/schedule/locations');
                        const data = await response.json();
                        this.locations = data.locations || [];
                        this.locationsLoaded = true;
                    } catch (error) {
                        console.error('Error fetching locations:', error);
                        this.locations = [];
                    }
                },

                openCreateModal() {
                    // Reset form
                    this.taskForm = {
                        location_id: '',
                        scheduled_date: this.selectedDate,
                        scheduled_time: '09:00',
                        rate_type: 'Normal',
                        service_type: 'Regular Cleaning',
                        task_description: ''
                    };
                    this.formErrors = {};
                    this.submitError = '';
                    this.showCreateModal = true;
                    this.fetchLocations();
                },

                validateForm() {
                    this.formErrors = {};
                    if (!this.taskForm.location_id) {
                        this.formErrors.location_id = 'Please select a location';
                    }
                    if (!this.taskForm.scheduled_date) {
                        this.formErrors.scheduled_date = 'Please select a date';
                    }
                    if (!this.taskForm.scheduled_time) {
                        this.formErrors.scheduled_time = 'Please select a time';
                    }
                    return Object.keys(this.formErrors).length === 0;
                },

                async submitTask() {
                    if (!this.validateForm()) return;

                    this.submitting = true;
                    this.submitError = '';

                    try {
                        const response = await fetch('/manager/schedule/tasks', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.taskForm)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (data.errors) {
                                this.formErrors = data.errors;
                            }
                            throw new Error(data.message || 'Failed to create task');
                        }

                        // Success - close modal and refresh tasks
                        this.showCreateModal = false;
                        this.fetchTasks();

                    } catch (error) {
                        console.error('Error creating task:', error);
                        this.submitError = error.message || 'An error occurred while creating the task';
                    }

                    this.submitting = false;
                },

                openTaskDetails(task) {
                    // TODO: Implement task details modal
                    console.log('Task details:', task);
                }
            };
        }
    </script>
    @endpush
</x-layouts.general-manager>
