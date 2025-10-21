<div class="flex flex-col flex-1">
    <!-- Task Overview Header -->
    <div class="flex flex-row justify-between w-full items-center mb-6">
        <x-labelwithvalue label="Task Overview" :count="'(' . $taskCount . ')'" />
        <div class="flex flex-row gap-4">

            @php
                $timeOptions = [
                    'day' => 'This Day',
                    'week' => 'This Week',
                    'month' => 'This Month'
                ];
                
                $serviceOptions = [
                    'all' => 'All Service Types',
                    'Daily Room Cleaning' => 'Daily Room Cleaning',
                    'Deep Cleaning' => 'Deep Cleaning',
                    'Snowout Cleaning' => 'Snowout Cleaning',
                    'Light Daily Cleaning' => 'Light Daily Cleaning',
                    'Historical Cleaning' => 'Historical Cleaning'
                ];
            @endphp

            <x-dropdown 
                label="Show:" 
                :options="$timeOptions" 
                :default="$selectedTime" 
                id="dropdown-time"
                wire-model="selectedTime" 
            />

            <x-dropdown 
                label="Service:" 
                :options="$serviceOptions" 
                :default="$selectedService" 
                id="dropdown-service-type"
                wire-model="selectedService" 
            />

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