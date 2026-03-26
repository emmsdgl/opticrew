<x-layouts.general-applicant title="Dashboard">

    {{-- ── Right Filters ── --}}
    <x-slot:rightFilters>
        <style>
            /* ── Elastic Dual-Thumb Slider ── */
            .elastic-slider { position: relative; padding: 12px 0; }
            .elastic-slider-wrapper {
                display: flex; align-items: center; gap: 10px;
                transition: transform 0.2s ease;
            }
            .elastic-slider-wrapper:hover { transform: scaleX(1.02) scaleY(1.05); }
            .elastic-slider-wrapper:hover .elastic-slider-root { height: 10px; margin-top: -2px; margin-bottom: -2px; }
            .elastic-slider-wrapper:hover .elastic-slider-thumb { width: 18px; height: 18px; }

            .elastic-slider-icon {
                display: flex; align-items: center; justify-content: center;
                width: 24px; height: 24px; flex-shrink: 0;
                transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            }
            .elastic-slider-icon.bounce-left { transform: scale(1.3) translateX(-2px); }
            .elastic-slider-icon.bounce-right { transform: scale(1.3) translateX(2px); }

            .elastic-slider-root {
                position: relative; flex: 1; height: 6px; cursor: pointer;
                border-radius: 999px; transition: height 0.2s ease, margin 0.2s ease;
            }
            .elastic-slider-track {
                position: absolute; inset: 0; border-radius: 999px;
                background: #e5e7eb; overflow: hidden;
            }
            .dark .elastic-slider-track { background: #374151; }
            .elastic-slider-range {
                position: absolute; top: 0; bottom: 0; border-radius: 999px;
                background: linear-gradient(90deg, #3b82f6, #6366f1);
                transition: left 0.1s ease, width 0.1s ease;
            }
            .elastic-slider-thumb {
                position: absolute; top: 50%; width: 16px; height: 16px;
                border-radius: 50%; background: #3b82f6; border: 2.5px solid white;
                box-shadow: 0 1px 4px rgba(0,0,0,0.2), 0 0 0 0 rgba(59,130,246,0);
                transform: translate(-50%, -50%); cursor: grab; z-index: 2;
                transition: width 0.2s ease, height 0.2s ease, box-shadow 0.2s ease;
            }
            .dark .elastic-slider-thumb { border-color: #1e293b; }
            .elastic-slider-thumb:hover,
            .elastic-slider-thumb.active {
                box-shadow: 0 1px 4px rgba(0,0,0,0.2), 0 0 0 6px rgba(59,130,246,0.15);
            }
            .elastic-slider-thumb.active { cursor: grabbing; }

            .elastic-slider-tooltip {
                position: absolute; bottom: calc(100% + 8px); left: 50%;
                transform: translateX(-50%) scale(0.8); opacity: 0;
                background: #1e293b; color: white; font-size: 10px; font-weight: 600;
                padding: 2px 6px; border-radius: 4px; white-space: nowrap;
                pointer-events: none; transition: all 0.15s ease;
            }
            .dark .elastic-slider-tooltip { background: #e2e8f0; color: #1e293b; }
            .elastic-slider-thumb:hover .elastic-slider-tooltip,
            .elastic-slider-thumb.active .elastic-slider-tooltip {
                opacity: 1; transform: translateX(-50%) scale(1);
            }
            .elastic-slider-tooltip::after {
                content: ''; position: absolute; top: 100%; left: 50%;
                transform: translateX(-50%); border: 4px solid transparent;
                border-top-color: #1e293b;
            }
            .dark .elastic-slider-tooltip::after { border-top-color: #e2e8f0; }
        </style>
        <div x-data="applicantFilters()" x-init="init()" class="px-1">

            <h3 class="text-xs font-bold text-gray-900 dark:text-white my-3">Filtering </h3>
            {{-- Job Type --}}
            <div>
                <div class="flex items-center justify-between mb-2.5">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white my-3">Job Type</h3>
                    <button @click="selectedTypes = []; applyFilters()"
                        x-show="selectedTypes.length > 0"
                        class="text-[10px] text-red-500 hover:text-red-600 font-medium">Clear all</button>
                </div>
                <div class="flex flex-col gap-2 relative">
                    <template x-for="type in jobTypes" :key="type.value">
                        <div
                            @click="toggleType(type.value)"
                            :class="selectedTypes.includes(type.value)
                                ? 'border-blue-400 dark:border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-1 ring-blue-400/30'
                                : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/80 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="flex items-center gap-3 p-2.5 rounded-xl shadow-sm cursor-pointer transition-all duration-200 border"
                        >
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                 :class="selectedTypes.includes(type.value)
                                    ? 'bg-blue-100 dark:bg-blue-900/40'
                                    : 'bg-gray-50 dark:bg-gray-700'">
                                <i class="fa-solid fa-briefcase text-[10px]"
                                   :class="selectedTypes.includes(type.value)
                                      ? 'text-blue-600 dark:text-blue-400'
                                      : 'text-gray-500 dark:text-gray-400'"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-semibold text-gray-900 dark:text-white truncate" x-text="type.label"></div>
                            </div>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full flex-shrink-0"
                                x-text="typeCounts[type.value] || 0"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Salary Range (Elastic Slider) --}}
            <div class="my-4">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white">Salary Range</h3>
                    <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400">
                        €<span x-text="salaryMin"></span> – €<span x-text="salaryMax"></span>
                    </span>
                </div>
                <div class="elastic-slider" x-ref="sliderWrap">
                    <div class="elastic-slider-wrapper">
                        {{-- Left Icon --}}
                        <div class="elastic-slider-icon" :class="sliderBounce === 'left' && 'bounce-left'">
                            <i class="fa-solid fa-coins text-sm text-gray-400 dark:text-gray-500"></i>
                        </div>

                        {{-- Track --}}
                        <div class="elastic-slider-root" x-ref="sliderTrack"
                             @pointerdown="handleTrackClick($event)">
                            <div class="elastic-slider-track">
                                <div class="elastic-slider-range" :style="elasticFillStyle()"></div>
                            </div>

                            {{-- Min Thumb --}}
                            <div class="elastic-slider-thumb"
                                 :class="activeThumb === 'min' && 'active'"
                                 :style="`left: ${thumbPercent(salaryMin)}%`"
                                 @pointerdown.stop="startDrag($event, 'min')">
                                <div class="elastic-slider-tooltip">€<span x-text="salaryMin"></span></div>
                            </div>

                            {{-- Max Thumb --}}
                            <div class="elastic-slider-thumb"
                                 :class="activeThumb === 'max' && 'active'"
                                 :style="`left: ${thumbPercent(salaryMax)}%`"
                                 @pointerdown.stop="startDrag($event, 'max')">
                                <div class="elastic-slider-tooltip">€<span x-text="salaryMax"></span></div>
                            </div>
                        </div>

                        {{-- Right Icon --}}
                        <div class="elastic-slider-icon" :class="sliderBounce === 'right' && 'bounce-right'">
                            <i class="fa-solid fa-sack-dollar text-sm text-gray-400 dark:text-gray-500"></i>
                        </div>
                    </div>

                    {{-- Min / Max labels --}}
                    <div class="flex items-center justify-between mt-2 px-[34px]">
                        <span class="text-[10px] text-gray-400 dark:text-gray-500">€<span x-text="salaryAbsMin"></span></span>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500">€<span x-text="salaryAbsMax"></span></span>
                    </div>
                </div>
            </div>

            {{-- Location (Stack List) --}}
            <div class="my-3">
                <div class="flex items-center justify-between mb-2.5">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white">Location</h3>
                    <button @click="selectedLocations = []; applyFilters()"
                        x-show="selectedLocations.length > 0"
                        class="text-[10px] text-red-500 hover:text-red-600 font-medium">Clear all</button>
                </div>

                <div class="flex flex-col gap-2 relative">
                    <template x-for="(loc, idx) in locations" :key="loc">
                        <div
                            x-show="expanded || idx < 3"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                            @click="toggleLocation(loc)"
                            :class="selectedLocations.includes(loc)
                                ? 'border-blue-400 dark:border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-1 ring-blue-400/30'
                                : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/80 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="flex items-center gap-3 p-2.5 rounded-xl shadow-sm cursor-pointer transition-all duration-200 border"
                        >
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                 :class="selectedLocations.includes(loc)
                                    ? 'bg-blue-100 dark:bg-blue-900/40'
                                    : 'bg-gray-50 dark:bg-gray-700'">
                                <i class="fa-solid fa-location-dot text-[10px]"
                                   :class="selectedLocations.includes(loc)
                                      ? 'text-blue-600 dark:text-blue-400'
                                      : 'text-gray-500 dark:text-gray-400'"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-semibold text-gray-900 dark:text-white truncate" x-text="loc"></div>
                            </div>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full flex-shrink-0"
                                x-text="locationCounts[loc] || 0"></span>
                        </div>
                    </template>
                </div>

                <div x-show="locations.length > 3" class="flex justify-center">
                    <button
                        @click="expanded = !expanded"
                        class="mt-2.5 flex items-center justify-center gap-1.5 px-3 py-1
                               border border-gray-200 dark:border-gray-700 rounded-full
                               text-[10px] font-medium text-gray-500 dark:text-gray-400 transition-colors
                               hover:bg-gray-50 dark:hover:bg-gray-800"
                    >
                        <span x-text="expanded ? 'Hide' : 'Show All'"></span>
                        <i class="fa-solid fa-chevron-down text-[8px] transition-transform duration-300"
                           :class="expanded && 'rotate-180'"></i>
                    </button>
                </div>
            </div>

        </div>
    </x-slot:rightFilters>

    {{-- ── Sidebar Summary (pushed to bottom of left sidebar) ── --}}
    <x-slot:sidebarBottom>
        <div class="py-12 px-6">
            <div class="shadow-sm">
                <div class="flex flex-col gap-2">
                    {{-- Available Jobs --}}
                    <div class="flex items-center gap-3 p-2.5 rounded-xl shadow-sm">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-gray-50 dark:bg-gray-700">
                            <i class="fa-solid fa-briefcase text-sm text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Available Jobs</div>
                        </div>
                        <span id="sidebar-visible-count" class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full flex-shrink-0">{{ $jobPostings->count() }}</span>
                    </div>
                    {{-- My Applications --}}
                    <div class="flex items-center gap-3 p-2.5 rounded-xl shadow-sm">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-gray-50 dark:bg-gray-700">
                            <i class="fa-solid fa-paper-plane text-sm text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">My Applications</div>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full flex-shrink-0">{{ $myApplications->count() }}</span>
                    </div>
                    {{-- Withdrawn --}}
                    <div class="flex items-center gap-3 p-2.5 rounded-xl shadow-sm">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-gray-50 dark:bg-gray-700">
                            <i class="fa-solid fa-rotate-left text-sm text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Withdrawn</div>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full flex-shrink-0">{{ $withdrawnCount ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:sidebarBottom>

    {{-- ── Middle Content ── --}}

    {{-- Row 1: Hero Card --}}
    <x-applicant-components.herocard
        headerName="{{ $user->name }}"
        headerDesc="Browse open positions and track your applications."
        headerIcon=""
    />

    {{-- Row 2: My Applications --}}
    <div>
        <x-labelwithvalue
            label="My Applications"
            count="{{ $myApplications->count() }}"
        />
        <div class="mt-3">
            <x-applicant-components.applied-positions :applications="$myApplications" :jobPostings="$allJobPostings" />
        </div>
    </div>

    {{-- Row 3: Available Positions --}}
    @php
        $appliedTitles = $myApplications->pluck('job_title')->map(fn($t) => strtolower(trim($t)))->toArray();
        $availableCount = $jobPostings->filter(fn($job) => !in_array(strtolower(trim($job->title)), $appliedTitles))->count();
    @endphp
    <div>
        <x-labelwithvalue
            label="Available Job Positions"
            count="{{ $availableCount }}"
        />
        <div class="mt-3">
            <x-applicant-components.available-positions :jobPostings="$jobPostings" :applications="$myApplications" :savedJobIds="$savedJobIds ?? []" />
        </div>
    </div>

    {{-- Apply Modal --}}
    <x-applicant-components.apply-modal />

    @php
        $allJobsJson = $jobPostings->map(fn($j) => [
            'id' => $j->id, 'title' => $j->title, 'type' => $j->type,
            'location' => $j->location, 'salary' => (float) ($j->salary ?? 0),
        ]);
    @endphp

    @push('scripts')
    <script>
    function applicantFilters() {
        const allJobs = @json($allJobsJson);

        const locs = [...new Set(allJobs.map(j => j.location).filter(Boolean))].sort();
        const locCounts = {};
        locs.forEach(loc => { locCounts[loc] = allJobs.filter(j => j.location === loc).length; });

        const typCounts = {};
        ['full-time', 'part-time', 'remote'].forEach(t => { typCounts[t] = allJobs.filter(j => j.type === t).length; });

        const salaries = allJobs.map(j => j.salary).filter(s => s > 0);
        const minSal = salaries.length ? Math.floor(Math.min(...salaries)) : 0;
        const maxSal = salaries.length ? Math.ceil(Math.max(...salaries)) : 1000;

        return {
            search: '',
            selectedTypes: [],
            selectedLocations: [],
            salaryMin: minSal,
            salaryMax: maxSal,
            salaryAbsMin: minSal,
            salaryAbsMax: maxSal,
            expanded: false,
            visibleCount: 0,
            jobTypes: [
                { value: 'full-time', label: 'Full-time' },
                { value: 'part-time', label: 'Part-time' },
                { value: 'remote', label: 'Remote' },
            ],
            locations: locs,
            locationCounts: locCounts,
            typeCounts: typCounts,

            toggleType(val) {
                const idx = this.selectedTypes.indexOf(val);
                if (idx > -1) { this.selectedTypes.splice(idx, 1); }
                else { this.selectedTypes.push(val); }
                this.applyFilters();
            },

            toggleLocation(loc) {
                const idx = this.selectedLocations.indexOf(loc);
                if (idx > -1) { this.selectedLocations.splice(idx, 1); }
                else { this.selectedLocations.push(loc); }
                this.applyFilters();
            },

            activeThumb: null,
            sliderBounce: null,

            init() {
                this.visibleCount = document.querySelectorAll('.avp-scroll > div').length;
            },

            thumbPercent(val) {
                const range = this.salaryAbsMax - this.salaryAbsMin || 1;
                return ((val - this.salaryAbsMin) / range) * 100;
            },

            elasticFillStyle() {
                const left = this.thumbPercent(this.salaryMin);
                const width = this.thumbPercent(this.salaryMax) - left;
                return `left:${left}%;width:${width}%`;
            },

            startDrag(e, which) {
                this.activeThumb = which;
                const track = this.$refs.sliderTrack;
                const onMove = (ev) => {
                    const rect = track.getBoundingClientRect();
                    let pct = ((ev.clientX - rect.left) / rect.width) * 100;
                    pct = Math.max(0, Math.min(100, pct));
                    const val = Math.round(this.salaryAbsMin + (pct / 100) * (this.salaryAbsMax - this.salaryAbsMin));
                    if (which === 'min') {
                        this.salaryMin = Math.min(val, this.salaryMax - 1);
                        this.sliderBounce = 'left';
                    } else {
                        this.salaryMax = Math.max(val, this.salaryMin + 1);
                        this.sliderBounce = 'right';
                    }
                };
                const onUp = () => {
                    this.activeThumb = null;
                    this.sliderBounce = null;
                    window.removeEventListener('pointermove', onMove);
                    window.removeEventListener('pointerup', onUp);
                    this.applyFilters();
                };
                window.addEventListener('pointermove', onMove);
                window.addEventListener('pointerup', onUp);
            },

            handleTrackClick(e) {
                const rect = this.$refs.sliderTrack.getBoundingClientRect();
                let pct = ((e.clientX - rect.left) / rect.width) * 100;
                pct = Math.max(0, Math.min(100, pct));
                const val = Math.round(this.salaryAbsMin + (pct / 100) * (this.salaryAbsMax - this.salaryAbsMin));
                const distMin = Math.abs(val - this.salaryMin);
                const distMax = Math.abs(val - this.salaryMax);
                if (distMin <= distMax) { this.salaryMin = Math.min(val, this.salaryMax - 1); }
                else { this.salaryMax = Math.max(val, this.salaryMin + 1); }
                this.applyFilters();
            },

            applyFilters() {
                const cards = document.querySelectorAll('[data-job-card]');
                let visible = 0;
                cards.forEach(card => {
                    const type = card.dataset.jobType || '';
                    const loc = card.dataset.jobLocation || '';
                    const sal = parseFloat(card.dataset.jobSalary || 0);

                    const typeOk = this.selectedTypes.length === 0 || this.selectedTypes.includes(type);
                    const locOk = this.selectedLocations.length === 0 || this.selectedLocations.includes(loc);
                    const salOk = sal >= this.salaryMin && sal <= this.salaryMax;

                    if (typeOk && locOk && salOk) {
                        card.style.display = '';
                        visible++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                this.visibleCount = visible;
                const counter = document.getElementById('sidebar-visible-count');
                if (counter) counter.textContent = visible;
            },
        };
    }
    </script>
    @endpush

</x-layouts.general-applicant>
