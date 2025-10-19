@props([
    'label' => 'A',
    'name' => 'Task Name',
    'subtitle' => 'Task description',
    'color' => '#3B82F6',
    'percentage' => 0,
    'dueDate' => null,
    'dueTime' => null,
    'teamName' => 'Team 1',
    'teamMembers' => []
])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs p-2 hover:shadow-md transition-all duration-200 border border-gray-100 dark:border-gray-700">
    <div class="flex items-start pr-6 pl-6 justify-between gap-5">
        <!-- Left Section: Badge + Task Info -->
        <div class="flex items-start justify-between gap-12 flex-1 min-w-0">
            <!-- Progress Badge -->
            <div class="relative flex-shrink-0">
                <svg class="w-14 h-14 transform -rotate-90" viewBox="0 0 36 36">
                    <!-- Background circle -->
                    <circle cx="18" cy="18" r="16" fill="none" 
                            class="stroke-gray-200 dark:stroke-gray-700" 
                            stroke-width="3"/>
                    <!-- Progress circle -->
                    <circle cx="18" cy="18" r="16" fill="none" 
                            stroke="{{ $color }}" 
                            stroke-width="3"
                            stroke-dasharray="{{ $percentage }}, 100"
                            stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $percentage }}%</span>
                </div>
            </div>

            <!-- Task Details -->
            <div class="flex-1 min-w-0 pt-1">
                <h3 class="font-bold text-gray-900 dark:text-gray-100 text-base mb-1 truncate">
                    {{ $name }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    {{ $subtitle }}
                </p>
            </div>
        </div>

        <!-- Right Section: Due Date + Team -->
        <div class="grid grid-cols-2 items-end gap-3 flex-shrink-0 w-1/3 justify-end">
            <!-- Due Date & Time -->
            @if($dueDate)
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ \Carbon\Carbon::parse($dueDate)->format('M d, Y') }}
                    </div>
                    @if($dueTime)
                        <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 justify-end mt-1">
                            <span class="text-gray-400 dark:text-gray-500">Due at</span>
                            <span class="font-medium">{{ $dueTime }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Team Section -->
            <div class="flex flex-col items-end gap-2">
                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $teamName }}</span>
                
                <!-- Team Member Avatars -->
                @if(count($teamMembers) > 0)
                    <div class="flex -space-x-2">
                        @foreach(array_slice($teamMembers, 0, 3) as $index => $member)
                            <div class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 bg-gray-300 dark:bg-gray-600 flex items-center justify-center overflow-hidden"
                                 title="{{ $member['name'] ?? '' }}">
                                @if(isset($member['avatar']) && $member['avatar'])
                                    <img src="{{ $member['avatar'] }}" alt="{{ $member['name'] ?? '' }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                        {{ isset($member['name']) ? strtoupper(substr($member['name'], 0, 2)) : '?' }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                        
                        @if(count($teamMembers) > 3)
                            <div class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 bg-gray-400 dark:bg-gray-500 flex items-center justify-center">
                                <span class="text-xs font-medium text-white">+{{ count($teamMembers) - 3 }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Empty state avatars -->
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 bg-gray-300 dark:bg-gray-600"></div>
                        <div class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 bg-gray-300 dark:bg-gray-600"></div>
                        <div class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 bg-gray-300 dark:bg-gray-600"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>