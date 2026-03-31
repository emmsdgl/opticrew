<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="icon" href="{{ asset('images/icons/castcrew/castcrew-pic-logo.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
    <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        @import url('{{ asset('app.css') }}');

        /* Aurora text effect */
        .aurora-text {
            background: linear-gradient(135deg, #4169e1, #22d3ee, #0077FF, #06b6d4, #4169e1);
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

        #container-2 {
            display: flex;
            flex-direction: column;
        }

        /* Floating label container */
        .input-group {
            position: relative;
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .input-group input {
            width: 100%;
            padding: 1.2em 1em 0.6em 2.5em;
            border-radius: 8px;
            outline: none;
            border: 1px solid transparent;
            transition: border-color 0.2s ease;
        }

        @keyframes onAutoFillStart { from {} to {} }
        .input-group input:-webkit-autofill {
            animation-name: onAutoFillStart;
        }

        .input-group input:focus {
            border-color: #0077FF;
        }

        .input-group label {
            position: absolute;
            top: 1.1em;
            left: 2.5em;
            pointer-events: none;
            transition: all 0.2s ease;
            background-color: transparent;
            padding: 0 0.3em;
        }

        .input-group input:focus+label,
        .input-group input.not-empty+label,
        .input-group input:-webkit-autofill+label {
            top: -0.6em;
            left: 2.3em;
            font-size: 0.75rem;
            background-color: white;
            color: #0077FF;
        }

        /* ICONS */
        .input-group .icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #0077FF;
        }

        #container-2-layer {
            display: flex;
            justify-content: space-between;
            padding-top: 1em;
            padding-bottom: 1em;
        }

        #btn-login,
        #btn-google {
            width: 100%;
            padding: 1em;
            border-radius: 25px;
            cursor: pointer;
            box-sizing: border-box;
            font-size: 0.875rem;
        }

        #btn-login {
            background: #0077FF;
            color: white;
        }

        /* Checkbox styling */
        input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            min-width: 18px;
            min-height: 18px;
            border: 1.5px solid #c4c4c4;
            border-radius: 6px;
            position: relative;
            cursor: pointer;
            background: transparent;
            transition: all 0.15s ease;
        }

        input[type="checkbox"]:checked {
            background-color: #0077FF;
            border-color: #0077FF;
        }

        input[type="checkbox"]:checked::after {
            content: "✓";
            color: white;
            font-size: 13px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        input[type="checkbox"]:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Floating glassmorphism icons */
        @keyframes floatIcon {
            0%, 100% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(var(--float-x), var(--float-y));
            }
            50% {
                transform: translate(calc(var(--float-x) * -0.5), calc(var(--float-y) * -0.7));
            }
            75% {
                transform: translate(calc(var(--float-x) * 0.7), calc(var(--float-y) * 0.4));
            }
        }

        .floating-icon {
            animation: floatIcon var(--float-duration, 6s) ease-in-out infinite;
            animation-delay: var(--float-delay, 0s);
        }
    </style>
</head>

