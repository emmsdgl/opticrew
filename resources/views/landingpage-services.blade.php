@extends('components.layouts.general-landing')

@section('title', __('services.title'))

@push('styles')
    <style>
        body{
            background-image: none;
        }

        @keyframes auroraShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .aurora-text {
            background: linear-gradient(135deg, #60a5fa, #3b82f6, #818cf8, #6366f1, #3b82f6, #60a5fa);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: auroraShift 6s ease-in-out infinite;
        }
        /* Vertical text for collapsed state */
        .writing-mode-vertical {
            writing-mode: vertical-rl;
            text-orientation: mixed;
        }

        /* Expanded card takes more space */
        .carousel-card.expanded {
            flex: 3 !important;
        }

        /* Hide collapsed content when expanded */
        .carousel-card.expanded .card-collapsed-content {
            opacity: 0;
            pointer-events: none;
        }

        /* Show expanded content when expanded */
        .carousel-card.expanded .card-expanded-content {
            opacity: 1 !important;
        }

        /* Smooth transitions */
        .carousel-card {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .carousel-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        /* Dark mode card shadows */
        .dark .carousel-card {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .dark .carousel-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7);
        }

        /* Mobile: Stack cards vertically and show one at a time - ONLY for smallest screens */
        @media (max-width: 639px) {
            #expanding-carousel {
                flex-direction: column;
                height: auto !important;
                gap: 1rem;
            }

            .carousel-card {
                width: 100% !important;
                min-height: 120px;
                flex: none !important;
            }

            .carousel-card.expanded {
                min-height: 400px;
                flex: none !important;
            }

            /* Hide non-expanded cards on mobile */
            .carousel-card:not(.expanded) {
                display: none;
            }

            /* Show all cards when no card is expanded */
            .no-active-card .carousel-card {
                display: flex !important;
            }
        }
    </style>
@endpush

