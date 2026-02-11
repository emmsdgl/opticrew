<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => route('manager.dashboard')],
            ['label' => 'Schedule', 'icon' => 'fa-calendar-days', 'href' => route('manager.schedule')],
            ['label' => 'Employees', 'icon' => 'fa-users', 'href' => route('manager.employees')],
            ['label' => 'Reports', 'icon' => 'fa-chart-line', 'href' => route('manager.reports')],
            ['label' => 'Activity', 'icon' => 'fa-bell', 'href' => route('manager.activity')],
            ['label' => 'History', 'icon' => 'fa-clock-rotate-left', 'href' => route('manager.history')],
        ];

        $teams = [];
    @endphp
    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
        {{ $slot }}
    </section>
    @stack('scripts')

</x-layouts.general-dashboard>
