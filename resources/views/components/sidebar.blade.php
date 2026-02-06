@props([
    'navOptions' => [], // Default to an empty array
    'teams' => []       // Default to an empty array
])

<!-- Mobile Overlay Backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden transition-opacity duration-300"></div>

<!-- Desktop Toggle Button (beside sidebar) -->
<button id="sidebar-toggle" class="hidden lg:block fixed top-6 z-50 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md p-2 shadow-md transition-all duration-300 border border-gray-200 dark:border-gray-700" style="left: 272px;">
    <i class="fa-solid fa-bars text-lg"></i>
</button>

<!-- SIDEBAR -->
<aside id="sidebar" class="fixed left-0 top-0 h-screen w-64 bg-[#FFFFFF] border-r border-[#D1D1D1] dark:bg-[#1E293B] dark:border-[#334155]
              flex flex-col justify-between transition-all duration-300 overflow-y-auto z-40
              -translate-x-full lg:translate-x-0">

    <div>
        <!-- Logo -->
        <div id="sidebar-header" class="flex items-center h-28 px-6 transition-all duration-300">
            <div class="flex-1 flex items-center justify-center">
                <a href="{{ Auth::check() && Auth::user()->role === 'admin' ? route('admin.dashboard') : route('employee.dashboard') }}"
                    class="flex items-center justify-center">
                    <!-- Light Mode Logo (visible by default, hidden in dark mode) -->
                    <img src="{{ asset('images/finnoys-text-logo-light.svg') }}"
                        class="block dark:hidden h-24 w-auto sidebar-logo"
                        alt="Finnoys Light Logo">

                    <!-- Dark Mode Logo (hidden by default, visible in dark mode) -->
                    <img src="{{ asset('images/finnoys-text-logo.svg') }}"
                        class="hidden dark:block h-24 w-auto sidebar-logo"
                        alt="Finnoys Dark Logo">
                </a>
            </div>

            <!-- Mobile Close Button -->
            <button id="mobile-sidebar-close" class="lg:hidden text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md p-2 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Nav -->
        <nav class="mt-6 space-y-1 px-5" x-data="{ openDropdowns: {} }">
                @foreach($navOptions as $index => $nav)
                    @if(isset($nav['children']) && count($nav['children']) > 0)
                        {{-- Dropdown Menu Item --}}
                        @php
                            $hasActiveChild = false;
                            foreach ($nav['children'] as $child) {
                                $childPath = isset($child['href']) ? trim(parse_url($child['href'], PHP_URL_PATH), '/') : '';
                                if (isset($child['href']) && (
                                    request()->url() === $child['href'] ||
                                    request()->is($childPath) ||
                                    request()->is($childPath.'/*')
                                )) {
                                    $hasActiveChild = true;
                                    break;
                                }
                            }
                        @endphp
                        <div x-data="{ open: {{ $hasActiveChild ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                   class="w-full flex items-center justify-between px-3 py-3.5 text-sm font-medium rounded-lg transition-colors duration-200
                                          {{ $hasActiveChild ? 'bg-[#2A6DFA] text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                <div class="flex items-center">
                                    <i class="fa-solid {{ $nav['icon'] }} w-5"></i>
                                    <span class="ml-8 nav-label">{{ $nav['label'] }}</span>
                                </div>
                                <i class="fa-solid fa-chevron-down nav-label transition-transform duration-200"
                                   :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-1"
                                 class="mt-1 ml-5 space-y-1">
                                @foreach($nav['children'] as $child)
                                    @php
                                        $childPath = isset($child['href']) ? trim(parse_url($child['href'], PHP_URL_PATH), '/') : '';
                                        $isChildActive = isset($child['href']) && (
                                            request()->url() === $child['href'] ||
                                            request()->is($childPath) ||
                                            request()->is($childPath.'/*')
                                        );
                                    @endphp
                                    <a href="{{ $child['href'] ?? '#' }}"
                                       class="flex items-center px-3 py-2.5 text-sm rounded-lg transition-colors duration-200
                                              {{ $isChildActive ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                        <i class="fa-solid {{ $child['icon'] ?? 'fa-circle' }} text-xs w-4"></i>
                                        <span class="ml-6 nav-label">{{ $child['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        {{-- Regular Menu Item --}}
                        @php
                            $navPath = isset($nav['href']) ? trim(parse_url($nav['href'], PHP_URL_PATH), '/') : '';
                            $isActive = isset($nav['href']) && (
                                request()->url() === $nav['href'] ||
                                request()->is($navPath) ||
                                request()->is($navPath.'/*')
                            );
                        @endphp
                        <a href="{{ $nav['href'] ?? '#' }}"
                           class="flex items-center px-3 py-3.5 text-sm font-medium rounded-lg transition-colors duration-200
                                  {{ $isActive ? 'bg-[#2A6DFA] text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-[#2A6DFA] hover:text-white' }}">
                            <i class="fa-solid {{ $nav['icon'] }} w-5"></i>
                            <span class="ml-8 nav-label">{{ $nav['label'] }}</span>
                        </a>
                    @endif
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