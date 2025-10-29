@props([
    'navOptions' => [], // Default to an empty array
    'teams' => []       // Default to an empty array
])

<!-- Mobile Overlay Backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden transition-opacity duration-300"></div>

<!-- SIDEBAR -->
<aside id="sidebar" class="fixed left-0 top-0 h-screen w-64 bg-[#FFFFFF] border-r border-[#D1D1D1] dark:bg-[#1E293B] dark:border-[#334155]
              flex flex-col justify-between transition-all duration-300 p-5 overflow-y-auto z-40
              -translate-x-full lg:translate-x-0">

    <div>
        <!-- Logo + toggle -->
        <div id="sidebar-header" class="flex items-center justify-between h-20 px-4 border-b border-[#D1D1D1] dark:border-[#334155] transition-all duration-300">
            <div class="flex items-center justify-center flex-1">
                <a href="{{ Auth::check() && Auth::user()->role === 'admin' ? route('admin.dashboard') : route('employee.dashboard') }}"
                    class="flex items-center justify-center">
                    <!-- Light Mode Logo (visible by default, hidden in dark mode) -->

                    <img src="{{ asset('images/opticrew-logo-dark.svg') }}"
                        class="block dark:hidden h-8 w-auto sidebar-logo"
                        alt="OptiCrew Light Logo">

                    <!-- Dark Mode Logo (hidden by default, visible in dark mode) -->
                    <img src="{{ asset('images/opticrew-logo-light.svg') }}"
                        class="hidden dark:block h-8 w-auto sidebar-logo"
                        alt="OptiCrew Dark Logo">
                </a>
            </div>

            <!-- Mobile Close Button -->
            <button id="mobile-sidebar-close" class="lg:hidden text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md p-2 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Desktop Collapse Toggle -->
            <button id="sidebar-toggle" class="hidden lg:block text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md p-1 transition-all duration-300 flex-shrink-0">
                <i class="fi fi-rr-angle-small-left text-lg"></i>
            </button>
        </div>

        <!-- Nav -->
        <nav class="mt-6 space-y-1">
                @foreach($navOptions as $nav)
                    @php
                        // Check if current route matches this nav item
                        $isActive = request()->url() === $nav['href'] ||
                                    (isset($nav['href']) && request()->is(trim(parse_url($nav['href'], PHP_URL_PATH), '/').'*'));
                    @endphp
                    <a href="{{ $nav['href'] ?? '#' }}"
                       class="flex items-center px-3 py-3.5 text-sm font-medium rounded-lg transition-colors duration-200
                              {{ $isActive ? 'bg-[#2A6DFA] text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-[#2A6DFA] hover:text-white' }}">
                        <i class="fa-solid {{ $nav['icon'] }} w-5"></i>
                        <span class="ml-8 nav-label">{{ $nav['label'] }}</span>
                    </a>
                @endforeach

                @php
                    // Filter out empty teams
                    $validTeams = array_filter($teams, function($team) {
                        return !empty(trim($team));
                    });
                @endphp

                @if(count($validTeams) > 0)
                <div class="flex flex-col mt-6 gap-0">
                    <!-- Teams heading -->
                    <div class="mt-6 space-y-1">
                        <div class="mb-6 px-3 text-xs text-gray-400 dark:text-gray-500 uppercase">
                            <span class="nav-label">Your Teams</span>
                        </div>
                        @foreach($validTeams as $team)
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
                @endif
        </nav>
    </div>
</aside>