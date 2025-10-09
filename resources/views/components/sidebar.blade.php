@props([
    'navOptions' => [], // Default to an empty array
    'teams' => []       // Default to an empty array
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
 
                    <button id="sidebar-toggle" class="text-gray-600 dark:text-gray-300 hover:hover:bg-[#2A6DFA]">
                    <i class="fi fi-rr-angle-small-left text-lg"></i>
                </button>
                </div>

        <!-- Nav -->
        <nav class="mt-6 space-y-1">
                @foreach($navOptions as $nav)
                    <a href="{{ $nav['href'] ?? '#' }}"
                       class="flex items-center px-3 py-3.5 text-sm font-medium rounded-lg hover:bg-[#2A6DFA] text-white dark:hover:bg-[#2A6DFA] text-">
                        <i class="fa-solid {{ $nav['icon'] }} w-5"></i>
                        <span class="ml-8 nav-label">{{ $nav['label'] }}</span>
                    </a>
                @endforeach
                
                <div class="flex flex-col mt-6 gap-0">
                <!-- Teams heading -->
                <div class="mt-6 space-y-1">
                    <div class="mb-6 px-3 text-xs text-gray-400 dark:text-gray-500 uppercase">
                        <span class="nav-label">Your Teams</span>
                    </div>
                    @foreach($teams as $team)
                        <a href="#"
                        class="flex items-center px-3 py-2 text-sm rounded-md hover:bg-[#2A6DFA] dark:hover:bg-[#2A6DFA]">
                            <span class="bg-gray-200 dark:bg-gray-700 w-6 h-6 flex items-center justify-center rounded-full text-xs">
                                {{ strtoupper(substr($team, 0, 1)) }}
                            </span>
                            <span class="nav-label ml-8">{{ $team }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>
    </div>

    <!-- Footer -->
    <div class="border-t border-[#D1D1D1] dark:border-[#334155]">
        <a href="#" class="flex items-center m-3 text-sm text-gray-500 dark:text-gray-400 hover:hover:bg-[#2A6DFA]"> <i
                class="fa-solid fa-arrow-right-from-bracket w-5"></i> <span class="logout-label ml-8">Logout</span>
        </a>
    </div>
</aside>