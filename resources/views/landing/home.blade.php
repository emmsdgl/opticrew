<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <title>Home</title>
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
    .chat-window {
        width: 400px;
        max-width: 90vw;
        height: 600px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .chat-header {
        padding-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
        flex-shrink: 0;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem 0;
        max-height: 450px;
        scroll-behavior: smooth;
    }

    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .chat-message {
        margin-bottom: 1rem;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        max-width: 85%;
        width: fit-content;
        word-wrap: break-word;
        word-break: break-word;
        line-height: 1.5;
        animation: fadeIn 0.3s ease-in;
        display: inline-block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .user-message {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        float: right;
        clear: both;
        border-bottom-right-radius: 0.25rem;
    }

    .assistant-message {
        background: #f1f5f9;
        color: #071957;
        float: left;
        clear: both;
        border-bottom-left-radius: 0.25rem;
        border: 1px solid #e2e8f0;
    }

    .loading-indicator {
        font-style: italic;
        opacity: 0.7;
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 0.7;
        }
        50% {
            opacity: 0.4;
        }
    }

    .chat-input-container {
        padding-top: 1rem;
        border-top: 2px solid #e5e7eb;
        flex-shrink: 0;
    }

    /* Rate Limit Indicator */
    .rate-limit-indicator {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0.75rem;
        background: #f8fafc;
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
        font-size: 0.875rem;
        border: 1px solid #e2e8f0;
    }

    .rate-limit-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
    }

    .rate-limit-badge.plenty {
        color: #059669;
    }

    .rate-limit-badge.low {
        color: #d97706;
    }

    .rate-limit-badge.critical {
        color: #dc2626;
    }

    .rate-limit-badge i {
        font-size: 1rem;
    }

    .cooldown-timer {
        color: #dc2626;
        font-weight: 600;
        animation: pulse 1s ease-in-out infinite;
    }

    .rate-limit-help {
        color: #64748b;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    /* Unread Message Badge */
    .unread-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #dc2626;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
        border: 2px solid white;
        animation: badgePulse 2s ease-in-out infinite;
    }

    @keyframes badgePulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

    #toggle-chat {
        position: relative;
    }
</style>

