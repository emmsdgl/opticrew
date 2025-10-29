<x-layouts.general-employer :title="'Add Employee'">
    <section class="flex flex-col gap-6 p-4 md:p-6 flex-1 max-w-4xl mx-auto">

        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.accounts.index', ['type' => 'employees']) }}"
               class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Add New Employee</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a new employee account</p>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.accounts.store') }}" class="space-y-6" id="employeeForm">
                @csrf

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2">
                    Account Information
                </h3>

                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="email_username" id="email_username" value="{{ old('email_username') }}" required
                               placeholder="john.doe"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <span class="px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium">
                            @finnoys.com
                        </span>
                    </div>
                    <input type="hidden" name="email" id="email" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2 mt-8">
                    Employee Details
                </h3>

                <!-- Skills -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Skills
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="skills[]" value="Driving" {{ is_array(old('skills')) && in_array('Driving', old('skills')) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Driving</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="skills[]" value="Cleaning" {{ is_array(old('skills')) && in_array('Cleaning', old('skills')) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Cleaning</span>
                        </label>
                    </div>
                    @error('skills')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Years of Experience -->
                <div>
                    <label for="years_of_experience" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Years of Experience <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="years_of_experience" id="years_of_experience" value="{{ old('years_of_experience', 0) }}" min="0" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('years_of_experience')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Salary Per Hour -->
                <div>
                    <label for="salary_per_hour" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Salary Per Hour (€) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="salary_per_hour" id="salary_per_hour" value="{{ old('salary_per_hour', 13.00) }}" min="0" step="0.01" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('salary_per_hour')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Efficiency -->
                <div>
                    <label for="efficiency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Efficiency Rating <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500">(0.01 to 9.99)</span>
                    </label>
                    <input type="number" name="efficiency" id="efficiency" value="{{ old('efficiency', 1.00) }}" min="0.01" max="9.99" step="0.01" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('efficiency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Driving License -->
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="has_driving_license" value="1" {{ old('has_driving_license') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Has Driving License</span>
                    </label>
                    @error('has_driving_license')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                            class="flex-1 sm:flex-none px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150">
                        <i class="fas fa-save mr-2"></i>
                        Create Employee
                    </button>
                    <a href="{{ route('admin.accounts.index', ['type' => 'employees']) }}"
                       class="flex-1 sm:flex-none text-center px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition duration-150">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

    </section>

    @push('scripts')
    <script>
        // Combine email username with @finnoys.com before form submission
        document.getElementById('employeeForm').addEventListener('submit', function(e) {
            const emailUsername = document.getElementById('email_username').value.trim();
            const fullEmail = emailUsername + '@finnoys.com';
            document.getElementById('email').value = fullEmail;
        });
    </script>
    @endpush
</x-layouts.general-employer>
