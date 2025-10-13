@props([
    'title' => '',
    'description' => '',
    'value' => '',
    'name' => 'card-selection',
    'checked' => false,
    'xModel' => ''
])

<label class="relative block cursor-pointer">
    <!-- Hidden Checkbox -->
    <input
        type="radio"
        name="{{ $name }}"
        value="{{ $value }}"
        @if($xModel) x-model="{{ $xModel }}" @endif
        @if($checked) checked @endif
        class="sr-only peer"
    />

    <!-- Card -->
    <div class="relative rounded-2xl border-2 p-6 transition-all duration-200
                border-gray-200 bg-white
                peer-checked:border-blue-500 peer-checked:bg-blue-50
                hover:border-gray-300 peer-checked:hover:border-blue-600
                dark:border-gray-700 dark:bg-gray-800
                dark:peer-checked:border-blue-400 dark:peer-checked:bg-blue-950
                dark:hover:border-gray-600">
        
        <!-- Title -->
        <h3 class="text-base font-semibold mb-2 
                   text-gray-900 peer-checked:text-blue-900
                   dark:text-white dark:peer-checked:text-blue-100">
            {{ $title }}
        </h3>

        <!-- Description -->
        <p class="text-xs
                  text-gray-500 peer-checked:text-blue-700
                  dark:text-gray-400 dark:peer-checked:text-blue-300">
            {{ $description }}
        </p>

        <!-- Checkmark Indicator (appears when selected) -->
        <div class="absolute top-4 right-4 w-6 h-6 rounded-full 
                    opacity-0 peer-checked:opacity-100 transition-opacity duration-200
                    bg-blue-500 dark:bg-blue-400
                    flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
    </div>
</label>