<!-- Topbar -->
<header class="flex items-center justify-between px-6 h-20 border-b border-gray-50 dark:border-gray-700">
    <div class="flex items-center w-full max-w-3xl">
        {{-- Search bar or other content can go here --}}
    </div>

    <div class="flex items-center space-x-6">
        <button class="text-gray-500 dark:text-gray-300 hover:text-indigo-500 transition-colors">
            <i class="fa-regular fa-bell text-lg"></i>
        </button>

        <button class="text-gray-500 dark:text-gray-300 hover:text-indigo-500 transition-colors">
            <i class="fa-solid fa-inbox text-lg"></i>
        </button>

        <!-- Theme Toggle Button -->
        <button id="theme-toggle"
            class="relative bg-gray-200 dark:bg-gray-800 px-3 py-2 rounded-full transition rotate-once">
            {{-- The icon will be updated by the JavaScript --}}
            <i id="theme-icon"
                class="fi fi-rr-brightness text-yellow-300 text-lg transition-transform duration-500"></i>
        </button>

        <div class="flex items-center space-x-3">
            <img src="https://i.pravatar.cc/40" alt="User" class="w-8 h-8 rounded-full">
            
            @auth
                <span class="text-sm font-medium">{{ explode(' ', auth()->user()->name)[0] }}</span>
            @else
                <span class="text-sm font-medium">Guest</span>
            @endauth

            <i class="fa-solid fa-caret-down text-gray-400 dark:text-gray-500"></i>
        </div>
    </div>
</header>