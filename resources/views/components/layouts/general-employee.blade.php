<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => route('employee.dashboard')],
            ['label' => 'Tasks', 'icon' => 'fa-file-lines', 'href' => route('employee.tasks')],
            ['label' => 'Attendance', 'icon' => 'fa-calendar', 'href' => route('employee.attendance')],
            ['label' => 'Schedule', 'icon' => 'fa-calendar', 'href' => '/calendar'],
            ['label' => 'Performance', 'icon' => 'fa-chart-line', 'href' => route('employee.performance')]
        ];

        $teams = ['HR Team', 'Research Team'];
    @endphp
    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
            {{ $slot }}
    </section>

</x-layouts.general-dashboard>