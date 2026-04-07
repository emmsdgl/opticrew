<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Forgot Password &mdash; Fin-Noys</title>
    <link rel="icon" href="{{ asset('images/icons/castcrew/castcrew-pic-logo.svg') }}" type="image/svg+xml">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class', theme: { fontFamily: { sans: ['Familjen Grotesk', 'system-ui', 'sans-serif'] } } }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">

    <style>
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
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        [x-cloak] { display: none !important; }
    </style>

    {{-- Alpine MUST be loaded before Livewire scripts so @entangle/x-data work after morphs --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @livewireStyles
</head>

<body class="font-normal font-sans bg-white">
    <div class="flex flex-col md:flex-row min-h-screen w-full">

        {{-- LEFT BRANDING PANEL (hidden on mobile, mirrors login.blade.php) --}}
        <div class="hidden md:flex md:w-1/2 flex-col justify-center p-6 lg:p-10 xl:p-12">
            <div class="p-6 lg:p-10 h-full flex flex-col justify-center items-center rounded-3xl bg-cover bg-no-repeat bg-center relative overflow-hidden"
                style="background-image: url('{{ asset('images/backgrounds/login_bg2.svg') }}'); background-color: #0a1a4a;">

                <div class="relative z-10 flex flex-col items-center text-center w-2/3">
                    <h1 class="text-3xl lg:text-4xl font-sans font-bold text-white mb-4">
                        Forgot your password?
                        <span class="aurora-text font-sans italic font-extrabold">no worries</span>
                    </h1>
                    <p class="text-blue-200 text-opacity-60 text-base mt-3">
                        We'll guide you through a secure 4-step process to get you back into your Fin-noys account safely.
                    </p>
                </div>
            </div>
        </div>

        {{-- RIGHT FORM PANEL (full width on mobile) --}}
        <div class="w-full min-h-screen md:w-1/2 flex justify-center items-start md:items-center px-4 py-8 sm:px-6 md:px-12 lg:px-16 xl:px-24">
            <div class="w-full max-w-md">
                <div class="flex justify-start mb-6 md:mb-10">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('/images/icons/finnoys-text-logo-light.svg') }}" alt="Fin-noys"
                            class="h-4 md:h-6 w-auto">
                    </a>
                </div>

                @livewire('auth.forgot-password-wizard')
            </div>
        </div>
    </div>

    @livewireScripts
</body>

</html>
