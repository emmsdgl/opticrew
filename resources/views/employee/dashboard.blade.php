<x-layouts.general-employee :title="'Employee Dashboard'">

    {{-- MOBILE LAYOUT (< 1024px) - Hidden on large screens --}} <div class="lg:hidden">
        @include('employee.mobile.dashboard')
        </div>

        {{-- DESKTOP LAYOUT (≥ 1024px) - Hidden on small screens --}}
        <section role="status" class="hidden lg:flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]"
            x-data="{
            showAttendanceDrawer: false,
            showRequestModal: false,
            selectedRequest: null,
            isCancelling: false,
            employeeRequests: {{ Js::from($employeeRequests) }},

            openAttendanceDrawer() {
                this.showAttendanceDrawer = true;
                document.body.style.overflow = 'hidden';
            },

            closeAttendanceDrawer() {
                this.showAttendanceDrawer = false;
                document.body.style.overflow = 'auto';
            },

            openRequestModal(index) {
                this.selectedRequest = this.employeeRequests[index];
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
                    const response = await fetch(`/employee/requests/${this.selectedRequest.id}/cancel`, {
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
            <!-- Left Panel - Dashboard Content -->
            <div
                class="flex flex-col gap-6 flex-1 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
                <!-- Inner Up - Dashboard Header -->
                <div
                    class="w-full mt-6 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-48 sm:h-56 md:h-64 lg:h-1/3">
                    <x-herocard :headerName="$employee->user->name ?? 'Employee'" :headerDesc="'Welcome to the employee dashboard. Track tasks and manage them in the dashboard'" :headerIcon="'hero-employee'" />
                </div>
                <!-- Inner Middle - Calendar -->
                <x-labelwithvalue label="My Calendar" count="" />
                <div
                    class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-60 sm:h-72 md:h-80 lg:h-1/3">
                    <x-calendar :holidays="$holidays" calendar-id="desktop" />
                </div>

                <!-- Inner Down - Tasks Particulars -->
                <div class="w-full rounded-lg h-56 sm:h-56 md:h-auto">
                    <x-labelwithvalue label="Your To-Do List" count="({{ $todoList->count() }})" />
                    <div
                        class="{{ $todoList->count() > 0 ? 'h-96 overflow-y-auto' : '' }} border border-dashed border-gray-400 dark:border-gray-700 rounded-lg my-6">

                        @php
                            // Transform tasks to the format expected by task-overview-list component
                            $tasks = $todoList->map(function ($task) {
                                return [
                                    'service' => $task->task_description,
                                    'status' => $task->status,
                                    'service_date' => \Carbon\Carbon::parse($task->date)->format('M d, Y'),
                                    'service_time' => $task->duration . ' min',
                                    'description' => 'Client: ' . $task->client_name . ($task->cabin_name ? ' • Location: ' . $task->cabin_name : ''),
                                    'action_url' => route('employee.tasks.show', ['task' => $task->id, 'from' => 'dashboard']),
                                    'action_label' => 'View Details',
                                ];
                            })->toArray();
                        @endphp

                        <x-employee-components.task-overview-list :items="$tasks" fixedHeight="20rem" maxHeight="30rem"
                            emptyTitle="No tasks assigned yet"
                            emptyMessage="You don't have any tasks at the moment. New tasks will appear here once assigned." />
                    </div>
                </div>
                <!-- Inner Down - New Lessons -->
                <div class="w-full rounded-lg h-48 sm:h-56 md:h-auto flex flex-col gap-6">
                    <div class="flex flex-row justify-between items-center w-full">
                        <x-labelwithvalue label="New Lessons" count="" />
                        @php
                            $timeOptions = ['All', 'Today', 'Yesterday', 'Last 7 days', 'Last 30 days'];
                        @endphp

                        <x-dropdown :options="$timeOptions" :default="$period" id="dropdown-time" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {{-- Card 1: Deep Cleaning Fundamentals --}}
                        <x-employee-components.lesson-card duration="40 mins" title="Deep Cleaning Fundamentals"
                            description="Master the essential techniques of deep cleaning for residential and commercial spaces"
                            :progress="0" buttonText="Check now"
                            buttonUrl="{{ route('employee.development') }}?course=1" />

                        {{-- Card 2: Professional Window Cleaning --}}
                        <x-employee-components.lesson-card duration="40 mins" title="Professional Window Cleaning"
                            description="Learn advanced window cleaning methods and safety protocols" :progress="45"
                            buttonText="Continue" buttonUrl="{{ route('employee.development') }}?course=2" />

                        {{-- Card 3: Industrial Floor Care --}}
                        <x-employee-components.lesson-card duration="40 mins" title="Industrial Floor Care"
                            description="Master the art of maintaining various floor types" :progress="100"
                            buttonText="Review" buttonUrl="{{ route('employee.development') }}?course=4" />
                    </div>
                </div>
            </div>

            <!-- Right Panel - Tasks Details -->
            <div
                class="flex flex-col gap-6 w-full lg:w-1/3 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">

                <!-- Inner Up - Tasks Summary (OPTIMIZED) -->
                <div class="flex flex-row justify-between items-center w-full">
                    <x-labelwithvalue label="Tasks Summary" count="" />
                    @php
                        $timeOptions = ['All', 'Today', 'Yesterday', 'Last 7 days', 'Last 30 days'];
                    @endphp

                    <x-dropdown :options="$timeOptions" :default="$period" id="dropdown-time" />
                </div>
                <div
                    class="w-full rounded-lg border border-dashed border-gray-400 dark:border-gray-700 overflow-hidden flex-shrink-0">
                    <div class="w-full aspect-square max-h-[450px] p-4">
                        <x-radialchart :chart-data="$tasksSummary" chart-id="task-chart" title="Last 7 days" :labels="[
        'done' => 'Done',
        'inProgress' => 'In Progress',
        'toDo' => 'To Do'
    ]" :colors="[
        'done' => '#2A6DFA',
        'inProgress' => '#2AC9FA',
        'toDo' => '#0028B3'
    ]" />
                    </div>
                </div>

                <!-- Log Your Attendance Card -->
                <div id="attendance-card"
                    class="snap-start shrink-0 w-full relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 py-6 flex-shrink-0">
                    <!-- Background Image for Light Mode -->
                    <div class="absolute inset-0 bg-cover bg-center block dark:hidden"
                        style="background-image: url('{{ asset('images/backgrounds/log-attendance-bg.svg') }}');">
                    </div>

                    <!-- Background Image for Dark Mode -->
                    <div class="absolute inset-0 bg-cover bg-center hidden dark:block"
                        style="background-image: url('{{ asset('images/backgrounds/log-attendance-bg-dark.svg') }}');">
                    </div>

                    <!-- Content -->
                    <div class="relative p-6 h-full">
                        <div class="flex flex-col lg:flex-col items-center lg:items-start">
                            <!-- Text Content -->
                            <div class="flex flex-row w-full">
                                <h3 class="text-xl lg:text-xl font-black text-gray-900 dark:text-white mb-2 mt-3">
                                    Already Logged<br>Your Attendance?
                                </h3>
                            </div>

                            <div class="mb-2">
                                @if($hasAttendanceToday)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 my-4">
                                        <span class="font-bold text-gray-900 dark:text-white text-sm">
                                            Today's attendance</span><br>
                                        successfully recorded
                                    </p>
                                    <!-- View Details Button -->
                                    <button @click="openAttendanceDrawer()"
                                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center gap-2 mt-6 mb-3">
                                        <i class="fi fi-rr-eye text-sm"></i>
                                        View Details
                                    </button>
                                @else
                                    <p class="text-sm text-gray-600 dark:text-gray-400 my-6">
                                        <span class="font-bold text-gray-900 dark:text-white text-sm">
                                            No attendance</span><br>
                                        recorded yet
                                    </p>
                                    <!-- Log Attendance Button -->
                                    <button @click="openAttendanceDrawer()"
                                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center gap-2 shadow-sm">
                                        <i class="fi fi-rr-clock text-sm"></i>
                                        Log Attendance
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Inner Up - Recent Requests (OPTIMIZED) -->
                <div class="flex flex-row justify-between items-center w-full">
                    <div class="flex flex-row items-center w-full justify-between">
                        <x-labelwithvalue label="Recent Requests" count="" />
                        <a href="{{ route('employee.requests.create') }}"
                            class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                            New Request
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="w-full rounded-lg overflow-hidden flex-shrink-0">
                    <div class="space-y-4">
                        @if(count($employeeRequests) > 0)
                            @foreach($employeeRequests as $index => $request)
                                <div @click="openRequestModal({{ $index }})">
                                    <x-employee-components.request-list-item
                                        :type="$request['type']"
                                        :date="$request['date']"
                                        :fromTime="$request['from_time'] ?? $request['time_range']"
                                        :toTime="$request['to_time'] ?? ''"
                                        :status="$request['status']"
                                        :reason="$request['reason']" />
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-clipboard-list text-3xl mb-3 opacity-50"></i>
                                <p class="text-sm">No requests yet</p>
                            </div>
                        @endif
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
                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
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
                                        <span class="px-3 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">
                                            Clocked In
                                        </span>
                                    @elseif($hasAttendanceToday)
                                        <span class="px-3 py-1 text-xs rounded-full bg-[#00BFFF20] text-[#00BFFF] font-semibold">
                                            Completed
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs rounded-full bg-[#FFA50020] text-[#FFA500] font-semibold">
                                            Not Logged
                                        </span>
                                    @endif
                                </div>

                                <!-- Attendance Information Section -->
                                <div class="mb-5">
                                    <div class="py-3">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Attendance Details</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">View your attendance time details for today</p>
                                    </div>

                                    <div class="space-y-4 text-sm py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500 dark:text-gray-400">Employee Name</span>
                                            <span class="font-medium text-gray-900 dark:text-white text-right">
                                                {{ $employee->user->name ?? 'N/A' }}
                                            </span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500 dark:text-gray-400">Date</span>
                                            <span class="font-medium text-gray-900 dark:text-white text-right">
                                                {{ \Carbon\Carbon::today()->format('M d, Y') }}
                                            </span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500 dark:text-gray-400">Clock In Time</span>
                                            <span class="font-medium text-gray-900 dark:text-white text-right">
                                                @if($hasAttendanceToday)
                                                    {{ \Carbon\Carbon::parse(\App\Models\Attendance::where('employee_id', $employee->id)->whereDate('clock_in', \Carbon\Carbon::today())->first()->clock_in)->format('h:i A') }}
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500">Not clocked in</span>
                                                @endif
                                            </span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500 dark:text-gray-400">Clock Out Time</span>
                                            <span class="font-medium text-gray-900 dark:text-white text-right">
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
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Total Hours</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right">
                                                    {{ $duration->h }}h {{ $duration->i }}m
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Notice -->
                                <div class="rounded-lg p-4 my-6 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-center"
                                        :class="{
                                            'text-green-500 dark:text-green-400': {{ $hasAttendanceToday && !$isClockedIn ? 'true' : 'false' }},
                                            'text-blue-500 dark:text-blue-400': {{ $isClockedIn ? 'true' : 'false' }},
                                            'text-orange-400 dark:text-orange-500': {{ !$hasAttendanceToday ? 'true' : 'false' }}
                                        }">
                                        @if($hasAttendanceToday && !$isClockedIn)
                                            <span><i class="fa-solid fa-circle-check mr-2"></i>Your attendance has been <span class="font-semibold">completed</span> for today</span>
                                        @elseif($isClockedIn)
                                            <span><i class="fa-solid fa-spinner fa-spin mr-2"></i>You are currently <span class="font-semibold">clocked in</span></span>
                                        @else
                                            <span><i class="fa-solid fa-clock mr-2"></i>You have <span class="font-semibold">not logged</span> your attendance yet</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Drawer Footer (Sticky) -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                                <div class="flex gap-3">
                                    @if(!$hasAttendanceToday)
                                        <form action="{{ route('employee.attendance.clockin') }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                class="w-full text-sm px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium flex items-center justify-center gap-2">
                                                <i class="fi fi-rr-play text-sm"></i>
                                                Clock In
                                            </button>
                                        </form>
                                    @elseif($isClockedIn)
                                        <form action="{{ route('employee.attendance.clockout') }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                class="w-full text-sm px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium flex items-center justify-center gap-2">
                                                <i class="fi fi-rr-stop text-sm"></i>
                                                Clock Out
                                            </button>
                                        </form>
                                    @endif
                                    <button
                                        @click="closeAttendanceDrawer()"
                                        class="flex-1 text-sm px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors font-medium">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': selectedRequest?.status === 'Pending',
                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': selectedRequest?.status === 'Approved',
                                        'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': selectedRequest?.status === 'Rejected'
                                    }"
                                    x-text="selectedRequest?.status"></span>
                            </div>
                        </div>

                        <!-- Request Information -->
                        <div class="space-y-4 text-sm py-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Request Type</span>
                                <span class="font-medium text-gray-900 dark:text-white" x-text="selectedRequest?.type"></span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Date</span>
                                <span class="font-medium text-gray-900 dark:text-white" x-text="selectedRequest?.date"></span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Time Range</span>
                                <span class="font-medium text-gray-900 dark:text-white" x-text="selectedRequest?.time_range"></span>
                            </div>

                            <template x-if="selectedRequest?.from_time && selectedRequest?.to_time">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 dark:text-gray-400">Custom Hours</span>
                                    <span class="font-medium text-gray-900 dark:text-white" x-text="selectedRequest?.from_time + ' - ' + selectedRequest?.to_time"></span>
                                </div>
                            </template>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Reason</span>
                                <span class="font-medium text-gray-900 dark:text-white" x-text="selectedRequest?.reason"></span>
                            </div>

                            <template x-if="selectedRequest?.description">
                                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-500 dark:text-gray-400 block mb-2">Description</span>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg" x-text="selectedRequest?.description"></p>
                                </div>
                            </template>

                            <template x-if="selectedRequest?.proof_document">
                                <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-500 dark:text-gray-400">Proof Document</span>
                                    <a :href="'/storage/' + selectedRequest?.proof_document" target="_blank"
                                        class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                        View Document
                                    </a>
                                </div>
                            </template>

                            <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400">Submitted On</span>
                                <span class="font-medium text-gray-900 dark:text-white text-sm" x-text="selectedRequest?.created_at"></span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 space-y-3">
                            <!-- Cancel Button (only for pending requests) -->
                            <template x-if="selectedRequest?.status === 'Pending'">
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

@push('scripts')
    <script>
        // This script handles the Tasks Summary filter dropdown.
        document.addEventListener('DOMContentLoaded', function () {
            // Find the button that triggers the dropdown. We assume it has a 'data-dropdown-toggle' attribute.
            const dropdownButton = document.querySelector('[data-dropdown-toggle="dropdown-time"]');
            const dropdownMenu = document.getElementById('dropdown-time');

            if (dropdownButton && dropdownMenu) {
                // Listen for clicks on the entire dropdown menu.
                dropdownMenu.addEventListener('click', function (event) {

                    // Find the specific item that was clicked (could be a link or any other element).
                    const target = event.target.closest('a, button, li'); // Make it flexible

                    if (target) {
                        // Get the text content of the clicked item.
                        const selectedPeriod = target.textContent.trim();

                        if (selectedPeriod) {
                            const currentUrl = new URL(window.location.href);
                            currentUrl.searchParams.set('period', selectedPeriod);
                            window.location.href = currentUrl.toString();
                        }
                    }
                });
            }
        });
    </script>
@endpush