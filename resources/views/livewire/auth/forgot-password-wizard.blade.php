<div class="w-full">


    {{-- Blue subheader (mirrors legacy forgot-password layout) --}}
    <p class="font-sans font-bold text-sm text-center text-[#0077FF] mb-2">
        @switch($step)
            @case(1) Can't remember your password? @break
            @case(2) One more security check @break
            @case(3) Confirm it's really you @break
            @case(4) Almost there @break
        @endswitch
    </p>

    {{-- Title & description --}}
    <h1 class="font-sans font-black text-2xl sm:text-3xl lg:text-4xl py-4 text-blue-950">
        @switch($step)
            @case(1) Forgot Password @break
            @case(2) Verify Identity @break
            @case(3) Enter Verification Code @break
            @case(4) Set New Password @break
        @endswitch
    </h1>
    <p class="text-[#07185788] font-sans font-normal text-sm sm:text-sm lg:text-base mb-6">
        @switch($step)
            @case(1) You're one step closer. Fill in the following details to reset your password. @break
            @case(2) Your account is linked to a Google account. Please verify your identity through Google to continue. @break
            @case(3) A 6-digit verification code has been sent to your linked Google account email. Enter it below. @break
            @case(4) Create a new password for your account. @break
        @endswitch
    </p>

    {{-- Inline alerts --}}
    @if ($errorMessage)
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
            {{ $errorMessage }}
        </div>
    @endif
    @if ($successMessage)
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">
            {{ $successMessage }}
        </div>
    @endif

    {{-- ============ STEP 1: EMAIL ============ --}}
    @if ($step === 1)
        @php
            $emailTrim = trim($email);
            $formatValid = (bool) filter_var($emailTrim, FILTER_VALIDATE_EMAIL);
            if (! $formatValid || $emailExists === null) {
                $state = 'reset';
            } elseif ($emailExists) {
                $state = 'found';
            } else {
                $state = 'not-found';
            }
            $borderColor = match ($state) {
                'found' => '#22c55e',
                'not-found' => '#ef4444',
                default => '',
            };
            $labelColor = match ($state) {
                'found' => '#22c55e',
                'not-found' => '#ef4444',
                default => '',
            };
        @endphp
        <form wire:submit.prevent="submitEmail" class="space-y-3">
            <div class="input-container w-full relative">
                <span id="fp-email-icon"
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 transition-all duration-150 flex items-center justify-center"
                    style="color: {{ $state === 'found' ? '#22c55e' : ($state === 'not-found' ? '#ef4444' : '#0077FF') }};">
                    <span wire:loading.remove wire:target="email,checkEmailExists">
                        @if ($state === 'found')
                            <i class="fa-solid fa-circle-check"></i>
                        @elseif ($state === 'not-found')
                            <i class="fa-solid fa-circle-xmark"></i>
                        @else
                            <i class="fa-solid fa-envelope"></i>
                        @endif
                    </span>
                    <span wire:loading wire:target="email,checkEmailExists" style="color:#9ca3af">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                    </span>
                </span>
                <input type="email" id="fp-email" placeholder=" "
                    wire:model.live.debounce.500ms="email"
                    class="input-field text-sm"
                    style="border-color: {{ $borderColor }};"
                    autofocus>
                <label for="fp-email" style="color: {{ $labelColor }};">Email Address</label>
            </div>

            <div class="text-[11px] h-5 flex items-center gap-1 ml-1 transition-all">
                <span wire:loading wire:target="email,checkEmailExists" class="text-gray-400 flex items-center gap-1">
                    <i class="fa-solid fa-spinner fa-spin"></i><span>Checking...</span>
                </span>
                <span wire:loading.remove wire:target="email,checkEmailExists">
                    @if ($state === 'not-found')
                        <span class="text-red-500">No account found with this email</span>
                    @elseif ($emailTrim !== '' && ! $formatValid)
                        <span class="text-red-500">Please enter a valid email address</span>
                    @endif
                </span>
            </div>

            @error('email')
                <div class="text-xs text-red-600">{{ $message }}</div>
            @enderror

            <div class="flex flex-row w-full justify-center items-center mt-8 gap-4">
                <a href="{{ route('login') }}"
                    class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-300 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm text-center transition-all">
                    Back to Login
                </a>
                <button type="submit"
                    class="w-full sm:w-auto px-10 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center transition-all disabled:opacity-60"
                    wire:loading.attr="disabled" wire:target="submitEmail">
                    <span wire:loading.remove wire:target="submitEmail">Continue</span>
                    <span wire:loading wire:target="submitEmail">Please wait...</span>
                </button>
            </div>
        </form>
    @endif

    {{-- ============ STEP 2: GOOGLE VERIFY ============ --}}
    @if ($step === 2)
        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 sm:p-6 mb-5 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-white shadow flex items-center justify-center mb-4">
                <img src="https://developers.google.com/identity/images/g-logo.png" class="w-7 h-7" alt="Google">
            </div>
            <h3 class="text-base font-bold text-blue-950 mb-2">Google Account Verification</h3>
            <p class="text-[13px] text-[#07185788] leading-5 whitespace-pre-line">This is part of our 3-Factor Authentication to ensure your account security.

