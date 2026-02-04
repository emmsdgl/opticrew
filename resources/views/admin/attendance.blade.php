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
                        reason: record.requestReason,
                        adminNotes: record.requestAdminNotes,
                        durationDays: record.requestDurationDays,
                        createdAt: record.requestCreatedAt,
                    };
                    this.adminNotes = record.requestAdminNotes || '';
                    this.rejectionReason = '';
                    this.showRequestModal = true;
                    document.body.style.overflow = 'hidden';
                },

                closeRequestModal() {
                    this.showRequestModal = false;
                    this.selectedRequest = null;
                    this.adminNotes = '';
                    this.rejectionReason = '';
                    document.body.style.overflow = 'auto';
                },

                async approveRequest() {
                    if (this.isSubmitting || !this.selectedRequest) return;
                    this.isSubmitting = true;

                    try {
                        const response = await fetch(`/api/admin/leave-requests/${this.selectedRequest.id}/approve`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ admin_notes: this.adminNotes })
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
                        const response = await fetch(`/api/admin/leave-requests/${this.selectedRequest.id}/reject`, {
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
                        'rejected': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
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

            <!-- Request Details Modal -->
            <div x-show="showRequestModal"
                x-cloak
                @click="closeRequestModal()"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/80 p-4"
                style="display: none;">
                <div @click.stop
                    class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-700"
                    x-show="showRequestModal"
                    x-transition>

                    <!-- Close Button -->
                    <button type="button"
                        @click="closeRequestModal()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Modal Content -->
                    <div class="px-6 py-8">
                        <!-- Header -->
                        <div class="text-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                Request Details
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Review and manage this leave request
                            </p>
                        </div>

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
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Start Date</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.date"></span>
                                </div>

                                <template x-if="selectedRequest.endDate">
                                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">End Date</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.endDate"></span>
                                    </div>
                                </template>

                                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Duration</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.durationDays + ' day(s)'"></span>
                                </div>

                                <div class="py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400 block mb-2">Reason</span>
                                    <p class="text-sm text-gray-900 dark:text-white" x-text="selectedRequest.reason"></p>
                                </div>

                                <div class="flex justify-between items-center py-3">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Submitted</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="selectedRequest.createdAt"></span>
                                </div>
                            </div>
                        </template>

                        <!-- Admin Notes Input (for pending requests) -->
                        <template x-if="selectedRequest && selectedRequest.status === 'pending'">
                            <div class="mb-6 space-y-4">
                                <!-- Rejection Reason Dropdown -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Rejection Reason <span class="text-gray-400">(required for rejection)</span>
                                    </label>
                                    <select
                                        x-model="rejectionReason"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select a reason...</option>
                                        <template x-for="reason in rejectionReasons" :key="reason">
                                            <option :value="reason" x-text="reason"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Additional Notes -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Additional Notes <span class="text-gray-400">(optional)</span>
                                    </label>
                                    <textarea
                                        x-model="adminNotes"
                                        rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Add additional notes..."></textarea>
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

                        <!-- Action Buttons (only for pending and rejected requests) -->
                        <template x-if="selectedRequest && selectedRequest.status !== 'approved'">
                            <div class="flex gap-3">
                                <!-- Decline Button -->
                                <button @click="selectedRequest.status === 'pending' && rejectionReason && rejectRequest()"
                                    :disabled="isSubmitting || selectedRequest.status !== 'pending' || (selectedRequest.status === 'pending' && !rejectionReason)"
                                    class="flex-1 px-4 py-3 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
                                    :class="{
                                        'bg-red-600 hover:bg-red-700': selectedRequest.status === 'pending' && rejectionReason && !isSubmitting,
                                        'bg-red-400 cursor-not-allowed': (isSubmitting && selectedRequest.status === 'pending') || (selectedRequest.status === 'pending' && !rejectionReason),
                                        'bg-red-600 cursor-default': selectedRequest.status === 'rejected'
                                    }">
                                    <i class="fa-solid fa-xmark"></i>
                                    <span x-text="selectedRequest.status === 'rejected' ? 'Declined' : (isSubmitting ? 'Processing...' : 'Decline')"></span>
                                </button>
                                <!-- Approve Button -->
                                <button @click="selectedRequest.status === 'pending' && !rejectionReason && approveRequest()"
                                    :disabled="isSubmitting || selectedRequest.status !== 'pending' || (selectedRequest.status === 'pending' && rejectionReason)"
                                    class="flex-1 px-4 py-3 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
                                    :class="{
                                        'bg-green-600 hover:bg-green-700': selectedRequest.status === 'pending' && !rejectionReason && !isSubmitting,
                                        'bg-green-400 cursor-not-allowed': (isSubmitting && selectedRequest.status === 'pending') || (selectedRequest.status === 'pending' && rejectionReason),
                                        'bg-gray-400 cursor-not-allowed': selectedRequest.status === 'rejected'
                                    }">
                                    <i class="fa-solid fa-check"></i>
                                    <span x-text="isSubmitting ? 'Processing...' : 'Approve'"></span>
                                </button>
                            </div>
                        </template>

                        <!-- Approved Status Message -->
                        <template x-if="selectedRequest && selectedRequest.status === 'approved'">
                            <div class="flex items-center justify-center gap-2 py-3 px-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                                <span class="text-sm font-medium text-green-700 dark:text-green-400">This request has been approved</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

    </section>

</x-layouts.general-employer>