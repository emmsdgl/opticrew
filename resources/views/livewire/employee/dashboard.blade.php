<div>
    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex items-center justify-between px-8 py-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Hello, {{ Auth::user()->name }}</h2>
                    <p class="text-gray-600 mt-1">Here is your task schedule.</p>
                </div>

                <!-- ADD THIS CLOCK IN/OUT BUTTON BLOCK -->
                <div>
                    @if ($currentAttendance)
                        <!-- Clocked In State -->
                        <div class="text-right">
                            <p class="text-sm text-green-600">Clocked in at: {{ \Carbon\Carbon::parse($currentAttendance->clock_in)->format('h:i A') }}</p>
                            <button wire:click="clockOut" class="mt-2 bg-red-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-600">
                                Clock Out
                            </button>
                        </div>
                    @else
                        <!-- Clocked Out State -->
                        <button wire:click="clockIn" class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600">
                            Clock In
                        </button>
                    @endif
                </div>
                <!-- END OF BLOCK -->
            </div>
        </header>

        <div class="p-8">
            <!-- ======================= TODAY'S TASKS SECTION ======================= -->
            <section class="mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">My Tasks for Today</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    @forelse ($tasks as $task)
                        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500">
                            {{-- ... Task Card HTML ... --}}
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-3 py-1 rounded-full">{{ $task->status }}</span>
                                <span class="text-sm font-semibold text-blue-600">{{ $task->scheduled_date }}</span>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-2">{{ $task->task_description }}</h4>
                            <p class="text-gray-600 mb-1">{{ $task->location->location_name ?? 'External Client Task' }}</p>
                            
                            <!-- ADD THIS BLOCK -->
                            @if($task->team && $task->team->car)
                                <div class="mt-2 text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-car mr-2"></i>
                                    <span>Vehicle: {{ $task->team->car->car_name }}</span>
                                </div>
                            @endif

                            {{-- Team Members --}}
                            @if($task->team && $task->team->members)
                            <div class="mt-4 mb-4">
                                <p class="text-sm text-gray-600 mb-2">Team Members:</p>
                                <div class="flex items-center space-x-2">
                                    <p class="text-sm text-gray-600 ml-2">
                                        {{ $task->team->members->pluck('employee.full_name')->join(', ') }}
                                    </p>
                                </div>
                            </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="flex gap-2 mt-4">
                                <button wire:click="startTask({{ $task->id }})" 
                                        class="flex-1 bg-green-600 text-white ... disabled:opacity-50 disabled:cursor-not-allowed" 
                                        {{ $task->status != 'Scheduled' ? 'disabled' : '' }}>
                                    <i class="fas fa-play mr-2"></i>Start Task
                                </button>
                                <button wire:click="completeTask({{ $task->id }})" 
                                        class="flex-1 bg-blue-600 text-white ... disabled:opacity-50 disabled:cursor-not-allowed" 
                                        {{ $task->status != 'In-Progress' ? 'disabled' : '' }}>
                                    <i class="fas fa-check mr-2"></i>Complete
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-3 bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                            You have no tasks scheduled for today.
                        </div>
                    @endforelse

                </div>
            </section>

            <!-- Divider -->
            <hr class="my-10">

            <!-- ======================= UPCOMING TASKS SECTION ======================= -->
            <section class="mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">My Upcoming Tasks</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    @forelse ($futureTasks as $task)
                        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-gray-400">
                            {{-- ... Task Card HTML (no buttons for future tasks) ... --}}
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-3 py-1 rounded-full">{{ $task->status }}</span>
                                <span class="text-sm font-semibold text-gray-500">{{ \Carbon\Carbon::parse($task->scheduled_date)->format('F d, Y') }}</span>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-2">{{ $task->task_description }}</h4>
                            <p class="text-gray-600 mb-1">{{ $task->location->location_name ?? 'External Client Task' }}</p>

                            {{-- Team Members --}}
                             @if($task->team && $task->team->members)
                             <div class="mt-4 mb-4">
                                 <p class="text-sm text-gray-600 mb-2">Team Members:</p>
                                 <div class="flex items-center space-x-2">
                                     <p class="text-sm text-gray-600 ml-2">
                                         {{ $task->team->members->pluck('employee.full_name')->join(', ') }}
                                     </p>
                                 </div>
                             </div>
                             @endif
                        </div>
                    @empty
                        <div class="md:col-span-3 bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                            You have no upcoming tasks.
                        </div>
                    @endforelse

                </div>
            </section>
        </div>
    </main>
</div>