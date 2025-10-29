<x-layouts.general-employer :title="'View Account'">
    <section class="flex flex-col gap-6 p-4 md:p-6 flex-1 max-w-4xl mx-auto">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.accounts.index') }}"
                   class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Account Details</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View user account information</p>
                </div>
            </div>
            <a href="{{ route('admin.accounts.edit', $user->id) }}"
               class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition duration-150">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
        </div>

        <!-- Account Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                <div class="flex items-center gap-4">
                    <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center text-blue-600 font-bold text-3xl">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="text-white">
                        <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                        <p class="text-blue-100">{{ '@' . $user->username }}</p>
                        <div class="mt-2">
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Section -->
            <div class="p-6 space-y-6">
                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-address-card text-blue-600 mr-2"></i>
                        Contact Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-envelope text-gray-400 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Email Address</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-phone text-gray-400 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Phone Number</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Status -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Account Status
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-check-circle {{ $user->email_verified_at ? 'text-green-500' : 'text-yellow-500' }} mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Verification Status</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-calendar text-gray-400 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Created Date</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $user->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-clock text-gray-400 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Last Updated</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $user->updated_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                @if($user->employee || $user->client)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-user-tag text-blue-600 mr-2"></i>
                        Additional Information
                    </h3>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        @if($user->employee)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Employee ID:</span> {{ $user->employee->id }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                <span class="font-medium">Full Name:</span> {{ $user->employee->first_name }} {{ $user->employee->last_name }}
                            </p>
                        @endif
                        @if($user->client)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Client ID:</span> {{ $user->client->id }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                <span class="font-medium">Client Type:</span> {{ ucfirst($user->client->client_type) }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                <span class="font-medium">Status:</span>
                                <span class="{{ $user->client->is_active ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                    {{ $user->client->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

    </section>
</x-layouts.general-employer>
