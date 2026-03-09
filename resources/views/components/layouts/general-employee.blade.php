<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
    @php
        $navOptions = [
            [
                'label' => 'Dashboard',
                'icon' => 'fa-solid fa-house', // 'fa-house' is the standard v6 name
                'href' => route('employee.dashboard')
            ],
            [
                'label' => 'Tasks',
                'icon' => 'fa-solid fa-list-check', 
                'href' => route('employee.tasks')
            ],
            [
                'label' => 'Courses',
                'icon' => 'fa-solid fa-book-open',
                'href' => route('employee.development')
            ],
            [
                'label' => 'Performance',
                'icon' => 'fa-solid fa-chart-line',
                'href' => route('employee.performance')
            ],
            [
                'label' => 'Attendance',
                'icon' => 'fa-solid fa-calendar-check', 
                'href' => route('employee.attendance')
            ],
            [
                'label' => 'History',
                'icon' => 'fa-clock-rotate-left', 
                'href' => route('employee.history')
            ]
        ];

<<<<<<< HEAD
        $employee = \App\Models\Employee::where('user_id', auth()->id())->first();
        $teams = [];
        if ($employee) {
            $teams = \App\Models\OptimizationTeam::whereDate('service_date', today())
                ->whereHas('members', function ($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })
                ->get()
                ->map(fn ($t) => 'Team ' . $t->team_index)
                ->toArray();
        }
=======
>>>>>>> 22e73c40d8ca7ff6d4ea2c0949804bdf13e0a151
    @endphp
    <x-sidebar :navOptions="$navOptions" />
    @endslot

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
        {{ $slot }}
    </section>

</x-layouts.general-dashboard>