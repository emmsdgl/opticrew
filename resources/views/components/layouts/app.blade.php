<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CastCrew</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @livewireStyles

    <style>
        /* Developers' Watermark */
        :root::after {
            content: "Developed by Ela-vate for Fin-noys";
            display: none;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="flex h-screen bg-gray-100 overflow-hidden">

        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-indigo-900 text-white flex-shrink-0 flex flex-col">
            <div class="p-6">
                <a
                    href="{{ (Auth::check() && Auth::user()->role === 'admin') ? route('admin.dashboard') : route('employee.dashboard') }}">
                    <h1 class="text-2xl font-bold">FIN-NOYS</h1>
                </a>
            </div>

            <nav class="flex-grow">
                {{-- ADMIN NAVIGATION --}}
                @if (Auth::check() && Auth::user()->role === 'admin')
                    <div class="space-y-1">
                        <a href="{{ route('admin.tasks') }}"
                            class="flex items-center px-6 py-3 {{ request()->routeIs('admin.tasks') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                            <i class="fas fa-tasks mr-3"></i> Tasks
                        </a>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center px-6 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                            <i class="fas fa-plus-circle mr-3"></i> Job Creation
                        </a>
                        <a href="{{ route('admin.schedules') }}"
                            class="flex items-center px-6 py-3 {{ request()->routeIs('admin.schedules') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                            <i class="fas fa-calendar-alt mr-3"></i> Schedules
                        </a>
                        <a href="{{ route('admin.simulation') }}"
                            class="flex items-center px-6 py-3 {{ request()->routeIs('admin.simulation') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                            <i class="fas fa-cogs mr-3"></i> Algorithm Simulation
                        </a>
                        <a href="{{ route('admin.analytics.employees') }}"
                            class="flex items-center px-6 py-3 {{ request()->routeIs('admin.analytics.employees') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                            <i class="fas fa-chart-line mr-3"></i> Employee Analytics
                        </a>
                        <a href="{{ route('admin.scheduling-log') }}"
                            class="flex items-center px-6 py-3 {{ request()->routeIs('admin.scheduling-log') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                            <i class="fas fa-file-alt mr-3"></i> Scheduling Log
                        </a>

                        <a href="{{ route('admin.payroll') }}"
                            class="flex items-center px-6 py-3 {{ request()->routeIs('admin.scheduling-log') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                            <i class="fas fa-file-alt mr-3"></i> Payroll
                        </a>
                    </div>
                @endif

                {{-- EMPLOYEE NAVIGATION --}}
                @if (Auth::check() && Auth::user()->role === 'employee')
                    <div class="space-y-1">
                        <a href="{{ route('employee.dashboard') }}"
                            class="flex items-center px-6 py-3 {{ request()->routeIs('employee.dashboard') ? 'bg-indigo-800' : 'text-indigo-200' }} hover:bg-indigo-800 hover:text-white transition">
                            <i class="fas fa-home mr-3"></i> My Dashboard
                        </a>
                    </div>
                @endif
            </nav>

            <!-- Logout Form -->
            <div class="p-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                        class="flex items-center px-6 py-3 text-indigo-200 hover:bg-indigo-800 hover:text-white transition">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Logout
                    </a>
                </form>
            </div>
        </aside>

        <!-- Main Content Area (This is the only part that scrolls) -->
        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>

</html>