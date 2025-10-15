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

    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">

        <!-- Left Panel -->
        <div class="flex flex-col gap-6 flex-1 w-full">
            <div>
                <x-herocard :headerName="$admin->full_name ?? 'Admin'" :headerDesc="'Welcome to the admin dashboard. Track tasks and manage them in the dashboard'" :headerIcon="'hero-employer'" />
            </div>

            <p class="text-sm font-sans font-bold text-gray-800 dark:text-gray-200">
                My Calendar
            </p>
            <div class="w-full">
                <x-calendar />
            </div>

            @livewire('admin.task-overview')

        </div>

        <!-- Right Panel -->
        <div class="flex flex-col gap-6 w-full lg:w-1/3">
            <div class="w-full flex flex-col p-4 rounded-lg bg-white dark:bg-gray-800">
                <p class="text-sm font-sans font-bold w-full text-left dark:text-gray-200">
                    Attendance Chart
                </p>
                <x-attendancechart :totalEmployees="$totalEmployees" :presentEmployees="$presentEmployees"
                    :absentEmployees="$absentEmployees" :attendanceRate="$attendanceRate" />
            </div>

            <!-- === THE FIX FOR RECENT ARRIVALS === -->
            <div class="w-full flex flex-col gap-4 p-4 rounded-lg bg-white dark:bg-gray-800">
                <x-labelwithvalue label="Recent Arrivals" :count="'(' . $recentArrivals->count() . ')'" />
                <div class="overflow-y-auto h-56 pr-2">
                    <div class="flex flex-col gap-3 w-full">
                        @forelse ($recentArrivals as $arrival)
                            @if($arrival->employee) {{-- Add a check to ensure employee exists --}}
                                <x-attendanceparticulars 
                                    :empName="$arrival->employee->full_name"
                                    :empNum="$arrival->employee->user->email ?? 'N/A'"
                                    :attendanceStatus="'Timed In at'"
                                    :attendanceDuration="\Carbon\Carbon::parse($arrival->clock_in)->format('h:i A')" />
                            @endif
                        @empty
                            <div class="flex items-center justify-center h-full">
                                <p class="text-gray-500 dark:text-gray-400">No arrivals yet for today.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.general-dashboard>