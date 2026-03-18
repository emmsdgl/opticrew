<x-layouts.general-employer :title="'Employee Account Setup'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">

        <!-- Breadcrumb & Header -->
        <div class="flex flex-col md:items-center md:justify-between gap-4 max-w-4xl w-full mx-auto">
            <div class="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Recruitment', 'url' => route('admin.recruitment.index')],
                    ['label' => 'Application #' . $application->id, 'url' => route('admin.recruitment.show', $application->id)],
                    ['label' => 'Employee Account Setup'],
                ]" />
            </div>
            <div class="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Employee Account Setup
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Create an employee account for the hired applicant. Their Google/Gmail account will be linked automatically.
                    </p>
                </div>
            </div>
        </div>

        <!-- Applicant Info Summary -->
        <div class="max-w-4xl w-full mx-auto">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 flex items-start gap-4">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-green-600 text-xl mt-0.5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-green-800 dark:text-green-300">Applicant Details</h3>
                    <p class="text-sm text-green-700 dark:text-green-400 mt-1">
                        <strong>{{ $profile['first_name'] ?? '' }} {{ $profile['last_name'] ?? '' }}</strong>
                        &mdash; {{ $application->email }}
                        &mdash; Applied for <strong>{{ $application->job_title }}</strong>
                    </p>
                    <p class="text-xs text-green-600 dark:text-green-500 mt-1">
                        <i class="fa-solid fa-info-circle mr-1"></i> The applicant will be marked as hired once this employee account is successfully created.
                    </p>
                    @if($applicantUser && $applicantUser->google_id)
                    <p class="text-xs text-green-600 dark:text-green-500 mt-1">
                        <i class="fa-brands fa-google mr-1"></i> Google account linked ({{ $applicantUser->email }})
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Validation Errors -->
        @if($errors->any())
        <div class="max-w-4xl w-full mx-auto">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Form -->
        <div class="max-w-4xl w-full mx-auto rounded-lg overflow-hidden">
            <form method="POST" action="{{ route('admin.recruitment.store-employee', $application->id) }}" id="employeeSetupForm">
                @csrf

                <!-- Account Information Section -->
                <div class="px-6 py-4 bg-white dark:bg-gray-900">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Account Information</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Set up the employee's Fin-noys login credentials. Their personal Gmail will be preserved for Google login.</p>
                </div>

                <dl>
                    <!-- Full Name -->
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                            Full name <span class="text-red-500">*</span>
                        </dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            @php
                                $defaultName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
                                if (!$defaultName && $applicantUser) $defaultName = $applicantUser->name;
                            @endphp
                            <input type="text" name="name" id="name" value="{{ old('name', $defaultName) }}" required
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
                            @php
                                $suggestedUsername = strtolower(str_replace(' ', '.', $defaultName));
                            @endphp
                            <input type="text" name="username" id="username" value="{{ old('username', $suggestedUsername) }}" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            @error('username')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </dd>
                    </div>

                    <!-- Fin-noys Email -->
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                            Fin-noys email <span class="text-red-500">*</span>
                        </dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            <div class="flex items-center gap-2">
                                @php
                                    $suggestedEmailUser = strtolower(str_replace(' ', '.', $defaultName));
                                @endphp
                                <input type="text" name="email_username" id="email_username" value="{{ old('email_username', $suggestedEmailUser) }}" required
                                       placeholder="john.doe"
                                       class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <span class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium">
                                    @finnoys.com
                                </span>
                            </div>
                            @error('email_username')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-info-circle mr-1"></i>
                                Their personal email ({{ $application->email }}) will be stored as an alternative email for Google login.
                            </p>
                        </dd>
                    </div>

                    <!-- Phone -->
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                            Phone number <span class="text-red-500">*</span>
                        </dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            @php
                                $defaultPhone = $profile['phone'] ?? ($applicantUser->phone ?? '');
                            @endphp
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $defaultPhone) }}" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            @error('phone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </dd>
                    </div>
                </dl>

                <!-- Password Section -->
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 bg-white dark:bg-gray-900">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Password</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Set the Fin-noys account password for this employee.</p>
                    </div>

                    <dl>
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Password <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="password" name="password" id="password" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>

                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Confirm password <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Employee Details Section -->
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 bg-white dark:bg-gray-900">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Employee Details</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Additional employee information and skills.</p>
                    </div>

                    <dl>
                        <!-- Skills -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">Skills</dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <div class="flex gap-6">
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
                                @if(!empty($profile['skills']))
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    Resume skills: {{ $profile['skills'] }}
                                </p>
                                @endif
                            </dd>
                        </div>

                        <!-- Years of Experience -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Years of experience <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="number" name="years_of_experience" id="years_of_experience" value="{{ old('years_of_experience', 0) }}" min="0" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                @error('years_of_experience')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>

                        <!-- Salary Per Hour -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Salary per hour <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">&#8364;</span>
                                    <input type="number" name="salary_per_hour" id="salary_per_hour" value="{{ old('salary_per_hour', 13.00) }}" min="0" step="0.01" required
                                           class="w-full pl-7 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                </div>
                                @error('salary_per_hour')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>

                        <!-- Efficiency -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Efficiency rating <span class="text-red-500">*</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-normal mt-0.5">0.01 to 9.99</p>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="number" name="efficiency" id="efficiency" value="{{ old('efficiency', 1.00) }}" min="0.01" max="9.99" step="0.01" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                @error('efficiency')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </dd>
                        </div>

                        <!-- Driving License -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">Driving license</dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="has_driving_license" value="1" {{ old('has_driving_license') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Has driving license</span>
                                </label>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Submit Buttons -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.recruitment.show', $application->id) }}"
                       class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-user-plus mr-1.5"></i> Create Employee Account
                    </button>
                </div>
            </form>
        </div>

    </section>
</x-layouts.general-employer>
