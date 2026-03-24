<!DOCTYPE html>
<html lang="en" class="overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('mobileMenu', {
                open: false,
                toggle() { this.open = !this.open; },
            });
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>



    <title>@yield('title', 'Fin-noys Cleaning Service')</title>
    <link rel="icon" href="{{ asset('images/icons/castcrew/castcrew-pic-logo.svg') }}" type="image/svg+xml">
    <script>
        // Suppress Tailwind CDN production warning
        if (typeof tailwindCSS !== 'undefined') {
            tailwindCSS.disableWarnings = true;
        }

        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <style>
        @font-face {
            font-family: 'fam-regular';
            src: url("{{ asset('fonts/FamiljenGrotesk-Regular.otf') }}") format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'fam-bold';
            src: url("{{ asset('fonts/FamiljenGrotesk-Bold.otf') }}") format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'fam-bold-italic';
            src: url("{{ asset('fonts/FamiljenGrotesk-BoldItalic.otf') }}") format('opentype');
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
            background-color: #1f2937;
        }

        /* Active navigation pill indicator */
        .nav-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: 9999px;
            transition: all 0.3s ease;
        }

        @media (min-width: 1024px) {
            .nav-link {
                padding: 1rem 1rem;
            }
        }

        .nav-link.active {
            background-color: #E8EAF2;
        }

        .dark .nav-link.active {
            background-color: rgba(55, 65, 81, 1);
            /* gray-700 */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .nav-link .nav-icon {
            width: 1rem;
            height: 1rem;
            display: none;
            flex-shrink: 0;
            line-height: 1;
        }

        .nav-link.active .nav-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .nav-link span {
            line-height: 1;
            display: inline-flex;
            align-items: center;
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

        /* Gradual Blur Component */
        .gradual-blur-inner {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .gradual-blur-inner > div {
            -webkit-backdrop-filter: inherit;
            backdrop-filter: inherit;
        }
        .gradual-blur {
            isolation: isolate;
        }
        @supports not (backdrop-filter: blur(1px)) {
            .gradual-blur-inner > div {
                background: rgba(0, 0, 0, 0.3);
                opacity: 0.5;
            }
        }
        .gradual-blur-fixed {
            position: fixed !important;
            left: 0;
            right: 0;
            pointer-events: none;
            z-index: 60;
        }
        .gradual-blur-fixed.top-0 { top: 0; bottom: auto; }
        .gradual-blur-fixed.bottom-0 { bottom: 0; top: auto; }

        @yield('additional-styles')
    </style>

    @stack('styles')
</head>

<body class="h-full bg-white dark:bg-gray-900 font-sans antialiased overflow-x-hidden">
    <x-material-ui.page-loader />
    <div id="main-container">
        <!-- Navigation Header -->
        <header class="inset-x-0 top-0 z-50">
            <nav aria-label="Global" class="flex items-center justify-between p-4 sm:p-6 lg:px-8">
                <div class="flex lg:flex-1">
                    <a href="{{ route('home') }}" class="-m-1.5 p-1.5">
                        <span class="sr-only">Fin-noys</span>
                        <!-- Light mode logo -->
                        <img src="{{ asset('images/finnoys-text-logo.svg') }}" alt="Fin-noys Logo"
                            class="h-12 sm:h-20 lg:h-24 w-auto hidden dark:block">
                        <!-- Dark mode logo -->
                        <img src="{{ asset('images/finnoys-text-logo-light.svg') }}" alt="Fin-noys Logo"
                            class="h-12 sm:h-20 lg:h-24 w-auto block dark:hidden">
                    </a>
                </div>

                <!-- Mobile Menu Button + Login -->
                <div class="flex lg:hidden items-center gap-2" x-data>
                    <button id="mobile-header-theme-toggle" type="button"
                        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none rounded-lg text-sm p-2 transition-colors">
                        <svg class="w-4 h-4 mobile-header-dark-icon" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                        <svg class="w-4 h-4 mobile-header-light-icon hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    </button>
                    <a href="/login"
                        class="group text-[10px] font-bold text-blue-950 bg-white border border-blue-950 px-3 py-1.5 rounded-full dark:text-gray-200 dark:bg-gray-700 dark:border-transparent hover:text-blue-600 hover:border-blue-600 dark:hover:text-blue-400 dark:hover:bg-gray-600 transition-colors">
                        {{ __('common.nav.login') }}
                        <span aria-hidden="true" class="ml-1 inline-block transition-transform group-hover:translate-x-1">&rarr;</span>
                    </a>
                    <button type="button" @click="$store.mobileMenu.toggle()"
                        class="inline-flex items-center justify-center rounded-md p-2.5 text-gray-700 dark:text-gray-200 transition-colors">
                        <span class="sr-only" x-text="$store.mobileMenu.open ? 'Close menu' : 'Open menu'"></span>
                        <div class="w-6 h-6 relative flex items-center justify-center">
                            <span class="absolute h-0.5 w-5 bg-current rounded transition-all duration-300 ease-in-out"
                                  :class="$store.mobileMenu.open ? 'rotate-45' : '-translate-y-1.5'"></span>
                            <span class="absolute h-0.5 w-5 bg-current rounded transition-all duration-300 ease-in-out"
                                  :class="$store.mobileMenu.open ? 'opacity-0 scale-0' : 'opacity-100'"></span>
                            <span class="absolute h-0.5 w-5 bg-current rounded transition-all duration-300 ease-in-out"
                                  :class="$store.mobileMenu.open ? '-rotate-45' : 'translate-y-1.5'"></span>
                        </div>
                    </button>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden lg:flex lg:gap-x-2 xl:gap-x-4 text-xs xl:text-sm">
                    <a href="{{ route('home') }}"
                        class="nav-link {{ request()->routeIs('home') ? 'active text-blue-950 dark:text-white font-bold' : 'text-blue-950 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400' }}">
                        <i class="nav-icon fas fa-home text-blue-950 dark:text-white"></i>
                        <span>{{ __('common.nav.home') }}</span>
                    </a>
                    <a href="{{ route('services') }}"
                        class="nav-link {{ request()->routeIs('services') ? 'active text-blue-950 dark:text-white font-bold' : 'text-blue-950 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400' }}">
                        <i class="nav-icon fas fa-broom text-blue-950 dark:text-white"></i>
                        <span>{{ __('common.nav.services') }}</span>
                    </a>
                    <a href="{{ route('quotation') }}"
                        class="nav-link {{ request()->routeIs('quotation') ? 'active text-blue-950 dark:text-white font-bold' : 'text-blue-950 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar text-blue-950 dark:text-white"></i>
                        <span>{{ __('common.nav.pricing') }}</span>
                    </a>
                    <a href="{{ route('about') }}"
                        class="nav-link {{ request()->routeIs('about') ? 'active text-blue-950 dark:text-white font-bold' : 'text-blue-950 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400' }}">
                        <i class="nav-icon fas fa-info-circle text-blue-950 dark:text-white"></i>
                        <span>{{ __('common.nav.about') }}</span>
                    </a>
                </div>
                <!-- Right side items: Theme Toggle, Language Switcher, Login -->
                <div class="hidden lg:flex lg:flex-1 lg:justify-end lg:items-center lg:gap-x-2 xl:gap-x-4">
                    <!-- Language Switcher -->
                    <div class="relative inline-flex">
                        <button id="language-toggle"
                            class="flex items-center gap-1.5 xl:gap-2 text-xs xl:text-sm text-blue-950 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            @if(app()->getLocale() == 'fi')
                                <span class="text-base xl:text-lg">🇫🇮</span>
                                <span>Suomi</span>
                            @else
                                <span class="text-base xl:text-lg">🇬🇧</span>
                                <span>English</span>
                            @endif
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div id="language-dropdown"
                            class="hidden absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50 top-full">
                            <a href="{{ route('language.switch', 'en') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors">
                                <span class="text-lg">🇬🇧</span> English
                            </a>
                            <a href="{{ route('language.switch', 'fi') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors">
                                <span class="text-lg">🇫🇮</span> Suomi
                            </a>
                        </div>
                    </div>

                    <!-- Theme Toggle Button -->
                    <button id="theme-toggle" type="button"
                        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2 xl:p-2.5 transition-colors">
                        <!-- Moon icon (shows in light mode, click to go dark) -->
                        <svg id="theme-toggle-dark-icon" class="w-4 h-4 xl:w-5 xl:h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <!-- Sun icon (shows in dark mode, click to go light) -->
                        <svg id="theme-toggle-light-icon" class="w-4 h-4 xl:w-5 xl:h-5 hidden" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <!-- Login Link -->
                    <a href="/login"
                        class="group text-xs xl:text-sm font-bold text-blue-950 bg-white border border-blue-950 px-4 xl:px-6 py-2.5 xl:py-3 rounded-full dark:text-gray-200 dark:bg-gray-700 dark:border-transparent hover:text-blue-600 hover:border-blue-600 dark:hover:text-blue-400 dark:hover:bg-gray-600 transition-colors">
                        {{ __('common.nav.login') }}
                        <span aria-hidden="true"
                            class="ml-1 xl:ml-2 inline-block transition-transform group-hover:translate-x-1">&rarr;</span>
                    </a>
                </div>
            </nav>

            <!-- Mobile Menu Panel (Alpine.js driven) -->
            <div x-data class="lg:hidden" x-show="$store.mobileMenu.open" x-cloak>
                {{-- Backdrop --}}
                <div x-show="$store.mobileMenu.open"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="$store.mobileMenu.open = false"
                     class="fixed inset-0 z-40 bg-black/50"></div>

                {{-- Slide-in Panel --}}
                <div x-show="$store.mobileMenu.open"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     class="fixed inset-y-0 right-0 z-50 w-[85%] max-w-sm overflow-y-auto bg-white dark:bg-gray-900 shadow-2xl">

                    {{-- Header --}}
                    <div class="flex items-center justify-end px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <button type="button" @click="$store.mobileMenu.open = false"
                            class="rounded-md p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <span class="sr-only">Close menu</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                aria-hidden="true" class="w-6 h-6">
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    {{-- Navigation --}}
                    <nav class="px-5 py-6">
                        <div class="space-y-1">
                            <a href="{{ route('home') }}"
                                class="block px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('home') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                {{ __('common.nav.home') }}
                            </a>
                            <a href="{{ route('services') }}"
                                class="block px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('services') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                {{ __('common.nav.services') }}
                            </a>
                            <a href="{{ route('quotation') }}"
                                class="block px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('quotation') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                {{ __('common.nav.pricing') }}
                            </a>
                            <a href="{{ route('about') }}"
                                class="block px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('about') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                {{ __('common.nav.about') }}
                            </a>
                        </div>

                        <div class="my-6 border-t border-gray-200 dark:border-gray-700"></div>

                        <div class="space-y-1">
                            <button id="mobile-language-toggle" type="button"
                                class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                                <span>Language</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    @if(app()->getLocale() == 'fi')
                                        🇫🇮 Suomi
                                    @else
                                        🇬🇧 English
                                    @endif
                                </span>
                            </button>

                            <div id="mobile-language-dropdown" class="hidden ml-4 space-y-1">
                                <a href="{{ route('language.switch', 'en') }}"
                                    class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                                    🇬🇧 English
                                </a>
                                <a href="{{ route('language.switch', 'fi') }}"
                                    class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                                    🇫🇮 Suomi
                                </a>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        @yield('content')

        <!-- Chatbot Component -->
        @include('components.landing-chatbot')
    </div>
    @include('components.footer')

    <!-- Common Scripts -->
    <x-global-dialogs />

    {{-- Typing text animation (globally available) --}}
    <x-material-ui.typing-text />

    @stack('scripts')

    <script>
        // Force scroll to top on page load/refresh
        if (history.scrollRestoration) {
            history.scrollRestoration = 'manual';
        }

        window.addEventListener('beforeunload', function () {
            window.scrollTo(0, 0);
        });

        window.addEventListener('load', function () {
            setTimeout(function () {
                window.scrollTo(0, 0);
            }, 0);
        });

        // Theme toggle functionality
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Check for saved theme preference or default to 'light'
        const currentTheme = localStorage.getItem('color-theme') || 'light';

        // Mobile header theme toggle elements
        const mobileHeaderThemeBtn = document.getElementById('mobile-header-theme-toggle');
        const mobileHeaderDarkIcon = mobileHeaderThemeBtn ? mobileHeaderThemeBtn.querySelector('.mobile-header-dark-icon') : null;
        const mobileHeaderLightIcon = mobileHeaderThemeBtn ? mobileHeaderThemeBtn.querySelector('.mobile-header-light-icon') : null;

        // Function to update icon visibility
        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeToggleDarkIcon.classList.add('hidden');
                themeToggleLightIcon.classList.remove('hidden');
                if (mobileHeaderDarkIcon) mobileHeaderDarkIcon.classList.add('hidden');
                if (mobileHeaderLightIcon) mobileHeaderLightIcon.classList.remove('hidden');
            } else {
                themeToggleLightIcon.classList.add('hidden');
                themeToggleDarkIcon.classList.remove('hidden');
                if (mobileHeaderLightIcon) mobileHeaderLightIcon.classList.add('hidden');
                if (mobileHeaderDarkIcon) mobileHeaderDarkIcon.classList.remove('hidden');
            }
        }

        // Set initial theme
        if (currentTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        updateThemeIcon(currentTheme);

        // Toggle theme helper
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
                updateThemeIcon('light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
                updateThemeIcon('dark');
            }
        }

        // Toggle theme on button click (desktop)
        themeToggleBtn.addEventListener('click', toggleTheme);

        // Toggle theme on mobile header button click
        if (mobileHeaderThemeBtn) {
            mobileHeaderThemeBtn.addEventListener('click', toggleTheme);
        }
    </script>
    <script>
        // LANGUAGE DROPDOWN TOGGLE
        document.addEventListener('DOMContentLoaded', function () {
            const languageToggle = document.getElementById('language-toggle');
            const languageDropdown = document.getElementById('language-dropdown');

            if (languageToggle && languageDropdown) {
                languageToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    languageDropdown.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function () {
                    languageDropdown.classList.add('hidden');
                });
            }

            // MOBILE MENU - Language Toggle
            const mobileLanguageToggle = document.getElementById('mobile-language-toggle');
            const mobileLanguageDropdown = document.getElementById('mobile-language-dropdown');

            if (mobileLanguageToggle && mobileLanguageDropdown) {
                mobileLanguageToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    mobileLanguageDropdown.classList.toggle('hidden');
                });
            }
        });
    </script>
    <x-click-spark />
</body>

</html>