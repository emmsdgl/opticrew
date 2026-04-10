<x-layouts.general-employer :title="$accountType === 'company' ? 'Add Company Account' : 'Add Employee'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">

        <!-- Breadcrumb & Header -->
        <div class="flex flex-col md:items-center md:justify-between gap-4 max-w-4xl w-full mx-auto">
            <div class="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Accounts', 'url' => route('admin.accounts.index')],
                    ['label' => $accountType === 'company' ? 'Add Company Account' : 'Add New Employee'],
                ]" />
            </div>
            <div class="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $accountType === 'company' ? 'Add Company Account' : 'Add New Employee' }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $accountType === 'company' ? 'Create account for contracted client company.' : 'Create a new employee account.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Account Type Selector -->
        <div class="max-w-4xl w-full mx-auto">
            <div class="flex gap-3">
                <a href="{{ route('admin.accounts.create', ['type' => 'employee']) }}"
                   class="px-4 py-2 text-xs font-medium rounded-lg transition-colors {{ $accountType === 'employee' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                    <i class="fas fa-user mr-1.5"></i> Employee
                </a>
                <a href="{{ route('admin.accounts.create', ['type' => 'company']) }}"
                   class="px-4 py-2 text-xs font-medium rounded-lg transition-colors {{ $accountType === 'company' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                    <i class="fas fa-building mr-1.5"></i> Company
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="max-w-4xl w-full mx-auto rounded-lg overflow-hidden">
            <form method="POST" action="{{ route('admin.accounts.store') }}" id="accountForm">
                @csrf
                <input type="hidden" name="account_type" value="{{ $accountType }}">

                @if($accountType === 'company')
                <!-- COMPANY ACCOUNT FORM -->
                <div x-data="companyAccountForm({{ json_encode($contractedClients) }})">
                    <input type="hidden" name="is_existing" :value="selectedCompanyId ? '1' : '0'">
                    <input type="hidden" name="existing_company_id" :value="selectedCompanyId">

                    <!-- Company Information Section Header -->
                    <div class="px-6 py-4 bg-white dark:bg-gray-900">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Company Information</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Select an existing company or create a new one.</p>
                    </div>

                    <dl>
                        <!-- Company Selection -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Select company <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <select x-model="selectedCompanyId" @change="loadCompanyData()" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    <option value="">-- Select Existing or Create New --</option>
                                    <optgroup label="Existing Companies (without accounts)">
                                        <template x-for="company in existingCompanies" :key="company.id">
                                            <option :value="company.id" x-text="company.name + ' - ' + (company.email || 'No email')"></option>
                                        </template>
                                    </optgroup>
                                    <option value="new">+ Create New Company</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500" x-show="existingCompanies.length === 0">
                                    <span class="text-orange-600 font-semibold">All existing companies already have accounts. Select "Create New Company"</span>
                                </p>
                            </dd>
                        </div>

                        <!-- Company Name -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Company name <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="text" name="company_name" x-model="formData.name" required
                                       :readonly="selectedCompanyId && selectedCompanyId !== 'new'"
                                       placeholder="e.g., Kakslauttanen Arctic Resort"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                       :class="selectedCompanyId && selectedCompanyId !== 'new' ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : ''">
                            </dd>
                        </div>

                        <!-- Email -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Email address <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="email" name="email" x-model="formData.email" required
                                       placeholder="contact@company.com"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-yellow-600" x-show="selectedCompanyId && selectedCompanyId !== 'new' && !formData.email">
                                    Email is missing in database. Please add it.
                                </p>
                            </dd>
                        </div>

                        <!-- Phone -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Phone number <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="text" name="phone" x-model="formData.phone" required
                                       placeholder="+358 12 345 6789"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </dd>
                        </div>

                        <!-- Address -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white pt-2">Address</dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <textarea name="address" x-model="formData.address" rows="2"
                                          placeholder="Company address"
                                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"></textarea>
                            </dd>
                        </div>

                        <!-- Business ID -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">Business ID</dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="text" name="business_id" x-model="formData.business_id"
                                       placeholder="1234567-8"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </dd>
                        </div>

                        <!-- Latitude -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Latitude <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="number" step="any" name="latitude" x-model="formData.latitude" required
                                       placeholder="68.4109"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-yellow-600" x-show="selectedCompanyId && selectedCompanyId !== 'new' && !formData.latitude">
                                    Missing - Please add
                                </p>
                            </dd>
                        </div>

                        <!-- Longitude -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                Longitude <span class="text-red-500">*</span>
                            </dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <input type="number" step="any" name="longitude" x-model="formData.longitude" required
                                       placeholder="27.0127"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-yellow-600" x-show="selectedCompanyId && selectedCompanyId !== 'new' && !formData.longitude">
                                    Missing - Please add
                                </p>
                            </dd>
                        </div>
                    </dl>

                    <!-- Account Credentials Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 bg-white dark:bg-gray-900">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Account Credentials</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Set the login password for this account.</p>
                        </div>

                        <dl>
                            <!-- Password -->
                            <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                                <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Password <span class="text-red-500">*</span>
                                </dt>
                                <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                    <input type="password" name="password" required
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                </dd>
                            </div>

                            <!-- Confirm Password -->
                            <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                                <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Confirm password <span class="text-red-500">*</span>
                                </dt>
                                <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                    <input type="password" name="password_confirmation" required
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.accounts.index', ['type' => 'contracted_company']) }}"
                           class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-1.5"></i> Create Company Account
                        </button>
                    </div>
                </div>

                <script>
                function companyAccountForm(companies) {
                    return {
                        allCompanies: companies,
                        selectedCompanyId: '',
                        formData: {
                            name: '',
                            email: '',
                            phone: '',
                            address: '',
                            business_id: '',
                            latitude: '',
                            longitude: ''
                        },

                        get existingCompanies() {
                            return this.allCompanies.filter(c => !c.user_id);
                        },

                        loadCompanyData() {
                            if (this.selectedCompanyId === 'new') {
                                this.formData = {
                                    name: '',
                                    email: '',
                                    phone: '',
                                    address: '',
                                    business_id: '',
                                    latitude: '',
                                    longitude: ''
                                };
                            } else if (this.selectedCompanyId) {
                                const company = this.allCompanies.find(c => c.id == this.selectedCompanyId);
                                if (company) {
                                    this.formData = {
                                        name: company.name || '',
                                        email: company.email || '',
                                        phone: company.phone || '',
                                        address: company.address || '',
                                        business_id: company.business_id || '',
                                        latitude: company.latitude || '',
                                        longitude: company.longitude || ''
                                    };
                                }
                            }
                        }
                    };
                }
                </script>

                @else
                <!-- EMPLOYEE ACCOUNT FORM -->

                <!-- Account Information Section Header -->
                <div class="px-6 py-4 bg-white dark:bg-gray-900">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Account Information</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Set up the employee's login credentials.</p>
                </div>

                <dl>
                    <!-- Full Name -->
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                            Full name <span class="text-red-500">*</span>
                        </dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
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
                            <input type="text" name="username" id="username" value="{{ old('username') }}" required
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
                            <div class="flex items-center gap-2">
                                <input type="text" name="email_username" id="email_username" value="{{ old('email_username') }}" required
                                       placeholder="john.doe"
                                       class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <span class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium">
                                    @finnoys.com
                                </span>
                            </div>
                            <input type="hidden" name="email" id="email" value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </dd>
                    </div>

                    <!-- Phone -->
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-white dark:bg-gray-900">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">
                            Phone number <span class="text-red-500">*</span>
                        </dt>
                        <dd class="mt-1 sm:col-span-2 sm:mt-0">
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
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
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Set the login password for this account.</p>
                    </div>

                    <dl>
                        <!-- Password -->
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

                        <!-- Confirm Password -->
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
                        <!-- Skills & Driving License -->
                        <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center bg-gray-50 dark:bg-gray-800/50">
                            <dt class="text-sm font-semibold text-gray-900 dark:text-white">Skills</dt>
                            <dd class="mt-1 sm:col-span-2 sm:mt-0">
                                <div class="flex items-center gap-4">
                                    {{-- Cleaning is always a default skill --}}
                                    <input type="hidden" name="skills[]" value="Cleaning">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400 cursor-default grayscale">
                                        Cleaning
                                    </span>

                                    {{-- Driving license checkbox --}}
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="has_driving_license" value="1" {{ old('has_driving_license') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Has driving license</span>
                                    </label>
                                </div>
                                @error('skills')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                @error('has_driving_license')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
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

                    </dl>
                </div>

                <!-- Submit Buttons -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.accounts.index', ['type' => 'employees']) }}"
                       class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-1.5"></i> Create Employee
                    </button>
                </div>

                @endif
            </form>
        </div>

    </section>

    @push('scripts')
    <script>
        // Combine email username with @finnoys.com before form submission
        const form = document.getElementById('accountForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const emailUsername = document.getElementById('email_username');
                const emailField = document.getElementById('email');
                if (emailUsername && emailField) {
                    const fullEmail = emailUsername.value.trim() + '@finnoys.com';
                    emailField.value = fullEmail;
                }
            });
        }
    </script>
    @endpush
</x-layouts.general-employer>
