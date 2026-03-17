{{-- Reusable Confirmation Dialog (apply-modal style + spring animation) --}}
@props([
    'show' => 'showConfirm',
    'icon' => 'fa-solid fa-paper-plane',
    'iconBg' => 'bg-blue-50 dark:bg-blue-900/30',
    'iconColor' => 'text-blue-500',
    'title' => 'Are you sure?',
    'cancelText' => 'Cancel',
    'confirmText' => 'Confirm',
    'confirmClass' => 'flex-1 py-2 rounded-xl text-xs font-bold bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-700 transition-colors',
    'onCancel' => '',
    'onConfirm' => '',
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
        @click.stop
        x-transition:enter="dialog-spring-in"
        x-transition:leave="dialog-spring-out"
        class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center"
    >
        <div class="w-12 h-12 rounded-full {{ $iconBg }} flex items-center justify-center mx-auto mb-3">
            <i class="{{ $icon }} {{ $iconColor }} text-lg"></i>
        </div>
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">{{ $title }}</h3>
        <div class="text-[11px] text-gray-500 dark:text-gray-400 mb-5">
            {{ $slot }}
        </div>
        <div class="flex gap-3">
            <button type="button" @click="{{ $onCancel }}"
                class="flex-1 py-2 rounded-xl text-xs font-semibold border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                {{ $cancelText }}
            </button>
            @if(isset($confirmButton))
                {{ $confirmButton }}
            @else
                <button type="button" @click="{{ $onConfirm }}"
                    class="{{ $confirmClass }}">
                    {{ $confirmText }}
                </button>
            @endif
        </div>
    </div>
</div>
