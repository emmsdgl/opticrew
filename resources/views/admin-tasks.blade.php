<x-layouts.general-employer :title="'Task Management'">
    <section role="status" class="flex flex-col w-full lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Main Panel - Dashboard Content -->
        <div class="main-panel flex flex-col gap-6 flex-1 w-full p-4">
            <!-- Pass data to the calendar -->
            <x-taskcalendar :clients="$clients" :events="$events" :booked-locations-by-date="$bookedLocationsByDate" :holidays="$holidays" />

            <!-- Pass data to the Kanban board -->
            <div class="w-full max-h-[600px] min-h-[400px]">
                <x-kanbanboard :tasks="$tasks" />
            </div>
        </div>
    </section>
</x-layouts.general-dashboard>