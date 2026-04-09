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

        /* Floating label inputs (mirrors legacy general-forgot-password layout) */
        .input-container {
            position: relative;
            margin-top: 0.3rem;
        }
        .input-container label {
            position: absolute;
            left: 3rem;
            top: 0.9rem;
            color: #07185778;
            pointer-events: none;
            transition: all 0.2s ease-out;
            font-size: small;
        }
        .input-container .input-field:focus + label,
        .input-container .input-field:not(:placeholder-shown) + label {
            top: -0.6rem;
            left: 1rem;
            font-size: 0.75rem;
            color: #0077FF;
            background-color: white;
            padding: 0 0.25rem;
        }
        .input-container .input-field {
            width: 100%;
            padding-left: 3rem;
            padding-right: 3rem;
            padding-top: 0.9rem;
            padding-bottom: 0.9rem;
            background-color: #f3f4f6;
            border-radius: 0.75rem;
            border: 1px solid transparent;
            color: #374151;
            outline: none;
            transition: border-color 0.2s ease;
        }
        .input-container .input-field:focus {
            border-color: #0077FF;
        }
        .input-container .input-field::placeholder { color: transparent; }
    </style>

    {{-- Alpine MUST be loaded before Livewire scripts so @entangle/x-data work after morphs --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @livewireStyles
</head>

<body class="flex flex-col justify-start items-center min-h-screen bg-[url('/images/backgrounds/login_bg.svg')] bg-cover bg-center bg-no-repeat bg-fixed gap-3 font-sans antialiased">

    {{-- Header with logo + back button (mirrors legacy forgot-password layout) --}}
    <header class="absolute top-0 left-0 w-full flex justify-between items-center px-12 py-8 z-20">
        <div class="flex items-center gap-2">
            <a href="{{ url('/') }}">
                <img src="{{ asset('/images/finnoys-text-logo-light.svg') }}" alt="Fin-noys" class="h-20 w-auto">
            </a>
        </div>

        <button type="button"
            x-data="{ step: 1 }"
            x-init="window.addEventListener('fp-step-changed', e => step = (e.detail && (e.detail.step ?? (Array.isArray(e.detail) ? e.detail[0]?.step : 1))) || 1)"
            :disabled="step === 1"
            :class="step === 1 ? 'opacity-30 cursor-not-allowed' : 'hover:text-blue-700'"
            onclick="if (window.Livewire) { window.Livewire.emit('fp-prev-step'); } else { history.back(); }"
            class="flex items-center gap-2 text-[#0077FF] transition-colors duration-200 text-sm font-medium">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
    </header>

    {{-- Centered card containing the Livewire wizard --}}
    <div class="w-full max-w-sm md:max-w-lg flex flex-col items-center text-center min-h-screen justify-center px-4 py-24">
        @livewire('auth.forgot-password-wizard')
    </div>

    @livewireScripts
    <x-global-dialogs />
</body>

</html>
