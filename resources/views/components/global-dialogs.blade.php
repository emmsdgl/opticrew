{{-- Global Success, Error & Confirm Dialogs (using components/dialogs style) --}}
{{-- Include in layouts to enable window.showSuccessDialog(), window.showErrorDialog(), window.showConfirmDialog() --}}

<div id="global-dialog-container"
     x-data="{
        showSuccess: false,
        successTitle: '',
        successMessage: '',
        successButtonText: '',
        successRedirectUrl: '',
        showError: false,
        errorTitle: '',
        errorMessage: '',
        errorButtonText: '',
        errorRedirectUrl: '',
        showConfirm: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmButtonText: '',
        confirmCancelText: '',
        showPasswordConfirm: false,
        passwordConfirmTitle: '',
        passwordConfirmMessage: '',
        passwordConfirmButtonText: '',
        passwordConfirmCancelText: '',
        passwordConfirmValue: '',
        passwordConfirmError: ''
     }"
     x-on:show-success-dialog.window="
        successTitle = $event.detail.title || 'Success';
        successMessage = $event.detail.message || '';
        successButtonText = $event.detail.buttonText || 'Done';
        successRedirectUrl = $event.detail.redirectUrl || '';
        showSuccess = true;
     "
     x-on:show-error-dialog.window="
        errorTitle = $event.detail.title || 'Error';
        errorMessage = $event.detail.message || '';
        errorButtonText = $event.detail.buttonText || 'Close';
        errorRedirectUrl = $event.detail.redirectUrl || '';
        showError = true;
     "
     x-on:show-confirm-dialog.window="
        confirmTitle = $event.detail.title || 'Are you sure?';
        confirmMessage = $event.detail.message || '';
        confirmButtonText = $event.detail.confirmText || 'Confirm';
        confirmCancelText = $event.detail.cancelText || 'Cancel';
        showConfirm = true;
     "
     x-on:show-password-confirm-dialog.window="
        passwordConfirmTitle = $event.detail.title || 'Admin Verification';
        passwordConfirmMessage = $event.detail.message || 'Enter your admin password to confirm.';
        passwordConfirmButtonText = $event.detail.confirmText || 'Confirm';
        passwordConfirmCancelText = $event.detail.cancelText || 'Cancel';
        passwordConfirmValue = '';
        passwordConfirmError = '';
        showPasswordConfirm = true;
        $nextTick(() => { const el = document.getElementById('global-password-confirm-input'); if (el) el.focus(); });
     "
     x-on:password-confirm-error.window="passwordConfirmError = $event.detail.error;"
     x-on:close-password-confirm.window="showPasswordConfirm = false;"
     x-on:set-password-confirm-error.window="passwordConfirmError = $event.detail.error;">

    {{-- ── Success Dialog ── --}}
    <div x-show="showSuccess" style="display:none"
        class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div x-show="showSuccess" x-transition:enter="dialog-spring-in" x-transition:leave="dialog-spring-out"
            class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center">
            <div class="w-12 h-12 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1" x-text="successTitle"></h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-5" x-text="successMessage"></p>
            <button type="button"
                @click="if (successRedirectUrl) { window.location.href = successRedirectUrl; } else { showSuccess = false; }"
                class="w-full py-2 rounded-xl text-xs font-bold bg-green-600 text-white hover:bg-green-700 transition-colors"
                x-text="successButtonText"></button>
        </div>
    </div>

    {{-- ── Error Dialog ── --}}
    <div x-show="showError" style="display:none"
        class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div x-show="showError" x-transition:enter="dialog-spring-in" x-transition:leave="dialog-spring-out"
            class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center">
            <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-circle-xmark text-red-500 text-xl"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1" x-text="errorTitle"></h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-5" x-text="errorMessage"></p>
            <button type="button"
                @click="if (errorRedirectUrl) { window.location.href = errorRedirectUrl; } else { showError = false; }"
                class="w-full py-2 rounded-xl text-xs font-bold border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                x-text="errorButtonText"></button>
        </div>
    </div>

    {{-- ── Confirm Dialog ── --}}
    <div x-show="showConfirm" style="display:none"
        class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div x-show="showConfirm" x-transition:enter="dialog-spring-in" x-transition:leave="dialog-spring-out"
            class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center">
            <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-question text-blue-500 text-xl"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1" x-text="confirmTitle"></h3>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-5" x-text="confirmMessage"></p>
            <div class="flex gap-3">
                <button type="button" @click="showConfirm = false; if (window.__confirmReject) window.__confirmReject();"
                    class="flex-1 py-2 rounded-xl text-xs font-semibold border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                    x-text="confirmCancelText"></button>
                <button type="button" @click="showConfirm = false; if (window.__confirmResolve) window.__confirmResolve();"
                    class="flex-1 py-2 rounded-xl text-xs font-bold bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors"
                    x-text="confirmButtonText"></button>
            </div>
        </div>
    </div>

    {{-- ── Password Confirm Dialog (component) ── --}}
    <x-dialogs.password-confirm-dialog
        show="showPasswordConfirm"
        :title="''"
        cancelText="Cancel"
        confirmText="Confirm"
        passwordModel="passwordConfirmValue"
        errorModel="passwordConfirmError"
        onCancel="showPasswordConfirm = false; if (window.__passwordConfirmReject) window.__passwordConfirmReject();"
        onConfirm="if (window.__passwordConfirmSubmit) window.__passwordConfirmSubmit();"
    >
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1" x-text="passwordConfirmTitle"></h3>
        <p class="text-[11px] text-gray-500 dark:text-gray-400" x-text="passwordConfirmMessage"></p>
    </x-dialogs.password-confirm-dialog>
</div>

