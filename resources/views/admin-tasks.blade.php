<x-layouts.general-dashboard :title="'Task Management'">
    <!-- Sidebar Contents -->
    @slot('sidebar')
    @php
        $navOptions = [
                ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => route('admin.dashboard')],
                ['label' => 'Accounts', 'icon' => 'fa-users', 'href' => '/users'],
                ['label' => 'Tasks', 'icon' => 'fa-folder', 'href' => route('admin.tasks')],
                ['label' => 'Appointments', 'icon' => 'fa-calendar', 'href' => '/calendar'],
                ['label' => 'Analytics', 'icon' => 'fa-chart-line', 'href' => '/analytics'],
                ['label' => 'Reports', 'icon' => 'fa-file-lines', 'href' => '/reports'],
        ];

        $teams = ['HR Team', 'Tech Team'];
    @endphp

    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section role="status" class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Main Panel - Dashboard Content -->
        <div class="main-panel flex flex-col gap-6 flex-1 w-full p-4">
            <!-- Pass data to the calendar -->
            <x-taskcalendar :clients="$clients" :events="$events" />

            <!-- Pass data to the Kanban board -->
            <div class="w-full h-full">
                <x-kanbanboard :tasks="$tasks" />
            </div>
        </div>
    </section>
</x-layouts.general-dashboard>