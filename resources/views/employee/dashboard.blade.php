<x-layouts.general-employee :title="'Employee Dashboard'">
    <x-skeleton-page :preset="'employee-dashboard'">

        {{-- MOBILE LAYOUT (< 768px) - Hidden on medium+ screens --}} <div class="md:hidden">
            @include('employee.mobile.dashboard')
        </div>

        {{-- DESKTOP LAYOUT (≥ 768px) - Hidden on small screens --}}
        <section role="status"
            class="hidden md:flex flex-col lg:flex-row gap-6 p-4 md:p-6"
            x-init="setInterval(() => currentTime = new Date(), 30000)"
            x-data="{
                showAttendanceDrawer: false,
                showRequestModal: false,
                selectedRequest: null,
                isCancelling: false,
                employeeRequests: {{ Js::from($employeeRequests) }},
                isOnBreak: {{ $activeBreakType ? 'true' : 'false' }},
                activeBreakType: '{{ $activeBreakType ?? '' }}',
                lunchBreakStatus: '{{ $lunchBreakStatus ?? '' }}',
                dinnerBreakStatus: '{{ $dinnerBreakStatus ?? '' }}',
                isProcessingBreak: false,
                currentTime: new Date(),

                currentBreakWindow() {
                    const mins = this.currentTime.getHours() * 60 + this.currentTime.getMinutes();
                    if (mins >= 720 && mins < 780) return 'lunch';
                    if (mins >= 1080 && mins < 1140) return 'dinner';
                    return null;
                },

                canStartBreak() {
                    if (this.isOnBreak) return false;
                    const w = this.currentBreakWindow();
                    if (!w) return false;
                    if (w === 'lunch' && this.lunchBreakStatus) return false;
                    if (w === 'dinner' && this.dinnerBreakStatus) return false;
                    return true;
                },

                breakButtonHint() {
                    if (this.isOnBreak) return '';
                    if (this.lunchBreakStatus && this.dinnerBreakStatus) return 'Both breaks already taken';
                    const w = this.currentBreakWindow();
                    if (!w) return 'Available 12:00–13:00 (lunch) or 18:00–19:00 (dinner)';
                    if (w === 'lunch' && this.lunchBreakStatus) return 'Lunch break already taken';
                    if (w === 'dinner' && this.dinnerBreakStatus) return 'Dinner break already taken';
                    return '';
                },

                async toggleBreak() {
                    if (this.isProcessingBreak) return;
                    this.isProcessingBreak = true;
                    const url = this.isOnBreak
                        ? '{{ route('employee.attendance.break.end') }}'
                        : '{{ route('employee.attendance.break.start') }}';
                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            window.location.reload();
                        } else {
                            window.showErrorDialog('Break Action Failed', data.message || 'Unable to update break');
                            this.isProcessingBreak = false;
                        }
                    } catch (e) {
                        window.showErrorDialog('Break Action Failed', 'An error occurred. Please try again.');
                        this.isProcessingBreak = false;
                    }
                },
            
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
                            window.showErrorDialog('Request Failed', data.message || 'Failed to cancel request');
                        }
                    } catch (error) {
                        window.showErrorDialog('Request Failed', 'An error occurred. Please try again.');
                    } finally {
                        this.isCancelling = false;
                    }
                }
            }">
            <!-- Left Panel - Dashboard Content -->
            <div class="flex flex-col gap-6 flex-1 w-full pt-12 px-3">

                {{-- Gmail Account Linking Prompt (via Sonner toast) --}}

                <!-- Inner Up - Dashboard Header -->
                    <div id="tour-emp-welcome">
                        <x-herocard :headerName="is_array($employee) ? ($employee['user']['name'] ?? 'Employee') : ($employee->user->name ?? 'Employee')" :headerDesc="'Welcome to the employee dashboard. Track tasks and manage them'" :headerIcon="'hero-employee'" />
                    </div>
                <!-- Inner Middle - Calendar -->
                <div class="mt-3">
                    <x-labelwithvalue label="My Calendar" count="" />
                </div>
                    <div id="tour-emp-calendar"
                        class="w-full rounded-lg bg-white/30 backdrop-blur-md border border-white/40 shadow-sm dark:bg-gray-800/40 dark:border-transparent dark:backdrop-blur-none">
                        <x-calendar :holidays="$holidays" calendar-id="desktop" />
                    </div>

                <!-- Inner Down - Tasks Particulars -->
                <div id="tour-emp-tasks" class="w-full flex-1 flex flex-col">
                    <div class="flex items-center justify-between gap-2">
                        <x-labelwithvalue label="Your To-Do List" count="({{ $todoList->count() }})" />
                        <span id="todo-list-date-label" class="hidden text-xs font-medium text-blue-600 dark:text-blue-400"></span>
                    </div>
                    <div id="todo-list-section" class="todo-list-container rounded-lg my-6 bg-white shadow-sm dark:bg-gray-800/40 dark:border-transparent flex-1">
                        @php
                            // Transform tasks to the format expected by task-overview-list component
                            $tasks = $todoList
                                ->map(function ($task) {
                                    // ✅ STAGE 2: prefer GA-computed start–end window over raw duration
                                    if ($task->optimized_start_minutes !== null && $task->optimized_end_minutes !== null) {
                                        $startLabel = sprintf('%d:%02d %s',
                                            ((int) intdiv($task->optimized_start_minutes, 60) + 11) % 12 + 1,
                                            $task->optimized_start_minutes % 60,
                                            intdiv($task->optimized_start_minutes, 60) >= 12 ? 'PM' : 'AM'
                                        );
                                        $endLabel = sprintf('%d:%02d %s',
                                            ((int) intdiv($task->optimized_end_minutes, 60) + 11) % 12 + 1,
                                            $task->optimized_end_minutes % 60,
                                            intdiv($task->optimized_end_minutes, 60) >= 12 ? 'PM' : 'AM'
                                        );
                                        $serviceTime = $startLabel . ' – ' . $endLabel . ' (' . $task->duration . ' min)';
                                    } else {
                                        $serviceTime = $task->duration . ' min';
                                    }
                                    return [
                                        'service' => $task->task_description,
                                        'status' => $task->status,
                                        'service_date' => \Carbon\Carbon::parse($task->date)->format('M d, Y'),
                                        'service_time' => $serviceTime,
                                        'description' =>
                                            'Client: ' .
                                            $task->client_name .
                                            ($task->cabin_name ? ' • Location: ' . $task->cabin_name : ''),
                                        'action_url' => route('employee.tasks.show', [
                                            'task' => $task->id,
                                            'from' => 'dashboard',
                                        ]),
                                        'action_label' => 'View Details',
                                    ];
                                })
                                ->toArray();
                        @endphp

                        <x-employee-components.task-overview-list :items="$tasks" fixedHeight="auto"
                            maxHeight="100%" bgClass="bg-transparent"
                            emptyTitle="No tasks assigned yet"
                            emptyMessage="You don't have any tasks at the moment. New tasks will appear here once assigned." />
                    </div>
                </div>

                @push('scripts')
                <script>
                (function () {
                    const section = document.getElementById('todo-list-section');
                    const dateLabel = document.getElementById('todo-list-date-label');
                    if (!section) return;

                    const endpoint = "{{ route('employee.dashboard.tasks-by-date') }}";
                    const csrf = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
                    let inflight = null;

                    document.addEventListener('calendar-date-selected', async function (e) {
                        const date = e.detail && e.detail.date;
                        if (!date) return;

                        // Cancel any in-flight request to keep the latest click authoritative
                        if (inflight) inflight.abort();
                        const controller = new AbortController();
                        inflight = controller;

                        section.classList.add('opacity-50');

                        try {
                            const res = await fetch(endpoint + '?date=' + encodeURIComponent(date), {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrf
                                },
                                signal: controller.signal,
                                credentials: 'same-origin'
                            });
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            const data = await res.json();
                            section.innerHTML = data.html || '';
                            if (dateLabel) {
                                dateLabel.textContent = 'Showing: ' + (data.label || date) + ' (' + (data.count || 0) + ')';
                                dateLabel.classList.remove('hidden');
                            }
                        } catch (err) {
                            if (err.name !== 'AbortError') {
                                console.error('Failed to load tasks for date', date, err);
                            }
                        } finally {
                            section.classList.remove('opacity-50');
                            if (inflight === controller) inflight = null;
                        }
                    });
                })();
                </script>
                @endpush
            </div>

            <!-- Right Panel - Tasks Details -->
            <div id="tour-emp-right-panel"
                class="flex flex-col gap-3 w-full lg:w-1/3 mt-8 px-4">

                <!-- Log Your Attendance Card -->
                <div id="attendance-card"
                    class="snap-start shrink-0 w-full relative overflow-hidden rounded-xl py-4 bg-white shadow-sm dark:bg-gray-800/40">
                    <!-- Background Image for Light Mode -->
                    <div class="absolute inset-0 bg-cover bg-center block dark:hidden" style="background-image: url('{{ asset('images/backgrounds/log-attendance-bg.svg') }}');"></div>

                    <!-- Background Image for Dark Mode -->
                    <div class="absolute inset-0 bg-cover bg-center hidden dark:block" style="background-image: url('{{ asset('images/backgrounds/log-attendance-bg-dark.svg') }}');"></div>

                    <!-- Content -->
                    <div class="relative px-6 py-2 h-full">
                        <div class="flex flex-col items-start my-3">
                            <!-- Text Content -->
                            <div class="flex flex-row w-full">
                                <h3 class="text-lg md:text-xl font-black text-gray-900 dark:text-white">
                                    Already Logged<br>Your Attendance?
                                </h3>
                            </div>

                            <div class="mb-2">
                                @if ($hasAttendanceToday)
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

                <!-- Course Progress List -->
                <div id="course-progress-section" class="flex flex-col gap-6">
                    <div class="flex flex-row justify-between items-center w-full">
                        <x-labelwithvalue label="Course Progress" count="({{ count($watchedLessons) }})" />
                    </div>

                    @php
                        $sortedCourses = collect($watchedLessons)->sort(function ($a, $b) {
                            $orderA = ($a['progress'] > 0 && $a['progress'] < 100) ? 0 : ($a['progress'] == 0 ? 1 : 2);
                            $orderB = ($b['progress'] > 0 && $b['progress'] < 100) ? 0 : ($b['progress'] == 0 ? 1 : 2);
                            return $orderA <=> $orderB;
                        })->values();
                        $courseVisibleCount = 2;
                        $courseTotal = $sortedCourses->count();
                        $courseHasOverflow = $courseTotal > $courseVisibleCount;
                        $courseHiddenCount = $courseTotal - $courseVisibleCount;
                    @endphp

                    @if($courseTotal === 0)
                        <div class="flex flex-col items-center justify-center py-16 px-6 text-center bg-white dark:bg-gray-800/30 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-800">
                            <div class="w-32 h-32 mb-4 flex items-center justify-center">
                                <img src="{{ asset('images/icons/no-items-found.svg') }}"
                                     alt="No courses"
                                     class="w-full h-full object-contain opacity-80 dark:opacity-60">
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">No courses yet</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md">
                                Start a course in the <a href="{{ route('employee.development') }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Development</a> section.
                            </p>
                        </div>
                    @else
                        <div x-data="{ expanded: false }">
                            {{-- Collapsed: show first 2 with stacked peek --}}
                            <div x-show="!expanded" class="relative">
                                <div class="space-y-3">
                                    @foreach($sortedCourses->take($courseVisibleCount) as $lesson)
                                        <x-employee-components.course-progress-card
                                            :title="$lesson['title']"
                                            :progress="$lesson['progress']"
                                            :duration="$lesson['duration']"
                                            buttonUrl="{{ route('employee.development') }}?course={{ $lesson['course_id'] }}" />
                                    @endforeach
                                </div>

                                @if($courseHasOverflow)
                                    <div class="relative mt-2 cursor-pointer" @click="expanded = true">
                                        <div class="mx-3 h-2 bg-white dark:bg-gray-700/30 border border-gray-300/50 dark:border-gray-700/50 rounded-b-lg"></div>
                                        <div class="mx-6 h-1.5 bg-white dark:bg-gray-700/20 border border-gray-200/50 dark:border-gray-700/40 rounded-b-lg"></div>
                                        <button type="button"
                                            class="w-full mt-2 text-center text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors py-1">
                                            Show all ({{ $courseHiddenCount }} more)
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Expanded: show all scrollable --}}
                            <div x-show="expanded" x-cloak>
                                <div class="overflow-y-auto space-y-3 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]"
                                     style="max-height: 20rem;">
                                    @foreach($sortedCourses as $lesson)
                                        <x-employee-components.course-progress-card
                                            :title="$lesson['title']"
                                            :progress="$lesson['progress']"
                                            :duration="$lesson['duration']"
                                            buttonUrl="{{ route('employee.development') }}?course={{ $lesson['course_id'] }}" />
                                    @endforeach
                                </div>

                                <button type="button" @click="expanded = false"
                                    class="w-full mt-2 text-center text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors py-1">
                                    Show less
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Tasks Summary - Radial Chart (hidden on small screens) -->
                <div class="hidden md:block w-full rounded-lg overflow-hidden flex-shrink-0 mt-3 bg-white dark:bg-transparent p-2">
                    <div class="w-full aspect-square max-h-[300px] md:max-h-[340px] lg:max-h-[385px]">
                        <x-radialchart :chart-data="$tasksSummary" chart-id="task-chart" title="Last 7 days" :labels="[
                            'done' => 'Done',
                            'inProgress' => 'In Progress',
                            'toDo' => 'To Do',
                        ]"
                            :colors="[
                                'done' => '#2A6DFA',
                                'inProgress' => '#2AC9FA',
                                'toDo' => '#0028B3',
                            ]" />
                    </div>
                    <a href="{{ route('employee.tasks') }}"
                        class="w-full text-center text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors py-1 block">
                        View All Tasks
                    </a>
                </div>
            </div>

            <!-- Full Width - Current Lessons -->
            {{-- <div id="tour-emp-lessons" class="w-full rounded-lg md:h-auto flex flex-col gap-4 px-4">
                <div class="flex flex-row justify-between items-center w-full">
                    <x-labelwithvalue label="Current Lessons" count="" />
                    @php
                        $timeOptions = ['All', 'Today', 'Yesterday', 'Last 7 days', 'Last 30 days'];
                    @endphp

                    <x-dropdown :options="$timeOptions" :default="$period" id="dropdown-time" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 rounded-lg">
                    @forelse($watchedLessons as $lesson)
                        <x-employee-components.lesson-card :duration="$lesson['duration']" :title="$lesson['title']" :description="$lesson['description']"
                            :progress="$lesson['progress']" :buttonText="$lesson['progress'] >= 100
                                ? 'Review'
                                : ($lesson['progress'] > 0
                                    ? 'Continue'
                                    : 'Check now')"
                            buttonUrl="{{ route('employee.development') }}?course={{ $lesson['course_id'] }}" />
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">
                            <i class="fa-regular fa-circle-play text-4xl mb-3"></i>
                            <p class="text-sm">No lessons watched yet. Start learning in the
                                <a href="{{ route('employee.development') }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Development</a>
                                section.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div> --}}



            <!-- Attendance Details Slide-in Drawer -->
            <div x-show="showAttendanceDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden"
                style="display: none;">
                <!-- Backdrop -->
                <div x-show="showAttendanceDrawer" x-transition:enter="transition-opacity ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="closeAttendanceDrawer()"
                    class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

                <!-- Drawer Panel -->
                <div class="fixed inset-y-0 right-0 flex max-w-full">
                    <div x-show="showAttendanceDrawer"
                        x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-200"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" @click.stop
                        class="relative w-screen max-w-sm">

                        <!-- Drawer Content -->
                        <div
                            class="h-full flex flex-col bg-white dark:bg-slate-800 shadow-2xl border-l border-gray-200 dark:border-slate-700">
                            <!-- Drawer Header -->
                            <div
                                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-slate-800/50">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Today's Attendance</h2>
                                <button type="button" @click="closeAttendanceDrawer()"
                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Drawer Body (Scrollable) -->
                            <div class="flex-1 overflow-y-auto p-6">
                                <!-- Status Badge -->
                                <div class="flex items-center gap-2 mb-6">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                    @if ($isClockedIn)
                                        <span
                                            class="px-3 py-1 text-xs rounded-full bg-[#2FBC0020] text-[#2FBC00] font-semibold">
                                            Clocked In
                                        </span>
                                    @elseif($hasAttendanceToday)
                                        <span
                                            class="px-3 py-1 text-xs rounded-full bg-[#00BFFF20] text-[#00BFFF] font-semibold">
                                            Completed
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-xs rounded-full bg-[#FFA50020] text-[#FFA500] font-semibold">
                                            Not Logged
                                        </span>
                                    @endif
                                </div>

                                <!-- Attendance Information Section -->
                                <div class="mb-5">
                                    <div class="py-3">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Attendance
                                            Details</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">View your attendance
                                            time
                                            details for today</p>
                                    </div>

                                    <div
                                        class="space-y-4 text-sm py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500 dark:text-gray-400">Employee Name</span>
                                            <span class="font-medium text-gray-900 dark:text-white text-right">
                                                {{ is_array($employee) ? ($employee['user']['name'] ?? 'N/A') : ($employee->user->name ?? 'N/A') }}
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
                                                @php $attendance = \App\Models\Attendance::where('employee_id', is_array($employee) ? $employee['id'] : $employee->id)->whereDate('clock_in', \Carbon\Carbon::today())->first(); @endphp
                                                @if ($hasAttendanceToday && $attendance && $attendance->clock_in)
                                                    {{ \Carbon\Carbon::parse($attendance->clock_in)->format('h:i A') }}
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500">Not clocked
                                                        in</span>
                                                @endif
                                            </span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500 dark:text-gray-400">Clock Out Time</span>
                                            <span class="font-medium text-gray-900 dark:text-white text-right">
                                                @if ($hasAttendanceToday && $attendance && !$attendance->clock_out)
                                                    <span class="text-blue-500 dark:text-blue-400">Still
                                                        working...</span>
                                                @elseif($hasAttendanceToday && $attendance && $attendance->clock_out)
                                                    {{ \Carbon\Carbon::parse($attendance->clock_out)->format('h:i A') }}
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                                @endif
                                            </span>
                                        </div>

                                        @if ($hasAttendanceToday && $attendance && $attendance->clock_out)
                                            @php
                                                $clockIn = \Carbon\Carbon::parse($attendance->clock_in);
                                                $clockOut = \Carbon\Carbon::parse($attendance->clock_out);
                                                $elapsedMinutes = $clockOut->diffInMinutes($clockIn);
                                                $breakMinutes = ($todayAttendance ? $todayAttendance->totalBreakMinutes() : 0);
                                                $netMinutes = max(0, $elapsedMinutes - $breakMinutes);
                                                $payableMinutes = min(\App\Models\Attendance::MAX_PAYABLE_MINUTES, $netMinutes);
                                            @endphp
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Total Hours</span>
                                                <span class="font-medium text-gray-900 dark:text-white text-right">
                                                    {{ intdiv($payableMinutes, 60) }}h {{ $payableMinutes % 60 }}m
                                                    @if ($netMinutes > \App\Models\Attendance::MAX_PAYABLE_MINUTES)
                                                        <span class="text-xs text-orange-500 dark:text-orange-400 block">capped at 12h</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Breaks Information Section -->
                                @if ($hasAttendanceToday && $todayAttendance)
                                    <div class="mb-5">
                                        <div class="py-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Breaks</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Lunch (12:00–13:00) and dinner (18:00–19:00)</p>
                                        </div>

                                        <div class="space-y-4 text-sm py-2.5 px-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                            @foreach ([['type' => 'lunch', 'label' => 'Lunch'], ['type' => 'dinner', 'label' => 'Dinner']] as $b)
                                                @php
                                                    $bStart = $todayAttendance->{$b['type'] . '_break_start'};
                                                    $bEnd = $todayAttendance->{$b['type'] . '_break_end'};
                                                    $bStatus = $todayAttendance->{$b['type'] . '_break_status'};
                                                    $bMinutes = $todayAttendance->breakMinutesFor($b['type']);
                                                @endphp
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-500 dark:text-gray-400">{{ $b['label'] }}</span>
                                                    <span class="font-medium text-gray-900 dark:text-white text-right text-xs">
                                                        @if ($bStatus === \App\Models\Attendance::STATUS_IN_PROGRESS)
                                                            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 font-semibold">On break since {{ \Carbon\Carbon::parse($bStart)->format('h:i A') }}</span>
                                                        @elseif ($bStatus === \App\Models\Attendance::STATUS_ENDED || $bStatus === \App\Models\Attendance::STATUS_AUTO_ENDED)
                                                            {{ \Carbon\Carbon::parse($bStart)->format('h:i A') }} – {{ \Carbon\Carbon::parse($bEnd)->format('h:i A') }}
                                                            <span class="block">{{ $bMinutes }} min
                                                                @if ($bStatus === \App\Models\Attendance::STATUS_AUTO_ENDED)
                                                                    <span class="px-1.5 py-0.5 ml-1 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 text-[10px] font-semibold">Auto-ended</span>
                                                                @endif
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400 dark:text-gray-500">Not taken</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Status Notice -->
                                <div class="rounded-lg p-4 my-6 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-center"
                                        :class="{
                                            'text-green-500 dark:text-green-400': {{ $hasAttendanceToday && !$isClockedIn ? 'true' : 'false' }},
                                            'text-blue-500 dark:text-blue-400': {{ $isClockedIn ? 'true' : 'false' }},
                                            'text-orange-400 dark:text-orange-500': {{ !$hasAttendanceToday ? 'true' : 'false' }}
                                        }">
                                        @if ($hasAttendanceToday && !$isClockedIn)
                                            <span><i class="fa-solid fa-circle-check mr-2"></i>Your attendance has been
                                                <span class="font-semibold">completed</span> for today</span>
                                        @elseif($isClockedIn)
                                            <span><i class="fa-solid fa-spinner fa-spin mr-2"></i>You are currently
                                                <span class="font-semibold">clocked in</span></span>
                                        @else
                                            <span><i class="fa-solid fa-clock mr-2"></i>You have <span
                                                    class="font-semibold">not logged</span> your attendance yet</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Drawer Footer (Sticky) -->
                            <div
                                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                                @if ($isClockedIn)
                                    <div class="mb-3" x-show="isOnBreak || canStartBreak() || breakButtonHint()">
                                        <button type="button" @click="toggleBreak()"
                                            :disabled="isProcessingBreak || (!isOnBreak && !canStartBreak())"
                                            :class="isOnBreak ? 'bg-amber-600 hover:bg-amber-700 text-white' : (canStartBreak() ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed')"
                                            class="w-full text-sm px-4 py-2.5 rounded-lg transition-colors font-medium flex items-center justify-center gap-2">
                                            <i :class="isOnBreak ? 'fi fi-rr-stop' : 'fi fi-rr-mug-hot'" class="text-sm"></i>
                                            <span x-text="isOnBreak ? ('End ' + (activeBreakType.charAt(0).toUpperCase() + activeBreakType.slice(1)) + ' Break') : 'Start Break'"></span>
                                        </button>
                                        <p x-show="!isOnBreak && breakButtonHint()" x-text="breakButtonHint()"
                                            class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1.5"></p>
                                    </div>
                                @endif
                                <div class="flex gap-3">
                                    @if (!$hasAttendanceToday)
                                        <form id="dashboard-clockin-form" action="{{ route('employee.attendance.clockin') }}" method="POST"
                                            class="flex-1" style="display:none;">
                                            @csrf
                                            <input type="hidden" name="latitude" class="geo-latitude">
                                            <input type="hidden" name="longitude" class="geo-longitude">
                                        </form>
                                        <button type="button" onclick="handleClockAction('dashboard-clockin-form', 'Clock In', 'Are you sure you want to clock in?', 'Clocked In', 'You have successfully clocked in.')"
                                            class="flex-1 text-sm px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium flex items-center justify-center gap-2">
                                            <i class="fi fi-rr-play text-sm"></i>
                                            Clock In
                                        </button>
                                    @elseif($isClockedIn)
                                        <form id="dashboard-clockout-form" action="{{ route('employee.attendance.clockout') }}" method="POST"
                                            class="flex-1" style="display:none;">
                                            @csrf
                                            <input type="hidden" name="latitude" class="geo-latitude">
                                            <input type="hidden" name="longitude" class="geo-longitude">
                                        </form>
                                        <button type="button" onclick="handleClockAction('dashboard-clockout-form', 'Clock Out', 'Are you sure you want to clock out?', 'Clocked Out', 'You have successfully clocked out.')"
                                            :disabled="isOnBreak"
                                            :class="isOnBreak ? 'bg-red-300 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'"
                                            class="flex-1 text-sm px-4 py-2.5 text-white rounded-lg transition-colors font-medium flex items-center justify-center gap-2">
                                            <i class="fi fi-rr-stop text-sm"></i>
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

            <!-- Request Details Slide-in Drawer -->
            <div x-show="showRequestModal" x-cloak class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
                <!-- Backdrop -->
                <div x-show="showRequestModal" x-transition:enter="transition-opacity ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    @click="closeRequestModal()" class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

                <!-- Drawer Panel -->
                <div class="fixed inset-y-0 right-0 flex max-w-full">
                    <div x-show="showRequestModal" x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-200"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" @click.stop
                        class="relative w-screen max-w-sm">

                        <!-- Drawer Content -->
                        <div
                            class="h-full flex flex-col bg-white dark:bg-slate-800 shadow-2xl border-l border-gray-200 dark:border-slate-700">
                            <!-- Drawer Header -->
                            <div
                                class="px-7 pt-8 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-slate-800/50">
                                <div class="flex flex-col w-full mb-5">
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request Details
                                    </h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        View your leave request details
                                    </p>
                                </div>
                                <button type="button" @click="closeRequestModal()"
                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200 focus:outline-none rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                            </div>

                            <!-- Drawer Body (Scrollable) -->
                            <div class="flex-1 overflow-y-auto p-6 pt-3" x-show="selectedRequest">

                                <!-- Request Information -->
                                <template x-if="selectedRequest">
                                    <div class="space-y-0 mb-6">
                                        <div
                                            class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                                            <span class="text-sm font-semibold px-2 py-1 rounded-full"
                                                :class="{
                                                    'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400': selectedRequest
                                                        .status === 'Pending',
                                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': selectedRequest
                                                        .status === 'Approved',
                                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': selectedRequest
                                                        .status === 'Rejected'
                                                }"
                                                x-text="selectedRequest.status"></span>
                                        </div>

                                        <div
                                            class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Request Type</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white"
                                                x-text="selectedRequest.type"></span>
                                        </div>

                                        <div
                                            class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Date</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white"
                                                x-text="selectedRequest.date"></span>
                                        </div>

                                        <div
                                            class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Time Range</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white"
                                                x-text="selectedRequest.time_range"></span>
                                        </div>

                                        <template x-if="selectedRequest.from_time && selectedRequest.to_time">
                                            <div
                                                class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Custom
                                                    Hours</span>
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white"
                                                    x-text="selectedRequest.from_time + ' - ' + selectedRequest.to_time"></span>
                                            </div>
                                        </template>

                                        <div class="py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span
                                                class="text-sm text-gray-500 dark:text-gray-400 block mb-2">Reason</span>
                                            <p class="text-sm text-gray-900 dark:text-white"
                                                x-text="selectedRequest.reason"></p>
                                        </div>

                                        <template x-if="selectedRequest.description">
                                            <div class="py-3 border-b border-gray-200 dark:border-gray-700">
                                                <span
                                                    class="text-sm text-gray-500 dark:text-gray-400 block mb-2">Description</span>
                                                <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg"
                                                    x-text="selectedRequest.description"></p>
                                            </div>
                                        </template>

                                        <template x-if="selectedRequest.proof_document">
                                            <div
                                                class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Proof
                                                    Document</span>
                                                <a :href="'/storage/' + selectedRequest.proof_document" target="_blank"
                                                    class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium max-w-[180px] truncate inline-block"
                                                    x-text="selectedRequest.proof_document.split('/').pop()">
                                                </a>
                                            </div>
                                        </template>

                                        <div class="flex justify-between items-center py-3">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Submitted</span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400"
                                                x-text="selectedRequest.created_at"></span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Status Messages -->
                                <template x-if="selectedRequest && selectedRequest.status === 'Approved'">
                                    <div
                                        class="flex items-center justify-center gap-2 py-3 px-4 mb-6 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                                        <span class="text-sm font-medium text-green-700 dark:text-green-400">This
                                            request
                                            has been approved</span>
                                    </div>
                                </template>

                                <template x-if="selectedRequest && selectedRequest.status === 'Rejected'">
                                    <div
                                        class="flex items-center justify-center gap-2 py-3 px-4 mb-6 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                        <i class="fa-solid fa-circle-xmark text-red-600 dark:text-red-400"></i>
                                        <span class="text-sm font-medium text-red-700 dark:text-red-400">This request
                                            has
                                            been declined</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Drawer Footer (Sticky) -->
                            <div
                                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                                <div class="flex gap-3">
                                    <!-- Cancel Button (only for pending requests) -->
                                    <template x-if="selectedRequest && selectedRequest.status === 'Pending'">
                                        <button @click="cancelRequest()" :disabled="isCancelling"
                                            class="flex-1 px-4 py-2.5 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2"
                                            :class="isCancelling ? 'bg-red-400 cursor-not-allowed' :
                                                'bg-red-600 hover:bg-red-700'">
                                            <i class="fa-solid fa-ban"></i>
                                            <span x-text="isCancelling ? 'Cancelling...' : 'Cancel Request'"></span>
                                        </button>
                                    </template>

                                    <!-- Status indicator for non-pending requests -->
                                    <template x-if="selectedRequest && selectedRequest.status !== 'Pending'">
                                        <div class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg border"
                                            :class="{
                                                'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800': selectedRequest
                                                    .status === 'Approved',
                                                'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800': selectedRequest
                                                    .status === 'Rejected'
                                            }">
                                            <i class="fa-solid"
                                                :class="{
                                                    'fa-circle-check text-green-600 dark:text-green-400': selectedRequest
                                                        .status === 'Approved',
                                                    'fa-circle-xmark text-red-600 dark:text-red-400': selectedRequest
                                                        .status === 'Rejected'
                                                }"></i>
                                            <span class="text-sm font-medium"
                                                :class="{
                                                    'text-green-700 dark:text-green-400': selectedRequest
                                                        .status === 'Approved',
                                                    'text-red-700 dark:text-red-400': selectedRequest
                                                        .status === 'Rejected'
                                                }"
                                                x-text="selectedRequest.status"></span>
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
        </section>

        @push('scripts')
            @once
                <script src="{{ asset('js/geofencing.js') }}"></script>
            @endonce
            @if (!auth()->user()->google_id)
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    const sonnerEl = document.querySelector('[x-data="sonnerToast()"]');
                    if (sonnerEl && sonnerEl._x_dataStack) {
                        Alpine.$data(sonnerEl).show(
                            'Link Your Gmail Account',
                            'For account verification and security, please link your personal Gmail account. This allows you to sign in with Google.',
                            'warning', {
                                actionUrl: "{{ route('employee.link-google') }}",
                                actionLabel: 'Link Gmail',
                                persistent: true
                            }
                        );
                    }
                }, 2000);
            });
            </script>
            @endif

            @if (!$hasAttendanceToday)
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    const sonnerEl = document.querySelector('[x-data="sonnerToast()"]');
                    if (sonnerEl && sonnerEl._x_dataStack) {
                        Alpine.$data(sonnerEl).show(
                            'Clock In Required',
                            'You haven\'t clocked in yet today. Your shift starts at {{ \Carbon\Carbon::parse($shiftStart)->format("g:i A") }} and ends at {{ \Carbon\Carbon::parse($shiftEnd)->format("g:i A") }}. Use the attendance drawer to clock in.',
                            'warning', {
                                persistent: true
                            }
                        );
                    }
                }, 3000);
            });
            </script>
            @endif
            <script>
                // Align Course Progress label with My Calendar label
                function alignCourseProgressWithCalendar() {
                    if (window.innerWidth < 1024) {
                        // Reset on small screens
                        const card = document.getElementById('attendance-card');
                        if (card) card.style.minHeight = '';
                        return;
                    }

                    const calendarLabel = document.querySelector('#tour-emp-calendar')?.previousElementSibling;
                    const courseProgressLabel = document.querySelector('#course-progress-section');
                    const attendanceCard = document.getElementById('attendance-card');

                    if (!calendarLabel || !courseProgressLabel || !attendanceCard) return;

                    // Reset first
                    attendanceCard.style.minHeight = '';

                    // Get positions
                    const calendarLabelTop = calendarLabel.getBoundingClientRect().top;
                    const courseLabelTop = courseProgressLabel.getBoundingClientRect().top;
                    const diff = calendarLabelTop - courseLabelTop;

                    if (Math.abs(diff) > 2) {
                        const currentHeight = attendanceCard.getBoundingClientRect().height;
                        attendanceCard.style.minHeight = (currentHeight + diff) + 'px';
                    }
                }

                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(alignCourseProgressWithCalendar, 100);
                });
                window.addEventListener('resize', function() {
                    const card = document.getElementById('attendance-card');
                    if (card) card.style.minHeight = '';
                });
            </script>
            <script>
                async function handleClockAction(formId, title, message, successTitle, successMessage) {
                    // Check location permission first
                    try {
                        const permResult = await navigator.permissions.query({ name: 'geolocation' });
                        if (permResult.state === 'denied') {
                            window.showErrorDialog('Location Required', 'Location access is blocked. Please click the lock icon in the address bar, allow Location, and reload the page.');
                            return;
                        }
                    } catch (e) { /* permissions API not supported */ }

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
                // Eagerly track user position as soon as page loads
                if (!window._cachedPosition) {
                    window._cachedPosition = null;
                    window._geoWatchId = null;

                    (function() {
                        if (!navigator.geolocation) return;
                        window._geoWatchId = navigator.geolocation.watchPosition(
                            function(position) {
                                window._cachedPosition = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude
                                };
                            },
                            function(error) {
                                console.warn('Geolocation error:', error.message);
                            }, {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 30000
                            }
                        );
                    })();
                }

                // Populate hidden lat/lng fields before form submission
                window.populateGeoFields = window.populateGeoFields || function(form) {
                    var latField = form.querySelector('.geo-latitude');
                    var lngField = form.querySelector('.geo-longitude');

                    // Source 1: Geofencing instance (already tracking for range check)
                    if (window.geofencing && window.geofencing.getUserLocation()) {
                        var loc = window.geofencing.getUserLocation();
                        latField.value = loc.lat;
                        lngField.value = loc.lng;
                        return true;
                    }

                    // Source 2: Eagerly cached position from watchPosition
                    if (window._cachedPosition) {
                        latField.value = window._cachedPosition.lat;
                        lngField.value = window._cachedPosition.lng;
                        return true;
                    }

                    // Source 3: Last resort - fetch position now and submit async
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            latField.value = position.coords.latitude;
                            lngField.value = position.coords.longitude;
                            form.submit();
                        }, function() {
                            form.submit();
                        }, {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 60000
                        });
                        return false;
                    }

                    return true;
                };
            </script>
            <script>
                // This script handles the Tasks Summary filter dropdown.
                document.addEventListener('DOMContentLoaded', function() {
                    const dropdownButton = document.querySelector('[data-dropdown-toggle="dropdown-time"]');
                    const dropdownMenu = document.getElementById('dropdown-time');

                    if (dropdownButton && dropdownMenu) {
                        dropdownMenu.addEventListener('click', function(event) {
                            const target = event.target.closest('a, button, li');

                            if (target) {
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

        <x-guided-tour tourName="employee-dashboard" :steps="json_encode([
            [
                'title' => 'Welcome to Your Dashboard',
                'description' =>
                    'This is your personal workspace where you can manage your tasks, track attendance, and monitor your progress. Let us show you around!',
                'side' => 'bottom',
                'align' => 'center',
            ],
            [
                'element' => '#sidebar',
                'title' => 'Navigation Menu',
                'description' =>
                    'Access your Tasks, Courses, Performance reviews, Attendance records, and History from here.',
                'side' => 'right',
                'align' => 'start',
            ],
            [
                'element' => '#tour-emp-calendar',
                'title' => 'Your Calendar',
                'description' =>
                    'Keep track of your schedule, holidays, and important dates on your personal calendar.',
                'side' => 'top',
                'align' => 'center',
            ],
            [
                'element' => '#tour-emp-tasks',
                'title' => 'Your To-Do List',
                'description' =>
                    'All your assigned tasks appear here. Click any task to see the full details including location, team members, and checklist.',
                'side' => 'top',
                'align' => 'center',
            ],
            [
                'element' => '#tour-emp-lessons',
                'title' => 'Training Courses',
                'description' =>
                    'Continue your learning here. Watch assigned training videos and track your course completion progress.',
                'side' => 'top',
                'align' => 'center',
            ],
            [
                'element' => '#tour-emp-right-panel',
                'title' => 'Tasks Summary & Attendance',
                'description' => 'View your task completion stats and log your daily attendance from this panel.',
                'side' => 'left',
                'align' => 'start',
            ],
        ])" />
    </x-skeleton-page>
</x-layouts.general-employee>
