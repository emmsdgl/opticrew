
@props([
    'label' => 'Select Date',
    'name' => 'date',
    'required' => false,
    'xModel' => null,
    'minDate' => null,
    'maxDate' => null,
    'placeholder' => 'Select date',
])

<div class="space-y-3" 
     x-data="datePicker({
        name: '{{ $name }}',
        minDate: '{{ $minDate }}',
        maxDate: '{{ $maxDate }}',
        xModel: '{{ $xModel }}'
     })"
     x-init="init()">
    
    <!-- Label -->
    <label class="block text-sm text-gray-700 dark:text-gray-300">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <!-- Date Input Trigger -->
    <div class="relative">
        <button
            type="button"
            @click="open = !open"
            class="w-full px-4 py-3 border-2 rounded-xl transition-all duration-300 bg-white dark:bg-gray-800
                   border-gray-300 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500
                   focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-600 text-left"
            :class="{ 'border-blue-600 bg-blue-50 dark:bg-blue-900/20': open }"
        >
            <div class="flex items-center gap-4">
                <i class="fa-solid fa-calendar text-[#081032] dark:text-blue-400"></i>
                <span class="text-gray-400 italic text-base dark:text-gray-500" x-text="selectedDate ? formatDisplayDate(selectedDate) : '{{ $placeholder }}'"></span>
            </div>
        </button>

        <!-- Hidden Input -->
        <input 
            type="hidden" 
            :name="name" 
            :value="selectedDate"
            {{ $xModel ? "x-model=\"$xModel\"" : '' }}
        >

        <!-- Calendar Dropdown -->
        <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="absolute z-50 mt-2 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-2xl shadow-xl overflow-hidden w-full sm:w-80"
            style="display: none;"
        >
            <div class="p-4">
                <!-- Month/Year Header -->
                <div class="flex items-center justify-between mb-4">
                    <button
                        type="button"
                        @click="previousMonth()"
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors"
                    >
                        <i class="fa-solid fa-chevron-left text-gray-600 dark:text-gray-300"></i>
                    </button>
                    
                    <div class="text-center">
                        <h3 class="text-gray-900 dark:text-white" x-text="getMonthYear()"></h3>
                    </div>
                    
                    <button
                        type="button"
                        @click="nextMonth()"
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors"
                    >
                        <i class="fa-solid fa-chevron-right text-gray-600 dark:text-gray-300"></i>
                    </button>
                </div>

                <!-- Days of Week -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    <template x-for="day in ['M', 'T', 'W', 'T', 'F', 'S', 'S']" :key="day">
                        <div class="text-center text-xs font-medium text-gray-500 dark:text-gray-400 py-2" x-text="day"></div>
                    </template>
                </div>

                <!-- Calendar Days -->
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="blank in startingDayOfWeek" :key="'blank-' + blank">
                        <div class="aspect-square"></div>
                    </template>
                    
                    <template x-for="day in daysInMonth" :key="day">
                        <button
                            type="button"
                            @click="selectDate(day)"
                            class="aspect-square flex items-center justify-center rounded-lg text-sm font-medium transition-all duration-200"
                            :class="{
                                'bg-blue-600 text-white hover:bg-blue-700': isSelectedDay(day),
                                'text-gray-300 dark:text-gray-600 cursor-not-allowed': isDisabledDay(day),
                                'hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300': !isSelectedDay(day) && !isDisabledDay(day),
                                'font-bold': isToday(day) && !isSelectedDay(day)
                            }"
                            :disabled="isDisabledDay(day)"
                            x-text="day"
                        ></button>
                    </template>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button
                        type="button"
                        @click="clearDate()"
                        class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 
                               text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors"
                    >
                        Remove
                    </button>
                    <button
                        type="button"
                        @click="confirmDate()"
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg 
                               transition-colors"
                    >
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function datePicker(config) {
    return {
        open: false,
        selectedDate: null,
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        name: config.name || 'date',
        minDate: config.minDate || null,
        maxDate: config.maxDate || null,
        
        init() {
            // Initialize to today's date if needed
            const today = new Date();
            this.currentMonth = today.getMonth();
            this.currentYear = today.getFullYear();
        },
        
        get daysInMonth() {
            return new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
        },
        
        get startingDayOfWeek() {
            const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
            // Convert Sunday (0) to 7, then subtract 1 to make Monday = 0
            return firstDay === 0 ? 6 : firstDay - 1;
        },
        
        getMonthYear() {
            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            return `${monthNames[this.currentMonth]} ${this.currentYear}`;
        },
        
        previousMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
        },
        
        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
        },
        
        selectDate(day) {
            const date = new Date(this.currentYear, this.currentMonth, day);
            if (!this.isDisabledDay(day)) {
                this.selectedDate = this.formatDate(date);
            }
        },
        
        confirmDate() {
            this.open = false;
        },
        
        clearDate() {
            this.selectedDate = null;
            this.open = false;
        },
        
        formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        
        formatDisplayDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const monthNames = [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ];
            return `${date.getDate()} ${monthNames[date.getMonth()]} ${date.getFullYear()}`;
        },
        
        isSelectedDay(day) {
            if (!this.selectedDate) return false;
            const selected = new Date(this.selectedDate);
            return selected.getDate() === day && 
                   selected.getMonth() === this.currentMonth && 
                   selected.getFullYear() === this.currentYear;
        },
        
        isToday(day) {
            const today = new Date();
            return today.getDate() === day && 
                   today.getMonth() === this.currentMonth && 
                   today.getFullYear() === this.currentYear;
        },
        
        isDisabledDay(day) {
            const date = new Date(this.currentYear, this.currentMonth, day);
            const dateString = this.formatDate(date);
            
            if (this.minDate && dateString < this.minDate) return true;
            if (this.maxDate && dateString > this.maxDate) return true;
            
            return false;
        }
    }
}
</script>
@endpush
@endonce