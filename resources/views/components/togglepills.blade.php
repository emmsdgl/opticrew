@props([
    'pills' => [],
    'label' => '',
    'name' => 'pills',
    'xModel' => '',
    'maxHeight' => 'max-h-48'
])

<div class="w-full mb-6">
    <!-- Label -->
    @if($label)
    <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
    </label>
    @endif

    <!-- Scrollable Pills Container with Border -->
    <div class="{{ $maxHeight }} overflow-y-auto rounded-xl p-3 bg-gray-50 dark:bg-gray-800 scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent">
        <div 
            x-data="{
                pills: {{ json_encode(array_map(function($pill) {
                    return [
                        'label' => is_array($pill) ? $pill['label'] : $pill,
                        'value' => is_array($pill) ? ($pill['value'] ?? $pill['label']) : $pill,
                        'selected' => false
                    ];
                }, $pills)) }},
                
                toggle(pill) {
                    pill.selected = !pill.selected;
                    this.syncModel();
                },
                
                syncModel() {
                    @if($xModel)
                    const selected = this.pills.filter(p => p.selected).map(p => p.value);
                    this.$dispatch('update-pills', selected);
                    @endif
                },
                
                getSelectedValues() {
                    return this.pills.filter(p => p.selected).map(p => p.value);
                }
            }"
            @if($xModel)
            x-init="$watch('pills', () => syncModel(), { deep: true })"
            @endif
            class="flex flex-wrap gap-2"
        >
            <template x-for="(pill, index) in pills" :key="index">
                <button 
                    type="button"
                    @click="toggle(pill)" 
                    class="inline-flex items-center gap-1.5
                           px-3 py-1 rounded-full text-xs font-medium 
                           transition-all duration-200 
                           focus:outline-none focus:ring-2 focus:ring-offset-1
                           dark:focus:ring-offset-gray-800 whitespace-nowrap"
                    :class="{
                        'bg-blue-600 text-white hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 focus:ring-blue-400': pill.selected,
                        'bg-gray-400 text-white hover:bg-gray-500 dark:bg-gray-500 dark:hover:bg-gray-400 focus:ring-blue-300': !pill.selected
                    }"
                >
                    <span class="truncate max-w-[120px]" x-text="pill.label"></span>
                    
                    <!-- Icon: Check when selected, Plus when unselected -->
                    <i 
                        class="text-xs flex-shrink-0"
                        :class="{
                            'fa-solid fa-check': pill.selected,
                            'fa-solid fa-plus': !pill.selected
                        }"
                    ></i>
                    
                    <!-- Hidden checkbox for standard form submission -->
                    <input 
                        type="checkbox" 
                        :name="`{{ $name }}[]`"
                        :value="pill.value"
                        :checked="pill.selected"
                        class="sr-only"
                    />
                </button>
            </template>
        </div>
    </div>

    <!-- Selected Count Indicator -->
    <div 
        x-data="{
            pills: {{ json_encode(array_map(function($pill) {
                return [
                    'label' => is_array($pill) ? $pill['label'] : $pill,
                    'value' => is_array($pill) ? ($pill['value'] ?? $pill['label']) : $pill,
                    'selected' => false
                ];
            }, $pills)) }}
        }"
        class="mt-2"
    >
        <p class="text-xs text-gray-500 dark:text-gray-400" x-show="pills.filter(p => p.selected).length > 0">
            <span x-text="pills.filter(p => p.selected).length"></span> selected
        </p>
    </div>
</div>