
<x-layouts.employee-dashboard>
    <section role="status" class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)] animate-pulse">
        <!-- Left Panel - Dashboard Content -->
        <div class="flex flex-col gap-6 flex-1 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <!-- Inner Up - Dashboard Header -->
            <div class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-48 sm:h-56 md:h-64 lg:h-1/3">
            </div>

            <!-- Inner Middle - Calendar -->
            <div class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-60 sm:h-72 md:h-80 lg:h-1/3">
            </div>

            <!-- Inner Bottom - Recent Orders -->
            <div class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-60 sm:h-72 md:h-80 lg:h-1/3">
            </div>
        </div>

        <!-- Right Panel - Attendance Overview -->
        <div
            class="flex flex-col gap-6 w-full lg:w-1/3 border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            
            <!-- Inner Up - Attendance Chart -->
            <div class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-56 sm:h-56 md:h-64">
            </div>

            <!-- Inner Down - Attendance Particulars -->
            <div class="w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg h-56 sm:h-56 md:h-full">
            </div>
        </div>
    </section>
</x-layouts.employee-dashboard>