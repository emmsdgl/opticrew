<x-layouts.general-employee :title="'Edit Profile'">
    <x-skeleton-page :preset="'profile'">
    <section class="flex w-full flex-col p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <div class="max-w-4xl mx-auto w-full">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Profile</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Update your personal information and profile picture</p>
            </div>

            <!-- Profile Picture Upload -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Picture</h2>

                <div class="flex items-center space-x-6">
                    <!-- Current Profile Picture -->
                    <div class="relative">
                        @if($user->profile_picture)
                            <img id="profilePicturePreview"
                                 src="{{ asset($user->profile_picture) }}"
                                 alt="Profile Picture"
                                 class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700">
                        @else
                            @php
                                $editNameParts = explode(' ', trim($user->name ?? ''));
                                $editInitials = strtoupper(substr($editNameParts[0] ?? '', 0, 1) . substr(end($editNameParts) ?: '', 0, 1));
                            @endphp
                            <div id="profilePicturePreview" class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-4 border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                <span class="text-white font-bold text-3xl">{{ $editInitials }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Upload Form -->
                    <div class="flex-1">
                        <form id="pictureForm" class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Choose new picture
                                </label>
                                <input type="file"
                                       name="profile_picture"
                                       id="profilePictureInput"
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-lg file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100
                                              dark:file:bg-blue-900 dark:file:text-blue-200
                                              dark:hover:file:bg-blue-800"
                                       required>
                            </div>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                Upload Picture
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">JPG, PNG or GIF. Max size 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- Profile Information Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>

                <form id="profileForm" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Full Name
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-normal focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Username
                        </label>
                        <input type="text"
                               name="username"
                               id="username"
                               value="{{ old('username', $user->username) }}"
                               placeholder="Enter username"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-normal focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email Address
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-normal focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <!-- Phone -->
                    <div x-data="phoneInput('{{ old('phone', $user->phone) }}')" x-init="init()">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Phone Number
                        </label>
                        <input type="hidden" name="phone" :value="fullNumber">
                        <div class="flex items-stretch">
                            {{-- Country prefix dropdown --}}
                            <div class="relative" @click.away="openPrefix = false">
                                <button type="button" @click="openPrefix = !openPrefix"
                                    class="flex items-center gap-2 h-full px-3 py-2 rounded-l-lg border border-r-0 border-gray-400 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 whitespace-nowrap">
                                    <img :src="selectedPrefix === '+63' ? '{{ asset('images/icons/philippine_flag.png') }}' : '{{ asset('images/icons/finland-flag.svg') }}'"
                                        class="h-4 w-auto" :alt="selectedCountry">
                                    <span x-text="selectedPrefix" class="font-medium text-sm"></span>
                                    <svg class="w-3 h-3 text-gray-400 transition-transform" :class="openPrefix ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openPrefix" x-cloak x-transition
                                    class="absolute left-0 top-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-50 w-56 py-1.5">
                                    <button type="button" @click="selectPrefix('+63', '🇵🇭', 'PH', 10); openPrefix = false"
                                        class="w-full text-left px-3 py-2.5 text-sm flex items-center gap-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                        :class="selectedPrefix === '+63' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-200'">
                                        <img src="{{ asset('images/icons/philippine_flag.png') }}" class="h-4 w-auto" alt="PH">
                                        <span>+63</span>
                                        <span class="text-gray-400 dark:text-gray-500 text-xs ml-auto">Philippines</span>
                                    </button>
                                    <button type="button" @click="selectPrefix('+358', '🇫🇮', 'FI', 9); openPrefix = false"
                                        class="w-full text-left px-3 py-2.5 text-sm flex items-center gap-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                        :class="selectedPrefix === '+358' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-200'">
                                        <img src="{{ asset('images/icons/finland-flag.svg') }}" class="h-4 w-auto" alt="FI">
                                        <span>+358</span>
                                        <span class="text-gray-400 dark:text-gray-500 text-xs ml-auto">Finland</span>
                                    </button>
                                </div>
                            </div>
                            {{-- Phone number input --}}
                            <input type="tel" x-ref="phoneLocal" x-model="localNumber"
                                @input="validateInput()"
                                @blur="validatePhone()"
                                :placeholder="selectedPrefix === '+63' ? '9XX XXX XXXX' : '4X XXX XXXX'"
                                :maxlength="maxLen"
                                class="flex-1 px-4 py-2 rounded-r-lg border border-gray-400 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-normal focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="error ? 'border-red-500 dark:border-red-500 focus:ring-red-500' : ''">
                        </div>
                        <p x-show="error" x-text="error" x-cloak class="mt-1 text-xs text-red-500"></p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500" x-show="!error">
                            <span x-show="selectedPrefix === '+63'">Philippine mobile: 10 digits starting with 9</span>
                            <span x-show="selectedPrefix === '+358'">Finnish mobile: 7-9 digits starting with 4 or 5</span>
                        </p>
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Location
                        </label>
                        <input type="text"
                               name="location"
                               id="location"
                               value="{{ old('location', $user->location) }}"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-normal focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-4 pt-4">
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            Save Changes
                        </button>
                        <a href="{{ route('employee.profile') }}"
                           class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        function phoneInput(existingValue) {
            return {
                selectedPrefix: '+358',
                selectedFlag: '🇫🇮',
                selectedCountry: 'FI',
                localNumber: '',
                maxLen: 9,
                openPrefix: false,
                error: '',
                numberStore: { '+63': '', '+358': '' },

                get fullNumber() {
                    if (!this.localNumber) return '';
                    return this.selectedPrefix + this.localNumber;
                },

                init() {
                    this.numberStore = { '+63': '', '+358': '' };
                    if (existingValue) {
                        if (existingValue.startsWith('+358')) {
                            this.selectedPrefix = '+358'; this.selectedFlag = '🇫🇮'; this.selectedCountry = 'FI'; this.maxLen = 9;
                            this.localNumber = existingValue.replace('+358', '').replace(/^0/, '');
                        } else if (existingValue.startsWith('+63')) {
                            this.selectedPrefix = '+63'; this.selectedFlag = '🇵🇭'; this.selectedCountry = 'PH'; this.maxLen = 10;
                            this.localNumber = existingValue.replace('+63', '').replace(/^0/, '');
                        } else if (/^09\d{9}$/.test(existingValue)) {
                            this.selectedPrefix = '+63'; this.selectedFlag = '🇵🇭'; this.selectedCountry = 'PH'; this.maxLen = 10;
                            this.localNumber = existingValue.replace(/^0/, '');
                        } else if (/^0[45]\d{6,8}$/.test(existingValue)) {
                            this.selectedPrefix = '+358'; this.selectedFlag = '🇫🇮'; this.selectedCountry = 'FI'; this.maxLen = 9;
                            this.localNumber = existingValue.replace(/^0/, '');
                        } else {
                            this.localNumber = existingValue.replace(/[^\d]/g, '');
                        }
                        this.numberStore[this.selectedPrefix] = this.localNumber;
                    }
                },

                selectPrefix(prefix, flag, country, max) {
                    this.numberStore[this.selectedPrefix] = this.localNumber;
                    this.selectedPrefix = prefix;
                    this.selectedFlag = flag;
                    this.selectedCountry = country;
                    this.maxLen = max;
                    this.error = '';
                    this.localNumber = this.numberStore[prefix] || '';
                },

                validateInput() {
                    this.localNumber = this.localNumber.replace(/[^\d]/g, '');
                    if (this.localNumber.startsWith('0')) this.localNumber = this.localNumber.replace(/^0/, '');
                    this.error = '';
                },

                validatePhone() {
                    if (!this.localNumber) { this.error = ''; return; }
                    if (this.selectedPrefix === '+63') {
                        if (this.localNumber.length !== 10) this.error = 'Philippine mobile number must be exactly 10 digits';
                        else if (!this.localNumber.startsWith('9')) this.error = 'Philippine mobile number must start with 9';
                        else this.error = '';
                    } else if (this.selectedPrefix === '+358') {
                        if (this.localNumber.length < 7 || this.localNumber.length > 9) this.error = 'Finnish mobile number must be 7-9 digits';
                        else if (!/^[45]/.test(this.localNumber)) this.error = 'Finnish mobile number must start with 4 or 5';
                        else this.error = '';
                    }
                }
            };
        }
    </script>
    <script>
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            try {
                await window.showConfirmDialog(
                    'Save Changes',
                    'Are you sure you want to update your profile information?',
                    'Save',
                    'Cancel'
                );
            } catch (e) {
                return;
            }

            const form = this;
            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('employee.profile.update') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    window.showSuccessDialog('Profile Updated', data.message || 'Your profile has been updated successfully.');
                } else {
                    const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Failed to update profile.');
                    window.showErrorDialog('Update Failed', errors);
                }
            } catch (err) {
                window.showErrorDialog('Error', 'Something went wrong. Please try again.');
            }
        });

        document.getElementById('pictureForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('profilePictureInput');
            if (!fileInput.files.length) {
                window.showErrorDialog('No File Selected', 'Please choose a picture to upload.');
                return;
            }

            try {
                await window.showConfirmDialog(
                    'Upload Picture',
                    'Are you sure you want to update your profile picture?',
                    'Upload',
                    'Cancel'
                );
            } catch (e) {
                return;
            }

            const formData = new FormData(this);

            try {
                const response = await fetch("{{ route('employee.profile.upload-picture') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (data.path) {
                        document.getElementById('profilePicturePreview').src = data.path;
                    }
                    fileInput.value = '';
                    window.showSuccessDialog('Picture Updated', data.message || 'Your profile picture has been updated successfully.');
                } else {
                    const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Failed to upload picture.');
                    window.showErrorDialog('Upload Failed', errors);
                }
            } catch (err) {
                window.showErrorDialog('Error', 'Something went wrong. Please try again.');
            }
        });
    </script>
    @endpush
    </x-skeleton-page>
</x-layouts.general-employee>
