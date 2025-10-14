<x-layouts.general-dashboard :title="'Task Management'">
    <!-- Sidebar Contents -->
    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => '/admin-dash'],
            ['label' => 'Accounts', 'icon' => 'fa-users', 'href' => ''],
            ['label' => 'Tasks', 'icon' => 'fa-folder', 'href' => '/admin-tasks'],
            ['label' => 'Appointments', 'icon' => 'fa-calendar', 'href' => '/admin-appointments'],
            ['label' => 'Analytics', 'icon' => 'fa-chart-line', 'href' => '/admin-analytics'],
            ['label' => 'Reports', 'icon' => 'fa-file-lines', 'href' => '/admin-reports'],
        ];

        $teams = ['HR Team', 'Tech Team'];
    @endphp

    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section role="status" class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Main Panel - Dashboard Content -->
        <div
            class="main-panel flex flex-col gap-6 flex-1 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <!-- Inner Up - Task Calendar -->
            <x-taskcalendar />
            <div
            class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-40 sm:h-56 md:h-64 lg:h-full">
            <x-kanbanboard/>
            </div>
            <!-- Inner Middle - Kanban Tasks -->
        </div>

    </section>
</x-layouts.general-dashboard>