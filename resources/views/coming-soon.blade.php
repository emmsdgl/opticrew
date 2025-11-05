<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - Fin-noys</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('{{ asset('app.css') }}');
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .float-animation {
            animation: float 5s ease-in-out infinite;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    'sans': ['Familjen Grotesk', 'system-ui', 'sans-serif'],
                }
            }
        }
    </script>
</head>

<body class="font-sans bg-white min-h-screen flex items-center justify-center p-4 overflow-hidden relative h-full">

    <!-- Background SVG Circle -->
    <div class="fixed inset-0 flex items-center justify-center pointer-events-none">
        <img src="{{ asset('images/backgrounds/404-bg.svg') }}" alt=""
            class="w-full h-full opacity-30 object-cover bg-no-repeat">
    </div>

    <div class="text-center max-w-2xl mx-auto relative z-10">
        <!-- Top Text -->
        <p class="text-gray-600 text-sm md:text-base mb-2">
            A new feature is launching soon.
        </p>

        <!-- Main Heading -->
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
            Something amazing is on the way.
        </h1>

        <!-- Illustration SVG -->
        <div class="relative float-animation">
            <img src="{{ asset('images/icons/coming-soon-illustration.svg') }}" alt="Coming illustration"
                class="w-60 h-60 md:w-60 md:h-60 mx-auto my-12">
        </div>

        <!-- Description Text -->
        <p class="text-gray-600 text-sm md:text-base mb-6 max-w-md mx-auto leading-relaxed">
            We're building a cleaner, easier experience for you. We're revising our service.
        </p>

        <!-- Home Page Button with Glassmorphism -->
        <div class="flex justify-center">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                        class="group relative inline-flex items-center justify-between gap-4 px-3 py-4 bg-cyan-50/60 backdrop-blur-lg hover:bg-cyan-50/70 text-gray-900 rounded-2xl shadow-[0_8px_32px_0_rgba(31,38,135,0.15)] border border-white/40 transform transition-all duration-300 hover:shadow-[0_8px_32px_0_rgba(31,38,135,0.25)] focus:outline-none w-full max-w-lg">
                        <!-- Icon and Text -->
                        <div class="flex items-center gap-5">
                            <div
                                class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <rect x="3" y="3" width="6" height="6" rx="1" />
                                    <rect x="11" y="3" width="6" height="6" rx="1" />
                                    <rect x="3" y="11" width="6" height="6" rx="1" />
                                    <rect x="11" y="11" width="6" height="6" rx="1" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm text-gray-900">Home Page</div>
                                <div class="text-sm text-gray-500">Let's get you back on track</div>
                            </div>
                        </div>
                        <!-- Arrow -->
                        <svg class="w-3 h-3 text-gray-900 flex-shrink-0 group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @elseif(Auth::user()->role === 'employee')
                    <a href="{{ route('employee.dashboard') }}"
                        class="group relative inline-flex items-center justify-between gap-4 px-6 py-4 bg-cyan-50/60 backdrop-blur-lg hover:bg-cyan-50/70 text-gray-900 rounded-2xl shadow-[0_8px_32px_0_rgba(31,38,135,0.15)] border border-white/40 transform transition-all duration-300 hover:shadow-[0_8px_32px_0_rgba(31,38,135,0.25)] focus:outline-none w-84">
                        <!-- Icon and Text -->
                        <div class="flex items-center gap-3">
                            <div
                                class="w-6 h-6 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <rect x="3" y="3" width="6" height="6" rx="1" />
                                    <rect x="11" y="3" width="6" height="6" rx="1" />
                                    <rect x="3" y="11" width="6" height="6" rx="1" />
                                    <rect x="11" y="11" width="6" height="6" rx="1" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="text-base font-bold text-gray-900">Home Page</div>
                                <div class="text-sm text-gray-500">Let's get you back on track</div>
                            </div>
                        </div>
                        <!-- Arrow -->
                        <svg class="w-6 h-6 text-gray-900 flex-shrink-0 group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @elseif(Auth::user()->role === 'client')
                    <a href="{{ route('client.dashboard') }}"
                        class="group relative inline-flex items-center justify-between gap-4 px-6 py-4 bg-cyan-50/60 backdrop-blur-lg hover:bg-cyan-50/70 text-gray-900 rounded-2xl shadow-[0_8px_32px_0_rgba(31,38,135,0.15)] border border-white/40 transform transition-all duration-300 hover:shadow-[0_8px_32px_0_rgba(31,38,135,0.25)] focus:outline-none w-84">
                        <!-- Icon and Text -->
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <rect x="3" y="3" width="6" height="6" rx="1" />
                                    <rect x="11" y="3" width="6" height="6" rx="1" />
                                    <rect x="3" y="11" width="6" height="6" rx="1" />
                                    <rect x="11" y="11" width="6" height="6" rx="1" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="text-base font-bold text-gray-900">Home Page</div>
                                <div class="text-sm text-gray-500">Let's get you back on track</div>
                            </div>
                        </div>
                        <!-- Arrow -->
                        <svg class="w-6 h-6 text-gray-900 flex-shrink-0 group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endif
            @else
                <a href="{{ route('home') }}"
                    class="group relative inline-flex items-center justify-between gap-4 px-6 py-4 bg-cyan-50/60 backdrop-blur-lg hover:bg-cyan-50/70 text-gray-900 rounded-2xl shadow-[0_8px_32px_0_rgba(31,38,135,0.15)] border border-white/40 transform transition-all duration-300 hover:shadow-[0_8px_32px_0_rgba(31,38,135,0.25)] focus:outline-none w-84">
                    <!-- Icon and Text -->
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <rect x="3" y="3" width="6" height="6" rx="1" />
                                <rect x="11" y="3" width="6" height="6" rx="1" />
                                <rect x="3" y="11" width="6" height="6" rx="1" />
                                <rect x="11" y="11" width="6" height="6" rx="1" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="text-base font-bold text-gray-900">Home Page</div>
                            <div class="text-sm text-gray-500">Let's get you back on track</div>
                        </div>
                    </div>
                    <!-- Arrow -->
                    <svg class="w-6 h-6 text-gray-900 flex-shrink-0 group-hover:translate-x-1 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @endauth
        </div>


        <!-- Optional: Back button -->
        <button onclick="window.history.back()"
            class="mt-4 text-gray-500 hover:text-gray-700 text-sm focus:outline-none">
            Go back to previous page
        </button>
    </div>

</body>

</html>