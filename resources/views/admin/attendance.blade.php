<x-layouts.general-employer :title="'Attendance'">

    <section role="status" class="w-full flex flex-col lg:flex-col gap-4 p-4 md:p-6">

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Inner Panel - Summary Cards Container -->
        <div class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <x-labelwithvalue label="Summary" count="" />

            <div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 p-6">
                @foreach($stats as $stat)
                    <x-statisticscard
                        :title="$stat['title']"
                        :value="$stat['value']"
                        :subtitle="$stat['subtitle'] ?? ''"
                        :trend="$stat['trend'] ?? null"
                        :trend-value="$stat['trendValue'] ?? null"
                        :trend-label="$stat['trendLabel'] ?? 'vs last month'"
                        :icon="$stat['icon'] ?? null"
                        :icon-bg="$stat['iconBg'] ?? 'bg-gray-100'"
                        :icon-color="$stat['iconColor'] ?? 'text-gray-600'"
                        :value-suffix="$stat['valueSuffix'] ?? ''"
                        :value-prefix="$stat['valuePrefix'] ?? ''"
                    />
                @endforeach
            </div>
        </div>

        <!-- Inner Panel - Attendance Records List -->
        <div class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <x-labelwithvalue label="Attendance Logs" count="({{ isset($attendanceRecords) ? count($attendanceRecords) : 0 }})" />

            @if(isset($attendanceRecords) && count($attendanceRecords) > 0)
                <x-attendancelistitem :records="$attendanceRecords" :show-header="true" />
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-calendar-xmark text-4xl mb-4"></i>
                    <p>No attendance records found.</p>
                </div>
            @endif
        </div>

        <!-- Inner Panel - Request Records List -->
        <div class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4"
            x-data="{
                showRequestModal: false,
                selectedRequest: null,
                adminNotes: '',
                rejectionReason: '',
                isSubmitting: false,
                showRejectionForm: false,
                requestRecords: @js($requestRecords),
                rejectionReasons: [
                    'Insufficient leave balance',
                    'Overlapping with other approved leaves',
                    'Critical project deadline',
                    'Short notice - requires more advance notice',
                    'Documentation incomplete',
                    'Department understaffed during requested period',
                    'Request conflicts with company policy',
                    'Other'
                ],

                openRequestModal(index) {
                    const record = this.requestRecords[index];
                    if (!record) return;

                    this.selectedRequest = {
                        id: record.requestId,
                        employeeName: record.requestEmployeeName,
                        type: record.requestType,
                        status: record.requestStatus,
                        date: record.requestDate,
                        endDate: record.requestEndDate,
                        timeRange: record.requestTimeRange,
                        fromTime: record.requestFromTime,
                        toTime: record.requestToTime,
                        reason: record.requestReason,
                        description: record.requestDescription,
                        proofDocument: record.requestProofDocument,
                        adminNotes: record.requestAdminNotes,
                        durationDays: record.requestDurationDays,
                        createdAt: record.requestCreatedAt,
                        tasks: record.employeeTasks || [],
                    };
                    this.adminNotes = record.requestAdminNotes || '';
                    this.rejectionReason = '';
                    this.showRejectionForm = false;
                    this.showRequestModal = true;
                    document.body.style.overflow = 'hidden';
                },

                closeRequestModal() {
                    this.showRequestModal = false;
                    this.selectedRequest = null;
                    this.adminNotes = '';
                    this.rejectionReason = '';
                    this.showRejectionForm = false;
                    document.body.style.overflow = 'auto';
                },

                async approveRequest() {
                    if (this.isSubmitting || !this.selectedRequest) return;
                    this.isSubmitting = true;

                    try {
                        const response = await fetch(`/admin/employee-requests/${this.selectedRequest.id}/approve`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ admin_notes: '' })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.closeRequestModal();
                            window.location.reload();
                        } else {
                            alert(data.message || 'Failed to approve request');
                        }
                    } catch (error) {
                        alert('An error occurred. Please try again.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                handleDeclineClick() {
                    if (!this.showRejectionForm) {
                        this.showRejectionForm = true;
                        return;
                    }
                    this.rejectRequest();
                },

                async rejectRequest() {
                    if (this.isSubmitting || !this.selectedRequest) return;
                    if (!this.rejectionReason) {
                        alert('Please select a reason for rejection');
                        return;
                    }
                    this.isSubmitting = true;

                    // Combine rejection reason with additional notes
                    const fullNotes = this.rejectionReason + (this.adminNotes ? ': ' + this.adminNotes : '');

                    try {
                        const response = await fetch(`/admin/employee-requests/${this.selectedRequest.id}/reject`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ admin_notes: fullNotes })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.closeRequestModal();
                            window.location.reload();
                        } else {
                            alert(data.message || 'Failed to reject request');
                        }
                    } catch (error) {
                        alert('An error occurred. Please try again.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                getStatusBadge(status) {
                    const badges = {
                        'pending': 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                        'approved': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                        'rejected': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                        'cancelled': 'bg-gray-100 text-gray-700 dark:bg-gray-700/30 dark:text-gray-400'
                    };
                    return badges[status] || 'bg-gray-100 text-gray-700';
                }
            }"
            @open-request-modal.window="openRequestModal($event.detail.index)">

            <x-labelwithvalue label="Request Logs" count="({{ isset($requestRecords) ? count($requestRecords) : 0 }})" />

            @if(isset($requestRecords) && count($requestRecords) > 0)
                <x-admin-request-list-item :records="$requestRecords" :show-header="true" />
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-calendar-xmark text-4xl mb-4"></i>
                    <p>No request records found.</p>
                </div>
            @endif

            <!-- Request Details Slide-in Drawer -->
            <div x-show="showRequestModal" x-cloak class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
                <!-- Backdrop -->
                <div x-show="showRequestModal"
                     x-transition:enter="transition-opacity ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeRequestModal()"
                     class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

                <!-- Drawer Panel -->
                <div class="fixed inset-y-0 right-0 flex max-w-full">
                    <div x-show="showRequestModal"
                         x-transition:enter="transform transition ease-in-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-200"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         @click.stop
                         class="relative w-screen max-w-sm">

                        <!-- Drawer Content -->
                        <div class="h-full flex flex-col bg-white dark:bg-slate-800 shadow-2xl border-l border-gray-200 dark:border-slate-700">
                            <!-- Drawer Header -->
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-slate-800/50">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request Details</h2>
                                <button type="button" @click="closeRequestModal()"
                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Drawer Body (Scrollable) -->
                            <div class="flex-1 overflow-y-auto p-6" x-show="selectedRequest">
                                <!-- Subtitle -->
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    Review and manage this leave request
                                </p>

                                <!-- Request Information -->
                                <template x-if="selectedRequest">
                                    <div class="space-y-0 mb-6">
                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Employee</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.employeeName"></span>
                                        </div>

                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Request Type</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.type"></span>
                                        </div>

                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                                            <span class="text-sm font-semibold px-2 py-1 rounded-full"
                                                :class="getStatusBadge(selectedRequest.status)"
                                                x-text="selectedRequest.status.charAt(0).toUpperCase() + selectedRequest.status.slice(1)"></span>
                                        </div>

                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Date</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.date"></span>
                                        </div>

                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Time Range</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.timeRange"></span>
                                        </div>

                                        <template x-if="selectedRequest.fromTime && selectedRequest.toTime">
                                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Custom Hours</span>
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.fromTime + ' - ' + selectedRequest.toTime"></span>
                                            </div>
                                        </template>

                                        <div class="py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400 block mb-2">Reason</span>
                                            <p class="text-sm text-gray-900 dark:text-white" x-text="selectedRequest.reason"></p>
                                        </div>

                                        <template x-if="selectedRequest.description">
                                            <div class="py-3 border-b border-gray-200 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400 block mb-2">Description</span>
                                                <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg" x-text="selectedRequest.description"></p>
                                            </div>
                                        </template>

                                        <template x-if="selectedRequest.proofDocument">
                                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Proof Document</span>
                                                <a :href="'/storage/' + selectedRequest.proofDocument" target="_blank"
                                                    class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                                    View Document
                                                </a>
                                            </div>
                                        </template>

                                        <div class="flex justify-between items-center py-3">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Submitted</span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400" x-text="selectedRequest.createdAt"></span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Existing Admin Notes (for processed requests) -->
                                <template x-if="selectedRequest && selectedRequest.status !== 'pending' && selectedRequest.adminNotes">
                                    <div class="mb-6 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-1">Admin Notes</span>
                                        <p class="text-sm text-gray-900 dark:text-white" x-text="selectedRequest.adminNotes"></p>
                                    </div>
                                </template>

                                <!-- Status Messages -->
                                <template x-if="selectedRequest && selectedRequest.status === 'rejected'">
                                    <div class="flex items-center justify-center gap-2 py-3 px-4 mb-6 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                        <i class="fa-solid fa-circle-xmark text-red-600 dark:text-red-400"></i>
                                        <span class="text-sm font-medium text-red-700 dark:text-red-400">This request has been declined</span>
                                    </div>
                                </template>

                                <template x-if="selectedRequest && selectedRequest.status === 'approved'">
                                    <div class="flex items-center justify-center gap-2 py-3 px-4 mb-6 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                                        <span class="text-sm font-medium text-green-700 dark:text-green-400">This request has been approved</span>
                                    </div>
                                </template>

                                <template x-if="selectedRequest && selectedRequest.status === 'cancelled'">
                                    <div class="flex items-center justify-center gap-2 py-3 px-4 mb-6 bg-gray-50 dark:bg-gray-700/20 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <i class="fa-solid fa-ban text-gray-500 dark:text-gray-400"></i>
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">This request was cancelled by the employee</span>
                                    </div>
                                </template>

                                <!-- Rejection Form (shown only after clicking Decline) -->
                                <template x-if="selectedRequest && selectedRequest.status === 'pending' && showRejectionForm">
                                    <div class="mb-6 space-y-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                        <div class="flex items-center gap-2 mb-2">
                                            <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                                            <span class="text-sm font-medium text-red-700 dark:text-red-400">Decline Request</span>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Rejection Reason <span class="text-red-500">*</span>
                                            </label>
                                            <select
                                                x-model="rejectionReason"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                                <option value="">Select a reason...</option>
                                                <template x-for="reason in rejectionReasons" :key="reason">
                                                    <option :value="reason" x-text="reason"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Additional Notes <span class="text-gray-400">(optional)</span>
                                            </label>
                                            <textarea
                                                x-model="adminNotes"
                                                rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                                placeholder="Add additional notes..."></textarea>
                                        </div>
                                        <button @click="showRejectionForm = false; rejectionReason = ''; adminNotes = '';"
                                            class="w-full px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                </template>

                                <!-- Employee Tasks Section -->
                                <template x-if="selectedRequest && selectedRequest.tasks && selectedRequest.tasks.length > 0">
                                    <div class="mt-6">
                                        <div class="flex items-center gap-2 mb-4">
                                            <i class="fa-solid fa-list-check text-blue-600 dark:text-blue-400"></i>
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Employee Tasks</h3>
                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="'(' + selectedRequest.tasks.length + ')'"></span>
                                        </div>
                                        <div class="space-y-3">
                                            <template x-for="(task, idx) in selectedRequest.tasks" :key="idx">
                                                <div class="p-3 border-b border-gray-200 dark:border-gray-600 py-4">
                                                    <div class="flex items-start justify-between gap-2 mb-2">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white flex-1" x-text="task.description"></p>
                                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap"
                                                            :class="{
                                                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': task.status === 'Completed',
                                                                'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': task.status === 'In Progress' || task.status === 'In-Progress',
                                                                'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400': task.status === 'Pending' || task.status === 'Scheduled'
                                                            }"
                                                            x-text="task.status"></span>
                                                    </div>
                                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                                        <span class="flex items-center gap-1">
                                                            <i class="fa-regular fa-calendar"></i>
                                                            <span x-text="task.date"></span>
                                                        </span>
                                                        <span class="flex items-center gap-1">
                                                            <i class="fa-regular fa-clock"></i>
                                                            <span x-text="task.duration + ' min'"></span>
                                                        </span>
                                                        <span class="flex items-center gap-1">
                                                            <i class="fa-regular fa-building"></i>
                                                            <span x-text="task.client"></span>
                                                        </span>
                                                    </div>
                                                    <template x-if="task.location">
                                                        <div class="mt-1 text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                                            <i class="fa-solid fa-location-dot"></i>
                                                            <span x-text="task.location"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- No Tasks Message -->
                                <template x-if="selectedRequest && (!selectedRequest.tasks || selectedRequest.tasks.length === 0)">
                                    <div class="mt-6">
                                        <div class="flex items-center gap-2 mb-4">
                                            <i class="fa-solid fa-list-check text-blue-600 dark:text-blue-400"></i>
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Employee Tasks</h3>
                                        </div>
                                        <div class="text-center py-4 text-gray-400 dark:text-gray-500">
                                            <i class="fa-regular fa-folder-open text-2xl mb-2"></i>
                                            <p class="text-sm">No tasks assigned</p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Drawer Footer (Sticky) -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                                <div class="flex gap-3">
                                    <!-- Action Buttons (only for pending requests) -->
                                    <template x-if="selectedRequest && selectedRequest.status === 'pending'">
                                        <div class="flex gap-3 flex-1">
                                            <button @click="handleDeclineClick()"
                                                :disabled="isSubmitting || (showRejectionForm && !rejectionReason)"
                                                class="flex-1 px-4 py-2.5 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
                                                :class="{
                                                    'bg-red-600 hover:bg-red-700': !isSubmitting && (!showRejectionForm || rejectionReason),
                                                    'bg-red-400 cursor-not-allowed': isSubmitting || (showRejectionForm && !rejectionReason)
                                                }">
                                                <i class="fa-solid fa-xmark"></i>
                                                <span x-text="isSubmitting ? 'Processing...' : (showRejectionForm ? 'Confirm Decline' : 'Decline')"></span>
                                            </button>
                                            <button @click="approveRequest()"
                                                :disabled="isSubmitting || showRejectionForm"
                                                class="flex-1 px-4 py-2.5 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
                                                :class="{
                                                    'bg-green-600 hover:bg-green-700': !isSubmitting && !showRejectionForm,
                                                    'bg-green-400 cursor-not-allowed': isSubmitting || showRejectionForm
                                                }">
                                                <i class="fa-solid fa-check"></i>
                                                <span x-text="isSubmitting ? 'Processing...' : 'Approve'"></span>
                                            </button>
                                        </div>
                                    </template>

                                    <!-- Status indicator for non-pending requests -->
                                    <template x-if="selectedRequest && selectedRequest.status !== 'pending'">
                                        <div class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg border"
                                            :class="{
                                                'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800': selectedRequest.status === 'approved',
                                                'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800': selectedRequest.status === 'rejected',
                                                'bg-gray-50 dark:bg-gray-700/20 border-gray-200 dark:border-gray-600': selectedRequest.status === 'cancelled'
                                            }">
                                            <i class="fa-solid"
                                                :class="{
                                                    'fa-circle-check text-green-600 dark:text-green-400': selectedRequest.status === 'approved',
                                                    'fa-circle-xmark text-red-600 dark:text-red-400': selectedRequest.status === 'rejected',
                                                    'fa-ban text-gray-500 dark:text-gray-400': selectedRequest.status === 'cancelled'
                                                }"></i>
                                            <span class="text-sm font-medium"
                                                :class="{
                                                    'text-green-700 dark:text-green-400': selectedRequest.status === 'approved',
                                                    'text-red-700 dark:text-red-400': selectedRequest.status === 'rejected',
                                                    'text-gray-600 dark:text-gray-400': selectedRequest.status === 'cancelled'
                                                }"
                                                x-text="selectedRequest.status === 'approved' ? 'Approved' : (selectedRequest.status === 'rejected' ? 'Declined' : 'Cancelled')"></span>
                                        </div>
                                    </template>

                                    <button @click="closeRequestModal()"
                                        class="flex-1 text-sm px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

</x-layouts.general-employer>