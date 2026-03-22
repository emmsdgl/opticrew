{{-- Skeleton: Activity History --}}
<div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit">
    <div class="flex-1">
        <div class="space-y-8">
            {{-- Header --}}
            <div class="flex items-center gap-4 mb-6">
                <x-skeleton variant="circle" class="h-12 w-12" />
                <x-skeleton class="h-7 w-48" />
            </div>

            {{-- Tabs --}}
            <div class="flex space-x-8 border-b border-gray-200 dark:border-gray-700 pb-1">
                @for($i = 0; $i < 4; $i++)
                    <x-skeleton class="h-4 w-16" />
                @endfor
            </div>

            {{-- Activity Cards --}}
            <div class="space-y-4">
                @for($i = 0; $i < 5; $i++)
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-start gap-4">
                            <x-skeleton variant="rounded" class="h-12 w-12 flex-shrink-0" />
                            <div class="flex-1 space-y-2.5">
                                <div class="flex justify-between">
                                    <x-skeleton class="h-4 w-40" />
                                    <x-skeleton class="h-5 w-20 rounded-full" />
                                </div>
                                <x-skeleton class="h-3 w-full" />
                                <x-skeleton class="h-3 w-2/3" />
                                <div class="flex gap-4 pt-1">
                                    <x-skeleton class="h-3 w-24" />
                                    <x-skeleton class="h-3 w-20" />
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
