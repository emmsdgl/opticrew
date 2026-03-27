@extends('components.layouts.general-landing')

@section('title', 'Job Opportunities')
@push('styles')
    <style>
        [x-cloak] { display: none !important; }

        body {
            background-image: none;
            background-color: #F6FAFD;
        }

        .dark body {
            background-color: #111827;
        }

        @keyframes ripple {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(2.2); opacity: 0; }
        }

        /* Custom scrollbar */
        .scrollbar-custom {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }

        .scrollbar-custom::-webkit-scrollbar {
            width: 6px;
        }

        .scrollbar-custom::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-custom::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        .scrollbar-custom::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.7);
        }

        .dark .scrollbar-custom {
            scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
        }

        .dark .scrollbar-custom::-webkit-scrollbar-thumb {
            background-color: rgba(75, 85, 99, 0.5);
        }

        .dark .scrollbar-custom::-webkit-scrollbar-thumb:hover {
            background-color: rgba(75, 85, 99, 0.7);
        }

        /* Dual range slider */
        .range-slider-wrap {
            position: relative;
            height: 6px;
            margin: 12px 0;
        }

        .range-slider-track {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            border-radius: 3px;
            background: #e5e7eb;
        }

        .dark .range-slider-track {
            background: #4b5563;
        }

        .range-slider-fill {
            position: absolute;
            top: 0;
            height: 6px;
            border-radius: 3px;
            background: #3b82f6;
        }

        .range-slider-wrap input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            position: absolute;
            top: -6px;
            left: 0;
            width: 100%;
            height: 18px;
            background: transparent;
            pointer-events: none;
            outline: none;
            margin: 0;
        }

        .range-slider-wrap input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #3b82f6;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
            pointer-events: all;
        }

        .range-slider-wrap input[type="range"]::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #3b82f6;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
            pointer-events: all;
        }

        /* Beam Circle Orbits */
        @keyframes orbit-spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes orbit-counter {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(-360deg);
            }
        }

        @keyframes center-pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .orbit-ring {
            position: absolute;
            border-radius: 50%;
            border-style: dashed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .orbit-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .orbit-icon {
            position: absolute;
            top: 50%;
            border-radius: 50%;
            display: grid;
            place-content: center;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.15);
        }

        /* Aurora text effect */
        .aurora-text {
            background: linear-gradient(135deg, #22d3ee, #4169e1, #06b6d4, #3b82f6, #22d3ee);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: aurora-text-shift 6s ease-in-out infinite;
        }

        @keyframes aurora-text-shift {
            0% {
                background-position: 0% 50%;
            }

            25% {
                background-position: 50% 100%;
            }

            50% {
                background-position: 100% 50%;
            }

            75% {
                background-position: 50% 0%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
        /* Detail panel icon colors */
        .detail-icon[data-icon-color="green"] { color: #16a34a; }
        .detail-icon[data-icon-color="purple"] { color: #9333ea; }
        .detail-icon[data-icon-color="orange"] { color: #ea580c; }
        .detail-icon[data-icon-color="red"] { color: #dc2626; }
        .detail-icon[data-icon-color="blue"] { color: #2563eb; }
        .detail-icon:not([data-icon-color]) { color: #2563eb; }

        .dark .detail-icon[data-icon-color="green"] { color: #4ade80; }
        .dark .detail-icon[data-icon-color="purple"] { color: #c084fc; }
        .dark .detail-icon[data-icon-color="orange"] { color: #fb923c; }
        .dark .detail-icon[data-icon-color="red"] { color: #f87171; }
        .dark .detail-icon[data-icon-color="blue"] { color: #60a5fa; }
        .dark .detail-icon:not([data-icon-color]) { color: #60a5fa; }
    </style>
@endpush

@section('content')
    {{-- Hero Section --}}
    <section class="w-full bg-gradient-to-br bg-white dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 overflow-hidden">
        <div class="max-w-[1600px] mx-auto px-6 md:px-12 lg:px-16 py-16 md:py-24 lg:py-28">
            <div class="flex flex-col lg:flex-row items-center justify-center gap-2">
                {{-- Left Content --}}
                <div class="flex flex-col text-center lg:text-left max-w-2xl px-6 items-center justify-center">
                    <div class="w-full px-10">
                        <p class="text-sm font-bold text-blue-600 dark:text-blue-400 mb-6">Welcome to Fin-noys</p>
                        <h1
                            class="text-4xl md:text-5xl lg:text-[3.5rem] font-black text-gray-900 dark:text-white leading-tight mb-6">
                            Explore<br>
                            opportunities<br>
                            <span class="aurora-text font-extrabold">with Fin-noys.</span>
                        </h1>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-sm md:text-sm mb-10 max-w-md mx-auto lg:mx-0 leading-relaxed">
                            Find a job according to your interest simply click on search
                            and choose category according to your skills
                        </p>
                    </div>

                    {{-- Hero Search Bar --}}
                    <div id="heroSearchBar"
                        class="bg-white dark:bg-gray-800 rounded-full shadow-lg shadow-gray-200/60 dark:shadow-black/20 p-2 flex items-center gap-2 max-w-lg mx-auto lg:mx-0">
                        <div class="flex items-center gap-2 flex-1 pl-4">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" id="heroSearchInput" placeholder="Job Title or keyword"
                                class="w-full bg-transparent border-none text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-0 py-2"
                                onkeydown="if(event.key==='Enter'){event.preventDefault();window.heroSearchGo();}">
                        </div>
                        <div id="heroLocWrapper"
                            class="hidden sm:flex items-center gap-2 flex-1 pl-4 border-l border-gray-200 dark:border-gray-600"
                            x-data="heroLocationDropdown()"
                            @click.away="openLoc = false"
                            @resize.window="if(openLoc) positionDropdown()">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <input type="hidden" id="heroLocationInput" :value="selectedLoc">
                            {{-- Dropdown trigger --}}
                            <button type="button" @click="toggle()" x-ref="locTrigger"
                                class="w-full flex items-center justify-between bg-transparent text-sm py-2 pr-2 cursor-pointer focus:outline-none focus:ring-0"
                                :class="selectedLoc ? 'text-gray-700 dark:text-gray-200' : 'text-gray-400'">
                                <span x-text="selectedLabel" class="truncate"></span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-1 transition-transform duration-200"
                                    :class="openLoc ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            {{-- Dropdown content (teleported to body to avoid overflow clipping) --}}
                            <template x-teleport="body">
                                <div x-show="openLoc" x-cloak
                                    x-ref="locDropdown"
                                    @click.away="openLoc = false"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-1"
                                    class="fixed z-[9999] bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-1.5 max-h-60 overflow-y-auto scrollbar-custom"
                                    :style="dropdownStyle">
                                    {{-- All Locations option --}}
                                    <button type="button"
                                        @click="selectLocation('', 'All Locations')"
                                        class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center gap-2"
                                        :class="selectedLoc === '' ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                                        <svg class="w-4 h-4 flex-shrink-0" :class="selectedLoc === '' ? 'text-blue-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>All Locations</span>
                                    </button>
                                    @php
                                        $heroLocations = ($jobPostings ?? collect())->pluck('location')->unique()->sort()->values();
                                    @endphp
                                    @foreach($heroLocations as $loc)
                                        <button type="button"
                                            @click="selectLocation('{{ $loc }}', '{{ $loc }}')"
                                            class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center gap-2"
                                            :class="selectedLoc === '{{ $loc }}' ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                                            <svg class="w-4 h-4 flex-shrink-0" :class="selectedLoc === '{{ $loc }}' ? 'text-blue-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span>{{ $loc }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </template>
                        </div>
                        <button
                            class="flex-shrink-0 bg-blue-500 hover:bg-blue-600 text-white text-sm font-normal px-6 py-3 rounded-full transition-colors"
                            onclick="window.heroSearchGo()">
                            Search
                        </button>
                    </div>
                </div>

                {{-- Beam Circle Orbits --}}
                <div class="flex justify-center lg:justify-center">
                    <div class="relative" style="width: 480px; height: 480px;">
                        {{-- Orbit 1: Briefcase (innermost) --}}
                        <div class="orbit-ring border-blue-950 dark:border-white/40"
                            style="width: 25%; height: 25%; border-width: 1.5px;"></div>
                        <div class="orbit-container" style="animation: orbit-spin 7s linear infinite;">
                            <div class="orbit-icon bg-blue-600 dark:bg-white"
                                style="left: calc(50% + 12.5%); transform: translate(-50%, -50%); width: 36px; height: 36px; animation: orbit-counter 7s linear infinite;">
                                <svg class="w-5 h-5 text-white dark:text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </div>

                        {{-- Orbit 2: Chat/Message --}}
                        <div class="orbit-ring border-blue-950 dark:border-white/40"
                            style="width: 45%; height: 45%; border-width: 1.5px;"></div>
                        <div class="orbit-container" style="animation: orbit-spin 12s linear infinite;">
                            <div class="orbit-icon bg-blue-600 dark:bg-white"
                                style="left: calc(50% + 22.5%); transform: translate(-50%, -50%); width: 40px; height: 40px; animation: orbit-counter 12s linear infinite;">
                                <svg class="w-5 h-5 text-white dark:text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Orbit 3: Document/Resume --}}
                        <div class="orbit-ring border-blue-950 dark:border-white/40"
                            style="width: 65%; height: 65%; border-width: 1.5px;"></div>
                        <div class="orbit-container" style="animation: orbit-spin 9s linear infinite;">
                            <div class="orbit-icon bg-blue-600 dark:bg-white"
                                style="left: calc(50% + 32.5%); transform: translate(-50%, -50%); width: 44px; height: 44px; animation: orbit-counter 9s linear infinite;">
                                <svg class="w-6 h-6 text-white dark:text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Orbit 4: Location (outermost) --}}
                        <div class="orbit-ring border-blue-950 dark:border-white/40"
                            style="width: 85%; height: 85%; border-width: 1.5px;"></div>
                        <div class="orbit-container" style="animation: orbit-spin 15s linear infinite;">
                            <div class="orbit-icon bg-blue-600 dark:bg-white"
                                style="left: calc(50% + 42.5%); transform: translate(-50%, -50%); width: 48px; height: 48px; animation: orbit-counter 15s linear infinite;">
                                <svg class="w-6 h-6 text-white dark:text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Center Icon: Briefcase --}}
                        <div class="absolute inset-0 grid place-content-center z-10">
                            <div class="rounded-full bg-blue-600 dark:bg-white shadow-lg grid place-content-center"
                                style="width: 64px; height: 64px; animation: center-pulse 2s ease-in-out infinite;">
                                <svg class="w-8 h-8 text-white dark:text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Job Listings Section --}}
    <div id="jobListingsSection" class="w-full min-h-screen bg-[#F6FAFD] dark:bg-gray-900 p-4 md:p-6 lg:p-12"
        x-data="recruitmentPage()" x-init="init()">

        <div class="max-w-[1600px] mx-auto p-3">


            <div class="flex flex-col lg:flex-row gap-6">

                {{-- Left Sidebar - Filters (hidden by default, toggled) --}}
                <aside x-show="showFilters" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-4"
                    class="w-full lg:w-80 xl:w-96 flex-shrink-0">
                    <div
                        class="bg-white shadow-lg dark:bg-gray-900 rounded-2xl p-6 sticky top-6 space-y-6 max-h-[calc(100vh-3rem)] overflow-y-auto scrollbar-custom">

                        {{-- Job Type Filter --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Job Type</h3>
                                <button @click="clearJobTypes()"
                                    class="text-xs text-red-500 hover:text-red-600 font-medium">Clear all</button>
                            </div>
                            <div class="space-y-2.5">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" value="full-time" x-model="selectedTypes"
                                        @change="applyFilters()"
                                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                    <span
                                        class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Full
                                        time</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" value="part-time" x-model="selectedTypes"
                                        @change="applyFilters()"
                                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                    <span
                                        class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Part
                                        time</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" value="remote" x-model="selectedTypes"
                                        @change="applyFilters()"
                                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                    <span
                                        class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Remote</span>
                                </label>
                            </div>
                        </div>

                        {{-- Salary Range Filter --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Salary Range</h3>
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">$<span
                                        x-text="salaryMin"></span> &ndash; $<span x-text="salaryMax"></span></span>
                            </div>
                            <div class="px-1">
                                <div class="range-slider-wrap" x-init="$nextTick(() => updateRangeSlider())" x-effect="updateRangeSlider()">
                                    <div class="range-slider-track"></div>
                                    <div class="range-slider-fill" :style="rangeFillStyle()"></div>
                                    <input type="range" x-model.number="salaryMin" :min="salaryAbsMin"
                                        :max="salaryAbsMax" step="1"
                                        @input="if(salaryMin > salaryMax) salaryMin = salaryMax; applyFilters()">
                                    <input type="range" x-model.number="salaryMax" :min="salaryAbsMin"
                                        :max="salaryAbsMax" step="1"
                                        @input="if(salaryMax < salaryMin) salaryMax = salaryMin; applyFilters()">
                                </div>
                                <div class="flex items-center justify-between mt-4">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">$<span
                                            x-text="salaryAbsMin"></span></span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">$<span
                                            x-text="salaryAbsMax"></span></span>
                                </div>
                            </div>
                        </div>

                        {{-- Location Filter --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Location</h3>
                                <button @click="selectedLocations = []; applyFilters()"
                                    x-show="selectedLocations.length > 0"
                                    class="text-xs text-red-500 hover:text-red-600 font-medium">Clear</button>
                            </div>
                            <div class="space-y-2.5 max-h-48 overflow-y-auto scrollbar-custom">
                                <template x-for="loc in locations" :key="loc">
                                    <label class="flex items-center justify-between cursor-pointer group">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" :value="loc" x-model="selectedLocations"
                                                @change="applyFilters()"
                                                class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                            <span
                                                class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white"
                                                x-text="loc"></span>
                                        </div>
                                        <span
                                            class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full"
                                            x-text="locationCounts[loc] || 0"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        {{-- Job Categories Filter --}}
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Job Categories</h3>
                            <div class="space-y-2.5">
                                <template x-for="cat in categories" :key="cat.value">
                                    <label class="flex items-center justify-between cursor-pointer group">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" :value="cat.value" x-model="selectedCategories"
                                                @change="applyFilters()"
                                                class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                            <span
                                                class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white"
                                                x-text="cat.label"></span>
                                        </div>
                                        <span
                                            class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full"
                                            x-text="cat.count"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                    </div>
                </aside>

                {{-- Middle - Job Cards List --}}
                <div class="flex-1 min-w-0">
                    {{-- Filter Toggle --}}
                    <div class="flex items-center gap-3 mb-4">
                        <button @click="showFilters = !showFilters"
                            class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            :class="showFilters ? 'text-blue-600 dark:text-blue-400 border-blue-300 dark:border-blue-600' : 'text-gray-400'">
                            <i class="fas fa-sliders-h text-sm"></i>
                        </button>
                        <span class="text-sm text-gray-500 dark:text-gray-400" x-show="searchQuery">
                            Searching: "<span x-text="searchQuery" class="font-medium text-gray-700 dark:text-gray-200"></span>"
                            <button @click="searchQuery = ''; selectedLocations = []; applyFilters();" class="ml-1 text-red-500 hover:text-red-600 text-xs font-medium">Clear</button>
                        </span>
                    </div>

                    {{-- Results Header --}}
                    <div class="flex items-center justify-between mb-4 px-8">
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Jobs For You</span>
                            <span
                                class="text-sm text-blue-600 dark:text-blue-400 font-semibold cursor-pointer">Popular</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400">Sort:</span>
                            <select x-model="sortBy" @change="applyFilters()"
                                class="text-xs px-2 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 dark:text-gray-300">
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest</option>
                                <option value="salary-high">Salary: High to Low</option>
                                <option value="salary-low">Salary: Low to High</option>
                            </select>
                        </div>
                    </div>

                    {{-- Scrollable Job Cards --}}
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-[calc(100vh-8rem)] overflow-y-auto overflow-x-visible scrollbar-custom px-1 pb-2"
                        :class="!showFilters ? 'xl:grid-cols-4' : 'xl:grid-cols-3'">
                        @forelse($jobPostings ?? [] as $job)
                            @php
                                $iconBgClass = match ($job->icon_color) {
                                    'green' => 'bg-green-50 dark:bg-green-900/30',
                                    'purple' => 'bg-purple-50 dark:bg-purple-900/30',
                                    'orange' => 'bg-orange-50 dark:bg-orange-900/30',
                                    'red' => 'bg-red-50 dark:bg-red-900/30',
                                    default => 'bg-blue-50 dark:bg-blue-900/30',
                                };
                                $iconTextClass = match ($job->icon_color) {
                                    'green' => 'text-green-600 dark:text-green-400',
                                    'purple' => 'text-purple-600 dark:text-purple-400',
                                    'orange' => 'text-orange-600 dark:text-orange-400',
                                    'red' => 'text-red-600 dark:text-red-400',
                                    default => 'text-blue-600 dark:text-blue-400',
                                };
                                $typeBadgeClass = match ($job->type) {
                                    'part-time'
                                        => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                                    'remote'
                                        => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
                                    default => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                                };
                                // Reverse-map icon to category
                                $categoryMap = [
                                    'fa-broom' => 'cleaning',
                                    'fa-dolly' => 'logistics',
                                    'fa-user-tie' => 'management',
                                    'fa-headset' => 'customer-service',
                                    'fa-spray-can' => 'maintenance',
                                    'fa-clipboard-check' => 'administration',
                                    'fa-users' => 'human-resources',
                                    'fa-briefcase' => 'general',
                                ];
                                $jobCategory = $categoryMap[$job->icon] ?? 'other';
                            @endphp
                            <div class="job-item" data-job-id="{{ $job->id }}" data-type="{{ $job->type }}"
                                data-category="{{ $jobCategory }}" data-location="{{ $job->location }}"
                                @click="selectJob({{ $job->id }})"
                                :style="filteredIds.includes({{ $job->id }}) ? '' : 'display:none'">
                                <div class="job-card bg-white dark:bg-gray-800 rounded-2xl p-4 border-2 transition-all duration-200 cursor-pointer hover:shadow-lg flex flex-col h-full"
                                    :class="selectedJobId === {{ $job->id }} ?
                                        'border-blue-500 dark:border-blue-400 shadow-lg bg-blue-50/50 dark:bg-blue-900/10' :
                                        'border-transparent shadow-sm hover:border-blue-300 dark:hover:border-blue-500/50'">

                                    {{-- Top row: Icon + Title + Heart --}}
                                    <div class="flex items-start gap-3 mb-3">
                                        <div
                                            class="w-10 h-10 {{ $iconBgClass }} rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i class="fas {{ $job->icon }} {{ $iconTextClass }} text-base"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-base font-bold text-gray-900 dark:text-white leading-snug truncate"
                                                title="{{ $job->title }}">
                                                {{ $job->title }}</h3>
                                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">
                                                {{ $job->location }}
                                            </p>
                                        </div>
                                        <button @click.stop
                                            class="text-gray-300 hover:text-red-400 dark:text-gray-600 dark:hover:text-red-400 transition-colors flex-shrink-0">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>

                                    {{-- Type Badges --}}
                                    <div class="flex flex-wrap gap-1.5 mb-3">
                                        <span
                                            class="px-2.5 py-0.5 {{ $typeBadgeClass }} text-sm font-medium rounded-full">
                                            {{ $job->type_badge }}
                                        </span>
                                    </div>

                                    {{-- Description --}}
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400 mb-3 line-clamp-2 flex-1 leading-relaxed">
                                        {{ $job->description }}
                                    </p>

                                    {{-- Footer: Salary + Posted --}}
                                    <div
                                        class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700 mt-auto">
                                        <span
                                            class="text-base font-bold text-gray-900 dark:text-white">&euro;{{ $job->salary }}<span
                                                class="text-sm font-normal text-gray-400">/hr</span></span>
                                        <span class="text-sm text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                            <i class="far fa-clock"></i>
                                            Posted {{ $job->created_at ? $job->created_at->diffForHumans() : '' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-16">
                                <i class="fas fa-briefcase text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-base font-medium">No job openings available
                                    at the moment.</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Please check back later for new
                                    opportunities.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- No results from filter --}}
                    <div x-show="visibleCount === 0 && totalJobs > 0" x-cloak class="text-center py-16">
                        <i class="fas fa-search text-gray-300 dark:text-gray-600 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">No jobs match your filters.</p>
                        <button @click="clearAllFilters()"
                            class="mt-3 text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">Clear all
                            filters</button>
                    </div>
                </div>

            </div>
        </div>

        {{-- Job Detail Slide-In Drawer — teleported to body to escape parent transform stacking context --}}
        <template x-teleport="body">
        <div @keydown.escape.window="if(showDetail){ showDetail = false; selectedJobId = null; selectedJob = null; }"
            x-init="$watch('showDetail', val => { if (val) lockScroll(); else unlockScroll(); })">
            {{-- Backdrop --}}
            <div x-show="showDetail" x-cloak
                class="fixed inset-0 z-[99] bg-black/40 backdrop-blur-sm"
                @click="showDetail = false; selectedJobId = null; selectedJob = null;"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
            </div>

            {{-- Drawer Panel --}}
            <div x-show="showDetail" x-cloak
                class="fixed inset-y-0 right-0 z-[100] w-full sm:w-[420px] md:w-[480px] flex flex-col bg-white dark:bg-gray-900 shadow-2xl"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full">

                    <template x-if="selectedJob">
                        <div class="flex flex-col h-full min-h-0">
                            {{-- Drawer Header --}}
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                                <h2 class="text-base font-bold text-gray-900 dark:text-white">Job Details</h2>
                                <button @click="showDetail = false; selectedJobId = null; selectedJob = null;"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Scrollable content --}}
                            <div class="flex-1 overflow-y-auto scrollbar-custom min-h-0">
                                {{-- Top section: Icon + Title + Location --}}
                                <div class="px-6 pt-8 pb-5 flex flex-col items-center text-center border-b border-gray-100 dark:border-gray-700">
                                    {{-- Company Icon --}}
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4 shadow-sm dark:shadow-none"
                                        :class="{
                                            'bg-green-50 dark:bg-green-900/30': selectedJob.iconColor === 'green',
                                            'bg-purple-50 dark:bg-purple-900/30': selectedJob.iconColor === 'purple',
                                            'bg-orange-50 dark:bg-orange-900/30': selectedJob.iconColor === 'orange',
                                            'bg-red-50 dark:bg-red-900/30': selectedJob.iconColor === 'red',
                                            'bg-blue-50 dark:bg-blue-900/30': !['green', 'purple', 'orange', 'red'].includes(selectedJob.iconColor)
                                        }">
                                        <i class="fas text-2xl detail-icon"
                                            :class="selectedJob.icon"
                                            :data-icon-color="selectedJob.iconColor"></i>
                                    </div>

                                    {{-- Title --}}
                                    <h2 class="text-lg font-bold text-gray-900 dark:text-white leading-snug mb-1"
                                        x-text="selectedJob.title"></h2>

                                    {{-- Location --}}
                                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedJob.location"></p>

                                    {{-- Salary + Type badges --}}
                                    <div class="flex items-center gap-2 mt-3">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                                            &euro;<span x-text="selectedJob.salary"></span><span class="text-xs font-normal text-gray-400">/hr</span>
                                        </span>
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full"
                                            :class="{
                                                'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400': selectedJob.type === 'part-time',
                                                'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400': selectedJob.type === 'remote',
                                                'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400': selectedJob.type === 'full-time' || !['part-time','remote'].includes(selectedJob.type)
                                            }"
                                            x-text="selectedJob.typeBadge || selectedJob.type"></span>
                                    </div>
                                </div>

                                {{-- About the Job --}}
                                <div class="px-6 py-5">
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">About the Job</h3>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                        <p class="w-full text-justify" x-text="selectedJob.description"></p>
                                    </div>
                                </div>

                                {{-- Required Skills --}}
                                <div class="px-6 py-5 border-t border-gray-100 dark:border-gray-700"
                                    x-show="selectedJob.requiredSkills && selectedJob.requiredSkills.length > 0">
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Required Skills</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="skill in selectedJob.requiredSkills" :key="skill">
                                            <span class="px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300"
                                                x-text="skill"></span>
                                        </template>
                                    </div>
                                </div>

                                {{-- Required Documents --}}
                                <div class="px-6 py-5 border-t border-gray-100 dark:border-gray-700"
                                    x-show="selectedJob.requiredDocs && selectedJob.requiredDocs.length > 0">
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Required Documents</h3>
                                    <ul class="space-y-2">
                                        <template x-for="doc in selectedJob.requiredDocs"
                                            :key="typeof doc === 'object' ? doc.name : doc">
                                            <li class="flex items-center gap-2.5 text-sm text-gray-600 dark:text-gray-400">
                                                <i class="fas fa-file-alt text-xs text-gray-400 dark:text-gray-500"></i>
                                                <span>
                                                    <span x-text="typeof doc === 'object' ? doc.name : doc"></span>
                                                    <template x-if="typeof doc === 'object' && doc.type">
                                                        <span class="ml-1 px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-900/40 text-[10px] uppercase font-bold text-purple-500 dark:text-purple-400"
                                                            x-text="doc.type"></span>
                                                    </template>
                                                </span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                {{-- Benefits --}}
                                <div class="px-6 py-5 border-t border-gray-100 dark:border-gray-700"
                                    x-show="selectedJob.benefits && selectedJob.benefits.length > 0">
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Benefits</h3>
                                    <ul class="space-y-2">
                                        <template x-for="benefit in selectedJob.benefits" :key="benefit">
                                            <li class="flex items-start gap-2.5 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                                <div class="w-5 h-5 bg-blue-50 dark:bg-blue-900/30 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <i class="fas fa-check text-blue-600 dark:text-blue-400 text-[8px]"></i>
                                                </div>
                                                <span x-text="benefit"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>

                            {{-- Fixed bottom: Apply Now + Heart --}}
                            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-900 flex items-center gap-3 flex-shrink-0">
                                <button @click="openApplicationModal()"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-full transition-all shadow-md text-sm font-semibold hover:shadow-lg">
                                    Apply Now
                                </button>
                                <button @click.stop
                                    class="w-11 h-11 bg-gray-100 dark:bg-gray-700 text-gray-300 hover:text-red-400 dark:text-gray-600 dark:hover:text-red-400 rounded-full transition-colors flex items-center justify-center flex-shrink-0">
                                    <i class="far fa-heart text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

    </div>
    </template>

    {{-- Application Modal — moved to body via JS to escape parent transform stacking context --}}
    <div id="applicationModal" class="fixed inset-0 bg-black bg-opacity-50 z-[200] flex items-center justify-center p-4"
        style="display: none;" x-data="{
            termsOpened: document.cookie.includes('finnoys_terms_accepted=1'),
            privacyOpened: document.cookie.includes('finnoys_policy_accepted=1'),
            agreed: document.cookie.includes('finnoys_terms_accepted=1') && document.cookie.includes('finnoys_policy_accepted=1'),
            openRecruitTermsModal() {
                document.getElementById('recruit-terms-modal').style.display = 'flex';
                lockScroll();
            },
            openRecruitPrivacyModal() {
                document.getElementById('recruit-privacy-modal').style.display = 'flex';
                lockScroll();
            },
            markTermsRead() {
                this.termsOpened = true;
                document.cookie = 'finnoys_terms_accepted=1; path=/; max-age=' + (30 * 24 * 60 * 60);
                document.getElementById('recruit-terms-modal').style.display = 'none';
                unlockScroll();
                if (window.showSuccessDialog) window.showSuccessDialog('Terms Accepted', 'You have read and accepted the Terms & Conditions.');
            },
            markPrivacyRead() {
                this.privacyOpened = true;
                document.cookie = 'finnoys_policy_accepted=1; path=/; max-age=' + (30 * 24 * 60 * 60);
                document.getElementById('recruit-privacy-modal').style.display = 'none';
                unlockScroll();
                if (window.showSuccessDialog) window.showSuccessDialog('Privacy Policy Accepted', 'You have read and accepted the Privacy Policy.');
            },
            get checkboxEnabled() { return this.termsOpened && this.privacyOpened; },
            get canSubmit() { return this.agreed && this.checkboxEnabled; }
        }">
        <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-md w-full max-h-[90vh] overflow-y-auto scrollbar-custom relative">
            {{-- Close Button --}}
            <button onclick="closeApplicationModal()" type="button"
                class="absolute top-4 right-4 z-20 w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full transition-colors">
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Modal Body --}}
            <div class="p-6">
                <p class="text-sm text-center mr-3 my-6 w-full text-gray-900 dark:text-white">
                    Application Form
                </p>
                <p class="text-3xl font-bold text-center mr-3 my-6 w-full text-gray-900 dark:text-white">
                    Want to proceed <br>with your application?
                </p>
                <form id="applicationForm" action="{{ route('recruitment.google.apply') }}" method="POST"
                    class="space-y-4 p-3">
                    @csrf
                    <input type="hidden" name="job_title" id="applicationJobTitle">
                    <input type="hidden" name="job_type" id="applicationJobType">
                    <input type="hidden" name="required_docs" id="applicationRequiredDocs">

                    {{-- Google Sign-In Info --}}
                    <div class="text-center">
                        <div class="relative w-16 h-16 mx-auto my-4">
                            <div class="absolute inset-0 rounded-full bg-blue-400/20 dark:bg-blue-500/15 animate-[ripple_2s_ease-out_infinite]"></div>
                            <div class="absolute inset-0 rounded-full bg-blue-400/15 dark:bg-blue-500/10 animate-[ripple_2s_ease-out_0.6s_infinite]"></div>
                            <div class="absolute inset-0 rounded-full bg-blue-400/10 dark:bg-blue-500/5 animate-[ripple_2s_ease-out_1.2s_infinite]"></div>
                            <div class="relative w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <i class="fab fa-google text-2xl text-blue-600"></i>
                            </div>
                        </div>
                        <p class="flex flex-col text-sm text-gray-600 dark:text-gray-400 leading-relaxed mt-8 mb-4">
                            <span class="font-normal">Sign in with your Google account to apply.</span> <span
                                class="font-normal">Your email will be used for application updates.</span>
                        </p>
                    </div>

                    {{-- Terms and Conditions Checkbox --}}
                    <div class="px-4 text-sm" x-show="!(termsOpened && privacyOpened && agreed)">
                        <div class="flex items-start space-x-3">
                            <div class="relative mt-0.5 flex-shrink-0" @click="if(!checkboxEnabled) { window.showErrorDialog('Action Required', 'Please open and read both the Terms & Conditions and Privacy Policy before you can agree.'); }">
                                <input type="checkbox" x-model="agreed" :disabled="!checkboxEnabled"
                                    class="w-5 h-5 rounded border-2 border-gray-400 bg-transparent appearance-none cursor-pointer checked:bg-blue-600 checked:border-blue-600 disabled:opacity-40 disabled:cursor-not-allowed"
                                    :class="!checkboxEnabled && 'pointer-events-none'">
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                                By signing in, I agree to the
                                <button type="button" @click.stop="openRecruitTermsModal()"
                                    class="text-sm text-blue-600 hover:underline font-semibold bg-transparent border-0 p-0 cursor-pointer text-xs inline">
                                    Terms & Conditions
                                </button>
                                <span x-show="termsOpened" class="text-green-500 text-[10px]"><i class="fas fa-check-circle"></i></span>
                                and
                                <button type="button" @click.stop="openRecruitPrivacyModal()"
                                    class="text-sm text-blue-600 hover:underline font-semibold bg-transparent border-0 p-0 cursor-pointer text-xs inline">
                                    Privacy Policy
                                </button>
                                <span x-show="privacyOpened" class="text-green-500 text-xs"><i class="fas fa-check-circle"></i></span>.
                            </span>
                        </div>
                    </div>
                    {{-- Already accepted indicator --}}
                    <div class="px-4 text-sm" x-show="termsOpened && privacyOpened && agreed" x-cloak>
                        <div class="flex flex-col items-center gap-1 text-center text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle text-sm"></i>
                            <span class="text-sm">The
                                <button type="button" @click.stop="openRecruitTermsModal()" class="text-green-700 dark:text-green-300 font-semibold underline hover:text-green-800 dark:hover:text-green-200 bg-transparent border-0 p-0 cursor-pointer text-sm inline">Terms & Conditions</button>
                                and
                                <button type="button" @click.stop="openRecruitPrivacyModal()" class="text-green-700 dark:text-green-300 font-semibold underline hover:text-green-800 dark:hover:text-green-200 bg-transparent border-0 p-0 cursor-pointer text-sm inline">Privacy Policy</button>
                                had already been read and accepted
                            </span>
                        </div>
                    </div>

                    {{-- Sign in with Google to Apply --}}
                    <div class="px-4 py-2">
                        <button type="submit" id="googleApplyBtn" :disabled="!canSubmit"
                            :class="!canSubmit ? 'opacity-50 cursor-not-allowed' :
                            'hover:bg-gray-50 dark:hover:bg-gray-600 shadow-lg hover:shadow-xl'"
                            class="w-full flex items-center justify-center gap-3 py-3.5 px-4 border border-gray-300 dark:border-gray-600 rounded-full bg-white dark:bg-gray-700 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 48 48">
                                <path fill="#EA4335"
                                    d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                                <path fill="#4285F4"
                                    d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                                <path fill="#FBBC05"
                                    d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                                <path fill="#34A853"
                                    d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sign in with Google to
                                Apply</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recruitment Terms & Conditions Modal -->
    <div id="recruit-terms-modal" class="fixed inset-0 z-[250] flex items-center justify-center bg-black/50 p-4" style="display: none;">
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Terms & Conditions</h2>
                <button type="button" onclick="document.getElementById('recruit-terms-modal').style.display='none'; unlockScroll();" class="w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full transition-colors">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5 overflow-y-auto flex-1">
                <div class="space-y-4 text-gray-700 dark:text-gray-300">
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Last Updated: November 5, 2025</p>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mt-4">1. Acceptance of Terms</h3>
                    <p class="text-sm leading-relaxed">By accessing or using the Castcrew workforce management and scheduling platform (the "System"), you ("User") agree to comply with and be bound by these Terms and Conditions.</p>
                    <p class="text-sm leading-relaxed">If you do not agree with any part of these Terms, you must refrain from using the System.</p>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mt-4">2. System Operations and Allocation Rules</h3>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-3">2.1 Workforce Allocation</h4>
                    <p class="text-sm leading-relaxed">Castcrew automatically determines the optimal number of employees required for each task based on employee availability, pending workload, budget constraints, and utilization targets.</p>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-3">2.2 Team Composition and Driver Requirement</h4>
                    <p class="text-sm leading-relaxed">Each assigned team must include at least one employee registered as having valid driving skills.</p>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-3">2.3 Task Prioritization</h4>
                    <p class="text-sm leading-relaxed">Tasks labeled with an "Arrival Status" are assigned the highest scheduling priority.</p>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-3">2.4 Schedule Generation and Re-Optimization</h4>
                    <p class="text-sm leading-relaxed">Schedules are generated automatically. If a new task is added before a schedule is finalized, the System will regenerate an optimized schedule.</p>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-3">2.5 Working Hours Compliance</h4>
                    <p class="text-sm leading-relaxed">The System enforces a maximum of 12 working hours per day per employee, in compliance with Finnish labor standards.</p>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mt-4">3. System Authority and Finality</h3>
                    <p class="text-sm leading-relaxed">All task assignments and schedules are the outcome of automated, rule-based optimization and are deemed final for operational purposes.</p>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mt-4">4. Modifications to System Rules</h3>
                    <p class="text-sm leading-relaxed">Castcrew reserves the right to modify these Terms at any time. Continued use constitutes acceptance of revised Terms.</p>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mt-4">5. Contact Information</h3>
                    <p class="text-sm leading-relaxed">For inquiries, contact: opticrewhelpcenter@gmail.com</p>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700" x-data="{ alreadyAccepted: document.cookie.includes('finnoys_terms_accepted=1') }">
                <button x-show="!alreadyAccepted" type="button" @click="
                    const el = document.getElementById('applicationModal');
                    if (el && el._x_dataStack) { el._x_dataStack[0].markTermsRead(); }
                    else { document.cookie = 'finnoys_terms_accepted=1; path=/; max-age=' + (30*24*60*60); document.getElementById('recruit-terms-modal').style.display='none'; unlockScroll(); }
                " class="w-full py-2.5 bg-[#0077FF] text-white text-sm font-semibold rounded-full hover:bg-blue-700 transition-colors">
                    I have read the Terms & Conditions
                </button>
                <button x-show="alreadyAccepted" x-cloak type="button" disabled class="w-full py-2.5 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-sm font-semibold rounded-full cursor-not-allowed">
                    Already Agreed
                </button>
            </div>
        </div>
    </div>

    <!-- Recruitment Privacy Policy Modal -->
    <div id="recruit-privacy-modal" class="fixed inset-0 z-[250] flex items-center justify-center bg-black/50 p-4" style="display: none;">
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Privacy Policy</h2>
                <button type="button" onclick="document.getElementById('recruit-privacy-modal').style.display='none'; unlockScroll();" class="w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full transition-colors">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5 overflow-y-auto flex-1">
                <div class="space-y-4 text-gray-700 dark:text-gray-300">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Last updated: January 2024</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">1. Information We Collect</h3>
                    <p class="text-sm leading-relaxed">We collect information you provide directly to us, including your name, email address, phone number, payment information, and service preferences.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">2. How We Use Your Information</h3>
                    <p class="text-sm leading-relaxed">We use the information we collect to provide, maintain, and improve our services, to process your bookings, and to communicate with you.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">3. Information Sharing</h3>
                    <p class="text-sm leading-relaxed">We do not sell or rent your personal information to third parties. We may share your information with service providers who assist us.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">4. Data Security</h3>
                    <p class="text-sm leading-relaxed">We implement appropriate technical and organizational measures to protect your personal information.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">5. Your Rights</h3>
                    <p class="text-sm leading-relaxed">You have the right to access, update, or delete your personal information. You may also opt-out of promotional communications.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">6. Cookies and Tracking</h3>
                    <p class="text-sm leading-relaxed">We use cookies and similar tracking technologies to improve our services. You can control cookies through your browser settings.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">7. Contact Us</h3>
                    <p class="text-sm leading-relaxed">If you have any questions about this Privacy Policy, please contact us at privacy@finnoys.com.</p>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700" x-data="{ alreadyAccepted: document.cookie.includes('finnoys_policy_accepted=1') }">
                <button x-show="!alreadyAccepted" type="button" @click="
                    const el = document.getElementById('applicationModal');
                    if (el && el._x_dataStack) { el._x_dataStack[0].markPrivacyRead(); }
                    else { document.cookie = 'finnoys_policy_accepted=1; path=/; max-age=' + (30*24*60*60); document.getElementById('recruit-privacy-modal').style.display='none'; unlockScroll(); }
                " class="w-full py-2.5 bg-[#0077FF] text-white text-sm font-semibold rounded-full hover:bg-blue-700 transition-colors">
                    I have read the Privacy Policy
                </button>
                <button x-show="alreadyAccepted" x-cloak type="button" disabled class="w-full py-2.5 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-sm font-semibold rounded-full cursor-not-allowed">
                    Already Agreed
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Show flash messages from session (after Google OAuth redirect)
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                showNotification('success', @json(session('success')));
            @endif
            @if (session('error'))
                showNotification('error', @json(session('error')));
            @endif

            // Auto-open modal if returning from terms/policy acceptance
            const urlParams = new URLSearchParams(window.location.search);
            const applyJob = urlParams.get('apply_job');
            if (applyJob && jobs[applyJob]) {
                // Let Alpine init first, then select + open modal
                setTimeout(() => {
                    const comp = document.querySelector('[x-data]').__x.$data;
                    comp.selectJob(parseInt(applyJob));
                    setTimeout(() => comp.openApplicationModal(), 300);
                }, 500);
            }
        });

        let selectedJobId = null;

        // Hero location dropdown component
        function heroLocationDropdown() {
            return {
                openLoc: false,
                selectedLoc: '',
                selectedLabel: 'All Locations',
                dropdownStyle: '',

                toggle() {
                    this.openLoc = !this.openLoc;
                    if (this.openLoc) {
                        this.$nextTick(() => this.positionDropdown());
                    }
                },

                selectLocation(val, label) {
                    this.selectedLoc = val;
                    this.selectedLabel = label;
                    this.openLoc = false;
                },

                positionDropdown() {
                    const trigger = this.$refs.locTrigger;
                    if (!trigger) return;
                    const rect = trigger.getBoundingClientRect();
                    const width = Math.max(rect.width + 40, 220);
                    this.dropdownStyle = `top: ${rect.bottom + 8}px; left: ${rect.left}px; width: ${width}px;`;
                }
            };
        }

        // Hero search bar handler
        window.heroSearchGo = function() {
            const keyword = document.getElementById('heroSearchInput').value.trim();
            const locationSelect = document.getElementById('heroLocationInput');
            const location = locationSelect ? locationSelect.value : '';

            // Scroll to job listings
            document.querySelector('#jobListingsSection').scrollIntoView({ behavior: 'smooth' });

            // Set search query on the Alpine component
            setTimeout(() => {
                const el = document.querySelector('#jobListingsSection');
                if (!el) return;
                // Alpine v3: use Alpine.$data helper or _x_dataStack
                const data = (typeof Alpine !== 'undefined' && Alpine.$data) ? Alpine.$data(el) : (el._x_dataStack ? el._x_dataStack[0] : null);
                if (data) {
                    data.searchQuery = keyword || '';
                    if (location) {
                        data.selectedLocations = [location];
                    } else {
                        data.selectedLocations = [];
                    }
                    data.applyFilters();
                }
            }, 400);
        };

        // Dynamically generated jobs from database
        const jobs = {
            @foreach ($jobPostings ?? [] as $job)
                {{ $job->id }}: {
                    id: {{ $job->id }},
                    title: @json($job->title),
                    description: @json($job->description),
                    location: @json($job->location),
                    salary: @json($job->salary),
                    type: @json($job->type),
                    typeBadge: @json($job->type_badge),
                    icon: @json($job->icon),
                    iconColor: @json($job->icon_color),
                    requiredSkills: @json($job->required_skills ?? []),
                    requiredDocs: @json($job->required_docs ?? []),
                    benefits: @json($job->benefits ?? [])
                },
            @endforeach
        };

        const allJobIds = Object.keys(jobs).map(Number);

        function recruitmentPage() {
            return {
                searchQuery: '',
                showFilters: false,
                selectedTypes: [],
                selectedLocations: [],
                selectedCategories: [],
                salaryMin: 0,
                salaryMax: 1000,
                salaryAbsMin: 0,
                salaryAbsMax: 1000,
                sortBy: 'newest',
                selectedJobId: null,
                selectedJob: null,
                showDetail: false,
                detailTab: 'overview',
                filteredIds: [...allJobIds],
                visibleCount: allJobIds.length,
                totalJobs: allJobIds.length,

                // Derived data for sidebar
                locations: [],
                locationCounts: {},
                categories: [],

                init() {
                    // Build unique locations (full "City, State" strings) with counts
                    const locCounts = {};
                    allJobIds.forEach(id => {
                        const loc = jobs[id].location;
                        locCounts[loc] = (locCounts[loc] || 0) + 1;
                    });
                    this.locationCounts = locCounts;
                    this.locations = Object.keys(locCounts).sort();

                    // Build salary range from all jobs
                    let minSal = Infinity,
                        maxSal = 0;
                    allJobIds.forEach(id => {
                        const parsed = this.parseSalary(jobs[id].salary);
                        if (parsed.min < minSal) minSal = parsed.min;
                        if (parsed.max > maxSal) maxSal = parsed.max;
                    });
                    if (minSal === Infinity) minSal = 0;
                    if (maxSal === 0) maxSal = 1000;
                    this.salaryAbsMin = minSal;
                    this.salaryAbsMax = maxSal;
                    this.salaryMin = minSal;
                    this.salaryMax = maxSal;

                    // Build categories with counts from icon mapping
                    const catMap = {
                        'fa-broom': {
                            value: 'cleaning',
                            label: 'Cleaning'
                        },
                        'fa-dolly': {
                            value: 'logistics',
                            label: 'Logistics'
                        },
                        'fa-user-tie': {
                            value: 'management',
                            label: 'Management'
                        },
                        'fa-headset': {
                            value: 'customer-service',
                            label: 'Customer Service'
                        },
                        'fa-spray-can': {
                            value: 'maintenance',
                            label: 'Maintenance'
                        },
                        'fa-clipboard-check': {
                            value: 'administration',
                            label: 'Administration'
                        },
                        'fa-users': {
                            value: 'human-resources',
                            label: 'Human Resources'
                        },
                        'fa-briefcase': {
                            value: 'general',
                            label: 'General'
                        },
                    };
                    // Initialize all categories with count 0
                    const allCats = [{
                            value: 'cleaning',
                            label: 'Cleaning'
                        },
                        {
                            value: 'logistics',
                            label: 'Logistics'
                        },
                        {
                            value: 'management',
                            label: 'Management'
                        },
                        {
                            value: 'customer-service',
                            label: 'Customer Service'
                        },
                        {
                            value: 'maintenance',
                            label: 'Maintenance'
                        },
                        {
                            value: 'administration',
                            label: 'Administration'
                        },
                        {
                            value: 'human-resources',
                            label: 'Human Resources'
                        },
                        {
                            value: 'general',
                            label: 'General'
                        },
                    ];
                    const catCounts = {};
                    allCats.forEach(c => {
                        catCounts[c.value] = {
                            ...c,
                            count: 0
                        };
                    });
                    allJobIds.forEach(id => {
                        const icon = jobs[id].icon;
                        const cat = catMap[icon] || {
                            value: 'other',
                            label: 'Other'
                        };
                        if (!catCounts[cat.value]) catCounts[cat.value] = {
                            ...cat,
                            count: 0
                        };
                        catCounts[cat.value].count++;
                    });
                    this.categories = Object.values(catCounts);

                    // Auto-select from URL param only
                    const urlParams = new URLSearchParams(window.location.search);
                    const jobId = urlParams.get('job');
                    if (jobId && jobs[jobId]) {
                        this.selectJob(parseInt(jobId));
                    }

                    this.applyFilters();
                },

                rangeFillStyle() {
                    const range = this.salaryAbsMax - this.salaryAbsMin || 1;
                    const left = ((this.salaryMin - this.salaryAbsMin) / range) * 100;
                    const right = ((this.salaryMax - this.salaryAbsMin) / range) * 100;
                    return `left:${left}%;width:${right - left}%`;
                },

                updateRangeSlider() {
                    // no-op placeholder for x-effect reactivity trigger
                    void(this.salaryMin + this.salaryMax);
                },

                // Parse salary string like "30 - 40/hr", "25/hr", "2500/mo" into { min, max }
                parseSalary(salaryStr) {
                    if (!salaryStr) return {
                        min: 0,
                        max: 0
                    };
                    const numbers = salaryStr.match(/[\d]+(?:\.[\d]+)?/g);
                    if (!numbers || numbers.length === 0) return {
                        min: 0,
                        max: 0
                    };
                    const nums = numbers.map(Number);
                    return {
                        min: Math.min(...nums),
                        max: Math.max(...nums)
                    };
                },

                selectJob(jobId) {
                    this.selectedJobId = jobId;
                    this.selectedJob = jobs[jobId] || null;
                    this.detailTab = 'overview';
                    this.showDetail = true;
                    selectedJobId = jobId; // global for terms/policy nav
                },

                applyFilters() {
                    const searchLower = this.searchQuery.toLowerCase();

                    this.filteredIds = allJobIds.filter(id => {
                        const job = jobs[id];
                        const el = document.querySelector(`[data-job-id="${id}"]`);
                        const jobCategory = el ? el.getAttribute('data-category') : 'other';

                        // Search filter
                        if (searchLower && !job.title.toLowerCase().includes(searchLower) && !job.description
                            .toLowerCase().includes(searchLower)) {
                            return false;
                        }

                        // Type filter
                        if (this.selectedTypes.length > 0 && !this.selectedTypes.includes(job.type)) {
                            return false;
                        }

                        // Salary range filter
                        const parsed = this.parseSalary(job.salary);
                        if (parsed.max < this.salaryMin || parsed.min > this.salaryMax) {
                            return false;
                        }

                        // Location filter (full "City, State" match)
                        if (this.selectedLocations.length > 0 && !this.selectedLocations.includes(job.location)) {
                            return false;
                        }

                        // Category filter
                        if (this.selectedCategories.length > 0 && !this.selectedCategories.includes(jobCategory)) {
                            return false;
                        }

                        return true;
                    });

                    this.visibleCount = this.filteredIds.length;
                },

                clearJobTypes() {
                    this.selectedTypes = [];
                    this.applyFilters();
                },

                clearAllFilters() {
                    this.searchQuery = '';
                    this.selectedTypes = [];
                    this.selectedLocations = [];
                    this.selectedCategories = [];
                    this.salaryMin = this.salaryAbsMin;
                    this.salaryMax = this.salaryAbsMax;
                    this.sortBy = 'newest';
                    this.applyFilters();
                },

                openApplicationModal() {
                    if (this.selectedJobId && jobs[this.selectedJobId]) {
                        document.getElementById('applicationJobTitle').value = jobs[this.selectedJobId].title;
                        document.getElementById('applicationJobType').value = jobs[this.selectedJobId].type;
                        document.getElementById('applicationRequiredDocs').value = JSON.stringify(jobs[this.selectedJobId].requiredDocs || []);
                    }
                    document.getElementById('applicationModal').style.display = 'flex';
                    lockScroll();
                }
            };
        }

        // Scroll lock helpers — locks both html and body
        let scrollLockCount = 0;
        function lockScroll() {
            scrollLockCount++;
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
        }
        function unlockScroll() {
            scrollLockCount = Math.max(0, scrollLockCount - 1);
            if (scrollLockCount === 0) {
                document.documentElement.style.overflow = '';
                document.body.style.overflow = '';
            }
        }

        // Modal functions (global)
        function closeApplicationModal() {
            document.getElementById('applicationModal').style.display = 'none';
            unlockScroll();
            document.getElementById('applicationForm').reset();
        }

        // Handle form submission - show loading state
        document.getElementById('applicationForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('googleApplyBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Redirecting to Google...';
        });

        // Notification function
        function showNotification(type, message) {
            const notification = document.createElement('div');
            notification.className = `fixed top-8 right-4 z-[100] max-w-sm p-4 rounded-xl shadow-lg transform transition-all duration-300 ${
                type === 'success'
                    ? 'bg-green-500 text-white'
                    : 'bg-red-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} text-xl"></i>
                    <p class="text-sm font-medium">${message}</p>
                </div>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Close modal when clicking outside
        document.getElementById('applicationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApplicationModal();
            }
        });

        // Move modals to body to escape parent transform stacking context
        ['applicationModal', 'recruit-terms-modal', 'recruit-privacy-modal'].forEach(id => {
            const el = document.getElementById(id);
            if (el) document.body.appendChild(el);
        });
    </script>
@endpush
