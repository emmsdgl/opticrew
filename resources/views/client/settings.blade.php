<x-layouts.general-client :title="'Settings'">
    <x-skeleton-page :preset="'default'">
    <section class="flex w-full flex-col p-4 md:p-6 min-h-[calc(100vh-4rem)] overflow-y-auto scroll-smooth">
        <div class="max-w-6xl mx-auto w-full">
            <div class="w-full flex-col justify-start text-justify sm:justify-center pb-12 p-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Settings</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 pr-24">Manage your account settings and
                    preferences</p>
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

            <!--Section 1: Account Information-->
            <div class="w-full p-4 flex flex-col md:flex-row pb-12 md:pb-24">
                <!--Description Panel-->
                <div class="section-description w-full md:flex-1 flex-col justify-start text-justify mb-6 md:mb-0">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Account Information</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 pr-24">View and update your personal account
                        details and information.</p>
                </div>
                <!--Form Details-->
                <div class="form-description w-full md:flex-1">
                    <!-- Account Classification -->
                    <div class="mt-0 md:mt-6 mb-6">
                        <div class="space-y-3 flex flex-col justify-center">
                            <!-- Account Type -->
                            <div
                                class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-800">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Account Type</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                    Personal Account
                                </span>
                            </div>

                            <!-- User Role -->
                            <div
                                class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-800">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">User Role</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                    Client
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-800">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Account Created
                                    On</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                    <span class="font-semibold">{{ $user->created_at->format('F j, Y') }}</span>
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-800">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Email Address</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                    <span class="font-semibold">{{ $user->email }}</span>
                                </span>
                            </div>
                        </div>
                    </div>


                    <!-- Toggle Settings -->
                    <div class="space-y-6">

                        <!-- Two-Factor Authentication -->
                        <div
                            class="flex items-start justify-between py-4 border-b border-gray-200 dark:border-gray-800">
                            <div class="flex-1 pr-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                    Two-Factor Authentication
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Add an extra layer of security to your account
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <!-- Toggle Switch -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" data-toggle-id="two-factor">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Section 2: Change Password-->
            @php
                $isGoogleUser = Auth::user()->google_id && !Auth::user()->password;
                $hasPassword = !is_null(Auth::user()->password);
            @endphp
            <div class="w-full p-4 flex flex-col md:flex-row pb-12 md:pb-24">
                <!--Description Panel-->
                <div class="section-description w-full md:flex-1 flex-col justify-start text-justify mb-6 md:mb-0">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">
                        {{ $isGoogleUser ? 'Set Password' : 'Change Password' }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 pr-24">
                        @if($isGoogleUser)
                            You signed in with Google. Set a password to also log in with your email and password.
                        @else
                            Update your password associated with your account.
                        @endif
                    </p>
                </div>
                <!--Form Details-->
                <div class="form-description w-full md:flex-1" x-data="{
                    isGoogleUser: {{ $isGoogleUser ? 'true' : 'false' }},
                    currentPassword: '', newPassword: '', confirmPassword: '',
                    showCurrent: false, showNew: false, showConfirm: false,
                    submittingPw: false,
                    pwCheckStatus: '',
                    pwCheckTimer: null,
                    checkCurrentPassword() {
                        clearTimeout(this.pwCheckTimer);
                        if (!this.currentPassword) { this.pwCheckStatus = ''; return; }
                        this.pwCheckStatus = 'checking';
                        this.pwCheckTimer = setTimeout(async () => {
                            try {
                                const res = await fetch('{{ route("client.settings.check-password") }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    body: JSON.stringify({ password: this.currentPassword })
                                });
                                const data = await res.json();
                                this.pwCheckStatus = data.valid ? 'correct' : 'incorrect';
                            } catch (e) { this.pwCheckStatus = ''; }
                        }, 500);
                    },
                    async submitPassword() {
                        if (!this.isGoogleUser && !this.currentPassword) {
                            window.showErrorDialog('Missing Information', 'Please enter your current password.'); return;
                        }
                        if (!this.newPassword || !this.confirmPassword) {
                            window.showErrorDialog('Missing Information', 'Please fill in all password fields.'); return;
                        }
                        if (this.newPassword !== this.confirmPassword) {
                            window.showErrorDialog('Mismatch', 'New password and confirmation do not match.'); return;
                        }
                        if (this.newPassword.length < 8) {
                            window.showErrorDialog('Too Short', 'Password must be at least 8 characters.'); return;
                        }
                        let pwScore = 0;
                        if (this.newPassword.length > 5) pwScore++; if (this.newPassword.length > 8) pwScore++;
                        if (/[A-Z]/.test(this.newPassword)) pwScore++; if (/[a-z]/.test(this.newPassword)) pwScore++;
                        if (/[0-9]/.test(this.newPassword)) pwScore++; if (/[^A-Za-z0-9]/.test(this.newPassword)) pwScore++;
                        if (pwScore < 5) {
                            window.showErrorDialog('Weak Password', 'Password must be at least Strong. Include uppercase, lowercase, numbers, and special characters.'); return;
                        }
                        const confirmTitle = this.isGoogleUser ? 'Set Password?' : 'Update Password?';
                        const confirmMsg = this.isGoogleUser
                            ? 'This will set a password for your account. You will be logged out and can log in with your email and password.'
                            : 'Are you sure you want to change your password?';
                        try { await window.showConfirmDialog(confirmTitle, confirmMsg, this.isGoogleUser ? 'Set Password' : 'Update', 'Cancel'); } catch (e) { return; }
                        this.submittingPw = true;
                        try {
                            const url = this.isGoogleUser
                                ? '{{ route('client.settings.set-password') }}'
                                : '{{ route('client.settings.update-password') }}';
                            const body = this.isGoogleUser
                                ? { password: this.newPassword, password_confirmation: this.confirmPassword }
                                : { current_password: this.currentPassword, password: this.newPassword, password_confirmation: this.confirmPassword };
                            const res = await fetch(url, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify(body)
                            });
                            const data = await res.json();
                            if (data.success) {
                                window.showSuccessDialog('Password {{ $isGoogleUser ? "Set" : "Updated" }}', data.message);
                                setTimeout(() => { window.location.href = data.redirect || '{{ route("login") }}'; }, 1500);
                            } else {
                                window.showErrorDialog('{{ $isGoogleUser ? "Setup" : "Update" }} Failed', data.message || 'Failed to update password.');
                            }
                        } catch (e) { window.location.href = '{{ route("login") }}'; return; }
                        finally { this.submittingPw = false; }
                    }
                }">
                    <div class="space-y-6 mt-0 md:mt-6">
                        {{-- Current Password (hidden for Google-auth users without a password) --}}
                        @unless($isGoogleUser)
                        <div>
                            <div class="relative" x-ref="currentPwWrap" @input="checkCurrentPassword()">
                                <x-material-ui.input-field label="Current Password" :type="'password'" model="currentPassword" icon="fi fi-rr-lock" placeholder="Enter current password" required />
                                <button type="button" @click="showCurrent = !showCurrent; $refs.currentPwWrap.querySelector('input').type = showCurrent ? 'text' : 'password'"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 z-10">
                                    <svg x-show="!showCurrent" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg x-show="showCurrent" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                                </button>
                            </div>
                            <p x-show="pwCheckStatus === 'checking'" x-cloak class="text-xs text-gray-400 mt-1"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Checking...</p>
                            <p x-show="pwCheckStatus === 'correct'" x-cloak class="text-xs text-green-500 mt-1"><i class="fa-solid fa-check-circle mr-1"></i>Current password is correct</p>
                            <p x-show="pwCheckStatus === 'incorrect'" x-cloak class="text-xs text-red-500 mt-1"><i class="fa-solid fa-times-circle mr-1"></i>Current password is incorrect</p>
                        </div>
                        @endunless

                        {{-- New Password --}}
                        <div class="relative" x-ref="newPwWrap">
                            <x-material-ui.input-field label="{{ $isGoogleUser ? 'Password' : 'New Password' }}" :type="'password'" model="newPassword" icon="fi fi-rr-key" placeholder="Minimum 8 characters" required />
                            <button type="button" @click="showNew = !showNew; $refs.newPwWrap.querySelector('input').type = showNew ? 'text' : 'password'"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 z-10">
                                <svg x-show="!showNew" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="showNew" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                            </button>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="relative" x-ref="confirmPwWrap">
                            <x-material-ui.input-field label="Confirm Password" :type="'password'" model="confirmPassword" icon="fi fi-rr-shield-check" placeholder="Re-enter password" required />
                            <button type="button" @click="showConfirm = !showConfirm; $refs.confirmPwWrap.querySelector('input').type = showConfirm ? 'text' : 'password'"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 z-10">
                                <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="showConfirm" x-cloak xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                            </button>
                        </div>

                        {{-- Password Strength Indicator --}}
                        <x-material-ui.password-strength model="newPassword" />

                        <div class="flex justify-center md:justify-end pt-4">
                            <button type="button" @click="submitPassword()" :disabled="submittingPw"
                                class="w-full md:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:opacity-50">
                                <span x-show="!submittingPw">{{ $isGoogleUser ? 'Set Password' : 'Update Password' }}</span>
                                <span x-show="submittingPw"><i class="fa-solid fa-spinner fa-spin mr-2"></i>{{ $isGoogleUser ? 'Setting...' : 'Updating...' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Section 3: Notification Preferences-->
            <div class="w-full p-4 flex flex-col md:flex-row pb-12 md:pb-24">
                <!--Description Panel-->
                <div class="section-description w-full md:flex-1 flex-col justify-start text-justify mb-6 md:mb-0">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Notification Preferences</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 md:pr-24">Choose which updates you'd like to
                        be
                        notified about and how you'd like to receive them.</p>
                </div>
                <!--Form Details-->
                <div class="form-description w-full md:flex-1">
                    <div class="space-y-6 mt-0 md:mt-6">
                        <!-- Account & Security -->
                        <div
                            class="flex items-start justify-between py-4 border-b border-gray-200 dark:border-gray-800">
                            <div class="flex-1 pr-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                    Account & Security
                                </h3>
                                <p class="text-sm text-gray-400 dark:text-gray-500">
                                    Get notifications about login activity, password changes, or account updates.
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <!-- Toggle Switch -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" data-toggle-id="account-security">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Email Notifications -->
                        <div
                            class="flex items-start justify-between py-4 border-b border-gray-200 dark:border-gray-800">
                            <div class="flex-1 pr-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                    Email Notifications
                                </h3>
                                <p class="text-sm text-gray-400 dark:text-gray-500">
                                    Receive email notifications for important updates
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <!-- Toggle Switch -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" data-toggle-id="email-notifications">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Appointment Updates -->
                        <div
                            class="flex items-start justify-between py-4 border-b border-gray-200 dark:border-gray-800">
                            <div class="flex-1 pr-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                    Appointment Updates
                                </h3>
                                <p class="text-sm text-gray-400 dark:text-gray-500">
                                    Get notified before your scheduled cleaning sessions.
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <!-- Toggle Switch -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" data-toggle-id="appointment-updates">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Service Updates -->
                        <div
                            class="flex items-start justify-between py-4 border-b border-gray-200 dark:border-gray-800">
                            <div class="flex-1 pr-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                    Service Updates
                                </h3>
                                <p class="text-sm text-gray-400 dark:text-gray-500">
                                    Stay informed about changes or updates to your booked services.
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <!-- Toggle Switch -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" data-toggle-id="service-updates">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Note -->
                        <div class="pt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                <span class="font-medium">Note:</span> Notification preferences will be functional in
                                <span class="italic">future updates</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!--Section 5: Delete Account-->
            <div class="w-full p-4 flex flex-col md:flex-row pt-12 md:pt-24">
                <!--Description Panel-->
                <div class="section-description w-full md:flex-1 flex-col justify-start text-justify mb-6 md:mb-0">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Delete Account</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 md:pr-24">No longer want to use our service?
                        You can
                        delete your account here. This action is not reversible. All information related to this account
                        will be deleted permanently.</p>
                </div>
                <!--Form Details-->
                <div class="form-description w-full md:flex-1">
                    <div class="mt-0 md:mt-6">
                        <button type="button"
                            class="w-full md:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-800">
                            Yes, delete my account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
 <script>
        // Smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Toggle password visibility
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const svg = button.querySelector('svg');

            if (input.getAttribute('type') === 'password') {
                input.setAttribute('type', 'text');
                // Change to eye-slash icon (hidden)
                svg.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
            `;
            } else {
                input.setAttribute('type', 'password');
                // Change to eye icon (visible)
                svg.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
            }
        }

        // Save toggle state to localStorage
        function saveToggleState(toggle) {
            const toggleId = toggle.dataset.toggleId;
            const isChecked = toggle.checked;
            localStorage.setItem('toggle_' + toggleId, isChecked ? 'true' : 'false');
        }

        // Load toggle states from localStorage on page load
        function loadToggleStates() {
            const toggles = document.querySelectorAll('input[type="checkbox"][data-toggle-id]');
            toggles.forEach(toggle => {
                const toggleId = toggle.dataset.toggleId;
                const savedState = localStorage.getItem('toggle_' + toggleId);
                
                // If there's a saved state, apply it
                if (savedState !== null) {
                    toggle.checked = savedState === 'true';
                } else {
                    // Default: unchecked
                    toggle.checked = false;
                }

                // Add event listener to save state on change
                toggle.addEventListener('change', function() {
                    saveToggleState(this);
                });
            });
        }

        // Load states when page loads
        document.addEventListener('DOMContentLoaded', loadToggleStates);
    </script>
    </x-skeleton-page>
</x-layouts.general-client>