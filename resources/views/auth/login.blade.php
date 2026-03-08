<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
    <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        @import url('{{ asset('app.css') }}');

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
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    'sans': ['Familjen Grotesk', 'system-ui', 'sans-serif'],
                }
            }
        }
    </script>
</head>

<body class="font-normal font-sans">
    <div class="flex flex-col md:flex-row min-h-screen max-h-screen w-full h-full">

        <!-- INTERACTIVE PICTURE START -->
        <div class="container-1 w-full md:w-1/2 flex flex-col justify-center m-12 mr-1">
            <div id="container-1-2"
                class="p-6 h-full flex flex-col justify-center items-center rounded-3xl bg-cover bg-no-repeat bg-center lg:block lg:flex md:block md:flex hidden"
                style="background-image: url('{{ asset('images/backgrounds/login_bg2.svg') }}');">

                <h1 id="header1" class="text-5xl font-sans font-bold text-white mb-4 text-left w-2/3">
                    One-stop booking for
                    <span class="text-[#0077FF] font-sans italic" id="spotless_text">a spotless space</span>
                </h1>

                <p id="desc1" class="text-[#688bff89] text-opacity-40 text-base text-left mt-3 w-2/3 ">
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
                    <img src="{{asset('/images/finnoys-text-logo-light.svg')}}" alt=""
                        class="absolute h-20 w-auto top-20 right-[29rem]">
                    <h1 id="login-header" class="font-sans font-bold text-4xl mb-3 mt-6 text-[#081032]">Log In</h1>
                    <p id="login-header2" class="text-[#07185788] font-sans font-normal text-sm mb-3">Welcome to
                        Fin-noys</p>
                </div>

                <!-- Floating Validation Errors -->
                @if ($errors->any())
                    <div id="floating-alert"
                         class="fixed top-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-5 py-3 bg-red-500 text-white rounded-lg shadow-lg text-sm transition-all duration-300 opacity-0 -translate-y-4"
                         style="min-width: 300px; max-width: 90vw;">
                        <i class="fa-solid fa-circle-exclamation text-lg flex-shrink-0"></i>
                        <div class="flex-1">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                        <button onclick="dismissAlert()" class="flex-shrink-0 ml-2 hover:text-red-200 transition-colors">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
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
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-300 hover:text-blue-500 focus:outline-none">
                        <i class="fa-solid fa-eye pr-3"></i>
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

                <div id="container-2-3" class="text-center p-3 text-sm">
                    <p id="donthaveacct" class="text-[#07185788]">
                        Don't have an account?
                        <span>
                            <a href="{{ route('signup') }}" id="createacc-label"
                                class="text-blue-600 font-sans font-bold hover:underline ml-1 text-xs">Create
                                Account</a>
                        </span>
                    </p>
                </div>

            </form>
        </div>
    </div>


    <script>
        // Floating alert
        function dismissAlert() {
            const alert = document.getElementById('floating-alert');
            if (alert) {
                alert.classList.add('opacity-0', '-translate-y-4');
                setTimeout(() => alert.remove(), 300);
            }
        }

        (function () {
            const alert = document.getElementById('floating-alert');
            if (alert) {
                // Animate in
                requestAnimationFrame(() => {
                    alert.classList.remove('opacity-0', '-translate-y-4');
                });
                // Auto-dismiss after 5 seconds
                setTimeout(() => dismissAlert(), 5000);
            }
        })();

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
            const icon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        });

    </script>
</body>

</html>