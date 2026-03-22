{{-- Skeleton: Default/Fallback --}}
<div class="space-y-6 p-4 md:p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="space-y-2">
            <x-skeleton class="h-7 w-48" />
            <x-skeleton class="h-3 w-64" />
        </div>
        <x-skeleton class="h-9 w-28 rounded-lg" />
    </div>

    {{-- Stats --}}
    <x-skeletons.stats-cards :count="3" />

    {{-- Content --}}
    <div class="space-y-4">
        @for($i = 0; $i < 4; $i++)
            <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                <x-skeleton variant="circle" class="h-10 w-10 flex-shrink-0" />
                <div class="flex-1 space-y-2">
                    <x-skeleton class="h-4 w-1/3" />
                    <x-skeleton class="h-3 w-2/3" />
                </div>
                <x-skeleton class="h-8 w-20 rounded-lg" />
            </div>
        @endfor
    </div>
</div>
