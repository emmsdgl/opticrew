<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiCrew</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-indigo-900 text-white flex-shrink-0">
            <div class="p-6">
                <h1 class="text-2xl font-bold">FIN-NOYS</h1>
            </div>

            <!-- Find the <nav> block in app.blade.php and replace it with this -->
            <nav class="mt-6 flex-grow flex flex-col">
                
                {{-- ADMIN NAVIGATION --}}
                @if (Auth::user()->role === 'admin')
                <div class="space-y-1">
                    <a href="{{ route('admin.tasks') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.tasks') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                        <i class="fas fa-tasks mr-3"></i> Tasks
                    </a>

                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                        <i class="fas fa-plus-circle mr-3"></i> Job Creation
                    </a>

                    <a href="{{ route('admin.schedules') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.schedules') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                        <i class="fas fa-calendar-alt mr-3"></i> Schedules
                    </a>

                    <a href="{{ route('admin.simulation') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.simulation') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                        <i class="fas fa-cogs mr-3"></i> Algorithm Simulation
                    </a>
                    {{-- Add other admin links here later --}}
                </div>
                @endif

                {{-- EMPLOYEE NAVIGATION --}}
                @if (Auth::user()->role === 'employee')
                <div class="space-y-1">
                    <a href="{{ route('employee.dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('employee.dashboard') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                        <i class="fas fa-home mr-3"></i> My Dashboard
                    </a>
                    {{-- We will create this route in the next step --}}
                    {{-- <a href="{{ route('employee.schedule') }}" class="flex items-center px-6 py-3 text-indigo-200 hover:bg-indigo-800 hover:text-white transition">
                        <i class="fas fa-calendar-alt mr-3"></i> My Schedule
                    </a> --}}
                </div>
                @endif
                
                {{-- LOGOUT (Visible to everyone) --}}
                <div class="mt-auto mb-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();"
                                class="flex items-center px-6 py-3 text-indigo-200 hover:bg-indigo-800 hover:text-white transition">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            Logout
                        </a>
                    </form>
                </div>
            </nav>


        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto">
            <!-- This is the 'hole' where the page content will go -->
            {{ $slot }}
        </main>
    </div>
    @livewireScriptConfig

    @livewireScripts
</body>
</html>