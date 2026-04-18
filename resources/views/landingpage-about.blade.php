@extends('components.layouts.general-landing')

@section('title', 'About')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    /* Aurora text effect */
    @keyframes auroraShift {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .aurora-text {
        background: linear-gradient(135deg, #60a5fa, #3b82f6, #818cf8, #6366f1, #3b82f6, #60a5fa);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: auroraShift 6s ease-in-out infinite;
    }

    /* Leaflet custom marker styles */
    .leaflet-marker-custom {
        width: 16px !important;
        height: 16px !important;
        border-radius: 50%;
        background: #3b82f6;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        margin-left: -8px !important;
        margin-top: -8px !important;
    }

    .leaflet-marker-custom.marker-primary {
        width: 20px !important;
        height: 20px !important;
        background: #2563eb;
        border: 3px solid #fff;
        box-shadow: 0 2px 12px rgba(37, 99, 235, 0.5);
        margin-left: -10px !important;
        margin-top: -10px !important;
    }

    .leaflet-popup-content-wrapper {
        border-radius: 12px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        padding: 0 !important;
    }

    .leaflet-popup-content {
        margin: 12px 14px !important;
        font-family: inherit !important;
    }

    .leaflet-popup-tip {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .dark .leaflet-popup-content-wrapper {
        background: #1e293b !important;
        color: #e2e8f0 !important;
    }

    .dark .leaflet-popup-tip {
        background: #1e293b !important;
    }

    .dark .leaflet-tile {
        filter: brightness(0.7) contrast(1.1) saturate(0.8);
    }

    .leaflet-tooltip {
        border-radius: 8px !important;
        padding: 6px 10px !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
    }

    .dark .leaflet-tooltip {
        background: #1e293b !important;
        color: #e2e8f0 !important;
        border-color: #334155 !important;
    }

    .dark .leaflet-tooltip::before {
        border-right-color: #1e293b !important;
    }

    body {
        background-image: url("{{ asset('images/backgrounds/aboutpage-bg.svg') }}");
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }

    /* Smooth scroll zoom animation */
    .scroll-zoom {
        opacity: 0;
        transform: scale(0.85);
        transition: opacity 1s ease-out, transform 1s ease-out;
    }

    .scroll-zoom.visible {
        opacity: 1;
        transform: scale(1);
    }

    /* Stagger animation delays for child elements */
    .scroll-zoom-child {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }

    .scroll-zoom-child.visible {
        opacity: 1;
        transform: scale(1) translateY(0);
    }

    .scroll-zoom-child:nth-child(1) {
        transition-delay: 0.1s;
    }

    .scroll-zoom-child:nth-child(2) {
        transition-delay: 0.2s;
    }

    .scroll-zoom-child:nth-child(3) {
        transition-delay: 0.3s;
    }

    .scroll-zoom-child:nth-child(4) {
        transition-delay: 0.4s;
    }

    .scroll-zoom-child:nth-child(5) {
        transition-delay: 0.5s;
    }

    /* Frosted glass effect */
    .frosted-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .dark .frosted-card {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Animation for cards */
    .card-float {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    /* Flip card styling */
    .flip-card {
        perspective: 1000px;
    }

    .flip-card:hover .flip-inner {
        transform: rotateY(180deg);
    }

    .flip-inner {
        transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .rotate-y-180 {
        transform: rotateY(180deg);
    }

    /* Add glow effect on hover */
    .flip-card:hover {
        filter: drop-shadow(0 0 20px rgba(59, 130, 246, 0.3));
    }

    .dark .flip-card:hover {
        filter: drop-shadow(0 0 20px rgba(96, 165, 250, 0.4));
    }

    /* Full height map styling */
    #container-3 .map-full-height {
        height: 100vh;
    }

    @media (max-width: 1023px) {
        #container-3 .map-full-height {
            height: 60vh;
            min-height: 400px;
        }
    }

    /* Enhanced map edge glow effects */
    #container-3 .map-edge-glow {
        box-shadow:
            inset -60px 0 80px -20px rgba(255, 255, 255, 0.8),
            inset -40px 0 60px -10px rgba(255, 255, 255, 0.6);
    }

    .dark #container-3 .map-edge-glow {
        box-shadow:
            inset -60px 0 80px -20px rgba(15, 23, 42, 0.9),
            inset -40px 0 60px -10px rgba(15, 23, 42, 0.7);
    }

    /* Smooth color transition animation */
    #container-3 .map-full-height * {
        transition: opacity 0.5s ease, filter 0.5s ease;
    }
</style>
@endpush

@section('content')
<div class="overflow-x-hidden">
    <!-- Section 1: Original About Section -->
    <section id="container-1"
        class="scroll-zoom relative flex flex-col items-center justify-center text-center w-full mx-auto px-6 sm:px-8 md:px-12 lg:px-16 py-12 sm:py-24 min-h-[80vh] overflow-hidden">

        <!-- Main Content - Centered Container -->
        <div class="z-10 w-full max-w-4xl mx-auto">
            <!-- Hello there text -->
            <p class="scroll-zoom-child mb-4 text-sm sm:text-base text-blue-500 dark:text-blue-400 font-semibold">
                {{ __('about.section1.greeting') }}
            </p>

            <!-- Main Heading -->
            <h1 data-typing data-typing-duration="1.8" class="scroll-zoom-child text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold text-gray-900 dark:text-white leading-tight lg:whitespace-nowrap">
                {{ __('about.section1.we_are') }}
                <span class="aurora-text inline-flex items-baseline gap-2"><img src="{{ asset('images/icons/single-sparkle.svg') }}" alt="Sparkle" class="h-6 sm:h-8 md:h-10 lg:h-12 w-auto inline-block align-middle">{{ __('about.section1.company_name') }}</span>
            </h1>

            <!-- Subtitle -->
            <p class="scroll-zoom-child mt-6 text-sm sm:text-base md:text-lg text-gray-700 dark:text-gray-300 font-medium">
                {{ __('about.section1.subtitle') }}
            </p>

            <!-- Description -->
            <p class="scroll-zoom-child mt-6 sm:mt-8 text-xs sm:text-sm md:text-base text-gray-700 dark:text-gray-300 leading-relaxed max-w-3xl mx-auto text-center sm:text-justify px-0 sm:px-4">
                {{ __('about.section1.description') }}
            </p>
        </div>

        <!-- Floating Cards -->
        <div class="absolute hidden xl:block w-64 lg:w-72 p-4 lg:p-5 rounded-2xl shadow-xl frosted-card card-float"
            style="top: 10%; left: 15%; animation-delay: 0s;">
            <div class="absolute -top-3 left-8 h-6 w-6 rounded-full">
                <img src="{{ asset('images/backgrounds/gradient-circle.svg') }}" alt="Decorator">
            </div>
            <p class="text-xs lg:text-sm text-gray-800 dark:text-gray-200 font-medium italic">
                {{ __('about.floating_cards.card1') }}
            </p>
        </div>

        <div class="absolute hidden xl:block w-60 lg:w-64 p-4 lg:p-5 rounded-2xl shadow-xl frosted-card card-float"
            style="top: 30%; right: 3%; animation-delay: 1s;">
            <div class="absolute -top-3 left-8 h-6 w-6 rounded-full">
                <img src="{{ asset('images/backgrounds/gradient-circle.svg') }}" alt="Decorator">
            </div>
            <p class="text-xs lg:text-sm text-gray-800 dark:text-gray-200 font-medium italic">
                {{ __('about.floating_cards.card2') }}
            </p>
        </div>

        <div class="absolute hidden xl:block w-60 lg:w-64 p-4 lg:p-5 rounded-2xl shadow-xl frosted-card card-float"
            style="bottom: 10%; left: 5%; animation-delay: 2s;">
            <div class="absolute -top-3 left-8 h-6 w-6 rounded-full">
                <img src="{{ asset('images/backgrounds/gradient-circle.svg') }}" alt="Decorator">
            </div>
            <p class="text-xs lg:text-sm text-gray-800 dark:text-gray-200 font-medium italic">
                {{ __('about.floating_cards.card3') }}
            </p>
        </div>

        <div class="absolute hidden xl:block w-56 lg:w-60 p-4 lg:p-5 rounded-2xl shadow-xl frosted-card card-float"
            style="bottom: 10%; right: 8%; animation-delay: 1.5s;">
            <div class="absolute -top-6 left-8 h-6 w-6 rounded-full">
                <img src="{{ asset('images/backgrounds/gradient-circle.svg') }}" alt="Decorator">
            </div>
            <p class="text-xs lg:text-sm text-gray-800 dark:text-gray-200 font-medium italic">
                {{ __('about.floating_cards.card4') }}
            </p>
        </div>
    </section>

    <!-- Section 2: Development Team with Flip Card -->
    <section id="container-2" class="scroll-zoom relative w-full bg-gradient-to-b from-transparent via-white/60 to-white/80
               dark:via-slate-900/60 dark:to-slate-900/80
               backdrop-blur-sm py-8 mt-10 sm:mt-20 lg:mt-32 min-h-screen transition-all duration-700 flex items-center">

        <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-white/70 dark:from-slate-900/70 to-transparent pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 lg:px-12 xl:px-16 w-full">
            <div class="flex flex-col lg:flex-row items-center justify-center gap-6 sm:gap-8 lg:gap-12 xl:gap-16">
                <!-- Text Content - Larger Width -->
                <div class="w-full lg:w-3/5 text-center lg:text-left">
                    <div class="space-y-3 sm:space-y-4 lg:space-y-6">
                        <h2 class="scroll-zoom-child text-blue-500 dark:text-blue-400 font-[fam-bold] text-[10px] sm:text-xs md:text-sm lg:text-base">
                            {{ __('about.section2.team_intro') }}
                        </h2>

                        <h1 data-typing data-typing-duration="1.5" class="scroll-zoom-child text-3xl sm:text-4xl md:text-4xl lg:text-5xl xl:text-5xl font-extrabold leading-tight text-gray-900 dark:text-white">
                            {{ __('about.section2.heading_before') }}
                            <span class="text-blue-500 dark:text-blue-400 font-[fam-bold]">
                                {{ __('about.section2.heading_highlight') }}
                            </span>
                            <img src="{{ asset('images/icons/single-sparkle.svg') }}" alt="sparkle"
                                class="inline-block h-4 sm:h-5 md:h-6 lg:h-7 xl:h-9 align-middle ml-1 sm:ml-2">
                        </h1>

                        <p class="scroll-zoom-child font-[fam-bold] text-[10px] sm:text-xs md:text-sm lg:text-base text-blue-500 dark:text-blue-400 italic">
                            {{ __('about.section2.tagline') }} <span class="not-italic">{{ __('about.section2.tagline_sub') }}</span>
                        </p>

                        <div class="scroll-zoom-child flex flex-col sm:flex-row flex-wrap gap-4 sm:gap-6 lg:gap-8 xl:gap-10 justify-center lg:justify-start pt-2 sm:pt-4">
                            <div class="flex flex-col items-center lg:items-start min-w-[100px] sm:min-w-[120px] lg:min-w-[140px]">
                                <p class="font-[fam-bold] text-[#071957] dark:text-white text-xs sm:text-sm lg:text-base mb-0.5 sm:mb-1">
                                    {{ __('about.section2.person1_name') }}
                                </p>
                                <p class="font-[fam-regular] text-[10px] sm:text-xs lg:text-sm text-gray-500 dark:text-gray-400 mb-0.5">
                                    {{ __('about.section2.person1_role') }}
                                </p>
                                <p class="font-[fam-regular] text-[9px] sm:text-xs text-gray-400 dark:text-gray-500">
                                    {{ __('about.section2.person1_desc') }}
                                </p>
                            </div>

                            <div class="flex flex-col items-center lg:items-start min-w-[100px] sm:min-w-[120px] lg:min-w-[140px]">
                                <p class="font-[fam-bold] text-[#071957] dark:text-white text-xs sm:text-sm lg:text-base mb-0.5 sm:mb-1">
                                    {{ __('about.section2.person2_name') }}
                                </p>
                                <p class="font-[fam-regular] text-[10px] sm:text-xs lg:text-sm text-gray-500 dark:text-gray-400 mb-0.5">
                                    {{ __('about.section2.person2_role') }}
                                </p>
                                <p class="font-[fam-regular] text-[9px] sm:text-xs text-gray-400 dark:text-gray-500">
                                    {{ __('about.section2.person2_desc') }}
                                </p>
                            </div>

                            <div class="flex flex-col items-center lg:items-start min-w-[100px] sm:min-w-[120px] lg:min-w-[140px]">
                                <p class="font-[fam-bold] text-[#071957] dark:text-white text-xs sm:text-sm lg:text-base mb-0.5 sm:mb-1">
                                    {{ __('about.section2.person3_name') }}
                                </p>
                                <p class="font-[fam-regular] text-[10px] sm:text-xs lg:text-sm text-gray-500 dark:text-gray-400 mb-0.5">
                                    {{ __('about.section2.person3_role') }}
                                </p>
                                <p class="font-[fam-regular] text-[9px] sm:text-xs text-gray-400 dark:text-gray-500">
                                    {{ __('about.section2.person3_desc') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flip Card - Smaller Width -->
                <div class="scroll-zoom-child w-full lg:w-2/5 flex justify-center items-center mt-6 sm:mt-8 lg:mt-0">
                    <div class="flip-card w-[200px] h-[200px] sm:w-[260px] sm:h-[260px] md:w-[300px] md:h-[300px] lg:w-[340px] lg:h-[340px] xl:w-[380px] xl:h-[380px] cursor-pointer transition-all duration-700">
                        <div class="flip-inner relative w-full h-full transition-transform duration-700 [transform-style:preserve-3d]">
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center rounded-full overflow-hidden
                                        bg-[url('/images/people/team-member-bg.svg')]
                                        dark:bg-[url('/images/people/team-member-bg.svg')]
                                        bg-center bg-[length:155%] bg-no-repeat [backface-visibility:hidden]">
                                <img src="{{ asset('images/people/cleaner-avatar.svg') }}" alt="Team Member"
                                    class="absolute inset-0 w-full h-full object-cover rounded-full">
                            </div>

                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-4 sm:p-6 md:p-8 rounded-full 
                                        backdrop-blur-md bg-white/90 dark:bg-slate-800/90 
                                        rotate-y-180 [backface-visibility:hidden]
                                        border-2 border-blue-500/20 dark:border-blue-400/20">
                                <h3 class="text-sm sm:text-base md:text-lg lg:text-xl font-[fam-bold] text-[#071957] dark:text-white mb-1.5 sm:mb-2 drop-shadow-md">
                                    {{ __('about.section2.flip_name') }}
                                </h3>
                                <p class="text-[10px] sm:text-xs md:text-sm lg:text-base text-[#071957] dark:text-gray-300 leading-relaxed px-2 sm:px-3 md:px-4">
                                    {{ __('about.section2.flip_desc') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Section 3: Contact Section with Full Height Map -->
    <section id="container-3"
        class="scroll-zoom relative flex flex-col lg:flex-row items-stretch
               w-full 
               bg-white dark:bg-slate-900 
               transition-all duration-700">

        <!-- Map Section - Full Height Left Side -->
        <div class="w-full lg:w-1/2 relative">
            <div class="map-full-height map-edge-glow lg:sticky lg:top-0 relative overflow-hidden">
                <!-- Leaflet Map Container -->
                <div id="leaflet-map" class="w-full h-full relative z-10"></div>

                <!-- Dark Mode Overlay -->
                <div class="absolute inset-0 bg-blue-900/10 dark:bg-blue-900/30 pointer-events-none z-20"></div>

                <!-- Right Edge Gradient Glow - Blends with background -->
                <div class="absolute top-0 right-0 w-32 sm:w-40 lg:w-48 xl:w-64 h-full pointer-events-none z-30
                            bg-gradient-to-l from-white via-white/80 to-transparent
                            dark:from-slate-900 dark:via-slate-900/80 dark:to-transparent
                            opacity-90 dark:opacity-95">
                </div>

                <!-- Enhanced Glow Effect Layer -->
                <div class="absolute top-0 right-0 w-24 sm:w-32 lg:w-40 h-full pointer-events-none z-40
                            bg-gradient-to-l from-white/95 via-white/50 to-transparent
                            dark:from-slate-900/95 dark:via-slate-900/50 dark:to-transparent
                            blur-sm">
                </div>
            </div>
        </div>

        <!-- Contact Info Section - Scrollable Right Side -->
        <div class="w-full lg:w-1/2 py-16 sm:py-20 lg:py-24 px-6 sm:px-8 md:px-12 lg:px-16">
            <div class="max-w-2xl mx-auto">
                <div class="space-y-8 sm:px-6">

                    <!-- Header -->
                    <div class="scroll-zoom-child mb-6 text-center lg:text-left">
                        <h3 data-typing data-typing-duration="1.2" class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-[#071957] dark:text-white mb-3 sm:mb-4">
                            {{ __('about.contact.title') }}
                        </h3>
                        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                            {{ __('about.contact.subtitle') }}
                        </p>
                    </div>

                    <!-- Contact List -->
                    <ul class="space-y-6 sm:space-y-8 flex flex-col items-center lg:items-start">

                        <!-- Head Office -->
                        <li class="scroll-zoom-child flex flex-row items-start gap-3 sm:gap-4 group w-full max-w-md lg:max-w-none">
                            <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-location-dot text-blue-500 dark:text-blue-400 text-xl sm:text-2xl"></i>
                            </div>
                            <div class="text-left flex-1">
                                <span class="block font-[fam-bold] text-base sm:text-lg text-gray-900 dark:text-white mb-1">
                                    {{ __('about.contact.head_office') }}
                                </span>
                                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                                    Saariselantie 6 C10, Saariselka 99830 Finland
                                </p>
                            </div>
                        </li>

                        <!-- Email -->
                        <li class="scroll-zoom-child flex flex-row items-start gap-3 sm:gap-4 group w-full max-w-md lg:max-w-none">
                            <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-envelope text-blue-500 dark:text-blue-400 text-xl sm:text-2xl"></i>
                            </div>
                            <div class="text-left flex-1">
                                <span class="block font-[fam-bold] text-base sm:text-lg text-gray-900 dark:text-white mb-1">
                                    {{ __('about.contact.email_us') }}
                                </span>
                                <a href="mailto:finnoys0823@gmail.com"
                                    class="text-sm sm:text-base text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                                    finnoys0823@gmail.com
                                </a>
                            </div>
                        </li>

                        <!-- Phone -->
                        <li class="scroll-zoom-child flex flex-row items-start gap-3 sm:gap-4 group w-full max-w-md lg:max-w-none">
                            <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-phone text-blue-500 dark:text-blue-400 text-xl sm:text-2xl"></i>
                            </div>
                            <div class="text-left flex-1">
                                <span class="block font-[fam-bold] text-base sm:text-lg text-gray-900 dark:text-white mb-1">
                                    {{ __('about.contact.contact_us') }}
                                </span>
                                <a href="tel:09288515619"
                                    class="text-sm sm:text-base text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                                    Service Center: 09288515619
                                </a>
                            </div>
                        </li>

                        <!-- Social Media -->
                        <li class="scroll-zoom-child pt-4 sm:pt-6 w-full max-w-md lg:max-w-none">
                            <div class="flex flex-col items-start gap-4">
                                <span class="font-[fam-bold] text-base sm:text-lg text-gray-900 dark:text-white">
                                    {{ __('about.contact.follow_us') }}
                                </span>
                                <div class="flex flex-row gap-4 sm:gap-6">
                                    <!-- Facebook -->
                                    <a href="#"
                                        class="w-10 h-10 sm:w-14 sm:h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-500 dark:text-blue-400 hover:bg-blue-500 dark:hover:bg-blue-500 hover:text-white dark:hover:text-white transform hover:scale-110 transition-all duration-300 shadow-md hover:shadow-xl"
                                        aria-label="Follow us on Facebook">
                                        <i class="fa-brands fa-facebook text-2xl sm:text-3xl"></i>
                                    </a>

                                    <!-- WhatsApp -->
                                    <a href="#"
                                        class="w-10 h-10 sm:w-14 sm:h-14 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-500 dark:text-green-400 hover:bg-green-500 dark:hover:bg-green-500 hover:text-white dark:hover:text-white transform hover:scale-110 transition-all duration-300 shadow-md hover:shadow-xl"
                                        aria-label="Contact us on WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-2xl sm:text-3xl"></i>
                                    </a>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Force scroll to top on page load/refresh
    if (history.scrollRestoration) {
        history.scrollRestoration = 'manual';
    }

    window.addEventListener('beforeunload', function() {
        window.scrollTo(0, 0);
    });

    window.addEventListener('load', function() {
        setTimeout(function() {
            window.scrollTo(0, 0);
        }, 0);
    });

    // Additional fallback
    document.addEventListener('DOMContentLoaded', function() {
        window.scrollTo(0, 0);
    });

    // Smooth scroll zoom-in animation
    const zoomObserverOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -100px 0px'
    };

    const zoomObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, zoomObserverOptions);

    // Observe all scroll-zoom elements
    document.querySelectorAll('.scroll-zoom, .scroll-zoom-child').forEach(element => {
        zoomObserver.observe(element);
    });

    // Smooth scroll animations for frosted cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe frosted cards
    document.querySelectorAll('.frosted-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
</script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const locations = [{
                id: 1,
                name: 'Fin-noys Head Office',
                description: 'Saariselantie 6 C10, Saariselka 99830 Finland',
                lat: 68.4101,
                lng: 27.4132,
                primary: true
            },
            {
                id: 2,
                name: 'Saariselka Service Area',
                description: 'Saariselka resort area coverage',
                lat: 68.4185,
                lng: 27.4310,
                primary: false
            },
            {
                id: 3,
                name: 'Ivalo Service Area',
                description: 'Ivalo district coverage',
                lat: 68.6573,
                lng: 27.5890,
                primary: false
            },
        ];

        const isDark = document.documentElement.classList.contains('dark');

        // Tile layers
        const lightTile = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
        const darkTile = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';

        const map = L.map('leaflet-map', {
            center: [68.42, 27.43],
            zoom: 10,
            zoomControl: false,
            attributionControl: false
        });

        // Add zoom control to bottom-left
        L.control.zoom({
            position: 'bottomleft'
        }).addTo(map);

        // Add attribution (small)
        L.control.attribution({
                position: 'bottomright',
                prefix: false
            })
            .addAttribution('&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OSM</a> &copy; <a href="https://carto.com/" target="_blank">CARTO</a>')
            .addTo(map);

        L.tileLayer(isDark ? darkTile : lightTile, {
            maxZoom: 19,
            subdomains: 'abcd'
        }).addTo(map);

        // Add markers
        locations.forEach(loc => {
            const markerIcon = L.divIcon({
                className: 'leaflet-marker-custom' + (loc.primary ? ' marker-primary' : ''),
                iconSize: loc.primary ? [20, 20] : [16, 16]
            });

            const marker = L.marker([loc.lat, loc.lng], {
                icon: markerIcon
            }).addTo(map);

            // Tooltip (hover)
            marker.bindTooltip(loc.name, {
                direction: 'top',
                offset: [0, -12],
                opacity: 0.95
            });

            // Popup (click)
            marker.bindPopup(`
                    <div style="min-width: 160px;">
                        <p style="font-weight: 600; font-size: 13px; margin: 0 0 4px 0;">${loc.name}</p>
                        <p style="font-size: 11px; color: #64748b; margin: 0;">${loc.description}</p>
                        <p style="font-size: 10px; color: #94a3b8; margin: 4px 0 0 0;">
                            ${loc.lat.toFixed(4)}, ${loc.lng.toFixed(4)}
                        </p>
                    </div>
                `, {
                closeButton: false,
                offset: [0, -8]
            });
        });

        // Listen for dark mode changes
        const darkObserver = new MutationObserver(() => {
            const nowDark = document.documentElement.classList.contains('dark');
            map.eachLayer(layer => {
                if (layer instanceof L.TileLayer) map.removeLayer(layer);
            });
            L.tileLayer(nowDark ? darkTile : lightTile, {
                maxZoom: 19,
                subdomains: 'abcd'
            }).addTo(map);
        });
        darkObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });

        // Fix map sizing on scroll into view
        const mapSection = document.getElementById('container-3');
        if (mapSection) {
            const resizeObserver = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) map.invalidateSize();
                });
            });
            resizeObserver.observe(mapSection);
        }
    });
</script>
@endpush