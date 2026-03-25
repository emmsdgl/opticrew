@extends('components.layouts.general-landing')

@section('title', __('common.nav.home'))

@push ('styles')
    <style>
        /* Floating animations with different speeds */
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(-3deg); }
            50% { transform: translateY(-12px) rotate(-3deg); }
        }
        @keyframes float-medium {
            0%, 100% { transform: translateY(0px) rotate(2deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }
        @keyframes float-fast {
            0%, 100% { transform: translateY(0px) rotate(1deg); }
            50% { transform: translateY(-10px) rotate(1deg); }
        }
        .animate-float-slow { animation: float-slow 4s ease-in-out infinite; }
        .animate-float-medium { animation: float-medium 3.5s ease-in-out infinite; animation-delay: 0.5s; }
        .animate-float-fast { animation: float-fast 3s ease-in-out infinite; animation-delay: 1s; }

        /* Shine button */
        .shine-btn {
            background-image: linear-gradient(325deg, hsl(217 100% 55%) 0%, hsl(210 100% 69%) 55%, hsl(217 100% 30%) 90%);
            background-size: 280% auto;
            background-position: initial;
            transition: background-position 0.8s, transform 0.15s;
            box-shadow:
                0px 0px 16px rgba(30,64,175,0.4),
                0px 5px 5px -1px rgba(30,58,138,0.3),
                inset 4px 4px 8px rgba(96,165,250,0.25),
                inset -4px -4px 8px rgba(15,23,42,0.4);
        }
        .shine-btn:hover {
            background-position: right top;
        }
        .shine-btn:active {
            transform: scale(0.95);
        }
        @keyframes shine-sweep {
            0% { left: -75%; opacity: 0; }
            50% { opacity: 0.4; }
            100% { left: 125%; opacity: 0; }
        }
        .shine-btn:hover .shine-effect {
            animation: shine-sweep 0.8s ease-in-out;
        }

        /* Aurora text effect */
        .aurora-text {
            background: linear-gradient(135deg, #22d3ee, #4169e1, #06b6d4, #3b82f6, #22d3ee);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: aurora-text-shift 6s ease-in-out infinite;
        }
        @keyframes aurora-text-shift {
            0% { background-position: 0% 50%; }
            25% { background-position: 50% 100%; }
            50% { background-position: 100% 50%; }
            75% { background-position: 50% 0%; }
            100% { background-position: 0% 50%; }
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
        <h1 id="header-1" data-typing data-typing-duration="1.8" class="text-4xl sm:text-5xl lg:text-6xl tracking-tight text-blue-950 dark:text-white p-4 sm:p-10">
            <span class="font-extrabold">{{ __('home.hero.title_1') }}</span>
            <span id="cleanliness" class="relative inline-flex items-center">
                <span id="spark" class="mr-1 sm:mr-2">
                    <img src="{{ asset('images/icons/sparkle.svg') }}" alt="Sparkle" class="h-6 sm:h-12 w-auto">
                </span>
                <span class="aurora-text font-extrabold">{{ __('home.hero.title_cleanliness') }}</span>
            </span>
            <br>
            <span class="font-extrabold">{{ __('home.hero.title_2') }}</span>
        </h1>
        <p class="mt-4 sm:mt-8 text-xs sm:text-base w-[90%] sm:w-[75%] mx-auto text-justify text-gray-700 dark:text-gray-300">
            {{ __('home.hero.description') }}
        </p>
        <div class="mt-6 sm:mt-10 flex flex-row items-center justify-center gap-2 sm:gap-x-6 px-4 sm:px-0">
            <a href="{{ route('login') }}"
                class="shine-btn relative overflow-hidden inline-flex items-center justify-center gap-2 rounded-full px-5 sm:px-6 py-2.5 sm:py-3 font-bold text-base sm:text-lg text-white cursor-pointer group">
                <!-- Icon Circle -->
                <span
                    class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 bg-white/20 rounded-full group-hover:rotate-45 transition-transform duration-300">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M7 17L17 7M17 7H7M17 7V17" />
                    </svg>
                </span>
                <!-- Button Text -->
                <span class="font-sans text-xs sm:text-sm font-semibold text-white">{{ __('common.buttons.get_started') }}</span>
                <!-- Shine sweep overlay -->
                <div class="shine-effect absolute top-0 left-[-75%] w-[200%] h-full bg-white/30 skew-x-[-20deg] opacity-0 pointer-events-none z-20"></div>
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
    {{-- <div id="container-2" class="bg-white dark:bg-gray-900 py-12 sm:py-24 lg:py-32 transition-colors">
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
    </div> --}}

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
            <h1 id="header-1" data-typing data-typing-duration="1.5" class="text-3xl sm:text-6xl tracking-normal text-blue-950 dark:text-white p-2 sm:p-3">
                <span class="font-extrabold">{{ __('home.services.title') }}</span>
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

            {{-- 3D Card Slider --}}
            <div class="mt-8 sm:mt-16 relative left-1/2 -translate-x-1/2 w-screen" x-data="threeDSlider()" x-init="init()">
                {{-- Slider Container - transparent background, full width --}}
                <div class="relative w-full h-[350px] sm:h-[450px] md:h-[500px]"
                    x-ref="sliderContainer"
                    @wheel.prevent="handleWheel($event)"
                    @mousedown="handleMouseDown($event)"
                    @touchstart.passive="handleTouchStart($event)">

                    {{-- Slider Items --}}
                    <div class="relative z-10 h-full pointer-events-none scale-[0.70] sm:scale-75 w-full">
                        <template x-for="(item, index) in items" :key="'slide-'+index">
                            <div class="slider-card absolute top-1/2 left-1/2 cursor-pointer select-none rounded-2xl shadow-xl pointer-events-auto overflow-hidden will-change-transform border border-white/50 dark:border-gray-700/50"
                                :data-index="index"
                                @click="handleClick(index)"
                                :style="{
                                    width: 'clamp(140px, 25vw, 260px)',
                                    height: 'clamp(190px, 35vw, 360px)',
                                    marginTop: 'calc(-1 * clamp(95px, 17.5vw, 180px))',
                                    marginLeft: 'calc(-1 * clamp(70px, 12.5vw, 130px))',
                                }">
                                {{-- Content overlay --}}
                                <div class="slider-item-content absolute inset-0 z-10 will-change-[opacity]">
                                    {{-- Gradient overlay --}}
                                    <div class="absolute inset-0 z-10 bg-gradient-to-b from-blue-950/40 via-blue-900/20 via-40% to-blue-950/80"></div>
                                    {{-- Number --}}
                                    <div class="absolute z-10 text-white/60 top-3 left-4 text-[clamp(20px,6vw,50px)] font-black leading-none" x-text="item.num"></div>
                                    {{-- Title --}}
                                    <div class="absolute z-10 text-white bottom-4 left-4 right-4">
                                        <div class="text-[clamp(12px,2vw,18px)] font-bold drop-shadow-lg leading-tight" x-text="item.title"></div>
                                    </div>
                                    {{-- Image --}}
                                    <img :src="item.imageUrl" :alt="item.title" class="w-full h-full object-cover pointer-events-none" loading="lazy">
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Navigation hints --}}
                    {{-- <div class="absolute bottom-2 right-4 sm:bottom-4 sm:right-6 z-20 flex items-center gap-3 text-gray-400 dark:text-gray-500 text-xs pointer-events-none">
                        <span class="hidden sm:inline">Scroll or drag to explore</span>
                        <div class="flex gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div> --}}
                </div>

                {{-- Info Card below slider --}}
                {{-- <div class="mt-4 sm:mt-6 text-center transition-all duration-300">
                    <h3 class="text-lg sm:text-xl font-bold text-blue-950 dark:text-white mb-2" x-text="items[activeIndex]?.title || ''"></h3>
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 max-w-md mx-auto" x-text="items[activeIndex]?.description || ''"></p>
                </div> --}}

                {{-- Dot indicators --}}
                <div class="flex justify-center gap-1.5 mt-4">
                    <template x-for="(item, index) in items" :key="'dot-'+index">
                        <button @click="handleClick(index)"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="activeIndex === index ? 'bg-blue-600 dark:bg-blue-400 w-6' : 'bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 w-2'">
                        </button>
                    </template>
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
        function threeDSlider() {
            return {
                items: [
                    {
                        title: 'Surface Sanitizing',
                        num: '01',
                        imageUrl: '{{ asset("images/backgrounds/card-sliders/1.png") }}',
                        description: 'Thorough surface sanitizing with professional-grade disinfectants to keep your spaces safe and germ-free.'
                    },
                    {
                        title: 'Spray & Disinfect',
                        num: '02',
                        imageUrl: '{{ asset("images/backgrounds/card-sliders/2.png") }}',
                        description: 'Precision spray treatment targeting high-touch areas for maximum hygiene protection.'
                    },
                    {
                        title: 'Detail Wiping',
                        num: '03',
                        imageUrl: '{{ asset("images/backgrounds/card-sliders/3.png") }}',
                        description: 'Careful hand-wiping of delicate surfaces and hard-to-reach areas for a spotless finish.'
                    },
                    {
                        title: 'Window Cleaning',
                        num: '04',
                        imageUrl: '{{ asset("images/backgrounds/card-sliders/4.png") }}',
                        description: 'Crystal-clear window and glass cleaning using professional tools and streak-free solutions.'
                    },
                    {
                        title: 'Full Team Service',
                        num: '05',
                        imageUrl: '{{ asset("images/backgrounds/card-sliders/5.png") }}',
                        description: 'Our dedicated crew works together to deep clean your entire space efficiently and thoroughly.'
                    },
                    {
                        title: 'Bathroom Care',
                        num: '06',
                        imageUrl: '{{ asset("images/backgrounds/card-sliders/6.png") }}',
                        description: 'Complete bathroom sanitation from sinks to fixtures, leaving every surface sparkling clean.'
                    },
                    {
                        title: 'Office Maintenance',
                        num: '07',
                        imageUrl: '{{ asset("images/backgrounds/card-sliders/7.png") }}',
                        description: 'Regular office cleaning and maintenance to create a productive and welcoming work environment.'
                    },
                    {
                        title: 'Fixture Polishing',
                        num: '08',
                        imageUrl: '{{ asset("images/backgrounds/card-sliders/8.png") }}',
                        description: 'Detailed cleaning and polishing of faucets, handles, and chrome fixtures to a mirror shine.'
                    },
                    {
                        title: 'Deep Scrubbing',
                        num: '09',
                        imageUrl: '{{ asset("images/backgrounds/card-sliders/9.png") }}',
                        description: 'Intensive scrubbing for built-up grime and residue, restoring surfaces to their original condition.'
                    },
                    {
                        title: 'Kitchen Degreasing',
                        num: '10',
                        imageUrl: '{{ asset("images/backgrounds/card-sliders/10.png") }}',
                        description: 'Professional kitchen counter and surface degreasing for a hygienic cooking environment.'
                    },
                ],
                activeFloat: 0,
                targetFloat: 0,
                activeIndex: 0,
                isDown: false,
                startX: 0,
                rafId: null,
                cache: {},
                speedWheel: 0.02,
                speedDrag: -0.05,

                init() {
                    // Start at the middle card
                    const mid = Math.floor(this.items.length / 2);
                    this.activeFloat = mid;
                    this.targetFloat = mid;
                    this.activeIndex = mid;

                    this.$nextTick(() => {
                        this.startLoop();
                        // Global mouse/touch move and up
                        window.addEventListener('mousemove', (e) => this.handleMouseMove(e));
                        window.addEventListener('mouseup', () => this.handleMouseUp());
                        window.addEventListener('touchmove', (e) => this.handleTouchMove(e), { passive: true });
                        window.addEventListener('touchend', () => this.handleMouseUp());
                    });
                },

                startLoop() {
                    const loop = () => {
                        this.update();
                        this.rafId = requestAnimationFrame(loop);
                    };
                    this.rafId = requestAnimationFrame(loop);
                },

                // Wrap a value into [0, n) range
                mod(val, n) {
                    return ((val % n) + n) % n;
                },

                // Shortest signed distance on a circular track of length n
                circularDiff(from, to, n) {
                    const d = this.mod(to - from, n);
                    return d > n / 2 ? d - n : d;
                },

                update() {
                    const cards = this.$refs.sliderContainer?.querySelectorAll('.slider-card');
                    if (!cards || !cards.length) return;

                    const n = this.items.length;

                    // Lerp towards target on the circular track
                    const diff = this.circularDiff(this.activeFloat, this.targetFloat, n);
                    this.activeFloat += diff * 0.1;
                    this.activeFloat = this.mod(this.activeFloat, n);

                    this.activeIndex = Math.round(this.activeFloat) % n;

                    cards.forEach((el, index) => {
                        // Circular offset: shortest path from activeFloat to this index
                        const offset = this.circularDiff(this.activeFloat, index, n);

                        const tx = offset * 55;
                        const ty = offset * 13;
                        const rot = offset * 8;

                        const dist = Math.abs(offset);
                        const z = n - dist;
                        const opacity = dist < n / 2 ? Math.max(0, 1 - dist * 0.35) : 0;

                        const newTransform = `translate3d(${tx}%, ${ty}%, 0) rotate(${rot}deg)`;
                        const newZIndex = Math.round(z * 10).toString();
                        const newOpacity = Math.max(0, Math.min(1, opacity)).toString();

                        if (!this.cache[index]) {
                            this.cache[index] = { transform: '', zIndex: '', opacity: '' };
                        }

                        const cache = this.cache[index];

                        if (cache.transform !== newTransform) {
                            el.style.transform = newTransform;
                            cache.transform = newTransform;
                        }
                        if (cache.zIndex !== newZIndex) {
                            el.style.zIndex = newZIndex;
                            cache.zIndex = newZIndex;
                        }

                        const inner = el.querySelector('.slider-item-content');
                        if (inner && cache.opacity !== newOpacity) {
                            inner.style.opacity = newOpacity;
                            cache.opacity = newOpacity;
                        }
                    });
                },

                handleWheel(e) {
                    const delta = e.deltaY * this.speedWheel;
                    this.targetFloat = this.mod(this.targetFloat + delta, this.items.length);
                },

                handleMouseDown(e) {
                    this.isDown = true;
                    this.startX = e.clientX || (e.touches && e.touches[0].clientX) || 0;
                },

                handleTouchStart(e) {
                    this.isDown = true;
                    this.startX = e.touches[0].clientX;
                },

                handleMouseMove(e) {
                    if (!this.isDown) return;
                    const x = e.clientX || (e.touches && e.touches[0].clientX) || 0;
                    const diff = (x - this.startX) * this.speedDrag;
                    this.targetFloat = this.mod(this.targetFloat + diff, this.items.length);
                    this.startX = x;
                },

                handleTouchMove(e) {
                    if (!this.isDown) return;
                    const x = e.touches[0].clientX;
                    const diff = (x - this.startX) * this.speedDrag;
                    this.targetFloat = this.mod(this.targetFloat + diff, this.items.length);
                    this.startX = x;
                },

                handleMouseUp() {
                    this.isDown = false;
                },

                handleClick(index) {
                    this.targetFloat = index;
                },

                destroy() {
                    if (this.rafId) cancelAnimationFrame(this.rafId);
                }
            };
        }
    </script>
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