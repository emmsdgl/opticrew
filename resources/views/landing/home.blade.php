<!DOCTYPE html>
<html lang="en" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Home</title>
    <style>[x-cloak] { display: none !important; }</style>
    <link rel="icon" href="{{ asset('images/icons/castcrew/castcrew-pic-logo.svg') }}" type="image/svg+xml">
</head>
<style>
    @font-face {
        font-family: 'fam-regular';
        src: url('/fonts/FamiljenGrotesk-Regular.otf') format('opentype');
        font-weight: normal;
        font-style: normal;
    }

    @font-face {
        font-family: 'fam-bold';
        src: url('/fonts/FamiljenGrotesk-Bold.otf') format('opentype');
        font-weight: normal;
        font-style: normal;
    }

    @font-face {
        font-family: 'fam-bold-italic';
        src: url('/fonts/FamiljenGrotesk-BoldItalic.otf') format('opentype');
        font-weight: normal;
        font-style: normal;
    }

    * {
        color: #071957;
        font-family: 'fam-regular';
    }

    body {
        background-image: url(/images/backgrounds/landing-page-2.svg);
        background-size: cover;
        background-repeat: no-repeat;
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

    /* === ADDED FOR BLUR EFFECT ON OTHER CARDS === */
    .feature-card.blurred {
        filter: blur(3px);
        /* Adjust blur strength as desired */
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

    /* === CHATBOT STYLES === */
    .hc-fab { width:56px; height:56px; background:#2563eb; box-shadow:0 4px 14px rgba(37,99,235,0.4); transition:all 0.3s ease; border:none; cursor:pointer; }
    .hc-fab:hover { transform:scale(1.1); box-shadow:0 6px 20px rgba(37,99,235,0.5); background:#1d4ed8; }
    .hc-panel { position:absolute; bottom:0; right:0; width:340px; height:460px; min-height:460px; max-height:calc(100vh - 100px); overflow:hidden; box-shadow:0 5px 40px rgba(0,0,0,0.12),0 2px 10px rgba(0,0,0,0.08); display:flex; flex-direction:column; z-index:100; }
    @media (min-width:1024px) { .hc-panel { width:380px; height:500px; min-height:500px; max-height:calc(100vh - 100px); } }
    @media (max-width:640px) { .hc-panel { position:fixed; top:0; left:0; right:0; bottom:0; width:100%!important; height:100%!important; max-height:none; border-radius:0!important; } }
    @keyframes hcSlideIn { from{opacity:0;transform:translateX(12px) scale(0.96)} to{opacity:1;transform:translateX(0) scale(1)} }
    @keyframes hcMsg { from{opacity:0;transform:translateY(8px) scale(0.97)} to{opacity:1;transform:translateY(0) scale(1)} }
    @keyframes hcMsgSent { 0%{transform:scale(0.95);opacity:0.5} 50%{transform:scale(1.02)} 100%{transform:scale(1);opacity:1} }
    @keyframes hcFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-5px)} }
    @keyframes hcDots { 0%,100%{opacity:0.5} 50%{opacity:0.2} }
    .hc-panel-anim { animation:hcSlideIn 0.25s ease-out; }
    .hc-msg { animation:hcMsg 0.35s cubic-bezier(0.16,1,0.3,1); }
    .hc-msg-user { animation:hcMsgSent 0.4s cubic-bezier(0.16,1,0.3,1); }
    .hc-float { animation:hcFloat 3s ease-in-out infinite; }
    .hc-typing { animation:hcDots 1.2s ease-in-out infinite; }
    .hc-scroll::-webkit-scrollbar { width:3px; }
    .hc-scroll::-webkit-scrollbar-track { background:transparent; }
    .hc-scroll::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:3px; }
</style>

