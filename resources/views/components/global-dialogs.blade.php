{{-- Global Success, Error & Confirmation Dialogs --}}
{{-- Include this partial in layouts or standalone pages to enable window.showSuccessDialog(), window.showErrorDialog(), and window.showConfirmDialog() --}}

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
        confirmCancelText: '',
        confirmResolveCallback: null,
        confirmRejectCallback: null
     }"
     x-on:show-success-dialog.window="
        successTitle = $event.detail.title || 'Success';
        successMessage = $event.detail.message || '';
        successButtonText = $event.detail.buttonText || 'Continue';
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
        confirmTitle = $event.detail.title || 'Confirm Action';
        confirmMessage = $event.detail.message || 'Are you sure you want to proceed?';
        confirmButtonText = $event.detail.confirmText || 'Confirm';
        confirmCancelText = $event.detail.cancelText || 'Cancel';
        confirmResolveCallback = $event.detail.onConfirm || null;
        confirmRejectCallback = $event.detail.onCancel || null;
        showConfirm = true;
     ">

    <x-employer-components.success-dialog />
    <x-employer-components.error-dialog />
    <x-employer-components.confirmation-dialog />
</div>

<script>
    /**
     * Global dialog API
     * Usage:
     *   window.showSuccessDialog('Title', 'Message', 'Button Text')
     *   window.showErrorDialog('Title', 'Message', 'Button Text')
     *   window.showConfirmDialog({ title, message, confirmText, cancelText, onConfirm, onCancel })
     */
    window.showSuccessDialog = function(title, message, buttonText, redirectUrl) {
        window.dispatchEvent(new CustomEvent('show-success-dialog', {
            detail: { title: title, message: message, buttonText: buttonText, redirectUrl: redirectUrl }
        }));
    };

    window.showErrorDialog = function(title, message, buttonText, redirectUrl) {
        window.dispatchEvent(new CustomEvent('show-error-dialog', {
            detail: { title: title, message: message, buttonText: buttonText, redirectUrl: redirectUrl }
        }));
    };

    window.showConfirmDialog = function(options) {
        window.dispatchEvent(new CustomEvent('show-confirm-dialog', {
            detail: {
                title: options.title || 'Confirm Action',
                message: options.message || 'Are you sure you want to proceed?',
                confirmText: options.confirmText || 'Confirm',
                cancelText: options.cancelText || 'Cancel',
                onConfirm: options.onConfirm || null,
                onCancel: options.onCancel || null
            }
        }));
    };
</script>
