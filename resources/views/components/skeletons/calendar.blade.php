{{-- Skeleton: Calendar --}}
<div class="space-y-4">
    {{-- Calendar header --}}
    <div class="flex items-center justify-between">
        <x-skeleton class="h-6 w-32" />
        <div class="flex gap-2">
            <x-skeleton variant="circle" class="h-8 w-8" />
            <x-skeleton variant="circle" class="h-8 w-8" />
        </div>
    </div>

    {{-- Day headers --}}
    <div class="grid grid-cols-7 gap-1">
        @for($d = 0; $d < 7; $d++)
            <x-skeleton class="h-4 mx-auto w-8" />
        @endfor
    </div>

    {{-- Calendar grid --}}
    <div class="grid grid-cols-7 gap-1">
        @for($i = 0; $i < 35; $i++)
            <div class="aspect-square p-1">
                <x-skeleton variant="rounded" class="h-full w-full" />
            </div>
        @endfor
    </div>
</div>
