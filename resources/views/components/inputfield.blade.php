@props([
    'label' => '',
    'inputId' => '',
    'inputName' => '',
    'inputType' => '',
    'icon' => ''
])

<div class="relative w-full mb-6">
    <!-- Icon -->
    <i class="fa-solid {{ $icon }} absolute top-1/2 left-3 -translate-y-1/2 text-blue-600"></i>

    <!-- Input -->
    <input
        type="{{ $inputType }}"
        id="{{ $inputId }}"
        name="{{ $inputName }}"
        placeholder=" "
        class="peer w-full rounded-lg bg-gray-100 px-10 pt-5 pb-2 border border-transparent
               focus:border-blue-600 outline-none transition-colors duration-200"
    />

    <!-- Label -->
    <label for="{{ $inputId }}"
        class="absolute left-10 top-4 text-gray-500 text-sm font-sans
               transition-all duration-200 ease-in-out
               peer-placeholder-shown:top-5 peer-placeholder-shown:text-gray-400
               peer-placeholder-shown:text-sm
               peer-focus:top-0 peer-focus:text-xs peer-focus:text-blue-600
               bg-white px-1">
        {{ $label }}
    </label>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('input-username');
    const label = document.querySelector('label[for="input-username"]');

    // Add class on focus
    input.addEventListener('focus', () => {
        label.classList.add('text-blue-600', 'top-0', 'text-xs');
        label.classList.remove('text-gray-400');
    });

    // Handle blur (when user clicks away)
    input.addEventListener('blur', () => {
        if (input.value.trim() === '') {
            // Return label to original position
            label.classList.remove('text-blue-600', 'top-0', 'text-xs');
            label.classList.add('text-gray-400', 'top-5', 'text-sm');
        }
    });

    // Maintain floating position if value pre-filled (like during edit)
    if (input.value.trim() !== '') {
        label.classList.add('text-blue-600', 'top-0', 'text-xs');
    }
});
</script>
