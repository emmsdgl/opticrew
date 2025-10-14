<x-layouts.general-dashboard :title="'Admin Dashboard'">

    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => '/admin-dash'],
            ['label' => 'Accounts', 'icon' => 'fa-users', 'href' => '/users'],
            ['label' => 'Tasks', 'icon' => 'fa-folder', 'href' => '/admin-tasks'],
            ['label' => 'Appointments', 'icon' => 'fa-calendar', 'href' => '/calendar'],
            ['label' => 'Analytics', 'icon' => 'fa-chart-line', 'href' => '/analytics'],
            ['label' => 'Reports', 'icon' => 'fa-file-lines', 'href' => '/reports'],
        ];

        $teams = ['HR Team', 'Tech Team'];
    @endphp

    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section role="status" class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Left Panel - Dashboard Content -->
        <div
            class="flex flex-col gap-6 flex-1 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <!-- Inner Up - Dashboard Header -->
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-48 sm:h-56 md:h-64 lg:h-1/3">
                <x-herocard :headerName="$admin->full_name ?? 'Admin'" :headerDesc="'Welcome to the admin dashboard. Track tasks and manage them in the dashboard'" :headerIcon="'hero-employer'" />
            </div>
            <!-- Inner Middle - Calendar -->
            <p class="text-sm font-sans font-bold mr-2">
                My Calendar
            </p>
            <div
                class="w-full mb-6 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-auto sm:h-72 md:h-80 lg:h-auto">
                <x-calendar />
            </div>

            <!-- Inner Bottom - Recent Orders -->
            <div class="flex flex-row justify-between w-full">
                <x-labelwithvalue label="Task Overview" count="(5)" />
                <div class="flex flex-row gap-3">
                    @php
                        $timeOptions = ['This Day', 'This Week', 'This Month'];
                        $serviceOptions = ['Deep Cleaning', 'Daily Full Cleaning', 'Daily Room Cleaning', 'Full Room Cleaning'];
                    @endphp

                    <x-dropdown :options="$timeOptions" default="This Day" id="dropdown-time" />
                    <x-dropdown :options="$serviceOptions" default="Service Type" id="dropdown-service-type" />
                    <x-button label="New Task" color="blue" size="sm" icon='<i class="fa-solid fa-plus"></i>' />
                </div>
            </div>
            <x-tasklist :tasks="[
        ['id' => 1, 'title' => 'ABC Company Co.', 'category' => 'Deep Cleaning', 'date' => 'Oct 15', 'startTime' => '3:00 pm', 'avatar' => 'https://i.pravatar.cc/30?img=1', 'done' => false],
        ['id' => 2, 'title' => 'Chase and Morgans Company', 'category' => 'Daily Full Cleaning', 'date' => 'Oct 16', 'startTime' => '1:30 pm', 'avatar' => 'https://i.pravatar.cc/30?img=2', 'done' => true],
    ]" />
        </div>

        <!-- Right Panel - Attendance Overview -->
        <div
            class="flex flex-col gap-6 w-full lg:w-1/3 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">

            <!-- Inner Up - Attendance Chart -->
            <div
                class="w-full flex flex-col border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-72 sm:h-72 md:h-auto">
                <p class="text-sm font-sans font-bold w-full text-left">
                    Attendance Chart
                </p>

                <x-attendancechart :totalEmployees="$totalEmployees ?? 0" :presentEmployees="$presentEmployees ?? 0"
                    :absentEmployees="$absentEmployees ?? 0" :attendanceRate="$attendanceRate ?? 0" />
            </div>

            <!-- Inner Down - Attendance Particulars -->
            <x-labelwithvalue label="Recent Arrivals" count="(4)" />
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-56 sm:h-56 md:h-56 overflow-y-scroll">

                <div class="w-full flex flex-col">

                    @php
                        $employees = [
                            [
                                'empName' => 'Emmaus L. Digol',
                                'empNum' => '12133193103',
                                'attendanceStatus' => 'Early Time In',
                                'attendanceDuration' => '1:30 mins',
                            ],
                            [
                                'empName' => 'Nicole T. Candelaria',
                                'empNum' => '12133193104',
                                'attendanceStatus' => 'Late Time In',
                                'attendanceDuration' => '0:20 mins',
                            ],
                            [
                                'empName' => 'Juan R. Dela Cruz',
                                'empNum' => '12133193104',
                                'attendanceStatus' => 'Late Time In',
                                'attendanceDuration' => '0:20 mins',
                            ],
                            [
                                'empName' => 'Diana P. Chua',
                                'empNum' => '12133193107',
                                'attendanceStatus' => 'Late Time In',
                                'attendanceDuration' => '0:15 mins'
                            ],

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