@once
@push('styles')
<style>
@keyframes dialogSpringIn {
    0%   { opacity: 0; transform: scale(0.3); }
    50%  { opacity: 1; transform: scale(1.06); }
    70%  { transform: scale(0.96); }
    85%  { transform: scale(1.02); }
    100% { transform: scale(1); }
}
@keyframes dialogSpringOut {
    0%   { opacity: 1; transform: scale(1); }
    100% { opacity: 0; transform: scale(0.3); }
}
.dialog-spring-in  { animation: dialogSpringIn 0.4s cubic-bezier(0.34,1.56,0.64,1) both; }
.dialog-spring-out { animation: dialogSpringOut 0.2s ease-in both; }
</style>
@endpush
@endonce

<script>
    window.showSuccessDialog = function(title, message, buttonText, redirectUrl) {
        window.dispatchEvent(new CustomEvent('show-success-dialog', {
            detail: { title, message, buttonText, redirectUrl }
        }));
    };

    window.showErrorDialog = function(title, message, buttonText, redirectUrl) {
        window.dispatchEvent(new CustomEvent('show-error-dialog', {
            detail: { title, message, buttonText, redirectUrl }
        }));
    };

    window.showConfirmDialog = function(title, message, confirmText, cancelText) {
        return new Promise((resolve, reject) => {
            window.__confirmResolve = resolve;
            window.__confirmReject = reject;
            window.dispatchEvent(new CustomEvent('show-confirm-dialog', {
                detail: { title, message, confirmText, cancelText }
            }));
        });
    };

    window.showPasswordConfirmDialog = function(title, message, confirmText, cancelText) {
        return new Promise((resolve, reject) => {
            window.__passwordConfirmReject = reject;
            window.__pcvState = '';
            window.__passwordConfirmSubmit = function() {
                const input = document.getElementById('global-password-confirm-input');
                const password = input ? input.value : '';
                if (!password) {
                    window.dispatchEvent(new CustomEvent('password-confirm-error', { detail: { error: 'Please enter your password.' } }));
                    return;
                }
                if (window.__pcvState === 'invalid') {
                    window.dispatchEvent(new CustomEvent('password-confirm-error', { detail: { error: 'Incorrect password.' } }));
                    return;
                }
                resolve(password);
                window.dispatchEvent(new CustomEvent('close-password-confirm'));
            };
            window.dispatchEvent(new CustomEvent('show-password-confirm-dialog', {
                detail: { title, message, confirmText, cancelText }
            }));
        });
    };

    // ── Real-time Password Validation for Password Confirm Dialog ──
    (function() {
        var pcvTimer = null;
        var prevPcv = '';
        var adminUserId = @json(auth()->id());
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        function setPcvMsg(type, text) {
            var el = document.getElementById('password-confirm-validation');
            if (!el) return;
            if (!text) { el.style.display = 'none'; el.innerHTML = ''; return; }
            var colors = { loading: 'text-gray-400', valid: 'text-green-600', invalid: 'text-red-500' };
            var icons = { loading: '<i class="fa-solid fa-spinner fa-spin"></i>', valid: '<i class="fa-solid fa-circle-check"></i>', invalid: '<i class="fa-solid fa-circle-xmark"></i>' };
            el.className = 'mt-1.5 text-[11px] h-4 flex items-center gap-1 ' + (colors[type] || '');
            el.innerHTML = (icons[type] || '') + '<span>' + text + '</span>';
            el.style.display = 'flex';
        }

        function checkAdminPassword() {
            var input = document.getElementById('global-password-confirm-input');
            if (!input) return;
            var pw = input.value;
            if (!pw) { setPcvMsg(null, ''); window.__pcvState = ''; prevPcv = ''; updateInputStyle(input, ''); return; }
            if (pw === prevPcv) return;
            prevPcv = pw;
            setPcvMsg('loading', 'Verifying...');
            window.__pcvState = 'checking';
            updateInputStyle(input, '');

            fetch('{{ route("auth.validate-login") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ user_id: adminUserId, login: '', password: pw })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (!input || input.value !== pw) return;
                if (d.password_checked) {
                    if (d.password_valid) {
                        setPcvMsg('valid', 'Password verified');
                        window.__pcvState = 'valid';
                        updateInputStyle(input, 'valid');
                    } else {
                        setPcvMsg('invalid', 'Incorrect password');
                        window.__pcvState = 'invalid';
                        updateInputStyle(input, 'invalid');
                    }
                }
            })
            .catch(function() { setPcvMsg(null, ''); window.__pcvState = ''; updateInputStyle(input, ''); });
        }

        function updateInputStyle(input, state) {
            input.style.borderColor = '';
            if (state === 'valid') input.style.borderColor = '#22c55e';
            else if (state === 'invalid') input.style.borderColor = '#ef4444';
        }

        document.addEventListener('input', function(e) {
            if (e.target && e.target.id === 'global-password-confirm-input') {
                window.__pcvState = '';
                prevPcv = '';
                updateInputStyle(e.target, '');
                setPcvMsg(null, '');
                clearTimeout(pcvTimer);
                if (e.target.value) {
                    pcvTimer = setTimeout(checkAdminPassword, 400);
                }
            }
        });

        // Reset state when dialog opens
        window.addEventListener('show-password-confirm-dialog', function() {
            prevPcv = '';
            window.__pcvState = '';
            setPcvMsg(null, '');
            clearTimeout(pcvTimer);
        });

        // Reset state when dialog closes
        window.addEventListener('close-password-confirm', function() {
            prevPcv = '';
            window.__pcvState = '';
            setPcvMsg(null, '');
            clearTimeout(pcvTimer);
        });
    })();
</script>
