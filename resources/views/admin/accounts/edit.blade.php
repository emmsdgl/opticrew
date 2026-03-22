<x-layouts.general-employer :title="'Edit Account'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">

        <!-- Breadcrumb & Header -->
        <div class="flex flex-col md:items-center md:justify-between gap-4 max-w-4xl w-full mx-auto">
            <div class="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Accounts', 'url' => route('admin.accounts.index')],
                    ['label' => $user->name, 'url' => route('admin.accounts.show', $user->id)],
                    ['label' => 'Edit Account'],
                ]" />
            </div>
            <div class="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Account</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Update user account information.</p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="max-w-4xl w-full mx-auto rounded-lg overflow-hidden">
            <form id="accountEditForm">

                <dl>
                    <!-- Full Name -->
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                            Full name <span class="text-red-500">*</span>
                        </dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </dd>
                    </div>

                    <!-- Username -->
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                            Username <span class="text-red-500">*</span>
                        </dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            @error('username')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </dd>
                    </div>

                    <!-- Email -->
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                            Email address <span class="text-red-500">*</span>
                        </dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            @if($user->google_id && $user->role !== 'employee')
                                {{-- Non-employee Google users: their primary email IS the Google email --}}
                                <input type="email" value="{{ $user->email }}" readonly
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-not-allowed">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <i class="fa-solid fa-lock text-[10px] text-amber-500"></i>
                                    This email is linked to a Google account and cannot be changed
                                </p>
                            @else
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            @endif
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </dd>
                    </div>

                    {{-- Personal email (employees with linked Google account) --}}
                    @if($user->role === 'employee' && $user->google_id && $user->alternative_email)
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                            Personal email (Gmail)
                        </dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            <input type="email" value="{{ $user->alternative_email }}" readonly
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <i class="fa-solid fa-lock text-[10px] text-amber-500"></i>
                                This email is linked to a Google account and cannot be changed
                            </p>
                        </dd>
                    </div>
                    @endif

                    <!-- Phone -->
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                            Phone number <span class="text-red-500">*</span>
                        </dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            @error('phone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </dd>
                    </div>

                    <!-- Role (Read-only) -->
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">Account role</dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            <input type="text" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" readonly
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Role cannot be changed after account creation</p>
                        </dd>
                    </div>
                </dl>

                <!-- Company Details Section (for contracted clients only) -->
                @if($user->contractedClient)
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <div class="py-4 bg-white dark:bg-gray-900 my-12">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Company Details</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Location and business information.</p>
                    </div>

                    <dl>
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Latitude <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="number" step="any" name="latitude" value="{{ old('latitude', $user->contractedClient->latitude) }}" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                @error('latitude')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>

                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Longitude <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="number" step="any" name="longitude" value="{{ old('longitude', $user->contractedClient->longitude) }}" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                @error('longitude')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>

                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white pt-2">Address</dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <textarea name="address" rows="2"
                                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">{{ old('address', $user->contractedClient->address) }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>

                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">Business ID</dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="text" name="business_id" value="{{ old('business_id', $user->contractedClient->business_id) }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                @error('business_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>
                    </dl>
                </div>
                @endif

                <!-- Password Section -->
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <div class="py-4 bg-white dark:bg-gray-900 my-12">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Change Password</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Leave blank to keep the current password.</p>
                    </div>

                    <dl>
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">New password</dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="password" name="password" id="password"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>

                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">Confirm password</dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Submit Buttons -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.accounts.show', $user->id) }}"
                       class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-1.5"></i> Update Account
                    </button>
                </div>
            </form>
        </div>

    </section>

    @push('scripts')
    <script>
        document.getElementById('accountEditForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            try {
                await window.showConfirmDialog(
                    'Update Account',
                    'Are you sure you want to update this account\'s details?',
                    'Update',
                    'Cancel'
                );
            } catch (e) {
                return;
            }

            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            try {
                const response = await fetch("{{ route('admin.accounts.update', $user->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    window.showSuccessDialog(
                        'Account Updated',
                        data.message || 'The account has been updated successfully.',
                        'OK',
                        "{{ route('admin.accounts.show', $user->id) }}"
                    );
                } else {
                    const errors = data.errors
                        ? Object.values(data.errors).flat().join('\n')
                        : (data.message || 'Failed to update account.');
                    window.showErrorDialog('Update Failed', errors);
                }
            } catch (err) {
                window.showErrorDialog('Error', 'Something went wrong. Please try again.');
            }
        });
    </script>
    @endpush
</x-layouts.general-employer>
