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
                <div id="jobList" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-8">
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
                    @endphp
                    <div class="job-item cursor-pointer" data-job-id="{{ $job->id }}" data-type="{{ $job->type }}"
                        onclick="selectJob({{ $job->id }})">
                        <div
                            class="job-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-800 flex flex-col h-full">
                            <div class="flex items-start justify-between mb-6">
                                <div class="w-12 h-12 {{ $iconBgClass }} rounded-xl flex items-center justify-center">
                                    <i class="fas {{ $job->icon }} {{ $iconTextClass }} text-lg"></i>
                                </div>

                                <button class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                    <i class="far fa-heart text-xl"></i>
                                </button>
                            </div>
                            <span class="inline-block self-start px-2 py-1 {{ $typeBadgeClass }} text-xs font-medium rounded mb-3">
                                {{ $job->type_badge }}
                            </span>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">{{ $job->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 line-clamp-3 flex-1">
                                {{ $job->description }}
                            </p>
                            <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $job->salary }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $job->location }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 text-center py-12">
                        <i class="fas fa-briefcase text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-base font-medium">No job openings available at the moment.</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Please check back later for new opportunities.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Right Side - Job Details --}}
            <div class="xl:col-span-1">
                <div id="jobDetailPanel"
                    class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 rounded-3xl p-8 sticky top-6 border border-blue-100 dark:border-gray-700"
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
                selectJob(parseInt(applyJob));
                setTimeout(() => openApplicationModal(), 500);
            }
        });

        // Navigate to Terms page with return context
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
                title: @json($job->title),
                description: @json($job->description),
                location: @json($job->location),
                salary: @json($job->salary),
                type: @json($job->type),
                typeBadge: @json($job->type_badge),
                icon: @json($job->icon),
                iconColor: @json($job->icon_color),
                requiredSkills: @json($job->required_skills ?? []),
                requiredDocs: @json($job->required_docs ?? [])
            },
            @endforeach
        };

        // Get first job ID for default selection
        const firstJobId = {{ ($jobPostings ?? collect())->first()?->id ?? 'null' }};

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
            } else if (firstJobId) {
                // Pre-select the first job on page load
                selectJob(firstJobId);
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
            // Set job details in hidden fields
            if (selectedJobId && jobs[selectedJobId]) {
                document.getElementById('applicationJobTitle').value = jobs[selectedJobId].title;
                document.getElementById('applicationJobType').value = jobs[selectedJobId].type;
            }
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