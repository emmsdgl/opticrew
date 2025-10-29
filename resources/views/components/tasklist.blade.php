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

                <!-- Team Member Avatars -->
                @if ($showAvatar)
                <div class="flex -space-x-2">
                    <template x-for="(member, idx) in task.teamMembers" :key="idx">
                        <div class="relative group/avatar flex-shrink-0">
                            <!-- Show uploaded picture if available -->
                            <img x-show="member.picture"
                                 :src="member.picture ? '/storage/' + member.picture : ''"
                                 class="w-7 h-7 min-w-[28px] min-h-[28px] rounded-full object-cover border-2 border-white dark:border-gray-800 shadow-sm flex-shrink-0">

                            <!-- Show initials placeholder if no picture -->
                            <div x-show="!member.picture"
                                 class="w-7 h-7 min-w-[28px] min-h-[28px] rounded-full bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center border-2 border-white dark:border-gray-800 shadow-sm relative overflow-hidden flex-shrink-0"
                                 x-data="{
                                     getInitials(name) {
                                         if (!name) return '??';
                                         return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                                     }
                                 }">
                                <span class="text-white text-xs font-semibold relative z-10 select-none leading-none"
                                      style="text-shadow: 0 1px 2px rgba(0,0,0,0.25);"
                                      x-text="getInitials(member.name)"></span>
                            </div>

                            <!-- Tooltip on hover -->
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded opacity-0 group-hover/avatar:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                                <span x-text="member.name"></span>
                            </div>
                        </div>
                    </template>
                </div>
                @endif

                <!-- Custom Slot -->
                <div>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>