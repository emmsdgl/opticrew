{{-- MOBILE EMPLOYEE ATTENDANCE --}}
<section class="flex flex-col gap-4 p-4 min-h-[calc(100vh-5rem)]">

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    {{-- Page Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            <i class="fas fa-clock text-[#2A6DFA]"></i>
            Attendance
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            {{ now()->format('F Y') }} Summary
        </p>
    </div>

    {{-- Quick Stats Cards - 2 Column Grid (First 2 stats only) --}}
    <div class="grid grid-cols-2 gap-3">
        @foreach($stats as $index => $stat)
            @if($index < 2)
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-10 h-10 {{ $stat['iconBg'] ?: 'bg-gray-100 dark:bg-gray-700' }} rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="{{ $stat['icon'] }} {{ $stat['iconColor'] }} text-lg"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                        {{ $stat['value'] }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-tight">{{ $stat['title'] }}</p>

                    {{-- Trend Indicator --}}
                    @if(isset($stat['trend']) && isset($stat['trendValue']))
                        <div class="flex items-center gap-1 mt-2">
                            @if($stat['trend'] === 'up')
                                <i class="fas fa-arrow-up text-green-600 dark:text-green-400 text-xs"></i>
                                <span class="text-xs text-green-600 dark:text-green-400 font-semibold">{{ $stat['trendValue'] }}</span>
                            @else
                                <i class="fas fa-arrow-down text-red-600 dark:text-red-400 text-xs"></i>
                                <span class="text-xs text-red-600 dark:text-red-400 font-semibold">{{ $stat['trendValue'] }}</span>
                            @endif
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $stat['trendLabel'] ?? '' }}</span>
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    {{-- Show 3rd stat as full width if it exists --}}
    @if(count($stats) >= 3)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 {{ $stats[2]['iconBg'] ?: 'bg-gray-100 dark:bg-gray-700' }} rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="{{ $stats[2]['icon'] }} {{ $stats[2]['iconColor'] }} text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ $stats[2]['title'] }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats[2]['value'] }}</p>
                    @if(isset($stats[2]['subtitle']))
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $stats[2]['subtitle'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Attendance Records - Collapsible Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <button onclick="toggleMobileAttendance()"
                class="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-[#2A6DFA]"></i>
                Attendance Records
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ count($attendanceRecords) }})</span>
            </h3>
            <i id="mobile-attendance-icon" class="fa-solid fa-chevron-down text-gray-400 dark:text-gray-500 transition-transform duration-300"></i>
        </button>

        <div id="mobile-attendance-content" class="max-h-0 overflow-hidden transition-all duration-300">
            <div class="p-4 pt-0 space-y-3 max-h-[500px] overflow-y-auto">
                @forelse($attendanceRecords as $record)
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                        {{-- Header: Date and Status --}}
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100">
                                    {{ $record['date'] }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $record['dayOfWeek'] }}
                                </p>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($record['status'] === 'present')
                                    bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                @elseif($record['status'] === 'late')
                                    bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                @else
                                    bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                @endif">
                                {{ ucfirst($record['status']) }}
                            </span>
                        </div>

                        {{-- Time Details Grid --}}
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            {{-- Clock In --}}
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-2">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-sign-in-alt text-green-600 dark:text-green-400 text-xs"></i>
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Clock In</p>
                                </div>
                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $record['timeIn'] }}</p>
                                @if($record['timeInNote'])
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $record['timeInNote'] }}</p>
                                @endif
                            </div>

                            {{-- Clock Out --}}
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-2">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-sign-out-alt text-red-600 dark:text-red-400 text-xs"></i>
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Clock Out</p>
                                </div>
                                @if($record['timeOut'])
                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $record['timeOut'] }}</p>
                                    @if($record['timeOutNote'])
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $record['timeOutNote'] }}</p>
                                    @endif
                                @else
                                    <p class="text-sm font-medium text-gray-400 dark:text-gray-500">Not yet</p>
                                @endif
                            </div>
                        </div>

                        {{-- Hours Worked Info --}}
                        @if(isset($record['hoursWorked']) && $record['hoursWorked'])
                            <div class="flex items-center justify-between p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-blue-600 dark:text-blue-400 text-sm"></i>
                                    <span class="text-xs font-semibold text-blue-800 dark:text-blue-300">Total Hours Worked</span>
                                </div>
                                <span class="text-sm font-bold text-blue-900 dark:text-blue-100">{{ $record['hoursWorked'] }}</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-calendar-xmark text-3xl mb-2"></i>
                        <p class="text-sm">No attendance records found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</section>

@push('scripts')
<script>
// Toggle mobile attendance records collapsible section
function toggleMobileAttendance() {
    const content = document.getElementById('mobile-attendance-content');
    const icon = document.getElementById('mobile-attendance-icon');

    if (content.style.maxHeight && content.style.maxHeight !== '0px') {
        content.style.maxHeight = '0';
        icon.classList.remove('rotate-180');
    } else {
        content.style.maxHeight = content.scrollHeight + 'px';
        icon.classList.add('rotate-180');
    }
}
</script>
@endpush
