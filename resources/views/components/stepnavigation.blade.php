@props([
    'currentStep' => 1,
    'totalSteps' => 3,
    'previousUrl' => null,
    'nextUrl' => null,
    'previousText' => 'Previous',
    'nextText' => 'Next',
    'submitText' => 'Submit',
    'showSubmit' => false
])

<div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
    <!-- Previous Button -->
    @if($currentStep > 1)
        <a href="{{ $previousUrl }}" 
           class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 
                  rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 
                  transition-colors duration-200 inline-flex items-center gap-2">
            <i class="fi fi-rr-angle-left"></i>
            {{ $previousText }}
        </a>
    @else
        <button type="button" 
                class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 
                       rounded-lg cursor-not-allowed inline-flex items-center gap-2"
                disabled>
            <i class="fi fi-rr-angle-left"></i>
            {{ $previousText }}
        </button>
    @endif

    <!-- Next/Submit Button -->
    @if($showSubmit || $currentStep >= $totalSteps)
        <button type="submit" 
                class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white 
                       rounded-lg transition-colors duration-200 inline-flex items-center gap-2">
            {{ $submitText }}
            <i class="fi fi-rr-check"></i>
        </button>
    @else
        <a href="{{ $nextUrl }}" 
           class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white 
                  rounded-lg transition-colors duration-200 inline-flex items-center gap-2">
            {{ $nextText }}
            <i class="fi fi-rr-angle-right"></i>
        </a>
    @endif
</div>