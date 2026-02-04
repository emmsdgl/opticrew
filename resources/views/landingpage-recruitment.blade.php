@extends('components.layouts.general-landing')

@section('title', 'Job Opportunities')
@push('styles')
    <style>
        body {
            background-image: none;
            background-color: #f9fafb;
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
    </style>
@endpush

@section('content')
    <div class="w-full min-h-screen bg-gray-50 dark:bg-gray-900 p-4 md:p-8 xl:col-span-2">
        <div class="max-w-[1600px] mx-auto grid grid-cols-1 xl:grid-cols-3 gap-3 sm:px-6 lg:px-24">

            {{-- Left Side - Job Listings --}}
            <div class="xl:col-span-2 sm:px-3 lg:px-12">
                {{-- Header --}}
                <div class="mb-8">
                    <h1 class="text-3xl md:text-4xl font-medium text-gray-900 dark:text-white mb-2">
                        Find the best<span class="text-blue-600 mx-2 font-bold dark:text-blue-400">Job Opportunities</span>for your career
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 my-6">What are you looking for?</p>

                    {{-- Search Bar --}}
                    <div class="relative mb-6">
                        <input type="text" id="searchInput" placeholder="Search jobs..."
                            class="w-full text-sm px-4 py-3 pr-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 shadow-sm">
                        <svg class="absolute right-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    {{-- Tab Navigation --}}
                    <div class="flex gap-3 mb-6 flex-wrap">
                        <button onclick="filterJobs('all')" data-filter="all"
                            class="filter-tab px-6 py-2.5 rounded-full text-sm transition-all duration-200 bg-blue-600 text-white shadow-lg">
                            All
                        </button>
                        <button onclick="filterJobs('full-time')" data-filter="full-time"
                            class="filter-tab px-6 py-2.5 rounded-full text-sm transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                            Full Time
                        </button>
                        <button onclick="filterJobs('part-time')" data-filter="part-time"
                            class="filter-tab px-6 py-2.5 rounded-full text-sm transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                            Part Time
                        </button>
                        <button onclick="filterJobs('remote')" data-filter="remote"
                            class="filter-tab px-6 py-2.5 rounded-full text-sm transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                            Remote
                        </button>
                    </div>
                </div>

                {{-- Job Cards Grid --}}
                <div id="jobList" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    {{-- Job Card 1 --}}
                    <div class="job-item cursor-pointer" data-job-id="1" data-type="full-time"
                        onclick="selectJob(1)">
                        <div
                            class="job-card bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-user-tie text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <button class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                    <i class="far fa-heart text-xl"></i>
                                </button>
                            </div>
                            <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-semibold rounded mb-2">
                                Full-time Employee
                            </span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Deep Cleaning Specialist</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                Handling intensive cleaning tasks using specialized equipment and chemicals to meet high sanitation standards.
                            </p>
                            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Imst, Finland
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">$30 - $40/hr</span>
                            </div>
                        </div>
                    </div>

                    {{-- Job Card 2 --}}
                    <div class="job-item cursor-pointer" data-job-id="2" data-type="part-time"
                        onclick="selectJob(2)">
                        <div
                            class="job-card bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-broom text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <button class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                    <i class="far fa-heart text-xl"></i>
                                </button>
                            </div>
                            <span class="inline-block px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-semibold rounded mb-2">
                                Part-time Employee
                            </span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Cleaner</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                An ideal position that offers flexible hours ideal for working parents or individuals seeking work-life balance.
                            </p>
                            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Imst, Finland
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">$30 - $40/hr</span>
                            </div>
                        </div>
                    </div>

                    {{-- Job Card 3 --}}
                    <div class="job-item cursor-pointer" data-job-id="3" data-type="full-time"
                        onclick="selectJob(3)">
                        <div
                            class="job-card bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-dolly text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <button class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                    <i class="far fa-heart text-xl"></i>
                                </button>
                            </div>
                            <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-semibold rounded mb-2">
                                Full-time Employee
                            </span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Move-Out Staff</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                Clean vacated properties, ensuring they meet move-out standards for landlords and tenants.
                            </p>
                            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Imst, Finland
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">$30 - $40/hr</span>
                            </div>
                        </div>
                    </div>

                    {{-- Job Card 4 --}}
                    <div class="job-item cursor-pointer" data-job-id="4" data-type="full-time"
                        onclick="selectJob(4)">
                        <div
                            class="job-card bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-clipboard-check text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <button class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                    <i class="far fa-heart text-xl"></i>
                                </button>
                            </div>
                            <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-semibold rounded mb-2">
                                Full-time Employee
                            </span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Quality Control Inspector</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                Ensures all cleaning tasks meet company standards through post-cleaning inspections.
                            </p>
                            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Imst, Finland
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">$30 - $40/hr</span>
                            </div>
                        </div>
                    </div>

                    {{-- Job Card 5 --}}
                    <div class="job-item cursor-pointer" data-job-id="5" data-type="remote"
                        onclick="selectJob(5)">
                        <div
                            class="job-card bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-headset text-purple-600 dark:text-purple-400 text-xl"></i>
                                </div>
                                <button class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                    <i class="far fa-heart text-xl"></i>
                                </button>
                            </div>
                            <span class="inline-block px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-semibold rounded mb-2">
                                Remote
                            </span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Customer Service Representative</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                Handle client inquiries, schedule appointments, and manage customer relationships remotely.
                            </p>
                            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Remote
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">$25 - $35/hr</span>
                            </div>
                        </div>
                    </div>

                    {{-- Job Card 6 --}}
                    <div class="job-item cursor-pointer" data-job-id="6" data-type="full-time"
                        onclick="selectJob(6)">
                        <div
                            class="job-card bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-green-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-spray-can text-green-600 dark:text-green-400 text-xl"></i>
                                </div>
                                <button class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                    <i class="far fa-heart text-xl"></i>
                                </button>
                            </div>
                            <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-semibold rounded mb-2">
                                Full-time Employee
                            </span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Sanitization Specialist</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                Focus on disinfection and sanitization in healthcare facilities and high-risk environments.
                            </p>
                            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Imst, Finland
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">$35 - $45/hr</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Applied Vacancies Section --}}
                <div class="mt-8">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4">Applied Vacancies</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="job-item cursor-pointer">
                            <div
                                class="job-card bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
                                <div class="flex items-start justify-between mb-4">
                                    <div
                                        class="w-12 h-12 bg-orange-50 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-users text-orange-600 dark:text-orange-400 text-xl"></i>
                                    </div>
                                    <button class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                        <i class="far fa-heart text-xl"></i>
                                    </button>
                                </div>
                                <span class="inline-block px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 text-xs font-semibold rounded mb-2">
                                    Pending
                                </span>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">HR Recruitment Officer</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                    Conduct strong onboarding processes that engage new hires from day one.
                                </p>
                                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Manila, NCR
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">$30 - $40/hr</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side - Job Details --}}
            <div class="xl:col-span-1">
                <div id="jobDetailPanel"
                    class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 rounded-3xl p-8 sticky top-6 shadow-xl border border-blue-100 dark:border-gray-700"
                    style="display: none;">

                    {{-- Job Type Badge --}}
                    <div class="mb-4">
                        <span id="jobTypeBadge"
                            class="inline-block px-3 py-1 bg-blue-900/30 text-blue-600 text-xs rounded-lg">
                            Full-time Employee
                        </span>
                    </div>

                    {{-- Job Title --}}
                    <h2 id="jobTitle" class="text-2xl font-semibold text-gray-900 dark:text-white mb-3">
                        Deep Cleaning Specialist
                    </h2>

                    {{-- Location & Salary --}}
                    <div class="mb-6">
                        <p id="jobLocation" class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2 mb-2">
                            <i class="fas fa-map-marker-alt"></i>
                            Imst, Finland
                        </p>
                        <p id="jobSalary" class="text-xl font-bold text-gray-900 dark:text-white">
                            $30 - $40/hr
                        </p>
                    </div>

                    {{-- Description --}}
                    <p id="jobDescription" class="text-sm text-justify text-gray-700 dark:text-gray-300 leading-relaxed mb-6">
                        Handling intensive cleaning tasks using specialized equipment and chemicals to meet high sanitation standards.
                    </p>

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 mb-8">
                        <button onclick="openApplicationModal()"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-full transition-all shadow-lg text-sm hover:shadow-xl">
                            Apply Now
                        </button>
                        <button
                            class="w-12 h-12 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-lg flex items-center justify-center">
                            <i class="far fa-heart text-xl"></i>
                        </button>
                    </div>

                    {{-- Tabs --}}
                    <div class="flex gap-4 mb-6 border-b border-gray-300 dark:border-gray-600">
                        <button onclick="switchDetailTab('overview')" id="tab-overview"
                            class="detail-tab pb-3 text-sm font-semibold transition-colors text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400">
                            Overview
                        </button>
                        <button onclick="switchDetailTab('benefits')" id="tab-benefits"
                            class="detail-tab pb-3 text-sm font-semibold transition-colors text-gray-600 dark:text-gray-400">
                            Benefits
                        </button>
                    </div>

                    {{-- Tab Content --}}
                    <div id="tab-content-overview" class="tab-content">
                        {{-- Required Skills --}}
                        <div class="mb-8">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">
                                Required Skills and Experience
                            </h3>
                            <ul id="requiredSkills" class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>Advanced cleaning methods</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>Equipment and chemical handling</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>Safety awareness</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>Prior deep cleaning experience</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>Knowledge of cleaning chemicals</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>PPR compliance</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Required Documentations --}}
                        <div class="mb-6">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">
                                Required Documentations
                            </h3>
                            <ul id="requiredDocs" class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>Resume / Bio-data</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>Valid Government ID (e.g., Passport, National ID)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>Passport-size (recent) photo</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>Educational certificates and photo</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>NBI / Police Clearance</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>2×2 ID Photo (or ROA-acknowledged employment</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                                    <span>Company Policy & NDA Acknowledgement</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Note --}}
                        <div class="bg-white/50 dark:bg-gray-800/50 rounded-xl p-4 backdrop-blur-sm">
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                <i class="fas fa-info-circle mr-1"></i>
                                Kindly prepare a compiled pdf document with the same sequence guide from the company policy and NDA acknowledgement which will be in the form.
                            </p>
                        </div>
                    </div>

                    <div id="tab-content-benefits" class="tab-content" style="display: none;">
                        <div class="space-y-3">
                            <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-hand-holding-usd text-blue-600 dark:text-blue-400 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-sm text-gray-900 dark:text-white mb-1">Competitive Salary</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Hourly rate with potential for overtime pay</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-medkit text-blue-600 dark:text-blue-400 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-sm text-gray-900 dark:text-white mb-1">Health Insurance</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Comprehensive health coverage for employees</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-graduation-cap text-blue-600 dark:text-blue-400 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-sm text-gray-900 dark:text-white mb-1">Training & Development</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Ongoing professional development opportunities</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-calendar-alt text-blue-600 dark:text-blue-400 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-sm text-gray-900 dark:text-white mb-1">Paid Time Off</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Generous vacation and sick leave policy</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Empty State --}}
                <div id="emptyState"
                    class="bg-white dark:bg-gray-800 rounded-3xl p-12 text-center border border-gray-200 dark:border-gray-700">
                    <div class="text-gray-400 dark:text-gray-500 mb-4">
                        <i class="fas fa-briefcase text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        Select a Job
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Click on any job card to view details
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- Application Modal --}}
    <div id="applicationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-md w-full max-h-[90vh] overflow-y-auto scrollbar-custom">
            {{-- Modal Header --}}
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center z-10">
                <button onclick="closeApplicationModal()" class=" text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <i class="fas fa-arrow-left text-xl"></i>
                </button>
                <h2 class="text-lg font-bold text-center mr-3 w-full text-gray-900 dark:text-white">Get Your Interview Schedule</h2>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                <form id="applicationForm" class="space-y-4">
                    {{-- Email Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address
                        </label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm"
                            placeholder="johndoe@example.com">
                    </div>

                    {{-- Alternative Email Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Alternative Email Address <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <input type="email" name="alternative_email"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm"
                            placeholder="johndoe@example.com">
                    </div>

                    {{-- Compiled PDF Document --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Compiled PDF Document
                        </label>
                        <div class="relative">
                            <input type="file" name="pdf_document" id="pdfDocument" accept=".pdf" required
                                class="hidden"
                                onchange="updateFileName(this)">
                            <label for="pdfDocument"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl flex items-center justify-between cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <span id="fileName" class="text-gray-500 dark:text-gray-400 text-sm">Choose file</span>
                                <i class="fas fa-upload text-gray-400"></i>
                            </label>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Kindly drag or click to upload your documents
                        </p>
                    </div>

                    {{-- Terms and Conditions --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <p class="text-xs text-gray-600 dark:text-gray-400 text-center leading-relaxed">
                            By signing to the form above, you acknowledge that you have read, understood, and agree to be bound by Glowjobs <a href="#" class="text-blue-600 dark:text-blue-400 underline">Terms and Conditions</a> and <a href="#" class="text-blue-600 dark:text-blue-400 underline">Privacy Policy</a>.
                        </p>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-full transition-all shadow-lg hover:shadow-xl text-sm">
                        Submit Application
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const jobs = {
            1: {
                title: "Deep Cleaning Specialist",
                description: "Handling intensive cleaning tasks using specialized equipment and chemicals to meet high sanitation standards.",
                location: "Imst, Finland",
                salary: "$30 - $40/hr",
                type: "full-time",
                typeBadge: "Full-time Employee",
                icon: "fa-user-tie",
                iconColor: "blue",
                requiredSkills: [
                    "Advanced cleaning methods",
                    "Equipment and chemical handling",
                    "Safety awareness",
                    "Prior deep cleaning experience",
                    "Knowledge of cleaning chemicals",
                    "PPR compliance"
                ],
                requiredDocs: [
                    "Resume / Bio-data",
                    "Valid Government ID (e.g., Passport, National ID)",
                    "Passport-size (recent) photo",
                    "Educational certificates and photo",
                    "NBI / Police Clearance",
                    "2×2 ID Photo (or ROA-acknowledged employment",
                    "Company Policy & NDA Acknowledgement"
                ]
            },
            2: {
                title: "Cleaner",
                description: "An ideal position that offers flexible hours ideal for working parents or individuals seeking work-life balance.",
                location: "Imst, Finland",
                salary: "$30 - $40/hr",
                type: "part-time",
                typeBadge: "Part-time Employee",
                icon: "fa-broom",
                iconColor: "blue",
                requiredSkills: [
                    "Basic cleaning knowledge",
                    "Attention to detail",
                    "Time management",
                    "Reliability and punctuality",
                    "Physical stamina",
                    "Customer service skills"
                ],
                requiredDocs: [
                    "Resume / Bio-data",
                    "Valid Government ID",
                    "Passport-size photo",
                    "NBI / Police Clearance",
                    "Company Policy & NDA Acknowledgement"
                ]
            },
            3: {
                title: "Move-Out Staff",
                description: "Clean vacated properties, ensuring they meet move-out standards for landlords and tenants.",
                location: "Imst, Finland",
                salary: "$30 - $40/hr",
                type: "full-time",
                typeBadge: "Full-time Employee",
                icon: "fa-dolly",
                iconColor: "blue",
                requiredSkills: [
                    "Deep cleaning expertise",
                    "Property inspection knowledge",
                    "Time-efficient cleaning methods",
                    "Understanding of move-out standards",
                    "Flexibility in scheduling",
                    "Professional communication"
                ],
                requiredDocs: [
                    "Resume / Bio-data",
                    "Valid Government ID",
                    "Passport-size photo",
                    "Educational certificates",
                    "NBI / Police Clearance",
                    "Company Policy & NDA Acknowledgement"
                ]
            },
            4: {
                title: "Quality Control Inspector",
                description: "Ensures all cleaning tasks meet company standards through post-cleaning inspections.",
                location: "Imst, Finland",
                salary: "$30 - $40/hr",
                type: "full-time",
                typeBadge: "Full-time Employee",
                icon: "fa-clipboard-check",
                iconColor: "blue",
                requiredSkills: [
                    "Quality assurance experience",
                    "Attention to detail",
                    "Knowledge of cleaning standards",
                    "Inspection and evaluation skills",
                    "Report writing",
                    "Communication skills"
                ],
                requiredDocs: [
                    "Resume / Bio-data",
                    "Valid Government ID",
                    "Passport-size photo",
                    "Educational certificates",
                    "NBI / Police Clearance",
                    "Relevant certifications",
                    "Company Policy & NDA Acknowledgement"
                ]
            },
            5: {
                title: "Customer Service Representative",
                description: "Handle client inquiries, schedule appointments, and manage customer relationships remotely.",
                location: "Remote",
                salary: "$25 - $35/hr",
                type: "remote",
                typeBadge: "Remote",
                icon: "fa-headset",
                iconColor: "purple",
                requiredSkills: [
                    "Excellent communication skills",
                    "Customer service experience",
                    "Scheduling and organization",
                    "CRM software proficiency",
                    "Problem-solving abilities",
                    "Professional phone etiquette"
                ],
                requiredDocs: [
                    "Resume / Bio-data",
                    "Valid Government ID",
                    "Passport-size photo",
                    "Educational certificates",
                    "NBI / Police Clearance",
                    "Internet setup proof",
                    "Company Policy & NDA Acknowledgement"
                ]
            },
            6: {
                title: "Sanitization Specialist",
                description: "Focus on disinfection and sanitization in healthcare facilities and high-risk environments.",
                location: "Imst, Finland",
                salary: "$35 - $45/hr",
                type: "full-time",
                typeBadge: "Full-time Employee",
                icon: "fa-spray-can",
                iconColor: "green",
                requiredSkills: [
                    "Healthcare sanitization protocols",
                    "Chemical safety knowledge",
                    "PPE usage expertise",
                    "Infection control procedures",
                    "Regulatory compliance",
                    "Documentation skills"
                ],
                requiredDocs: [
                    "Resume / Bio-data",
                    "Valid Government ID",
                    "Passport-size photo",
                    "Healthcare certifications",
                    "Safety training certificates",
                    "NBI / Police Clearance",
                    "Company Policy & NDA Acknowledgement"
                ]
            }
        };

        let currentFilter = 'all';
        let selectedJobId = null;

        // Check for job parameter in URL and auto-select on page load
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const jobId = urlParams.get('job');

            if (jobId && jobs[jobId]) {
                selectJob(parseInt(jobId));
                const selectedJobCard = document.querySelector(`[data-job-id="${jobId}"]`);
                if (selectedJobCard) {
                    selectedJobCard.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            } else {
                // Pre-select the first job (job ID 1) on page load
                selectJob(1);
            }
        });

        function selectJob(jobId) {
            selectedJobId = jobId;

            // Remove active styling from all jobs
            document.querySelectorAll('.job-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-blue-500', 'dark:ring-blue-400');
            });

            // Add active styling to selected job
            const selectedCard = document.querySelector(`[data-job-id="${jobId}"] .job-card`);
            if (selectedCard) {
                selectedCard.classList.add('ring-2', 'ring-blue-500', 'dark:ring-blue-400');
            }

            // Update job details
            const job = jobs[jobId];
            if (job) {
                updateJobDetails(job);
                // Show detail panel, hide empty state
                document.getElementById('jobDetailPanel').style.display = 'block';
                document.getElementById('emptyState').style.display = 'none';
            }
        }

        function updateJobDetails(job) {
            // Update type badge
            document.getElementById('jobTypeBadge').textContent = job.typeBadge;

            // Update title
            document.getElementById('jobTitle').textContent = job.title;

            // Update location
            document.getElementById('jobLocation').innerHTML =
                `<i class="fas fa-map-marker-alt"></i> ${job.location}`;

            // Update salary
            document.getElementById('jobSalary').textContent = job.salary;

            // Update description
            document.getElementById('jobDescription').textContent = job.description;

            // Update required skills
            const skillsList = document.getElementById('requiredSkills');
            if (skillsList && job.requiredSkills) {
                skillsList.innerHTML = job.requiredSkills.map(skill => `
                    <li class="flex items-start gap-2">
                        <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                        <span>${skill}</span>
                    </li>
                `).join('');
            }

            // Update required documents
            const docsList = document.getElementById('requiredDocs');
            if (docsList && job.requiredDocs) {
                docsList.innerHTML = job.requiredDocs.map(doc => `
                    <li class="flex items-start gap-2">
                        <span class="text-blue-600 dark:text-blue-400 mt-1">•</span>
                        <span>${doc}</span>
                    </li>
                `).join('');
            }
        }

        function filterJobs(type) {
            currentFilter = type;

            // Update tab styles
            document.querySelectorAll('.filter-tab').forEach(tab => {
                const filter = tab.getAttribute('data-filter');
                if (filter === type) {
                    tab.className =
                        'filter-tab px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 bg-blue-600 text-white shadow-lg';
                } else {
                    tab.className =
                        'filter-tab px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700';
                }
            });

            // Filter jobs
            const jobItems = document.querySelectorAll('.job-item');
            jobItems.forEach(item => {
                const jobType = item.getAttribute('data-type');
                if (type === 'all' || jobType === type) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function switchDetailTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.detail-tab').forEach(tab => {
                tab.classList.remove('text-blue-600', 'dark:text-blue-400', 'border-b-2', 'border-blue-600',
                    'dark:border-blue-400');
                tab.classList.add('text-gray-600', 'dark:text-gray-400');
            });

            const activeTab = document.getElementById(`tab-${tabName}`);
            if (activeTab) {
                activeTab.classList.remove('text-gray-600', 'dark:text-gray-400');
                activeTab.classList.add('text-blue-600', 'dark:text-blue-400', 'border-b-2', 'border-blue-600',
                    'dark:border-blue-400');
            }

            // Update tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });

            const activeContent = document.getElementById(`tab-content-${tabName}`);
            if (activeContent) {
                activeContent.style.display = 'block';
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const jobItems = document.querySelectorAll('.job-item');

            jobItems.forEach(item => {
                const title = item.querySelector('h3').textContent.toLowerCase();
                const description = item.querySelector('p').textContent.toLowerCase();
                const jobType = item.getAttribute('data-type');

                const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
                const matchesFilter = currentFilter === 'all' || jobType === currentFilter;

                if (matchesSearch && matchesFilter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Modal functions
        function openApplicationModal() {
            document.getElementById('applicationModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeApplicationModal() {
            document.getElementById('applicationModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('applicationForm').reset();
            document.getElementById('fileName').textContent = 'Choose file';
        }

        // Update file name display
        function updateFileName(input) {
            const fileName = input.files[0]?.name || 'Choose file';
            document.getElementById('fileName').textContent = fileName;
        }

        // Handle form submission
        document.getElementById('applicationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Here you would typically send the data to your server
            console.log('Form submitted with data:', {
                email: formData.get('email'),
                alternative_email: formData.get('alternative_email'),
                pdf_document: formData.get('pdf_document')?.name
            });

            // Show success message or handle the submission
            alert('Application submitted successfully!');
            closeApplicationModal();
        });

        // Close modal when clicking outside
        document.getElementById('applicationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApplicationModal();
            }
        });
    </script>
@endpush