@props(['clients', 'events', 'bookedLocationsByDate', 'holidays'])

<style>
    /* Prevent modal flash on page load */
    [x-cloak] {
        display: none !important;
    }

    /* Toast Notification Styles */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        max-width: 500px;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        animation: slideInRight 0.3s ease-out;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toast-notification.success {
        background: #10b981;
        color: white;
    }

    .toast-notification.error {
        background: #ef4444;
        color: white;
    }

    .toast-notification.warning {
        background: #f59e0b;
        color: white;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .toast-notification.hiding {
        animation: slideOutRight 0.3s ease-in;
    }
</style>

<div x-data="calendarComponent(@js($clients), @js($events), @js($bookedLocationsByDate), @js($holidays))" x-init="init()"
    class="w-full h-full bg-gray-50 dark:bg-gray-900 rounded-lg p-4 transition-colors duration-300">

    <!-- Calendar Header -->
    <div class="flex flex-row justify-between items-center mb-4 w-full">
        <h2 class="text-base justify-start font-bold font-sans text-gray-800 dark:text-gray-100" x-text="monthYear"></h2>
        <div class="flex flex-row gap-6">
            <div class="flex flex-row gap-3">
                <button @click="saveSchedule()"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-full transition inline-flex items-center gap-2"
                    x-bind:disabled="!hasUnsavedSchedule"
                    x-bind:class="{'opacity-50 cursor-not-allowed': !hasUnsavedSchedule}">
                    <i class="fa-solid fa-save"></i>
                    <span x-text="hasUnsavedSchedule ? 'Save Schedule' : 'No Unsaved Schedule'"></span>
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
        <template x-for="(date, index) in dates" :key="`${currentYear}-${currentMonth}-${index}`">
            <div @click="date.date ? openModal(date) : null"
                :class="{
                    'bg-blue-100 dark:bg-blue-900/30 border-blue-400 dark:border-blue-600': date.date && isToday(date),
                    'bg-gray-100 dark:bg-gray-800 opacity-60': date.date && isPastDate(date) && !isToday(date),
                    'cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900': date.date && (!isPastDate(date) || isToday(date)),
                    'cursor-not-allowed': date.date && isPastDate(date) && !isToday(date),
                    'bg-gray-50 dark:bg-gray-900': !date.date
                }"
                class="h-32 p-2 border border-gray-200 dark:border-gray-700 transition relative">

                <!-- Date Number with Today Indicator -->
                <template x-if="date.date">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            <span class="text-xs font-semibold"
                                :class="{
                                    'text-blue-600 dark:text-blue-400': isToday(date),
                                    'text-gray-400 dark:text-gray-500': isPastDate(date) && !isToday(date),
                                    'text-gray-700 dark:text-gray-200': !isPastDate(date) && !isToday(date)
                                }"
                                x-text="date.date"></span>

                            <!-- Holiday Badge -->
                            <template x-if="date.holiday">
                                <span class="text-xs font-semibold bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 px-1.5 py-0.5 rounded-full flex items-center gap-1" :title="date.holiday.name">
                                    <i class="fa-solid fa-star text-xs"></i>
                                </span>
                            </template>
                        </div>

                        <div class="flex items-center gap-1">
                            <!-- TODAY Badge -->
                            <template x-if="isToday(date)">
                                <span class="text-xs font-bold bg-blue-600 dark:bg-blue-500 text-white px-2 py-0.5 rounded-full">
                                    TODAY
                                </span>
                            </template>

                            <!-- Holiday Management Button -->
                            <button @click.stop="handleHolidayClick(date)"
                                class="text-xs p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                                :class="date.holiday ? 'text-red-500' : 'text-gray-400 dark:text-gray-500'"
                                :title="date.holiday ? 'Manage Holiday' : 'Add Holiday'">
                                <i class="fa-solid fa-calendar-check"></i>
                            </button>
                        </div>
                    </div>
                </template>

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
                                        <option value="">Select Cabin Type</option>
                                        <option value="Arrival">Arrival (High Priority)</option>
                                        <option value="Departure">Departure</option>
                                        <option value="Daily Clean">Daily Clean</option>
                                    </select>

                                    <div class="mt-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/40 max-h-32 overflow-y-auto">
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="cabin in formData.availableCabins" :key="cabin.id">
                                                <button type="button"
                                                    @click="toggleCabin(cabin.name)"
                                                    :class="{
                                                        'bg-blue-600 text-white': isCabinSelected(cabin.name),
                                                        'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300': !isCabinSelected(cabin.name) && !isCabinAlreadyTaken(cabin.name),
                                                        'bg-green-600 text-white': isCabinAlreadyTaken(cabin.name) && !isCabinSelected(cabin.name)
                                                    }"
                                                    class="px-3 py-1 rounded-full text-sm transition-colors duration-200">
                                                    <span x-text="cabin.name"></span>
                                                    <i x-show="isCabinAlreadyTaken(cabin.name)" class="fas fa-check ml-1"></i>
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
                                <div class="bg-white dark:bg-gray-800 rounded-lg px-3 py-2 text-sm">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-semibold text-gray-900 dark:text-white" x-text="item.cabin"></span>
                                        <button type="button" @click="removeCabin(index)"
                                            class="text-red-500 hover:text-red-700 text-sm">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                        <span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300" x-text="item.serviceType"></span>
                                        <span class="px-2 py-0.5 rounded"
                                            :class="{
                                                'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300': item.cabinType === 'Arrival',
                                                'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300': item.cabinType === 'Departure',
                                                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300': item.cabinType === 'Daily Clean'
                                            }"
                                            x-text="item.cabinType"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Extra Tasks -->
                        <div class="space-y-3 text-sm">
                            <div class="flex flex-row w-full mb-4 justify-between">
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
                                <div class="bg-white dark:bg-gray-800 rounded-lg px-4 py-3 space-y-3 border border-gray-200 dark:border-gray-700">
                                    <!-- Task Name -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Task Name</label>
                                        <input type="text" x-model="task.type" placeholder="e.g., Extra beddings, Pool cleaning" required
                                            class="w-full px-3 py-2 text-sm rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>

                                    <!-- Base Price & Duration Row -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <!-- Base Price -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Base Price (€)</label>
                                            <input type="number" x-model="task.basePrice" @input="calculateExtraTaskFinalPrice(task)" placeholder="Base price" min="0" step="0.01" required
                                                class="w-full px-3 py-2 text-sm rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>

                                        <!-- Duration -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Duration</label>
                                            <select x-model="task.duration" required
                                                class="w-full px-3 py-2 text-sm rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                                <option value="">Select</option>
                                                <option value="30">30 mins</option>
                                                <option value="60">60 mins</option>
                                                <option value="150">150 mins</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Final Price Display & Remove Button -->
                                    <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Final Price: </span>
                                            <span class="text-sm font-bold" :class="task.isSpecialDay ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white'">
                                                €<span x-text="(task.finalPrice || 0).toFixed(2)"></span>
                                            </span>
                                            <span x-show="task.isSpecialDay" class="text-xs text-orange-600 dark:text-orange-400 ml-1">
                                                (Doubled for <span x-text="task.specialDayType"></span>)
                                            </span>
                                        </div>
                                        <button type="button" @click="removeExtraTask(index)" class="px-2 py-1 text-red-500 hover:text-red-700 text-sm">
                                            <i class="fa-solid fa-trash"></i>
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
                        
                        <!-- Title: Client Name - Cabin Type -->
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                <span x-text="event.client"></span>
                                <span class="text-gray-500" x-show="event.cabinType"> - </span>
                                <span x-text="event.cabinType"></span>
                            </h4>
                            <div class="flex items-center gap-2">
                                <!-- URGENT Badge for Arrival Tasks -->
                                <span x-show="event.arrival_status === true"
                                    class="text-xs px-2 py-1 rounded-full bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-exclamation-triangle"></i>
                                    URGENT
                                </span>
                                <!-- Status Badge -->
                                <span class="text-xs px-2 py-1 rounded-full"
                                    :class="event.statusColor"
                                    x-text="event.status"></span>
                            </div>
                        </div>

                        <!-- Task Details -->
                        <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                            <!-- Assigned Team -->
                            <div x-show="event.employees && event.employees.length > 0">
                                <div class="font-semibold text-xs text-gray-500 dark:text-gray-400 mb-1">Assigned Team:</div>
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="(employee, empIdx) in event.employees" :key="empIdx">
                                        <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 rounded text-xs font-medium"
                                            x-text="employee"></span>
                                    </template>
                                </div>
                            </div>

                            <!-- No Team Assigned -->
                            <div x-show="!event.employees || event.employees.length === 0"
                                class="text-xs text-gray-500 dark:text-gray-400 italic">
                                <i class="fa-solid fa-exclamation-circle mr-1"></i>
                                No team assigned yet
                            </div>

                            <!-- Location -->
                            <div x-show="event.location">
                                <div class="font-semibold text-xs text-gray-500 dark:text-gray-400 mb-1">Location:</div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-home text-gray-400"></i>
                                    <span x-text="event.location"></span>
                                </div>
                            </div>

                            <!-- Service Type -->
                            <div>
                                <div class="font-semibold text-xs text-gray-500 dark:text-gray-400 mb-1">Service Type:</div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-broom text-gray-400"></i>
                                    <span x-text="event.serviceType"></span>
                                </div>
                            </div>
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

    <!-- Add Holiday Modal -->
    <div x-show="showAddHolidayModal" x-cloak x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Add Holiday</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                Do you want to add a holiday to <span class="font-semibold" x-text="selectedHolidayDate"></span>?
            </p>
            <div class="flex gap-3">
                <button @click="promptHolidayName()"
                    class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    Yes
                </button>
                <button @click="closeHolidayModals()"
                    class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition">
                    No
                </button>
            </div>
        </div>
    </div>

    <!-- Holiday Name Input Modal -->
    <div x-show="showHolidayNameModal" x-cloak x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Holiday Name</h2>
            <input type="text" x-model="holidayName" placeholder="Enter holiday name"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white mb-4"
                @keydown.enter="confirmAddHoliday()">
            <div class="flex gap-3">
                <button @click="confirmAddHoliday()"
                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Confirm
                </button>
                <button @click="closeHolidayModals()"
                    class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Holiday Modal -->
    <div x-show="showDeleteHolidayModal" x-cloak x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Delete Holiday</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                Do you want to delete holiday "<span class="font-semibold" x-text="selectedHolidayName"></span>"?
            </p>
            <div class="flex gap-3">
                <button @click="confirmDeleteHoliday()"
                    class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    Confirm
                </button>
                <button @click="closeHolidayModals()"
                    class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function calendarComponent(initialClients, initialEvents, bookedLocationsByDate, initialHolidays) { // 1. Accept new data
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
        bookedLocationsByDate: bookedLocationsByDate || {}, // 2. Store the data
        hasUnsavedSchedule: false, // Track if there's an unsaved optimization run
        currentOptimizationRunId: null, // Store the current run ID (backward compatibility)
        currentServiceDate: null, // Store the service date with unsaved schedules

        // Holiday management
        holidays: initialHolidays || {}, // Store holidays by date key
        showAddHolidayModal: false,
        showHolidayNameModal: false,
        showDeleteHolidayModal: false,
        selectedHolidayDate: null,
        selectedHolidayId: null,
        selectedHolidayName: null,
        holidayName: '',

        formData: {
            client: '',
            clientLabel: '',
            serviceDate: '',
            serviceType: '',
            rateType: 'Normal',
            selectedCabinType: '',
            cabinsList: [], // Stores: { cabin, serviceType, cabinType }
            extraTasks: [],
            availableCabins: []
        },

        async init() {
            this.renderCalendar();
            // Check for unsaved schedules on page load
            await this.checkForUnsavedSchedule();
        },

        async checkForUnsavedSchedule() {
            try {
                const response = await fetch('/admin/optimization/check-unsaved', {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    console.error('Failed to check unsaved schedule:', response.status);
                    return;
                }

                const data = await response.json();

                console.log('Check unsaved schedule response:', data);

                if (data.has_unsaved) {
                    this.currentOptimizationRunId = data.optimization_run_id;
                    this.currentServiceDate = data.service_date; // ✅ Store service date
                    this.hasUnsavedSchedule = true;
                    console.log('✅ Found unsaved schedule:', {
                        run_id: data.optimization_run_id,
                        service_date: data.service_date,
                        created_at: data.created_at
                    });
                } else {
                    this.hasUnsavedSchedule = false;
                    this.currentOptimizationRunId = null;
                    this.currentServiceDate = null;
                    console.log('No unsaved schedules found');
                }
            } catch (error) {
                console.error('Error checking for unsaved schedule:', error);
                this.showToast('Failed to check for unsaved schedules', 'error');
            }
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

            // Debug logging
            console.log(`Rendering calendar for: ${this.monthYear} (Month: ${this.currentMonth}, Year: ${this.currentYear})`);
            const today = new Date();
            console.log(`Today is: ${today.getDate()}/${today.getMonth() + 1}/${today.getFullYear()}`);

            for (let i = 0; i < startDay; i++) {
                this.dates.push({ date: '', events: [], groupedEvents: [] });
            }

            for (let d = 1; d <= totalDays; d++) {
                const key = `${this.currentYear}-${this.currentMonth + 1}-${d}`;
                const dayEvents = this.events[key] || [];
                const holiday = this.holidays[key] || null;
                this.dates.push({
                    date: d,
                    events: dayEvents,
                    groupedEvents: this.groupEventsByClient(dayEvents),
                    holiday: holiday // Add holiday data to each date
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

        // Check if a date is today
        isToday(date) {
            if (!date || !date.date) return false;
            const today = new Date();
            const todayDay = today.getDate();
            const todayMonth = today.getMonth();
            const todayYear = today.getFullYear();

            // Compare using calendar's displayed month/year and the date number
            return date.date === todayDay &&
                   this.currentMonth === todayMonth &&
                   this.currentYear === todayYear;
        },

        // Check if a date is in the past
        isPastDate(date) {
            if (!date || !date.date) return false;

            // Get today's date and reset time
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Create a date object for the calendar cell we're checking
            // Use the DISPLAYED calendar's month/year, not today's
            const checkDate = new Date(this.currentYear, this.currentMonth, date.date);
            checkDate.setHours(0, 0, 0, 0);

            return checkDate < today;
        },

        openModal(date) {
            // Prevent opening modal for empty calendar cells
            if (!date || !date.date) {
                return;
            }

            const ENABLE_PAST_DATE_VALIDATION = true;

            // Check if trying to select a past date
            if (ENABLE_PAST_DATE_VALIDATION && this.isPastDate(date)) {
                alert('⚠️ You cannot create tasks for previous dates.\n\nPlease select today or a future date.');
                return; // Stop execution - do not open modal
            }

            // Reset form first
            this.resetForm();

            // Then set the service date
            const dateString = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(date.date).padStart(2, '0')}`;
            this.selectedDate = dateString;
            this.formData.serviceDate = dateString;

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
            
            this.formData.cabinsList = []; // Clear any previously selected cabins

            if (selectedClient) {
                this.formData.clientLabel = selectedClient.label;
                
                // Get all locations for the selected client
                const allClientLocations = selectedClient.locations || [];
                
                // --- THIS IS THE KEY LOGIC ---
                // Get the list of booked location IDs for the currently selected date
                const bookedIdsForDate = this.bookedLocationsByDate[this.selectedDate] || [];
                
                // Filter the client's locations, keeping only those NOT in the booked list
                this.formData.availableCabins = allClientLocations.filter(location => {
                    return !bookedIdsForDate.includes(location.id);
                });
                
            } else {
                this.formData.clientLabel = '';
                this.formData.availableCabins = [];
            }
        },

        toggleCabin(cabin) {
            // Validate that service type and cabin type are selected
            if (!this.formData.serviceType) {
                alert('Please select a Service Type first');
                return;
            }
            if (!this.formData.selectedCabinType) {
                alert('Please select a Cabin Type first');
                return;
            }

            // Check if cabin already in list with current combination
            const existingIndex = this.formData.cabinsList.findIndex(
                item => item.cabin === cabin &&
                        item.serviceType === this.formData.serviceType &&
                        item.cabinType === this.formData.selectedCabinType
            );

            if (existingIndex > -1) {
                // Remove if already selected with same combination
                this.formData.cabinsList.splice(existingIndex, 1);
            } else {
                // Remove any existing entry for this cabin (to allow changing service type)
                const oldIndex = this.formData.cabinsList.findIndex(item => item.cabin === cabin);
                if (oldIndex > -1) {
                    this.formData.cabinsList.splice(oldIndex, 1);
                }

                // Add with current service type + cabin type
                this.formData.cabinsList.push({
                    cabin: cabin,
                    serviceType: this.formData.serviceType,
                    cabinType: this.formData.selectedCabinType
                });
            }
        },

        isCabinSelected(cabin) {
            return this.formData.cabinsList.some(
                item => item.cabin === cabin &&
                        item.serviceType === this.formData.serviceType &&
                        item.cabinType === this.formData.selectedCabinType
            );
        },

        isCabinAlreadyTaken(cabin) {
            return this.formData.cabinsList.some(item => item.cabin === cabin);
        },

        removeCabin(index) {
            this.formData.cabinsList.splice(index, 1);
        },

        addExtraTask() {
            const newTask = {
                type: '',
                basePrice: 0,
                finalPrice: 0,
                duration: '',
                isSpecialDay: false,
                specialDayType: '',
                price: 0 // Keep for backward compatibility
            };

            // Calculate price based on current service date
            this.calculateExtraTaskFinalPrice(newTask);

            this.formData.extraTasks.push(newTask);
        },

        removeExtraTask(index) {
            this.formData.extraTasks.splice(index, 1);
        },

        calculateExtraTaskFinalPrice(task) {
            // Check if service date is selected
            if (!this.formData.serviceDate) {
                task.isSpecialDay = false;
                task.specialDayType = '';
                task.finalPrice = parseFloat(task.basePrice) || 0;
                task.price = task.finalPrice; // For backward compatibility
                return;
            }

            const selectedDate = new Date(this.formData.serviceDate);
            const dayOfWeek = selectedDate.getDay();

            // Check if Sunday
            const isSunday = dayOfWeek === 0;

            // Check if Holiday
            const isHoliday = this.holidays[this.formData.serviceDate] !== undefined;

            // Determine if special day
            task.isSpecialDay = isSunday || isHoliday;

            if (isSunday) {
                task.specialDayType = 'Sunday';
            } else if (isHoliday) {
                task.specialDayType = 'Holiday';
            } else {
                task.specialDayType = '';
            }

            // Calculate final price
            const basePrice = parseFloat(task.basePrice) || 0;
            task.finalPrice = task.isSpecialDay ? basePrice * 2 : basePrice;
            task.price = task.finalPrice; // For backward compatibility
        },

        calculateTotalCabins() {
            return this.formData.cabinsList.length;
        },

        // Toast Notification System
        showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;

            const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : '⚠️';
            toast.innerHTML = `
                <span style="font-size: 20px;">${icon}</span>
                <span style="flex: 1;">${message}</span>
            `;

            document.body.appendChild(toast);

            // Auto remove after 4 seconds
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 4000);
        },

        async submitTask() {
            // Validate BEFORE doing anything
            console.log('Form validation check:', {
                client: this.formData.client,
                serviceDate: this.formData.serviceDate,
                cabinsList: this.formData.cabinsList
            });

            if (!this.formData.client) {
                this.showToast('Please select a client', 'error');
                return;
            }

            if (!this.formData.serviceDate) {
                this.showToast('Please select a service date', 'error');
                return;
            }

            // Validate that either cabins OR extra tasks are provided (not both required)
            if (this.calculateTotalCabins() === 0 && this.formData.extraTasks.length === 0) {
                this.showToast('Please select at least one cabin with service details OR add at least one extra task', 'error');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                            document.querySelector('input[name="_token"]')?.value || '';

            if (!csrfToken) {
                this.showToast('CSRF token not found. Please refresh the page.', 'error');
                return;
            }

            // ✅ SAVE form data BEFORE closing modal and showing optimization
            const requestData = {
                client: this.formData.client,
                serviceDate: this.formData.serviceDate,
                rateType: this.formData.rateType,
                cabinsList: JSON.parse(JSON.stringify(this.formData.cabinsList)), // Deep copy - now includes { cabin, serviceType, cabinType }
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
                        // Store the optimization run ID and service date, mark as unsaved
                        this.currentOptimizationRunId = data.optimization_run_id;
                        this.currentServiceDate = this.formData.serviceDate; // ✅ Store service date
                        this.hasUnsavedSchedule = true;

                        await this.loadOptimizationResults(data.optimization_run_id);

                        // Show success message and close modal
                        this.optimizationLoading = false;
                        this.closeOptimizationModal();
                        alert('Tasks created and optimized successfully! Please click "Save Schedule" to save this optimization.');

                        // Reload the page to show new tasks on calendar
                        window.location.reload();
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

        async saveSchedule() {
            console.log('Save schedule clicked. State:', {
                hasUnsavedSchedule: this.hasUnsavedSchedule,
                currentServiceDate: this.currentServiceDate,
                currentOptimizationRunId: this.currentOptimizationRunId
            });

            if (!this.hasUnsavedSchedule || !this.currentServiceDate) {
                this.showToast('No unsaved schedule to save. Please create and optimize tasks first.', 'warning');
                return;
            }

            if (!confirm('Are you sure you want to save ALL schedules for ' + this.currentServiceDate + '? This will lock all teams for that day and enable real-time task addition.')) {
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                console.log('Sending save request for date:', this.currentServiceDate);

                const response = await fetch('/admin/optimization/save-schedule', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        service_date: this.currentServiceDate // ✅ Send service_date instead of run_id
                    })
                });

                const data = await response.json();

                console.log('Save schedule response:', { status: response.status, data });

                if (response.ok) {
                    this.hasUnsavedSchedule = false;
                    this.currentOptimizationRunId = null;
                    this.currentServiceDate = null;

                    // Show detailed success message
                    const runCount = data.runs_saved || 1;
                    const clientText = runCount > 1 ? `${runCount} clients` : '1 client';

                    this.showToast(`All schedules saved successfully! (${clientText}) Teams are now locked for ${data.service_date}.`, 'success');

                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    this.showToast('Error: ' + (data.message || 'Unknown error'), 'error');
                    console.error('Save failed:', data);
                }
            } catch (error) {
                console.error('Error saving schedule:', error);
                this.showToast('Error saving schedule. Please try again.', 'error');
            }
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
                
                return {
                    client: clientName,
                    cabinType: event.cabinType || 'N/A',
                    title: event.title,
                    serviceType: event.pureServiceType,
                    status: event.status || 'Pending',
                    statusColor: this.getStatusColor(event.status),
                    location: event.location || 'N/A',
                    employees: event.employees || [], // ✅ Include employee data
                    team_id: event.team_id || null
                };
            });
            
            console.log('Event details:', this.eventDetailsList); // ✅ Debug
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
        },

        // Holiday Management Methods
        handleHolidayClick(date) {
            if (!date || !date.date) return;

            const dateString = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(date.date).padStart(2, '0')}`;
            this.selectedHolidayDate = new Date(this.currentYear, this.currentMonth, date.date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            if (date.holiday) {
                // Holiday exists - show delete confirmation
                this.selectedHolidayId = date.holiday.id;
                this.selectedHolidayName = date.holiday.name;
                this.showDeleteHolidayModal = true;
            } else {
                // No holiday - show add confirmation
                this.showAddHolidayModal = true;
            }
        },

        promptHolidayName() {
            this.showAddHolidayModal = false;
            this.holidayName = '';
            this.showHolidayNameModal = true;
        },

        async confirmAddHoliday() {
            if (!this.holidayName.trim()) {
                alert('Please enter a holiday name');
                return;
            }

            try {
                const dateString = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(this.dates.find(d => {
                    const ds = new Date(this.currentYear, this.currentMonth, d.date).toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    return ds === this.selectedHolidayDate;
                })?.date).padStart(2, '0')}`;

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                const response = await fetch('/admin/holidays', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        date: dateString,
                        name: this.holidayName
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // Add holiday to local data
                    const key = `${this.currentYear}-${this.currentMonth + 1}-${parseInt(dateString.split('-')[2])}`;
                    this.holidays[key] = data.holiday;

                    // Refresh calendar to show the holiday
                    this.renderCalendar();

                    alert('✅ Holiday added successfully!');
                    this.closeHolidayModals();
                } else {
                    alert('❌ Error: ' + (data.message || 'Failed to add holiday'));
                }
            } catch (error) {
                console.error('Error adding holiday:', error);
                alert('❌ Error adding holiday. Please try again.');
            }
        },

        async confirmDeleteHoliday() {
            if (!this.selectedHolidayId) return;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                const response = await fetch(`/admin/holidays/${this.selectedHolidayId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Remove holiday from local data
                    Object.keys(this.holidays).forEach(key => {
                        if (this.holidays[key]?.id === this.selectedHolidayId) {
                            delete this.holidays[key];
                        }
                    });

                    // Refresh calendar to hide the holiday
                    this.renderCalendar();

                    alert('✅ Holiday deleted successfully!');
                    this.closeHolidayModals();
                } else {
                    alert('❌ Error: ' + (data.message || 'Failed to delete holiday'));
                }
            } catch (error) {
                console.error('Error deleting holiday:', error);
                alert('❌ Error deleting holiday. Please try again.');
            }
        },

        closeHolidayModals() {
            this.showAddHolidayModal = false;
            this.showHolidayNameModal = false;
            this.showDeleteHolidayModal = false;
            this.selectedHolidayDate = null;
            this.selectedHolidayId = null;
            this.selectedHolidayName = null;
            this.holidayName = '';
        }
    }
}
</script>
@endpush