<x-layouts.general-employer :title="'Application Details'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header with Back Button -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.recruitment.index') }}"
                   class="p-2 bg-white dark:bg-gray-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fa-solid fa-arrow-left text-gray-600 dark:text-gray-300"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Application #{{ $application->id }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Applied on {{ $application->created_at->format('F d, Y \a\t h:i A') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.recruitment.download', $application->id) }}"
                   class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fa-solid fa-download mr-2"></i>Download Resume
                </a>
                <form action="{{ route('admin.recruitment.destroy', $application->id) }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this application? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fa-solid fa-trash mr-2"></i>Delete
                    </button>
                </form>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Applicant Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-user-circle text-blue-600 dark:text-blue-400 mr-2"></i>
                        Applicant Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Email Address</label>
                            <p class="text-gray-900 dark:text-white">{{ $application->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Alternative Email</label>
                            <p class="text-gray-900 dark:text-white">{{ $application->alternative_email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Job Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-briefcase text-green-600 dark:text-green-400 mr-2"></i>
                        Position Applied For
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Job Title</label>
                            <p class="text-gray-900 dark:text-white text-lg font-medium">{{ $application->job_title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Employment Type</label>
                            @if($application->job_type)
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                {{ ucfirst(str_replace('-', ' ', $application->job_type)) }}
                            </span>
                            @else
                            <p class="text-gray-900 dark:text-white">N/A</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Resume Preview -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-file-pdf text-red-600 dark:text-red-400 mr-2"></i>
                        Resume / Documents
                    </h2>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                        <i class="fa-solid fa-file-pdf text-red-500 text-4xl mb-3"></i>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $application->resume_original_name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">PDF Document</p>
                        <a href="{{ route('admin.recruitment.download', $application->id) }}"
                           class="inline-flex items-center px-4 py-2 mt-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fa-solid fa-download mr-2"></i>
                            Download Resume
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Update -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-sliders text-purple-600 dark:text-purple-400 mr-2"></i>
                        Update Status
                    </h2>
                    <form action="{{ route('admin.recruitment.update-status', $application->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Status</label>
                                <span class="px-3 py-1.5 text-sm font-semibold rounded-full {{ $application->status_badge_class }}">
                                    {{ $application->status_label }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Change Status</label>
                                <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="reviewed" {{ $application->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                    <option value="interview_scheduled" {{ $application->status == 'interview_scheduled' ? 'selected' : '' }}>Interview Scheduled</option>
                                    <option value="hired" {{ $application->status == 'hired' ? 'selected' : '' }}>Hired</option>
                                    <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Admin Notes</label>
                                <textarea name="admin_notes" rows="4"
                                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Add notes about this applicant...">{{ $application->admin_notes }}</textarea>
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fa-solid fa-save mr-2"></i>Update Status
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Timeline -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-clock-rotate-left text-orange-600 dark:text-orange-400 mr-2"></i>
                        Timeline
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 mt-2 bg-blue-600 rounded-full"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Application Submitted</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @if($application->reviewed_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 mt-2 bg-green-600 rounded-full"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Last Updated</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->reviewed_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-bolt text-yellow-600 dark:text-yellow-400 mr-2"></i>
                        Quick Actions
                    </h2>
                    <div class="space-y-3">
                        <a href="mailto:{{ $application->email }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fa-solid fa-envelope text-blue-600 dark:text-blue-400"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Send Email</span>
                        </a>
                        <a href="{{ route('admin.recruitment.download', $application->id) }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fa-solid fa-download text-green-600 dark:text-green-400"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Download Resume</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.general-employer>
