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
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">
                    {{ $applications->where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Reviewed</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">
                    {{ $applications->where('status', 'reviewed')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Interview</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">
                    {{ $applications->where('status', 'interview_scheduled')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Hired</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">
                    {{ $applications->where('status', 'hired')->count() }}</p>
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
                            'interview_scheduled' =>
                                'Interview (' . $applications->where('status', 'interview_scheduled')->count() . ')',
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
                        ]"
                            id="recruitment-sort-dropdown" />
                    </div>

                    <!-- View Archived Button -->
                    <div class="w-full md:w-auto">
                        <a href="{{ route('admin.job-postings.archived') }}"
                            class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-blue-950 hover:bg-gray-200 hover:text-blue-600 dark:text-white dark:bg-gray-800 text-xs font-medium rounded-lg transition duration-150 ease-in-out whitespace-nowrap h-full">
                            <i class="fas fa-archive mr-2"></i>
                            View Archived
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Applications List -->
        <div class="flex flex-col gap-6 w-full rounded-lg p-4" x-data="applicationDrawerData()">

            <!-- Confirm Dialog for Applications -->
            <template x-teleport="body">
                <div x-show="showConfirm"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-[70] flex items-center justify-center p-4"
                     style="display: none;">
                    <div class="absolute inset-0 bg-black/30 dark:bg-black/50" @click="cancelConfirm()"></div>
                    <div x-show="showConfirm"
                         x-transition:enter="transition ease-out duration-300 delay-100"
                         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-90"
                         class="relative w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-600 shadow-2xl overflow-hidden p-3">
                        <div class="px-8 pt-10 pb-8 flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 border-2 border-amber-400 dark:border-amber-500 flex items-center justify-center mb-6">
                                <svg class="w-7 h-7 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2" x-text="confirmTitle"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed" x-text="confirmMessage"></p>
                        </div>
                        <div class="px-8 pb-8 flex gap-3">
                            <button @click="cancelConfirm()" type="button"
                                class="w-full px-6 py-3 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-semibold text-sm rounded-xl transition-all duration-200">
                                Cancel
                            </button>
                            <button @click="confirmAction()" type="button"
                                class="w-full px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold text-sm rounded-xl transition-all duration-200">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Bulk Actions Bar for Applications -->
            <div x-show="selectedAppIds.length > 0" x-transition
                class="flex flex-row justify-between items-center gap-3 px-4 py-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300"
                    x-text="selectedAppIds.length + ' selected'"></span>
                <div class = "flex flex-row gap-3">
                    <button @click="bulkDeleteApps()"
                        class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        <i class="fa-solid fa-trash mr-1"></i>Delete Selected
                    </button>
                    <button @click="deselectAllApps()"
                        class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Deselect All
                    </button>
                </div>



            </div>

            @if ($applications->count() > 0)
                <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-4 w-10">
                                    <input type="checkbox" @change="toggleAllApps($event)" :checked="allAppsSelected"
                                        class="appearance-none w-4 h-4 rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 checked:bg-blue-600 checked:border-blue-600 checked:bg-[url('data:image/svg+xml,%3Csvg%20viewBox%3D%220%200%2016%2016%22%20fill%3D%22white%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12.207%204.793a1%201%200%20010%201.414l-5%205a1%201%200%2001-1.414%200l-2-2a1%201%200%20011.414-1.414L6.5%209.086l4.293-4.293a1%201%200%20011.414%200z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-colors">
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Applicant</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Position</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Resume</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Applied</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $application)
                                <tr class="even:bg-gray-50 dark:even:bg-gray-800/50"
                                    data-email="{{ strtolower($application->email) }}"
                                    data-job="{{ strtolower($application->job_title) }}"
                                    data-status="{{ $application->status }}"
                                    data-date="{{ $application->created_at->timestamp }}">
                                    <!-- Checkbox -->
                                    <td class="px-4 py-4 w-10">
                                        <input type="checkbox" value="{{ $application->id }}"
                                            @change="toggleApp({{ $application->id }})"
                                            :checked="selectedAppIds.includes({{ $application->id }})"
                                            class="appearance-none w-4 h-4 rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 checked:bg-blue-600 checked:border-blue-600 checked:bg-[url('data:image/svg+xml,%3Csvg%20viewBox%3D%220%200%2016%2016%22%20fill%3D%22white%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12.207%204.793a1%201%200%20010%201.414l-5%205a1%201%200%2001-1.414%200l-2-2a1%201%200%20011.414-1.414L6.5%209.086l4.293-4.293a1%201%200%20011.414%200z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-colors">
                                    </td>

                                    <!-- Applicant -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $application->email }}</div>
                                        @if ($application->alternative_email)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">Alt:
                                                {{ $application->alternative_email }}</div>
                                        @endif
                                    </td>

                                    <!-- Position -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-200">
                                            {{ $application->job_title }}</div>
                                        @if ($application->job_type)
                                            <div class="text-xs text-blue-600 dark:text-blue-400">
                                                {{ ucfirst(str_replace('-', ' ', $application->job_type)) }}</div>
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
                                        <div class="text-sm text-gray-900 dark:text-gray-200">
                                            {{ $application->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $application->created_at->format('h:i A') }}</div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($application->status)
                                            @case('pending')
                                                <span
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400">Pending</span>
                                            @break

                                            @case('reviewed')
                                                <span
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">Reviewed</span>
                                            @break

                                            @case('interview_scheduled')
                                                <span
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400">Interview</span>
                                            @break

                                            @case('hired')
                                                <span
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400">Hired</span>
                                            @break

                                            @case('rejected')
                                                <span
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400">Rejected</span>
                                            @break

                                            @default
                                                <span
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-900/20 dark:text-gray-400">{{ ucfirst($application->status) }}</span>
                                        @endswitch
                                    </td>

                                    <!-- Action -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button @click="openDrawer({{ $application->id }})"
                                            class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                            <i class="fa-regular fa-eye mr-1 text-xs"></i> View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    @if ($applications->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $applications->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div
                    class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                    <i class="fa-solid fa-inbox text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No job applications found</p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Applications submitted from the
                        recruitment page will appear here</p>
                </div>
            @endif

            <!-- Application Details Slide-in Drawer -->
            <div x-show="showDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden">
                <!-- Backdrop -->
                <div x-show="showDrawer" x-transition:enter="transition-opacity ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="closeDrawer()"
                    class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

                <!-- Drawer Panel -->
                <div class="fixed inset-y-0 right-0 flex max-w-full">
                    <div x-show="showDrawer" x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-200"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" @click.stop
                        class="relative w-screen max-w-md sm:max-w-lg">

                        <div
                            class="h-full flex flex-col bg-white dark:bg-slate-800 shadow-2xl border-l border-gray-200 dark:border-slate-700">
                            <!-- Header -->
                            <div
                                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-slate-800/50">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Application Details
                                    </h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-show="selectedApp"
                                        x-text="'#' + selectedApp?.id"></p>
                                </div>
                                <button type="button" @click="closeDrawer()"
                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Body -->
                            <template x-if="selectedApp">
                                <div class="flex-1 overflow-y-auto p-6">

                                    <!-- Status Badge -->
                                    <div class="flex items-center gap-2 mb-6">
                                        <span
                                            class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                        <span class="px-3 py-1 text-xs rounded-full font-semibold"
                                            :class="getStatusClass(selectedApp.status)"
                                            x-text="getStatusLabel(selectedApp.status)"></span>
                                    </div>

                                    <!-- Applicant Information -->
                                    <div class="mb-5">
                                        <h4
                                            class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-user-circle text-gray-600 dark:text-gray-400"></i>
                                            Applicant Information
                                        </h4>
                                        <div
                                            class="space-y-3 text-sm py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Email</span>
                                                <span class="font-medium text-gray-900 dark:text-white"
                                                    x-text="selectedApp.email"></span>
                                            </div>
                                            <div class="flex justify-between items-center"
                                                x-show="selectedApp.alternative_email">
                                                <span class="text-gray-500 dark:text-gray-400">Alt. Email</span>
                                                <span class="font-medium text-gray-900 dark:text-white"
                                                    x-text="selectedApp.alternative_email"></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Applied On</span>
                                                <span class="font-medium text-gray-900 dark:text-white"
                                                    x-text="selectedApp.created_at"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Position Applied For -->
                                    <div class="mb-5">
                                        <h4
                                            class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-briefcase text-gray-600 dark:text-gray-400"></i>
                                            Position Applied For
                                        </h4>
                                        <div
                                            class="space-y-3 text-sm py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Job Title</span>
                                                <span class="font-medium text-gray-900 dark:text-white"
                                                    x-text="selectedApp.job_title"></span>
                                            </div>
                                            <div class="flex justify-between items-center"
                                                x-show="selectedApp.job_type">
                                                <span class="text-gray-500 dark:text-gray-400">Employment Type</span>
                                                <span
                                                    class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400"
                                                    x-text="selectedApp.job_type_label"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Resume -->
                                    <div class="mb-5">
                                        <h4
                                            class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-file-pdf text-gray-600 dark:text-gray-400"></i>
                                            Resume / Documents
                                        </h4>
                                        <div
                                            class="border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <i class="fa-solid fa-file-pdf text-gray-500 text-xl"></i>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white"
                                                        x-text="selectedApp.resume_name"></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF Document
                                                    </p>
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
                                        <h4
                                            class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-sliders text-gray-600 dark:text-gray-400"></i>
                                            Update Status
                                        </h4>
                                        <div class="space-y-3 py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Change
                                                    Status</label>
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
                                                <label
                                                    class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Admin
                                                    Notes</label>
                                                <textarea x-model="drawerNotes" rows="3"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                                    placeholder="Add notes about this applicant..."></textarea>
                                            </div>
                                            <button @click="updateStatus()" :disabled="isUpdating"
                                                class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                                                <span x-show="!isUpdating"><i class="fa-solid fa-save mr-2"></i>Update
                                                    Status</span>
                                                <span x-show="isUpdating">Updating...</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Timeline -->
                                    <div class="mb-5">
                                        <h4
                                            class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i
                                                class="fa-solid fa-clock-rotate-left text-gray-600 dark:text-gray-400"></i>
                                            Timeline
                                        </h4>
                                        <div class="space-y-3 py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <div class="flex items-start gap-3">
                                                <div class="w-2 h-2 mt-1.5 bg-blue-600 rounded-full flex-shrink-0">
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        Application Submitted</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400"
                                                        x-text="selectedApp.created_at"></p>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-3" x-show="selectedApp.reviewed_at">
                                                <div class="w-2 h-2 mt-1.5 bg-green-600 rounded-full flex-shrink-0">
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Last
                                                        Updated</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400"
                                                        x-text="selectedApp.reviewed_at"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div>
                                        <h4
                                            class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-bolt text-gray-600 dark:text-gray-400"></i>
                                            Quick Actions
                                        </h4>
                                        <div class="space-y-2">
                                            <a :href="'mailto:' + selectedApp.email"
                                                class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                <i class="fa-solid fa-envelope text-gray-600 dark:text-gray-400"></i>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">Send
                                                    Email</span>
                                            </a>
                                            <a :href="selectedApp.download_url"
                                                class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                <i class="fa-solid fa-download text-gray-600 dark:text-gray-400"></i>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">Download
                                                    Resume</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Footer -->
                            <div
                                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
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
        <div class="flex flex-col gap-6 w-full rounded-lg p-4" x-data="jobPostingsData()">

            <!-- Confirm Dialog for Job Postings -->
            <template x-teleport="body">
                <div x-show="showConfirm"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-[70] flex items-center justify-center p-4"
                     style="display: none;">
                    <div class="absolute inset-0 bg-black/30 dark:bg-black/50" @click="cancelConfirm()"></div>
                    <div x-show="showConfirm"
                         x-transition:enter="transition ease-out duration-300 delay-100"
                         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-90"
                         class="relative w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-600 shadow-2xl overflow-hidden p-3">
                        <div class="px-8 pt-10 pb-8 flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 border-2 border-amber-400 dark:border-amber-500 flex items-center justify-center mb-6">
                                <svg class="w-7 h-7 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2" x-text="confirmTitle"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed" x-text="confirmMessage"></p>
                        </div>
                        <div class="px-8 pb-8 flex gap-3">
                            <button @click="cancelConfirm()" type="button"
                                class="w-full px-6 py-3 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-semibold text-sm rounded-xl transition-all duration-200">
                                Cancel
                            </button>
                            <button @click="confirmAction()" type="button"
                                class="w-full px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold text-sm rounded-xl transition-all duration-200">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <div class="flex items-center justify-between">
                <x-labelwithvalue label="Job Postings" count="" />
                <button @click="openModal()"
                    class="px-4 py-2 text-blue-600 rounded-lg hover:bg-blue-700 hover:text-white hover:dark:bg-gray-800 hover:dark:text-white transition-colors text-sm font-medium">
                    <i class="fa-solid fa-plus mr-2"></i>Add Job Posting
                </button>
            </div>

            <!-- Bulk Actions Bar for Job Postings -->
            <div x-show="selectedJobIds.length > 0" x-transition
                class="flex flex-row justify-between items-center gap-3 px-4 py-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300"
                    x-text="selectedJobIds.length + ' selected'"></span>
                <div class="flex flex-row gap-3">
                    <button @click="bulkDeleteJobs()"
                        class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        <i class="fa-solid fa-trash mr-1"></i>Delete Selected
                    </button>
                    <button @click="deselectAllJobs()"
                        class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Deselect All
                    </button>
                </div>
            </div>

            <!-- Job Postings Table -->
            <div x-show="jobPostings.length > 0"
                class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-4 w-10">
                                <input type="checkbox" @change="toggleAllJobs($event)" :checked="allJobsSelected"
                                    class="appearance-none w-4 h-4 rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 checked:bg-blue-600 checked:border-blue-600 checked:bg-[url('data:image/svg+xml,%3Csvg%20viewBox%3D%220%200%2016%2016%22%20fill%3D%22white%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12.207%204.793a1%201%200%20010%201.414l-5%205a1%201%200%2001-1.414%200l-2-2a1%201%200%20011.414-1.414L6.5%209.086l4.293-4.293a1%201%200%20011.414%200z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-colors">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Job
                                Title</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Type
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Location</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Salary</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Applicants</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(job, index) in jobPostings" :key="index">
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800/50">
                                <!-- Checkbox -->
                                <td class="px-4 py-4 w-10">
                                    <input type="checkbox" :value="job.id" @change="toggleJob(job.id)"
                                        :checked="selectedJobIds.includes(job.id)"
                                        class="appearance-none w-4 h-4 rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 checked:bg-blue-600 checked:border-blue-600 checked:bg-[url('data:image/svg+xml,%3Csvg%20viewBox%3D%220%200%2016%2016%22%20fill%3D%22white%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12.207%204.793a1%201%200%20010%201.414l-5%205a1%201%200%2001-1.414%200l-2-2a1%201%200%20011.414-1.414L6.5%209.086l4.293-4.293a1%201%200%20011.414%200z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-colors">
                                </td>

                                <!-- Job Title + Icon -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                            :class="getIconBgClass(job.iconColor)">
                                            <i class="fas text-sm"
                                                :class="[job.icon, getIconTextClass(job.iconColor)]"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white"
                                                x-text="job.title"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 max-w-[200px] truncate"
                                                x-text="job.description"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="getTypeBadgeClass(job.type)" x-text="job.typeBadge"></span>
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
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white"
                                        x-text="job.salary"></div>
                                </td>

                                <!-- Applicants -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200"
                                        x-text="(job.applicantCount || 0) + ' ' + ((job.applicantCount === 1) ? 'applicant' : 'applicants')">
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="getJobStatusClass(job.status)"
                                        x-text="getJobStatusLabel(job.status)"></span>
                                </td>

                                <!-- Action -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="editJob(index)"
                                            class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                            title="Edit">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <button @click="archiveJob(index)"
                                            class="text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors"
                                            title="Archive">
                                            <i class="fa-solid fa-archive text-sm"></i>
                                        </button>
                                        <button @click="deleteJob(index)"
                                            class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                            title="Delete">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

            </div>

            <!-- Empty State -->
            <template x-if="jobPostings.length === 0">
                <div
                    class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                    <i class="fa-solid fa-briefcase text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No job postings yet</p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Click "Add Job Posting" to create your
                        first job listing</p>
                </div>
            </template>

            <!-- Job Posting Modal -->
            <div x-show="showModal" x-cloak @click="closeModal()"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/80 p-4"
                style="display: none;">
                <div @click.stop
                    class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700"
                    x-show="showModal" x-transition>

                    <!-- Modal Header -->
                    <div
                        class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white"
                            x-text="editingIndex !== null ? 'Edit Job Posting' : 'Create Job Posting'"></h3>
                        <button @click="closeModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Title
                                *</label>
                            <input type="text" x-model="formData.title" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="e.g., Deep Cleaning Specialist">
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description
                                *</label>
                            <textarea x-model="formData.description" rows="3" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="Atleast 180 characters"></textarea>
                        </div>

                        <!-- Two Column Layout -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- State -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State / Region *</label>
                                <div class="relative" x-data="{ stateOpen: false, stateSearch: '' }">
                                    <button type="button" @click="stateOpen = !stateOpen; $nextTick(() => { if(stateOpen) $refs.stateSearchInput.focus(); })"
                                        class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm bg-white dark:bg-gray-700">
                                        <span x-text="formData.state || 'Select State'" :class="!formData.state && 'text-gray-400'"></span>
                                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="stateOpen && 'rotate-180'"></i>
                                    </button>
                                    <div x-show="stateOpen" @click.away="stateOpen = false" x-transition
                                        class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-hidden flex flex-col">
                                        <div class="p-2 border-b border-gray-200 dark:border-gray-600">
                                            <input type="text" x-model="stateSearch" x-ref="stateSearchInput" placeholder="Search..."
                                                class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-800 dark:text-white">
                                        </div>
                                        <div class="overflow-y-auto max-h-48">
                                            <template x-for="s in stateOptions.filter(s => s.name.toLowerCase().includes(stateSearch.toLowerCase()))" :key="s.iso2">
                                                <button type="button"
                                                    @click="formData.state = s.name; stateOpen = false; stateSearch = ''; onStateChange();"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    :class="formData.state === s.name ? 'bg-blue-50 dark:bg-blue-900/20 font-medium' : 'text-gray-900 dark:text-white'">
                                                    <span x-text="s.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- City -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City *</label>
                                <div class="relative" x-data="{ cityOpen: false, citySearch: '' }">
                                    <button type="button" @click="if(cityOptions.length > 0) { cityOpen = !cityOpen; $nextTick(() => { if(cityOpen) $refs.citySearchInput.focus(); }); }"
                                        class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm bg-white dark:bg-gray-700"
                                        :class="cityOptions.length === 0 && 'opacity-50 cursor-not-allowed'">
                                        <span x-text="formData.city || 'Select City'" :class="!formData.city && 'text-gray-400'"></span>
                                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="cityOpen && 'rotate-180'"></i>
                                    </button>
                                    <div x-show="cityOpen" @click.away="cityOpen = false" x-transition
                                        class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-hidden flex flex-col">
                                        <div class="p-2 border-b border-gray-200 dark:border-gray-600">
                                            <input type="text" x-model="citySearch" x-ref="citySearchInput" placeholder="Search..."
                                                class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-800 dark:text-white">
                                        </div>
                                        <div class="overflow-y-auto max-h-48">
                                            <template x-for="c in cityOptions.filter(c => c.name.toLowerCase().includes(citySearch.toLowerCase()))" :key="c.name">
                                                <button type="button"
                                                    @click="formData.city = c.name; cityOpen = false; citySearch = '';"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    :class="formData.city === c.name ? 'bg-blue-50 dark:bg-blue-900/20 font-medium' : 'text-gray-900 dark:text-white'">
                                                    <span x-text="c.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Salary -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Salary
                                    *</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-500 dark:text-gray-400">&euro;</span>
                                    <input type="text" x-model="formData.salary" required
                                        class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                        placeholder="e.g., 30 - 40/hr">
                                </div>
                            </div>

                            <!-- Job Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Type *</label>
                                <div class="relative" x-data="{ typeOpen: false }">
                                    <button type="button" @click="typeOpen = !typeOpen"
                                        class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm bg-white dark:bg-gray-700">
                                        <span x-text="{'full-time':'Full-time','part-time':'Part-time','remote':'Remote'}[formData.type] || 'Select Type'"></span>
                                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="typeOpen && 'rotate-180'"></i>
                                    </button>
                                    <div x-show="typeOpen" @click.away="typeOpen = false" x-transition
                                        class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg overflow-hidden">
                                        <template x-for="opt in [{v:'full-time',l:'Full-time'},{v:'part-time',l:'Part-time'},{v:'remote',l:'Remote'}]" :key="opt.v">
                                            <button type="button"
                                                @click="formData.type = opt.v; updateTypeBadge(); typeOpen = false;"
                                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                :class="formData.type === opt.v ? 'bg-blue-50 dark:bg-blue-900/20 font-medium' : 'text-gray-900 dark:text-white'">
                                                <span x-text="opt.l"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Job Category -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Category *</label>
                                <div class="relative" x-data="{ categoryOpen: false }">
                                    <button type="button" @click="categoryOpen = !categoryOpen"
                                        class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm bg-white dark:bg-gray-700">
                                        <span class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded flex items-center justify-center flex-shrink-0"
                                                :class="getIconBgClass(formData.iconColor)">
                                                <i class="fas text-xs" :class="[formData.icon, getIconTextClass(formData.iconColor)]"></i>
                                            </span>
                                            <span x-text="getCategoryLabel(formData.category)"></span>
                                        </span>
                                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="categoryOpen && 'rotate-180'"></i>
                                    </button>
                                    <div x-show="categoryOpen" @click.away="categoryOpen = false" x-transition
                                        class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                        <template x-for="cat in jobCategories" :key="cat.value">
                                            <button type="button"
                                                @click="formData.category = cat.value; formData.icon = cat.icon; formData.iconColor = cat.color; categoryOpen = false"
                                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-left"
                                                :class="formData.category === cat.value ? 'bg-blue-50 dark:bg-blue-900/20' : ''">
                                                <span class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                                                    :class="getIconBgClass(cat.color)">
                                                    <i class="fas text-xs" :class="[cat.icon, getIconTextClass(cat.color)]"></i>
                                                </span>
                                                <div class="flex flex-col">
                                                    <span class="text-gray-900 dark:text-white font-medium" x-text="cat.label"></span>
                                                    <span class="text-xs text-gray-400 dark:text-gray-500 leading-tight" x-text="cat.description"></span>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Required Skills -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Required Skills</label>
                            <div class="flex flex-wrap gap-2 mb-3" x-show="formData.requiredSkills.length > 0">
                                <template x-for="(skill, idx) in formData.requiredSkills" :key="'skill-'+idx">
                                    <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1.5 rounded-full border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium">
                                        <span x-text="skill"></span>
                                        <button type="button" @click="formData.requiredSkills.splice(idx, 1)"
                                            class="w-4 h-4 inline-flex items-center justify-center rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 text-blue-400 hover:text-blue-600 dark:hover:text-blue-200 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </span>
                                </template>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" x-ref="skillInput"
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                    placeholder="Type a skill and press Enter..."
                                    @keydown.enter.prevent="if($refs.skillInput.value.trim()) { formData.requiredSkills.push($refs.skillInput.value.trim()); $refs.skillInput.value = ''; }">
                                <button type="button"
                                    @click="if($refs.skillInput.value.trim()) { formData.requiredSkills.push($refs.skillInput.value.trim()); $refs.skillInput.value = ''; }"
                                    class="px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg font-medium">
                                    <i class="fa-solid fa-plus mr-1"></i>Add
                                </button>
                            </div>
                        </div>

                        <!-- Required Documents -->
                        <div x-data="{ docTypeOpen: false, docInput: '', selectedDocType: 'any' }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Required Documents</label>
                            <div class="flex flex-wrap gap-2 mb-3" x-show="formData.requiredDocs.length > 0">
                                <template x-for="(doc, idx) in formData.requiredDocs" :key="'doc-'+idx">
                                    <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1.5 rounded-full border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-xs font-medium">
                                        <i class="fa-solid fa-file-lines text-[10px] text-amber-400 dark:text-amber-500"></i>
                                        <span x-text="typeof doc === 'object' ? doc.name + (doc.fileType && doc.fileType !== 'any' ? ' (' + doc.fileType.toUpperCase() + ')' : '') : doc"></span>
                                        <button type="button" @click="formData.requiredDocs.splice(idx, 1)"
                                            class="w-4 h-4 inline-flex items-center justify-center rounded-full hover:bg-amber-200 dark:hover:bg-amber-800 text-amber-400 hover:text-amber-600 dark:hover:text-amber-200 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </span>
                                </template>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" x-model="docInput"
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                    placeholder="e.g., Resume, Cover Letter, Certificate..."
                                    @keydown.enter.prevent="if(docInput.trim()) { formData.requiredDocs.push({name: docInput.trim(), fileType: selectedDocType}); docInput = ''; selectedDocType = 'any'; }">
                                <!-- File Type Dropdown -->
                                <div class="relative">
                                    <button type="button" @click="docTypeOpen = !docTypeOpen"
                                        class="h-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 flex items-center gap-2 min-w-[100px] justify-between transition-colors">
                                        <span x-text="selectedDocType === 'any' ? 'Any file' : selectedDocType.toUpperCase()" class="truncate"></span>
                                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="docTypeOpen && 'rotate-180'"></i>
                                    </button>
                                    <div x-show="docTypeOpen" @click.away="docTypeOpen = false"
                                        x-transition:enter="transition ease-out duration-150"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute right-0 z-50 mt-1 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg overflow-hidden"
                                        style="display: none;">
                                        <template x-for="ft in [{v:'any',l:'Any file'},{v:'pdf',l:'PDF'},{v:'doc',l:'DOC / DOCX'},{v:'jpg',l:'JPG / JPEG'},{v:'png',l:'PNG'},{v:'xls',l:'XLS / XLSX'},{v:'csv',l:'CSV'},{v:'txt',l:'TXT'}]" :key="ft.v">
                                            <button type="button"
                                                @click="selectedDocType = ft.v; docTypeOpen = false"
                                                :class="selectedDocType === ft.v ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                                class="w-full text-left px-4 py-2 text-sm transition-colors">
                                                <span x-text="ft.l"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <button type="button"
                                    @click="if(docInput.trim()) { formData.requiredDocs.push({name: docInput.trim(), fileType: selectedDocType}); docInput = ''; selectedDocType = 'any'; }"
                                    class="px-4 py-2 text-sm text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-900/20 rounded-lg font-medium">
                                    <i class="fa-solid fa-plus mr-1"></i>Add
                                </button>
                            </div>
                        </div>

                        <!-- Benefits -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Benefits</label>
                            <div class="flex flex-wrap gap-2 mb-3" x-show="formData.benefits.length > 0">
                                <template x-for="(benefit, idx) in formData.benefits" :key="'benefit-'+idx">
                                    <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1.5 rounded-full border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-medium">
                                        <span x-text="benefit"></span>
                                        <button type="button" @click="formData.benefits.splice(idx, 1)"
                                            class="w-4 h-4 inline-flex items-center justify-center rounded-full hover:bg-emerald-200 dark:hover:bg-emerald-800 text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-200 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </span>
                                </template>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" x-ref="benefitInput"
                                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                    placeholder="e.g., Health Insurance, Paid Time Off..."
                                    @keydown.enter.prevent="if($refs.benefitInput.value.trim()) { formData.benefits.push($refs.benefitInput.value.trim()); $refs.benefitInput.value = ''; }">
                                <button type="button"
                                    @click="if($refs.benefitInput.value.trim()) { formData.benefits.push($refs.benefitInput.value.trim()); $refs.benefitInput.value = ''; }"
                                    class="px-4 py-2 text-sm text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20 rounded-lg font-medium">
                                    <i class="fa-solid fa-plus mr-1"></i>Add
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
                        <template x-if="editingIndex === null">
                            <button @click="saveAsDraft()"
                                class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-medium">
                                <i class="fa-solid fa-file-pen mr-2"></i>Save as Draft
                            </button>
                        </template>
                        <template x-if="editingIndex !== null">
                            <button @click="closeModal()"
                                class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-medium">
                                Cancel
                            </button>
                        </template>
                        <button @click="saveJob()"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            <i class="fa-solid fa-save mr-2"></i>
                            <span x-text="editingIndex !== null ? 'Update' : 'Post'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Success Dialog -->
            <template x-teleport="body">
                <x-employer-components.success-dialog title="Success" message="" buttonText="Back to Recruitment" />
            </template>

        </div>

    </section>

    @php
        $applicationsData = $applications->getCollection()->map(function ($app) {
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
                selectedAppIds: [],
                showConfirm: false,
                confirmTitle: '',
                confirmMessage: '',
                pendingConfirmAction: null,
                get allAppsSelected() {
                    return applications.length > 0 && this.selectedAppIds.length === applications.length;
                },

                toggleApp(id) {
                    const idx = this.selectedAppIds.indexOf(id);
                    if (idx > -1) {
                        this.selectedAppIds.splice(idx, 1);
                    } else {
                        this.selectedAppIds.push(id);
                    }
                },

                toggleAllApps(event) {
                    if (event.target.checked) {
                        this.selectedAppIds = applications.map(a => a.id);
                    } else {
                        this.selectedAppIds = [];
                    }
                },

                deselectAllApps() {
                    this.selectedAppIds = [];
                },

                bulkDeleteApps() {
                    this.confirmTitle = 'Delete Applications';
                    this.confirmMessage = `Are you sure you want to delete ${this.selectedAppIds.length} application(s)? This action cannot be undone.`;
                    this.pendingConfirmAction = 'bulkDeleteApps';
                    this.showConfirm = true;
                },

                async doBulkDeleteApps() {
                    try {
                        const response = await fetch('/admin/recruitment/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ids: this.selectedAppIds
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            // Remove deleted rows from the DOM immediately
                            this.selectedAppIds.forEach(id => {
                                const checkbox = document.querySelector(`input[type="checkbox"][value="${id}"]`);
                                if (checkbox) {
                                    const row = checkbox.closest('tr');
                                    if (row) row.remove();
                                }
                            });
                            this.selectedAppIds = [];
                            window.showSuccessDialog('Successfully Deleted', data.message, 'Back to Recruitment', window.location.href);
                        } else {
                            window.showErrorDialog('Delete Failed', data.message || 'Failed to delete applications.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.showErrorDialog('Delete Failed', 'An error occurred while deleting applications.');
                    }
                },

                confirmAction() {
                    this.showConfirm = false;
                    if (this.pendingConfirmAction === 'bulkDeleteApps') {
                        this.doBulkDeleteApps();
                    }
                    this.pendingConfirmAction = null;
                },

                cancelConfirm() {
                    this.showConfirm = false;
                    this.pendingConfirmAction = null;
                },

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
                                month: 'short',
                                day: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                            // Reload to reflect changes in table
                            window.location.reload();
                        } else {
                            window.showErrorDialog('Update Failed', 'Failed to update status. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.showErrorDialog('Update Failed', 'An error occurred while updating the status.');
                    } finally {
                        this.isUpdating = false;
                    }
                }
            };
        }

        function jobPostingsData() {
            // Map database records to JS format
            const applicantCounts = @json($applicantCounts ?? new \stdClass());
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
                status: job.status || 'published',
                requiredSkills: job.required_skills || [],
                requiredDocs: job.required_docs || [],
                benefits: job.benefits || [],
                applicantCount: applicantCounts[job.title] || 0
            }));

            return {
                showModal: false,
                showSuccess: false,
                successTitle: '',
                successMessage: '',
                successButtonText: 'Back to Recruitment',
                successRedirectUrl: '',
                editingIndex: null,
                isSubmitting: false,
                selectedJobIds: [],
                showConfirm: false,
                confirmTitle: '',
                confirmMessage: '',
                pendingConfirmAction: null,
                pendingDeleteIndex: null,
                get allJobsSelected() {
                    return this.jobPostings.length > 0 && this.selectedJobIds.length === this.jobPostings.length;
                },
                stateOptions: [],
                cityOptions: [],
                cscApiKey: @json($cscApiKey),
                cscBaseUrl: 'https://api.countrystatecity.in/v1',
                finlandIso2: 'FI',
                jobCategories: [
                    { value: 'cleaning', label: 'Cleaning', description: 'General and deep cleaning roles', icon: 'fa-broom', color: 'green' },
                    { value: 'management', label: 'Management', description: 'Supervisory and leadership positions', icon: 'fa-user-tie', color: 'blue' },
                    { value: 'logistics', label: 'Logistics', description: 'Supply chain and equipment handling', icon: 'fa-dolly', color: 'orange' },
                    { value: 'quality-assurance', label: 'Quality Assurance', description: 'Inspection and compliance checks', icon: 'fa-clipboard-check', color: 'purple' },
                    { value: 'customer-service', label: 'Customer Service', description: 'Client communication and support', icon: 'fa-headset', color: 'blue' },
                    { value: 'sanitation', label: 'Sanitation', description: 'Hygiene and disinfection specialists', icon: 'fa-spray-can', color: 'green' },
                    { value: 'team-lead', label: 'Team Lead', description: 'On-site crew coordination', icon: 'fa-users', color: 'purple' },
                    { value: 'operations', label: 'Operations', description: 'Day-to-day business operations', icon: 'fa-briefcase', color: 'orange' },
                    { value: 'maintenance', label: 'Maintenance', description: 'Facility and equipment upkeep', icon: 'fa-wrench', color: 'red' },
                    { value: 'administration', label: 'Administration', description: 'Office and administrative tasks', icon: 'fa-folder-open', color: 'blue' },
                ],
                formData: {
                    id: null,
                    title: '',
                    description: '',
                    state: '',
                    city: '',
                    salary: '',
                    type: 'full-time',
                    typeBadge: 'Full-time Employee',
                    category: 'cleaning',
                    icon: 'fa-broom',
                    iconColor: 'green',
                    is_active: true,
                    requiredSkills: [],
                    requiredDocs: [],
                    benefits: []
                },
                jobPostings: dbJobPostings,

                toggleJob(id) {
                    const idx = this.selectedJobIds.indexOf(id);
                    if (idx > -1) {
                        this.selectedJobIds.splice(idx, 1);
                    } else {
                        this.selectedJobIds.push(id);
                    }
                },

                toggleAllJobs(event) {
                    if (event.target.checked) {
                        this.selectedJobIds = this.jobPostings.map(j => j.id);
                    } else {
                        this.selectedJobIds = [];
                    }
                },

                deselectAllJobs() {
                    this.selectedJobIds = [];
                },

                bulkDeleteJobs() {
                    this.confirmTitle = 'Delete Job Postings';
                    this.confirmMessage = `Are you sure you want to delete ${this.selectedJobIds.length} job posting(s)? This action cannot be undone.`;
                    this.pendingConfirmAction = 'bulkDeleteJobs';
                    this.showConfirm = true;
                },

                async doBulkDeleteJobs() {
                    try {
                        const response = await fetch('/admin/job-postings/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ids: this.selectedJobIds
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.jobPostings = this.jobPostings.filter(j => !this.selectedJobIds.includes(j.id));
                            this.successTitle = 'Job Postings Deleted';
                            this.successMessage = data.message;
                            this.successRedirectUrl = window.location.href;
                            this.showSuccess = true;
                            this.selectedJobIds = [];
                        } else {
                            window.showErrorDialog('Delete Failed', data.message || 'Failed to delete job postings.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.showErrorDialog('Delete Failed', 'An error occurred while deleting job postings.');
                    }
                },

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
                        state: '',
                        city: '',
                        salary: '',
                        type: 'full-time',
                        typeBadge: 'Full-time Employee',
                        category: 'cleaning',
                        icon: 'fa-broom',
                        iconColor: 'green',
                        is_active: true,
                        requiredSkills: [],
                        requiredDocs: [],
                        benefits: []
                    };
                    this.cityOptions = [];
                },

                editJob(index) {
                    this.editingIndex = index;
                    const job = this.jobPostings[index];
                    // Parse "City, State" from location
                    const parts = (job.location || '').split(',').map(p => p.trim());
                    const city = parts[0] || '';
                    const state = parts[1] || '';
                    // Find category by matching icon
                    const matchedCat = this.jobCategories.find(c => c.icon === job.icon) || this.jobCategories[0];
                    this.formData = {
                        id: job.id,
                        title: job.title,
                        description: job.description,
                        state: state,
                        city: city,
                        salary: job.salary,
                        type: job.type,
                        typeBadge: job.typeBadge,
                        category: matchedCat.value,
                        icon: job.icon,
                        iconColor: job.iconColor,
                        is_active: job.is_active,
                        requiredSkills: job.requiredSkills?.length ? [...job.requiredSkills] : [],
                        requiredDocs: job.requiredDocs?.length ? job.requiredDocs.map(d => typeof d === 'string' ? {name: d, fileType: 'any'} : {...d}) : [],
                        benefits: job.benefits?.length ? [...job.benefits] : []
                    };
                    // Load cities for the selected state
                    if (state) {
                        const stateObj = this.stateOptions.find(s => s.name === state);
                        if (stateObj) {
                            this.loadCities(stateObj.iso2);
                        }
                    }
                    this.openModal();
                },

                archiveJob(index) {
                    this.confirmTitle = 'Archive Job Posting';
                    this.confirmMessage = 'Are you sure you want to archive this job posting? It will no longer be visible to applicants.';
                    this.pendingConfirmAction = 'archiveJob';
                    this.pendingDeleteIndex = index;
                    this.showConfirm = true;
                },

                async doArchiveJob(index) {
                    const job = this.jobPostings[index];
                    if (!job.id) return;

                    try {
                        const response = await fetch(`/admin/job-postings/${job.id}/archive`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.jobPostings.splice(index, 1);
                            this.successTitle = 'Job Posting Archived';
                            this.successMessage = 'The job posting has been archived successfully.';
                            this.successRedirectUrl = window.location.href;
                            this.showSuccess = true;
                        } else {
                            window.showErrorDialog('Archive Failed', data.message || 'Failed to archive job posting.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.showErrorDialog('Archive Failed', 'An error occurred while archiving the job posting.');
                    }
                },

                deleteJob(index) {
                    this.confirmTitle = 'Delete Job Posting';
                    this.confirmMessage = 'Are you sure you want to delete this job posting? This action cannot be undone.';
                    this.pendingConfirmAction = 'deleteJob';
                    this.pendingDeleteIndex = index;
                    this.showConfirm = true;
                },

                async doDeleteJob(index) {
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
                            this.successTitle = 'Job Posting Deleted';
                            this.successMessage = 'The job posting has been removed successfully.';
                            this.successRedirectUrl = window.location.href;
                            this.showSuccess = true;
                        } else {
                            window.showErrorDialog('Delete Failed', data.message || 'Failed to delete job posting.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.showErrorDialog('Delete Failed', 'An error occurred while deleting the job posting.');
                    }
                },

                confirmAction() {
                    this.showConfirm = false;
                    if (this.pendingConfirmAction === 'bulkDeleteJobs') {
                        this.doBulkDeleteJobs();
                    } else if (this.pendingConfirmAction === 'deleteJob') {
                        this.doDeleteJob(this.pendingDeleteIndex);
                    } else if (this.pendingConfirmAction === 'archiveJob') {
                        this.doArchiveJob(this.pendingDeleteIndex);
                    }
                    this.pendingConfirmAction = null;
                    this.pendingDeleteIndex = null;
                },

                cancelConfirm() {
                    this.showConfirm = false;
                    this.pendingConfirmAction = null;
                    this.pendingDeleteIndex = null;
                },

                async cscFetch(endpoint) {
                    try {
                        const response = await fetch(`${this.cscBaseUrl}${endpoint}`, {
                            headers: { 'X-CSCAPI-KEY': this.cscApiKey }
                        });
                        if (!response.ok) return [];
                        return await response.json();
                    } catch (error) {
                        console.error('CSC API error:', error);
                        return [];
                    }
                },

                async loadStates() {
                    const states = await this.cscFetch(`/countries/${this.finlandIso2}/states`);
                    this.stateOptions = states.sort((a, b) => a.name.localeCompare(b.name));
                },

                async onStateChange() {
                    this.formData.city = '';
                    this.cityOptions = [];
                    if (!this.formData.state) return;
                    const stateObj = this.stateOptions.find(s => s.name === this.formData.state);
                    if (stateObj) {
                        await this.loadCities(stateObj.iso2);
                    }
                },

                async loadCities(stateIso2) {
                    const cities = await this.cscFetch(`/countries/${this.finlandIso2}/states/${stateIso2}/cities`);
                    this.cityOptions = cities.sort((a, b) => a.name.localeCompare(b.name));
                },

                init() {
                    this.loadStates();
                },

                async saveJob() {
                    if (!this.formData.title || !this.formData.description || !this.formData.state || !this.formData.city || !this.formData
                        .salary) {
                        window.showErrorDialog('Validation Error', 'Please fill in all required fields.');
                        return;
                    }

                    this.isSubmitting = true;

                    // Filter out empty skills and docs
                    const requiredSkills = this.formData.requiredSkills.filter(s => s.trim() !== '');
                    const requiredDocs = this.formData.requiredDocs.filter(d => typeof d === 'object' ? d.name.trim() !== '' : d.trim() !== '');

                    // Combine city and state into location string
                    const location = `${this.formData.city}, ${this.formData.state}`;

                    // Prepare data for API
                    const payload = {
                        title: this.formData.title,
                        description: this.formData.description,
                        location: location,
                        salary: this.formData.salary,
                        type: this.formData.type,
                        type_badge: this.formData.typeBadge,
                        icon: this.formData.icon,
                        icon_color: this.formData.iconColor,
                        is_active: this.formData.is_active,
                        status: 'published',
                        required_skills: requiredSkills,
                        required_docs: requiredDocs,
                        benefits: this.formData.benefits
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
                                status: data.data.status || 'published',
                                requiredSkills: data.data.required_skills || [],
                                requiredDocs: data.data.required_docs || [],
                                benefits: data.data.benefits || []
                            };

                            const wasEditing = this.editingIndex !== null;
                            if (wasEditing) {
                                this.jobPostings[this.editingIndex] = savedJob;
                            } else {
                                this.jobPostings.unshift(savedJob);
                            }
                            this.closeModal();
                            this.successTitle = wasEditing ? 'Job Posting Updated' : 'Job Posting Created';
                            this.successMessage = wasEditing ?
                                'The job posting has been updated successfully.' :
                                'The job posting has been created and is now visible to applicants.';
                            this.showSuccess = true;
                        } else {
                            window.showErrorDialog('Save Failed', data.message || 'Failed to save job posting.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.showErrorDialog('Save Failed', 'An error occurred while saving the job posting.');
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

                getCategoryLabel(value) {
                    const cat = this.jobCategories.find(c => c.value === value);
                    return cat ? cat.label : 'Select Category';
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
                },

                getJobStatusClass(status) {
                    const classes = {
                        'published': 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
                        'draft': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400',
                        'archived': 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                        'inactive': 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'
                    };
                    return classes[status] || classes['published'];
                },

                getJobStatusLabel(status) {
                    const labels = {
                        'published': 'Active',
                        'draft': 'Draft',
                        'archived': 'Archived',
                        'inactive': 'Inactive'
                    };
                    return labels[status] || 'Active';
                },

                async saveAsDraft() {
                    if (!this.formData.title || !this.formData.description || !this.formData.state || !this.formData.city || !this.formData.salary) {
                        window.showErrorDialog('Validation Error', 'Please fill in all required fields.');
                        return;
                    }

                    // Show confirmation dialog
                    try {
                        await window.showConfirmDialog(
                            'Save as Draft',
                            'Are you sure you want to save this job posting as a draft?',
                            'Save as Draft',
                            'Cancel'
                        );
                    } catch (e) {
                        return; // User cancelled
                    }

                    this.isSubmitting = true;

                    const requiredSkills = this.formData.requiredSkills.filter(s => s.trim() !== '');
                    const requiredDocs = this.formData.requiredDocs.filter(d => typeof d === 'object' ? d.name.trim() !== '' : d.trim() !== '');
                    const location = `${this.formData.city}, ${this.formData.state}`;

                    const payload = {
                        title: this.formData.title,
                        description: this.formData.description,
                        location: location,
                        salary: this.formData.salary,
                        type: this.formData.type,
                        type_badge: this.formData.typeBadge,
                        icon: this.formData.icon,
                        icon_color: this.formData.iconColor,
                        is_active: false,
                        status: 'draft',
                        required_skills: requiredSkills,
                        required_docs: requiredDocs,
                        benefits: this.formData.benefits
                    };

                    try {
                        const response = await fetch('/admin/job-postings', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();
                        if (data.success) {
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
                                status: data.data.status || 'draft',
                                requiredSkills: data.data.required_skills || [],
                                requiredDocs: data.data.required_docs || [],
                                benefits: data.data.benefits || []
                            };
                            this.jobPostings.unshift(savedJob);
                            this.closeModal();
                            this.successTitle = 'Saved as Draft';
                            this.successMessage = 'Job posting successfully saved as draft.';
                            this.showSuccess = true;
                        } else {
                            window.showErrorDialog('Save Failed', data.message || 'Failed to save job posting as draft.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.showErrorDialog('Save Failed', 'An error occurred while saving the job posting.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
    </script>
</x-layouts.general-employer>
