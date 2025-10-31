{{-- MOBILE EMPLOYEE TASKS PAGE --}}
<section class="flex flex-col gap-4 p-4 min-h-screen">

    {{-- Sticky Clock-In Status Header --}}
    <div class="sticky top-0 z-20 -mx-4 -mt-4 px-4 pt-4 pb-3 bg-white dark:bg-gray-900 shadow-md">
        @if($isClockedIn)
            {{-- Compact green banner when clocked in --}}
            <div class="flex items-center gap-2 p-2 rounded-lg bg-green-500 text-white shadow-sm">
                <div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>
                <span class="text-sm font-medium">
                    <i class="fas fa-check-circle"></i> Clocked In • {{ $clockInTime }}
                </span>
            </div>
        @else
            {{-- Warning banner with CTA when not clocked in --}}
            <div class="p-3 rounded-lg bg-orange-500 text-white shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-sm font-medium">
                        <i class="fas fa-exclamation-triangle"></i> Not Clocked In
                    </span>
                    <a href="{{ route('employee.dashboard') }}"
                       class="px-3 py-1 bg-white text-orange-600 rounded-full text-xs font-bold whitespace-nowrap hover:bg-gray-100 transition-colors">
                        Clock In
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session()->has('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded-md text-sm" role="alert">
            <p class="font-semibold">Success!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-md text-sm" role="alert">
            <p class="font-semibold">Error!</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Quick Stats Cards --}}
    <div class="grid grid-cols-3 gap-2">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg text-center border border-blue-200 dark:border-blue-800">
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $todayTasks->count() }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Today</p>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg text-center border border-green-200 dark:border-green-800">
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $todayTasks->where('status', 'Completed')->count() }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Done</p>
        </div>
        <div class="bg-orange-50 dark:bg-orange-900/20 p-3 rounded-lg text-center border border-orange-200 dark:border-orange-800">
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $todayTasks->where('status', 'In Progress')->count() }}</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Active</p>
        </div>
    </div>

    {{-- Wrap filter buttons and tasks in single Alpine.js scope --}}
    <div x-data="{ filter: 'all' }">
        {{-- Filter Buttons --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide">
            <button @click="filter = 'all'"
                    :class="filter === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors">
                All ({{ $todayTasks->count() }})
            </button>
            <button @click="filter = 'scheduled'"
                    :class="filter === 'scheduled' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors">
                Scheduled ({{ $todayTasks->where('status', 'Scheduled')->count() }})
            </button>
            <button @click="filter = 'in-progress'"
                    :class="filter === 'in-progress' ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors">
                In Progress ({{ $todayTasks->where('status', 'In Progress')->count() }})
            </button>
            <button @click="filter = 'on-hold'"
                    :class="filter === 'on-hold' ? 'bg-yellow-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors">
                On Hold ({{ $todayTasks->where('status', 'On Hold')->count() }})
            </button>
        </div>

        {{-- Today's Tasks - Mobile Optimized Cards --}}
        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2 mt-4">
            <i class="fa-solid fa-list-check text-blue-600"></i>
            Today's Tasks
        </h3>

        <div class="space-y-3 mt-3">
            @forelse($todayTasks as $task)
                <div x-show="filter === 'all' ||
                            (filter === 'scheduled' && '{{ $task->status }}' === 'Scheduled') ||
                            (filter === 'in-progress' && '{{ $task->status }}' === 'In Progress') ||
                            (filter === 'on-hold' && '{{ $task->status }}' === 'On Hold')"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">
                    <x-mobile-task-card :task="$task" :isClockedIn="$isClockedIn" />
                </div>
            @empty
                <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                    <i class="fa-solid fa-calendar-check text-5xl mb-3"></i>
                    <p class="text-sm font-medium">No tasks for today!</p>
                    <p class="text-xs mt-1">Enjoy your day or check back later</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Divider --}}
    @if($upcomingTasks->count() > 0)
        <hr class="my-4 border-gray-300 dark:border-gray-700">
    @endif

    {{-- Upcoming Tasks - Compact List --}}
    @if($upcomingTasks->count() > 0)
        <div x-data="{ showUpcoming: false }">
            <button @click="showUpcoming = !showUpcoming"
                    class="w-full flex items-center justify-between text-left mb-3">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <i class="fa-regular fa-calendar-days text-blue-600"></i>
                    Upcoming Tasks ({{ $upcomingTasks->count() }})
                </h3>
                <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 transition-transform duration-300"
                   :class="{'rotate-180': showUpcoming}"></i>
            </button>

            <div x-show="showUpcoming"
                 x-collapse
                 class="space-y-2">
                @foreach($upcomingTasks as $task)
                    <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-gray-900 dark:text-gray-100 truncate">
                                    {{ $task->task_description }}
                                </p>
                                <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-calendar text-blue-500"></i>
                                        {{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-location-dot text-red-500"></i>
                                        {{ Str::limit($task->location->location_name ?? 'TBD', 15) }}
                                    </span>
                                    @if($task->estimated_duration_minutes)
                                        <span class="flex items-center gap-1">
                                            <i class="fa-solid fa-clock text-orange-500"></i>
                                            {{ $task->estimated_duration_minutes }}m
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full whitespace-nowrap font-medium">
                                {{ $task->status }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Bottom Padding for Safe Scrolling --}}
    <div class="h-20"></div>

</section>

<style>
/* Hide scrollbar for filter buttons */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
