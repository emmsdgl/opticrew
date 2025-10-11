<x-layouts.admin-dashboard>
    <section role="status" class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Left Panel - Dashboard Content -->
        <div
            class="flex flex-col gap-6 flex-1 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <!-- Inner Up - Dashboard Header -->
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-48 sm:h-56 md:h-64 lg:h-1/3">
                <x-herocard 
                :headerName="$admin->full_name ?? 'Admin'"
                :headerDesc="'Welcome to the admin dashboard. Track tasks and manage them in the dashboard'"
                :headerIcon="'hero-employer'"
                />
            </div>
            <!-- Inner Middle - Calendar -->
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-60 sm:h-72 md:h-80 lg:h-1/3">
                <x-calendar />
            </div>

            <!-- Inner Bottom - Recent Orders -->
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-60 sm:h-72 md:h-80 lg:h-1/3">

            </div>
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
                <x-attendancechart />
            </div>

            <!-- Inner Down - Attendance Particulars -->
            <p class="text-sm font-sans font-bold w-full text-left ">
                Recent Arrivals
            </p>
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
                                'empName' => 'Joshua R. Cruz',
                                'empNum' => '12133193104',
                                'attendanceStatus' => 'Late Time In',
                                'attendanceDuration' => '0:20 mins',
                            ],
                            [
                                'empName' => 'Joshua R. Cruz',
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
</x-layouts.admin-dashboard>