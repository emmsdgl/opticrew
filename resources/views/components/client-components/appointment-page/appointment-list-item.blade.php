@props([
    'items' => [],
    'maxHeight' => '30rem', // Default max height
])

<div class="w-full" x-data="{ openMenuId: null }">
    <!-- Scrollable container with max height -->
    <div class="overflow-y-auto" 
         style="max-height: {{ $maxHeight }};"
         @scroll.window="openMenuId = null"
         @scroll="openMenuId = null">
        @foreach($items as $index => $item)
        <div class="group bg-white dark:bg-transparent border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors duration-200">
            <div class="py-6 px-6">
                <!-- Header Section -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $item['service'] }}
                            </h3>
                            
                            @if(isset($item['status']))
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-md
                                @if($item['status'] === 'Complete' || $item['status'] === 'Completed')
                                    bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400
                                @elseif($item['status'] === 'In progress')
                                    bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($item['status'] === 'Archived')
                                    bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400
                                @else
                                    bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                @endif
                            ">
                                {{ $item['status'] }}
                            </span>
                            @endif
                        </div>
                        
                        <!-- Meta Information -->
                        <div class="flex flex-wrap items-center gap-x-3 text-xs text-gray-500 dark:text-gray-400">
                            @if(isset($item['service_date']))
                            <span class="flex items-center gap-1">
                                Scheduled on <span class="font-bold">{{ $item['service_date'] }}</span>
                            </span>
                            @endif
                            
                            @if(isset($item['service_date']) && isset($item['service_time']))
                            <span>·</span>
                            @endif
                            
                            @if(isset($item['service_time']))
                            <span class="flex items-center gap-1">
                                At <span class="font-bold">{{ $item['service_time'] }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 ml-4">
                        @if(isset($item['action_url']) || isset($item['action_onclick']))
                            <button 
                                @if(isset($item['action_url']))
                                onclick="window.location.href='{{ $item['action_url'] }}'"
                                @elseif(isset($item['action_onclick']))
                                @click="{{ $item['action_onclick'] }}"
                                @endif
                                class="px-6 py-3 text-xs font-medium text-blue-500 bg-white dark:bg-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                {{ $item['action_label'] ?? $item['action_text'] ?? 'View Details' }}
                            </button>
                        @endif
                        
                        @if(isset($item['menu_items']) && count($item['menu_items']) > 0)
                        <div class="relative"
                            x-data="{
                                menuStyle: '',
                                positionMenu() {
                                    const button = $refs.menuButton;
                                    const rect = button.getBoundingClientRect();
                                    const menuWidth = 160; // w-48 = 12rem = 192px
                                    
                                    this.menuStyle = `
                                        position: fixed;
                                        top: ${rect.bottom + 8}px;
                                        left: ${rect.right - menuWidth}px;
                                    `;
                                },
                                isButtonVisible() {
                                    const button = $refs.menuButton;
                                    if (!button) return false;
                                    
                                    const rect = button.getBoundingClientRect();
                                    return (
                                        rect.top >= 0 &&
                                        rect.left >= 0 &&
                                        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                                        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                                    );
                                }
                            }"
                            x-init="
                                $watch('openMenuId', value => {
                                    if (value === {{ $index }}) {
                                        positionMenu();
                                        const checkVisibility = setInterval(() => {
                                            if (openMenuId === {{ $index }}) {
                                                if (!isButtonVisible()) {
                                                    openMenuId = null;
                                                    clearInterval(checkVisibility);
                                                }
                                            } else {
                                                clearInterval(checkVisibility);
                                            }
                                        }, 100);
                                    }
                                });
                            ">
                            <button 
                                x-ref="menuButton"
                                @click.stop="
                                    if (openMenuId === {{ $index }}) {
                                        openMenuId = null;
                                    } else {
                                        openMenuId = {{ $index }};
                                        $nextTick(() => positionMenu());
                                    }
                                "
                                class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu Portal -->
                            <template x-teleport="body">
                                <div 
                                    x-show="openMenuId === {{ $index }}"
                                    @click.away="openMenuId = null"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    :style="menuStyle"
                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-[9999] w-48"
                                    style="display: none;">
                                    <div class="py-1">
                                        @foreach($item['menu_items'] as $menuItem)
                                        <button 
                                            @if(isset($menuItem['action']))
                                            onclick="{{ $menuItem['action'] }}"
                                            @endif
                                            @click.stop="openMenuId = null"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                            {{ $menuItem['label'] }}
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                            </template>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Description -->
                @if(isset($item['description']))
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">
                    {{ $item['description'] }}
                </p>
                @endif
                
                <!-- Custom Slot for Extra Content -->
                @if(isset($item['extra_content']))
                <div class="mt-4">
                    {!! $item['extra_content'] !!}
                </div>
                @endif
            </div>
        </div>
        @endforeach
        
        @if(empty($items))
        <div class="text-center py-16 bg-white dark:bg-gray-800">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-base">No items to display</p>
        </div>
        @endif
    </div>
</div>