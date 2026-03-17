@props([
    'user',
    'totalApps'   => 0,
    'pendingApps' => 0,
    'hiredApps'   => 0,
])

<div class="w-full bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

    {{-- Cover Photo --}}
    <div class="relative h-24 bg-gray-100 dark:bg-gray-700">
        {{-- Empty cover placeholder --}}
        <div class="absolute inset-0 flex items-center justify-center">
            <i class="fa-regular fa-image text-2xl text-gray-300 dark:text-gray-600"></i>
        </div>
        {{-- + button --}}
        <button class="absolute top-3 right-3 w-7 h-7 rounded-full bg-white dark:bg-gray-800 shadow-md flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 hover:scale-110 transition-all">
            <i class="fa-solid fa-plus text-xs"></i>
        </button>
    </div>

    {{-- Avatar (overlapping cover) --}}
    <div class="flex justify-center -mt-9 px-4">
        <div class="p-[3px] rounded-full bg-gradient-to-br from-blue-400 via-purple-400 to-pink-400 shadow-md ring-2 ring-white dark:ring-gray-800">
            <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <i class="fa-solid fa-user text-2xl text-gray-400 dark:text-gray-500"></i>
            </div>
        </div>
    </div>

    {{-- Info --}}
    <div class="px-5 pt-3 pb-5 text-center">
        <h3 class="font-bold text-gray-900 dark:text-white text-base leading-tight">{{ $user->name }}</h3>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 truncate" title="{{ $user->email }}">{{ $user->email }}</p>

        {{-- Divider --}}
        <div class="my-4 border-t border-gray-100 dark:border-gray-700"></div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-700">
            <div class="flex flex-col items-center px-2 gap-0.5">
                <span class="font-bold text-base text-gray-900 dark:text-white">{{ $totalApps }}</span>
                <span class="text-xs text-gray-400 dark:text-gray-500">Applied</span>
            </div>
            <div class="flex flex-col items-center px-2 gap-0.5">
                <span class="font-bold text-base text-yellow-500 dark:text-yellow-400">{{ $pendingApps }}</span>
                <span class="text-xs text-gray-400 dark:text-gray-500">Pending</span>
            </div>
            <div class="flex flex-col items-center px-2 gap-0.5">
                <span class="font-bold text-base text-green-500 dark:text-green-400">{{ $hiredApps }}</span>
                <span class="text-xs text-gray-400 dark:text-gray-500">Hired</span>
            </div>
        </div>
    </div>

</div>
