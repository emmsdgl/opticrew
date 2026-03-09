@props([
    'title' => 'Error',
    'message' => '',
    'buttonText' => 'Close',
    'buttonUrl' => null,
])

<div x-show="showError"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[60] flex items-center justify-center p-4"
     style="display: none;">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm"></div>

    <!-- Dialog Card -->
    <div x-show="showError"
         x-transition:enter="transition ease-out duration-300 delay-100"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="relative w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-600 shadow-2xl overflow-hidden p-3">

        <div class="px-8 pt-10 pb-8 flex flex-col items-center text-center">
            <!-- X Icon -->
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 border-2 border-red-400 dark:border-red-500 flex items-center justify-center mb-6">
                <svg class="w-6 h-6 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <!-- Title -->
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2"
                x-text="errorTitle || '{{ $title }}'"></h3>

            <!-- Message -->
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed"
               x-text="errorMessage || '{{ $message }}'"></p>
        </div>

        <!-- Button -->
        <div class="px-8 pb-8">
            @if($buttonUrl)
                <a href="{{ $buttonUrl }}"
                   class="block w-full text-center px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold rounded-xl shadow-lg shadow-red-500/25 transition-all duration-200 text-sm">
                    {{ $buttonText }}
                </a>
            @else
                <button @click="if (errorRedirectUrl) { window.location.href = errorRedirectUrl; } else { showError = false; }"
                        type="button"
                        class="w-full px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold text-sm rounded-xl transition-all duration-200">
                    <span x-text="errorButtonText || '{{ $buttonText }}'">{{ $buttonText }}</span>
                </button>
            @endif
        </div>
    </div>
</div>
