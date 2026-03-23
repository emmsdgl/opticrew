<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
    @php
        $navOptions = [
            [
                'label' => 'Dashboard',
                'icon' => 'fa-solid fa-house', // 'fa-house' is the standard v6 name
                'href' => route('employee.dashboard')
            ],
            [
                'label' => 'Tasks',
                'icon' => 'fa-solid fa-list-check', 
                'href' => route('employee.tasks')
            ],
            [
                'label' => 'Courses',
                'icon' => 'fa-solid fa-book-open',
                'href' => route('employee.development')
            ],
            [
                'label' => 'Performance',
                'icon' => 'fa-solid fa-chart-line',
                'href' => route('employee.performance')
            ],
            [
                'label' => 'Attendance',
                'icon' => 'fa-solid fa-calendar-check', 
                'href' => route('employee.attendance')
            ],
            [
                'label' => 'History',
                'icon' => 'fa-clock-rotate-left', 
                'href' => route('employee.history')
            ]
        ];

    @endphp
    <x-sidebar :navOptions="$navOptions" />
    @endslot

    <div class="flex-1">
        {{ $slot }}
    </div>

    {{-- Employee Profile Modal --}}
    @php
        $profileUser = auth()->user();
        $hasPassword = !empty($profileUser->password);
    @endphp
    <div x-data="employeeProfileModal()" @open-profile-modal.window="openModal()">
        <template x-teleport="body">
            <div x-show="profileOpen" x-cloak
                 class="fixed inset-0 z-[70] flex items-center justify-center p-4"
                 @keydown.escape.window="closeModal()">

                {{-- Backdrop --}}
                <div x-show="profileOpen"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     @click="closeModal()"
                     class="absolute inset-0 bg-black/50"></div>

                {{-- Modal --}}
                <div x-show="profileOpen"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     class="relative w-full max-w-sm bg-white dark:bg-[#1E293B] rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">

                    {{-- Edit button (top-right) --}}
                    <button x-show="!editing" @click="startEditing()"
                        class="absolute top-4 right-4 z-20 w-8 h-8 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur shadow-md flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:scale-110 transition-all" title="Edit Profile">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>

                    {{-- Close / Cancel button (top-left) --}}
                    <button @click="editing ? cancelEditing() : closeModal()"
                        class="absolute top-4 left-4 z-20 w-8 h-8 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur shadow-md flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 hover:scale-110 transition-all"
                        :title="editing ? 'Cancel' : 'Close'">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>

                    {{-- Profile Card (cover + avatar) --}}
                    <x-material-ui.profile-card
                        :user="$profileUser"
                        coverUploadRoute="{{ route('employee.profile.upload-cover') }}"
                    />

                    {{-- Editable Fields --}}
                    <div class="px-6 pb-6">
                        <div class="mb-3 border-t border-gray-100 dark:border-gray-700"></div>

                        <div class="space-y-3 text-left">
                            {{-- Phone --}}
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-phone text-xs text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Phone</p>
                                    <template x-if="!editing">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" x-text="form.phone || '—'"></p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="tel" x-model="form.phone" placeholder="+358 XX XXX XXXX"
                                            class="w-full text-sm font-medium text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                                    </template>
                                </div>
                            </div>

                            {{-- Email (read-only) --}}
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-envelope text-xs text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Email</p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $profileUser->email ?? '—' }}</p>
                                </div>
                            </div>

                            {{-- Username --}}
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-user-tag text-xs text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Username</p>
                                    <template x-if="!editing">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" x-text="form.username || '—'"></p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" x-model="form.username" placeholder="Enter username"
                                            class="w-full text-sm font-medium text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                                    </template>
                                </div>
                            </div>

                            {{-- Location --}}
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-location-dot text-xs text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Location</p>
                                    <template x-if="!editing">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" x-text="form.location || '—'"></p>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" x-model="form.location" placeholder="Enter location"
                                            class="w-full text-sm font-medium text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                                    </template>
                                </div>
                            </div>

                            {{-- Change Password (edit mode only) --}}
                            <template x-if="editing">
                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-3">
                                    <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Change Password</p>

                                    {{-- Current Password --}}
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-lock text-xs text-blue-500 dark:text-blue-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Current Password</p>
                                            <input type="password" x-model="form.current_password"
                                                :placeholder="hasPassword ? 'Enter current password' : 'No password set'"
                                                :disabled="!hasPassword"
                                                class="w-full text-sm font-medium text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        </div>
                                    </div>

                                    {{-- New Password --}}
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-key text-xs text-blue-500 dark:text-blue-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">New Password</p>
                                            <input type="password" x-model="form.new_password" placeholder="Enter new password"
                                                class="w-full text-sm font-medium text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                                        </div>
                                    </div>

                                    {{-- Confirm Password --}}
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-check-double text-xs text-blue-500 dark:text-blue-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Confirm Password</p>
                                            <input type="password" x-model="form.new_password_confirmation" placeholder="Confirm new password"
                                                class="w-full text-sm font-medium text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Save Button (edit mode) --}}
                        <div x-show="editing" x-transition class="mt-5">
                            <button @click="saveProfile()" :disabled="saving"
                                class="w-full py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <span x-show="!saving">Save Changes</span>
                                <span x-show="saving" class="flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-spinner fa-spin text-xs"></i> Saving...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
    function employeeProfileModal() {
        return {
            profileOpen: false,
            editing: false,
            saving: false,
            hasPassword: @json($hasPassword),
            form: {
                phone: @json($profileUser->phone ?? ''),
                username: @json($profileUser->username ?? ''),
                location: @json($profileUser->location ?? ''),
                current_password: '',
                new_password: '',
                new_password_confirmation: '',
            },
            original: {},

            openModal() {
                this.profileOpen = true;
                this.editing = false;
            },

            closeModal() {
                if (this.editing) {
                    this.cancelEditing();
                    return;
                }
                this.profileOpen = false;
            },

            startEditing() {
                this.original = { ...this.form };
                this.editing = true;
                window.dispatchEvent(new CustomEvent('profile-edit-toggled', { detail: { editing: true } }));
            },

            cancelEditing() {
                this.form = { ...this.original };
                this.form.current_password = '';
                this.form.new_password = '';
                this.form.new_password_confirmation = '';
                this.editing = false;
                window.dispatchEvent(new CustomEvent('profile-edit-toggled', { detail: { editing: false } }));
            },

            async saveProfile() {
                try {
                    const confirmed = await window.showConfirmDialog(
                        'Update Profile',
                        'Are you sure you want to save these changes to your profile?',
                        'Save',
                        'Cancel'
                    );
                } catch {
                    return;
                }

                this.saving = true;

                try {
                    const res = await fetch('{{ route("employee.profile.update") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    });

                    const data = await res.json();

                    if (res.ok && data.success) {
                        if (data.password_changed) {
                            await window.showSuccessDialog('Password Changed', 'Your password has been changed. Please log in again.', 'OK');
                            window.location.href = '{{ route("login") }}';
                            return;
                        }
                        this.editing = false;
                        this.original = { ...this.form };
                        this.form.current_password = '';
                        this.form.new_password = '';
                        this.form.new_password_confirmation = '';
                        window.dispatchEvent(new CustomEvent('profile-edit-toggled', { detail: { editing: false } }));
                        window.showSuccessDialog('Profile Updated', 'Your profile has been updated successfully.', 'OK');
                    } else {
                        const msg = data.message || Object.values(data.errors || {}).flat().join(', ') || 'Something went wrong.';
                        window.showErrorDialog('Update Failed', msg, 'OK');
                    }
                } catch (e) {
                    window.showErrorDialog('Update Failed', 'A network error occurred. Please try again.', 'OK');
                } finally {
                    this.saving = false;
                }
            }
        };
    }
    </script>
    @endpush

</x-layouts.general-dashboard>