@props([
    'events' => [], // Expected format: [{ id, title, date, startTime, endTime, time, description, color, status, position, height }]
    'initialView' => 'month'
])

<div x-data="calendarScheduler(@js($events), '{{ $initialView }}')" class="w-full bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
    <!-- Calendar Header -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 gap-4">
        <!-- Month/Year Display with Picker -->
        <div class="flex items-center gap-4">
            <div class="relative" x-data="{ showTooltip: false }">
                <button @click="showMonthPicker = !showMonthPicker"
                        @mouseenter="showTooltip = true"
                        @mouseleave="showTooltip = false"
                        class="text-base lg:text-base font-bold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span x-text="currentMonthYear"></span>
                </button>

                <!-- Tooltip -->
                <div x-show="showTooltip && !showMonthPicker"
                     x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 z-50">
                    <div class="bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium px-3 py-2 rounded-lg shadow-lg whitespace-nowrap">
                        Click to select a date
                        <!-- Tooltip Arrow -->
                        <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 dark:bg-gray-700 rotate-45"></div>
                    </div>
                </div>

                <!-- Month/Year Picker Dropdown -->
                <div x-show="showMonthPicker"
                     x-cloak
                     @click.away="showMonthPicker = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute top-full left-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-5 z-50 min-w-[340px]">
                    
                    <!-- Year Selector -->
                    <div class="flex items-center justify-between mb-5">
                        <button @click="pickerYear--; updatePickerDisplay()"
                                class="p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chevron-left text-gray-600 dark:text-gray-400 text-sm"></i>
                        </button>
                        <span class="text-lg font-black text-gray-900 dark:text-white" x-text="pickerYear"></span>
                        <button @click="pickerYear++; updatePickerDisplay()"
                                class="p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chevron-right text-gray-600 dark:text-gray-400 text-sm"></i>
                        </button>
                    </div>

                    <!-- Month Grid -->
                    <div class="grid grid-cols-3 gap-2.5">
                        <template x-for="(month, index) in months" :key="index">
                            <button @click="selectMonthYear(index, pickerYear)"
                                    class="px-4 py-2.5 text-sm font-semibold rounded-lg transition-all"
                                    :class="isSelectedMonth(index, pickerYear)
                                        ? 'bg-blue-600 text-white shadow-sm'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    x-text="month"></button>
                        </template>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex gap-2.5 mt-5 pt-5 border-t border-gray-200 dark:border-gray-700">
                        <button @click="goToCurrentMonth()"
                                class="flex-1 px-4 py-2.5 text-sm font-semibold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                            Current Month
                        </button>
                        <button @click="showMonthPicker = false"
                                class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Arrows -->
            <div class="flex items-center gap-2">
                <button @click="goToToday"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Today
                </button>
                <button @click="previousPeriod"
                        class="p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-chevron-left text-gray-600 dark:text-gray-400 text-sm"></i>
                </button>
                <button @click="nextPeriod"
                        class="p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-chevron-right text-gray-600 dark:text-gray-400 text-sm"></i>
                </button>
            </div>
        </div>

        <!-- View Options and Actions -->
        <div class="flex items-center gap-4">
            <!-- View Switcher -->
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button @click="view = 'day'; updateView()"
                        :class="view === 'day' ? 'bg-white dark:bg-gray-600 shadow-md' : ''"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 rounded-md transition-all">
                    Day
                </button>
                <button @click="view = 'week'; updateView()"
                        :class="view === 'week' ? 'bg-white dark:bg-gray-600 shadow-md' : ''"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 rounded-md transition-all">
                    Week
                </button>
                <button @click="view = 'month'; updateView()"
                        :class="view === 'month' ? 'bg-white dark:bg-gray-600 shadow-md' : ''"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 rounded-md transition-all">
                    Month
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar Body -->
    <div class="p-6">
        <!-- Week/Day View -->
        <div x-show="view === 'week' || view === 'day'" class="overflow-x-auto">
            <!-- Days Header -->
            <div class="grid gap-4 mb-6" :class="view === 'week' ? 'grid-cols-8' : 'grid-cols-2'">
                <!-- Time Column Header (Empty) -->
                <div class="text-xs font-bold text-gray-500 dark:text-gray-400 text-center">
                    <span x-show="view === 'week'">GMT+8</span>
                </div>

                <!-- Day Headers -->
                <template x-for="day in visibleDays" :key="day.date">
                    <div class="text-center p-2 rounded-lg" :class="day.isToday ? 'bg-blue-50 dark:bg-blue-900/20' : ''">
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide" x-text="day.dayName"></div>
                        <div class="text-2xl font-bold mt-1.5"
                             :class="day.isToday ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white'"
                             x-text="day.dayNumber"></div>
                    </div>
                </template>
            </div>

            <!-- Time Grid -->
            <div class="grid gap-4" :class="view === 'week' ? 'grid-cols-8' : 'grid-cols-2'">
                <!-- Time Labels Column -->
                <div class="space-y-10">
                    <template x-for="hour in timeSlots" :key="hour">
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 -mt-2" x-text="hour"></div>
                    </template>
                </div>

                <!-- Event Columns -->
                <template x-for="day in visibleDays" :key="day.date">
                    <div class="relative border-l border-gray-200 dark:border-gray-700 min-h-[440px]">
                        <!-- Hour Lines -->
                        <template x-for="(hour, index) in timeSlots" :key="index">
                            <div class="absolute w-full border-t border-gray-100 dark:border-gray-800"
                                 :style="`top: ${index * 40}px`"></div>
                        </template>

                        <!-- Events -->
                        <template x-for="event in getEventsForDay(day.date)" :key="event.id">
                            <div class="absolute left-1 right-1 rounded-lg p-2.5 cursor-pointer transition-all hover:shadow-xl hover:scale-[1.02]"
                                 :style="`top: ${getEventTop(event)}px; height: ${event.height}px; background-color: ${event.color}15; border-left: 4px solid ${event.color}`"
                                 @click="openEventModal(event)">
                                <div class="text-xs font-bold truncate" :style="`color: ${event.color}`" x-text="event.title"></div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1 font-medium" x-text="event.time"></div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <!-- Month View -->
        <div x-show="view === 'month'" class="grid grid-cols-7 gap-3">
            <!-- Day Names -->
            <template x-for="dayName in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']" :key="dayName">
                <div class="text-center text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wide py-3" x-text="dayName"></div>
            </template>

            <!-- Calendar Days -->
            <template x-for="day in monthDays" :key="day.date">
                <div class="min-h-[110px] p-3 border border-gray-200 dark:border-gray-700 rounded-lg transition-all hover:shadow-md"
                     :class="day.isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900 opacity-60'">
                    <div class="text-sm font-black mb-2"
                         :class="day.isToday ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 w-7 h-7 rounded-full flex items-center justify-center' : 'text-gray-900 dark:text-white'"
                         x-text="day.dayNumber"></div>

                    <!-- Events in Month View -->
                    <div class="space-y-1.5">
                        <template x-for="event in getEventsForDay(day.date).slice(0, 3)" :key="event.id">
                            <div class="text-xs p-1.5 rounded-md truncate cursor-pointer font-semibold transition-all hover:shadow-sm"
                                 :style="`background-color: ${event.color}15; color: ${event.color}; border-left: 3px solid ${event.color}`"
                                 @click="openEventModal(event)"
                                 x-text="event.title"></div>
                        </template>
                        <div x-show="getEventsForDay(day.date).length > 3"
                             class="text-xs text-gray-500 dark:text-gray-400 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition-colors pl-1"
                             x-text="`+${getEventsForDay(day.date).length - 3} more`"></div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Event Modal -->
    <div x-show="showEventModal"
         x-cloak
         @click.self="showEventModal = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 dark:bg-black/80 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-8 transform transition-all border border-gray-200 dark:border-gray-700"
             @click.away="showEventModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <!-- Modal Header -->
            <div class="flex justify-between items-start mb-5">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-tasks text-blue-600 dark:text-blue-400"></i>
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Task Details</span>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white" x-text="selectedEvent?.title"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-medium flex items-center gap-2">
                        <i class="fas fa-clock text-xs"></i>
                        <span x-text="selectedEvent?.time"></span>
                    </p>
                </div>
                <button @click="showEventModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Task Status Indicator -->
            <div class="h-1.5 rounded-full mb-6" :style="`background-color: ${selectedEvent?.color}`"></div>

            <!-- Task Details -->
            <div class="space-y-4">
                <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                    <i class="fas fa-calendar text-blue-600 dark:text-blue-400 mt-1"></i>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Scheduled Date</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="formatDate(selectedEvent?.date)"></p>
                    </div>
                </div>

                <div x-show="selectedEvent?.description" class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                    <i class="fas fa-map-marker-alt text-blue-600 dark:text-blue-400 mt-1"></i>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Location & Duration</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedEvent?.description"></p>
                    </div>
                </div>

                <div x-show="selectedEvent?.status" class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-1"></i>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Status</p>
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full"
                              :style="`background-color: ${selectedEvent?.color}20; color: ${selectedEvent?.color}`"
                              x-text="selectedEvent?.status || 'Scheduled'"></span>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex gap-3 mt-8">
                <button @click="viewTaskDetails(selectedEvent)"
                        class="flex-1 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-semibold shadow-sm hover:shadow-md">
                    <i class="fas fa-eye mr-2 text-sm"></i>View Details
                </button>
                <button @click="showEventModal = false"
                        class="flex-1 px-5 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg transition-colors font-semibold shadow-sm hover:shadow-md">
                    <i class="fas fa-times mr-2 text-sm"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function calendarScheduler(initialEvents, initialView) {
    return {
        events: initialEvents,
        view: initialView,
        currentDate: new Date(),
        visibleDays: [],
        monthDays: [],
        timeSlots: ['08 AM', '09 AM', '10 AM', '11 AM', '12 PM', '01 PM', '02 PM', '03 PM', '04 PM', '05 PM', '06 PM'],
        showEventModal: false,
        selectedEvent: null,
        showMonthPicker: false,
        pickerYear: new Date().getFullYear(),
        months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],

        init() {
            this.updateView();
            this.pickerYear = this.currentDate.getFullYear();
        },

        get currentMonthYear() {
            return this.currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        },

        updateView() {
            if (this.view === 'week') {
                this.generateWeekDays();
            } else if (this.view === 'day') {
                this.generateDayView();
            } else if (this.view === 'month') {
                this.generateMonthDays();
            }
        },

        updatePickerDisplay() {
            // Update picker when year changes
        },

        isSelectedMonth(monthIndex, year) {
            return this.currentDate.getMonth() === monthIndex && 
                   this.currentDate.getFullYear() === year;
        },

        selectMonthYear(monthIndex, year) {
            this.currentDate = new Date(year, monthIndex, 1);
            this.showMonthPicker = false;
            this.updateView();
        },

        goToCurrentMonth() {
            this.currentDate = new Date();
            this.pickerYear = this.currentDate.getFullYear();
            this.showMonthPicker = false;
            this.updateView();
        },

        generateWeekDays() {
            const days = [];
            const startOfWeek = new Date(this.currentDate);
            startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay() + 1); // Monday

            for (let i = 0; i < 7; i++) {
                const date = new Date(startOfWeek);
                date.setDate(date.getDate() + i);
                const dateString = date.getFullYear() + '-' + 
                                 String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                                 String(date.getDate()).padStart(2, '0');
                days.push({
                    date: dateString,
                    dayName: date.toLocaleDateString('en-US', { weekday: 'short' }).toUpperCase(),
                    dayNumber: date.getDate(),
                    isToday: this.isToday(date)
                });
            }
            console.log('Week View Days:', days);
            this.visibleDays = days;
        },

        generateDayView() {
            const date = new Date(this.currentDate);
            const dateString = date.getFullYear() + '-' + 
                             String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                             String(date.getDate()).padStart(2, '0');
            this.visibleDays = [{
                date: dateString,
                dayName: date.toLocaleDateString('en-US', { weekday: 'long' }).toUpperCase(),
                dayNumber: date.getDate(),
                isToday: this.isToday(date)
            }];
        },

        generateMonthDays() {
            const days = [];
            const year = this.currentDate.getFullYear();
            const month = this.currentDate.getMonth();
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - (startDate.getDay() || 7) + 1);

            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(date.getDate() + i);
                const dateString = date.getFullYear() + '-' + 
                                 String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                                 String(date.getDate()).padStart(2, '0');
                days.push({
                    date: dateString,
                    dayNumber: date.getDate(),
                    isCurrentMonth: date.getMonth() === month,
                    isToday: this.isToday(date)
                });
            }
            console.log('Month View Days:', days.filter(d => d.isCurrentMonth));
            this.monthDays = days;
        },

        getEventsForDay(date) {
            return this.events.filter(event => event.date === date);
        },

        getEventTop(event) {
            const [hours, minutes] = event.startTime.split(':').map(Number);
            const startHour = 8; // Calendar starts at 8 AM
            const pixelsPerHour = 40; // Changed from 60 to 40
            return ((hours - startHour) * pixelsPerHour) + (minutes * pixelsPerHour / 60);
        },

        isToday(date) {
            const today = new Date();
            return date.toDateString() === today.toDateString();
        },

        previousPeriod() {
            if (this.view === 'week') {
                this.currentDate.setDate(this.currentDate.getDate() - 7);
            } else if (this.view === 'day') {
                this.currentDate.setDate(this.currentDate.getDate() - 1);
            } else if (this.view === 'month') {
                this.currentDate.setMonth(this.currentDate.getMonth() - 1);
            }
            this.pickerYear = this.currentDate.getFullYear();
            this.updateView();
        },

        nextPeriod() {
            if (this.view === 'week') {
                this.currentDate.setDate(this.currentDate.getDate() + 7);
            } else if (this.view === 'day') {
                this.currentDate.setDate(this.currentDate.getDate() + 1);
            } else if (this.view === 'month') {
                this.currentDate.setMonth(this.currentDate.getMonth() + 1);
            }
            this.pickerYear = this.currentDate.getFullYear();
            this.updateView();
        },

        goToToday() {
            this.currentDate = new Date();
            this.pickerYear = this.currentDate.getFullYear();
            this.updateView();
        },

        openEventModal(event) {
            this.selectedEvent = event;
            this.showEventModal = true;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        },

        viewTaskDetails(task) {
            // Redirect to task details page or open detailed task view
            if (task && task.id) {
                // You can redirect to a task details page
                window.location.href = `/employee/tasks/${task.id}`;
            }
            this.showEventModal = false;
        }
    }
}
</script>

<style>
[x-cloak] {
    display: none !important;
}
</style>