{{-- Skeleton: Card Grid (e.g. templates, training videos) --}}
@props(['count' => 8, 'cols' => 4])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $cols }} gap-4">
    @for($i = 0; $i < $count; $i++)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Card image area --}}
            <x-skeleton class="h-40 w-full rounded-none" />

            {{-- Card content --}}
            <div class="p-4 space-y-3">
                <x-skeleton class="h-5 w-3/4" />
                <div class="space-y-2">
                    <x-skeleton class="h-3 w-full" />
                    <x-skeleton class="h-3 w-5/6" />
                </div>
                <div class="flex gap-2 pt-1">
                    <x-skeleton class="h-5 w-14 rounded-full" />
                    <x-skeleton class="h-5 w-18 rounded-full" />
                </div>
            </div>
        </div>
    @endfor
</div>
