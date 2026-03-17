{{-- Global Success & Error Dialogs --}}
{{-- Include this partial in layouts or standalone pages to enable window.showSuccessDialog() and window.showErrorDialog() --}}

<div id="global-dialog-container"
     x-data="{
        showSuccess: false,
        successTitle: '',
        successMessage: '',
        successButtonText: '',
        showError: false,
        errorTitle: '',
        errorMessage: '',
        errorButtonText: '',
        errorRedirectUrl: '',
        successRedirectUrl: '',
        showConfirm: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmButtonText: '',
        confirmCancelText: ''
     }"
     x-on:show-success-dialog.window="
        successTitle = $event.detail.title || 'Success';
        successMessage = $event.detail.message || '';
        successButtonText = $event.detail.buttonText || 'Continue';
        successRedirectUrl = $event.detail.redirectUrl || '';
        showSuccess = true;
     "
     x-on:show-confirm-dialog.window="
        confirmTitle = $event.detail.title || 'Are you sure?';
        confirmMessage = $event.detail.message || '';
        confirmButtonText = $event.detail.confirmText || 'Confirm';
        confirmCancelText = $event.detail.cancelText || 'Cancel';
        showConfirm = true;
     "
     x-on:show-error-dialog.window="
        errorTitle = $event.detail.title || 'Error';
        errorMessage = $event.detail.message || '';
        errorButtonText = $event.detail.buttonText || 'Close';
        errorRedirectUrl = $event.detail.redirectUrl || '';
        showError = true;
     ">

    <x-employer-components.success-dialog />
    <x-employer-components.error-dialog />
    <x-employer-components.confirm-dialog />
</div>

<script>
    /**
     * Global dialog API
     * Usage:
     *   window.showSuccessDialog('Title', 'Message', 'Button Text')
     *   window.showErrorDialog('Title', 'Message', 'Button Text')
     */
    window.showSuccessDialog = function(title, message, buttonText, redirectUrl) {
        window.dispatchEvent(new CustomEvent('show-success-dialog', {
            detail: { title: title, message: message, buttonText: buttonText, redirectUrl: redirectUrl }
        }));
    };

    /**
     * Show a confirmation dialog that returns a Promise.
     * Usage:
     *   window.showConfirmDialog('Title', 'Message', 'Confirm Text', 'Cancel Text')
     *     .then(() => { // confirmed })
     *     .catch(() => { // cancelled });
     */
    window.showConfirmDialog = function(title, message, confirmText, cancelText) {
        return new Promise((resolve, reject) => {
            window.__confirmResolve = resolve;
            window.__confirmReject = reject;
            window.dispatchEvent(new CustomEvent('show-confirm-dialog', {
                detail: {
                    title: title,
                    message: message,
                    confirmText: confirmText,
                    cancelText: cancelText
                }
            }));
        });
    };

    window.showErrorDialog = function(title, message, buttonText, redirectUrl) {
        window.dispatchEvent(new CustomEvent('show-error-dialog', {
            detail: { title: title, message: message, buttonText: buttonText, redirectUrl: redirectUrl }
        }));
    };
</script>
