{{--
    Material Input Field Component
    Floating label with icon, subtle glow on focus, dark mode support.

    Usage:
    <x-material-ui.input-field
        label="Email Address"
        type="email"
        model="formData.email"
        icon="fi fi-rr-envelope"
        placeholder="Ex: yourname@example.com"
        required
    />
--}}
@props([
    'label' => '',
    'type' => 'text',
    'model' => null,
    'name' => null,
    'icon' => null,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'maxlength' => null,
    'min' => null,
    'max' => null,
    'readonly' => false,
])

@php
    $uid = 'mif_' . uniqid();
    $hasIcon = !empty($icon);
@endphp

<div class="mui-input-group relative" x-data="{ focused: false, filled: false }" x-init="filled = {{ $model ? "(!!{$model})" : 'false' }}">
    {{-- Icon --}}
    @if($hasIcon)
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 z-[1] text-blue-600 dark:text-blue-600">
            <i class="{{ $icon }} text-sm"></i>
        </span>
    @endif

    {{-- Input --}}
    @if($type === 'textarea')
        <textarea
            id="{{ $uid }}"
            @if($model) x-model="{{ $model }}" @endif
            @if($name) name="{{ $name }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            @focus="focused = true"
            @blur="focused = false; filled = !!$el.value"
            @input="filled = !!$el.value"
            placeholder="{{ $placeholder }}"
            class="mui-input peer w-full {{ $hasIcon ? 'pl-10' : 'pl-4' }} pr-4 {{ $label ? 'pt-5 pb-2' : 'py-2.5' }} text-sm
                   border border-gray-400 dark:border-gray-700 rounded-xl
                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                   placeholder-transparent
                   transition-all duration-200
                   focus:outline-none focus:border-blue-500 dark:focus:border-blue-400
                   focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)] dark:focus:shadow-[0_0_0_3px_rgba(96,165,250,0.1)]
                   disabled:opacity-50 disabled:cursor-not-allowed
                   resize-none"
            {{ $attributes }}
        ></textarea>
    @else
        <input
            id="{{ $uid }}"
            type="{{ $type }}"
            @if($model) x-model="{{ $model }}" @endif
            @if($name) name="{{ $name }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            @if($min !== null) min="{{ $min }}" @endif
            @if($max !== null) max="{{ $max }}" @endif
            @focus="focused = true"
            @blur="focused = false; filled = !!$el.value"
            @input="filled = !!$el.value"
            placeholder="{{ $placeholder }}"
            class="mui-input peer w-full {{ $hasIcon ? 'pl-10' : 'pl-4' }} pr-4 {{ $label ? 'pt-5 pb-2' : 'py-2.5' }} text-sm
                   border border-gray-400 dark:border-gray-700 rounded-xl
                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                   placeholder-transparent
                   transition-all duration-200
                   focus:outline-none focus:border-blue-500 dark:focus:border-blue-400
                   focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)] dark:focus:shadow-[0_0_0_3px_rgba(96,165,250,0.1)]
                   disabled:opacity-50 disabled:cursor-not-allowed"
            {{ $attributes }}
        >
    @endif

    {{-- Floating Label --}}
    @if($label)
        <label for="{{ $uid }}"
            class="absolute {{ $hasIcon ? 'left-10' : 'left-4' }} pointer-events-none
                   transition-all duration-200 origin-left
                   peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:text-sm
                   peer-focus:top-1.5 peer-focus:translate-y-0 peer-focus:text-[11px] peer-focus:text-blue-500 dark:peer-focus:text-blue-400
                   text-gray-400 dark:text-gray-500"
            :class="(focused || filled || {{ $model ? $model : 'false' }}) ? 'top-1.5 translate-y-0 text-[11px] ' + (focused ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500') : 'top-1/2 -translate-y-1/2 text-sm'">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
</div>

@once
<style>
/* Ensure placeholder-shown works correctly */
.mui-input::placeholder {
    color: transparent;
}
.mui-input:focus::placeholder {
    color: transparent;
}
</style>
@endonce
