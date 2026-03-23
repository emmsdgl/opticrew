@props([
    'items' => [],
    'maxHeight' => '20rem',
    'fixedHeight' => '20rem',
    'visibleCount' => 2,
    'emptyTitle' => 'No requests yet',
    'emptyMessage' => 'You don\'t have any requests at the moment.',
])

@php
    $statusBadge = [
        'Approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
        'Pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'Rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        'Cancelled' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    ];
    $totalItems = count($items);
    $hasOverflow = $totalItems > $visibleCount;
    $hiddenCount = $totalItems - $visibleCount;
@endphp

<div class="w-full">
    @if(empty($items))
        <div class="flex flex-col items-center justify-center py-16 px-6 text-center h-auto bg-white dark:bg-gray-800/30 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-800">
            <div class="w-32 h-32 mb-4 flex items-center justify-center">
                <img src="{{ asset('images/icons/no-items-found.svg') }}"
                     alt="No requests"
                     class="w-full h-full object-contain opacity-80 dark:opacity-60">
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">
                {{ $emptyTitle }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md">
                {{ $emptyMessage }}
            </p>
        </div>
    @else
        <div x-data="{ expanded: false }">
            {{-- Collapsed view: show first N items with stacked peek --}}
            <div x-show="!expanded" class="relative">
                <div class="space-y-3">
                    @foreach(array_slice($items, 0, $visibleCount) as $index => $item)
                        <div class="block p-4 px-6 bg-white/30 backdrop-blur-md border border-white/40 shadow-sm dark:bg-gray-800/40 dark:border-transparent dark:backdrop-blur-none hover:bg-white/50 dark:hover:bg-gray-700/40 rounded-lg transition-colors duration-200 cursor-pointer"
                             @if(isset($item['onclick']))
                             @click="{{ $item['onclick'] }}"
                             @endif>
                            <div class="flex items-center justify-between mb-0.5">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate pr-3">{{ $item['type'] }}</p>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-md flex-shrink-0 {{ $statusBadge[$item['status']] ?? $statusBadge['Pending'] }}">
                                    {{ $item['status'] }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $item['date'] }}
                                @if(!empty($item['time_range']))
                                    &middot; {{ $item['time_range'] }}
                                @endif
                                @if(!empty($item['reason']))
                                    &middot; {{ Str::limit($item['reason'], 30) }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>

                @if($hasOverflow)
                    {{-- Stacked cards peek effect --}}
                    <div class="relative mt-2 cursor-pointer" @click="expanded = true">
                        <div class="mx-3 h-2 bg-gray-200/60 dark:bg-gray-700/30 border border-gray-300/50 dark:border-gray-700/50 rounded-b-lg"></div>
                        <div class="mx-6 h-1.5 bg-gray-100/60 dark:bg-gray-700/20 border border-gray-200/50 dark:border-gray-700/40 rounded-b-lg"></div>
                        <button type="button"
                            class="w-full mt-2 text-center text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors py-1">
                            Show all ({{ $hiddenCount }} more)
                        </button>
                    </div>
                @endif
            </div>

            {{-- Expanded view: show all items scrollable --}}
            <div x-show="expanded" x-cloak>
                <div class="overflow-y-auto space-y-3 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]"
                     style="{{ $fixedHeight !== 'auto' ? 'max-height: ' . $fixedHeight . ';' : '' }}">
                    @foreach($items as $index => $item)
                        <div class="block p-4 px-6 bg-white/30 backdrop-blur-md border border-white/40 shadow-sm dark:bg-gray-800/40 dark:border-transparent dark:backdrop-blur-none hover:bg-white/50 dark:hover:bg-gray-700/40 rounded-lg transition-colors duration-200 cursor-pointer"
                             @if(isset($item['onclick']))
                             @click="{{ $item['onclick'] }}"
                             @endif>
                            <div class="flex items-center justify-between mb-0.5">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate pr-3">{{ $item['type'] }}</p>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-md flex-shrink-0 {{ $statusBadge[$item['status']] ?? $statusBadge['Pending'] }}">
                                    {{ $item['status'] }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $item['date'] }}
                                @if(!empty($item['time_range']))
                                    &middot; {{ $item['time_range'] }}
                                @endif
                                @if(!empty($item['reason']))
                                    &middot; {{ Str::limit($item['reason'], 30) }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>

                <button type="button" @click="expanded = false"
                    class="w-full mt-2 text-center text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors py-1">
                    Show less
                </button>
            </div>
        </div>
    @endif
</div>
