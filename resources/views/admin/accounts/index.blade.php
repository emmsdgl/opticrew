<x-layouts.general-employer :title="'User Accounts'">
    <section class="flex flex-col gap-6 p-4 md:p-6 flex-1">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">User Accounts</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage all user accounts in the system</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.accounts.archived') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                    <i class="fas fa-archive mr-2"></i>
                    View Archived
                </a>
                <a href="{{ route('admin.accounts.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                    <i class="fas fa-plus mr-2"></i>
                    Add Employee
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Search Bar -->
            <div class="flex-1">
                <div class="relative">
                    <input type="text" id="searchInput"
                           placeholder="Search by name, username, or email..."
                           class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Account Type Filter -->
            <div class="w-full md:w-64">
                <select id="accountTypeFilter"
                        onchange="window.location.href='{{ route('admin.accounts.index') }}?type=' + this.value"
                        class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="all" {{ $accountType === 'all' ? 'selected' : '' }}>
                        All Accounts ({{ $employeesCount + $companyCount + $personalCount }})
                    </option>
                    <option value="employees" {{ $accountType === 'employees' ? 'selected' : '' }}>
                        Employee Accounts ({{ $employeesCount }})
                    </option>
                    <option value="company" {{ $accountType === 'company' ? 'selected' : '' }}>
                        Company Accounts ({{ $companyCount }})
                    </option>
                    <option value="personal" {{ $accountType === 'personal' ? 'selected' : '' }}>
                        Personal/Private Accounts ({{ $personalCount }})
                    </option>
                </select>
            </div>
        </div>

        <!-- Accounts Table/Cards -->
        @if($users->count() > 0)
            <!-- Desktop Table View (Hidden on mobile) -->
            <div class="hidden md:block bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    User
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Username
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Contact
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    data-name="{{ strtolower($user->name) }}"
                                    data-username="{{ strtolower($user->username) }}"
                                    data-email="{{ strtolower($user->email) }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $user->username }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $user->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->role === 'employee')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Employee
                                            </span>
                                        @elseif($user->client && $user->client->client_type === 'company')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Company
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Personal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $user->email_verified_at ? 'Active' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.accounts.show', $user->id) }}"
                                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.accounts.edit', $user->id) }}"
                                               class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="confirmDelete({{ $user->id }})"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View (Hidden on desktop) -->
            <div class="md:hidden space-y-4">
                @foreach($users as $user)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4"
                         data-name="{{ strtolower($user->name) }}"
                         data-username="{{ strtolower($user->username) }}"
                         data-email="{{ strtolower($user->email) }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-lg">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">@{{ $user->username }}</p>
                                </div>
                            </div>
                            @if($user->role === 'employee')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Employee
                                </span>
                            @elseif($user->client && $user->client->client_type === 'company')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Company
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    Personal
                                </span>
                            @endif
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-envelope w-5"></i>
                                <span>{{ $user->email }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-phone w-5"></i>
                                <span>{{ $user->phone }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-circle w-5 {{ $user->email_verified_at ? 'text-green-500' : 'text-yellow-500' }}"></i>
                                <span class="text-gray-600 dark:text-gray-400">
                                    {{ $user->email_verified_at ? 'Active' : 'Pending' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.accounts.show', $user->id) }}"
                               class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                            <a href="{{ route('admin.accounts.edit', $user->id) }}"
                               class="flex-1 text-center px-3 py-2 bg-yellow-50 text-yellow-600 rounded-lg text-sm font-medium hover:bg-yellow-100">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <button onclick="confirmDelete({{ $user->id }})"
                                    class="flex-1 text-center px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <p class="text-lg font-medium text-gray-500 dark:text-gray-400">No accounts found</p>
                <p class="text-sm text-gray-400 mt-1">Try adjusting your filters or add a new account</p>
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
                            <label for="admin_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
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
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</x-layouts.general-employer>
