@extends('components.layouts.general-landing')

@section('title', 'Services')

@push('styles')
    <style>
        body{
            background-image: none;
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
            'title' => 'From Request to Refresh — We Make It Shine',
            'subtitle' => 'What We Offer · Our Services',
            'description' => 'Discover our range of professional cleaning services designed to make your home or business shine',
            'buttonText' => 'Explore Services',
            'buttonUrl' => route('services'),
            'backgroundImage' => asset('images/backgrounds/services_carousel/cover-page.svg'),
            'textColor' => 'text-white',
            'darkTextColor' => 'text-white',
        ],
        [
            'title' => 'Deep Cleaning',
            'price' => '€ 120 - € 200',
            'description' => 'A thorough, top-to-bottom cleaning that tackles dirt and grime in hard-to-reach places...',
            'badges' => ['Linen Laundry', 'Toilet Cleaning', 'Window Cleaning', 'Interior Arrangement'],
            'buttonText' => 'Book A Service',
            'buttonUrl' => route('quotation'),
            'backgroundImage' => asset('images/backgrounds/services_carousel/deep-cleaning.svg'),
            'textColor' => 'text-gray-900',
            'darkTextColor' => 'text-white',
        ],
        [
            'title' => 'Full Daily Cleaning',
            'price' => '€ 80 - € 150',
            'description' => 'Complete room refresh tailored for guest accommodations...',
            'badges' => ['Light Cleaning', 'Emptying Trash Bins', 'Sweeping & Dusting', 'Replacing Amenities', 'Restocking Amenities'],
            'buttonText' => 'Book A Service',
            'buttonUrl' => route('quotation'),
            'backgroundImage' => asset('images/backgrounds/services_carousel/full-daily-cleaning.svg'),
            'textColor' => 'text-gray-900',
            'darkTextColor' => 'text-white',
        ],
        [
            'title' => 'Snow Out Cleaning',
            'price' => '€ 150 - € 250',
            'description' => 'Comprehensive cleaning service for vacation rentals between guest stays...',
            'badges' => ['Deep Cleaning', 'Full Sanitization', 'Linen Replacement', 'Complete Reset'],
            'buttonText' => 'Book A Service',
            'buttonUrl' => route('quotation'),
            'backgroundImage' => asset('images/backgrounds/services_carousel/snow-out-cleaning.svg'),
            'textColor' => 'text-gray-900',
            'darkTextColor' => 'text-white',
        ],
        [
            'title' => 'Full Daily Cleaning',
            'price' => '€ 200 - € 300',
            'description' => 'Comprehensive daily cleaning service covering all areas...',
            'badges' => ['Complete Service', 'All Rooms', 'Full Maintenance', 'Premium Care'],
            'buttonText' => 'Book A Service',
            'buttonUrl' => route('quotation'),
            'backgroundImage' => asset('images/backgrounds/services_carousel/full-daily-cleaning.svg'),
            'textColor' => 'text-gray-900',
            'darkTextColor' => 'text-white',
        ],
    ];
        // Expanding Cards Data
    $expandingCards = [
            [
            'title' => 'Serving Homes & Businesses Across These Areas',
            'price' => '',
            'description' => 'Comprehensive cleaning service for vacation rentals between guest stays.',
            'badges' => ['Inari', 'Lapland', 'Saariselkä'],
            'buttonText' => '',
            'buttonUrl' => route('quotation'),
            'bgImage' => asset('images/backgrounds/areas-serve-bg.svg'),
            'gradient' => '',
            'darkGradient' => 'from-blue-600 via-blue-700 to-blue-800',
            'overlayOpacity' => '60',
        ],
        [
            'title' => 'Municipality of Inari',
            'price' => '',
            'description' => 'This is one of the municipalities inside Lapland, on the northernmost region, taking up roughly the entire top third of the country, above the Arctic Circle.',
            'badges' => ['Saariselkä', 'Nellim', 'Lemmenjoki','Ivalo'],
            'buttonText' => '',
            'buttonUrl' => route('quotation'),
            
            'bgImage' => asset('images/backgrounds/inari-bg.svg'),            
            // Optional: Keep gradient overlay for better text readability
            'gradient' => '',
            'darkGradient' => 'from-blue-600 via-blue-700 to-blue-800',
            'overlayOpacity' => '40', // Lower opacity to show more of the image
        ],
        [
            'title' => 'Saariselkä',
            'price' => '',
            'description' => 'In Lapland, with the shimmering northern lights dance above snow-covered fells, reindeer roam freely, and adventure awaits year-round through skiing, hiking, and the serene beauty of the Arctic wilderness.',
            'badges' => ['Utsjoki', 'Inari','Saariselkä','Kaamanen'],
            'buttonText' => '',
            'buttonUrl' => route('quotation'),
            'bgImage' => asset('images/backgrounds/sar-bg.svg'),
            'gradient' => '',
            'darkGradient' => 'from-blue-600 via-blue-700 to-blue-800',
            'overlayOpacity' => '30',
        ],
        [
            'title' => 'Lapland Region',
            'price' => '',
            'description' => 'The capital of Lapland and the official hometown of Santa Claus, known for its vibrant urban life and Northern Lights views.',
            'badges' => ['Utsjoki', 'Inari','Saariselkä','Kaamanen'],
            'buttonText' => '',
            'buttonUrl' => route('quotation'),
            'bgImage' => asset('images/backgrounds/lapland-region-bg.svg'),
            'gradient' => '',
            'darkGradient' => 'from-blue-600 via-blue-700 to-blue-800',
            'overlayOpacity' => '35',
        ]
    ];
    @endphp

    <!-- SECTION 1: SLIDE CAROUSEL -->
    <section class="w-full py-6 bg-white dark:bg-gray-900 transition-colors duration-300 mb-8">
        <div class="container mx-auto max-wl-8xl px-4 sm:px-6 lg:px-8">
            <div id="services-carousel" class="relative">
                <!-- Carousel wrapper with rounded corners -->
                <div class="relative h-[500px] sm:h-[600px] md:h-[700px] overflow-hidden rounded-2xl sm:rounded-3xl shadow-2xl dark:shadow-gray-900/50">

                    @foreach($slides as $index => $slide)
                        <x-landing-page-components.carousel-slide
                            :title="$slide['title']"
                            :subtitle="$slide['subtitle'] ?? null"
                            :description="$slide['description']"
                            :buttonText="$slide['buttonText']"
                            :buttonUrl="$slide['buttonUrl']"
                            :badges="$slide['badges'] ?? []"
                            :price="$slide['price'] ?? null"
                            :backgroundImage="$slide['backgroundImage']"
                            :textColor="$slide['textColor']"
                            :darkTextColor="$slide['darkTextColor']"
                            :isActive="$index === 0"
                            :index="$index"
                        />
                    @endforeach

                </div>

                <!-- Navigation Arrows -->
                <button type="button" id="carousel-prev"
                    class="absolute top-1/2 left-2 sm:left-4 -translate-y-1/2 z-30 flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/30 dark:bg-gray-800/50 backdrop-blur-sm hover:bg-white/50 dark:hover:bg-gray-700/70 transition-all duration-300 shadow-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button type="button" id="carousel-next"
                    class="absolute top-1/2 right-2 sm:right-4 -translate-y-1/2 z-30 flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/30 dark:bg-gray-800/50 backdrop-blur-sm hover:bg-white/50 dark:hover:bg-gray-700/70 transition-all duration-300 shadow-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Indicators/Dots -->
                <div class="absolute bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 z-30 flex gap-2">
                    @foreach($slides as $index => $slide)
                        <button type="button" 
                            class="carousel-indicator w-2 h-2 rounded-full bg-white/60 dark:bg-gray-400/60 hover:bg-white dark:hover:bg-gray-200 transition-all duration-300" 
                            data-slide="{{ $index }}">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: EXPANDING CARDS CAROUSEL -->
    <section class="w-full py-8 sm:py-12 lg:py-16 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-8 sm:mb-12">
                <h2 class="text-xs sm:text-sm md:text-base text-blue-600 dark:text-blue-400 font-semibold mb-4 sm:mb-6 lg:mb-8">Explore Our Services</h2>
                <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-2 sm:mb-4">
                    Just a <span class="text-blue-600">click away</span>
                </h2>
            </div>

            <!-- Expanding Cards -->
            <div id="expanding-carousel" class="flex gap-2 sm:gap-3 lg:gap-4 justify-center items-stretch h-auto lg:h-[600px]">
                @foreach($expandingCards as $index => $card)
                    <x-landing-page-components.expand-card
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
                    />
                @endforeach
            </div>

            <div class="mt-8 sm:mt-12 w-full justify-center align-items-center">
                <p class="text-center text-sm sm:text-base lg:text-lg text-gray-600 dark:text-gray-400 mb-6 sm:mb-8 px-4">
                    Proudly serving homes and businesses across Finland, we bring professional, reliable cleaning right to your doorstep
                </p>
            </div>
        </div>
    </section>

    <!-- SECTION 3: PRICING -->
    <section class="w-full py-8 sm:py-12 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
        <div class="mx-auto max-w-4xl text-center px-4">
            <h2 class="text-xs sm:text-sm md:text-base text-blue-600 dark:text-blue-400 font-semibold">Pricing</h2>
            <p class="p-6 sm:p-8 lg:p-12 pb-4 sm:pb-6 pt-4 sm:pt-6 justify-center mt-2 text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight text-gray-900 dark:text-white">
                Find the <span class="text-blue-600 dark:text-blue-400 font-bold">best plan</span> for your cleaning needs.
            </p>
        </div>
        <p class="mx-auto mt-4 sm:mt-6 max-w-3xl text-center text-sm sm:text-base lg:text-lg xl:text-xl text-gray-600 dark:text-gray-300 px-4">
            Flexible cleaning plans designed to keep your space spotless — on your schedule, your way.
        </p>

        <!-- Pricing Cards -->
        <div class="mx-auto mt-8 sm:mt-12 lg:mt-16 p-4 sm:p-6 grid max-w-6xl grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            
            <!-- CARD 1: Contractual Daily Cleaning -->
            <div class="relative bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm h-auto sm:h-[28rem] lg:h-[30rem] rounded-2xl sm:rounded-3xl p-6 sm:p-8 ring-1 ring-gray-900/10 dark:ring-gray-100/10 transition-all duration-300 hover:bg-gray-900 dark:hover:bg-blue-600 hover:text-white hover:scale-105 hover:z-[5] group shadow-sm hover:shadow-2xl dark:shadow-gray-900/50">
                <h3 class="text-sm sm:text-base font-bold text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200">
                    Contractual Daily Cleaning
                </h3>
                <p class="mt-3 sm:mt-4 flex items-baseline gap-x-2">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-white">
                        $29
                    </span>
                    <span class="text-sm sm:text-base text-gray-500 dark:text-gray-400 group-hover:text-gray-300">
                        /year
                    </span>
                </p>
                <p class="mt-4 sm:mt-6 text-sm sm:text-base text-gray-600 dark:text-gray-300 group-hover:text-gray-300">
                    Ideal for occasional home cleaning and requests.
                </p>
                <ul role="list" class="mt-6 sm:mt-8 space-y-2 sm:space-y-3 text-xs sm:text-sm text-gray-600 dark:text-gray-300 group-hover:text-gray-300">
                    <li class="flex gap-x-2 sm:gap-x-3">
                        <svg class="h-5 w-4 sm:h-6 sm:w-5 flex-none text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                        </svg>
                        Daily room cleaning
                    </li>
                    <li class="flex gap-x-2 sm:gap-x-3">
                        <svg class="h-5 w-4 sm:h-6 sm:w-5 flex-none text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                        </svg>
                        Basic cleaning supplies
                    </li>
                    <li class="flex gap-x-2 sm:gap-x-3">
                        <svg class="h-5 w-4 sm:h-6 sm:w-5 flex-none text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                        </svg>
                        Contract-based pricing
                    </li>
                </ul>
                <a href="#"
                    class="mt-6 sm:mt-8 block rounded-full bg-blue-600 dark:bg-blue-500 text-center text-xs sm:text-sm text-white px-4 py-3 sm:py-3.5 hover:bg-blue-500 dark:hover:bg-blue-400 transition-colors shadow-md">
                    Purchase
                </a>
            </div>

            <!-- CARD 2: Interval Cleaning -->
            <div class="relative bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm h-auto sm:h-[28rem] lg:h-[30rem] rounded-2xl sm:rounded-3xl p-6 sm:p-8 ring-1 ring-gray-900/10 dark:ring-gray-100/10 transition-all duration-300 hover:bg-gray-900 dark:hover:bg-blue-600 hover:text-white hover:scale-105 hover:z-[5] group shadow-sm hover:shadow-2xl dark:shadow-gray-900/50">
                <h3 class="text-sm sm:text-base font-bold text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200">
                    Interval Cleaning
                </h3>
                <p class="mt-3 sm:mt-4 flex items-baseline gap-x-2">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-white">
                        $59
                    </span>
                    <span class="text-sm sm:text-base text-gray-500 dark:text-gray-400 group-hover:text-gray-300">
                        /month
                    </span>
                </p>
                <p class="mt-4 sm:mt-6 text-sm sm:text-base text-gray-600 dark:text-gray-300 group-hover:text-gray-300">
                    Great for regular home or office cleaning services.
                </p>
                <ul role="list" class="mt-6 sm:mt-8 space-y-2 sm:space-y-3 text-xs sm:text-sm text-gray-600 dark:text-gray-300 group-hover:text-gray-300">
                    <li class="flex gap-x-2 sm:gap-x-3">
                        <svg class="h-5 w-4 sm:h-6 sm:w-5 flex-none text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                        </svg>
                        Weekly/bi-weekly service
                    </li>
                    <li class="flex gap-x-2 sm:gap-x-3">
                        <svg class="h-5 w-4 sm:h-6 sm:w-5 flex-none text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                        </svg>
                        Premium cleaning supplies
                    </li>
                    <li class="flex gap-x-2 sm:gap-x-3">
                        <svg class="h-5 w-4 sm:h-6 sm:w-5 flex-none text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                        </svg>
                        Flexible scheduling
                    </li>
                </ul>
                <a href="#"
                    class="mt-6 sm:mt-8 block rounded-full bg-blue-600 dark:bg-blue-500 text-center text-xs sm:text-sm text-white px-4 py-3 sm:py-3.5 hover:bg-blue-500 dark:hover:bg-blue-400 transition-colors shadow-md">
                    Purchase
                </a>
            </div>

            <!-- CARD 3: On-Call Cleaning -->
            <div class="relative bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm h-auto sm:h-[28rem] lg:h-[30rem] rounded-2xl sm:rounded-3xl p-6 sm:p-8 ring-1 ring-gray-900/10 dark:ring-gray-100/10 transition-all duration-300 hover:bg-gray-900 dark:hover:bg-blue-600 hover:text-white hover:scale-105 hover:z-[5] group shadow-sm hover:shadow-2xl dark:shadow-gray-900/50">
                <h3 class="text-sm sm:text-base font-bold text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200">
                    On-Call Cleaning
                </h3>
                <p class="mt-3 sm:mt-4 flex items-baseline gap-x-2">
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-white">
                        $99
                    </span>
                    <span class="text-sm sm:text-base text-gray-500 dark:text-gray-400 group-hover:text-gray-300">
                        /week
                    </span>
                </p>
                <p class="mt-4 sm:mt-6 text-sm sm:text-base text-gray-600 dark:text-gray-300 group-hover:text-gray-300">
                    Complete cleaning service with 24/7 support and customization.
                </p>
                <ul role="list" class="mt-6 sm:mt-8 space-y-2 sm:space-y-3 text-xs sm:text-sm text-gray-600 dark:text-gray-300 group-hover:text-gray-300">
                    <li class="flex gap-x-2 sm:gap-x-3">
                        <svg class="h-5 w-4 sm:h-6 sm:w-5 flex-none text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                        </svg>
                        24/7 on-demand service
                    </li>
                    <li class="flex gap-x-2 sm:gap-x-3">
                        <svg class="h-5 w-4 sm:h-6 sm:w-5 flex-none text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                        </svg>
                        Priority support
                    </li>
                    <li class="flex gap-x-2 sm:gap-x-3">
                        <svg class="h-5 w-4 sm:h-6 sm:w-5 flex-none text-blue-600 dark:text-blue-400 group-hover:text-blue-400 dark:group-hover:text-blue-200"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                        </svg>
                        Custom cleaning plans
                    </li>
                </ul>
                <a href="#"
                    class="mt-6 sm:mt-8 block rounded-full bg-blue-600 dark:bg-blue-500 text-center text-xs sm:text-sm text-white px-4 py-3 sm:py-3.5 hover:bg-blue-500 dark:hover:bg-blue-400 transition-colors shadow-md">
                    Purchase
                </a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ===== SLIDE CAROUSEL FUNCTIONALITY =====
            const carousel = document.getElementById('services-carousel');
            const items = carousel.querySelectorAll('.carousel-item');
            const indicators = carousel.querySelectorAll('.carousel-indicator');
            const prevBtn = document.getElementById('carousel-prev');
            const nextBtn = document.getElementById('carousel-next');

            let currentSlide = 0;
            const totalSlides = items.length;
            let autoplayInterval;

            function showSlide(index) {
                items.forEach((item, i) => {
                    if (i === index) {
                        item.classList.remove('opacity-0', 'z-0');
                        item.classList.add('opacity-100', 'z-10');
                    } else {
                        item.classList.remove('opacity-100', 'z-10');
                        item.classList.add('opacity-0', 'z-0');
                    }
                });

                indicators.forEach((indicator, i) => {
                    if (i === index) {
                        indicator.classList.add('bg-white', 'dark:bg-gray-200', 'w-8');
                        indicator.classList.remove('bg-white/60', 'dark:bg-gray-400/60', 'w-2');
                    } else {
                        indicator.classList.remove('bg-white', 'dark:bg-gray-200', 'w-8');
                        indicator.classList.add('bg-white/60', 'dark:bg-gray-400/60', 'w-2');
                    }
                });

                currentSlide = index;
            }

            function nextSlide() {
                const next = (currentSlide + 1) % totalSlides;
                showSlide(next);
            }

            function prevSlide() {
                const prev = (currentSlide - 1 + totalSlides) % totalSlides;
                showSlide(prev);
            }

            prevBtn.addEventListener('click', () => {
                prevSlide();
                resetAutoplay();
            });

            nextBtn.addEventListener('click', () => {
                nextSlide();
                resetAutoplay();
            });

            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    showSlide(index);
                    resetAutoplay();
                });
            });

            function startAutoplay() {
                autoplayInterval = setInterval(nextSlide, 5000);
            }

            function stopAutoplay() {
                clearInterval(autoplayInterval);
            }

            function resetAutoplay() {
                stopAutoplay();
                startAutoplay();
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    prevSlide();
                    resetAutoplay();
                } else if (e.key === 'ArrowRight') {
                    nextSlide();
                    resetAutoplay();
                }
            });

            carousel.addEventListener('mouseenter', stopAutoplay);
            carousel.addEventListener('mouseleave', startAutoplay);

            let touchStartX = 0;
            let touchEndX = 0;

            carousel.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            });

            carousel.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            });

            function handleSwipe() {
                if (touchEndX < touchStartX - 50) {
                    nextSlide();
                    resetAutoplay();
                }
                if (touchEndX > touchStartX + 50) {
                    prevSlide();
                    resetAutoplay();
                }
            }

            showSlide(0);
            startAutoplay();

            // ===== EXPANDING CARDS FUNCTIONALITY =====
            const cards = document.querySelectorAll('.carousel-card');
            const expandingCarouselContainer = document.getElementById('expanding-carousel');
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

                // Only add hover behavior on non-mobile (sm screens and up)
                if (!isMobile) {
                    card.addEventListener('mouseenter', function () {
                        if (!activeCard) {
                            cards.forEach(c => c.classList.remove('expanded'));
                            card.classList.add('expanded');
                        }
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