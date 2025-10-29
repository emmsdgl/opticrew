@extends('components.layouts.general-landing')

@section('title', 'About')

@push('styles')
    <style>
        body {
            background-image: url('{{ asset('images/backgrounds/aboutpage-bg.svg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Dark mode background override */
        body.dark {
            background-image: url('{{ asset('images/backgrounds/aboutpage-bg-dark.svg') }}');
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
            0%, 100% {
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

        /* Ensure proper centering for sections */
        #container-1, #container-2 {
            margin-left: auto;
            margin-right: auto;
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
    <!-- Section 1: Original About Section -->
    <section id="container-1"
        class="relative flex flex-col items-center justify-center text-center w-full max-w-7xl mx-auto px-6 sm:px-8 md:px-12 lg:px-16 py-12 sm:py-24 min-h-[80vh]">
        
        <!-- Decorative Images -->
        <div class="absolute hidden xl:block" style="top: 22%; left: -20%;">
            <img src="{{ asset('images/icons/cleaning_bucket.svg') }}" class="w-48 h-48 opacity-40 dark:opacity-30" alt="Cleaning Bucket">
        </div>
        <div class="absolute hidden xl:block" style="top: -10%; right: -20%;">
            <img src="{{ asset('images/icons/cleaning_spray.svg') }}" class="w-48 h-48 opacity-40 dark:opacity-30" alt="Cleaning Spray">
        </div>

        <!-- Main Content - Centered Container -->
        <div class="z-10 w-full max-w-4xl mx-auto">
            <!-- Hello there text -->
            <p class="mb-4 text-sm sm:text-base text-blue-500 dark:text-blue-400 font-semibold">
                Hello there,
            </p>

            <!-- Main Heading -->
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold text-gray-900 dark:text-white leading-tight">
                We are 
                <span class="text-blue-500 dark:text-blue-400 inline-flex items-center gap-2">
                    <img src="{{ asset('images/icons/single-sparkle.svg') }}" alt="Sparkle" class="h-6 sm:h-8 md:h-10 lg:h-12 w-auto inline-block">
                    Fin-noys
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="mt-6 text-sm sm:text-base md:text-lg text-gray-700 dark:text-gray-300 font-medium">
                Your hassle-free buddy for a sparkling clean space.
            </p>

            <!-- Description -->
            <p class="mt-6 sm:mt-8 text-xs sm:text-sm md:text-base text-gray-700 dark:text-gray-300 leading-relaxed max-w-3xl mx-auto text-center sm:text-justify px-0 sm:px-4">
                Fin-noys is a professional cleaning services provider with extensive experience in the hospitality industry 
                and are dedicated to maintaining a clean, healthy environment and ensuring a secure efficient cleaning process.
            </p>
        </div>

        <!-- Floating Cards -->
        <div class="absolute hidden xl:block w-64 lg:w-72 p-4 lg:p-5 rounded-2xl shadow-xl frosted-card card-float"
            style="top: 10%; left: 15%; animation-delay: 0s;">
            <div class="absolute -top-3 left-8 h-6 w-6 rounded-full">
                <img src="{{ asset('images/backgrounds/gradient-circle.svg') }}" alt="Decorator">
            </div>
            <p class="text-xs lg:text-sm text-gray-800 dark:text-gray-200 font-medium italic">
                " Proven track record in hotel and holiday cottages cleaning services. "
            </p>
        </div>

        <div class="absolute hidden xl:block w-60 lg:w-64 p-4 lg:p-5 rounded-2xl shadow-xl frosted-card card-float"
            style="top: 30%; right: -5%; animation-delay: 1s;">
            <div class="absolute -top-3 left-8 h-6 w-6 rounded-full">
                <img src="{{ asset('images/backgrounds/gradient-circle.svg') }}" alt="Decorator">
            </div>
            <p class="text-xs lg:text-sm text-gray-800 dark:text-gray-200 font-medium italic">
                " Commitment to <span class="font-bold">quality cleaning services</span>. "
            </p>
        </div>

        <div class="absolute hidden xl:block w-60 lg:w-64 p-4 lg:p-5 rounded-2xl shadow-xl frosted-card card-float"
            style="bottom: 10%; left: 5%; animation-delay: 2s;">
            <div class="absolute -top-3 left-8 h-6 w-6 rounded-full">
                <img src="{{ asset('images/backgrounds/gradient-circle.svg') }}" alt="Decorator">
            </div>
            <p class="text-xs lg:text-sm text-gray-800 dark:text-gray-200 font-medium italic">
                " Flexible and responsive to your needs. "
            </p>
        </div>

        <div class="absolute hidden xl:block w-56 lg:w-60 p-4 lg:p-5 rounded-2xl shadow-xl frosted-card card-float"
            style="bottom: 10%; right: 8%; animation-delay: 1.5s;">
            <div class="absolute -top-6 left-8 h-6 w-6 rounded-full">
                <img src="{{ asset('images/backgrounds/gradient-circle.svg') }}" alt="Decorator">
            </div>
            <p class="text-xs lg:text-sm text-gray-800 dark:text-gray-200 font-medium italic">
                " Wide range of cleaning services. "
            </p>
        </div>
    </section>

    <!-- Section 2: Development Team with Flip Card -->
    <section id="container-2" class="relative w-full bg-gradient-to-b from-transparent via-white/60 to-white/80
               dark:via-slate-900/60 dark:to-slate-900/80
               backdrop-blur-sm py-16 sm:py-20 lg:py-24 mt-20 sm:mt-32 min-h-[80vh] transition-all duration-700">

        <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-white/70 dark:from-slate-900/70 to-transparent pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto px-6 sm:px-8 md:px-12 lg:px-16">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8 lg:gap-12 xl:gap-16">
                <div class="w-full lg:w-full text-center lg:text-left">
                    <div class="space-y-6">
                        <h2 class="text-blue-500 dark:text-blue-400 font-[fam-bold] text-xs sm:text-sm md:text-base">
                            Introducing the Development Team: Ela-vate
                        </h2>
                        
                        <h1 class="text-3xl sm:text-3xl md:text-4xl lg:text-5xl font-[fam-bold] leading-tight text-gray-900 dark:text-white">
                            Cleaning is more than a task;
                            <span class="text-blue-500 dark:text-blue-400 font-[fam-bold]">
                                it's a promise of comfort and care
                            </span>
                            <img src="{{ asset('images/icons/single-sparkle.svg') }}" alt="sparkle"
                                class="inline-block h-6 sm:h-7 md:h-8 lg:h-9 align-middle ml-2">
                        </h1>
                        
                        <p class="font-[fam-bold] text-xs sm:text-sm md:text-base text-blue-500 dark:text-blue-400 italic">
                            Behind Fin-noys: <span class="not-italic">Making Clean Spaces Possible</span>
                        </p>
                        
                        <div class="flex flex-col sm:flex-row flex-wrap gap-6 sm:gap-8 lg:gap-10 justify-center lg:justify-start pt-4">
                            <div class="flex flex-col items-center lg:items-start min-w-[140px]">
                                <p class="font-[fam-bold] text-[#071957] dark:text-white text-sm sm:text-base mb-1">
                                    Merlyn Guzman
                                </p>
                                <p class="font-[fam-regular] text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-0.5">
                                    CEO
                                </p>
                                <p class="font-[fam-regular] text-xs text-gray-400 dark:text-gray-500">
                                    Founder of Fin-noys
                                </p>
                            </div>
                            
                            <div class="flex flex-col items-center lg:items-start min-w-[140px]">
                                <p class="font-[fam-bold] text-[#071957] dark:text-white text-sm sm:text-base mb-1">
                                    Earl Leonardo
                                </p>
                                <p class="font-[fam-regular] text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-0.5">
                                    CFO
                                </p>
                                <p class="font-[fam-regular] text-xs text-gray-400 dark:text-gray-500">
                                    Co-Founder of Finnoys
                                </p>
                            </div>
                            
                            <div class="flex flex-col items-center lg:items-start min-w-[140px]">
                                <p class="font-[fam-bold] text-[#071957] dark:text-white text-sm sm:text-base mb-1">
                                    Fin-noys Employees
                                </p>
                                <p class="font-[fam-regular] text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-0.5">
                                    Employees
                                </p>
                                <p class="font-[fam-regular] text-xs text-gray-400 dark:text-gray-500">
                                    Enablers of Finnoys
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-1/3 flex justify-center lg:justify-end items-center mt-8 lg:mt-0">
                    <div class="flip-card w-[260px] h-[260px] sm:w-[300px] sm:h-[300px] md:w-[340px] md:h-[340px] lg:w-[380px] lg:h-[380px] cursor-pointer transition-all duration-700">
                        <div class="flip-inner relative w-full h-full transition-transform duration-700 [transform-style:preserve-3d]">
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center rounded-full overflow-hidden
                                        bg-[url('/images/people/team-member-bg.svg')]
                                        dark:bg-[url('/images/people/team-member-bg-dark.svg')]
                                        bg-center bg-[length:155%] bg-no-repeat [backface-visibility:hidden]">
                                <img src="{{ asset('images/people/cleaner-avatar.svg') }}" alt="Team Member"
                                    class="absolute inset-0 w-full h-full object-cover rounded-full">
                            </div>

                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 sm:p-8 rounded-full 
                                        backdrop-blur-md bg-white/90 dark:bg-slate-800/90 
                                        rotate-y-180 [backface-visibility:hidden]
                                        border-2 border-blue-500/20 dark:border-blue-400/20">
                                <h3 class="text-base sm:text-lg md:text-xl font-[fam-bold] text-[#071957] dark:text-white mb-2 drop-shadow-md">
                                    Merlyn Guzman
                                </h3>
                                <p class="text-xs sm:text-sm md:text-base text-[#071957] dark:text-gray-300 leading-relaxed px-3 sm:px-4">
                                    Founder of Fin-noys. Dedicated to maintaining comfort, care, and excellence in every clean space.
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
        class="relative flex flex-col lg:flex-row items-stretch
               w-full 
               bg-white dark:bg-slate-900 
               transition-all duration-700">

        <!-- Map Section - Full Height Left Side -->
        <div class="w-full lg:w-1/2 relative">
            <div class="map-full-height map-edge-glow lg:sticky lg:top-0 relative overflow-hidden">
                <!-- Map Container -->
                <div id="hs-grayscale-leaflet" class="w-full h-full relative z-10">
                    <iframe
                        src="https://www.openstreetmap.org/export/embed.html?bbox=120.976%2C14.599%2C120.986%2C14.605&amp;layer=mapnik&amp;marker=14.6020%2C120.9810"
                        class="w-full h-full border-0 grayscale dark:brightness-75 dark:contrast-125" 
                        loading="lazy"
                        title="Fin-noys Location Map"
                        allowfullscreen>
                    </iframe>
                </div>
                
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
            <div class="max-w-2xl mx-auto lg:mx-0">
                <div class="text-center lg:text-left space-y-8">
                    
                    <!-- Header -->
                    <div class="mb-6">
                        <h3 class="text-3xl sm:text-4xl md:text-5xl font-[fam-bold] text-[#071957] dark:text-white mb-3 sm:mb-4">
                            Get In Touch
                        </h3>
                        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                            We're here to help and answer any questions you might have
                        </p>
                    </div>

                    <!-- Contact List -->
                    <ul class="space-y-6 sm:space-y-8">
                        
                        <!-- Head Office -->
                        <li class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 group">
                            <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-location-dot text-blue-500 dark:text-blue-400 text-xl sm:text-2xl"></i>
                            </div>
                            <div class="text-center sm:text-left flex-1">
                                <span class="block font-[fam-bold] text-base sm:text-lg text-gray-900 dark:text-white mb-1">
                                    Head Office
                                </span>
                                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                                    Saariselantie 6 C10, Saariselka 99830 Finland
                                </p>
                            </div>
                        </li>

                        <!-- Email -->
                        <li class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 group">
                            <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-envelope text-purple-500 dark:text-purple-400 text-xl sm:text-2xl"></i>
                            </div>
                            <div class="text-center sm:text-left flex-1">
                                <span class="block font-[fam-bold] text-base sm:text-lg text-gray-900 dark:text-white mb-1">
                                    Email us at
                                </span>
                                <a href="mailto:finnoys0823@gmail.com" 
                                   class="text-sm sm:text-base text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                                    finnoys0823@gmail.com
                                </a>
                            </div>
                        </li>

                        <!-- Phone -->
                        <li class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 group">
                            <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-phone text-green-500 dark:text-green-400 text-xl sm:text-2xl"></i>
                            </div>
                            <div class="text-center sm:text-left flex-1">
                                <span class="block font-[fam-bold] text-base sm:text-lg text-gray-900 dark:text-white mb-1">
                                    Contact us at
                                </span>
                                <a href="tel:09288515619" 
                                   class="text-sm sm:text-base text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                                    Service Center: 09288515619
                                </a>
                            </div>
                        </li>

                        <!-- Social Media -->
                        <li class="pt-4 sm:pt-6">
                            <div class="flex flex-col items-center sm:items-start gap-4">
                                <span class="font-[fam-bold] text-base sm:text-lg text-gray-900 dark:text-white">
                                    Follow us on
                                </span>
                                <div class="flex flex-row gap-4 sm:gap-6">
                                    <!-- Facebook -->
                                    <a href="#" 
                                       class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-500 dark:text-blue-400 hover:bg-blue-500 dark:hover:bg-blue-500 hover:text-white dark:hover:text-white transform hover:scale-110 transition-all duration-300 shadow-md hover:shadow-xl"
                                       aria-label="Follow us on Facebook">
                                        <i class="fa-brands fa-facebook text-2xl sm:text-3xl"></i>
                                    </a>
                                    
                                    <!-- WhatsApp -->
                                    <a href="#" 
                                       class="w-12 h-12 sm:w-14 sm:h-14 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-500 dark:text-green-400 hover:bg-green-500 dark:hover:bg-green-500 hover:text-white dark:hover:text-white transform hover:scale-110 transition-all duration-300 shadow-md hover:shadow-xl"
                                       aria-label="Contact us on WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-2xl sm:text-3xl"></i>
                                    </a>
                                </div>
                            </div>
                        </li>

                    </ul>

                    <!-- Call to Action Button -->
                    <div class="pt-6 sm:pt-8">
                        <a href="#contact-form" 
                           class="inline-flex items-center justify-center gap-3 px-6 sm:px-8 py-3 sm:py-4 
                                  bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 
                                  text-white font-semibold text-sm sm:text-base 
                                  rounded-xl shadow-lg hover:shadow-xl 
                                  transform hover:scale-105 transition-all duration-300
                                  group">
                            <span>Send us a message</span>
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>

        // Smooth scroll animations for cards
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
@endpush