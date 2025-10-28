<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
       <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <title>@yield('title', 'Fin-noys Cleaning Service')</title>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <style>
        @font-face {
            font-family: 'fam-regular';
            src: url('{{ asset('fonts/FamiljenGrotesk-Regular.otf') }}') format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'fam-bold';
            src: url('{{ asset('fonts/FamiljenGrotesk-Bold.otf') }}') format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'fam-bold-italic';
            src: url('{{ asset('fonts/FamiljenGrotesk-BoldItalic.otf') }}') format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        * {
            font-family: 'fam-regular';
        }

        /* Light mode text color */

        body {
            background-image: url('{{ asset('images/backgrounds/landing-page-2.svg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Remove background image in dark mode */
        .dark body {
            background-image: none;
            background-color: #1f2937; /* gray-800 */
        }

        #header-1,
        #cleanliness {
            font-family: 'fam-bold';
        }

        .soft-glow {
            box-shadow: 0 80px 90px rgba(255, 252, 252, 0.937);
        }

        .soft-glow-2 {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.218);
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* CUSTOM FROSTED GLASS EFFECT */
        .frosted-card {
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Dark mode frosted glass */
        .dark .frosted-card {
            background-color: rgba(31, 41, 55, 0.6);
            border: 1px solid rgba(107, 114, 128, 0.3);
        }

        /* === ADDED FOR BLUR EFFECT ON OTHER CARDS === */
        .feature-card.blurred {
            filter: blur(3px);
            transition: filter 0.3s ease-in-out;
        }

        /* Base styles for scroll animation (start state) */
        .feature-card.scroll-hidden {
            opacity: 0;
            transform: translateY(50px);
        }

        .feature-card.scroll-visible {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        @yield('additional-styles')
    </style>

    @stack('styles')
</head>

<body class="h-full dark:bg-gray-900">
    <!-- Theme Toggle Button -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" type="button"
            class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 transition-colors">
            <!-- Moon icon (shows in light mode, click to go dark) -->
            <svg id="theme-toggle-dark-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            <!-- Sun icon (shows in dark mode, click to go light) -->
            <svg id="theme-toggle-light-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                    fill-rule="evenodd" clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>

    <div id="main-container">
        <!-- Navigation Header -->
        <header class="inset-x-0 top-0 z-50">
            <nav aria-label="Global" class="flex items-center justify-between p-6 lg:px-8">
                <div class="flex lg:flex-1">
                    <a href="{{ route('home') }}" class="-m-1.5 p-1.5">
                        <span class="sr-only">Fin-noys</span>
                        <!-- Light mode logo -->
                        <img src="{{ asset('images/finnoys-text-logo.svg') }}" alt="Fin-noys Logo" 
                             class="h-20 w-auto dark:hidden">
                        <!-- Dark mode logo (use same if you don't have a light version) -->
                        <img src="{{ asset('images/finnoys-text-logo.svg') }}" alt="Fin-noys Logo" 
                             class="h-20 w-auto hidden dark:block brightness-0 invert">
                    </a>
                </div>
                <div class="flex lg:hidden">
                    <button type="button" command="show-modal" commandfor="mobile-menu"
                        class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-200 transition-colors">
                        <span class="sr-only">Open main menu</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon"
                            aria-hidden="true" class="size-6">
                            <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <div class="hidden lg:flex lg:gap-x-12">
                    <a href="{{ route('home') }}"
                        class="text-sm/6 text-blue-950 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:font-bold transition-colors">Home</a>
                    <a href="{{ route('services') }}"
                        class="text-sm/6 text-blue-950 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:font-bold transition-colors">Services</a>
                    <a href="{{ route('quotation') }}"
                        class="text-sm/6 text-blue-950 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:font-bold transition-colors">Price Quotation</a>
                    <a href="{{ route('about') }}"
                        class="text-sm/6 text-blue-950 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:font-bold transition-colors">About</a>
                </div>
                <div class="hidden lg:flex lg:flex-1 lg:justify-end">
                    <span aria-hidden="true"></span>
                </div>
            </nav>

            <!-- Mobile Menu Dialog -->
            <el-dialog>
                <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
                    <div tabindex="0" class="fixed inset-0 focus:outline-none">
                        <el-dialog-panel
                            class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-blue-100 dark:bg-gray-800 bg-blend-color-multiply p-6 sm:max-w-sm sm:ring-1 sm:ring-gray-100/10 transition-colors">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('home') }}" class="-m-1.5 p-1.5">
                                    <span class="sr-only">Fin-noys</span>
                                    <!-- Light mode logo -->
                                    <img src="{{ asset('images/finnoys-text-logo.svg') }}" alt="Fin-noys Logo"
                                        class="h-20 w-auto dark:hidden">
                                    <!-- Dark mode logo -->
                                    <img src="{{ asset('images/finnoys-text-logo.svg') }}" alt="Fin-noys Logo"
                                        class="h-20 w-auto hidden dark:block brightness-0 invert">
                                </a>
                                <button type="button" command="close" commandfor="mobile-menu"
                                    class="-m-2.5 rounded-md p-2.5 text-gray-700 dark:text-gray-200 transition-colors">
                                    <span class="sr-only">Close menu</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        data-slot="icon" aria-hidden="true" class="size-6">
                                        <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-6 flow-root">
                                <div class="-my-6 divide-y divide-white/10 dark:divide-gray-700">
                                    <div class="space-y-2 py-6">
                                        <a href="{{ route('home') }}"
                                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 dark:text-gray-200 hover:bg-blue-600/10 dark:hover:bg-gray-700 transition-colors">Home</a>
                                        <a href="{{ route('about') }}"
                                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 dark:text-gray-200 hover:bg-blue-600/10 dark:hover:bg-gray-700 transition-colors">About</a>
                                        <a href="{{ route('services') }}"
                                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 dark:text-gray-200 hover:bg-blue-600/10 dark:hover:bg-gray-700 transition-colors">Services</a>
                                        <a href="{{ route('quotation') }}"
                                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 dark:text-gray-200 hover:bg-blue-600/10 dark:hover:bg-gray-700 transition-colors">Price
                                            Quotation</a>
                                    </div>
                                    <div class="py-6">
                                        <a href="{{ route('login') }}"
                                            class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 text-blue-950 dark:text-gray-200 hover:bg-blue-600/10 dark:hover:bg-gray-700 transition-colors">Log
                                            in</a>
                                    </div>
                                </div>
                            </div>
                        </el-dialog-panel>
                    </div>
                </dialog>
            </el-dialog>
        </header>

        <!-- Main Content -->
        @yield('content')

        <!-- Chatbot Component -->
        @include('components.chatbot')
    </div>
    @include('components.footer')

    <!-- Common Scripts -->
    @stack('scripts')

    <script>
        // Theme toggle functionality
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Check for saved theme preference or default to 'light'
        const currentTheme = localStorage.getItem('color-theme') || 'light';

        // Function to update icon visibility
        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeToggleDarkIcon.classList.add('hidden');
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleLightIcon.classList.add('hidden');
                themeToggleDarkIcon.classList.remove('hidden');
            }
        }

        // Set initial theme
        if (currentTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        updateThemeIcon(currentTheme);

        // Toggle theme on button click
        themeToggleBtn.addEventListener('click', function () {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
                updateThemeIcon('light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
                updateThemeIcon('dark');
            }
        });
    </script>
</body>

</html>