<x-layouts.general-employee :title="'Attendance'">
    <x-skeleton-page :preset="'stats-table'">

    {{-- MOBILE LAYOUT (< 1024px) - Hidden on large screens --}}
    <div class="lg:hidden">
        @include('employee.mobile.attendance')
    </div>

    {{-- DESKTOP LAYOUT (≥ 1024px) - Hidden on small screens --}}
    <section role="status" class="w-full hidden lg:flex flex-col lg:flex-col gap-1 p-16 md:p-6"
        x-data="{
            showRequestModal: false,
            selectedRequest: null,
            isCancelling: false,
            requestRecords: {{ Js::from($requestRecords ?? []) }},
            showAttendanceDrawer: false,

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

            openAttendanceDrawer() {
                this.showAttendanceDrawer = true;
                document.body.style.overflow = 'hidden';
            },

            closeAttendanceDrawer() {
                this.showAttendanceDrawer = false;
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
                        window.showErrorDialog('Request Failed', data.message || 'Failed to cancel request');
                    }
                } catch (error) {
                    window.showErrorDialog('Request Failed', 'An error occurred. Please try again.');
                } finally {
                    this.isCancelling = false;
                }
            }
        }">

        <!-- Clock In Status Banner -->
        @if($isClockedIn)
            @php
                $todayAttendanceRecord = \App\Models\Attendance::where('employee_id', $employee->id)->whereDate('clock_in', \Carbon\Carbon::today())->first();
                $clockInTimeFormatted = $todayAttendanceRecord ? \Carbon\Carbon::parse($todayAttendanceRecord->clock_in)->format('g:i A') : '';
            @endphp
            <div class="flex items-center gap-2 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-sm font-medium text-green-800 dark:text-green-300">
                    <i class="fas fa-check-circle"></i> You're Present Today, Clocked in at {{ $clockInTimeFormatted }}
                </span>
            </div>
        @endif

        <!-- Inner Panel - Summary Cards Container -->
        <div class="flex flex-col gap-6 w-full rounded-lg p-4">
            <x-labelwithvalue label="Summary" count="" />

            @php
                $shiftStart = $employee->shift_start ?? '11:00';
                $shiftEnd = $employee->shift_end ?? '19:00';

                // Calculate time values
                $now = \Carbon\Carbon::now();
                $shiftEndTime = \Carbon\Carbon::today()->setTimeFromTimeString($shiftEnd);
                $shiftStartTime = \Carbon\Carbon::today()->setTimeFromTimeString($shiftStart);

                // Shift Start subtitle
                if ($now->lt($shiftStartTime)) {
                    $startMinutes = $now->diffInMinutes($shiftStartTime);
                    $startHours = floor($startMinutes / 60);
                    $startMins = $startMinutes % 60;
                    $shiftStartSubtitle = 'Starts in ' . $startHours . 'h ' . $startMins . 'm';
                } else {
                    $shiftStartSubtitle = 'Shift started';
                }

                // Shift End subtitle
                if ($now->lt($shiftStartTime)) {
                    $shiftEndSubtitle = 'Not started yet';
                } elseif ($now->gt($shiftEndTime)) {
                    $shiftEndSubtitle = 'Shift ended';
                } else {
                    $remainingMinutes = $now->diffInMinutes($shiftEndTime);
                    $remainingHours = floor($remainingMinutes / 60);
                    $remainingMins = $remainingMinutes % 60;
                    $shiftEndSubtitle = $remainingHours . 'h ' . $remainingMins . 'm remaining';
                }

                $shiftStartValue = \Carbon\Carbon::createFromFormat('H:i', $shiftStart)->format('g:i A');
                $shiftEndValue = \Carbon\Carbon::createFromFormat('H:i', $shiftEnd)->format('g:i A');
            @endphp

            <div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-6">
                @foreach($stats as $stat)
                    <x-employee-components.kpi-stat-card
                        :label="$stat['title']"
                        :value="$stat['value']"
                        :icon="$stat['icon'] ?? 'fas fa-chart-bar'"
                        :trend="$stat['trendValue'] ?? $stat['subtitle'] ?? ''"
                        :trendUp="($stat['trend'] ?? '') === 'up'"
                    />
                @endforeach

                <!-- Shift Start Card -->
                <x-employee-components.kpi-stat-card
                    label="Shift Start"
                    :value="$shiftStartValue"
                    icon="fa-solid fa-play"
                    :trend="$shiftStartSubtitle"
                    :trendUp="true"
                />

                <!-- Shift End Card -->
                <x-employee-components.kpi-stat-card
                    label="Shift End"
                    :value="$shiftEndValue"
                    icon="fa-solid fa-stop"
                    :trend="$shiftEndSubtitle"
                    :trendUp="false"
                />
            </div>
        </div>
        <!-- Inner Panel - Attendance Records List -->
        <div class="flex flex-col gap-6 w-full rounded-lg p-4">
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
        <div class="flex flex-col gap-6 w-full rounded-lg p-4">
            <div class="flex flex-row w-full justify-between">
                <x-labelwithvalue label="Request Logs" count="({{ count($requestRecords ?? []) }})" />
                <a href="{{ route('employee.requests.create') }}"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                    <i class="fas fa-plus mr-1"></i>New Request
                </a>
            </div>

            @if(count($requestRecords ?? []) > 0)
                <!-- Request Records Grid -->
                <div class="w-full overflow-x-auto bg-white dark:bg-gray-800/30 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800">
                    <!-- Table Header -->
                    <div class="hidden md:grid grid-cols-6 gap-4 px-6 py-4 bg-gray-50 dark:bg-gray-800/50
                                border-b border-gray-200 dark:border-gray-700 rounded-t-2xl">
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
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 px-6 py-4
                                    hover:bg-blue-400/10 dark:hover:bg-blue-400/10 transition-colors">

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
                                    class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                    <i class="fa-regular fa-eye mr-1 text-xs"></i> View
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-16 px-6 text-center h-auto bg-white dark:bg-gray-800/30 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-800">
                    <div class="w-48 h-48 mb-6 flex items-center justify-center">
                        <img src="{{ asset('images/icons/no-items-found.svg') }}"
                             alt="No requests"
                             class="w-full h-full object-contain opacity-80 dark:opacity-60">
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">
                        No request records yet
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md">
                        You don't have any request records at the moment. New requests will appear here once submitted.
                    </p>
                </div>
            @endif
        </div>

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
                                View your leave request details
                            </p>

                            <!-- Request Information -->
                            <template x-if="selectedRequest">
                                <div class="space-y-0 mb-6">
                                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                                        <span class="text-sm font-semibold px-2 py-1 rounded-full"
                                            :class="{
                                                'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400': selectedRequest.requestStatus === 'Pending',
                                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': selectedRequest.requestStatus === 'Approved',
                                                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': selectedRequest.requestStatus === 'Rejected'
                                            }"
                                            x-text="selectedRequest.requestStatus"></span>
                                    </div>

                                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Request Type</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.requestType"></span>
                                    </div>

                                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Date</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.requestDate"></span>
                                    </div>

                                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Time Range</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.requestTimeRange"></span>
                                    </div>

                                    <template x-if="selectedRequest.requestFromTime && selectedRequest.requestToTime">
                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Custom Hours</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedRequest.requestFromTime + ' - ' + selectedRequest.requestToTime"></span>
                                        </div>
                                    </template>

                                    <div class="py-3 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-sm text-gray-500 dark:text-gray-400 block mb-2">Reason</span>
                                        <p class="text-sm text-gray-900 dark:text-white" x-text="selectedRequest.requestReason"></p>
                                    </div>

                                    <template x-if="selectedRequest.requestDescription">
                                        <div class="py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400 block mb-2">Description</span>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg" x-text="selectedRequest.requestDescription"></p>
                                        </div>
                                    </template>

                                    <template x-if="selectedRequest.requestProofDocument">
                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Proof Document</span>
                                            <a :href="'/storage/' + selectedRequest.requestProofDocument" target="_blank"
                                                class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium max-w-[180px] truncate inline-block"
                                                x-text="selectedRequest.requestProofDocument.split('/').pop()">
                                            </a>
                                        </div>
                                    </template>

                                    <div class="flex justify-between items-center py-3">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Submitted</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400" x-text="selectedRequest.requestCreatedAt"></span>
                                    </div>
                                </div>
                            </template>

                            <!-- Status Messages -->
                            <template x-if="selectedRequest && selectedRequest.requestStatus === 'Approved'">
                                <div class="flex items-center justify-center gap-2 py-3 px-4 mb-6 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                    <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                                    <span class="text-sm font-medium text-green-700 dark:text-green-400">This request has been approved</span>
                                </div>
                            </template>

                            <template x-if="selectedRequest && selectedRequest.requestStatus === 'Rejected'">
                                <div class="flex items-center justify-center gap-2 py-3 px-4 mb-6 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                    <i class="fa-solid fa-circle-xmark text-red-600 dark:text-red-400"></i>
                                    <span class="text-sm font-medium text-red-700 dark:text-red-400">This request has been declined</span>
                                </div>
                            </template>
                        </div>

                        <!-- Drawer Footer (Sticky) -->
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                            <div class="flex gap-3">
                                <!-- Cancel Button (only for pending requests) -->
                                <template x-if="selectedRequest && selectedRequest.requestStatus === 'Pending'">
                                    <button @click="cancelRequest()"
                                        :disabled="isCancelling"
                                        class="flex-1 px-4 py-2.5 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
                                        :class="isCancelling ? 'bg-red-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'">
                                        <i class="fa-solid fa-ban"></i>
                                        <span x-text="isCancelling ? 'Cancelling...' : 'Cancel Request'"></span>
                                    </button>
                                </template>

                                <!-- Status indicator for non-pending requests -->
                                <template x-if="selectedRequest && selectedRequest.requestStatus !== 'Pending'">
                                    <div class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg border"
                                        :class="{
                                            'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800': selectedRequest.requestStatus === 'Approved',
                                            'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800': selectedRequest.requestStatus === 'Rejected'
                                        }">
                                        <i class="fa-solid"
                                            :class="{
                                                'fa-circle-check text-green-600 dark:text-green-400': selectedRequest.requestStatus === 'Approved',
                                                'fa-circle-xmark text-red-600 dark:text-red-400': selectedRequest.requestStatus === 'Rejected'
                                            }"></i>
                                        <span class="text-sm font-medium"
                                            :class="{
                                                'text-green-700 dark:text-green-400': selectedRequest.requestStatus === 'Approved',
                                                'text-red-700 dark:text-red-400': selectedRequest.requestStatus === 'Rejected'
                                            }"
                                            x-text="selectedRequest.requestStatus"></span>
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

        <!-- Attendance Details Slide-in Drawer -->
        <div x-show="showAttendanceDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
            <!-- Backdrop -->
            <div x-show="showAttendanceDrawer"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="closeAttendanceDrawer()"
                 class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

            <!-- Drawer Panel -->
            <div class="fixed inset-y-0 right-0 flex max-w-full">
                <div x-show="showAttendanceDrawer"
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
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Today's Attendance</h2>
                            <button type="button" @click="closeAttendanceDrawer()"
                                class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Drawer Body (Scrollable) -->
                        <div class="flex-1 overflow-y-auto p-6">
                            <!-- Status Badge -->
                            <div class="flex items-center gap-2 mb-6">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                @if($isClockedIn)
                                    <span class="px-3 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">Clocked In</span>
                                @elseif($hasAttendanceToday)
                                    <span class="px-3 py-1 text-xs rounded-full bg-[#00BFFF20] text-[#00BFFF] font-semibold">Completed</span>
                                @else
                                    <span class="px-3 py-1 text-xs rounded-full bg-[#FFA50020] text-[#FFA500] font-semibold">Not Logged</span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">View your attendance time details for today</p>

                            <!-- Attendance Information -->
                            <div class="space-y-0">
                                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Employee Name</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white text-right">
                                        {{ $employee->user->name ?? 'N/A' }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Date</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white text-right">
                                        {{ \Carbon\Carbon::today()->format('M d, Y') }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Clock In Time</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white text-right">
                                        @if($hasAttendanceToday)
                                            {{ \Carbon\Carbon::parse(\App\Models\Attendance::where('employee_id', $employee->id)->whereDate('clock_in', \Carbon\Carbon::today())->first()->clock_in)->format('h:i A') }}
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">Not clocked in</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Clock Out Time</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white text-right">
                                        @if($hasAttendanceToday && !\App\Models\Attendance::where('employee_id', $employee->id)->whereDate('clock_in', \Carbon\Carbon::today())->first()->clock_out)
                                            <span class="text-blue-500 dark:text-blue-400">Still working...</span>
                                        @elseif($hasAttendanceToday)
                                            {{ \Carbon\Carbon::parse(\App\Models\Attendance::where('employee_id', $employee->id)->whereDate('clock_in', \Carbon\Carbon::today())->first()->clock_out)->format('h:i A') }}
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                        @endif
                                    </span>
                                </div>

                                @if($hasAttendanceToday && \App\Models\Attendance::where('employee_id', $employee->id)->whereDate('clock_in', \Carbon\Carbon::today())->first()->clock_out)
                                    @php
                                        $attendance = \App\Models\Attendance::where('employee_id', $employee->id)->whereDate('clock_in', \Carbon\Carbon::today())->first();
                                        $clockIn = \Carbon\Carbon::parse($attendance->clock_in);
                                        $clockOut = \Carbon\Carbon::parse($attendance->clock_out);
                                        $duration = $clockOut->diff($clockIn);
                                    @endphp
                                    <div class="flex justify-between items-center py-3">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Hours</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white text-right">
                                            {{ $duration->h }}h {{ $duration->i }}m
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Drawer Footer (Sticky) -->
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                            <div class="flex gap-3">
                                @if(!$hasAttendanceToday)
                                    <form id="attendance-clockin-form" action="{{ route('employee.attendance.clockin') }}" method="POST" style="display:none;">
                                        @csrf
                                        <input type="hidden" name="latitude" class="geo-latitude">
                                        <input type="hidden" name="longitude" class="geo-longitude">
                                    </form>
                                    <button type="button" onclick="handleClockAction('attendance-clockin-form', 'Clock In', 'Are you sure you want to clock in?', 'Clocked In', 'You have successfully clocked in.')"
                                        class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-play"></i>
                                        Clock In
                                    </button>
                                @elseif($isClockedIn)
                                    <form id="attendance-clockout-form" action="{{ route('employee.attendance.clockout') }}" method="POST" style="display:none;">
                                        @csrf
                                        <input type="hidden" name="latitude" class="geo-latitude">
                                        <input type="hidden" name="longitude" class="geo-longitude">
                                    </form>
                                    <button type="button" onclick="handleClockAction('attendance-clockout-form', 'Clock Out', 'Are you sure you want to clock out?', 'Clocked Out', 'You have successfully clocked out.')"
                                        class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-stop"></i>
                                        Clock Out
                                    </button>
                                @endif
                                <button @click="closeAttendanceDrawer()"
                                    class="flex-1 text-sm px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    </x-skeleton-page>

@push('scripts')
@once
<script src="{{ asset('js/geofencing.js') }}"></script>
@endonce
<script>
    async function handleClockAction(formId, title, message, successTitle, successMessage) {
        // Check location permission first - must be granted before clock in/out
        try {
            const permResult = await navigator.permissions.query({ name: 'geolocation' });
            if (permResult.state === 'denied') {
                window.showErrorDialog('Location Required', 'Location access is blocked. Please click the lock icon in the address bar, allow Location, and reload the page.');
                return;
            }
        } catch (e) { /* permissions API not supported, proceed with geolocation request */ }

        // Request location - block clock in/out if denied
        let position;
        try {
            position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true, timeout: 10000, maximumAge: 30000
                });
            });
        } catch (geoError) {
            if (geoError.code === geoError.PERMISSION_DENIED) {
                window.showErrorDialog('Location Required', 'Location permission is required to clock in/out. Please enable location access in your browser settings and try again.');
            } else {
                window.showErrorDialog('Location Error', 'Unable to get your location. Please check your device settings and try again.');
            }
            return;
        }

        try {
            await window.showConfirmDialog(title, message, 'Confirm', 'Cancel');
        } catch { return; }

        const form = document.getElementById(formId);

        // Set location from the position we just obtained
        const latField = form.querySelector('.geo-latitude');
        const lngField = form.querySelector('.geo-longitude');
        if (latField && lngField) {
            latField.value = position.coords.latitude;
            lngField.value = position.coords.longitude;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            });

            if (response.ok || response.redirected) {
                window.showSuccessDialog(successTitle, successMessage, 'Done', window.location.href);
            } else {
                const data = await response.json().catch(() => ({}));
                window.showErrorDialog('Error', data.message || 'Something went wrong. Please try again.');
            }
        } catch (error) {
            window.showErrorDialog('Error', 'An unexpected error occurred. Please try again.');
        }
    }
