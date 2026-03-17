{{-- Skeleton: Performance Page --}}
<div class="flex lg:flex-row gap-6 w-full">
    {{-- Left Panel - Charts --}}
    <div class="flex flex-col gap-6 flex-1 w-full border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <x-skeleton class="h-5 w-36" />
            <x-skeleton class="h-8 w-28 rounded-lg" />
        </div>

        {{-- Line Chart --}}
        <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg h-72 p-4 flex items-end gap-3">
            @php $heights = [30, 55, 45, 70, 50, 65, 40, 75, 55, 80]; @endphp
            @foreach($heights as $h)
                <x-skeleton variant="rounded" class="flex-1" style="height: {{ $h }}%" />
            @endforeach
        </div>

        {{-- Stats Row --}}
        <div class="grid grid-cols-3 gap-4">
            @for($i = 0; $i < 3; $i++)
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-100 dark:border-gray-700 space-y-2">
                    <x-skeleton class="h-3 w-20" />
                    <x-skeleton class="h-6 w-16" />
                </div>
            @endfor
        </div>

        {{-- Secondary Chart --}}
        <x-skeletons.chart />
    </div>

    {{-- Right Panel --}}
    <div class="flex flex-col gap-6 w-full lg:w-80">
        {{-- Rating --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 space-y-3">
            <x-skeleton class="h-5 w-28" />
            <div class="flex items-center justify-center py-4">
                <x-skeleton variant="circle" class="h-24 w-24" />
            </div>
            <x-skeleton class="h-3 w-full" />
        </div>

        {{-- Recent Tasks --}}
        <div class="space-y-3">
            <x-skeleton class="h-5 w-28" />
            @for($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                    <x-skeleton variant="rounded" class="h-8 w-8" />
                    <div class="flex-1 space-y-1.5">
                        <x-skeleton class="h-3 w-28" />
                        <x-skeleton class="h-2.5 w-16" />
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
