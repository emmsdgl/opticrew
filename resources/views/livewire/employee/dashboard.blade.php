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
            <!-- Flash Messages -->
            @if(session()->has('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                    <p class="font-semibold">Success!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session()->has('warning'))
                <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md" role="alert">
                    <p class="font-semibold">Warning!</p>
                    <p>{{ session('warning') }}</p>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                    <p class="font-semibold">Error!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

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
                            
                            <!-- Vehicle Info -->
                            @if($task->optimizationTeam && $task->optimizationTeam->car)
                                <div class="mt-2 text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-car mr-2"></i>
                                    <span>Vehicle: {{ $task->optimizationTeam->car->car_name }}</span>
                                </div>
                            @endif

                            {{-- Team Members --}}
                            @if($task->optimizationTeam && $task->optimizationTeam->members)
                            <div class="mt-4 mb-4">
                                <p class="text-sm text-gray-600 mb-2">Team Members:</p>
                                <div class="flex items-center space-x-2">
                                    <p class="text-sm text-gray-600 ml-2">
                                        {{ $task->optimizationTeam->members->pluck('employee.full_name')->join(', ') }}
                                    </p>
                                </div>
                            </div>
                            @endif

                            {{-- On Hold Reason --}}
                            @if($task->status === 'On Hold' && $task->on_hold_reason)
                            <div class="mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 text-sm">
                                <p class="font-semibold text-yellow-800">On Hold Reason:</p>
                                <p class="text-yellow-700">{{ $task->on_hold_reason }}</p>
                            </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="flex gap-2 mt-4">
                                <!-- Start Task Button -->
                                <button wire:click="startTask({{ $task->id }})"
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        {{ $task->status != 'Scheduled' ? 'disabled' : '' }}>
                                    <i class="fas fa-play mr-2"></i>Start
                                </button>

                                <!-- Hold Task Button -->
                                <button wire:click="openHoldModal({{ $task->id }})"
                                        class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        {{ $task->status != 'In Progress' ? 'disabled' : '' }}>
                                    <i class="fas fa-pause mr-2"></i>Hold
                                </button>

                                <!-- Complete Task Button -->
                                <button wire:click="completeTask({{ $task->id }})"
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        {{ $task->status != 'In Progress' ? 'disabled' : '' }}>
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
                             @if($task->optimizationTeam && $task->optimizationTeam->members)
                             <div class="mt-4 mb-4">
                                 <p class="text-sm text-gray-600 mb-2">Team Members:</p>
                                 <div class="flex items-center space-x-2">
                                     <p class="text-sm text-gray-600 ml-2">
                                         {{ $task->optimizationTeam->members->pluck('employee.full_name')->join(', ') }}
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

    <!-- Hold Task Modal -->
    @if($showHoldModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeHoldModal"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-pause text-yellow-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Put Task On Hold
                            </h3>
                            <div class="mt-4">
                                <label for="holdReason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Reason for Hold <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    wire:model.defer="holdReason"
                                    id="holdReason"
                                    rows="3"
                                    class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="e.g., Guest still in cabin, Equipment malfunction, Waiting for supplies..."
                                ></textarea>
                                @error('holdReason')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mt-3 text-sm text-gray-500">
                                <p>⚠️ Note: If the delay exceeds 30 minutes, the admin will be automatically notified.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button
                        wire:click="submitHoldTask"
                        type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Submit
                    </button>
                    <button
                        wire:click="closeHoldModal"
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>