<body class="font-normal font-sans">
    <x-material-ui.page-loader />

    <div class="flex flex-col md:flex-row min-h-screen w-full">

        <!-- INTERACTIVE PICTURE START -->
        <div class="container-1 hidden md:flex md:w-1/2 flex-col justify-center p-6 lg:p-10 xl:p-12">
            <div id="container-1-2"
                class="p-6 lg:p-10 h-full flex flex-col justify-center items-center rounded-3xl bg-cover bg-no-repeat bg-center relative overflow-hidden"
                style="background-image: url('{{ asset('images/backgrounds/login_bg2.svg') }}');">

                {{-- Liquid Ether Overlay --}}
                <x-liquid-ether
                    :colors="['#1730F0', '#4169E1', '#93AAFF', '#0055FF']"
                    :opacity="0.35"
                    :mouse-force="18"
                    :auto-demo="true"
                />

                {{-- Glassmorphism arc ripples above & below description-container --}}
                <div class="absolute inset-0 z-[1] pointer-events-none overflow-hidden">

                    {{-- TOP ARC (inverted, opening upward) --}}
                    {{-- Outer ripple --}}
                    <div class="absolute left-1/2 -translate-x-1/2" style="bottom: 78%; width: 92%; aspect-ratio: 2/1;">
                        <div class="w-full h-full rounded-b-full backdrop-blur-sm bg-white/[0.04] border border-white/[0.12] border-t-0"></div>
                    </div>
                    {{-- Middle ripple --}}
                    <div class="absolute left-1/2 -translate-x-1/2" style="bottom: 82%; width: 78%; aspect-ratio: 2/1;">
                        <div class="w-full h-full rounded-b-full backdrop-blur-sm bg-white/[0.03] border border-white/[0.07] border-t-0"></div>
                    </div>
                    {{-- Inner ripple (most faded) --}}
                    <div class="absolute left-1/2 -translate-x-1/2" style="bottom: 86%; width: 64%; aspect-ratio: 2/1;">
                        <div class="w-full h-full rounded-b-full backdrop-blur-sm bg-white/[0.02] border border-white/[0.03] border-t-0"></div>
                    </div>

                    {{-- BOTTOM ARC glow (bright blue, behind the arcs) --}}
                    <div class="absolute left-1/2 -translate-x-1/2 bottom-0" style="width: 100%; height: 50%; transform: translateX(-50%);">
                        <div class="w-full h-full" style="background: radial-gradient(ellipse 60% 70% at 50% 100%, rgba(59, 130, 246, 0.7) 0%, rgba(59, 130, 246, 0.4) 25%, rgba(37, 99, 235, 0.15) 50%, transparent 75%);"></div>
                    </div>

                    {{-- BOTTOM ARC (opening downward) --}}
                    {{-- Outer ripple --}}
                    <div class="absolute left-1/2 -translate-x-1/2" style="top: 78%; width: 92%; aspect-ratio: 2/1;">
                        <div class="w-full h-full rounded-t-full backdrop-blur-sm bg-white/[0.04] border border-white/[0.12] border-b-0"></div>
                    </div>
                    {{-- Middle ripple --}}
                    <div class="absolute left-1/2 -translate-x-1/2" style="top: 82%; width: 78%; aspect-ratio: 2/1;">
                        <div class="w-full h-full rounded-t-full backdrop-blur-sm bg-white/[0.03] border border-white/[0.07] border-b-0"></div>
                    </div>
                    {{-- Inner ripple (most faded) --}}
                    <div class="absolute left-1/2 -translate-x-1/2" style="top: 86%; width: 64%; aspect-ratio: 2/1;">
                        <div class="w-full h-full rounded-t-full backdrop-blur-sm bg-white/[0.02] border border-white/[0.03] border-b-0"></div>
                    </div>
                </div>

                <div class="description-container flex flex-col justify-center items-center w-full my-3">
                    <h1 id="header1" class="text-3xl sm:text-3xl lg:text-4xl font-sans font-bold text-white mb-4 text-left w-2/3 relative z-10">
                        One-stop booking for
                        <span class="aurora-text font-sans italic font-extrabold" id="spotless_text">a spotless space</span>
                    </h1>
    
                    <p id="desc1" class="text-blue-200 text-opacity-40 text-base text-left mt-3 w-2/3 relative z-10">
                        Fin-noys is a cleaning agency catering your cleaning needs with its offered quality cleaning
                        services.
                    </p>
                </div>
            </div>
        </div>

        <!-- LOG IN CONTENTS -->
        <div id="container-2"
            class="w-full min-h-screen md:w-1/2 flex justify-center items-center px-6 py-12 md:px-12 lg:px-16 xl:px-24">
            <form action="{{ route('login') }}" method="POST" class="space-y-4 w-full max-w-md h-fit px-8">
                @csrf

                <div id="container-2-1" class="flex flex-col items-start my-12 w-full">
                    <a href="{{ url('/') }}" class="my-8">
                        <img src="{{asset('/images/icons/finnoys-text-logo-light.svg')}}" alt="Fin-noys"
                            class="h-3 md:h-6 w-auto cursor-pointer block">
                    </a>
                    <h1 id="login-header" class="font-sans font-bold text-3xl sm:text-3xl lg:text-4xl mb-3 text-blue-950">Log In</h1>
                    <p id="login-header2" class="text-[#07185788] font-sans font-normal text-sm sm:text-sm lg:text-base mb-3">Welcome to
                        Fin-noys</p>
                </div>

                <!-- Validation Errors via Dialog -->
                @if ($errors->any())
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            window.showErrorDialog('Almost there!', 'Please enter your email address and password to continue.');
                        });
                    </script>
                @endif

                <!-- Session Error (e.g. Google auth role conflict) -->
                @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            window.showErrorDialog('Account Conflict', @json(session('error')));
                        });
                    </script>
                @endif

                <!-- Banned Account Dialog -->
                @if (session('banned'))
                    <div x-data="{ showBanned: true }" x-show="showBanned" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display: none;">
                        <div class="absolute inset-0 bg-black/30"></div>
                        <div x-show="showBanned" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 scale-90 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative w-full max-w-sm bg-white rounded-2xl border border-gray-200 shadow-2xl overflow-hidden p-3">
                            <div class="px-8 pt-10 pb-6 flex flex-col items-center text-center">
                                <div class="w-14 h-14 rounded-full bg-red-100 border-2 border-red-400 flex items-center justify-center mb-6">
                                    <i class="fa-solid fa-ban text-red-500 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Account Suspended</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Your account has been banned from accessing the system. If you believe this is a mistake, please contact Fin-noys support.</p>
                            </div>
                            <div class="px-8 pb-8 flex flex-col gap-3">
                                <a href="https://mail.google.com/mail/?view=cm&to=finnoys0823@gmail.com&su=Account%20Ban%20Appeal&body=Hello%20Fin-noys%20Support%2C%0A%0AI%20believe%20my%20account%20has%20been%20banned%20by%20mistake.%20Please%20review%20my%20account.%0A%0AThank%20you."
                                   target="_blank" rel="noopener noreferrer"
                                   class="block w-full text-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/25 transition-all duration-200 text-sm no-underline">
                                    <i class="fa-solid fa-envelope mr-2"></i>Contact Us via Email
                                </a>
                                <button @click="showBanned = false" type="button" class="w-full px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition-all duration-200">
                                    Close
                                </button>
                                <p class="text-xs text-gray-400 text-center mt-1">
                                    <i class="fa-solid fa-envelope text-[10px] mr-1"></i>finnoys0823@gmail.com
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- LOGIN FIELD -->
                <div class="input-group" style="margin-bottom: 0.5rem;">
                    <span id="login-icon" class="icon" style="transition: opacity 0.15s ease, transform 0.15s ease, color 0.15s ease; display: inline-flex; color: #0077FF;"><i class="fa-solid fa-envelope"></i></span>
                    <input type="text" id="input-username" name="login" class="bg-gray-100 text-xs sm:text-xs lg:text-sm" autocomplete="username">
                    <label for="input-username" class="text-[#07185788] text-sm font-sans">Email / Username</label>
                </div>
                <div id="login-validation" class="text-sm h-5 flex items-center gap-1 ml-1 transition-all duration-200" style="display: none;"></div>

                <!-- PASSWORD FIELD -->
                <div class="input-group relative group" style="margin-bottom: 0.5rem;" id="password-group">
                    <span id="password-icon" class="icon" style="transition: opacity 0.15s ease, transform 0.15s ease, color 0.15s ease; display: inline-flex; color: #0077FF;"><i class="fa-solid fa-key"></i></span>
                    <input type="password" id="input-password" name="password" class="bg-gray-100 pr-10 text-xs sm:text-xs lg:text-sm disabled:opacity-40 disabled:cursor-not-allowed"
                        autocomplete="current-password" disabled>
                    <label for="input-password" class="text-[#07185788] text-sm font-sans">Password</label>

                    <button type="button" id="togglePassword"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-300 hover:text-blue-500 focus:outline-none pr-3">
                        <svg id="icon-eye-closed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/>
                        </svg>
                        <svg id="icon-eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <div id="password-validation" class="text-[11px] ml-1 h-5 flex items-center gap-1 transition-all duration-200" style="display: none;"></div>

                {{-- <div id="container-2-layer" class="text-sm">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="border border-gray-300" name="remember">
                        <span class="text-gray-500 font-sans text-sm">Remember Me</span>
                    </label>
                    <a href="{{ route('forgot.password') }}" class="text-blue-600 hover:underline text-sm">Forgot
                        Password?</a>
                </div> --}}

                <input type="submit" id="btn-login"
                    class="text-sm py-3 px-4 border border-gray-300 rounded-full font-sans font-semibold hover:bg-blue-800 focus:outline-white" value="Login">

                {{-- <div id="container-2-3" class="text-center p-3 text-sm">
                    <p id="donthaveacct" class="text-[#07185788]">
                        Don't have an account?
                        <span id="createacc-label"
                            class="text-blue-600 font-sans font-bold ml-1 text-xs">Create account with Google</span>
                    </p>
                </div> --}}

                
                <!-- Sign in with Google -->
                <a href="{{ route('google.redirect') }}" id="btn-google"
                    class="flex items-center justify-center gap-3 border border-gray-300 bg-white transition-colors duration-200 mt-3 no-underline opacity-50 cursor-not-allowed pointer-events-none"
                    aria-disabled="true">
                    <svg width="15" height="15" viewBox="0 0 48 48">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                    <span class="text-sm font-semibold text-gray-600">Sign in with Google</span>
                </a>

                <!-- Terms Agreement Checkbox -->
                <div class="mt-3 w-full items-center text-center" x-data="{
                    termsOpened: document.cookie.includes('finnoys_terms_accepted=1'),
                    privacyOpened: document.cookie.includes('finnoys_policy_accepted=1'),
                    get bothAccepted() { return this.termsOpened && this.privacyOpened; }
                }">
                    <div x-show="!bothAccepted">
                        <div class="flex items-start space-x-2" id="terms-label">
                            <div class="relative mt-0.5 flex-shrink-0" @click="if(!bothAccepted && (!termsOpened || !privacyOpened)) { window.showErrorDialog('Action Required', 'Please open and read both the Terms & Conditions and Privacy Policy before you can agree.'); }">
                                <input type="checkbox" id="google-terms-checkbox" disabled
                                    :class="(!termsOpened || !privacyOpened) && 'pointer-events-none'">
                            </div>
                            <span class="text-xs text-gray-500 leading-relaxed text-center">
                                By signing in, you confirm that you have read and agreed to the
                                <button type="button" id="terms-link" class="text-blue-600 hover:underline font-bold bg-transparent border-0 p-0 cursor-pointer text-xs">Terms & Conditions</button>
                                <span x-show="termsOpened" class="text-green-500 text-xs"><i class="fas fa-check-circle"></i></span>
                                and
                                <button type="button" id="privacy-link" class="text-blue-600 hover:underline font-bold bg-transparent border-0 p-0 cursor-pointer text-xs">Privacy Policy</button>
                                <span x-show="privacyOpened" class="text-green-500 text-xs"><i class="fas fa-check-circle"></i></span>.
                            </span>
                        </div>
                    </div>
                    <div x-show="bothAccepted" x-cloak>
                        <div class="flex items-start gap-2 text-green-600">
                            <i class="fas fa-check-circle text-sm mt-0.5 flex-shrink-0"></i>
                            <span class="text-xs sm:text-xs lg:text-sm">The
                                <button type="button" id="terms-link-accepted" class="text-green-700 font-semibold underline hover:text-green-800 bg-transparent border-0 p-0 cursor-pointer text-xs inline">Terms & Conditions</button>
                                and
                                <button type="button" id="privacy-link-accepted" class="text-green-700 font-semibold underline hover:text-green-800 bg-transparent border-0 p-0 cursor-pointer text-xs inline">Privacy Policy</button>
                                had already been read and accepted
                            </span>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>


    <!-- Terms & Conditions Modal (matches recruitment page) -->
    <div id="terms-modal" class="fixed inset-0 z-[250] flex items-center justify-center bg-black/50 p-4" style="display: none;">
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Terms & Conditions</h2>
                <button type="button" onclick="closeTermsModal()" class="w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full transition-colors">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5 overflow-y-auto flex-1" id="terms-modal-content">
                <div class="space-y-4 text-gray-700 dark:text-gray-300">
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Last Updated: November 5, 2025</p>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mt-4">1. Acceptance of Terms</h3>
                    <p class="text-sm leading-relaxed">By accessing or using the Castcrew workforce management and scheduling platform (the "System"), you ("User") agree to comply with and be bound by these Terms and Conditions.</p>
                    <p class="text-sm leading-relaxed">If you do not agree with any part of these Terms, you must refrain from using the System.</p>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mt-4">2. System Operations and Allocation Rules</h3>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-3">2.1 Workforce Allocation</h4>
                    <p class="text-sm leading-relaxed">Castcrew automatically determines the optimal number of employees required for each task based on employee availability, pending workload, budget constraints, and utilization targets.</p>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-3">2.2 Team Composition and Driver Requirement</h4>
                    <p class="text-sm leading-relaxed">Each assigned team must include at least one employee registered as having valid driving skills.</p>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-3">2.3 Task Prioritization</h4>
                    <p class="text-sm leading-relaxed">Tasks labeled with an "Arrival Status" are assigned the highest scheduling priority.</p>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-3">2.4 Schedule Generation and Re-Optimization</h4>
                    <p class="text-sm leading-relaxed">Schedules are generated automatically. If a new task is added before a schedule is finalized, the System will regenerate an optimized schedule.</p>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-3">2.5 Working Hours Compliance</h4>
                    <p class="text-sm leading-relaxed">The System enforces a maximum of 12 working hours per day per employee, in compliance with Finnish labor standards.</p>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mt-4">3. System Authority and Finality</h3>
                    <p class="text-sm leading-relaxed">All task assignments and schedules are the outcome of automated, rule-based optimization and are deemed final for operational purposes.</p>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mt-4">4. Modifications to System Rules</h3>
                    <p class="text-sm leading-relaxed">Castcrew reserves the right to modify these Terms at any time. Continued use constitutes acceptance of revised Terms.</p>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mt-4">5. Contact Information</h3>
                    <p class="text-sm leading-relaxed">For inquiries, contact: opticrewhelpcenter@gmail.com</p>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700" x-data="{ alreadyAccepted: document.cookie.includes('finnoys_terms_accepted=1') }">
                <button x-show="!alreadyAccepted" type="button" @click="markTermsRead(); alreadyAccepted = true;" class="w-full py-2.5 bg-[#0077FF] text-white text-sm font-semibold rounded-full hover:bg-blue-700 transition-colors">
                    I have read the Terms & Conditions
                </button>
                <button x-show="alreadyAccepted" x-cloak type="button" disabled class="w-full py-2.5 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-sm font-semibold rounded-full cursor-not-allowed">
                    Already Agreed
                </button>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal (matches recruitment page) -->
    <div id="privacy-modal" class="fixed inset-0 z-[250] flex items-center justify-center bg-black/50 p-4" style="display: none;">
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Privacy Policy</h2>
                <button type="button" onclick="closePrivacyModal()" class="w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full transition-colors">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5 overflow-y-auto flex-1" id="privacy-modal-content">
                <div class="space-y-4 text-gray-700 dark:text-gray-300">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Last updated: January 2024</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">1. Information We Collect</h3>
                    <p class="text-sm leading-relaxed">We collect information you provide directly to us, including your name, email address, phone number, payment information, and service preferences.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">2. How We Use Your Information</h3>
                    <p class="text-sm leading-relaxed">We use the information we collect to provide, maintain, and improve our services, to process your bookings, and to communicate with you.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">3. Information Sharing</h3>
                    <p class="text-sm leading-relaxed">We do not sell or rent your personal information to third parties. We may share your information with service providers who assist us.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">4. Data Security</h3>
                    <p class="text-sm leading-relaxed">We implement appropriate technical and organizational measures to protect your personal information.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">5. Your Rights</h3>
                    <p class="text-sm leading-relaxed">You have the right to access, update, or delete your personal information. You may also opt-out of promotional communications.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">6. Cookies and Tracking</h3>
                    <p class="text-sm leading-relaxed">We use cookies and similar tracking technologies to improve our services. You can control cookies through your browser settings.</p>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">7. Contact Us</h3>
                    <p class="text-sm leading-relaxed">If you have any questions about this Privacy Policy, please contact us at privacy@finnoys.com.</p>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700" x-data="{ alreadyAccepted: document.cookie.includes('finnoys_policy_accepted=1') }">
                <button x-show="!alreadyAccepted" type="button" @click="markPrivacyRead(); alreadyAccepted = true;" class="w-full py-2.5 bg-[#0077FF] text-white text-sm font-semibold rounded-full hover:bg-blue-700 transition-colors">
                    I have read the Privacy Policy
                </button>
                <button x-show="alreadyAccepted" x-cloak type="button" disabled class="w-full py-2.5 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-sm font-semibold rounded-full cursor-not-allowed">
                    Already Agreed
                </button>
            </div>
        </div>
    </div>

    <script>
        function closeTermsModal() { document.getElementById('terms-modal').style.display = 'none'; }
        function closePrivacyModal() { document.getElementById('privacy-modal').style.display = 'none'; }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('.input-group input');

            inputs.forEach(input => {
                // Handle pre-filled fields (e.g., autofill)
                if (input.value.trim() !== '') {
                    input.classList.add('not-empty');
                }

                // Toggle label floating based on content
                input.addEventListener('input', () => {
                    if (input.value.trim() !== '') {
                        input.classList.add('not-empty');
                    } else {
                        input.classList.remove('not-empty');
                    }
                });

                // Detect browser autofill via animation event
                input.addEventListener('animationstart', (e) => {
                    if (e.animationName === 'onAutoFillStart') {
                        input.classList.add('not-empty');
                    }
                });
            });

            // Re-check after delay for autofill that happens after DOMContentLoaded
            setTimeout(() => {
                inputs.forEach(input => {
                    if (input.value.trim() !== '') {
                        input.classList.add('not-empty');
                    }
                });
            }, 500);

            const passwordInput = document.getElementById('input-password');
            const togglePassword = document.getElementById('togglePassword');
            const iconEyeClosed = document.getElementById('icon-eye-closed');
            const iconEyeOpen = document.getElementById('icon-eye-open');

            togglePassword.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                iconEyeClosed.style.display = isPassword ? 'none' : 'block';
                iconEyeOpen.style.display = isPassword ? 'block' : 'none';
            });

            // Terms & Privacy checkbox logic for Google Sign-in (persisted via cookies)
            let termsOpened = document.cookie.includes('finnoys_terms_accepted=1');
            let privacyOpened = document.cookie.includes('finnoys_policy_accepted=1');
            const googleCheckbox = document.getElementById('google-terms-checkbox');
            const googleBtn = document.getElementById('btn-google');
            const termsLink = document.getElementById('terms-link');
            const privacyLink = document.getElementById('privacy-link');

            function updateCheckboxState() {
                if (termsOpened && privacyOpened) {
                    googleCheckbox.disabled = false;
                }
            }

            function updateGoogleBtn() {
                if (googleCheckbox.checked) {
                    googleBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                    googleBtn.classList.add('hover:bg-gray-50');
                    googleBtn.setAttribute('aria-disabled', 'false');
                } else {
                    googleBtn.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                    googleBtn.classList.remove('hover:bg-gray-50');
                    googleBtn.setAttribute('aria-disabled', 'true');
                }
            }

            // Restore state on load — if both already accepted, enable Google button directly
            if (termsOpened && privacyOpened) {
                enableGoogleBtn();
            }

            termsLink.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('terms-modal').style.display = 'flex';
            });

            privacyLink.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('privacy-modal').style.display = 'flex';
            });

            // Also wire up the accepted-state links
            const termsLinkAccepted = document.getElementById('terms-link-accepted');
            const privacyLinkAccepted = document.getElementById('privacy-link-accepted');
            if (termsLinkAccepted) {
                termsLinkAccepted.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    document.getElementById('terms-modal').style.display = 'flex';
                });
            }
            if (privacyLinkAccepted) {
                privacyLinkAccepted.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    document.getElementById('privacy-modal').style.display = 'flex';
                });
            }

            // Enable the Google button directly (skip checkbox dependency)
            function enableGoogleBtn() {
                googleBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                googleBtn.classList.add('hover:bg-gray-50');
                googleBtn.setAttribute('aria-disabled', 'false');
            }

            // Called from "I have read" buttons in modals
            window.markTermsRead = function() {
                termsOpened = true;
                document.cookie = 'finnoys_terms_accepted=1; path=/; max-age=' + (30 * 24 * 60 * 60);
                document.getElementById('terms-modal').style.display = 'none';
                // Sync Alpine x-data so x-show directives update
                const termsDiv = document.querySelector('#terms-label')?.closest('[x-data]');
                if (termsDiv && termsDiv._x_dataStack) {
                    termsDiv._x_dataStack[0].termsOpened = true;
                }
                if (termsOpened && privacyOpened) enableGoogleBtn();
                if (window.showSuccessDialog) window.showSuccessDialog('Terms Accepted', 'You have read and accepted the Terms & Conditions.');
            };
            window.markPrivacyRead = function() {
                privacyOpened = true;
                document.cookie = 'finnoys_policy_accepted=1; path=/; max-age=' + (30 * 24 * 60 * 60);
                document.getElementById('privacy-modal').style.display = 'none';
                // Sync Alpine x-data so x-show directives update
                const termsDiv = document.querySelector('#terms-label')?.closest('[x-data]');
                if (termsDiv && termsDiv._x_dataStack) {
                    termsDiv._x_dataStack[0].privacyOpened = true;
                }
                if (termsOpened && privacyOpened) enableGoogleBtn();
                if (window.showSuccessDialog) window.showSuccessDialog('Privacy Policy Accepted', 'You have read and accepted the Privacy Policy.');
            };

            googleCheckbox.addEventListener('change', updateGoogleBtn);
        });

    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var loginInput = document.getElementById('input-username');
        var passwordInput = document.getElementById('input-password');
        var loginMsg = document.getElementById('login-validation');
        var passwordMsg = document.getElementById('password-validation');
        var vTimer = null, prevL = '', prevP = '', accountFound = false, cachedUserId = null, passwordValid = false;

        function setMsg(el, type, text) {
            if (!text) { el.style.display = 'none'; el.innerHTML = ''; return; }
            var c = { error: 'text-red-500', 'error-noicon': 'text-red-500', success: 'text-green-600', 'success-noicon': 'text-green-600', loading: 'text-gray-400' };
            var ic = { error: '<i class="fa-solid fa-circle-xmark"></i>', 'error-noicon': '', success: '<i class="fa-solid fa-circle-check"></i>', 'success-noicon': '', loading: '<i class="fa-solid fa-spinner fa-spin"></i>' };
            el.className = 'text-[11px] h-5 flex items-center gap-1 ml-1 ' + (c[type] || '');
            el.innerHTML = (ic[type] || '') + '<span>' + text + '</span>';
            el.style.display = 'flex';
        }

        var loginLabel = document.querySelector('label[for="input-username"]');
        var passwordLabel = document.querySelector('label[for="input-password"]');
        var loginIcon = document.getElementById('login-icon');

        var svgCheck = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>';
        var svgX = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>';
        var svgEnvelope = '<i class="fa-solid fa-envelope"></i>';
        var svgKey = '<i class="fa-solid fa-key"></i>';

        function morphIcon(iconEl, content, color) {
            iconEl.style.opacity = '0';
            iconEl.style.transform = 'translateY(-50%) scale(0.4)';
            setTimeout(function() {
                iconEl.innerHTML = content;
                iconEl.style.color = color || '#0077FF';
                iconEl.style.opacity = '1';
                iconEl.style.transform = 'translateY(-50%) scale(1)';
            }, 150);
        }

        var passwordIcon = document.getElementById('password-icon');

        function setLoginStyle(state) {
            loginInput.style.borderColor = '';
            loginInput.style.backgroundColor = '';
            loginInput.style.color = '';
            loginLabel.style.color = '';
            if (state === 'found') {
                loginInput.style.borderColor = '#22c55e';
                loginLabel.style.color = '#22c55e';
                morphIcon(loginIcon, svgCheck, '#22c55e');
            } else if (state === 'found-blur') {
                loginInput.style.borderColor = '#3b82f6';
                loginInput.style.backgroundColor = '#eff6ff';
                loginInput.style.color = '#1e40af';
                loginLabel.style.color = '#0077FF';
                morphIcon(loginIcon, svgEnvelope, '#0077FF');
            } else if (state === 'not-found') {
                loginInput.style.borderColor = '#ef4444';
                loginLabel.style.color = '#ef4444';
                morphIcon(loginIcon, svgX, '#ef4444');
            } else {
                morphIcon(loginIcon, svgEnvelope, '#0077FF');
            }
        }

        function setPasswordStyle(state) {
            passwordInput.style.borderColor = '';
            passwordInput.style.backgroundColor = '';
            passwordInput.style.color = '';
            passwordLabel.style.color = '';
            if (state === 'valid') {
                passwordInput.style.borderColor = '#22c55e';
                passwordLabel.style.color = '#22c55e';
                morphIcon(passwordIcon, svgCheck, '#22c55e');
            } else if (state === 'valid-blur') {
                passwordInput.style.borderColor = '#3b82f6';
                passwordInput.style.backgroundColor = '#eff6ff';
                passwordInput.style.color = '#1e40af';
                passwordLabel.style.color = '#0077FF';
                morphIcon(passwordIcon, svgKey, '#0077FF');
            } else if (state === 'invalid') {
                passwordInput.style.borderColor = '#ef4444';
                passwordLabel.style.color = '#ef4444';
                morphIcon(passwordIcon, svgX, '#ef4444');
            } else {
                morphIcon(passwordIcon, svgKey, '#0077FF');
            }
        }

        function setPasswordEnabled(enabled) {
            passwordInput.disabled = !enabled;
            if (!enabled) { passwordInput.value = ''; setMsg(passwordMsg, null, ''); prevP = ''; setPasswordStyle('reset'); }
        }

        function checkLogin() {
            var l = loginInput.value.trim();
            if (!l) { setMsg(loginMsg, null, ''); setMsg(passwordMsg, null, ''); setPasswordEnabled(false); accountFound = false; prevL = ''; setLoginStyle('reset'); return; }
            if (l === prevL) return;
            prevL = l;
            setMsg(loginMsg, 'loading', 'Checking...');
            setLoginStyle('reset');
            var csrfEl = document.querySelector('input[name="_token"]');
            fetch('{{ route("auth.validate-login") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfEl.value, 'Accept': 'application/json' },
                body: JSON.stringify({ login: l, password: '' })
            }).then(function(r) { return r.json(); }).then(function(d) {
                if (loginInput.value.trim() !== l) return;
                if (d.login_checked) {
                    accountFound = d.login_exists;
                    cachedUserId = d.user_id;
                    if (d.login_exists) {
                        setMsg(loginMsg, null, '');
                        setLoginStyle(document.activeElement === loginInput ? 'found' : 'found-blur');
                        setPasswordEnabled(true);
                    } else {
                        cachedUserId = null;
                        setMsg(loginMsg, 'error-noicon', 'No account found with this email or username');
                        setLoginStyle('not-found');
                        setPasswordEnabled(false);
                    }
                }
            }).catch(function() { setMsg(loginMsg, null, ''); setLoginStyle('reset'); });
        }

        function checkPassword() {
            var l = loginInput.value.trim(), p = passwordInput.value;
            if (!accountFound || !p) { setMsg(passwordMsg, null, ''); return; }
            if (p === prevP) return;
            prevP = p;
            setMsg(passwordMsg, 'loading', 'Verifying...');
            var csrfEl = document.querySelector('input[name="_token"]');
            fetch('{{ route("auth.validate-login") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfEl.value, 'Accept': 'application/json' },
                body: JSON.stringify({ user_id: cachedUserId, login: l, password: p })
            }).then(function(r) { return r.json(); }).then(function(d) {
                if (passwordInput.value !== p) return;
                if (d.password_checked) {
                    passwordValid = d.password_valid;
                    if (d.password_valid) { setMsg(passwordMsg, null, ''); setPasswordStyle(document.activeElement === passwordInput ? 'valid' : 'valid-blur'); }
                    else { setMsg(passwordMsg, 'error-noicon', 'Incorrect password'); setPasswordStyle('invalid'); }
                }
            }).catch(function() { setMsg(passwordMsg, null, ''); setPasswordStyle('reset'); });
        }

        var pwTimer = null;
        function debounceLogin() { clearTimeout(vTimer); vTimer = setTimeout(checkLogin, 300); }
        function debouncePassword() { clearTimeout(pwTimer); pwTimer = setTimeout(checkPassword, 400); }
        loginInput.addEventListener('input', function() { accountFound = false; cachedUserId = null; passwordValid = false; setPasswordEnabled(false); setMsg(passwordMsg, null, ''); prevP = ''; setLoginStyle('reset'); debounceLogin(); });
        loginInput.addEventListener('focus', function() { if (accountFound) setLoginStyle('found'); });
        loginInput.addEventListener('blur', function() { clearTimeout(vTimer); if (loginInput.value.trim()) { checkLogin(); if (accountFound) setLoginStyle('found-blur'); } });
        passwordInput.addEventListener('input', function() { passwordValid = false; setPasswordStyle('reset'); setMsg(passwordMsg, null, ''); prevP = ''; if (accountFound && passwordInput.value) debouncePassword(); });
        passwordInput.addEventListener('focus', function() { if (passwordValid) setPasswordStyle('valid'); });
        passwordInput.addEventListener('blur', function() { clearTimeout(pwTimer); if (accountFound && passwordInput.value) checkPassword(); if (passwordValid) setPasswordStyle('valid-blur'); });

    });
    </script>

    <x-global-dialogs />
</body>

</html>