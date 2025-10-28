{{-- resources/views/components/client-components/quotation-page/quantity-counter.blade.php --}}
@props([
    'label' => 'Quantity',
    'name' => 'quantity',
    'min' => 1,
    'max' => 100,
    'step' => 1,
    'default' => 1,
    'icon' => 'fa-solid fa-hashtag',
    'unit' => '',
    'required' => false,
    'showUnit' => true,
])

@php
    $unitOptions = [
        ['value' => 'sqm', 'label' => 'Square Meter (m² / sqm)'],
        ['value' => 'sqft', 'label' => 'Square Foot (sq ft / ft²)'],
        ['value' => 'sqyd', 'label' => 'Square Yard (yd²)'],
        ['value' => 'are', 'label' => 'Are (a)'],
        ['value' => 'hectare', 'label' => 'Hectare (ha)'],
        ['value' => 'sqin', 'label' => 'Square Inch (in²)'],
    ];
@endphp

<div class="space-y-3" x-data="quantityCounter({
    name: '{{ $name }}',
    min: {{ $min }},
    max: {{ $max }},
    step: {{ $step }},
    default: {{ $default }},
    unit: '{{ $unit }}',
    showUnit: {{ $showUnit ? 'true' : 'false' }}
})" x-init="init()">
    
    <!-- Label -->
    <label class="block text-sm text-gray-700 dark:text-gray-300">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <!-- Counter Container -->
    <div class="flex items-stretch gap-0 border-2 border-gray-300 dark:border-gray-600 rounded-xl overflow-hidden
                bg-white dark:bg-gray-800 transition-all duration-300
                focus-within:ring-2 focus-within:ring-blue-500 dark:focus-within:ring-blue-400
                focus-within:border-blue-500 dark:focus-within:border-blue-400">
        
        <!-- Icon -->
        <div class="flex items-center justify-center px-4 bg-gray-50 dark:bg-gray-700 border-r-2 border-gray-300 dark:border-gray-600">
            <i class="{{ $icon }} text-gray-400 dark:text-gray-500"></i>
        </div>

        <!-- Decrease Button -->
        <button
            type="button"
            @click="decrement()"
            :disabled="value <= min"
            class="px-4 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 
                   border-r-2 border-gray-300 dark:border-gray-600
                   disabled:opacity-50 disabled:cursor-not-allowed
                   transition-colors duration-200 group">
            <i class="fa-solid fa-minus text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
        </button>

        <!-- Value Display -->
        <div class="flex-1 flex items-center justify-center px-4 py-3 min-w-0">
            <input
                type="number"
                :name="name"
                x-model="value"
                :min="min"
                :max="max"
                :step="step"
                @input="validateInput()"
                class="w-full text-center text-base bg-transparent border-0 focus:outline-none focus:ring-0
                       text-gray-900 dark:text-white
                       [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                readonly>
        </div>

        <!-- Increase Button -->
        <button
            type="button"
            @click="increment()"
            :disabled="value >= max"
            class="px-4 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 
                   border-l-2 border-gray-300 dark:border-gray-600
                   disabled:opacity-50 disabled:cursor-not-allowed
                   transition-colors duration-200 group">
            <i class="fa-solid fa-plus text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
        </button>
    </div>

    <!-- Unit Selector (Optional) -->
    <template x-if="showUnit && unitOptions.length > 0">
        <div class="relative">
            <button
                type="button"
                @click="unitOpen = !unitOpen"
                class="w-full px-4 py-3 text-left border-2 border-gray-300 dark:border-gray-600 rounded-xl
                       focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                       bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                       hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300"
                :class="{ 'border-blue-600 dark:border-blue-500 bg-blue-50 dark:bg-gray-700': unitOpen }">
                
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm" :class="!selectedUnit ? 'text-gray-400 dark:text-gray-500' : ''">
                        <span x-show="!selectedUnit">Select unit</span>
                        <span x-show="selectedUnit" x-text="getUnitLabel()"></span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-gray-400 dark:text-gray-500 text-xs transition-transform duration-200"
                       :class="{ 'rotate-180': unitOpen }"></i>
                </div>
            </button>

            <!-- Hidden Input -->
            <input type="hidden" :name="name + '_unit'" x-model="selectedUnit">

            <!-- Unit Dropdown -->
            <div
                x-show="unitOpen"
                @click.away="unitOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 
                       rounded-xl shadow-xl overflow-hidden"
                style="display: none;">
                
                <ul class="py-1 max-h-60 overflow-y-auto">
                    <template x-for="unitOption in unitOptions" :key="unitOption.value">
                        <li>
                            <button 
                                type="button"
                                @click="selectUnit(unitOption.value)"
                                class="w-full px-4 py-2.5 text-left text-sm hover:bg-blue-50 dark:hover:bg-blue-900/20 
                                       transition-colors duration-150"
                                :class="{ 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400': selectedUnit === unitOption.value }">
                                <div class="flex items-center justify-between">
                                    <span x-text="unitOption.label" class="text-gray-900 dark:text-white"></span>
                                    <i x-show="selectedUnit === unitOption.value" 
                                       class="fa-solid fa-check text-blue-600 dark:text-blue-400 text-xs"></i>
                                </div>
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </template>

    <!-- Display Unit Badge (when unit is selected and showUnit is false) -->
    <template x-if="!showUnit && selectedUnit">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                         bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                <i class="fa-solid fa-ruler-combined text-xs mr-2"></i>
                <span x-text="getUnitLabel()"></span>
            </span>
        </div>
    </template>
</div>

@once
@push('scripts')
<script>
function quantityCounter(config) {
    return {
        value: config.default || config.min || 1,
        name: config.name || 'quantity',
        min: config.min || 1,
        max: config.max || 100,
        step: config.step || 1,
        unit: config.unit || '',
        showUnit: config.showUnit !== false,
        selectedUnit: '',
        unitOpen: false,
        unitOptions: [
            { value: 'sqm', label: 'Square Meter (m² / sqm)' },
            { value: 'sqft', label: 'Square Foot (sq ft / ft²)' },
            { value: 'sqyd', label: 'Square Yard (yd²)' },
            { value: 'are', label: 'Are (a)' },
            { value: 'hectare', label: 'Hectare (ha)' },
            { value: 'sqin', label: 'Square Inch (in²)' },
        ],
        
        init() {
            // Initialize with default unit if provided
            if (this.unit) {
                this.selectedUnit = this.unit;
            }
            
            // Ensure value is within bounds
            this.validateInput();
        },
        
        increment() {
            const newValue = parseFloat(this.value) + parseFloat(this.step);
            if (newValue <= this.max) {
                this.value = newValue;
            }
        },
        
        decrement() {
            const newValue = parseFloat(this.value) - parseFloat(this.step);
            if (newValue >= this.min) {
                this.value = newValue;
            }
        },
        
        validateInput() {
            let numValue = parseFloat(this.value);
            
            if (isNaN(numValue) || numValue < this.min) {
                this.value = this.min;
            } else if (numValue > this.max) {
                this.value = this.max;
            } else {
                this.value = numValue;
            }
        },
        
        selectUnit(unitValue) {
            this.selectedUnit = unitValue;
            this.unitOpen = false;
        },
        
        getUnitLabel() {
            const unit = this.unitOptions.find(u => u.value === this.selectedUnit);
            return unit ? unit.label : '';
        }
    }
}
</script>
@endpush
@endonce