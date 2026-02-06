@props([
    'events' => [],
    'initialDate' => null,
    'initialView' => 'week', // 'month', 'week', 'day'
    'timeFormat' => '12', // '12' or '24'
    'startHour' => 0,
    'endHour' => 24,
])

<div x-data="calendarScheduler({
    events: {{ json_encode($events) }},
    initialDate: '{{ $initialDate ?? date('Y-m-d') }}',
    initialView: '{{ $initialView }}',
    timeFormat: '{{ $timeFormat }}',
    startHour: {{ $startHour }},
    endHour: {{ $endHour }}
})" 
x-init="init()"
class="calendar-scheduler w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg">

    <!-- Header -->
    <div class="calendar-header flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <!-- Left: Month/Year and Navigation -->
        <div class="flex items-center gap-4">
            <h2 class="text-base font-medium text-gray-900 dark:text-gray-100" x-text="currentMonthYear"></h2>
            
            <!-- Navigation Arrows -->
            <div class="flex items-center gap-2">
                <button @click="previousPeriod()" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                
                <button @click="goToToday()" 
                    class="px-3 py-1 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Today
                </button>
                
                <button @click="nextPeriod()" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Right: View Switcher and Actions -->
        <div class="flex items-center gap-3">
            <!-- View Switcher -->
            <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button @click="view = 'month'" 
                    :class="view === 'month' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                    class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-all">
                    Month
                </button>
                <button @click="view = 'week'" 
                    :class="view === 'week' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                    class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-all">
                    Week
                </button>
                <button @click="view = 'day'" 
                    :class="view === 'day' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                    class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-all">
                    Day
                </button>
            </div>
            
            <!-- Filter Buttons -->
            <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Home">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </button>
            
            <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Priority">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </button>
            
            <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Deadline">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
            
            <!-- Settings/Options -->
            <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>
            
            <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar-body">
        <!-- Month View -->
        <div x-show="view === 'month'" class="month-view">
            <!-- Weekday Headers -->
            <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700">
                <template x-for="day in weekDays" :key="day">
                    <div class="p-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-400" x-text="day"></div>
                </template>
            </div>
            
            <!-- Calendar Days -->
            <div class="grid grid-cols-7 min-h-[600px]">
                <template x-for="(week, weekIndex) in monthDays" :key="weekIndex">
                    <template x-for="(day, dayIndex) in week" :key="dayIndex">
                        <div @click="selectDate(day.date)" 
                            :class="{
                                'bg-gray-50 dark:bg-gray-900': !day.isCurrentMonth,
                                'bg-blue-50 dark:bg-blue-900/20': day.isToday,
                                'ring-2 ring-blue-500': day.isSelected
                            }"
                            class="border-r border-b border-gray-200 dark:border-gray-700 p-2 min-h-[100px] hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <span :class="{'font-bold text-blue-600 dark:text-blue-400': day.isToday, 'text-gray-400': !day.isCurrentMonth}"
                                    class="text-sm" x-text="day.day"></span>
                            </div>
                            
                            <!-- Events for this day -->
                            <div class="space-y-1">
                                <template x-for="event in getEventsForDate(day.date)" :key="event.id">
                                    <div @click.stop="openEventModal(event)"
                                        :style="'background-color: ' + event.color + '20; border-left: 3px solid ' + event.color"
                                        class="text-xs p-1 rounded cursor-pointer hover:shadow-md transition-shadow">
                                        <div class="font-medium truncate" x-text="event.title"></div>
                                        <div class="text-gray-600 dark:text-gray-400 truncate" x-text="event.time"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
        </div>

        <!-- Week View -->
        <div x-show="view === 'week'" class="week-view overflow-x-auto">
            <div class="min-w-[900px]">
                <!-- Day Headers -->
                <div class="grid grid-cols-8 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                    <div class="p-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">
                        GMT+8
                    </div>
                    <template x-for="day in weekDates" :key="day.date">
                        <div class="p-3 text-center border-l border-gray-200 dark:border-gray-700">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="day.dayName"></div>
                            <div :class="{'bg-blue-600 text-white': day.isToday}"
                                class="text-lg font-semibold rounded-full w-8 h-8 flex items-center justify-center mx-auto mt-1" 
                                x-text="day.dayNum"></div>
                        </div>
                    </template>
                </div>
                
                <!-- Time Grid -->
                <div class="relative">
                    <template x-for="hour in hours" :key="hour">
                        <div class="grid grid-cols-8 border-b border-gray-200 dark:border-gray-700" style="height: 60px;">
                            <!-- Time Label -->
                            <div class="p-2 text-xs text-gray-500 dark:text-gray-400 text-right pr-3" x-text="formatHour(hour)"></div>
                            
                            <!-- Day Columns -->
                            <template x-for="day in weekDates" :key="day.date + '-' + hour">
                                <div @click="createEvent(day.date, hour)" 
                                    class="border-l border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 cursor-pointer transition-colors relative">
                                    <!-- Events will be positioned absolutely here -->
                                    <template x-for="event in getEventsForDateHour(day.date, hour)" :key="event.id">
                                        <div @click.stop="openEventModal(event)"
                                            :style="'background-color: ' + event.color + '; top: ' + event.position + 'px; height: ' + event.height + 'px'"
                                            class="absolute left-1 right-1 rounded px-2 py-1 text-white text-xs font-medium shadow-md hover:shadow-lg transition-shadow cursor-pointer z-10">
                                            <div class="font-semibold truncate" x-text="event.title"></div>
                                            <div class="text-xs opacity-90 truncate" x-text="event.time"></div>
                                            <div class="text-xs opacity-75 truncate" x-text="event.description"></div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Day View -->
        <div x-show="view === 'day'" class="day-view overflow-x-auto">
            <div class="min-w-[600px]">
                <!-- Day Header -->
                <div class="grid grid-cols-2 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                    <div class="p-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">
                        GMT+8
                    </div>
                    <div class="p-4 text-center border-l border-gray-200 dark:border-gray-700">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="selectedDayName"></div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1" x-text="selectedDayNum"></div>
                    </div>
                </div>
                
                <!-- Time Grid -->
                <div class="relative">
                    <template x-for="hour in hours" :key="hour">
                        <div class="grid grid-cols-2 border-b border-gray-200 dark:border-gray-700" style="height: 80px;">
                            <div class="p-2 text-xs text-gray-500 dark:text-gray-400 text-right pr-4" x-text="formatHour(hour)"></div>
                            <div @click="createEvent(selectedDate, hour)" 
                                class="border-l border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 cursor-pointer transition-colors relative">
                                <template x-for="event in getEventsForDateHour(selectedDate, hour)" :key="event.id">
                                    <div @click.stop="openEventModal(event)"
                                        :style="'background-color: ' + event.color + '; top: ' + event.position + 'px; height: ' + event.height + 'px'"
                                        class="absolute left-2 right-2 rounded-lg px-3 py-2 text-white shadow-lg hover:shadow-xl transition-shadow cursor-pointer z-10">
                                        <div class="font-semibold text-sm" x-text="event.title"></div>
                                        <div class="text-xs opacity-90 mt-1" x-text="event.time"></div>
                                        <div class="text-xs opacity-75 mt-1" x-text="event.description"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Modal -->
    <div x-show="showEventModal" 
         x-cloak
         @click.self="showEventModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div @click.stop 
             class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6 transform transition-all">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100" x-text="selectedEvent?.title || 'New Event'"></h3>
                <button @click="showEventModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
                    <p class="text-gray-900 dark:text-gray-100" x-text="selectedEvent?.time"></p>
                </div>
                
                <div x-show="selectedEvent?.description">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <p class="text-gray-900 dark:text-gray-100" x-text="selectedEvent?.description"></p>
                </div>
                
                <div class="flex gap-2 pt-4">
                    <button @click="editEvent()"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Edit Schedule
                    </button>
                    <button @click="showEventModal = false"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button @click="saveEvent()"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    .calendar-scheduler {
        font-family: system-ui, -apple-system, sans-serif;
    }
    
    .week-view, .day-view {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    
    /* Custom scrollbar */
    .week-view::-webkit-scrollbar,
    .day-view::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .week-view::-webkit-scrollbar-track,
    .day-view::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .dark .week-view::-webkit-scrollbar-track,
    .dark .day-view::-webkit-scrollbar-track {
        background: #374151;
    }
    
    .week-view::-webkit-scrollbar-thumb,
    .day-view::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    
    .week-view::-webkit-scrollbar-thumb:hover,
    .day-view::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<script>
function calendarScheduler(config) {
    return {
        events: config.events || [],
        view: config.initialView || 'week',
        currentDate: new Date(config.initialDate || new Date()),
        selectedDate: config.initialDate || new Date().toISOString().split('T')[0],
        showEventModal: false,
        selectedEvent: null,
        timeFormat: config.timeFormat || '12',
        startHour: config.startHour || 0,
        endHour: config.endHour || 24,
        
        weekDays: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
        
        init() {
            this.updateCalendar();
        },
        
        get currentMonthYear() {
            const options = { month: 'long', year: 'numeric' };
            return this.currentDate.toLocaleDateString('en-US', options);
        },
        
        get hours() {
            const hours = [];
            for (let i = this.startHour; i < this.endHour; i++) {
                hours.push(i);
            }
            return hours;
        },
        
        get monthDays() {
            const year = this.currentDate.getFullYear();
            const month = this.currentDate.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());
            
            const weeks = [];
            let currentWeek = [];
            const today = new Date().toISOString().split('T')[0];
            
            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(date.getDate() + i);
                
                currentWeek.push({
                    day: date.getDate(),
                    date: date.toISOString().split('T')[0],
                    isCurrentMonth: date.getMonth() === month,
                    isToday: date.toISOString().split('T')[0] === today,
                    isSelected: date.toISOString().split('T')[0] === this.selectedDate
                });
                
                if (currentWeek.length === 7) {
                    weeks.push(currentWeek);
                    currentWeek = [];
                }
            }
            
            return weeks;
        },
        
        get weekDates() {
            const dates = [];
            const startOfWeek = new Date(this.currentDate);
            startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());
            const today = new Date().toISOString().split('T')[0];
            
            for (let i = 0; i < 7; i++) {
                const date = new Date(startOfWeek);
                date.setDate(date.getDate() + i);
                dates.push({
                    date: date.toISOString().split('T')[0],
                    dayName: this.weekDays[i],
                    dayNum: date.getDate(),
                    isToday: date.toISOString().split('T')[0] === today
                });
            }
            
            return dates;
        },
        
        get selectedDayName() {
            const date = new Date(this.selectedDate);
            return date.toLocaleDateString('en-US', { weekday: 'long' });
        },
        
        get selectedDayNum() {
            const date = new Date(this.selectedDate);
            return date.getDate();
        },
        
        formatHour(hour) {
            if (this.timeFormat === '24') {
                return hour.toString().padStart(2, '0') + ':00';
            }
            const period = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 === 0 ? 12 : hour % 12;
            return displayHour + ':00 ' + period;
        },
        
        getEventsForDate(date) {
            return this.events.filter(event => event.date === date);
        },
        
        getEventsForDateHour(date, hour) {
            return this.events.filter(event => {
                if (event.date !== date) return false;
                const eventHour = parseInt(event.startTime.split(':')[0]);
                return eventHour === hour;
            });
        },
        
        selectDate(date) {
            this.selectedDate = date;
            this.currentDate = new Date(date);
        },
        
        previousPeriod() {
            const date = new Date(this.currentDate);
            if (this.view === 'month') {
                date.setMonth(date.getMonth() - 1);
            } else if (this.view === 'week') {
                date.setDate(date.getDate() - 7);
            } else {
                date.setDate(date.getDate() - 1);
            }
            this.currentDate = date;
            this.selectedDate = date.toISOString().split('T')[0];
        },
        
        nextPeriod() {
            const date = new Date(this.currentDate);
            if (this.view === 'month') {
                date.setMonth(date.getMonth() + 1);
            } else if (this.view === 'week') {
                date.setDate(date.getDate() + 7);
            } else {
                date.setDate(date.getDate() + 1);
            }
            this.currentDate = date;
            this.selectedDate = date.toISOString().split('T')[0];
        },
        
        goToToday() {
            this.currentDate = new Date();
            this.selectedDate = new Date().toISOString().split('T')[0];
        },
        
        createEvent(date, hour) {
            this.selectedEvent = {
                id: Date.now(),
                title: 'New Event',
                date: date,
                startTime: hour.toString().padStart(2, '0') + ':00',
                endTime: (hour + 1).toString().padStart(2, '0') + ':00',
                time: this.formatHour(hour) + ' - ' + this.formatHour(hour + 1),
                description: '',
                color: '#3B82F6'
            };
            this.showEventModal = true;
        },
        
        openEventModal(event) {
            this.selectedEvent = event;
            this.showEventModal = true;
        },
        
        editEvent() {
            console.log('Edit event:', this.selectedEvent);
            // Implement edit functionality
        },
        
        saveEvent() {
            if (this.selectedEvent) {
                const existingIndex = this.events.findIndex(e => e.id === this.selectedEvent.id);
                if (existingIndex >= 0) {
                    this.events[existingIndex] = this.selectedEvent;
                } else {
                    this.events.push(this.selectedEvent);
                }
            }
            this.showEventModal = false;
        },
        
        updateCalendar() {
            // Refresh calendar data
        }
    }
}
</script>