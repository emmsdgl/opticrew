{{-- Skeleton: Table with header and rows --}}
@props(['rows' => 6, 'cols' => 5])

<div class="space-y-6">
    {{-- Stats Cards --}}
    <x-skeletons.stats-cards :count="4" />

    {{-- Table Header Area --}}
    <div class="flex items-center justify-between">
        <x-skeleton class="h-5 w-40" />
        <div class="flex gap-3">
            <x-skeleton class="h-9 w-48 rounded-lg" />
            <x-skeleton class="h-9 w-28 rounded-lg" />
        </div>
    </div>

    {{-- Table --}}
    <div class="w-full overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        {{-- Table Head --}}
        <div class="flex gap-4 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
            @for($c = 0; $c < $cols; $c++)
                <x-skeleton class="h-3 flex-1" />
            @endfor
        </div>

        {{-- Table Rows --}}
        @for($r = 0; $r < $rows; $r++)
            <div class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
                <x-skeleton variant="circle" class="h-8 w-8 flex-shrink-0" />
                @for($c = 1; $c < $cols; $c++)
                    <x-skeleton class="h-3 flex-1" />
                @endfor
            </div>
        @endfor
    </div>
</div>
