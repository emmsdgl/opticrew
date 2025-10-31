<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - OptiCrew</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin-slow {
            animation: spin-slow 20s linear infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-2xl mx-auto">
        <!-- Animated 404 Icon -->
        <div class="mb-8 relative">
            <div class="inline-block float-animation">
                <div class="relative">
                    <!-- Background circle -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full blur-2xl opacity-30 spin-slow"></div>

                    <!-- Icon container -->
                    <div class="relative p-8 bg-white rounded-full shadow-2xl">
                        <i class="fas fa-search text-blue-500 text-8xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 404 Text -->
        <h1 class="text-9xl md:text-[180px] font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 leading-none mb-4">
            404
        </h1>

        <!-- Main Message -->
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
            Oops! Page Not Found
        </h2>

        <!-- Description -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 mb-8 shadow-lg border border-gray-200">
            <p class="text-gray-700 text-lg leading-relaxed mb-4">
                The page you're looking for doesn't exist or hasn't been implemented yet.
            </p>
            <p class="text-gray-600 text-base">
                Don't worry! You can navigate back or return to the dashboard.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
            <button onclick="window.history.back()"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg shadow-lg border-2 border-gray-300 transform transition hover:scale-105 focus:outline-none focus:ring-4 focus:ring-gray-300">
                <i class="fas fa-arrow-left"></i>
                <span>Go Back</span>
            </button>

            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg transform transition hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
                        <i class="fas fa-home"></i>
                        <span>Go to Dashboard</span>
                    </a>
                @elseif(Auth::user()->role === 'employee')
                    <a href="{{ route('employee.dashboard') }}"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg transform transition hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
                        <i class="fas fa-home"></i>
                        <span>Go to Dashboard</span>
                    </a>
                @elseif(Auth::user()->role === 'client')
                    <a href="{{ route('client.dashboard') }}"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg transform transition hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
                        <i class="fas fa-home"></i>
                        <span>Go to Dashboard</span>
                    </a>
                @endif
            @else
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg transform transition hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
                    <i class="fas fa-home"></i>
                    <span>Go Home</span>
                </a>
            @endauth
        </div>

        <!-- Quick Links -->
        @auth
        <div class="bg-white/60 backdrop-blur-sm rounded-xl p-6 shadow-md border border-gray-200">
            <p class="text-sm text-gray-600 font-semibold mb-3">Quick Links:</p>
            <div class="flex flex-wrap gap-3 justify-center">
                @if(Auth::user()->role === 'employee')
                    <a href="{{ route('employee.tasks') }}" class="text-blue-600 hover:text-blue-800 text-sm hover:underline">
                        <i class="fas fa-tasks mr-1"></i> My Tasks
                    </a>
                    <span class="text-gray-400">|</span>
                    <a href="{{ route('employee.attendance') }}" class="text-blue-600 hover:text-blue-800 text-sm hover:underline">
                        <i class="fas fa-calendar-check mr-1"></i> Attendance
                    </a>
                    <span class="text-gray-400">|</span>
                    <a href="{{ route('employee.profile') }}" class="text-blue-600 hover:text-blue-800 text-sm hover:underline">
                        <i class="fas fa-user mr-1"></i> Profile
                    </a>
                @elseif(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.tasks') }}" class="text-blue-600 hover:text-blue-800 text-sm hover:underline">
                        <i class="fas fa-tasks mr-1"></i> Tasks
                    </a>
                    <span class="text-gray-400">|</span>
                    <a href="{{ route('admin.analytics') }}" class="text-blue-600 hover:text-blue-800 text-sm hover:underline">
                        <i class="fas fa-chart-line mr-1"></i> Analytics
                    </a>
                    <span class="text-gray-400">|</span>
                    <a href="{{ route('admin.profile') }}" class="text-blue-600 hover:text-blue-800 text-sm hover:underline">
                        <i class="fas fa-user mr-1"></i> Profile
                    </a>
                @endif
            </div>
        </div>
        @endauth
    </div>

    <!-- Decorative Elements -->
    <div class="fixed top-20 left-10 text-blue-200 opacity-20">
        <i class="fas fa-circle text-4xl float-animation" style="animation-delay: 0.5s;"></i>
    </div>
    <div class="fixed top-1/3 right-20 text-purple-200 opacity-20">
        <i class="fas fa-square text-5xl float-animation" style="animation-delay: 1s;"></i>
    </div>
    <div class="fixed bottom-20 left-1/4 text-indigo-200 opacity-20">
        <i class="fas fa-triangle-exclamation text-6xl float-animation" style="animation-delay: 1.5s;"></i>
    </div>
    <div class="fixed bottom-1/3 right-1/4 text-blue-300 opacity-20">
        <i class="fas fa-star text-3xl float-animation" style="animation-delay: 2s;"></i>
    </div>
</body>
</html>
