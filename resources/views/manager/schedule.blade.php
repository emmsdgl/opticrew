<x-layouts.general-manager :title="'Schedule'">
    <div class="flex flex-col gap-6 w-full" x-data="scheduleManager()" x-init="init()" @keydown.escape.window="showCreateModal = false; showTaskModal = false; showLocationPicker = false; showEmployeePicker = false">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Schedule</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your tasks and assignments</p>
            </div>
            <div class="flex gap-3">
                <button @click="runOptimization()"
                        :disabled="optimizing"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium disabled:opacity-50">
                    <i :class="optimizing ? 'fa-solid fa-spinner animate-spin' : 'fa-solid fa-wand-magic-sparkles'"></i>
                    <span x-text="optimizing ? 'Optimizing...' : 'Optimize Schedule'"></span>
                </button>
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
                <div x-show="loading" class="flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>

                <div x-show="!loading && tasks.length > 0" class="space-y-3">
                    <template x-for="task in tasks" :key="task.id">
                        <div class="flex items-center gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                             :class="{ 'ring-2 ring-red-400': task.employee_approved === false, 'ring-2 ring-amber-400': task.employee_approved !== false && task.arrival_status && task.arrival_status !== 0 }"
                             @click="openTaskDetails(task)">
                            <div class="flex-shrink-0">
                                <div class="w-3 h-3 rounded-full"
                                     :class="{
                                         'bg-green-500': task.status === 'Completed',
                                         'bg-blue-500 animate-pulse': task.status === 'In Progress',
                                         'bg-yellow-500': task.status === 'On Hold',
                                         'bg-red-500': task.status === 'Cancelled',
                                         'bg-gray-400': task.status === 'Scheduled' || task.status === 'Pending'
                                     }"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="task.location_name"></p>
                                    <span x-show="task.assigned_team_id"
                                          class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold"
                                          :class="teamBadgeClass(task.assigned_team_id)"
                                          x-text="'Team ' + task.assigned_team_id"></span>
                                    <span x-show="task.employee_approved === false"
                                          class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">DECLINED</span>
                                    <span x-show="task.arrival_status && task.arrival_status !== 0 && task.arrival_status !== '0'"
                                          class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">ARRIVAL</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{-- ✅ STAGE 2: prefer GA-computed start–end window over the static scheduled_time --}}
                                    <template x-if="task.optimized_start && task.optimized_end">
                                        <span>
                                            <span x-text="task.optimized_start + ' - ' + task.optimized_end"></span>
                                            <span class="mx-1" x-show="task.duration">·</span>
                                            <span x-show="task.duration" x-text="task.duration + ' min'"></span>
                                        </span>
                                    </template>
                                    <template x-if="!(task.optimized_start && task.optimized_end)">
                                        <span>
                                            <span x-text="task.scheduled_time"></span>
                                            <span class="mx-1" x-show="task.duration">-</span>
                                            <span x-show="task.duration" x-text="task.duration + ' min'"></span>
                                        </span>
                                    </template>
                                    <span class="mx-1" x-show="task.cabin_status">-</span>
                                    <span x-show="task.cabin_status" class="capitalize" x-text="(task.cabin_status || '').replace('_', ' ')"></span>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': task.status === 'Completed',
                                          'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': task.status === 'In Progress',
                                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': task.status === 'On Hold',
                                          'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': task.status === 'Cancelled',
                                          'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': task.status === 'Scheduled' || task.status === 'Pending'
                                      }"
                                      x-text="task.status"></span>
                            </div>
                            <div class="flex-shrink-0 flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-user-group"></i>
                                <span x-text="task.employee_count"></span>
                            </div>
                        </div>
                    </template>
                </div>

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
                                    @case('Small') <i class="fa-solid fa-house text-blue-500"></i> @break
                                    @case('Medium') <i class="fa-solid fa-house-chimney text-green-500"></i> @break
                                    @case('Big') <i class="fa-solid fa-building text-purple-500"></i> @break
                                    @case('Queen') <i class="fa-solid fa-crown text-yellow-500"></i> @break
                                    @case('Igloo') <i class="fa-solid fa-igloo text-cyan-500"></i> @break
                                    @default <i class="fa-solid fa-location-dot text-gray-500"></i>
                                @endswitch
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $type }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $count }} locations</p>
                        </div>
                    @endforeach
                </div>
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

        <!-- Task Details Modal -->
        <div x-show="showTaskModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showTaskModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Task Details</h3>
                        <button @click="showTaskModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"><i class="fa-solid fa-xmark text-gray-500"></i></button>
                    </div>
                    <div class="p-4 space-y-4" x-show="selectedTask">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-location-dot text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedTask?.location_name"></p>
                                    <span x-show="selectedTask?.assigned_team_id"
                                          class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold"
                                          :class="teamBadgeClass(selectedTask?.assigned_team_id || 1)"
                                          x-text="'Team ' + selectedTask?.assigned_team_id"></span>
                                </div>
                                <p class="text-xs text-gray-500" x-text="selectedTask?.location_type"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Time</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedTask?.scheduled_time || 'N/A'"></p>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Duration</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="(selectedTask?.duration || 0) + ' min'"></p>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedTask?.status"></p>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Rate Type</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedTask?.rate_type || 'Normal'"></p>
                            </div>
                        </div>

                        <div x-show="selectedTask?.cabin_status" class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Cabin Status</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white capitalize" x-text="(selectedTask?.cabin_status || '').replace('_', ' ')"></p>
                        </div>

                        <div x-show="selectedTask?.task_description" class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Description</p>
                            <p class="text-sm text-gray-900 dark:text-white" x-text="selectedTask?.task_description"></p>
                        </div>

                        <!-- Declined Notice -->
                        <div x-show="selectedTask?.employee_approved === false" class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-xmark text-red-500"></i>
                                <div>
                                    <p class="text-sm font-medium text-red-700 dark:text-red-400">Task Declined</p>
                                    <p class="text-xs text-red-600 dark:text-red-500" x-show="selectedTask?.declined_by" x-text="'Declined by ' + selectedTask?.declined_by"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Employees -->
                        <div x-show="selectedTask?.employees?.length > 0">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assigned Employees</p>
                            <div class="space-y-2">
                                <template x-for="emp in selectedTask?.employees || []" :key="emp.id">
                                    <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-xs font-semibold text-blue-600 dark:text-blue-400"
                                             x-text="emp.name.charAt(0).toUpperCase()"></div>
                                        <span class="text-sm text-gray-900 dark:text-white" x-text="emp.name"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700">
                        <button x-show="selectedTask && (selectedTask.status === 'Scheduled' || selectedTask.status === 'Pending')"
                                @click="cancelTask()"
                                :disabled="submitting"
                                class="px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 disabled:opacity-50">
                            Cancel Task
                        </button>
                        <button @click="showTaskModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg ml-auto">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div x-show="toast.show" x-transition
             class="fixed bottom-4 right-4 z-[70] px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
             x-text="toast.message" style="display: none;"></div>
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

                // Scenario #1: Minimum booking notice (configurable)
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

                init() {
                    this.currentWeekStart = this.getStartOfWeek(new Date());
                    this.generateWeekDays();
                    this.fetchTasks();
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                // ---- Date Navigation ----
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

                // ---- Data Fetching ----
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

                // ---- Create Modal ----
                openCreateModal() {
                    // Use selected date if it meets minimum booking notice, otherwise use earliest allowed
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
                    if (this.taskForm.cabin_status === 'arrival') return; // locked for arrival
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

                // ---- Location Picker ----
                openLocationPicker() {
                    this.fetchLocations();
                    this.showLocationPicker = true;
                },

                toggleLocationGroup(name) {
                    this.expandedGroups[name] = !this.expandedGroups[name];
                },

                getLocationIcon(type) {
                    switch ((type || '').toLowerCase()) {
                        case 'small': return '\u{1F3E0}';
                        case 'medium': return '\u{1F3E1}';
                        case 'big': return '\u{1F3D8}\u{FE0F}';
                        case 'queen': return '\u{1F451}';
                        case 'igloo': return '\u{1F9CA}';
                        default: return '\u{1F4CD}';
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

                // ---- Employee Picker ----
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

                // ---- Extra Task Price ----
                calculateExtraTaskPrice() {
                    const base = parseFloat(this.extraTask.price) || 16;
                    if (this.holidayInfo && this.holidayInfo.is_sunday_or_holiday) {
                        return (base * 1.5).toFixed(2);
                    }
                    return base.toFixed(2);
                },

                // ---- Submit ----
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
                        this.locationsLoaded = false; // refresh occupancy
                        this.fetchTasks();
                        this.showToast(data.message || 'Task(s) created successfully!');
                    } catch (e) {
                        this.submitError = e.message || 'An error occurred';
                    }
                    this.submitting = false;
                },

                // ---- Task Details ----
                openTaskDetails(task) {
                    this.selectedTask = task;
                    this.showTaskModal = true;
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
                            this.fetchTasks();
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
                    try {
                        const res = await fetch('/manager/schedule/optimize', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ date: this.selectedDate })
                        });
                        const data = await res.json();
                        if (res.ok) {
                            this.showToast(data.message || 'Optimization completed!');
                            this.fetchTasks();
                        } else {
                            this.showToast(data.message || 'Optimization failed', 'error');
                        }
                    } catch (e) {
                        this.showToast('Optimization failed', 'error');
                    }
                    this.optimizing = false;
                }
            };
        }
    </script>
    @endpush
</x-layouts.general-manager>