@section('content')
    @php
        // Slide Carousel Data
    $slides = [
        [
            'title' => __('services.carousel.slide1_title'),
            'subtitle' => __('services.carousel.slide1_subtitle'),
            'description' => __('services.carousel.slide1_description'),
            'buttonText' => __('services.carousel.slide1_button'),
            'buttonUrl' => route('services'),
            'backgroundImage' => asset('images/backgrounds/services_carousel/cover-page.svg'),
            'textColor' => 'text-white',
            'darkTextColor' => 'text-white',
        ],
        [
            'title' => __('services.carousel.slide2_title'),
            'price' => __('services.carousel.slide2_price'),
            'description' => __('services.carousel.slide2_description'),
            'badges' => __('services.carousel.slide2_badges'),
            'buttonText' => __('services.carousel.slide2_button'),
            'buttonUrl' => route('quotation'),
            'backgroundImage' => asset('images/backgrounds/services_carousel/deep-cleaning.svg'),
            'textColor' => 'text-gray-900',
            'darkTextColor' => 'text-white',
        ],
        [
            'title' => __('services.carousel.slide3_title'),
            'price' => __('services.carousel.slide3_price'),
            'description' => __('services.carousel.slide3_description'),
            'badges' => __('services.carousel.slide3_badges'),
            'buttonText' => __('services.carousel.slide3_button'),
            'buttonUrl' => route('quotation'),
            'backgroundImage' => asset('images/backgrounds/services_carousel/full-daily-cleaning.svg'),
            'textColor' => 'text-gray-900',
            'darkTextColor' => 'text-white',
        ],
        [
            'title' => __('services.carousel.slide4_title'),
            'price' => __('services.carousel.slide4_price'),
            'description' => __('services.carousel.slide4_description'),
            'badges' => __('services.carousel.slide4_badges'),
            'buttonText' => __('services.carousel.slide4_button'),
            'buttonUrl' => route('quotation'),
            'backgroundImage' => asset('images/backgrounds/services_carousel/snow-out-cleaning.svg'),
            'textColor' => 'text-gray-900',
            'darkTextColor' => 'text-white',
        ],
        [
            'title' => __('services.carousel.slide5_title'),
            'price' => __('services.carousel.slide5_price'),
            'description' => __('services.carousel.slide5_description'),
            'badges' => __('services.carousel.slide5_badges'),
            'buttonText' => __('services.carousel.slide5_button'),
            'buttonUrl' => route('quotation'),
            'backgroundImage' => asset('images/backgrounds/services_carousel/full-daily-cleaning.svg'),
            'textColor' => 'text-gray-900',
            'darkTextColor' => 'text-white',
        ],
    ];
        // Expanding Cards Data
    $expandingCards = [
            [
            'title' => __('services.expanding.card1_title'),
            'price' => '',
            'description' => __('services.expanding.card1_description'),
            'badges' => __('services.expanding.card1_badges'),
            'buttonText' => '',
            'buttonUrl' => route('quotation'),
            'bgImage' => asset('images/backgrounds/areas-serve-bg.svg'),
            'gradient' => '',
            'darkGradient' => 'from-blue-600 via-blue-700 to-blue-800',
            'overlayOpacity' => '60',
        ],
        [
            'title' => __('services.expanding.card2_title'),
            'price' => '',
            'description' => __('services.expanding.card2_description'),
            'badges' => __('services.expanding.card2_badges'),
            'buttonText' => '',
            'buttonUrl' => route('quotation'),

            'bgImage' => asset('images/backgrounds/inari-bg.svg'),
            // Optional: Keep gradient overlay for better text readability
            'gradient' => '',
            'darkGradient' => 'from-blue-600 via-blue-700 to-blue-800',
            'overlayOpacity' => '40', // Lower opacity to show more of the image
        ],
        [
            'title' => __('services.expanding.card3_title'),
            'price' => '',
            'description' => __('services.expanding.card3_description'),
            'badges' => __('services.expanding.card3_badges'),
            'buttonText' => '',
            'buttonUrl' => route('quotation'),
            'bgImage' => asset('images/backgrounds/sar-bg.svg'),
            'gradient' => '',
            'darkGradient' => 'from-blue-600 via-blue-700 to-blue-800',
            'overlayOpacity' => '30',
        ],
        [
            'title' => __('services.expanding.card4_title'),
            'price' => '',
            'description' => __('services.expanding.card4_description'),
            'badges' => __('services.expanding.card4_badges'),
            'buttonText' => '',
            'buttonUrl' => route('quotation'),
            'bgImage' => asset('images/backgrounds/lapland-region-bg.svg'),
            'gradient' => '',
            'darkGradient' => 'from-blue-600 via-blue-700 to-blue-800',
            'overlayOpacity' => '35',
        ]
    ];
    @endphp

    <!-- SECTION 1: HERO + SERVICES CAROUSEL -->
    <section class="w-full py-12 sm:py-20 transition-colors duration-300 mb-8">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="text-base/7 font-bold text-blue-600 dark:text-blue-400">What We Offer · Our Services</h2>
                <h3 data-typing data-typing-duration="1.8" class="my-8 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 dark:text-white">
                    From Request to Refresh — <br>We Make It <span class="aurora-text">Shine</span>
                </h3>
                <p class="mx-auto mt-6 max-w-2xl text-center text-base text-gray-500 dark:text-gray-300">
                    Discover our range of professional cleaning services designed to make your home or business shine.
                </p>
            </div>

            {{-- Chain Carousel --}}
            @php
                $serviceItems = [
                    [
                        'name' => __('services.carousel.slide2_title'),
                        'price' => __('services.carousel.slide2_price'),
                        'description' => __('services.carousel.slide2_description'),
                        'badges' => __('services.carousel.slide2_badges'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/><path d="M20 3v4"/><path d="M22 5h-4"/><path d="M4 17v2"/><path d="M5 18H3"/></svg>',
                    ],
                    [
                        'name' => __('services.carousel.slide3_title'),
                        'price' => __('services.carousel.slide3_price'),
                        'description' => __('services.carousel.slide3_description'),
                        'badges' => __('services.carousel.slide3_badges'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>',
                    ],
                    [
                        'name' => __('services.carousel.slide4_title'),
                        'price' => __('services.carousel.slide4_price'),
                        'description' => __('services.carousel.slide4_description'),
                        'badges' => __('services.carousel.slide4_badges'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M2 12h10"/><path d="M9 4v16"/><path d="m3 9 3 3-3 3"/><path d="M12 6 9 9 6 6"/><path d="m6 18 3-3 1.5 1.5"/><path d="M20 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0Z"/></svg>',
                    ],
                    [
                        'name' => __('services.carousel.slide5_title'),
                        'price' => __('services.carousel.slide5_price'),
                        'description' => __('services.carousel.slide5_description'),
                        'badges' => __('services.carousel.slide5_badges'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M8 2h8l4 10H4L8 2Z"/><path d="M12 12v6"/><path d="M8 22v-2c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v2H8Z"/><path d="M2 22h20"/></svg>',
                    ],
                    [
                        'name' => 'Light Daily Cleaning',
                        'price' => '€ 50 - € 80',
                        'description' => 'Quick daily maintenance cleaning to keep your space tidy and fresh between deep cleans.',
                        'badges' => ['Dusting', 'Vacuuming', 'Surface Wipe', 'Trash Removal'],
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>',
                    ],
                    [
                        'name' => 'Linen & Laundry Service',
                        'price' => '€ 40 - € 70',
                        'description' => 'Professional linen replacement, washing, and fresh bedding preparation for guest stays.',
                        'badges' => ['Bed Linen', 'Towel Replacement', 'Ironing', 'Folding'],
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/></svg>',
                    ],
                ];
            @endphp
            <x-material-ui.chain-carousel
                :items="$serviceItems"
                :scrollSpeedMs="2200"
                :visibleItemCount="7"
            />
        </div>
    </section>

    <!-- SECTION 2: EXPANDING CARDS CAROUSEL -->
    <section class="w-full sm:py-6 lg:py-8 transition-colors duration-300">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-8 sm:mb-12">
                <h2 class="text-xs sm:text-sm md:text-base text-blue-600 dark:text-blue-400 font-semibold mb-4 sm:mb-6 lg:mb-8">{{ __('services.section2.subtitle') }}</h2>
                <h2 data-typing data-typing-duration="1.2" class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-2 sm:mb-4">
                    {{ __('services.section2.title_before') }} <span class="text-blue-600">{{ __('services.section2.title_highlight') }}</span>
                </h2>
            </div>

            <!-- Expanding Cards with Glow Effect -->
            <x-material-ui.glowing-cards :glowRadius="20" :glowOpacity="1" :animationDuration="400">
                <div id="expanding-carousel" class="flex gap-2 sm:gap-3 lg:gap-4 justify-center items-stretch h-auto lg:h-[600px] w-full overflow-hidden">
                    @foreach($expandingCards as $index => $card)
                        <x-material-ui.expand-card
                        :title="$card['title']"
                        :price="$card['price']"
                        :description="$card['description']"
                        :badges="$card['badges']"
                        :bgImage="$card['bgImage'] ?? null"
                        :darkBgImage="$card['darkBgImage'] ?? null"
                        :gradient="$card['gradient']"
                        :darkGradient="$card['darkGradient']"
                        :overlayOpacity="$card['overlayOpacity'] ?? '40'"
                        :buttonText="$card['buttonText']"
                        :buttonUrl="$card['buttonUrl']"
                        :index="$index"
                        :glowColor="'#3b82f6'"
                        />
                    @endforeach
                </div>
            </x-material-ui.glowing-cards>

            <div class="mt-8 sm:mt-12 w-full justify-center align-items-center">
                <p class="text-center text-base text-gray-500 dark:text-gray-300 mb-6 sm:mb-8 px-4">
                    {{ __('services.section2.footer_text') }}
                </p>
            </div>
        </div>
    </section>

    
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ===== EXPANDING CARDS FUNCTIONALITY =====
            const expandingCarouselContainer = document.getElementById('expanding-carousel');
            if (!expandingCarouselContainer) return;
            const cards = expandingCarouselContainer.querySelectorAll('.carousel-card');
            let activeCard = null;
            const isMobile = window.innerWidth < 640; // Changed from 1024 to 640 (sm breakpoint)

            // Initialize first card as expanded
            if (cards.length > 0) {
                cards[0].classList.add('expanded');
                activeCard = cards[0];
            }

            function updateCarouselState() {
                if (activeCard) {
                    expandingCarouselContainer.classList.remove('no-active-card');
                } else {
                    expandingCarouselContainer.classList.add('no-active-card');
                }
            }

            cards.forEach((card) => {
                card.addEventListener('click', function () {
                    if (activeCard === card) {
                        // Clicking the same card - collapse it (show all cards on mobile)
                        card.classList.remove('expanded');
                        activeCard = null;
                        updateCarouselState();
                    } else {
                        // Clicking a different card - expand it
                        cards.forEach(c => c.classList.remove('expanded'));
                        card.classList.add('expanded');
                        activeCard = card;
                        updateCarouselState();
                    }
                });

                // Hover expands the card on non-mobile
                if (!isMobile) {
                    card.addEventListener('mouseenter', function () {
                        cards.forEach(c => c.classList.remove('expanded'));
                        card.classList.add('expanded');
                        activeCard = card;
                        updateCarouselState();
                    });
                }
            });

            // Only for non-mobile: clicking outside collapses cards
            if (!isMobile) {
                document.addEventListener('click', function (e) {
                    if (!e.target.closest('.carousel-card')) {
                        cards.forEach(c => c.classList.remove('expanded'));
                        activeCard = null;
                        updateCarouselState();
                    }
                });

                if (expandingCarouselContainer) {
                    expandingCarouselContainer.addEventListener('mouseleave', function () {
                        if (!activeCard) {
                            cards.forEach(c => c.classList.remove('expanded'));
                        }
                    });
                }
            }

            // Initialize state
            updateCarouselState();
        });
    </script>

@endpush