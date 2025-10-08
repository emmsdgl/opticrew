@props([
    'nav_option_1' => null,
    'nav_option_2' => null,
    'nav_option_3' => null,
    'nav_option_4' => null,
    'nav_option_5' => null,
    'nav_option_6' => null,
    'team_name_1' => null,
    'team_name_2' => null,
    'team_name_3' => null,
])

<!-- SIDEBAR -->
<aside id="sidebar" class="w-64 bg-[#FFFFFF] border-r border-[#D1D1D1] dark:bg-[#1E293B] dark:border-[#334155]
              flex flex-col justify-between transition-all duration-300 p-5">

    <div>
        <!-- Logo + toggle -->
        <div class="flex items-center justify-between h-20 px-4 border-b border-[#D1D1D1] dark:border-[#334155]">
            <div class="flex items-center space-x-3">
                <img src="/public/images/finnoys-text-logo.svg"
                    class="h-20 flex flex-col justify-center w-full sidebar-logo" alt="logo">
            </div>

            <button id="sidebar-toggle" class="text-gray-600 dark:text-gray-300 hover:text-indigo-500">
                <i class="fi fi-rr-angle-small-left text-lg"></i>
            </button>
        </div>

        <!-- Nav -->
        <nav class="mt-6 space-y-1">
            <a href="#"
                class="flex items-center justify-start px-3 py-3.5 text-sm font-medium rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-800 transition-all">
                <i class="fa-solid fa-house text-lg"></i>
                <span class="ml-8 nav-label">{{$nav_option_1}}</span>
            </a>

            <a href="#"
                class="flex items-center px-3 py-3 text-sm font-medium rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-800">
                <i class="fa-solid fa-users w-5"></i>
                <span class="ml-8 nav-label">{{$nav_option_2}}</span>
            </a>

            <a href="#"
                class="flex items-center px-3 py-3 text-sm font-medium rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-800">
                <i class="fa-solid fa-folder w-5"></i>
                <span class="ml-8 nav-label">{{$nav_option_3}}</span>
            </a>

            <a href="#"
                class="flex items-center px-3 py-3 text-sm font-medium rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-800">
                <i class="fa-solid fa-calendar w-5"></i>
                <span class="ml-8 nav-label">{{$nav_option_4}}</span>
            </a>

            <a href="#"
                class="flex items-center px-3 py-3 text-sm font-medium rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-800">
                <i class="fa-solid fa-chart-line w-5"></i>
                <span class="ml-8 nav-label">{{$nav_option_5}}</span>
            </a>

            <a href="#"
                class="flex items-center px-3 py-3 text-sm font-medium rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-800">
                <i class="fa-solid fa-file-lines w-5"></i>
                <span class="ml-8 nav-label">{{$nav_option_6}}</span>
            </a>

            <div class="flex flex-col mt-6 gap-0">
                <!-- Teams heading -->

                <div class="mt-6 space-y-1">
                    <div class="mb-6 px-3 text-xs text-gray-400 dark:text-gray-500 uppercase">
                        <span class="nav-label">My Teams</span>
                    </div>
                    <a href="#"
                        class="flex items-center px-3 py-2 text-sm rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-800">
                        <span
                            class="bg-gray-200 dark:bg-gray-700 w-6 h-6 flex items-center justify-center rounded-full text-xs">H</span>
                        <span class="nav-label ml-8">{{ $team_name_1}}</span>
                    </a>

                    <a href="#"
                        class="flex items-center px-3 py-2 text-sm rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-800">
                        <span
                            class="bg-gray-200 dark:bg-gray-700 w-6 h-6 flex items-center justify-center rounded-full text-xs">T</span>
                        <span class="nav-label ml-8">{{$team_name_2}}</span>
                    </a>

                    <a href="#"
                        class="flex items-center px-3 py-2 text-sm rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-800">
                        <span
                            class="bg-gray-200 dark:bg-gray-700 w-6 h-6 flex items-center justify-center rounded-full text-xs">W</span>
                        <span class="nav-label ml-8">{{$team_name_3}}</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Footer -->
    <div class="border-t border-[#D1D1D1] dark:border-[#334155]">
        <a href="#" class="flex items-center m-3 text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-500"> <i
                class="fa-solid fa-arrow-right-from-bracket w-5"></i> <span class="logout-label ml-8">Logout</span>
        </a>
    </div>
</aside>