{{-- Skeleton: Recruitment / Job Applications --}}
<div class="w-full flex flex-col gap-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2 mb-2 px-8">
        <x-skeleton class="h-7 w-48" />
        <x-skeleton class="h-3.5 w-72" />
    </div>

    {{-- Stats Cards --}}
    <div class="py-10 px-8">
        <x-skeletons.stats-cards :count="5" />
    </div>

    {{-- Filters --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mx-4">
        <x-skeleton class="h-5 w-32" />
        <div class="flex gap-3 flex-1 md:max-w-3xl">
            <x-skeleton class="h-9 flex-1 rounded-lg" />
            <x-skeleton class="h-9 w-36 rounded-lg" />
            <x-skeleton class="h-9 w-28 rounded-lg" />
        </div>
    </div>

    {{-- Application Table --}}
    <div class="mx-4">
        <div class="w-full overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex gap-4 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                @for($c = 0; $c < 7; $c++)
                    <x-skeleton class="h-3 flex-1" />
                @endfor
            </div>
            @for($r = 0; $r < 6; $r++)
                <div class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
                    <x-skeleton variant="circle" class="h-8 w-8 flex-shrink-0" />
                    @for($c = 0; $c < 6; $c++)
                        <x-skeleton class="h-3 flex-1" />
                    @endfor
                </div>
            @endfor
        </div>
    </div>
</div>
