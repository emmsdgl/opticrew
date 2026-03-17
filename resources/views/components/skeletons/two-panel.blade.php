{{-- Skeleton: Two Panel Layout (Analytics) --}}
<div class="flex flex-col lg:flex-row md:gap-4 w-full">
    {{-- Left Panel --}}
    <div class="flex flex-col gap-6 w-full rounded-lg p-3 h-fit lg:w-2/3 md:p-3">
        {{-- Header + Export --}}
        <div class="flex items-center justify-between">
            <x-skeleton class="h-5 w-36" />
            <x-skeleton class="h-9 w-32 rounded-lg" />
        </div>

        {{-- Stats Cards --}}
        <x-skeletons.stats-cards :count="4" />

        {{-- Chart --}}
        <x-skeletons.chart />

        {{-- Secondary Chart --}}
        <x-skeletons.chart />
    </div>

    {{-- Right Panel --}}
    <div class="flex flex-col gap-6 w-full lg:w-1/3 p-3">
        <x-skeleton class="h-5 w-28" />

        {{-- Donut chart placeholder --}}
        <div class="flex items-center justify-center p-8">
            <x-skeleton variant="circle" class="h-48 w-48" />
        </div>

        {{-- Legend items --}}
        <div class="space-y-3">
            @for($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-3">
                    <x-skeleton variant="circle" class="h-3 w-3 flex-shrink-0" />
                    <x-skeleton class="h-3 flex-1" />
                    <x-skeleton class="h-3 w-8" />
                </div>
            @endfor
        </div>
    </div>
</div>
