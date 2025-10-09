<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<style>
    /* FONTS */
    /* @font-face {
        font-family: 'fam-regular';
        src: url('/fonts/FamiljenGrotesk-Regular.otf') format('opentype');
        font-weight: normal;
        font-style: normal;
    }

    @font-face {
        font-family: 'fam-bold';
        src: url('/fonts/FamiljenGrotesk-Bold.otf') format('opentype');
        font-weight: bold;
        font-style: normal;
    }

    @font-face {
        font-family: 'fam-bold-italic';
        src: url('/fonts/FamiljenGrotesk-BoldItalic.otf') format('opentype');
        font-weight: bold;
        font-style: italic;
    } */

    * {
        color: #071957;
    }

    #container-2 {
        display: flex;
        flex-direction: column;
    }

    form {
        width: 100%;
        max-width: 500px;
        height: 70vh;
        padding: 2rem;
        border-radius: 12px;
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
        padding-bottom: 2em;
    }

    #btn-login {
        width: 100%;
        padding: 1em;
        background: #0077FF;
        color: white;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
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

    #login-header {
        font-size: 2.8rem;
        margin-top: 0.5em;
    }

    #donthaveacct,
    #login-header2,
    #text-1 {
        color: #07185785;
    }

    #terms-container input[type="checkbox"] {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        margin-top: 2px;
    }

    #terms-container span {
        text-align: justify;
    }

    #spotless_text {
        font-style: italic;
    }

</style>

<body class="font-sans font-normal text-base">
    <div class="flex flex-col md:flex-row min-h-screen w-full">
        <!-- INTERACTIVE PICTURE START-->
        <div class="container-1 hidden md:flex flex-col justify-center w-full md:w-1/2 lg:w-1/2"
            style="width:60%; padding:2em;">
            <div id="container-1-2" class="p-2 md:p-12 h-full flex flex-col justify-center items-center rounded-3xl"
                style="background-image: url({{ asset('/images/backgrounds/login_bg2.svg') }});background-size: cover;padding:5em;">
                <h1 id="header1" class=" md:text-4xl lg:text-5xl font-sans font-bold text-white mb-4 text-left " style="width: 90%;">
                    One-stop booking for<span>
                        <p id="spotless_text" style="color:#0077FF"> a spotless space</p>
                    </span></h1>
                <p id="desc1" class="md:text-sm lg:text-base text-justify mt-3" style="width: 90%;color: #688bffa8;">
                    Finnoys is a cleaning agency catering your cleaning needs with its offered quality cleaning services
                </p>
            </div>
        </div>
        <!-- INTERACTIVE PICTURE END-->
        <!-- LOG IN CONTENTS -->
        <div id="container-2" class="w-full md:w-1/2 flex justify-center items-center">
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div id="container-2-1">
                    <h1 id="login-header" class="font-sans font-bold mb-6">Log In</h1>
                    <p id="login-header2" class="text-base pb-6">Welcome to Fin-noys</p>
                </div>

                <!-- EMAIL FIELD -->
                <div class="input-group">
                    <i class="fa-solid fa-envelope icon pr-6 pl-2"></i>
                    <input type="text" id="input-username" name="email" class="pl-12 font-sans font-normal bg-gray-100">
                    <label for="input-username" class="text-gray-400 text-sm font-sans">Email/Username</label>
                </div>

                <!-- PASSWORD FIELD -->
                <div class="input-group">
                    <i class="fa-solid fa-key icon pr-6 pl-2"></i>
                    <input type="password" id="input-password" name="password" class="bg-gray-100">
                    <label for="input-password" class="text-gray-400 text-sm  font-sans">Password</label>
                </div>

                <div id="container-2-layer" class="text-sm">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="remember">
                        <span>Remember Me</span>
                    </label>
                    <a href="/forgot-password" class="text-blue-600 hover:underline text-sm">Forgot Password?</a>
                </div>

                <input type="submit" id="btn-login" value="Login">

                <div id="container-2-3" class="text-center mb-6 text-sm">
                    <p id="donthaveacct">
                        Don't have an account?
                        <span>
                            <a href="/register" id="createacc-label"
                                class="text-blue-600 hover:underline ml-1 text-sm">Create Account</a>
                        </span>
                    </p>
                </div>

                <label id="terms-container" class="flex items-center space-x-2">
                    <input type="checkbox" name="terms">
                    <span class="text-xs text-justify">
                        <p id="text-1">
                            By signing in to your account, you acknowledge that you have read and understood the
                            website's
                            <a id="terms-label" href="/terms" class="text-blue-600 hover:underline ml-1">Terms and
                                Conditions</a> and
                            <a id="privacy-label" href="/privacy" class="text-blue-600 hover:underline ml-1">Privacy
                                Policy</a>.
                        </p>
                    </span>
                </label>
            </form>
        </div>
    </div>
    </div>
</body>
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
    });
</script>

</html>