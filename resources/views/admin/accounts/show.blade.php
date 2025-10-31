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
                @if($user->employee || $user->client || $user->contractedClient)
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

                        @if($user->contractedClient)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Company ID:</span> {{ $user->contractedClient->id }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                <span class="font-medium">Business ID:</span> {{ $user->contractedClient->business_id ?? 'N/A' }}
                            </p>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Company Details Section (for contracted clients only) -->
                @if($user->contractedClient)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-building text-blue-600 mr-2"></i>
                        Company Details
                    </h3>
                    <form method="POST" action="{{ route('admin.accounts.update-details', $user->id) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Latitude <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="any" name="latitude"
                                       value="{{ old('latitude', $user->contractedClient->latitude) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Longitude <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="any" name="longitude"
                                       value="{{ old('longitude', $user->contractedClient->longitude) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                            <textarea name="address" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('address', $user->contractedClient->address) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business ID</label>
                            <input type="text" name="business_id"
                                   value="{{ old('business_id', $user->contractedClient->business_id) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                <i class="fas fa-save mr-2"></i> Update Details
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Locations/Cabins Management -->
                <div class="mt-6" x-data="cabinTypesManager({{ $user->id }}, {{ json_encode($cabinTypes) }})">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                            Cabins/Locations Management
                        </h3>
                        <button @click="openAddModal" type="button"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg">
                            <i class="fas fa-plus mr-2"></i> Add Cabin Type
                        </button>
                    </div>

                    <!-- Cabin Types Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cabin Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Units</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Normal Rate (€/hr)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Rate (€/hr)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sunday/Holiday (€/hr)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Est. Time (mins)</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="cabinType in cabinTypes" :key="cabinType.location_type">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="cabinType.location_type"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="cabinType.units"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="'€' + parseFloat(cabinType.normal_rate_per_hour).toFixed(2)"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="cabinType.student_rate ? '€' + parseFloat(cabinType.student_rate).toFixed(2) : 'N/A'"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="'€' + parseFloat(cabinType.sunday_holiday_rate).toFixed(2)"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="cabinType.base_cleaning_duration_minutes"></td>
                                        <td class="px-4 py-3 text-sm text-right space-x-2">
                                            <button @click="openEditModal(cabinType)" type="button"
                                                    class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button @click="deleteCabinType(cabinType.location_type)" type="button"
                                                    class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Add/Edit Modal -->
                    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-black opacity-50" @click="closeModal"></div>
                            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-3xl w-full p-6 max-h-[90vh] overflow-y-auto">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4" x-text="editingCabinType ? 'Edit Cabin Type' : 'Add New Cabin Type'"></h4>

                                <form @submit.prevent="saveCabinType" class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cabin Type Name <span class="text-red-500">*</span></label>
                                            <input type="text" x-model="formData.location_type" required
                                                   :readonly="editingCabinType"
                                                   placeholder="e.g., Small Cabin"
                                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                   :class="editingCabinType ? 'bg-gray-100 dark:bg-gray-600' : ''">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Number of Units <span class="text-red-500">*</span></label>
                                            <input type="number" x-model="formData.units" required min="1"
                                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Normal Rate (€/hr) <span class="text-red-500">*</span></label>
                                            <input type="number" step="0.01" x-model="formData.normal_rate_per_hour" required
                                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student Rate (€/hr)</label>
                                            <input type="number" step="0.01" x-model="formData.student_rate"
                                                   placeholder="Optional"
                                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sunday/Holiday Rate (€/hr) <span class="text-red-500">*</span></label>
                                            <input type="number" step="0.01" x-model="formData.sunday_holiday_rate" required
                                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student Sunday/Holiday Rate (€/hr)</label>
                                            <input type="number" step="0.01" x-model="formData.student_sunday_holiday_rate"
                                                   placeholder="Optional"
                                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deep Cleaning Rate (€/hr)</label>
                                            <input type="number" step="0.01" x-model="formData.deep_cleaning_rate"
                                                   placeholder="Optional"
                                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Light Deep Cleaning Rate (€/hr)</label>
                                            <input type="number" step="0.01" x-model="formData.light_deep_cleaning_rate"
                                                   placeholder="Optional"
                                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Est. Cleaning Time (minutes) <span class="text-red-500">*</span></label>
                                            <input type="number" x-model="formData.base_cleaning_duration_minutes" required min="1"
                                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-3 mt-6">
                                        <button type="button" @click="closeModal"
                                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                            <i class="fas fa-save mr-2"></i>
                                            <span x-text="editingCabinType ? 'Update' : 'Add'"></span> Cabin Type
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </section>

    @if($user->contractedClient)
    @push('scripts')
    <script>
        function cabinTypesManager(userId, initialCabinTypes) {
            return {
                cabinTypes: initialCabinTypes,
                showModal: false,
                editingCabinType: null,
                formData: {},

                openAddModal() {
                    this.editingCabinType = null;
                    this.formData = {
                        location_type: '',
                        units: '',
                        normal_rate_per_hour: '',
                        student_rate: '',
                        sunday_holiday_rate: '',
                        student_sunday_holiday_rate: '',
                        deep_cleaning_rate: '',
                        light_deep_cleaning_rate: '',
                        base_cleaning_duration_minutes: ''
                    };
                    this.showModal = true;
                },

                openEditModal(cabinType) {
                    this.editingCabinType = cabinType;
                    this.formData = { ...cabinType };
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                    this.editingCabinType = null;
                },

                async saveCabinType() {
                    const url = this.editingCabinType
                        ? `/admin/accounts/${userId}/cabin-types/${encodeURIComponent(this.editingCabinType.location_type)}`
                        : `/admin/accounts/${userId}/cabin-types`;

                    const method = this.editingCabinType ? 'PUT' : 'POST';

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Reload the page to reflect changes
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error saving cabin type');
                        }
                    } catch (error) {
                        alert('Error saving cabin type');
                        console.error(error);
                    }
                },

                async deleteCabinType(locationType) {
                    if (!confirm(`Are you sure you want to delete all "${locationType}" cabins? This will remove all units of this type.`)) return;

                    try {
                        const response = await fetch(`/admin/accounts/${userId}/cabin-types/${encodeURIComponent(locationType)}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.cabinTypes = this.cabinTypes.filter(ct => ct.location_type !== locationType);
                            alert(data.message);
                        } else {
                            alert(data.message || 'Error deleting cabin type');
                        }
                    } catch (error) {
                        alert('Error deleting cabin type');
                        console.error(error);
                    }
                }
            };
        }
    </script>
    @endpush
    @endif
</x-layouts.general-employer>
