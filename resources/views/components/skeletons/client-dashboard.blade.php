{{-- Skeleton: Client Dashboard --}}
<div class="flex flex-col gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
    {{-- Hero Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-4">
            <x-skeleton variant="circle" class="h-14 w-14" />
            <div class="flex-1 space-y-2">
                <x-skeleton class="h-6 w-40" />
                <x-skeleton class="h-3 w-56" />
            </div>
            <x-skeleton class="h-9 w-32 rounded-lg" />
        </div>
    </div>

    {{-- Stats --}}
    <x-skeletons.stats-cards :count="3" />

    {{-- Filter/Sort bar --}}
    <div class="flex items-center justify-between">
        <x-skeleton class="h-5 w-36" />
        <div class="flex gap-3">
            <x-skeleton class="h-9 w-48 rounded-lg" />
            <x-skeleton class="h-9 w-32 rounded-lg" />
        </div>
    </div>

    {{-- Appointment Cards --}}
    <div class="space-y-4">
        @for($i = 0; $i < 4; $i++)
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                <div class="flex items-start gap-4">
                    <x-skeleton variant="rounded" class="h-14 w-14 flex-shrink-0" />
                    <div class="flex-1 space-y-2">
                        <div class="flex justify-between items-start">
                            <x-skeleton class="h-5 w-48" />
                            <x-skeleton class="h-5 w-20 rounded-full" />
                        </div>
                        <x-skeleton class="h-3 w-full" />
                        <div class="flex gap-6">
                            <x-skeleton class="h-3 w-28" />
                            <x-skeleton class="h-3 w-24" />
                        </div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>
