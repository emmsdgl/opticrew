@props(['task', 'isClockedIn' => false])

<div x-data="taskActionCard({{ $task->id }}, '{{ $task->status }}', '{{ $task->on_hold_reason ?? '' }}', {{ $isClockedIn ? 'true' : 'false' }})"
     class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 p-6 border-l-4"
     :class="{
         'border-blue-500': status === 'Scheduled',
         'border-green-500': status === 'In Progress',
         'border-yellow-500': status === 'On Hold',
         'border-gray-400': status === 'Completed'
     }">

    <!-- Task Header -->
    <div class="flex items-center justify-between mb-4">
        <span class="px-3 py-1 text-xs font-semibold rounded-full"
              :class="{
                  'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': status === 'Scheduled',
                  'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': status === 'In Progress',
                  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': status === 'On Hold',
                  'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200': status === 'Completed'
              }"
              x-text="status">
        </span>
        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
            {{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}
        </span>
    </div>

    <!-- Task Title & Location -->
    <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">
        {{ $task->task_description }}
    </h4>
    <p class="text-gray-600 dark:text-gray-400 mb-3 flex items-center">
        <i class="fas fa-map-marker-alt mr-2"></i>
        {{ $task->location->location_name ?? 'External Client Task' }}
    </p>

    <!-- Duration Info -->
    @if($task->estimated_duration_minutes)
    <div class="text-sm text-gray-500 dark:text-gray-400 mb-3 flex items-center">
        <i class="fas fa-clock mr-2"></i>
        <span>Est. Duration: {{ $task->estimated_duration_minutes }} minutes</span>
    </div>
    @endif

    <!-- Vehicle Info -->
    @if($task->optimizationTeam && $task->optimizationTeam->car)
    <div class="text-sm text-gray-500 dark:text-gray-400 mb-3 flex items-center">
        <i class="fas fa-car mr-2"></i>
        <span>Vehicle: {{ $task->optimizationTeam->car->car_name }}</span>
    </div>
    @endif

    <!-- Team Members -->
    @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
    <div class="mb-4">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 font-semibold">Team Members:</p>
        <div class="flex flex-wrap gap-2">
            @foreach($task->optimizationTeam->members as $member)
                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs">
                    {{ $member->employee->full_name }}
                </span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Clock In Warning -->
    <div x-show="!isClockedIn && (status === 'Scheduled' || status === 'On Hold')"
         class="mb-4 p-3 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-400 rounded">
        <div class="flex items-start gap-2">
            <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400 mt-0.5"></i>
            <div>
                <p class="font-semibold text-orange-800 dark:text-orange-300 text-sm">Clock In Required</p>
                <p class="text-orange-700 dark:text-orange-400 text-xs mt-1">You must clock in before you can start any tasks. Please use the Clock In button at the top of the page.</p>
            </div>
        </div>
    </div>

    <!-- On Hold Reason -->
    <div x-show="status === 'On Hold' && holdReason"
         class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 rounded">
        <p class="font-semibold text-yellow-800 dark:text-yellow-300 text-sm">On Hold Reason:</p>
        <p class="text-yellow-700 dark:text-yellow-400 text-sm" x-text="holdReason"></p>
    </div>

    <!-- Flash Messages -->
    <div x-show="message" x-transition class="mb-4 p-3 rounded-md"
         :class="{
             'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300': messageType === 'success',
             'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300': messageType === 'warning',
             'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300': messageType === 'error'
         }">
        <p class="text-sm font-semibold" x-text="message"></p>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-2">
        <!-- Start/Resume Button -->
        <button @click="startTask"
                :disabled="(status !== 'Scheduled' && status !== 'On Hold') || loading || !isClockedIn"
                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <i class="fas fa-play" x-show="!loading || action !== 'start'"></i>
            <i class="fas fa-spinner fa-spin" x-show="loading && action === 'start'"></i>
            <span x-text="status === 'On Hold' ? 'Resume' : 'Start'"></span>
        </button>

        <!-- Hold Button -->
        <button @click="openHoldModal"
                :disabled="status === 'Completed' || loading"
                class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <i class="fas fa-pause"></i>
            <span x-text="status === 'On Hold' ? 'Update Hold' : 'Hold'"></span>
        </button>

        <!-- Complete Button -->
        <button @click="completeTask"
                :disabled="status !== 'In Progress' || loading"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <i class="fas fa-check" x-show="!loading || action !== 'complete'"></i>
            <i class="fas fa-spinner fa-spin" x-show="loading && action === 'complete'"></i>
            <span>Complete</span>
        </button>
    </div>

    <!-- Hold Modal -->
    <div x-show="showHoldModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeHoldModal"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-pause text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" x-text="status === 'On Hold' ? 'Update Hold Reason' : (status === 'Scheduled' ? 'Put Task On Hold (Before Starting)' : 'Put Task On Hold')">
                            </h3>
                            <div class="mt-4">
                                <label for="holdReasonInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Reason for Hold <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    x-model="holdReasonInput"
                                    id="holdReasonInput"
                                    rows="3"
                                    class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md"
                                    placeholder="e.g., Guest still in cabin, Equipment malfunction, Waiting for supplies..."
                                ></textarea>
                                <p x-show="holdReasonError" class="mt-2 text-sm text-red-600 dark:text-red-400" x-text="holdReasonError"></p>
                            </div>
                            <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                <p x-show="status === 'Scheduled'">💡 You can put a task on hold before starting if you anticipate issues (e.g., guest still in cabin).</p>
                                <p x-show="status === 'In Progress'">⚠️ Note: If the delay exceeds 30 minutes, the admin will be automatically notified.</p>
                                <p x-show="status === 'On Hold'">⚠️ Updating the hold reason will refresh the delay timer notification.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button
                        @click="submitHoldTask"
                        :disabled="loading"
                        type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                        <i class="fas fa-spinner fa-spin mr-2" x-show="loading && action === 'hold'"></i>
                        <span>Submit</span>
                    </button>
                    <button
                        @click="closeHoldModal"
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function taskActionCard(taskId, initialStatus, initialHoldReason, isClockedIn) {
    return {
        taskId: taskId,
        status: initialStatus,
        holdReason: initialHoldReason,
        isClockedIn: isClockedIn,
        showHoldModal: false,
        holdReasonInput: '',
        holdReasonError: '',
        loading: false,
        action: '',
        message: '',
        messageType: '',

        async startTask() {
            // Check if employee is clocked in
            if (!this.isClockedIn) {
                this.showMessage('You must clock in before starting tasks', 'error');
                return;
            }

            this.loading = true;
            this.action = 'start';
            this.message = '';

            try {
                const response = await fetch(`/api/tasks/${this.taskId}/start`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.status = 'In Progress';
                    this.showMessage('Task started successfully!', 'success');
                    // Reload page after 1.5 seconds to refresh data
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    this.showMessage(data.message || 'Failed to start task', 'error');
                }
            } catch (error) {
                this.showMessage('Error: ' + error.message, 'error');
            } finally {
                this.loading = false;
                this.action = '';
            }
        },

        openHoldModal() {
            this.showHoldModal = true;
            // Pre-populate with existing reason if task is already on hold
            this.holdReasonInput = this.status === 'On Hold' ? this.holdReason : '';
            this.holdReasonError = '';
        },

        closeHoldModal() {
            this.showHoldModal = false;
            this.holdReasonInput = '';
            this.holdReasonError = '';
        },

        async submitHoldTask() {
            this.holdReasonError = '';

            if (!this.holdReasonInput || this.holdReasonInput.trim().length < 3) {
                this.holdReasonError = 'Please provide a reason (minimum 3 characters)';
                return;
            }

            this.loading = true;
            this.action = 'hold';

            try {
                const response = await fetch(`/api/tasks/${this.taskId}/hold`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        reason: this.holdReasonInput
                    })
                });

                const result = await response.json();

                if (result.success) {
                    const wasOnHold = this.status === 'On Hold';
                    this.status = 'On Hold';
                    this.holdReason = this.holdReasonInput;
                    this.closeHoldModal();

                    if (result.data.alert_triggered) {
                        this.showMessage('Task put on hold. Admin has been notified of the delay.', 'warning');
                    } else {
                        const message = wasOnHold ? 'Hold reason updated successfully.' : 'Task put on hold successfully.';
                        this.showMessage(message, 'success');
                    }

                    // Reload page after 2 seconds
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    this.showMessage(result.message || 'Failed to put task on hold', 'error');
                }
            } catch (error) {
                this.showMessage('Error: ' + error.message, 'error');
            } finally {
                this.loading = false;
                this.action = '';
            }
        },

        async completeTask() {
            this.loading = true;
            this.action = 'complete';
            this.message = '';

            try {
                const response = await fetch(`/api/tasks/${this.taskId}/complete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.status = 'Completed';

                    if (result.data.performance_flagged) {
                        this.showMessage('Task completed! Duration exceeded estimate.', 'warning');
                    } else {
                        this.showMessage('Task completed successfully!', 'success');
                    }

                    // Reload page after 1.5 seconds
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    this.showMessage(result.message || 'Failed to complete task', 'error');
                }
            } catch (error) {
                this.showMessage('Error: ' + error.message, 'error');
            } finally {
                this.loading = false;
                this.action = '';
            }
        },

        showMessage(text, type) {
            this.message = text;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
            }, 5000);
        }
    }
}
</script>
@endpush
