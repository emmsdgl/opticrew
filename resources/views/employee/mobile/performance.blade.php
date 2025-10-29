{{-- MOBILE EMPLOYEE PERFORMANCE PAGE --}}
<section class="flex flex-col gap-4 p-4 min-h-screen">

    {{-- Page Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            <i class="fas fa-chart-line text-[#2A6DFA]"></i>
            Performance
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Track your work progress and achievements
        </p>
    </div>

    {{-- Performance Stats Cards - 3 Column Grid --}}
    <div class="grid grid-cols-3 gap-2">
        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg text-center border border-green-200 dark:border-green-800">
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $totalTasksCompleted }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Completed</p>
        </div>
        <div class="bg-orange-50 dark:bg-orange-900/20 p-3 rounded-lg text-center border border-orange-200 dark:border-orange-800">
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $incompleteTasks }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">In Progress</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg text-center border border-blue-200 dark:border-blue-800">
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $pendingTasks }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Pending</p>
        </div>
    </div>

    {{-- Performance Chart Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Hours Worked</h3>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $performanceData['dateRange'] }}</span>
        </div>

        {{-- Chart Stats --}}
        <div class="mb-4 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Total Hours</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($performanceData['currentValue'], 1) }}h</p>
                </div>
                @if($performanceData['changePercent'] != 0)
                <div class="text-right">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Change</p>
                    <p class="text-sm font-semibold {{ $performanceData['changePercent'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $performanceData['changePercent'] >= 0 ? '+' : '' }}{{ number_format($performanceData['changePercent'], 1) }}%
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- Simple Bar Chart --}}
        <div class="flex items-end justify-between gap-1 h-32">
            @foreach($performanceData['values'] as $index => $value)
                @php
                    $maxValue = max($performanceData['values']);
                    $heightPercent = $maxValue > 0 ? ($value / $maxValue) * 100 : 0;
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="w-full bg-purple-200 dark:bg-purple-900/40 rounded-t relative group" style="height: {{ $heightPercent }}%;">
                        <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-900 dark:bg-gray-700 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                            {{ number_format($value, 1) }}h
                        </div>
                    </div>
                    <span class="text-xs text-gray-600 dark:text-gray-400 mt-auto">{{ $performanceData['labels'][$index] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Attendance Summary --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2 mb-4">
            <i class="fa-solid fa-calendar-check text-[#2A6DFA]"></i>
            Attendance Summary
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(This Month)</span>
        </h3>

        <div class="space-y-3">
            @foreach($attendanceData as $data)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $data['label'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $data['current'] }}/{{ $data['total'] }}</span>
                    </div>
                </div>
                {{-- Progress Bar --}}
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    @php
                        $percentage = $data['total'] > 0 ? ($data['current'] / $data['total']) * 100 : 0;
                        $colorClass = match($data['color']) {
                            'blue' => 'bg-blue-600',
                            'navy' => 'bg-indigo-600',
                            'cyan' => 'bg-cyan-600',
                            'yellow' => 'bg-yellow-500',
                            default => 'bg-gray-600'
                        };
                    @endphp
                    <div class="{{ $colorClass }} h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recently Completed Tasks --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <button onclick="toggleMobileCompletedTasks()"
                class="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-check-circle text-green-600"></i>
                Recently Completed
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $recentlyCompletedTasks->count() }})</span>
            </h3>
            <i id="mobile-completed-tasks-icon" class="fa-solid fa-chevron-down text-gray-400 dark:text-gray-500 transition-transform duration-300"></i>
        </button>

        <div id="mobile-completed-tasks-content" class="max-h-0 overflow-hidden transition-all duration-300">
            <div class="p-4 pt-0 space-y-3 max-h-96 overflow-y-auto">
                @forelse($recentlyCompletedTasks as $task)
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                        {{-- Header: Client Name and Date --}}
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0" style="background-color: {{ $task['color'] }};">
                                    {{ $task['label'] }}
                                </div>
                                <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100">
                                    {{ $task['name'] }}
                                </h4>
                            </div>
                            <span class="text-xs px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-full whitespace-nowrap font-medium">
                                100%
                            </span>
                        </div>

                        {{-- Task Description --}}
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                            {{ $task['subtitle'] }}
                        </p>

                        {{-- Completion Info --}}
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-calendar-check text-green-500"></i>
                                <span>{{ $task['due_date'] ? \Carbon\Carbon::parse($task['due_date'])->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            @if($task['due_time'])
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-clock text-blue-500"></i>
                                <span>{{ $task['due_time'] }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Team Members --}}
                        @if(count($task['team_members']) > 0)
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Team:</span>
                            <div class="flex -space-x-2">
                                @foreach($task['team_members'] as $member)
                                    <div class="relative group/avatar flex-shrink-0">
                                        @if($member['picture'])
                                            <img src="{{ asset('storage/' . $member['picture']) }}"
                                                 class="w-6 h-6 rounded-full object-cover border-2 border-white dark:border-gray-800 shadow-sm flex-shrink-0">
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center border-2 border-white dark:border-gray-800 shadow-sm flex-shrink-0">
                                                <span class="text-white text-xs font-semibold">
                                                    {{ strtoupper(substr($member['name'], 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        {{-- Tooltip --}}
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded opacity-0 group-hover/avatar:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                                            {{ $member['name'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                        <i class="fa-solid fa-clipboard-check text-3xl mb-2"></i>
                        <p class="text-sm">No completed tasks yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bottom Padding for Safe Scrolling --}}
    <div class="h-20"></div>

</section>

@push('scripts')
<script>
    // Toggle completed tasks collapsible
    function toggleMobileCompletedTasks() {
        const content = document.getElementById('mobile-completed-tasks-content');
        const icon = document.getElementById('mobile-completed-tasks-icon');

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
