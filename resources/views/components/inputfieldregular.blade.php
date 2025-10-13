@props([
    'label' => '',
    'inputId' => '',
    'inputName' => '',
    'inputType' => 'text',
    'placeholder' => '',
    'icon' => null,
    'xModel' => '',
    'required' => false,
    'value' => '',
    'disabled' => false
])

<div class="flex flex-col w-full mb-6">
    <!-- Label -->
    @if($label)
    <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium text-primary-dark dark:text-white">
        {{ $label }}
    </label>
    @endif

    <!-- Input Container -->
    <div class="relative">
        <!-- Icon -->
        @if($icon)
        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
            {!! $icon !!}
        </div>
        @endif

        <!-- Input Field -->
        <input
            type="{{ $inputType }}"
            id="{{ $inputId }}"
            name="{{ $inputName }}"
            @if($xModel) x-model="{{ $xModel }}" @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($value) value="{{ $value }}" @endif
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                   focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                   dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                   dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500
                   {{ $icon ? 'ps-10' : 'ps-3' }}
                   {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
        />
    </div>
</div>