<x-layouts.general-dashboard :title="'Employee Dashboard'">
    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => '/employee-dash'],
            ['label' => 'Tasks', 'icon' => 'fa-file-lines', 'href' => '/employee-tasks'],
            ['label' => 'Attendance', 'icon' => 'fa-calendar', 'href' => '/employee-attendance'],
            ['label' => 'Performance', 'icon' => 'fa-chart-line', 'href' => '/employee-performance']
        ];

        $teams = ['', ''];
    @endphp
    <x-sidebar :navOptions="$navOptions" :teams="$teams" />
    @endslot

    <section role="status" class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Left Panel - Dashboard Content -->
        <div
            class="flex flex-col gap-6 flex-1 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <!-- Inner Up - Dashboard Header -->
            <div
                class="w-full mt-6 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-48 sm:h-56 md:h-64 lg:h-1/3">
                <x-herocard :headerName="$employee->full_name ?? 'Employee'" :headerDesc="'Welcome to the employee dashboard. Track tasks and manage them in the dashboard'" :headerIcon="'hero-employee'" />
            </div>
            <!-- Inner Middle - Calendar -->
            <x-labelwithvalue label="My Calendar" count="" />
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-60 sm:h-72 md:h-80 lg:h-1/3">
                <x-calendar />
            </div>

            <!-- Inner Bottom - Daily Schedule -->
            <x-labelwithvalue label="Schedule" count="" />
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-60 sm:h-72 md:h-80 lg:h-1/3">
                @php
                    $appointments = [
                        [
                            'id' => 1,
                            'title' => 'Deep Cleaning',
                            'location' => 'Cabin 1',
                            'status' => 'in_progress',
                            'duration' => '02 h 30 m',
                            'progress' => 30,
                            'date' => '2025-07-07',
                            'time' => '10:00 AM'
                        ],
                        [
                            'id' => 2, // Changed to 2 (you had duplicate id: 1)
                            'title' => 'Deep Cleaning',
                            'location' => 'Cabin 1',
                            'status' => 'incomplete',
                            'duration' => '02 h 30 m',
                            'progress' => 50,
                            'date' => '2025-07-07',
                            'time' => '10:00 AM'
                        ],
                    ];
                @endphp

                <x-appointmentlistitem :appointments="$appointments" :editable="false" :show-progress="true"
                    :show-duration="true" on-item-click="handleAppointmentClick">
                </x-appointmentlistitem>
            </div>
        </div>

        <!-- Right Panel - Tasks Details -->
        <div
            class="flex flex-col gap-6 w-full lg:w-1/3 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">

            <div class="flex flex-row justify-between w-full">
                <x-labelwithvalue label="Tasks Summary" count="" />
                @php
                    $timeOptions = ['Yesterday', 'Today', 'Last 7 days', 'Last 30 days'];
                @endphp

                <x-dropdown :options="$timeOptions" default="This Day" id="dropdown-time" />

            </div>

            <!-- Inner Up - Tasks Summary -->
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-64 sm:h-1/2 md:h-1/2">

                <x-radialchart :chart-data="[
                    'done' => 88,
                    'inProgress' => 65,
                    'toDo' => 42
                ]" chart-id="task-chart" title="Last 7 days" :labels="[
                    'done' => 'Done',
                    'inProgress' => 'In Progress',
                    'toDo' => 'To Do'
                ]" :colors="[
                    'done' => '#2A6DFA',
                    'inProgress' => '#2AC9FA',
                    'toDo' => '#0028B3'
                ]" />
            </div>

            <x-labelwithvalue label="Your To-Do List" count="(5)" />

            <!-- Inner Down - Tasks Particulars -->
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-auto sm:h-56 md:h-auto overflow-y-auto">

                <div class="w-full flex flex-col">
                    @php
                        $tasks = [
                            [
                                'title' => 'Full Daily Cleaning',
                                'company' => 'Webify Inc.',
                                'subtitle' => 'Frontend Development',
                                'date' => 'Oct 15, 2025',
                                'dueTime' => '5:00 PM',
                                'iconBg' => 'bg-blue-100',
                            ],
                            [
                                'title' => 'Daily Room Cleaning',
                                'company' => 'PitchDeck Co.',
                                'subtitle' => 'Content Strategy',
                                'date' => 'Oct 16, 2025',
                                'dueTime' => '2:30 PM',
                                'iconBg' => 'bg-green-100',
                            ],
                        ];
                    @endphp

                    <div class="space-y-4">
                        @foreach($tasks as $task)
                            <x-todolistitem :task="$task" />
                        @endforeach
                    </div>


                </div>
            </div>
        </div>
    </section>
    </x-layouts.general-dashboard>
    @stack('scripts')