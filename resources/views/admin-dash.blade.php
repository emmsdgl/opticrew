<x-layouts.general-dashboard :title="'Admin Dashboard'">

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

    {{-- The <section> is now a flex container that will wrap its children --}}
    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">

        <!-- Left Panel - Dashboard Content -->
        {{-- FIX 1: Made this a flex column so the task list can expand --}}
        <div class="flex flex-col gap-6 flex-1 w-full">
            <!-- Inner Up - Dashboard Header -->
            <div>
                <x-herocard :headerName="$admin->full_name ?? 'Admin'" :headerDesc="'Welcome to the admin dashboard. Track tasks and manage them in the dashboard'" :headerIcon="'hero-employer'" />
            </div>

            <!-- Inner Middle - Calendar -->
            <p class="text-sm font-sans font-bold text-gray-800 dark:text-gray-200">
                My Calendar
            </p>
            <div class="w-full">
                <x-calendar />
            </div>

            <!-- FIX 2: Wrapped the Task Overview in a flex-col container that will grow and handle overflow -->
            <!-- Inner Bottom - Recent Orders -->
            @livewire('admin.task-overview')

        </div>

        <!-- Right Panel - Attendance Overview -->
        <div class="flex flex-col gap-6 w-full lg:w-1/3">
            <!-- Inner Up - Attendance Chart -->
            <div class="w-full flex flex-col p-4 rounded-lg bg-white dark:bg-gray-800">
                <p class="text-sm font-sans font-bold w-full text-left dark:text-gray-200">
                    Attendance Chart
                </p>
                <x-attendancechart :totalEmployees="$totalEmployees ?? 0" :presentEmployees="$presentEmployees ?? 0"
                    :absentEmployees="$absentEmployees ?? 0" :attendanceRate="$attendanceRate ?? 0" />
            </div>

            <!-- Inner Down - Recent Arrivals -->
            <div class="w-full flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-gray-800">
                <x-labelwithvalue label="Recent Arrivals" count="(4)" />
                <div class="overflow-y-auto h-56">
                    @php
                        $employees = [
                            ['empName' => 'Emmaus L. Digol', 'empNum' => '12133193103', 'attendanceStatus' => 'Early Time In', 'attendanceDuration' => '1:30 mins'],
                            ['empName' => 'Nicole T. Candelaria', 'empNum' => '12133193104', 'attendanceStatus' => 'Late Time In', 'attendanceDuration' => '0:20 mins'],
                            ['empName' => 'Juan R. Dela Cruz', 'empNum' => '12133193104', 'attendanceStatus' => 'Late Time In', 'attendanceDuration' => '0:20 mins'],
                            ['empName' => 'Diana P. Chua', 'empNum' => '12133193107', 'attendanceStatus' => 'Late Time In', 'attendanceDuration' => '0:15 mins'],
                        ];
                    @endphp
                    <div class="flex flex-col gap-3 w-full">
                        @foreach ($employees as $employee)
                            <x-attendanceparticulars :empName="$employee['empName']" :empNum="$employee['empNum']"
                                :attendanceStatus="$employee['attendanceStatus']"
                                :attendanceDuration="$employee['attendanceDuration']" />
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.general-dashboard>