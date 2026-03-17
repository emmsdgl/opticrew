{{-- Read-only Feedback Viewer Modal (mirrors client feedback form but disabled) --}}
@props([
    'show' => 'showFeedbackViewer',
    'onClose' => 'showFeedbackViewer = false',
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
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="{{ $onClose }}"></div>

    {{-- Card --}}
    <div
        x-show="{{ $show }}"
        @click.stop
        x-transition:enter="dialog-spring-in"
        x-transition:leave="dialog-spring-out"
        class="relative bg-white dark:bg-gray-900 py-6 px-3 rounded-2xl shadow-2xl w-full max-w-md border border-gray-100 dark:border-gray-800 overflow-hidden max-h-[90vh] overflow-y-auto"
    >
        {{-- Close button --}}
        <button type="button" @click="{{ $onClose }}"
            class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-900 dark:bg-gray-800 text-white rounded-full hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors z-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="p-6 sm:p-12 sm:py-8">
            {{-- Header --}}
            <div class="text-center flex flex-col gap-2">
                <p class="text-xs text-gray-500 dark:text-gray-400 tracking-wide"
                    x-text="viewingFeedback?.type === 'employee' ? 'Employee Feedback View' : 'Client Feedback View'"></p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight my-2">
                    Service Feedback
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mx-auto"
                    x-text="'Submitted by ' + (viewingFeedback?.submittedBy || 'Unknown')"></p>
            </div>

            {{-- Emoji Rating (read-only) --}}
            <div class="flex justify-center items-end gap-2 sm:gap-3 my-9 mt-6">
                @php
                    $emojis = [
                        1 => asset('images/icons/emojis/Very-Dissatisfied.svg'),
                        2 => asset('images/icons/emojis/Dissatisfied.svg'),
                        3 => asset('images/icons/emojis/Neutral.svg'),
                        4 => asset('images/icons/emojis/Satisfied.svg'),
                        5 => asset('images/icons/emojis/Very-Satisfied.svg')
                    ];
                    $ratingLabels = [1 => 'Very Dissatisfied', 2 => 'Dissatisfied', 3 => 'Neutral', 4 => 'Satisfied', 5 => 'Very Satisfied'];
                @endphp
                @foreach($emojis as $rating => $emojiSrc)
                    <div class="relative flex flex-col items-center transition-all duration-200">
                        <div class="rounded-full flex items-center justify-center transition-all duration-200"
                            :class="(viewingFeedback?.rating || 0) === {{ $rating }}
                                ? 'bg-blue-600 dark:bg-blue-500 ring-4 ring-blue-200 dark:ring-blue-900 w-14 h-14 sm:w-16 sm:h-16'
                                : 'bg-gray-200 dark:bg-gray-800 w-12 h-12 sm:w-14 sm:h-14'">
                            <img src="{{ $emojiSrc }}" alt="Rating {{ $rating }}"
                                :class="(viewingFeedback?.rating || 0) === {{ $rating }} ? 'w-8 h-8 sm:w-10 sm:h-10' : 'w-6 h-6 sm:w-8 sm:h-8 grayscale opacity-60'"
                                class="transition-all duration-200">
                        </div>
                        <span x-show="(viewingFeedback?.rating || 0) === {{ $rating }}" x-transition
                            class="absolute -bottom-8 text-xs font-semibold text-white bg-blue-600 dark:bg-blue-500 px-3 py-1 rounded-full whitespace-nowrap shadow-lg">
                            {{ $rating }}.0 {{ $ratingLabels[$rating] }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Keyword Tags (read-only, hidden when empty) --}}
            <div class="mt-12 mb-4" x-show="viewingFeedback?.tags?.length > 0">
                <div class="flex flex-wrap justify-center gap-3">
                    <template x-for="(tag, idx) in (viewingFeedback?.tags || [])" :key="'view-tag-' + idx">
                        <span class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs font-medium border rounded-full bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 border-gray-900 dark:border-gray-100"
                            x-text="tag"></span>
                    </template>
                </div>
            </div>

            {{-- Detailed Review (read-only) --}}
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-4">
                    Detailed Review
                </label>
                <div class="w-full rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-4 py-3 text-sm text-gray-900 dark:text-white min-h-[70px]">
                    <p x-text="viewingFeedback?.comment || 'No comments provided'" class="italic"
                        :class="viewingFeedback?.comment ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500'"></p>
                </div>
            </div>

            {{-- Disabled buttons --}}
            <div class="flex items-center justify-between w-full gap-2 mt-2">
                <button type="button" disabled
                    class="flex-1 px-6 py-3 text-xs font-medium text-white rounded-full bg-blue-600 dark:bg-blue-800 opacity-50 cursor-not-allowed">
                    Submit Feedback
                </button>
                <span class="flex-1 text-xs text-blue-400 dark:text-blue-600 font-medium whitespace-nowrap text-center opacity-50 cursor-not-allowed">
                    Want to File a report?
                </span>
            </div>

            {{-- Service Report Section (shown only if report data exists) --}}
            <div x-show="viewingFeedback?.report" class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center">
                        <i class="fa-solid fa-flag text-red-500 text-xs"></i>
                    </div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white">Service Report Filed</h4>
                </div>

                {{-- Concern Type --}}
                <div class="mb-3">
                    <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-1">Type of Concern</label>
                    <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400"
                        x-text="viewingFeedback?.report?.concernType || 'N/A'"></span>
                </div>

                {{-- Report Detail --}}
                <div class="mb-3">
                    <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-1">Report Details</label>
                    <div class="w-full rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-3 py-2 text-sm min-h-[50px]">
                        <p class="text-gray-700 dark:text-gray-300 italic text-xs"
                            x-text="viewingFeedback?.report?.details || 'No details provided'"></p>
                    </div>
                </div>

                {{-- Report Date --}}
                <p class="text-[11px] text-gray-400 dark:text-gray-500">
                    <i class="fa-regular fa-clock mr-1"></i>
                    Reported on: <span class="font-semibold" x-text="viewingFeedback?.report?.submittedAt || 'N/A'"></span>
                </p>
            </div>

            {{-- Date Submitted Footer --}}
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fa-regular fa-clock mr-1"></i>
                    Date Submitted: <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="viewingFeedback?.submittedAt || 'N/A'"></span>
                </p>
            </div>
        </div>
    </div>
</div>