<body class="h-full overflow-x-hidden">
    <div id="main-container">
        <header class="inset-x-0 top-0 z-50">
            <nav aria-label="Global" class="flex items-center justify-between p-6 lg:px-8">
                <div class="flex lg:flex-1">
                    <a href="#" class="-m-1.5 p-1.5">
                        <span class="sr-only"></span>
                        <img src="/images/finnoys-text-logo.svg" alt="" class="h-20 w-auto">
                    </a>
                </div>
                <div class="flex lg:hidden">
                    <button type="button" command="show-modal" commandfor="mobile-menu"
                        class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-200">
                        <span class="sr-only">Open main menu</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon"
                            aria-hidden="true" class="size-6">
                            <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <div class="hidden lg:flex lg:gap-x-12">
                    <a href="/" class="text-sm/6 text-blue-950 hover:text-blue-600 hover:font-bold">{{ __('common.nav.home') }}</a>
                    <a href="/about" class="text-sm/6 text-blue-950 hover:text-blue-600 hover:font-bold">{{ __('common.nav.about') }}</a>
                    <a href="/services" class="text-sm/6 text-blue-950 hover:text-blue-600 hover:font-bold">{{ __('common.nav.services') }}</a>
                    <a href="/pricing" class="text-sm/6 text-blue-950 hover:text-blue-600 hover:font-bold">{{ __('common.nav.pricing') }}</a>
                </div>
                <div class="hidden lg:flex lg:flex-1 lg:justify-end lg:items-center lg:gap-x-4">
                    <!-- Language Switcher -->
                    <div class="relative">
                        <button id="language-toggle" class="flex items-center gap-2 text-sm text-blue-950 hover:text-blue-600">
                            @if(app()->getLocale() == 'fi')
                                <span class="text-lg">🇫🇮</span>
                                <span>Suomi</span>
                            @else
                                <span class="text-lg">🇬🇧</span>
                                <span>English</span>
                            @endif
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div id="language-dropdown" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 flex items-center gap-2">
                                <span class="text-lg">🇬🇧</span> English
                            </a>
                            <a href="{{ route('language.switch', 'fi') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 flex items-center gap-2">
                                <span class="text-lg">🇫🇮</span> Suomi
                            </a>
                        </div>
                    </div>
                    <a href="/login" class="text-sm/6 font-semibold text-blue-950 hover:text-blue-600">{{ __('common.nav.login') }} <span aria-hidden="true">&rarr;</span></a>
                </div>
            </nav>
            <el-dialog>
                <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
                    <div tabindex="0" class="fixed inset-0 focus:outline-none">
                        <el-dialog-panel
                            class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-blue-100 bg-blend-color-multiply  p-6 sm:max-w-sm sm:ring-1 sm:ring-gray-100/10">
                            <div class="flex items-center justify-between">
                                <a href="#" class="-m-1.5 p-1.5">
                                    <span class="sr-only">Fin-noys</span>
                                    <img src="/images/finnoys-text-logo.svg" alt="" class="h-20 w-auto">
                                </a>
                                <button type="button" command="close" commandfor="mobile-menu"
                                    class="-m-2.5 rounded-md p-2.5 text-gray-200">
                                    <span class="sr-only">Close menu</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        data-slot="icon" aria-hidden="true" class="size-6">
                                        <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-6 flow-root">
                                <div class="-my-6 divide-y divide-white/10">
                                    <div class="space-y-2 py-6">
                                        <a href="/"
                                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 hover:bg-blue-600/10">{{ __('common.nav.home') }}</a>
                                        <a href="/about"
                                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 hover:bg-blue-600/10">{{ __('common.nav.about') }}</a>
                                        <a href="/services"
                                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 hover:bg-blue-600/10">{{ __('common.nav.services') }}</a>
                                        <a href="/pricing"
                                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 hover:bg-blue-600/10">{{ __('common.nav.pricing') }}</a>
                                    </div>
                                    <div class="py-6 space-y-2">
                                        <!-- Language Switcher Mobile -->
                                        <div class="flex gap-2 px-3">
                                            <a href="{{ route('language.switch', 'en') }}"
                                               class="flex-1 flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm text-blue-950 hover:bg-blue-600/10 {{ app()->getLocale() == 'en' ? 'bg-blue-600/20 font-bold' : '' }}">
                                                <span class="text-lg">🇬🇧</span> English
                                            </a>
                                            <a href="{{ route('language.switch', 'fi') }}"
                                               class="flex-1 flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm text-blue-950 hover:bg-blue-600/10 {{ app()->getLocale() == 'fi' ? 'bg-blue-600/20 font-bold' : '' }}">
                                                <span class="text-lg">🇫🇮</span> Suomi
                                            </a>
                                        </div>
                                        <a href="/login"
                                            class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 text-blue-950 hover:bg-blue-600/10">{{ __('common.nav.login') }}</a>
                                    </div>
                                </div>
                            </div>
                        </el-dialog-panel>
                    </div>
                </dialog>
            </el-dialog>
        </header>
        <div id="container-1" class="relative isolate text-center w-full sm:w-[85%] lg:w-[60%] mx-auto pt-11 pb-24 px-4 sm:px-0 overflow-hidden">
            <div class="hidden sm:mb-8 sm:flex sm:justify-center">
                <div
                    class="relative rounded-full px-3 py-1 text-sm/6 text-gray-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20">
                    {{ __('home.hero.tagline') }}
                </div>
            </div>
            <h1 id="header-1" class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl tracking-normal text-blue-950 p-4 sm:p-10">
                {{ __('home.hero.title_1') }}
                <span id="cleanliness" class="text-blue-500 inline-flex items-center">
                    <span id="spark" class="mr-2">
                        <img src="/images/icons/sparkle.svg" alt="" class="h-12 w-auto">
                    </span>
                    {{ __('home.hero.title_cleanliness') }}
                </span>
                <br>
                {{ __('home.hero.title_2') }}
            </h1>
            <p class="mt-8 text-[12px] text-pretty sm:text-base w-[75%] mx-auto text-justify">
                {{ __('home.hero.description') }}
            </p>
            <div class="mt-10 flex items-center justify-center gap-x-5">
                <a href="/signup"
                    class="flex items-center justify-center rounded-full bg-blue-600 px-3 py-2.5 text-sm text-white shadow-xs hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    <span aria-hidden="true"
                        class="ml-2 bg-amber-50 rounded-full mr-1 px-3 py-2 text-sm text-[#000c1b]"><i
                            class="fa-solid fa-arrow-right fa-xs"></i></span>{{ __('common.buttons.get_started') }} <span aria-hidden="true"
                        class="ml-2"></span>
                </a>
                <a href="/pricing"
                    class="rounded-full px-3.5 py-2.5 text-sm/6 text-gray-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20">
                    {{ __('common.buttons.get_quote') }}
                </a>
            </div>

            <img id="web-screenshot" width="2432" height="1442" src="/images/backgrounds/large_screenshot.svg"
                alt="Booking Page Screenshot"
                class="mt-16 w-[90%] md:w-[70%] lg:w-[60%] xl:w-[100%] max-w-7xl rounded-xl soft-glow mx-auto" />
            <div class="absolute hidden lg:block w-72 p-6 rounded-2xl shadow-xl frosted-card text-center"
                style="top: 45%; left: -15%;">
                <div class="absolute -left-1 bottom-1/3 -translate-x-1/2 h-6 w-6 rounded-full bg-blue-600"></div>
                    <h3 class="text-base mb-2 font-bold" id="comment-title">Trusted Professionals</h3>
                    <p class="text-sm">
                        Our well-trained and professional cleaning staff are dedicated to maintaining a clean, healthy
                        environment.
                    </p>
            </div>

            <div class="absolute hidden lg:block w-72 p-6 rounded-2xl shadow-xl frosted-card text-center"
                style="top: 63%; right: -15%;">
                <div class="absolute -top-3 left-1/4 -translate-x-1/2 h-6 w-6 rounded-full bg-blue-600"></div>
                <h3 class="text-base mb-2 font-bold">Extensive Cleaning Experience</h3>
                <p class="text-sm">
                    professional cleaning services provider with extensive experience in the hospitality industry
                </p>
            </div>

            <div class="absolute hidden lg:block w-72 p-6 rounded-2xl shadow-xl frosted-card text-center"
                style="top: 80%; left: 3%; transform: translateX(-50%);">
                <div class="absolute -top-3 left-1/4 -translate-x-1/2 h-6 w-6 rounded-full bg-blue-600"></div>
                <h3 class="text-base mb-2 font-bold">Hassle-Free Online Booking</h3>
                <p class="text-sm">
                    secure efficient cleaning process in a click.
                </p>
            </div>
        </div>
        <div class="py-16 sm:py-24 justify-center">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <dl id="stats-container" class="grid grid-cols-1 gap-x-8 gap-y-16 text-center lg:grid-cols-3">
                    <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                        <dt class="text-base/7 text-gray-600">Transactions every 24 hours</dt>
                        <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl counter-up"
                            data-target="44" data-suffix=" million">0</dd>
                    </div>
                    <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                        <dt class="text-base/7 text-gray-600">Returning Clients</dt>
                        <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl counter-up"
                            data-target="119" data-prefix="$" data-suffix=" trillion">0</dd>
                    </div>
                    <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                        <dt class="text-base/7 text-gray-600">New Users</dt>
                        <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl counter-up"
                            data-target="46000" data-separator=",">0</dd>
                    </div>
                </dl>
            </div>
        </div>
        <div id="container-3" class=" py-8 sm:py-16">
            <div class="relative isolate text-center w-full sm:w-[85%] lg:w-[60%] mx-auto pt-3 pb-16 px-4 sm:px-0">
                <div id="badge-container">
                    <span class="bg-blue-100 text-blue-500 text-xs me-2 px-2.5 py-0.5 rounded-xl">Hotel Cleaning</span>
                    <span class="bg-blue-100 text-blue-500 text-xs me-2 px-2.5 py-0.5 rounded-xl">Snowout</span>
                    <span class="bg-blue-100 text-blue-500 text-xs me-2 px-2.5 py-0.5 rounded-xl">Daily Cleaning</span>
                </div>
                <p id="subheader-1" class="text-blue-600 p-12 font-bold">Why Choose Fin-noys?</p>
                <h1 id="header-1" class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl tracking-normal text-blue-950 p-3">
                    Your trusted partner in professional
                    <span id="cleaning" class="text-blue-500 inline-flex items-center font-bold">
                        cleaning
                        <span id="spark" class="mr-2">
                            <img src="/images/icons/single-sparkle.svg" alt="" class="h-12 w-auto">
                        </span>
                    </span>
                    <br>
                </h1>
                <p id="para-desc" class="mt-8 text-[12px] text-pretty sm:text-base w-[75%] mx-auto text-justify">
                    With a team of dedicated experts and a commitment to excellence, we make every space feel fresh,
                    safe, and inviting — so you can focus on what truly matters — ensuring premium, meticulous, and
                    worry-free cleaning for every client
                </p>

                <div class="mt-16 grid grid-cols-1 lg:grid-cols-3 gap-8 w-full max-w-5xl mx-auto">
                    <div id="icon-expertise" class="absolute -top-30 -left-30 z-10 flex justify-center w-full">
                    </div>
                    <div class="frosted-card px-3 py-3 rounded-2xl soft-glow-2 feature-card scroll-hidden transition-all duration-300 ease-in-out hover:scale-[1.02] hover:rotate-1 hover:shadow-2xl"
                        data-animation-delay="0">
                        <h3 class="text-base font-bold mt-2 mb-4 text-blue-950">Proven Expertise</h3>
                        <p class="text-sm text-gray-700 text-justify">
                            Our proven expertise, reliable team, and commitment to excellence ensure every home
                            and business receives the highest standard of care — every time.
                        </p>
                    </div>

                    <div class="frosted-card px-6 py-3 rounded-2xl soft-glow-2 feature-card scroll-hidden transition-all duration-300 ease-in-out hover:scale-[1.02] hover:rotate-1 hover:shadow-2xl"
                        data-animation-delay="200">
                        <div id="icon-trustworthy" class="feature-icon"></div>
                        <h3 class="text-base font-bold mt-4 mb-4 text-blue-950">Professional and Trustworthy</h3>
                        <p class="text-sm text-gray-700 text-justify">
                            We deliver meticulous, high-quality cleaning that transforms spaces and leaves lasting
                            impressions.
                        </p>
                    </div>

                    <div class="frosted-card px-6 py-3 rounded-2xl soft-glow-2 feature-card scroll-hidden transition-all duration-300 ease-in-out hover:scale-[1.02] hover:rotate-1 hover:shadow-2xl"
                        data-animation-delay="400">
                        <div id="icon-licensed" class="feature-icon"></div>
                        <h3 class="text-base font-bold mt-3 mb-4 text-blue-950">Licensed Business</h3>
                        <p class="text-sm text-gray-700 text-justify">
                            Our official certification ensures that every service we provide meets strict quality
                            and
                            safety
                            standards, giving you complete peace of mind.
                        </p>
                    </div>
                </div>

                <div class="mt-10 flex items-center justify-center gap-x-5">
                    <button id="see-features-btn" type="button"
                        class="w-full sm:w-auto px-10 py-4 text-white z-10 bg-blue-950 font-medium rounded-full text-sm">See
                        Features</button>
                </div>

            </div>
        </div>
        <!-- Chatbot -->
        <div class="fixed bottom-4 right-3 sm:bottom-5 sm:right-5 z-50" x-data="homeChatbot()" x-init="init()">
            <button @click="toggle()" x-show="!open"
                    x-transition:enter="transition ease-out duration-200 delay-150"
                    x-transition:enter-start="opacity-0 scale-75"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-75"
                    style="width:56px;height:56px;background:#2563eb;box-shadow:0 4px 14px rgba(37,99,235,0.4);border:none;cursor:pointer;"
                    class="relative flex items-center justify-center rounded-full focus:outline-none focus:ring-4 focus:ring-blue-300 hover:scale-110 hover:shadow-xl">
                <i class="fas fa-comment-dots text-white text-xl"></i>
                <span x-show="unread > 0" x-text="unread" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold border-2 border-white"></span>
            </button>

            <div x-show="open" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="open = false"
                 class="hc-panel hc-panel-anim bg-white rounded-xl" style="border-radius:0.75rem;">

                <div class="flex items-center justify-between px-3 py-2.5 sm:px-4 sm:py-3 flex-shrink-0" style="background:linear-gradient(135deg,#0061ff 0%,#0a7cff 50%,#59a6ff 100%);">
                    <div class="flex items-center gap-2.5">
                        <img src="/images/icons/castcrew/castcrew-pic-logo-ondark.svg" class="w-7 h-7 rounded-full" alt="">
                        <div class="leading-none">
                            <p class="text-white text-xs font-semibold">Fin-noys Assistant</p>
                            <p class="text-white/70 text-[10px] flex items-center gap-1 mt-0.5"><span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span> Online</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button @click="clearChat()" class="w-7 h-7 rounded-full flex items-center justify-center text-white/70 hover:text-white hover:bg-white/15 transition" title="Clear"><i class="fas fa-redo" style="font-size:9px"></i></button>
                        <button @click="open = false" class="w-7 h-7 rounded-full flex items-center justify-center text-white/70 hover:text-white hover:bg-white/15 transition" title="Close"><i class="fas fa-times" style="font-size:11px"></i></button>
                    </div>
                </div>

                <div x-show="!started" class="flex-1 flex flex-col items-center justify-center px-5 py-6 text-center overflow-y-auto bg-white" style="min-height:0">
                    <div class="hc-float mb-4">
                        <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center">
                            <img src="/images/icons/opticrew-logo.svg" class="w-9 h-9" alt="">
                        </div>
                    </div>
                    <p class="text-gray-400 text-[9px] uppercase tracking-widest mb-1">Welcome to</p>
                    <h2 class="text-base font-bold text-blue-600 mb-1">Fin-noys Assistant</h2>
                    <p class="text-gray-400 text-[11px] leading-relaxed mb-4 max-w-[200px]">Ask me anything about our cleaning services!</p>
                    <div class="flex flex-col gap-1.5 w-full max-w-[190px]">
                        <button @click="send('What services do you offer?')" class="w-full py-1.5 text-[11px] rounded-full border border-blue-200 text-blue-600 hover:bg-blue-50 transition">Our Services</button>
                        <button @click="send('How do I book an appointment?')" class="w-full py-1.5 text-[11px] rounded-full border border-blue-200 text-blue-600 hover:bg-blue-50 transition">Book Appointment</button>
                        <button @click="send('What are your prices?')" class="w-full py-1.5 text-[11px] rounded-full border border-blue-200 text-blue-600 hover:bg-blue-50 transition">Pricing Info</button>
                        <button @click="send('How can I contact you?')" class="w-full py-1.5 text-[11px] rounded-full border border-blue-200 text-blue-600 hover:bg-blue-50 transition">Contact Us</button>
                    </div>
                </div>

                <div x-show="started" x-ref="msgBox" class="hc-scroll flex-1 overflow-y-auto py-2.5 space-y-1.5 bg-gray-50" style="min-height:0"></div>

                <div class="px-2.5 py-2 bg-white border-t border-gray-100 flex-shrink-0">
                    <div class="flex items-center gap-1.5 bg-gray-100 rounded-full pl-3 pr-1 py-0.5">
                        <input type="text" x-ref="input" x-model="text" @keydown.enter.prevent="send()" placeholder="Type a message..." class="flex-1 bg-transparent border-none outline-none text-xs text-gray-800 placeholder-gray-400 py-1.5 rounded-full">
                        <button @click="send()" :disabled="busy || !text.trim()" class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 transition-colors" :class="text.trim() && !busy ? 'bg-blue-500 hover:bg-blue-600 text-white' : 'bg-gray-300 text-gray-400'">
                            <i class="fas fa-paper-plane" style="font-size:10px"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    // LANGUAGE DROPDOWN TOGGLE
    document.addEventListener('DOMContentLoaded', function() {
        const languageToggle = document.getElementById('language-toggle');
        const languageDropdown = document.getElementById('language-dropdown');

        if (languageToggle && languageDropdown) {
            languageToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                languageDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                languageDropdown.classList.add('hidden');
            });
        }
    });
