{{--
    Calendar Picker Component (Alpine.js)
    Animated date picker with dark/light mode, today indicator, and selection glow.

    Usage:
    <x-material-ui.calendar-picker model="formData.service_date" />
    <x-material-ui.calendar-picker model="selectedDate" :min="now()->toDateString()" />
--}}
@props([
    'model' => null,
    'min' => null,
    'max' => null,
    'disabledDates' => '[]',
])

@php
    $uid = 'calpick_' . uniqid();
    $minJs = $min ? "new Date('" . $min . "T00:00:00')" : 'null';
    $maxJs = $max ? "new Date('" . $max . "T00:00:00')" : 'null';
@endphp

<div class="cal-picker p-4 bg-white/90 dark:bg-gray-900/80 backdrop-blur-xl border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm select-none"
     x-data="{
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        selectedDate: {{ $model }} ? new Date({{ $model }} + 'T00:00:00') : null,
        direction: 0,
        animating: false,
        minDate: {!! $minJs !!},
        maxDate: {!! $maxJs !!},
        disabledDates: {!! $disabledDates !!},

        get daysInMonth() {
            return new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
        },
        get firstDayOfWeek() {
            const d = new Date(this.currentYear, this.currentMonth, 1).getDay();
            return d === 0 ? 6 : d - 1; // Monday-first
        },
        get monthLabel() {
            return new Date(this.currentYear, this.currentMonth).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        },
        get weeks() {
            const days = [];
            const prev = new Date(this.currentYear, this.currentMonth, 0).getDate();
            // Previous month fill
            for (let i = this.firstDayOfWeek - 1; i >= 0; i--) {
                days.push({ day: prev - i, outside: true, date: new Date(this.currentYear, this.currentMonth - 1, prev - i) });
            }
            // Current month
            for (let i = 1; i <= this.daysInMonth; i++) {
                days.push({ day: i, outside: false, date: new Date(this.currentYear, this.currentMonth, i) });
            }
            // Next month fill
            const remaining = 42 - days.length;
            for (let i = 1; i <= remaining; i++) {
                days.push({ day: i, outside: true, date: new Date(this.currentYear, this.currentMonth + 1, i) });
            }
            // Chunk into weeks
            const weeks = [];
            for (let i = 0; i < days.length; i += 7) weeks.push(days.slice(i, i + 7));
            return weeks;
        },

        prevMonth() {
            this.direction = -1;
            this.animating = true;
            setTimeout(() => {
                if (this.currentMonth === 0) { this.currentMonth = 11; this.currentYear--; }
                else this.currentMonth--;
                setTimeout(() => this.animating = false, 50);
            }, 150);
        },
        nextMonth() {
            this.direction = 1;
            this.animating = true;
            setTimeout(() => {
                if (this.currentMonth === 11) { this.currentMonth = 0; this.currentYear++; }
                else this.currentMonth++;
                setTimeout(() => this.animating = false, 50);
            }, 150);
        },

        isToday(d) {
            const t = new Date();
            return d.day === t.getDate() && this.currentMonth === t.getMonth() && this.currentYear === t.getFullYear() && !d.outside;
        },
        isSelected(d) {
            if (!this.selectedDate || d.outside) return false;
            return d.day === this.selectedDate.getDate() && this.currentMonth === this.selectedDate.getMonth() && this.currentYear === this.selectedDate.getFullYear();
        },
        isDisabled(d) {
            if (d.outside) return true;
            const date = new Date(this.currentYear, this.currentMonth, d.day);
            if (this.minDate && date < this.minDate) return true;
            if (this.maxDate && date > this.maxDate) return true;
            const dateStr = date.toISOString().split('T')[0];
            return this.disabledDates.includes(dateStr);
        },

        selectDate(d) {
            if (d.outside || this.isDisabled(d)) return;
            const date = new Date(this.currentYear, this.currentMonth, d.day);
            this.selectedDate = date;
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            {{ $model }} = y + '-' + m + '-' + dd;
        }
     }"
     x-init="
        if ({{ $model }}) {
            const d = new Date({{ $model }} + 'T00:00:00');
            if (!isNaN(d)) { currentMonth = d.getMonth(); currentYear = d.getFullYear(); selectedDate = d; }
        }
     "
     id="{{ $uid }}">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <button type="button" @click="prevMonth()"
            class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all active:scale-90">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <span class="text-sm font-bold tracking-tight text-gray-800 dark:text-gray-200" x-text="monthLabel"></span>
        <button type="button" @click="nextMonth()"
            class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all active:scale-90">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
    </div>

    {{-- Weekday headers --}}
    <div class="grid grid-cols-7 mb-1">
        <template x-for="wd in ['Mo','Tu','We','Th','Fr','Sa','Su']" :key="wd">
            <div class="text-center text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 py-1" x-text="wd"></div>
        </template>
    </div>

    {{-- Days grid --}}
    <div class="cal-picker-body" :class="animating ? (direction > 0 ? 'cal-slide-left' : 'cal-slide-right') : 'cal-slide-in'">
        <template x-for="(week, wi) in weeks" :key="'w'+wi">
            <div class="grid grid-cols-7">
                <template x-for="(d, di) in week" :key="'d'+wi+'-'+di">
                    <div class="flex items-center justify-center py-0.5">
                        <button type="button"
                            @click="selectDate(d)"
                            :disabled="isDisabled(d)"
                            class="relative w-9 h-9 rounded-full text-sm font-medium transition-all duration-200 flex items-center justify-center"
                            :class="{
                                'text-gray-300 dark:text-gray-600 cursor-default': d.outside,
                                'text-gray-200 dark:text-gray-700 cursor-not-allowed': isDisabled(d) && !d.outside,
                                'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:scale-110 active:scale-90': !d.outside && !isDisabled(d) && !isSelected(d),
                                'text-white cal-selected-glow': isSelected(d),
                                'font-bold': isToday(d),
                            }"
                            :style="isSelected(d) ? 'background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8); box-shadow: 0 0 12px rgba(59,130,246,0.4), 0 2px 8px rgba(59,130,246,0.2);' : ''">
                            <span x-text="d.day"></span>
                            {{-- Today dot --}}
                            <span x-show="isToday(d) && !isSelected(d)"
                                  class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-blue-500"></span>
                        </button>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>

@once
<style>
.cal-picker-body {
    transition: opacity 0.15s ease, transform 0.15s ease, filter 0.15s ease;
    opacity: 1; transform: translateX(0); filter: blur(0);
}
.cal-slide-left {
    opacity: 0; transform: translateX(-12px); filter: blur(2px);
}
.cal-slide-right {
    opacity: 0; transform: translateX(12px); filter: blur(2px);
}
.cal-slide-in {
    opacity: 1; transform: translateX(0); filter: blur(0);
}
</style>
@endonce
