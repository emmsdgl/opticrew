<x-layouts.general-employer :title="'Admin Dashboard'">
    <section class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
        
        <!-- Left Panel -->
        <div class="flex flex-col gap-6 flex-1 w-full">
            <div>
                <x-herocard 
                    :headerName="$admin->full_name ?? 'Admin'" 
                    :headerDesc="'Welcome to the admin dashboard. Track tasks and manage them in the dashboard'" 
                    :headerIcon="'hero-employer'" 
                />
            </div>

            <p class="text-sm font-sans font-bold text-gray-800 dark:text-gray-200">
                My Calendar
            </p>
            <div class="w-full">
                <x-calendar :holidays="$holidays" />
            </div>

            @livewire('admin.task-overview')
        </div>

        <!-- Right Panel -->
        <div class="flex flex-col gap-6 w-full lg:w-1/3">
            {{-- LIVEWIRE ATTENDANCE CHART - AUTO REFRESHES --}}
            @livewire('admin.attendance-chart')

            {{-- LIVEWIRE RECENT ARRIVALS - AUTO REFRESHES --}}
            @livewire('admin.recent-arrivals')
        </div>
    </section>
</x-layouts.general-employer>