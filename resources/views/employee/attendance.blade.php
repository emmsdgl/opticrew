<x-layouts.general-employee :title="'Attendance'">

    {{-- MOBILE LAYOUT (< 1024px) - Hidden on large screens --}}
    <div class="lg:hidden">
        @include('employee.mobile.attendance')
    </div>

    {{-- DESKTOP LAYOUT (≥ 1024px) - Hidden on small screens --}}
    <section role="status" class="w-full hidden lg:flex flex-col lg:flex-col gap-1 p-4 md:p-6"
        x-data="{
            showRequestModal: false,
            selectedRequest: null,
            isCancelling: false,
            requestRecords: {{ Js::from($requestRecords ?? []) }},

            openRequestModal(index) {
                this.selectedRequest = this.requestRecords[index];
                this.showRequestModal = true;
                document.body.style.overflow = 'hidden';
            },

            closeRequestModal() {
                this.showRequestModal = false;
                this.selectedRequest = null;
                this.isCancelling = false;
                document.body.style.overflow = 'auto';
            },

            async cancelRequest() {
                if (this.isCancelling || !this.selectedRequest) return;
                if (!confirm('Are you sure you want to cancel this request?')) return;

                this.isCancelling = true;

                try {
                    const response = await fetch(`/employee/requests/${this.selectedRequest.requestId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.closeRequestModal();
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to cancel request');
                    }
                } catch (error) {
                    alert('An error occurred. Please try again.');
                } finally {
                    this.isCancelling = false;
                }
            }
        }">

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
            <x-labelwithvalue label="Attendance Logs" count="({{ count($attendanceRecords) }})" />

            @if(count($attendanceRecords) > 0)
                <x-attendancelistitem :records="$attendanceRecords" :show-header="true" />
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-calendar-xmark text-4xl mb-4"></i>
                    <p>No attendance records found for this month.</p>
                </div>
            @endif
        </div>

        <!-- Inner Panel - Requests Records List -->
        <div class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <div class="flex flex-row w-full justify-between">
                <x-labelwithvalue label="Request Logs" count="({{ count($requestRecords ?? []) }})" />
                <a href="{{ route('employee.requests.create') }}"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                    New Request →
                </a>
            </div>

            @if(count($requestRecords ?? []) > 0)
                <!-- Request Records Grid -->
                <div class="w-full overflow-x-auto">
                    <!-- Table Header -->
                    <div class="hidden md:grid grid-cols-6 gap-4 px-6 py-4 bg-gray-50 dark:bg-gray-800
                                border-b border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Status
                        </div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Date
                        </div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Type
                        </div>
                        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Time Range</div>
                        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Reason</div>
                        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Action</div>
                    </div>

                    <!-- Table Body -->
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($requestRecords as $index => $request)
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 px-6 py-4 bg-white dark:bg-gray-900
                                    hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">

                            <!-- Status Badge -->
                            <div class="flex items-center gap-2">
                                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400">Status:</span>
                                @if($request['status'] === 'present')
                                    <x-badge
                                        label="Approved"
                                        colorClass="bg-[#2FBC0020] text-[#2FBC00]"
                                        size="text-xs" />
                                @elseif($request['status'] === 'late')
                                    <x-badge
                                        label="Pending"
                                        colorClass="bg-[#FF7F0020] text-[#FF7F00]"
                                        size="text-xs" />
                                @else
                                    <x-badge
                                        label="Rejected"
                                        colorClass="bg-[#FE1E2820] text-[#FE1E28]"
                                        size="text-xs" />
                                @endif
                            </div>

                            <!-- Date -->
                            <div class="flex flex-col">
                                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Date:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $request['date'] }}</span>
                            </div>

                            <!-- Type -->
                            <div class="flex flex-col">
                                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Type:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $request['requestType'] }}</span>
                            </div>

                            <!-- Time Range -->
                            <div class="flex flex-col">
                                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Time Range:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $request['requestTimeRange'] }}</span>
                            </div>

                            <!-- Reason -->
                            <div class="flex flex-col">
                                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Reason:</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400 truncate" title="{{ $request['requestReason'] }}">{{ Str::limit($request['requestReason'], 25) }}</span>
                            </div>

                            <!-- Action Button -->
                            <div class="flex items-center justify-center">
                                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 mr-2">Action:</span>
                                <button
                                    @click="openRequestModal({{ $index }})"
                                    class="w-full px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                                    View
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-clipboard-list text-4xl mb-4"></i>
                    <p>No request records found.</p>
                </div>
            @endif
        </div>

        <!-- Request Details Modal -->
        <div x-show="showRequestModal" x-cloak @click="closeRequestModal()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 dark:bg-black/80 p-4 sm:p-8"
            style="display: none;">
            <div @click.stop
                class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-700"
                x-show="showRequestModal" x-transition>

                <!-- Close button -->
                <button type="button" @click="closeRequestModal()"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none rounded-lg p-1 z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Modal Body -->
                <div class="p-6 sm:p-8">
                    <!-- Header -->
                    <div class="py-4 text-center">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            Request Details
                        </h3>
                        <!-- Status Badge -->
                        <div class="flex items-center justify-center gap-2 mt-3">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                            <span class="px-3 py-1 text-xs rounded-full font-semibold"
                                :class="{
                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': selectedRequest?.requestStatus === 'Pending',
                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': selectedRequest?.requestStatus === 'Approved',
                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': selectedRequest?.requestStatus === 'Rejected'
                                }"
                                x-text="selectedRequest?.requestStatus"></span>
                        </div>
                    </div>

                    <!-- Request Information -->
                    <div class="space-y-4 text-sm py-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Request Type</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="selectedRequest?.requestType"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Date</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="selectedRequest?.requestDate"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Time Range</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="selectedRequest?.requestTimeRange"></span>
                        </div>

                        <template x-if="selectedRequest?.requestFromTime && selectedRequest?.requestToTime">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Custom Hours</span>
                                <span class="font-medium text-gray-900 dark:text-white" x-text="selectedRequest?.requestFromTime + ' - ' + selectedRequest?.requestToTime"></span>
                            </div>
                        </template>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Reason</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="selectedRequest?.requestReason"></span>
                        </div>

                        <template x-if="selectedRequest?.requestDescription">
                            <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400 block mb-2">Description</span>
                                <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg" x-text="selectedRequest?.requestDescription"></p>
                            </div>
                        </template>

                        <template x-if="selectedRequest?.requestProofDocument">
                            <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400">Proof Document</span>
                                <a :href="'/storage/' + selectedRequest?.requestProofDocument" target="_blank"
                                    class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                    View Document
                                </a>
                            </div>
                        </template>

                        <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-gray-500 dark:text-gray-400">Submitted On</span>
                            <span class="font-medium text-gray-900 dark:text-white text-sm" x-text="selectedRequest?.requestCreatedAt"></span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 space-y-3">
                        <!-- Cancel Button (only for pending requests) -->
                        <template x-if="selectedRequest?.requestStatus === 'Pending'">
                            <button @click="cancelRequest()"
                                :disabled="isCancelling"
                                class="w-full px-6 py-3 text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
                                :class="isCancelling ? 'bg-red-400 text-white cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 text-white'">
                                <i class="fa-solid fa-ban"></i>
                                <span x-text="isCancelling ? 'Cancelling...' : 'Cancel Request'"></span>
                            </button>
                        </template>

                        <!-- Close Button -->
                        <button @click="closeRequestModal()"
                            class="w-full px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors duration-200">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </section>
</x-layouts.general-employee>
