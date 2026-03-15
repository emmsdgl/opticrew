<x-layouts.general-employer :title="'Interviews'">

    <div class="w-full flex flex-col gap-4" x-data="interviewsPage({{ $interviewApplications->toJson() }})">

        {{-- Header --}}
        <div class="flex flex-col gap-2 my-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Interview Schedule</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">View and manage scheduled interviews with applicants</p>
        </div>

        {{-- Main layout: Calendar + Right sidebar --}}
        {{-- 5 hour rows (6AM-10AM) × 64px = 320px + header ~90px = ~410px visible --}}
        <div class="flex gap-4 flex-1 min-h-0" style="height: calc(100vh - 200px); max-height: calc(100vh - 200px);">

            {{-- Calendar (main area) --}}
            <div class="flex-1 min-w-0 min-h-0 flex flex-col">
                <x-applicant-components.big-calendar :interviews="$interviewApplications" />
            </div>

            {{-- Right sidebar: Selected day interviews --}}
            <div class="w-80 flex-shrink-0 flex flex-col min-h-0"
                @big-calendar-date-selected.window="selectDay($event.detail.date)">

                {{-- Selected date header --}}
                <div class="bg-white dark:bg-gray-900/50 rounded-2xl shadow-sm flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700/50 flex-shrink-0">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100" x-text="selectedDateLabel"></h3>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5" x-text="selectedDayName"></p>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300"
                            x-text="dayInterviews.length + ' interview' + (dayInterviews.length !== 1 ? 's' : '')"></span>
                    </div>

                    {{-- Interview list (scrollable) --}}
                    <div class="flex-1 min-h-0 overflow-y-auto px-3 py-2 space-y-2">
                        <template x-for="(interview, idx) in dayInterviews" :key="interview.id">
                            <div class="flex items-start gap-3 p-3 rounded-xl border transition-all hover:shadow-sm"
                                :class="colorClasses[idx % colorClasses.length].card">

                                {{-- Time badge --}}
                                <div class="flex-shrink-0 flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                        :class="colorClasses[idx % colorClasses.length].icon">
                                        <i class="fa-solid fa-calendar-check text-sm"></i>
                                    </div>
                                    <span class="text-[9px] font-bold mt-1" :class="colorClasses[idx % colorClasses.length].time"
                                        x-text="interview.timeLabel"></span>
                                </div>

                                {{-- Details --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold truncate text-gray-800 dark:text-gray-100" x-text="interview.job_title"></p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5" x-text="interview.applicantName"></p>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 truncate" x-text="interview.email"></p>
                                </div>

                                {{-- Status badge --}}
                                <div class="flex-shrink-0">
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-300">Scheduled</span>
                                </div>
                            </div>
                        </template>

                        {{-- Empty state --}}
                        <div x-show="dayInterviews.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                            <i class="fa-regular fa-calendar text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500">No interviews scheduled</p>
                            <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-1">Select a date with interviews to view details</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function interviewsPage(interviews) {
        // Pre-process interviews
        const allInterviews = interviews.map(interview => {
            const raw = interview.interview_date || '';
            const dateStr = raw.split('T')[0].split(' ')[0];
            const timePart = raw.includes('T') ? raw.split('T')[1] : (raw.split(' ')[1] || '09:00:00');
            const [hh, mm] = timePart.split(':').map(Number);
            const ampm = hh < 12 ? 'AM' : 'PM';
            const display = hh === 0 ? 12 : hh > 12 ? hh - 12 : hh;

            const profile = typeof interview.applicant_profile === 'string'
                ? JSON.parse(interview.applicant_profile || '{}')
                : (interview.applicant_profile || {});

            return {
                id: interview.id,
                job_title: interview.job_title || 'Interview',
                email: interview.email || '',
                applicantName: [profile.first_name, profile.last_name].filter(Boolean).join(' ') || interview.applicant_name || interview.email,
                date: dateStr,
                hour: hh,
                minute: mm,
                timeLabel: String(display).padStart(2, '0') + ':' + String(mm).padStart(2, '0') + ' ' + ampm,
            };
        });

        // Group by date
        const byDate = {};
        allInterviews.forEach(i => {
            if (!byDate[i.date]) byDate[i.date] = [];
            byDate[i.date].push(i);
        });
        // Sort each day by time
        Object.values(byDate).forEach(arr => arr.sort((a, b) => (a.hour * 60 + a.minute) - (b.hour * 60 + b.minute)));

        return {
            selectedDate: null,
            dayInterviews: [],

            colorClasses: [
                { card: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800/40', icon: 'bg-blue-100 dark:bg-blue-900/30 text-blue-500', time: 'text-blue-600 dark:text-blue-400' },
                { card: 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800/40', icon: 'bg-purple-100 dark:bg-purple-900/30 text-purple-500', time: 'text-purple-600 dark:text-purple-400' },
                { card: 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800/40', icon: 'bg-green-100 dark:bg-green-900/30 text-green-500', time: 'text-green-600 dark:text-green-400' },
                { card: 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800/40', icon: 'bg-amber-100 dark:bg-amber-900/30 text-amber-500', time: 'text-amber-600 dark:text-amber-400' },
                { card: 'bg-cyan-50 dark:bg-cyan-900/20 border-cyan-200 dark:border-cyan-800/40', icon: 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-500', time: 'text-cyan-600 dark:text-cyan-400' },
                { card: 'bg-pink-50 dark:bg-pink-900/20 border-pink-200 dark:border-pink-800/40', icon: 'bg-pink-100 dark:bg-pink-900/30 text-pink-500', time: 'text-pink-600 dark:text-pink-400' },
            ],

            get selectedDateLabel() {
                if (!this.selectedDate) return 'Select a date';
                const parts = this.selectedDate.split('-');
                const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            },

            get selectedDayName() {
                if (!this.selectedDate) return '';
                const parts = this.selectedDate.split('-');
                const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                return d.toLocaleDateString('en-US', { weekday: 'long' });
            },

            selectDay(dateStr) {
                this.selectedDate = dateStr;
                this.dayInterviews = byDate[dateStr] || [];
            },

            init() {
                // Default to today
                const today = new Date();
                const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
                this.selectDay(todayStr);
            },
        };
    }
    </script>

</x-layouts.general-employer>
