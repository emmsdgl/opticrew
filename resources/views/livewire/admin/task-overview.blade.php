<div class="flex flex-col flex-1 min-h-0">
    <!-- Task Overview Header -->
    <div class="flex flex-row justify-between w-full items-center mb-4">
        <x-labelwithvalue label="Task Overview" :count="'(' . $taskCount . ')'" />
        <div class="flex flex-row gap-3">
            
            <select wire:model.live="selectedTime" id="dropdown-time" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-slate-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="day">This Day</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
            </select>

            <select wire:model.live="selectedService" id="dropdown-service-type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-slate-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="all">All Service Types</option>
                <option value="Daily Room Cleaning">Daily Room Cleaning</option>
                <option value="Deep Cleaning">Deep Cleaning</option>
                <option value="Snowout Cleaning">Snowout Cleaning</option>
                <option value="Light Daily Cleaning">Light Daily Cleaning</option>
                <option value="Historical Cleaning">Historical Cleaning</option>
            </select>

            <x-button label="New Task" color="blue" size="sm" icon='<i class="fa-solid fa-plus"></i>' />
        </div>
    </div>
    
    <!-- Scrollable Task List -->
    <div class="overflow-y-auto h-64 pr-2">
        @if($tasks->count() === 0)
            <div class="flex items-center justify-center h-full">
                <p class="text-gray-500 dark:text-gray-400">No tasks found for the selected filter.</p>
            </div>
        @else
            {{-- ADD wire:key to force re-render --}}
            <div wire:key="tasks-{{ $selectedTime }}-{{ $selectedService }}-{{ $taskCount }}">
                <x-tasklist :tasks="$tasks" />
            </div>
        @endif
    </div>
</div>