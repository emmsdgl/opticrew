{{-- Skeleton: Chart area --}}
<div class="space-y-4">
    {{-- Chart header --}}
    <div class="flex items-center justify-between">
        <div class="space-y-2">
            <x-skeleton class="h-5 w-32" />
            <x-skeleton class="h-3 w-24" />
        </div>
        <x-skeleton class="h-8 w-28 rounded-lg" />
    </div>

    {{-- Chart area --}}
    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4 h-72 flex items-end gap-3">
        @php $heights = [40, 65, 50, 80, 55, 70, 45, 75, 60, 85, 50, 70]; @endphp
        @foreach($heights as $h)
            <x-skeleton variant="rounded" class="flex-1" style="height: {{ $h }}%" />
        @endforeach
    </div>
</div>
