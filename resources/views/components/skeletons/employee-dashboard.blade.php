{{-- Skeleton: Employee Dashboard --}}
<div class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
    {{-- Left Panel --}}
    <div class="flex flex-col gap-6 flex-1 w-full">
        {{-- Hero Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <x-skeleton variant="circle" class="h-14 w-14" />
                <div class="flex-1 space-y-2">
                    <x-skeleton class="h-6 w-44" />
                    <x-skeleton class="h-3 w-64" />
                </div>
            </div>
        </div>

        {{-- Calendar --}}
        <x-skeleton class="h-4 w-24" />
        <div class="border border-dashed rounded-lg border-gray-300 dark:border-gray-700 p-4">
            <x-skeletons.calendar />
        </div>

        {{-- Task Cards --}}
        <div class="space-y-4">
            <x-skeleton class="h-5 w-28" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @for($i = 0; $i < 4; $i++)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-100 dark:border-gray-700 space-y-3">
                        <div class="flex items-center justify-between">
                            <x-skeleton class="h-4 w-2/3" />
                            <x-skeleton class="h-5 w-16 rounded-full" />
                        </div>
                        <x-skeleton class="h-3 w-full" />
                        <x-skeleton class="h-3 w-1/2" />
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="flex flex-col gap-6 w-full lg:w-80">
        {{-- Attendance --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 space-y-3">
            <x-skeleton class="h-5 w-24" />
            <x-skeleton class="h-10 w-full rounded-lg" />
            <x-skeleton class="h-3 w-32" />
        </div>

        {{-- Upcoming --}}
        <div class="space-y-3">
            <x-skeleton class="h-5 w-28" />
            @for($i = 0; $i < 3; $i++)
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                    <x-skeleton variant="rounded" class="h-10 w-10" />
                    <div class="flex-1 space-y-1.5">
                        <x-skeleton class="h-3.5 w-24" />
                        <x-skeleton class="h-2.5 w-16" />
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
