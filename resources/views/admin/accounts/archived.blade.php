<x-layouts.general-employer :title="'Archived Accounts'">
    <section class="flex flex-col gap-6 p-4 md:p-6 flex-1">

        <!-- Header -->
        <div class="flex flex-col gap-2">
            <div class="my-6">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Accounts', 'url' => route('admin.accounts.index')],
                    ['label' => 'Archived Accounts'],
                ]" />
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Archived Accounts</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">View and restore soft-deleted user accounts.</p>
        </div>

        <!-- Stats Cards -->
        <div class="py-6">
            <x-employer-components.stats-cards :stats="[
                ['label' => 'Total Archived', 'value' => $employeesCount + $companyCount + $personalCount, 'icon' => 'fi fi-rr-archive', 'iconColor' => '#f59e0b'],
                ['label' => 'Employees', 'value' => $employeesCount, 'icon' => 'fi fi-rr-user', 'iconColor' => '#3b82f6'],
                ['label' => 'Company Clients', 'value' => $companyCount, 'icon' => 'fi fi-rr-building', 'iconColor' => '#8b5cf6'],
                ['label' => 'Personal Clients', 'value' => $personalCount, 'icon' => 'fi fi-rr-portrait', 'iconColor' => '#6b7280'],
            ]" />
        </div>

        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Search Bar -->
            <div class="flex-1">
                <div class="relative">
                    <input type="text" id="searchInput"
                           placeholder="Search by name, username, or email..."
                           class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Account Type Filter -->
            <div class="w-full md:w-auto">
                <x-filter-dropdown label="Filter by Account Type" :selected="$accountType" :options="[
                    'all' => 'All Archived (' . ($employeesCount + $companyCount + $personalCount) . ')',
                    'employees' => 'Employees (' . $employeesCount . ')',
                    'company' => 'Company Clients (' . $companyCount . ')',
                    'personal' => 'Personal Clients (' . $personalCount . ')',
                ]"
                    onSelect="window.location.href='{{ route('admin.accounts.archived') }}?type={value}'" />
            </div>
        </div>

        <!-- Accounts Table -->
        @if($users->count() > 0)
            <div x-data="{
                selectedIds: [],
                allIds: @json($users->pluck('id')->toArray()),
                get allSelected() {
                    return this.allIds.length > 0 && this.selectedIds.length === this.allIds.length;
                },
                toggleAll(event) {
                    this.selectedIds = event.target.checked ? [...this.allIds] : [];
                },
                toggleOne(id) {
                    const idx = this.selectedIds.indexOf(id);
                    if (idx > -1) { this.selectedIds.splice(idx, 1); } else { this.selectedIds.push(id); }
                },
                deselectAll() { this.selectedIds = []; }
            }">

            <!-- Bulk Actions Bar -->
            <div x-show="selectedIds.length > 0" x-transition
                class="flex flex-row justify-between items-center gap-3 px-4 py-3 mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300"
                    x-text="selectedIds.length + ' selected'"></span>
                <div class="flex flex-row gap-3">
                    <button @click="bulkRestore(selectedIds)"
                        class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                        <i class="fa-solid fa-rotate-left mr-1"></i>Restore Selected
                    </button>
                    <button @click="bulkPermanentDelete(selectedIds)"
                        class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        <i class="fa-solid fa-trash mr-1"></i>Delete Selected
                    </button>
                    <button @click="deselectAll()"
                        class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Deselect All
                    </button>
                </div>
            </div>

            <div class="w-full rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-4 w-10">
                                <input type="checkbox" @change="toggleAll($event)" :checked="allSelected"
                                    class="appearance-none w-4 h-4 rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 checked:bg-blue-600 checked:border-blue-600 checked:bg-[url('data:image/svg+xml,%3Csvg%20viewBox%3D%220%200%2016%2016%22%20fill%3D%22white%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12.207%204.793a1%201%200%20010%201.414l-5%205a1%201%200%2001-1.414%200l-2-2a1%201%200%20011.414-1.414L6.5%209.086l4.293-4.293a1%201%200%20011.414%200z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-colors">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Title</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Deleted</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php
                                if ($user->role === 'employee') {
                                    $title = $user->employee->position ?? 'Employee';
                                    $subtitle = $user->username ? '@' . $user->username : '';
                                } elseif ($user->client && $user->client->client_type === 'company') {
                                    $title = 'Company Account';
                                    $subtitle = $user->username ? '@' . $user->username : '';
                                } else {
                                    $title = 'Personal Account';
                                    $subtitle = $user->username ? '@' . $user->username : '';
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

                                <!-- Checkbox -->
                                <td class="px-4 py-4 w-10">
                                    <input type="checkbox" value="{{ $user->id }}"
                                        @change="toggleOne({{ $user->id }})"
                                        :checked="selectedIds.includes({{ $user->id }})"
                                        class="appearance-none w-4 h-4 rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 checked:bg-blue-600 checked:border-blue-600 checked:bg-[url('data:image/svg+xml,%3Csvg%20viewBox%3D%220%200%2016%2016%22%20fill%3D%22white%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12.207%204.793a1%201%200%20010%201.414l-5%205a1%201%200%2001-1.414%200l-2-2a1%201%200%20011.414-1.414L6.5%209.086l4.293-4.293a1%201%200%20011.414%200z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-colors">
                                </td>

                                <!-- Name -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</span>
                                                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">Archived</span>
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Title -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $title }}</div>
                                    @if($subtitle)
                                        <div class="text-xs text-blue-600 dark:text-blue-400">{{ $subtitle }}</div>
                                    @endif
                                </td>

                                <!-- Role -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $roleBadge }}">{{ $role }}</span>
                                </td>

                                <!-- Deleted Date -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200">{{ $user->deleted_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->deleted_at->format('h:i A') }}</div>
                                </td>

                                <!-- Action -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="restoreAccount({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            class="text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors" title="Restore">
                                            <i class="fa-solid fa-rotate-left text-sm"></i>
                                        </button>
                                        <button onclick="confirmPermanentDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Permanently Delete">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            @if($users->hasPages())
                <div class="mt-4">
                    <x-pagination :paginator="$users" />
                </div>
            @endif
            </div>
        @else
            <div class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                <i class="fa-solid fa-archive text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No archived accounts found</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Deleted accounts will appear here</p>
            </div>
        @endif
    </section>

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

        // Restore account with confirm + success dialogs
        async function restoreAccount(userId, userName) {
            try {
                await window.showConfirmDialog(
                    'Restore Account',
                    `Are you sure you want to restore the account for "${userName}"? The user will be able to log in again.`,
                    'Restore',
                    'Cancel'
                );
            } catch (e) {
                return;
            }

            try {
                const res = await fetch(`/admin/accounts/${userId}/restore`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    window.showSuccessDialog(
                        'Account Restored',
                        `The account for "${userName}" has been restored successfully.`,
                        'OK',
                        '{{ route("admin.accounts.archived") }}'
                    );
                } else {
                    window.showErrorDialog('Restore Failed', data.message || 'Failed to restore the account. Please try again.');
                }
            } catch (error) {
                window.showErrorDialog('Error', 'Something went wrong. Please try again.');
            }
        }

        // Permanent delete with password confirmation + success dialogs
        async function confirmPermanentDelete(userId, userName) {
            let password;

            try {
                password = await window.showPasswordConfirmDialog(
                    'Permanent Deletion',
                    `This will PERMANENTLY delete the account for "${userName}" and ALL associated data. This action CANNOT be undone. Enter your admin password to confirm.`,
                    'Delete Forever',
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

                // Password verified — proceed with permanent delete
                const deleteRes = await fetch(`/admin/accounts/${userId}/force-delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                const data = await deleteRes.json();

                if (deleteRes.ok && data.success) {
                    window.showSuccessDialog(
                        'Account Deleted',
                        `The account for "${userName}" has been permanently deleted.`,
                        'OK',
                        '{{ route("admin.accounts.archived") }}'
                    );
                } else {
                    window.showErrorDialog('Delete Failed', data.message || 'Failed to delete the account. Please try again.');
                }
            } catch (error) {
                window.showErrorDialog('Error', 'Something went wrong. Please try again.');
            }
        }
        // Bulk restore selected accounts
        async function bulkRestore(ids) {
            try {
                await window.showConfirmDialog(
                    'Restore Accounts',
                    `Are you sure you want to restore ${ids.length} account(s)? They will be able to log in again.`,
                    'Restore All',
                    'Cancel'
                );
            } catch (e) {
                return;
            }

            try {
                let successCount = 0;
                for (const id of ids) {
                    const res = await fetch(`/admin/accounts/${id}/restore`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    });
                    if (res.ok) successCount++;
                }

                window.showSuccessDialog(
                    'Accounts Restored',
                    `${successCount} account(s) have been restored successfully.`,
                    'OK',
                    '{{ route("admin.accounts.archived") }}'
                );
            } catch (error) {
                window.showErrorDialog('Error', 'Something went wrong. Please try again.');
            }
        }

        // Bulk permanent delete selected accounts (with password)
        async function bulkPermanentDelete(ids) {
            let password;

            try {
                password = await window.showPasswordConfirmDialog(
                    'Permanent Deletion',
                    `This will PERMANENTLY delete ${ids.length} account(s) and ALL associated data. This action CANNOT be undone. Enter your admin password to confirm.`,
                    'Delete Forever',
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

                // Password verified — proceed with deletions
                let successCount = 0;
                for (const id of ids) {
                    const res = await fetch(`/admin/accounts/${id}/force-delete`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    });
                    if (res.ok) successCount++;
                }

                window.showSuccessDialog(
                    'Accounts Deleted',
                    `${successCount} account(s) have been permanently deleted.`,
                    'OK',
                    '{{ route("admin.accounts.archived") }}'
                );
            } catch (error) {
                window.showErrorDialog('Error', 'Something went wrong. Please try again.');
            }
        }
    </script>
</x-layouts.general-employer>
