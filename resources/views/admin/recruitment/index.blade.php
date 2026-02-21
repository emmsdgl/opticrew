<x-layouts.general-employer :title="'Job Applications'">
    <section role="status" class="w-full flex flex-col lg:flex-col gap-4 p-4 md:p-6">
        <!-- Header -->
        <div class="flex flex-col gap-2 mb-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Job Applications</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">View and manage job applications from candidates</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-px bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Pending</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $applications->where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Reviewed</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $applications->where('status', 'reviewed')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Interview</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $applications->where('status', 'interview_scheduled')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Hired</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $applications->where('status', 'hired')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Total applications</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $applications->total() }}</p>
            </div>
        </div>

        <!-- Applications Section Header with Filters -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 my-4 mx-4">
            <div>
                <x-labelwithvalue label="Applications" count="({{ $applications->total() }})" />
            </div>

            <!-- Filters -->
            <div class="flex flex-col md:flex-row gap-4 flex-1 md:max-w-3xl">
                <!-- Search Bar -->
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" id="recruitmentSearchInput" placeholder="Search by email or job title..."
                            class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-2 md:gap-4">
                    <!-- Status Filter -->
                    <div class="w-full md:w-auto">
                        <x-filter-dropdown label="Filter by Status" :selected="request('status', 'all')" :options="[
                            'all' => 'All Status (' . $applications->total() . ')',
                            'pending' => 'Pending (' . $applications->where('status', 'pending')->count() . ')',
                            'reviewed' => 'Reviewed (' . $applications->where('status', 'reviewed')->count() . ')',
                            'interview_scheduled' => 'Interview (' . $applications->where('status', 'interview_scheduled')->count() . ')',
                            'hired' => 'Hired (' . $applications->where('status', 'hired')->count() . ')',
                            'rejected' => 'Rejected (' . $applications->where('status', 'rejected')->count() . ')',
                        ]"
                            onSelect="window.location.href='{{ route('admin.recruitment.index') }}?status={value}' + (document.getElementById('recruitmentSearchInput')?.value ? '&search=' + document.getElementById('recruitmentSearchInput').value : '')" />
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="w-full md:w-auto">
                        <x-dropdown label="Sort by:" default="Newest" :options="[
                            'newest' => 'Newest',
                            'oldest' => 'Oldest',
                            'email_asc' => 'Email (A-Z)',
                            'email_desc' => 'Email (Z-A)',
                        ]" id="recruitment-sort-dropdown" />
                    </div>
                </div>
            </div>
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
        <div class="flex flex-col gap-6 w-full rounded-lg p-4" x-data="applicationDrawerData()">

            <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Applicant</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Position</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Resume</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Applied</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                        <tr class="even:bg-gray-50 dark:even:bg-gray-800/50"
                            data-email="{{ strtolower($application->email) }}"
                            data-job="{{ strtolower($application->job_title) }}"
                            data-status="{{ $application->status }}"
                            data-date="{{ $application->created_at->timestamp }}">
                            <!-- Applicant -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $application->email }}</div>
                                @if($application->alternative_email)
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">Alt: {{ $application->alternative_email }}</div>
                                @endif
                            </td>

                            <!-- Position -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-200">{{ $application->job_title }}</div>
                                @if($application->job_type)
                                <div class="text-xs text-blue-600 dark:text-blue-400">{{ ucfirst(str_replace('-', ' ', $application->job_type)) }}</div>
                                @endif
                            </td>

                            <!-- Resume -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.recruitment.download', $application->id) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-xs">
                                    <i class="fa-solid fa-download mr-2"></i>
                                    {{ Str::limit($application->resume_original_name, 15) }}
                                </a>
                            </td>

                            <!-- Applied Date -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-200">{{ $application->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $application->created_at->format('h:i A') }}</div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($application->status)
                                    @case('pending')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400">Pending</span>
                                        @break
                                    @case('reviewed')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">Reviewed</span>
                                        @break
                                    @case('interview_scheduled')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400">Interview</span>
                                        @break
                                    @case('hired')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400">Hired</span>
                                        @break
                                    @case('rejected')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400">Rejected</span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-900/20 dark:text-gray-400">{{ ucfirst($application->status) }}</span>
                                @endswitch
                            </td>

                            <!-- Action -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button @click="openDrawer({{ $application->id }})"
                                   class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    View Details
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-inbox text-3xl mb-3 block w-full"></i>
                                <p class="text-base font-medium">No job applications found</p>
                                <p class="text-xs mt-2">Applications submitted from the recruitment page will appear here</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($applications->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $applications->links() }}
                </div>
                @endif
            </div>

            <!-- Application Details Slide-in Drawer -->
            <div x-show="showDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden">
                <!-- Backdrop -->
                <div x-show="showDrawer"
                     x-transition:enter="transition-opacity ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeDrawer()"
                     class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

                <!-- Drawer Panel -->
                <div class="fixed inset-y-0 right-0 flex max-w-full">
                    <div x-show="showDrawer"
                         x-transition:enter="transform transition ease-in-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-200"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         @click.stop
                         class="relative w-screen max-w-md sm:max-w-lg">

                        <div class="h-full flex flex-col bg-white dark:bg-slate-800 shadow-2xl border-l border-gray-200 dark:border-slate-700">
                            <!-- Header -->
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-slate-800/50">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Application Details</h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-show="selectedApp" x-text="'#' + selectedApp?.id"></p>
                                </div>
                                <button type="button" @click="closeDrawer()"
                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Body -->
                            <template x-if="selectedApp">
                                <div class="flex-1 overflow-y-auto p-6">

                                    <!-- Status Badge -->
                                    <div class="flex items-center gap-2 mb-6">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                        <span class="px-3 py-1 text-xs rounded-full font-semibold"
                                            :class="getStatusClass(selectedApp.status)"
                                            x-text="getStatusLabel(selectedApp.status)"></span>
                                    </div>

                                    <!-- Applicant Information -->
                                    <div class="mb-5">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-user-circle text-gray-600 dark:text-gray-400"></i>
                                            Applicant Information
                                        </h4>
                                        <div class="space-y-3 text-sm py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Email</span>
                                                <span class="font-medium text-gray-900 dark:text-white" x-text="selectedApp.email"></span>
                                            </div>
                                            <div class="flex justify-between items-center" x-show="selectedApp.alternative_email">
                                                <span class="text-gray-500 dark:text-gray-400">Alt. Email</span>
                                                <span class="font-medium text-gray-900 dark:text-white" x-text="selectedApp.alternative_email"></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Applied On</span>
                                                <span class="font-medium text-gray-900 dark:text-white" x-text="selectedApp.created_at"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Position Applied For -->
                                    <div class="mb-5">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-briefcase text-gray-600 dark:text-gray-400"></i>
                                            Position Applied For
                                        </h4>
                                        <div class="space-y-3 text-sm py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Job Title</span>
                                                <span class="font-medium text-gray-900 dark:text-white" x-text="selectedApp.job_title"></span>
                                            </div>
                                            <div class="flex justify-between items-center" x-show="selectedApp.job_type">
                                                <span class="text-gray-500 dark:text-gray-400">Employment Type</span>
                                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400"
                                                    x-text="selectedApp.job_type_label"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Resume -->
                                    <div class="mb-5">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-file-pdf text-gray-600 dark:text-gray-400"></i>
                                            Resume / Documents
                                        </h4>
                                        <div class="border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <i class="fa-solid fa-file-pdf text-gray-500 text-xl"></i>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedApp.resume_name"></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF Document</p>
                                                </div>
                                            </div>
                                            <a :href="selectedApp.download_url"
                                               class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Update Status -->
                                    <div class="mb-5">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-sliders text-gray-600 dark:text-gray-400"></i>
                                            Update Status
                                        </h4>
                                        <div class="space-y-3 py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Change Status</label>
                                                <select x-model="drawerStatus"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                    <option value="pending">Pending</option>
                                                    <option value="reviewed">Reviewed</option>
                                                    <option value="interview_scheduled">Interview Scheduled</option>
                                                    <option value="hired">Hired</option>
                                                    <option value="rejected">Rejected</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Admin Notes</label>
                                                <textarea x-model="drawerNotes" rows="3"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                                    placeholder="Add notes about this applicant..."></textarea>
                                            </div>
                                            <button @click="updateStatus()"
                                                :disabled="isUpdating"
                                                class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                                                <span x-show="!isUpdating"><i class="fa-solid fa-save mr-2"></i>Update Status</span>
                                                <span x-show="isUpdating">Updating...</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Timeline -->
                                    <div class="mb-5">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-clock-rotate-left text-gray-600 dark:text-gray-400"></i>
                                            Timeline
                                        </h4>
                                        <div class="space-y-3 py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <div class="flex items-start gap-3">
                                                <div class="w-2 h-2 mt-1.5 bg-blue-600 rounded-full flex-shrink-0"></div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Application Submitted</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedApp.created_at"></p>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-3" x-show="selectedApp.reviewed_at">
                                                <div class="w-2 h-2 mt-1.5 bg-green-600 rounded-full flex-shrink-0"></div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Last Updated</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedApp.reviewed_at"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-bolt text-gray-600 dark:text-gray-400"></i>
                                            Quick Actions
                                        </h4>
                                        <div class="space-y-2">
                                            <a :href="'mailto:' + selectedApp.email"
                                               class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                <i class="fa-solid fa-envelope text-gray-600 dark:text-gray-400"></i>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">Send Email</span>
                                            </a>
                                            <a :href="selectedApp.download_url"
                                               class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                <i class="fa-solid fa-download text-gray-600 dark:text-gray-400"></i>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">Download Resume</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Footer -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                                <button @click="closeDrawer()"
                                    class="w-full px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Job Postings Section -->
        <div class="flex flex-col gap-6 w-full rounded-lg p-4"
            x-data="jobPostingsData()">

            <div class="flex items-center justify-between">
                <x-labelwithvalue label="Job Postings" count="" />
                <button @click="openModal()"
                    class="px-4 py-2 text-blue-600 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fa-solid fa-plus mr-2"></i>Add Job Posting
                </button>
            </div>

            <!-- Job Postings Table -->
            <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Job Title</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Location</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Salary</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Applicants</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(job, index) in jobPostings" :key="index">
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800/50">
                                <!-- Job Title + Icon -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                            :class="getIconBgClass(job.iconColor)">
                                            <i class="fas text-sm" :class="[job.icon, getIconTextClass(job.iconColor)]"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="job.title"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 max-w-[200px] truncate" x-text="job.description"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="getTypeBadgeClass(job.type)"
                                        x-text="job.typeBadge"></span>
                                </td>

                                <!-- Location -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1 text-sm text-gray-900 dark:text-gray-200">
                                        <i class="fas fa-map-marker-alt text-gray-400 text-xs"></i>
                                        <span x-text="job.location"></span>
                                    </div>
                                </td>

                                <!-- Salary -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="job.salary"></div>
                                </td>

                                <!-- Applicants -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200" x-text="(job.applicantCount || 0) + ' ' + ((job.applicantCount === 1) ? 'applicant' : 'applicants')"></div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="job.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
                                        x-text="job.is_active ? 'Active' : 'Inactive'"></span>
                                </td>

                                <!-- Action -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="editJob(index)"
                                            class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <button @click="deleteJob(index)"
                                            class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <!-- Empty State -->
                <template x-if="jobPostings.length === 0">
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-briefcase text-3xl mb-3"></i>
                        <p class="text-base font-medium">No job postings yet</p>
                        <p class="text-xs mt-2">Click "Add Job Posting" to create your first job listing</p>
                    </div>
                </template>
            </div>

            <!-- Job Posting Modal -->
            <div x-show="showModal" x-cloak
                @click="closeModal()"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/80 p-4"
                style="display: none;">
                <div @click.stop
                    class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700"
                    x-show="showModal" x-transition>

                    <!-- Modal Header -->
                    <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="editingIndex !== null ? 'Edit Job Posting' : 'Create Job Posting'"></h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Title *</label>
                            <input type="text" x-model="formData.title" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="e.g., Deep Cleaning Specialist">
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description *</label>
                            <textarea x-model="formData.description" rows="3" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="Brief description of the job position..."></textarea>
                        </div>

                        <!-- Two Column Layout -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Location -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location *</label>
                                <input type="text" x-model="formData.location" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                    placeholder="e.g., Imst, Finland">
                            </div>

                            <!-- Salary -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Salary *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-500 dark:text-gray-400">&euro;</span>
                                    <input type="text" x-model="formData.salary" required
                                        class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                        placeholder="e.g., 30 - 40/hr">
                                </div>
                            </div>

                            <!-- Job Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Type *</label>
                                <select x-model="formData.type" @change="updateTypeBadge()"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                                    <option value="full-time">Full-time</option>
                                    <option value="part-time">Part-time</option>
                                    <option value="remote">Remote</option>
                                </select>
                            </div>

                            <!-- Icon -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Icon</label>
                                <select x-model="formData.icon"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                                    <option value="fa-user-tie">User Tie</option>
                                    <option value="fa-broom">Broom</option>
                                    <option value="fa-dolly">Dolly</option>
                                    <option value="fa-clipboard-check">Clipboard Check</option>
                                    <option value="fa-headset">Headset</option>
                                    <option value="fa-spray-can">Spray Can</option>
                                    <option value="fa-users">Users</option>
                                    <option value="fa-briefcase">Briefcase</option>
                                </select>
                            </div>

                            <!-- Icon Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Icon Color</label>
                                <select x-model="formData.iconColor"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                                    <option value="blue">Blue</option>
                                    <option value="green">Green</option>
                                    <option value="purple">Purple</option>
                                    <option value="orange">Orange</option>
                                    <option value="red">Red</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <select x-model="formData.is_active"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                                    <option :value="true">Active</option>
                                    <option :value="false">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Required Skills -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Required Skills</label>
                            <div class="space-y-2">
                                <template x-for="(skill, idx) in formData.requiredSkills" :key="idx">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="formData.requiredSkills[idx]"
                                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                            placeholder="Enter skill...">
                                        <button type="button" @click="removeSkill(idx)" class="px-3 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="addSkill()" class="px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg">
                                    <i class="fa-solid fa-plus mr-2"></i>Add Skill
                                </button>
                            </div>
                        </div>

                        <!-- Required Documents -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Required Documents</label>
                            <div class="space-y-2">
                                <template x-for="(doc, idx) in formData.requiredDocs" :key="idx">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="formData.requiredDocs[idx]"
                                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                            placeholder="Enter document...">
                                        <button type="button" @click="removeDoc(idx)" class="px-3 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="addDoc()" class="px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg">
                                    <i class="fa-solid fa-plus mr-2"></i>Add Document
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
                        <button @click="closeModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-medium">
                            Cancel
                        </button>
                        <button @click="saveJob()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            <i class="fa-solid fa-save mr-2"></i>
                            <span x-text="editingIndex !== null ? 'Update' : 'Create'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </section>

    @php
        $applicationsData = $applications->getCollection()->map(function($app) {
            return [
                'id' => $app->id,
                'email' => $app->email,
                'alternative_email' => $app->alternative_email,
                'job_title' => $app->job_title,
                'job_type' => $app->job_type,
                'job_type_label' => $app->job_type ? ucfirst(str_replace('-', ' ', $app->job_type)) : null,
                'status' => $app->status,
                'admin_notes' => $app->admin_notes,
                'resume_name' => $app->resume_original_name,
                'download_url' => route('admin.recruitment.download', $app->id),
                'created_at' => $app->created_at->format('M d, Y h:i A'),
                'reviewed_at' => $app->reviewed_at ? $app->reviewed_at->format('M d, Y h:i A') : null,
            ];
        });
    @endphp

    <script>
    // Search functionality for applications table
    const recruitmentSearchInput = document.getElementById('recruitmentSearchInput');
    if (recruitmentSearchInput) {
        recruitmentSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr[data-email]');

            rows.forEach(row => {
                const email = row.getAttribute('data-email') || '';
                const job = row.getAttribute('data-job') || '';

                const matches = email.includes(searchTerm) || job.includes(searchTerm);
                row.style.display = matches ? '' : 'none';
            });
        });
    }

    // Sort functionality
    const sortDropdown = document.getElementById('recruitment-sort-dropdown');
    if (sortDropdown) {
        sortDropdown.addEventListener('change', function() {
            const sortBy = this.value;
            const tbody = document.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr[data-email]'));

            rows.sort((a, b) => {
                switch (sortBy) {
                    case 'oldest':
                        return parseInt(a.dataset.date) - parseInt(b.dataset.date);
                    case 'newest':
                        return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                    case 'email_asc':
                        return a.dataset.email.localeCompare(b.dataset.email);
                    case 'email_desc':
                        return b.dataset.email.localeCompare(a.dataset.email);
                    default:
                        return 0;
                }
            });

            rows.forEach(row => tbody.appendChild(row));
        });
    }

    function applicationDrawerData() {
        const applications = @json($applicationsData);

        return {
            showDrawer: false,
            selectedApp: null,
            drawerStatus: '',
            drawerNotes: '',
            isUpdating: false,

            openDrawer(id) {
                this.selectedApp = applications.find(a => a.id === id);
                if (this.selectedApp) {
                    this.drawerStatus = this.selectedApp.status;
                    this.drawerNotes = this.selectedApp.admin_notes || '';
                    this.showDrawer = true;
                    document.body.style.overflow = 'hidden';
                }
            },

            closeDrawer() {
                this.showDrawer = false;
                this.selectedApp = null;
                document.body.style.overflow = 'auto';
            },

            getStatusClass(status) {
                const classes = {
                    'pending': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400',
                    'reviewed': 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
                    'interview_scheduled': 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400',
                    'hired': 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
                    'rejected': 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
                };
                return classes[status] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400';
            },

            getStatusLabel(status) {
                const labels = {
                    'pending': 'Pending',
                    'reviewed': 'Reviewed',
                    'interview_scheduled': 'Interview Scheduled',
                    'hired': 'Hired',
                    'rejected': 'Rejected',
                };
                return labels[status] || status;
            },

            async updateStatus() {
                if (!this.selectedApp) return;
                this.isUpdating = true;

                try {
                    const response = await fetch(`/admin/recruitment/${this.selectedApp.id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status: this.drawerStatus,
                            admin_notes: this.drawerNotes
                        })
                    });

                    if (response.ok) {
                        this.selectedApp.status = this.drawerStatus;
                        this.selectedApp.admin_notes = this.drawerNotes;
                        this.selectedApp.reviewed_at = new Date().toLocaleString('en-US', {
                            month: 'short', day: '2-digit', year: 'numeric',
                            hour: '2-digit', minute: '2-digit', hour12: true
                        });
                        // Reload to reflect changes in table
                        window.location.reload();
                    } else {
                        alert('Failed to update status. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while updating the status.');
                } finally {
                    this.isUpdating = false;
                }
            }
        };
    }

    function jobPostingsData() {
        // Map database records to JS format
        const applicantCounts = @json($applicantCounts ?? new \stdClass);
        const dbJobPostings = @json($jobPostings ?? []).map(job => ({
            id: job.id,
            title: job.title,
            description: job.description,
            location: job.location,
            salary: job.salary,
            type: job.type,
            typeBadge: job.type_badge,
            icon: job.icon,
            iconColor: job.icon_color,
            is_active: job.is_active,
            requiredSkills: job.required_skills || [],
            requiredDocs: job.required_docs || [],
            applicantCount: applicantCounts[job.title] || 0
        }));

        return {
            showModal: false,
            editingIndex: null,
            isSubmitting: false,
            formData: {
                id: null,
                title: '',
                description: '',
                location: '',
                salary: '',
                type: 'full-time',
                typeBadge: 'Full-time Employee',
                icon: 'fa-user-tie',
                iconColor: 'blue',
                is_active: true,
                requiredSkills: [''],
                requiredDocs: ['']
            },
            jobPostings: dbJobPostings,

            openModal() {
                this.showModal = true;
                document.body.style.overflow = 'hidden';
            },

            closeModal() {
                this.showModal = false;
                this.editingIndex = null;
                this.resetForm();
                document.body.style.overflow = 'auto';
            },

            resetForm() {
                this.formData = {
                    id: null,
                    title: '',
                    description: '',
                    location: '',
                    salary: '',
                    type: 'full-time',
                    typeBadge: 'Full-time Employee',
                    icon: 'fa-user-tie',
                    iconColor: 'blue',
                    is_active: true,
                    requiredSkills: [''],
                    requiredDocs: ['']
                };
            },

            editJob(index) {
                this.editingIndex = index;
                const job = this.jobPostings[index];
                this.formData = {
                    id: job.id,
                    title: job.title,
                    description: job.description,
                    location: job.location,
                    salary: job.salary,
                    type: job.type,
                    typeBadge: job.typeBadge,
                    icon: job.icon,
                    iconColor: job.iconColor,
                    is_active: job.is_active,
                    requiredSkills: job.requiredSkills?.length ? [...job.requiredSkills] : [''],
                    requiredDocs: job.requiredDocs?.length ? [...job.requiredDocs] : ['']
                };
                this.openModal();
            },

            async deleteJob(index) {
                if (!confirm('Are you sure you want to delete this job posting?')) return;

                const job = this.jobPostings[index];
                if (!job.id) {
                    this.jobPostings.splice(index, 1);
                    return;
                }

                try {
                    const response = await fetch(`/admin/job-postings/${job.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.jobPostings.splice(index, 1);
                    } else {
                        alert(data.message || 'Failed to delete job posting.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the job posting.');
                }
            },

            async saveJob() {
                if (!this.formData.title || !this.formData.description || !this.formData.location || !this.formData.salary) {
                    alert('Please fill in all required fields.');
                    return;
                }

                this.isSubmitting = true;

                // Filter out empty skills and docs
                const requiredSkills = this.formData.requiredSkills.filter(s => s.trim() !== '');
                const requiredDocs = this.formData.requiredDocs.filter(d => d.trim() !== '');

                // Prepare data for API
                const payload = {
                    title: this.formData.title,
                    description: this.formData.description,
                    location: this.formData.location,
                    salary: this.formData.salary,
                    type: this.formData.type,
                    type_badge: this.formData.typeBadge,
                    icon: this.formData.icon,
                    icon_color: this.formData.iconColor,
                    is_active: this.formData.is_active,
                    required_skills: requiredSkills,
                    required_docs: requiredDocs
                };

                try {
                    let response;
                    if (this.editingIndex !== null && this.formData.id) {
                        // Update existing job
                        response = await fetch(`/admin/job-postings/${this.formData.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });
                    } else {
                        // Create new job
                        response = await fetch('/admin/job-postings', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });
                    }

                    const data = await response.json();
                    if (data.success) {
                        // Map response data to JS format
                        const savedJob = {
                            id: data.data.id,
                            title: data.data.title,
                            description: data.data.description,
                            location: data.data.location,
                            salary: data.data.salary,
                            type: data.data.type,
                            typeBadge: data.data.type_badge,
                            icon: data.data.icon,
                            iconColor: data.data.icon_color,
                            is_active: data.data.is_active,
                            requiredSkills: data.data.required_skills || [],
                            requiredDocs: data.data.required_docs || []
                        };

                        if (this.editingIndex !== null) {
                            this.jobPostings[this.editingIndex] = savedJob;
                        } else {
                            this.jobPostings.unshift(savedJob);
                        }
                        this.closeModal();
                    } else {
                        alert(data.message || 'Failed to save job posting.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while saving the job posting.');
                } finally {
                    this.isSubmitting = false;
                }
            },

            updateTypeBadge() {
                const badges = {
                    'full-time': 'Full-time Employee',
                    'part-time': 'Part-time Employee',
                    'remote': 'Remote'
                };
                this.formData.typeBadge = badges[this.formData.type] || 'Full-time Employee';
            },

            addSkill() {
                this.formData.requiredSkills.push('');
            },

            removeSkill(index) {
                if (this.formData.requiredSkills.length > 1) {
                    this.formData.requiredSkills.splice(index, 1);
                }
            },

            addDoc() {
                this.formData.requiredDocs.push('');
            },

            removeDoc(index) {
                if (this.formData.requiredDocs.length > 1) {
                    this.formData.requiredDocs.splice(index, 1);
                }
            },

            getIconBgClass(color) {
                const classes = {
                    'blue': 'bg-blue-50 dark:bg-blue-900/30',
                    'green': 'bg-green-50 dark:bg-green-900/30',
                    'purple': 'bg-purple-50 dark:bg-purple-900/30',
                    'orange': 'bg-orange-50 dark:bg-orange-900/30',
                    'red': 'bg-red-50 dark:bg-red-900/30'
                };
                return classes[color] || classes['blue'];
            },

            getIconTextClass(color) {
                const classes = {
                    'blue': 'text-blue-600 dark:text-blue-400',
                    'green': 'text-green-600 dark:text-green-400',
                    'purple': 'text-purple-600 dark:text-purple-400',
                    'orange': 'text-orange-600 dark:text-orange-400',
                    'red': 'text-red-600 dark:text-red-400'
                };
                return classes[color] || classes['blue'];
            },

            getTypeBadgeClass(type) {
                const classes = {
                    'full-time': 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                    'part-time': 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                    'remote': 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400'
                };
                return classes[type] || classes['full-time'];
            }
        };
    }
    </script>
</x-layouts.general-employer>
