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
                {{-- <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2.5">Summary</h3> --}}
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
                        <div class="flex-1 min-w-0 ">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white ">My Applications</div>
                        </div>
                        <span class="text-xs mb-3 text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full flex-shrink-0">{{ $myApplications->count() }}</span>
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
    <div class="mx-8">

        {{-- Welcome Header --}}
        <x-applicant-components.herocard
            headerName="{{ $user->name }}"
            headerDesc="Browse open positions and track your applications."
            headerIcon=""
        />

        {{-- Applied Positions --}}
        <div id="tour-app-my-applications">
        <div class="mt-6 mb-6">
            <x-labelwithvalue
                label="My Applications"
                count="{{ $myApplications->count() }}"
            />
        </div>
        <x-applicant-components.applied-positions :applications="$myApplications" :jobPostings="$allJobPostings" />
        </div>

        {{-- Available Positions --}}
        @php
            $appliedTitles = $myApplications->pluck('job_title')->map(fn($t) => strtolower(trim($t)))->toArray();
            $availableCount = $jobPostings->filter(fn($job) => !in_array(strtolower(trim($job->title)), $appliedTitles))->count();
        @endphp
        <div id="tour-app-available-jobs">
        <div class="mt-6 mb-6">
            <x-labelwithvalue
                label="Available Job Positions"
                count="{{ $availableCount }}"
            />
        </div>
        <x-applicant-components.available-positions :jobPostings="$jobPostings" :applications="$myApplications" :savedJobIds="$savedJobIds ?? []" />
        </div>

        {{-- Apply Modal (global — listens for open-apply-modal window events) --}}
        <x-applicant-components.apply-modal />

    </div>

    {{-- Profile Modal (triggered from header dropdown) --}}
    @php $profileUser = auth()->user(); @endphp
    <div x-data="profileModal()" @open-profile-modal.window="openModal()">
        <template x-teleport="body">
            <div x-show="profileOpen" x-cloak
                 class="fixed inset-0 z-[70] flex items-center justify-center p-4"
                 @keydown.escape.window="closeModal()">
                <div x-show="profileOpen"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     @click="closeModal()"
                     class="absolute inset-0 bg-black/50"></div>
                <div x-show="profileOpen"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     class="relative w-full max-w-sm max-h-[85vh] bg-white dark:bg-[#1E293B] rounded-2xl shadow-2xl overflow-y-auto">

                    {{-- Edit / Save button (top-right) --}}
                    <button x-show="!editing" @click="startEditing()"
                        class="absolute top-4 right-4 z-10 w-8 h-8 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur shadow-md flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:scale-110 transition-all" title="Edit Profile">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>

                    {{-- Close / Cancel button (top-left) --}}
                    <button @click="editing ? cancelEditing() : closeModal()"
                        class="absolute top-4 left-4 z-10 w-8 h-8 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur shadow-md flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 hover:scale-110 transition-all"
                        :title="editing ? 'Cancel' : 'Close'">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>

                    {{-- Cover --}}
                    <div class="h-28 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 relative">
                        <div class="absolute inset-0 bg-black/10"></div>
                    </div>

                    {{-- Avatar --}}
                    <div class="flex justify-center -mt-12 relative z-10">
                        <div class="p-1 rounded-full bg-white dark:bg-[#1E293B] shadow-lg">
                            <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                                @if($profileUser && $profileUser->profile_picture)
                                    <img src="{{ $profileUser->profile_picture }}" alt="{{ $profileUser->name }}" class="w-full h-full object-cover">
                                @else
                                    @php
                                        $nameParts = explode(' ', trim($profileUser->name ?? ''));
                                        $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr(end($nameParts) ?: '', 0, 1));
                                        if (strlen($initials) < 1) $initials = '?';
                                    @endphp
                                    <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                        <span class="text-white font-bold text-2xl">{{ $initials }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 pt-3 pb-6 text-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $profileUser->name ?? 'Applicant' }}</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $profileUser->email ?? '' }}</p>
                        <div class="my-5 border-t border-gray-100 dark:border-gray-700"></div>

                        <div class="space-y-4 text-left px-2">
                            {{-- Phone --}}
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fa-solid fa-phone text-xs text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Phone</p>
                                    <template x-if="!editing">
                                        <p class="text-sm font-normal text-gray-800 dark:text-gray-200 truncate" x-text="form.phone || '—'"></p>
                                    </template>
                                    <template x-if="editing">
                                        <div>
                                            <div class="flex items-stretch">
                                                {{-- Country prefix dropdown (signup style with flag images) --}}
                                                <div class="relative" @click.away="phoneDropOpen = false">
                                                    <button type="button" @click="phoneDropOpen = !phoneDropOpen"
                                                        class="flex items-center gap-1.5 h-full px-2.5 py-1.5 rounded-l-lg border border-r-0 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-xs font-normal text-gray-800 dark:text-gray-200 focus:outline-none whitespace-nowrap">
                                                        <img :src="phonePrefix === '+63' ? '{{ asset('images/icons/philippine_flag.png') }}' : '{{ asset('images/icons/finland-flag.svg') }}'"
                                                            class="h-3.5 w-auto" :alt="phonePrefix === '+63' ? 'PH' : 'FI'">
                                                        <span x-text="phonePrefix" class="text-xs font-medium"></span>
                                                        <svg class="w-2.5 h-2.5 text-gray-400 transition-transform" :class="phoneDropOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                    </button>
                                                    <div x-show="phoneDropOpen" x-cloak x-transition
                                                        class="absolute left-0 top-full mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg z-50 w-52 py-1.5">
                                                        <button type="button" @click="setPhonePrefix('+63', '🇵🇭', 10); phoneDropOpen = false"
                                                            class="w-full text-left px-3 py-2 text-xs flex items-center gap-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                                                            :class="phonePrefix === '+63' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-200'">
                                                            <img src="{{ asset('images/icons/philippine_flag.png') }}" class="h-4 w-auto" alt="PH">
                                                            <span>+63</span>
                                                            <span class="text-gray-400 dark:text-gray-500 text-[10px] ml-auto">Philippines</span>
                                                        </button>
                                                        <button type="button" @click="setPhonePrefix('+358', '🇫🇮', 9); phoneDropOpen = false"
                                                            class="w-full text-left px-3 py-2 text-xs flex items-center gap-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                                                            :class="phonePrefix === '+358' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-200'">
                                                            <img src="{{ asset('images/icons/finland-flag.svg') }}" class="h-4 w-auto" alt="FI">
                                                            <span>+358</span>
                                                            <span class="text-gray-400 dark:text-gray-500 text-[10px] ml-auto">Finland</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                {{-- Local number --}}
                                                <input type="tel" x-model="phoneLocal"
                                                    @input="phoneLocal = phoneLocal.replace(/[^\d]/g, ''); syncPhone()"
                                                    @blur="validatePhoneNumber()"
                                                    :placeholder="phonePrefix === '+63' ? '9XX XXX XXXX' : '4X XXX XXXX'"
                                                    :maxlength="phoneMaxLen"
                                                    class="flex-1 text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-r-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all"
                                                    :class="phoneError ? 'border-red-500 dark:border-red-500' : ''">
                                            </div>
                                            <p x-show="phoneError" x-text="phoneError" x-cloak class="mt-1 text-[10px] text-red-500"></p>
                                            <p class="mt-0.5 text-[10px] text-gray-400 dark:text-gray-500" x-show="!phoneError">
                                                <span x-show="phonePrefix === '+63'">10 digits starting with 9</span>
                                                <span x-show="phonePrefix === '+358'">7-9 digits starting with 4 or 5</span>
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Email (read-only) --}}
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-envelope text-xs text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Email</p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $profileUser->email ?? '—' }}</p>
                                </div>
                            </div>

                            {{-- Alternative Email --}}
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-at text-xs text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Alternative Email</p>
                                    <template x-if="!editing">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" x-text="form.alternative_email || '—'"></p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="email" x-model="form.alternative_email" placeholder="Enter alternative email"
                                            class="w-full text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                                    </template>
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-location-dot text-xs text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Address</p>
                                    <template x-if="!editing">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" x-text="form.location || '—'"></p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" x-model="form.location" placeholder="Enter address"
                                            class="w-full text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                                    </template>
                                </div>
                            </div>

                            {{-- Change Password (edit mode only) --}}
                            <template x-if="editing">
                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-4">
                                    <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 tracking-wider">Change Password</p>

                                    {{-- Current Password --}}
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-lock text-xs text-blue-500 dark:text-blue-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Current Password</p>
                                            <div class="relative">
                                                <input :type="showCurrentPw ? 'text' : 'password'" x-model="form.current_password"
                                                    :placeholder="hasPassword ? 'Enter current password' : 'No password set'"
                                                    :disabled="!hasPassword"
                                                    class="w-full text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 pr-8 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                                <button type="button" @click="showCurrentPw = !showCurrentPw" x-show="hasPassword"
                                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <svg x-show="!showCurrentPw" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                                    <svg x-show="showCurrentPw" x-cloak xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- New Password --}}
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-key text-xs text-blue-500 dark:text-blue-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">New Password</p>
                                            <div class="relative">
                                                <input :type="showNewPw ? 'text' : 'password'" x-model="form.new_password" placeholder="Enter new password"
                                                    class="w-full text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 pr-8 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                                                <button type="button" @click="showNewPw = !showNewPw"
                                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <svg x-show="!showNewPw" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                                    <svg x-show="showNewPw" x-cloak xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Confirm Password --}}
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-check-double text-xs text-blue-500 dark:text-blue-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Confirm Password</p>
                                            <div class="relative">
                                                <input :type="showConfirmPw ? 'text' : 'password'" x-model="form.new_password_confirmation" placeholder="Confirm new password"
                                                    class="w-full text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 pr-8 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                                                <button type="button" @click="showConfirmPw = !showConfirmPw"
                                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <svg x-show="!showConfirmPw" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                                    <svg x-show="showConfirmPw" x-cloak xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Password Strength Indicator --}}
                                    <x-material-ui.password-strength model="form.new_password" />
                                </div>
                            </template>
                        </div>

                        {{-- Save Button (edit mode) --}}
                        <div x-show="editing" x-transition class="mt-5">
                            <button @click="saveProfile()" :disabled="saving"
                                class="w-full py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <span x-show="!saving">Save Changes</span>
                                <span x-show="saving" class="flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-spinner fa-spin text-xs"></i> Saving...
                                </span>
                            </button>
                        </div>

                        {{-- Stats --}}
                        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-700">
                                <div class="flex flex-col items-center px-2 gap-0.5">
                                    <span class="font-bold text-base text-gray-900 dark:text-white">{{ $myApplications->count() }}</span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">Applied</span>
                                </div>
                                <div class="flex flex-col items-center px-2 gap-0.5">
                                    <span class="font-bold text-base text-yellow-500 dark:text-yellow-400">{{ $myApplications->whereIn('status', ['pending', 'reviewed'])->count() }}</span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">Pending</span>
                                </div>
                                <div class="flex flex-col items-center px-2 gap-0.5">
                                    <span class="font-bold text-base text-green-500 dark:text-green-400">{{ $myApplications->where('status', 'hired')->count() }}</span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">Hired</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
    function profileModal() {
        return {
            profileOpen: false,
            editing: false,
            saving: false,
            hasPassword: @json($hasPassword),
            showCurrentPw: false,
            showNewPw: false,
            showConfirmPw: false,
            // Phone prefix state
            phonePrefix: '+63',
            phoneFlag: '🇵🇭',
            phoneLocal: '',
            phoneMaxLen: 10,
            phoneDropOpen: false,
            phoneError: '',
            phoneStore: { '+63': '', '+358': '' },
            form: {
                phone: @json($profileUser->phone ?? ''),
                alternative_email: @json($profileUser->alternative_email ?? ''),
                location: @json($profileUser->location ?? ''),
                current_password: '',
                new_password: '',
                new_password_confirmation: '',
            },
            original: {},

            initPhone() {
                this.phoneStore = { '+63': '', '+358': '' };
                const ph = this.form.phone || '';
                if (ph.startsWith('+358')) {
                    this.phonePrefix = '+358'; this.phoneFlag = '🇫🇮'; this.phoneMaxLen = 9;
                    this.phoneLocal = ph.replace('+358', '').replace(/^0/, '');
                } else if (ph.startsWith('+63')) {
                    this.phonePrefix = '+63'; this.phoneFlag = '🇵🇭'; this.phoneMaxLen = 10;
                    this.phoneLocal = ph.replace('+63', '').replace(/^0/, '');
                } else {
                    this.phoneLocal = ph.replace(/[^\d]/g, '');
                }
                this.phoneStore[this.phonePrefix] = this.phoneLocal;
            },

            setPhonePrefix(prefix, flag, max) {
                this.phoneStore[this.phonePrefix] = this.phoneLocal;
                this.phonePrefix = prefix;
                this.phoneFlag = flag;
                this.phoneMaxLen = max;
                this.phoneError = '';
                this.phoneLocal = this.phoneStore[prefix] || '';
                this.syncPhone();
            },

            syncPhone() {
                this.form.phone = this.phoneLocal ? this.phonePrefix + this.phoneLocal : '';
            },

            validatePhoneNumber() {
                if (!this.phoneLocal) { this.phoneError = ''; return; }
                if (this.phonePrefix === '+63') {
                    if (this.phoneLocal.length !== 10) this.phoneError = 'Must be exactly 10 digits';
                    else if (!this.phoneLocal.startsWith('9')) this.phoneError = 'Must start with 9';
                    else this.phoneError = '';
                } else if (this.phonePrefix === '+358') {
                    if (this.phoneLocal.length < 7 || this.phoneLocal.length > 9) this.phoneError = 'Must be 7-9 digits';
                    else if (!/^[45]/.test(this.phoneLocal)) this.phoneError = 'Must start with 4 or 5';
                    else this.phoneError = '';
                }
            },

            openModal() {
                this.profileOpen = true;
                this.editing = false;
            },

            closeModal() {
                if (this.editing) {
                    this.cancelEditing();
                    return;
                }
                this.profileOpen = false;
            },

            startEditing() {
                this.original = { ...this.form };
                this.initPhone();
                this.editing = true;
            },

            cancelEditing() {
                this.form = { ...this.original };
                this.form.current_password = '';
                this.form.new_password = '';
                this.form.new_password_confirmation = '';
                this.showCurrentPw = false;
                this.showNewPw = false;
                this.showConfirmPw = false;
                this.phoneError = '';
                this.phoneDropOpen = false;
                this.editing = false;
            },

            async saveProfile() {
                // Validate phone if entered
                if (this.phoneLocal) {
                    this.validatePhoneNumber();
                    if (this.phoneError) {
                        window.showErrorDialog('Invalid Phone', this.phoneError);
                        return;
                    }
                }

                // Validate password strength if changing password
                if (this.form.new_password) {
                    if (this.form.new_password !== this.form.new_password_confirmation) {
                        window.showErrorDialog('Mismatch', 'New password and confirmation do not match.');
                        return;
                    }
                    if (this.form.new_password.length < 8) {
                        window.showErrorDialog('Too Short', 'Password must be at least 8 characters.');
                        return;
                    }
                    let pwScore = 0;
                    const p = this.form.new_password;
                    if (p.length > 5) pwScore++; if (p.length > 8) pwScore++;
                    if (/[A-Z]/.test(p)) pwScore++; if (/[a-z]/.test(p)) pwScore++;
                    if (/[0-9]/.test(p)) pwScore++; if (/[^A-Za-z0-9]/.test(p)) pwScore++;
                    if (pwScore < 5) {
                        window.showErrorDialog('Weak Password', 'Password must be at least Strong. Include uppercase, lowercase, numbers, and special characters.');
                        return;
                    }
                }

                try {
                    const confirmed = await window.showConfirmDialog(
                        'Update Profile',
                        'Are you sure you want to save these changes to your profile?',
                        'Save',
                        'Cancel'
                    );
                } catch {
                    return;
                }

                this.saving = true;

                try {
                    const res = await fetch('{{ route("applicant.profile.update") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    });

                    const data = await res.json();

                    if (res.ok && data.success) {
                        if (data.password_changed) {
                            window.showSuccessDialog('Password Changed', data.message || 'Please log in again with your new password.');
                            setTimeout(() => { window.location.href = '/'; }, 2000);
                            return;
                        }
                        this.editing = false;
                        this.original = { ...this.form };
                        this.form.current_password = '';
                        this.form.new_password = '';
                        this.form.new_password_confirmation = '';
                        this.showCurrentPw = false;
                        this.showNewPw = false;
                        this.showConfirmPw = false;
                        window.showSuccessDialog('Profile Updated', 'Your profile has been updated successfully.', 'OK');
                    } else {
                        const msg = data.message || Object.values(data.errors || {}).flat().join(', ') || 'Something went wrong.';
                        window.showErrorDialog('Update Failed', msg, 'OK');
                    }
                } catch (e) {
                    window.showErrorDialog('Update Failed', 'A network error occurred. Please try again.', 'OK');
                } finally {
                    this.saving = false;
                }
            }
        };
    }
    </script>
    @endpush

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
                const right = this.thumbPercent(this.salaryMax);
                return `left:${left}%;width:${right - left}%`;
            },

            startDrag(e, which) {
                this.activeThumb = which;
                e.target.closest('.elastic-slider-thumb').setPointerCapture(e.pointerId);

                const track = this.$refs.sliderTrack;
                const onMove = (ev) => {
                    const rect = track.getBoundingClientRect();
                    let pct = (ev.clientX - rect.left) / rect.width;
                    pct = Math.max(0, Math.min(1, pct));
                    const val = Math.round(this.salaryAbsMin + pct * (this.salaryAbsMax - this.salaryAbsMin));

                    if (which === 'min') {
                        this.salaryMin = Math.min(val, this.salaryMax);
                    } else {
                        this.salaryMax = Math.max(val, this.salaryMin);
                    }

                    // Bounce icons when near edges
                    if (pct <= 0.02) { this.sliderBounce = 'left'; }
                    else if (pct >= 0.98) { this.sliderBounce = 'right'; }
                    else { this.sliderBounce = null; }

                    this.applyFilters();
                };

                const onUp = () => {
                    this.activeThumb = null;
                    this.sliderBounce = null;
                    document.removeEventListener('pointermove', onMove);
                    document.removeEventListener('pointerup', onUp);
                };

                document.addEventListener('pointermove', onMove);
                document.addEventListener('pointerup', onUp);
            },

            handleTrackClick(e) {
                const rect = this.$refs.sliderTrack.getBoundingClientRect();
                let pct = (e.clientX - rect.left) / rect.width;
                pct = Math.max(0, Math.min(1, pct));
                const val = Math.round(this.salaryAbsMin + pct * (this.salaryAbsMax - this.salaryAbsMin));

                // Snap to closest thumb
                const distMin = Math.abs(val - this.salaryMin);
                const distMax = Math.abs(val - this.salaryMax);
                if (distMin <= distMax) {
                    this.salaryMin = Math.min(val, this.salaryMax);
                } else {
                    this.salaryMax = Math.max(val, this.salaryMin);
                }
                this.applyFilters();
            },

            applyFilters() {
                const cards = document.querySelectorAll('.avp-scroll > div');
                let count = 0;

                allJobs.forEach((job, i) => {
                    if (i >= cards.length) return;
                    const card = cards[i];
                    let show = true;

                    if (this.search) {
                        const q = this.search.toLowerCase();
                        if (!job.title.toLowerCase().includes(q)) show = false;
                    }

                    if (this.selectedTypes.length > 0) {
                        if (!this.selectedTypes.includes(job.type)) show = false;
                    }

                    if (this.selectedLocations.length > 0) {
                        if (!this.selectedLocations.includes(job.location)) show = false;
                    }

                    if (job.salary > 0 && (job.salary < this.salaryMin || job.salary > this.salaryMax)) {
                        show = false;
                    }

                    card.style.display = show ? '' : 'none';
                    if (show) count++;
                });

                this.visibleCount = count;
                const sidebarEl = document.getElementById('sidebar-visible-count');
                if (sidebarEl) sidebarEl.textContent = count;

                // Show/hide filtered empty state
                const scrollEl = document.querySelector('.avp-scroll');
                const emptyEl = document.getElementById('avp-filtered-empty');
                if (scrollEl && emptyEl) {
                    scrollEl.style.display = count === 0 ? 'none' : '';
                    emptyEl.classList.toggle('hidden', count > 0);
                }
            },

            resetFilters() {
                this.search = '';
                this.selectedTypes = [];
                this.selectedLocations = [];
                this.salaryMin = this.salaryAbsMin;
                this.salaryMax = this.salaryAbsMax;
                this.applyFilters();
            }
        };
    }
    </script>
    @endpush

    {{-- Auto-open apply modal if redirected from landing page recruitment --}}
    @if(!empty($pendingApply))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.dispatchEvent(new CustomEvent('open-apply-modal', {
                    detail: {
                        title: @json($pendingApply['job_title'] ?? ''),
                        type: @json($pendingApply['job_type'] ?? ''),
                        requiredDocs: @json($pendingApply['required_docs'] ?? []),
                    }
                }));
            }, 500);
        });
    </script>
    @endpush
    @endif

    <x-guided-tour tourName="applicant-dashboard" :steps="json_encode([
        [
            'title' => 'Welcome to the Job Portal',
            'description' => 'Find and apply for available positions here. Let us show you how to navigate the portal!',
            'side' => 'bottom',
            'align' => 'center',
        ],
        [
            'element' => '#sidebar',
            'title' => 'Navigation Menu',
            'description' => 'Access your Dashboard, Saved jobs, Interview schedules, and Withdrawn applications from here.',
            'side' => 'right',
            'align' => 'start',
        ],
        [
            'element' => '#tour-app-my-applications',
            'title' => 'My Applications',
            'description' => 'Track the status of all your submitted job applications here. See which ones are pending, under review, or have received responses.',
            'side' => 'bottom',
            'align' => 'center',
        ],
        [
            'element' => '#tour-app-available-jobs',
            'title' => 'Available Positions',
            'description' => 'Browse all open job positions. Click on any job to view details and apply. You can also save jobs for later.',
            'side' => 'top',
            'align' => 'center',
        ],
    ])" />
</x-layouts.general-applicant>
