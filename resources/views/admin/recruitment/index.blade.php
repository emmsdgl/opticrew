<x-layouts.general-employer :title="'Job Applications'">
    <section role="status" class="w-full flex flex-col lg:flex-col gap-4 p-4 md:p-6">
        <!-- Header -->
        <div class="flex flex-col gap-2 mb-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Job Applications</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">View and manage job applications from candidates</p>
        </div>

        <!-- Stats Cards -->
        <div class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <x-labelwithvalue label="Summary" count="" />

            <div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 px-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $applications->where('status', 'pending')->count() }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-full">
                            <i class="fa-solid fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Reviewed</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $applications->where('status', 'reviewed')->count() }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                            <i class="fa-solid fa-eye text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Interview</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $applications->where('status', 'interview_scheduled')->count() }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                            <i class="fa-solid fa-calendar-check text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Hired</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $applications->where('status', 'hired')->count() }}</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                            <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $applications->total() }}</p>
                        </div>
                        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                            <i class="fa-solid fa-users text-gray-600 dark:text-gray-300 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-col gap-4 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <x-labelwithvalue label="Filters" count="" />

            <form method="GET" action="{{ route('admin.recruitment.index') }}" class="flex flex-col md:flex-row gap-4 px-2">
                <!-- Search -->
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by email or job title..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                </div>

                <!-- Status Filter -->
                <div class="w-full md:w-48">
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="all">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="interview_scheduled" {{ request('status') == 'interview_scheduled' ? 'selected' : '' }}>Interview Scheduled</option>
                        <option value="hired" {{ request('status') == 'hired' ? 'selected' : '' }}>Hired</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fa-solid fa-filter mr-2"></i>Filter
                </button>

                <!-- Clear Button -->
                @if(request('search') || request('status'))
                <a href="{{ route('admin.recruitment.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-center text-sm font-medium">
                    <i class="fa-solid fa-times mr-2"></i>Clear
                </a>
                @endif
            </form>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Applications List -->
        <div class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <x-labelwithvalue label="Applications" count="({{ $applications->total() }})" />

            <div class="w-full overflow-x-auto">
                <!-- Table Header -->
                <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-4 bg-gray-50 dark:bg-gray-800
                            border-b border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="col-span-1 flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Status
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
                        </svg>
                    </div>
                    <div class="col-span-1 flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        ID
                    </div>
                    <div class="col-span-3 flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Applicant
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
                        </svg>
                    </div>
                    <div class="col-span-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Position</div>
                    <div class="col-span-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Resume</div>
                    <div class="col-span-2 flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Applied
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
                        </svg>
                    </div>
                    <div class="col-span-1 text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Action</div>
                </div>

                <!-- Table Body -->
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($applications as $application)
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 px-6 py-4 bg-white dark:bg-gray-900
                                hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">

                        <!-- Status Badge -->
                        <div class="col-span-1 flex items-center gap-2 justify-start">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400">Status:</span>
                            @switch($application->status)
                                @case('pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        Pending
                                    </span>
                                    @break
                                @case('reviewed')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        Reviewed
                                    </span>
                                    @break
                                @case('interview_scheduled')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                        Interview
                                    </span>
                                    @break
                                @case('hired')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        Hired
                                    </span>
                                    @break
                                @case('rejected')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        Rejected
                                    </span>
                                    @break
                                @default
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        {{ ucfirst($application->status) }}
                                    </span>
                            @endswitch
                        </div>

                        <!-- ID -->
                        <div class="col-span-1 flex flex-col justify-center">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">ID:</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">#{{ $application->id }}</span>
                        </div>

                        <!-- Applicant -->
                        <div class="col-span-3 flex flex-col justify-center">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Applicant:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $application->email }}</span>
                            @if($application->alternative_email)
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate">Alt: {{ $application->alternative_email }}</span>
                            @endif
                        </div>

                        <!-- Position -->
                        <div class="col-span-2 flex flex-col justify-center">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Position:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $application->job_title }}</span>
                            @if($application->job_type)
                            <span class="text-xs text-blue-600 dark:text-blue-400">{{ ucfirst(str_replace('-', ' ', $application->job_type)) }}</span>
                            @endif
                        </div>

                        <!-- Resume -->
                        <div class="col-span-2 flex flex-col justify-center">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Resume:</span>
                            <a href="{{ route('admin.recruitment.download', $application->id) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-xs w-fit">
                                <i class="fa-solid fa-download mr-2"></i>
                                {{ Str::limit($application->resume_original_name, 15) }}
                            </a>
                        </div>

                        <!-- Applied Date -->
                        <div class="col-span-2 flex flex-col justify-center">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Applied:</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $application->created_at->format('M d, Y') }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $application->created_at->format('h:i A') }}</span>
                        </div>

                        <!-- Action Button -->
                        <div class="col-span-1 flex items-center justify-center">
                            <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 mr-2">Action:</span>
                            <a href="{{ route('admin.recruitment.show', $application->id) }}"
                               class="w-full px-4 py-2 rounded-lg text-xs transition-all duration-200 text-center text-white dark:text-blue-500">
                                <i class="fa-solid fa-eye mr-1"></i>View
                            </a>
                        </div>
                    </div>
                    @empty
                    <!-- Empty State -->
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-inbox text-4xl mb-3"></i>
                        <p class="text-lg">No job applications found</p>
                        <p class="text-sm mt-2">Applications submitted from the recruitment page will appear here</p>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($applications->hasPages())
                <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6 mt-4 rounded-lg">
                    {{ $applications->links() }}
                </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.general-employer>
