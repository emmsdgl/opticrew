@props([
    'interviews' => collect(),
])

<div x-data="smallCalendar({{ $interviews->toJson() }})" class="bg-white dark:bg-gray-900/80 rounded-2xl shadow-sm p-4">

    {{-- Month navigation --}}
    <div class="flex items-center justify-between mb-3">
        <button @click="prevMonth()" class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fa-solid fa-chevron-left text-[10px] text-gray-500 dark:text-gray-400"></i>
        </button>
        <span class="text-sm font-bold text-gray-700 dark:text-gray-200" x-text="monthLabel"></span>
        <button @click="nextMonth()" class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-500 dark:text-gray-400"></i>
        </button>
    </div>

    {{-- Day-of-week headers --}}
    <div class="grid grid-cols-7 gap-0.5 mb-1">
        <template x-for="d in ['Mo','Tu','We','Th','Fr','Sa','Su']" :key="d">
            <div class="text-center text-xs font-semibold text-gray-400 dark:text-gray-500 py-1" x-text="d"></div>
        </template>
    </div>

    {{-- Day grid --}}
    <div class="grid grid-cols-7 gap-0.5">
        <template x-for="cell in cells" :key="cell.key">
            <button
                @click="cell.inMonth && selectDate(cell.date)"
                :class="{
                    'text-gray-300 dark:text-gray-600 pointer-events-none': !cell.inMonth,
                    'bg-blue-600 text-white font-bold shadow-sm': cell.isSelected && cell.inMonth,
                    'bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold': cell.isToday && !cell.isSelected && cell.inMonth,
                    'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300': !cell.isSelected && !cell.isToday && cell.inMonth,
                }"
                class="relative flex items-center justify-center w-7 h-7 mx-auto rounded-lg text-[10px] transition-all duration-150">
                <span x-text="cell.day"></span>
                {{-- Interview dot --}}
                <span x-show="cell.hasInterview && cell.inMonth"
                    :class="cell.isSelected ? 'bg-white' : 'bg-blue-500'"
                    class="absolute bottom-0.5 w-1 h-1 rounded-full"></span>
            </button>
        </template>
    </div>
</div>

<script>
function smallCalendar(interviews) {
    const interviewDates = interviews.map(i => i.interview_date?.split('T')[0] || i.interview_date?.split(' ')[0]);

    return {
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        selectedDate: null,
        cells: [],

        get monthLabel() {
            return new Date(this.currentYear, this.currentMonth).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        },

        init() {
            this.selectedDate = this.fmt(new Date());
            this.buildCells();
        },

        fmt(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        },

        buildCells() {
            const first = new Date(this.currentYear, this.currentMonth, 1);
            const last = new Date(this.currentYear, this.currentMonth + 1, 0);
            let startDay = first.getDay() || 7; // Monday = 1

            const cells = [];
            const today = this.fmt(new Date());

            // Previous month padding
            const prevLast = new Date(this.currentYear, this.currentMonth, 0);
            for (let i = startDay - 1; i >= 1; i--) {
                const day = prevLast.getDate() - i + 1;
                const d = new Date(this.currentYear, this.currentMonth - 1, day);
                cells.push({ key: 'p' + day, day, date: this.fmt(d), inMonth: false, isToday: false, isSelected: false, hasInterview: false });
            }

            // Current month
            for (let day = 1; day <= last.getDate(); day++) {
                const d = new Date(this.currentYear, this.currentMonth, day);
                const dateStr = this.fmt(d);
                cells.push({
                    key: 'c' + day,
                    day,
                    date: dateStr,
                    inMonth: true,
                    isToday: dateStr === today,
                    isSelected: dateStr === this.selectedDate,
                    hasInterview: interviewDates.includes(dateStr),
                });
            }

            // Next month padding
            const remaining = 42 - cells.length;
            for (let day = 1; day <= remaining; day++) {
                const d = new Date(this.currentYear, this.currentMonth + 1, day);
                cells.push({ key: 'n' + day, day, date: this.fmt(d), inMonth: false, isToday: false, isSelected: false, hasInterview: false });
            }

            this.cells = cells;
        },

        selectDate(date) {
            this.selectedDate = date;
            this.buildCells();
            this.$dispatch('calendar-date-selected', { date });
        },

        prevMonth() {
            this.currentMonth--;
            if (this.currentMonth < 0) { this.currentMonth = 11; this.currentYear--; }
            this.buildCells();
        },

        nextMonth() {
            this.currentMonth++;
            if (this.currentMonth > 11) { this.currentMonth = 0; this.currentYear++; }
            this.buildCells();
        },
    };
}
</script>
