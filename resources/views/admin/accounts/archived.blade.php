<x-layouts.general-employer :title="'User Accounts'">
    <section class="flex flex-col gap-6 p-4 md:p-6 flex-1">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Archived Accounts</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View and restore soft-deleted user accounts</p>
            </div>
            <a href="{{ route('admin.accounts.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Active Accounts
            </a>
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
                        onchange="window.location.href='{{ route('admin.accounts.archived') }}?type=' + this.value"
                        class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="all" {{ $accountType === 'all' ? 'selected' : '' }}>
                        All Archived ({{ $employeesCount + $companyCount + $personalCount }})
                    </option>
                    <option value="employees" {{ $accountType === 'employees' ? 'selected' : '' }}>
                        Archived Employees ({{ $employeesCount }})
                    </option>
                    <option value="company" {{ $accountType === 'company' ? 'selected' : '' }}>
                        Archived Companies ({{ $companyCount }})
                    </option>
                    <option value="personal" {{ $accountType === 'personal' ? 'selected' : '' }}>
                        Archived Personal ({{ $personalCount }})
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
                                            <form action="{{ route('admin.accounts.restore', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="text-green-600 hover:text-green-900 dark:text-green-400" title="Restore Account">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            <button onclick="confirmPermanentDelete({{ $user->id }})"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400" title="Permanently Delete">
                                                <i class="fas fa-trash-alt"></i>
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
                            <form action="{{ route('admin.accounts.restore', $user->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full text-center px-3 py-2 bg-green-50 text-green-600 rounded-lg text-sm font-medium hover:bg-green-100">
                                    <i class="fas fa-undo mr-1"></i> Restore
                                </button>
                            </form>
                            <button onclick="confirmPermanentDelete({{ $user->id }})"
                                    class="flex-1 text-center px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100">
                                <i class="fas fa-trash-alt mr-1"></i> Delete Forever
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

    <!-- Permanent Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4">Permanent Deletion Warning</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        This will PERMANENTLY delete this account and ALL associated data. This action CANNOT be undone!
                    </p>
                    <p class="text-xs text-red-600 font-semibold">
                        Are you absolutely sure?
                    </p>
                </div>
                <div class="flex gap-4 px-4 py-3">
                    <button onclick="closeDeleteModal()"
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                            Delete Forever
                        </button>
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

        // Permanent delete modal functions
        function confirmPermanentDelete(userId) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            form.action = `/admin/accounts/${userId}/force-delete`;
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</x-layouts.general-employer>