Factor 1: Email identification
Factor 2: Google account verification
Factor 3: Email OTP code</p>
        </div>

        <a href="{{ route('forgot.password.new.google') }}?email={{ urlencode($email) }}"
            class="flex items-center justify-center bg-white border border-gray-300 rounded-full h-12 hover:bg-gray-50 transition text-sm">
            <img src="https://developers.google.com/identity/images/g-logo.png" class="w-5 h-5 mr-3" alt="">
            <span class="text-[#081032] font-semibold font-sans">Verify with Google</span>
        </a>
    @endif

    {{-- ============ STEP 3: OTP ============ --}}
    @if ($step === 3)
        <div x-data="{
            digits: ['', '', '', '', '', ''],
            verifying: false,
            maybeVerify() {
                if (this.verifying) return;
                if (this.digits.every(d => d && d.length === 1)) {
                    this.verifying = true;
                    @this.call('verifyOtp').finally(() => { this.verifying = false; });
                }
            },
            handleInput(e, i) {
                let v = (e.target.value || '').replace(/\D/g, '');
                if (v.length > 1) v = v.slice(-1);
                this.digits[i] = v;
                e.target.value = v;
                @this.set('otp', this.digits.join(''));
                if (v && i < 5) {
                    this.$nextTick(() => this.$refs['d' + (i + 1)]?.focus());
                }
                this.maybeVerify();
            },
            handlePaste(e, i) {
                const text = (e.clipboardData || window.clipboardData).getData('text') || '';
                const arr = text.replace(/\D/g, '').slice(0, 6).split('');
                if (!arr.length) return;
                e.preventDefault();
                for (let j = 0; j < 6; j++) this.digits[j] = arr[j] || '';
                for (let j = 0; j < 6; j++) {
                    if (this.$refs['d' + j]) this.$refs['d' + j].value = this.digits[j];
                }
                @this.set('otp', this.digits.join(''));
                const last = Math.min(arr.length - 1, 5);
                this.$nextTick(() => this.$refs['d' + last]?.focus());
                this.maybeVerify();
            },
            handleKey(e, i) {
                if (e.key === 'Backspace' && !this.digits[i] && i > 0) {
                    this.$refs['d' + (i - 1)]?.focus();
                }
            },
            clearAll() {
                this.digits = ['', '', '', '', '', ''];
                for (let j = 0; j < 6; j++) {
                    if (this.$refs['d' + j]) this.$refs['d' + j].value = '';
                }
                @this.set('otp', '');
                this.verifying = false;
                this.$nextTick(() => this.$refs.d0?.focus());
            }
        }" x-init="$nextTick(() => $refs.d0?.focus())"
           @web-fp-resent.window="clearAll()"
           @web-fp-otp-clear.window="clearAll()">
            <div class="flex justify-center gap-2 sm:gap-2.5 mb-6">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" inputmode="numeric" maxlength="1"
                        x-ref="d{{ $i }}"
                        :value="digits[{{ $i }}]"
                        wire:ignore
                        @input="handleInput($event, {{ $i }})"
                        @paste="handlePaste($event, {{ $i }})"
                        @keydown="handleKey($event, {{ $i }})"
                        class="w-10 h-12 sm:w-12 sm:h-14 rounded-xl border-2 bg-gray-100 text-center text-xl sm:text-2xl font-bold text-[#081032] focus:border-[#0077FF] focus:bg-blue-50 focus:outline-none transition"
                        :class="digits[{{ $i }}] ? 'border-[#0077FF] bg-blue-50' : 'border-gray-200'">
                @endfor
            </div>

            <button type="button" wire:click="verifyOtp"
                class="w-full px-10 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center transition-all disabled:opacity-60"
                wire:loading.attr="disabled" wire:target="verifyOtp">
                <span wire:loading.remove wire:target="verifyOtp">Verify Code</span>
                <span wire:loading wire:target="verifyOtp">Verifying...</span>
            </button>

            <div x-data="{ countdown: 60, timer: null, start() { clearInterval(this.timer); this.timer = setInterval(() => { if (this.countdown > 0) this.countdown--; else clearInterval(this.timer); }, 1000); } }"
                x-init="start()"
                @web-fp-resent.window="countdown = 60; start()"
                class="text-center mt-4">
                <button type="button" wire:click="resendOtp" :disabled="countdown > 0"
                    :class="countdown > 0 ? 'text-slate-400 cursor-not-allowed' : 'text-[#0077FF] hover:underline'"
                    class="text-sm font-medium">
                    <span x-show="countdown > 0">Resend code in <span x-text="countdown"></span>s</span>
                    <span x-show="countdown === 0" x-cloak>Resend verification code</span>
                </button>
            </div>
        </div>
    @endif

    {{-- ============ STEP 4: NEW PASSWORD ============ --}}
    @if ($step === 4)
        <form wire:submit.prevent="resetPassword" class="space-y-4" id="fp-step4-form">
            <div class="input-container w-full relative">
                <i class="fa-solid fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500 z-10"></i>
                <input type="password" wire:model.defer="newPassword" id="fp-pw" placeholder=" "
                    class="input-field text-sm"
                    autocomplete="new-password" autofocus
                    oninput="window.fpUpdateHints && window.fpUpdateHints()">
                <label for="fp-pw">New Password</label>
                <button type="button" id="fp-pw-toggle" tabindex="-1"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 z-10">
                    <i class="fa-solid fa-eye-slash" id="fp-pw-icon"></i>
                </button>
            </div>

            <div class="input-container w-full relative">
                <i class="fa-solid fa-square-check absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500 z-10"></i>
                <input type="password" wire:model.defer="confirmPassword" id="fp-cpw" placeholder=" "
                    class="input-field text-sm"
                    autocomplete="new-password"
                    oninput="window.fpUpdateHints && window.fpUpdateHints()">
                <label for="fp-cpw">Confirm New Password</label>
                <button type="button" id="fp-cpw-toggle" tabindex="-1"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 z-10">
                    <i class="fa-solid fa-eye-slash" id="fp-cpw-icon"></i>
                </button>
            </div>

            {{-- Password strength hints --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-1.5 text-[13px]">
                @php
                    $hints = [
                        ['id' => 'min',     'label' => 'At least 8 characters'],
                        ['id' => 'upper',   'label' => 'One uppercase letter'],
                        ['id' => 'num',     'label' => 'One number'],
                        ['id' => 'special', 'label' => 'One special character (e.g. @, #, !)'],
                        ['id' => 'match',   'label' => 'Passwords match'],
                    ];
                @endphp
                @foreach ($hints as $h)
                    <div class="flex items-center" data-fp-hint="{{ $h['id'] }}">
                        <span class="w-4 mr-2.5 text-center text-slate-300 fp-hint-mark">&bull;</span>
                        <span class="text-slate-400 fp-hint-label">{{ $h['label'] }}</span>
                    </div>
                @endforeach
            </div>

            @error('newPassword')
                <div class="text-xs text-red-600">{{ $message }}</div>
            @enderror

            <button type="submit"
                class="w-full px-10 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center transition-all disabled:opacity-60"
                wire:loading.attr="disabled" wire:target="resetPassword">
                <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                <span wire:loading wire:target="resetPassword">Resetting...</span>
            </button>
        </form>

    @endif

    {{-- Step-4 wiring (lives outside @if so it survives Livewire morphs) --}}
    <script>
        (function () {
            if (window.__fpStep4Bound) return;
            window.__fpStep4Bound = true;

            function init() {
                var pw = document.getElementById('fp-pw');
                var cpw = document.getElementById('fp-cpw');
                if (!pw || !cpw) return;
                if (pw.dataset.fpBound === '1') return;
                pw.dataset.fpBound = '1';

                window.fpUpdateHints = function () {
                        var v = pw.value || '';
                        var c = cpw.value || '';
                        var checks = {
                            min:     v.length >= 8,
                            upper:   /[A-Z]/.test(v),
                            num:     /\d/.test(v),
                            special: /[^A-Za-z0-9]/.test(v),
                            match:   v.length > 0 && v === c,
                        };
                        Object.keys(checks).forEach(function (k) {
                            var row = document.querySelector('[data-fp-hint="' + k + '"]');
                            if (!row) return;
                            var mark = row.querySelector('.fp-hint-mark');
                            var label = row.querySelector('.fp-hint-label');
                            if (checks[k]) {
                                mark.textContent = '\u2713';
                                mark.classList.remove('text-slate-300');
                                mark.classList.add('text-green-500');
                                label.classList.remove('text-slate-400');
                                label.classList.add('text-green-500');
                            } else {
                                mark.textContent = '\u2022';
                                mark.classList.add('text-slate-300');
                                mark.classList.remove('text-green-500');
                                label.classList.add('text-slate-400');
                                label.classList.remove('text-green-500');
                            }
                        });
                    };

                    function bindToggle(btnId, inputEl, iconId) {
                        var btn = document.getElementById(btnId);
                        var icon = document.getElementById(iconId);
                        if (!btn || !icon) return;
                        btn.addEventListener('click', function () {
                            if (inputEl.type === 'password') {
                                inputEl.type = 'text';
                                icon.classList.remove('fa-eye-slash');
                                icon.classList.add('fa-eye');
                            } else {
                                inputEl.type = 'password';
                                icon.classList.remove('fa-eye');
                                icon.classList.add('fa-eye-slash');
                            }
                        });
                    }
                bindToggle('fp-pw-toggle', pw, 'fp-pw-icon');
                bindToggle('fp-cpw-toggle', cpw, 'fp-cpw-icon');

                window.fpUpdateHints();
            }

            // Try once on initial load (in case we landed directly on step 4)
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            // Re-init after every Livewire DOM update (covers step 3 -> step 4 morph)
            document.addEventListener('livewire:load', function () {
                if (window.Livewire && window.Livewire.hook) {
                    window.Livewire.hook('message.processed', function () { init(); });
                }
            });
        })();
    </script>

    <script>
        window.addEventListener('web-fp-success', () => {
            setTimeout(() => { window.location.href = "{{ route('login') }}"; }, 2000);
        });
        window.addEventListener('fp-error', (e) => {
            const d = e.detail || {};
            const payload = Array.isArray(d) ? d[0] : d;
            if (window.showErrorDialog) {
                window.showErrorDialog(payload.title || 'Error', payload.message || 'Something went wrong.');
            }
        });
        window.addEventListener('fp-success', (e) => {
            const d = e.detail || {};
            const payload = Array.isArray(d) ? d[0] : d;
            if (window.showSuccessDialog) {
                window.showSuccessDialog(payload.title || 'Success', payload.message || '');
            }
        });
    </script>

    {{-- Bottom progress bars (mirrors legacy forgot-password layout) --}}
    <div class="w-full mt-8 flex justify-center space-x-2">
        @foreach ([1, 2, 3, 4] as $s)
            <div class="h-1.5 w-16 sm:w-20 rounded-full {{ $step >= $s ? 'bg-[#0077FF]' : 'bg-gray-300' }}"></div>
        @endforeach
    </div>
</div>
