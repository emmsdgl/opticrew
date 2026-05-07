@php
    /**
     * @var array  $locationTypes
     * @var int    $totalLocations
     * @var string $companyAddress
     * @var string $companyCityState
     * @var string $companyStreetAddress
     * @var array  $locationsByType
     * @var int    $typesAddedLastMonth
     * @var int    $locationsAddedLastMonth
     * @var int    $minimumBookingNoticeDays
     * @var array  $checklists
     * @var array  $predefinedCategories
     */
@endphp
<x-layouts.general-manager :title="'Schedule'">
    <div id="schedule-bootstrap-data" data-checklists="{{ json_encode($checklists ?? []) }}" hidden></div>
    <div class="flex flex-col gap-8 p-8 w-full" x-data="scheduleManager()" x-init="init()" @keydown.escape.window="showCreateModal = false; showTaskModal = false; showLocationPicker = false; showEmployeePicker = false; clShowAddModal = false; clShowEditModal = false">

        {{-- ============================================================ --}}
        {{-- STAT CARDS (admin reports style)                              --}}
        {{-- ============================================================ --}}
        @php $typeCount = count($locationTypes ?? []); @endphp
        <div class="grid grid-cols-2 md:grid-cols-3 gap-px bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
            {{-- Total Types of Locations --}}
            <div class="relative bg-white dark:bg-slate-900 px-6 py-5" x-data="{ showTip: false }">
                <div class="flex items-center gap-2 mb-2 ml-3">
                    <i class="fa-solid fa-layer-group" style="color: #3b82f6"></i>
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Total Types of Locations</p>
                </div>
                <p @mouseenter="showTip = true" @mouseleave="showTip = false"
                   class="text-3xl font-bold text-gray-900 dark:text-white ml-3 cursor-help">{{ $typeCount }}</p>
                @if($typesAddedLastMonth > 0)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">
                        <span class="text-blue-500 font-semibold">+{{ $typesAddedLastMonth }}</span> added last month
                    </p>
                @else
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">No new types last month</p>
                @endif
                {{-- Tooltip --}}
                <div x-show="showTip" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute left-3 top-full mt-2 z-50 w-56 p-3 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-300">
                    <div class="flex items-center gap-1.5 mb-2">
                        <i class="fa-solid fa-layer-group text-[10px] text-blue-500"></i>
                        <span class="font-bold text-gray-900 dark:text-white">Location Types</span>
                    </div>
                    <div class="space-y-1.5">
                        @foreach($locationTypes ?? [] as $type => $count)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    @switch($type)
                                        @case('Small') <i class="fa-solid fa-house text-blue-500 text-[10px]"></i> @break
                                        @case('Medium') <i class="fa-solid fa-house-chimney text-green-500 text-[10px]"></i> @break
                                        @case('Big') <i class="fa-solid fa-building text-purple-500 text-[10px]"></i> @break
                                        @case('Queen') <i class="fa-solid fa-crown text-yellow-500 text-[10px]"></i> @break
                                        @case('Igloo') <i class="fa-solid fa-igloo text-cyan-500 text-[10px]"></i> @break
                                        @default <i class="fa-solid fa-location-dot text-gray-500 text-[10px]"></i>
                                    @endswitch
                                    <span>{{ $type }}</span>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Total Locations --}}
            <div class="relative bg-white dark:bg-slate-900 px-6 py-5" x-data="{ showTip: false }">
                <div class="flex items-center gap-2 mb-2 ml-3">
                    <i class="fa-solid fa-location-dot" style="color: #8b5cf6"></i>
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Total Locations</p>
                </div>
                <p @mouseenter="showTip = true" @mouseleave="showTip = false"
                   class="text-3xl font-bold text-gray-900 dark:text-white ml-3 cursor-help">{{ $totalLocations }}</p>
                @if($locationsAddedLastMonth > 0)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">
                        <span class="text-purple-500 font-semibold">+{{ $locationsAddedLastMonth }}</span> added last month
                    </p>
                @else
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">No new locations last month</p>
                @endif
                {{-- Tooltip --}}
                <div x-show="showTip" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute left-3 top-full mt-2 z-50 w-64 p-3 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-300 max-h-64 overflow-y-auto">
                    <div class="flex items-center gap-1.5 mb-2">
                        <i class="fa-solid fa-location-dot text-[10px] text-purple-500"></i>
                        <span class="font-bold text-gray-900 dark:text-white">All Locations</span>
                    </div>
                    <div class="space-y-3">
                        @foreach($locationsByType ?? [] as $type => $names)
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white mb-1 flex items-center gap-1">
                                    @switch($type)
                                        @case('Small') <i class="fa-solid fa-house text-blue-500 text-[10px]"></i> @break
                                        @case('Medium') <i class="fa-solid fa-house-chimney text-green-500 text-[10px]"></i> @break
                                        @case('Big') <i class="fa-solid fa-building text-purple-500 text-[10px]"></i> @break
                                        @case('Queen') <i class="fa-solid fa-crown text-yellow-500 text-[10px]"></i> @break
                                        @case('Igloo') <i class="fa-solid fa-igloo text-cyan-500 text-[10px]"></i> @break
                                        @default <i class="fa-solid fa-location-dot text-gray-500 text-[10px]"></i>
                                    @endswitch
                                    {{ $type }} ({{ count($names) }})
                                </p>
                                <div class="space-y-0.5 pl-4">
                                    @foreach($names as $name)
                                        <p class="text-gray-600 dark:text-gray-400">{{ $name }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Company Location (geographical address) --}}
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <div class="flex items-center gap-2 mb-2 ml-3">
                    <i class="fa-solid fa-map-location-dot" style="color: #10b981"></i>
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Company Location</p>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white ml-3">{{ $companyCityState }}</p>
                @if($companyStreetAddress)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-3">{{ $companyStreetAddress }}</p>
                @endif
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- CALENDAR (mirrors appointment-calendar visual style)         --}}
        {{-- ============================================================ --}}
        <div class="w-full bg-white/30 dark:bg-transparent rounded-2xl">
            <!-- Calendar Header -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between px-8 py-4 border-b border-gray-200 dark:border-gray-700 gap-4">
                <!-- Month/Year Display with Picker -->
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <button @click="showMonthPicker = !showMonthPicker"
                                class="text-base lg:text-base font-bold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 py-2.5 rounded-lg transition-colors flex items-center gap-2 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <span x-text="currentMonthYear"></span>
                            <i class="fas fa-chevron-down text-sm transition-transform" :class="showMonthPicker ? 'rotate-180' : ''"></i>
                        </button>

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
                                <button @click="pickerYear--"
                                        class="p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <i class="fas fa-chevron-left text-gray-600 dark:text-gray-400 text-sm"></i>
                                </button>
                                <span class="text-lg font-black text-gray-900 dark:text-white" x-text="pickerYear"></span>
                                <button @click="pickerYear++"
                                        class="p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <i class="fas fa-chevron-right text-gray-600 dark:text-gray-400 text-sm"></i>
                                </button>
                            </div>

                            <!-- Month Grid -->
                            <div class="grid grid-cols-3 gap-2.5">
                                <template x-for="(month, index) in calendarMonths" :key="index">
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
                        <button @click="goToToday()"
                                class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                            Today
                        </button>
                        <button @click="previousPeriod()"
                                class="p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chevron-left text-gray-600 dark:text-gray-400 text-sm"></i>
                        </button>
                        <button @click="nextPeriod()"
                                class="p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chevron-right text-gray-600 dark:text-gray-400 text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Right side: view switcher + New Task button -->
                <div class="flex items-center gap-3 py-4 flex-wrap">
                    <!-- View Switcher -->
                    <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg py-1.5 px-1">
                        <button @click="calendarView = 'day'; updateCalendarView()"
                                :class="calendarView === 'day' ? 'bg-white dark:bg-gray-600 shadow-md' : ''"
                                class="px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 rounded-md transition-all">
                            Day
                        </button>
                        <button @click="calendarView = 'week'; updateCalendarView()"
                                :class="calendarView === 'week' ? 'bg-white dark:bg-gray-600 shadow-md' : ''"
                                class="px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 rounded-md transition-all">
                            Week
                        </button>
                        <button @click="calendarView = 'month'; updateCalendarView()"
                                :class="calendarView === 'month' ? 'bg-white dark:bg-gray-600 shadow-md' : ''"
                                class="px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 rounded-md transition-all">
                            Month
                        </button>
                    </div>

                    <button @click="openCreateModal()"
                            class="inline-flex items-center gap-2 py-2.5 px-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-plus"></i>
                        New Task
                    </button>
                </div>
            </div>

            <!-- Calendar Body -->
            <div class="p-6">

                <!-- Loading overlay -->
                <div x-show="calendarLoading" x-cloak class="flex justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>

                <!-- ========== Week / Day View ========== -->
                <div x-show="!calendarLoading && (calendarView === 'week' || calendarView === 'day')" x-cloak class="overflow-x-auto">
                    <!-- Days Header -->
                    <div class="grid gap-4 mb-6" :class="calendarView === 'week' ? 'grid-cols-8' : 'grid-cols-2'">
                        <!-- Time Column Header -->
                        <div class="text-xs font-bold text-gray-500 dark:text-gray-400 text-center">
                            <span x-show="calendarView === 'week'">GMT+8</span>
                        </div>
                        <!-- Day Headers -->
                        <template x-for="day in visibleDays" :key="day.date">
                            <div class="text-center p-2 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                 :class="day.isToday ? 'bg-blue-50 dark:bg-blue-900/20' : ''"
                                 @click="calendarView = 'day'; calendarDate = new Date(day.date + 'T00:00:00'); updateCalendarView();">
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide" x-text="day.dayName"></div>
                                <div class="text-2xl font-black mt-1.5"
                                     :class="day.isToday ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white'"
                                     x-text="day.dayNumber"></div>
                                <!-- Task count indicator -->
                                <div class="flex justify-center gap-1 mt-1" x-show="getTasksForDay(day.date).length > 0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    <span class="text-[10px] text-gray-400" x-text="getTasksForDay(day.date).length"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Time Grid (scrollable) -->
                    <div x-ref="timeGrid" class="overflow-y-auto max-h-[600px]">
                        <div class="grid gap-4" :class="calendarView === 'week' ? 'grid-cols-8' : 'grid-cols-2'">
                            <!-- Time Labels Column -->
                            <div class="space-y-10">
                                <template x-for="hour in timeSlots" :key="hour">
                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 -mt-2" x-text="hour"></div>
                                </template>
                            </div>

                            <!-- Event Columns -->
                            <template x-for="day in visibleDays" :key="day.date">
                                <div class="relative border-l border-gray-200 dark:border-gray-700 min-h-[960px]">
                                    <!-- Hour Lines -->
                                    <template x-for="(hour, index) in timeSlots" :key="index">
                                        <div class="absolute w-full border-t border-gray-100 dark:border-gray-800"
                                             :style="`top: ${index * 40}px`"></div>
                                    </template>

                                    <!-- Tasks rendered as event blocks -->
                                    <template x-for="task in getTasksForDay(day.date)" :key="task.id">
                                        <div class="absolute left-1 right-1 rounded-lg p-2.5 cursor-pointer transition-all hover:shadow-xl hover:scale-[1.02]"
                                             :style="`top: ${getTaskTop(task)}px; height: ${getTaskHeight(task)}px; background-color: ${getTaskColor(task)}15; border-left: 4px solid ${getTaskColor(task)}`"
                                             @click="openTaskDetails(task)">
                                            <div class="text-xs font-bold truncate" :style="`color: ${getTaskColor(task)}`" x-text="task.location_name"></div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1 font-medium" x-text="task.scheduled_time + (task.duration ? ' · ' + task.duration + 'min' : '')"></div>
                                            <div x-show="task.arrival_status && task.arrival_status !== 0 && task.arrival_status !== '0'"
                                                 class="mt-1">
                                                <span class="inline-flex items-center px-1 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">ARRIVAL</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- ========== Month View ========== -->
                <div x-show="!calendarLoading && calendarView === 'month'" x-cloak class="grid grid-cols-7 gap-3">
                    <!-- Day Names -->
                    <template x-for="dayName in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']" :key="dayName">
                        <div class="text-center text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wide py-3" x-text="dayName"></div>
                    </template>

                    <!-- Calendar Days -->
                    <template x-for="day in monthDays" :key="day.date">
                        <div class="group/cell relative min-h-[110px] p-3 border border-gray-200 dark:border-gray-700 rounded-lg transition-all hover:shadow-md cursor-pointer"
                             :class="day.isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900 opacity-60'"
                             @click="calendarView = 'day'; calendarDate = new Date(day.date + 'T00:00:00'); updateCalendarView();">
                            <div class="text-sm font-black mb-2"
                                 :class="day.isToday ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 w-7 h-7 rounded-full flex items-center justify-center' : 'text-gray-900 dark:text-white'"
                                 x-text="day.dayNumber"></div>

                            <!-- Tasks in Month View -->
                            <div class="space-y-1.5">
                                <template x-for="task in getTasksForDay(day.date).slice(0, 3)" :key="task.id">
                                    <div class="text-xs p-1.5 rounded-md truncate cursor-pointer font-semibold transition-all hover:shadow-sm"
                                         :style="`background-color: ${getTaskColor(task)}15; color: ${getTaskColor(task)}; border-left: 3px solid ${getTaskColor(task)}`"
                                         @click.stop="openTaskDetails(task)"
                                         x-text="task.location_name"></div>
                                </template>
                                <div x-show="getTasksForDay(day.date).length > 3"
                                     class="text-xs text-gray-500 dark:text-gray-400 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition-colors pl-1"
                                     x-text="`+${getTasksForDay(day.date).length - 3} more`"></div>
                            </div>

                            <!-- Add Task Button (shows on hover when no tasks) -->
                            <button x-show="getTasksForDay(day.date).length === 0 && day.isCurrentMonth"
                                    @click.stop="selectedDate = day.date; openCreateModal()"
                                    class="absolute bottom-1.5 right-1.5 w-5 h-5 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center opacity-0 group-hover/cell:opacity-100 transition-all duration-200 shadow-md hover:scale-110"
                                    title="Create task">
                                <i class="fa-solid fa-plus" style="font-size: 0.5rem;"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- TASK CHECKLIST TEMPLATES                   --}}
        {{-- ========================================== --}}
        <div class="flex flex-col gap-4 w-full">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Task Checklist Templates</h2>
                <button @click="clOpenAddTemplate()"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Add Template
                </button>
            </div>

            <!-- Templates Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <template x-for="template in clTemplates" :key="template.id">
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg flex border-l-4 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors cursor-pointer h-16 group shadow-sm"
                         :style="'border-left-color: ' + template.color"
                         x-data="{ showTip: false }"
                         @mouseenter="showTip = true" @mouseleave="showTip = false">

                        <!-- Tooltip -->
                        <div x-show="showTip" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute bottom-full left-0 mb-2 w-56 bg-gray-900 border border-gray-700 rounded-lg shadow-xl p-3 z-50 pointer-events-none">
                            <p class="text-xs font-semibold text-white mb-1.5" x-text="template.name"></p>
                            <template x-if="template.reminders">
                                <div class="mb-2 pb-1.5 border-b border-gray-700">
                                    <p class="text-[10px] text-amber-400 font-semibold mb-0.5"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Reminders</p>
                                    <p class="text-[11px] text-gray-400 line-clamp-2" x-text="template.reminders"></p>
                                </div>
                            </template>
                            <template x-if="(template.categories || []).length > 0">
                                <ul class="space-y-1">
                                    <template x-for="(cat, i) in (template.categories || []).slice(0, 6)" :key="cat.id">
                                        <li class="flex items-start gap-1.5 text-[11px] text-gray-400">
                                            <i class="fa-regular fa-folder text-[9px] mt-0.5 text-gray-500 flex-shrink-0"></i>
                                            <span class="line-clamp-1" x-text="cat.name + ' (' + (cat.items || []).length + ')'"></span>
                                        </li>
                                    </template>
                                    <template x-if="(template.categories || []).length > 6">
                                        <li class="text-[11px] text-gray-500 italic" x-text="'+ ' + ((template.categories || []).length - 6) + ' more...'"></li>
                                    </template>
                                </ul>
                            </template>
                            <template x-if="(template.categories || []).length === 0">
                                <p class="text-[11px] text-gray-500 italic">No categories yet</p>
                            </template>
                            <div class="absolute -bottom-1 left-6 w-2 h-2 bg-gray-900 border-r border-b border-gray-700 transform rotate-45"></div>
                        </div>

                        <!-- Color Badge with Initials -->
                        <div class="flex items-center justify-center w-14 h-full"
                             :style="'background-color: ' + template.color">
                            <span class="text-white font-bold text-sm" x-text="template.initials"></span>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 flex items-center justify-between px-4 py-3">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1" x-text="template.name"></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="template.itemCount + ' Items'"></p>
                            </div>

                            <!-- 3-dot Menu -->
                            <div class="relative" x-data="{ menuOpen: false }">
                                <button @click.stop="menuOpen = !menuOpen"
                                        class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-white rounded transition-colors">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div x-show="menuOpen"
                                     @click.away="menuOpen = false"
                                     x-transition
                                     class="absolute right-0 top-full mt-1 w-36 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-50">
                                    <button @click="clEditTemplate(template); menuOpen = false"
                                            class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 flex items-center gap-2">
                                        <i class="fa-solid fa-pen text-xs"></i> Edit
                                    </button>
                                    <button @click="clDeleteTemplate(template.id); menuOpen = false"
                                            class="w-full px-4 py-2 text-left text-sm text-red-500 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600 flex items-center gap-2">
                                        <i class="fa-solid fa-trash text-xs"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div x-show="clTemplates.length === 0"
                     class="col-span-full p-8 text-center text-gray-500 dark:text-gray-400 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    <i class="fa-solid fa-clipboard-list text-3xl mb-3 opacity-50"></i>
                    <p class="font-semibold">No checklist templates</p>
                    <p class="text-sm">Create templates to standardize task checklists.</p>
                </div>
            </div>

            <!-- Add Template Modal -->
            <div x-show="clShowAddModal"
                 x-transition
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                 @click.self="clCloseModal()" style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add New Template</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Template Name</label>
                            <input type="text" x-model="clFormData.name" placeholder="e.g., Daily Room Cleaning"
                                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Important Reminders (Optional)</label>
                            <textarea x-model="clFormData.reminders" rows="3" placeholder="Add any important reminders..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none text-sm"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="clCloseModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">Cancel</button>
                        <button @click="clCreateTemplate()" :disabled="clSubmitting || !clFormData.name.trim()"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-text="clSubmitting ? 'Creating...' : 'Create'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Template Modal -->
            <div x-show="clShowEditModal"
                 x-transition
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                 @click.self="clCloseModal()" style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Template</h3>
                        <button @click="clCloseModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fa-solid fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-5">
                        <!-- Template Name + Reminders -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Template Name</label>
                                <input type="text" x-model="clFormData.name" @blur="clUpdateChecklistDetails()" placeholder="e.g., Daily Room Cleaning"
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Important Reminders</label>
                                <textarea x-model="clFormData.reminders" @blur="clUpdateChecklistDetails()" rows="3" placeholder="Add any important reminders..."
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                            </div>
                        </div>

                        <!-- Categories Header -->
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Categories</h4>
                            <button @click="clOpenAddCategoryModal()"
                                    class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 font-medium">
                                <i class="fa-solid fa-folder-plus mr-1"></i> Add Category
                            </button>
                        </div>

                        <!-- Categories list -->
                        <div class="space-y-3">
                            <template x-for="category in clFormData.categories" :key="category.id">
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    <!-- Category header -->
                                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/40 flex items-center justify-between cursor-pointer"
                                         @click="clToggleCategory(category.id)">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-chevron-right text-gray-400 text-xs transition-transform duration-200"
                                               :class="{ 'rotate-90': clExpandedCategories.includes(category.id) }"></i>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="category.name"></span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded-full"
                                                  x-text="(category.items || []).length + ' items'"></span>
                                        </div>
                                        <div class="flex items-center gap-1" @click.stop>
                                            <button @click="clOpenEditCategory(category)"
                                                    class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors text-gray-500 hover:text-blue-600">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </button>
                                            <button @click="clConfirmDeleteCategory(category)"
                                                    class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors text-gray-500 hover:text-red-600">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                            <button @click="clOpenAddItem(category)"
                                                    class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors text-gray-500 hover:text-green-600">
                                                <i class="fa-solid fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Items list (expandable) -->
                                    <div x-show="clExpandedCategories.includes(category.id)" x-collapse>
                                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                            <template x-for="item in (category.items || [])" :key="item.id">
                                                <div class="px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-7 h-7 rounded-md bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                                            <i class="fa-solid fa-check text-blue-600 dark:text-blue-400 text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="item.name"></p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'Qty: ' + (item.quantity || '1')"></p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <button @click="clOpenEditItem(category, item)"
                                                                class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors text-gray-500 hover:text-blue-600">
                                                            <i class="fa-solid fa-pen text-xs"></i>
                                                        </button>
                                                        <button @click="clConfirmDeleteItem(category, item)"
                                                                class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors text-gray-500 hover:text-red-600">
                                                            <i class="fa-solid fa-trash text-xs"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="!(category.items || []).length">
                                                <div class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    No items in this category.
                                                    <button @click="clOpenAddItem(category)" class="text-blue-600 dark:text-blue-400 hover:underline">Add one</button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Empty state -->
                            <div x-show="!(clFormData.categories || []).length"
                                 class="border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                No categories yet. Add your first category to start building this template.
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <button @click="clCloseModal()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">Done</button>
                    </div>
                </div>
            </div>

            <!-- Add Category Sub-Modal -->
            <div x-show="clShowAddCategoryModal" x-transition class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
                 @click.self="clShowAddCategoryModal = false" style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Category</h3>
                        <button @click="clShowAddCategoryModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"><i class="fa-solid fa-xmark text-gray-500"></i></button>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quick Select</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($predefinedCategories ?? [] as $cat)
                                    <button type="button" @click="clCategoryForm.name = '{{ $cat }}'"
                                            :class="{ 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-blue-300 dark:border-blue-700': clCategoryForm.name === '{{ $cat }}' }"
                                            class="px-3 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors">
                                        {{ $cat }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Or Custom Name</label>
                            <input type="text" x-model="clCategoryForm.name" placeholder="Category name"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="clShowAddCategoryModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                        <button @click="clAddCategory()" :disabled="clSubmitting || !clCategoryForm.name"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50">
                            <span x-text="clSubmitting ? 'Adding...' : 'Add Category'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Category Sub-Modal -->
            <div x-show="clShowEditCategoryModal" x-transition class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
                 @click.self="clShowEditCategoryModal = false" style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Category</h3>
                        <button @click="clShowEditCategoryModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"><i class="fa-solid fa-xmark text-gray-500"></i></button>
                    </div>
                    <div class="p-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category Name</label>
                        <input type="text" x-model="clEditCategoryForm.name"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="clShowEditCategoryModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                        <button @click="clUpdateCategory()" :disabled="clSubmitting || !clEditCategoryForm.name"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50">Save</button>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Item Sub-Modal -->
            <div x-show="clShowItemModal" x-transition class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
                 @click.self="clShowItemModal = false" style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="clItemForm.id ? 'Edit Item' : 'Add Item'"></h3>
                        <button @click="clShowItemModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"><i class="fa-solid fa-xmark text-gray-500"></i></button>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Item Name</label>
                            <input type="text" x-model="clItemForm.name" placeholder="e.g. Mop floors"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                            <input type="text" x-model="clItemForm.quantity" placeholder="1"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="clShowItemModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                        <button @click="clSaveItem()" :disabled="clSubmitting || !clItemForm.name"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50">
                            <span x-text="clSubmitting ? 'Saving...' : (clItemForm.id ? 'Update' : 'Add Item')"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Sub-Modal -->
            <div x-show="clShowDeleteModal" x-transition class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
                 @click.self="clShowDeleteModal = false" style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm" @click.stop>
                    <div class="p-6 text-center">
                        <div class="w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-trash text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6" x-text="clDeleteMessage"></p>
                        <div class="flex gap-3 justify-center">
                            <button @click="clShowDeleteModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Cancel</button>
                            <button @click="clExecuteDelete()" :disabled="clSubmitting"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium disabled:opacity-50">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- ALL TASKS LIST (appointment-list style)    --}}
        {{-- ========================================== --}}
        <div class="bg-white dark:bg-transparent rounded-xl px-4">
            <div class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">All Tasks</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="allTasksList.length + ' task' + (allTasksList.length !== 1 ? 's' : '') + ' total'"></p>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    {{-- Search Bar --}}
                    <div class="relative">
                        <input type="search" x-model="taskListSearch" @input="taskListPage = 1" placeholder="Search tasks..."
                            class="w-full md:w-56 px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 dark:text-white">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    {{-- Sort by Status Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-filter text-xs"></i>
                            <span class="text-xs" x-text="taskListStatusFilter === 'all' ? 'Filter by Status' : taskListStatusFilter"></span>
                            <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="taskListStatusFilter = 'all'; taskListPage = 1; open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    :class="taskListStatusFilter === 'all' ? 'font-semibold bg-gray-50 dark:bg-gray-700/50' : ''">
                                    All Statuses
                                </button>
                                <template x-for="status in ['Scheduled', 'Pending', 'In Progress', 'On Hold', 'Completed', 'Cancelled']" :key="status">
                                    <button type="button" @click="taskListStatusFilter = status; taskListPage = 1; open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
                                        :class="taskListStatusFilter === status ? 'font-semibold bg-gray-50 dark:bg-gray-700/50' : ''">
                                        <div class="w-2 h-2 rounded-full"
                                             :class="{ 'bg-green-500': status === 'Completed', 'bg-blue-500': status === 'In Progress', 'bg-yellow-500': status === 'On Hold', 'bg-red-500': status === 'Cancelled', 'bg-gray-400': status === 'Scheduled' || status === 'Pending' }"></div>
                                        <span x-text="status"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Header -->
            <div x-show="!calendarLoading && paginatedTaskList.length > 0"
                 class="hidden md:grid md:grid-cols-[1fr_1.2fr_1.2fr_1fr_1fr_1fr] gap-4 px-6 py-4 border-b border-gray-200 dark:border-gray-700 w-full">
                <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Status</div>
                <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Location</div>
                <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Date & Time</div>
                <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Urgency</div>
                <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Team</div>
                <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Action</div>
            </div>

            <!-- Loading -->
            <div x-show="calendarLoading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>

            <!-- Table Body -->
            <div x-show="!calendarLoading && paginatedTaskList.length > 0"
                 class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="task in paginatedTaskList" :key="task.id + '-' + task.date">
                    <div class="grid grid-cols-1 md:grid-cols-[1fr_1.2fr_1.2fr_1fr_1fr_1fr] gap-4 px-6 py-4
                                hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors w-full cursor-pointer"
                         @click="openTaskDetails(task)">
                        <!-- Status Badge -->
                        <div class="flex items-center gap-2">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400">Status:</span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded border"
                                  :class="{ 'bg-green-50 border-green-500/40 text-green-700 dark:bg-green-950/40 dark:border-green-400/40 dark:text-green-300': task.status === 'Completed', 'bg-blue-50 border-blue-500/40 text-blue-700 dark:bg-blue-950/40 dark:border-blue-400/40 dark:text-blue-300': task.status === 'In Progress', 'bg-yellow-50 border-yellow-500/40 text-yellow-700 dark:bg-yellow-950/40 dark:border-yellow-400/40 dark:text-yellow-300': task.status === 'On Hold' || task.status === 'Pending', 'bg-red-50 border-red-500/40 text-red-700 dark:bg-red-950/40 dark:border-red-400/40 dark:text-red-300': task.status === 'Cancelled', 'bg-gray-50 border-gray-400/40 text-gray-600 dark:bg-gray-800/40 dark:border-gray-500/40 dark:text-gray-400': task.status === 'Scheduled' || !task.status }"
                                  x-text="task.status || 'Scheduled'"></span>
                        </div>
                        <!-- Location -->
                        <div class="flex flex-col">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Location:</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="task.location_name"></span>
                            <span x-show="task.location_type" class="text-xs text-gray-500 dark:text-gray-400" x-text="task.location_type"></span>
                        </div>
                        <!-- Date & Time -->
                        <div class="flex flex-col">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Date & Time:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="formatTaskDate(task.date)"></span>
                            <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                <i class="fa-regular fa-clock text-xs"></i>
                                <span x-text="task.scheduled_time || 'No time set'"></span>
                                <template x-if="task.duration">
                                    <span class="ml-1">(<span x-text="task.duration"></span> min)</span>
                                </template>
                            </div>
                        </div>
                        <!-- Urgency Badges -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400">Urgency:</span>
                            <span x-show="task.arrival_status && task.arrival_status !== 0 && task.arrival_status !== '0'"
                                  class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                <i class="fa-solid fa-plane-arrival mr-1 text-[8px]"></i> Arrival
                            </span>
                            <span x-show="task.cabin_status === 'arrival'"
                                  class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                <i class="fa-solid fa-triangle-exclamation mr-1 text-[8px]"></i> Emergency
                            </span>
                            <span x-show="(!task.arrival_status || task.arrival_status === 0 || task.arrival_status === '0') && task.cabin_status !== 'arrival'"
                                  class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                Normal
                            </span>
                        </div>
                        <!-- Team -->
                        <div class="flex flex-col">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Team:</span>
                            <div class="flex items-center gap-1.5 text-sm font-medium text-gray-900 dark:text-gray-100">
                                <i class="fa-solid fa-user-group text-xs text-gray-400"></i>
                                <span x-text="(task.employee_count || 0) + ' assigned'"></span>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="task.rate_type || 'Normal'"></span>
                        </div>
                        <!-- Action Button -->
                        <div class="flex items-center justify-start md:justify-center">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mr-2">Action:</span>
                            <button @click.stop="openTaskDetails(task)"
                                    class="px-6 py-3 text-xs font-medium text-gray-700 dark:text-white hover:text-blue-500 hover:bg-blue-500/10 dark:hover:text-blue-500 rounded-full transition-colors duration-200">
                                View Details
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Pagination -->
            <div x-show="!calendarLoading && filteredTaskList.length > taskListPerPage"
                 class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Showing <span x-text="((taskListPage - 1) * taskListPerPage) + 1"></span>-<span x-text="Math.min(taskListPage * taskListPerPage, filteredTaskList.length)"></span> of <span x-text="filteredTaskList.length"></span>
                </p>
                <div class="flex items-center gap-1">
                    <button @click="taskListPage = Math.max(1, taskListPage - 1)" :disabled="taskListPage === 1"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        Previous
                    </button>
                    <template x-for="page in visibleTaskListPages" :key="page">
                        <button @click="taskListPage = page"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
                                :class="taskListPage === page ? 'bg-blue-600 text-white' : 'border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                x-text="page"></button>
                    </template>
                    <button @click="taskListPage = Math.min(taskListTotalPages, taskListPage + 1)" :disabled="taskListPage >= taskListTotalPages"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        Next
                    </button>
                </div>
            </div>

            <!-- Empty State -->
            <div x-show="!calendarLoading && filteredTaskList.length === 0"
                 class="flex flex-col items-center justify-center py-12 px-6 text-center">
                <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-calendar-xmark text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1"
                    x-text="taskListStatusFilter !== 'all' ? 'No ' + taskListStatusFilter.toLowerCase() + ' tasks found' : (taskListSearch ? 'No matching tasks found' : 'No tasks found')"></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mb-2">
                    There are no tasks matching the current filter for the visible date range.
                </p>
                <button @click="taskListStatusFilter = 'all'; taskListSearch = ''"
                        x-show="taskListStatusFilter !== 'all' || taskListSearch"
                        class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                    Clear filters
                </button>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- CREATE TASK MODAL (matches mobile flow)    --}}
        {{-- ========================================== --}}
        <div x-show="showCreateModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showCreateModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div x-show="showCreateModal" x-transition class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New Task</h3>
                        <button @click="showCreateModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"><i class="fa-solid fa-xmark text-gray-500"></i></button>
                    </div>

                    <div class="p-4 md:p-5 space-y-5">

                        <!-- 1. Service Type (Primary Selector) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Type *</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button @click="setCabinStatus('departure')"
                                        :class="taskForm.cabin_status === 'departure' ? 'bg-blue-600 text-white ring-2 ring-blue-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                        class="flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-sm font-medium transition-all">
                                    <span class="text-lg">&#x1F9F3;</span> Departure
                                </button>
                                <button @click="setCabinStatus('daily_clean')"
                                        :class="taskForm.cabin_status === 'daily_clean' ? 'bg-blue-600 text-white ring-2 ring-blue-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                        class="flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-sm font-medium transition-all">
                                    <span class="text-lg">&#x1F6CF;&#xFE0F;</span> Daily Clean
                                </button>
                                <button @click="setCabinStatus('arrival')"
                                        :class="taskForm.cabin_status === 'arrival' ? 'bg-amber-500 text-white ring-2 ring-amber-500' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                        class="flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-sm font-medium transition-all">
                                    <span class="text-lg">&#x2708;&#xFE0F;</span> Arrival
                                </button>
                            </div>
                        </div>

                        <!-- 2. Location Selector (Multi-select) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Locations *</label>
                            <button @click="openLocationPicker()"
                                    class="w-full flex items-center justify-between px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-left hover:border-blue-400 dark:hover:border-blue-500 transition-colors">
                                <span :class="selectedLocations.length > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"
                                      x-text="selectedLocations.length > 0 ? selectedLocations.length + ' location' + (selectedLocations.length > 1 ? 's' : '') + ' selected' : 'Select locations'"></span>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-sm"></i>
                            </button>

                            <!-- Selected Locations Chips -->
                            <div x-show="selectedLocations.length > 0" class="flex flex-wrap gap-2 mt-2">
                                <template x-for="loc in selectedLocations" :key="loc.id">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium">
                                        <span x-text="loc.location_name"></span>
                                        <button @click="removeLocation(loc.id)" class="hover:text-blue-900 dark:hover:text-blue-100 ml-0.5">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                        </button>
                                    </span>
                                </template>
                            </div>

                            <!-- Task Preview -->
                            <div x-show="selectedLocations.length > 0" class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/10 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-xs text-blue-600 dark:text-blue-400">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    <span x-text="selectedLocations.length"></span> task<span x-show="selectedLocations.length > 1">s</span> will be created:
                                    <span class="font-medium" x-text="taskForm.cabin_status === 'departure' ? 'Departure' : taskForm.cabin_status === 'arrival' ? 'Arrival' : 'Daily Clean'"></span>
                                </p>
                            </div>
                            <p x-show="formErrors.location_ids" class="mt-1 text-sm text-red-500" x-text="formErrors.location_ids"></p>
                        </div>

                        <!-- 3. Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Scheduled Date</label>
                            <input type="date" x-model="taskForm.scheduled_date" @change="checkHoliday(); locationsLoaded = false;"
                                :min="minBookingDate"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i>Minimum <span x-text="minimumBookingNoticeDays"></span>-day advance booking required
                            </p>
                            <!-- Holiday indicator -->
                            <div x-show="holidayInfo && holidayInfo.is_sunday_or_holiday" class="mt-2 flex items-center gap-2 p-2 bg-amber-50 dark:bg-amber-900/10 rounded-lg border border-amber-200 dark:border-amber-800">
                                <span class="text-lg">&#x1F389;</span>
                                <div>
                                    <p class="text-xs font-medium text-amber-700 dark:text-amber-400" x-text="holidayInfo?.is_holiday ? 'Holiday: ' + holidayInfo.holiday_name : 'Sunday'"></p>
                                    <p class="text-[11px] text-amber-600 dark:text-amber-500">1.5x rate applies to extra tasks</p>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Time (Preset Buttons) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Scheduled Time</label>
                            <div class="grid grid-cols-6 gap-2">
                                <template x-for="time in ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00']" :key="time">
                                    <button @click="taskForm.scheduled_time = time"
                                            :class="taskForm.scheduled_time === time ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                            class="px-2 py-2 rounded-lg text-sm font-medium transition-all text-center" x-text="time">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- 5. Duration (Preset Buttons) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estimated Duration (minutes)</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                Leave unselected to use the location's default duration. Click a selected button again to clear it.
                            </p>
                            <div class="grid grid-cols-5 gap-2">
                                <template x-for="dur in [30, 60, 90, 120, 180]" :key="dur">
                                    <button type="button"
                                            @click="taskForm.estimated_duration_minutes = (taskForm.estimated_duration_minutes === dur ? null : dur)"
                                            :class="taskForm.estimated_duration_minutes === dur ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                            class="px-2 py-2 rounded-lg text-sm font-medium transition-all text-center" x-text="dur">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- 6. Rate Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rate Type</label>
                            <div class="flex gap-3">
                                <template x-for="rate in ['Normal', 'Student']" :key="rate">
                                    <button @click="taskForm.rate_type = rate"
                                            :class="taskForm.rate_type === rate ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                            class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all" x-text="rate">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- 7. Employee Assignment -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employee Assignment</label>

                            <!-- Auto-Assign Toggle -->
                            <div @click="toggleAutoAssign()"
                                 class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-colors"
                                 :class="taskForm.auto_assign ? 'border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/10' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Auto-Assign (Recommended)</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Automatically assign the best available employees</p>
                                </div>
                                <div class="w-11 h-6 rounded-full transition-colors relative"
                                     :class="taskForm.auto_assign ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'">
                                    <div class="absolute w-5 h-5 bg-white rounded-full top-0.5 transition-all shadow-sm"
                                         :class="taskForm.auto_assign ? 'left-[22px]' : 'left-0.5'"></div>
                                </div>
                            </div>

                            <!-- Manual Employee Selector -->
                            <div x-show="!taskForm.auto_assign" x-transition class="mt-3">
                                <button @click="openEmployeePicker()"
                                        class="w-full flex items-center justify-between px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-left hover:border-blue-400 transition-colors">
                                    <span :class="selectedEmployees.length > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"
                                          x-text="selectedEmployees.length > 0 ? selectedEmployees.length + ' employee' + (selectedEmployees.length > 1 ? 's' : '') + ' selected' : 'Select employees manually'"></span>
                                    <i class="fa-solid fa-chevron-down text-gray-400 text-sm"></i>
                                </button>
                                <!-- Selected employees chips -->
                                <div x-show="selectedEmployees.length > 0" class="flex flex-wrap gap-2 mt-2">
                                    <template x-for="emp in selectedEmployees" :key="emp.id">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-full text-xs font-medium">
                                            <span x-text="emp.name"></span>
                                            <button @click="removeEmployee(emp.id)" class="hover:text-green-900 dark:hover:text-green-100 ml-0.5">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                            </button>
                                        </span>
                                    </template>
                                </div>
                            </div>

                        </div>

                        <!-- 8. Priority Toggle -->
                        <div @click="togglePriority()"
                             class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                             :class="{
                                 'border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/10': !!taskForm.arrival_status,
                                 'border-gray-200 dark:border-gray-600': !taskForm.arrival_status,
                                 'opacity-75 cursor-not-allowed': taskForm.cabin_status === 'arrival'
                             }">
                            <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors"
                                 :class="taskForm.arrival_status ? 'bg-amber-500 border-amber-500 text-white' : 'border-gray-300 dark:border-gray-500'">
                                <i x-show="taskForm.arrival_status" class="fa-solid fa-check text-[10px]"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    High Priority (Guest Arrival)
                                    <span x-show="taskForm.cabin_status === 'arrival'" class="text-xs text-amber-600 dark:text-amber-400 ml-1">- Required</span>
                                </p>
                            </div>
                        </div>

                        <!-- 9. Extra Charges for Billing -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Extra Charges (for Billing)</label>
                            <div @click="taskForm.extra_bed = !taskForm.extra_bed"
                                 class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                                 :class="taskForm.extra_bed ? 'border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/10' : 'border-gray-200 dark:border-gray-600'">
                                <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors"
                                     :class="taskForm.extra_bed ? 'bg-blue-500 border-blue-500 text-white' : 'border-gray-300 dark:border-gray-500'">
                                    <i x-show="taskForm.extra_bed" class="fa-solid fa-check text-[10px]"></i>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Extra Bed</p>
                            </div>
                        </div>

                        <!-- 10. Extra Task (Independent Task) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Extra Task (Independent Task)</label>
                            <div @click="extraTaskEnabled = !extraTaskEnabled"
                                 class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-colors"
                                 :class="extraTaskEnabled ? 'border-purple-300 dark:border-purple-700 bg-purple-50 dark:bg-purple-900/10' : 'border-gray-200 dark:border-gray-600'">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors"
                                         :class="extraTaskEnabled ? 'bg-purple-500 border-purple-500 text-white' : 'border-gray-300 dark:border-gray-500'">
                                        <i x-show="extraTaskEnabled" class="fa-solid fa-check text-[10px]"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Add Extra Task</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Creates a separate independent task</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Extra Task Details -->
                            <div x-show="extraTaskEnabled" x-transition class="mt-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3 border border-gray-200 dark:border-gray-600">
                                <!-- Holiday banner for extra task -->
                                <div x-show="holidayInfo && holidayInfo.is_sunday_or_holiday" class="flex items-center gap-2 p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                    <span class="text-lg">&#x1F389;</span>
                                    <div>
                                        <p class="text-xs font-medium text-amber-700 dark:text-amber-400" x-text="holidayInfo?.is_holiday ? 'Holiday: ' + holidayInfo.holiday_name : 'Sunday'"></p>
                                        <p class="text-[11px] text-amber-600 dark:text-amber-500">1.5x rate applies to this task</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Task Name *</label>
                                    <input type="text" x-model="extraTask.name" placeholder="e.g., Window Cleaning"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-500 rounded-lg bg-white dark:bg-gray-600 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Base Price (EUR)</label>
                                    <input type="number" x-model="extraTask.price" min="0" step="0.01" placeholder="16"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-500 rounded-lg bg-white dark:bg-gray-600 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Duration (minutes)</label>
                                    <div class="grid grid-cols-4 gap-2">
                                        <template x-for="dur in [30, 60, 90, 120]" :key="'extra-' + dur">
                                            <button @click="extraTask.duration = dur"
                                                    :class="extraTask.duration === dur ? 'bg-purple-600 text-white' : 'bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-500'"
                                                    class="px-2 py-2 rounded-lg text-sm font-medium transition-all text-center" x-text="dur">
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Price Preview -->
                                <div class="p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500">
                                    <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                                        <span>Base Price:</span>
                                        <span x-text="'EUR ' + (parseFloat(extraTask.price) || 16).toFixed(2)"></span>
                                    </div>
                                    <div x-show="holidayInfo && holidayInfo.is_sunday_or_holiday" class="flex justify-between text-xs text-amber-600 dark:text-amber-400 mt-1">
                                        <span>Holiday/Sunday Rate (1.5x):</span>
                                        <span>Applied</span>
                                    </div>
                                    <div class="flex justify-between text-sm font-semibold text-gray-900 dark:text-white mt-1 pt-1 border-t border-gray-200 dark:border-gray-500">
                                        <span>Final Price:</span>
                                        <span x-text="'EUR ' + calculateExtraTaskPrice()"></span>
                                    </div>
                                </div>

                                <!-- Extra Task Preview -->
                                <div x-show="extraTask.name && selectedLocations.length > 0" class="p-3 bg-purple-50 dark:bg-purple-900/10 rounded-lg border border-purple-200 dark:border-purple-800">
                                    <p class="text-xs text-purple-600 dark:text-purple-400 mb-1">Task will be created as:</p>
                                    <p class="text-sm font-medium text-purple-800 dark:text-purple-300" x-text="extraTask.name"></p>
                                    <p class="text-xs text-purple-600 dark:text-purple-400">Extra Task</p>
                                    <p class="text-xs text-purple-500 dark:text-purple-500" x-text="(selectedLocations[0]?.location_name || '') + ' - ' + extraTask.duration + ' min'"></p>
                                </div>
                            </div>
                        </div>

                        <!-- 11. Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Special Request)</label>
                            <textarea x-model="taskForm.notes" rows="2" placeholder="Enter any special requests or notes for this task..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none text-sm"></textarea>
                        </div>

                        <!-- Error -->
                        <div x-show="submitError" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-sm text-red-600 dark:text-red-400" x-text="submitError"></p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700 sticky bottom-0 bg-white dark:bg-gray-800">
                        <button @click="showCreateModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                        <button @click="submitTask()" :disabled="submitting"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50 transition-colors">
                            <i x-show="submitting" class="fa-solid fa-spinner animate-spin"></i>
                            <span x-text="submitting ? 'Creating...' : 'Create Task'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- LOCATION PICKER MODAL (grouped + occupancy) --}}
        {{-- ========================================== --}}
        <div x-show="showLocationPicker" x-transition class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showLocationPicker = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg max-h-[80vh] flex flex-col" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Locations</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedLocations.length + ' selected'"></p>
                        </div>
                        <button @click="showLocationPicker = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"><i class="fa-solid fa-xmark text-gray-500"></i></button>
                    </div>
                    <div class="overflow-y-auto flex-1 p-4">
                        <div x-show="locationGroups.length === 0" class="text-center py-8 text-gray-400">
                            <i class="fa-solid fa-spinner animate-spin text-xl mb-2"></i>
                            <p class="text-sm">Loading locations...</p>
                        </div>
                        <div class="space-y-2">
                            <template x-for="group in locationGroups" :key="group.name">
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                    <!-- Group Header -->
                                    <button @click="toggleLocationGroup(group.name)"
                                            class="w-full flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <span x-text="getLocationIcon(group.type)" class="text-lg"></span>
                                            <div class="text-left">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="group.name"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="group.items.length + ' unit' + (group.items.length > 1 ? 's' : '') + ' - ' + group.duration + ' min'"></p>
                                            </div>
                                        </div>
                                        <i class="fa-solid text-gray-400 text-xs" :class="expandedGroups[group.name] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                    </button>
                                    <!-- Group Items -->
                                    <div x-show="expandedGroups[group.name]" x-transition class="p-2">
                                        <div :class="group.items.length > 1 ? 'grid grid-cols-3 sm:grid-cols-4 gap-1.5' : ''">
                                            <template x-for="loc in group.items" :key="loc.id">
                                                <button @click="!loc.is_occupied && toggleLocation(loc)"
                                                        :disabled="loc.is_occupied"
                                                        class="flex items-center gap-1.5 px-2.5 py-2 rounded-lg text-xs font-medium transition-all border"
                                                        :class="{
                                                            'bg-blue-600 text-white border-blue-600': isLocationSelected(loc.id),
                                                            'bg-gray-100 dark:bg-gray-600 text-gray-400 dark:text-gray-500 border-gray-200 dark:border-gray-500 cursor-not-allowed line-through': loc.is_occupied,
                                                            'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-blue-400': !isLocationSelected(loc.id) && !loc.is_occupied
                                                        }">
                                                    <span x-text="getLocationDisplayName(loc, group)"></span>
                                                    <i x-show="isLocationSelected(loc.id)" class="fa-solid fa-check text-[9px]"></i>
                                                    <span x-show="loc.is_occupied" class="text-[10px] text-red-400" x-text="loc.task_status"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="showLocationPicker = false"
                                class="w-full py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                            Done (<span x-text="selectedLocations.length"></span> selected)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- EMPLOYEE PICKER MODAL                      --}}
        {{-- ========================================== --}}
        <div x-show="showEmployeePicker" x-transition class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showEmployeePicker = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md max-h-[80vh] flex flex-col" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Employees</h3>
                        <button @click="showEmployeePicker = false" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">Done</button>
                    </div>
                    <div class="overflow-y-auto flex-1 p-4">
                        <div x-show="availableEmployees.length === 0" class="text-center py-8 text-gray-400 text-sm">
                            No employees available
                        </div>
                        <div class="space-y-2">
                            <template x-for="emp in availableEmployees" :key="emp.id">
                                <button @click="toggleEmployee(emp)"
                                        class="w-full flex items-center gap-3 p-3 rounded-lg border transition-colors"
                                        :class="isEmployeeSelected(emp.id) ? 'border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/10' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold"
                                         :class="isEmployeeSelected(emp.id) ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300'"
                                         x-text="emp.name.charAt(0).toUpperCase()"></div>
                                    <div class="flex-1 text-left">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="emp.name"></p>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                            <span x-show="emp.has_driving_license" class="text-green-600 dark:text-green-400">Driver</span>
                                            <span x-text="emp.tasks_today + ' tasks today'"></span>
                                        </div>
                                    </div>
                                    <i x-show="isEmployeeSelected(emp.id)" class="fa-solid fa-check text-green-500"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Details Slide-in Drawer -->
        <x-client-components.shared.appointment-details-drawer
            showVar="showTaskModal"
            dataVar="selectedTask"
            closeMethod="closeTaskDrawer"
            title="Task Details"
            :showTeam="true"
            :showChecklist="true">
            <x-slot name="footer">
                <div class="flex gap-3">
                    <button
                        x-show="selectedTask && (selectedTask.status === 'Scheduled' || selectedTask.status === 'Pending')"
                        @click="cancelTask()"
                        :disabled="submitting"
                        class="flex-1 text-sm px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors font-medium disabled:opacity-50">
                        Cancel Task
                    </button>
                    <button
                        @click="closeTaskDrawer()"
                        class="flex-1 text-sm px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium">
                        Close
                    </button>
                </div>
            </x-slot>
        </x-client-components.shared.appointment-details-drawer>

        <!-- Toast -->
        <div x-show="toast.show" x-transition
             class="fixed bottom-4 right-4 z-[70] px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
             x-text="toast.message" style="display: none;"></div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function scheduleManager() {
            return {
                // ---- Calendar state ----
                calendarView: 'month',
                calendarDate: new Date(),
                visibleDays: [],
                monthDays: [],
                timeSlots: ['12 AM', '01 AM', '02 AM', '03 AM', '04 AM', '05 AM', '06 AM', '07 AM', '08 AM', '09 AM', '10 AM', '11 AM', '12 PM', '01 PM', '02 PM', '03 PM', '04 PM', '05 PM', '06 PM', '07 PM', '08 PM', '09 PM', '10 PM', '11 PM'],
                showMonthPicker: false,
                pickerYear: new Date().getFullYear(),
                calendarMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                calendarLoading: false,

                // Tasks cache keyed by date string
                calendarTasks: {},

                // Task list state
                taskListStatusFilter: 'all',
                taskListSearch: '',
                taskListPage: 1,
                taskListPerPage: 5,
                _taskCacheVersion: 0, // reactivity trigger

                // Legacy (still used by create modal / optimization)
                selectedDate: '{{ now()->format("Y-m-d") }}',
                tasks: [],
                loading: false,
                optimizing: false,

                // Modals
                showCreateModal: false,
                showTaskModal: false,
                showLocationPicker: false,
                showEmployeePicker: false,

                // Location data
                locationGroups: [],
                locationsLoaded: false,
                expandedGroups: {},
                selectedLocations: [],

                // Employee data
                availableEmployees: [],
                employeesLoaded: false,
                selectedEmployees: [],

                // Holiday
                holidayInfo: null,

                // Extra task
                extraTaskEnabled: false,
                extraTask: { name: '', price: '16', duration: 30 },

                // Task form
                submitting: false,
                submitError: '',
                formErrors: {},
                selectedTask: null,

                toast: { show: false, message: '', type: 'success' },

                minimumBookingNoticeDays: {{ $minimumBookingNoticeDays }},
                minBookingDate: new Date(Date.now() + {{ $minimumBookingNoticeDays }} * 86400000).toISOString().split('T')[0],

                taskForm: {
                    scheduled_date: new Date(Date.now() + {{ $minimumBookingNoticeDays }} * 86400000).toISOString().split('T')[0],
                    scheduled_time: '08:00',
                    rate_type: 'Normal',
                    cabin_status: 'departure',
                    arrival_status: 0,
                    extra_bed: false,
                    estimated_duration_minutes: null,
                    auto_assign: true,
                    notes: '',
                },

                csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

                // ===============================
                // Initialization
                // ===============================
                init() {
                    this.pickerYear = this.calendarDate.getFullYear();
                    this.updateCalendarView();
                    this.clInitTemplates();
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                // ===============================
                // Calendar header helpers
                // ===============================
                get currentMonthYear() {
                    return this.calendarDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                },

                isSelectedMonth(monthIndex, year) {
                    return this.calendarDate.getMonth() === monthIndex &&
                           this.calendarDate.getFullYear() === year;
                },

                selectMonthYear(monthIndex, year) {
                    this.calendarDate = new Date(year, monthIndex, 1);
                    this.showMonthPicker = false;
                    this.updateCalendarView();
                },

                goToCurrentMonth() {
                    this.calendarDate = new Date();
                    this.pickerYear = this.calendarDate.getFullYear();
                    this.showMonthPicker = false;
                    this.updateCalendarView();
                },

                goToToday() {
                    this.calendarDate = new Date();
                    this.pickerYear = this.calendarDate.getFullYear();
                    this.updateCalendarView();
                },

                previousPeriod() {
                    const d = new Date(this.calendarDate);
                    if (this.calendarView === 'week') d.setDate(d.getDate() - 7);
                    else if (this.calendarView === 'day') d.setDate(d.getDate() - 1);
                    else if (this.calendarView === 'month') d.setMonth(d.getMonth() - 1);
                    this.calendarDate = d;
                    this.pickerYear = this.calendarDate.getFullYear();
                    this.updateCalendarView();
                },

                nextPeriod() {
                    const d = new Date(this.calendarDate);
                    if (this.calendarView === 'week') d.setDate(d.getDate() + 7);
                    else if (this.calendarView === 'day') d.setDate(d.getDate() + 1);
                    else if (this.calendarView === 'month') d.setMonth(d.getMonth() + 1);
                    this.calendarDate = d;
                    this.pickerYear = this.calendarDate.getFullYear();
                    this.updateCalendarView();
                },

                // ===============================
                // Calendar view generation
                // ===============================
                updateCalendarView() {
                    if (this.calendarView === 'week') this.generateCalendarWeek();
                    else if (this.calendarView === 'day') this.generateCalendarDay();
                    else if (this.calendarView === 'month') this.generateCalendarMonth();

                    this.fetchCalendarTasks();

                    this.$nextTick(() => {
                        if (this.$refs.timeGrid && (this.calendarView === 'week' || this.calendarView === 'day')) {
                            this.$refs.timeGrid.scrollTop = 8 * 40; // scroll to 8 AM
                        }
                    });
                },

                _toDateStr(date) {
                    return date.getFullYear() + '-' +
                           String(date.getMonth() + 1).padStart(2, '0') + '-' +
                           String(date.getDate()).padStart(2, '0');
                },

                _isToday(date) {
                    return date.toDateString() === new Date().toDateString();
                },

                generateCalendarWeek() {
                    const days = [];
                    const start = new Date(this.calendarDate);
                    start.setDate(start.getDate() - start.getDay() + (start.getDay() === 0 ? -6 : 1)); // Monday

                    for (let i = 0; i < 7; i++) {
                        const d = new Date(start);
                        d.setDate(d.getDate() + i);
                        days.push({
                            date: this._toDateStr(d),
                            dayName: d.toLocaleDateString('en-US', { weekday: 'short' }).toUpperCase(),
                            dayNumber: d.getDate(),
                            isToday: this._isToday(d)
                        });
                    }
                    this.visibleDays = days;
                },

                generateCalendarDay() {
                    const d = new Date(this.calendarDate);
                    this.visibleDays = [{
                        date: this._toDateStr(d),
                        dayName: d.toLocaleDateString('en-US', { weekday: 'long' }).toUpperCase(),
                        dayNumber: d.getDate(),
                        isToday: this._isToday(d)
                    }];
                },

                generateCalendarMonth() {
                    const days = [];
                    const year = this.calendarDate.getFullYear();
                    const month = this.calendarDate.getMonth();
                    const firstDay = new Date(year, month, 1);
                    const startDate = new Date(firstDay);
                    startDate.setDate(startDate.getDate() - (startDate.getDay() || 7) + 1);

                    for (let i = 0; i < 42; i++) {
                        const d = new Date(startDate);
                        d.setDate(d.getDate() + i);
                        days.push({
                            date: this._toDateStr(d),
                            dayNumber: d.getDate(),
                            isCurrentMonth: d.getMonth() === month,
                            isToday: this._isToday(d)
                        });
                    }
                    this.monthDays = days;
                },

                // ===============================
                // Task data for calendar
                // ===============================
                getVisibleDateRange() {
                    if (this.calendarView === 'month') {
                        return this.monthDays.map(d => d.date);
                    }
                    return this.visibleDays.map(d => d.date);
                },

                async fetchCalendarTasks() {
                    const dates = this.getVisibleDateRange();
                    const missing = dates.filter(d => !(d in this.calendarTasks));

                    if (missing.length === 0) return;

                    this.calendarLoading = true;
                    try {
                        const results = await Promise.all(
                            missing.map(date =>
                                fetch(`/manager/schedule/tasks?date=${date}`)
                                    .then(r => r.json())
                                    .then(data => ({ date, tasks: data.tasks || [] }))
                                    .catch(() => ({ date, tasks: [] }))
                            )
                        );
                        results.forEach(r => {
                            this.calendarTasks[r.date] = r.tasks;
                        });
                    } catch (e) {
                        // silently fail
                    }
                    this.calendarLoading = false;
                    this._taskCacheVersion++;
                },

                getTasksForDay(date) {
                    // Access _taskCacheVersion to ensure Alpine reactivity on cache updates
                    void this._taskCacheVersion;
                    return this.calendarTasks[date] || [];
                },

                // All tasks from currently visible dates, flattened
                get allTasksList() {
                    void this._taskCacheVersion;
                    const dates = this.getVisibleDateRange();
                    const all = [];
                    dates.forEach(date => {
                        const tasks = this.calendarTasks[date] || [];
                        tasks.forEach(t => {
                            all.push({ ...t, date });
                        });
                    });
                    // Sort by date then time
                    all.sort((a, b) => {
                        if (a.date !== b.date) return a.date.localeCompare(b.date);
                        return (a.scheduled_time || '').localeCompare(b.scheduled_time || '');
                    });
                    return all;
                },

                get filteredTaskList() {
                    let list = this.allTasksList;
                    if (this.taskListStatusFilter !== 'all') {
                        list = list.filter(t => t.status === this.taskListStatusFilter);
                    }
                    if (this.taskListSearch) {
                        const q = this.taskListSearch.toLowerCase();
                        list = list.filter(t =>
                            (t.location_name || '').toLowerCase().includes(q) ||
                            (t.location_type || '').toLowerCase().includes(q) ||
                            (t.status || '').toLowerCase().includes(q) ||
                            (t.cabin_status || '').toLowerCase().includes(q) ||
                            (t.scheduled_time || '').includes(q)
                        );
                    }
                    return list;
                },

                get taskListTotalPages() {
                    return Math.ceil(this.filteredTaskList.length / this.taskListPerPage);
                },

                get paginatedTaskList() {
                    const start = (this.taskListPage - 1) * this.taskListPerPage;
                    return this.filteredTaskList.slice(start, start + this.taskListPerPage);
                },

                // Sliding window of up to 5 page numbers around the current page
                get visibleTaskListPages() {
                    const total = this.taskListTotalPages;
                    if (total < 1) return [];
                    const windowSize = 5;
                    if (total < windowSize + 1) {
                        return Array.from({ length: total }, (_, i) => i + 1);
                    }
                    let start = Math.max(1, this.taskListPage - 2);
                    let end = start + windowSize - 1;
                    if (end > total) {
                        end = total;
                        start = end - windowSize + 1;
                    }
                    return Array.from({ length: end - start + 1 }, (_, i) => start + i);
                },

                // ===============================
                // Task rendering helpers
                // ===============================
                getTaskColor(task) {
                    if (!task) return '#6B7280';
                    switch (task.status) {
                        case 'Completed': return '#2FBC00';
                        case 'In Progress': return '#3B82F6';
                        case 'On Hold': return '#F59E0B';
                        case 'Cancelled': return '#FE1E28';
                        default: return '#6B7280'; // Scheduled / Pending
                    }
                },

                getTaskTop(task) {
                    if (!task.scheduled_time) return 0;
                    const [hours, minutes] = task.scheduled_time.split(':').map(Number);
                    const pxPerHour = 40;
                    return (hours * pxPerHour) + (minutes * pxPerHour / 60);
                },

                getTaskHeight(task) {
                    const duration = parseInt(task.duration) || 60;
                    const pxPerHour = 40;
                    const h = (duration / 60) * pxPerHour;
                    return Math.max(h, 20); // minimum 20px so it's clickable
                },

                formatDate(dateStr) {
                    const date = new Date(dateStr + 'T00:00:00');
                    return date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
                },

                teamBadgeClass(teamId) {
                    const colors = [
                        'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                        'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                        'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-400',
                        'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400',
                        'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                    ];
                    return colors[(teamId - 1) % colors.length];
                },

                formatTaskDate(dateStr) {
                    if (!dateStr) return '-';
                    const date = new Date(dateStr + 'T00:00:00');
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                },

                // ===============================
                // Data Fetching (original)
                // ===============================
                async fetchTasks() {
                    this.loading = true;
                    try {
                        const res = await fetch(`/manager/schedule/tasks?date=${this.selectedDate}`);
                        const data = await res.json();
                        this.tasks = data.tasks || [];
                    } catch (e) {
                        this.tasks = [];
                    }
                    this.loading = false;
                },

                async fetchLocations() {
                    if (this.locationsLoaded) return;
                    try {
                        const res = await fetch(`/manager/schedule/locations?date=${this.taskForm.scheduled_date}`);
                        const data = await res.json();
                        this.locationGroups = data.grouped || [];
                        this.locationsLoaded = true;
                    } catch (e) {
                        this.locationGroups = [];
                    }
                },

                async refreshLocations() {
                    try {
                        const res = await fetch(`/manager/schedule/locations?date=${this.taskForm.scheduled_date}`);
                        const data = await res.json();
                        this.locationGroups = data.grouped || [];
                    } catch (e) {}
                },

                async fetchEmployees() {
                    if (this.employeesLoaded) return;
                    try {
                        const res = await fetch(`/manager/schedule/employees?date=${this.taskForm.scheduled_date}`);
                        const data = await res.json();
                        this.availableEmployees = data.employees || [];
                        this.employeesLoaded = true;
                    } catch (e) {
                        this.availableEmployees = [];
                    }
                },

                async checkHoliday() {
                    try {
                        const res = await fetch(`/manager/schedule/check-holiday?date=${this.taskForm.scheduled_date}`);
                        this.holidayInfo = await res.json();
                    } catch (e) {
                        this.holidayInfo = null;
                    }
                },

                // ===============================
                // Create Modal
                // ===============================
                openCreateModal() {
                    const selectedOrMin = this.selectedDate >= this.minBookingDate ? this.selectedDate : this.minBookingDate;
                    this.taskForm = {
                        scheduled_date: selectedOrMin,
                        scheduled_time: '08:00',
                        rate_type: 'Normal',
                        cabin_status: 'departure',
                        arrival_status: 0,
                        extra_bed: false,
                        estimated_duration_minutes: null,
                        auto_assign: true,
                        notes: '',
                    };
                    this.selectedLocations = [];
                    this.selectedEmployees = [];
                    this.extraTaskEnabled = false;
                    this.extraTask = { name: '', price: '16', duration: 30 };
                    this.holidayInfo = null;
                    this.formErrors = {};
                    this.submitError = '';
                    this.expandedGroups = {};
                    this.showCreateModal = true;
                    this.fetchLocations();
                    this.checkHoliday();
                },

                setCabinStatus(status) {
                    this.taskForm.cabin_status = status;
                    if (status === 'arrival') {
                        this.taskForm.arrival_status = 1;
                    } else {
                        this.taskForm.arrival_status = 0;
                    }
                },

                togglePriority() {
                    if (this.taskForm.cabin_status === 'arrival') return;
                    this.taskForm.arrival_status = this.taskForm.arrival_status ? 0 : 1;
                },

                toggleAutoAssign() {
                    this.taskForm.auto_assign = !this.taskForm.auto_assign;
                    if (this.taskForm.auto_assign) {
                        this.selectedEmployees = [];
                    } else {
                        this.fetchEmployees();
                    }
                },

                // ===============================
                // Location Picker
                // ===============================
                openLocationPicker() {
                    this.fetchLocations();
                    this.showLocationPicker = true;
                },

                toggleLocationGroup(name) {
                    this.expandedGroups[name] = !this.expandedGroups[name];
                },

                getLocationIcon(type) {
                    switch ((type || '').toLowerCase()) {
                        case 'small': return '🏠';
                        case 'medium': return '🏡';
                        case 'big': return '🏘️';
                        case 'queen': return '👑';
                        case 'igloo': return '🧊';
                        default: return '📍';
                    }
                },

                getLocationDisplayName(loc, group) {
                    if (group.items.length === 1) return loc.location_name;
                    const match = loc.location_name.match(/#(\d+)/);
                    return match ? `#${match[1]}` : loc.location_name;
                },

                isLocationSelected(id) {
                    return this.selectedLocations.some(l => l.id === id);
                },

                toggleLocation(loc) {
                    if (this.isLocationSelected(loc.id)) {
                        this.selectedLocations = this.selectedLocations.filter(l => l.id !== loc.id);
                    } else {
                        this.selectedLocations.push(loc);
                    }
                },

                removeLocation(id) {
                    this.selectedLocations = this.selectedLocations.filter(l => l.id !== id);
                },

                // ===============================
                // Employee Picker
                // ===============================
                openEmployeePicker() {
                    this.fetchEmployees();
                    this.showEmployeePicker = true;
                },

                isEmployeeSelected(id) {
                    return this.selectedEmployees.some(e => e.id === id);
                },

                toggleEmployee(emp) {
                    if (this.isEmployeeSelected(emp.id)) {
                        this.selectedEmployees = this.selectedEmployees.filter(e => e.id !== emp.id);
                    } else {
                        this.selectedEmployees.push(emp);
                    }
                },

                removeEmployee(id) {
                    this.selectedEmployees = this.selectedEmployees.filter(e => e.id !== id);
                },

                // ===============================
                // Extra Task Price
                // ===============================
                calculateExtraTaskPrice() {
                    const base = parseFloat(this.extraTask.price) || 16;
                    if (this.holidayInfo && this.holidayInfo.is_sunday_or_holiday) {
                        return (base * 1.5).toFixed(2);
                    }
                    return base.toFixed(2);
                },

                // ===============================
                // Submit Task
                // ===============================
                async submitTask() {
                    this.formErrors = {};
                    if (this.selectedLocations.length === 0) {
                        this.formErrors.location_ids = 'Please select at least one location';
                        return;
                    }
                    if (this.extraTaskEnabled && !this.extraTask.name.trim()) {
                        this.submitError = 'Please enter a name for the Extra Task';
                        return;
                    }

                    this.submitting = true;
                    this.submitError = '';
                    try {
                        const payload = {
                            location_ids: this.selectedLocations.map(l => l.id),
                            scheduled_date: this.taskForm.scheduled_date,
                            scheduled_time: this.taskForm.scheduled_time,
                            rate_type: this.taskForm.rate_type,
                            cabin_status: this.taskForm.cabin_status,
                            arrival_status: this.taskForm.arrival_status,
                            extra_bed: this.taskForm.extra_bed ? 1 : 0,
                            estimated_duration_minutes: this.taskForm.estimated_duration_minutes,
                            auto_assign: this.taskForm.auto_assign,
                            notes: this.taskForm.notes || null,
                        };

                        if (!this.taskForm.auto_assign && this.selectedEmployees.length > 0) {
                            payload.employee_ids = this.selectedEmployees.map(e => e.id);
                        }

                        if (this.extraTaskEnabled) {
                            payload.extra_task_enabled = true;
                            payload.extra_task_name = this.extraTask.name;
                            payload.extra_task_price = parseFloat(this.extraTask.price) || 16;
                            payload.extra_task_duration = this.extraTask.duration;
                        }

                        const res = await fetch('/manager/schedule/tasks', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload)
                        });
                        const data = await res.json();
                        if (!res.ok) {
                            if (data.errors) {
                                this.formErrors = data.errors;
                            }
                            throw new Error(data.message || 'Failed to create task');
                        }
                        this.showCreateModal = false;
                        this.locationsLoaded = false;
                        // Invalidate cache for affected date and refresh
                        delete this.calendarTasks[this.taskForm.scheduled_date];
                        this.fetchCalendarTasks();
                        this.showToast(data.message || 'Task(s) created successfully!');
                    } catch (e) {
                        this.submitError = e.message || 'An error occurred';
                    }
                    this.submitting = false;
                },

                // ===============================
                // Task Details
                // ===============================
                openTaskDetails(task) {
                    // Map task data to drawer-compatible format
                    this.selectedTask = {
                        ...task,
                        id: task.id,
                        status: (task.status || 'Scheduled').toLowerCase(),
                        serviceType: (task.cabin_status || '').replace('_', ' '),
                        service_type: (task.cabin_status || '').replace('_', ' '),
                        serviceDate: task.date,
                        service_date: task.date,
                        serviceTime: task.scheduled_time,
                        service_time: task.scheduled_time,
                        location: task.location_name,
                        cabin_name: task.location_name,
                        location_type: task.location_type,
                        duration: task.duration,
                        rate_type: task.rate_type,
                        task_description: task.task_description,
                        assignedMembers: (task.employees || []).map(e => ({
                            name: e.name,
                            initial: e.name ? e.name.charAt(0).toUpperCase() : '?'
                        })),
                    };
                    this.showTaskModal = true;
                    document.body.style.overflow = 'hidden';
                },

                closeTaskDrawer() {
                    this.showTaskModal = false;
                    this.selectedTask = null;
                    document.body.style.overflow = 'auto';
                },

                // Drawer helper methods required by shared drawer component
                getDrawerStatus() {
                    return this.selectedTask?.status || 'scheduled';
                },

                getDrawerData(key) {
                    if (key === 'assignedMembers') {
                        return this.selectedTask?.assignedMembers || [];
                    }
                    return this.selectedTask?.[key];
                },

                getDrawerChecklistItems() {
                    const serviceType = this.selectedTask?.cabin_status || '';
                    return window.getChecklistByServiceType ? window.getChecklistByServiceType(serviceType) : [];
                },

                isChecklistItemCompleted(itemIndex) {
                    if (!this.selectedTask) return false;
                    const completions = this.selectedTask.checklist_completions || [];
                    return completions.includes(itemIndex) || completions.includes(itemIndex + 1);
                },

                getDrawerChecklistProgress() {
                    if (!this.selectedTask) return { completed: 0, total: 0, percentage: 0 };
                    const items = this.getDrawerChecklistItems();
                    const total = items.length;
                    const completions = this.selectedTask.checklist_completions || [];
                    const completed = completions.length;
                    const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
                    return { completed, total, percentage };
                },

                formatDrawerDate(dateString) {
                    if (!dateString) return '-';
                    const date = new Date(dateString + 'T00:00:00');
                    return date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                },

                formatDrawerTime(timeString) {
                    if (!timeString) return '-';
                    const parts = timeString.split(':');
                    if (parts.length < 2) return timeString;
                    let hours = parseInt(parts[0]);
                    const minutes = parts[1];
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12;
                    return hours + ':' + minutes + ' ' + ampm;
                },

                async cancelTask() {
                    if (!this.selectedTask) return;
                    this.submitting = true;
                    try {
                        const res = await fetch(`/manager/schedule/tasks/${this.selectedTask.id}/cancel`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            this.showTaskModal = false;
                            // Invalidate all cached dates and refresh
                            this.calendarTasks = {};
                            this.fetchCalendarTasks();
                            this.showToast('Task cancelled');
                        } else {
                            const data = await res.json();
                            this.showToast(data.message || 'Failed to cancel task', 'error');
                        }
                    } catch (e) {
                        this.showToast('Failed to cancel task', 'error');
                    }
                    this.submitting = false;
                },

                async runOptimization() {
                    this.optimizing = true;
                    // Use the currently visible day for optimization
                    const optDate = this.calendarView === 'day'
                        ? this.visibleDays[0]?.date
                        : this.selectedDate;
                    try {
                        const res = await fetch('/manager/schedule/optimize', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ date: optDate })
                        });
                        const data = await res.json();
                        if (res.ok) {
                            this.showToast(data.message || 'Optimization completed!');
                            // Invalidate cache and refresh
                            this.calendarTasks = {};
                            this.fetchCalendarTasks();
                        } else {
                            this.showToast(data.message || 'Optimization failed', 'error');
                        }
                    } catch (e) {
                        this.showToast('Optimization failed', 'error');
                    }
                    this.optimizing = false;
                },

                // ===============================
                // Checklist Templates (CompanyChecklist-backed)
                // ===============================
                clShowAddModal: false,
                clShowEditModal: false,
                clShowAddCategoryModal: false,
                clShowEditCategoryModal: false,
                clShowItemModal: false,
                clShowDeleteModal: false,
                clEditingId: null,
                clSubmitting: false,
                clExpandedCategories: [],
                clFormData: { name: '', reminders: '', categories: [] },
                clCategoryForm: { name: '' },
                clEditCategoryForm: { id: null, name: '' },
                clItemForm: { id: null, category_id: null, name: '', quantity: '1' },
                clDeleteTarget: { type: null, id: null, parentId: null },
                clDeleteMessage: '',
                clColorPalette: ['#22c55e', '#a855f7', '#3b82f6', '#14b8a6', '#f59e0b', '#ef4444', '#06b6d4', '#8b5cf6', '#ec4899', '#10b981'],
                clTemplates: [],

                clInitTemplates() {
                    let raw = [];
                    try {
                        const node = document.getElementById('schedule-bootstrap-data');
                        if (node && node.dataset.checklists) {
                            raw = JSON.parse(node.dataset.checklists) || [];
                        }
                    } catch (e) { raw = []; }
                    this.clTemplates = raw.map(c => this.clDecorateTemplate({
                        id: c.id,
                        name: c.name,
                        reminders: c.important_reminders || '',
                        categories: c.categories || [],
                    }));
                },

                clDecorateTemplate(t) {
                    return {
                        ...t,
                        initials: this.clGetInitials(t.name || ''),
                        color: this.clGetColor(t.id),
                        itemCount: this.clCountItems(t.categories || []),
                    };
                },

                clRefreshTemplate(id) {
                    const idx = this.clTemplates.findIndex(t => t.id === id);
                    if (idx !== -1) {
                        this.clTemplates[idx] = this.clDecorateTemplate(this.clTemplates[idx]);
                    }
                },

                clGetInitials(name) {
                    return (name || '').split(/\s+/).filter(Boolean).map(w => w.charAt(0).toUpperCase()).slice(0, 2).join('') || 'CL';
                },

                clGetColor(id) {
                    const i = ((id || 0) - 1) % this.clColorPalette.length;
                    return this.clColorPalette[(i + this.clColorPalette.length) % this.clColorPalette.length];
                },

                clCountItems(categories) {
                    return (categories || []).reduce((sum, c) => sum + ((c.items || []).length), 0);
                },

                clCurrentTemplate() {
                    return this.clTemplates.find(t => t.id === this.clEditingId) || null;
                },

                clEditTemplate(template) {
                    this.clEditingId = template.id;
                    this.clFormData = {
                        name: template.name,
                        reminders: template.reminders || '',
                        categories: template.categories || []
                    };
                    this.clExpandedCategories = [];
                    this.clShowEditModal = true;
                },

                clOpenAddTemplate() {
                    this.clEditingId = null;
                    this.clFormData = { name: '', reminders: '', categories: [] };
                    this.clShowAddModal = true;
                },

                async clCreateTemplate() {
                    if (!this.clFormData.name.trim() || this.clSubmitting) return;
                    this.clSubmitting = true;
                    try {
                        const res = await fetch('/manager/checklist', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ name: this.clFormData.name, important_reminders: this.clFormData.reminders })
                        });
                        const data = await res.json();
                        if (res.ok) {
                            const created = this.clDecorateTemplate({
                                id: data.checklist.id,
                                name: data.checklist.name,
                                reminders: data.checklist.important_reminders || '',
                                categories: data.checklist.categories || [],
                            });
                            this.clTemplates.push(created);
                            this.clShowAddModal = false;
                            this.showToast('Template created');
                        } else {
                            this.showToast(data.message || 'Failed to create template', 'error');
                        }
                    } catch (e) { this.showToast('Failed to create template', 'error'); }
                    this.clSubmitting = false;
                },

                async clDeleteTemplate(id) {
                    const tpl = this.clTemplates.find(t => t.id === id);
                    if (!tpl) return;
                    this.clDeleteTarget = { type: 'template', id, parentId: null };
                    this.clDeleteMessage = `Delete "${tpl.name}" and all its categories and items?`;
                    this.clShowDeleteModal = true;
                },

                async clUpdateChecklistDetails() {
                    if (!this.clEditingId) return;
                    try {
                        const res = await fetch(`/manager/checklist/${this.clEditingId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ name: this.clFormData.name, important_reminders: this.clFormData.reminders })
                        });
                        if (res.ok) {
                            const tpl = this.clCurrentTemplate();
                            if (tpl) {
                                tpl.name = this.clFormData.name;
                                tpl.reminders = this.clFormData.reminders;
                                this.clRefreshTemplate(tpl.id);
                            }
                        }
                    } catch (e) { /* silent */ }
                },

                clToggleCategory(id) {
                    const idx = this.clExpandedCategories.indexOf(id);
                    if (idx > -1) this.clExpandedCategories.splice(idx, 1);
                    else this.clExpandedCategories.push(id);
                },

                clOpenAddCategoryModal() {
                    this.clCategoryForm = { name: '' };
                    this.clShowAddCategoryModal = true;
                },

                async clAddCategory() {
                    if (!this.clEditingId || !this.clCategoryForm.name.trim() || this.clSubmitting) return;
                    this.clSubmitting = true;
                    try {
                        const res = await fetch(`/manager/checklist/${this.clEditingId}/categories`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ name: this.clCategoryForm.name })
                        });
                        const data = await res.json();
                        if (res.ok) {
                            const newCat = { id: data.category.id, name: data.category.name, items: data.category.items || [] };
                            this.clFormData.categories.push(newCat);
                            const tpl = this.clCurrentTemplate();
                            if (tpl) {
                                tpl.categories = this.clFormData.categories;
                                this.clRefreshTemplate(tpl.id);
                            }
                            this.clShowAddCategoryModal = false;
                            this.showToast('Category added');
                        } else {
                            this.showToast(data.message || 'Failed to add category', 'error');
                        }
                    } catch (e) { this.showToast('Failed to add category', 'error'); }
                    this.clSubmitting = false;
                },

                clOpenEditCategory(category) {
                    this.clEditCategoryForm = { id: category.id, name: category.name };
                    this.clShowEditCategoryModal = true;
                },

                async clUpdateCategory() {
                    if (!this.clEditCategoryForm.name.trim() || this.clSubmitting) return;
                    this.clSubmitting = true;
                    try {
                        const res = await fetch(`/manager/checklist/categories/${this.clEditCategoryForm.id}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ name: this.clEditCategoryForm.name })
                        });
                        if (res.ok) {
                            const cat = this.clFormData.categories.find(c => c.id === this.clEditCategoryForm.id);
                            if (cat) cat.name = this.clEditCategoryForm.name;
                            this.clShowEditCategoryModal = false;
                            this.showToast('Category updated');
                        } else {
                            this.showToast('Failed to update category', 'error');
                        }
                    } catch (e) { this.showToast('Failed to update category', 'error'); }
                    this.clSubmitting = false;
                },

                clConfirmDeleteCategory(category) {
                    this.clDeleteTarget = { type: 'category', id: category.id, parentId: null };
                    this.clDeleteMessage = `Delete "${category.name}" and all its items?`;
                    this.clShowDeleteModal = true;
                },

                clOpenAddItem(category) {
                    this.clItemForm = { id: null, category_id: category.id, name: '', quantity: '1' };
                    this.clShowItemModal = true;
                    if (!this.clExpandedCategories.includes(category.id)) {
                        this.clExpandedCategories.push(category.id);
                    }
                },

                clOpenEditItem(category, item) {
                    this.clItemForm = { id: item.id, category_id: category.id, name: item.name, quantity: item.quantity || '1' };
                    this.clShowItemModal = true;
                },

                async clSaveItem() {
                    if (!this.clItemForm.name.trim() || this.clSubmitting) return;
                    this.clSubmitting = true;
                    const isEdit = !!this.clItemForm.id;
                    const url = isEdit
                        ? `/manager/checklist/items/${this.clItemForm.id}`
                        : `/manager/checklist/categories/${this.clItemForm.category_id}/items`;
                    try {
                        const res = await fetch(url, {
                            method: isEdit ? 'PUT' : 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ name: this.clItemForm.name, quantity: this.clItemForm.quantity })
                        });
                        const data = await res.json();
                        if (res.ok) {
                            const cat = this.clFormData.categories.find(c => c.id === this.clItemForm.category_id);
                            if (cat) {
                                cat.items = cat.items || [];
                                if (isEdit) {
                                    const idx = cat.items.findIndex(i => i.id === this.clItemForm.id);
                                    if (idx > -1) cat.items[idx] = data.item;
                                } else {
                                    cat.items.push(data.item);
                                }
                            }
                            const tpl = this.clCurrentTemplate();
                            if (tpl) this.clRefreshTemplate(tpl.id);
                            this.clShowItemModal = false;
                            this.showToast(isEdit ? 'Item updated' : 'Item added');
                        } else {
                            this.showToast(data.message || 'Failed to save item', 'error');
                        }
                    } catch (e) { this.showToast('Failed to save item', 'error'); }
                    this.clSubmitting = false;
                },

                clConfirmDeleteItem(category, item) {
                    this.clDeleteTarget = { type: 'item', id: item.id, parentId: category.id };
                    this.clDeleteMessage = `Delete "${item.name}"?`;
                    this.clShowDeleteModal = true;
                },

                async clExecuteDelete() {
                    if (this.clSubmitting) return;
                    const { type, id, parentId } = this.clDeleteTarget;
                    let url = '';
                    if (type === 'template') url = `/manager/checklist/${id}`;
                    else if (type === 'category') url = `/manager/checklist/categories/${id}`;
                    else if (type === 'item') url = `/manager/checklist/items/${id}`;
                    else return;

                    this.clSubmitting = true;
                    try {
                        const res = await fetch(url, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            if (type === 'template') {
                                this.clTemplates = this.clTemplates.filter(t => t.id !== id);
                                if (this.clEditingId === id) this.clCloseModal();
                            } else if (type === 'category') {
                                this.clFormData.categories = this.clFormData.categories.filter(c => c.id !== id);
                                const tpl = this.clCurrentTemplate();
                                if (tpl) {
                                    tpl.categories = this.clFormData.categories;
                                    this.clRefreshTemplate(tpl.id);
                                }
                            } else if (type === 'item') {
                                const cat = this.clFormData.categories.find(c => c.id === parentId);
                                if (cat) cat.items = (cat.items || []).filter(i => i.id !== id);
                                const tpl = this.clCurrentTemplate();
                                if (tpl) this.clRefreshTemplate(tpl.id);
                            }
                            this.clShowDeleteModal = false;
                            this.showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} deleted`);
                        } else {
                            this.showToast('Failed to delete', 'error');
                        }
                    } catch (e) { this.showToast('Failed to delete', 'error'); }
                    this.clSubmitting = false;
                },

                clCloseModal() {
                    this.clShowAddModal = false;
                    this.clShowEditModal = false;
                    this.clShowAddCategoryModal = false;
                    this.clShowEditCategoryModal = false;
                    this.clShowItemModal = false;
                    this.clShowDeleteModal = false;
                    this.clEditingId = null;
                    this.clExpandedCategories = [];
                    this.clFormData = { name: '', reminders: '', categories: [] };
                    this.clCategoryForm = { name: '' };
                    this.clEditCategoryForm = { id: null, name: '' };
                    this.clItemForm = { id: null, category_id: null, name: '', quantity: '1' };
                    this.clDeleteTarget = { type: null, id: null, parentId: null };
                    this.clDeleteMessage = '';
                }
            };
        }
    </script>
    @endpush
</x-layouts.general-manager>

<style>
[x-cloak] {
    display: none !important;
}
</style>
