@extends('components.layouts.general-landing')

@section('title', 'Job Opportunities')
@push('styles')
    <style>
        body {
            background-image: none;
            background-color: #F6FAFD;
        }

        .dark body {
            background-color: #111827;
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
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
            pointer-events: all;
        }
        .range-slider-wrap input[type="range"]::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #3b82f6;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
            pointer-events: all;
        }
    </style>
@endpush

@section('content')
    <div class="w-full min-h-screen bg-[#F6FAFD] dark:bg-gray-900 p-4 md:p-6 lg:p-8"
         x-data="recruitmentPage()"
         x-init="init()">

        <div class="max-w-[1600px] mx-auto p-3">
            {{-- Page Header --}}
            <div class="text-center my-10">
                <p class="text-sm font-bold text-blue-600 dark:text-blue-400 mb-2">Get the help you need</p>
                <h1 class="text-4xl md:text-4xl font-black text-gray-900 dark:text-white mb-3">
                    Explore Job Opportunities at Fin-noys
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xl mx-auto leading-relaxed">
                    Browse available positions from top employers. Find the right role and apply today.
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="max-w-xl mx-auto mb-10">
                <div class="relative">
                    <input type="text" x-model="searchQuery" @input="applyFilters()" placeholder="Search by Category, Company or ..."
                        class="w-full text-sm px-5 py-3.5 pr-20 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white placeholder-gray-400">
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button @click="applyFilters()"
                            class="w-9 h-9 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-full transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
                {{-- Active filter pills --}}
                <div class="flex flex-wrap items-center justify-center gap-2 mt-4" x-show="selectedTypes.length > 0 || selectedLocations.length > 0 || selectedCategories.length > 0" x-cloak>
                    <template x-for="t in selectedTypes" :key="'t-'+t">
                        <span class="inline-flex items-center gap-1 text-xs font-medium px-3.5 py-1.5 rounded-full bg-blue-600 text-white shadow-sm">
                            <span x-text="t.replace('-',' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                            <button @click="selectedTypes = selectedTypes.filter(x => x !== t); applyFilters()" class="ml-0.5 hover:text-blue-200">&times;</button>
                        </span>
                    </template>
                    <template x-for="loc in selectedLocations" :key="'l-'+loc">
                        <span class="inline-flex items-center gap-1 text-xs font-medium px-3.5 py-1.5 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 shadow-sm">
                            <span x-text="loc"></span>
                            <button @click="selectedLocations = selectedLocations.filter(x => x !== loc); applyFilters()" class="ml-0.5 hover:text-gray-400">&times;</button>
                        </span>
                    </template>
                    <template x-for="cat in selectedCategories" :key="'c-'+cat">
                        <span class="inline-flex items-center gap-1 text-xs font-medium px-3.5 py-1.5 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 shadow-sm">
                            <span x-text="cat.replace('-',' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                            <button @click="selectedCategories = selectedCategories.filter(x => x !== cat); applyFilters()" class="ml-0.5 hover:text-gray-400">&times;</button>
                        </span>
                    </template>
                    <button @click="clearAllFilters()" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium px-1">Clear filters</button>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">

                {{-- Left Sidebar - Filters --}}
                <aside class="w-full lg:w-72 xl:w-80 flex-shrink-0">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 sticky top-6 space-y-6 max-h-[calc(100vh-3rem)] overflow-y-auto scrollbar-custom">

                        {{-- Job Type Filter --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Job Type</h3>
                                <button @click="clearJobTypes()" class="text-xs text-red-500 hover:text-red-600 font-medium">Clear all</button>
                            </div>
                            <div class="space-y-2.5">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" value="full-time" x-model="selectedTypes" @change="applyFilters()"
                                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Full time</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" value="part-time" x-model="selectedTypes" @change="applyFilters()"
                                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Part time</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" value="remote" x-model="selectedTypes" @change="applyFilters()"
                                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Remote</span>
                                </label>
                            </div>
                        </div>

                        {{-- Salary Range Filter --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Salary Range</h3>
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">$<span x-text="salaryMin"></span> &ndash; $<span x-text="salaryMax"></span></span>
                            </div>
                            <div class="px-1">
                                <div class="range-slider-wrap"
                                     x-init="$nextTick(() => updateRangeSlider())"
                                     x-effect="updateRangeSlider()">
                                    <div class="range-slider-track"></div>
                                    <div class="range-slider-fill" :style="rangeFillStyle()"></div>
                                    <input type="range" x-model.number="salaryMin"
                                        :min="salaryAbsMin" :max="salaryAbsMax" step="1"
                                        @input="if(salaryMin > salaryMax) salaryMin = salaryMax; applyFilters()">
                                    <input type="range" x-model.number="salaryMax"
                                        :min="salaryAbsMin" :max="salaryAbsMax" step="1"
                                        @input="if(salaryMax < salaryMin) salaryMax = salaryMin; applyFilters()">
                                </div>
                                <div class="flex items-center justify-between mt-4">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">$<span x-text="salaryAbsMin"></span></span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">$<span x-text="salaryAbsMax"></span></span>
                                </div>
                            </div>
                        </div>

                        {{-- Location Filter --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Location</h3>
                                <button @click="selectedLocations = []; applyFilters()" x-show="selectedLocations.length > 0" class="text-xs text-red-500 hover:text-red-600 font-medium">Clear</button>
                            </div>
                            <div class="space-y-2.5 max-h-48 overflow-y-auto scrollbar-custom">
                                <template x-for="loc in locations" :key="loc">
                                    <label class="flex items-center justify-between cursor-pointer group">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" :value="loc" x-model="selectedLocations" @change="applyFilters()"
                                                class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white" x-text="loc"></span>
                                        </div>
                                        <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full" x-text="locationCounts[loc] || 0"></span>
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
                                            <input type="checkbox" :value="cat.value" x-model="selectedCategories" @change="applyFilters()"
                                                class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white" x-text="cat.label"></span>
                                        </div>
                                        <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full" x-text="cat.count"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                    </div>
                </aside>

                {{-- Right Side - Job Cards Grid --}}
                <div class="flex-1 min-w-0">
                    {{-- Results Header --}}
                    <div class="flex items-center justify-between mb-4 px-1">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Showing <span class="font-semibold text-gray-900 dark:text-white" x-text="visibleCount"></span> jobs
                        </p>
                        <select x-model="sortBy" @change="applyFilters()"
                            class="text-sm px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 dark:text-gray-300">
                            <option value="newest">Newest first</option>
                            <option value="oldest">Oldest first</option>
                            <option value="salary-high">Salary: High to Low</option>
                            <option value="salary-low">Salary: Low to High</option>
                        </select>
                    </div>

                    {{-- Job Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @forelse($jobPostings ?? [] as $job)
                        @php
                            $iconBgClass = match($job->icon_color) {
                                'green' => 'bg-green-50 dark:bg-green-900/30',
                                'purple' => 'bg-purple-50 dark:bg-purple-900/30',
                                'orange' => 'bg-orange-50 dark:bg-orange-900/30',
                                'red' => 'bg-red-50 dark:bg-red-900/30',
                                default => 'bg-blue-50 dark:bg-blue-900/30',
                            };
                            $iconTextClass = match($job->icon_color) {
                                'green' => 'text-green-600 dark:text-green-400',
                                'purple' => 'text-purple-600 dark:text-purple-400',
                                'orange' => 'text-orange-600 dark:text-orange-400',
                                'red' => 'text-red-600 dark:text-red-400',
                                default => 'text-blue-600 dark:text-blue-400',
                            };
                            $typeBadgeClass = match($job->type) {
                                'part-time' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                                'remote' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
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
                        <div class="job-item" data-job-id="{{ $job->id }}" data-type="{{ $job->type }}" data-category="{{ $jobCategory }}" data-location="{{ $job->location }}"
                            @click="selectJob({{ $job->id }})"
                            :style="filteredIds.includes({{ $job->id }}) ? '' : 'display:none'">
                            <div class="job-card bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-200 cursor-pointer flex flex-col h-full"
                                 :class="selectedJobId === {{ $job->id }} ? 'ring-2 ring-blue-500 dark:ring-blue-400 shadow-lg' : 'shadow-sm hover:border-gray-200 dark:hover:border-gray-600'">

                                {{-- Top row: Icon + Title + Heart --}}
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-10 h-10 {{ $iconBgClass }} rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i class="fas {{ $job->icon }} {{ $iconTextClass }} text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $job->title }}</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-0.5">
                                            <i class="fas fa-map-marker-alt text-[10px]"></i>
                                            {{ $job->location }}
                                        </p>
                                    </div>
                                    <button @click.stop class="text-gray-300 hover:text-red-400 dark:text-gray-600 dark:hover:text-red-400 transition-colors flex-shrink-0">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>

                                {{-- Type Badges --}}
                                <div class="flex flex-wrap gap-1.5 mb-3">
                                    <span class="px-2 py-0.5 {{ $typeBadgeClass }} text-[11px] font-semibold rounded">
                                        {{ $job->type_badge }}
                                    </span>
                                </div>

                                {{-- Description --}}
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 line-clamp-2 flex-1 leading-relaxed">
                                    {{ $job->description }}
                                </p>

                                {{-- Footer: Salary + Posted --}}
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700 mt-auto">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">&euro;{{ $job->salary }}</span>
                                    <span class="text-[11px] text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                        <i class="far fa-clock"></i>
                                        {{ $job->created_at ? $job->created_at->diffForHumans() : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-16">
                            <i class="fas fa-briefcase text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400 text-base font-medium">No job openings available at the moment.</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Please check back later for new opportunities.</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- No results from filter --}}
                    <div x-show="visibleCount === 0 && totalJobs > 0" x-cloak class="text-center py-16">
                        <i class="fas fa-search text-gray-300 dark:text-gray-600 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">No jobs match your filters.</p>
                        <button @click="clearAllFilters()" class="mt-3 text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">Clear all filters</button>
                    </div>
                </div>

                {{-- Job Detail Panel --}}
                <div x-show="showDetail" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="w-full lg:w-80 xl:w-[340px] flex-shrink-0">
                    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl sticky top-6 max-h-[calc(100vh-3rem)] flex flex-col overflow-hidden relative my-3">
                        <template x-if="selectedJob">
                            <div class="flex flex-col h-full min-h-0">
                                {{-- Scrollable content --}}
                                <div class="flex-1 overflow-y-auto scrollbar-custom min-h-0">
                                    {{-- Top section: Icon + Title + Location --}}
                                    <div class="px-6 pt-8 pb-5 flex flex-col items-center text-center border-b border-gray-100 dark:border-gray-700">
                                        {{-- Close button --}}
                                        <button @click="showDetail = false; selectedJobId = null; selectedJob = null;"
                                            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors z-10">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>

                                        {{-- Company Icon --}}
                                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4 shadow-sm dark:shadow-none"
                                             :class="{
                                                'bg-green-50 dark:bg-green-900/30': selectedJob.iconColor === 'green',
                                                'bg-purple-50 dark:bg-purple-900/30': selectedJob.iconColor === 'purple',
                                                'bg-orange-50 dark:bg-orange-900/30': selectedJob.iconColor === 'orange',
                                                'bg-red-50 dark:bg-red-900/30': selectedJob.iconColor === 'red',
                                                'bg-blue-50 dark:bg-blue-900/30': !['green','purple','orange','red'].includes(selectedJob.iconColor)
                                             }">
                                            <i class="fas text-2xl"
                                               :class="[selectedJob.icon, {
                                                    'text-green-600 dark:text-green-400': selectedJob.iconColor === 'green',
                                                    'text-purple-600 dark:text-purple-400': selectedJob.iconColor === 'purple',
                                                    'text-orange-600 dark:text-orange-400': selectedJob.iconColor === 'orange',
                                                    'text-red-600 dark:text-red-400': selectedJob.iconColor === 'red',
                                                    'text-blue-600 dark:text-blue-400': !['green','purple','orange','red'].includes(selectedJob.iconColor)
                                               }]"></i>
                                        </div>

                                        {{-- Title --}}
                                        <h2 class="text-lg font-bold text-gray-900 dark:text-white leading-snug mb-1" x-text="selectedJob.title"></h2>

                                        {{-- Company + Location --}}
                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedJob.location"></p>
                                    </div>

                                    {{-- Required Skills (Minimum qualifications) --}}
                                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700"
                                         x-show="selectedJob.requiredSkills && selectedJob.requiredSkills.length > 0">
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Minimum qualifications:</h3>
                                        <ul class="space-y-2">
                                            <template x-for="skill in selectedJob.requiredSkills" :key="skill">
                                                <li class="flex items-start gap-2.5 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                                    <span class="text-gray-300 dark:text-gray-600 mt-0.5 flex-shrink-0">&#8226;</span>
                                                    <span x-text="skill"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>

                                    {{-- Required Documents --}}
                                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700"
                                         x-show="selectedJob.requiredDocs && selectedJob.requiredDocs.length > 0">
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Required documents</h3>
                                        <ul class="space-y-2">
                                            <template x-for="doc in selectedJob.requiredDocs" :key="typeof doc === 'object' ? doc.name : doc">
                                                <li class="flex items-start gap-2.5 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                                    <span class="text-gray-300 dark:text-gray-600 mt-0.5 flex-shrink-0">&#8226;</span>
                                                    <span>
                                                        <span x-text="typeof doc === 'object' ? doc.name : doc"></span>
                                                        <template x-if="typeof doc === 'object' && doc.type">
                                                            <span class="ml-1 px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-900/40 text-[10px] uppercase font-bold text-purple-500 dark:text-purple-400" x-text="doc.type"></span>
                                                        </template>
                                                    </span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>

                                    {{-- About the Job --}}
                                    <div class="px-6 py-5"
                                         x-data="{ expanded: false }">
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">About the Job</h3>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                            <p :class="expanded ? '' : 'line-clamp-4'" x-text="selectedJob.description"></p>
                                        </div>
                                        <button @click="expanded = !expanded"
                                            class="mt-2 text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1">
                                            <span x-text="expanded ? 'Show less' : 'Read More'"></span>
                                            <svg class="w-3 h-3 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Benefits --}}
                                    <div class="px-6 py-5 border-t border-gray-100 dark:border-gray-700"
                                         x-show="selectedJob.benefits && selectedJob.benefits.length > 0">
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Benefits:</h3>
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
                                    <button @click.stop class="w-11 h-11 bg-gray-100 dark:bg-gray-700 text-gray-300 hover:text-red-400 dark:text-gray-600 dark:hover:text-red-400 rounded-full transition-colors flex items-center justify-center flex-shrink-0">
                                        <i class="far fa-heart text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Application Modal --}}
    <div id="applicationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;"
        x-data="{
            termsAccepted: document.cookie.includes('finnoys_terms_accepted=1'),
            policyAccepted: document.cookie.includes('finnoys_policy_accepted=1')
        }">
        <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-md w-full max-h-[90vh] overflow-y-auto scrollbar-custom">
            {{-- Modal Header --}}
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center z-10">
                <button onclick="closeApplicationModal()" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <i class="fas fa-arrow-left text-xl"></i>
                </button>
                <h2 class="text-lg font-bold text-center mr-3 w-full text-gray-900 dark:text-white">Apply for this Position</h2>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                <form id="applicationForm" action="{{ route('recruitment.google.apply') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="job_title" id="applicationJobTitle">
                    <input type="hidden" name="job_type" id="applicationJobType">

                    {{-- Google Sign-In Info --}}
                    <div class="text-center mb-2">
                        <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fab fa-google text-2xl text-blue-600"></i>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            Sign in with your Google account to apply. Your email will be used for application updates.
                        </p>
                    </div>

                    {{-- Terms and Conditions Acceptance --}}
                    <div class="space-y-2">
                        <div id="termsCheckRow" class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-colors"
                            :class="termsAccepted ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600'"
                            onclick="navigateToTerms()">
                            <div class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center"
                                :class="termsAccepted ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-500'">
                                <i class="fas fa-check text-white text-xs" x-show="termsAccepted"></i>
                            </div>
                            <span class="text-sm flex-1" :class="termsAccepted ? 'text-green-700 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'">
                                <span x-text="termsAccepted ? 'Terms and Conditions accepted' : 'Read and accept Terms and Conditions'"></span>
                            </span>
                            <i class="fas fa-arrow-right text-xs text-gray-400" x-show="!termsAccepted"></i>
                        </div>

                        <div id="policyCheckRow" class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-colors"
                            :class="policyAccepted ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600'"
                            onclick="navigateToPolicy()">
                            <div class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center"
                                :class="policyAccepted ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-500'">
                                <i class="fas fa-check text-white text-xs" x-show="policyAccepted"></i>
                            </div>
                            <span class="text-sm flex-1" :class="policyAccepted ? 'text-green-700 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'">
                                <span x-text="policyAccepted ? 'Privacy Policy accepted' : 'Read and accept Privacy Policy'"></span>
                            </span>
                            <i class="fas fa-arrow-right text-xs text-gray-400" x-show="!policyAccepted"></i>
                        </div>
                    </div>

                    {{-- Sign in with Google to Apply --}}
                    <button type="submit" id="googleApplyBtn"
                        :disabled="!termsAccepted || !policyAccepted"
                        :class="(!termsAccepted || !policyAccepted) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-600 shadow-lg hover:shadow-xl'"
                        class="w-full flex items-center justify-center gap-3 py-3.5 px-4 border border-gray-300 dark:border-gray-600 rounded-full bg-white dark:bg-gray-700 transition-colors">
                        <svg width="20" height="20" viewBox="0 0 48 48">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sign in with Google to Apply</span>
                    </button>

                    <p x-show="!termsAccepted || !policyAccepted" class="text-xs text-center text-amber-600 dark:text-amber-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        Please accept both Terms and Privacy Policy to continue
                    </p>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Show flash messages from session (after Google OAuth redirect)
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showNotification('success', @json(session('success')));
            @endif
            @if(session('error'))
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

        // Navigate to Terms page with return context
        let selectedJobId = null;
        function navigateToTerms() {
            if (document.cookie.includes('finnoys_terms_accepted=1')) return;
            const jobId = selectedJobId || '';
            window.location.href = '{{ route("termscondition") }}?from=recruitment&job=' + jobId;
        }

        // Navigate to Policy page with return context
        function navigateToPolicy() {
            if (document.cookie.includes('finnoys_policy_accepted=1')) return;
            const jobId = selectedJobId || '';
            window.location.href = '{{ route("privacypolicy") }}?from=recruitment&job=' + jobId;
        }

        // Dynamically generated jobs from database
        const jobs = {
            @foreach($jobPostings ?? [] as $job)
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
                    let minSal = Infinity, maxSal = 0;
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
                        'fa-broom': { value: 'cleaning', label: 'Cleaning' },
                        'fa-dolly': { value: 'logistics', label: 'Logistics' },
                        'fa-user-tie': { value: 'management', label: 'Management' },
                        'fa-headset': { value: 'customer-service', label: 'Customer Service' },
                        'fa-spray-can': { value: 'maintenance', label: 'Maintenance' },
                        'fa-clipboard-check': { value: 'administration', label: 'Administration' },
                        'fa-users': { value: 'human-resources', label: 'Human Resources' },
                        'fa-briefcase': { value: 'general', label: 'General' },
                    };
                    // Initialize all categories with count 0
                    const allCats = [
                        { value: 'cleaning', label: 'Cleaning' },
                        { value: 'logistics', label: 'Logistics' },
                        { value: 'management', label: 'Management' },
                        { value: 'customer-service', label: 'Customer Service' },
                        { value: 'maintenance', label: 'Maintenance' },
                        { value: 'administration', label: 'Administration' },
                        { value: 'human-resources', label: 'Human Resources' },
                        { value: 'general', label: 'General' },
                    ];
                    const catCounts = {};
                    allCats.forEach(c => { catCounts[c.value] = { ...c, count: 0 }; });
                    allJobIds.forEach(id => {
                        const icon = jobs[id].icon;
                        const cat = catMap[icon] || { value: 'other', label: 'Other' };
                        if (!catCounts[cat.value]) catCounts[cat.value] = { ...cat, count: 0 };
                        catCounts[cat.value].count++;
                    });
                    this.categories = Object.values(catCounts);

                    // Auto-select from URL param, or default to newest job
                    const urlParams = new URLSearchParams(window.location.search);
                    const jobId = urlParams.get('job');
                    if (jobId && jobs[jobId]) {
                        this.selectJob(parseInt(jobId));
                    } else if (allJobIds.length > 0) {
                        // Select the newest job (highest ID)
                        const newestId = Math.max(...allJobIds);
                        this.selectJob(newestId);
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
                    if (!salaryStr) return { min: 0, max: 0 };
                    const numbers = salaryStr.match(/[\d]+(?:\.[\d]+)?/g);
                    if (!numbers || numbers.length === 0) return { min: 0, max: 0 };
                    const nums = numbers.map(Number);
                    return { min: Math.min(...nums), max: Math.max(...nums) };
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
                        if (searchLower && !job.title.toLowerCase().includes(searchLower) && !job.description.toLowerCase().includes(searchLower)) {
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
                    }
                    document.getElementById('applicationModal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            };
        }

        // Modal functions (global)
        function closeApplicationModal() {
            document.getElementById('applicationModal').style.display = 'none';
            document.body.style.overflow = 'auto';
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
    </script>
@endpush
