@props([
    'label' => 'Show:',
    'default' => 'Now',
    'options' => [],
    'id' => null, // optional id

])

@php
    $uniqueId = uniqid('dropdown_'); // unique per instance
@endphp

<div class="relative inline-block">
    <!-- Sort Button -->
    <button 
        id="{{ $uniqueId }}_button"
        type="button"
        class="bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none
               focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-3 inline-flex justify-between items-center gap-2
               dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-blue-800 transition-all duration-300">
        <span class="text-gray-700 dark:text-white text-xs font-normal">{{ $label }}</span>
        <span id="{{ $uniqueId }}_selected" class="text-gray-700 dark:text-white text-xs font-normal">{{ $default }}</span>
        <svg id="{{ $uniqueId }}_arrow"
            class="w-2.5 h-2.5 ms-2 transition-transform duration-300 text-gray-600 dark:text-gray-400"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 10 6">
            <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 1 4 4 4-4" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div 
        id="{{ $uniqueId }}_menu"
        class="absolute right-0 w-full top-full mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-lg
               opacity-0 invisible transform scale-y-0 origin-top transition-all duration-300 dark:bg-gray-700">
        <ul class="py-2 text-xs text-gray-700 dark:text-white">
            @foreach ($options as $option)
                <li>
                    <button data-value="{{ $option }}"
                        class="w-full text-left px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        {{ $option }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const id = @json($uniqueId);
    const button = document.getElementById(`${id}_button`);
    const menu = document.getElementById(`${id}_menu`);
    const arrow = document.getElementById(`${id}_arrow`);
    const selected = document.getElementById(`${id}_selected`);
    const options = menu.querySelectorAll('button[data-value]');

    // Toggle dropdown
    button.addEventListener('click', (e) => {
        e.stopPropagation();
        const isHidden = menu.classList.contains('invisible');

        if (isHidden) {
            menu.classList.remove('invisible', 'opacity-0', 'scale-y-0');
            menu.classList.add('opacity-100', 'scale-y-100');
            arrow.classList.add('rotate-180');
        } else {
            menu.classList.add('invisible', 'opacity-0', 'scale-y-0');
            menu.classList.remove('opacity-100', 'scale-y-100');
            arrow.classList.remove('rotate-180');
        }
    });

    // Handle selection
    options.forEach(option => {
        option.addEventListener('click', (e) => {
            const value = e.target.dataset.value;
            selected.textContent = value;
            menu.classList.add('invisible', 'opacity-0', 'scale-y-0');
            menu.classList.remove('opacity-100', 'scale-y-100');
            arrow.classList.remove('rotate-180');
        });
    });

    // Close when clicking outside
    window.addEventListener('click', (e) => {
        if (!menu.contains(e.target) && !button.contains(e.target)) {
            menu.classList.add('invisible', 'opacity-0', 'scale-y-0');
            menu.classList.remove('opacity-100', 'scale-y-100');
            arrow.classList.remove('rotate-180');
        }
    });
});
</script>
@endpush
@stack('scripts')
