@props([
    'interviews' => collect(),
])

<style>
.week-grid::-webkit-scrollbar { width: 4px; }
.week-grid::-webkit-scrollbar-track { background: transparent; }
.week-grid::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.dark .week-grid::-webkit-scrollbar-thumb { background: #475569; }
</style>

<div x-data="bigCalendar({{ $interviews->toJson() }})"
    @calendar-date-selected.window="goToDate($event.detail.date)"
    class="bg-white dark:bg-gray-900/50 rounded-2xl flex flex-col h-full min-h-0 overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-700/50 flex-shrink-0">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-gray-800 dark:text-gray-100" x-text="headerLabel"></h2>
            <div class="flex items-center gap-1">
                <button @click="prevWeek()" class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-500 hover:bg-blue-600 text-white transition-colors">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </button>
                <button @click="nextWeek()" class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-500 hover:bg-blue-600 text-white transition-colors">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        </div>
        <button @click="goToToday()" class="text-[11px] font-bold px-4 py-1.5 rounded-full border-2 border-blue-500 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
            Today
        </button>
    </div>

    {{-- Day column headers --}}
    <div class="grid flex-shrink-0 border-b border-gray-100 dark:border-gray-700/50 min-w-0 overflow-hidden" style="grid-template-columns: 44px repeat(7, minmax(0, 1fr));">
        <div class="flex items-center justify-center text-[9px] font-medium text-gray-400 dark:text-gray-500 py-2 border-r border-gray-100 dark:border-gray-700/30">
            <span x-text="'GMT' + (new Date().getTimezoneOffset() <= 0 ? '+' : '-') + Math.abs(Math.floor(new Date().getTimezoneOffset()/60))"></span>
        </div>
        <template x-for="day in weekDays" :key="day.key">
            <div @click="selectedDay = day.date; $dispatch('big-calendar-date-selected', { date: day.date })"
                class="flex flex-col items-center justify-center py-2 border-r border-gray-100 dark:border-gray-700/30 last:border-r-0 cursor-pointer transition-colors"
                :class="{
                    'bg-blue-100 dark:bg-blue-900/30 ring-2 ring-blue-500 ring-inset': selectedDay === day.date,
                    'bg-blue-50 dark:bg-blue-900/20': day.isToday && selectedDay !== day.date,
                    'hover:bg-gray-50 dark:hover:bg-gray-800/50': !day.isToday && selectedDay !== day.date,
                }">
                <span class="text-[9px] font-bold uppercase tracking-wide"
                    :class="day.isToday || selectedDay === day.date ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'"
                    x-text="day.dayName"></span>
                <span class="text-sm font-bold mt-0.5"
                    :class="day.isToday || selectedDay === day.date ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-200'"
                    x-text="day.dayNum"></span>

                {{-- Interview pill indicators --}}
                <div class="flex items-center gap-0.5 mt-1 max-w-full overflow-hidden" x-show="getEventsForDay(day.date).length > 0">
                    <template x-for="(ev, eIdx) in getEventsForDay(day.date).slice(0, 3)" :key="ev.id">
                        <div class="h-1.5 rounded-full flex-shrink-0"
                            :class="ev.dotColor"
                            :style="'width: ' + Math.min(24, Math.max(8, 40 / getEventsForDay(day.date).length)) + 'px'">
                        </div>
                    </template>
                    <span x-show="getEventsForDay(day.date).length > 3"
                        class="text-[7px] font-bold text-gray-400 dark:text-gray-500 flex-shrink-0"
                        x-text="'+' + (getEventsForDay(day.date).length - 3)"></span>
                </div>
            </div>
        </template>
    </div>

    {{-- Time grid (scrollable) — shows 6AM-10AM (5 rows) before scrolling --}}
    <div x-ref="weekGrid" class="week-grid flex-1 min-h-0 min-w-0 overflow-y-auto overflow-x-hidden">

        {{-- Each hour row is a grid matching the header --}}
        <template x-for="(hour, hIdx) in hours" :key="hour">
            <div class="grid relative min-w-0" style="grid-template-columns: 44px repeat(7, minmax(0, 1fr)); height: 64px;">

                {{-- Time label --}}
                <div class="flex items-start justify-center border-r border-gray-100 dark:border-gray-700/30">
                    <span class="text-[9px] font-medium text-gray-400 dark:text-gray-500 -mt-1.5" x-text="hour"></span>
                </div>

                {{-- 7 day columns --}}
                <template x-for="(day, dIdx) in weekDays" :key="'cell-'+hour+'-'+day.key">
                    <div class="relative border-r border-b border-gray-100 dark:border-gray-700/20 last:border-r-0"
                        :class="day.isToday ? 'bg-blue-50/30 dark:bg-blue-900/5' : ''">

                        {{-- Events that start in this cell --}}
                        <template x-for="ev in getEventsForCell(day.date, hIdx)" :key="ev.id">
                            <div class="absolute inset-x-0.5 rounded-lg px-2 py-1.5 overflow-hidden cursor-pointer border transition-all hover:shadow-md z-10"
                                :class="ev.colorClass"
                                :style="'top: ' + ev.offsetPx + 'px; height: ' + ev.height + 'px; min-height: 28px;'">
                                <p class="text-[10px] font-bold truncate" x-text="ev.title"></p>
                                <p class="text-[9px] opacity-80 truncate" x-text="ev.timeLabel"></p>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Now indicator --}}
                <template x-if="nowIndicatorHourIdx === hIdx">
                    <div class="absolute pointer-events-none z-20 right-0 flex items-center" style="left: 44px;"
                        :style="'top: ' + nowIndicatorOffsetPx + 'px'">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-500 -ml-1.5 shadow-sm"></div>
                        <div class="flex-1 h-[2px]" style="background: linear-gradient(90deg, rgba(59,130,246,0.8), rgba(59,130,246,0.1));"></div>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>

