@props([
    'tasks' => [],
    'editable' => false,
    'showAvatar' => true,
    'onToggle' => '',
])

{{-- Convert collection to array for Alpine --}}
@php
    $tasksArray = $tasks instanceof \Illuminate\Support\Collection ? $tasks->toArray() : $tasks;
@endphp

<div x-data="{ tasks: [] }" 
     x-init="tasks = {{ json_encode(array_values($tasksArray)) }}"
     class="w-full">
    <template x-for="(task, index) in tasks" :key="task.id">
        <div
            class="flex justify-between items-center p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
                   rounded-xl shadow-sm hover:shadow-md transition cursor-pointer group mb-3">

            <!-- LEFT: Checkbox + Title -->
            <div class="flex font-sans text-sm items-center gap-3 ml-6">

                <!-- Editable Title -->
                <template x-if="{{ $editable ? 'true' : 'false' }}">
                    <input type="text"
                        x-model="task.title"
                        class="bg-transparent font-sans border-b border-gray-400 focus:outline-none text-gray-800 dark:text-gray-100">
                </template>

                <!-- Non-Editable Title -->
                <template x-if="{{ $editable ? 'false' : 'true' }}">
                    <p class="font-sans text-gray-800 dark:text-gray-100 transition"
                       :class="{ 'line-through text-gray-400 dark:text-gray-500': task.status === 'complete' }"
                       x-text="task.title"></p>
                </template>
            </div>

            <!-- RIGHT: Meta Info -->
            <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">

                <!-- Dynamic Badge for Status -->
                <template x-if="task.status === 'complete'">
                    <x-badge label="Complete" colorClass="bg-green-100 text-green-800" size="text-xs" />
                </template>
                <template x-if="task.status !== 'complete'">
                    <x-badge label="Incomplete" colorClass="bg-[#FE1E2820] text-[#FE1E28]" size="text-xs" />
                </template>

                <!-- Category -->
                <template x-if="task.category">
                    <span class="bg-blue-100 dark:bg-blue-700 text-blue-700 dark:text-blue-100 px-2 py-1 rounded"
                        x-text="task.category"></span>
                </template>

                <!-- Date -->
                <div class="flex items-center gap-1">
                    <i class="fa-regular fa-calendar"></i>
                    <span x-text="task.date"></span>
                </div>

                <!-- Time -->
                <div class="flex items-center gap-1">
                    <i class="fa-regular fa-clock"></i>
                    <span x-text="task.startTime"></span>
                </div>

                <!-- Avatar -->
                @if ($showAvatar)
                <img :src="task.avatar"
                     class="rounded-full w-7 h-7 border border-gray-300 dark:border-gray-600">
                @endif

                <!-- Custom Slot -->
                <div>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>