<!-- Save this as: resources/views/components/taskcalendar.blade.php -->
<!-- This is the COMPLETE file - replace everything in your current file -->

@props(['clients', 'events'])

<style>
    /* Prevent modal flash on page load */
    [x-cloak] {
        display: none !important;
    }
</style>

<div x-data="calendarComponent(@js($clients), @js($events))" x-init="init()"
    class="w-full h-full bg-gray-50 dark:bg-gray-900 rounded-lg shadow p-4 transition-colors duration-300">

    <!-- Calendar Header -->
    <div class="flex flex-row justify-between items-center mb-4 w-full">
        <h2 class="text-base justify-start font-semibold text-gray-800 dark:text-gray-100" x-text="monthYear"></h2>
        <div class="flex flex-row gap-6">
            <div class="flex flex-row gap-3">
                <button @click="showModal = true; selectedDate = null; resetForm();"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-full transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>New Task</span>
                </button>
            </div>

            <div class="flex flex-row items-center gap-2">
                <button @click="prevMonth"
                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                    <i class="fa-solid fa-chevron-left text-gray-700 dark:text-gray-200"></i>
                </button>
                <button @click="nextMonth"
                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                    <i class="fa-solid fa-chevron-right text-gray-700 dark:text-gray-200"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 gap-2 text-center">
        <template x-for="day in weekDays" :key="day">
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                <span x-text="day"></span>
            </div>
        </template>
    </div>

    <div class="grid grid-cols-7 mt-2">
        <template x-for="(date, index) in dates" :key="index">
            <div @click="openModal(date)"
                class="h-32 p-2 border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900 transition relative">
                <span class="text-xs text-gray-700 dark:text-gray-200 font-semibold" x-text="date.date"></span>

                <!-- Grouped Events Display -->
                <div class="mt-2 space-y-1 overflow-y-auto max-h-24">
                    <template x-for="(group, gindex) in date.groupedEvents" :key="gindex">
                        <div @click.stop="showEventDetails(date, group.client)" class="relative">
                            <div class="text-xs rounded-lg px-2 py-1.5 transition-all duration-200 cursor-pointer"
                                :class="group.color">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-medium truncate flex-1" x-text="group.client"></span>
                                    <span class="flex-shrink-0 px-1.5 py-0.5 rounded-full text-xs font-semibold bg-white/30 dark:bg-black/20" 
                                        x-text="group.count + ' task' + (group.count > 1 ? 's' : '')"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Task Creation Modal -->
    <div x-show="showModal" x-cloak x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 overflow-y-auto p-4 py-8">
        <div @click.away="closeModal" class="bg-white dark:bg-gray-900 rounded-2xl w-full max-w-6xl relative 
               max-h-fit overflow-y-auto border border-gray-200 dark:border-gray-700 transform transition-all duration-300">

            <button @click="closeModal"
                class="sticky top-0 right-0 m-4 text-gray-600 dark:text-gray-300 hover:text-red-500 transition-colors z-20 bg-white dark:bg-gray-900 p-2 rounded-full shadow">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2 m-6">
                <!-- LEFT PANEL - FORM -->
                <div class="p-8 space-y-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Create a Task</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Adding a new custom task</p>
                    </div>

                    <form @submit.prevent="submitTask" class="space-y-6">
                        <!-- Client -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1 block">Client</label>
                            <select x-model="formData.client" @change="handleClientChange"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="">Select a Client</option>
                                <template x-for="client in clientOptions" :key="client.value">
                                    <option :value="client.value" x-text="client.label"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Service Date -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1 block">Service Date</label>
                            <input type="text" x-model="formData.serviceDate" disabled
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white"
                                placeholder="Select a date from calendar">
                        </div>

                        <!-- Service Type -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1 block">Service Type</label>
                            <select x-model="formData.serviceType"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="">Select a Service</option>
                                <option value="Deep Cleaning">Deep Cleaning</option>
                                <option value="Daily Room Cleaning">Daily Room Cleaning</option>
                                <option value="Snowout Cleaning">Snowout Cleaning</option>
                            </select>
                        </div>

                        <!-- Rate Type -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2 block">Type of Rates</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer" 
                                    :class="formData.rateType === 'Normal' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'">
                                    <input type="radio" x-model="formData.rateType" value="Normal" class="mr-2">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Normal</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Standard pricing</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer" 
                                    :class="formData.rateType === 'Student' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'">
                                    <input type="radio" x-model="formData.rateType" value="Student" class="mr-2">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Student</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Student discount</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Cabins/Units -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2 block">Cabins / Units</label>
                            
                            <template x-if="!formData.client">
                                <div class="p-4 rounded-lg bg-gray-100 dark:bg-gray-800 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    Please select a client first
                                </div>
                            </template>

                            <template x-if="formData.client && formData.availableCabins.length === 0">
                                <div class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 text-center text-yellow-700 dark:text-yellow-400 text-sm">
                                    No locations available for this client
                                </div>
                            </template>

                            <template x-if="formData.client && formData.availableCabins.length > 0">
                                <div>
                                    <select x-model="formData.selectedCabinType"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 mb-3">
                                        <option value="">Select Type</option>
                                        <option value="Arrival">Arrival</option>
                                        <option value="Departure">Departure</option>
                                        <option value="Daily Clean">Daily Clean</option>
                                    </select>

                                    <div class="mt-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/40 max-h-32 overflow-y-auto">
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="cabin in formData.availableCabins" :key="cabin.id">
                                                <button type="button" @click="toggleCabin(cabin.name)"
                                                    :class="isCabinSelected(cabin.name) ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                                    class="px-3 py-1 rounded-full text-sm hover:opacity-80 transition">
                                                    <span x-text="cabin.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </form>
                </div>

                <!-- RIGHT PANEL - PREVIEW -->
                <div class="bg-blue-50 dark:bg-blue-950/30 p-8 flex flex-col justify-between rounded-xl">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Task Preview</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6"
                            x-text="formData.clientLabel ? 'for ' + formData.clientLabel : 'for Client Name'"></p>

                        <!-- Cabins Summary -->
                        <div class="space-y-2 mb-6">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-6">Cabins</p>

                            <template x-if="formData.cabinsList.length === 0">
                                <div class="flex justify-center bg-white dark:bg-gray-800 rounded-lg px-3 py-6 text-gray-400 dark:text-gray-500 text-sm italic">
                                    No cabins selected
                                </div>
                            </template>

                            <template x-for="(item, index) in formData.cabinsList" :key="index">
                                <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg px-3 py-2 text-sm">
                                    <div>
                                        <span class="font-medium" x-text="item.type"></span>
                                        <span class="text-gray-500 dark:text-gray-400 mx-2">•</span>
                                        <span x-text="item.cabin"></span>
                                    </div>
                                    <button type="button" @click="removeCabin(index)" 
                                        class="text-red-500 hover:text-red-700 text-sm">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Extra Tasks -->
                        <div class="space-y-1 text-sm">
                            <div class="flex flex-row w-full mb-6 justify-between">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Extra Tasks</p>
                                <button type="button" @click="addExtraTask"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-plus text-lg"></i>
                                </button>
                            </div>
                            
                            <template x-if="formData.extraTasks.length === 0">
                                <div class="flex justify-center bg-white dark:bg-gray-800 rounded-lg px-3 py-6 text-gray-400 dark:text-gray-500 text-sm italic">
                                    No extra tasks added
                                </div>
                            </template>

                            <template x-for="(task, index) in formData.extraTasks" :key="index">
                                <div class="flex items-center justify-between gap-3 bg-white dark:bg-gray-800 rounded-lg px-3 py-2">
                                    <input type="text" x-model="task.type" placeholder="Extra Task Type"
                                        class="flex-1 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500 dark:text-gray-400">₱</span>
                                        <input type="number" x-model="task.price" placeholder="Price" min="0" step="100"
                                            class="w-24 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <button type="button" @click="removeExtraTask(index)" class="text-red-500 hover:text-red-700 text-sm">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Summary Stats -->
                        <div class="text-sm space-y-3 mb-12 mt-12">
                            <div class="flex justify-between">
                                <span>Total Cabins</span>
                                <span class="font-medium" x-text="calculateTotalCabins() + ' cabins'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button @click="submitTask"
                            class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
                            Create Task
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div x-show="showEventDetailsModal" x-cloak
        x-transition.opacity
        @click="showEventDetailsModal = false"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div @click.stop 
            class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full max-h-[80vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="eventDetailsTitle"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="eventDetailsDate"></p>
                </div>
                <button @click="showEventDetailsModal = false" 
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-3">
                <template x-for="(event, idx) in eventDetailsList" :key="idx">
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="event.title"></span>
                            <span class="text-xs px-2 py-1 rounded-full" 
                                :class="event.statusColor"
                                x-text="event.status"></span>
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                            <div><i class="fa-solid fa-calendar-day w-4"></i> <span x-text="event.serviceType"></span></div>
                            <div x-show="event.cabins"><i class="fa-solid fa-home w-4"></i> <span x-text="event.cabins"></span></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Optimization Visualization Modal -->
    <div x-show="showOptimizationModal" x-cloak x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Optimization Process</h2>
                    <p class="text-blue-100 text-sm" x-show="optimizationData">
                        <span x-text="optimizationData?.optimization_run?.service_date"></span>
                    </p>
                </div>
                <button @click="closeOptimizationModal" class="text-white hover:text-red-200 transition-colors">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>

            <!-- Loading State -->
            <div x-show="optimizationLoading" class="flex-1 flex flex-col items-center justify-center p-12">
                <div class="relative">
                    <div class="w-24 h-24 border-8 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fa-solid fa-brain text-blue-600 text-3xl"></i>
                    </div>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-700 dark:text-gray-200">Optimizing Schedule...</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Running Genetic Algorithm</p>
            </div>

            <!-- Results Display (showing when not loading) -->
            <div x-show="!optimizationLoading && optimizationData" class="flex-1 overflow-y-auto p-6">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <i class="fa-solid fa-check-circle text-green-500 mr-2"></i>
                        Optimization Complete!
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Successfully optimized <span class="font-bold" x-text="optimizationData?.optimization_run?.total_tasks"></span> tasks 
                        across <span class="font-bold" x-text="optimizationData?.optimization_run?.total_teams"></span> teams.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                            <div class="text-blue-600 dark:text-blue-400 text-sm font-medium">Total Tasks</div>
                            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1" 
                                x-text="optimizationData?.optimization_run?.total_tasks"></div>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                            <div class="text-green-600 dark:text-green-400 text-sm font-medium">Teams</div>
                            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1" 
                                x-text="optimizationData?.optimization_run?.total_teams"></div>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                            <div class="text-purple-600 dark:text-purple-400 text-sm font-medium">Employees</div>
                            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1" 
                                x-text="optimizationData?.optimization_run?.total_employees"></div>
                        </div>
                        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4">
                            <div class="text-amber-600 dark:text-amber-400 text-sm font-medium">Fitness</div>
                            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                                <!-- ✅ FIXED: Handle both string and number, check for null/undefined -->
                                <template x-if="optimizationData?.optimization_run?.final_fitness_score">
                                    <span x-text="typeof optimizationData.optimization_run.final_fitness_score === 'number' ? 
                                                optimizationData.optimization_run.final_fitness_score.toFixed(4) : 
                                                parseFloat(optimizationData.optimization_run.final_fitness_score).toFixed(4)"></span>
                                </template>
                                <template x-if="!optimizationData?.optimization_run?.final_fitness_score">
                                    <span class="text-gray-400">N/A</span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div x-show="!optimizationLoading" class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-900">
                <button @click="closeOptimizationModal" 
                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function calendarComponent(initialClients, initialEvents) {
    return {
        today: new Date(),
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        monthYear: '',
        weekDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        dates: [],
        showModal: false,
        selectedDate: null,
        events: initialEvents || {},
        currentStatus: 'All',
        clientOptions: initialClients || [],
        showOptimizationModal: false,
        optimizationLoading: false,
        optimizationData: null,
        showEventDetailsModal: false,
        eventDetailsTitle: '',
        eventDetailsDate: '',
        eventDetailsList: [],
        
        formData: {
            client: '',
            clientLabel: '',
            serviceDate: '',
            serviceType: '',
            rateType: 'Normal',
            selectedCabinType: '',
            cabinsList: [],
            extraTasks: [],
            availableCabins: []
        },

        init() {
            this.renderCalendar();
        },

        groupEventsByClient(events) {
            const groups = {};
            const colors = [
                'bg-blue-100 text-blue-800 dark:bg-blue-900/80 dark:text-blue-200',
                'bg-purple-100 text-purple-800 dark:bg-purple-900/80 dark:text-purple-200',
                'bg-green-100 text-green-800 dark:bg-green-900/80 dark:text-green-200',
            ];
            
            events.forEach(event => {
                const clientName = event.title.split('-')[0].trim();
                if (!groups[clientName]) {
                    groups[clientName] = {
                        client: clientName,
                        count: 0,
                        color: colors[Object.keys(groups).length % colors.length],
                        tasks: []
                    };
                }
                groups[clientName].count++;
                groups[clientName].tasks.push(event);
            });
            return Object.values(groups);
        },

        renderCalendar() {
            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
            this.monthYear = `${firstDay.toLocaleString('default', { month: 'long' })} ${this.currentYear}`;
            const startDay = firstDay.getDay();
            const totalDays = lastDay.getDate();
            this.dates = [];

            for (let i = 0; i < startDay; i++) {
                this.dates.push({ date: '', events: [], groupedEvents: [] });
            }

            for (let d = 1; d <= totalDays; d++) {
                const key = `${this.currentYear}-${this.currentMonth + 1}-${d}`;
                const dayEvents = this.events[key] || [];
                this.dates.push({
                    date: d,
                    events: dayEvents,
                    groupedEvents: this.groupEventsByClient(dayEvents)
                });
            }
        },

        nextMonth() {
            this.currentMonth++;
            if (this.currentMonth > 11) {
                this.currentMonth = 0;
                this.currentYear++;
            }
            this.renderCalendar();
        },

        prevMonth() {
            this.currentMonth--;
            if (this.currentMonth < 0) {
                this.currentMonth = 11;
                this.currentYear--;
            }
            this.renderCalendar();
        },

        openModal(date) {
            // Reset form first
            this.resetForm();
            
            // Then set the service date
            if (date && date.date) {
                const dateString = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(date.date).padStart(2, '0')}`;
                this.selectedDate = dateString;
                this.formData.serviceDate = dateString;
            }
            
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
            this.selectedDate = null; // Clear selected date too
        },

        resetForm() {
            this.formData = {
                client: '',
                clientLabel: '',
                serviceDate: '', // This will be set by openModal
                serviceType: '',
                rateType: 'Normal',
                selectedCabinType: '',
                cabinsList: [],
                extraTasks: [],
                availableCabins: []
            };
        },

        handleClientChange() {
            const selectedClient = this.clientOptions.find(c => c.value === this.formData.client);
            if (selectedClient) {
                this.formData.clientLabel = selectedClient.label;
                this.formData.availableCabins = selectedClient.locations || [];
            } else {
                this.formData.clientLabel = '';
                this.formData.availableCabins = [];
            }
            this.formData.cabinsList = [];
        },

        toggleCabin(cabin) {
            if (!this.formData.selectedCabinType) {
                alert('Please select a cabin type first');
                return;
            }
            const existingIndex = this.formData.cabinsList.findIndex(
                item => item.type === this.formData.selectedCabinType && item.cabin === cabin
            );
            if (existingIndex > -1) {
                this.formData.cabinsList.splice(existingIndex, 1);
            } else {
                this.formData.cabinsList.push({
                    type: this.formData.selectedCabinType,
                    cabin: cabin
                });
            }
        },

        isCabinSelected(cabin) {
            return this.formData.cabinsList.some(
                item => item.type === this.formData.selectedCabinType && item.cabin === cabin
            );
        },

        removeCabin(index) {
            this.formData.cabinsList.splice(index, 1);
        },

        addExtraTask() {
            this.formData.extraTasks.push({ type: '', price: 0 });
        },

        removeExtraTask(index) {
            this.formData.extraTasks.splice(index, 1);
        },

        calculateTotalCabins() {
            return this.formData.cabinsList.length;
        },

        async submitTask() {
            // Validate BEFORE doing anything
            console.log('Form validation check:', {
                client: this.formData.client,
                serviceDate: this.formData.serviceDate,
                serviceType: this.formData.serviceType,
                cabinsList: this.formData.cabinsList
            });

            if (!this.formData.client) {
                alert('Please select a client');
                return;
            }
            
            if (!this.formData.serviceDate) {
                alert('Please select a service date');
                return;
            }
            
            if (!this.formData.serviceType) {
                alert('Please select a service type');
                return;
            }
            
            if (this.calculateTotalCabins() === 0) {
                alert('Please select at least one cabin');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                            document.querySelector('input[name="_token"]')?.value || '';
            
            if (!csrfToken) {
                alert('CSRF token not found. Please refresh the page.');
                return;
            }

            // ✅ SAVE form data BEFORE closing modal and showing optimization
            const requestData = {
                client: this.formData.client,
                serviceDate: this.formData.serviceDate,
                serviceType: this.formData.serviceType,
                rateType: this.formData.rateType,
                cabinsList: JSON.parse(JSON.stringify(this.formData.cabinsList)), // Deep copy
                extraTasks: JSON.parse(JSON.stringify(this.formData.extraTasks))   // Deep copy
            };

            console.log('Submitting task with data:', requestData);

            // NOW close modal and show optimization
            this.optimizationLoading = true;
            this.showOptimizationModal = true;
            this.closeModal(); // Safe to call now since we saved the data

            try {
                const response = await fetch('/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData) // Use saved data
                });

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const textResponse = await response.text();
                    console.error('Non-JSON response received:', textResponse);
                    throw new Error('Server returned an invalid response. Please check the console for details.');
                }

                const data = await response.json();
                console.log('Response:', data);

                if (response.ok) {
                    if (data.optimization_run_id) {
                        await this.loadOptimizationResults(data.optimization_run_id);
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        this.optimizationLoading = false;
                        this.closeOptimizationModal();
                        alert('Task created successfully! Message: ' + data.message);
                        window.location.reload();
                    }
                } else {
                    this.optimizationLoading = false;
                    this.closeOptimizationModal();
                    console.error('Server error:', data);
                    
                    // Better error display
                    if (data.errors) {
                        const errorMessages = Object.entries(data.errors)
                            .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
                            .join('\n');
                        alert('Validation errors:\n' + errorMessages);
                    } else {
                        alert('Error: ' + (data.message || JSON.stringify(data)));
                    }
                }
            } catch (error) {
                this.optimizationLoading = false;
                this.closeOptimizationModal();
                console.error('Error:', error);
                alert('Failed to create task: ' + error.message);
            }
        },

        async loadOptimizationResults(optimizationRunId) {
            try {
                const response = await fetch(`/admin/optimization/${optimizationRunId}/results`);
                const data = await response.json();
                
                // ✅ DETAILED DEBUG
                console.log('=== OPTIMIZATION DATA DEBUG ===');
                console.log('Full data:', data);
                console.log('optimization_run:', data?.optimization_run);
                console.log('final_fitness_score:', data?.optimization_run?.final_fitness_score);
                console.log('Type of fitness score:', typeof data?.optimization_run?.final_fitness_score);
                console.log('=== END DEBUG ===');
                
                this.optimizationData = data;
                this.optimizationLoading = false;
            } catch (error) {
                console.error('Error loading optimization results:', error);
                this.optimizationLoading = false;
            }
        },

        closeOptimizationModal() {
            this.showOptimizationModal = false;
            this.optimizationData = null;
        },

        showEventDetails(date, clientName) {
            const key = `${this.currentYear}-${this.currentMonth + 1}-${date.date}`;
            const allEvents = this.events[key] || [];
            
            const clientEvents = allEvents.filter(event => 
                event.title.split('-')[0].trim() === clientName
            );
            
            this.eventDetailsTitle = clientName;
            this.eventDetailsDate = new Date(this.currentYear, this.currentMonth, date.date).toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            this.eventDetailsList = clientEvents.map(event => {
                const titleParts = event.title.split('-');
                const extractedServiceType = titleParts.length > 1 ? titleParts[1].trim() : 'N/A';
                
                return {
                    title: event.title,
                    serviceType: event.serviceType || extractedServiceType,
                    status: event.status || 'Incomplete',
                    statusColor: this.getStatusColor(event.status),
                    cabins: event.cabins || event.location || event.cabin || 'N/A'
                };
            });
            
            this.showEventDetailsModal = true;
        },

        getStatusColor(status) {
            const colors = {
                'Completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                'In Progress': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                'Scheduled': 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-200',
                'Pending': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                'Incomplete': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
            };
            return colors[status] || colors['Incomplete'];
        }
    }
}
</script>
@endpush