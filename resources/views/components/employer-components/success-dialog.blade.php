@props([
    'title' => 'Success',
    'message' => '',
    'buttonText' => 'Continue',
    'buttonUrl' => null,
])

<div x-show="showSuccess"
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
    <div x-show="showSuccess"
         x-transition:enter="transition ease-out duration-300 delay-100"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="relative w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-600 shadow-2xl overflow-hidden p-3">

        <div class="px-8 pt-10 pb-8 flex flex-col items-center text-center">
            <!-- Checkmark Icon -->
            <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 border-2 border-emerald-400 dark:border-emerald-500 flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <!-- Title -->
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2"
                x-text="successTitle || '{{ $title }}'"></h3>

            <!-- Message -->
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed"
               x-text="successMessage || '{{ $message }}'"></p>
        </div>

        <!-- Button -->
        <div class="px-8 pb-8">
            @if($buttonUrl)
                <a href="{{ $buttonUrl }}"
                   class="block w-full text-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all duration-200">
                    {{ $buttonText }}
                </a>
            @else
                <button @click="if (successRedirectUrl) { window.location.href = successRedirectUrl; } else { showSuccess = false; }"
                        type="button"
                        class="w-full px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold text-sm rounded-xl transition-all duration-200">
                    <span x-text="successButtonText || '{{ $buttonText }}'">{{ $buttonText }}</span>
                </button>
            @endif
        </div>
    </div>
</div>
