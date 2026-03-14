<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
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
        .input-group input.not-empty+label {
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

        #btn-login {
            width: 100%;
            padding: 1em;
            background: #0077FF;
            color: white;
            border-radius: 25px;
            cursor: pointer;
        }

        /* Checkbox styling */
        input[type="checkbox"] {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 1px solid #868282;
            border-radius: 4px;
            position: relative;
            cursor: pointer;
        }

        input[type="checkbox"]:checked {
            background-color: #0077FF;
        }

        input[type="checkbox"]:checked::after {
            content: "✓";
            color: white;
            font-size: 14px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

    </style>
</head>

<body class="font-normal font-sans">
    <div class="flex flex-col md:flex-row min-h-screen max-h-screen w-full h-full">

        <!-- INTERACTIVE PICTURE START -->
        <div class="container-1 w-full md:w-1/2 flex flex-col justify-center m-12 mr-1">
            <div id="container-1-2"
                class="p-6 h-full flex flex-col justify-center items-center rounded-3xl bg-cover bg-no-repeat bg-center relative overflow-hidden lg:block lg:flex md:block md:flex hidden"
                style="background-image: url('{{ asset('images/backgrounds/login_bg2.svg') }}');">

                {{-- Liquid Ether Overlay --}}
                <x-liquid-ether
                    :colors="['#1730F0', '#4169E1', '#93AAFF', '#0055FF']"
                    :opacity="0.35"
                    :mouse-force="18"
                    :auto-demo="true"
                />

                <h1 id="header1" class="text-5xl font-sans font-bold text-white mb-4 text-left w-2/3 relative z-10">
                    One-stop booking for
                    <span class="aurora-text font-sans italic font-extrabold" id="spotless_text">a spotless space</span>
                </h1>

                <p id="desc1" class="text-[#688bff89] text-opacity-40 text-base text-left mt-3 w-2/3 relative z-10">
                    Finnoys is a cleaning agency catering your cleaning needs with its offered quality cleaning
                    services.
                </p>
            </div>
        </div>

        <!-- LOG IN CONTENTS -->
        <div id="container-2"
            class="w-full h-screen md:w-1/2 flex justify-center align-items-center items-center pt-12">
            <form action="{{ route('login') }}" method="POST" class="space-y-4 w-1/2 h-fit">
                @csrf

                <div id="container-2-1" class="flex flex-col items-start my-12 w-full">
                    <a href="{{ url('/') }}" class="absolute top-20 right-[29rem]">
                        <img src="{{asset('/images/finnoys-text-logo-light.svg')}}" alt="Finnoys"
                            class="h-20 w-auto cursor-pointer">
                    </a>
                    <h1 id="login-header" class="font-sans font-bold text-4xl mb-3 mt-6 text-[#081032]">Log In</h1>
                    <p id="login-header2" class="text-[#07185788] font-sans font-normal text-sm mb-3">Welcome to
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

                <!-- LOGIN FIELD -->
                <div class="input-group">
                    <i class="fa-solid fa-envelope icon"></i>
                    <input type="text" id="input-username" name="login" class="bg-gray-100" autocomplete="username">
                    <label for="input-username" class="text-[#07185788] text-sm font-sans">Email / Username</label>
                </div>

                <!-- PASSWORD FIELD -->
                <div class="input-group">
                    <i class="fa-solid fa-key icon"></i>
                    <input type="password" id="input-password" name="password" class="bg-gray-100 pr-10"
                        autocomplete="current-password">

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

                <div id="container-2-layer" class="text-sm">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" class="border border-gray-300" name="remember">
                        <span class="text-gray-500 font-sans text-sm">Remember Me</span>
                    </label>
                    <a href="{{ route('forgot.password') }}" class="text-blue-600 hover:underline text-sm">Forgot
                        Password?</a>
                </div>

                <input type="submit" id="btn-login"
                    class="text-sm font-sans font-semibold hover:bg-blue-800 focus:outline-white" value="Login">

                {{-- <div id="container-2-3" class="text-center p-3 text-sm">
                    <p id="donthaveacct" class="text-[#07185788]">
                        Don't have an account?
                        <span id="createacc-label"
                            class="text-blue-600 font-sans font-bold ml-1 text-xs">Create account with Google</span>
                    </p>
                </div> --}}

                <!-- Sign in with Google -->
                <a href="{{ route('google.redirect') }}" id="btn-google"
                    class="w-full flex items-center justify-center gap-3 py-3 px-4 border border-gray-300 rounded-full bg-white hover:bg-gray-50 transition-colors duration-200 mt-3 no-underline">
                    <svg width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                    <span class="text-sm font-semibold text-gray-600">Sign in with Google</span>
                </a>

            </form>
        </div>
    </div>


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
            });

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
        });

    </script>


    <x-global-dialogs />
</body>

</html>