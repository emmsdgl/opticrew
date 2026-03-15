<x-layouts.general-employer :title="'User Accounts'">
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
                                    $subtitle = $user->employee->department ?? 'N/A';
                                } elseif ($user->client && $user->client->client_type === 'company') {
                                    $title = 'Company Account';
                                    $subtitle = ucfirst($user->client->client_type);
                                } else {
                                    $title = 'Personal Account';
                                    $subtitle = $user->client ? ucfirst($user->client->client_type) : 'User';
                                }

                                if ($user->role === 'employee') {
                                    $role = 'Employee';
                                    $roleBadge = 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400';
                                } elseif ($user->role === 'applicant') {
                                    $role = 'Applicant';
                                    $roleBadge = 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400';
                                } elseif ($user->client && $user->client->client_type === 'company') {
                                    $role = 'External Client';
                                    $roleBadge = 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400';
                                } else {
                                    $role = 'Employer';
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
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
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
                                    @if($user->email_verified_at)
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400">Active</span>
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
                                    <div class="flex items-center justify-end gap-3">
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
                                        <button onclick="confirmDelete({{ $user->id }})"
                                            class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                            title="Delete">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
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
                <table class="w-full min-w-[700px]" id="applicantsTable">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Applicant</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Provider</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Registered</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Applications</th>
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
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $applicant->name }}</div>
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
                                    @if($applicant->email_verified_at)
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400">Active</span>
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
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $appCount > 0 ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                        {{ $appCount }} {{ Str::plural('application', $appCount) }}
                                    </span>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4 text-center">Delete Account</h3>
                <div class="mt-2 px-4 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">
                        This account will be soft-deleted and can be restored later. Enter your admin password to confirm.
                    </p>

                    <form id="deleteForm" method="POST" onsubmit="return validatePassword(event)">
                        @csrf
                        @method('DELETE')

                        <div class="mb-4">
                            <label for="admin_password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Admin Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="admin_password" name="admin_password" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Enter your password">
                            <p id="password_error" class="mt-1 text-sm text-red-600 hidden"></p>
                        </div>

                        <div class="flex gap-4">
                            <button type="button" onclick="closeDeleteModal()"
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        // Delete modal functions
        function confirmDelete(userId) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const passwordInput = document.getElementById('admin_password');
            const errorMsg = document.getElementById('password_error');

            form.action = `/admin/accounts/${userId}`;
            passwordInput.value = '';
            errorMsg.classList.add('hidden');
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            const passwordInput = document.getElementById('admin_password');
            const errorMsg = document.getElementById('password_error');

            passwordInput.value = '';
            errorMsg.classList.add('hidden');
            modal.classList.add('hidden');
        }

        async function validatePassword(event) {
            event.preventDefault();
            const password = document.getElementById('admin_password').value;
            const errorMsg = document.getElementById('password_error');
            const form = document.getElementById('deleteForm');

            try {
                const response = await fetch('/admin/accounts/verify-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ password: password })
                });

                const data = await response.json();

                if (data.valid) {
                    form.submit();
                } else {
                    errorMsg.textContent = 'Incorrect password. Please try again.';
                    errorMsg.classList.remove('hidden');
                }
            } catch (error) {
                errorMsg.textContent = 'An error occurred. Please try again.';
                errorMsg.classList.remove('hidden');
            }

            return false;
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</x-layouts.general-employer>
