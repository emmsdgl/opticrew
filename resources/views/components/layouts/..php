@props([
    'title' => 'Forgot Password'
])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            min-height: 100vh;
        }
        
        #header-1, #step-number, #step-number-1, #step-number-2, #step-number-3 {
            color: #0077FF;
        }
        
        #header-1-2 {
            color: #07185778;
        }
        
        #header-2 {
            color: #071957;
        }
        
        #header-3 {
            color: #07185778;
            text-align: justify;
        }
        
        #header-container {
            padding: 1em;
            margin-top: 2em;
        }
        
        /* Floating Label Specific Styles */
        .input-container {
            position: relative;
            margin-top: 0.3rem;
        }

        .input-container label {
            position: absolute;
            left: 3rem;
            top: 0.8rem;
            color: #07185778;
            pointer-events: none;
            transition: all 0.2s ease-out;
            font-size: small;
        }

        .input-container .input-field:focus + label,
        .input-container .input-field:not(:placeholder-shown) + label {
            top: -0.8rem;
            left: 1rem;
            font-size: 0.75rem;
            color: #0077FF;
            background-color: white;
            padding: 0 0.25rem;
        }

        .input-container .input-field {
            padding-left: 3rem;
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
        }

        .input-container .input-field:focus::placeholder {
            color: transparent;
        }
        
        .input-container .input-field:not(:placeholder-shown)::placeholder {
            color: transparent;
        }
        
        /* Container for OTP inputs */
        #otp-container {
            display: flex;
            justify-content: center;
            gap: 13px;            
            padding-top: 0.4rem;
            padding-bottom: 1rem;
        }

        /* OTP input slots */
        #otp-container input[type="text"] {
            width: 40px;
            height: 50px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            border: 1px solid #d1d5db;
            background-color: #c2c6d94b;
            border-radius: 10px;
            outline: none;
            transition: all 0.2s ease;
        }

        /* Focused state */
        #otp-container input[type="text"]:focus {
            border-color: #0077FF;
            box-shadow: 0 0 0 3px rgba(0, 119, 255, 0.2);
        }
    </style>
</head>

<body class="flex flex-col justify-start items-center min-h-screen bg-[url('/images/backgrounds/login_bg.svg')] bg-cover bg-center bg-no-repeat bg-fixed gap-3">
    <!-- Header with logo + back button -->
    <header class="absolute top-0 left-0 w-full flex justify-between items-center px-12 py-8">
        <div class="flex items-center gap-2">
            <span class="text-[#0077FF]"><img src="/images/finnoys-text-logo-light.svg" alt="" class="h-20 w-auto"></span>
        </div>

        <button 
            onclick="window.location.href='{{ route('login') }}'"
            class="flex items-center gap-2 text-[#0077FF] hover:text-blue-700 transition-colors duration-200 text-sm font-medium">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
    </header>

    <div id="pasres-main-container" class="w-full gap-8 max-w-sm md:max-w-lg lg:max-w-screen md:p-10 flex flex-col items-center text-center min-h-screen justify-center">
        
        <!-- STEP 1 Content -->
        <div id="step1" class="step-content w-full">
            {{ $slot1 }}
        </div>

        <!-- STEP 2 Content -->
        <div id="step2" class="step-content w-full hidden">
            {{ $slot2 }}
        </div>

        <!-- STEP 3 Content -->
        <div id="step3" class="step-content w-full hidden">
            {{ $slot3 }}
        </div>
        
        <!-- Progress Bar -->
        <div id="stepctr-container-2" class="w-full mt-8">
            <div id="progress-container" class="w-full flex justify-center space-x-2">
                <div class="progress-bar h-1.5 w-32 rounded-full bg-blue-600"></div>
                <div class="progress-bar h-1.5 w-32 rounded-full bg-gray-300"></div>
                <div class="progress-bar h-1.5 w-32 rounded-full bg-gray-300"></div>
            </div>
        </div>
    </div>
    
    @stack('scripts')
</body>

</html>