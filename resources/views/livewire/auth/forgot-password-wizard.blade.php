<div class="w-full">

    {{-- Step progress dots --}}
    <div class="flex items-center justify-center mb-2">
        @foreach ([1, 2, 3, 4] as $s)
            <div class="flex items-center">
                <div @class([
                    'w-8 h-8 rounded-full border-2 flex items-center justify-center text-[13px] font-semibold transition',
                    'bg-slate-100 border-slate-200 text-slate-400' => $step < $s,
                    'bg-[#0077FF] border-[#0077FF] text-white' => $step >= $s,
                ])>
                    @if ($step > $s)
                        &#10003;
                    @else
                        {{ $s }}
                    @endif
                </div>
                @if ($s < 4)
                    <div @class([
                        'w-8 sm:w-12 h-0.5 transition',
                        'bg-[#0077FF]' => $step > $s,
                        'bg-slate-200' => $step <= $s,
                    ])></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Step labels --}}
    <div class="flex justify-between px-1 mb-7 text-[11px] font-medium">
        @foreach (['Email', 'Google', 'OTP', 'Reset'] as $i => $label)
            <span class="w-12 text-center {{ $step >= $i + 1 ? 'text-[#0077FF]' : 'text-slate-400' }}">
                {{ $label }}
            </span>
        @endforeach
    </div>

    {{-- Title & description --}}
    <h1 class="font-sans font-bold text-2xl sm:text-3xl lg:text-4xl mb-2 text-blue-950">
        @switch($step)
            @case(1) Forgot Password @break
            @case(2) Verify Identity @break
            @case(3) Enter Verification Code @break
            @case(4) Set New Password @break
        @endswitch
    </h1>
    <p class="text-[#07185788] font-sans font-normal text-sm sm:text-sm lg:text-base mb-6">
        @switch($step)
            @case(1) Enter the email address associated with your account. @break
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
        <form wire:submit.prevent="submitEmail" class="space-y-4">
            <div class="flex items-center bg-gray-100 rounded-lg px-4 h-14 border border-transparent focus-within:border-[#0077FF] transition">
                <i class="fa-regular fa-envelope text-[#0077FF] mr-3"></i>
                <input type="email" wire:model.defer="email" placeholder="Email"
                    class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none text-sm text-[#081032]"
                    autofocus>
            </div>
            @error('email')
                <div class="text-xs text-red-600">{{ $message }}</div>
            @enderror

            <button type="submit"
                class="w-full text-sm py-3 px-4 border border-gray-300 rounded-full font-sans font-semibold text-white bg-[#0077FF] hover:bg-blue-700 transition disabled:opacity-60"
                wire:loading.attr="disabled" wire:target="submitEmail">
                <span wire:loading.remove wire:target="submitEmail">Continue</span>
                <span wire:loading wire:target="submitEmail">Please wait...</span>
            </button>
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
            handleInput(e, i) {
                let v = e.target.value.replace(/\D/g, '');
                if (v.length > 1) {
                    const arr = v.slice(0, 6).split('');
                    for (let j = 0; j < 6; j++) this.digits[j] = arr[j] || '';
                    this.sync();
                    const last = Math.min(arr.length - 1, 5);
                    this.$refs['d' + last]?.focus();
                    return;
                }
                this.digits[i] = v;
                this.sync();
                if (v && i < 5) this.$refs['d' + (i + 1)]?.focus();
            },
            handleKey(e, i) {
                if (e.key === 'Backspace' && !this.digits[i] && i > 0) {
                    this.$refs['d' + (i - 1)]?.focus();
                }
            },
            sync() { @this.set('otp', this.digits.join('')); },
            clearAll() {
                this.digits = ['', '', '', '', '', ''];
                this.sync();
                this.$refs.d0?.focus();
            }
        }" x-init="$nextTick(() => $refs.d0?.focus())" @web-fp-resent.window="clearAll()">
            <div class="flex justify-center gap-2 sm:gap-2.5 mb-6">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" inputmode="numeric" maxlength="{{ $i === 0 ? 6 : 1 }}"
                        x-ref="d{{ $i }}"
                        x-model="digits[{{ $i }}]"
                        @input="handleInput($event, {{ $i }})"
                        @keydown="handleKey($event, {{ $i }})"
                        class="w-10 h-12 sm:w-12 sm:h-14 rounded-xl border-2 bg-gray-100 text-center text-xl sm:text-2xl font-bold text-[#081032] focus:border-[#0077FF] focus:bg-blue-50 focus:outline-none transition"
                        :class="digits[{{ $i }}] ? 'border-[#0077FF] bg-blue-50' : 'border-gray-200'">
                @endfor
            </div>

            <button type="button" wire:click="verifyOtp"
                class="w-full text-sm py-3 px-4 border border-gray-300 rounded-full font-sans font-semibold text-white bg-[#0077FF] hover:bg-blue-700 transition disabled:opacity-60"
                wire:loading.attr="disabled" wire:target="verifyOtp">
                <span wire:loading.remove wire:target="verifyOtp">Verify Code</span>
                <span wire:loading wire:target="verifyOtp">Verifying...</span>
            </button>

            <div x-data="{ countdown: 60 }"
                x-init="let t = setInterval(() => { if (countdown > 0) countdown--; else clearInterval(t); }, 1000)"
                @web-fp-resent.window="countdown = 60"
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
            <div class="flex items-center bg-gray-100 rounded-lg px-4 h-14 border border-transparent focus-within:border-[#0077FF] transition">
                <i class="fa-solid fa-key text-[#0077FF] mr-3"></i>
                <input type="password" wire:model.defer="newPassword" id="fp-pw" placeholder="New password"
                    class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none text-sm text-[#081032]"
                    autocomplete="new-password" autofocus
                    oninput="window.fpUpdateHints && window.fpUpdateHints()">
                <button type="button" id="fp-pw-toggle" class="p-2 text-gray-500" tabindex="-1">
                    <i class="fa-solid fa-eye-slash" id="fp-pw-icon"></i>
                </button>
            </div>

            <div class="flex items-center bg-gray-100 rounded-lg px-4 h-14 border border-transparent focus-within:border-[#0077FF] transition">
                <i class="fa-solid fa-key text-[#0077FF] mr-3"></i>
                <input type="password" wire:model.defer="confirmPassword" id="fp-cpw" placeholder="Confirm new password"
                    class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none text-sm text-[#081032]"
                    autocomplete="new-password"
                    oninput="window.fpUpdateHints && window.fpUpdateHints()">
                <button type="button" id="fp-cpw-toggle" class="p-2 text-gray-500" tabindex="-1">
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
                class="w-full text-sm py-3 px-4 border border-gray-300 rounded-full font-sans font-semibold text-white bg-[#0077FF] hover:bg-blue-700 transition disabled:opacity-60"
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

    {{-- Back to login link --}}
    <div class="text-center mt-6">
        @if ($step > 1)
            <button type="button" wire:click="backTo({{ $step - 1 }})"
                class="text-xs text-[#07185788] hover:text-[#0077FF] mr-3">
                &larr; Previous step
            </button>
        @endif
        <a href="{{ route('login') }}" class="text-sm text-[#07185788]">
            Back to <span class="text-[#0077FF] font-semibold">Login</span>
        </a>
    </div>

    <script>
        window.addEventListener('web-fp-success', () => {
            setTimeout(() => { window.location.href = "{{ route('login') }}"; }, 2000);
        });
    </script>
</div>
