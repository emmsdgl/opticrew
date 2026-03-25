<x-layouts.general-manager :title="'Settings'">
    <div class="flex flex-col gap-6 w-full">
        <div class="max-w-4xl mx-auto w-full" x-data="{
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
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
                        try {
                            await window.showConfirmDialog('Update Password?', 'Are you sure you want to change your password?', 'Update', 'Cancel');
                        } catch (e) { return; }
                        this.submittingPw = true;
                        try {
                            const res = await fetch('{{ route('manager.settings.update-password') }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify({ current_password: this.currentPassword, password: this.newPassword, password_confirmation: this.confirmPassword })
                            });
                            const data = await res.json();
                            if (data.success) {
                                window.showSuccessDialog('Password Updated', data.message);
                                setTimeout(() => { window.location.href = data.redirect || '/'; }, 2000);
                            } else {
                                window.showErrorDialog('Update Failed', data.message || 'Failed to update password.');
                            }
                        } catch (e) {
                            window.showErrorDialog('Error', 'An error occurred. Please try again.');
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
                            <i class="fa-solid fa-eye text-sm" x-show="!showCurrent"></i>
                            <i class="fa-solid fa-eye-slash text-sm" x-show="showCurrent" style="display:none"></i>
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
                            <i class="fa-solid fa-eye text-sm" x-show="!showNew"></i>
                            <i class="fa-solid fa-eye-slash text-sm" x-show="showNew" style="display:none"></i>
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
                            <i class="fa-solid fa-eye text-sm" x-show="!showConfirm"></i>
                            <i class="fa-solid fa-eye-slash text-sm" x-show="showConfirm" style="display:none"></i>
                        </button>
                    </div>

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
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Company Manager</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Member Since</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('F j, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.general-manager>
