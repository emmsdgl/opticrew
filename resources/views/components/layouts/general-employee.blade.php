<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
    @php
        $navOptions = [
            [
                'label' => 'Dashboard',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
          <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>',
                'href' => route('employee.dashboard')
            ],
            ['label' => 'Tasks', 'icon' => 'fa-file-lines', 'href' => route('employee.tasks')],
            ['label' => 'Development', 'icon' => 'fa-file-lines', 'href' => route('employee.development')],
            ['label' => 'Performance', 'icon' => 'fa-chart-line', 'href' => route('employee.performance')],
            ['label' => 'Attendance', 'icon' => 'fa-calendar', 'href' => route('employee.attendance')]
        ];

        $teams = ['HR Team', 'Research Team'];
    @endphp
    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
        {{ $slot }}
    </section>

</x-layouts.general-dashboard>