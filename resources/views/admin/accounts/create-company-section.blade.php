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
