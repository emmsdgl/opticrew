{{-- Skeleton: Stats/KPI Cards Row --}}
@props(['count' => 4])

<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @for($i = 0; $i < $count; $i++)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <x-skeleton variant="rounded" class="h-10 w-10 flex-shrink-0" />
                <div class="flex-1 space-y-2">
                    <x-skeleton class="h-3 w-20" />
                    <x-skeleton class="h-6 w-12" />
                </div>
            </div>
            <x-skeleton class="h-2.5 w-full mt-3" />
        </div>
    @endfor
</div>
