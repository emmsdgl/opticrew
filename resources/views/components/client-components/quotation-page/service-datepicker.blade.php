
@props([
    'label' => 'Select Date',
    'name' => 'date',
    'required' => false,
    'xModel' => null,
    'minDate' => null,
    'maxDate' => null,
    'placeholder' => 'Select date',
])

<div x-data="datePicker({
        name: '{{ $name }}',
        minDate: '{{ $minDate }}',
        maxDate: '{{ $maxDate }}',
        xModel: '{{ $xModel }}'
     })"
     x-init="init()">

    <!-- Label -->
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <!-- Date Input Trigger -->
    <div class="relative" x-ref="triggerWrap">
        <button
            type="button"
            @click="open = !open; if (open) $nextTick(() => positionCalendar())"
            class="w-full px-3 py-2.5 border rounded-lg transition-all duration-300 bg-white dark:bg-gray-800
                   border-gray-300 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500
                   focus:outline-none focus:ring-2 focus:ring-blue-500 text-left text-sm"
            :class="{ 'border-blue-600 bg-blue-50 dark:bg-blue-900/20': open }"
        >
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500 flex-shrink-0"><path d="M16 2v4"/><path d="M21 11.75V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7.25"/><path d="m22 22-1.875-1.875"/><path d="M3 10h18"/><path d="M8 2v4"/><circle cx="18" cy="18" r="3"/></svg>
                <span :class="selectedDate ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'" x-text="selectedDate ? formatDisplayDate(selectedDate) : '{{ $placeholder }}'"></span>
            </div>
        </button>

        <!-- Hidden Input -->
        <input
            type="hidden"
            :name="name"
            :value="selectedDate"
            @if($xModel)
                x-model="{{ $xModel }}"
            @endif
        >
    </div>

    <!-- Calendar Dropdown (fixed position, floats above modal) -->
    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-ref="calendarPanel"
        class="fixed z-[10000] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-2xl w-64"
        style="display: none;"
    >
        <div class="p-3">
            <!-- Month/Year Header -->
            <div class="flex items-center justify-between mb-2">
                <button type="button" @click="previousMonth()"
                    class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                    <i class="fa-solid fa-chevron-left text-xs text-gray-500 dark:text-gray-400"></i>
                </button>
                <span class="text-xs font-semibold text-gray-900 dark:text-white" x-text="getMonthYear()"></span>
                <button type="button" @click="nextMonth()"
                    class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                    <i class="fa-solid fa-chevron-right text-xs text-gray-500 dark:text-gray-400"></i>
                </button>
            </div>

            <!-- Days of Week -->
            <div class="grid grid-cols-7 mb-1">
                <template x-for="(day, index) in ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su']" :key="'day-' + index">
                    <div class="text-center text-[10px] font-medium text-gray-400 dark:text-gray-500 py-1" x-text="day"></div>
                </template>
            </div>

            <!-- Calendar Days -->
            <div class="grid grid-cols-7">
                <template x-for="blank in startingDayOfWeek" :key="'blank-' + blank">
                    <div class="w-8 h-8"></div>
                </template>
                <template x-for="day in daysInMonth" :key="day">
                    <button type="button" @click="selectDate(day)"
                        class="w-8 h-8 flex items-center justify-center rounded-md text-xs transition-colors"
                        :class="{
                            'bg-blue-600 text-white font-semibold': isSelectedDay(day),
                            'text-gray-300 dark:text-gray-600 cursor-not-allowed': isDisabledDay(day),
                            'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300': !isSelectedDay(day) && !isDisabledDay(day),
                            'ring-1 ring-blue-400 font-semibold': isToday(day) && !isSelectedDay(day)
                        }"
                        :disabled="isDisabledDay(day)"
                        x-text="day"
                    ></button>
                </template>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                <button type="button" @click="clearDate()"
                    class="flex-1 px-2 py-1.5 text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 font-medium rounded-md transition-colors">
                    Clear
                </button>
                <button type="button" @click="confirmDate()"
                    class="flex-1 px-2 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                    Done
                </button>
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
            const today = new Date();
            this.currentMonth = today.getMonth();
            this.currentYear = today.getFullYear();
        },

        positionCalendar() {
            this.$nextTick(() => {
                const trigger = this.$refs.triggerWrap;
                const panel = this.$refs.calendarPanel;
                if (!trigger || !panel) return;
                const rect = trigger.getBoundingClientRect();
                const panelW = 256;
                const panelH = panel.offsetHeight || 310;
                panel.style.top = (rect.top - panelH - 4) + 'px';
                let left = rect.left + (rect.width / 2) - (panelW / 2);
                left = Math.max(8, Math.min(left, window.innerWidth - panelW - 8));
                panel.style.left = left + 'px';
            });
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
            this.$dispatch('date-selected', { value: this.selectedDate });
        },
        
        clearDate() {
            this.selectedDate = null;
            this.open = false;
            this.$dispatch('date-selected', { value: null });
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