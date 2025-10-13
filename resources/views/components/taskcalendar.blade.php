<div x-data="calendarComponent()" x-init="init()"
    class="w-full h-full bg-gray-50 dark:bg-gray-900 rounded-lg shadow p-4 transition-colors duration-300">

    <!-- Header -->
    <div class="flex flex-row justify-between items-center mb-4 w-full">
        <h2 class="text-base justify-start font-semibold text-gray-800 dark:text-gray-100" x-text="monthYear"></h2>
        <div class="flex flex-row gap-6">

            <div class="flex flex-row gap-3">
                @php
                    $taskStatus = ['Incomplete', 'In Progress', 'Completed'];
                @endphp

                <x-dropdown :options="$taskStatus" default="Now" id="dropdown-taskStatus" />
                <x-button label="New Task" color="blue" size="sm" icon='<i class="fa-solid fa-plus"></i>' />
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
        <div @click.away="closeModal" class="bg-white dark:bg-gray-900 rounded-2xl w-full max-w-6xl shadow-xl relative 
               max-h-[90vh] overflow-y-auto 
               border border-gray-200 dark:border-gray-700 transform transition-all duration-300">

            <button @click="closeModal"
                class="sticky top-0 right-0 m-4 text-gray-600 dark:text-gray-300 hover:text-red-500 transition-colors z-20 bg-white dark:bg-gray-900 p-2 rounded-full shadow">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2 m-6">
                <div class="p-8 space-y-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Create a Task</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Adding a new custom task</p>
                    </div>

                    <form @submit.prevent="addTask" class="space-y-6">
                        <div>
                            <label
                                class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1 block">Client</label>
                            <x-twolayerdropdown label="Select a Company"
                                description="Choose the client or company to assign this task to." :options="[
        ['label' => 'ABC Cleaning Co.'],
        ['label' => 'PrimeStay Hotels'],
        ['label' => 'Urban Residences']
    ]" />
                        </div>

                        <div>
                            <x-inputfieldregular label="Service Date" inputId="serviceDate" inputName="serviceDate"
                                inputType="text" placeholder="Select a Date"
                                icon='<i class="fa-solid fa-calendar text-gray-500 dark:text-gray-400"></i>' disabled
                                aria-readonly />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2 block">Type of
                                Rates</label>
                            <div class="grid grid-cols-2 gap-4">
                                <x-checkboxcard title="Normal"
                                    description="Standard pricing for guests under the regular category. Discounts may vary." value="normal"
                                    name="rate-type" :checked="true" />
                                <x-checkboxcard title="Student"
                                    description="Applicable to student guests who qualify for a fixed student discount."
                                    value="student" name="rate-type" />
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2 block">Cabins /
                                Units</label>
                            <x-twolayerdropdown label="Arrival"
                                description="Standard pricing for guests under the regular category, with no discounts applied."
                                :options="[
        ['label' => 'Arrival'],
        ['label' => 'Departure'],
        ['label' => 'Daily Clean']
    ]" />

                            <div
                                class="mt-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/40">
                                <x-togglepills label="Select Services" :pills="['Cabin 1', 'Cabin 2', 'Cabin 3', 'Cabin 4', 'Cabin 5', 'Cabin 6', 'Cabin 7', 'Cabin 8']" name="services" max-height="max-h-32" />
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

                <div class="bg-blue-50 dark:bg-blue-950/30 p-8 flex flex-col justify-between rounded-xl">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Task Preview</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6"
                            x-text="'for ' + (selectedClient || 'Client Name')"></p>

                        <div class="bg-blue-50 dark:bg-blue-950/30 flex flex-row justify-between mb-6">
                            <div>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Assigned
                                    Employees <span class="text-xs text-gray-400">(8)</span></p>
                            </div>
                            <x-teamavatarcols :teamName="'23 employees'" :members="['member-1', 'member-2', 'member-3', 'member-4', 'member-5', 'member6', 'member7', 'member8']" />
                        </div>
                        <div class="space-y-2 mb-6">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-6">Cabins</p>

                            <div
                                class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg px-3 py-2 text-sm">
                                <span>Arrival</span>
                                <span class="text-gray-500 dark:text-gray-400">0 cabins selected</span>
                            </div>
                            <div
                                class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg px-3 py-2 text-sm">
                                <span>Departure</span>
                                <span class="text-gray-500 dark:text-gray-400">10 cabins selected</span>
                            </div>
                            <div
                                class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg px-3 py-2 text-sm">
                                <span>Daily Clean</span>
                                <span class="text-gray-500 dark:text-gray-400">20 cabins selected</span>
                            </div>
                        </div>

                        <div class="space-y-1 text-sm">
                            <div class="flex flex-row w-full mb-6 justify-between">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Extra Tasks</p>
                                <button type="button"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-plus text-lg"></i>
                                </button>

                            </div>
                            <div class="flex justify-between bg-white dark:bg-gray-800 rounded-lg px-3 py-2">
                                <span>Extra Task Type</span>
                                <span class="text-gray-500">₱ Price</span>
                            </div>
                            <div class="flex justify-between bg-white dark:bg-gray-800 rounded-lg px-3 py-2">
                                <span>Extra Task Type</span>
                                <span class="text-gray-500">₱ Price</span>
                            </div>
                        </div>

                        <div class="text-sm space-y-3 mb-12 mt-12">
                            <div class="flex justify-between">
                                <span>Total Work Duration</span>
                                <span class="font-medium">23 hours</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Workforce Size</span>
                                <span class="font-medium">22 employees</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button
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
                newEventTitle: '',
                newEventColor: 'bg-blue-100 text-blue-800',
                events: {},

                init() {
                    this.renderCalendar();
                    this.updateTheme();
                },

                renderCalendar() {
                    const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                    const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
                    this.monthYear = `${firstDay.toLocaleString('default', { month: 'long' })} ${this.currentYear}`;

                    const startDay = firstDay.getDay();
                    const totalDays = lastDay.getDate();
                    this.dates = [];

                    // Fill in empty days before start
                    for (let i = 0; i < startDay; i++) {
                        this.dates.push({ date: '', events: [] });
                    }

                    // Fill in dates
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
                    if (!date.date) return;
                    this.selectedDate = `${this.currentYear}-${this.currentMonth + 1}-${date.date}`;
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                    this.newEventTitle = '';
                },

                addEvent() {
                    const key = this.selectedDate;
                    if (!this.events[key]) this.events[key] = [];
                    this.events[key].push({
                        title: this.newEventTitle,
                        color: this.newEventColor
                    });
                    this.closeModal();
                    this.renderCalendar();
                },
            }
        }
    </script>
@endpush
@stack('scripts')