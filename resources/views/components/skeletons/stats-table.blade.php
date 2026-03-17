{{-- Skeleton: Stats Cards + Table (Accounts, Attendance, Appointments) --}}
<div class="space-y-6 p-4 md:p-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <x-skeleton class="h-6 w-40" />
        <div class="flex gap-2">
            <x-skeleton class="h-9 w-28 rounded-lg" />
            <x-skeleton class="h-9 w-28 rounded-lg" />
        </div>
    </div>

    {{-- Stats --}}
    <x-skeletons.stats-cards :count="4" />

    {{-- Search + Filters --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <x-skeleton class="h-5 w-32" />
        <div class="flex gap-3">
            <x-skeleton class="h-9 w-56 rounded-lg" />
            <x-skeleton class="h-9 w-32 rounded-lg" />
        </div>
    </div>

    {{-- Table --}}
    <div class="w-full overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex gap-4 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
            @for($c = 0; $c < 6; $c++)
                <x-skeleton class="h-3 flex-1" />
            @endfor
        </div>
        @for($r = 0; $r < 8; $r++)
            <div class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
                <x-skeleton variant="circle" class="h-8 w-8 flex-shrink-0" />
                @for($c = 0; $c < 5; $c++)
                    <x-skeleton class="h-3 flex-1" />
                @endfor
            </div>
        @endfor
    </div>
</div>