</script>
<script>
    // Initialize geofencing for desktop attendance page
    (function() {
        const hasAttendanceToday = {{ ($hasAttendanceToday && !$isClockedIn) ? 'true' : 'false' }};
        if (hasAttendanceToday) return; // Already completed, no need for geofencing

        if (window.geofencingInitialized) return;
        window.geofencingInitialized = true;

        document.addEventListener('DOMContentLoaded', function() {
            const timestamp = new Date().getTime();
            fetch(`/api/company-settings?_=${timestamp}`, {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!window.geofencing) {
                    window.geofencing = new Geofencing({
                        officeLatitude: data.office_latitude,
                        officeLongitude: data.office_longitude,
                        radius: data.geofence_radius,
                        locationName: data.location_name,
                        locationType: data.location_type,
                        message: data.message,
                        buttonId: 'desktop-clock-button',
                        statusElementId: 'desktop-geofence-status',
                        distanceElementId: 'desktop-geofence-distance'
                    });
                }
            })
            .catch(error => console.error('Error fetching task location:', error));
        });
    })();

    window.addEventListener('beforeunload', function() {
        if (window.geofencing) {
            window.geofencing.stopWatching();
        }
    });
</script>

@if(!$hasAttendanceToday && !$isClockedIn)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const sonnerEl = document.querySelector('[x-data="sonnerToast()"]');
            if (sonnerEl && sonnerEl._x_dataStack) {
                Alpine.$data(sonnerEl).show(
                    'Clock In Reminder',
                    "You haven't clocked in yet today. Use the attendance drawer to clock in.",
                    'warning', {
                        persistent: true,
                    }
                );
            }
        }, 1000);
    });
</script>
@endif
@endpush
</x-layouts.general-employee>
