<x-layouts.general-employee :title="'Employee Dashboard'">

    {{-- MOBILE LAYOUT (< 1024px) - Hidden on large screens --}} <div class="lg:hidden">
        @include('employee.mobile.dashboard')
        </div>

        {{-- DESKTOP LAYOUT (≥ 1024px) - Hidden on small screens --}}
        <section role="status" class="hidden lg:flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]"
            x-data="{
            showAttendanceModal: false,

            openAttendanceModal() {
                this.showAttendanceModal = true;
                document.body.style.overflow = 'hidden';
            },

            closeAttendanceModal() {
                this.showAttendanceModal = false;
                document.body.style.overflow = 'auto';
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
                <div class="w-full rounded-lg h-48 sm:h-56 md:h-auto">
                    <x-labelwithvalue label="Your To-Do List" count="({{ $todoList->count() }})" />
                    <div class="h-48 overflow-y-auto">

                        @php
                            // Transform tasks to the format expected by task-overview-list component
                            $tasks = $todoList->map(function ($task) {
                                return [
                                    'service' => $task->task_description,
                                    'status' => $task->status,
                                    'service_date' => \Carbon\Carbon::parse($task->date)->format('M d, Y'),
                                    'service_time' => $task->duration . ' min',
                                    'description' => 'Client: ' . $task->client_name . ($task->cabin_name ? ' • Location: ' . $task->cabin_name : ''),
                                ];
                            })->toArray();
                        @endphp

                        <x-employee-components.task-overview-list :items="$tasks" fixedHeight="20rem" maxHeight="30rem"
                            emptyTitle="No tasks assigned yet"
                            emptyMessage="You don't have any tasks at the moment. New tasks will appear here once assigned." />
                    </div>
                </div>
            </div>

            <!-- Right Panel - Tasks Details -->
            <div
                class="flex flex-col gap-6 w-full lg:w-1/3 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
                <div class="flex flex-row justify-between w-full">
                    <x-labelwithvalue label="Tasks Summary" count="" />
                    @php
                        $timeOptions = ['All', 'Today', 'Yesterday', 'Last 7 days', 'Last 30 days'];
                    @endphp

                    <x-dropdown :options="$timeOptions" :default="$period" id="dropdown-time" />

                </div>

                <!-- Inner Up - Tasks Summary -->
                <div class="w-full rounded-lg h-64 sm:h-1/2 md:h-1/2">

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

                <!-- Log Your Attendance Card - NEW -->
                <div id="attendance-card"
                    class="snap-start shrink-0 w-full relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 py-6">
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
                                    <button @click="openAttendanceModal()"
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
                                    <button @click="openAttendanceModal()"
                                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center gap-2 shadow-sm">
                                        <i class="fi fi-rr-clock text-sm"></i>
                                        Log Attendance
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Details Modal -->
            <div x-show="showAttendanceModal" x-cloak @click="closeAttendanceModal()"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 dark:bg-black/80 p-4 sm:p-8"
                style="display: none;">
                <div @click.stop
                    class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-3xl w-full sm:w-2/5 max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-700"
                    x-show="showAttendanceModal" x-transition>

                    <!-- Close button -->
                    <button type="button" @click="closeAttendanceModal()"
                        class="absolute top-4 right-4 sm:top-5 sm:right-5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-800 rounded-lg p-1 z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Modal Body -->
                    <div class="p-6 sm:p-8">
                        <!-- Header -->
                        <div class="py-6">
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white text-center mb-3">
                                Today's Attendance
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 text-center w-full">View your attendance time details
                                for today</p>

                            <!-- Status Badge - Centered -->
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                @if($isClockedIn)
                                    <span
                                        class="px-3 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">Clocked
                                        In</span>
                                @elseif($hasAttendanceToday)
                                    <span
                                        class="px-3 py-1 text-xs rounded-full bg-[#00BFFF20] text-[#00BFFF] font-semibold">Completed</span>
                                @else
                                    <span
                                        class="px-3 py-1 text-xs rounded-full bg-[#FFA50020] text-[#FFA500] font-semibold">Not
                                        Logged</span>
                                @endif
                            </div>
                        </div>

                        <!-- Attendance Information Section -->
                        <div class="mb-5">

                            <div class="space-y-4 text-sm py-2.5 px-3">
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

                        <!-- Action Buttons -->
                        <div class="mt-8 flex flex-col sm:flex-row gap-3">
                            @if(!$hasAttendanceToday)
                                <form action="{{ route('employee.attendance.clockin') }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                        <i class="fi fi-rr-play text-sm"></i>
                                        Clock In
                                    </button>
                                </form>
                            @elseif($isClockedIn)
                                <form action="{{ route('employee.attendance.clockout') }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                        <i class="fi fi-rr-stop text-sm"></i>
                                        Clock Out
                                    </button>
                                </form>
                            @endif

                            <button @click="closeAttendanceModal()"
                                class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white text-sm font-medium rounded-lg transition-colors duration-200">
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