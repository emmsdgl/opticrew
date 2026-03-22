<x-layouts.general-employer :title="'User Accounts'">
    <x-skeleton-page :preset="'stats-table'">
    <section class="flex flex-col gap-6 p-4 px-12 md:p-6 flex-1">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <x-labelwithvalue label="Account Summary" :count="''" />
            </div>
            <div class="flex gap-2 flex-wrap">
                <!-- View Archived Button -->
                <a href="{{ route('admin.accounts.archived') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-blue-950 hover:bg-gray-200 hover:text-blue-600 dark:text-white dark:bg-gray-800 text-xs font-medium rounded-lg transition duration-150 ease-in-out whitespace-nowrap">
                    <i class="fas fa-archive mr-2"></i>
                    View Archived
                </a>

                <!-- Add User Dropdown -->
                <x-action-dropdown label="Add User" icon='<i class="fas fa-plus mr-2"></i>'>
                    <x-action-dropdown-item href="{{ route('admin.accounts.create', ['type' => 'company']) }}"
                        icon="fas fa-building">
                        Company
                    </x-action-dropdown-item>
                    <x-action-dropdown-item href="{{ route('admin.accounts.create') }}" icon="fas fa-user">
                        Employee
                    </x-action-dropdown-item>
                </x-action-dropdown>
            </div>
        </div>

        <!-- Success Dialog -->
        @if(session('success'))
            <div x-data="{ showSuccess: true, successTitle: '', successMessage: '', successButtonText: '' }">
                <x-employer-components.success-dialog
                    title="Action Completed"
                    message="{{ session('success') }}"
                    buttonText="Continue"
                    buttonUrl="{{ route('admin.accounts.index') }}" />
            </div>
        @endif

        <!-- KPI Statistics -->
        <div class="py-6">
            <x-employer-components.stats-cards :stats="[
                ['label' => 'All Accounts', 'value' => $employeesCount + $contractedCompanyCount + $companyCount + $personalCount, 'subtitle' => 'users growth vs last month', 'icon' => 'fi fi-rr-users-alt', 'iconColor' => '#3b82f6'],
                ['label' => 'Active Accounts', 'value' => $users->where('email_verified_at', '!=', null)->count(), 'subtitle' => 'activity growth vs last month', 'icon' => 'fi fi-rr-check-circle', 'iconColor' => '#22c55e'],
                ['label' => 'Registered Applicants', 'value' => $applicantsCount, 'subtitle' => 'Applicants registered via Google', 'icon' => 'fi fi-rr-user-add', 'iconColor' => '#8b5cf6'],
                ['label' => 'Archived Accounts', 'value' => 0, 'subtitle' => 'Deleted accounts that are no longer active', 'icon' => 'fi fi-rr-archive', 'iconColor' => '#f59e0b'],
            ]" />
        </div>

        <!-- Users Section Header with Filters -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 my-4">
            <div>
                <x-labelwithvalue label="Company Users" :count="''" />
            </div>

            <!-- Filters -->
            <div class="flex flex-col md:flex-row gap-4 flex-1 md:max-w-3xl">
                <!-- Search Bar -->
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search by name, username, or email..."
                            class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-2 md:gap-4">
                    <!-- Account Type Filter -->
                    <div class="w-full md:w-auto">
                        <x-filter-dropdown label="Filter by Account Type" :selected="$accountType" :options="[
                            'all' => 'All Accounts (' . ($employeesCount + $contractedCompanyCount + $companyCount + $personalCount) . ')',
                            'employees' => 'Employee Accounts (' . $employeesCount . ')',
                            'contracted_company' => 'Contracted Company Accounts (' . $contractedCompanyCount . ')',
                            'company' => 'External Company Accounts (' . $companyCount . ')',
                            'personal' => 'Personal/Private Accounts (' . $personalCount . ')',
                            'applicants' => 'Applicant Accounts (' . $applicantsCount . ')',
                        ]"
                            onSelect="window.location.href='{{ route('admin.accounts.index') }}?type={value}'" />
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="w-full md:w-auto">
                        <x-dropdown label="Sort by:" default="Latest" :options="[
                            'latest' => 'Latest',
                            'oldest' => 'Oldest',
                            'name_asc' => 'Name (A-Z)',
                            'name_desc' => 'Name (Z-A)',
                            'role_asc' => 'Role (A-Z)',
                            'role_desc' => 'Role (Z-A)'
                        ]" id="sort-dropdown" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Accounts Table (Recruitment-style) -->
        @if($users->count() > 0)
            <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full min-w-[900px]">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Title</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Joined</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php
                                if ($user->role === 'employee') {
                                    $title = $user->employee->position ?? 'Employee';
                                    $subtitle = $user->username ? '@' . $user->username : '@';
                                } elseif ($user->client && $user->client->client_type === 'company') {
                                    $title = 'Company Account';
                                    $subtitle = $user->username ? '@' . $user->username : '@';
                                } else {
                                    $title = 'Personal Account';
                                    $subtitle = $user->username ? '@' . $user->username : '@';
                                }

                                if ($user->role === 'employee') {
                                    $role = 'Employee';
                                    $roleBadge = 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400';
                                } elseif ($user->role === 'applicant') {
                                    $role = 'Applicant';
                                    $roleBadge = 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400';
                                } elseif ($user->role === 'company') {
                                    $role = 'Company Client';
                                    $roleBadge = 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400';
                                } else {
                                    $role = 'Personal Client';
                                    $roleBadge = 'bg-gray-100 text-gray-700 dark:bg-gray-900/20 dark:text-gray-400';
                                }
                            @endphp
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800/50"
                                data-name="{{ strtolower($user->name) }}"
                                data-username="{{ strtolower($user->username ?? '') }}"
                                data-email="{{ strtolower($user->email) }}">

                                <!-- Name -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold {{ !$user->is_active ? 'text-red-500 line-through dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $user->name }}</span>
                                                @if(!$user->is_active)
                                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">BANNED</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Title -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $title }}</div>
                                    <div class="text-xs text-blue-600 dark:text-blue-400">{{ $subtitle }}</div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->google_id)
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400">Verified</span>
                                    @elseif($user->email_verified_at)
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">Not Linked</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400">Pending</span>
                                    @endif
                                </td>

                                <!-- Role -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $roleBadge }}">{{ $role }}</span>
                                </td>

                                <!-- Joined -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200">{{ $user->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->created_at->format('h:i A') }}</div>
                                </td>

                                <!-- Action -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2" x-data="{ open: false }">
                                        <a href="{{ route('admin.accounts.show', $user->id) }}"
                                            class="text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors"
                                            title="View">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('admin.accounts.edit', $user->id) }}"
                                            class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                            title="Edit">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </a>

                                        <!-- More Actions Dropdown -->
                                        <div class="relative">
                                            <button @click="open = !open" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" title="More actions">
                                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-transition
                                                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 py-1">

                                                @if($user->role !== 'admin')
                                                    <!-- Change Role -->
                                                    <button @click="open = false; openChangeRoleModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->role }}')"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                                        <i class="fa-solid fa-user-tag text-xs text-blue-500"></i> Change Role
                                                    </button>

                                                    <!-- Ban/Unban -->
                                                    <button @click="open = false; toggleBanUser({{ $user->id }}, '{{ $user->name }}', {{ $user->is_active ? 'true' : 'false' }})"
                                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 {{ $user->is_active ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                                        <i class="fa-solid {{ $user->is_active ? 'fa-ban' : 'fa-check-circle' }} text-xs"></i>
                                                        {{ $user->is_active ? 'Ban User' : 'Unban User' }}
                                                    </button>

                                                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                                @endif

                                                <!-- Delete -->
                                                <button @click="open = false; confirmDelete({{ $user->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2">
                                                    <i class="fa-solid fa-trash text-xs"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <x-pagination :paginator="$users" />
                    </div>
                @endif
            </div>
        @else
            <div class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                <i class="fa-solid fa-inbox text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No accounts found</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters or add a new account</p>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- Registered Applicants Table                                     --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mt-8 mb-2">
            <div>
                <x-labelwithvalue label="Registered Applicants" count="{{ $applicantsCount }}" />
            </div>
            <div class="flex-1 max-w-xs">
                <div class="relative">
                    <input type="text" id="applicantSearchInput" placeholder="Search applicants..."
                        class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
        </div>

        @if($applicants->count() > 0)
            <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full min-w-[800px]" id="applicantsTable">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Applicant</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Provider</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Registered</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Applications</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applicants as $applicant)
                            @php
                                $appCount = \App\Models\JobApplication::where('email', $applicant->email)->count();
                            @endphp
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800/50 applicant-row"
                                data-applicant-name="{{ strtolower($applicant->name) }}"
                                data-applicant-email="{{ strtolower($applicant->email) }}">

                                <!-- Applicant -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            @if($applicant->profile_picture)
                                                <img src="{{ $applicant->profile_picture }}" alt="" class="h-10 w-10 rounded-full object-cover shadow-md">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                                    {{ strtoupper(substr($applicant->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold {{ !$applicant->is_active ? 'text-red-500 line-through dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $applicant->name }}</span>
                                                @if(!$applicant->is_active)
                                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">BANNED</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $applicant->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Provider -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($applicant->google_id)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24"><path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                            Google
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Email</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($applicant->google_id)
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400">Verified</span>
                                    @elseif($applicant->email_verified_at)
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">Not Linked</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400">Pending</span>
                                    @endif
                                </td>

                                <!-- Registered -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200">{{ $applicant->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $applicant->created_at->format('h:i A') }}</div>
                                </td>

                                <!-- Applications Count -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $appCount > 0 ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                        {{ $appCount }} {{ Str::plural('application', $appCount) }}
                                    </span>
                                </td>

                                <!-- Action -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2" x-data="{ open: false }">
                                        <a href="{{ route('admin.accounts.show', $applicant->id) }}"
                                            class="text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors"
                                            title="View">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </a>

                                        <!-- More Actions Dropdown -->
                                        <div class="relative">
                                            <button @click="open = !open" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" title="More actions">
                                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-transition
                                                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 py-1">

                                                <!-- Change Role -->
                                                <button @click="open = false; openChangeRoleModal({{ $applicant->id }}, '{{ $applicant->name }}', '{{ $applicant->role }}')"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                                    <i class="fa-solid fa-user-tag text-xs text-blue-500"></i> Change Role
                                                </button>

                                                <!-- Ban/Unban -->
                                                <button @click="open = false; toggleBanUser({{ $applicant->id }}, '{{ $applicant->name }}', {{ $applicant->is_active ? 'true' : 'false' }})"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 {{ $applicant->is_active ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                                    <i class="fa-solid {{ $applicant->is_active ? 'fa-ban' : 'fa-check-circle' }} text-xs"></i>
                                                    {{ $applicant->is_active ? 'Ban User' : 'Unban User' }}
                                                </button>

                                                <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

                                                <!-- Delete -->
                                                <button @click="open = false; confirmDelete({{ $applicant->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2">
                                                    <i class="fa-solid fa-trash text-xs"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($applicants->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <x-pagination :paginator="$applicants" />
                    </div>
                @endif
            </div>
        @else
            <div class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-16 text-center">
                <i class="fa-solid fa-user-plus text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No registered applicants yet</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Applicants who sign up via Google will appear here</p>
            </div>
        @endif

    </section>
    </x-skeleton-page>

    {{-- Change Role Modal --}}
    <div id="changeRoleModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Change Role</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Changing role for <span id="changeRoleUserName" class="font-medium text-gray-900 dark:text-white"></span>
            </p>

            <input type="hidden" id="changeRoleUserId">
            <input type="hidden" id="changeRoleCurrentRole">

            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Role</label>
            <select id="changeRoleSelect"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </select>

            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeChangeRoleModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                    Cancel
                </button>
                <button onclick="submitChangeRole()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <script>
        // Change Role functions
        const roleTransitions = {
            employee: [{ value: 'external_client', label: 'Personal Client' }],
            external_client: [{ value: 'employee', label: 'Employee' }],
            applicant: [{ value: 'employee', label: 'Employee' }],
        };

        function openChangeRoleModal(userId, userName, currentRole) {
            document.getElementById('changeRoleUserId').value = userId;
            document.getElementById('changeRoleUserName').textContent = userName;
            document.getElementById('changeRoleCurrentRole').value = currentRole;

            const select = document.getElementById('changeRoleSelect');
            select.innerHTML = '';

            const options = roleTransitions[currentRole] || [];
            if (options.length === 0) {
                select.innerHTML = '<option disabled>No role changes available</option>';
            } else {
                options.forEach(opt => {
                    const el = document.createElement('option');
                    el.value = opt.value;
                    el.textContent = opt.label;
                    select.appendChild(el);
                });
            }

            const modal = document.getElementById('changeRoleModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeChangeRoleModal() {
            const modal = document.getElementById('changeRoleModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        async function submitChangeRole() {
            const userId = document.getElementById('changeRoleUserId').value;
            const newRole = document.getElementById('changeRoleSelect').value;

            if (!newRole) return;

            try {
                const res = await fetch(`/admin/accounts/${userId}/change-role`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ new_role: newRole })
                });

                const data = await res.json();

                if (res.ok) {
                    closeChangeRoleModal();
                    window.showSuccessDialog('Role Changed', data.message, 'OK', '{{ route("admin.accounts.index") }}');
                } else {
                    window.showErrorDialog('Error', data.message || 'Failed to change role.');
                }
            } catch (error) {
                window.showErrorDialog('Error', 'Something went wrong. Please try again.');
            }
        }

        // Search functionality for main users table
        const searchInput = document.getElementById('searchInput');

        function filterAccounts() {
            const searchTerm = searchInput.value.toLowerCase();
            const allRows = document.querySelectorAll('[data-name]');

            allRows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const username = row.getAttribute('data-username') || '';
                const email = row.getAttribute('data-email') || '';

                const matchesSearch = name.includes(searchTerm) ||
                    username.includes(searchTerm) ||
                    email.includes(searchTerm);

                row.style.display = matchesSearch ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterAccounts);

        // Search functionality for applicants table
        const applicantSearchInput = document.getElementById('applicantSearchInput');
        if (applicantSearchInput) {
            applicantSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('.applicant-row').forEach(row => {
                    const name = row.getAttribute('data-applicant-name') || '';
                    const email = row.getAttribute('data-applicant-email') || '';
                    row.style.display = (name.includes(searchTerm) || email.includes(searchTerm)) ? '' : 'none';
                });
            });
        }

        // Delete flow using components/dialog
        async function confirmDelete(userId) {
            let password;

            try {
                password = await window.showPasswordConfirmDialog(
                    'Delete Account',
                    'This account will be soft-deleted and can be restored later. Enter your admin password to confirm.',
                    'Delete Account',
                    'Cancel'
                );
            } catch (e) {
                return;
            }

            try {
                // Verify password
                const verifyRes = await fetch('/admin/accounts/verify-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ password: password })
                });

                const verifyData = await verifyRes.json();

                if (!verifyData.valid) {
                    window.showErrorDialog('Verification Failed', 'Incorrect password. Please try again.');
                    return;
                }

                // Password verified — proceed with delete
                const deleteRes = await fetch(`/admin/accounts/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                if (deleteRes.ok) {
                    window.showSuccessDialog(
                        'Account Deleted',
                        'The account has been soft-deleted successfully and can be restored later.',
                        'OK',
                        '{{ route("admin.accounts.index") }}'
                    );
                } else {
                    const errData = await deleteRes.json().catch(() => ({}));
                    window.showErrorDialog('Delete Failed', errData.message || 'Failed to delete the account. Please try again.');
                }
            } catch (error) {
                window.showErrorDialog('Error', 'Something went wrong. Please try again.');
            }
        }
    </script>
</x-layouts.general-employer>
