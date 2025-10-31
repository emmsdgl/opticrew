@props(['task', 'isClockedIn'])

<div x-data="mobileTaskCard({{ $task->id }}, '{{ $task->status }}', '{{ addslashes($task->on_hold_reason ?? '') }}', {{ $isClockedIn ? 'true' : 'false' }})"
     class="bg-white dark:bg-gray-800 rounded-xl shadow-md border-l-4 overflow-hidden transition-all duration-200"
     :class="{
         'border-blue-500': status === 'Scheduled',
         'border-green-500': status === 'In Progress',
         'border-yellow-500': status === 'On Hold',
         'border-gray-400': status === 'Completed'
     }">

    {{-- Collapsible Header --}}
    <div @click="expanded = !expanded" class="p-4 cursor-pointer active:bg-gray-50 dark:active:bg-gray-700 transition-colors">
        <div class="flex items-start justify-between gap-2 mb-2">
            <h4 class="font-bold text-sm flex-1 text-gray-900 dark:text-gray-100 leading-tight">
                {{ $task->task_description }}
            </h4>
            <span class="text-xs px-2 py-1 rounded-full font-semibold whitespace-nowrap flex-shrink-0"
                  :class="{
                      'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': status === 'Scheduled',
                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': status === 'In Progress',
                      'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': status === 'On Hold',
                      'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200': status === 'Completed'
                  }"
                  x-text="status">
            </span>
        </div>

        <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1">
                <i class="fa-solid fa-location-dot text-red-500"></i>
                {{ Str::limit($task->location->location_name ?? 'External Client', 20) }}
            </span>
            @if($task->estimated_duration_minutes)
                <span class="flex items-center gap-1">
                    <i class="fa-solid fa-clock text-orange-500"></i>
                    {{ $task->estimated_duration_minutes }} min
                </span>
            @endif
        </div>

        <div class="flex items-center justify-between mt-2">
            <span class="text-xs text-gray-400 dark:text-gray-500">
                {{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}
            </span>
            <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 transition-transform duration-300"
               :class="{'rotate-180': expanded}"></i>
        </div>
    </div>

    {{-- Expandable Details & Actions --}}
    <div x-show="expanded"
         x-collapse
         class="border-t border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/50">

        {{-- Team Members --}}
        @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
        <div class="mb-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-semibold">Team Members:</p>
            <div class="flex flex-wrap gap-1">
                @foreach($task->optimizationTeam->members as $member)
                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-full text-xs font-medium">
                        {{ $member->employee->user->name ?? 'Unknown' }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Vehicle --}}
        @if($task->optimizationTeam && $task->optimizationTeam->car)
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-car text-purple-500"></i>
            <span class="font-medium">Vehicle:</span>
            {{ $task->optimizationTeam->car->car_name }}
        </p>
        @endif

        {{-- Clock In Warning --}}
        <div x-show="!isClockedIn && status !== 'Completed'"
             class="mb-3 p-3 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-400 rounded text-xs">
            <p class="font-semibold text-orange-800 dark:text-orange-300 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                Clock in required
            </p>
            <p class="text-orange-700 dark:text-orange-400 mt-1">
                You must clock in before starting tasks
            </p>
        </div>

        {{-- On Hold Reason --}}
        <div x-show="status === 'On Hold' && holdReason"
             class="mb-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 rounded text-xs">
            <p class="font-semibold text-yellow-800 dark:text-yellow-300">On Hold Reason:</p>
            <p class="text-yellow-700 dark:text-yellow-400 mt-1" x-text="holdReason"></p>
        </div>

        {{-- Flash Messages --}}
        <div x-show="message"
             x-transition
             class="mb-3 p-3 rounded-md text-xs"
             :class="{
                 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300': messageType === 'success',
                 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300': messageType === 'warning',
                 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300': messageType === 'error'
             }">
            <p class="font-semibold" x-text="message"></p>
        </div>

        {{-- Action Buttons - STACKED VERTICALLY for mobile --}}
        <div class="space-y-2">
            {{-- Start/Resume Button --}}
            <button @click="startTask"
                    :disabled="(status !== 'Scheduled' && status !== 'On Hold') || loading || !isClockedIn"
                    class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-sm active:scale-95">
                <i class="fas fa-play" x-show="!loading || action !== 'start'"></i>
                <i class="fas fa-spinner fa-spin" x-show="loading && action === 'start'"></i>
                <span x-text="status === 'On Hold' ? 'Resume Task' : 'Start Task'"></span>
            </button>

            {{-- Hold Button --}}
            <button @click="openHoldModal"
                    :disabled="status === 'Completed' || loading || !isClockedIn"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-sm active:scale-95">
                <i class="fas fa-pause"></i>
                <span x-text="status === 'On Hold' ? 'Update Hold Reason' : 'Put On Hold'"></span>
            </button>

            {{-- Complete Button --}}
            <button @click="completeTask"
                    :disabled="status !== 'In Progress' || loading"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-sm active:scale-95">
                <i class="fas fa-check" x-show="!loading || action !== 'complete'"></i>
                <i class="fas fa-spinner fa-spin" x-show="loading && action === 'complete'"></i>
                <span>Mark Complete</span>
            </button>
        </div>
    </div>

    {{-- Hold Modal - Mobile Optimized --}}
    <div x-show="showHoldModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;"
         @click.self="closeHoldModal">

        <div class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            {{-- Background overlay --}}
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeHoldModal"></div>

            {{-- Modal panel - Mobile optimized (slides up from bottom) --}}
            <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-t-2xl sm:rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                {{-- Modal Header --}}
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                                <i class="fas fa-pause text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-gray-100">
                                <span x-show="status === 'On Hold'">Update Hold Reason</span>
                                <span x-show="status !== 'On Hold'">Put Task On Hold</span>
                            </h3>
                        </div>
                        <button @click="closeHoldModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div class="mt-4">
                        <label for="holdReasonInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Reason for Hold <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            x-model="holdReasonInput"
                            id="holdReasonInput"
                            rows="4"
                            class="shadow-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg"
                            placeholder="e.g., Guest still in cabin, Equipment malfunction, Waiting for supplies..."
                        ></textarea>
                        <p x-show="holdReasonError" class="mt-2 text-sm text-red-600 dark:text-red-400" x-text="holdReasonError"></p>
                    </div>

                    <div class="mt-3 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg">
                        <p x-show="status === 'Scheduled'">💡 You can put a task on hold before starting if you anticipate issues.</p>
                        <p x-show="status === 'In Progress'">⚠️ If the delay exceeds 30 minutes, the admin will be automatically notified.</p>
                        <p x-show="status === 'On Hold'">⚠️ Updating the hold reason will refresh the delay timer notification.</p>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 flex flex-col-reverse sm:flex-row gap-2">
                    <button
                        @click="closeHoldModal"
                        type="button"
                        class="w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-3 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button
                        @click="submitHoldTask"
                        :disabled="loading"
                        type="button"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-3 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-spinner fa-spin mr-2" x-show="loading && action === 'hold'"></i>
                        <span>Submit</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mobileTaskCard(taskId, initialStatus, initialHoldReason, isClockedIn) {
    return {
        taskId: taskId,
        status: initialStatus,
        holdReason: initialHoldReason,
        isClockedIn: isClockedIn,
        expanded: false, // Collapsed by default to save space
        showHoldModal: false,
        holdReasonInput: '',
        holdReasonError: '',
        loading: false,
        action: '',
        message: '',
        messageType: '',

        async startTask() {
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success) {
                    this.status = 'In Progress';
                    this.showMessage('Task started successfully!', 'success');

                    // Dispatch event for parent to update counts
                    window.dispatchEvent(new CustomEvent('task-updated', {
                        detail: { taskId: this.taskId, status: this.status }
                    }));

                    // Haptic feedback if supported
                    if ('vibrate' in navigator) {
                        navigator.vibrate(50);
                    }
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
            if (!this.isClockedIn) {
                this.showMessage('You must clock in before managing tasks', 'error');
                return;
            }

            this.showHoldModal = true;
            this.holdReasonInput = this.status === 'On Hold' ? this.holdReason : '';
            this.holdReasonError = '';

            // Prevent body scroll on mobile
            document.body.style.overflow = 'hidden';
        },

        closeHoldModal() {
            this.showHoldModal = false;
            this.holdReasonInput = '';
            this.holdReasonError = '';

            // Restore body scroll
            document.body.style.overflow = '';
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
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
                        this.showMessage('Task on hold. Admin notified of delay.', 'warning');
                    } else {
                        const message = wasOnHold ? 'Hold reason updated successfully.' : 'Task put on hold successfully.';
                        this.showMessage(message, 'success');
                    }

                    // Dispatch event
                    window.dispatchEvent(new CustomEvent('task-updated', {
                        detail: { taskId: this.taskId, status: this.status }
                    }));

                    // Haptic feedback
                    if ('vibrate' in navigator) {
                        navigator.vibrate(50);
                    }
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (result.success) {
                    this.status = 'Completed';

                    if (result.data.performance_flagged) {
                        this.showMessage('Task completed! Duration exceeded estimate.', 'warning');
                    } else {
                        this.showMessage('Task completed successfully!', 'success');
                    }

                    // Dispatch event
                    window.dispatchEvent(new CustomEvent('task-updated', {
                        detail: { taskId: this.taskId, status: this.status }
                    }));

                    // Success haptic pattern
                    if ('vibrate' in navigator) {
                        navigator.vibrate([50, 100, 50]);
                    }
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
