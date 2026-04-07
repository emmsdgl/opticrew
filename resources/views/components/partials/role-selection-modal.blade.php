{{-- Role Selection Modal — shown when user clicks "Create account" on login page --}}
<div id="roleSelectionModal"
    class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-[200] flex items-center justify-center p-4"
    style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-sm w-full min-h-[28rem] max-h-[90vh] overflow-y-auto scrollbar-custom relative flex flex-col">
        {{-- Close Button --}}
        <button type="button" onclick="closeRoleSelectionModal()"
            class="absolute top-4 right-4 z-20 w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full transition-colors">
            <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor"
                viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Modal Body — flex-1 + center for vertical & horizontal centering --}}
        <div class="flex-1 flex flex-col items-center justify-center p-6 text-center">
            <p class="text-sm w-full text-gray-900 dark:text-white">
                Creating account as an
            </p>
            <p class="text-3xl font-bold mt-2 w-full text-gray-900 dark:text-white">
                Choose your role
            </p>

            <p class="flex flex-col text-sm text-gray-600 dark:text-gray-400 leading-relaxed mt-4 mb-6">
                <span class="font-normal">Select the type of account you want to create.</span>
                <span class="font-normal">Each role has its own registration process.</span>
            </p>

            {{-- Role Buttons --}}
            <div class="space-y-3 w-full px-2">
                <a href="{{ route('signup') }}?role=contracted_client"
                    class="w-full flex items-center justify-center gap-3 py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-full transition-colors no-underline">
                    <i class="fas fa-building"></i>
                    Contracted Client
                </a>

                <a href="{{ route('signup') }}?role=private_client"
                    class="w-full flex items-center justify-center gap-3 py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-full transition-colors no-underline">
                    <i class="fas fa-user"></i>
                    Private Client
                </a>

                <a href="{{ route('recruitment') }}"
                    class="w-full flex items-center justify-center gap-3 py-3 px-4 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-blue-600 dark:text-blue-400 text-sm font-semibold rounded-full transition-colors border border-blue-600 dark:border-blue-400 no-underline">
                    <i class="fas fa-briefcase"></i>
                    Applicant
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function openRoleSelectionModal() {
        const modal = document.getElementById('roleSelectionModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeRoleSelectionModal() {
        const modal = document.getElementById('roleSelectionModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Close on backdrop click
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('roleSelectionModal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeRoleSelectionModal();
                }
            });
        }
    });
</script>
