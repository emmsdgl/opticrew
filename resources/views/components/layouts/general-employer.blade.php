<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-solid fa-house', 'href' => route('admin.dashboard')],
            ['label' => 'Accounts', 'icon' => 'fa-users', 'href' => route('admin.accounts.index')],
            ['label' => 'Tasks', 'icon' => 'fa-list-check', 'href' => route('admin.tasks')],
            [
                'label' => 'Appointments',
                'icon' => 'fa-calendar-check',
                'children' => [
                    ['label' => 'Appointments', 'icon' => 'fa-calendar-days', 'href' => route('admin.appointments.index')],
                    ['label' => 'Quotation Requests', 'icon' => 'fa-file-invoice', 'href' => route('admin.quotations.index')],
                ]
            ],
            [
                'label' => 'Recruitment',
                'icon' => 'fa-user-plus',
                'children' => [
                    ['label' => 'Applications', 'icon' => 'fa-file-lines', 'href' => route('admin.recruitment.index'), 'route' => 'admin.recruitment.index'],
                    ['label' => 'Interviews', 'icon' => 'fa-calendar-check', 'href' => route('admin.recruitment.interviews'), 'route' => 'admin.recruitment.interviews'],
                ]
            ],
            ['label' => 'Training', 'icon' => 'fa-video', 'href' => route('admin.training-videos.index')],
            ['label' => 'Attendance', 'icon' => 'fa-solid fa-calendar-check', 'href' => route('admin.attendance')],
            ['label' => 'History', 'icon' => 'fa-clock-rotate-left', 'href' => route('admin.history')],
            ['label' => 'Analytics', 'icon' => 'fa-chart-line', 'href' => route('admin.analytics')],
        ];

        // Only show Optimization Result in local development environment
        // Hidden: Optimization Result nav item
        // if (app()->environment('local')) {
        //     $navOptions[] = ['label' => 'Optimization Result', 'icon' => 'fa-file-lines', 'href' => route('optimization.result')];
        // }

    @endphp
    <x-sidebar :navOptions="$navOptions" />
    @endslot

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
            {{ $slot }}
    </section>

    {{-- Profile Modal --}}
    @php
        $profileUser = auth()->user();

        $profilePhotoUrl = null;
        if ($profileUser->profile_picture) {
            if (str_starts_with($profileUser->profile_picture, 'profile_pictures/')) {
                $profilePhotoUrl = asset('storage/' . $profileUser->profile_picture);
            } else {
                $profilePhotoUrl = asset($profileUser->profile_picture);
            }
            $profilePhotoUrl .= '?v=' . time();
        }

        $totalEmployees = \App\Models\User::where('role', 'employee')->count();
        $totalClients = \App\Models\User::whereIn('role', ['client', 'external_client'])->count();
        $totalJobPostings = \App\Models\JobPosting::where('status', 'published')->count();
    @endphp

    <div x-data="adminProfileModal()" @open-profile-modal.window="openModal()">
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
                        coverUploadRoute="{{ route('admin.profile.upload-cover') }}"
                    />

                    {{-- Editable Fields --}}
                    <div class="px-6 pb-6">
                        <div class="mb-3 border-t border-gray-100 dark:border-gray-700"></div>

                        @include('components.partials.profile-modal-fields', ['profileUser' => $profileUser, 'hasPassword' => !empty($profileUser->password)])

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

                        {{-- Stats --}}
                        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-700">
                                <div class="flex flex-col items-center px-2 gap-0.5">
                                    <span class="font-bold text-base text-blue-500 dark:text-blue-400">{{ $totalEmployees }}</span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">Employees</span>
                                </div>
                                <div class="flex flex-col items-center px-2 gap-0.5">
                                    <span class="font-bold text-base text-green-500 dark:text-green-400">{{ $totalClients }}</span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">Clients</span>
                                </div>
                                <div class="flex flex-col items-center px-2 gap-0.5">
                                    <span class="font-bold text-base text-yellow-500 dark:text-yellow-400">{{ $totalJobPostings }}</span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">Job Posts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
    function adminProfileModal() {
        return {
            profileOpen: false,
            editing: false,
            saving: false,
            hasPassword: @json(!empty($profileUser->password)),
            showCurrentPw: false,
            showNewPw: false,
            showConfirmPw: false,
            phonePrefix: '+358',
            phoneLocal: '',
            phoneMaxLen: 9,
            phoneDropOpen: false,
            phoneError: '',
            phoneStore: { '+63': '', '+358': '' },
            form: {
                name: @json($profileUser->name ?? ''),
                username: @json($profileUser->username ?? ''),
                email: @json($profileUser->email ?? ''),
                phone: @json($profileUser->phone ?? ''),
                location: @json($profileUser->location ?? ''),
                current_password: '',
                new_password: '',
                new_password_confirmation: '',
            },
            original: {},

            initPhone() {
                this.phoneStore = { '+63': '', '+358': '' };
                const ph = this.form.phone || '';
                if (ph.startsWith('+358')) { this.phonePrefix = '+358'; this.phoneMaxLen = 9; this.phoneLocal = ph.replace('+358', '').replace(/^0/, ''); }
                else if (ph.startsWith('+63')) { this.phonePrefix = '+63'; this.phoneMaxLen = 10; this.phoneLocal = ph.replace('+63', '').replace(/^0/, ''); }
                else if (/^09\d{9}$/.test(ph)) { this.phonePrefix = '+63'; this.phoneMaxLen = 10; this.phoneLocal = ph.replace(/^0/, ''); }
                else if (/^0[45]\d{6,8}$/.test(ph)) { this.phonePrefix = '+358'; this.phoneMaxLen = 9; this.phoneLocal = ph.replace(/^0/, ''); }
                else { this.phoneLocal = ph.replace(/[^\d]/g, ''); }
                this.phoneStore[this.phonePrefix] = this.phoneLocal;
            },
            setPhonePrefix(prefix, max) {
                this.phoneStore[this.phonePrefix] = this.phoneLocal;
                this.phonePrefix = prefix; this.phoneMaxLen = max; this.phoneError = '';
                this.phoneLocal = this.phoneStore[prefix] || '';
                this.syncPhone();
            },
            syncPhone() { this.form.phone = this.phoneLocal ? this.phonePrefix + this.phoneLocal : ''; },
            validatePhoneNumber() {
                if (!this.phoneLocal) { this.phoneError = ''; return; }
                if (this.phonePrefix === '+63') {
                    if (this.phoneLocal.length !== 10) this.phoneError = 'Must be exactly 10 digits';
                    else if (!this.phoneLocal.startsWith('9')) this.phoneError = 'Must start with 9';
                    else this.phoneError = '';
                } else if (this.phonePrefix === '+358') {
                    if (this.phoneLocal.length < 7 || this.phoneLocal.length > 9) this.phoneError = 'Must be 7-9 digits';
                    else if (!/^[45]/.test(this.phoneLocal)) this.phoneError = 'Must start with 4 or 5';
                    else this.phoneError = '';
                }
            },

            openModal() { this.profileOpen = true; this.editing = false; },
            closeModal() { if (this.editing) { this.cancelEditing(); return; } this.profileOpen = false; },

            startEditing() {
                this.original = { ...this.form };
                this.initPhone();
                this.editing = true;
                window.dispatchEvent(new CustomEvent('profile-edit-toggled', { detail: { editing: true } }));
            },

            cancelEditing() {
                this.form = { ...this.original };
                this.form.current_password = ''; this.form.new_password = ''; this.form.new_password_confirmation = '';
                this.showCurrentPw = false; this.showNewPw = false; this.showConfirmPw = false;
                this.phoneError = ''; this.phoneDropOpen = false;
                this.editing = false;
                window.__pendingProfilePic = null;
                window.dispatchEvent(new CustomEvent('profile-edit-toggled', { detail: { editing: false } }));
            },

            async saveProfile() {
                if (this.phoneLocal) { this.validatePhoneNumber(); if (this.phoneError) { window.showErrorDialog('Invalid Phone', this.phoneError); return; } }
                if (this.form.new_password) {
                    if (this.form.new_password !== this.form.new_password_confirmation) { window.showErrorDialog('Mismatch', 'New password and confirmation do not match.'); return; }
                    if (this.form.new_password.length < 8) { window.showErrorDialog('Too Short', 'Password must be at least 8 characters.'); return; }
                    let s = 0; const p = this.form.new_password;
                    if (p.length > 5) s++; if (p.length > 8) s++; if (/[A-Z]/.test(p)) s++; if (/[a-z]/.test(p)) s++; if (/[0-9]/.test(p)) s++; if (/[^A-Za-z0-9]/.test(p)) s++;
                    if (s < 5) { window.showErrorDialog('Weak Password', 'Password must be at least Strong. Include uppercase, lowercase, numbers, and special characters.'); return; }
                    if (this.hasPassword && !this.form.current_password) { window.showErrorDialog('Missing Information', 'Please enter your current password.'); return; }
                }
                try { await window.showConfirmDialog('Update Profile', 'Are you sure you want to save these changes?', 'Save', 'Cancel'); } catch { return; }
                this.saving = true;
                try {
                    // Upload photo first if one was selected
                    if (window.__pendingProfilePic) {
                        const fd = new FormData();
                        fd.append('profile_picture', window.__pendingProfilePic.file);
                        const picRes = await fetch(window.__pendingProfilePic.route, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                            body: fd,
                        });
                        const picData = await picRes.json();
                        if (picRes.ok && picData.success) {
                            window.dispatchEvent(new CustomEvent('profile-picture-updated', { detail: { url: picData.path } }));
                            window.__pendingProfilePic = null;
                        } else {
                            window.showErrorDialog('Upload Failed', picData.message || 'Failed to upload photo.');
                            this.saving = false;
                            return;
                        }
                    }

                    const res = await fetch('{{ route("admin.profile.update") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        body: JSON.stringify(this.form),
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        if (data.password_changed) { window.showSuccessDialog('Password Changed', 'Your password has been changed. Please log in again.', 'OK', '{{ route("login") }}'); return; }
                        this.editing = false; this.original = { ...this.form };
                        this.form.current_password = ''; this.form.new_password = ''; this.form.new_password_confirmation = '';
                        window.dispatchEvent(new CustomEvent('profile-edit-toggled', { detail: { editing: false } }));
                        window.showSuccessDialog('Profile Updated', 'Your profile has been updated successfully.', 'OK');
                    } else {
                        const msg = data.message || Object.values(data.errors || {}).flat().join(', ') || 'Something went wrong.';
                        window.showErrorDialog('Update Failed', msg, 'OK');
                    }
                } catch (e) { window.showErrorDialog('Update Failed', 'A network error occurred. Please try again.', 'OK'); }
                finally { this.saving = false; }
            }
        };
    }
    </script>
    @endpush

    @stack('scripts')

</x-layouts.general-dashboard>
