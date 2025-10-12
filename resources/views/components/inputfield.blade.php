@props([
    'label' => '',
    'inputId' => '',
    'inputName' => '',
    'inputType' => '',
    'icon' => ''
])

<div class="input-container w-full relative">
    <!-- Icon -->
    <i class="fa-solid {{ $icon }} absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500 z-10"></i>

    <!-- Input -->
    <input
        type="{{ $inputType }}"
        id="{{ $inputId }}"
        name="{{ $inputName }}"
        placeholder=" "
        class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl border border-transparent
               focus:outline-none focus:border-blue-500 text-gray-700"
    />

    <!-- Floating Label -->
    <label for="{{ $inputId }}" class="input-label">
        {{ $label }}
    </label>
</div>

@push('styles')
<style>
    .input-container {
        margin-bottom: 1.5rem;
    }

    .input-container .input-field {
        padding-left: 3rem;
        padding-top: 0.875rem;
        padding-bottom: 0.875rem;
        padding-right: 1rem;
    }

    .input-container .input-label {
        position: absolute;
        left: 3rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
        transition: all 0.2s ease-out;
        font-size: 0.875rem;
        background-color: transparent;
        padding: 0;
    }

    /* Floated label state - sits ON the border */
    .input-container .input-field:focus + .input-label,
    .input-container .input-field:not(:placeholder-shown) + .input-label {
        top: 0;
        left: 2rem;
        transform: translateY(-50%);
        font-size: 0.75rem;
        color: #0077FF;
        background-color: white;
        padding: 0 0.25rem;
    }

    /* Hide placeholder when typing or focused */
    .input-container .input-field:focus::placeholder,
    .input-container .input-field:not(:placeholder-shown)::placeholder {
        color: transparent;
    }

    /* Focus border */
    .input-container .input-field:focus {
        border-color: #0077FF;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById(@json($inputId));
    if (!input) return;

    const label = document.querySelector(`label[for="${@json($inputId)}"]`);
    if (!label) return;

    // Handle pre-filled fields or autofill
    const checkValue = () => {
        if (input.value.trim() !== '') {
            // Has value - ensure label stays floated
            label.style.top = '0';
            label.style.left = '1rem';
            label.style.transform = 'translateY(-50%)';
            label.style.fontSize = '0.75rem';
            label.style.color = '#0077FF';
            label.style.backgroundColor = 'white';
            label.style.padding = '0 0.25rem';
        }
    };

    // Check on load
    checkValue();

    // Check on input
    input.addEventListener('input', checkValue);

    // Check on blur
    input.addEventListener('blur', checkValue);
});
</script>
@endpush