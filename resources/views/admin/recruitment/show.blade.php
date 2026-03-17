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
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Application #  {{ $application->id }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Applied on {{ $application->created_at->format('F d, Y \a\t h:i A') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.recruitment.download', $application->id) }}"
                   class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fa-solid fa-download mr-2"></i>Download Resume
                </a>
                @if($application->status !== 'withdrawn')
                <form action="{{ route('admin.recruitment.destroy', $application->id) }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this application? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fa-solid fa-trash mr-2"></i>Delete
                    </button>
                </form>
                @endif
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

        {{-- Existing employee without Gmail linked --}}
        @if(session('gmail_link_prompt'))
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 flex items-start gap-4">
            <div class="flex-shrink-0">
                <i class="fa-brands fa-google text-amber-600 text-xl mt-0.5"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-300">Gmail Account Not Linked</h3>
                <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                    <strong>{{ session('gmail_link_user_name') }}</strong> is already an existing employee in the system, but their Gmail account is not yet linked.
                    For security purposes, they should link their personal Gmail account. They will be prompted to do so upon their next login.
                </p>
            </div>
        </div>
        @endif

        {{-- Hired but no employee profile yet — show setup button --}}
        @if($application->status === 'hired')
            @php
                $existingEmployee = \App\Models\User::where('email', $application->email)->where('role', 'employee')->whereHas('employee')->first();
                $applicantNoProfile = \App\Models\User::where('email', $application->email)->where('role', 'applicant')->first();
                $needsSetup = !$existingEmployee && ($applicantNoProfile || !\App\Models\User::where('email', $application->email)->where('role', 'employee')->exists());
            @endphp
            @if($needsSetup)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-user-plus text-blue-600 text-xl mt-0.5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-300">Employee Account Required</h3>
                        <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                            This applicant has been hired but doesn't have an employee account yet. Set up their Fin-noys employee account to complete the onboarding.
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.recruitment.setup-employee', $application->id) }}"
                   class="flex-shrink-0 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fa-solid fa-user-plus mr-2"></i>Setup Employee Account
                </a>
            </div>
            @endif
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Applicant Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-user-circle text-gray-600 dark:text-gray-400 mr-2"></i>
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
                        <i class="fa-solid fa-briefcase text-gray-600 dark:text-gray-400 mr-2"></i>
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
                        <i class="fa-solid fa-file-pdf text-gray-600 dark:text-gray-400 mr-2"></i>
                        Resume / Documents
                    </h2>
                    @php $resumeExt = strtolower(pathinfo($application->resume_original_name ?? '', PATHINFO_EXTENSION)); @endphp
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                        <i class="{{ $resumeExt === 'pdf' ? 'fa-solid fa-file-pdf text-red-500' : 'fa-solid fa-file-word text-blue-500' }} text-4xl mb-3"></i>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $application->resume_original_name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $resumeExt === 'pdf' ? 'PDF' : 'DOCX' }} Document</p>
                        <a href="{{ route('admin.recruitment.download', $application->id) }}"
                           class="inline-flex items-center px-4 py-2 mt-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fa-solid fa-download mr-2"></i>
                            Download Resume
                        </a>
                    </div>
                    @if($application->documents)
                        @foreach($application->documents as $doc)
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <i class="{{ str_ends_with($doc['original_name'] ?? '', '.pdf') ? 'fa-solid fa-file-pdf text-red-500' : 'fa-solid fa-file-word text-blue-500' }} text-xl"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $doc['original_name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $doc['label'] }}</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $doc['path']) }}" download
                                   class="p-2 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                {{-- Scenario #7: Communication Lock — show banner and disable all controls when withdrawn --}}
                @if($application->status === 'withdrawn')
                <div class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                    <i class="fa-solid fa-lock text-gray-400 text-3xl mb-3"></i>
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Application is Withdrawn</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">This application was withdrawn by the candidate. All edit and status change actions are disabled.</p>
                    @if($application->withdraw_reason)
                    <div class="mt-4 text-left bg-white dark:bg-gray-800 rounded-lg p-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Withdrawal Reason</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $application->withdraw_reason }}</p>
                        @if($application->withdraw_details)
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mt-3 mb-1">Additional Details</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $application->withdraw_details }}</p>
                        @endif
                    </div>
                    @endif
                    <div class="mt-4">
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-full {{ $application->status_badge_class }}">
                            {{ $application->status_label }}
                        </span>
                    </div>
                </div>
                @else
                <!-- Status Update -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-sliders text-gray-600 dark:text-gray-400 mr-2"></i>
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
                                <select name="status" id="statusSelect" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="reviewed" {{ $application->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                    <option value="interview_scheduled" {{ $application->status == 'interview_scheduled' ? 'selected' : '' }}>Interview Scheduled</option>
                                    <option value="hired" {{ $application->status == 'hired' ? 'selected' : '' }}>Hired</option>
                                    <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            {{-- Scenario #4: Interview date picker — shown when "Interview Scheduled" is selected --}}
                            <div id="interviewDateSection" style="display: {{ $application->status == 'interview_scheduled' ? 'block' : 'none' }};">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Interview Date & Time</label>
                                <input type="datetime-local" name="interview_date"
                                       value="{{ $application->interview_date ? $application->interview_date->format('Y-m-d\TH:i') : '' }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">An interview invitation email will be sent automatically.</p>
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
                @endif

                <!-- Timeline -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-clock-rotate-left text-gray-600 dark:text-gray-400 mr-2"></i>
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
                        <i class="fa-solid fa-bolt text-gray-600 dark:text-gray-400 mr-2"></i>
                        Quick Actions
                    </h2>
                    <div class="space-y-3">
                        @if($application->status !== 'withdrawn')
                        <a href="mailto:{{ $application->email }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fa-solid fa-envelope text-gray-600 dark:text-gray-400"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Send Email</span>
                        </a>
                        @else
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg opacity-50 cursor-not-allowed">
                            <i class="fa-solid fa-envelope-circle-xmark text-gray-400"></i>
                            <span class="text-sm text-gray-400">Email Disabled (Withdrawn)</span>
                        </div>
                        @endif
                        <a href="{{ route('admin.recruitment.download', $application->id) }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fa-solid fa-download text-gray-600 dark:text-gray-400"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Download Resume</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Scenario #4: Toggle interview date picker based on status selection --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('statusSelect');
            const interviewDateSection = document.getElementById('interviewDateSection');

            if (statusSelect && interviewDateSection) {
                statusSelect.addEventListener('change', function() {
                    interviewDateSection.style.display = this.value === 'interview_scheduled' ? 'block' : 'none';
                });
            }
        });
    </script>
</x-layouts.general-employer>
