@props([
    'items' => [],
])

<div class="item-list-container w-full max-w-4xl mx-auto px-4 py-6">
    @foreach($items as $item)
    <div class="item-card group bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 mb-4 overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <!-- Header Section -->
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 transition-colors">
                            {{ $item['title'] }}
                        </h3>
                        
                        @if(isset($item['status']))
                        <span class="status-badge px-3 py-1 text-xs font-medium rounded-full transition-colors
                            @if($item['status'] === 'Complete')
                                bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                            @elseif($item['status'] === 'In progress')
                                bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                            @elseif($item['status'] === 'Archived')
                                bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400
                            @else
                                bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                            @endif
                        ">
                            {{ $item['status'] }}
                        </span>
                        @endif
                    </div>
                    
                    <!-- Meta Information -->
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-600 dark:text-gray-400">
                        @if(isset($item['due_date']))
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Due on {{ $item['due_date'] }}
                        </span>
                        @endif
                        
                        @if(isset($item['created_by']))
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Created by {{ $item['created_by'] }}
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
                        onclick="{{ $item['action_onclick'] }}"
                        @endif
                        class="action-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 hover:shadow-sm">
                        {{ $item['action_label'] ?? $item['action_text'] ?? 'View project' }}
                    </button>
                    @endif
                    
                    @if(isset($item['menu_items']) && count($item['menu_items']) > 0)
                    <button class="menu-btn p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
            
            <!-- Additional Content -->
            @if(isset($item['description']))
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-3 leading-relaxed">
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
    <div class="empty-state text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-gray-500 dark:text-gray-400 text-lg">No items to display</p>
    </div>
    @endif
</div>

<style>
    /* Smooth transitions for theme switching */
    .item-list-container * {
        transition-property: background-color, border-color, color;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
    
    /* Hover animations */
    .item-card {
        transform-origin: center;
    }
    
    .item-card:hover {
        transform: translateY(-2px);
    }
    
    /* Action button hover effect */
    .action-btn {
        position: relative;
        overflow: hidden;
    }
    
    .action-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.05);
        transform: translate(-50%, -50%);
        transition: width 0.3s, height 0.3s;
    }
    
    .action-btn:active::before {
        width: 200px;
        height: 200px;
    }
    
    /* Menu button pulse on hover */
    .menu-btn:hover {
        animation: pulse 0.5s ease-in-out;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
    
    /* Status badge animation */
    .status-badge {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    /* Responsive adjustments */
    @media (max-width: 640px) {
        .item-card .flex-wrap {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .action-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sync with theme toggle
        const itemListContainer = document.querySelector('.item-list-container');
        
        // Listen for theme changes (assumes your general-landing has a theme toggle)
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    // Theme has changed, animations are handled by CSS transitions
                }
            });
        });
        
        // Observe the html or body element for dark class changes
        const targetNode = document.documentElement || document.body;
        observer.observe(targetNode, { attributes: true, attributeFilter: ['class'] });
        
        // Add staggered animation on page load
        const cards = document.querySelectorAll('.item-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
        
        // Optional: Menu button click handler
        document.querySelectorAll('.menu-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                // Add your menu logic here
                console.log('Menu clicked');
            });
        });
    });
</script>