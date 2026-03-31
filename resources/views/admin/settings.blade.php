<x-layouts.general-employer :title="'Settings'">
    <x-skeleton-page :preset="'default'">
    <section class="flex w-full flex-col p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <div class="max-w-4xl mx-auto w-full" x-data="{
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
            autoQuotation: true,
            quotationFiles: {
                deep_cleaning: null,
                final_cleaning: null,
                daily_cleaning: null,
                snowout_cleaning: null,
                general_cleaning: null,
                hotel_cleaning: null,
            }
        }">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your account settings and preferences</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Password Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6"
                 x-data="{
                    showCurrent: false, showNew: false, showConfirm: false,
                    submittingPw: false,
                    async submitPassword() {
                        if (!this.currentPassword || !this.newPassword || !this.confirmPassword) {
                            window.showErrorDialog('Missing Information', 'Please fill in all password fields.');
                            return;
                        }
                        if (this.newPassword !== this.confirmPassword) {
                            window.showErrorDialog('Mismatch', 'New password and confirmation do not match.');
                            return;
                        }
                        if (this.newPassword.length < 8) {
                            window.showErrorDialog('Too Short', 'Password must be at least 8 characters.');
                            return;
                        }
                        let pwScore = 0;
                        if (this.newPassword.length > 5) pwScore++; if (this.newPassword.length > 8) pwScore++;
                        if (/[A-Z]/.test(this.newPassword)) pwScore++; if (/[a-z]/.test(this.newPassword)) pwScore++;
                        if (/[0-9]/.test(this.newPassword)) pwScore++; if (/[^A-Za-z0-9]/.test(this.newPassword)) pwScore++;
                        if (pwScore < 5) {
                            window.showErrorDialog('Weak Password', 'Password must be at least Strong. Include uppercase, lowercase, numbers, and special characters.');
                            return;
                        }
                        try {
                            await window.showConfirmDialog('Update Password?', 'Are you sure you want to change your password?', 'Update', 'Cancel');
                        } catch (e) { return; }
                        this.submittingPw = true;
                        try {
                            const res = await fetch('{{ route('admin.settings.update-password') }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify({ current_password: this.currentPassword, password: this.newPassword, password_confirmation: this.confirmPassword })
                            });
                            const data = await res.json();
                            if (data.success) {
                                window.showSuccessDialog('Password Updated', data.message);
                                setTimeout(() => { window.location.href = data.redirect || '{{ route("login") }}'; }, 1500);
                            } else {
                                window.showErrorDialog('Update Failed', data.message || 'Failed to update password.');
                            }
                        } catch (e) {
                            // Session may already be invalidated — redirect to login
                            window.location.href = '{{ route("login") }}';
                            return;
                        } finally { this.submittingPw = false; }
                    }
                 }">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fa-solid fa-lock mr-2 text-blue-500"></i>
                    Change Password
                </h2>

                <div class="space-y-4">
                    {{-- Current Password --}}
                    <div class="relative" x-ref="currentPwWrap">
                        <x-material-ui.input-field
                            label="Current Password"
                            :type="'password'"
                            model="currentPassword"
                            icon="fi fi-rr-lock"
                            placeholder="Enter current password"
                            required
                        />
                        <button type="button" @click="showCurrent = !showCurrent; $refs.currentPwWrap.querySelector('input').type = showCurrent ? 'text' : 'password'"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 z-10">
                            <svg x-show="!showCurrent" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showCurrent" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                        </button>
                    </div>

                    {{-- New Password --}}
                    <div class="relative" x-ref="newPwWrap">
                        <x-material-ui.input-field
                            label="New Password"
                            :type="'password'"
                            model="newPassword"
                            icon="fi fi-rr-key"
                            placeholder="Minimum 8 characters"
                            required
                        />
                        <button type="button" @click="showNew = !showNew; $refs.newPwWrap.querySelector('input').type = showNew ? 'text' : 'password'"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 z-10">
                            <svg x-show="!showNew" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showNew" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                        </button>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="relative" x-ref="confirmPwWrap">
                        <x-material-ui.input-field
                            label="Confirm New Password"
                            :type="'password'"
                            model="confirmPassword"
                            icon="fi fi-rr-shield-check"
                            placeholder="Re-enter new password"
                            required
                        />
                        <button type="button" @click="showConfirm = !showConfirm; $refs.confirmPwWrap.querySelector('input').type = showConfirm ? 'text' : 'password'"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 z-10">
                            <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showConfirm" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                        </button>
                    </div>

                    {{-- Password Strength Indicator --}}
                    <x-material-ui.password-strength model="newPassword" />

                    <button type="button" @click="submitPassword()" :disabled="submittingPw"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm disabled:opacity-50">
                        <span x-show="!submittingPw">Update Password</span>
                        <span x-show="submittingPw"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Updating...</span>
                    </button>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fa-solid fa-user mr-2 text-blue-500"></i>
                    Account Information
                </h2>

                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Email Address</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Account Type</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Administrator</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Member Since</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('F j, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quotation Automation Preferences -->
            <div id="quotation-automation" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6"
                 x-data="{
                    savingQuotation: false,
                    async saveQuotationSettings() {
                        try {
                            await window.showConfirmDialog('Save Quotation Settings?', 'Are you sure you want to update the quotation automation preferences?', 'Save', 'Cancel');
                        } catch (e) { return; }
                        this.savingQuotation = true;
                        try {
                            const form = this.$refs.quotationForm;
                            const fd = new FormData(form);
                            const res = await fetch('{{ route('admin.settings.update-quotation') }}', {
                                method: 'POST',
                                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: fd
                            });
                            const data = await res.json();
                            if (data.success) {
                                window.showSuccessDialog('Settings Saved', data.message);
                            } else {
                                window.showErrorDialog('Save Failed', data.message || 'Failed to save quotation settings.');
                            }
                        } catch (e) {
                            window.showErrorDialog('Error', 'An error occurred. Please try again.');
                        } finally { this.savingQuotation = false; }
                    }
                 }">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fa-solid fa-file-invoice mr-2 text-blue-500"></i>
                    Quotation Automation Preferences
                </h2>

                <form x-ref="quotationForm" @submit.prevent>
                <!-- Auto-send Toggle -->
                <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Automated Quotation Sending</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Automatically send quotation PDFs to clients upon submission</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="auto_send_enabled" value="0">
                        <input type="checkbox" name="auto_send_enabled" value="1" {{ ($quotationSettings['auto_send_enabled'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- PDF Quotation Files per Service -->
                <div class="mt-5">
                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">Quotation PDF Templates</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Upload PDF quotation templates for each cleaning service. These will be sent to clients automatically when enabled.</p>

                    <div class="space-y-3">
                        @php
                            $services = [
                                ['key' => 'deep_cleaning', 'label' => 'Deep Cleaning', 'icon' => 'fa-solid fa-broom'],
                                ['key' => 'final_cleaning', 'label' => 'Final Cleaning', 'icon' => 'fa-solid fa-wand-magic-sparkles'],
                                ['key' => 'daily_cleaning', 'label' => 'Daily Cleaning', 'icon' => 'fa-solid fa-house-chimney'],
                                ['key' => 'snowout_cleaning', 'label' => 'Snowout Cleaning', 'icon' => 'fa-solid fa-snowflake'],
                                ['key' => 'general_cleaning', 'label' => 'General Cleaning', 'icon' => 'fa-solid fa-spray-can-sparkles'],
                                ['key' => 'hotel_cleaning', 'label' => 'Hotel Cleaning', 'icon' => 'fa-solid fa-hotel'],
                            ];
                        @endphp

                        @foreach($services as $service)
                            @php $currentPdf = $quotationSettings['pdf_' . $service['key']] ?? null; @endphp
                            <div class="flex items-center justify-between py-3 border-b dark:border-gray-700/50 border-gray-500"
                                 x-data="{ fileName: '{{ $currentPdf ? basename($currentPdf) : '' }}' }">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                        <i class="{{ $service['icon'] }} text-blue-500 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $service['label'] }}</p>
                                        <p class="text-xs" :class="fileName ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'"
                                           x-text="fileName || 'No file uploaded'"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="px-3 py-1.5 text-xs font-medium bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-500 transition-colors cursor-pointer">
                                        <i class="fa-solid fa-arrow-up-from-bracket mr-1"></i> Change
                                        <input type="file" name="pdf_{{ $service['key'] }}" accept=".pdf" class="hidden"
                                               @change="fileName = $event.target.files[0]?.name || fileName">
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5">
                        <button type="button" @click="saveQuotationSettings()" :disabled="savingQuotation"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm disabled:opacity-50">
                            <span x-show="!savingQuotation">Save Quotation Settings</span>
                            <span x-show="savingQuotation"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Saving...</span>
                        </button>
                    </div>
                </div>
                </form>
            </div>

            <!-- Workforce Configuration -->
            <div id="workforce-config" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6"
                 x-data="{
                    savingConfig: false,
                    config: {
                        minimum_booking_notice_days: {{ $companySettings['minimum_booking_notice_days'] }},
                        minimum_leave_notice_days: {{ $companySettings['minimum_leave_notice_days'] }},
                        task_approval_grace_period_minutes: {{ $companySettings['task_approval_grace_period_minutes'] }},
                        reassignment_grace_period_minutes: {{ $companySettings['reassignment_grace_period_minutes'] }},
                        unstaffed_escalation_timeout_minutes: {{ $companySettings['unstaffed_escalation_timeout_minutes'] }},
                        overtime_threshold_hours: {{ $companySettings['overtime_threshold_hours'] }},
                        geofence_radius: {{ $companySettings['geofence_radius'] }},
                    },
                    async saveConfig() {
                        try {
                            await window.showConfirmDialog('Save Configuration?', 'Are you sure you want to update the workforce configuration? These changes affect how scenarios are enforced across the system.', 'Save', 'Cancel');
                        } catch (e) { return; }
                        this.savingConfig = true;
                        try {
                            const res = await fetch('{{ route('admin.settings.update-company') }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify(this.config)
                            });
                            const data = await res.json();
                            if (data.success) {
                                window.showSuccessDialog('Configuration Saved', data.message);
                            } else {
                                let errorMsg = data.message || 'Failed to save configuration.';
                                if (data.errors) {
                                    errorMsg = Object.values(data.errors).flat().join('\n');
                                }
                                window.showErrorDialog('Save Failed', errorMsg);
                            }
                        } catch (e) {
                            window.showErrorDialog('Error', 'An error occurred. Please try again.');
                        } finally { this.savingConfig = false; }
                    }
                 }">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fa-solid fa-gears mr-2 text-blue-500"></i>
                    Workforce Configuration
                </h2>

                <div class="space-y-3">
                    <!-- Minimum Booking Notice -->
                    <div class="flex items-center justify-between py-3 border-b dark:border-gray-700/50 border-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-calendar-check text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Minimum Booking Notice</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">How many days in advance must tasks be booked?</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model.number="config.minimum_booking_notice_days" min="1" max="30"
                                   class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="text-sm text-gray-500 dark:text-gray-400">days</span>
                        </div>
                    </div>

                    <!-- Minimum Leave Notice -->
                    <div class="flex items-center justify-between py-3 border-b dark:border-gray-700/50 border-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-plane-departure text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Minimum Leave Notice</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">How many days in advance must standard leave be requested?</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model.number="config.minimum_leave_notice_days" min="1" max="30"
                                   class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="text-sm text-gray-500 dark:text-gray-400">days</span>
                        </div>
                    </div>

                    <!-- Task Approval Grace Period -->
                    <div class="flex items-center justify-between py-3 border-b dark:border-gray-700/50 border-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-clipboard-check text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Task Approval Grace Period</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Minutes before an unapproved task is marked as unstaffed</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model.number="config.task_approval_grace_period_minutes" min="5" max="240"
                                   class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="text-sm text-gray-500 dark:text-gray-400">mins</span>
                        </div>
                    </div>

                    <!-- Reassignment Grace Period -->
                    <div class="flex items-center justify-between py-3 border-b dark:border-gray-700/50 border-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-people-arrows text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Reassignment Grace Period</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Minutes allowed for task reassignment after leave approval</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model.number="config.reassignment_grace_period_minutes" min="5" max="240"
                                   class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="text-sm text-gray-500 dark:text-gray-400">mins</span>
                        </div>
                    </div>

                    <!-- Unstaffed Escalation Timeout -->
                    <div class="flex items-center justify-between py-3 border-b dark:border-gray-700/50 border-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-triangle-exclamation text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Unstaffed Escalation Timeout</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Minutes before unaccepted tasks trigger critical escalation</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model.number="config.unstaffed_escalation_timeout_minutes" min="10" max="480"
                                   class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="text-sm text-gray-500 dark:text-gray-400">mins</span>
                        </div>
                    </div>

                    <!-- Overtime Threshold -->
                    <div class="flex items-center justify-between py-3 border-b dark:border-gray-700/50 border-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-clock-rotate-left text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Overtime Threshold</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Hours after which overtime pay rate is applied</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model.number="config.overtime_threshold_hours" min="1" max="24"
                                   class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="text-sm text-gray-500 dark:text-gray-400">hours</span>
                        </div>
                    </div>

                    <!-- Geofence Radius -->
                    <div class="flex items-center justify-between py-3 border-b dark:border-gray-700/50 border-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-location-crosshairs text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Geofence Radius</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Allowed distance from job site for clock-in</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model.number="config.geofence_radius" min="10" max="1000"
                                   class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="text-sm text-gray-500 dark:text-gray-400">meters</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <button type="button" @click="saveConfig()" :disabled="savingConfig"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm disabled:opacity-50">
                        <span x-show="!savingConfig">Save Configuration</span>
                        <span x-show="savingConfig"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </div>

            <!-- Job Posting Salary Configuration -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6"
                 x-data="{
                    savingSalary: false,
                    salaryConfig: {
                        salary_full_time: {{ $salarySettings['salary_full_time'] }},
                        salary_part_time: {{ $salarySettings['salary_part_time'] }},
                        salary_remote: {{ $salarySettings['salary_remote'] }},
                    },
                    formatCurrency(value) {
                        return parseFloat(value).toLocaleString('en-EU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    },
                    async saveSalaryConfig() {
                        // Validate
                        for (const [key, val] of Object.entries(this.salaryConfig)) {
                            if (isNaN(val) || val < 0) {
                                window.showErrorDialog('Invalid Input', 'All salary values must be non-negative numbers.');
                                return;
                            }
                        }
                        try {
                            await window.showConfirmDialog('Save Salary Configuration?', 'Are you sure you want to update the default base salaries? These values will auto-populate the salary field when creating new job postings.', 'Save', 'Cancel');
                        } catch (e) { return; }
                        this.savingSalary = true;
                        try {
                            const res = await fetch('{{ route('admin.settings.update-salary') }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify(this.salaryConfig)
                            });
                            const data = await res.json();
                            if (data.success) {
                                window.showSuccessDialog('Configuration Saved', data.message);
                            } else {
                                let errorMsg = data.message || 'Failed to save salary configuration.';
                                if (data.errors) {
                                    errorMsg = Object.values(data.errors).flat().join('\n');
                                }
                                window.showErrorDialog('Save Failed', errorMsg);
                            }
                        } catch (e) {
                            window.showErrorDialog('Error', 'An error occurred. Please try again.');
                        } finally { this.savingSalary = false; }
                    }
                 }">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fa-solid fa-money-bill-wave mr-2 text-blue-500"></i>
                    Job Posting Salary Configuration
                </h2>

                <div class="space-y-3">
                    <!-- Full-time -->
                    <div class="flex items-center justify-between py-3 border-b dark:border-gray-700/50 border-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-briefcase text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Full-time</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Base salary for full-time roles</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">&euro;</span>
                            <input type="number" x-model.number="salaryConfig.salary_full_time" min="0" step="0.01"
                                   class="w-28 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="2500">
                        </div>
                    </div>

                    <!-- Part-time -->
                    <div class="flex items-center justify-between py-3 border-b dark:border-gray-700/50 border-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-clock text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Part-time</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Base salary for part-time roles</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">&euro;</span>
                            <input type="number" x-model.number="salaryConfig.salary_part_time" min="0" step="0.01"
                                   class="w-28 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="1200">
                        </div>
                    </div>

                    <!-- Remote -->
                    <div class="flex items-center justify-between py-3 border-b dark:border-gray-700/50 border-gray-500">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-house-laptop text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Remote</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Base salary for remote roles</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">&euro;</span>
                            <input type="number" x-model.number="salaryConfig.salary_remote" min="0" step="0.01"
                                   class="w-28 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="2000">
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <button type="button" @click="saveSalaryConfig()" :disabled="savingSalary"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm disabled:opacity-50">
                        <span x-show="!savingSalary">Save Salary Configuration</span>
                        <span x-show="savingSalary"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </div>

            <!-- Notification Preferences -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fa-solid fa-bell mr-2 text-blue-500"></i>
                    Notification Preferences
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Email Notifications</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Receive email notifications for important updates</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Task Reminders</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Get reminded about upcoming tasks and deadlines</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Weekly Reports</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Receive weekly summary of activities</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">Note: Notification preferences will be functional in a future update</p>
            </div>

            <!-- Privacy & Security -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fa-solid fa-shield-halved mr-2 text-blue-500"></i>
                    Privacy & Security
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Two-Factor Authentication</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Add an extra layer of security to your account</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" data-toggle-id="two-factor">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                            </div>
                        </label>
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">Note: Advanced security features will be functional in a future update</p>
            </div>

            <!-- Language & Region -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fa-solid fa-globe mr-2 text-blue-500"></i>
                    Language & Region
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Language
                        </label>
                        <select class="w-full px-4 py-3 rounded-xl border border-gray-400 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)] dark:focus:shadow-[0_0_0_3px_rgba(96,165,250,0.1)] transition-all duration-200">
                            <option>English</option>
                            <option>Spanish</option>
                            <option>French</option>
                            <option>German</option>
                            <option>Finnish</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Timezone
                        </label>
                        <select class="w-full px-4 py-3 rounded-xl border border-gray-400 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)] dark:focus:shadow-[0_0_0_3px_rgba(96,165,250,0.1)] transition-all duration-200">
                            <option>UTC+02:00 (Helsinki)</option>
                            <option>UTC+00:00 (London)</option>
                            <option>UTC-05:00 (New York)</option>
                            <option>UTC-08:00 (Los Angeles)</option>
                        </select>
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">Note: Language and timezone settings will be functional in a future update</p>
            </div>
        </div>
    </section>
    </x-skeleton-page>
</x-layouts.general-employer>