<script>
function bigCalendar(interviews) {
    const eventColors = [
        'bg-blue-100 dark:bg-blue-900/40 border-blue-200 dark:border-blue-800/50 text-blue-800 dark:text-blue-200',
        'bg-purple-100 dark:bg-purple-900/40 border-purple-200 dark:border-purple-800/50 text-purple-800 dark:text-purple-200',
        'bg-green-100 dark:bg-green-900/40 border-green-200 dark:border-green-800/50 text-green-800 dark:text-green-200',
        'bg-amber-100 dark:bg-amber-900/40 border-amber-200 dark:border-amber-800/50 text-amber-800 dark:text-amber-200',
        'bg-cyan-100 dark:bg-cyan-900/40 border-cyan-200 dark:border-cyan-800/50 text-cyan-800 dark:text-cyan-200',
        'bg-pink-100 dark:bg-pink-900/40 border-pink-200 dark:border-pink-800/50 text-pink-800 dark:text-pink-200',
    ];

    const dotColors = [
        'bg-blue-400 dark:bg-blue-500',
        'bg-purple-400 dark:bg-purple-500',
        'bg-green-400 dark:bg-green-500',
        'bg-amber-400 dark:bg-amber-500',
        'bg-cyan-400 dark:bg-cyan-500',
        'bg-pink-400 dark:bg-pink-500',
    ];

    const startHour = 0;
    const pxPerHour = 64;

    // Pre-process interviews into event objects
    const parsedEvents = interviews.map((interview, idx) => {
        const dateStr = (interview.interview_date || '').split('T')[0].split(' ')[0];
        const timePart = (interview.interview_date || '').includes('T')
            ? interview.interview_date.split('T')[1]
            : (interview.interview_date || '').split(' ')[1] || '09:00:00';
        const [hh, mm] = timePart.split(':').map(Number);

        return {
            id: interview.id || idx,
            title: interview.job_title || 'Interview',
            date: dateStr,
            hour: hh,
            minute: mm,
            hourIdx: hh - startHour,
            colorClass: eventColors[idx % eventColors.length],
            dotColor: dotColors[idx % dotColors.length],
        };
    }).filter(e => e.hourIdx >= 0);

    return {
        weekStart: null,
        weekDays: [],
        hours: [],
        selectedDay: null,
        nowIndicatorHourIdx: null,
        nowIndicatorOffsetPx: 0,
        _nowTimer: null,

        get headerLabel() {
            if (!this.weekStart) return '';
            const end = new Date(this.weekStart);
            end.setDate(end.getDate() + 6);
            const sMonth = this.weekStart.toLocaleDateString('en-US', { month: 'long' });
            const eMonth = end.toLocaleDateString('en-US', { month: 'long' });
            const year = this.weekStart.getFullYear();
            if (sMonth === eMonth) return `${sMonth} ${year}`;
            return `${sMonth} – ${eMonth} ${year}`;
        },

        init() {
            this.hours = [];
            for (let h = startHour; h <= 23; h++) {
                const ampm = h < 12 ? 'AM' : 'PM';
                const display = h === 0 ? 12 : (h > 12 ? h - 12 : h);
                this.hours.push(`${String(display).padStart(2, '0')} ${ampm}`);
            }

            this.weekStart = this.getMonday(new Date());
            this.buildWeek();
            this.selectedDay = this.fmt(new Date());
            this.updateNowIndicator();
            this._nowTimer = setInterval(() => this.updateNowIndicator(), 60000);
            this.$nextTick(() => {
                this.$dispatch('big-calendar-date-selected', { date: this.selectedDay });
                // Auto-scroll to 6 AM on load
                if (this.$refs.weekGrid) {
                    const scrollTo6AM = 6 * pxPerHour;
                    this.$refs.weekGrid.scrollTop = scrollTo6AM;
                }
            });
        },

        destroy() {
            if (this._nowTimer) clearInterval(this._nowTimer);
        },

        fmt(d) {
            return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        },

        getMonday(d) {
            const date = new Date(d);
            const day = date.getDay();
            const diff = day === 0 ? -6 : 1 - day;
            date.setDate(date.getDate() + diff);
            date.setHours(0,0,0,0);
            return date;
        },

        buildWeek() {
            const today = this.fmt(new Date());
            const days = [];
            const dayNames = ['MON','TUE','WED','THU','FRI','SAT','SUN'];

            for (let i = 0; i < 7; i++) {
                const d = new Date(this.weekStart);
                d.setDate(d.getDate() + i);
                days.push({
                    key: this.fmt(d),
                    date: this.fmt(d),
                    dayName: dayNames[i],
                    dayNum: d.getDate(),
                    isToday: this.fmt(d) === today,
                });
            }
            this.weekDays = days;
        },

        formatTime(h, m) {
            const ampm = h < 12 ? 'AM' : 'PM';
            const display = h === 0 ? 12 : h > 12 ? h - 12 : h;
            return `${String(display).padStart(2,'0')}:${String(m).padStart(2,'0')} ${ampm}`;
        },

        getEventsForDay(date) {
            return parsedEvents.filter(e => e.date === date);
        },

        getEventsForCell(date, hourIdx) {
            return parsedEvents
                .filter(e => e.date === date && e.hourIdx === hourIdx)
                .map(e => ({
                    ...e,
                    offsetPx: (e.minute / 60) * pxPerHour,
                    height: pxPerHour,
                    timeLabel: this.formatTime(e.hour, e.minute) + ' - ' + this.formatTime(e.hour + 1, e.minute),
                }));
        },

        updateNowIndicator() {
            const now = new Date();
            const h = now.getHours();
            const m = now.getMinutes();
            if (h < startHour || h > 23) {
                this.nowIndicatorHourIdx = null;
                return;
            }
            this.nowIndicatorHourIdx = h - startHour;
            this.nowIndicatorOffsetPx = (m / 60) * pxPerHour;
        },

        goToDate(dateStr) {
            const parts = dateStr.split('-');
            const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            this.weekStart = this.getMonday(d);
            this.selectedDay = dateStr;
            this.buildWeek();
            this.$dispatch('big-calendar-date-selected', { date: dateStr });
        },

        goToToday() {
            this.weekStart = this.getMonday(new Date());
            this.selectedDay = this.fmt(new Date());
            this.buildWeek();
            this.$dispatch('big-calendar-date-selected', { date: this.selectedDay });
        },

        prevWeek() {
            const d = new Date(this.weekStart);
            d.setDate(d.getDate() - 7);
            this.weekStart = d;
            this.buildWeek();
        },

        nextWeek() {
            const d = new Date(this.weekStart);
            d.setDate(d.getDate() + 7);
            this.weekStart = d;
            this.buildWeek();
        },
    };
}
</script>
