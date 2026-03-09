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
        successRedirectUrl: ''
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
     ">

    <x-employer-components.success-dialog />
    <x-employer-components.error-dialog />
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

    window.showErrorDialog = function(title, message, buttonText, redirectUrl) {
        window.dispatchEvent(new CustomEvent('show-error-dialog', {
            detail: { title: title, message: message, buttonText: buttonText, redirectUrl: redirectUrl }
        }));
    };
</script>
