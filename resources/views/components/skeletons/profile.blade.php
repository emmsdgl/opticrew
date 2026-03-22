{{-- Skeleton: Profile / Settings Page --}}
<div class="flex flex-col gap-6 p-4 md:p-6 max-w-4xl mx-auto w-full">
    {{-- Profile Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex flex-col items-center gap-4 sm:flex-row">
            <x-skeleton variant="circle" class="h-20 w-20" />
            <div class="flex-1 space-y-2 text-center sm:text-left">
                <x-skeleton class="h-6 w-40 mx-auto sm:mx-0" />
                <x-skeleton class="h-3 w-32 mx-auto sm:mx-0" />
                <x-skeleton class="h-3 w-48 mx-auto sm:mx-0" />
            </div>
            <x-skeleton class="h-9 w-24 rounded-lg" />
        </div>
    </div>

    {{-- Form Fields --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700 space-y-6">
        <x-skeleton class="h-5 w-36" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @for($i = 0; $i < 6; $i++)
                <div class="space-y-2">
                    <x-skeleton class="h-3 w-20" />
                    <x-skeleton class="h-10 w-full rounded-lg" />
                </div>
            @endfor
        </div>

        <div class="flex justify-end">
            <x-skeleton class="h-10 w-28 rounded-lg" />
        </div>
    </div>
</div>
