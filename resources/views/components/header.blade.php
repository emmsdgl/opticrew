@props([
    'notifications' => []
])

<!-- Topbar -->
<header class="flex items-center justify-between px-3 sm:px-6 h-24 border-b border-gray-50 dark:border-gray-700">
    <div class="flex items-center gap-2 sm:gap-4 flex-1 min-w-0">
        <!-- Mobile Menu Button (Hamburger) -->
        <button id="mobile-menu-toggle" class="lg:hidden text-gray-600 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 transition-colors p-2 flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <div class="flex items-center gap-2 sm:gap-4 md:gap-6 flex-shrink-0">       

        <!-- Notification Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="relative bg-blue-100 dark:bg-blue-900/30 px-3 py-2 rounded-full transition hover:bg-gray-300 dark:hover:bg-gray-700">
                <i class="fi fi-rr-bell text-blue-600 dark:text-blue-300 text-base"></i>

                <!-- Notification Badge -->
                @if(count($notifications) > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                    {{ count($notifications) > 9 ? '9+' : count($notifications) }}
                </span>
                @endif
            </button>

            <!-- Notification Dropdown Panel -->
            <div x-show="open"
                 x-cloak
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">

                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                    @if(count($notifications) > 0)
                    <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Mark all as read</button>
                    @endif
                </div>

                <!-- Notification List -->
                <div class="max-h-80 overflow-y-auto">
                    @forelse($notifications as $notification)
                    <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors">
                        <div class="flex items-start gap-3">
                            @php
                                $icon = $notification['data']['icon'] ?? 'info';
                                $iconMap = [
                                    'star' => 'fi-rr-star',
                                    'check-circle' => 'fi-rr-checkbox',
                                    'times-circle' => 'fi-rr-cross-circle',
                                    'play-circle' => 'fi-rr-play',
                                    'clipboard-check' => 'fi-rr-checklist',
                                    'clipboard-list' => 'fi-rr-list',
                                    'users' => 'fi-rr-users',
                                    'calendar-plus' => 'fi-rr-calendar',
                                    'calendar-times' => 'fi-rr-calendar',
                                    'user-clock' => 'fi-rr-clock',
                                    'tasks' => 'fi-rr-ballot-check',
                                    'user-plus' => 'fi-rr-user-add',
                                    'info' => 'fi-rr-info',
                                ];
                                $iconClass = $iconMap[$icon] ?? 'fi-rr-info';
                                $color = $notification['data']['color'] ?? 'blue';
                                $colorClasses = [
                                    'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                                    'green' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                                    'yellow' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
                                    'red' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                                ];
                                $bgColorClass = $colorClasses[$color] ?? $colorClasses['blue'];
                            @endphp
                            <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $bgColorClass }} flex items-center justify-center">
                                <i class="fi {{ $iconClass }} text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $notification['title'] ?? 'Notification' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">
                                    {{ $notification['message'] ?? '' }}
                                </p>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $notification['time'] ?? 'Just now' }}
                                    </p>
                                    @if(isset($notification['data']['action_url']))
                                    <a href="{{ $notification['data']['action_url'] }}"
                                       class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                        {{ $notification['data']['action_text'] ?? 'View' }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-4 py-8 text-center">
                        <i class="fi fi-rr-bell-slash text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No notifications</p>
                    </div>
                    @endforelse
                </div>

                <!-- Footer -->
                @if(count($notifications) > 0)
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    
                </div>
                @endif
            </div>
        </div>

        <!-- Theme Toggle Button -->
        <button id="theme-toggle"
            class="relative bg-blue-100 dark:bg-blue-900/30 px-3 py-2 rounded-full transition rotate-once">
            <i id="theme-icon"
                class="fi fi-rr-brightness text-yellow-300 text-lg transition-transform duration-500"></i>
        </button>

        <!-- Profile Dropdown -->
        <div class="relative">
            <button id="profile-dropdown-toggle" class="flex items-center space-x-2 md:space-x-3 hover:opacity-80 transition-opacity">
                <div class="flex-shrink-0">
                    @auth
                        @if(auth()->user()->profile_picture)
                            @php
                                // Handle both old and new profile picture paths
                                $profilePic = auth()->user()->profile_picture;
                                $profileUrl = str_starts_with($profilePic, 'profile_pictures/')
                                    ? asset('storage/' . $profilePic)
                                    : asset($profilePic);
                            @endphp
                            <img src="{{ $profileUrl }}?v={{ time() }}" alt="User" class="w-10 h-10 md:w-8 md:h-8 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700" style="aspect-ratio: 1/1;">
                        @else
                            <img src="https://i.pravatar.cc/40" alt="User" class="w-10 h-10 md:w-8 md:h-8 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700" style="aspect-ratio: 1/1;">
                        @endif
                    @else
                        <img src="https://i.pravatar.cc/40" alt="Guest" class="w-10 h-10 md:w-8 md:h-8 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700" style="aspect-ratio: 1/1;">
                    @endauth
                </div>

                <span class="hidden md:inline text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">
                    @auth
                        {{ explode(' ', auth()->user()->name)[0] }}
                    @else
                        Guest
                    @endauth
                </span>

                <i id="caret-icon" class="hidden md:inline fa-solid fa-caret-down text-gray-400 dark:text-gray-500 transition-transform duration-200"></i>
            </button>

            <!-- Profile Dropdown Menu -->
            <div id="profile-dropdown" 
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible transition-all duration-200 transform origin-top-right scale-95 z-50">
                
                @auth
                    <!-- Menu Items -->
                    <div class="py-2">
                        <a href="{{
                            auth()->user()->role === 'admin' ? route('admin.profile') :
                            (auth()->user()->role === 'employee' ? route('employee.profile') :
                            route('client.profile'))
                        }}"
                            class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fa-regular fa-user w-5 text-gray-500 dark:text-gray-400"></i>
                            <span class="ml-3">Profile</span>
                        </a>
                                                
                        <a href="{{
                            auth()->user()->role === 'admin' ? route('admin.settings') :
                            (auth()->user()->role === 'employee' ? route('employee.settings') :
                            route('client.settings'))
                        }}"
                            class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fa-solid fa-gear w-5 text-gray-500 dark:text-gray-400"></i>
                            <span class="ml-3">Settings</span>
                        </a>

                        <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                        
                        
                        <a href="{{
                            auth()->user()->role === 'admin' ? route('admin.helpcenter') :
                            (auth()->user()->role === 'employee' ? route('employee.helpcenter') :
                            route('client.helpcenter'))
                        }}"
                            class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fa-regular fa-circle-question w-5 text-gray-500 dark:text-gray-400"></i>
                            <span class="ml-3">Help Center</span>
                        </a>


                        <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                class="flex items-center w-full px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fa-solid fa-arrow-right-from-bracket w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="ml-3">Logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Guest Menu -->
                    <div class="py-2">
                        <a href="{{ route('login') }}" 
                            class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fa-solid fa-arrow-right-to-bracket w-5 text-gray-500 dark:text-gray-400"></i>
                            <span class="ml-3">Login</span>
                        </a>
                        
                        <a href="{{ route('register') }}" 
                            class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fa-solid fa-user-plus w-5 text-gray-500 dark:text-gray-400"></i>
                            <span class="ml-3">Register</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>