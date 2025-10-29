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
                        Role
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
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $user->role === 'employee' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
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
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    {{ $user->role === 'employee' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                </span>
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
