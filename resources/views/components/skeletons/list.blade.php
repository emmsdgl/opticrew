{{-- Skeleton: List items (e.g. activity cards, appointments) --}}
@props(['count' => 5])

<div class="space-y-4">
    @for($i = 0; $i < $count; $i++)
        <div class="flex items-start gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
            <x-skeleton variant="circle" class="h-10 w-10 flex-shrink-0" />
            <div class="flex-1 space-y-2">
                <x-skeleton class="h-4 w-2/3" />
                <x-skeleton class="h-3 w-full" />
                <x-skeleton class="h-3 w-1/2" />
            </div>
            <x-skeleton class="h-6 w-16 rounded-full flex-shrink-0" />
        </div>
    @endfor
</div>
