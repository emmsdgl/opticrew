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
                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition duration-150 ease-in-out whitespace-nowrap">
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

        <!-- Success Message -->
        @if(session('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- KPI Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-statisticscard
                title="All Accounts"
                :value="$employeesCount + $contractedCompanyCount + $companyCount + $personalCount"
                trend="up"
                trendValue="+ 3.4 %"
                trendLabel="users growth vs last month" />

            <x-statisticscard
                title="Active Accounts"
                :value="$users->where('email_verified_at', '!=', null)->count()"
                trend="up"
                trendValue="+ 3.4 %"
                trendLabel="activity growth vs last month" />

            <x-statisticscard
                title="Archived Accounts"
                :value="0"
                subtitle="Deleted accounts that are no longer..." />
        </div>

        <!-- Users Section Header with Filters -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 my-4">
            <div>
                <x-labelwithvalue label="All Users" :count="''" />
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

        <!-- Accounts Table/Cards -->

        @php
            $tableHeaders = [
                ['label' => 'Name', 'align' => 'left'],
                ['label' => 'Title', 'align' => 'left'],
                ['label' => 'Status', 'align' => 'left'],
                ['label' => 'Role', 'align' => 'left'],
                ['label' => '', 'align' => 'right'],
            ];

            $tableRows = [];
            foreach ($users as $user) {
                // Determine title
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

                // Determine role
                if ($user->role === 'employee') {
                    $role = 'Employee';
                } elseif ($user->client && $user->client->client_type === 'company') {
                    $role = 'External Client';
                } else {
                    $role = 'Employer';
                }

                $tableRows[] = [
                    'attributes' => [
                        'data-name' => strtolower($user->name),
                        'data-username' => strtolower($user->username),
                        'data-email' => strtolower($user->email),
                    ],
                    'columns' => [
                        // Name Column
                        '<div class="flex items-center gap-3">
                                                                <div class="flex-shrink-0">
                                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                                                        ' . strtoupper(substr($user->name, 0, 2)) . '
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">' . e($user->name) . '</div>
                                                                    <div class="text-xs text-gray-500 dark:text-slate-400">' . e($user->email) . '</div>
                                                                </div>
                                                            </div>',

                        // Title Column
                        '<div class="text-sm font-medium text-gray-900 dark:text-white">' . e($title) . '</div>
                                                            <div class="text-xs text-blue-600 dark:text-blue-400">' . e($subtitle) . '</div>',

                        // Status Column
                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' .
                        ($user->email_verified_at ? 'bg-emerald-500/10 text-emerald-400' : 'bg-yellow-500/10 text-yellow-400') . '">
                                                                ' . ($user->email_verified_at ? 'Active' : 'Pending') . '
                                                            </span>',

                        // Role Column
                        '<span class="text-sm text-gray-700 dark:text-slate-300 font-medium">' . e($role) . '</span>',

                        // Actions Column
                        '<div class="relative" x-data="{ open: false }">
                                                                <button @click="open = !open" @click.away="open = false"
                                                                    class="text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-300 transition-colors p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800/50">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                                                                    </svg>
                                                                </button>

                                                                <div x-show="open" x-cloak
                                                                    x-transition:enter="transition ease-out duration-100"
                                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                                    x-transition:leave="transition ease-in duration-75"
                                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                                    class="absolute right-0 mt-2 w-auto min-w-[120px] bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 py-1 z-10"
                                                                    style="display: none;">
                                                                    <a href="' . route('admin.accounts.show', $user->id) . '"
                                                                       class="flex items-center justify-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white transition-colors whitespace-nowrap">
                                                                        <i class="fas fa-eye w-4 text-center"></i>
                                                                        <span>View</span>
                                                                    </a>
                                                                    <a href="' . route('admin.accounts.edit', $user->id) . '"
                                                                       class="flex items-center justify-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 hover:text-gray-900 dark:hover:text-white transition-colors whitespace-nowrap">
                                                                        <i class="fas fa-edit w-4 text-center"></i>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                    <button onclick="confirmDelete(' . $user->id . ')"
                                                                            class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-slate-700 hover:text-red-700 dark:hover:text-red-300 transition-colors whitespace-nowrap">
                                                                        <i class="fas fa-trash w-4 text-center"></i>
                                                                        <span>Delete</span>
                                                                    </button>
                                                                </div>
                                                            </div>',
                    ],
                    'mobile' => '
                                                            <div class="flex items-start justify-between mb-4">
                                                                <div class="flex items-center gap-3">
                                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                                                        ' . strtoupper(substr($user->name, 0, 2)) . '
                                                                    </div>
                                                                    <div>
                                                                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">' . e($user->name) . '</h3>
                                                                        <p class="text-xs text-gray-500 dark:text-slate-400">' . e($user->email) . '</p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="space-y-2 mb-4">
                                                                <div class="flex justify-between items-center">
                                                                    <span class="text-xs text-gray-500 dark:text-slate-400">Title</span>
                                                                    <div class="text-right">
                                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">' . e($title) . '</div>
                                                                        <div class="text-xs text-blue-600 dark:text-blue-400">' . e($subtitle) . '</div>
                                                                    </div>
                                                                </div>

                                                                <div class="flex justify-between items-center">
                                                                    <span class="text-xs text-gray-500 dark:text-slate-400">Status</span>
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' .
                        ($user->email_verified_at ? 'bg-emerald-500/10 text-emerald-400' : 'bg-yellow-500/10 text-yellow-400') . '">
                                                                        ' . ($user->email_verified_at ? 'Active' : 'Pending') . '
                                                                    </span>
                                                                </div>

                                                                <div class="flex justify-between items-center">
                                                                    <span class="text-xs text-gray-500 dark:text-slate-400">Role</span>
                                                                    <span class="text-sm text-gray-700 dark:text-slate-300 font-medium">' . e($role) . '</span>
                                                                </div>
                                                            </div>

                                                            <div class="pt-3 border-t border-gray-200 dark:border-slate-800">
                                                                <div class="flex gap-2">
                                                                    <a href="' . route('admin.accounts.show', $user->id) . '"
                                                                       class="flex-1 text-center px-3 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white text-sm font-medium rounded-lg transition-colors">
                                                                        <i class="fas fa-eye mr-1"></i> View
                                                                    </a>
                                                                    <a href="' . route('admin.accounts.edit', $user->id) . '"
                                                                       class="flex-1 text-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                                        <i class="fas fa-edit mr-1"></i> Edit
                                                                    </a>
                                                                    <button onclick="confirmDelete(' . $user->id . ')"
                                                                            class="flex-1 text-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                                        <i class="fas fa-trash mr-1"></i> Delete
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        ',
                ];
            }
        @endphp

        <x-employer-components.data-table :headers="$tableHeaders" :rows="$tableRows" emptyTitle="No accounts found"
            emptyMessage="Try adjusting your filters or add a new account" />

        @if($users->count() > 0)
            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
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
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4 text-center">Delete Account
                </h3>
                <div class="mt-2 px-4 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">
                        This account will be soft-deleted and can be restored later. Enter your admin password to
                        confirm.
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
        // Search functionality
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
                    // Password is correct, submit the delete form
                    form.submit();
                } else {
                    // Password is incorrect
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