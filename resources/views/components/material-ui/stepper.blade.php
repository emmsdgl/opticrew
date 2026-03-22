{{--
    Stepper Component
    Compact step indicator with gradient active step, glow, and connector lines.

    Usage:
    <x-material-ui.stepper :steps="['Upload', 'Details', 'Confirm']" model="currentStep" />
--}}
@props([
    'steps' => [],
    'model' => null,
    'step' => 1,
])

@php
    $currentExpr = $model ?? $step;
@endphp

<div class="flex items-center w-full">
    @foreach($steps as $index => $label)
        @php $num = $index + 1; @endphp

        {{-- Step circle + label --}}
        <div class="flex flex-col items-center gap-1.5 flex-shrink-0">
            {{-- Completed --}}
            <div x-show="{{ $currentExpr }} > {{ $num }}"
                 class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white"
                 style="background: #2563eb; border: 2px solid #2563eb; box-shadow: 0 1px 3px rgba(37,99,235,0.2);">
                <i class="fa-solid fa-check text-[10px]"></i>
            </div>

            {{-- Active --}}
            <div x-show="{{ $currentExpr }} === {{ $num }}"
                 class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white"
                 style="background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8); border: 2px solid #60a5fa; box-shadow: 0 0 12px rgba(59,130,246,0.5), 0 0 4px rgba(59,130,246,0.3);">
                {{ $num }}
            </div>

            {{-- Inactive --}}
            <div x-show="{{ $currentExpr }} < {{ $num }}"
                 class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800">
                {{ $num }}
            </div>

            <span class="text-xs font-semibold"
                :class="{{ $currentExpr }} >= {{ $num }} ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500'">
                {{ $label }}
            </span>
        </div>

        {{-- Connector line --}}
        @if($index < count($steps) - 1)
            <div class="flex-1 mx-3 mb-5 h-[2px] rounded-full transition-colors duration-300"
                :class="{{ $currentExpr }} > {{ $num }} ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'">
            </div>
        @endif
    @endforeach
</div>