</script>
<script>
    // COUNTER-UP ANIMATION FUNCTION
    function animateCount(el) {
        // Only run if a target is defined
        if (!el.hasAttribute('data-target')) return;

        const target = parseFloat(el.getAttribute('data-target'));
        const prefix = el.getAttribute('data-prefix') || '';
        const suffix = el.getAttribute('data-suffix') || '';
        const separator = el.getAttribute('data-separator');
        const duration = 1500; // 1.5 seconds

        let start = 0;
        const startTime = performance.now();

        function updateCount(timestamp) {
            const elapsed = timestamp - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const current = progress * target;

            let displayValue;
            if (target < 1000) {
                // For simplified large numbers (e.g., 44 million)
                displayValue = Math.floor(current).toString();
            } else {
                // For numbers like 46000
                displayValue = Math.floor(current).toString();
            }

            if (separator === ',') {
                // Add comma separator for thousands
                displayValue = displayValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            el.textContent = prefix + displayValue + suffix;

            if (progress < 1) {
                requestAnimationFrame(updateCount);
            }
        }
        requestAnimationFrame(updateCount);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const featureCards = document.querySelectorAll('.feature-card');
        const counterElements = document.querySelectorAll('.counter-up');

        // ----------------------------------------------------
        // --- FEATURE CARD SCROLL ANIMATION LOGIC (EXISTING) ---
        // ----------------------------------------------------
        const cardObserverOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.2
        };

        const cardObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    const delay = parseInt(card.getAttribute('data-animation-delay')) || 0;

                    setTimeout(() => {
                        card.classList.remove('scroll-hidden');
                        card.classList.add('scroll-visible');
                    }, delay);
                    observer.unobserve(card);
                }
            });
        }, cardObserverOptions);

        featureCards.forEach(card => {
            if (!card.classList.contains('scroll-hidden') && !card.classList.contains('scroll-visible')) {
                card.classList.add('scroll-hidden');
            }
            cardObserver.observe(card);
        });

        // --- HOVER BLUR EFFECT LOGIC (EXISTING) ---
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

        let counterAnimationTriggered = false;

        const counterObserverOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.5 // Triggers when 50% of the section is visible
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

        const statsContainer = document.getElementById('stats-container');
        if (statsContainer) {
            counterObserver.observe(statsContainer);
        }
    });
