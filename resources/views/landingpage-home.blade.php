@extends('components.layouts.general-landing')

@section('title', 'Home')

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
    <!-- Hero Section - DONE REVISION-->
    <div id="container-1" class="relative isolate text-center w-[60%] mx-auto pt-11 pb-24">
        <div class="hidden sm:mb-8 sm:flex sm:justify-center">
            <div
                class="relative rounded-full px-3 py-1 text-sm/6 text-gray-600 dark:text-gray-300 ring-1 ring-gray-900/10 dark:ring-gray-100/10 hover:ring-gray-900/20 dark:hover:ring-gray-100/20 transition-all">
                OptiCrew: Your Doorway To A Clean Space.
            </div>
        </div>
        <h1 id="header-1" class="text-6xl tracking-normal text-blue-950 dark:text-white p-10 sm:text-6xl">
            Delivering
            <span id="cleanliness" class="text-blue-500 dark:text-blue-400 inline-flex items-center">
                <span id="spark" class="mr-2">
                    <img src="{{ asset('images/icons/sparkle.svg') }}" alt="Sparkle" class="h-12 w-auto">
                </span>
                cleanliness
            </span>
            <br>
            in every corner
        </h1>
        <p class="mt-8 text-[12px] text-pretty sm:text-base w-[75%] mx-auto text-justify text-gray-700 dark:text-gray-300">
            At Fin-noys, we believe a clean space is the foundation for a productive and healthy life. As a trusted
            cleaning service provider, we specialize in delivering tailored solutions for homes, offices, and commercial
            spaces. From routine maintenance to deep cleaning, our skilled team is committed to delivering exceptional
            quality with attention to every detail. Choose Fin-noys for a spotless environment you can rely on.
        </p>
        <div class="mt-10 flex items-center justify-center gap-x-6">
            <a href="{{ route('services') }}"
                class="inline-flex items-center gap-2 rounded-full bg-[#0A1B3D] dark:bg-blue-600 px-6 py-3 font-bold text-lg hover:bg-[#152847] dark:hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl group">
                <!-- Icon Circle -->
                <span
                    class="flex items-center justify-center w-8 h-8 bg-white dark:bg-blue-950 rounded-full group-hover:rotate-45 transition-transform duration-300">
                    <svg class="w-4 h-4 dark:text-white text-blue-950" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M7 17L17 7M17 7H7M17 7V17" />
                    </svg>
                </span>
                <!-- Button Text -->
                <span class="font-sans text-sm font-semibold dark:text-blue-950 text-white">Get Started</span>
            </a>

            <a href="{{ route('quotation') }}"
                class="font-sans text-sm border border-blue-950 dark:border-blue-400 px-6 py-4 rounded-full font-semibold text-blue-950 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 hover:border-blue-600 dark:hover:border-blue-300 transition-all">
                Get a Free Quote
            </a>
        </div>
    </div>

    <!-- Dashboard Preview Image -->
    <div class="relative mx-auto max-w-5xl pb-32 sm:pb-40 lg:pb-48">
        <div class="absolute inset-0 -z-10">
            <!-- Decorative Elements -->
            <div
                class="absolute top-0 left-0 w-40 h-40 bg-blue-200 dark:bg-blue-900 rounded-full blur-3xl opacity-30 dark:opacity-20">
            </div>
            <div
                class="absolute bottom-0 right-0 w-60 h-60 bg-blue-300 dark:bg-blue-800 rounded-full blur-3xl opacity-20 dark:opacity-15">
            </div>
        </div>

        <!-- Main Dashboard Card -->
        <div
            class="relative overflow-hidden rounded-3xl shadow-2xl soft-glow-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md p-6 transition-colors">
            <div
                class="aspect-[16/10] bg-gradient-to-br from-blue-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-2xl border border-gray-200 dark:border-gray-600 flex items-center justify-center transition-colors">
                    <img src="{{ asset('images/backgrounds/large_screenshot.svg') }}" alt="Dashboard Preview"
                        class="w-full h-full object-cover rounded-2xl border border-gray-200 dark:border-gray-600">
            </div>
        </div>

        <!-- Floating Stats Cards - Positioned like screenshot -->

        <!-- Card 1: Top Left - Trusted Professionals -->
        <div class="absolute -top-4 -left-8 sm:-left-12 lg:-left-16 
                                w-80 sm:w-52 lg:w-80 xl:w-80
                                z-20 transform -rotate-3
                                animate-float-slow">
            <div class="group relative backdrop-blur-xl bg-white/10 dark:bg-gray-800/70
                                    rounded-3xl p-5 sm:p-6 
                                    shadow-[0_8px_32px_rgba(0,0,0,0.1)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.3)]
                                    border border-white/40 dark:border-gray-700/40
                                    hover:bg-white/80 dark:hover:bg-gray-800/80 hover:shadow-[0_12px_48px_rgba(0,0,0,0.15)] dark:hover:shadow-[0_12px_48px_rgba(0,0,0,0.4)]
                                    transition-all duration-500 ease-out
                                    hover:scale-105 hover:-rotate-2">
                <!-- Decorative blue dot -->
                <div class="absolute -top-2 -left-2 w-4 h-4 bg-blue-500 dark:bg-blue-400 rounded-full shadow-lg"></div>

                <div class="text-center">
                    <h3 class="text-sm sm:text-base lg:text-lg font-bold text-blue-950 dark:text-white mb-2 sm:mb-3">
                        Trusted Professionals
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        Our well-trained and professional cleaning staff are dedicated to maintaining a clean, healthy
                        environment.
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 2: Top Right - Extensive Cleaning Experience -->
        <div class="absolute top-64 sm:top-42 -right-24 sm:-right-24 lg:-right-24 
                                w-80 sm:w-52 lg:w-80 xl:w-80
                                z-20 transform rotate-0
                                animate-float-medium
                                hidden sm:block">
            <div class="group relative backdrop-blur-xl bg-white/10 dark:bg-gray-800/70
                                    rounded-3xl p-5 sm:p-6 
                                    shadow-[0_8px_32px_rgba(0,0,0,0.1)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.3)]
                                    border border-white/40 dark:border-gray-700/40
                                    hover:bg-white/80 dark:hover:bg-gray-800/80 hover:shadow-[0_12px_48px_rgba(0,0,0,0.15)] dark:hover:shadow-[0_12px_48px_rgba(0,0,0,0.4)]
                                    transition-all duration-500 ease-out
                                    hover:scale-105 hover:rotate-1">
                <!-- Decorative blue dot -->
                <div class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 dark:bg-blue-400 rounded-full shadow-lg"></div>

                <div class="text-center">
                    <h3 class="text-sm sm:text-base lg:text-lg font-bold text-blue-950 dark:text-white mb-2 sm:mb-3">
                        Extensive Cleaning Experience
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        Professional cleaning services provider with extensive experience in the hospitality industry
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 3: Bottom Center-Right - Hassle-Free Online Booking -->
        <div class="absolute -bottom-[-8rem] right-96 sm:right-96 lg:right-96
                                w-80 sm:w-52 lg:w-80 xl:w-80
                                z-20 transform rotate-1
                                animate-float-fast
                                hidden md:block">
            <div class="group relative backdrop-blur-xl bg-white/10 dark:bg-gray-800/70
                                    rounded-3xl p-5 sm:p-6 
                                    shadow-[0_8px_32px_rgba(0,0,0,0.1)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.3)]
                                    border border-white/40 dark:border-gray-700/40
                                    hover:bg-white/80 dark:hover:bg-gray-800/80 hover:shadow-[0_12px_48px_rgba(0,0,0,0.15)] dark:hover:shadow-[0_12px_48px_rgba(0,0,0,0.4)]
                                    transition-all duration-500 ease-out
                                    hover:scale-105 hover:rotate-2">
                <!-- Decorative blue dot -->
                <div class="absolute -bottom-2 -right-2 w-4 h-4 bg-blue-500 dark:bg-blue-400 rounded-full shadow-lg"></div>

                <div class="text-center">
                    <h3 class="text-sm sm:text-base lg:text-lg font-bold text-blue-950 dark:text-white mb-2 sm:mb-3">
                        Hassle-Free Online Booking
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        Secure efficient cleaning process in a click.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div id="container-2" class="bg-white dark:bg-gray-900 py-24 sm:py-32 transition-colors">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <dl class="grid grid-cols-1 gap-x-8 gap-y-16 text-center lg:grid-cols-3">
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="font-sans text-base/7 text-gray-600 dark:text-gray-300">Transactions every 24 hours</dt>
                    <dd
                        class="order-first font-sans text-3xl font-bold tracking-tight text-blue-950 dark:text-white sm:text-5xl">
                        <span class="counter text-blue-950 dark:text-white" data-target="44">0</span> million
                    </dd>
                </div>
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="font-sans text-base/7 text-gray-600 dark:text-gray-300">Assets under holding</dt>
                    <dd
                        class="order-first font-sans text-3xl font-bold tracking-tight text-blue-950 dark:text-white sm:text-5xl">
                        $<span class="counter text-blue-950 dark:text-white" data-target="119">0</span> trillion
                    </dd>
                </div>
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="font-sans text-base/7 text-gray-600 dark:text-gray-300">New users annually</dt>
                    <dd
                        class="order-first font-sans text-3xl font-bold tracking-tight text-blue-950 dark:text-white sm:text-5xl">
                        <span class="counter text-blue-950 dark:text-white" data-target="46000">0</span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Service Introduction -->
    <div id="container-3" class="py-8 sm:py-16 bg-white dark:bg-gray-900 transition-colors">
        <div class="relative isolate text-center w-[60%] mx-auto pt-3 pb-16">
            <div id="badge-container">
                <span
                    class="bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300 text-xs me-2 px-2.5 py-0.5 rounded-xl transition-colors">Hotel
                    Cleaning</span>
                <span
                    class="bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300 text-xs me-2 px-2.5 py-0.5 rounded-xl transition-colors">Snowout</span>
                <span
                    class="bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300 text-xs me-2 px-2.5 py-0.5 rounded-xl transition-colors">Daily
                    Cleaning</span>
            </div>
            <p id="subheader-1" class="text-blue-600 dark:text-blue-400 p-12 font-bold">Why Choose Fin-noys?</p>
            <h1 id="header-1" class="text-6xl tracking-normal text-blue-950 dark:text-white p-3 sm:text-6xl">
                Your trusted partner in professional
                <span id="cleaning" class="text-blue-500 dark:text-blue-400 inline-flex items-center font-bold">
                    cleaning
                    <span id="spark" class="mr-2">
                        <img src="{{ asset('images/icons/single-sparkle.svg') }}" alt="" class="h-12 w-auto">
                    </span>
                </span>
                <br>
            </h1>
            <p id="para-desc"
                class="mt-8 text-[12px] text-pretty sm:text-base w-[75%] mx-auto text-justify text-gray-700 dark:text-gray-300">
                With a team of dedicated experts and a commitment to excellence, we make every space feel fresh,
                safe, and inviting — so you can focus on what truly matters — ensuring premium, meticulous, and
                worry-free cleaning for every client
            </p>

            <div class="mt-16 grid grid-cols-1 lg:grid-cols-3 gap-8 w-full max-w-5xl mx-auto">
                <div id="icon-expertise" class="absolute -top-30 -left-30 z-10 flex justify-center w-full">
                </div>
                <div class="frosted-card px-3 py-3 rounded-2xl soft-glow-2 bg-white/70 dark:bg-gray-800/70 border border-white/40 dark:border-gray-700/40 feature-card scroll-hidden transition-all duration-300 ease-in-out hover:scale-[1.02] hover:rotate-1 hover:shadow-2xl"
                    data-animation-delay="0">
                    <h3 class="text-base font-bold mt-2 mb-4 text-blue-950 dark:text-white">Proven Expertise</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 text-justify">
                        Our proven expertise, reliable team, and commitment to excellence ensure every home
                        and business receives the highest standard of care — every time.
                    </p>
                </div>

                <div class="frosted-card px-6 py-3 rounded-2xl soft-glow-2 bg-white/70 dark:bg-gray-800/70 border border-white/40 dark:border-gray-700/40 feature-card scroll-hidden transition-all duration-300 ease-in-out hover:scale-[1.02] hover:rotate-1 hover:shadow-2xl"
                    data-animation-delay="200">
                    <div id="icon-trustworthy" class="feature-icon"></div>
                    <h3 class="text-base font-bold mt-4 mb-4 text-blue-950 dark:text-white">Professional and Trustworthy
                    </h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 text-justify">
                        We deliver meticulous, high-quality cleaning that transforms spaces and leaves lasting
                        impressions.
                    </p>
                </div>

                <div class="frosted-card px-6 py-3 rounded-2xl soft-glow-2 bg-white/70 dark:bg-gray-800/70 border border-white/40 dark:border-gray-700/40 feature-card scroll-hidden transition-all duration-300 ease-in-out hover:scale-[1.02] hover:rotate-1 hover:shadow-2xl"
                    data-animation-delay="400">
                    <div id="icon-licensed" class="feature-icon"></div>
                    <h3 class="text-base font-bold mt-3 mb-4 text-blue-950 dark:text-white">Licensed Business</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 text-justify">
                        Our official certification ensures that every service we provide meets strict quality
                        and
                        safety
                        standards, giving you complete peace of mind.
                    </p>
                </div>
            </div>

            <div class="mt-10 flex items-center justify-center gap-x-5">
                <button id="see-features-btn" type="button"
                    class="w-full sm:w-auto px-10 py-4 text-white z-10 bg-blue-950 dark:bg-blue-600 hover:bg-blue-900 dark:hover:bg-blue-700 font-medium rounded-full text-sm transition-all">See
                    Features</button>
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