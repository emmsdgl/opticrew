{{-- Reusable Success Dialog (apply-modal style + spring animation) --}}
@props([
    'show' => 'showSuccess',
    'title' => 'Success!',
    'buttonText' => 'Done',
])

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

<div
    x-show="{{ $show }}"
    style="display:none"
    class="fixed inset-0 z-[120] flex items-center justify-center p-4"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Card --}}
    <div
        x-show="{{ $show }}"
        x-transition:enter="dialog-spring-in"
        x-transition:leave="dialog-spring-out"
        class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center"
    >
        <div class="w-12 h-12 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
        </div>
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">{{ $title }}</h3>
        <div class="text-[11px] text-gray-500 dark:text-gray-400 mb-5">
            {{ $slot }}
        </div>
        <button type="button"
            {{ $attributes->merge(['class' => 'w-full py-2 rounded-xl text-xs font-bold bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-700 transition-colors']) }}>
            {{ $buttonText }}
        </button>
    </div>
</div>
