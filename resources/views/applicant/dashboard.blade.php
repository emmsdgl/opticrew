<x-layouts.general-applicant title="Dashboard">
    <div class="w-full space-y-6">

        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white">
            <h1 class="text-2xl font-bold">Welcome, {{ $user->name }}!</h1>
            <p class="text-blue-100 text-sm mt-1">Browse available positions and track your applications.</p>
        </div>

        {{-- My Applications --}}
        @if($myApplications->count() > 0)
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="fa-solid fa-file-lines mr-2 text-blue-500"></i>My Applications
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($myApplications as $application)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $application->job_title }}</h3>
                        @php
                            $badgeClass = match($application->status) {
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'reviewed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'interview_scheduled' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                'hired' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            };
                            $statusLabel = match($application->status) {
                                'pending' => 'Pending',
                                'reviewed' => 'Under Review',
                                'interview_scheduled' => 'Interview Scheduled',
                                'hired' => 'Hired',
                                'rejected' => 'Not Selected',
                                default => ucfirst($application->status),
                            };
                        @endphp
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badgeClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    @if($application->job_type)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        <i class="fa-solid fa-briefcase mr-1"></i>{{ ucfirst(str_replace('-', ' ', $application->job_type)) }}
                    </p>
                    @endif
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        <i class="fa-regular fa-clock mr-1"></i>Applied {{ $application->created_at->diffForHumans() }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Available Job Postings --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="fa-solid fa-magnifying-glass mr-2 text-blue-500"></i>Available Positions
            </h2>

            @if($jobPostings->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($jobPostings as $job)
                @php
                    $alreadyApplied = $myApplications->where('job_title', $job->title)->count() > 0;
                    $iconColors = [
                        'blue' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                        'green' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
                        'purple' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
                        'orange' => 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400',
                        'red' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                    ];
                    $iconColorClass = $iconColors[$job->icon_color] ?? $iconColors['blue'];
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                    {{-- Job Icon & Type Badge --}}
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $iconColorClass }}">
                            <i class="fa-solid {{ $job->icon ?? 'fa-briefcase' }}"></i>
                        </div>
                        <span class="text-xs font-medium px-2 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                            {{ $job->type_badge }}
                        </span>
                    </div>

                    {{-- Job Title --}}
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $job->title }}</h3>

                    {{-- Location & Salary --}}
                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mb-3">
                        @if($job->location)
                        <span><i class="fa-solid fa-location-dot mr-1"></i>{{ $job->location }}</span>
                        @endif
                        @if($job->salary)
                        <span><i class="fa-solid fa-coins mr-1"></i>{{ $job->salary }}</span>
                        @endif
                    </div>

                    {{-- Description --}}
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">{{ $job->description }}</p>

                    {{-- Required Skills --}}
                    @if($job->required_skills && count($job->required_skills) > 0)
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @foreach(array_slice($job->required_skills, 0, 3) as $skill)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ $skill }}</span>
                        @endforeach
                        @if(count($job->required_skills) > 3)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">+{{ count($job->required_skills) - 3 }} more</span>
                        @endif
                    </div>
                    @endif

                    {{-- Apply Button --}}
                    @if($alreadyApplied)
                    <button disabled class="w-full py-2.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 cursor-not-allowed">
                        <i class="fa-solid fa-check mr-1"></i>Already Applied
                    </button>
                    @else
                    <form action="{{ route('recruitment.google.apply') }}" method="POST">
                        @csrf
                        <input type="hidden" name="job_title" value="{{ $job->title }}">
                        <input type="hidden" name="job_type" value="{{ $job->type }}">
                        <button type="submit" class="w-full py-2.5 rounded-lg text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                            <i class="fa-solid fa-paper-plane mr-1"></i>Apply Now
                        </button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-briefcase text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-1">No positions available</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Check back later for new job openings.</p>
            </div>
            @endif
        </div>
    </div>
</x-layouts.general-applicant>
