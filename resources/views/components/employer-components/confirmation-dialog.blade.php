@props([
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
])

<div x-show="showConfirm"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[60] flex items-center justify-center p-4"
     style="display: none;">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm"
         @click="showConfirm = false; if (confirmRejectCallback) confirmRejectCallback();"></div>

    <!-- Dialog Card -->
    <div x-show="showConfirm"
         x-transition:enter="transition ease-out duration-300 delay-100"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="relative w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-600 shadow-2xl overflow-hidden p-3">

        <div class="px-8 pt-10 pb-8 flex flex-col items-center text-center">
            <!-- Checkmark Icon (matches success-dialog) -->
            <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 border-2 border-emerald-400 dark:border-emerald-500 flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <!-- Title -->
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2"
                x-text="confirmTitle || '{{ $title }}'"></h3>

            <!-- Message -->
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed"
               x-text="confirmMessage || '{{ $message }}'"></p>
        </div>

        <!-- Buttons -->
        <div class="px-8 pb-8 flex gap-3">
            <button @click="showConfirm = false; if (confirmRejectCallback) confirmRejectCallback();"
                    type="button"
                    class="flex-1 px-6 py-3 bg-white hover:bg-gray-50 dark:bg-slate-700 dark:hover:bg-slate-600 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-gray-300 font-semibold text-sm rounded-xl transition-all duration-200">
                <span x-text="confirmCancelText || '{{ $cancelText }}'">{{ $cancelText }}</span>
            </button>
            <button @click="showConfirm = false; if (confirmResolveCallback) confirmResolveCallback();"
                    type="button"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-indigo-500/25 transition-all duration-200">
                <span x-text="confirmButtonText || '{{ $confirmText }}'">{{ $confirmText }}</span>
            </button>
        </div>
    </div>
</div>
