<x-layouts.general-dashboard :title="'Employee Dashboard'">
    @slot('sidebar')
    @php
        $navOptions = [
            ['label' => 'Dashboard', 'icon' => 'fa-house', 'href' => route('employee.dashboard')],
            ['label' => 'Tasks', 'icon' => 'fa-file-lines', 'href' => '/employee-tasks'],
            ['label' => 'Attendance', 'icon' => 'fa-calendar', 'href' => route('employee.attendance')],
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
                <x-appointmentlistitem :appointments="$dailySchedule->all()" :editable="false" :show-progress="true"
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
                    $timeOptions = ['All', 'Today', 'Yesterday', 'Last 7 days', 'Last 30 days'];
                @endphp

                <x-dropdown :options="$timeOptions" :default="$period" id="dropdown-time" />

            </div>

            <!-- Inner Up - Tasks Summary -->
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-64 sm:h-1/2 md:h-1/2">

                <x-radialchart :chart-data="$tasksSummary" chart-id="task-chart" title="Last 7 days" :labels="[
                    'done' => 'Done',
                    'inProgress' => 'In Progress',
                    'toDo' => 'To Do'
                ]" :colors="[
                    'done' => '#2A6DFA',
                    'inProgress' => '#2AC9FA',
                    'toDo' => '#0028B3'
                ]" />
            </div>

            <x-labelwithvalue label="Your To-Do List" count="({{ count($todoList) }})" />

            <!-- Inner Down - Tasks Particulars -->
            <div
                class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-auto sm:h-56 md:h-auto overflow-y-auto">

                <div class="w-full flex flex-col">
                    <div class="space-y-4">
                        @foreach($todoList as $task)
                            <x-todolistitem :task="(array)$task" />
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.general-dashboard>
@push('scripts')
<script>
    // This script handles the Tasks Summary filter dropdown.
    document.addEventListener('DOMContentLoaded', function () {
        // Find the button that triggers the dropdown. We assume it has a 'data-dropdown-toggle' attribute.
        const dropdownButton = document.querySelector('[data-dropdown-toggle="dropdown-time"]');
        const dropdownMenu = document.getElementById('dropdown-time');

        if (dropdownButton && dropdownMenu) {
            // Listen for clicks on the entire dropdown menu.
            dropdownMenu.addEventListener('click', function(event) {
                
                // Find the specific item that was clicked (could be a link or any other element).
                const target = event.target.closest('a, button, li'); // Make it flexible

                if (target) {
                    // Get the text content of the clicked item.
                    const selectedPeriod = target.textContent.trim();
                    
                    if (selectedPeriod) {
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.set('period', selectedPeriod);
                        window.location.href = currentUrl.toString();
                    }
                }
            });
        }
    });
</script>
@endpush
@stack('scripts')