</script>
<script>
function homeChatbot() {
    return {
        open: false, started: false, text: '', busy: false, unread: 0, history: [],
        isMobile() { return window.innerWidth <= 640; },
        init() {
            try {
                const h = sessionStorage.getItem('fn_chat_h');
                const m = sessionStorage.getItem('fn_chat_m');
                if (h) this.history = JSON.parse(h);
                if (m) { const msgs = JSON.parse(m); if (msgs.length) { this.started = true; this.$nextTick(() => { msgs.forEach(m => this._bubble(m.r, m.t)); this._scroll(); }); } }
            } catch(e) {}
        },
        toggle() { this.open = !this.open; if (this.open) { this.unread = 0; this.$nextTick(() => { this.$refs.input?.focus(); this._scroll(); }); } },
        async send(msg) {
            const q = msg || this.text.trim(); if (!q || this.busy) return;
            this.started = true; this.text = ''; this.busy = true; await this.$nextTick();
            this.history.push({role:'user',parts:[{text:q}]}); this._bubble('user', q);
            const loader = this._bubble('bot', 'Typing...', true);
            try {
                const res = await fetch('/api/chatbot/message', { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, body:JSON.stringify({message:q,chat_history:this.history}) });
                const data = await res.json(); loader.remove();
                const reply = data.success ? data.message : (data.message||'Sorry, something went wrong.');
                if (data.success) { this.history = data.chat_history||this.history; this.history.push({role:'model',parts:[{text:data.message}]}); }
                this._bubble('bot', reply);
            } catch(e) { loader.remove(); this._bubble('bot', 'Network error. Please try again.'); }
            this.busy = false; this._save(); if (!this.open) this.unread++; this.$nextTick(() => this.$refs.input?.focus());
        },
        _bubble(role, txt, loading=false) {
            const box = this.$refs.msgBox; if (!box) return null;
            const isUser = role==='user';
            const botAv = '/images/icons/castcrew/castcrew-pic-logo.svg';
            const wrap = document.createElement('div');
            wrap.className = 'flex '+(isUser?'flex-row-reverse':'flex-row')+' items-start gap-1.5 px-2.5 mb-2';
            const av = document.createElement('div');
            av.className = 'w-6 h-6 rounded-full shrink-0 flex items-center justify-center '+(isUser?'bg-blue-500':'bg-gray-200');
            av.innerHTML = isUser ? '<i class="fas fa-user text-white" style="font-size:10px"></i>' : '<img src="'+botAv+'" class="w-4 h-4" alt="">';
            const col = document.createElement('div');
            col.className = 'flex flex-col '+(isUser?'items-end':'items-start')+' min-w-0 max-w-[calc(100%-2.5rem)]';
            const b = document.createElement('div');
            b.className = (isUser?'hc-msg-user':'hc-msg')+' px-3 py-2 text-xs leading-relaxed break-words '+(isUser?'text-white rounded-2xl rounded-br-md':'bg-gray-100 text-gray-800 rounded-2xl rounded-bl-md');
            if (isUser) b.style.background='linear-gradient(135deg,#0084ff,#0066ff)';
            if (loading) b.classList.add('hc-typing');
            b.dataset.raw = txt;
            b.innerHTML = txt.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>').replace(/\*(.+?)\*/g,'<em>$1</em>').replace(/\n/g,'<br>').replace(/^•\s?(.+)/gm,'<span class="flex gap-1"><span>&bull;</span><span>$1</span></span>');
            col.appendChild(b);
            if (!loading) { const ts=document.createElement('span'); ts.className='text-[9px] text-gray-400 mt-0.5 px-1'; const now=new Date(); let h=now.getHours(); const mn=now.getMinutes(); const ap=h>=12?'PM':'AM'; h=h%12||12; ts.textContent=h+':'+(mn<10?'0':'')+mn+' '+ap; col.appendChild(ts); }
            wrap.appendChild(av); wrap.appendChild(col);
            box.appendChild(wrap); this._scroll(); return wrap;
        },
        _scroll() { const box = this.$refs.msgBox; if (box) box.scrollTop = box.scrollHeight; },
        clearChat() { if (!confirm('Clear chat?')) return; this.history=[]; this.started=false; if(this.$refs.msgBox) this.$refs.msgBox.innerHTML=''; sessionStorage.removeItem('fn_chat_h'); sessionStorage.removeItem('fn_chat_m'); },
        _save() { try { sessionStorage.setItem('fn_chat_h',JSON.stringify(this.history)); const box=this.$refs.msgBox; if(!box) return; const msgs=[]; box.querySelectorAll('.hc-msg').forEach(el=>{const isUser=el.style.background?.includes('0084ff'); msgs.push({r:isUser?'user':'bot',t:el.dataset.raw||el.textContent.trim()});}); sessionStorage.setItem('fn_chat_m',JSON.stringify(msgs)); } catch(e){} }
    };
}
</script>
</html>
