<x-layouts.general-employer :title="'Job Applications'">
    <section role="status" class="w-full flex flex-col lg:flex-col gap-4 py-6">
        <!-- Header -->
        <div class="flex flex-col gap-2 mb-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Job Applications</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">View and manage job applications from candidates</p>
        </div>

        <!-- Stats Cards -->
        <x-employer-components.stats-cards :stats="[
            ['label' => 'Pending', 'value' => $applications->where('status', 'pending')->count(), 'icon' => 'fi fi-rr-clock', 'iconColor' => '#eab308'],
            ['label' => 'Reviewed', 'value' => $applications->where('status', 'reviewed')->count(), 'icon' => 'fi fi-rr-eye', 'iconColor' => '#3b82f6'],
            ['label' => 'Interview', 'value' => $applications->where('status', 'interview_scheduled')->count(), 'icon' => 'fi fi-rr-calendar', 'iconColor' => '#8b5cf6'],
            ['label' => 'Hired', 'value' => $applications->where('status', 'hired')->count(), 'icon' => 'fi fi-rr-check-circle', 'iconColor' => '#22c55e'],
            ['label' => 'Total Applications', 'value' => $applications->total(), 'icon' => 'fi fi-rr-document', 'iconColor' => '#6b7280'],
        ]" />

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
                    <table class="w-full min-w-[1000px]">
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
                        class="relative w-screen max-w-sm">

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
                                            <i class="fa-solid fa-file-lines text-gray-600 dark:text-gray-400"></i>
                                            Resume / Documents
                                        </h4>
                                        {{-- Primary resume --}}
                                        <div
                                            class="border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <i :class="selectedApp.resume_ext === 'pdf' ? 'fa-solid fa-file-pdf text-red-500 text-xl' : 'fa-solid fa-file-word text-blue-500 text-xl'"></i>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white"
                                                        x-text="selectedApp.resume_name"></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedApp.resume_ext === 'pdf' ? 'PDF Document' : 'DOCX Document'"></p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <button @click="openViewer()"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                                    title="View">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                                <a :href="selectedApp.download_url"
                                                    class="p-2 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                    title="Download">
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                        {{-- Additional documents (Cover Letter, etc.) --}}
                                        <template x-for="(doc, idx) in (selectedApp.documents || [])" :key="idx">
                                            <div class="border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 flex items-center justify-between mt-2">
                                                <div class="flex items-center gap-3">
                                                    <i :class="doc.original_name?.endsWith('.pdf') ? 'fa-solid fa-file-pdf text-red-500 text-xl' : 'fa-solid fa-file-word text-blue-500 text-xl'"></i>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="doc.original_name"></p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="doc.label"></p>
                                                    </div>
                                                </div>
                                                <a :href="'/storage/' + doc.path"
                                                    class="p-2 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                    title="Download" download>
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Scenario #5: Duplicate Applicant Alert --}}
                                    <template x-if="selectedApp?.duplicate_of">
                                        <div class="mb-5 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-300 dark:border-amber-700 rounded-lg">
                                            <div class="flex items-start gap-3">
                                                <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
                                                <div class="flex-1">
                                                    <h4 class="text-sm font-semibold text-amber-800 dark:text-amber-300 mb-1">Potential Duplicate Found</h4>
                                                    <p class="text-xs text-amber-700 dark:text-amber-400 mb-3">
                                                        This applicant shares a phone number with
                                                        <strong x-text="selectedApp.duplicate_of.existing_email"></strong>
                                                        (Application #<span x-text="selectedApp.duplicate_of.existing_id"></span>).
                                                    </p>
                                                    <div class="flex gap-2" x-show="!selectedApp.duplicate_resolved">
                                                        <button @click="handleDuplicate('merge')"
                                                            :disabled="isDuplicateProcessing"
                                                            class="px-3 py-1.5 text-xs font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors disabled:opacity-50">
                                                            <i class="fa-solid fa-code-merge mr-1"></i>Merge
                                                        </button>
                                                        <button @click="handleDuplicate('ignore')"
                                                            :disabled="isDuplicateProcessing"
                                                            class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors disabled:opacity-50">
                                                            <i class="fa-solid fa-xmark mr-1"></i>Ignore
                                                        </button>
                                                    </div>
                                                    <p x-show="selectedApp.duplicate_resolved" class="text-xs font-medium text-green-600 dark:text-green-400">
                                                        <i class="fa-solid fa-circle-check mr-1"></i><span x-text="selectedApp.duplicate_resolved"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Status & Notes -->
                                    <div class="mb-5">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-sliders text-gray-600 dark:text-gray-400"></i>
                                            Status & Notes
                                        </h4>
                                        <div class="space-y-3 py-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            {{-- Admin Notes --}}
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Admin Notes</label>
                                                <textarea x-model="drawerNotes" rows="2"
                                                    :disabled="selectedApp?.status === 'hired' || selectedApp?.status === 'rejected'"
                                                    class="w-full px-3 py-4 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                                    placeholder="Add notes about this applicant..."></textarea>
                                            </div>

                                            {{-- Pending state: waiting for review --}}
                                            <div x-show="selectedApp?.status === 'pending'" class="p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700">
                                                <p class="text-xs text-yellow-700 dark:text-yellow-300 flex items-center gap-2">
                                                    <i class="fa-solid fa-clock"></i>
                                                    Pending review. View the resume for 30 seconds to mark as reviewed.
                                                </p>
                                            </div>

                                            {{-- Interview Scheduled info banner --}}
                                            <div x-show="selectedApp?.status === 'interview_scheduled'" class="p-3 rounded-lg bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700">
                                                <p class="text-xs text-purple-700 dark:text-purple-300 flex items-center gap-2">
                                                    <i class="fa-solid fa-calendar-check"></i>
                                                    Interview scheduled for <span class="font-semibold" x-text="selectedApp?.interview_date_display || selectedApp?.interview_date"></span>
                                                </p>
                                            </div>

                                            {{-- Hired state --}}
                                            <div x-show="selectedApp?.status === 'hired'" class="p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700">
                                                <p class="text-xs text-green-700 dark:text-green-300 flex items-center gap-2">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    This applicant has been <span class="font-bold">hired</span>.
                                                </p>
                                            </div>

                                            {{-- Rejected state --}}
                                            <div x-show="selectedApp?.status === 'rejected'" class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
                                                <p class="text-xs text-red-700 dark:text-red-300 flex items-center gap-2">
                                                    <i class="fa-solid fa-circle-xmark"></i>
                                                    This applicant has been <span class="font-bold">rejected</span>.
                                                </p>
                                            </div>
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
                                        <div class="space-y-0 py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            {{-- Static: Application Submitted --}}
                                            <div class="flex items-start gap-3 relative pb-3">
                                                <div class="flex flex-col items-center flex-shrink-0">
                                                    <div class="w-2.5 h-2.5 mt-1 bg-blue-600 rounded-full"></div>
                                                    <div class="w-px flex-1 bg-gray-300 dark:bg-gray-600 mt-1"
                                                        x-show="selectedApp.status_history && selectedApp.status_history.length > 0"></div>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Application Submitted</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="selectedApp.created_at"></p>
                                                </div>
                                            </div>

                                            {{-- Dynamic: Status History entries --}}
                                            <template x-for="(entry, idx) in (selectedApp.status_history || []).filter(e => e.from !== null)" :key="idx">
                                                <div class="flex items-start gap-3 relative pb-3">
                                                    <div class="flex flex-col items-center flex-shrink-0">
                                                        <div class="w-2.5 h-2.5 mt-1 rounded-full"
                                                            :class="{
                                                                'bg-blue-500': entry.to === 'reviewed',
                                                                'bg-purple-500': entry.to === 'interview_scheduled',
                                                                'bg-green-500': entry.to === 'hired',
                                                                'bg-red-500': entry.to === 'rejected',
                                                                'bg-gray-400': !['reviewed','interview_scheduled','hired','rejected'].includes(entry.to)
                                                            }"></div>
                                                        <div class="w-px flex-1 bg-gray-300 dark:bg-gray-600 mt-1"
                                                            x-show="idx < (selectedApp.status_history || []).filter(e => e.from !== null).length - 1"></div>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="getTimelineLabel(entry)"></p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatTimelineDate(entry.timestamp)"></p>
                                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                                            by <span x-text="entry.by"></span>
                                                        </p>
                                                        <p x-show="entry.interview_date" class="text-xs text-purple-600 dark:text-purple-400 mt-0.5">
                                                            <i class="fa-solid fa-calendar-day mr-1"></i>
                                                            <span x-text="entry.interview_date ? new Date(entry.interview_date + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : ''"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    {{-- <div>
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
                                    </div> --}}
                                </div>
                            </template>

                            <!-- Footer -->
                            <div
                                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50 space-y-3">

                                {{-- Pending: just close --}}
                                <div x-show="selectedApp?.status === 'pending'">
                                    <button @click="closeDrawer()"
                                        class="w-full px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                                        Close
                                    </button>
                                </div>

                                {{-- Reviewed: Schedule Interview + Reject --}}
                                <template x-if="selectedApp?.status === 'reviewed'">
                                    <div class="space-y-3">
                                        <div x-show="showDatePicker" x-transition class="space-y-3">
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Select Interview Date</label>

                                            {{-- Custom Calendar Picker --}}
                                            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                                                {{-- Month navigation --}}
                                                <div class="flex items-center justify-between mb-2">
                                                    <button @click="calPrev()" type="button" class="p-1 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                        <i class="fa-solid fa-chevron-left text-[10px] text-gray-500 dark:text-gray-400"></i>
                                                    </button>
                                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-200" x-text="calMonthLabel"></span>
                                                    <button @click="calNext()" type="button" class="p-1 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                        <i class="fa-solid fa-chevron-right text-[10px] text-gray-500 dark:text-gray-400"></i>
                                                    </button>
                                                </div>

                                                {{-- Day-of-week headers --}}
                                                <div class="grid grid-cols-7 gap-0.5 mb-1">
                                                    <template x-for="d in ['Mo','Tu','We','Th','Fr','Sa','Su']" :key="d">
                                                        <div class="text-center text-[9px] font-semibold text-gray-400 dark:text-gray-500 py-1" x-text="d"></div>
                                                    </template>
                                                </div>

                                                {{-- Day grid --}}
                                                <div class="grid grid-cols-7 gap-0.5">
                                                    <template x-for="cell in calCells" :key="cell.key">
                                                        <div class="relative group">
                                                            <button type="button"
                                                                @click="selectCalDate(cell)"
                                                                :disabled="!cell.inMonth || cell.isPast || cell.isBooked || cell.isHoliday || cell.isSunday"
                                                                :class="{
                                                                    'text-gray-300 dark:text-gray-600 cursor-default': !cell.inMonth,
                                                                    'text-gray-300 dark:text-gray-600 cursor-not-allowed line-through': cell.inMonth && cell.isPast && !cell.isHoliday && !cell.isSunday,
                                                                    'bg-red-100 dark:bg-red-900/30 text-red-400 dark:text-red-500 cursor-not-allowed': cell.inMonth && cell.isBooked && !cell.isPast && !cell.isHoliday,
                                                                    'bg-orange-100 dark:bg-orange-900/30 text-orange-500 dark:text-orange-400 cursor-not-allowed': cell.inMonth && cell.isHoliday && !cell.isPast,
                                                                    'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed': cell.inMonth && cell.isSunday && !cell.isHoliday && !cell.isPast && !cell.isBooked,
                                                                    'bg-purple-600 text-white font-bold shadow-sm ring-2 ring-purple-300 dark:ring-purple-500': cell.isSelected && cell.inMonth && !cell.isBooked && !cell.isHoliday && !cell.isSunday,
                                                                    'bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold': cell.isToday && !cell.isSelected && cell.inMonth && !cell.isBooked && !cell.isPast && !cell.isHoliday && !cell.isSunday,
                                                                    'hover:bg-purple-100 dark:hover:bg-purple-900/30 text-gray-700 dark:text-gray-300 cursor-pointer': cell.inMonth && !cell.isPast && !cell.isBooked && !cell.isHoliday && !cell.isSunday && !cell.isSelected && !cell.isToday,
                                                                }"
                                                                class="flex items-center justify-center w-8 h-8 mx-auto rounded-lg text-[10px] transition-all duration-150">
                                                                <span x-text="cell.day"></span>
                                                            </button>

                                                            {{-- Tooltip for booked dates --}}
                                                            <template x-if="cell.isBooked && cell.inMonth && !cell.isPast">
                                                                <div class="absolute z-50 hidden group-hover:block bottom-full left-1/2 -translate-x-1/2 mb-1 w-44 pointer-events-none">
                                                                    <div class="bg-gray-900 dark:bg-gray-700 text-white text-[9px] rounded-lg px-2.5 py-1.5 shadow-lg">
                                                                        <p class="font-bold text-red-300 mb-0.5"><i class="fa-solid fa-ban mr-1"></i>Unavailable</p>
                                                                        <template x-for="b in cell.bookedBy" :key="b.id">
                                                                            <p class="truncate"><span x-text="b.job_title"></span> — <span x-text="b.email"></span></p>
                                                                        </template>
                                                                    </div>
                                                                    <div class="w-2 h-2 bg-gray-900 dark:bg-gray-700 rotate-45 mx-auto -mt-1"></div>
                                                                </div>
                                                            </template>

                                                            {{-- Tooltip for holidays --}}
                                                            <template x-if="cell.isHoliday && cell.inMonth && !cell.isPast">
                                                                <div class="absolute z-50 hidden group-hover:block bottom-full left-1/2 -translate-x-1/2 mb-1 w-40 pointer-events-none">
                                                                    <div class="bg-gray-900 dark:bg-gray-700 text-white text-[9px] rounded-lg px-2.5 py-1.5 shadow-lg">
                                                                        <p class="font-bold text-orange-300 mb-0.5"><i class="fa-solid fa-calendar-xmark mr-1"></i>Holiday</p>
                                                                        <p x-text="cell.holidayName"></p>
                                                                    </div>
                                                                    <div class="w-2 h-2 bg-gray-900 dark:bg-gray-700 rotate-45 mx-auto -mt-1"></div>
                                                                </div>
                                                            </template>

                                                            {{-- Tooltip for Sundays --}}
                                                            <template x-if="cell.isSunday && !cell.isHoliday && cell.inMonth && !cell.isPast && !cell.isBooked">
                                                                <div class="absolute z-50 hidden group-hover:block bottom-full left-1/2 -translate-x-1/2 mb-1 w-32 pointer-events-none">
                                                                    <div class="bg-gray-900 dark:bg-gray-700 text-white text-[9px] rounded-lg px-2.5 py-1.5 shadow-lg text-center">
                                                                        <p class="font-bold text-gray-400"><i class="fa-solid fa-ban mr-1"></i>Sunday</p>
                                                                    </div>
                                                                    <div class="w-2 h-2 bg-gray-900 dark:bg-gray-700 rotate-45 mx-auto -mt-1"></div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>

                                                {{-- Legend --}}
                                                <div class="flex flex-wrap items-center gap-3 mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                                    <div class="flex items-center gap-1">
                                                        <span class="w-2.5 h-2.5 rounded-sm bg-purple-600"></span>
                                                        <span class="text-[8px] text-gray-500 dark:text-gray-400">Selected</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <span class="w-2.5 h-2.5 rounded-sm bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800"></span>
                                                        <span class="text-[8px] text-gray-500 dark:text-gray-400">Booked</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <span class="w-2.5 h-2.5 rounded-sm bg-orange-100 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-800"></span>
                                                        <span class="text-[8px] text-gray-500 dark:text-gray-400">Holiday</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <span class="w-2.5 h-2.5 rounded-sm bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700"></span>
                                                        <span class="text-[8px] text-gray-500 dark:text-gray-400">Sunday</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <span class="w-2.5 h-2.5 rounded-sm bg-gray-900 dark:bg-white"></span>
                                                        <span class="text-[8px] text-gray-500 dark:text-gray-400">Today</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Selected date & time display --}}
                                            <div x-show="interviewDate" class="space-y-2">
                                                <div class="flex items-center gap-2 px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/40 rounded-lg">
                                                    <i class="fa-solid fa-calendar-check text-purple-500 text-xs"></i>
                                                    <span class="text-xs font-semibold text-purple-700 dark:text-purple-300" x-text="interviewDate ? new Date(interviewDate + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }) : ''"></span>
                                                </div>

                                                {{-- Time picker --}}
                                                <div class="flex items-center gap-2 px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/40 rounded-lg">
                                                    <i class="fa-solid fa-clock text-purple-500 text-xs"></i>
                                                    <label class="text-xs font-medium text-purple-700 dark:text-purple-300">Time:</label>
                                                    <input type="time" x-model="interviewTime"
                                                        class="flex-1 text-xs font-semibold text-purple-700 dark:text-purple-300 bg-transparent border-none outline-none focus:ring-0 p-0"
                                                    />
                                                </div>

                                                {{-- Duration picker --}}
                                                <div class="flex items-center gap-2 px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/40 rounded-lg">
                                                    <i class="fa-solid fa-hourglass-half text-purple-500 text-xs"></i>
                                                    <label class="text-xs font-medium text-purple-700 dark:text-purple-300">Duration:</label>
                                                    <select x-model="interviewDuration"
                                                        class="flex-1 text-xs font-semibold text-purple-700 dark:text-purple-300 bg-transparent border-none outline-none focus:ring-0 p-0 cursor-pointer">
                                                        <option value="15">15 minutes</option>
                                                        <option value="30">30 minutes</option>
                                                        <option value="45">45 minutes</option>
                                                        <option value="60">1 hour</option>
                                                        <option value="90">1 hour 30 minutes</option>
                                                        <option value="120">2 hours</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Action buttons --}}
                                            <div class="flex gap-2">
                                                <button @click="
                                                    if (interviewDate && interviewTime) {
                                                        const conflicts = checkTimeOverlap(interviewDate, interviewTime, interviewDuration);
                                                        if (conflicts) {
                                                            const details = conflicts.map(c => {
                                                                const [ch, cm] = (c.time || '09:00').split(':').map(Number);
                                                                const cEnd = ch * 60 + cm + (c.duration || 60);
                                                                const fmtT = (mins) => {
                                                                    const hh = Math.floor(mins / 60);
                                                                    const mm = mins % 60;
                                                                    const ampm = hh < 12 ? 'AM' : 'PM';
                                                                    const disp = hh === 0 ? 12 : (hh > 12 ? hh - 12 : hh);
                                                                    return String(disp).padStart(2,'0') + ':' + String(mm).padStart(2,'0') + ' ' + ampm;
                                                                };
                                                                return c.job_title + ' (' + c.email + ') — ' + fmtT(ch * 60 + cm) + ' to ' + fmtT(cEnd);
                                                            }).join('\n');
                                                            window.showErrorDialog('Schedule Conflict', 'The selected time slot overlaps with an existing interview:\n\n' + details);
                                                        } else {
                                                            setStatus('interview_scheduled', { interview_date: interviewDate + ' ' + interviewTime + ':00', interview_duration: parseInt(interviewDuration) });
                                                        }
                                                    }"
                                                    :disabled="!interviewDate || !interviewTime || isUpdating"
                                                    class="flex-1 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50">
                                                    <i class="fa-solid fa-calendar-check mr-1.5"></i>Confirm Schedule
                                                </button>
                                                <button @click="showDatePicker = false; interviewDate = ''; interviewTime = '09:00'; interviewDuration = '60'"
                                                    class="px-4 py-2.5 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                        <div x-show="!showDatePicker" class="flex gap-2">
                                            <button @click="showDatePicker = true; buildCalCells()"
                                                :disabled="isUpdating"
                                                class="flex-1 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50">
                                                <i class="fa-solid fa-calendar-plus mr-1.5"></i>Schedule Interview
                                            </button>
                                            <button @click="setStatus('rejected')"
                                                :disabled="isUpdating"
                                                class="flex-1 px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50">
                                                <i class="fa-solid fa-xmark mr-1.5"></i>Reject
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                {{-- Interview Scheduled: Hire + Reject --}}
                                <template x-if="selectedApp?.status === 'interview_scheduled'">
                                    <div class="flex gap-2">
                                        <button @click="setStatus('hired')"
                                            :disabled="isUpdating"
                                            class="flex-1 px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50">
                                            <i class="fa-solid fa-circle-check mr-1.5"></i>Hire
                                        </button>
                                        <button @click="setStatus('rejected')"
                                            :disabled="isUpdating"
                                            class="flex-1 px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50">
                                            <i class="fa-solid fa-xmark mr-1.5"></i>Reject
                                        </button>
                                    </div>
                                </template>

                                {{-- Hired/Rejected: just close --}}
                                <div x-show="selectedApp?.status === 'hired' || selectedApp?.status === 'rejected'">
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

            {{-- ── Resume Viewer Modal ── --}}
            <template x-teleport="body">
            <div>
                <div
                    x-show="showViewer"
                    style="display:none"
                    class="fixed inset-0 z-[120] bg-black/70 backdrop-blur-sm flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                >
                    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden"
                        @click.stop
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">

                        {{-- Viewer Header --}}
                        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex-shrink-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <i class="fa-solid fa-file-word text-blue-500 text-lg flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="selectedApp?.resume_name"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500" x-text="selectedApp?.email"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                {{-- Timer --}}
                                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium"
                                    :class="viewerElapsed >= 30 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'">
                                    <i class="fa-solid fa-stopwatch text-[10px]"></i>
                                    <span x-text="formatViewerTime(viewerElapsed)"></span>
                                    <i x-show="viewerElapsed >= 30" class="fa-solid fa-circle-check text-green-500 text-[10px]"></i>
                                </div>
                                {{-- Auto-review hint --}}
                                <span x-show="selectedApp?.status === 'pending' && viewerElapsed < 30"
                                    class="text-[10px] text-gray-400 dark:text-gray-500 hidden sm:inline">
                                    Auto-review at 30s
                                </span>
                                {{-- Download --}}
                                <a :href="selectedApp?.download_url"
                                    class="p-2 text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                    title="Download">
                                    <i class="fa-solid fa-download text-sm"></i>
                                </a>
                                {{-- Close --}}
                                <button @click="closeViewer()"
                                    class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                    <i class="fa-solid fa-xmark text-gray-600 dark:text-gray-300 text-sm"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Viewer Body --}}
                        <div class="flex-1 min-h-0 bg-gray-100 dark:bg-gray-800">
                            <iframe
                                x-show="showViewer && viewerUrl"
                                :src="viewerUrl"
                                class="w-full h-full border-0"
                                sandbox="allow-scripts allow-same-origin allow-popups"
                            ></iframe>
                        </div>

                        {{-- Viewer Footer --}}
                        <div class="flex items-center justify-between px-5 py-2.5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex-shrink-0">
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                <span x-show="selectedApp?.status === 'pending' && viewerElapsed >= 30">
                                    <i class="fa-solid fa-circle-check text-green-500 mr-1"></i>Status will be updated to <span class="font-semibold text-green-600 dark:text-green-400">Reviewed</span> when you close this viewer.
                                </span>
                                <span x-show="selectedApp?.status !== 'pending'">
                                    Status: <span class="font-semibold capitalize" x-text="selectedApp?.status?.replace('_', ' ')"></span>
                                </span>
                                <span x-show="selectedApp?.status === 'pending' && viewerElapsed < 30">
                                    View for at least 30 seconds to auto-mark as reviewed.
                                </span>
                            </p>
                            <button @click="closeViewer()"
                                class="text-xs font-semibold px-4 py-1.5 rounded-lg bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors">
                                Close Viewer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </template>

        </div>

        <!-- Applicant Profiles Section -->
        <div class="flex flex-col gap-4 w-full rounded-lg p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mx-4">
                <div>
                    <x-labelwithvalue label="Registered Applicants" count="({{ $applicantUsers->count() }})" />
                </div>
                <div class="flex-1 md:max-w-xs">
                    {{-- <div class="relative">
                        <input type="text" id="applicantSearchInput" placeholder="Search applicants..."
                            class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div> --}}
                </div>
            </div>

            @if($applicantUsers->count() > 0)
            <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full min-w-[1000px]" id="applicantsTable">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Applicant</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Account Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Location</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Applications</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Registered</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applicantUsers as $applicant)
                        @php
                            $appCount = $applications->where('email', $applicant->email)->count();
                            $profilePic = $applicant->profile_picture;
                            $profileUrl = $profilePic
                                ? (str_starts_with($profilePic, 'profile_pictures/')
                                    ? asset('storage/' . $profilePic)
                                    : asset($profilePic))
                                : null;
                        @endphp
                        <tr class="even:bg-gray-50 dark:even:bg-gray-800/50 applicant-row"
                            data-name="{{ strtolower($applicant->name) }}"
                            data-email="{{ strtolower($applicant->email) }}">

                            {{-- Applicant --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    @if($profileUrl)
                                    <img src="{{ $profileUrl }}" alt="" class="w-8 h-8 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700 {{ !$applicant->is_active ? 'opacity-50 grayscale' : '' }}">
                                    @else
                                    <div class="w-8 h-8 rounded-full {{ !$applicant->is_active ? 'bg-red-100 dark:bg-red-900/30' : 'bg-blue-100 dark:bg-blue-900/30' }} flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold {{ !$applicant->is_active ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400' }}">{{ strtoupper(substr($applicant->name, 0, 1)) }}</span>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $applicant->name }}</p>
                                            @if(!$applicant->is_active)
                                            <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold rounded bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">Banned</span>
                                            @endif
                                        </div>
                                        @if($applicant->phone)
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $applicant->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-900 dark:text-gray-200">{{ $applicant->email }}</p>
                            </td>

                            {{-- Account Type --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($applicant->google_id)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24"><path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                    Google
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                    <i class="fa-solid fa-envelope text-[10px]"></i>
                                    Email
                                </span>
                                @endif
                            </td>

                            {{-- Location --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($applicant->location)
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <i class="fa-solid fa-location-dot text-gray-400 mr-1 text-[10px]"></i>{{ $applicant->location }}
                                </p>
                                @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">--</span>
                                @endif
                            </td>

                            {{-- Applications count --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($appCount > 0)
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400">
                                    {{ $appCount }} {{ Str::plural('application', $appCount) }}
                                </span>
                                @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">No applications</span>
                                @endif
                            </td>

                            {{-- Registered --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-900 dark:text-gray-200">{{ $applicant->created_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $applicant->created_at->format('h:i A') }}</p>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div x-data="{ open: false, showRoleModal: false, selectedRole: '{{ $applicant->role }}', processing: false }" class="relative">
                                    <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>

                                    {{-- Dropdown --}}
                                    <div x-show="open" @click.away="open = false" x-transition
                                        class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 py-1">

                                        {{-- Change Role --}}
                                        <button @click="open = false; showRoleModal = true"
                                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <i class="fa-solid fa-user-gear text-gray-400 w-4 text-center"></i>
                                            Change Role
                                        </button>

                                        {{-- Ban / Unban --}}
                                        <button @click="open = false; if(confirm('{{ $applicant->is_active ? "Ban this user? They will not be able to log in or submit applications." : "Unban this user? They will regain access to their account." }}')) {
                                                processing = true;
                                                fetch('/admin/recruitment/applicant/{{ $applicant->id }}/ban', {
                                                    method: 'PATCH',
                                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
                                                }).then(r => r.json()).then(d => { if(d.success) location.reload(); else alert(d.message); processing = false; }).catch(() => { processing = false; });
                                            }"
                                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm {{ $applicant->is_active ? 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20' : 'text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20' }} transition-colors">
                                            <i class="fa-solid {{ $applicant->is_active ? 'fa-ban' : 'fa-circle-check' }} w-4 text-center"></i>
                                            {{ $applicant->is_active ? 'Ban User' : 'Unban User' }}
                                        </button>
                                    </div>

                                    {{-- Change Role Modal --}}
                                    <template x-teleport="body">
                                        <div x-show="showRoleModal" x-transition.opacity class="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center" @click.self="showRoleModal = false">
                                            <div @click.stop class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
                                                <div class="px-6 pt-5 pb-4">
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Change Role</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ $applicant->name }}</p>
                                                    <select x-model="selectedRole" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                                        <option value="applicant">Applicant</option>
                                                        <option value="external_client">External Client</option>
                                                        <option value="employee">Employee</option>
                                                    </select>
                                                </div>
                                                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900/50 flex justify-end gap-2">
                                                    <button @click="showRoleModal = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">Cancel</button>
                                                    <button @click="processing = true;
                                                        fetch('/admin/recruitment/applicant/{{ $applicant->id }}/role', {
                                                            method: 'PATCH',
                                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                                                            body: JSON.stringify({ role: selectedRole })
                                                        }).then(r => r.json()).then(d => { if(d.success) location.reload(); else alert(d.message); processing = false; }).catch(() => { processing = false; });"
                                                        :disabled="processing"
                                                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                                                        <span x-show="!processing">Save</span>
                                                        <span x-show="processing"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Saving...</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="w-full rounded-lg border border-dashed border-gray-200 dark:border-gray-700 px-6 py-16 text-center">
                <i class="fa-solid fa-users text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                <p class="text-base font-medium text-gray-500 dark:text-gray-400">No registered applicants yet</p>
                <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Applicants who sign up will appear here</p>
            </div>
            @endif
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
                <table class="w-full min-w-[1000px]">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-4 w-10">
                                <input type="checkbox" @change="toggleAllJobs($event)" :checked="allJobsSelected"
                                    class="appearance-none w-4 h-4 rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 checked:bg-blue-600 checked:border-blue-600 checked:bg-[url('data:image/svg+xml,%3Csvg%20viewBox%3D%220%200%2016%2016%22%20fill%3D%22white%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12.207%204.793a1%201%200%20010%201.414l-5%205a1%201%200%2001-1.414%200l-2-2a1%201%200%20011.414-1.414L6.5%209.086l4.293-4.293a1%201%200%20011.414%200z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-colors">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 max-w-[250px]">Job
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
                                <td class="px-6 py-4 max-w-[250px]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                            :class="getIconBgClass(job.iconColor)">
                                            <i class="fas text-sm"
                                                :class="[job.icon, getIconTextClass(job.iconColor)]"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate"
                                                x-text="job.title"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate"
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
                                        <button @click="viewJob(index)"
                                            class="text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors"
                                            title="View">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </button>
                                        <button @click="editJob(index)"
                                            class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                            title="Edit">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <button @click="archiveJob(index)"
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
            <x-employer-components.job-posting-modal />
            <!-- Success Dialog -->
            <template x-teleport="body">
                <x-employer-components.success-dialog title="Success" message="" buttonText="Back to Recruitment" />
            </template>

            <!-- Job Posting View Drawer -->
            <div x-show="showJobDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden">
                <!-- Backdrop -->
                <div x-show="showJobDrawer" x-transition:enter="transition-opacity ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    @click="closeJobDrawer()"
                    class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

                <!-- Drawer Panel -->
                <div class="fixed inset-y-0 right-0 flex max-w-full">
                    <div x-show="showJobDrawer" x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-200"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                        @click.stop
                        class="relative w-screen max-w-sm">

                        <div class="h-full flex flex-col bg-white dark:bg-slate-800 shadow-2xl border-l border-gray-200 dark:border-slate-700">
                            <!-- Header -->
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-slate-800/50">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Job Posting Details</h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="viewingJob?.title"></p>
                                </div>
                                <button type="button" @click="closeJobDrawer()"
                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Body -->
                            <template x-if="viewingJob">
                                <div class="flex-1 overflow-y-auto p-6">

                                    <!-- Job Info -->
                                    <div class="mb-5">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-briefcase text-gray-600 dark:text-gray-400"></i>
                                            Job Information
                                        </h4>
                                        <div class="space-y-3 text-sm py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Title</span>
                                                <span class="font-medium text-gray-900 dark:text-white" x-text="viewingJob.title"></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Type</span>
                                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                                    :class="getTypeBadgeClass(viewingJob.type)" x-text="viewingJob.typeBadge"></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Location</span>
                                                <span class="font-medium text-gray-900 dark:text-white" x-text="viewingJob.location"></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Salary</span>
                                                <span class="font-medium text-gray-900 dark:text-white" x-text="viewingJob.salary"></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Status</span>
                                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                                    :class="getJobStatusClass(viewingJob.status)"
                                                    x-text="getJobStatusLabel(viewingJob.status)"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Required Skills -->
                                    <div class="mb-5" x-show="viewingJob.requiredSkills && viewingJob.requiredSkills.length > 0">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-star text-gray-600 dark:text-gray-400"></i>
                                            Required Skills
                                        </h4>
                                        <div class="flex flex-wrap gap-1.5 py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            <template x-for="skill in viewingJob.requiredSkills" :key="skill">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                                    x-text="skill"></span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Applicant Suitability Ranking -->
                                    <div class="mb-5">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-ranking-star text-gray-600 dark:text-gray-400"></i>
                                            Applicant Suitability Ranking
                                            <span class="text-xs font-normal text-gray-400 dark:text-gray-500"
                                                x-text="'(' + rankedApplicants.length + ' applicant' + (rankedApplicants.length !== 1 ? 's' : '') + ')'"></span>
                                        </h4>

                                        <template x-if="rankedApplicants.length === 0">
                                            <div class="text-center py-8 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                                <i class="fa-solid fa-users-slash text-2xl text-gray-300 dark:text-gray-600 mb-2 block"></i>
                                                <p class="text-xs text-gray-400 dark:text-gray-500">No applicants for this position yet</p>
                                            </div>
                                        </template>

                                        <template x-if="rankedApplicants.length > 0">
                                            <div class="space-y-2">
                                                <template x-for="(applicant, idx) in rankedApplicants" :key="applicant.id">
                                                    <div class="flex items-center gap-3 py-3 px-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700/50">
                                                        <!-- Rank -->
                                                        <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold"
                                                            :class="idx === 0 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' :
                                                                     idx === 1 ? 'bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300' :
                                                                     idx === 2 ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' :
                                                                     'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'"
                                                            x-text="idx + 1"></div>

                                                        <!-- Info -->
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                                                x-text="applicant.name || applicant.email"></p>
                                                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate" x-show="applicant.name"
                                                                x-text="applicant.email"></p>
                                                            <!-- Matched skills -->
                                                            <div class="flex flex-wrap gap-1 mt-1.5" x-show="applicant.matchedSkills.length > 0">
                                                                <template x-for="s in applicant.matchedSkills" :key="s">
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"
                                                                        x-text="s"></span>
                                                                </template>
                                                            </div>
                                                        </div>

                                                        <!-- Score -->
                                                        <div class="flex-shrink-0 text-right">
                                                            <div class="text-lg font-bold"
                                                                :class="applicant.score >= 75 ? 'text-green-600 dark:text-green-400' :
                                                                         applicant.score >= 50 ? 'text-yellow-600 dark:text-yellow-400' :
                                                                         applicant.score >= 25 ? 'text-orange-500 dark:text-orange-400' :
                                                                         'text-red-500 dark:text-red-400'"
                                                                x-text="applicant.score + '%'"></div>
                                                            <p class="text-[10px] text-gray-400 dark:text-gray-500"
                                                                x-text="applicant.matchedSkills.length + '/' + (viewingJob.requiredSkills?.length || 0) + ' skills'"></p>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>

                                </div>
                            </template>

                            <!-- Footer -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                                <button @click="closeJobDrawer()"
                                    class="w-full px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>

    @php
        $applicationsData = $applications->getCollection()->map(function ($app) use ($duplicateAlerts) {
            $dupAlert = $duplicateAlerts[$app->id] ?? null;
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
                'resume_ext' => strtolower(pathinfo($app->resume_original_name ?? '', PATHINFO_EXTENSION)),
                'documents' => $app->documents ?? [],
                'view_url' => route('admin.recruitment.view', $app->id),
                'preview_url' => route('admin.recruitment.preview', $app->id),
                'download_url' => route('admin.recruitment.download', $app->id),
                'interview_date' => $app->interview_date ? $app->interview_date->format('Y-m-d') : null,
                'interview_time' => $app->interview_date ? $app->interview_date->format('H:i') : '09:00',
                'interview_duration' => $app->interview_duration ?? 60,
                'interview_date_display' => $app->interview_date
                    ? $app->interview_date->format('M d, Y \a\t h:i A') . ' (' . ($app->interview_duration >= 60 ? floor($app->interview_duration/60).'h'.($app->interview_duration%60 ? ' '.$app->interview_duration%60 .'m' : '') : ($app->interview_duration ?? 60).'m') . ')'
                    : null,
                'created_at' => $app->created_at->format('M d, Y h:i A'),
                'reviewed_at' => $app->reviewed_at ? $app->reviewed_at->format('M d, Y h:i A') : null,
                'status_history' => $app->status_history ?? [],
                'duplicate_of' => $dupAlert ? [
                    'existing_id' => $dupAlert['existing_application_id'],
                    'existing_email' => $dupAlert['existing_email'],
                    'phone' => $dupAlert['phone'],
                    'notification_id' => $dupAlert['notification_id'],
                ] : null,
                'duplicate_resolved' => null,
            ];
        });

        // Collect all booked interview dates (for overlap check)
        $bookedInterviews = \App\Models\JobApplication::whereNotNull('interview_date')
            ->whereIn('status', ['interview_scheduled', 'hired'])
            ->select('id', 'interview_date', 'interview_duration', 'job_title', 'email')
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'date' => $a->interview_date->format('Y-m-d'),
                'time' => $a->interview_date->format('H:i'),
                'duration' => $a->interview_duration ?? 60,
                'job_title' => $a->job_title,
                'email' => $a->email,
            ]);

        // Collect holidays (admin-created + general)
        $holidays = \App\Models\Holiday::all()->map(fn($h) => [
            'date' => $h->date->format('Y-m-d'),
            'name' => $h->name,
        ]);
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

        // Applicant profiles search
        const applicantSearchInput = document.getElementById('applicantSearchInput');
        if (applicantSearchInput) {
            applicantSearchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.applicant-row').forEach(row => {
                    const name = row.dataset.name || '';
                    const email = row.dataset.email || '';
                    row.style.display = (name.includes(q) || email.includes(q)) ? '' : 'none';
                });
            });
        }

        function applicationDrawerData() {
            const applications = @json($applicationsData);
            const bookedInterviews = @json($bookedInterviews);
            const holidays = @json($holidays);

            return {
                showDrawer: false,
                selectedApp: null,
                drawerStatus: '',
                drawerNotes: '',
                isUpdating: false,
                interviewDate: '',
                interviewTime: '09:00',
                interviewDuration: '60',
                showDatePicker: false,
                // Calendar picker state
                calMonth: new Date().getMonth(),
                calYear: new Date().getFullYear(),
                calCells: [],
                // Resume viewer state
                showViewer: false,
                viewerUrl: '',
                viewerStartTime: null,
                viewerTimerInterval: null,
                viewerElapsed: 0,
                selectedAppIds: [],
                showConfirm: false,
                confirmTitle: '',
                confirmMessage: '',
                pendingConfirmAction: null,
                isDuplicateProcessing: false,

                async handleDuplicate(action) {
                    if (!this.selectedApp?.duplicate_of) return;
                    this.isDuplicateProcessing = true;

                    const url = action === 'merge'
                        ? '/admin/recruitment/duplicate/merge'
                        : '/admin/recruitment/duplicate/ignore';

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                new_application_id: this.selectedApp.id,
                                existing_application_id: this.selectedApp.duplicate_of.existing_id
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.selectedApp.duplicate_resolved = action === 'merge'
                                ? 'Merged successfully'
                                : 'Duplicate dismissed';
                        }
                    } catch (e) {
                        console.error('Duplicate handling error:', e);
                    }
                    this.isDuplicateProcessing = false;
                },

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
                        this.interviewDate = this.selectedApp.interview_date ? this.selectedApp.interview_date.split('T')[0].split(' ')[0] : '';
                        this.interviewTime = this.selectedApp.interview_time || '09:00';
                        this.interviewDuration = this.selectedApp.interview_duration || '60';
                        this.showDatePicker = false;
                        this.calMonth = new Date().getMonth();
                        this.calYear = new Date().getFullYear();
                        this.buildCalCells();
                        this.showDrawer = true;
                        document.body.style.overflow = 'hidden';
                    }
                },

                closeDrawer() {
                    this.closeViewer();
                    this.showDrawer = false;
                    this.selectedApp = null;
                    document.body.style.overflow = 'auto';
                },

                // ── Calendar picker methods ──
                fmtDate(d) {
                    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
                },

                isDateBooked(dateStr) {
                    // Check if any OTHER application (not the current one) has an interview on this date
                    return bookedInterviews.some(b => b.date === dateStr && b.id !== this.selectedApp?.id);
                },

                getBookedInfo(dateStr) {
                    return bookedInterviews.filter(b => b.date === dateStr && b.id !== this.selectedApp?.id);
                },

                isHoliday(dateStr) {
                    return holidays.some(h => h.date === dateStr);
                },

                getHolidayName(dateStr) {
                    const h = holidays.find(h => h.date === dateStr);
                    return h ? h.name : '';
                },

                /**
                 * Check for overlapping interviews using: start1 < end2 AND end1 > start2
                 * Returns the conflicting booking or null.
                 */
                checkTimeOverlap(date, time, duration) {
                    const [h1, m1] = time.split(':').map(Number);
                    const newStart = h1 * 60 + m1;
                    const newEnd = newStart + parseInt(duration);

                    const conflicts = bookedInterviews.filter(b => {
                        if (b.date !== date || b.id === this.selectedApp?.id) return false;
                        const [bh, bm] = (b.time || '09:00').split(':').map(Number);
                        const bStart = bh * 60 + bm;
                        const bEnd = bStart + (b.duration || 60);
                        // Overlap check: start1 < end2 AND end1 > start2
                        return newStart < bEnd && newEnd > bStart;
                    });

                    return conflicts.length > 0 ? conflicts : null;
                },

                buildCalCells() {
                    const first = new Date(this.calYear, this.calMonth, 1);
                    const last = new Date(this.calYear, this.calMonth + 1, 0);
                    let startDay = first.getDay() || 7;
                    const today = this.fmtDate(new Date());
                    const cells = [];

                    // Prev month padding
                    const prevLast = new Date(this.calYear, this.calMonth, 0);
                    for (let i = startDay - 1; i >= 1; i--) {
                        const day = prevLast.getDate() - i + 1;
                        const d = new Date(this.calYear, this.calMonth - 1, day);
                        cells.push({ key: 'p'+day, day, date: this.fmtDate(d), inMonth: false, isPast: true, isToday: false, isBooked: false, isSelected: false, bookedBy: [] });
                    }

                    // Current month
                    for (let day = 1; day <= last.getDate(); day++) {
                        const d = new Date(this.calYear, this.calMonth, day);
                        const dateStr = this.fmtDate(d);
                        const isPast = dateStr < today;
                        const isSunday = d.getDay() === 0;
                        cells.push({
                            key: 'c'+day,
                            day,
                            date: dateStr,
                            inMonth: true,
                            isPast,
                            isToday: dateStr === today,
                            isBooked: this.isDateBooked(dateStr),
                            isHoliday: this.isHoliday(dateStr),
                            holidayName: this.getHolidayName(dateStr),
                            isSunday,
                            isSelected: dateStr === this.interviewDate,
                            bookedBy: this.getBookedInfo(dateStr),
                        });
                    }

                    // Next month padding
                    const remaining = 42 - cells.length;
                    for (let day = 1; day <= remaining; day++) {
                        const d = new Date(this.calYear, this.calMonth + 1, day);
                        cells.push({ key: 'n'+day, day, date: this.fmtDate(d), inMonth: false, isPast: false, isToday: false, isBooked: false, isSelected: false, bookedBy: [] });
                    }

                    this.calCells = cells;
                },

                calPrev() {
                    this.calMonth--;
                    if (this.calMonth < 0) { this.calMonth = 11; this.calYear--; }
                    this.buildCalCells();
                },

                calNext() {
                    this.calMonth++;
                    if (this.calMonth > 11) { this.calMonth = 0; this.calYear++; }
                    this.buildCalCells();
                },

                selectCalDate(cell) {
                    if (!cell.inMonth || cell.isPast || cell.isBooked || cell.isHoliday || cell.isSunday) return;
                    this.interviewDate = cell.date;
                    this.buildCalCells();
                },

                get calMonthLabel() {
                    return new Date(this.calYear, this.calMonth).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                },

                openViewer() {
                    if (!this.selectedApp) return;
                    // Use the preview endpoint — handles both PDF (inline) and DOCX (server-side HTML conversion)
                    this.viewerUrl = this.selectedApp.preview_url;
                    this.showViewer = true;
                    this.viewerStartTime = Date.now();
                    this.viewerElapsed = 0;
                    this.viewerTimerInterval = setInterval(() => {
                        this.viewerElapsed = Math.floor((Date.now() - this.viewerStartTime) / 1000);
                    }, 1000);
                },

                async closeViewer() {
                    if (this.viewerTimerInterval) {
                        clearInterval(this.viewerTimerInterval);
                        this.viewerTimerInterval = null;
                    }
                    // Auto-mark as reviewed if viewed for 60+ seconds and status is still pending
                    if (this.selectedApp && this.viewerElapsed >= 30 && this.selectedApp.status === 'pending') {
                        try {
                            const response = await fetch(`/admin/recruitment/${this.selectedApp.id}/status`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ status: 'reviewed', admin_notes: this.selectedApp.admin_notes || '' })
                            });
                            if (response.ok) {
                                this.selectedApp.status = 'reviewed';
                                this.drawerStatus = 'reviewed';
                                this.selectedApp.reviewed_at = new Date().toLocaleString('en-US', {
                                    month: 'short', day: '2-digit', year: 'numeric',
                                    hour: '2-digit', minute: '2-digit', hour12: true
                                });
                                // Update local timeline
                                if (!this.selectedApp.status_history) this.selectedApp.status_history = [];
                                this.selectedApp.status_history.push({
                                    from: 'pending',
                                    to: 'reviewed',
                                    timestamp: new Date().toISOString(),
                                    by: 'Auto-review (30s)',
                                });
                            }
                        } catch (e) {
                            console.error('Auto-review failed:', e);
                        }
                    }
                    this.showViewer = false;
                    this.viewerUrl = '';
                    this.viewerElapsed = 0;
                },

                formatViewerTime(seconds) {
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    return m > 0 ? `${m}m ${s}s` : `${s}s`;
                },

                getTimelineLabel(entry) {
                    const labels = {
                        'reviewed': 'Marked as Reviewed',
                        'interview_scheduled': 'Interview Scheduled',
                        'hired': 'Hired',
                        'rejected': 'Rejected',
                        'pending': 'Reset to Pending',
                    };
                    return labels[entry.to] || `Status changed to ${entry.to}`;
                },

                formatTimelineDate(timestamp) {
                    if (!timestamp) return '';
                    const d = new Date(timestamp);
                    return d.toLocaleDateString('en-US', {
                        month: 'short', day: '2-digit', year: 'numeric',
                        hour: '2-digit', minute: '2-digit', hour12: true
                    });
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

                async setStatus(newStatus, extraData = {}) {
                    if (!this.selectedApp) return;
                    this.isUpdating = true;

                    const payload = {
                        status: newStatus,
                        admin_notes: this.drawerNotes,
                        ...extraData
                    };

                    try {
                        const response = await fetch(`/admin/recruitment/${this.selectedApp.id}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        if (response.ok) {
                            this.selectedApp.status = newStatus;
                            this.drawerStatus = newStatus;
                            this.selectedApp.admin_notes = this.drawerNotes;
                            if (extraData.interview_date) {
                                const fullDate = extraData.interview_date;
                                const datePart = fullDate.split(' ')[0];
                                const timePart = fullDate.split(' ')[1] || '09:00:00';
                                this.selectedApp.interview_date = datePart;
                                this.selectedApp.interview_time = timePart.substring(0, 5);
                                this.selectedApp.interview_duration = extraData.interview_duration || 60;

                                const dur = parseInt(extraData.interview_duration || 60);
                                const durLabel = dur >= 60 ? Math.floor(dur/60) + 'h' + (dur%60 ? ' ' + dur%60 + 'm' : '') : dur + 'm';

                                const dt = new Date(datePart + 'T' + timePart);
                                this.selectedApp.interview_date_display = dt.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
                                    + ' at ' + dt.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })
                                    + ' (' + durLabel + ')';

                                // Update booked interviews for overlap checking within same session
                                const existingIdx = bookedInterviews.findIndex(b => b.id === this.selectedApp.id);
                                const booking = {
                                    id: this.selectedApp.id,
                                    date: datePart,
                                    time: timePart.substring(0, 5),
                                    duration: extraData.interview_duration || 60,
                                    job_title: this.selectedApp.job_title,
                                    email: this.selectedApp.email
                                };
                                if (existingIdx >= 0) { bookedInterviews[existingIdx] = booking; }
                                else { bookedInterviews.push(booking); }
                            }
                            this.selectedApp.reviewed_at = new Date().toLocaleString('en-US', {
                                month: 'short', day: '2-digit', year: 'numeric',
                                hour: '2-digit', minute: '2-digit', hour12: true
                            });
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
            const allApps = @json($allApplications ?? []);
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
                // Job posting view drawer
                showJobDrawer: false,
                viewingJob: null,
                rankedApplicants: [],
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
                    { value: 'management', label: 'Management', description: 'Supervisory and leadership positions', icon: 'fa-user-tie', color: 'purple' },
                    { value: 'logistics', label: 'Logistics', description: 'Supply chain and equipment handling', icon: 'fa-dolly', color: 'orange' },
                    { value: 'quality-assurance', label: 'Quality Assurance', description: 'Inspection and compliance checks', icon: 'fa-clipboard-check', color: 'purple' },
                    { value: 'customer-service', label: 'Customer Service', description: 'Client communication and support', icon: 'fa-headset', color: 'blue' },
                    { value: 'operations', label: 'Operations', description: 'Day-to-day business operations', icon: 'fa-briefcase', color: 'orange' },
                    { value: 'maintenance', label: 'Maintenance', description: 'Facility and equipment upkeep', icon: 'fa-wrench', color: 'red' },
                ],
                defaultSkillsMap: {
                    'cleaning': ['Surface Sanitization', 'Disinfection Procedures', 'Waste Disposal', 'Deep Cleaning', 'Carpet Cleaning', 'Restroom Sanitation'],
                    'management': ['Team Leadership', 'Staff Supervision', 'Task Delegation', 'Performance Monitoring', 'Workflow Management'],
                    'logistics': ['Inventory Management', 'Supply Coordination', 'Route Planning', 'Resource Scheduling', 'Time Management'],
                    'quality-assurance': ['Quality Inspection', 'Safety Compliance', 'Cleaning Standards', 'Process Monitoring', 'Issue Reporting', 'Quality Control'],
                    'customer-service': ['Client Communication', 'Complaint Handling', 'Service Coordination', 'Professional Communication', 'Client Support'],
                    'operations': ['Operations Coordination', 'Task Prioritization', 'Workflow Optimization', 'Resource Management', 'Service Monitoring', 'Operational Reporting'],
                    'maintenance': ['Equipment Maintenance', 'Preventive Maintenance', 'Facility Maintenance', 'Minor Repairs', 'Equipment Troubleshooting', 'Maintenance Reporting'],
                },
                defaultDocs: [
                    { name: 'Resume', fileType: 'docx,pdf' },
                    { name: 'Cover Letter', fileType: 'docx,pdf' },
                ],
                defaultBenefits: ['Occupational health care', 'Lodging benefits', 'Commuting benefits', 'Occupational accident insurance', 'Public Holidays', 'Annual paid leave'],
                defaultDescriptionMap: {
                    'cleaning': 'This role is responsible for maintaining cleanliness and sanitation across assigned areas. Duties include sweeping, mopping, vacuuming, dusting, disinfecting surfaces, and ensuring that facilities are hygienic and presentable. The role also involves proper waste disposal, restocking cleaning supplies, and following safety and sanitation standards.',
                    'management': 'This role oversees daily operations and ensures that cleaning teams perform their tasks efficiently and according to company standards. Responsibilities include supervising staff, scheduling shifts, assigning tasks, monitoring performance, and resolving operational issues to maintain high service quality.',
                    'logistics': 'This role manages the distribution and tracking of cleaning supplies, equipment, and other resources. The role ensures that teams have the necessary materials to perform their duties, coordinates deliveries, and maintains inventory records to support smooth operational workflows.',
                    'quality-assurance': 'This role ensures that cleaning services meet company standards and client expectations. This role involves conducting inspections, monitoring cleaning procedures, identifying areas for improvement, and ensuring compliance with health and safety regulations.',
                    'customer-service': 'The Customer Service Representative acts as the primary point of contact for clients. Responsibilities include responding to inquiries, addressing concerns or complaints, coordinating service requests, and ensuring that customers receive professional and satisfactory service.',
                    'operations': 'The Operations Coordinator manages the day-to-day activities of the cleaning services team. This role involves organizing workflows, monitoring service delivery, coordinating staff assignments, and ensuring that operational processes run efficiently.',
                    'maintenance': 'The Maintenance Technician is responsible for maintaining and repairing cleaning equipment and facility assets. Duties include performing routine inspections, preventive maintenance, minor repairs, and ensuring that equipment operates safely and efficiently.',
                },
                formData: {
                    id: null,
                    title: '',
                    description: 'This role is responsible for maintaining cleanliness and sanitation across assigned areas. Duties include sweeping, mopping, vacuuming, dusting, disinfecting surfaces, and ensuring that facilities are hygienic and presentable. The role also involves proper waste disposal, restocking cleaning supplies, and following safety and sanitation standards.',
                    state: '',
                    city: '',
                    salary: '',
                    type: 'full-time',
                    typeBadge: 'Full-time Employee',
                    category: 'cleaning',
                    icon: 'fa-broom',
                    iconColor: 'green',
                    is_active: true,
                    requiredSkills: ['Surface Sanitization', 'Disinfection Procedures', 'Waste Disposal', 'Deep Cleaning', 'Carpet Cleaning', 'Restroom Sanitation'],
                    requiredDocs: [{ name: 'Resume', fileType: 'docx' }, { name: 'Cover Letter', fileType: 'docx' }],
                    benefits: ['Occupational health care', 'Lodging benefits', 'Commuting benefits', 'Occupational accident insurance', 'Public Holidays', 'Annual paid leave']
                },
                jobPostings: dbJobPostings,

                viewJob(index) {
                    const job = this.jobPostings[index];
                    this.viewingJob = job;
                    this.rankedApplicants = this.calculateRanking(job);
                    this.showJobDrawer = true;
                    document.body.style.overflow = 'hidden';
                },

                closeJobDrawer() {
                    this.showJobDrawer = false;
                    this.viewingJob = null;
                    this.rankedApplicants = [];
                    document.body.style.overflow = 'auto';
                },

                calculateRanking(job) {
                    const jobSkills = (job.requiredSkills || []).map(s => s.toLowerCase().trim());
                    if (jobSkills.length === 0) {
                        // No required skills — return applicants with 0% score
                        return allApps
                            .filter(a => a.job_title === job.title)
                            .map(a => ({ ...a, score: 0, matchedSkills: [] }));
                    }

                    // Get applicants for this job title
                    const applicants = allApps.filter(a => a.job_title === job.title);

                    return applicants.map(a => {
                        const applicantSkills = (a.skills || '').split(/[,;]+/).map(s => s.toLowerCase().trim()).filter(Boolean);
                        const matched = [];

                        jobSkills.forEach(reqSkill => {
                            // Check if any applicant skill contains the required skill or vice versa
                            const found = applicantSkills.some(aSkill =>
                                aSkill.includes(reqSkill) || reqSkill.includes(aSkill)
                            );
                            if (found) {
                                // Use the original casing from job posting
                                const origIdx = jobSkills.indexOf(reqSkill);
                                matched.push(job.requiredSkills[origIdx]);
                            }
                        });

                        const score = Math.round((matched.length / jobSkills.length) * 100);
                        return { ...a, score, matchedSkills: matched };
                    }).sort((a, b) => b.score - a.score);
                },

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
                    this.confirmTitle = 'Archive Job Postings';
                    this.confirmMessage = `Are you sure you want to archive ${this.selectedJobIds.length} job posting(s)? They can be restored from the Archived section.`;
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
                            this.successTitle = 'Job Postings Archived';
                            this.successMessage = data.message;
                            this.successRedirectUrl = window.location.href;
                            this.showSuccess = true;
                            this.selectedJobIds = [];
                        } else {
                            window.showErrorDialog('Archive Failed', data.message || 'Failed to archive job postings.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.showErrorDialog('Archive Failed', 'An error occurred while archiving job postings.');
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
                        description: this.defaultDescriptionMap['cleaning'] || '',
                        state: '',
                        city: '',
                        salary: '',
                        type: 'full-time',
                        typeBadge: 'Full-time Employee',
                        category: 'cleaning',
                        icon: 'fa-broom',
                        iconColor: 'green',
                        is_active: true,
                        requiredSkills: [...(this.defaultSkillsMap['cleaning'] || [])],
                        requiredDocs: this.defaultDocs.map(d => ({...d})),
                        benefits: [...this.defaultBenefits]
                    };
                    this.cityOptions = [];
                },

                populateDefaults(categoryValue) {
                    this.formData.requiredSkills = [...(this.defaultSkillsMap[categoryValue] || [])];
                    this.formData.description = this.defaultDescriptionMap[categoryValue] || '';
                    this.formData.requiredDocs = this.defaultDocs.map(d => ({...d}));
                    this.formData.benefits = [...this.defaultBenefits];
                },

                getApplicantCount(job) {
                    return allApps.filter(a => a.job_title === job.title).length;
                },

                get editingHasApplicants() {
                    if (this.editingIndex === null) return false;
                    const job = this.jobPostings[this.editingIndex];
                    return job ? this.getApplicantCount(job) > 0 : false;
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
                        status: job.status || 'published',
                        requiredSkills: job.requiredSkills?.length ? [...job.requiredSkills] : [...(this.defaultSkillsMap[matchedCat.value] || [])],
                        requiredDocs: job.requiredDocs?.length ? job.requiredDocs.map(d => typeof d === 'string' ? {name: d, fileType: 'any'} : {...d}) : this.defaultDocs.map(d => ({...d})),
                        benefits: job.benefits?.length ? [...job.benefits] : [...this.defaultBenefits]
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
                    this.confirmTitle = 'Archive Job Posting';
                    this.confirmMessage = 'Are you sure you want to archive this job posting? It can be restored from the Archived section.';
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
                            this.successTitle = 'Job Posting Archived';
                            this.successMessage = 'The job posting has been moved to archived.';
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

                confirmAction() {
                    this.showConfirm = false;
                    if (this.pendingConfirmAction === 'bulkDeleteJobs') {
                        this.doBulkDeleteJobs();
                    } else if (this.pendingConfirmAction === 'deleteJob') {
                        this.doDeleteJob(this.pendingDeleteIndex);
                    } else if (this.pendingConfirmAction === 'archiveJob') {
                        this.doArchiveJob(this.pendingDeleteIndex);
                    } else if (this.pendingConfirmAction === 'publishDraft') {
                        this.doPublishDraft();
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
                    // When editing, preserve current status; when creating new, publish
                    const status = (this.editingIndex !== null && this.formData.status)
                        ? this.formData.status
                        : 'published';

                    const payload = {
                        title: this.formData.title,
                        description: this.formData.description,
                        location: location,
                        salary: this.formData.salary,
                        type: this.formData.type,
                        type_badge: this.formData.typeBadge,
                        icon: this.formData.icon,
                        icon_color: this.formData.iconColor,
                        is_active: status === 'published' ? this.formData.is_active : false,
                        status: status,
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

                publishDraft() {
                    this.confirmTitle = 'Publish Job Posting';
                    this.confirmMessage = 'Are you sure you want to publish this draft? It will become visible to applicants.';
                    this.pendingConfirmAction = 'publishDraft';
                    this.showConfirm = true;
                },

                async doPublishDraft() {
                    this.formData.status = 'published';
                    this.formData.is_active = true;

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
                        is_active: true,
                        status: 'published',
                        required_skills: requiredSkills,
                        required_docs: requiredDocs,
                        benefits: this.formData.benefits
                    };

                    try {
                        const response = await fetch(`/admin/job-postings/${this.formData.id}`, {
                            method: 'PUT',
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
                                status: data.data.status || 'published',
                                requiredSkills: data.data.required_skills || [],
                                requiredDocs: data.data.required_docs || [],
                                benefits: data.data.benefits || []
                            };

                            this.jobPostings[this.editingIndex] = savedJob;
                            this.closeModal();
                            this.successTitle = 'Job Posting Published';
                            this.successMessage = 'The draft has been published and is now visible to applicants.';
                            this.successRedirectUrl = window.location.href;
                            this.showSuccess = true;
                        } else {
                            window.showErrorDialog('Publish Failed', data.message || 'Failed to publish the job posting.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.showErrorDialog('Publish Failed', 'An error occurred while publishing the job posting.');
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
