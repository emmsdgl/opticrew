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

        $teams = ['HR Team', 'Research Team'];
    @endphp
    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
        {{ $slot }}
    </section>

</x-layouts.general-dashboard>