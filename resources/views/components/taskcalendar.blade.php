<!-- resources/views/components/taskcalendar.blade.php -->

@props(['clients', 'events'])

<div x-data="calendarComponent(@js($clients), @js($events))" x-init="init()"
    class="w-full h-full bg-gray-50 dark:bg-gray-900 rounded-lg shadow p-4 transition-colors duration-300">

    <!-- Header -->
    <div class="flex flex-row justify-between items-center mb-4 w-full">
        <h2 class="text-base justify-start font-semibold text-gray-800 dark:text-gray-100" x-text="monthYear"></h2>
        <div class="flex flex-row gap-6">
            <div class="flex flex-row gap-3">
                <!-- Status Filter Dropdown -->
                <div class="relative inline-block">
                    <button @click="toggleStatusDropdown" type="button"
                        class="bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-3 inline-flex justify-between items-center gap-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-blue-800 transition-all duration-300">
                        <span class="text-gray-700 dark:text-white text-xs font-normal">Show:</span>
                        <span class="text-gray-700 dark:text-white text-xs font-normal" x-text="currentStatus"></span>
                        <svg :class="showStatusDropdown ? 'rotate-180' : ''"
                            class="w-2.5 h-2.5 ms-2 transition-transform duration-300 text-gray-600 dark:text-gray-400"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>

                    <div x-show="showStatusDropdown" @click.away="showStatusDropdown = false" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 w-full top-full mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-lg w-40 dark:bg-gray-700">
                        <ul class="py-2 text-xs text-gray-700 dark:text-white">
                            <li>
                                <button @click="currentStatus = 'All'; showStatusDropdown = false" type="button"
                                    class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    All
                                </button>
                            </li>
                            <li>
                                <button @click="currentStatus = 'Incomplete'; showStatusDropdown = false" type="button"
                                    class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    Incomplete
                                </button>
                            </li>
                            <li>
                                <button @click="currentStatus = 'In Progress'; showStatusDropdown = false" type="button"
                                    class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    In Progress
                                </button>
                            </li>
                            <li>
                                <button @click="currentStatus = 'Completed'; showStatusDropdown = false" type="button"
                                    class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    Completed
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

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

                <!-- Events -->
                <template x-for="(event, eindex) in date.events" :key="eindex">
                    <div class="mt-1 text-xs truncate rounded px-1.5 py-0.5" :class="event.color">
                        <span x-text="event.title"></span>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <!-- Modal -->
    <div x-show="showModal" x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 overflow-y-auto p-4 py-8">
        <div @click.away="closeModal" class="bg-white dark:bg-gray-900 rounded-2xl w-full max-w-6xl relative 
               max-h-fit overflow-y-auto 
               border border-gray-200 dark:border-gray-700 transform transition-all duration-300">

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
                        <!-- Client Dropdown with Database Data -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1 block">Client</label>
                            <div class="relative">
                                <select x-model="formData.client" @change="handleClientChange"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select a Client</option>
                                    <template x-for="client in clientOptions" :key="client.value">
                                        <option :value="client.value" x-text="client.label"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Service Date -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1 block">Service Date</label>
                            <input type="text" x-model="selectedDate" disabled
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white"
                                placeholder="Select a date from calendar">
                        </div>

                        <!-- Service Type Dropdown -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1 block">Service Type</label>
                            <select x-model="formData.serviceType"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="">Select a Service</option>
                                <option value="Deep Cleaning">Deep Cleaning</option>
                                <option value="Daily Room Cleaning">Daily Room Cleaning</option>
                                <option value="Snowout Cleaning">Snowout Cleaning</option>
                                <option value="Light Daily Cleaning">Light Daily Cleaning</option>
                                <option value="Full Daily Cleaning">Full Daily Cleaning</option>
                            </select>
                        </div>

                        <!-- Rate Type -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2 block">Type of Rates</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer" :class="formData.rateType === 'Normal' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'">
                                    <input type="radio" x-model="formData.rateType" value="Normal" class="mr-2">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Normal</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Standard pricing</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer" :class="formData.rateType === 'Student' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'">
                                    <input type="radio" x-model="formData.rateType" value="Student" class="mr-2">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Student</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Student discount</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Cabins/Units Section -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2 block">Cabins / Units</label>
                            
                            <select x-model="formData.selectedCabinType"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 mb-3">
                                <option value="">Select Type</option>
                                <option value="Arrival">Arrival</option>
                                <option value="Departure">Departure</option>
                                <option value="Daily Clean">Daily Clean</option>
                            </select>

                            <div class="mt-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/40 max-h-32 overflow-y-auto">
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="cabin in ['Cabin 1', 'Cabin 2', 'Cabin 3', 'Cabin 4', 'Cabin 5', 'Cabin 6', 'Cabin 7', 'Cabin 8']" :key="cabin">
                                        <button type="button" @click="toggleCabin(cabin)"
                                            :class="isCabinSelected(cabin) ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                            class="px-3 py-1 rounded-full text-sm hover:opacity-80 transition">
                                            <span x-text="cabin"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full py-3 border-2 border-blue-600 text-blue-600 rounded-xl font-semibold hover:bg-blue-600 hover:text-white transition">
                                Add Task
                            </button>
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
                                    <input type="text" 
                                        x-model="task.type"
                                        placeholder="Extra Task Type"
                                        class="flex-1 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                    
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500 dark:text-gray-400">₱</span>
                                        <input type="number" 
                                            x-model="task.price"
                                            placeholder="Price"
                                            min="0"
                                            step="100"
                                            class="w-24 px-2 py-1 text-sm rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                        
                                        <button type="button" @click="removeExtraTask(index)" 
                                            class="text-red-500 hover:text-red-700 text-sm">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Summary Stats -->
                        <div class="text-sm space-y-3 mb-12 mt-12">
                            <div class="flex justify-between">
                                <span>Total Work Duration</span>
                                <span class="font-medium" x-text="calculateTotalHours() + ' hours'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Workforce Size</span>
                                <span class="font-medium" x-text="calculateWorkforce() + ' employees'"></span>
                            </div>
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
            showStatusDropdown: false,
            clientOptions: initialClients || [],
            
            // Form data
            formData: {
                client: '',
                clientLabel: '',
                serviceType: '',
                rateType: 'Normal',
                selectedCabinType: '',
                cabinsList: [],
                extraTasks: []
            },

            init() {
                this.renderCalendar();
            },

            toggleStatusDropdown() {
                this.showStatusDropdown = !this.showStatusDropdown;
            },

            renderCalendar() {
                const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
                this.monthYear = `${firstDay.toLocaleString('default', { month: 'long' })} ${this.currentYear}`;

                const startDay = firstDay.getDay();
                const totalDays = lastDay.getDate();
                this.dates = [];

                for (let i = 0; i < startDay; i++) {
                    this.dates.push({ date: '', events: [] });
                }

                for (let d = 1; d <= totalDays; d++) {
                    const key = `${this.currentYear}-${this.currentMonth + 1}-${d}`;
                    this.dates.push({
                        date: d,
                        events: this.events[key] || []
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
                if (date && date.date) {
                    this.selectedDate = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(date.date).padStart(2, '0')}`;
                } else {
                    const today = new Date();
                    this.selectedDate = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
                }
                this.resetForm();
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
                this.resetForm();
            },

            resetForm() {
                this.formData = {
                    client: '',
                    clientLabel: '',
                    serviceType: '',
                    rateType: 'Normal',
                    selectedCabinType: '',
                    cabinsList: [],
                    extraTasks: []
                };
            },

            handleClientChange() {
                const selectedClient = this.clientOptions.find(c => c.value === this.formData.client);
                this.formData.clientLabel = selectedClient ? selectedClient.label : '';
            },

            toggleCabin(cabin) {
                if (!this.formData.selectedCabinType) {
                    alert('Please select a cabin type (Arrival, Departure, or Daily Clean) first');
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
                this.formData.extraTasks.push({
                    type: '',
                    price: 0
                });
            },

            removeExtraTask(index) {
                this.formData.extraTasks.splice(index, 1);
            },

            calculateTotalCabins() {
                return this.formData.cabinsList.length;
            },

            calculateTotalHours() {
                const totalCabins = this.calculateTotalCabins();
                const baseHours = totalCabins * 2;
                const extraHours = this.formData.extraTasks.length * 3;
                return baseHours + extraHours;
            },

            calculateWorkforce() {
                const totalCabins = this.calculateTotalCabins();
                return Math.max(8, Math.ceil(totalCabins / 3));
            },

            async submitTask() {
                if (!this.formData.client || !this.formData.serviceType) {
                    alert('Please fill in client and service type');
                    return;
                }

                if (this.calculateTotalCabins() === 0) {
                    alert('Please select at least one cabin');
                    return;
                }

                try {
                    const response = await fetch('/admin/tasks', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            client: this.formData.client,
                            serviceDate: this.selectedDate,
                            serviceType: this.formData.serviceType,
                            rateType: this.formData.rateType,
                            cabinsList: this.formData.cabinsList,
                            extraTasks: this.formData.extraTasks
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // Add to calendar display
                        const key = this.selectedDate.split('-').map((part, index) => index === 1 ? parseInt(part) : parseInt(part)).join('-');
                        if (!this.events[key]) this.events[key] = [];
                        
                        this.events[key].push({
                            title: `${this.formData.clientLabel} - ${this.formData.serviceType}`,
                            color: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                        });

                        this.closeModal();
                        this.renderCalendar();
                        
                        alert('Task created successfully!');
                        window.location.reload(); // Reload to show updated data
                    } else {
                        alert('Error creating task: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to create task. Please try again.');
                }
            }
        }
    }
</script>
@endpush