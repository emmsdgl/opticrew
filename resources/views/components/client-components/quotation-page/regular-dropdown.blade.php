{{-- resources/views/components/client-components/quotation-page/multi-select-dropdown.blade.php --}}
@props([
    'label' => 'Select Options',
    'name' => 'selection',
    'options' => [],
    'required' => false,
    'placeholder' => 'Select options',
    'xModel' => null,
    'multiple' => true,
])

<div class="space-y-3" 
     x-data="multiSelectDropdown({
        name: '{{ $name }}',
        options: {{ json_encode($options) }},
        multiple: {{ $multiple ? 'true' : 'false' }},
        xModel: '{{ $xModel }}'
     })"
     x-init="init()">
    
    <!-- Label -->
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <!-- Dropdown Button -->
    <div class="relative">
        <button
            type="button"
            @click="open = !open"
            class="w-full px-4 py-3 text-left border-1 border-gray-300 dark:border-gray-600 rounded-xl
                   focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                   hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300"
            :class="{ 'border-blue-600 dark:border-blue-500 bg-blue-50 dark:bg-gray-700': open }">
            
            <div class="flex items-center justify-between gap-3">
                <span class="truncate" :class="selectedValues.length === 0 ? 'text-gray-400 dark:text-gray-500' : ''">
                    <span x-show="selectedValues.length === 0">{{ $placeholder }}</span>
                    <span x-show="selectedValues.length === 1" x-text="getSelectedText()"></span>
                    <span x-show="selectedValues.length > 1" x-text="selectedValues.length + ' selected'"></span>
                </span>
                <i class="fa-solid fa-chevron-down text-gray-400 dark:text-gray-500 text-sm transition-transform duration-200"
                   :class="{ 'rotate-180': open }"></i>
            </div>
        </button>

        <!-- Hidden Inputs -->
        <template x-if="multiple">
            <div>
                <template x-for="(value, index) in selectedValues" :key="index">
                    <input type="hidden" :name="name + '[]'" :value="value">
                </template>
            </div>
        </template>
        <template x-if="!multiple">
            <input type="hidden" :name="name" :value="selectedValues[0] || ''">
        </template>

        <!-- Dropdown Menu -->
        <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 
                   rounded-xl shadow-xl overflow-hidden"
            style="display: none;">
            
            <!-- Options List -->
            <div class="max-h-60 overflow-y-auto">
                <ul class="py-1">
                    <template x-for="option in filteredOptions" :key="option.value">
                        <li>
                            <button 
                                type="button"
                                @click="toggleOption(option.value)"
                                class="w-full px-4 py-2.5 text-left flex items-center gap-3 hover:bg-gray-100 dark:hover:bg-gray-700 
                                       transition-colors duration-150"
                                :class="{ 'bg-gray-50 dark:bg-gray-700': isSelected(option.value) }">
                                
                                <!-- Checkbox (for multiple) -->
                                <template x-if="multiple">
                                    <div class="flex-shrink-0">
                                        <div class="w-5 h-5 border-2 rounded transition-all duration-200 flex items-center justify-center"
                                             :class="isSelected(option.value) 
                                                ? 'border-blue-600 dark:border-blue-500 bg-blue-600 dark:bg-blue-500' 
                                                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'">
                                            <i x-show="isSelected(option.value)" 
                                               class="fa-solid fa-check text-white text-xs"></i>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Radio (for single) -->
                                <template x-if="!multiple">
                                    <div class="flex-shrink-0">
                                        <div class="w-5 h-5 border-2 rounded-full transition-all duration-200 flex items-center justify-center"
                                             :class="isSelected(option.value) 
                                                ? 'border-blue-600 dark:border-blue-500' 
                                                : 'border-gray-300 dark:border-gray-600'">
                                            <div x-show="isSelected(option.value)" 
                                                 class="w-2.5 h-2.5 bg-blue-600 dark:bg-blue-500 rounded-full"></div>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Option Text -->
                                <span class="flex-1 text-sm text-gray-900 dark:text-white" x-text="option.label"></span>
                            </button>
                        </li>
                    </template>
                    
                    <li x-show="filteredOptions.length === 0">
                        <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                            No results found
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Footer Actions (for multiple select) -->
            <div x-show="multiple && selectedValues.length > 0" 
                 class="sticky bottom-0 p-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        <span x-text="selectedValues.length"></span> selected
                    </span>
                    <button
                        type="button"
                        @click="clearAll()"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 
                               font-medium transition-colors">
                        Clear all
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Items Display (Optional Pills) -->
    <div x-show="multiple && selectedValues.length > 0" class="flex flex-wrap gap-2 mt-2">
        <template x-for="value in selectedValues" :key="value">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 
                        text-blue-700 dark:text-blue-300 rounded-lg text-sm">
                <span x-text="getLabelByValue(value)"></span>
                <button
                    type="button"
                    @click="removeOption(value)"
                    class="hover:text-blue-900 dark:hover:text-blue-100 transition-colors">
                    <i class="fa-solid fa-times text-xs"></i>
                </button>
            </div>
        </template>
    </div>
</div>

@once
@push('scripts')
<script>
function multiSelectDropdown(config) {
    return {
        open: false,
        search: '',
        selectedValues: [],
        name: config.name || 'selection',
        options: config.options || [],
        multiple: config.multiple !== false,
        
        init() {
            // Initialize with default values if provided
            if (config.xModel) {
                this.$watch('selectedValues', value => {
                    // Sync with parent x-model if provided
                    const parentData = this.$root;
                    const modelPath = config.xModel.split('.');
                    let obj = parentData;
                    for (let i = 0; i < modelPath.length - 1; i++) {
                        obj = obj[modelPath[i]];
                    }
                    obj[modelPath[modelPath.length - 1]] = this.multiple ? value : (value[0] || '');
                });
            }
        },
        
        get filteredOptions() {
            if (!this.search) return this.options;
            return this.options.filter(option => 
                option.label.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        
        isSelected(value) {
            return this.selectedValues.includes(value);
        },
        
        toggleOption(value) {
            if (this.multiple) {
                // Multiple selection
                const index = this.selectedValues.indexOf(value);
                if (index === -1) {
                    this.selectedValues.push(value);
                } else {
                    this.selectedValues.splice(index, 1);
                }
            } else {
                // Single selection
                if (this.selectedValues[0] === value) {
                    this.selectedValues = [];
                } else {
                    this.selectedValues = [value];
                    this.open = false;
                }
            }
        },
        
        removeOption(value) {
            const index = this.selectedValues.indexOf(value);
            if (index !== -1) {
                this.selectedValues.splice(index, 1);
            }
        },
        
        clearAll() {
            this.selectedValues = [];
        },
        
        getSelectedText() {
            if (this.selectedValues.length === 0) return '';
            const option = this.options.find(opt => opt.value === this.selectedValues[0]);
            return option ? option.label : '';
        },
        
        getLabelByValue(value) {
            const option = this.options.find(opt => opt.value === value);
            return option ? option.label : value;
        }
    }
}
</script>
@endpush
@endonce