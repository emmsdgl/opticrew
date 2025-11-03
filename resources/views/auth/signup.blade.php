<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css">

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

        /* Stepper styles */
        .step-circle {
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
            background-color: white;
        }

        .step-container {
            position: relative;
            z-index: 1;
        }

        /* Ensure step circles are on a higher z-index on mobile */
        @media (max-width: 640px) {
            .step-circle {
                width: 2.5rem;
                height: 2.5rem;
            }

            /* Adjust progress line for smaller circles on mobile */
            #stepper-container .absolute.h-0 {
                top: 1.25rem !important;
            }
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

        /* Auto-fill detection */
        @keyframes onAutoFillStart {
            from {
                opacity: 0.99;
            }

            to {
                opacity: 1;
            }
        }

        input:-webkit-autofill {
            animation-name: onAutoFillStart;
            animation-duration: 0.001s;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <div id="header-container"
        class="relative flex items-center justify-center p-3 lg:px-8 inset-x-0 top-0 z-50 w-full hidden lg:flex">
        <div id="logo-container" class="absolute top-0 left-0 mt-3 ml-6">
            <a href="#" class="-m-1.5 p-1.5">
                <span class="sr-only"></span>
                <img src="{{asset('/images/finnoys-text-logo-light.svg')}}" alt=""
                    class="h-20 w-full flex justify-start ml-8">

            </a>
        </div>

        <div id="stepper-container" class="w-full max-w-3xl px-8 py-8">
            <div class="relative max-w-4xl mx-auto px-4">
                <!-- Steps Container -->
                <div class="relative flex justify-between items-start">
                    <!-- Step 1 -->
                    <div id="step-1-indicator" class="flex flex-col items-center  flex-1 step-container relative z-10">
                        <div
                            class="step-circle w-10 h-auto sm:w-12 sm:h-auto rounded-full flex items-center justify-center font-semibold text-sm sm:text-base bg-blue-500 text-white ring-4 ring-blue-100 relative">
                            <span>1</span>
                        </div>
                        <span
                            class="mt-2 text-xs sm:text-sm font-medium text-center text-blue-600 max-w-[80px] sm:max-w-none">Basic
                            Details</span>
                    </div>

                    <!-- Step 2 -->
                    <div id="step-2-indicator" class="flex flex-col items-center flex-1 step-container relative z-10">
                        <div
                            class="step-circle w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-semibold text-sm sm:text-base bg-white text-gray-400 border-2 border-gray-300 relative">
                            <span>2</span>
                        </div>
                        <span
                            class="mt-2 text-xs sm:text-sm font-medium text-center text-gray-400 max-w-[80px] sm:max-w-none">Email
                            Verification</span>
                    </div>

                    <!-- Step 3 -->
                    <div id="step-3-indicator" class="flex flex-col items-center flex-1 step-container relative z-10">
                        <div
                            class="step-circle w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-semibold text-sm sm:text-base bg-white text-gray-400 border-2 border-gray-300 relative">
                            <span>3</span>
                        </div>
                        <span id="step-3-label"
                            class="mt-2 text-xs sm:text-sm font-medium text-center text-gray-400 max-w-[80px] sm:max-w-none">Account
                            Setup</span>
                    </div>

                    <!-- Progress Line - positioned to connect circle centers -->
                    <div class="absolute left-0 right-0 h-0.5 bg-gray-300"
                        style="top: 1.25rem; left: 16.67%; right: 16.67%;">
                        <div id="progress-line" class="h-full bg-blue-500 transition-all duration-300"
                            style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="content-container">
        <div id="container-1" class="w-full p-3 md:p-5">
            <div id="inner-container-1" class="w-full pl-14">
                <span id="form-status"
                    class="text-xs font-medium me-2 px-2.5 py-0.5 rounded-2xl bg-red-100 text-red-500">Incomplete</span>
                <p id="step-label" class="font-bold text-base mt-7">Step 1</p>
                <h1 id="step-head">Basic Details</h1>
                <p id="step-desc">First things first, let us know your the following information for identification and
                    account verification purposes.</p>
            </div>
        </div>
        <div id="container-2" class="w-full p-12">
            <!-- START: Copy from here -->
            <form method="POST" action="{{ route('register.client') }}">
                @csrf

                <!-- ====================================================== -->
                <!--                       STEP 1                           -->
                <!-- ====================================================== -->
                <div id="step-1" class="step-content w-full px-12">
                    <h1 id="form-head" class="mb-4 w-full text-center font-sans font-medium italic">Tell Us About You
                    </h1>

                    <div class="space-y-4">
                        <!-- Account Type Selection -->
                        <div class="w-full mb-8">
                            <label class="block text-sm font-semibold mb-3 text-gray-700">
                                Account Type
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Personal Option -->
                                <label class="relative">
                                    <input type="radio" name="account_type" value="personal" id="account-type-personal"
                                        class="peer sr-only" checked>
                                    <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer
                                                peer-checked:border-blue-500 peer-checked:bg-blue-50
                                                hover:border-gray-400 transition-all">
                                        <div class="font-semibold text-gray-900 mb-1">Personal</div>
                                        <div class="text-sm text-gray-600">
                                            Individual account with immediate access upon registration
                                        </div>
                                    </div>
                                </label>

                                <!-- Company Option -->
                                <label class="relative">
                                    <input type="radio" name="account_type" value="company" id="account-type-company"
                                        class="peer sr-only">
                                    <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer
                                                peer-checked:border-blue-500 peer-checked:bg-blue-50
                                                hover:border-gray-400 transition-all">
                                        <div class="font-semibold text-gray-900 mb-1">Company</div>
                                        <div class="text-sm text-gray-600">
                                            Business account requiring admin approval before activation
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Personal Account Fields -->
                        <div id="personal-fields" class="space-y-4">
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
                                    <input type="text" id="input-mname" placeholder=" " maxlength="5"
                                        class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                        name="middle_initial">
                                    <label for="input-mname">M.I.</label>
                                </div>
                            </div>

                            <!-- Birthdate -->
                            <div class="input-container relative w-full">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <i
                                        class="fas fa-calendar-day absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                </div>
                                <input datepicker id="datepicker" name="birthdate" datepicker-format="mm-dd-yyyy"
                                    type="text"
                                    class="input-field w-full text-sm pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    placeholder="mm - dd - yyyy" required>
                                <label for="datepicker">Birthdate</label>
                                <div id="age-error" class="text-red-500 text-sm mt-1 hidden">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    You must be at least 18 years old to create an account.
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div
                                class="input-container w-full flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-3">
                                <div class="custom-dropdown-container w-full sm:w-36">
                                    <button id="dropdown-btn" type="button" class="custom-dropdown-btn">
                                        <img id="selected-flag" src="{{asset('/images/icons/finland-flag.svg')}}"
                                            alt="Finland Flag" class="h-4 w-auto mr-2">
                                        <span id="selected-code" class="text-sm">+358</span>
                                        <span
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div id="dropdown-list" class="custom-dropdown-list">
                                        <div class="custom-dropdown-item" data-value="+358"
                                            data-flag="{{asset('/images/icons/finland-flag.svg')}}">
                                            <img src="{{asset('/images/icons/finland-flag.svg')}}" alt="Finland Flag"
                                                class="h-4 w-auto mr-2">
                                            <span class="text-sm">+358 Finland</span>
                                        </div>
                                        <div class="custom-dropdown-item" data-value="+46"
                                            data-flag="{{asset('/images/icons/sweden-flag.svg')}}">
                                            <img src="{{asset('/images/icons/sweden-flag.svg')}}" alt="Sweden Flag"
                                                class="h-4 w-auto mr-2">
                                            <span class="text-sm">+46 Sweden</span>
                                        </div>
                                        <div class="custom-dropdown-item" data-value="+47"
                                            data-flag="{{asset('/images/icons/norway-flag.svg')}}">
                                            <img src="{{asset('/images/icons/norway-flag.svg')}}" alt="Norway Flag"
                                                class="h-4 w-auto mr-2">
                                            <span class="text-sm">+47 Norway</span>
                                        </div>
                                        <div class="custom-dropdown-item" data-value="+45"
                                            data-flag="{{asset('/images/icons/denmark-flag.svg')}}">
                                            <img src="{{asset('/images/icons/denmark-flag.svg')}}" alt="Denmark Flag"
                                                class="h-4 w-auto mr-2">
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
                                <i
                                    class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                <input type="email" id="input-email" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="email" required>
                                <label for="input-email">Email Address</label>
                            </div>

                            <!-- Finnish Address Fields -->
                            <div class="input-container w-full relative">
                                <i
                                    class="fas fa-map-marker-alt absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                <input type="text" id="input-street" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="street_address" required>
                                <label for="input-street">Street Address</label>
                            </div>

                            <div class="w-full flex flex-col sm:flex-row justify-between sm:space-x-3">
                                <div class="input-container flex-1">
                                    <i
                                        class="fas fa-mail-bulk absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                    <input type="text" id="input-postal" placeholder=" " maxlength="5"
                                        class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                        name="postal_code" required>
                                    <label for="input-postal">Postal Code</label>
                                </div>
                                <div class="input-container flex-1">
                                    <i
                                        class="fas fa-city absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                    <input type="text" id="input-city" placeholder=" "
                                        class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                        name="city" required>
                                    <label for="input-city">City</label>
                                </div>
                            </div>

                            <!-- District with Autocomplete -->
                            <div class="input-container w-full relative" id="district-container">
                                <i
                                    class="fas fa-building absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500 z-10"></i>
                                <input type="text" id="input-district" placeholder=" " autocomplete="off"
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="district" required>
                                <label for="input-district">District (Kaupunginosa)</label>

                                <!-- District Suggestions Dropdown -->
                                <div id="district-suggestions"
                                    class="hidden absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                </div>
                            </div>
                        </div>
                        <!-- End Personal Account Fields -->

                        <!-- Company Account Fields -->
                        <div id="company-fields" class="space-y-4 hidden">
                            <!-- Company Name -->
                            <div class="input-container w-full relative">
                                <i
                                    class="fas fa-building absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                <input type="text" id="input-company-name" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="company_name">
                                <label for="input-company-name">Company Name</label>
                            </div>

                            <!-- Contact Person Name -->
                            <div class="w-full flex flex-col sm:flex-row justify-between sm:space-x-3">
                                <div class="input-container flex-1">
                                    <input type="text" id="input-contact-fname" placeholder=" "
                                        class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                        name="contact_first_name">
                                    <label for="input-contact-fname">Contact Person First Name</label>
                                </div>
                                <div class="input-container flex-1">
                                    <input type="text" id="input-contact-lname" placeholder=" "
                                        class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                        name="contact_last_name">
                                    <label for="input-contact-lname">Contact Person Last Name</label>
                                </div>
                            </div>

                            <!-- Business Registration Number -->
                            <div class="input-container w-full relative">
                                <i
                                    class="fas fa-id-card absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                <input type="text" id="input-business-id" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="business_id">
                                <label for="input-business-id">Business ID (Y-tunnus)</label>
                            </div>

                            <!-- E-Invoice Number -->
                            <div class="input-container w-full relative">
                                <i
                                    class="fas fa-receipt absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                <input type="text" id="input-einvoice-number" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="einvoice_number">
                                <label for="input-einvoice-number">E-Invoice Number</label>
                            </div>

                            <!-- Phone Number (Company) -->
                            <div
                                class="input-container w-full flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-3">
                                <div class="custom-dropdown-container w-full sm:w-36">
                                    <button id="dropdown-btn-company" type="button" class="custom-dropdown-btn">
                                        <img id="selected-flag-company"
                                            src="{{asset('/images/icons/finland-flag.svg')}}" alt="Finland Flag"
                                            class="h-4 w-auto mr-2">
                                        <span id="selected-code-company" class="text-sm">+358</span>
                                        <span
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div id="dropdown-list-company" class="custom-dropdown-list">
                                        <div class="custom-dropdown-item" data-value="+358"
                                            data-flag="{{asset('/images/icons/finland-flag.svg')}}">
                                            <img src="{{asset('/images/icons/finland-flag.svg')}}" alt="Finland Flag"
                                                class="h-4 w-auto mr-2">
                                            <span class="text-sm">+358 Finland</span>
                                        </div>
                                        <div class="custom-dropdown-item" data-value="+46"
                                            data-flag="{{asset('/images/icons/sweden-flag.svg')}}">
                                            <img src="{{asset('/images/icons/sweden-flag.svg')}}" alt="Sweden Flag"
                                                class="h-4 w-auto mr-2">
                                            <span class="text-sm">+46 Sweden</span>
                                        </div>
                                        <div class="custom-dropdown-item" data-value="+47"
                                            data-flag="{{asset('/images/icons/norway-flag.svg')}}">
                                            <img src="{{asset('/images/icons/norway-flag.svg')}}" alt="Norway Flag"
                                                class="h-4 w-auto mr-2">
                                            <span class="text-sm">+47 Norway</span>
                                        </div>
                                        <div class="custom-dropdown-item" data-value="+45"
                                            data-flag="{{asset('/images/icons/denmark-flag.svg')}}">
                                            <img src="{{asset('/images/icons/denmark-flag.svg')}}" alt="Denmark Flag"
                                                class="h-4 w-auto mr-2">
                                            <span class="text-sm">+45 Denmark</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1 input-container w-full">
                                    <input type="tel" id="input-company-phone" placeholder="40 1234567"
                                        class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                        name="company_phone_number">
                                    <label for="input-company-phone">Company Phone Number</label>
                                </div>
                            </div>

                            <!-- Email (Company) -->
                            <div class="input-container w-full relative">
                                <i
                                    class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                <input type="email" id="input-company-email" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="company_email">
                                <label for="input-company-email">Company Email Address</label>
                            </div>

                            <!-- Company Address Fields -->
                            <div class="input-container w-full relative">
                                <i
                                    class="fas fa-map-marker-alt absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                <input type="text" id="input-company-street" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="company_street_address">
                                <label for="input-company-street">Company Street Address</label>
                            </div>

                            <div class="w-full flex flex-col sm:flex-row justify-between sm:space-x-3">
                                <div class="input-container flex-1">
                                    <i
                                        class="fas fa-mail-bulk absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                    <input type="text" id="input-company-postal" placeholder=" " maxlength="5"
                                        class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                        name="company_postal_code">
                                    <label for="input-company-postal">Postal Code</label>
                                </div>
                                <div class="input-container flex-1">
                                    <i
                                        class="fas fa-city absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                    <input type="text" id="input-company-city" placeholder=" "
                                        class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                        name="company_city">
                                    <label for="input-company-city">City</label>
                                </div>
                            </div>

                            <div class="input-container w-full relative">
                                <i
                                    class="fas fa-building absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                                <input type="text" id="input-company-district" placeholder=" "
                                    class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                    name="company_district">
                                <label for="input-company-district">District (Kaupunginosa)</label>
                            </div>
                        </div>
                        <!-- End Company Account Fields -->

                        <div id="buttons-container" class="flex justify-center gap-4 mt-6">
                            <button id="back1-btn" type="button"
                                class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm">Cancel</button>
                            <button type="button" id="next-1"
                                class="w-full px-20 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Confirm</button>
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
                            <p class="text-sm text-center"> To verify your provided email address, please enter the
                                one-time pin (OTP) that we sent to <span id="email_address"
                                    class="font-bold text-blue-600">your.email@example.com</span> </p>
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
                                <button id="back2-btn" type="button"
                                    class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm">Back</button>
                                <button type="button" id="next-2"
                                    class="w-full px-20 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Verify
                                    OTP</button>
                            </div>
                            <p id="resend-label" class="text-sm text-center w-full mt-4">Didn't receive an OTP?
                                <span><a href="#" class="text-blue-600 font-bold">Resend Code</a></span> <span
                                    id="timer"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ====================================================== -->
                <!--                       STEP 3                           -->
                <!-- ====================================================== -->
                <div id="step-3" class="step-content hidden">
                    <!-- Display Validation Errors for Step 3 -->
                    @if ($errors->any())
                        <div id="step3-errors" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative max-w-md mx-auto" role="alert">
                            <strong class="font-bold">Oops! There were some problems:</strong>
                            <ul class="mt-2 ml-4 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Personal Account Step 3 -->
                    <div id="step-3-personal" class="w-full space-y-4">
                        <h1 id="form-head" class="mb-4">Set up your security questions</h1>
                        <div class="input-container w-full max-w-md mx-auto relative">
                            <i
                                class="fas fa-question-circle absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <button type="button" id="dropdown-secques1"
                                class="security-question-btn1 select-field w-full pl-12 pr-4 py-4 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm text-gray-400 text-left font-normal">
                                Select a security question
                            </button>
                            <div
                                class="security-question-dropdown z-10 absolute left-0 right-0 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-full">
                                <ul class="py-2 text-sm text-gray-700">
                                    <li><a href="#" class="text-left block px-4 py-2 hover:bg-blue-200"
                                            data-value="pet_name">What is the name of your first pet?</a></li>
                                    <li><a href="#" class="text-left block px-4 py-2 hover:bg-blue-200"
                                            data-value="birth_city">In what city were you born?</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="input-container w-full max-w-md mx-auto">
                            <i
                                class="fas fa-comment absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="text" id="input-secans-1" placeholder=" "
                                class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                name="security_answer_1" required>
                            <label for="input-secans-1">Security Question Answer</label>
                        </div>
                        @error('security_answer_1')
                            <p class="text-red-500 text-xs text-center max-w-md mx-auto">{{ $message }}</p>
                        @enderror

                        <div class="input-container w-full max-w-md mx-auto relative">
                            <i
                                class="fas fa-question-circle absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <button type="button" id="dropdown-secques2"
                                class="security-question-btn2 select-field w-full pl-12 pr-4 py-4 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm text-gray-400 text-left font-normal">
                                Select a security question
                            </button>
                            <div
                                class="security-question-dropdown z-10 absolute left-0 right-0 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-full">
                                <ul class="py-2 text-sm text-gray-700">
                                    <li><a href="#" class="text-left block px-4 py-2 hover:bg-blue-200"
                                            data-value="best_friend">What is the name of your best friend?</a></li>
                                    <li><a href="#" class="text-left block px-4 py-2 hover:bg-blue-200"
                                            data-value="teacher_name">Who was your favorite teacher?</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="input-container w-full max-w-md mx-auto">
                            <i
                                class="fas fa-comment absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="text" id="input-secans-2" placeholder=" "
                                class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                name="security_answer_2" required>
                            <label for="input-secans-2">Security Question Answer</label>
                        </div>
                        @error('security_answer_2')
                            <p class="text-red-500 text-xs text-center max-w-md mx-auto">{{ $message }}</p>
                        @enderror

                        <h1 id="form-head" class="mb-4 p-6">Let's Get Your Account Ready</h1>
                        <div class="input-container w-full max-w-md mx-auto">
                            <i
                                class="fas fa-id-card absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="text" id="input-username" placeholder=" "
                                class="input-field w-full pr-4 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                name="username" value="{{ old('username') }}" required>
                            <label for="input-username">Username</label>
                        </div>
                        @error('username')
                            <p class="text-red-500 text-xs text-center max-w-md mx-auto">{{ $message }}</p>
                        @enderror

                        <div class="input-container w-full max-w-md mx-auto relative">
                            <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="password" id="input-new-password" placeholder=" "
                                class="input-field w-full pr-12 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                name="password" autocomplete="new-password" required>
                            <label for="input-new-password">New Password</label>
                            <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer"
                                id="togglePassword"></i>
                        </div>
                        <div class="input-container w-full max-w-md mx-auto relative">
                            <i
                                class="fas fa-square-check absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500"></i>
                            <input type="password" id="input-confirm-password" placeholder=" "
                                class="input-field w-full pr-12 py-3 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700"
                                name="password_confirmation" autocomplete="new-password" required>
                            <label for="input-confirm-password">Confirm New Password</label>
                            <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer"
                                id="toggleConfirmPassword"></i>
                        </div>

                        <!-- Password Validation Indicators -->
                        <div class="w-full max-w-md mx-auto mt-3 space-y-2">
                            <!-- Username Length -->
                            <div id="username-length-indicator" class="flex items-center text-xs">
                                <i class="fas fa-circle text-gray-300 mr-2 text-[8px]"></i>
                                <span class="text-gray-500">Username: 6-8 characters</span>
                            </div>

                            <!-- Password Requirements -->
                            <div id="password-length-indicator" class="flex items-center text-xs">
                                <i class="fas fa-circle text-gray-300 mr-2 text-[8px]"></i>
                                <span class="text-gray-500">Password: At least 8 characters</span>
                            </div>
                            <div id="password-capital-indicator" class="flex items-center text-xs">
                                <i class="fas fa-circle text-gray-300 mr-2 text-[8px]"></i>
                                <span class="text-gray-500">Contains 1 capital letter</span>
                            </div>
                            <div id="password-number-indicator" class="flex items-center text-xs">
                                <i class="fas fa-circle text-gray-300 mr-2 text-[8px]"></i>
                                <span class="text-gray-500">Contains 1 number</span>
                            </div>
                            <div id="password-special-indicator" class="flex items-center text-xs">
                                <i class="fas fa-circle text-gray-300 mr-2 text-[8px]"></i>
                                <span class="text-gray-500">Contains 1 special character (!@#$%^&*)</span>
                            </div>
                            <div id="password-match-indicator" class="flex items-center text-xs">
                                <i class="fas fa-circle text-gray-300 mr-2 text-[8px]"></i>
                                <span class="text-gray-500">Passwords match</span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6 w-full pt-4">
                            <button id="back3-btn" type="button"
                                class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm">Back</button>
                            <button type="submit" id="next-3-personal"
                                class="w-full sm:w-auto px-20 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center">Create
                                Account</button>
                        </div>
                    </div>

                    <!-- Company Account Step 3 - Service Details -->
                    <div id="step-3-company" class="w-full space-y-4 hidden">
                        <p class="text-sm text-center text-gray-600">
                            Please select the services you are inquiring about. Our team will review your requirements
                            and send a detailed quotation to your email.
                        </p>

                        <!-- Service Details Section -->
                        <div class="mt-6 p-6 bg-blue-50 rounded-xl border border-blue-200 max-w-3xl mx-auto">
                            <!-- Service Type Checkboxes -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Service Type <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Hotel Rooms Cleaning"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Hotel Rooms Cleaning</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Light Daily Cleaning"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Light Daily Cleaning</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Full Daily Cleaning"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Full Daily Cleaning</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Deep Cleaning"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Deep Cleaning</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Snowout"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Snowout</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Cabins"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Cabins</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Cottages"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Cottages</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Igloos"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Igloos</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Restaurant"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Restaurant</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Reception"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Reception</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Saunas"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Saunas</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="service_types[]" value="Hallway"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-gray-700">Hallway</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Other Concerns -->
                            <div class="w-full">
                                <label for="input-other-concerns" class="block text-sm font-medium text-gray-700 mb-2">
                                    Additional Information or Other Concerns
                                </label>
                                <textarea id="input-other-concerns" rows="4"
                                    placeholder="Please provide any additional details about your service requirements..."
                                    class="w-full px-4 py-3 bg-white rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 resize-none"
                                    name="other_concerns"></textarea>
                                <p class="text-xs text-gray-500 mt-2">Our team will review your requirements and send a
                                    custom quotation to your email.</p>
                            </div>
                        </div>
                        <!-- End Service Details Section -->

                        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6 w-full pt-4">
                            <button id="back3-company-btn" type="button"
                                class="w-full sm:w-auto px-10 py-4 text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm">Back</button>
                            <button type="submit" id="next-3-company"
                                class="w-full sm:w-auto px-20 py-4 text-white text-sm bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-center">Submit
                                Inquiry</button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- END: Copy until here -->
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Signup page JavaScript loaded - version 2.0');

            // Initialize step completion flags at the very top
            let isStep1Completed = false;
            let isStep2Completed = false;
            let isStep3Completed = false;

            // ===== AUTO-NAVIGATE TO STEP 3 IF ERRORS EXIST =====
            const hasStep3Errors = {{ ($errors->has('username') || $errors->has('password') || $errors->has('security_question') || $errors->has('security_answer_1') || $errors->has('security_answer_2')) ? 'true' : 'false' }};

            if (hasStep3Errors) {
                // Mark steps 1 and 2 as completed to allow navigation to step 3
                isStep1Completed = true;
                isStep2Completed = true;
                // Set current step to 3 after a brief delay to ensure DOM is ready
                setTimeout(() => {
                    currentStep = 3;
                    updateStepper();
                    // Scroll to error message
                    const errorDiv = document.getElementById('step3-errors');
                    if (errorDiv) {
                        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 100);
            }

            // ===== CLEAR ERROR MESSAGES WHEN STARTING FRESH =====
            // Clear errors when user makes changes to the username field
            const usernameField = document.getElementById('input-username');
            if (usernameField) {
                usernameField.addEventListener('input', () => {
                    const errorDiv = document.getElementById('step3-errors');
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                    }
                });
            }

            // ===== REAL-TIME PASSWORD & USERNAME VALIDATION =====
            const passwordField = document.getElementById('input-new-password');
            const confirmPasswordField = document.getElementById('input-confirm-password');

            // Validation indicators
            const usernameIndicator = document.getElementById('username-length-indicator');
            const passwordLengthIndicator = document.getElementById('password-length-indicator');
            const passwordCapitalIndicator = document.getElementById('password-capital-indicator');
            const passwordNumberIndicator = document.getElementById('password-number-indicator');
            const passwordSpecialIndicator = document.getElementById('password-special-indicator');
            const passwordMatchIndicator = document.getElementById('password-match-indicator');

            // Helper function to update indicator status
            function updateIndicator(indicator, isValid) {
                const icon = indicator.querySelector('i');
                const text = indicator.querySelector('span');

                if (isValid) {
                    icon.classList.remove('fa-circle', 'text-gray-300');
                    icon.classList.add('fa-check-circle', 'text-green-500');
                    text.classList.remove('text-gray-500');
                    text.classList.add('text-green-600', 'font-medium');
                } else {
                    icon.classList.remove('fa-check-circle', 'text-green-500');
                    icon.classList.add('fa-circle', 'text-gray-300');
                    text.classList.remove('text-green-600', 'font-medium');
                    text.classList.add('text-gray-500');
                }
            }

            // Username validation (6-8 characters)
            if (usernameField) {
                usernameField.addEventListener('input', () => {
                    const username = usernameField.value.trim();
                    const isValid = username.length >= 6 && username.length <= 8;
                    updateIndicator(usernameIndicator, isValid);

                    // Show error message if exceeds max length
                    if (username.length > 8) {
                        usernameField.value = username.substring(0, 8);
                    }
                });
            }

            // Password strength validation
            function validatePassword() {
                if (!passwordField) return;

                const password = passwordField.value;

                // Check length (at least 8 characters)
                const hasMinLength = password.length >= 8;
                updateIndicator(passwordLengthIndicator, hasMinLength);

                // Check for capital letter
                const hasCapital = /[A-Z]/.test(password);
                updateIndicator(passwordCapitalIndicator, hasCapital);

                // Check for number
                const hasNumber = /[0-9]/.test(password);
                updateIndicator(passwordNumberIndicator, hasNumber);

                // Check for special character
                const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
                updateIndicator(passwordSpecialIndicator, hasSpecial);

                // Check password match
                checkPasswordMatch();

                // Return overall validity
                return hasMinLength && hasCapital && hasNumber && hasSpecial;
            }

            // Password match validation
            function checkPasswordMatch() {
                if (!passwordField || !confirmPasswordField) return;

                const password = passwordField.value;
                const confirmPassword = confirmPasswordField.value;

                // Only show match indicator if both fields have values
                if (password && confirmPassword) {
                    const isMatch = password === confirmPassword;
                    updateIndicator(passwordMatchIndicator, isMatch);

                    // Update confirm password field border
                    if (isMatch) {
                        confirmPasswordField.classList.remove('ring-red-500');
                        confirmPasswordField.classList.add('ring-green-500');
                    } else {
                        confirmPasswordField.classList.remove('ring-green-500');
                        confirmPasswordField.classList.add('ring-red-500');
                    }
                } else {
                    // Reset indicator if either field is empty
                    updateIndicator(passwordMatchIndicator, false);
                    confirmPasswordField.classList.remove('ring-red-500', 'ring-green-500');
                }
            }

            // Add event listeners
            if (passwordField) {
                passwordField.addEventListener('input', validatePassword);
            }

            if (confirmPasswordField) {
                confirmPasswordField.addEventListener('input', checkPasswordMatch);
            }

            // ===== ACCOUNT TYPE TOGGLE =====
            const accountTypePersonal = document.getElementById('account-type-personal');
            const accountTypeCompany = document.getElementById('account-type-company');
            const personalFields = document.getElementById('personal-fields');
            const companyFields = document.getElementById('company-fields');

            function toggleAccountFields() {
                const step3Label = document.getElementById('step-3-label');

                if (accountTypePersonal.checked) {
                    // Show personal fields, hide company fields
                    personalFields.classList.remove('hidden');
                    companyFields.classList.add('hidden');

                    // Update Step 3 label to "Account Setup" for personal accounts
                    if (step3Label) {
                        step3Label.textContent = 'Account Setup';
                    }

                    // Enable personal field requirements, disable company field requirements
                    personalFields.querySelectorAll('input[required], input').forEach(input => {
                        if (input.name !== 'middle_initial') { // M.I. is optional
                            input.setAttribute('required', 'required');
                        }
                    });
                    companyFields.querySelectorAll('input').forEach(input => {
                        input.removeAttribute('required');
                        input.value = ''; // Clear company field values
                    });

                    // Enable Step 3 personal account field requirements
                    const step3Personal = document.getElementById('step-3-personal');
                    if (step3Personal) {
                        step3Personal.querySelectorAll('input, textarea').forEach(input => {
                            input.setAttribute('required', 'required');
                        });
                    }

                    // Disable Step 3 company field requirements
                    const step3Company = document.getElementById('step-3-company');
                    if (step3Company) {
                        step3Company.querySelectorAll('input, textarea').forEach(input => {
                            input.removeAttribute('required');
                        });
                    }
                } else {
                    // Show company fields, hide personal fields
                    personalFields.classList.add('hidden');
                    companyFields.classList.remove('hidden');

                    // Update Step 3 label to "Service Details" for company accounts
                    if (step3Label) {
                        step3Label.textContent = 'Service Details';
                    }

                    // Enable company field requirements, disable personal field requirements
                    companyFields.querySelectorAll('input').forEach(input => {
                        input.setAttribute('required', 'required'); // All company fields are required
                    });
                    personalFields.querySelectorAll('input').forEach(input => {
                        input.removeAttribute('required');
                        input.value = ''; // Clear personal field values
                    });

                    // Disable Step 3 personal account field requirements
                    const step3Personal = document.getElementById('step-3-personal');
                    if (step3Personal) {
                        step3Personal.querySelectorAll('input, textarea').forEach(input => {
                            input.removeAttribute('required');
                        });
                    }

                    // Enable Step 3 company field requirements (at least one service type checkbox)
                    const step3Company = document.getElementById('step-3-company');
                    if (step3Company) {
                        // Note: Checkboxes validation is handled separately in the submit handler
                        // Textarea (other concerns) is optional so no required attribute needed
                    }
                }
                checkFormCompletion(); // Re-check form completion
            }

            // Add event listeners for account type radio buttons
            accountTypePersonal.addEventListener('change', toggleAccountFields);
            accountTypeCompany.addEventListener('change', toggleAccountFields);

            // Initialize with personal fields shown
            toggleAccountFields();

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
                        `${NOMINATIM_API}?format=json&countrycodes=FI&addressdetails=1&limit=5&q=${encodeURIComponent(query)}`,
                        {
                            headers: {
                                'User-Agent': 'OptiCrew/1.0'
                            }
                        }
                    );

                    // Check if response is ok
                    if (!response.ok) {
                        console.warn('Nominatim API error:', response.status, response.statusText);
                        return [];
                    }

                    const data = await response.json();

                    // Ensure we return an array
                    return Array.isArray(data) ? data : [];
                } catch (error) {
                    console.error('Error fetching street suggestions:', error);
                    return [];
                }
            }

            function showStreetSuggestions(suggestions) {
                // Check if suggestions is an array
                if (!Array.isArray(suggestions) || suggestions.length === 0) {
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
                        `${NOMINATIM_API}?format=json&countrycodes=FI&postalcode=${postcode}&addressdetails=1&limit=1`,
                        {
                            headers: {
                                'User-Agent': 'OptiCrew/1.0'
                            }
                        }
                    );

                    if (!response.ok) {
                        console.warn('Postal code lookup error:', response.status);
                        return null;
                    }

                    const data = await response.json();
                    return (Array.isArray(data) && data.length > 0) ? data[0] : null;
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
                        `${NOMINATIM_API}?format=json&countrycodes=FI&city=${encodeURIComponent(cityName)}&addressdetails=1&limit=5`,
                        {
                            headers: {
                                'User-Agent': 'OptiCrew/1.0'
                            }
                        }
                    );

                    if (!response.ok) {
                        console.warn('City lookup error:', response.status);
                        return [];
                    }

                    const data = await response.json();
                    return Array.isArray(data) ? data : [];
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
                        `${NOMINATIM_API}?format=json&countrycodes=FI&city=${encodeURIComponent(cityName)}&addressdetails=1&limit=20`,
                        {
                            headers: {
                                'User-Agent': 'OptiCrew/1.0'
                            }
                        }
                    );

                    if (!response.ok) {
                        console.warn('District fetch error:', response.status);
                        return [];
                    }

                    const data = await response.json();

                    if (!Array.isArray(data)) {
                        return [];
                    }

                    const districts = new Set();
                    data.forEach(item => {
                        if (item && item.address) {
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

            // ===== COMPANY ADDRESS AUTOCOMPLETE =====
            const companyStreetInput = document.getElementById('input-company-street');
            const companyPostalInput = document.getElementById('input-company-postal');
            const companyCityInput = document.getElementById('input-company-city');
            const companyDistrictInput = document.getElementById('input-company-district');
            const companyDistrictSuggestions = document.getElementById('company-district-suggestions');

            // Create company street address suggestions dropdown
            if (companyStreetInput) {
                const companyStreetSuggestionsDiv = document.createElement('div');
                companyStreetSuggestionsDiv.id = 'company-street-suggestions';
                companyStreetSuggestionsDiv.className = 'hidden absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto';
                companyStreetInput.parentElement.appendChild(companyStreetSuggestionsDiv);

                companyStreetInput.addEventListener('input', (e) => {
                    clearTimeout(addressTimeout);
                    addressTimeout = setTimeout(async () => {
                        const query = e.target.value;
                        if (query.length >= 3) {
                            const suggestions = await fetchStreetSuggestions(query);
                            showCompanyStreetSuggestions(suggestions, companyStreetSuggestionsDiv);
                        } else {
                            companyStreetSuggestionsDiv.classList.add('hidden');
                        }
                    }, 500);
                });

                companyStreetInput.addEventListener('focus', () => {
                    if (companyStreetInput.value.length >= 3) {
                        fetchStreetSuggestions(companyStreetInput.value).then(suggestions =>
                            showCompanyStreetSuggestions(suggestions, companyStreetSuggestionsDiv)
                        );
                    }
                });

                // Close company street suggestions when clicking outside
                document.addEventListener('click', (e) => {
                    if (!companyStreetInput.contains(e.target) && !companyStreetSuggestionsDiv.contains(e.target)) {
                        companyStreetSuggestionsDiv.classList.add('hidden');
                    }
                });
            }

            function showCompanyStreetSuggestions(suggestions, suggestionsDiv) {
                if (!Array.isArray(suggestions) || suggestions.length === 0) {
                    suggestionsDiv.classList.add('hidden');
                    return;
                }

                suggestionsDiv.innerHTML = suggestions.map(place => {
                    const streetName = place.address?.road || place.display_name.split(',')[0];
                    const postalCode = place.address?.postcode || '';
                    const city = place.address?.city || place.address?.town || place.address?.village || '';
                    const district = place.address?.suburb || place.address?.neighbourhood || '';

                    return `
                        <div class="street-item px-4 py-2 hover:bg-blue-100 cursor-pointer border-b border-gray-200"
                             data-street="${streetName}"
                             data-postal="${postalCode}"
                             data-city="${city}"
                             data-district="${district}">
                            <div class="font-semibold text-gray-900">${streetName}</div>
                            <div class="text-sm text-gray-600">${postalCode} ${city}${district ? ', ' + district : ''}</div>
                        </div>
                    `;
                }).join('');

                suggestionsDiv.classList.remove('hidden');

                document.querySelectorAll('#company-street-suggestions .street-item').forEach(item => {
                    item.addEventListener('click', () => {
                        companyStreetInput.value = item.getAttribute('data-street');
                        if (companyPostalInput) companyPostalInput.value = item.getAttribute('data-postal');
                        if (companyCityInput) companyCityInput.value = item.getAttribute('data-city');
                        if (companyDistrictInput) companyDistrictInput.value = item.getAttribute('data-district');
                        suggestionsDiv.classList.add('hidden');
                        checkFormCompletion();
                    });
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

            // No need to store original icons with new design

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
            // Cancel button handler
            const back1Btn = document.getElementById('back1-btn');
            console.log('Cancel button element:', back1Btn);
            if (back1Btn) {
                back1Btn.addEventListener('click', function (e) {
                    console.log('Cancel button clicked!');
                    e.preventDefault();
                    // Go back to previous page, or homepage if no referrer
                    const referrer = document.referrer;
                    console.log('Referrer:', referrer);
                    if (referrer && referrer !== '' && referrer !== window.location.href) {
                        console.log('Redirecting to referrer');
                        window.location.href = referrer;
                    } else {
                        console.log('Redirecting to homepage');
                        window.location.href = '/';
                    }
                });
            }
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
                    personal: {
                        name: 'Account Setup',
                        desc: 'Just a few quick steps to personalize your experience and make sure everything runs smoothly.'
                    },
                    company: {
                        name: 'Service Details',
                        desc: 'Please select the services you are inquiring about and provide any additional information.'
                    }
                }
            };

            const checkIconHTML = `<i class="fi fi-rr-check text-sm sm:text-base"></i>`;

            function updateStepper() {
                stepLabel.textContent = `Step ${currentStep}`;

                // Handle Step 3 dynamically based on account type
                if (currentStep === 3) {
                    const accountType = accountTypePersonal.checked ? 'personal' : 'company';
                    stepHeader.textContent = stepNames[3][accountType].name;
                    stepDesc.textContent = stepNames[3][accountType].desc;
                } else {
                    stepHeader.textContent = stepNames[currentStep].name;
                    stepDesc.textContent = stepNames[currentStep].desc;
                }

                // Calculate progress line width
                const totalStepsCount = 3;
                const progressPercentage = totalStepsCount > 1 ? ((currentStep - 1) / (totalStepsCount - 1)) * 100 : 0;

                // Update progress line width
                if (progressLine) {
                    progressLine.style.width = `${progressPercentage}%`;
                }

                // ===== FIX: Manage required attributes based on current step =====
                // This prevents HTML5 validation errors on hidden fields

                // Step 1 fields - only required when on Step 1
                const step1Personal = document.querySelectorAll('#personal-fields input, #personal-fields select');
                const step1Company = document.querySelectorAll('#company-fields input, #company-fields select');

                if (currentStep === 1) {
                    if (accountTypePersonal.checked) {
                        step1Personal.forEach(field => {
                            if (field.name !== 'middle_initial') {
                                field.setAttribute('required', 'required');
                            }
                        });
                        step1Company.forEach(field => field.removeAttribute('required'));
                    } else {
                        step1Company.forEach(field => field.setAttribute('required', 'required'));
                        step1Personal.forEach(field => field.removeAttribute('required'));
                    }
                } else {
                    // Remove required from all Step 1 fields when not on Step 1
                    step1Personal.forEach(field => field.removeAttribute('required'));
                    step1Company.forEach(field => field.removeAttribute('required'));
                }

                // Step 2 fields (OTP) - only required when on Step 2
                const step2Fields = document.querySelectorAll('#step-2 input');
                step2Fields.forEach(field => {
                    if (currentStep === 2) {
                        field.setAttribute('required', 'required');
                    } else {
                        field.removeAttribute('required');
                    }
                });

                // Update steps' appearance
                for (let i = 1; i <= totalSteps; i++) {
                    const indicator = stepIndicators[i];
                    const circle = indicator.querySelector('.step-circle');
                    const content = circle ? circle.querySelector('span') : null; // Fixed: select span inside circle
                    const label = indicator.querySelector('span:last-child');
                    const isCompleted = (i === 1 && isStep1Completed) || (i === 2 && isStep2Completed) || (i === 3 && isStep3Completed);

                    // Reset all classes
                    if (circle) {
                        circle.className = 'step-circle w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-semibold text-sm sm:text-base';

                        if (isCompleted) {
                            // Completed step - green with check mark
                            circle.classList.add('bg-blue-500', 'text-white');
                            if (content) content.innerHTML = checkIconHTML;
                            if (label) label.className = 'mt-2 text-xs sm:text-sm font-medium text-center text-gray-700';
                        } else if (i === currentStep) {
                            // Current/Active step - blue with ring
                            circle.classList.add('bg-blue-500', 'text-white', 'ring-4', 'ring-blue-100');
                            if (content) content.textContent = i;
                            if (label) label.className = 'mt-2 text-xs sm:text-sm font-medium text-center text-blue-600';
                        } else {
                            // Pending step - gray
                            circle.classList.add('bg-white', 'text-gray-400', 'border-2', 'border-gray-300');
                            if (content) content.textContent = i;
                            if (label) label.className = 'mt-2 text-xs sm:text-sm font-medium text-center text-gray-400';
                        }
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

                // Handle Step 3 content based on account type
                if (currentStep === 3) {
                    const step3Personal = document.getElementById('step-3-personal');
                    const step3Company = document.getElementById('step-3-company');

                    if (accountTypePersonal.checked) {
                        // Show personal account step 3 (security questions)
                        step3Personal.classList.remove('hidden');
                        step3Company.classList.add('hidden');

                        // Enable required attributes for personal fields
                        step3Personal.querySelectorAll('input, textarea').forEach(input => {
                            input.setAttribute('required', 'required');
                        });

                        // Disable required attributes for company fields
                        step3Company.querySelectorAll('input, textarea').forEach(input => {
                            input.removeAttribute('required');
                        });
                    } else {
                        // Show company account step 3 (service details)
                        step3Personal.classList.add('hidden');
                        step3Company.classList.remove('hidden');

                        // Disable required attributes for personal fields
                        step3Personal.querySelectorAll('input, textarea').forEach(input => {
                            input.removeAttribute('required');
                        });

                        // Company fields: checkboxes validation handled in submit handler
                        // Textarea is optional, so no required attribute needed
                    }
                }

                // Run completion check after updating stepper visuals
                checkFormCompletion();
            }

            function checkFormCompletion() {
                // --- 0. Status Element and Global Variables (Must be defined first) ---
                const formStatus = document.getElementById('form-status'); // Element showing status
                if (!formStatus) {
                    return;
                }

                // Use the currently selected code from the dropdown (safely check if it exists)
                const selectedCodeElement = document.getElementById('selected-code');
                const selectedCountryCode = selectedCodeElement ? selectedCodeElement.textContent.replace('+', '') : '358';

                // Check which account type is selected
                const isPersonalAccount = accountTypePersonal.checked;
                const isCompanyAccount = accountTypeCompany.checked;

                // --- Form 1 Inputs (Personal Account) ---
                const fName = document.getElementById('input-fname');
                const lName = document.getElementById('input-lname');
                const bDate = document.getElementById('datepicker');
                const phone = document.getElementById('input-phone');
                const email = document.getElementById('input-email');
                const street = document.getElementById('input-street');
                const postal = document.getElementById('input-postal');
                const city = document.getElementById('input-city');
                const district = document.getElementById('input-district');

                // --- Form 1 Inputs (Company Account) ---
                const companyName = document.getElementById('input-company-name');
                const contactFName = document.getElementById('input-contact-fname');
                const contactLName = document.getElementById('input-contact-lname');
                const businessId = document.getElementById('input-business-id');
                const einvoiceNumber = document.getElementById('input-einvoice-number');
                const companyPhone = document.getElementById('input-company-phone');
                const companyEmail = document.getElementById('input-company-email');
                const companyStreet = document.getElementById('input-company-street');
                const companyPostal = document.getElementById('input-company-postal');
                const companyCity = document.getElementById('input-company-city');
                const companyDistrict = document.getElementById('input-company-district');

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
                let isForm1PersonalComplete = false;
                let isForm1CompanyComplete = false;

                if (isPersonalAccount) {
                    // Personal Account Validation
                    const isFNameComplete = fName && fName.value.trim() !== "";
                    const isLNameComplete = lName && lName.value.trim() !== "";

                    // Birthdate validation - must be 18 years or older
                    let isBDateComplete = false;
                    let isAgeValid = false;
                    const ageErrorElement = document.getElementById('age-error');

                    if (bDate && bDate.value.trim() !== "") {
                        isBDateComplete = true;

                        // Parse date in mm-dd-yyyy format
                        const dateParts = bDate.value.trim().split(/[-/]/);
                        if (dateParts.length === 3) {
                            const month = parseInt(dateParts[0], 10);
                            const day = parseInt(dateParts[1], 10);
                            const year = parseInt(dateParts[2], 10);

                            // Create date object (month is 0-indexed in JavaScript)
                            const birthDate = new Date(year, month - 1, day);
                            const today = new Date();

                            // Calculate age
                            let age = today.getFullYear() - birthDate.getFullYear();
                            const monthDiff = today.getMonth() - birthDate.getMonth();

                            // Adjust age if birthday hasn't occurred this year
                            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                                age--;
                            }

                            isAgeValid = age >= 18;

                            // Show/hide age error message
                            if (ageErrorElement) {
                                if (isAgeValid) {
                                    ageErrorElement.classList.add('hidden');
                                } else {
                                    ageErrorElement.classList.remove('hidden');
                                }
                            }
                        } else {
                            // Invalid date format
                            isAgeValid = false;
                            if (ageErrorElement) {
                                ageErrorElement.classList.remove('hidden');
                            }
                        }
                    } else {
                        // Hide error if birthdate is empty
                        if (ageErrorElement) {
                            ageErrorElement.classList.add('hidden');
                        }
                    }

                    const isEmailValid = email && email.value.trim() !== "" && email.value.includes('@') && email.value.includes('.');
                    const isStreetComplete = street && street.value.trim() !== "";
                    const isPostalComplete = postal && postal.value.trim() !== "" && postal.value.length === 5;
                    const isCityComplete = city && city.value.trim() !== "";
                    const isDistrictComplete = district && district.value.trim() !== "";

                    let isPhoneValid = false;
                    if (phone && phone.value.trim() !== "") {
                        const cleanNumber = phone.value.replace(/\D/g, '');
                        if (selectedCountryCode === '63') {
                            isPhoneValid = cleanNumber.length === 10;
                        } else if (selectedCountryCode === '358') {
                            isPhoneValid = cleanNumber.length >= 7 && cleanNumber.length <= 10;
                        } else {
                            isPhoneValid = cleanNumber.length >= 7;
                        }
                    }

                    isForm1PersonalComplete = isFNameComplete && isLNameComplete && isBDateComplete && isAgeValid &&
                        isEmailValid && isPhoneValid && isStreetComplete &&
                        isPostalComplete && isCityComplete && isDistrictComplete;
                } else {
                    // Company Account Validation
                    const isCompanyNameComplete = companyName && companyName.value.trim() !== "";
                    const isContactFNameComplete = contactFName && contactFName.value.trim() !== "";
                    const isContactLNameComplete = contactLName && contactLName.value.trim() !== "";
                    const isBusinessIdComplete = businessId && businessId.value.trim() !== "";
                    const isEinvoiceNumberComplete = einvoiceNumber && einvoiceNumber.value.trim() !== "";
                    const isCompanyEmailValid = companyEmail && companyEmail.value.trim() !== "" &&
                        companyEmail.value.includes('@') && companyEmail.value.includes('.');
                    const isCompanyStreetComplete = companyStreet && companyStreet.value.trim() !== "";
                    const isCompanyPostalComplete = companyPostal && companyPostal.value.trim() !== "" &&
                        companyPostal.value.length === 5;
                    const isCompanyCityComplete = companyCity && companyCity.value.trim() !== "";
                    const isCompanyDistrictComplete = companyDistrict && companyDistrict.value.trim() !== "";

                    let isCompanyPhoneValid = false;
                    if (companyPhone && companyPhone.value.trim() !== "") {
                        const cleanNumber = companyPhone.value.replace(/\D/g, '');
                        const selectedCodeCompanyElement = document.getElementById('selected-code-company');
                        const selectedCompanyCode = selectedCodeCompanyElement ? selectedCodeCompanyElement.textContent.replace('+', '') : '358';
                        if (selectedCompanyCode === '63') {
                            isCompanyPhoneValid = cleanNumber.length === 10;
                        } else if (selectedCompanyCode === '358') {
                            isCompanyPhoneValid = cleanNumber.length >= 7 && cleanNumber.length <= 10;
                        } else {
                            isCompanyPhoneValid = cleanNumber.length >= 7;
                        }
                    }

                    isForm1CompanyComplete = isCompanyNameComplete && isContactFNameComplete && isContactLNameComplete &&
                        isBusinessIdComplete && isEinvoiceNumberComplete &&
                        isCompanyEmailValid && isCompanyPhoneValid && isCompanyStreetComplete &&
                        isCompanyPostalComplete && isCompanyCityComplete && isCompanyDistrictComplete;
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
                const isForm1Complete = isPersonalAccount ? isForm1PersonalComplete : isForm1CompanyComplete;

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
                    'bg-yellow-100', 'text-yellow-600',
                    'bg-red-100', 'text-red-500'
                );

                // Check if ANY Step 1 field has data (based on current account type)
                let isAnyStep1FieldPopulated = false;
                if (isPersonalAccount) {
                    // Check personal account fields
                    isAnyStep1FieldPopulated =
                        (fName && fName.value.trim().length > 0) ||
                        (lName && lName.value.trim().length > 0) ||
                        (bDate && bDate.value.trim().length > 0) ||
                        (phone && phone.value.trim().length > 0) ||
                        (email && email.value.trim().length > 0) ||
                        (street && street.value.trim().length > 0) ||
                        (postal && postal.value.trim().length > 0) ||
                        (city && city.value.trim().length > 0) ||
                        (district && district.value.trim().length > 0);
                } else {
                    // Check company account fields
                    isAnyStep1FieldPopulated =
                        (companyName && companyName.value.trim().length > 0) ||
                        (contactFName && contactFName.value.trim().length > 0) ||
                        (contactLName && contactLName.value.trim().length > 0) ||
                        (businessId && businessId.value.trim().length > 0) ||
                        (einvoiceNumber && einvoiceNumber.value.trim().length > 0) ||
                        (companyPhone && companyPhone.value.trim().length > 0) ||
                        (companyEmail && companyEmail.value.trim().length > 0) ||
                        (companyStreet && companyStreet.value.trim().length > 0) ||
                        (companyPostal && companyPostal.value.trim().length > 0) ||
                        (companyCity && companyCity.value.trim().length > 0) ||
                        (companyDistrict && companyDistrict.value.trim().length > 0);
                }

                // Check if any Step 2 or Step 3 fields have data
                const isAnyStep2FieldPopulated =
                    (otp1 && otp1.value.length > 0) || (otp2 && otp2.value.length > 0) ||
                    (otp3 && otp3.value.length > 0) || (otp4 && otp4.value.length > 0) ||
                    (otp5 && otp5.value.length > 0) || (otp6 && otp6.value.length > 0);

                const isAnyStep3FieldPopulated =
                    (secans1 && secans1.value.trim().length > 0) || (secans2 && secans2.value.trim().length > 0) ||
                    (username && username.value.trim().length > 0) || (password && password.value.length > 0) ||
                    (confirmPassword && confirmPassword.value.length > 0);

                // Option A: If all three steps are complete
                if (isStep1Completed && isStep2Completed && isStep3Completed) {
                    formStatus.textContent = `Completed`;
                    formStatus.classList.add('bg-green-100', 'text-green-500');
                    console.log('Status: Completed');
                }
                // Option B: If the form has been started (at least one field entered in any step)
                else if (isAnyStep1FieldPopulated || isAnyStep2FieldPopulated || isAnyStep3FieldPopulated || isStep1Completed || isStep2Completed) {
                    formStatus.textContent = `In Progress`;
                    formStatus.classList.add('bg-yellow-100', 'text-yellow-600');
                    console.log('Status: In Progress', {
                        isAnyStep1FieldPopulated,
                        isAnyStep2FieldPopulated,
                        isAnyStep3FieldPopulated
                    });
                }
                // Option C: If the form hasn't been touched
                else {
                    formStatus.textContent = `Incomplete`;
                    formStatus.classList.add('bg-red-100', 'text-red-500');
                    console.log('Status: Incomplete');
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
                '#input-company-name, #input-contact-fname, #input-contact-lname, #input-business-id, #input-einvoice-number, ' +
                '#input-company-phone, #input-company-email, #input-company-street, #input-company-postal, ' +
                '#input-company-city, #input-company-district, ' +
                '#otp1, #otp2, #otp3, #otp4, #otp5, #otp6, ' +
                '#input-secans-1, #input-secans-2, #input-username, #input-new-password, #input-confirm-password'
            );
            allFormInputs.forEach(input => {
                input.addEventListener('input', checkFormCompletion);
                input.addEventListener('blur', checkFormCompletion);
                input.addEventListener('change', checkFormCompletion); // Added for auto-fill support
                input.addEventListener('focus', checkFormCompletion); // Check when user clicks on auto-filled field

                // Detect Chrome/Edge auto-fill using animationstart event
                input.addEventListener('animationstart', (e) => {
                    if (e.animationName === 'onAutoFillStart') {
                        checkFormCompletion();
                    }
                });
            });
            checkFormCompletion();

            // Auto-fill detection: Check periodically for auto-filled values
            // Browser auto-fill doesn't always trigger input/change events
            let autofillCheckCount = 0;
            const autofillInterval = setInterval(() => {
                checkFormCompletion();
                autofillCheckCount++;
                // Stop checking after 5 seconds (10 checks x 500ms)
                if (autofillCheckCount >= 10) {
                    clearInterval(autofillInterval);
                }
            }, 500);

            // Also check when user clicks anywhere on the page (after auto-fill)
            document.addEventListener('click', () => {
                if (autofillCheckCount < 10) {
                    checkFormCompletion();
                }
            }, { once: false });

            // Function to display the entered email on Step 2
            function updateEmailDisplay() {
                const emailInput = accountTypePersonal.checked
                    ? document.getElementById('input-email').value
                    : document.getElementById('input-company-email').value;
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

                    // Get the correct email based on account type
                    const email = accountTypePersonal.checked
                        ? document.getElementById('input-email').value
                        : document.getElementById('input-company-email').value;

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
                            } else if (error.message) {
                                errorMessage = error.message;
                            }

                            // Display error in a more visible way
                            alert('❌ ' + errorMessage + '\n\nPlease use a different email address or log in if you already have an account.');

                            // Also log to console for debugging
                            console.error('OTP sending failed:', error);
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

            // Back button for Step 3 (both personal and company use this)
            document.getElementById('back3-btn').addEventListener('click', () => {
                currentStep = 2;
                updateStepper();
            });

            document.getElementById('back3-company-btn').addEventListener('click', () => {
                currentStep = 2;
                updateStepper();
            });

            // Submit buttons - different handlers for personal vs company
            const next3PersonalBtn = document.getElementById('next-3-personal');
            if (next3PersonalBtn) {
                next3PersonalBtn.addEventListener('click', (e) => {
                    const { isForm3Complete } = checkFormCompletion();

                    // If the form is NOT complete, prevent the submission and show an alert.
                    if (!isForm3Complete) {
                        e.preventDefault();
                        alert("Please complete all required fields for Account Setup correctly.");
                    }
                });
            }

            const next3CompanyBtn = document.getElementById('next-3-company');
            if (next3CompanyBtn) {
                next3CompanyBtn.addEventListener('click', (e) => {
                    // Check if at least one service type is selected
                    const serviceTypes = document.querySelectorAll('input[name="service_types[]"]:checked');
                    if (serviceTypes.length === 0) {
                        e.preventDefault();
                        alert("Please select at least one service type.");
                        return;
                    }
                    // Form will submit normally if validation passes
                });
            }

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

            // Set up the company phone number dropdown
            const dropdownBtnCompany = document.getElementById('dropdown-btn-company');
            const dropdownListCompany = document.getElementById('dropdown-list-company');
            const selectedFlagCompany = document.getElementById('selected-flag-company');
            const selectedCodeCompany = document.getElementById('selected-code-company');

            if (dropdownBtnCompany) {
                dropdownBtnCompany.addEventListener('click', () => {
                    dropdownListCompany.classList.toggle('active');
                });

                dropdownListCompany.querySelectorAll('.custom-dropdown-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const value = item.getAttribute('data-value');
                        const flagSrc = item.getAttribute('data-flag');

                        selectedFlagCompany.src = flagSrc;
                        selectedCodeCompany.textContent = value;
                        dropdownListCompany.classList.remove('active');
                        checkFormCompletion();
                    });
                });
            }

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