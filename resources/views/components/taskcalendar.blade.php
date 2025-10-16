<div x-data="calendarComponent()" x-init="init()"
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

                    <form @submit.prevent="addTask" class="space-y-6">
                        <!-- Client Dropdown -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1 block">Client</label>
                            <div @click="handleClientClick($event)" @change="handleClientChange($event)">
                                <x-twolayerdropdown label="Select a Company"
                                    description="Choose the client or company to assign this task to." 
                                    :options="[
                                        ['label' => 'ABC Cleaning Co.'],
                                        ['label' => 'PrimeStay Hotels'],
                                        ['label' => 'Urban Residences']
                                    ]" />
                            </div>
                        </div>

                        <!-- Service Date -->
                        <div>
                            <x-inputfieldregular label="Service Date" inputId="serviceDate" inputName="serviceDate"
                                inputType="text" placeholder="Select a date"
                                x-model="selectedDate"
                                icon='<i class="fa-solid fa-calendar text-gray-500 dark:text-gray-400"></i>' 
                                disabled />
                        </div>

                        <!-- Service Type Dropdown -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1 block">Service Type</label>
                            <div @click="handleServiceTypeClick($event)" @change="handleServiceTypeChange($event)">
                                <x-twolayerdropdown label="Select a Service"
                                    description="Choose the service for this custom task addition." 
                                    :options="[
                                        ['label' => 'Deep Cleaning'],
                                        ['label' => 'Daily Room Cleaning'],
                                        ['label' => 'Snowout Cleaning'],
                                        ['label' => 'Light Daily Cleaning'],
                                    ]" />
                            </div>
                        </div>

                        <!-- Rate Type -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2 block">Type of Rates</label>
                            <div class="grid grid-cols-2 gap-4" @change="handleRateTypeChange($event)">
                                <x-checkboxcard title="Normal"
                                    description="Standard pricing for guests under the regular category. Discounts may vary." 
                                    value="normal" name="rate-type" :checked="true" />
                                <x-checkboxcard title="Student"
                                    description="Applicable to student guests who qualify for a fixed student discount."
                                    value="student" name="rate-type" />
                            </div>
                        </div>

                        <!-- Cabins/Units Section -->
                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2 block">Cabins / Units</label>
                            
                            <div @click="handleCabinTypeClick($event)" @change="handleCabinTypeChange($event)">
                                <x-twolayerdropdown label="Select Type"
                                    description="Choose the cabin category for this task"
                                    :options="[
                                        ['label' => 'Arrival'],
                                        ['label' => 'Departure'],
                                        ['label' => 'Daily Clean']
                                    ]" />
                            </div>

                            <div class="mt-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/40" 
                                 @change="handleCabinSelection($event)"
                                 @click="handleCabinSelection($event)">
                                <x-togglepills label="Select Cabins" 
                                    :pills="['Cabin 1', 'Cabin 2', 'Cabin 3', 'Cabin 4', 'Cabin 5', 'Cabin 6', 'Cabin 7', 'Cabin 8']" 
                                    name="cabin-selection" max-height="max-h-32" />
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full py-3 border-2 border-blue-600 text-blue-600 rounded-xl font-semibold hover:bg-blue-600 hover:text-white transition">
                                Add
                            </button>
                        </div>
                    </form>
                </div>

                <!-- RIGHT PANEL - PREVIEW -->
                <div class="bg-blue-50 dark:bg-blue-950/30 p-8 flex flex-col justify-between rounded-xl">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Task Preview</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6"
                            x-text="formData.client ? 'for ' + formData.client : 'for Client Name'"></p>

                        <div class="bg-blue-50 dark:bg-blue-950/30 flex flex-row justify-between mb-6">
                            <div>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Assigned
                                    Employees <span class="text-xs text-gray-400" x-text="'(' + calculateWorkforce() + ')'"></span></p>
                            </div>
                            <x-teamavatarcols :teamName="'employees'" 
                                :members="['member-1', 'member-2', 'member-3', 'member-4', 'member-5', 'member6', 'member7', 'member8']" />
                        </div>

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
                        <button @click="addTask"
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
        function calendarComponent() {
            return {
                today: new Date(),
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                monthYear: '',
                weekDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                dates: [],
                showModal: false,
                selectedDate: null,
                events: {},
                currentStatus: 'All',
                showStatusDropdown: false,
                
                // Form data
                formData: {
                    client: '',
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
                        this.selectedDate = `${this.currentYear}-${this.currentMonth + 1}-${date.date}`;
                    } else {
                        // Manual modal open without a date
                        const today = new Date();
                        this.selectedDate = `${today.getFullYear()}-${today.getMonth() + 1}-${today.getDate()}`;
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
                        serviceType: '',
                        rateType: 'Normal',
                        selectedCabinType: '',
                        cabinsList: [],
                        extraTasks: []
                    };
                },

                // Handle component change events
                handleClientChange(event) {
                    const target = event.target;
                    if (target.tagName === 'INPUT' || target.tagName === 'SELECT') {
                        this.formData.client = target.value || target.textContent?.trim();
                    }
                },

                handleClientClick(event) {
                    const target = event.target;
                    // Handle clicks on dropdown items
                    if (target.textContent && target.textContent.trim()) {
                        const text = target.textContent.trim();
                        if (text === 'ABC Cleaning Co.' || text === 'PrimeStay Hotels' || text === 'Urban Residences') {
                            this.formData.client = text;
                        }
                    }
                },

                handleServiceTypeChange(event) {
                    const target = event.target;
                    if (target.tagName === 'INPUT' || target.tagName === 'SELECT') {
                        this.formData.serviceType = target.value || target.textContent?.trim();
                    }
                },

                handleServiceTypeClick(event) {
                    const target = event.target;
                    const validServices = ['Deep Cleaning', 'Daily Room Cleaning', 'Snowout Cleaning', 'Light Daily Cleaning'];
                    if (target.textContent && target.textContent.trim()) {
                        const text = target.textContent.trim();
                        if (validServices.includes(text)) {
                            this.formData.serviceType = text;
                        }
                    }
                },

                handleRateTypeChange(event) {
                    const target = event.target;
                    if (target.type === 'radio' || target.type === 'checkbox') {
                        this.formData.rateType = target.value === 'normal' ? 'Normal' : 'Student';
                    }
                },

                handleCabinTypeChange(event) {
                    const target = event.target;
                    if (target.tagName === 'INPUT' || target.tagName === 'SELECT' || target.tagName === 'BUTTON') {
                        const value = target.value || target.textContent?.trim();
                        if (value === 'Arrival' || value === 'Departure' || value === 'Daily Clean') {
                            this.formData.selectedCabinType = value;
                        }
                    }
                },

                handleCabinTypeClick(event) {
                    const target = event.target;
                    const validTypes = ['Arrival', 'Departure', 'Daily Clean'];
                    if (target.textContent && target.textContent.trim()) {
                        const text = target.textContent.trim();
                        if (validTypes.includes(text)) {
                            this.formData.selectedCabinType = text;
                        }
                    }
                },

                handleCabinSelection(event) {
                    const target = event.target;
                    
                    // Handle checkbox changes
                    if (target.type === 'checkbox') {
                        const cabinName = target.value || target.name || target.getAttribute('data-cabin');
                        
                        // Try to extract cabin name from nearby elements if not in value
                        let cabin = cabinName;
                        if (!cabin || !cabin.includes('Cabin')) {
                            const label = target.closest('label');
                            if (label) {
                                cabin = label.textContent?.trim();
                            }
                        }
                        
                        if (!this.formData.selectedCabinType) {
                            alert('Please select a cabin type (Arrival, Departure, or Daily Clean) first');
                            event.preventDefault();
                            target.checked = false;
                            return;
                        }
                        
                        if (target.checked && cabin) {
                            // Check if this cabin type combination already exists
                            const exists = this.formData.cabinsList.some(
                                item => item.type === this.formData.selectedCabinType && item.cabin === cabin
                            );
                            
                            if (!exists) {
                                this.formData.cabinsList.push({
                                    type: this.formData.selectedCabinType,
                                    cabin: cabin
                                });
                                console.log('Added cabin:', cabin, 'Type:', this.formData.selectedCabinType);
                            }
                        } else if (!target.checked && cabin) {
                            // Remove the item if unchecked
                            const index = this.formData.cabinsList.findIndex(
                                item => item.type === this.formData.selectedCabinType && item.cabin === cabin
                            );
                            if (index > -1) {
                                this.formData.cabinsList.splice(index, 1);
                                console.log('Removed cabin:', cabin);
                            }
                        }
                    }
                    
                    // Also handle click events on pill buttons
                    if (target.tagName === 'BUTTON' || target.closest('button')) {
                        const button = target.tagName === 'BUTTON' ? target : target.closest('button');
                        const cabinText = button.textContent?.trim();
                        
                        if (cabinText && cabinText.includes('Cabin')) {
                            if (!this.formData.selectedCabinType) {
                                alert('Please select a cabin type (Arrival, Departure, or Daily Clean) first');
                                return;
                            }
                            
                            // Toggle cabin in list
                            const existingIndex = this.formData.cabinsList.findIndex(
                                item => item.type === this.formData.selectedCabinType && item.cabin === cabinText
                            );
                            
                            if (existingIndex > -1) {
                                this.formData.cabinsList.splice(existingIndex, 1);
                                console.log('Removed cabin:', cabinText);
                            } else {
                                this.formData.cabinsList.push({
                                    type: this.formData.selectedCabinType,
                                    cabin: cabinText
                                });
                                console.log('Added cabin:', cabinText, 'Type:', this.formData.selectedCabinType);
                            }
                        }
                    }
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

                addTask() {
                    if (!this.formData.client || !this.formData.serviceType) {
                        alert('Please fill in client and service type');
                        return;
                    }

                    if (this.calculateTotalCabins() === 0) {
                        alert('Please select at least one cabin');
                        return;
                    }

                    const key = this.selectedDate;
                    if (!this.events[key]) this.events[key] = [];
                    
                    this.events[key].push({
                        title: `${this.formData.client} - ${this.formData.serviceType}`,
                        color: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                    });

                    this.closeModal();
                    this.renderCalendar();
                    
                    alert('Task added successfully to calendar!');
                }
            }
        }
    </script>
@endpush
@stack('scripts')