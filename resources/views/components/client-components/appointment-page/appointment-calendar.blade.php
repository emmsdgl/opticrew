@props([
    'events' => [],
    'initialView' => 'week'
])

<div x-data="calendarScheduler(@js($events), '{{ $initialView }}')" class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg">
    <!-- Calendar Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <!-- Month/Year Display with Picker -->
        <div class="flex items-center gap-4">
            <div class="relative">
                <button @click="showMonthPicker = !showMonthPicker" 
                        class="text-base font-bold gap-4 text-gray-900 dark:text-white hover:text-blue-500 dark:hover:text-blue-500 px-3 py-2 rounded-lg transition-colors flex items-center">
                    <span x-text="currentMonthYear"></span>
                </button>

                <!-- Month/Year Picker Dropdown -->
                <div x-show="showMonthPicker" 
                     x-cloak
                     @click.away="showMonthPicker = false"
                     class="absolute top-full left-0 mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-4 z-50 min-w-[320px]">
                    
                    <!-- Year Selector -->
                    <div class="flex items-center justify-between mb-4">
                        <button @click="pickerYear--; updatePickerDisplay()" 
                                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chevron-left text-gray-600 dark:text-gray-400"></i>
                        </button>
                        <span class="text-lg font-bold text-gray-900 dark:text-white" x-text="pickerYear"></span>
                        <button @click="pickerYear++; updatePickerDisplay()" 
                                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chevron-right text-gray-600 dark:text-gray-400"></i>
                        </button>
                    </div>

                    <!-- Month Grid -->
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="(month, index) in months" :key="index">
                            <button @click="selectMonthYear(index, pickerYear)"
                                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all"
                                    :class="isSelectedMonth(index, pickerYear) 
                                        ? 'bg-blue-600 text-white' 
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    x-text="month"></button>
                        </template>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="goToCurrentMonth()" 
                                class="flex-1 px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                            Current Month
                        </button>
                        <button @click="showMonthPicker = false" 
                                class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Arrows -->
            <div class="flex items-center gap-2">
                <button @click="goToToday" 
                        class="px-6 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Today
                </button>
                <button @click="previousPeriod" 
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-chevron-left text-gray-600 dark:text-gray-400"></i>
                </button>
                <button @click="nextPeriod" 
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-chevron-right text-gray-600 dark:text-gray-400"></i>
                </button>
            </div>
        </div>

        <!-- View Options and Actions -->
        <div class="flex items-center gap-4">
            <!-- View Switcher -->
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button @click="view = 'day'; updateView()" 
                        :class="view === 'day' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                        class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-all">
                    Day
                </button>
                <button @click="view = 'week'; updateView()" 
                        :class="view === 'week' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                        class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-all">
                    Week
                </button>
                <button @click="view = 'month'; updateView()" 
                        :class="view === 'month' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                        class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-all">
                    Month
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar Body -->
    <div class="p-4">
        <!-- Week/Day View -->
        <div x-show="view === 'week' || view === 'day'" class="overflow-x-auto">
            <!-- Days Header -->
            <div class="grid gap-4 mb-4" :class="view === 'week' ? 'grid-cols-8' : 'grid-cols-2'">
                <!-- Time Column Header (Empty) -->
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 text-center">
                    <span x-show="view === 'week'">GMT+8</span>
                </div>
                
                <!-- Day Headers -->
                <template x-for="day in visibleDays" :key="day.date">
                    <div class="text-center">
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase" x-text="day.dayName"></div>
                        <div class="text-2xl font-bold mt-1" 
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
                        <div class="text-xs text-gray-500 dark:text-gray-400 -mt-2" x-text="hour"></div>
                    </template>
                </div>

                <!-- Event Columns -->
                <template x-for="day in visibleDays" :key="day.date">
                    <div class="relative border-l border-gray-200 dark:border-gray-700 min-h-[400px]">
                        <!-- Hour Lines -->
                        <template x-for="(hour, index) in timeSlots" :key="index">
                            <div class="absolute w-full border-t border-gray-100 dark:border-gray-700" 
                                 :style="`top: ${index * 40}px`"></div>
                        </template>

                        <!-- Events -->
                        <template x-for="event in getEventsForDay(day.date)" :key="event.id">
                            <div class="absolute left-1 right-1 rounded-lg p-2 cursor-pointer transition-all hover:shadow-lg"
                                 :style="`top: ${getEventTop(event)}px; height: ${event.height}px; background-color: ${event.color}20; border-left: 3px solid ${event.color}`"
                                 @click="openEventModal(event)">
                                <div class="text-xs font-semibold truncate" :style="`color: ${event.color}`" x-text="event.title"></div>
                                <div class="text-xs text-gray-600 dark:text-gray-300 mt-0.5" x-text="event.time"></div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <!-- Month View -->
        <div x-show="view === 'month'" class="grid grid-cols-7 gap-2">
            <!-- Day Names -->
            <template x-for="dayName in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']" :key="dayName">
                <div class="text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase py-2" x-text="dayName"></div>
            </template>

            <!-- Calendar Days -->
            <template x-for="day in monthDays" :key="day.date">
                <div class="min-h-[100px] p-2 border border-gray-200 dark:border-gray-700 rounded-lg"
                     :class="day.isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900'">
                    <div class="text-sm font-semibold mb-1"
                         :class="day.isToday ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white'"
                         x-text="day.dayNumber"></div>
                    
                    <!-- Events in Month View -->
                    <div class="space-y-1">
                        <template x-for="event in getEventsForDay(day.date).slice(0, 3)" :key="event.id">
                            <div class="text-xs p-1 rounded truncate cursor-pointer"
                                 :style="`background-color: ${event.color}20; color: ${event.color}`"
                                 @click="openEventModal(event)"
                                 x-text="event.title"></div>
                        </template>
                        <div x-show="getEventsForDay(day.date).length > 3" 
                             class="text-xs text-gray-500 dark:text-gray-400 cursor-pointer hover:underline"
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
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all"
             @click.away="showEventModal = false">
            <!-- Modal Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white" x-text="selectedEvent?.title"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="selectedEvent?.time"></p>
                </div>
                <button @click="showEventModal = false" 
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Event Color Indicator -->
            <div class="h-1 rounded-full mb-4" :style="`background-color: ${selectedEvent?.color}`"></div>

            <!-- Event Details -->
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <i class="fas fa-clock text-gray-400 mt-1"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Time</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300" x-text="selectedEvent?.time"></p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <i class="fas fa-calendar text-gray-400 mt-1"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Date</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300" x-text="formatDate(selectedEvent?.date)"></p>
                    </div>
                </div>

                <div x-show="selectedEvent?.description" class="flex items-start gap-3">
                    <i class="fas fa-align-left text-gray-400 mt-1"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Description</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300" x-text="selectedEvent?.description"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex gap-3 mt-6">
                <button @click="editEvent(selectedEvent)" 
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                    <i class="fas fa-edit mr-2"></i>Edit
                </button>
                <button @click="deleteEvent(selectedEvent)" 
                        class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium">
                    <i class="fas fa-trash mr-2"></i>Delete
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

        editEvent(event) {
            console.log('Edit event:', event);
            // Implement edit functionality
            this.showEventModal = false;
        },

        deleteEvent(event) {
            if (confirm('Are you sure you want to delete this event?')) {
                this.events = this.events.filter(e => e.id !== event.id);
                this.showEventModal = false;
            }
        }
    }
}
</script>

<style>
[x-cloak] {
    display: none !important;
}
</style>