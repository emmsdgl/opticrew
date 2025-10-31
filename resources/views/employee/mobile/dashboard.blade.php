{{-- MOBILE EMPLOYEE DASHBOARD --}}
<section class="flex flex-col gap-4 p-4 min-h-[calc(100vh-5rem)]">

    {{-- Mobile Hero Card - Solid Blue Background --}}
    <div class="bg-[#2A6DFA] rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-white mb-2">
            Hello, {{ $employee->user->name ?? 'Employee' }}!
        </h2>
        <p class="text-white opacity-90 text-sm mb-3">
            Welcome to your dashboard. Track tasks and manage them efficiently.
        </p>

        {{-- Real-time Clock --}}
        <div class="bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2.5 border border-white/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-clock text-white/90 text-lg"></i>
                    <div>
                        <div id="mobile-current-time" class="text-white font-semibold text-lg font-mono">
                            --:--:--
                        </div>
                        <div id="mobile-current-date" class="text-white/80 text-xs">
                            Loading...
                        </div>
                    </div>
                </div>
                <div id="mobile-timezone" class="text-white/70 text-xs font-medium bg-white/10 px-2 py-1 rounded">
                    EET
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions Grid --}}
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('employee.tasks') }}"
           class="flex items-center justify-center gap-2 bg-[#2A6DFA] text-white rounded-full px-4 py-3 shadow-sm hover:bg-[#2558d6] focus:ring-4 focus:ring-blue-300 transition-all duration-300 active:scale-95">
            <i class="fa-solid fa-list-check"></i>
            <span class="font-medium text-sm">View Tasks</span>
        </a>

        @if($hasAttendanceToday && !$isClockedIn)
            {{-- Already clocked in AND clocked out today - show disabled message --}}
            <div class="flex items-center justify-center gap-2 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-full px-4 py-3 shadow-sm border border-green-300 dark:border-green-700">
                <i class="fa-solid fa-check-circle"></i>
                <span class="font-medium text-xs">Already clocked in today</span>
            </div>
        @else
            {{-- Normal clock in/out button --}}
            <button type="button"
               id="clock-in-out-button"
               class="flex items-center justify-center gap-2 bg-gray-300 dark:bg-gray-600 text-white rounded-full px-4 py-3 shadow-sm border border-gray-200 dark:border-gray-700 focus:ring-4 focus:ring-gray-300 transition-all duration-300 cursor-not-allowed pointer-events-none"
               disabled>
                <i class="fa-regular fa-clock"></i>
                <span class="font-medium text-sm" id="clock-button-text">
                    @if($isClockedIn)
                        Clock Out
                    @else
                        Clock In
                    @endif
                </span>
            </button>
        @endif
    </div>

    {{-- Geofencing Status Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        @if($hasAttendanceToday && !$isClockedIn)
            {{-- Show completion message if already clocked in today --}}
            <div class="text-sm text-green-600 dark:text-green-400 flex items-center gap-2">
                <i class="fa-solid fa-check-circle text-green-500"></i>
                <span>Attendance completed for today</span>
            </div>
        @else
            {{-- Show geofencing status for active clock in/out --}}
            <div id="geofence-status" class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <i class="fa-solid fa-spinner fa-spin text-blue-500"></i>
                <span>Checking location...</span>
            </div>
            <div id="geofence-distance" class="text-xs text-gray-400 dark:text-gray-500 mt-1 hidden"></div>
        @endif
    </div>

    {{-- Quick Stats Cards - 2 Column Grid --}}
    <div class="grid grid-cols-2 gap-3">
        {{-- Today's Tasks --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-clipboard-list text-blue-600 dark:text-blue-400 text-lg"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                {{ count($todoList) }}
            </p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Today's Tasks</p>
        </div>

        {{-- Completed Tasks --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 text-lg"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                {{ $tasksSummary['done'] ?? 0 }}
            </p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Completed</p>
        </div>
    </div>

    {{-- Today's Schedule Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <i class="fa-regular fa-calendar text-[#2A6DFA]"></i>
                Today's Schedule
            </h3>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ now()->format('M d') }}
            </span>
        </div>

        <div class="space-y-3 max-h-64 overflow-y-auto">
            @forelse($dailySchedule as $schedule)
                @php
                    // Calculate total duration in hours
                    $durationMinutes = (int) $schedule->total_duration;
                    $hours = floor($durationMinutes / 60);
                    $minutes = $durationMinutes % 60;

                    // Format duration message
                    if ($hours > 0 && $minutes > 0) {
                        $durationText = "{$hours} hr {$minutes} min";
                    } elseif ($hours > 0) {
                        $durationText = $hours == 1 ? "{$hours} hr" : "{$hours} hrs";
                    } else {
                        $durationText = "{$minutes} min";
                    }
                @endphp
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600 hover:shadow-md transition-shadow duration-200">
                    {{-- Time Display --}}
                    <div class="flex-shrink-0">
                        <div class="text-[#2A6DFA] font-bold text-base">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                        </div>
                        <div class="text-gray-400 dark:text-gray-500 text-xs mt-0.5">
                            Est {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                        </div>
                    </div>

                    {{-- Company Details --}}
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-sm text-gray-900 dark:text-gray-100 mb-1">
                            {{ $schedule->client_name }}
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Finish tasks in {{ $durationText }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                    <i class="fa-regular fa-calendar-xmark text-3xl mb-2"></i>
                    <p class="text-sm">No schedules for today</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- To-Do List - Collapsible Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <button onclick="toggleMobileTodos()"
                class="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-list text-[#2A6DFA]"></i>
                Your To-Do List
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ count($todoList) }})</span>
            </h3>
            <i id="mobile-todos-icon" class="fa-solid fa-chevron-down text-gray-400 dark:text-gray-500 transition-transform duration-300"></i>
        </button>

        <div id="mobile-todos-content" class="max-h-0 overflow-hidden transition-all duration-300">
            <div class="p-4 pt-0 space-y-4 max-h-96 overflow-y-auto">
                @forelse($todoList as $task)
                    @php
                        // Calculate duration
                        $durationMinutes = (int) $task->duration;
                        $hours = floor($durationMinutes / 60);
                        $minutes = $durationMinutes % 60;

                        if ($hours > 0 && $minutes > 0) {
                            $durationText = "{$hours} hr {$minutes} min";
                        } elseif ($hours > 0) {
                            $durationText = $hours == 1 ? "{$hours} hr" : "{$hours} hrs";
                        } else {
                            $durationText = "{$minutes} min";
                        }
                    @endphp
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                        {{-- Header: Company Name and Date --}}
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100">
                                {{ $task->client_name }}
                            </h4>
                            <span class="font-bold text-xs text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($task->date)->format('Y-m-d') }}
                            </span>
                        </div>

                        {{-- Cabin Name and Duration --}}
                        <div class="flex items-start justify-between text-xs mb-1">
                            <p class="text-gray-700 dark:text-gray-300">
                                {{ $task->cabin_name ?? 'Location TBD' }}
                            </p>
                            <span class="text-gray-500 dark:text-gray-400 whitespace-nowrap ml-2">
                                Est finish in {{ $durationText }}
                            </span>
                        </div>

                        {{-- Task Description --}}
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $task->task_description }}
                        </p>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                        <i class="fa-regular fa-circle-check text-3xl mb-2"></i>
                        <p class="text-sm">All caught up!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Calendar Widget - Collapsible Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <button onclick="toggleMobileCalendar()"
                class="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <i class="fa-regular fa-calendar-days text-[#2A6DFA]"></i>
                My Calendar
            </h3>
            <i id="mobile-calendar-icon" class="fa-solid fa-chevron-down text-gray-400 dark:text-gray-500 transition-transform duration-300"></i>
        </button>

        <div id="mobile-calendar-content" class="max-h-0 overflow-hidden transition-all duration-300">
            <div class="p-4 pt-0">
                <x-calendar :holidays="$holidays" calendar-id="mobile" />
            </div>
        </div>
    </div>

</section>

@push('scripts')
<!-- Include Geofencing Script (only if not already loaded) -->
@once
<script src="{{ asset('js/geofencing.js') }}"></script>
@endonce

<script>
    // Mobile Todos Toggle
    function toggleMobileTodos() {
        const content = document.getElementById('mobile-todos-content');
        const icon = document.getElementById('mobile-todos-icon');

        if (content.style.maxHeight && content.style.maxHeight !== '0px') {
            content.style.maxHeight = '0px';
            icon.classList.remove('rotate-180');
        } else {
            content.style.maxHeight = content.scrollHeight + 'px';
            icon.classList.add('rotate-180');
        }
    }

    // Mobile Calendar Toggle
    function toggleMobileCalendar() {
        const content = document.getElementById('mobile-calendar-content');
        const icon = document.getElementById('mobile-calendar-icon');

        if (content.style.maxHeight && content.style.maxHeight !== '0px') {
            content.style.maxHeight = '0px';
            icon.classList.remove('rotate-180');
        } else {
            content.style.maxHeight = content.scrollHeight + 'px';
            icon.classList.add('rotate-180');
        }
    }

    // Initialize Geofencing on page load (only once)
    (function() {
        // Skip geofencing if attendance is already complete for today
        const hasAttendanceToday = {{ $hasAttendanceToday ? 'true' : 'false' }};
        const isClockedIn = {{ $isClockedIn ? 'true' : 'false' }};

        if (hasAttendanceToday && !isClockedIn) {
            console.log('Attendance already completed for today - skipping geofencing');
            return;
        }

        // Prevent duplicate initialization
        if (window.geofencingInitialized) {
            return;
        }
        window.geofencingInitialized = true;

        document.addEventListener('DOMContentLoaded', function() {
            // Fetch task location from backend (with credentials for session auth)
            // Add timestamp to bypass browser cache
            const timestamp = new Date().getTime();
            fetch(`/api/company-settings?_=${timestamp}`, {
                credentials: 'same-origin',
                cache: 'no-store', // Prevent browser caching
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch task location');
                    }
                    return response.json();
                })
                .then(data => {
                    // Initialize geofencing with task location (only if not already initialized)
                    if (!window.geofencing) {
                        window.geofencing = new Geofencing({
                            officeLatitude: data.office_latitude,
                            officeLongitude: data.office_longitude,
                            radius: data.geofence_radius,
                            locationName: data.location_name,
                            locationType: data.location_type,
                            message: data.message,
                            buttonId: 'clock-in-out-button',
                            statusElementId: 'geofence-status',
                            distanceElementId: 'geofence-distance'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching task location:', error);
                    const statusEl = document.getElementById('geofence-status');
                    if (statusEl) {
                        statusEl.innerHTML = '<i class="fa-solid fa-exclamation-triangle text-red-500"></i> <span class="text-red-600 dark:text-red-400">Failed to load task location</span>';
                    }
                });
        });
    })();

    // Clean up geofencing when leaving page
    window.addEventListener('beforeunload', function() {
        if (window.geofencing) {
            window.geofencing.stopWatching();
        }
    });

    // Handle Clock In/Out button click (use 'let' to avoid redeclaration errors)
    (function() {
        const clockButton = document.getElementById('clock-in-out-button');
        const isClockedIn = {{ $isClockedIn ? 'true' : 'false' }};

        if (clockButton) {
            clockButton.addEventListener('click', function() {
                if (clockButton.disabled) return;

                // Disable button and show loading
                clockButton.disabled = true;
                const buttonText = document.getElementById('clock-button-text');
                const originalText = buttonText.textContent;
                buttonText.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

                // Determine which action to take
                const url = isClockedIn
                    ? '{{ route("employee.attendance.clockout") }}'
                    : '{{ route("employee.attendance.clockin") }}';

                // Submit POST request
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    // Check if response is ok (status 200-299)
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Server error');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Check if the operation was successful
                    if (data.success === false) {
                        throw new Error(data.message || 'Operation failed');
                    }

                    // Show success message
                    const statusElement = document.getElementById('geofence-status');
                    if (statusElement) {
                        statusElement.innerHTML = `<i class="fa-solid fa-check-circle text-green-500"></i> <span class="text-green-600 dark:text-green-400">${isClockedIn ? 'Clocked out' : 'Clocked in'} successfully!</span>`;
                    }

                    // Redirect to attendance page after 1 second
                    setTimeout(() => {
                        window.location.href = '{{ route("employee.attendance") }}';
                    }, 1000);
                })
                .catch(error => {
                    console.error('Clock in/out error:', error);

                    // Show error message with actual error text
                    const statusElement = document.getElementById('geofence-status');
                    if (statusElement) {
                        statusElement.innerHTML = `<i class="fa-solid fa-exclamation-triangle text-red-500"></i> <span class="text-red-600 dark:text-red-400">${error.message || 'Failed to clock in/out. Please try again.'}</span>`;
                    }

                    // Re-enable button
                    clockButton.disabled = false;
                    buttonText.textContent = originalText;
                });
            });
        }
    })();

    // Real-time Clock Update (Finnish Timezone)
    function updateClock() {
        const now = new Date();
        const timeZone = 'Europe/Helsinki';

        // Format time in Finnish timezone (HH:MM:SS)
        const timeFormatter = new Intl.DateTimeFormat('en-US', {
            timeZone: timeZone,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        const timeString = timeFormatter.format(now);

        // Format date in Finnish timezone (Day, Month DD, YYYY)
        const dateFormatter = new Intl.DateTimeFormat('en-US', {
            timeZone: timeZone,
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
        const dateString = dateFormatter.format(now);

        // Get timezone abbreviation (EET or EEST) - Check if Finland is in DST
        const dateInHelsinki = new Intl.DateTimeFormat('en-US', {
            timeZone: timeZone,
            timeZoneName: 'short'
        }).format(now);

        // Extract timezone abbreviation from formatted string
        const tzMatch = dateInHelsinki.match(/GMT([+-]\d+)/);
        let tzAbbr = 'EET';

        if (tzMatch) {
            const offset = parseInt(tzMatch[1]);
            // Finland is UTC+2 (EET) in winter, UTC+3 (EEST) in summer
            tzAbbr = offset === 3 ? 'EEST' : 'EET';
        } else {
            // Fallback: Check if we're in DST period for Finland
            const january = new Date(now.getFullYear(), 0, 1);
            const july = new Date(now.getFullYear(), 6, 1);

            const janOffset = new Intl.DateTimeFormat('en-US', {
                timeZone: timeZone,
                hour: 'numeric'
            }).format(january);

            const julyOffset = new Intl.DateTimeFormat('en-US', {
                timeZone: timeZone,
                hour: 'numeric'
            }).format(july);

            const currentHour = new Intl.DateTimeFormat('en-US', {
                timeZone: timeZone,
                hour: 'numeric'
            }).format(now);

            // If current offset matches July (summer), it's EEST
            tzAbbr = julyOffset !== janOffset ? 'EEST' : 'EET';
        }

        // Update DOM elements
        const timeElement = document.getElementById('mobile-current-time');
        const dateElement = document.getElementById('mobile-current-date');
        const tzElement = document.getElementById('mobile-timezone');

        if (timeElement) timeElement.textContent = timeString;
        if (dateElement) dateElement.textContent = dateString;
        if (tzElement) tzElement.textContent = tzAbbr;
    }

    // Initialize clock on page load (only once)
    (function() {
        // Prevent duplicate clock initialization
        if (window.clockInitialized) {
            return;
        }
        window.clockInitialized = true;

        document.addEventListener('DOMContentLoaded', function() {
            updateClock(); // Update immediately
            setInterval(updateClock, 1000); // Update every second
        });
    })();
</script>
@endpush
