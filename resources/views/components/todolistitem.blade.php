@props(['task' => []])

<div
    class="flex items-center justify-between w-full rounded-xl bg-white dark:bg-gray-800 p-4 shadow-sm hover:shadow-md transition-all duration-300 ease-in-out
           border border-gray-100 dark:border-gray-700 group">

    <!-- LEFT: Icon + Details -->
    <div class="flex items-center gap-4 flex-1 min-w-0">

        <!-- Icon -->
        <div
            class="flex-shrink-0 w-10 h-10 {{ $task['iconBg'] ?? 'bg-[#2A6DFA20] dark:bg-[#2A6DFA30]' }}
                   rounded-lg flex items-center justify-center transition-all duration-300 group-hover:scale-110">
            <span class="text-[#2A6DFA] dark:text-[#2A6DFA] font-semibold text-lg">
                {{ strtoupper(substr($task['title'] ?? '', 0, 1)) }}
            </span>
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0">
            <div class="text-blue-500 dark:text-blue-400 text-xs font-medium mb-0.5 truncate">
                {{ $task['company'] ?? '' }}
            </div>
            <h3 class="text-gray-900 dark:text-gray-100 font-semibold text-sm mb-1 truncate">
                {{ $task['title'] ?? '' }}
            </h3>
            <div class="text-blue-600 dark:text-blue-400 text-xs font-medium truncate">
                {{ $task['subtitle'] ?? '' }}
            </div>
        </div>
    </div>

    <!-- RIGHT: Date and Time -->
    <div class="flex-shrink-0 text-right ml-4">
        <div class="text-gray-500 dark:text-gray-400 text-xs mb-1">{{ $task['date'] ?? '' }}</div>
        <div class="text-gray-900 dark:text-gray-100 text-xs">
            <span class="text-gray-500 dark:text-gray-400">Due at</span>
            <span class="font-semibold ml-1">{{ $task['dueTime'] ?? '' }}</span>
        </div>
    </div>
</div>
