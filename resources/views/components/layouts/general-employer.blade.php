<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => route('admin.dashboard')],
            ['label' => 'Accounts', 'icon' => 'fa-users', 'href' => '/users'],
            ['label' => 'Tasks', 'icon' => 'fa-folder', 'href' => route('admin.tasks')],
            ['label' => 'Appointments', 'icon' => 'fa-calendar', 'href' => '/calendar'],
            ['label' => 'Analytics', 'icon' => 'fa-chart-line', 'href' => route('admin.analytics')],
            ['label' => 'Reports', 'icon' => 'fa-file-lines', 'href' => '/reports'],
        ];

        $teams = ['HR Team', 'Research Team'];
    @endphp
    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
            {{ $slot }}
    </section>
    @stack('scripts')

</x-layouts.general-dashboard>