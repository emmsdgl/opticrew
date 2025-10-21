<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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

        body {
            background-image: url(/images/backgrounds/login_bg.svg);
            background-size: cover;
        }

        /* Override font for the stepper for a modern look */
        #stepper-container * {
            font-family: 'Inter', sans-serif !important;
            color: #071957;
        }

        /* Custom styles for the line */
        .timeline-line {
            height: 2px;
            background-color: #e5e7eb;
            position: absolute;
            top: 28%;
            left: 0;
            right: 0;
            transform: translateY(-50%);
            z-index: -1;
            margin: 0 40px;
            /* Adjust based on step icon size to start and end correctly */
        }

        .step-container {
            flex: 1;
            /* Make steps distribute space evenly */
            max-width: 33.33%;
            /* 3 steps */
            z-index: 10;
        }

        .step-text {
            min-width: 120px;
        }

        /* Transition color for active line (progress) */
        .progress-line {
            height: 2px;
            background-color: #3b82f6;
            /* Blue-600 */
            position: absolute;
            top: 28%;
            left: 40px;
            /* Start after first icon center */
            transform: translateY(-50%);
            z-index: 0;
            transition: width 0.5s ease-in-out;
        }

        .timeline-icon {
            box-shadow: 0 0 0 4px white;
            /* Mimic ring-4 ring-white */
        }

        .completed-step .timeline-icon {
            background-color: #10b981;
            /* Green for completed */
        }

        .current-step .timeline-icon {
            background-color: #3b82f6;
            /* Blue for current */
        }

        .pending-step .timeline-icon {
            background-color: #9ca3af;
            /* Gray for pending */
        }

        /* ----------------------------------------------- */

        body {
            background-color: #f7fafc;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        #header-container {
            display: flex;
            flex-direction: row;
        }

        #content-container {
            display: flex;
            flex-direction: column;
        }

        #step-label {
            font-size: small;
            font-family: 'fam-bold';
        }

        #step-head {
            font-size: 2.7rem;
            font-family: 'fam-bold';
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        #step-desc {
            color: #0718577c;
            width: 100%;
        }

        #form-head {
            font-size: 1.8rem;
            text-align: center;
        }

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

        .input-container .input-field:focus+label,
        .input-container .input-field:not(:placeholder-shown)+label {
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

        .input-container .input-field::placeholder {
            color: transparent;
        }

        .input-container .input-field:focus::placeholder {
            color: #9ca3af;
        }

        .input-container .input-field:not(:placeholder-shown)::placeholder {
            color: transparent;
        }

        .custom-dropdown-container {
            position: relative;
            width: 100%;
            border-radius: 0.75rem;
        }

        .custom-dropdown-btn {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: #f3f4f6;
            border-radius: 0.75rem;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease-out;
        }

        .custom-dropdown-btn:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }

        .custom-dropdown-list {
            position: absolute;
            z-index: 20;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 0.25rem;
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e5e7eb;
            display: none;
        }

        .custom-dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            cursor: pointer;
        }

        .custom-dropdown-item:hover {
            background-color: #f3f4f6;
        }

        .custom-dropdown-list.active {
            display: block;
        }

        /* New Styles for phone number dropdown */
        .flag-icon {
            width: 24px;
            height: 16px;
        }

        .dropdown-button-text {
            margin-left: 8px;
        }

        .hidden-mobile {
            display: none;
        }

        @media (min-width: 768px) {
            #content-container {
                flex-direction: row;
                width: 100%;
            }

            #container-1 {
                padding: 8rem;
            }

            .hidden-mobile {
                display: block;
            }
        }

        #otp-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        /* OTP input slots */
        #otp-container input[type="text"] {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border: 1px solid #d1d5db;
            background-color: #c2c6d95b;
            border-radius: 10px;
            outline: none;
            transition: all 0.2s ease;
        }

        /* Focused state */
        #otp-container input[type="text"]:focus {
            border-color: #0077FF;
            /* highlight color */
            box-shadow: 0 0 0 3px rgba(0, 119, 255, 0.2);
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <div id="header-container"
        class="items-center justify-between p-3 lg:px-8 inset-x-0 top-0 z-50 w-full hidden lg:flex lg:gap-x-12">
        <div id="logo-container" class=" mt-3 ml-6">
            <a href="#" class="-m-1.5 p-1.5">
                <span class="sr-only"></span>
                <img src="{{asset('/images/finnoys-text-logo-light.svg')}}" alt="" class="h-20 w-auto">
            </a>
        </div>

        <div id="stepper-container" class="w-full max-w-full px-8 py-8 md:p-8">
            <div class="relative flex justify-between items-center">
                <div class="timeline-line"></div>
                <div id="progress-line" class="progress-line"></div>

                <div id="step-1-indicator" class="relative flex flex-col items-center step-container current-step">
                    <div
                        class="timeline-icon flex items-center justify-center w-10 h-10 bg-blue-600 rounded-full transition-colors duration-300">
                        <svg id="step-1-icon" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg> <span id="step-1-number" class="text-white text-sm absolute">1</span>
                    </div>
                    <div class="mt-4 text-center step-text hidden sm:block">
                        <h3 class="font-sans font-medium leading-tight text-gray-800 text-sm">Basic Details</h3>
                        <p class="font-sans font-normal text-xs text-gray-500">Step 1</p>
                    </div>
                </div>

                <div id="step-2-indicator" class="relative flex flex-col items-center step-container pending-step">
                    <div
                        class="timeline-icon flex items-center justify-center w-10 h-10 bg-gray-400 rounded-full transition-colors duration-300">
                        <svg id="step-2-icon" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 19">
                            <path
                                d="M18 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2Zm-1.8 4.7-5.4 3.9a2 2 0 0 1-2.4 0l-5.4-3.9A2 2 0 0 1 1.6 3h16.8a2 2 0 0 1 0 1.7Z" />
                        </svg>
                        <span id="step-2-number" class="text-white text-sm absolute">2</span>
                    </div>
                    <div class="mt-4 text-center step-text hidden sm:block">
                        <h3 class="font-medium leading-tight text-gray-800 text-sm">Email Verification</h3>
                        <p class="text-xs text-gray-500">Step 2</p>
                    </div>
                </div>

                <div id="step-3-indicator" class="relative flex flex-col items-center step-container pending-step">
                    <div
                        class="timeline-icon flex items-center justify-center w-10 h-10 bg-gray-400 rounded-full transition-colors duration-300">
                        <svg id="step-3-icon" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 18 20">
                            <path
                                d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z" />
                        </svg>
                        <span id="step-3-number" class="text-white text-sm absolute">3</span>
                    </div>
                    <div class="mt-4 text-center step-text hidden sm:block">
                        <h3 class="font-medium leading-tight text-gray-800 text-sm">Account Setup</h3>
                        <p class="text-xs text-gray-500">Step 3</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div id="content-container">
        <div id="container-1" class="p-3 md:p-28">
            <div id="inner-container-1" class="w-full pl-14">
                <span id="form-status"
                    class="bg-blue-100 text-blue-500 text-xs font-medium me-2 px-2.5 py-0.5 rounded-2xl bg-blue-300 text-blue-500">Form
                    Status</span>
                <p id="step-label" class="font-bold text-base mt-7">Step 1</p>
                <h1 id="step-head">Basic Details</h1>
                <p id="step-desc">First things first, let us know your the following information for identification and
                    account verification purposes.</p>
            </div>
        </div>
        <div id="container-2" class="w-full p-20">
            <!-- START: Copy from here -->
            <form method="POST" action="{{ route('register.client') }}">
                @csrf

                <!-- ====================================================== -->
                <!--                       STEP 1                           -->
                <!-- ====================================================== -->
                <div id="step-1" class="step-content">
                    <h1 id="form-head" class="mb-4 w-full text-center font-sans font-medium italic">Tell Us About You</h1>
                    
                    <div class="space-y-4">
                        <div id="name-layer" class="w-full flex flex-col sm:flex-row justify-between sm:space-x-3">
                            <div class="input-container flex-1">
                                <input type="text" id="input-fname" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="first_name" value="{{ old('first_name') }}" required>
                                <label for="input-fname">First Name</label>
                            </div>
                            @error('first_name')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                            <div class="input-container flex-1">
                                <input type="text" id="input-lname" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="last_name" required>
                                <label for="input-lname">Last Name</label>
                            </div>
                            <div class="input-container w-[7rem] max-sm:w-full">
                                <input type="text" id="input-mname" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="middle_initial">
                                <label for="input-mname">M.I.</label>
                            </div>
                        </div>

                        <!-- Birthdate -->
                        <div class="input-container relative w-full">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <i class="fas fa-calendar-day absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            </div>
                            <input datepicker id="datepicker" name="birthdate" datepicker-format="mm-dd-yyyy" type="text"
                                class="input-field w-full text-sm pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                placeholder="mm - dd - yyyy" required>
                            <label for="datepicker">Birthdate</label>
                        </div>

                        <!-- Phone Number -->
                        <div class="input-container w-full flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-3">
                            <div class="custom-dropdown-container w-full sm:w-36">
                                <button id="dropdown-btn" type="button" class="custom-dropdown-btn">
                                    <img id="selected-flag" src="{{asset('/images/icons/finland-flag.svg')}}" alt="Finland Flag" class="h-4 w-auto mr-2">
                                    <span id="selected-code" class="text-sm">+358</span>
                                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </span>
                                </button>
                                <div id="dropdown-list" class="custom-dropdown-list">
                                    <div class="custom-dropdown-item" data-value="+358" data-flag="{{asset('/images/icons/finland-flag.svg')}}">
                                        <img src="{{asset('/images/icons/finland-flag.svg')}}" alt="Finland Flag" class="h-4 w-auto mr-2">
                                        <span class="text-sm">+358 Finland</span>
                                    </div>
                                    <div class="custom-dropdown-item" data-value="+46" data-flag="{{asset('/images/icons/sweden-flag.svg')}}">
                                        <img src="{{asset('/images/icons/sweden-flag.svg')}}" alt="Sweden Flag" class="h-4 w-auto mr-2">
                                        <span class="text-sm">+46 Sweden</span>
                                    </div>
                                    <div class="custom-dropdown-item" data-value="+47" data-flag="{{asset('/images/icons/norway-flag.svg')}}">
                                        <img src="{{asset('/images/icons/norway-flag.svg')}}" alt="Norway Flag" class="h-4 w-auto mr-2">
                                        <span class="text-sm">+47 Norway</span>
                                    </div>
                                    <div class="custom-dropdown-item" data-value="+45" data-flag="{{asset('/images/icons/denmark-flag.svg')}}">
                                        <img src="{{asset('/images/icons/denmark-flag.svg')}}" alt="Denmark Flag" class="h-4 w-auto mr-2">
                                        <span class="text-sm">+45 Denmark</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 input-container w-full">
                                <input type="tel" id="input-phone" placeholder="40 1234567"
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="phone_number" required>
                                <label for="input-phone">Phone Number</label>
                            </div>
                        </div>

                        <div class="input-container w-full relative">
                            <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="email" id="input-email" placeholder=" "
                                class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                name="email" required>
                            <label for="input-email">Email Address</label>
                        </div>

                        <!-- Finnish Address Fields -->
                        <div class="input-container w-full relative">
                            <i class="fas fa-map-marker-alt absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="text" id="input-street" placeholder=" "
                                class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                name="street_address" required>
                            <label for="input-street">Street Address</label>
                        </div>

                        <div class="w-full flex flex-col sm:flex-row justify-between sm:space-x-3">
                            <div class="input-container flex-1">
                                <i class="fas fa-mail-bulk absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                <input type="text" id="input-postal" placeholder=" " maxlength="5"
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="postal_code" required>
                                <label for="input-postal">Postal Code</label>
                            </div>
                            <div class="input-container flex-1">
                                <i class="fas fa-city absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                <input type="text" id="input-city" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="city" required>
                                <label for="input-city">City</label>
                            </div>
                        </div>

                        <!-- District with Autocomplete -->
                        <div class="input-container w-full relative" id="district-container">
                            <i class="fas fa-building absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500 z-10"></i>
                            <input type="text" id="input-district" placeholder=" " autocomplete="off"
                                class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                name="district" required>
                            <label for="input-district">District (Kaupunginosa)</label>

                            <!-- District Suggestions Dropdown -->
                            <div id="district-suggestions" class="hidden absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            </div>
                        </div>

                        <div id="buttons-container" class="flex justify-center gap-4 mt-6">
                            <button id="back1-btn" type="button" class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm">Cancel</button>
                            <button type="button" id="next-1" class="w-full px-20 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Confirm</button>
                        </div>
                    </div>
                </div>

                <!-- ====================================================== -->
                <!--                       STEP 2                           -->
                <!-- ====================================================== -->
                <div id="step-2" class="step-content hidden">
                    <div class="space-y-4">
                        <h1 id="form-head" class="mb-4">OTP Verification</h1>
                        <div>
                            <p class="text-sm text-center"> To verify your provided email address, please enter the one-time pin (OTP) that we sent to <span id="email_address" class="font-bold text-blue-600">your.email@example.com</span> </p>
                        </div>

                        <div>
                            <div id="otp-container">
                                <input id="otp1" type="text" maxlength="1" pattern="\d" inputmode="numeric">
                                <input id="otp2" type="text" maxlength="1" pattern="\d" inputmode="numeric">
                                <input id="otp3" type="text" maxlength="1" pattern="\d" inputmode="numeric">
                                <input id="otp4" type="text" maxlength="1" pattern="\d" inputmode="numeric">
                                <input id="otp5" type="text" maxlength="1" pattern="\d" inputmode="numeric">
                                <input id="otp6" type="text" maxlength="1" pattern="\d" inputmode="numeric">
                            </div>
                            <div class="flex justify-center gap-4 mt-6">
                                <button id="back2-btn" type="button" class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm">Back</button>
                                <button type="button" id="next-2" class="w-full px-20 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Verify OTP</button>
                            </div>
                            <p id="resend-label" class="text-sm text-center w-full mt-4">Didn't receive an OTP?
                                <span><a href="#" class="text-blue-600 font-bold">Resend Code</a></span> <span id="timer"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ====================================================== -->
                <!--                       STEP 3                           -->
                <!-- ====================================================== -->
                <div id="step-3" class="step-content hidden">
                    <div class="w-full space-y-4">
                        <h1 id="form-head" class="mb-4">Set up your security questions</h1>
                        <div class="input-container w-full max-w-md mx-auto relative">
                            <i class="fas fa-question-circle absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <button type="button" id="dropdown-secques1" class="security-question-btn1 select-field w-full pl-12 pr-4 py-4 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm text-gray-400 text-left font-normal">
                                Select a security question
                            </button>
                            <div class="security-question-dropdown z-10 absolute left-0 right-0 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-full">
                                <ul class="py-2 text-sm text-gray-700">
                                    <li><a href="#" class="text-left block px-4 py-2 hover:bg-blue-200" data-value="pet_name">What is the name of your first pet?</a></li>
                                    <li><a href="#" class="text-left block px-4 py-2 hover:bg-blue-200" data-value="birth_city">In what city were you born?</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="input-container w-full max-w-md mx-auto">
                            <i class="fas fa-comment absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="text" id="input-secans-1" placeholder=" " class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700" name="security_answer_1" required>
                            <label for="input-secans-1">Security Question Answer</label>
                        </div>
                        @error('security_answer_1')
                            <p class="text-red-500 text-xs text-center max-w-md mx-auto">{{ $message }}</p>
                        @enderror

                        <div class="input-container w-full max-w-md mx-auto relative">
                            <i class="fas fa-question-circle absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <button type="button" id="dropdown-secques2" class="security-question-btn2 select-field w-full pl-12 pr-4 py-4 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm text-gray-400 text-left font-normal">
                                Select a security question
                            </button>
                            <div class="security-question-dropdown z-10 absolute left-0 right-0 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-full">
                                <ul class="py-2 text-sm text-gray-700">
                                    <li><a href="#" class="text-left block px-4 py-2 hover:bg-blue-200" data-value="best_friend">What is the name of your best friend?</a></li>
                                    <li><a href="#" class="text-left block px-4 py-2 hover:bg-blue-200" data-value="teacher_name">Who was your favorite teacher?</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="input-container w-full max-w-md mx-auto">
                            <i class="fas fa-comment absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="text" id="input-secans-2" placeholder=" " class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700" name="security_answer_2" required>
                            <label for="input-secans-2">Security Question Answer</label>
                        </div>
                        @error('security_answer_2')
                            <p class="text-red-500 text-xs text-center max-w-md mx-auto">{{ $message }}</p>
                        @enderror
                        
                        <h1 id="form-head" class="mb-4 p-6">Let's Get Your Account Ready</h1>
                        <div class="input-container w-full max-w-md mx-auto">
                            <i class="fas fa-id-card absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="text" id="input-username" placeholder=" " class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700" name="username" value="{{ old('username') }}" required>
                            <label for="input-username">Username</label>
                        </div>
                        @error('username')
                            <p class="text-red-500 text-xs text-center max-w-md mx-auto">{{ $message }}</p>
                        @enderror

                        <div class="input-container w-full max-w-md mx-auto relative">
                            <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="password" id="input-new-password" placeholder=" " class="input-field w-full pr-12 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700" name="password" autocomplete="new-password" required>
                            <label for="input-new-password">New Password</label>
                            <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer" id="togglePassword"></i>
                        </div>
                        <div class="input-container w-full max-w-md mx-auto relative">
                            <i class="fas fa-square-check absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="password" id="input-confirm-password" placeholder=" " class="input-field w-full pr-12 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700" name="password_confirmation" autocomplete="new-password" required>
                            <label for="input-confirm-password">Confirm New Password</label>
                            <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer" id="toggleConfirmPassword"></i>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6 w-full pt-4">
                            <button id="back3-btn" type="button" class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm">Back</button>
                            <button type="submit" id="next-3" class="w-full sm:w-auto px-20 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center">Create Account</button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- END: Copy until here -->
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ===== CLOUD-BASED ADDRESS VALIDATION =====
            let addressTimeout, postalTimeout;
            const NOMINATIM_API = 'https://nominatim.openstreetmap.org/search';
            const REVERSE_API = 'https://nominatim.openstreetmap.org/reverse';

            const streetInput = document.getElementById('input-street');
            const postalInput = document.getElementById('input-postal');
            const cityInput = document.getElementById('input-city');
            const districtInput = document.getElementById('input-district');
            const districtSuggestions = document.getElementById('district-suggestions');

            // Create street address suggestions dropdown
            const streetSuggestionsDiv = document.createElement('div');
            streetSuggestionsDiv.id = 'street-suggestions';
            streetSuggestionsDiv.className = 'hidden absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto';
            streetInput.parentElement.appendChild(streetSuggestionsDiv);

            // === 1. STREET ADDRESS AUTOCOMPLETE ===
            async function fetchStreetSuggestions(query) {
                if (!query || query.length < 3) return [];

                try {
                    const response = await fetch(
                        `${NOMINATIM_API}?format=json&country=Finland&addressdetails=1&limit=5&q=${encodeURIComponent(query)}`
                    );
                    const data = await response.json();
                    return data;
                } catch (error) {
                    console.error('Error fetching street suggestions:', error);
                    return [];
                }
            }

            function showStreetSuggestions(suggestions) {
                if (suggestions.length === 0) {
                    streetSuggestionsDiv.classList.add('hidden');
                    return;
                }

                streetSuggestionsDiv.innerHTML = suggestions.map(item => {
                    const address = item.address || {};
                    const road = address.road || '';
                    const houseNumber = address.house_number || '';
                    const suburb = address.suburb || address.neighbourhood || '';
                    const city = address.city || address.town || address.municipality || '';
                    const postcode = address.postcode || '';

                    const displayText = `${road} ${houseNumber}`.trim();
                    const subText = [suburb, city, postcode].filter(x => x).join(', ');

                    return `
                        <div class="street-item px-4 py-2 hover:bg-blue-100 cursor-pointer"
                             data-road="${road}"
                             data-house="${houseNumber}"
                             data-suburb="${suburb}"
                             data-city="${city}"
                             data-postcode="${postcode}">
                            <div class="font-semibold text-gray-900">${displayText}</div>
                            <div class="text-sm text-gray-600">${subText}</div>
                        </div>
                    `;
                }).join('');

                streetSuggestionsDiv.classList.remove('hidden');

                // Add click handlers
                document.querySelectorAll('.street-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const road = item.getAttribute('data-road');
                        const house = item.getAttribute('data-house');
                        const suburb = item.getAttribute('data-suburb');
                        const city = item.getAttribute('data-city');
                        const postcode = item.getAttribute('data-postcode');

                        streetInput.value = `${road} ${house}`.trim();

                        // Auto-fill other fields
                        if (city && !cityInput.value) {
                            cityInput.value = city;
                            cityInput.dispatchEvent(new Event('input'));
                        }
                        if (postcode && !postalInput.value) {
                            postalInput.value = postcode;
                            postalInput.dispatchEvent(new Event('input'));
                        }
                        if (suburb && !districtInput.value) {
                            districtInput.value = suburb;
                            districtInput.dispatchEvent(new Event('input'));
                        }

                        streetSuggestionsDiv.classList.add('hidden');
                        checkFormCompletion();
                    });
                });
            }

            if (streetInput) {
                streetInput.addEventListener('input', (e) => {
                    clearTimeout(addressTimeout);
                    addressTimeout = setTimeout(async () => {
                        const query = e.target.value;
                        if (query.length >= 3) {
                            const suggestions = await fetchStreetSuggestions(query);
                            showStreetSuggestions(suggestions);
                        } else {
                            streetSuggestionsDiv.classList.add('hidden');
                        }
                    }, 500); // Debounce 500ms
                });

                streetInput.addEventListener('focus', () => {
                    if (streetInput.value.length >= 3) {
                        fetchStreetSuggestions(streetInput.value).then(showStreetSuggestions);
                    }
                });
            }

            // === 2. POSTAL CODE TO CITY LOOKUP ===
            async function lookupPostalCode(postcode) {
                if (!postcode || postcode.length !== 5) return null;

                try {
                    const response = await fetch(
                        `${NOMINATIM_API}?format=json&country=Finland&postalcode=${postcode}&addressdetails=1&limit=1`
                    );
                    const data = await response.json();
                    return data.length > 0 ? data[0] : null;
                } catch (error) {
                    console.error('Error looking up postal code:', error);
                    return null;
                }
            }

            if (postalInput) {
                postalInput.addEventListener('input', (e) => {
                    clearTimeout(postalTimeout);
                    const postcode = e.target.value.trim();

                    if (postcode.length === 5) {
                        postalTimeout = setTimeout(async () => {
                            const result = await lookupPostalCode(postcode);
                            if (result && result.address) {
                                const city = result.address.city || result.address.town || result.address.municipality || '';
                                const suburb = result.address.suburb || result.address.neighbourhood || '';

                                if (city && !cityInput.value) {
                                    cityInput.value = city;
                                    cityInput.dispatchEvent(new Event('input'));
                                }
                                if (suburb && !districtInput.value) {
                                    districtInput.value = suburb;
                                    districtInput.dispatchEvent(new Event('input'));
                                }

                                checkFormCompletion();
                            }
                        }, 500);
                    }
                });
            }

            // === 3. CITY TO POSTAL CODE LOOKUP ===
            async function lookupCity(cityName) {
                if (!cityName || cityName.length < 3) return [];

                try {
                    const response = await fetch(
                        `${NOMINATIM_API}?format=json&country=Finland&city=${encodeURIComponent(cityName)}&addressdetails=1&limit=5`
                    );
                    const data = await response.json();
                    return data;
                } catch (error) {
                    console.error('Error looking up city:', error);
                    return [];
                }
            }

            if (cityInput) {
                let cityTimeout;
                cityInput.addEventListener('input', (e) => {
                    clearTimeout(cityTimeout);
                    const city = e.target.value.trim();

                    if (city.length >= 3 && !postalInput.value) {
                        cityTimeout = setTimeout(async () => {
                            const results = await lookupCity(city);
                            if (results.length > 0 && results[0].address && results[0].address.postcode) {
                                postalInput.value = results[0].address.postcode;
                                postalInput.dispatchEvent(new Event('input'));
                                checkFormCompletion();
                            }
                        }, 800);
                    }
                });
            }

            // === 4. DISTRICT AUTOCOMPLETE (ENHANCED) ===
            async function fetchDistrictsForCity(cityName) {
                if (!cityName) return [];

                try {
                    const response = await fetch(
                        `${NOMINATIM_API}?format=json&country=Finland&city=${encodeURIComponent(cityName)}&addressdetails=1&limit=20`
                    );
                    const data = await response.json();

                    const districts = new Set();
                    data.forEach(item => {
                        if (item.address) {
                            if (item.address.suburb) districts.add(item.address.suburb);
                            if (item.address.neighbourhood) districts.add(item.address.neighbourhood);
                            if (item.address.quarter) districts.add(item.address.quarter);
                        }
                    });

                    return Array.from(districts).sort();
                } catch (error) {
                    console.error('Error fetching districts:', error);
                    return [];
                }
            }

            let cachedDistricts = [];

            async function showDistrictSuggestions() {
                const query = districtInput.value.toLowerCase();
                const city = cityInput.value.trim();

                // If city is provided and we don't have cached districts, fetch them
                if (city && cachedDistricts.length === 0) {
                    cachedDistricts = await fetchDistrictsForCity(city);
                }

                // Filter districts based on query
                const filtered = cachedDistricts.filter(district =>
                    district.toLowerCase().includes(query)
                );

                if (filtered.length === 0) {
                    districtSuggestions.classList.add('hidden');
                    return;
                }

                districtSuggestions.innerHTML = filtered.map(district => `
                    <div class="district-item px-4 py-2 hover:bg-blue-100 cursor-pointer text-gray-900" data-district="${district}">
                        ${district}
                    </div>
                `).join('');

                districtSuggestions.classList.remove('hidden');

                // Add click handlers
                document.querySelectorAll('.district-item').forEach(item => {
                    item.addEventListener('click', () => {
                        districtInput.value = item.getAttribute('data-district');
                        districtSuggestions.classList.add('hidden');
                        checkFormCompletion();
                    });
                });
            }

            if (districtInput) {
                districtInput.addEventListener('input', showDistrictSuggestions);
                districtInput.addEventListener('focus', showDistrictSuggestions);

                // Close suggestions when clicking outside
                document.addEventListener('click', (e) => {
                    if (!districtInput.contains(e.target) && !districtSuggestions.contains(e.target)) {
                        districtSuggestions.classList.add('hidden');
                    }
                    if (!streetInput.contains(e.target) && !streetSuggestionsDiv.contains(e.target)) {
                        streetSuggestionsDiv.classList.add('hidden');
                    }
                });
            }

            // Refresh district cache when city changes
            if (cityInput) {
                cityInput.addEventListener('change', () => {
                    cachedDistricts = [];
                });
            }

            // STEP 1 SCRIPT
            const dropdownBtn = document.getElementById('dropdown-btn');
            const dropdownList = document.getElementById('dropdown-list');
            const selectedFlag = document.getElementById('selected-flag');
            const selectedCode = document.getElementById('selected-code');
            const birthdateInput = document.getElementById('datepicker');

            if (birthdateInput) {
                birthdateInput.addEventListener('focus', () => {
                    birthdateInput.type = 'text';
                });

                birthdateInput.addEventListener('blur', () => {
                    if (!birthdateInput.value) {
                        birthdateInput.type = 'text';
                    }
                });
            }

            // FIXED: Initialize completion flags
            let isStep1Completed = false;
            let isStep2Completed = false;
            let isStep3Completed = false;

            const originalStepIcons = {};
            document.querySelectorAll('.step-container').forEach(indicator => {
                const stepNumber = indicator.id.split('-')[1];
                // Store the full original HTML content of the icon container
                originalStepIcons[stepNumber] = indicator.querySelector('.timeline-icon').innerHTML;
            });

            // Back button handlers (resetting completion status when going back)
            document.getElementById('back3-btn').addEventListener('click', (e) => {
                e.preventDefault();
                if (currentStep > 1) { // Ensure we don't go below step 1
                    currentStep = 2;
                    isStep3Completed = false;
                    updateStepper(); // Only need to reset isStep3Completed as isStep2Completed reset is not needed when moving from 3 to 2
                }
            });
            document.getElementById('back2-btn').addEventListener('click', (e) => {
                e.preventDefault();
                if (currentStep >= 1) {
                    currentStep = 1;
                    isStep1Completed = false;
                    isStep2Completed = false; // Reset the completed status of the step being left
                    updateStepper();
                }
            });
            document.getElementById('back1-btn').addEventListener('click', (e) => {
                e.preventDefault();
                window.location.href = '/login';

            });
            dropdownBtn.addEventListener('click', () => {
                dropdownList.classList.toggle('active');
            });
            dropdownList.querySelectorAll('.custom-dropdown-item').forEach(item => {
                item.addEventListener('click', () => {
                    const value = item.getAttribute('data-value');
                    const flagSrc = item.getAttribute('data-flag');

                    selectedFlag.src = flagSrc;
                    selectedCode.textContent = value;
                    dropdownList.classList.remove('active');
                    checkFormCompletion(); // Trigger status check on phone code change
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (event) => {
                if (!dropdownBtn.contains(event.target) && !dropdownList.contains(event.target)) {
                    dropdownList.classList.remove('active');
                }
            });

            // Birthdate input behavior (Existing code remains)
            birthdateInput.addEventListener('focus', () => {
                birthdateInput.type = 'text';
            });

            birthdateInput.addEventListener('blur', () => {
                if (!birthdateInput.value) {
                    birthdateInput.type = 'text';
                }
            });

            // STEP 3 SCRIPT
            const dropdownButtons = document.querySelectorAll('.security-question-btn1, .security-question-btn2'); const allDropdownMenus = document.querySelectorAll('.dropdown-menu'); // Get all menus
            const allSecurityDropdownMenus = document.querySelectorAll('.security-question-dropdown');

            dropdownButtons.forEach(button => {
                // The dropdown menu is the element immediately next to the button
                const dropdownMenu = button.nextElementSibling;
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                // Use the user's requested array naming
                hiddenInput.name = 'security_question[]';
                button.parentNode.appendChild(hiddenInput);

                // Toggle dropdown
                button.addEventListener('click', (e) => {
                    e.stopPropagation();

                    // FIX: Close all other security question dropdowns
                    allSecurityDropdownMenus.forEach(menu => {
                        // Close if the menu is NOT the one belonging to the current button
                        if (menu !== dropdownMenu) {
                            menu.classList.add('hidden');
                        }
                    });

                    // Open/close the current dropdown
                    dropdownMenu.classList.toggle('hidden');
                });

                // Handle selection
                dropdownMenu.querySelectorAll('a').forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        button.textContent = item.textContent.trim();
                        // Update the specific hidden input for this question
                        hiddenInput.value = item.getAttribute('data-value');
                        button.classList.remove('text-gray-400');
                        dropdownMenu.classList.add('hidden');
                        checkFormCompletion(); // Trigger status check
                    });
                });

                // Close when clicking outside
                document.addEventListener('click', (e) => {
                    if (!button.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.add('hidden');
                    }
                });
            });

            let currentStep = 1;
            const steps = document.querySelectorAll('.step-content');
            const stepIndicators = {
                1: document.getElementById('step-1-indicator'),
                2: document.getElementById('step-2-indicator'),
                3: document.getElementById('step-3-indicator')
            };
            const progressLine = document.getElementById('progress-line');
            const stepLabel = document.getElementById('step-label');
            const stepHeader = document.getElementById('step-head');
            const stepDesc = document.getElementById('step-desc');
            const totalSteps = Object.keys(stepIndicators).length;
            const stepNames = {
                1: {
                    name: 'Basic Details',
                    desc: 'First things first, let us know your the following information for identification and account verification purposes.'
                },
                2: {
                    name: 'Email Authentication',
                    desc: 'Please authenticate your email to protect your account and confirm your identity. Verifying your email ensures secure access and uninterrupted service.'
                },
                3: {
                    name: 'Account Setup',
                    desc: 'Just a few quick steps to personalize your experience and make sure everything runs smoothly.'

                }
            };

            const checkIconSVG = `<svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5" /></svg>`;

            function updateStepper() {
                stepLabel.textContent = `Step ${currentStep}`;
                stepHeader.textContent = stepNames[currentStep].name;
                stepDesc.textContent = stepNames[currentStep].desc;

                // Calculate progress line width
                const progressSegments = totalSteps - 1;
                const progressPercentage = (currentStep - 1) / progressSegments * 100;

                // Adjust the width of the progress line
                if (progressLine) {
                    // This logic is mostly correct for visual positioning
                    if (currentStep === 1) {
                        progressLine.style.width = '0%';
                    } else if (currentStep === totalSteps) {
                        // Full width is 100% - 80px margin
                        progressLine.style.width = `calc(100% - 80px)`;
                    } else {
                        // Intermediate steps: total width is (stepIndex - 1) / (totalSteps - 1) of the full line width
                        progressLine.style.width = `calc(${progressPercentage}% - 40px)`;
                    }

                }

                // Update steps' appearance
                for (let i = 1; i <= totalSteps; i++) {
                    const indicator = stepIndicators[i];
                    const iconContainer = indicator.querySelector('.timeline-icon');
                    const isCompleted = (i === 1 && isStep1Completed) || (i === 2 && isStep2Completed) || (i === 3 && isStep3Completed);

                    // Reset classes
                    indicator.classList.remove('current-step', 'completed-step', 'pending-step');
                    iconContainer.classList.remove('bg-blue-300', 'bg-green-500', 'bg-blue-300');

                    if (isCompleted) {
                        indicator.classList.add('completed-step');
                        iconContainer.classList.add('bg-green-500'); // Use green for completed
                        iconContainer.innerHTML = checkIconSVG;
                    } else if (i === currentStep) {
                        indicator.classList.add('current-step');
                        iconContainer.classList.add('bg-blue-600'); // Use blue for current
                        iconContainer.innerHTML = originalStepIcons[i];
                        const currentNumberSpan = iconContainer.querySelector(`#step-${i}-number`);
                        const currentIconSVG = iconContainer.querySelector(`#step-${i}-icon`);
                        if (currentIconSVG) currentIconSVG.classList.remove('hidden'); // Show the SVG icon
                        if (currentNumberSpan) currentNumberSpan.classList.add('hidden'); // Hide the number
                    } else {
                        indicator.classList.add('pending-step');
                        iconContainer.classList.add('bg-blue-300'); // Use lighter blue for pending (matching the style)
                        iconContainer.innerHTML = originalStepIcons[i];
                        const pendingNumberSpan = iconContainer.querySelector(`#step-${i}-number`);
                        const pendingIconSVG = iconContainer.querySelector(`#step-${i}-icon`);
                        if (pendingIconSVG) pendingIconSVG.classList.add('hidden'); // Hide the SVG icon
                        if (pendingNumberSpan) pendingNumberSpan.classList.remove('hidden'); // Show the number
                    }
                }

                // Show/hide content for each step
                steps.forEach((step, index) => {
                    if (index + 1 === currentStep) {
                        step.classList.remove('hidden');
                    } else {
                        step.classList.add('hidden');
                    }
                });
                // Run completion check after updating stepper visuals
                checkFormCompletion();
            }

            function checkFormCompletion() {
                // --- 0. Status Element and Global Variables (Must be defined first) ---
                const formStatus = document.getElementById('form-status'); // Element showing status
                // Use the currently selected code from the dropdown
                const selectedCountryCode = selectedCode ? selectedCode.textContent.replace('+', '') : '63';
                if (!formStatus) return;

                // --- Form 1 Inputs ---
                const fName = document.getElementById('input-fname');
                const lName = document.getElementById('input-lname');
                const bDate = document.getElementById('datepicker');
                const phone = document.getElementById('input-phone');
                const email = document.getElementById('input-email');
                const street = document.getElementById('input-street');
                const postal = document.getElementById('input-postal');
                const city = document.getElementById('input-city');
                const district = document.getElementById('input-district');

                // --- Form 2 Inputs ---
                const otp1 = document.getElementById('otp1');
                const otp2 = document.getElementById('otp2');
                const otp3 = document.getElementById('otp3');
                const otp4 = document.getElementById('otp4');
                const otp5 = document.getElementById('otp5');
                const otp6 = document.getElementById('otp6');

                // --- Form 3 Inputs ---
                const secques1Btn = document.getElementById('dropdown-secques1');
                const secques2Btn = document.getElementById('dropdown-secques2');
                // CHANGE: Select all elements with name 'security_question[]'
                const hiddenSecQuestions = document.querySelectorAll('input[name="security_question[]"]');
                const secans1 = document.getElementById('input-secans-1');
                const secans2 = document.getElementById('input-secans-2');
                const username = document.getElementById('input-username');
                const password = document.getElementById('input-new-password');
                const confirmPassword = document.getElementById('input-confirm-password');

                const USERNAME_LENGTH = 8;

                // --- 1. INDIVIDUAL FORM 1 CHECKS ---
                const isFNameComplete = fName && fName.value.trim() !== "";
                const isLNameComplete = lName && lName.value.trim() !== "";
                const isBDateComplete = bDate && bDate.value.trim() !== ""; // Check if birthdate is entered
                const isEmailValid = email && email.value.trim() !== "" && email.value.includes('@') && email.value.includes('.');
                const isStreetComplete = street && street.value.trim() !== "";
                const isPostalComplete = postal && postal.value.trim() !== "" && postal.value.length === 5;
                const isCityComplete = city && city.value.trim() !== "";
                const isDistrictComplete = district && district.value.trim() !== "";

                let isPhoneValid = false;
                if (phone && phone.value.trim() !== "") {
                    const cleanNumber = phone.value.replace(/\D/g, '');
                    if (selectedCountryCode === '63') { // Philippines
                        // PH local number is typically 10 digits (e.g., 9xxxxxxxxx)
                        isPhoneValid = cleanNumber.length === 10;
                    } else if (selectedCountryCode === '358') { // Finland
                        // FI numbers vary, checking for a reasonable length for a mobile number (7 to 10 digits after code)
                        isPhoneValid = cleanNumber.length >= 7 && cleanNumber.length <= 10;
                    } else {
                        isPhoneValid = cleanNumber.length >= 7; // Generic check
                    }
                }

                // --- 2. INDIVIDUAL FORM 2 CHECKS (OTP) ---
                const isOTPComplete =
                    otp1 && otp1.value.length === 1 &&
                    otp2 && otp2.value.length === 1 &&
                    otp3 && otp3.value.length === 1 &&
                    otp4 && otp4.value.length === 1 &&
                    otp5 && otp5.value.length === 1 &&
                    otp6 && otp6.value.length === 1;

                // --- 3. INDIVIDUAL FORM 3 CHECKS ---
                // CHANGE: Check if both questions are selected (assuming two were created)
                const isSecQues1Selected = hiddenSecQuestions.length > 0 && hiddenSecQuestions[0].value !== "";
                const isSecQues2Selected = hiddenSecQuestions.length > 1 && hiddenSecQuestions[1].value !== "";
                // Simplified validity for demonstration, proper password validation (e.g., regex) should be implemented
                const isPasswordValid = password && password.value.length >= 8;
                const isConfirmed = confirmPassword && password && (password.value === confirmPassword.value && password.value.length > 0);
                const isUsernameComplete = username && username.value.trim() !== "" && username.value.trim().length >= 4; // Changed to >=4 for a more typical minimum
                const isSecAns1Complete = secans1 && secans1.value.trim() !== "";
                const isSecAns2Complete = secans2 && secans2.value.trim() !== "";

                // --- 4. AGGREGATE CHECKS (Used in the event listeners) ---
                const isForm1Complete = isFNameComplete && isLNameComplete && isBDateComplete && isEmailValid && isPhoneValid && isStreetComplete && isPostalComplete && isCityComplete && isDistrictComplete;

                const isForm3Complete = isPasswordValid &&
                    isConfirmed &&
                    isUsernameComplete &&
                    isSecAns1Complete &&
                    isSecAns2Complete &&
                    isSecQues1Selected && // Check if first security question is selected
                    isSecQues2Selected; // Check if second security question is selected


                // --- 5. Status Element Update (Existing code remains) ---
                formStatus.classList.remove(
                    'bg-green-100', 'text-green-500',
                    'bg-orange-100', 'text-orange-500',
                    'bg-red-100', 'text-red-500'
                );

                // Check if ANY field in any form has data (for overall "In Progress" status)
                const isAnyFieldPopulated = (fName && fName.value.length > 0) || (lName && lName.value.length > 0) || (bDate && bDate.value.length > 0) || (email && email.value.length > 0) ||
                    (otp1 && otp1.value.length > 0) || (otp2 && otp2.value.length > 0) || // Check individual Form 2
                    (username && username.value.length > 0) || (password && password.value.length > 0) || (confirmPassword && confirmPassword.value.length > 0);


                // Option A: If all three steps are complete
                if (isStep1Completed && isStep2Completed && isStep3Completed) {
                    formStatus.textContent = `Completed`;
                    formStatus.classList.add('bg-green-100', 'text-green-500');
                }
                // Option B: If the form has been started (at least one field entered)
                else if (isAnyFieldPopulated || isStep1Completed || isStep2Completed) {
                    formStatus.textContent = `In Progress`;
                    formStatus.classList.add('bg-orange-100', 'text-orange-500');
                }
                // Option C: If the form hasn't been touched
                else {
                    formStatus.textContent = `Incomplete`;
                    formStatus.classList.add('bg-red-100', 'text-red-500');
                }

                // Return the aggregate status for the event handlers
                return {
                    isForm1Complete,
                    isOTPComplete,
                    isForm3Complete
                };
            }

            function setupOTPAutoAdvance() {
                // Array creation is correct, ensures only existing elements are included
                const otpFields = [
                    document.getElementById('otp1'),
                    document.getElementById('otp2'),
                    document.getElementById('otp3'),
                    document.getElementById('otp4'),
                    document.getElementById('otp5'),
                    document.getElementById('otp6')
                ].filter(field => field);

                // Loop through fields to attach the listener
                otpFields.forEach((currentField, index) => {
                    currentField.addEventListener('input', () => {

                        // CRITICAL CHECK: Ensure the field has exactly one character
                        if (currentField.value.length === 1) {
                            const nextField = otpFields[index + 1];

                            // If the next field exists, move focus
                            if (nextField) {
                                nextField.focus();
                            } else {
                                // If this is the last field, remove focus
                                currentField.blur();
                            }
                        } else if (currentField.value.length > 1) {
                            // Truncate to 1 character if pasted more than one
                            currentField.value = currentField.value.slice(0, 1);
                            const nextField = otpFields[index + 1];
                            if (nextField) {
                                nextField.focus();
                            } else {
                                currentField.blur();
                            }
                        }
                        // Optional: Auto-shift focus backward on backspace/delete
                        else if (currentField.value.length === 0 && index > 0) {
                            const prevField = otpFields[index - 1];
                            if (prevField) {
                                prevField.focus();
                            }
                        }
                        checkFormCompletion(); // Check status on every OTP input
                    });
                });
            }
            setupOTPAutoAdvance();

            const allFormInputs = document.querySelectorAll(
                '#input-fname, #input-lname, #datepicker, #input-phone, #input-email, ' +
                '#input-street, #input-postal, #input-city, #input-district, ' +
                '#otp1, #otp2, #otp3, #otp4, #otp5, #otp6, ' +
                '#input-secans-1, #input-secans-2, #input-username, #input-new-password, #input-confirm-password'
            );
            allFormInputs.forEach(input => {
                input.addEventListener('input', checkFormCompletion);
                input.addEventListener('blur', checkFormCompletion);
            });
            checkFormCompletion();

            // Function to display the entered email on Step 2
            function updateEmailDisplay() {
                const emailInput = document.getElementById('input-email').value;
                document.getElementById('email_address').textContent = emailInput;
            }

            let timerInterval; // To hold the interval ID

            function startResendTimer() {
                let seconds = 28; // Countdown from 28 seconds
                const timerElement = document.getElementById('timer');
                const resendLink = document.querySelector('#resend-label a');

                // Disable the link immediately
                resendLink.style.pointerEvents = 'none';
                resendLink.style.color = '#9ca3af'; // Make it look disabled (gray)

                timerElement.textContent = `in ${seconds} seconds`;

                timerInterval = setInterval(() => {
                    seconds--;
                    timerElement.textContent = `in ${seconds} seconds`;

                    if (seconds <= 0) {
                        clearInterval(timerInterval);
                        timerElement.textContent = ''; // Hide the timer text
                        resendLink.style.pointerEvents = 'auto'; // Re-enable the link
                        resendLink.style.color = ''; // Restore original color
                    }
                }, 1000);
            }

            function handleResendOtp() {
                // This function will be called when the user clicks "Resend Code"
                // It's very similar to the next-1 button's logic
                
                // Optional: show a sending state
                alert('Sending a new OTP...');

                const email = document.getElementById('input-email').value;

                fetch('/signup/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Failed to send new OTP.');
                    return response.json();
                })
                .then(data => {
                    alert('A new OTP has been sent to your email.');
                    startResendTimer(); // Restart the timer
                })
                .catch(error => {
                    alert(error.message);
                });
            }

            // Stepper Navigation with AJAX
            document.getElementById('next-1').addEventListener('click', (e) => {
                e.preventDefault();
                const { isForm1Complete } = checkFormCompletion();

                if (isForm1Complete) {
                    // Show a loading state if you want
                    e.target.textContent = 'Sending...';
                    e.target.disabled = true;

                    const email = document.getElementById('input-email').value;

                    fetch('/signup/send-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ email: email })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        isStep1Completed = true;
                        currentStep = 2;
                        updateEmailDisplay(); // Update the email on the OTP screen
                        updateStepper();
                        startResendTimer();
                    })
                    .catch(error => {
                        // Handle errors, like if the email is already taken
                        let errorMessage = "An error occurred. Please try again.";
                        if (error.errors && error.errors.email) {
                            errorMessage = error.errors.email[0];
                        }
                        alert(errorMessage);
                    })
                    .finally(() => {
                        // Restore button state
                        e.target.textContent = 'Confirm';
                        e.target.disabled = false;
                    });

                } else {
                    alert("Please complete all required fields for Basic Details correctly.");
                }
            });
            
            document.getElementById('next-2').addEventListener('click', (e) => {
                e.preventDefault();
                const { isOTPComplete } = checkFormCompletion();
                
                if (isOTPComplete) {
                    e.target.textContent = 'Verifying...';
                    e.target.disabled = true;

                    const otpDigits = [
                        document.getElementById('otp1').value,
                        document.getElementById('otp2').value,
                        document.getElementById('otp3').value,
                        document.getElementById('otp4').value,
                        document.getElementById('otp5').value,
                        document.getElementById('otp6').value
                    ];

                    fetch('/signup/verify-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ otp: otpDigits })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        isStep2Completed = true;
                        currentStep = 3;
                        updateStepper();
                    })
                    .catch(error => {
                        alert(error.message || "Invalid OTP. Please try again.");
                    })
                    .finally(() => {
                        e.target.textContent = 'Verify OTP';
                        e.target.disabled = false;
                    });

                } else {
                    alert("Please enter the complete 6-digit OTP.");
                }
            });

            document.getElementById('next-3').addEventListener('click', (e) => {
                const { isForm3Complete } = checkFormCompletion();

                // If the form is NOT complete, prevent the submission and show an alert.
                if (!isForm3Complete) {
                    e.preventDefault(); 
                    alert("Please complete all required fields for Account Setup correctly.");
                }
            });

            // Function to set up a dropdown (reused from signup1 and signup3)
            // Kept for phone dropdown logic
            function setupDropdown(buttonId, dropdownId, hiddenInputName, isPhoneDropdown = false) {
                const dropdownButton = document.getElementById(buttonId);
                const dropdownMenu = document.getElementById(dropdownId);
                const menuItems = dropdownId === 'dropdown-list' ? dropdownMenu.querySelectorAll('.custom-dropdown-item') : dropdownMenu.querySelectorAll('a');

                // Toggle dropdown visibility on button click
                if (dropdownButton) {
                    dropdownButton.addEventListener('click', (e) => {
                        e.stopPropagation();
                        dropdownMenu.classList.toggle('hidden');
                    });
                }

                // Handle selection of a dropdown item
                menuItems.forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        const selectedValue = item.getAttribute('data-value');

                        if (isPhoneDropdown) {
                            const selectedFlag = dropdownButton.querySelector('#selected-flag');
                            const selectedCode = dropdownButton.querySelector('#selected-code');
                            const selectedText = item.querySelector('span').textContent.split(' ')[0]; // Just take the code
                            const flagSrc = item.getAttribute('data-flag');
                            selectedFlag.src = flagSrc;
                            selectedCode.textContent = selectedText;
                        } else {
                            // This else block is no longer needed since the new security question
                            // logic handles its own setup and is not called for security questions anymore.
                            // I'll keep it commented out for reference/robustness against future changes.
                            // dropdownButton.textContent = item.textContent.trim();
                            // const hiddenInput = document.querySelector(`input[name='${hiddenInputName}']`);
                            // if (hiddenInput) {
                            //     hiddenInput.value = selectedValue;
                            // }
                        }

                        dropdownMenu.classList.add('hidden');
                        checkFormCompletion(); // Trigger status check on selection
                    });
                });

                // Close the dropdown if the user clicks outside
                document.addEventListener('click', (e) => {
                    if (dropdownButton && dropdownMenu && !dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.add('hidden');
                    }
                });
            }

            // Set up the phone number dropdown (ONLY THIS CALL REMAINS)
            setupDropdown('dropdown-btn', 'dropdown-list', 'phone_code', true);

            // REMOVED calls for setupDropdown('dropdown-secques1', ...) and setupDropdown('dropdown-secques2', ...)

            function setupPasswordToggle(toggleButtonId, passwordInputId) {
                const toggleButton = document.getElementById(toggleButtonId);
                const passwordInput = document.getElementById(passwordInputId);
                if (toggleButton && passwordInput) {
                    toggleButton.addEventListener('click', function () {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        this.classList.toggle('fa-eye');
                        this.classList.toggle('fa-eye-slash');
                    });
                }
            }
            setupPasswordToggle('togglePassword', 'input-new-password');
            setupPasswordToggle('toggleConfirmPassword', 'input-confirm-password');

            // Initial state
            updateStepper();
        });
    </script>
</body>

</html>