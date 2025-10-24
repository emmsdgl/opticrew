@props([
    'notifications' => []
])

<!-- Topbar -->
<header class="flex items-center justify-between px-6 h-20 border-b border-gray-50 dark:border-gray-700">
    <div class="flex items-center w-full max-w-3xl">
        {{-- Search bar or other content can go here --}}
    </div>

    <div class="flex items-center space-x-6">
        <!-- Notifications Dropdown -->
        <div class="relative">
            <button id="notification-toggle" class="text-gray-500 dark:text-gray-300 hover:text-blue-500 transition-colors relative">
                <i class="fa-regular fa-bell text-lg"></i>
                <!-- Notification Badge -->
                @if(count($notifications) > 0)
                    <span class="absolute -top-1 -right-2 bg-blue-500 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full">
                        {{ count($notifications) > 9 ? '9+' : count($notifications) }}
                    </span>
                @endif
            </button>

            <!-- Notifications Dropdown Menu -->
            <div id="notification-dropdown" 
                class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible transition-all duration-200 transform origin-top-right scale-95 z-50">
                
                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Notifications</h3>
                    @if(count($notifications) > 0)
                        <button class="text-xs text-blue-500 hover:text-blue-600 font-medium flex items-center">
                            <i class="fa-solid fa-check-double mr-1"></i>
                            Mark as read
                        </button>
                    @endif
                </div>

                <!-- Notification Items -->
                <div class="max-h-96 overflow-y-auto">
                    @forelse($notifications as $notification)
                        <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-start">

                                <div class="ml-3 flex-1">
                                    <div class="flex items-start">
                                        <!-- Unread Indicator -->
                                        @if($notification['unread'] ?? false)
                                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 mr-2"></span>
                                        @else
                                            <span class="w-2 h-2 mr-2"></span>
                                        @endif

                                        <div class="flex-1">
                                            <p class="text-sm {{ ($notification['highlighted'] ?? false) ? 'text-blue-500' : 'text-gray-900 dark:text-white' }} font-normal">
                                                {{ $notification['message'] }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                @if(isset($notification['sender']))
                                                    {{ $notification['sender'] }} • 
                                                @endif
                                                {{ $notification['time'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <i class="fa-regular fa-bell-slash text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No notifications yet</p>
                        </div>
                    @endforelse
                </div>

                <!-- Footer -->
                @if(count($notifications) > 0)
                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                        <a href="#" class="text-sm text-blue-500 hover:text-blue-600 font-medium">View all notifications</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Theme Toggle Button -->
        <button id="theme-toggle"
            class="relative bg-gray-200 dark:bg-gray-800 px-3 py-2 rounded-full transition rotate-once">
            <i id="theme-icon"
                class="fi fi-rr-brightness text-yellow-300 text-lg transition-transform duration-500"></i>
        </button>

        <!-- Profile Dropdown -->
        <div class="relative">
            <button id="profile-dropdown-toggle" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                <img src="https://i.pravatar.cc/40" alt="User" class="w-8 h-8 rounded-full">
                
                @auth
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ explode(' ', auth()->user()->name)[0] }}</span>
                @else
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Guest</span>
                @endauth

                <i id="caret-icon" class="fa-solid fa-caret-down text-gray-400 dark:text-gray-500 transition-transform duration-200"></i>
            </button>

            <!-- Profile Dropdown Menu -->
            <div id="profile-dropdown" 
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible transition-all duration-200 transform origin-top-right scale-95 z-50">
                
                @auth
                    <!-- Menu Items -->
                    <div class="py-2">
                        <a href="#" 
                            class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fa-regular fa-user w-5 text-gray-500 dark:text-gray-400"></i>
                            <span class="ml-3">Profile</span>
                        </a>
                                                
                        <a href="#" 
                            class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fa-solid fa-gear w-5 text-gray-500 dark:text-gray-400"></i>
                            <span class="ml-3">Settings</span>
                        </a>

                        <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                        
                        <a href="#" 
                            class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fa-regular fa-file-lines w-5 text-gray-500 dark:text-gray-400"></i>
                            <span class="ml-3">Guide</span>
                        </a>
                        
                        <a href="#" 
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