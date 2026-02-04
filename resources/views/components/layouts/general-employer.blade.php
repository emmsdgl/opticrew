<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-solid fa-house', 'href' => route('admin.dashboard')],
            ['label' => 'Accounts', 'icon' => 'fa-users', 'href' => route('admin.accounts.index')],
            ['label' => 'Tasks', 'icon' => 'fa-solid fa-list-check', 'href' => route('admin.tasks')],
            [
                'label' => 'Appointments',
                'icon' => 'fa-calendar-check',
                'children' => [
                    ['label' => 'Appointments', 'icon' => 'fa-calendar-days', 'href' => route('admin.appointments.index')],
                    ['label' => 'Quotation Requests', 'icon' => 'fa-file-invoice', 'href' => route('admin.quotations.index')],
                ]
            ],
            ['label' => 'Attendance', 'icon' => 'fa-solid fa-calendar-check', 'href' => route('admin.attendance')],
            ['label' => 'History', 'icon' => 'fa-clock-rotate-left', 'href' => route('admin.history')],
            ['label' => 'Analytics', 'icon' => 'fa-chart-line', 'href' => route('admin.analytics')],
        ];

        // Only show Optimization Result in local development environment
        if (app()->environment('local')) {
            $navOptions[] = ['label' => 'Optimization Result', 'icon' => 'fa-file-lines', 'href' => route('optimization.result')];
        }

        $teams = ['HR Team', 'Research Team'];
    @endphp
    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
            {{ $slot }}
    </section>
    @stack('scripts')

</x-layouts.general-dashboard>