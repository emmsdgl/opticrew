<x-layouts.general-employer :title="'View Account'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">

        <!-- Breadcrumb & Header -->
        <div class="flex flex-col md:items-center md:justify-between gap-4 max-w-4xl w-full mx-auto">
            <div class="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Accounts', 'url' => route('admin.accounts.index')],
                    ['label' => 'Account Details'],
                ]" />
            </div>
            <div class="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Account Information</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Personal details and account.</p>
                </div>
                <a href="{{ route('admin.accounts.edit', $user->id) }}"
                   class="px-4 py-2.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-1.5"></i> Edit
                </a>
            </div>
        </div>

        <!-- Account Details Card -->
        <div class="max-w-4xl w-full mx-auto rounded-lg overflow-hidden">
            <dl>
                <!-- Full name -->
                <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-gray-50 dark:bg-gray-800/50">
                    <dt class="text-sm font-semibold text-gray-900 dark:text-white">Full name</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->name }}</dd>
                </div>
                <!-- Username -->
                <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-white dark:bg-gray-900">
                    <dt class="text-sm font-semibold text-gray-900 dark:text-white">Username</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ '@' . $user->username }}</dd>
                </div>
                <!-- Email -->
                <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-gray-50 dark:bg-gray-800/50">
                    <dt class="text-sm font-semibold text-gray-900 dark:text-white">Email address</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->email }}</dd>
                </div>
                <!-- Phone -->
                <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-white dark:bg-gray-900">
                    <dt class="text-sm font-semibold text-gray-900 dark:text-white">Phone number</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->phone }}</dd>
                </div>
                <!-- Role -->
                <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-gray-50 dark:bg-gray-800/50">
                    <dt class="text-sm font-semibold text-gray-900 dark:text-white">Account role</dt>
                    <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </dd>
                </div>
                <!-- Verification Status -->
                <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-white dark:bg-gray-900">
                    <dt class="text-sm font-semibold text-gray-900 dark:text-white">Verification status</dt>
                    <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                <i class="fas fa-check-circle mr-1"></i> Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @endif
                    </dd>
                </div>
                <!-- Created Date -->
                <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-gray-50 dark:bg-gray-800/50">
                    <dt class="text-sm font-semibold text-gray-900 dark:text-white">Created date</dt>
                    <dd class="mt-1 sm:col-span-2 sm:mt-0">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $user->created_at->format('M d, Y') }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $user->created_at->format('h:i A') }}</span>
                    </dd>
                </div>
                <!-- Last Updated -->
                <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-white dark:bg-gray-900">
                    <dt class="text-sm font-semibold text-gray-900 dark:text-white">Last updated</dt>
                    <dd class="mt-1 sm:col-span-2 sm:mt-0">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $user->updated_at->format('M d, Y') }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $user->updated_at->format('h:i A') }}</span>
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Company Details Section (for contracted clients only) -->
        @if($user->contractedClient)
        <div class="max-w-4xl w-full mx-auto">
            <div class="flex flex-col gap-2 mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Company Details</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Location and business information.</p>
            </div>
            <div class="rounded-lg overflow-hidden">
                <dl>
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-gray-50 dark:bg-gray-800/50">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">Latitude</dt>
                        <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->contractedClient->latitude ?? 'N/A' }}</dd>
                    </div>
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-white dark:bg-gray-900">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">Longitude</dt>
                        <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->contractedClient->longitude ?? 'N/A' }}</dd>
                    </div>
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-gray-50 dark:bg-gray-800/50">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">Address</dt>
                        <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->contractedClient->address ?: 'N/A' }}</dd>
                    </div>
                    <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 bg-white dark:bg-gray-900">
                        <dt class="text-sm font-semibold text-gray-900 dark:text-white">Business ID</dt>
                        <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->contractedClient->business_id ?: 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Cabins/Locations Management -->
        <div class="max-w-4xl w-full mx-auto" x-data="cabinTypesManager({{ $user->id }}, {{ json_encode($cabinTypes) }})">
            <div class="flex flex-row items-center justify-between mb-4">
                <div class="flex flex-col gap-2">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Cabins / Locations</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage cabin types and rates.</p>
                </div>
                <button @click="openAddModal" type="button"
                        class="px-4 py-2 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-1.5"></i> Add Cabin Type
                </button>
            </div>

            <!-- Cabin Types Table -->
            <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Cabin Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Units</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Normal Rate</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Student Rate</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Sunday/Holiday</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Est. Time</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="cabinType in cabinTypes" :key="cabinType.location_type">
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="cabinType.location_type"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="cabinType.units"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="'€' + parseFloat(cabinType.normal_rate_per_hour).toFixed(2) + '/hr'"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="cabinType.student_rate ? '€' + parseFloat(cabinType.student_rate).toFixed(2) + '/hr' : 'N/A'"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="'€' + parseFloat(cabinType.sunday_holiday_rate).toFixed(2) + '/hr'"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="cabinType.base_cleaning_duration_minutes + ' mins'"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEditModal(cabinType)" type="button"
                                                class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button @click="deleteCabinType(cabinType.location_type)" type="button"
                                                class="p-1.5 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
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
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                           :class="editingCabinType ? 'bg-gray-100 dark:bg-gray-600' : ''">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Number of Units <span class="text-red-500">*</span></label>
                                    <input type="number" x-model="formData.units" required min="1"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Normal Rate (€/hr) <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" x-model="formData.normal_rate_per_hour" required
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student Rate (€/hr)</label>
                                    <input type="number" step="0.01" x-model="formData.student_rate"
                                           placeholder="Optional"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sunday/Holiday Rate (€/hr) <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" x-model="formData.sunday_holiday_rate" required
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student Sunday/Holiday Rate (€/hr)</label>
                                    <input type="number" step="0.01" x-model="formData.student_sunday_holiday_rate"
                                           placeholder="Optional"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deep Cleaning Rate (€/hr)</label>
                                    <input type="number" step="0.01" x-model="formData.deep_cleaning_rate"
                                           placeholder="Optional"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Light Deep Cleaning Rate (€/hr)</label>
                                    <input type="number" step="0.01" x-model="formData.light_deep_cleaning_rate"
                                           placeholder="Optional"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Est. Cleaning Time (minutes) <span class="text-red-500">*</span></label>
                                    <input type="number" x-model="formData.base_cleaning_duration_minutes" required min="1"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" @click="closeModal"
                                        class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg transition-colors">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-save mr-1.5"></i>
                                    <span x-text="editingCabinType ? 'Update' : 'Add'"></span> Cabin Type
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

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
                            window.location.reload();
                        } else {
                            window.showErrorDialog('Save Failed', data.message || 'Error saving cabin type');
                        }
                    } catch (error) {
                        window.showErrorDialog('Save Failed', 'Error saving cabin type');
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
                            window.showSuccessDialog('Cabin Type Deleted', data.message);
                        } else {
                            window.showErrorDialog('Delete Failed', data.message || 'Error deleting cabin type');
                        }
                    } catch (error) {
                        window.showErrorDialog('Delete Failed', 'Error deleting cabin type');
                        console.error(error);
                    }
                }
            };
        }
    </script>
    @endpush
    @endif
</x-layouts.general-employer>
