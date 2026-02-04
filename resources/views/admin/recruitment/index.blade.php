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
                            <i class="fa-solid fa-eye text-blue-600 dark:text-blue-400 text-sm"></i>
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
                            <span class="md:hidden text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1 mr-2">Action:</span>
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

        <!-- Job Postings Section -->
        <div class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4"
            x-data="jobPostingsData()">

            <div class="flex items-center justify-between">
                <x-labelwithvalue label="Job Postings" count="" />
                <button @click="openModal()"
                    class="px-4 py-2 text-blue-600 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fa-solid fa-plus mr-2"></i>Add Job Posting
                </button>
            </div>

            <!-- Job Postings Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <template x-for="(job, index) in jobPostings" :key="index">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                :class="getIconBgClass(job.iconColor)">
                                <i class="fas text-xl" :class="[job.icon, getIconTextClass(job.iconColor)]"></i>
                            </div>
                            <div class="flex gap-2">
                                <button @click="editJob(index)" class="text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <button @click="deleteJob(index)" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded mb-2"
                            :class="getTypeBadgeClass(job.type)"
                            x-text="job.typeBadge"></span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2" x-text="job.title"></h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4 line-clamp-2" x-text="job.description"></p>
                        <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mb-3">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-map-marker-alt"></i>
                                <span x-text="job.location"></span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="job.salary"></span>
                            <span class="px-2 py-1 text-xs rounded-full"
                                :class="job.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
                                x-text="job.is_active ? 'Active' : 'Inactive'"></span>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <template x-if="jobPostings.length === 0">
                    <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-briefcase text-4xl mb-3"></i>
                        <p class="text-lg">No job postings yet</p>
                        <p class="text-sm mt-2">Click "Add Job Posting" to create your first job listing</p>
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
                                <input type="text" x-model="formData.salary" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                    placeholder="e.g., $30 - $40/hr">
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

    <script>
    function jobPostingsData() {
        // Map database records to JS format
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
            requiredDocs: job.required_docs || []
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
