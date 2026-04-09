@extends('components.layouts.general-landing')

@section('title', __('common.nav.home'))

@push ('styles')

    <style>
        /* Smooth scrolling for anchor links */
        html {
            scroll-behavior: smooth;
            background-color: white;
        }

        body {
            background-image: none;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Remove background image in dark mode */
        .dark body {
            background-image: none;
            background-color: #1f2937;
        }

        /* Custom scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="w-full text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-slate-900 mb-4">{{ __('landing.documentation.hero_title') }}</h1>
                <p class="text-sm sm:text-xs lg:text-base text-slate-600 leading-relaxed">
                    {{ __('landing.documentation.hero_description') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 bg-white">
        <!-- Mobile Navigation (horizontal scroll) -->
        <div class="lg:hidden mb-8 -mx-4 px-4 overflow-x-auto">
            <nav class="flex gap-2 pb-2 min-w-max">
                <a href="#accessing"
                    class="inline-block py-1.5 px-3 text-xs font-medium text-blue-600 bg-blue-50 rounded-full whitespace-nowrap">
                    {{ __('landing.documentation.nav_accessing') }}
                </a>
                <a href="#employers"
                    class="inline-block py-1.5 px-3 text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-full whitespace-nowrap transition-colors">
                    {{ __('landing.documentation.nav_employers') }}
                </a>
                <a href="#employees"
                    class="inline-block py-1.5 px-3 text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-full whitespace-nowrap transition-colors">
                    {{ __('landing.documentation.nav_employees') }}
                </a>
                <a href="#clients"
                    class="inline-block py-1.5 px-3 text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-full whitespace-nowrap transition-colors">
                    {{ __('landing.documentation.nav_clients') }}
                </a>
                <a href="#requirements"
                    class="inline-block py-1.5 px-3 text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-full whitespace-nowrap transition-colors">
                    {{ __('landing.documentation.nav_requirements') }}
                </a>
            </nav>
        </div>

        <div class="flex gap-12">
            <!-- Sidebar Navigation (desktop only) -->
            <aside class="hidden lg:block w-64 flex-shrink-0 sticky top-8 self-start">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">{{ __('landing.documentation.on_this_page') }}</p>

                    <nav class="space-y-1">
                        <a href="#accessing"
                            class="block py-2 px-3 text-sm font-medium text-blue-600 bg-blue-50 rounded-md">
                            {{ __('landing.documentation.sidebar_accessing') }}
                        </a>
                        <a href="#employers"
                            class="block py-2 px-3 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition-colors">
                            {{ __('landing.documentation.sidebar_employers') }}
                        </a>
                        <a href="#employees"
                            class="block py-2 px-3 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition-colors">
                            {{ __('landing.documentation.sidebar_employees') }}
                        </a>
                        <a href="#clients"
                            class="block py-2 px-3 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition-colors">
                            {{ __('landing.documentation.sidebar_clients') }}
                        </a>
                        <a href="#requirements"
                            class="block py-2 px-3 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition-colors">
                            {{ __('landing.documentation.sidebar_requirements') }}
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Main Documentation Content -->
            <main class="flex-1 max-w-3xl min-w-0">
                <!-- Accessing the Platform Section -->
                <section id="accessing" class="mb-8 sm:mb-12 scroll-mt-8">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 sm:mb-6">{{ __('landing.documentation.sec1_title') }}</h2>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed"><span class="font-semibold">{{ __('landing.documentation.sec1_url_strong') }}</span> {{ __('landing.documentation.sec1_url_text') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed"><span class="font-semibold">{{ __('landing.documentation.sec1_login_strong') }}</span> {{ __('landing.documentation.sec1_login_text') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed"><span class="font-semibold">{{ __('landing.documentation.sec1_role_strong') }}</span> {{ __('landing.documentation.sec1_role_text') }}</span>
                        </li>
                    </ul>
                </section>

                <!-- For Employers Section -->
                <section id="employers" class="mb-8 sm:mb-12 scroll-mt-8">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-3 sm:mb-4">{{ __('landing.documentation.sec2_title') }}</h2>

                    <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed mb-4 sm:mb-6 font-semibold">
                        {{ __('landing.documentation.sec2_goal') }}
                    </p>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec2_1_title') }}</h3>
                            <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed">
                                {{ __('landing.documentation.sec2_1_p') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec2_2_title') }}</h3>
                            <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed">
                                {{ __('landing.documentation.sec2_2_p') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec2_3_title') }}</h3>
                            <ul class="space-y-2 ml-6 mt-2">
                                <li class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed list-disc">{{ __('landing.documentation.sec2_3_li_1') }}</li>
                                <li class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed list-disc">
                                    <span class="font-semibold">{{ __('landing.documentation.sec2_3_li_2_strong') }}</span> {{ __('landing.documentation.sec2_3_li_2_text') }}
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec2_4_title') }}</h3>
                            <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed">
                                {{ __('landing.documentation.sec2_4_p') }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- For Employees Section -->
                <section id="employees" class="mb-8 sm:mb-12 scroll-mt-8">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-3 sm:mb-4">{{ __('landing.documentation.sec3_title') }}</h2>

                    <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed mb-4 sm:mb-6 font-semibold">
                        {{ __('landing.documentation.sec3_goal') }}
                    </p>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec3_1_title') }}</h3>
                            <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed">
                                {{ __('landing.documentation.sec3_1_p') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec3_2_title') }}</h3>
                            <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed">
                                {{ __('landing.documentation.sec3_2_p') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec3_3_title') }}</h3>
                            <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed">
                                {{ __('landing.documentation.sec3_3_p') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec3_4_title') }}</h3>
                            <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed">
                                {{ __('landing.documentation.sec3_4_p') }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- For Clients Section -->
                <section id="clients" class="mb-8 sm:mb-12 scroll-mt-8">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-3 sm:mb-4">{{ __('landing.documentation.sec4_title') }}</h2>

                    <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed mb-4 sm:mb-6 font-semibold">
                        {{ __('landing.documentation.sec4_goal') }}
                    </p>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec4_1_title') }}</h3>
                            <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed">
                                {{ __('landing.documentation.sec4_1_p') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec4_2_title') }}</h3>
                            <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed">
                                {{ __('landing.documentation.sec4_2_p') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec4_3_title') }}</h3>
                            <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed">
                                {{ __('landing.documentation.sec4_3_p') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-slate-900 mb-1.5 sm:mb-2">{{ __('landing.documentation.sec4_4_title') }}</h3>
                            <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed">
                                {{ __('landing.documentation.sec4_4_p') }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- System Requirements Section -->
                <section id="requirements" class="mb-8 sm:mb-12 scroll-mt-8">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-3 sm:mb-4">{{ __('landing.documentation.sec5_title') }}</h2>

                    <p class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed mb-4 sm:mb-6">
                        {{ __('landing.documentation.sec5_intro') }}
                    </p>

                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed"><span class="font-semibold">{{ __('landing.documentation.sec5_browser_strong') }}</span> {{ __('landing.documentation.sec5_browser_text') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed"><span class="font-semibold">{{ __('landing.documentation.sec5_internet_strong') }}</span> {{ __('landing.documentation.sec5_internet_text') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs sm:text-sm lg:text-base text-slate-700 leading-relaxed"><span class="font-semibold">{{ __('landing.documentation.sec5_permissions_strong') }}</span> {{ __('landing.documentation.sec5_permissions_text') }}</span>
                        </li>
                    </ul>
                </section>

                <!-- Footer Navigation -->
                <div class="border-t border-slate-200 pt-8 mt-12">
                    <div class="flex items-center justify-between">
                        <a href="#"
                            class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            {{ __('landing.documentation.back_to_top') }}
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Counter Animation
            const counterElements = document.querySelectorAll('.counter');

            function animateCount(element) {
                const target = parseInt(element.getAttribute('data-target'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        element.textContent = target;
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(current);
                    }
                }, 16);
            }

            // Scroll Animation for Cards
            const featureCards = document.querySelectorAll('.feature-card');

            const cardObserverOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.2
            };

            const cardObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.remove('scroll-hidden');
                        entry.target.classList.add('scroll-visible');
                    }
                });
            }, cardObserverOptions);

            featureCards.forEach(card => {
                if (!card.classList.contains('scroll-hidden') && !card.classList.contains('scroll-visible')) {
                    card.classList.add('scroll-hidden');
                }
                cardObserver.observe(card);
            });

            // Hover Blur Effect
            featureCards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    featureCards.forEach(otherCard => {
                        if (otherCard !== card) {
                            otherCard.classList.add('blurred');
                        }
                    });
                });

                card.addEventListener('mouseleave', () => {
                    featureCards.forEach(otherCard => {
                        otherCard.classList.remove('blurred');
                    });
                });
            });

            // Counter Animation Trigger
            let counterAnimationTriggered = false;

            const counterObserverOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.5
            };

            const counterObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !counterAnimationTriggered) {
                        counterElements.forEach(animateCount);
                        counterAnimationTriggered = true;
                        observer.unobserve(entry.target);
                    }
                });
            }, counterObserverOptions);

            const statsContainer = document.getElementById('container-2');
            if (statsContainer) {
                counterObserver.observe(statsContainer);
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Smooth scroll behavior for navigation links
            const navLinks = document.querySelectorAll('aside nav a');

            navLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }

                    // Update active state
                    navLinks.forEach(l => {
                        l.classList.remove('text-blue-600', 'bg-blue-50');
                        l.classList.add('text-slate-600');
                    });
                    this.classList.remove('text-slate-600');
                    this.classList.add('text-blue-600', 'bg-blue-50');
                });
            });

            // Highlight current section on scroll
            const sections = document.querySelectorAll('section[id]');

            window.addEventListener('scroll', () => {
                let current = '';

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    if (window.pageYOffset >= sectionTop - 100) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('text-blue-600', 'bg-blue-50');
                    link.classList.add('text-slate-600');

                    if (link.getAttribute('href').substring(1) === current) {
                        link.classList.remove('text-slate-600');
                        link.classList.add('text-blue-600', 'bg-blue-50');
                    }
                });
            });
        });
    </script>
@endpush