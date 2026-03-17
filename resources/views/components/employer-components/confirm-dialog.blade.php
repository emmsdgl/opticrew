@props([
    'title' => 'Confirm',
    'message' => '',
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
    <div class="absolute inset-0 bg-black/30 dark:bg-black/50" @click="showConfirm = false; if (window.__confirmReject) { window.__confirmReject(); window.__confirmReject = null; window.__confirmResolve = null; }"></div>

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
            <!-- Warning Icon -->
            <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 border-2 border-amber-400 dark:border-amber-500 flex items-center justify-center mb-6">
                <svg class="w-7 h-7 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
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
            <button @click="showConfirm = false; if (window.__confirmReject) { window.__confirmReject(); window.__confirmReject = null; window.__confirmResolve = null; }"
                    type="button"
                    class="w-full px-6 py-3 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-semibold text-sm rounded-xl transition-all duration-200">
                <span x-text="confirmCancelText || '{{ $cancelText }}'">{{ $cancelText }}</span>
            </button>
            <button @click="showConfirm = false; if (window.__confirmResolve) { window.__confirmResolve(); window.__confirmResolve = null; window.__confirmReject = null; }"
                    type="button"
                    class="w-full px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold text-sm rounded-xl transition-all duration-200">
                <span x-text="confirmButtonText || '{{ $confirmText }}'">{{ $confirmText }}</span>
            </button>
        </div>
    </div>
</div>
