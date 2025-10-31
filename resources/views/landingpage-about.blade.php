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
        
        .scroll-zoom-child:nth-child(1) { transition-delay: 0.1s; }
        .scroll-zoom-child:nth-child(2) { transition-delay: 0.2s; }
        .scroll-zoom-child:nth-child(3) { transition-delay: 0.3s; }
        .scroll-zoom-child:nth-child(4) { transition-delay: 0.4s; }
        .scroll-zoom-child:nth-child(5) { transition-delay: 0.5s; }

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
        class="scroll-zoom relative flex flex-col items-center justify-center text-center w-full mx-auto px-6 sm:px-8 md:px-12 lg:px-16 py-12 sm:py-24 min-h-[80vh]">
        
        <!-- Main Content - Centered Container -->
        <div class="z-10 w-full max-w-4xl mx-auto">
            <!-- Hello there text -->
            <p class="scroll-zoom-child mb-4 text-sm sm:text-base text-blue-500 dark:text-blue-400 font-semibold">
                Hello there,
            </p>

            <!-- Main Heading -->
            <h1 class="scroll-zoom-child text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold text-gray-900 dark:text-white leading-tight">
                We are 
                <span class="text-blue-500 dark:text-blue-400 inline-flex items-center gap-2">
                    <img src="{{ asset('images/icons/single-sparkle.svg') }}" alt="Sparkle" class="h-6 sm:h-8 md:h-10 lg:h-12 w-auto inline-block">
                    Fin-noys
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="scroll-zoom-child mt-6 text-sm sm:text-base md:text-lg text-gray-700 dark:text-gray-300 font-medium">
                Your hassle-free buddy for a sparkling clean space.
            </p>

            <!-- Description -->
            <p class="scroll-zoom-child mt-6 sm:mt-8 text-xs sm:text-sm md:text-base text-gray-700 dark:text-gray-300 leading-relaxed max-w-3xl mx-auto text-center sm:text-justify px-0 sm:px-4">
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
    <section id="container-2" class="scroll-zoom relative w-full bg-gradient-to-b from-transparent via-white/60 to-white/80
               dark:via-slate-900/60 dark:to-slate-900/80
               backdrop-blur-sm py-8 sm:py-16 lg:py-20 xl:py-24 mt-10 sm:mt-20 lg:mt-32 min-h-screen transition-all duration-700 flex items-center">

        <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-white/70 dark:from-slate-900/70 to-transparent pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 lg:px-12 xl:px-16 w-full">
            <div class="flex flex-col lg:flex-row items-center justify-center gap-6 sm:gap-8 lg:gap-12 xl:gap-16">
                <!-- Text Content - Larger Width -->
                <div class="w-full lg:w-3/5 text-center lg:text-left">
                    <div class="space-y-3 sm:space-y-4 lg:space-y-6">
                        <h2 class="scroll-zoom-child text-blue-500 dark:text-blue-400 font-[fam-bold] text-[10px] sm:text-xs md:text-sm lg:text-base">
                            Introducing the Development Team: Ela-vate
                        </h2>
                        
                        <h1 class="scroll-zoom-child text-3xl sm:text-4xl md:text-4xl lg:text-5xl xl:text-5xl font-[fam-bold] leading-tight text-gray-900 dark:text-white">
                            Cleaning is more than a task;
                            <span class="text-blue-500 dark:text-blue-400 font-[fam-bold]">
                                it's a promise of comfort and care
                            </span>
                            <img src="{{ asset('images/icons/single-sparkle.svg') }}" alt="sparkle"
                                class="inline-block h-4 sm:h-5 md:h-6 lg:h-7 xl:h-9 align-middle ml-1 sm:ml-2">
                        </h1>
                        
                        <p class="scroll-zoom-child font-[fam-bold] text-[10px] sm:text-xs md:text-sm lg:text-base text-blue-500 dark:text-blue-400 italic">
                            Behind Fin-noys: <span class="not-italic">Making Clean Spaces Possible</span>
                        </p>
                        
                        <div class="scroll-zoom-child flex flex-col sm:flex-row flex-wrap gap-4 sm:gap-6 lg:gap-8 xl:gap-10 justify-center lg:justify-start pt-2 sm:pt-4">
                            <div class="flex flex-col items-center lg:items-start min-w-[100px] sm:min-w-[120px] lg:min-w-[140px]">
                                <p class="font-[fam-bold] text-[#071957] dark:text-white text-xs sm:text-sm lg:text-base mb-0.5 sm:mb-1">
                                    Merlyn Guzman
                                </p>
                                <p class="font-[fam-regular] text-[10px] sm:text-xs lg:text-sm text-gray-500 dark:text-gray-400 mb-0.5">
                                    CEO
                                </p>
                                <p class="font-[fam-regular] text-[9px] sm:text-xs text-gray-400 dark:text-gray-500">
                                    Founder of Fin-noys
                                </p>
                            </div>
                            
                            <div class="flex flex-col items-center lg:items-start min-w-[100px] sm:min-w-[120px] lg:min-w-[140px]">
                                <p class="font-[fam-bold] text-[#071957] dark:text-white text-xs sm:text-sm lg:text-base mb-0.5 sm:mb-1">
                                    Earl Leonardo
                                </p>
                                <p class="font-[fam-regular] text-[10px] sm:text-xs lg:text-sm text-gray-500 dark:text-gray-400 mb-0.5">
                                    CFO
                                </p>
                                <p class="font-[fam-regular] text-[9px] sm:text-xs text-gray-400 dark:text-gray-500">
                                    Co-Founder of Finnoys
                                </p>
                            </div>
                            
                            <div class="flex flex-col items-center lg:items-start min-w-[100px] sm:min-w-[120px] lg:min-w-[140px]">
                                <p class="font-[fam-bold] text-[#071957] dark:text-white text-xs sm:text-sm lg:text-base mb-0.5 sm:mb-1">
                                    Fin-noys Employees
                                </p>
                                <p class="font-[fam-regular] text-[10px] sm:text-xs lg:text-sm text-gray-500 dark:text-gray-400 mb-0.5">
                                    Employees
                                </p>
                                <p class="font-[fam-regular] text-[9px] sm:text-xs text-gray-400 dark:text-gray-500">
                                    Enablers of Finnoys
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
                                        dark:bg-[url('/images/people/team-member-bg-dark.svg')]
                                        bg-center bg-[length:155%] bg-no-repeat [backface-visibility:hidden]">
                                <img src="{{ asset('images/people/cleaner-avatar.svg') }}" alt="Team Member"
                                    class="absolute inset-0 w-full h-full object-cover rounded-full">
                            </div>

                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-4 sm:p-6 md:p-8 rounded-full 
                                        backdrop-blur-md bg-white/90 dark:bg-slate-800/90 
                                        rotate-y-180 [backface-visibility:hidden]
                                        border-2 border-blue-500/20 dark:border-blue-400/20">
                                <h3 class="text-sm sm:text-base md:text-lg lg:text-xl font-[fam-bold] text-[#071957] dark:text-white mb-1.5 sm:mb-2 drop-shadow-md">
                                    Merlyn Guzman
                                </h3>
                                <p class="text-[10px] sm:text-xs md:text-sm lg:text-base text-[#071957] dark:text-gray-300 leading-relaxed px-2 sm:px-3 md:px-4">
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
        class="scroll-zoom relative flex flex-col lg:flex-row items-stretch
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
                        class="w-full h-full border-0 dark:brightness-90" 
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
            <div class="max-w-2xl mx-auto">
                <div class="space-y-8 sm:px-6">
                    
                    <!-- Header -->
                    <div class="scroll-zoom-child mb-6 text-center lg:text-left">
                        <h3 class="text-3xl sm:text-4xl md:text-5xl font-[fam-bold] text-[#071957] dark:text-white mb-3 sm:mb-4">
                            Get In Touch
                        </h3>
                        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                            We're here to help and answer any questions you might have
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
                                    Head Office
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
                                    Email us at
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
                                    Contact us at
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
                                    Follow us on
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
@endpush