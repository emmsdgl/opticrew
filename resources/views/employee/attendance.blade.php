<x-layouts.general-employee :title="'Attendance'">

    {{-- MOBILE LAYOUT (< 1024px) - Hidden on large screens --}}
    <div class="lg:hidden">
        @include('employee.mobile.attendance')
    </div>

    {{-- DESKTOP LAYOUT (≥ 1024px) - Hidden on small screens --}}
    <section role="status" class="w-full hidden lg:flex flex-col lg:flex-col gap-1 p-4 md:p-6">

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Inner Panel - Summary Cards Container -->
        <div class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <x-labelwithvalue label="Summary" count="" />

            <div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 p-6">
                @foreach($stats as $stat)
                    <x-statisticscard 
                        :title="$stat['title']" 
                        :value="$stat['value']" 
                        :subtitle="$stat['subtitle'] ?? ''"
                        :trend="$stat['trend'] ?? null" 
                        :trend-value="$stat['trendValue'] ?? null"
                        :trend-label="$stat['trendLabel'] ?? 'vs last month'" 
                        :icon="$stat['icon'] ?? null"
                        :icon-bg="$stat['iconBg'] ?? 'bg-gray-100'" 
                        :icon-color="$stat['iconColor'] ?? 'text-gray-600'"
                        :value-suffix="$stat['valueSuffix'] ?? ''" 
                        :value-prefix="$stat['valuePrefix'] ?? ''" 
                    />
                @endforeach
            </div>
        </div>

        <!-- Inner Panel - Attendance Records List -->
        <div class="flex flex-col gap-6 w-full border border-dashed border-gray-400 dark:border-gray-700 rounded-lg p-4">
            <x-labelwithvalue label="Attendance Records" count="({{ count($attendanceRecords) }})" />
            
            @if(count($attendanceRecords) > 0)
                <x-attendancelistitem :records="$attendanceRecords" :show-header="true" />
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-calendar-xmark text-4xl mb-4"></i>
                    <p>No attendance records found for this month.</p>
                </div>
            @endif
        </div>

    </section>
</x-layouts.general-dashboard>