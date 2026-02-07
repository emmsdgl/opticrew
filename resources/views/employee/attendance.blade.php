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

            <div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-6 p-6">
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

                <!-- Shift Start Card -->
                <x-statisticscard
                    title="Shift Start"
                    :value="$shiftStartValue"
                    :subtitle="$shiftStartSubtitle"
                    icon="fa-solid fa-play"
                    icon-bg="bg-blue-100 dark:bg-blue-900/30"
                    icon-color="text-blue-600 dark:text-blue-400"
                />

                <!-- Shift End Card -->
                <x-statisticscard
                    title="Shift End"
                    :value="$shiftEndValue"
                    :subtitle="$shiftEndSubtitle"
                    icon="fa-solid fa-stop"
                    icon-bg="bg-blue-100 dark:bg-blue-900/30"
                    icon-color="text-blue-600 dark:text-blue-400"
                />
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
                                    <form action="{{ route('employee.attendance.clockin') }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit"
                                            class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-play"></i>
                                            Clock In
                                        </button>
                                    </form>
                                @elseif($isClockedIn)
                                    <form action="{{ route('employee.attendance.clockout') }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit"
                                            class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-stop"></i>
                                            Clock Out
                                        </button>
                                    </form>
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
</x-layouts.general-employee>
