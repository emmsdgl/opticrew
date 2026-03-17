{{-- Skeleton: Training Management --}}
<div class="w-full flex flex-col gap-4 p-4 md:p-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2 mb-2">
        <x-skeleton class="h-7 w-52" />
        <x-skeleton class="h-3.5 w-80" />
    </div>

    {{-- Stats Cards --}}
    <div class="my-8">
        <x-skeletons.stats-cards :count="4" />
    </div>

    {{-- Section Header + Add Button --}}
    <div class="flex items-center justify-between">
        <x-skeleton class="h-5 w-36" />
        <x-skeleton class="h-9 w-28 rounded-lg" />
    </div>

    {{-- Video Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @for($i = 0; $i < 6; $i++)
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Video thumbnail --}}
                <x-skeleton class="h-44 w-full rounded-none" />
                <div class="p-4 space-y-3">
                    <x-skeleton class="h-4 w-3/4" />
                    <x-skeleton class="h-3 w-full" />
                    <div class="flex items-center justify-between pt-2">
                        <x-skeleton class="h-5 w-20 rounded-full" />
                        <div class="flex gap-2">
                            <x-skeleton variant="circle" class="h-7 w-7" />
                            <x-skeleton variant="circle" class="h-7 w-7" />
                        </div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>
