{{-- Skeleton: Admin Dashboard --}}
<div class="flex flex-col lg:flex-row gap-6 p-4 md:p-6 flex-1">
    {{-- Left Panel --}}
    <div class="flex flex-col gap-6 flex-1 w-full">
        {{-- Hero Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <x-skeleton variant="circle" class="h-14 w-14" />
                <div class="flex-1 space-y-2">
                    <x-skeleton class="h-6 w-48" />
                    <x-skeleton class="h-3 w-72" />
                </div>
            </div>
        </div>

        {{-- Calendar Label --}}
        <x-skeleton class="h-4 w-24" />

        {{-- Calendar --}}
        <div class="border border-dashed rounded-lg border-gray-300 dark:border-gray-700 p-4">
            <x-skeletons.calendar />
        </div>

        {{-- Task Overview --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <x-skeleton class="h-5 w-32" />
                <x-skeleton class="h-8 w-24 rounded-lg" />
            </div>

            {{-- Task cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @for($i = 0; $i < 4; $i++)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-100 dark:border-gray-700 space-y-3">
                        <x-skeleton class="h-4 w-3/4" />
                        <x-skeleton class="h-3 w-full" />
                        <x-skeleton class="h-3 w-1/2" />
                        <x-skeleton class="h-6 w-16 rounded-full" />
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="flex flex-col gap-6 w-full lg:w-80">
        {{-- Upcoming section --}}
        <div class="space-y-3">
            <x-skeleton class="h-5 w-28" />
            @for($i = 0; $i < 3; $i++)
                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
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
