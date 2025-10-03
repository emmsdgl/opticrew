<!-- <nav x-data="{ open: false }" class="bg-indigo-900 border-b border-indigo-800">
    // Primary Navigation Menu
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col h-screen">
          // Logo
            <div class="flex-shrink-0 flex items-center h-16">
                <a href="{{ route('admin.dashboard') }}">
                    <h1 class="text-2xl font-bold text-white">FIN-NOYS</h1>
                </a>
            </div>

            // Navigation Links
            <div class="mt-6 flex-grow flex flex-col">
                <div class="space-y-1">
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <i class="fas fa-home mr-3"></i>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    // NOTE: These are placeholders for now. We will create these pages later.
                    {{-- <x-nav-link href="#">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        {{ __('Orders') }}
                    </x-nav-link> --}}

                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <i class="fas fa-tasks mr-3"></i>
                        {{ __('Tasks') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('admin.schedules')" :active="request()->routeIs('admin.schedules')">
                        <i class="fas fa-calendar-alt mr-3"></i>
                        {{ __('Schedules') }}
                    </x-nav-link>
                    
                    {{-- <x-nav-link href="#">
                        <i class="fas fa-chart-bar mr-3"></i>
                        {{ __('Analytics') }}
                    </x-nav-link>

                    <x-nav-link href="#">
                        <i class="fas fa-users mr-3"></i>
                        {{ __('Teams') }}
                    </x-nav-link>

                    <x-nav-link href="#">
                        <i class="fas fa-user-circle mr-3"></i>
                        {{ __('Accounts') }}
                    </x-nav-link> --}}
                </div>

                // Logout Button at the bottom
                <div class="mt-auto mb-4">
                    // Authentication
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            {{ __('Logout') }}
                        </x-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav> -->