<x-layouts.general-landing :title="'Home'">

    @slot('topbar')
    @php
        $navOptions = [
        ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => '/employee/dashboard'],
        ['label' => 'Tasks', 'icon' => 'fa-folder', 'href' => '/users'],
        ['label' => 'Attendance', 'icon' => 'fa-users', 'href' => '/projects'],
        ['label' => 'Schedule', 'icon' => 'fa-calendar', 'href' => '/calendar'],
        ['label' => 'Performance', 'icon' => 'fa-chart-line', 'href' => '/analytics']
        ];

        $teams = ['HR Team', 'Research Team'];
    @endphp
    <x-topbar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
    </section>
    @stack('scripts')

</x-layouts.general-landing>