<body class="h-full">
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
        <div id="container-1" class="relative isolate text-center w-[60%] mx-auto pt-11 pb-24">
            <div class="hidden sm:mb-8 sm:flex sm:justify-center">
                <div
                    class="relative rounded-full px-3 py-1 text-sm/6 text-gray-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20">
                    {{ __('home.hero.tagline') }}
                </div>
            </div>
            <h1 id="header-1" class="text-6xl tracking-normal text-blue-950 p-10 sm:text-6xl">
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
            <div class="relative isolate text-center w-[60%] mx-auto pt-3 pb-16">
                <div id="badge-container">
                    <span class="bg-blue-100 text-blue-500 text-xs me-2 px-2.5 py-0.5 rounded-xl">Hotel Cleaning</span>
                    <span class="bg-blue-100 text-blue-500 text-xs me-2 px-2.5 py-0.5 rounded-xl">Snowout</span>
                    <span class="bg-blue-100 text-blue-500 text-xs me-2 px-2.5 py-0.5 rounded-xl">Daily Cleaning</span>
                </div>
                <p id="subheader-1" class="text-blue-600 p-12 font-bold">Why Choose Fin-noys?</p>
                <h1 id="header-1" class="text-6xl tracking-normal text-blue-950 p-3 sm:text-6xl">
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
        <div class="fixed end-14 bottom-12 group">
            <div id="chat-window"
                class="bg-white rounded-xl chat-window hidden absolute bottom-0 right-full mr-2 mb-2 p-6">

                <div class="chat-header">
                    <div class="flex flex-row items-center">
                        <img src="/images/icons/opticrew-logo.svg" class="h-8 w-8 m-3 mr-2">
                        <h2 class="text-blue-950 font-semibold mr-6">{{ __('common.chatbot.title') }}</h2>
                        <button id="close-chat" class="text-white hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Rate Limit Indicator -->
                <div id="rate-limit-indicator" class="rate-limit-indicator" style="display: none;">
                    <div class="rate-limit-badge plenty">
                        <i class="fas fa-comments"></i>
                        <span id="rate-limit-text">15/15 {{ __('common.chatbot.messages') }}</span>
                    </div>
                    <div id="cooldown-container" class="cooldown-timer" style="display: none;">
                        <i class="fas fa-clock"></i>
                        <span id="cooldown-text">{{ __('common.chatbot.wait') }} 0s</span>
                    </div>
                </div>

                <div id="chat-messages" class="chat-messages">
                    <div class="assistant-message chat-message">
                        {!! __('common.chatbot.welcome') !!}
                    </div>
                </div>

                <div class="chat-input-container">
                    <div class="flex items-center">
                        <input type="text" id="user-input" placeholder="{{ __('common.chatbot.placeholder') }}"
                            class="flex-grow p-1.5 border border-gray-100 rounded-l-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <button id="send-button"
                            class="bg-blue-600 text-white p-3 rounded-r-lg hover:bg-blue-700 transition duration-150">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button id="toggle-chat" type="button"
                class="flex items-center justify-center text-white bg-blue-600 rounded-full w-14 h-14 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none shadow-lg">
                <i class="fas fa-comment-dots w-6 h-4 text-blue-950"></i>
                <span class="sr-only">Open Chat Assistant</span>
                <span id="unread-badge" class="unread-badge" style="display: none;">0</span>
            </button>
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
<script type="module">

    // --- CHATBOT LOGIC ---
    const chatWindow = document.getElementById('chat-window');
    const toggleButton = document.getElementById('toggle-chat');
    const closeButton = document.getElementById('close-chat');
    const messagesContainer = document.getElementById('chat-messages');
    const userInput = document.getElementById('user-input');
    const sendButton = document.getElementById('send-button');
    const rateLimitIndicator = document.getElementById('rate-limit-indicator');
    const rateLimitText = document.getElementById('rate-limit-text');
    const rateLimitBadge = document.querySelector('.rate-limit-badge');
    const cooldownContainer = document.getElementById('cooldown-container');
    const cooldownText = document.getElementById('cooldown-text');
    const unreadBadge = document.getElementById('unread-badge');

    // ========== CONVERSATION RETENTION WITH SESSIONSTORAGE ==========
    const STORAGE_KEY = 'opticrew_chat_history';
    const STORAGE_MESSAGES_KEY = 'opticrew_chat_messages';

    let chatHistory = [];
    let cooldownInterval = null;
    let unreadCount = 0;
    let isChatOpen = false;
    const apiUrl = "/api/chatbot/message"; // Laravel backend API endpoint

    // Load chat history from sessionStorage (persists until browser/tab closes)
    function loadChatFromStorage() {
        try {
            const stored = sessionStorage.getItem(STORAGE_KEY);
            const storedMessages = sessionStorage.getItem(STORAGE_MESSAGES_KEY);

            if (stored) {
                chatHistory = JSON.parse(stored);
            }

            if (storedMessages) {
                const messages = JSON.parse(storedMessages);
                // Restore previous messages
                messages.forEach(msg => {
                    appendMessage(msg.role, msg.text, false); // false = don't save again
                });
                scrollToBottom();
            }
        } catch (error) {
            console.error('Error loading chat from storage:', error);
        }
    }

    // Save chat history to sessionStorage
    function saveChatToStorage() {
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(chatHistory));

            // Also save rendered messages for UI restoration
            const messages = [];
            const messageElements = messagesContainer.querySelectorAll('.chat-message:not(:first-child)'); // Skip welcome message
            messageElements.forEach(el => {
                const role = el.classList.contains('user-message') ? 'user' : 'assistant';
                const text = el.innerHTML.replace(/<br>/g, '\n');
                messages.push({ role, text });
            });
            sessionStorage.setItem(STORAGE_MESSAGES_KEY, JSON.stringify(messages));
        } catch (error) {
            console.error('Error saving chat to storage:', error);
        }
    }

    // Clear chat storage (call this when user logs in)
    window.clearChatStorage = function() {
        sessionStorage.removeItem(STORAGE_KEY);
        sessionStorage.removeItem(STORAGE_MESSAGES_KEY);
        chatHistory = [];
        console.log('Chat history cleared');
    };

    // Function to scroll to the bottom of the chat messages
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Function to update unread message badge
    function updateUnreadBadge() {
        if (unreadCount > 0) {
            unreadBadge.textContent = unreadCount;
            unreadBadge.style.display = 'flex';
        } else {
            unreadBadge.style.display = 'none';
        }
    }

    // Function to increment unread count (when AI responds and chat is closed)
    function incrementUnread() {
        if (!isChatOpen) {
            unreadCount++;
            updateUnreadBadge();
        }
    }

    // Function to clear unread count (when user opens chat)
    function clearUnread() {
        unreadCount = 0;
        updateUnreadBadge();
    }

    // Function to update rate limit indicator
    function updateRateLimitDisplay(rateLimitData) {
        if (!rateLimitData) return;

        const { remaining, limit, reset_in } = rateLimitData;

        // Show indicator
        rateLimitIndicator.style.display = 'flex';

        // Update text
        rateLimitText.textContent = `${remaining}/${limit} messages`;

        // Update badge color based on remaining messages
        rateLimitBadge.classList.remove('plenty', 'low', 'critical');
        if (remaining > limit * 0.5) {
            rateLimitBadge.classList.add('plenty'); // Green - plenty remaining
        } else if (remaining > 0) {
            rateLimitBadge.classList.add('low'); // Orange - running low
        } else {
            rateLimitBadge.classList.add('critical'); // Red - no messages left
        }

        // Handle cooldown timer
        if (remaining === 0) {
            startCooldown(reset_in);
        } else {
            stopCooldown();
        }
    }

    // Function to start cooldown timer
    function startCooldown(seconds) {
        stopCooldown(); // Clear any existing timer

        cooldownContainer.style.display = 'flex';
        sendButton.disabled = true;
        userInput.disabled = true;
        userInput.placeholder = "Rate limit reached...";

        let remainingSeconds = seconds;

        function updateTimer() {
            cooldownText.textContent = `Wait ${remainingSeconds}s`;
            remainingSeconds--;

            if (remainingSeconds < 0) {
                stopCooldown();
                // Re-enable input after cooldown
                sendButton.disabled = false;
                userInput.disabled = false;
                userInput.placeholder = "Ask a question...";
            }
        }

        updateTimer(); // Update immediately
        cooldownInterval = setInterval(updateTimer, 1000);
    }

    // Function to stop cooldown timer
    function stopCooldown() {
        if (cooldownInterval) {
            clearInterval(cooldownInterval);
            cooldownInterval = null;
        }
        cooldownContainer.style.display = 'none';
    }

    // Function to append a message to the chat UI
    function appendMessage(role, text, saveToStorage = true) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('chat-message', role === 'user' ? 'user-message' : 'assistant-message');
        messageDiv.innerHTML = text.replace(/\n/g, '<br>'); // Use innerHTML to handle markdown formatting and line breaks
        messagesContainer.appendChild(messageDiv);

        // Add clearfix to prevent float issues
        const clearDiv = document.createElement('div');
        clearDiv.style.clear = 'both';
        messagesContainer.appendChild(clearDiv);

        scrollToBottom();

        // Save to sessionStorage
        if (saveToStorage) {
            saveChatToStorage();
        }
    }

    // Function to handle the actual API call to Laravel backend
    async function getAssistantResponse(query) {
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    message: query,
                    chat_history: chatHistory
                })
            });

            const result = await response.json();

            // Update rate limit display if data is available
            if (result.rate_limit) {
                updateRateLimitDisplay(result.rate_limit);
            }

            if (result.success) {
                // Update chat history with the backend response
                chatHistory = result.chat_history || chatHistory;

                // Add assistant response to history
                chatHistory.push({
                    role: "model",
                    parts: [{ text: result.message }]
                });

                // Save updated history to storage
                saveChatToStorage();

                return result.message;
            } else {
                console.error("API error:", result.message);
                return result.message || "Sorry, I couldn't process that request.";
            }

        } catch (error) {
            console.error("Fetch error:", error);
            return "A network error occurred. Please check your connection and try again.";
        }
    }

    // Function to handle sending a message
    async function sendMessage() {
        const query = userInput.value.trim();
        if (!query) return;

        // Display user message and clear input
        appendMessage('user', query);
        userInput.value = '';
        sendButton.disabled = true;
        userInput.placeholder = "Assistant is typing...";

        // Display a loading indicator
        const thinkingMessage = document.createElement('div');
        thinkingMessage.classList.add('assistant-message', 'chat-message', 'loading-indicator');
        thinkingMessage.innerHTML = '...';
        messagesContainer.appendChild(thinkingMessage);
        scrollToBottom();

        // Get response from backend assistant
        const responseText = await getAssistantResponse(query);

        // Remove loading indicator
        messagesContainer.removeChild(thinkingMessage);

        // Display assistant response
        appendMessage('assistant', responseText);

        // Increment unread count if chat is closed
        incrementUnread();

        // Re-enable input
        sendButton.disabled = false;
        userInput.placeholder = "Ask a question...";
        userInput.focus();
    }

    // Event Listeners
    toggleButton.addEventListener('click', () => {
        chatWindow.classList.toggle('hidden');
        isChatOpen = !chatWindow.classList.contains('hidden');

        if (isChatOpen) {
            // Clear unread messages when opening chat
            clearUnread();
            userInput.focus();
        }
    });

    closeButton.addEventListener('click', () => {
        chatWindow.classList.add('hidden');
        isChatOpen = false;
    });

    sendButton.addEventListener('click', sendMessage);

    userInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // ========== INITIALIZE: LOAD CHAT HISTORY ON PAGE LOAD ==========
    window.addEventListener('DOMContentLoaded', () => {
        loadChatFromStorage();
    });

</script>
</html>
