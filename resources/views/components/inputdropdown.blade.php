@props([
    'label' => '',
    'default' => '',
    'options' => [],
    'id' => null,
])

@php
    $uniqueId = $id ?? uniqid('inputdropdown_');
@endphp

<div class="input-container w-full relative mb-4">
    <!-- Icon -->
    <i class="fas fa-question-circle absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500 z-10"></i>
    
    <!-- Dropdown Button -->
    <button 
        id="{{ $uniqueId }}_button"
        type="button"
        class="input-field w-full pl-12 pr-12 py-3 bg-gray-100 rounded-xl text-left text-sm
               focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 transition-all duration-200">
        <span id="{{ $uniqueId }}_selected" 
              class="block {{ $default === 'Select a Security Question' ? 'text-gray-400' : 'text-gray-700 dark:text-white' }}">
            {{ $default }}
        </span>
    </button>
        
    <!-- Arrow Icon -->
    <i id="{{ $uniqueId }}_arrow"
       class="fas fa-chevron-up absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 
              transition-transform duration-200 z-10 pointer-events-none rotate-180"></i>

    <!-- Dropdown Menu -->
    <div 
        id="{{ $uniqueId }}_menu"
        class="absolute left-0 right-0 top-full mt-2 z-20 bg-white border border-gray-200 rounded-xl shadow-lg
               opacity-0 invisible transform scale-y-95 origin-top transition-all duration-200 
               dark:bg-gray-700 dark:border-gray-600 max-h-60 overflow-y-auto">
        <ul class="py-2">
            @foreach ($options as $option)
                <li>
                    <button 
                        type="button"
                        data-value="{{ $option }}"
                        class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-200 
                               hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-150
                               {{ $option === $default ? 'bg-gray-50 dark:bg-gray-600 font-medium' : '' }}">
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
    const label = button.nextElementSibling;
    const options = menu.querySelectorAll('button[data-value]');

    // Handle label floating on focus/value
    const updateLabel = () => {
        if (selected.textContent !== 'Select a Security Question') {
            label.classList.add('active');
        } else {
            label.classList.remove('active');
        }
    };

    // Initial label state
    updateLabel();

    // Toggle dropdown
    button.addEventListener('click', (e) => {
        e.stopPropagation();
        const isHidden = menu.classList.contains('invisible');

        if (isHidden) {
            // Open menu - arrow points up
            menu.classList.remove('invisible', 'opacity-0', 'scale-y-95');
            menu.classList.add('opacity-100', 'scale-y-100');
            arrow.classList.remove('rotate-180');
            arrow.classList.add('rotate-0');
            label.classList.add('active');
        } else {
            // Close menu - arrow points down
            menu.classList.add('invisible', 'opacity-0', 'scale-y-95');
            menu.classList.remove('opacity-100', 'scale-y-100');
            arrow.classList.remove('rotate-0');
            arrow.classList.add('rotate-180');
            updateLabel();
        }
    });

    // Handle selection
    options.forEach(option => {
        option.addEventListener('click', (e) => {
            const value = e.target.dataset.value;
            
            // Update selected text and styling
            selected.textContent = value;
            selected.classList.remove('text-gray-400');
            selected.classList.add('text-[#071957]');
            
            // Remove active state from all options
            options.forEach(opt => {
                opt.classList.remove('bg-gray-50', 'dark:bg-gray-600', 'font-medium');
            });
            
            // Add active state to selected option
            e.target.classList.add('bg-gray-50', 'dark:bg-gray-600', 'font-medium');
            
            // Close menu - arrow points down
            menu.classList.add('invisible', 'opacity-0', 'scale-y-95');
            menu.classList.remove('opacity-100', 'scale-y-100');
            arrow.classList.remove('rotate-0');
            arrow.classList.add('rotate-180');
            
            // Update label
            updateLabel();
            
            // Dispatch custom event for parent components to listen to
            button.dispatchEvent(new CustomEvent('dropdown-changed', { 
                detail: { value },
                bubbles: true 
            }));
        });
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!button.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('invisible', 'opacity-0', 'scale-y-95');
            menu.classList.remove('opacity-100', 'scale-y-100');
            arrow.classList.remove('rotate-0');
            arrow.classList.add('rotate-180');
            updateLabel();
        }
    });
});
</script>
@endpush