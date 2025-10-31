@extends('components.layouts.general-landing')

@section('title', __('common.nav.home'))

@push ('styles')
    <style>
        /* Floating animations with different speeds */
        @keyframes float-slow {

            0%,
            100% {
                transform: translateY(0px) rotate(-3deg);
            }

            50% {
                transform: translateY(-12px) rotate(-3deg);
            }
        }

        @keyframes float-medium {

            0%,
            100% {
                transform: translateY(0px) rotate(2deg);
            }

            50% {
                transform: translateY(-15px) rotate(2deg);
            }
        }

        @keyframes float-fast {

            0%,
            100% {
                transform: translateY(0px) rotate(1deg);
            }

            50% {
                transform: translateY(-10px) rotate(1deg);
            }
        }

        .animate-float-slow {
            animation: float-slow 4s ease-in-out infinite;
        }

        .animate-float-medium {
            animation: float-medium 3.5s ease-in-out infinite;
            animation-delay: 0.5s;
        }

        .animate-float-fast {
            animation: float-fast 3s ease-in-out infinite;
            animation-delay: 1s;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <div id="container-1" class="relative isolate text-center w-[90%] sm:w-[60%] mx-auto pt-6 sm:pt-11 pb-12 sm:pb-24">
        <div class="hidden sm:mb-8 sm:flex sm:justify-center">
            <div
                class="relative rounded-full px-3 py-1 text-sm/6 text-gray-600 dark:text-gray-300 ring-1 ring-gray-900/10 dark:ring-gray-100/10 hover:ring-gray-900/20 dark:hover:ring-gray-100/20 transition-all">
                {{ __('home.hero.tagline') }}
            </div>
        </div>
        <h1 id="header-1" class="text-3xl sm:text-6xl tracking-normal text-blue-950 dark:text-white p-4 sm:p-10">
            {{ __('home.hero.title_1') }}
            <span id="cleanliness" class="text-blue-500 dark:text-blue-400 inline-flex items-center">
                <span id="spark" class="mr-1 sm:mr-2">
                    <img src="{{ asset('images/icons/sparkle.svg') }}" alt="Sparkle" class="h-6 sm:h-12 w-auto">
                </span>
                {{ __('home.hero.title_cleanliness') }}
            </span>
            <br>
            {{ __('home.hero.title_2') }}
        </h1>
        <p class="mt-4 sm:mt-8 text-xs sm:text-base w-[90%] sm:w-[75%] mx-auto text-justify text-gray-700 dark:text-gray-300">
            {{ __('home.hero.description') }}
        </p>
        <div class="mt-6 sm:mt-10 flex flex-row items-center justify-center gap-2 sm:gap-x-6 px-4 sm:px-0">
            <a href="{{ route('services') }}"
                class="inline-flex items-center justify-center gap-2 rounded-full bg-[#0A1B3D] dark:bg-blue-600 px-5 sm:px-6 py-2.5 sm:py-3 font-bold text-base sm:text-lg hover:bg-[#152847] dark:hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl group">
                <!-- Icon Circle -->
                <span
                    class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 bg-white dark:bg-blue-950 rounded-full group-hover:rotate-45 transition-transform duration-300">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 dark:text-white text-blue-950" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M7 17L17 7M17 7H7M17 7V17" />
                    </svg>
                </span>
                <!-- Button Text -->
                <span class="font-sans text-xs sm:text-sm font-semibold dark:text-blue-950 text-white">{{ __('common.buttons.get_started') }}</span>
            </a>

            <a href="{{ route('quotation') }}"
                class="font-sans text-xs sm:text-sm border border-blue-950 dark:border-blue-400 px-5 sm:px-6 py-3 sm:py-4 rounded-full font-semibold text-blue-950 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 hover:border-blue-600 dark:hover:border-blue-300 transition-all text-center">
                {{ __('common.buttons.get_quote') }}
            </a>
        </div>
    </div>

    <!-- Dashboard Preview Image -->
    <div class="relative mx-auto max-w-5xl pb-16 sm:pb-32 lg:pb-48 px-4 sm:px-0">
        <div class="absolute inset-0 -z-10">
            <!-- Decorative Elements -->
            <div
                class="absolute top-0 left-0 w-24 h-24 sm:w-40 sm:h-40 bg-blue-200 dark:bg-blue-900 rounded-full blur-3xl opacity-30 dark:opacity-20">
            </div>
            <div
                class="absolute bottom-0 right-0 w-36 h-36 sm:w-60 sm:h-60 bg-blue-300 dark:bg-blue-800 rounded-full blur-3xl opacity-20 dark:opacity-15">
            </div>
        </div>

        <!-- Main Dashboard Card -->
        <div
            class="relative overflow-hidden rounded-2xl sm:rounded-3xl shadow-2xl soft-glow-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md p-3 sm:p-6 transition-colors">
            <div
                class="aspect-[16/10] bg-gradient-to-br from-blue-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-xl sm:rounded-2xl border border-gray-200 dark:border-gray-600 flex items-center justify-center transition-colors">
                    <img src="{{ asset('images/backgrounds/large_screenshot.svg') }}" alt="Dashboard Preview"
                        class="w-full h-full object-cover rounded-xl sm:rounded-2xl border border-gray-200 dark:border-gray-600">
            </div>
        </div>

        <!-- Floating Stats Cards -->

        <!-- Card 1: Top Left - Trusted Professionals -->
        <div class="hidden sm:block absolute -top-4 -left-4 sm:-left-8 lg:-left-12 xl:-left-16 
                                w-48 sm:w-52 lg:w-80 xl:w-80
                                z-20 transform -rotate-3
                                animate-float-slow">
            <div class="group relative backdrop-blur-xl bg-white/10 dark:bg-gray-800/70
                                    rounded-2xl sm:rounded-3xl p-3 sm:p-5 lg:p-6 
                                    shadow-[0_8px_32px_rgba(0,0,0,0.1)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.3)]
                                    border border-white/40 dark:border-gray-700/40
                                    hover:bg-white/80 dark:hover:bg-gray-800/80 hover:shadow-[0_12px_48px_rgba(0,0,0,0.15)] dark:hover:shadow-[0_12px_48px_rgba(0,0,0,0.4)]
                                    transition-all duration-500 ease-out
                                    hover:scale-105 hover:-rotate-2">
                <!-- Decorative blue dot -->
                <div class="absolute -top-1.5 -left-1.5 sm:-top-2 sm:-left-2 w-3 h-3 sm:w-4 sm:h-4 bg-blue-500 dark:bg-blue-400 rounded-full shadow-lg"></div>

                <div class="text-center">
                    <h3 class="text-xs sm:text-sm lg:text-lg font-bold text-blue-950 dark:text-white mb-1.5 sm:mb-2 lg:mb-3">
                        {{ __('home.cards.professionals_title') }}
                    </h3>
                    <p class="text-[10px] sm:text-xs lg:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ __('home.cards.professionals_desc') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 2: Top Right - Extensive Cleaning Experience -->
        <div class="hidden sm:block absolute top-32 sm:top-42 lg:top-64 -right-8 sm:-right-24 lg:-right-24 
                                w-48 sm:w-52 lg:w-80 xl:w-80
                                z-20 transform rotate-0
                                animate-float-medium">
            <div class="group relative backdrop-blur-xl bg-white/10 dark:bg-gray-800/70
                                    rounded-2xl sm:rounded-3xl p-3 sm:p-5 lg:p-6 
                                    shadow-[0_8px_32px_rgba(0,0,0,0.1)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.3)]
                                    border border-white/40 dark:border-gray-700/40
                                    hover:bg-white/80 dark:hover:bg-gray-800/80 hover:shadow-[0_12px_48px_rgba(0,0,0,0.15)] dark:hover:shadow-[0_12px_48px_rgba(0,0,0,0.4)]
                                    transition-all duration-500 ease-out
                                    hover:scale-105 hover:rotate-1">
                <!-- Decorative blue dot -->
                <div class="absolute -top-0.5 -right-0.5 sm:-top-1 sm:-right-1 w-3 h-3 sm:w-4 sm:h-4 bg-blue-500 dark:bg-blue-400 rounded-full shadow-lg"></div>

                <div class="text-center">
                    <h3 class="text-xs sm:text-sm lg:text-lg font-bold text-blue-950 dark:text-white mb-1.5 sm:mb-2 lg:mb-3">
                        {{ __('home.cards.experience_title') }}
                    </h3>
                    <p class="text-[10px] sm:text-xs lg:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ __('home.cards.experience_desc') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 3: Bottom Center-Right - Hassle-Free Online Booking -->
        <div class="absolute -bottom-[-8rem] right-96 sm:right-96 lg:right-96
                                w-48 sm:w-52 lg:w-80 xl:w-80
                                z-20 transform rotate-1
                                animate-float-fast
                                hidden md:block">
            <div class="group relative backdrop-blur-xl bg-white/10 dark:bg-gray-800/70
                                    rounded-2xl sm:rounded-3xl p-3 sm:p-5 lg:p-6 
                                    shadow-[0_8px_32px_rgba(0,0,0,0.1)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.3)]
                                    border border-white/40 dark:border-gray-700/40
                                    hover:bg-white/80 dark:hover:bg-gray-800/80 hover:shadow-[0_12px_48px_rgba(0,0,0,0.15)] dark:hover:shadow-[0_12px_48px_rgba(0,0,0,0.4)]
                                    transition-all duration-500 ease-out
                                    hover:scale-105 hover:rotate-2">
                <!-- Decorative blue dot -->
                <div class="absolute -bottom-1.5 -right-1.5 sm:-bottom-2 sm:-right-2 w-3 h-3 sm:w-4 sm:h-4 bg-blue-500 dark:bg-blue-400 rounded-full shadow-lg"></div>

                <div class="text-center">
                    <h3 class="text-xs sm:text-sm lg:text-lg font-bold text-blue-950 dark:text-white mb-1.5 sm:mb-2 lg:mb-3">
                        {{ __('home.cards.booking_title') }}
                    </h3>
                    <p class="text-[10px] sm:text-xs lg:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ __('home.cards.booking_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div id="container-2" class="bg-white dark:bg-gray-900 py-12 sm:py-24 lg:py-32 transition-colors">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <dl class="grid grid-cols-1 gap-x-8 gap-y-8 sm:gap-y-16 text-center lg:grid-cols-3">
                <div class="mx-auto flex max-w-xs flex-col gap-y-2 sm:gap-y-4">
                    <dt class="font-sans text-sm sm:text-base text-gray-600 dark:text-gray-300">{{ __('home.stats.transactions_label') }}</dt>
                    <dd
                        class="order-first font-sans text-2xl sm:text-3xl lg:text-5xl font-bold tracking-tight text-blue-950 dark:text-white">
                        <span class="counter text-blue-950 dark:text-white" data-target="44">0</span> million
                    </dd>
                </div>
                <div class="mx-auto flex max-w-xs flex-col gap-y-2 sm:gap-y-4">
                    <dt class="font-sans text-sm sm:text-base text-gray-600 dark:text-gray-300">{{ __('home.stats.clients_label') }}</dt>
                    <dd
                        class="order-first font-sans text-2xl sm:text-3xl lg:text-5xl font-bold tracking-tight text-blue-950 dark:text-white">
                        $<span class="counter text-blue-950 dark:text-white" data-target="119">0</span> trillion
                    </dd>
                </div>
                <div class="mx-auto flex max-w-xs flex-col gap-y-2 sm:gap-y-4">
                    <dt class="font-sans text-sm sm:text-base text-gray-600 dark:text-gray-300">{{ __('home.stats.users_label') }}</dt>
                    <dd
                        class="order-first font-sans text-2xl sm:text-3xl lg:text-5xl font-bold tracking-tight text-blue-950 dark:text-white">
                        <span class="counter text-blue-950 dark:text-white" data-target="46000">0</span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Service Introduction -->
    <div id="container-3" class="py-8 sm:py-16 bg-white dark:bg-gray-900 transition-colors">
        <div class="relative isolate text-center w-[90%] sm:w-[60%] mx-auto pt-3 pb-8 sm:pb-16">
            <div id="badge-container" class="flex flex-wrap justify-center gap-2">
                <span
                    class="bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300 text-[10px] sm:text-xs px-2 sm:px-2.5 py-0.5 rounded-xl transition-colors">{{ __('home.services.badge_hotel') }}</span>
                <span
                    class="bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300 text-[10px] sm:text-xs px-2 sm:px-2.5 py-0.5 rounded-xl transition-colors">{{ __('home.services.badge_snow') }}</span>
                <span
                    class="bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300 text-[10px] sm:text-xs px-2 sm:px-2.5 py-0.5 rounded-xl transition-colors">{{ __('home.services.badge_daily') }}</span>
            </div>
            <p id="subheader-1" class="text-blue-600 dark:text-blue-400 p-6 sm:p-12 font-bold text-sm sm:text-base">{{ __('home.services.subtitle') }}</p>
            <h1 id="header-1" class="text-3xl sm:text-6xl tracking-normal text-blue-950 dark:text-white p-2 sm:p-3">
                {{ __('home.services.title') }}
                <span id="cleaning" class="text-blue-500 dark:text-blue-400 inline-flex items-center font-bold">
                    {{ __('home.services.title_cleaning') }}
                    <span id="spark" class="mr-1 sm:mr-2">
                        <img src="{{ asset('images/icons/single-sparkle.svg') }}" alt="" class="h-6 sm:h-12 w-auto">
                    </span>
                </span>
                <br>
            </h1>
            <p id="para-desc"
                class="mt-4 sm:mt-8 text-xs sm:text-base w-[90%] sm:w-[75%] mx-auto text-justify text-gray-700 dark:text-gray-300">
                {{ __('home.services.description') }}
            </p>

            <div class="mt-8 sm:mt-16 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-8 w-full max-w-5xl mx-auto px-4 sm:px-0">
                <div id="icon-expertise" class="absolute -top-30 -left-30 z-10 flex justify-center w-full">
                </div>
                <div class="frosted-card px-4 sm:px-3 py-4 sm:py-3 rounded-xl sm:rounded-2xl soft-glow-2 bg-white/70 dark:bg-gray-800/70 border border-white/40 dark:border-gray-700/40 feature-card scroll-hidden transition-all duration-300 ease-in-out hover:scale-[1.02] hover:rotate-1 hover:shadow-2xl"
                    data-animation-delay="0">
                    <h3 class="text-sm sm:text-base font-bold mt-2 mb-3 sm:mb-4 text-blue-950 dark:text-white">{{ __('home.features.expertise_title') }}</h3>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 text-justify">
                        {{ __('home.features.expertise_desc') }}
                    </p>
                </div>

                <div class="frosted-card px-4 sm:px-6 py-4 sm:py-3 rounded-xl sm:rounded-2xl soft-glow-2 bg-white/70 dark:bg-gray-800/70 border border-white/40 dark:border-gray-700/40 feature-card scroll-hidden transition-all duration-300 ease-in-out hover:scale-[1.02] hover:rotate-1 hover:shadow-2xl"
                    data-animation-delay="200">
                    <div id="icon-trustworthy" class="feature-icon"></div>
                    <h3 class="text-sm sm:text-base font-bold mt-4 mb-3 sm:mb-4 text-blue-950 dark:text-white">{{ __('home.features.trustworthy_title') }}</h3>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 text-justify">
                        {{ __('home.features.trustworthy_desc') }}
                    </p>
                </div>

                <div class="frosted-card px-4 sm:px-6 py-4 sm:py-3 rounded-xl sm:rounded-2xl soft-glow-2 bg-white/70 dark:bg-gray-800/70 border border-white/40 dark:border-gray-700/40 feature-card scroll-hidden transition-all duration-300 ease-in-out hover:scale-[1.02] hover:rotate-1 hover:shadow-2xl"
                    data-animation-delay="400">
                    <div id="icon-licensed" class="feature-icon"></div>
                    <h3 class="text-sm sm:text-base font-bold mt-3 mb-3 sm:mb-4 text-blue-950 dark:text-white">{{ __('home.features.licensed_title') }}</h3>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 text-justify">
                        {{ __('home.features.licensed_desc') }}
                    </p>
                </div>
            </div>

            <div class="mt-6 sm:mt-10 flex items-center justify-center gap-x-5 px-4 sm:px-0">
                <button id="see-features-btn" type="button"
                    class="w-full sm:w-auto px-8 sm:px-10 py-3 sm:py-4 text-white z-10 bg-blue-950 dark:bg-blue-600 hover:bg-blue-900 dark:hover:bg-blue-700 font-medium rounded-full text-xs sm:text-sm transition-all">{{ __('common.buttons.see_features') }}</button>
            </div>

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
@endpush