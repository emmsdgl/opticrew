<x-layouts.general-employer :title="$accountType === 'company' ? 'Add Company Account' : 'Add Employee'">
    <section class="flex flex-col gap-6 p-4 md:p-6 flex-1 max-w-4xl mx-auto">

        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.accounts.index', ['type' => $accountType === 'company' ? 'contracted_company' : 'employees']) }}"
               class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $accountType === 'company' ? 'Add Company Account' : 'Add New Employee' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $accountType === 'company' ? 'Create account for contracted client company' : 'Create a new employee account' }}
                </p>
            </div>
        </div>

        <!-- Account Type Selector -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Type</label>
            <div class="flex gap-4">
                <a href="{{ route('admin.accounts.create', ['type' => 'employee']) }}"
                   class="px-4 py-2 rounded-lg {{ $accountType === 'employee' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-user mr-2"></i> Employee
                </a>
                <a href="{{ route('admin.accounts.create', ['type' => 'company']) }}"
                   class="px-4 py-2 rounded-lg {{ $accountType === 'company' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-building mr-2"></i> Company
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.accounts.store') }}" class="space-y-6" id="accountForm">
                @csrf
                <input type="hidden" name="account_type" value="{{ $accountType }}">

                @if($accountType === 'company')
                <!-- COMPANY ACCOUNT FORM -->
<div x-data="companyAccountForm({{ json_encode($contractedClients) }})">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2 mb-4">
        Company Information
    </h3>

    <input type="hidden" name="is_existing" :value="selectedCompanyId ? '1' : '0'">
    <input type="hidden" name="existing_company_id" :value="selectedCompanyId">

    <!-- Company Selection Dropdown -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Select Company <span class="text-red-500">*</span>
        </label>
        <select x-model="selectedCompanyId" @change="loadCompanyData()" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="">-- Select Existing or Create New --</option>
            <optgroup label="Existing Companies (without accounts)">
                <template x-for="company in existingCompanies" :key="company.id">
                    <option :value="company.id" x-text="company.name + ' - ' + (company.email || 'No email')"></option>
                </template>
            </optgroup>
            <option value="new">➕ Create New Company</option>
        </select>
        <p class="mt-1 text-xs text-gray-500" x-show="existingCompanies.length === 0">
            <span class="text-orange-600 font-semibold">All existing companies already have accounts. Select "Create New Company"</span>
        </p>
    </div>

    <!-- Company Details Form (Auto-filled or Empty) -->
    <div class="space-y-4">
        <!-- Company Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Company Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="company_name" x-model="formData.name" required
                   :readonly="selectedCompanyId && selectedCompanyId !== 'new'"
                   placeholder="e.g., Kakslauttanen Arctic Resort"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                   :class="selectedCompanyId && selectedCompanyId !== 'new' ? 'bg-gray-100 dark:bg-gray-600' : ''">
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Email Address <span class="text-red-500">*</span>
            </label>
            <input type="email" name="email" x-model="formData.email" required
                   placeholder="contact@company.com"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <p class="mt-1 text-xs text-gray-500" x-show="selectedCompanyId && selectedCompanyId !== 'new' && !formData.email">
                <span class="text-yellow-600">⚠️ Email is missing in database. Please add it.</span>
            </p>
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Phone Number <span class="text-red-500">*</span>
            </label>
            <input type="text" name="phone" x-model="formData.phone" required
                   placeholder="+358 12 345 6789"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>

        <!-- Address -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Address
            </label>
            <textarea name="address" x-model="formData.address" rows="2"
                      placeholder="Company address"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
        </div>

        <!-- Business ID -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Business ID
            </label>
            <input type="text" name="business_id" x-model="formData.business_id"
                   placeholder="1234567-8"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>

        <!-- Coordinates -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Latitude <span class="text-red-500">*</span>
                </label>
                <input type="number" step="any" name="latitude" x-model="formData.latitude" required
                       placeholder="68.4109"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <p class="mt-1 text-xs text-gray-500" x-show="selectedCompanyId && selectedCompanyId !== 'new' && !formData.latitude">
                    <span class="text-yellow-600">⚠️ Missing - Please add</span>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Longitude <span class="text-red-500">*</span>
                </label>
                <input type="number" step="any" name="longitude" x-model="formData.longitude" required
                       placeholder="27.0127"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <p class="mt-1 text-xs text-gray-500" x-show="selectedCompanyId && selectedCompanyId !== 'new' && !formData.longitude">
                    <span class="text-yellow-600">⚠️ Missing - Please add</span>
                </p>
            </div>
        </div>
    </div>

    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b pb-2 mt-6">
        Account Credentials
    </h3>

    <!-- Password -->
    <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Password <span class="text-red-500">*</span>
        </label>
        <input type="password" name="password" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
    </div>

    <!-- Confirm Password -->
    <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Confirm Password <span class="text-red-500">*</span>
        </label>
        <input type="password" name="password_confirmation" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end gap-3 pt-6 mt-6 border-t">
        <a href="{{ route('admin.accounts.index', ['type' => 'contracted_company']) }}"
           class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition">
            Cancel
        </a>
        <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Create Company Account
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
                // Reset form for new company
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
                // Load existing company data
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
