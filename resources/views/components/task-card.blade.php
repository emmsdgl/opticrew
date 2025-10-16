@props([
    'task'
])

@php
    // Logic to determine priority for display. You can customize this.
    $priority = 'Normal'; // Default priority
    $priorityColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    if (stripos($task->task_description, 'deep') !== false) {
        $priority = 'Urgent';
        $priorityColor = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    } elseif (stripos($task->task_description, 'light') !== false) {
        $priority = 'Low';
        $priorityColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
    }
@endphp

<div class="bg-white dark:bg-gray-800 p-4 rounded-lg mb-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300 cursor-grab active:cursor-grabbing">

    <div class="flex items-start justify-between mb-3">
        <div>
            <span class="px-2 py-0.5 rounded-full text-xs font-sans font-medium {{ $priorityColor }}">
                {{ $priority }}
            </span>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-2">
                {{ optional($task->client)->first_name ?? 'Contract Client' }}
            </p>
        </div>
        <button class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
            <i class="fa-regular fa-trash-can"></i>
        </button>
    </div>

    <p class="text-base py-1.5 font-bold text-gray-800 dark:text-gray-100">
        {{ $task->task_description }}
    </p>

    <div class="flex items-center justify-between mt-3 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700 pt-3">
        <div class="flex items-center gap-2">
            <i class="fa-regular fa-calendar"></i>
            <span>{{ \Carbon\Carbon::parse($task->scheduled_date)->format('F j, Y') }}</span>
        </div>
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-clock"></i>
            <span>{{ $task->started_at ? \Carbon\Carbon::parse($task->started_at)->format('g:i A') : 'Not Started' }}</span>
        </div>
    </div>

    @if($task->team && $task->team->members->isNotEmpty())
        <div class="mt-4 flex justify-end">
             {{-- Note: You might need to create the teamavatarcols component if it doesn't exist --}}
             {{-- <x-teamavatarcols :teamName="'Team ' . $task->team->id" :members="$task->team->members->pluck('employee.full_name')->toArray()" /> --}}
             <span class="text-xs text-gray-400">Team {{ $task->team->id }}</span>
        </div>
    @endif
</div>