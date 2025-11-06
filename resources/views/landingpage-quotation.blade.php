@extends('components.layouts.general-landing')

@section('title', 'Quotation')

@push('styles')
    <style>
        body {
            background-image: none;
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar for form panel */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            @apply bg-gray-100 dark:bg-gray-800;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            @apply bg-gray-300 dark:bg-gray-600 rounded-full;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            @apply bg-gray-400 dark:bg-gray-500;
        }

        /* Fade in animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Active breadcrumb pulse */
        @keyframes pulse-slow {
            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .breadcrumb-active {
            animation: pulse-slow 2s ease-in-out infinite;
        }

        /* Scroll tooltip animation */
        .scroll-tooltip {
            animation: bounce-tooltip 2s ease-in-out infinite;
        }

        @keyframes bounce-tooltip {
            0%, 100% {
                transform: translateY(0);
                opacity: 1;
            }
            50% {
                transform: translateY(-10px);
                opacity: 0.8;
            }
        }

        /* Fade out animation for tooltip */
        .tooltip-fade-out {
            animation: fadeOut 0.5s ease-out forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        /* Reduce overall text sizes */
        #quotation-form label {
            @apply text-xs font-medium;
        }

        #quotation-form input,
        #quotation-form select,
        #quotation-form button[type="button"] {
            @apply text-sm;
        }

        #quotation-form .space-y-6 {
            @apply space-y-4;
        }

        #quotation-form .space-y-3 {
            @apply space-y-2;
        }

        #quotation-form .space-y-2 {
            @apply space-y-1.5;
        }
    </style>
@endpush

@section('content')
    <!-- MAIN SECTION CONTAINER -->
    <section id="main-container" class="w-full grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 px-4 lg:px-12 py-8">
        
        <!-- LEFT PANEL - Fixed/Sticky -->
        <section id="container-1"
            class="flex flex-col justify-center bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl px-8 lg:px-12 py-8 lg:py-12 shadow-sm lg:sticky lg:top-8 lg:h-full">

            <!-- Breadcrumb -->
            <div class="w-full mb-8 lg:mb-12">
                <nav aria-label="Progress">
                    <ol class="flex items-center justify-between sm:justify-start gap-2 sm:gap-4">
                        <!-- Step 1: Service -->
                        <li class="flex items-center">
                            <button onclick="scrollToSection('step-1')" class="group transition-all duration-300"
                                id="breadcrumb-step-1">
                                <span class="text-sm font-medium transition-colors text-gray-400 dark:text-gray-500">
                                    Service
                                </span>
                            </button>
                        </li>

                        <!-- Arrow 1 -->
                        <li>
                            <i class="fa-solid fa-chevron-right text-xs transition-colors text-gray-300 dark:text-gray-600"
                                id="arrow-1"></i>
                        </li>

                        <!-- Step 2: Property -->
                        <li class="flex items-center">
                            <button onclick="scrollToSection('step-2')" class="group transition-all duration-300"
                                id="breadcrumb-step-2">
                                <span class="text-sm font-medium transition-colors text-gray-400 dark:text-gray-500">
                                    Property
                                </span>
                            </button>
                        </li>

                        <!-- Arrow 2 -->
                        <li>
                            <i class="fa-solid fa-chevron-right text-xs transition-colors text-gray-300 dark:text-gray-600"
                                id="arrow-2"></i>
                        </li>

                        <!-- Step 3: Contact -->
                        <li class="flex items-center">
                            <button onclick="scrollToSection('step-3')" class="group transition-all duration-300"
                                id="breadcrumb-step-3">
                                <span class="text-sm font-medium transition-colors text-gray-400 dark:text-gray-500">
                                    Contact
                                </span>
                            </button>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Text Contents -->
            <div class="w-full">
                <h1 class="text-3xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-2">
                    Get a <span class="text-blue-600 dark:text-blue-400">Free Cleaning</span><br>
                    Quote Instantly
                </h1>
                <p class="text-base text-gray-600 dark:text-gray-300 font-medium py-4 lg:py-6">No hidden fees. No commitment
                </p>
                <p class="text-sm lg:text-base text-gray-500 dark:text-gray-400 mt-3">
                    Get your custom quote and calculate the approximate cost of service <span class="font-bold">according to
                        your space</span>
                </p>
            </div>
        </section>

        <!-- RIGHT PANEL - Scrollable -->
        <section id="container-2" class="flex flex-col justify-center align-items-center">
            <!-- Scroll More Tooltip - Centered -->
                <!-- <div id="scroll-tooltip" 
                    class="scroll-tooltip absolute bottom-8 right-4 z-50
                            bg-gradient-to-r from-blue-600 to-blue-500 
                            dark:from-blue-500 dark:to-blue-600 
                            text-white 
                            px-3 py-3 rounded-full shadow-xl
                            flex text-center items-center gap-2.5 text-sm
                            pointer-events-none
                            border-2 border-white/20">
                    <span>Scroll for more</span>
                    <i class="fa-solid fa-chevron-down text-xs animate-pulse"></i>
                </div> -->

            <div class="overflow-hidden">
                <!-- Scrollable Container -->
                <div id="form-scroll-container"
                    class="custom-scrollbar overflow-y-auto max-h-[calc(100vh-4rem)] lg:max-h-[calc(100vh-6rem)]">
                    <form id="quotation-form" class="px-6 sm:px-8 lg:px-10 py-8" x-data="quotationForm()">

                        <!-- Step 1: Service Information -->
                        <section id="step-1" class="scroll-section space-y-6 mb-16">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                    Step 1: <span class="text-blue-600 dark:text-blue-400">Service Information</span>
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400">Tell us about your cleaning needs</p>
                            </div>

                            <!-- Booking Type -->
                            <div class="space-y-3">
                                <label class="block text-sm text-gray-700 dark:text-gray-300">
                                    Booking Type <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Personal Option -->
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" name="booking_type" value="personal"
                                            x-model="formData.bookingType" class="peer sr-only">
                                        <div class="w-full p-4 sm:p-5 border-2 rounded-xl transition-all duration-300
                                                        peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20
                                                        border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800
                                                        hover:border-blue-400 dark:hover:border-blue-500">
                                            <div class="flex items-start gap-3">
                                                <i
                                                    class="fa-solid fa-user text-[#081032] dark:text-blue-400 text-xl mt-1"></i>
                                                <div class="flex-1">
                                                    <h3 class="text-gray-900 dark:text-white mb-1">Personal
                                                    </h3>
                                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                                        I am looking for a single individual or small team for my residence
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Company Option -->
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" name="booking_type" value="company"
                                            x-model="formData.bookingType" class="peer sr-only">
                                        <div class="w-full p-4 sm:p-5 border-2 rounded-xl transition-all duration-300
                                                        peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20
                                                        border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800
                                                        hover:border-blue-400 dark:hover:border-blue-500">
                                            <div class="flex items-start gap-3">
                                                <i
                                                    class="fa-solid fa-building text-[#081032] dark:text-blue-400 text-xl mt-1"></i>
                                                <div class="flex-1">
                                                    <h3 class="text-gray-900 dark:text-white mb-1">Company
                                                    </h3>
                                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                                        If you are booking for a company with a multiple workers for
                                                        cleaning
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Type of Cleaning Service -->
                            <div class="space-y-3">
                                @php
                                    $cleaningServices = [
                                        [
                                            'value' => 'deep_cleaning',
                                            'title' => 'Deep Cleaning',
                                            'description' => 'A thorough, top-to-bottom cleaning that tackles dirt and grime in hard-to-reach places.',
                                            'icon' => 'fa-solid fa-spray-can-sparkles'
                                        ],
                                        [
                                            'value' => 'daily_room_cleaning',
                                            'title' => 'Daily Room Cleaning',
                                            'description' => 'Complete room refresh tailored for guest accommodations.',
                                            'icon' => 'fa-solid fa-bed'
                                        ],
                                        [
                                            'value' => 'snowout_cleaning',
                                            'title' => 'Snowout Cleaning',
                                            'description' => 'Seasonal service focused on clearing snow and ice from cabin pathways for safety and accessibility.',
                                            'icon' => 'fa-solid fa-snowflake'
                                        ],
                                        [
                                            'value' => 'light_daily_cleaning',
                                            'title' => 'Light Daily Cleaning',
                                            'description' => 'Routine upkeep designed to keep spaces fresh and presentable.',
                                            'icon' => 'fa-solid fa-broom'
                                        ],
                                        [
                                            'value' => 'full_daily_cleaning',
                                            'title' => 'Full Daily Cleaning',
                                            'description' => 'Comprehensive cleaning service covering all areas for optimal hygiene and presentation.',
                                            'icon' => 'fa-solid fa-house-circle-check'
                                        ],
                                    ];

                                    $companyServiceTypes = [
                                        [
                                            'value' => 'Hotel Rooms Cleaning',
                                            'label' => 'Hotel Rooms Cleaning',
                                            'icon' => 'fa-solid fa-bed'
                                        ],
                                        [
                                            'value' => 'Light Daily Cleaning',
                                            'label' => 'Light Daily Cleaning',
                                            'icon' => 'fa-solid fa-broom'
                                        ],
                                        [
                                            'value' => 'Full Daily Cleaning',
                                            'label' => 'Full Daily Cleaning',
                                            'icon' => 'fa-solid fa-house-circle-check'
                                        ],
                                        [
                                            'value' => 'Deep Cleaning',
                                            'label' => 'Deep Cleaning',
                                            'icon' => 'fa-solid fa-spray-can-sparkles'
                                        ],
                                        [
                                            'value' => 'Snowout',
                                            'label' => 'Snowout',
                                            'icon' => 'fa-solid fa-snowflake'
                                        ],
                                        [
                                            'value' => 'Cabins',
                                            'label' => 'Cabins',
                                            'icon' => 'fa-solid fa-house'
                                        ],
                                        [
                                            'value' => 'Cottages',
                                            'label' => 'Cottages',
                                            'icon' => 'fa-solid fa-home'
                                        ],
                                        [
                                            'value' => 'Igloos',
                                            'label' => 'Igloos',
                                            'icon' => 'fa-solid fa-igloo'
                                        ],
                                        [
                                            'value' => 'Restaurant',
                                            'label' => 'Restaurant',
                                            'icon' => 'fa-solid fa-utensils'
                                        ],
                                        [
                                            'value' => 'Reception',
                                            'label' => 'Reception',
                                            'icon' => 'fa-solid fa-bell-concierge'
                                        ],
                                        [
                                            'value' => 'Saunas',
                                            'label' => 'Saunas',
                                            'icon' => 'fa-solid fa-hot-tub-person'
                                        ],
                                        [
                                            'value' => 'Hallway',
                                            'label' => 'Hallway',
                                            'icon' => 'fa-solid fa-door-open'
                                        ]
                                    ];
                                @endphp

                                <!-- Single Selection (Personal) -->
                                <div x-show="formData.bookingType === 'personal'">
                                    <x-client-components.quotation-page.service-dropdown label="Type of Cleaning Service"
                                        name="cleaning_service" :options="$cleaningServices" :required="true"
                                        :placeholdericon="'fa-solid fa-broom'" />
                                </div>

                                <!-- Multiple Selection (Company) - Card Based -->
                                <div x-show="formData.bookingType === 'company'" class="space-y-3">
                                    <label class="block text-sm text-gray-700 dark:text-gray-300">
                                        Service Type <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Select all services you need</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($companyServiceTypes as $service)
                                            <label class="relative flex cursor-pointer">
                                                <input type="checkbox"
                                                    value="{{ $service['value'] }}"
                                                    @change="toggleCleaningService('{{ $service['value'] }}')"
                                                    class="peer sr-only">
                                                <div class="w-full p-4 border-2 rounded-xl transition-all duration-300
                                                            peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20
                                                            border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800
                                                            hover:border-blue-400 dark:hover:border-blue-500">
                                                    <div class="flex items-center gap-3">
                                                        <div class="flex-shrink-0">
                                                            <i class="{{ $service['icon'] }} text-[#081032] dark:text-blue-400 text-lg"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $service['label'] }}</span>
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <i class="fa-solid fa-check text-blue-600 dark:text-blue-400 text-sm opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>


                            <!-- Date and Duration (Personal Only) -->
                            <div x-show="formData.bookingType === 'personal'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                <!-- Date of Service -->
                                <div class="space-y-2">
                                    @php
                                        $today = date('Y-m-d');
                                    @endphp

                                    <x-client-components.quotation-page.service-datepicker label="Date of Service"
                                        name="date_of_service" x-model="formData.dateOfService"
                                        ::required="formData.bookingType === 'personal'"
                                        :min-date="$today" placeholder="What is your preferred date?" />

                                </div>


                                <!-- Duration of Service -->
                                <div class="space-y-2">
                                    <x-client-components.quotation-page.quantity-picker label="Duration of Service"
                                        name="rooms" :min="1" :max="20" :default="1" icon="fa-solid fa-door-open"
                                        ::required="formData.bookingType === 'personal'" :showUnit="false" />

                                </div>
                            </div>

                            <!-- Type of urgency (Personal Only) -->
                            <div x-show="formData.bookingType === 'personal'" class="space-y-3">
                                <div class="relative">
                                    @php
                                        $urgencyType = [
                                            [
                                                'value' => 'same_day',
                                                'title' => 'Same-day (within 24h)',
                                                'description' => 'Urgent cleaning service available within the next 24 hours for immediate needs.',
                                                'icon' => ''
                                            ],
                                            [
                                                'value' => 'tomorrow',
                                                'title' => 'Tomorrow / Next Day',
                                                'description' => 'Schedule your cleaning service for the next day at your preferred time.',
                                                'icon' => ''
                                            ],
                                            [
                                                'value' => 'this_week',
                                                'title' => 'Within This Week (2–5 days from now)',
                                                'description' => 'Book a cleaning service within the current week at your convenience.',
                                                'icon' => ''
                                            ],
                                            [
                                                'value' => 'next_week',
                                                'title' => 'Next Week (5–10 days from now)',
                                                'description' => 'Plan ahead and schedule your cleaning for the upcoming week.',
                                                'icon' => ''
                                            ],
                                            [
                                                'value' => 'this_month',
                                                'title' => 'Within This Month',
                                                'description' => 'Flexible scheduling option for any time within the current month.',
                                                'icon' => ''
                                            ],
                                            [
                                                'value' => 'recurring',
                                                'title' => 'Recurring Cleaning',
                                                'description' => 'Set up regular cleaning service on a weekly, bi-weekly, or monthly schedule.',
                                                'icon' => ''
                                            ],
                                        ];
                                    @endphp
                                    <x-client-components.quotation-page.service-dropdown label="Type of Urgency"
                                        name="cleaning_service" :options="$urgencyType"
                                        ::required="formData.bookingType === 'personal'"
                                        :placeholdericon="'fa-solid fa-triangle-exclamation'" />

                                </div>
                            </div>

                        </section>

                        <!-- Step 2: Property Information -->
                        <section id="step-2" class="scroll-section space-y-6 mb-16">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                    Step 2: <span class="text-blue-600 dark:text-blue-400">Property Information</span>
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400">Tell us about your property details</p>
                            </div>

                            <!-- Type of Property and No. of Floors -->
                            <div class="space-y-2">
                                <div class="relative">
                                    @php
                                        $propertyType = [
                                            [
                                                'value' => 'apartment',
                                                'title' => 'Apartment / Flat',
                                                'description' => 'Single-level residential unit within a building',
                                                'icon' => 'fa-solid fa-building'
                                            ],
                                            [
                                                'value' => 'detached_house',
                                                'title' => 'Detached House',
                                                'description' => 'Standalone house',
                                                'icon' => 'fa-solid fa-house'
                                            ],
                                            [
                                                'value' => 'semi_detached',
                                                'title' => 'Semi-Detached / Duplex',
                                                'description' => 'Two attached houses sharing a wall',
                                                'icon' => 'fa-solid fa-house-chimney'
                                            ],
                                            [
                                                'value' => 'townhouse',
                                                'title' => 'Row House / Townhouse',
                                                'description' => 'Series of attached homes',
                                                'icon' => 'fa-solid fa-city'
                                            ],
                                            [
                                                'value' => 'student_apartment',
                                                'title' => 'Student Apartment',
                                                'description' => 'Shared unit, smaller rooms',
                                                'icon' => 'fa-solid fa-graduation-cap'
                                            ],
                                            [
                                                'value' => 'summer_cottage',
                                                'title' => 'Summer Cottage (Mökki)',
                                                'description' => 'Seasonal or vacation home',
                                                'icon' => 'fa-solid fa-umbrella-beach'
                                            ],
                                            [
                                                'value' => 'studio',
                                                'title' => 'Studio / Small Apartment',
                                                'description' => 'One-room units',
                                                'icon' => 'fa-solid fa-door-open'
                                            ],
                                            [
                                                'value' => 'office',
                                                'title' => 'Office / Workspace',
                                                'description' => 'Business premises',
                                                'icon' => 'fa-solid fa-briefcase'
                                            ],
                                            [
                                                'value' => 'retail',
                                                'title' => 'Retail Store / Shop',
                                                'description' => 'Storefront cleaning',
                                                'icon' => 'fa-solid fa-shop'
                                            ],
                                            [
                                                'value' => 'hotel',
                                                'title' => 'Hotel / Airbnb / Lodging',
                                                'description' => 'Short-stay cleaning',
                                                'icon' => 'fa-solid fa-hotel'
                                            ],
                                            [
                                                'value' => 'warehouse',
                                                'title' => 'Warehouse / Storage',
                                                'description' => 'Large area of storage, fewer materials, furniture',
                                                'icon' => 'fa-solid fa-warehouse'
                                            ],
                                            [
                                                'value' => 'clinic',
                                                'title' => 'Clinic / Healthcare Facility',
                                                'description' => 'Sanitization standards required',
                                                'icon' => 'fa-solid fa-hospital'
                                            ],
                                            [
                                                'value' => 'factory',
                                                'title' => 'Factory / Industrial Unit',
                                                'description' => 'Heavy-duty area',
                                                'icon' => 'fa-solid fa-industry'
                                            ],
                                            [
                                                'value' => 'school',
                                                'title' => 'School / University',
                                                'description' => 'Large spaces with classrooms',
                                                'icon' => 'fa-solid fa-school'
                                            ],
                                            [
                                                'value' => 'public_building',
                                                'title' => 'Public Building / Municipality Office',
                                                'description' => 'Government or civic buildings',
                                                'icon' => 'fa-solid fa-landmark'
                                            ],
                                            [
                                                'value' => 'gym',
                                                'title' => 'Gym / Fitness Center',
                                                'description' => 'Equipment and locker area sanitation',
                                                'icon' => 'fa-solid fa-dumbbell'
                                            ],
                                        ];
                                    @endphp
                                    <x-client-components.quotation-page.service-dropdown label="Type of Property"
                                        name="property_type" :options="$propertyType" :required="true"
                                        :placeholdericon="'fa-solid fa-house'" />
                                </div>
                                <div class="space-y-2">
                                        <x-client-components.quotation-page.quantity-picker label="Number of Floors"
                                        name="rooms" :min="1" :max="20" :default="1" icon="fa-solid fa-layer-group"
                                        :required="true" :showUnit="false" />

                                </div>
                            </div>

                            <!-- No. of Rooms and No. of People Per Room -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                <div class="space-y-2">
                                    <x-client-components.quotation-page.quantity-picker label="Number of Rooms"
                                        name="rooms" :min="1" :max="20" :default="1" icon="fa-solid fa-door-open"
                                        :required="true" :showUnit="false" />
                                </div>

                                <div class="space-y-2">
                                    <x-client-components.quotation-page.quantity-picker label="Number of People Per Room"
                                        name="rooms" :min="1" :max="20" :default="1" icon="fa-solid fa-people-roof"
                                        :required="true" :showUnit="false" />
                                </div>
                            </div>

                            <!-- Floor Area Value and Unit -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                <div class="space-y-2">
                                    <x-client-components.quotation-page.quantity-picker label="Floor Area Value"
                                        name="rooms" :min="1" :max="20" :default="1" icon="fa-solid fa-ruler-combined"
                                        :required="true" :showUnit="false" />
                                </div>

                                <div class="space-y-2">
                                    @php
                                        $areaUnits = [
                                            ['value' => 'sqm', 'label' => 'Square Meter (m² / sqm)'],
                                            ['value' => 'sqft', 'label' => 'Square Foot (sq ft / ft²)'],
                                            ['value' => 'sqyd', 'label' => 'Square Yard (yd²)'],
                                            ['value' => 'are', 'label' => 'Are (a)'],
                                            ['value' => 'hectare', 'label' => 'Hectare (ha)'],
                                            ['value' => 'sqin', 'label' => 'Square Inch (in²)'],
                                        ];
                                    @endphp

                                    <x-client-components.quotation-page.regular-dropdown label="Units" name="area_units"
                                        :options="$areaUnits" :multiple="false" :required="true"
                                        placeholder="e.g., m² / sqm, sq..." />

                                </div>
                            </div>

                            <!-- Property Location with Current Location Button -->
                            <div class="space-y-2">
                                <!-- Use the Location Combobox Component -->
                                <div @location-selected="handleLocationSelected($event.detail)"
                                    @location-option-changed="handleOptionChanged($event.detail)">
                                    <x-client-components.quotation-page.location-combobox label="Property Location"
                                        name="property_location" :required="true" />
                                </div>
                            </div>

                        </section>

                        <!-- Location Picker Modal -->
                        <div x-show="showLocationPicker" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto"
                            style="display: none;">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                                <!-- Background overlay -->
                                <div class="fixed inset-0 transition-opacity bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75"
                                    @click="showLocationPicker = false"></div>

                                <!-- Modal panel -->
                                <div
                                    class="relative inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg text-gray-900 dark:text-white">
                                            Select Property Location
                                        </h3>
                                        <button @click="showLocationPicker = false"
                                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                            <i class="fa-solid fa-times text-xl"></i>
                                        </button>
                                    </div>

                                    <!-- Search Box -->
                                    <div class="mb-4">
                                        <input type="text" id="location-search" placeholder="Search for a location..."
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                                        focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                                        bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                        placeholder-gray-400 dark:placeholder-gray-500">
                                    </div>

                                    <!-- Map Container -->
                                    <div id="map"
                                        class="w-full h-96 rounded-lg border border-gray-300 dark:border-gray-600"></div>

                                    <!-- Selected Location Display -->
                                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">Selected
                                            Location:</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400"
                                            x-text="formData.propertyLocation || 'No location selected'"></p>
                                    </div>

                                    <!-- Confirm Button -->
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button @click="showLocationPicker = false"
                                            class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600
                                                        text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-all duration-300">
                                            Cancel
                                        </button>
                                        <button @click="confirmLocation()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600
                                                        text-white font-medium rounded-lg transition-all duration-300">
                                            Confirm Location
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Contact Information -->
                        <section id="step-3" class="scroll-section space-y-6 mb-8">
                            <div class="mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                    Step 3: <span class="text-blue-600 dark:text-blue-400">Contact Information</span>
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400">Please provide your contact details</p>
                            </div>

                            <!-- Company Name (Company Only) -->
                            <div x-show="formData.bookingType === 'company'" class="space-y-2">
                                <label class="block text-sm text-gray-700 dark:text-gray-300">
                                    Company Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-building absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" x-model="formData.companyName" placeholder="Enter company name"
                                        :required="formData.bookingType === 'company'"
                                        class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                                    focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                                    bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                                    placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300">
                                </div>
                            </div>

                            <!-- Contact Person Name -->
                            <div class="space-y-2">
                                <label class="block text-sm text-gray-700 dark:text-gray-300">
                                    <span x-text="formData.bookingType === 'company' ? 'Contact Person Name' : 'Client Name'"></span>
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" x-model="formData.clientName" placeholder="Enter your name"
                                        required
                                        class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                                    focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                                    bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                                    placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300">
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div class="space-y-2">
                                <label class="block text-sm text-gray-700 dark:text-gray-300">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="tel" x-model="formData.phoneNumber" placeholder="+358 40 123 4567"
                                        required
                                        class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                                    focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                                    bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                                    placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300">
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div class="space-y-2">
                                <label class="block text-sm text-gray-700 dark:text-gray-300">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="email" x-model="formData.email"
                                        placeholder="Where should we send the quotation?"
                                        required
                                        class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg
                                                    focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                                    bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                                    placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-6">
                                <button type="submit" @click.prevent="submitForm()"
                                    class="w-full py-4 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600
                                                text-white rounded-full transition-all duration-300
                                                transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl text-base">
                                    Get the price quotation
                                </button>
                            </div>
                        </section>

                    </form>
                </div>
            </div>
        </section>
    </section>
@endsection

@push('scripts')
    <script>
        function quotationForm() {
            return {
                showLocationPicker: false,
                formData: {
                    // Step 1
                    bookingType: '',
                    cleaningServices: ['deep_cleaning'], // Default: Deep Cleaning
                    dateOfService: '',
                    durationOfService: '',
                    propertyType: 'apartment', // Default: Apartment/Flat

                    // Step 2
                    propertyType2: '',
                    floors: 1,
                    rooms: 1,
                    peoplePerRoom: 1,
                    floorArea: 0,
                    areaUnit: 'sqm', // Default: Square Meter
                    propertyLocation: '',
                    postalCode: '',
                    municipality: '',
                    region: '',
                    latitude: null,
                    longitude: null,

                    // Step 3
                    companyName: '',
                    clientName: '',
                    phoneNumber: '',
                    email: ''
                },

                init() {
                    // Watch for booking type changes to clear conditional fields
                    this.$watch('formData.bookingType', (newValue, oldValue) => {
                        if (oldValue && newValue !== oldValue) {
                            console.log('Booking type changed from', oldValue, 'to', newValue);

                            // Clear personal-only fields when switching to company
                            if (newValue === 'company') {
                                this.formData.dateOfService = '';
                                this.formData.durationOfService = '';
                                this.formData.propertyType = 'apartment'; // Keep default

                                // Clear the hidden inputs for personal-only fields
                                const form = document.getElementById('quotation-form');
                                const dateInput = form.querySelector('input[name="date_of_service"]');
                                if (dateInput) dateInput.value = '';

                                // Clear urgency dropdown hidden input
                                const urgencyInputs = form.querySelectorAll('input[type="hidden"][name="cleaning_service"]');
                                if (urgencyInputs.length > 1) urgencyInputs[1].value = '';

                                console.log('Cleared personal-only fields');
                            }

                            // Clear company name when switching to personal
                            if (newValue === 'personal') {
                                this.formData.companyName = '';
                                // Set default cleaning service for personal bookings
                                this.formData.cleaningServices = ['deep_cleaning'];
                                console.log('Cleared company-only fields and set default service');
                            }

                            // Clear cleaning services when switching to company
                            if (newValue === 'company') {
                                this.formData.cleaningServices = [];
                            }

                            // Uncheck all cleaning service checkboxes
                            const checkboxes = document.querySelectorAll('input[type="checkbox"][value]');
                            checkboxes.forEach(cb => cb.checked = false);
                        }
                    });
                },

                toggleCleaningService(serviceValue) {
                    const index = this.formData.cleaningServices.indexOf(serviceValue);
                    if (index > -1) {
                        // Remove if already selected
                        this.formData.cleaningServices.splice(index, 1);
                    } else {
                        // Add if not selected
                        this.formData.cleaningServices.push(serviceValue);
                    }
                },

                handleLocationSelected(detail) {
                    console.log('Location selected:', detail);
                    if (detail) {
                        this.formData.propertyLocation = detail.address || detail.streetAddress || '';
                        this.formData.postalCode = detail.postalCode || '';
                        this.formData.municipality = detail.city || '';
                        this.formData.region = detail.district || '';
                        this.formData.latitude = detail.latitude || null;
                        this.formData.longitude = detail.longitude || null;
                    }
                },

                handleOptionChanged(detail) {
                    console.log('Location option changed:', detail);
                    // Handle when user switches between "Use Current Location" and "Select a Location"
                },

                openLocationPicker() {
                    this.showLocationPicker = true;
                    setTimeout(() => {
                        if (typeof initMap === 'function') {
                            initMap();
                        }
                    }, 100);
                },

                confirmLocation() {
                    // Check if location is selected
                    if (!this.formData.propertyLocation) {
                        alert('Please select a location on the map first.');
                        return;
                    }

                    // Close the modal
                    this.showLocationPicker = false;

                    // Show confirmation
                    const locationSummary = [];
                    if (this.formData.propertyLocation) locationSummary.push(`Location: ${this.formData.propertyLocation}`);
                    if (this.formData.municipality) locationSummary.push(`City: ${this.formData.municipality}`);
                    if (this.formData.region) locationSummary.push(`Region: ${this.formData.region}`);
                    if (this.formData.postalCode) locationSummary.push(`Postal Code: ${this.formData.postalCode}`);

                    console.log('Location confirmed:', this.formData);
                },

                getCurrentLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition((position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            this.formData.latitude = lat;
                            this.formData.longitude = lng;

                            // Show loading state
                            const originalText = this.formData.propertyLocation;
                            this.formData.propertyLocation = 'Getting your location...';

                            // Reverse geocode to get address
                            if (typeof geocoder !== 'undefined' && geocoder) {
                                geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                                    if (status === 'OK' && results[0]) {
                                        // Set full address
                                        this.formData.propertyLocation = results[0].formatted_address;

                                        // Extract and set address components
                                        const addressComponents = results[0].address_components;

                                        // Reset fields first
                                        this.formData.postalCode = '';
                                        this.formData.municipality = '';
                                        this.formData.region = '';

                                        addressComponents.forEach(component => {
                                            // Postal Code
                                            if (component.types.includes('postal_code')) {
                                                this.formData.postalCode = component.long_name;
                                            }
                                            // Municipality/City
                                            if (component.types.includes('locality')) {
                                                this.formData.municipality = component.long_name;
                                            } else if (component.types.includes('administrative_area_level_2') && !this.formData.municipality) {
                                                this.formData.municipality = component.long_name;
                                            }
                                            // Region
                                            if (component.types.includes('administrative_area_level_1')) {
                                                this.formData.region = component.long_name;
                                            }
                                        });

                                        // Force Alpine to update the UI
                                        this.$nextTick(() => {
                                            console.log('Location updated:', {
                                                location: this.formData.propertyLocation,
                                                postal: this.formData.postalCode,
                                                city: this.formData.municipality,
                                                region: this.formData.region
                                            });
                                        });

                                        alert('Location found! Address fields have been auto-filled.');
                                    } else {
                                        this.formData.propertyLocation = originalText;
                                        console.error('Geocoder failed:', status);
                                        alert('Unable to get address from coordinates. Please try again.');
                                    }
                                });
                            } else {
                                this.formData.propertyLocation = originalText;
                                alert('Geocoder not initialized. Please try again.');
                            }
                        }, (error) => {
                            console.error('Error getting location:', error);
                            let errorMessage = 'Unable to retrieve your location. ';

                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMessage += 'Please allow location access in your browser settings.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMessage += 'Location information is unavailable.';
                                    break;
                                case error.TIMEOUT:
                                    errorMessage += 'Location request timed out.';
                                    break;
                                default:
                                    errorMessage += 'An unknown error occurred.';
                            }

                            alert(errorMessage);
                        });
                    } else {
                        alert('Geolocation is not supported by your browser.');
                    }
                },

                async submitForm() {
                    // Basic validation
                    if (!this.formData.bookingType) {
                        alert('Please select a booking type (Personal or Company)');
                        scrollToSection('step-1');
                        return;
                    }

                    if (!this.formData.clientName || !this.formData.phoneNumber || !this.formData.email) {
                        alert('Please fill in all required contact information');
                        scrollToSection('step-3');
                        return;
                    }

                    // Get the submit button
                    const submitButton = document.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;

                    // Show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Submitting...';

                    try {
                        // Collect data from form fields directly
                        const form = document.getElementById('quotation-form');

                        // Get cleaning service (from dropdown or checkboxes)
                        let cleaningServices = this.formData.cleaningServices;
                        if (cleaningServices.length === 0 && this.formData.bookingType === 'personal') {
                            // Try to get from hidden input (service-dropdown component)
                            const serviceInput = form.querySelector('input[type="hidden"][name="cleaning_service"]');
                            if (serviceInput && serviceInput.value) {
                                cleaningServices = [serviceInput.value];
                            } else {
                                // Fallback to default if still empty
                                cleaningServices = ['deep_cleaning'];
                            }
                        }

                        // Get property type from hidden input (service-dropdown component)
                        // Fallback to formData default if not selected
                        const propertyTypeInput = form.querySelector('input[type="hidden"][name="property_type"]');
                        const propertyType = (propertyTypeInput && propertyTypeInput.value) ? propertyTypeInput.value : this.formData.propertyType;

                        // Get area unit from hidden input (regular-dropdown component)
                        // Fallback to formData default if not selected
                        const areaUnitInput = form.querySelector('input[type="hidden"][name="area_units"]');
                        const areaUnit = (areaUnitInput && areaUnitInput.value) ? areaUnitInput.value : this.formData.areaUnit;

                        // Get date of service (only if it's a valid date string)
                        const dateInput = form.querySelector('input[name="date_of_service"]');
                        let dateOfService = null;
                        if (dateInput && dateInput.value && dateInput.value.match(/^\d{4}-\d{2}-\d{2}$/)) {
                            dateOfService = dateInput.value;
                        }

                        // Get urgency type - need to find the SECOND hidden input named cleaning_service
                        // (This is a form bug - two fields share the same name)
                        const allCleaningInputs = form.querySelectorAll('input[type="hidden"][name="cleaning_service"]');
                        const typeOfUrgency = allCleaningInputs.length > 1 ? allCleaningInputs[1].value : null;

                        // Collect all number inputs for property details
                        const numberInputs = form.querySelectorAll('input[type="number"]');
                        let floors = 1, rooms = 1, peoplePerRoom = 1, floorArea = 0, durationOfService = null;

                        // Get the values in order they appear in the form
                        numberInputs.forEach((input, index) => {
                            const value = parseInt(input.value) || 0;
                            const label = input.closest('.space-y-2')?.querySelector('label')?.textContent?.trim() || '';

                            console.log(`Number input ${index}: value=${value}, label=${label}`);

                            // Try to identify by label text or position
                            if (label.includes('Duration')) {
                                durationOfService = value;
                            } else if (label.includes('Floor') && label.includes('Number')) {
                                floors = value || 1;
                            } else if (label.includes('Room') && !label.includes('People')) {
                                rooms = value || 1;
                            } else if (label.includes('People')) {
                                peoplePerRoom = value || 1;
                            } else if (label.includes('Floor Area') || label.includes('Area')) {
                                floorArea = value;
                            } else {
                                // Fallback to positional logic
                                if (index === 0 && !durationOfService) durationOfService = value;
                                else if (index === 1 || (!floors && floors === 1)) floors = value || 1;
                                else if (index === 2 || (!rooms && rooms === 1)) rooms = value || 1;
                                else if (index === 3) peoplePerRoom = value || 1;
                                else if (index === 4) floorArea = value;
                            }
                        });

                        // Get location data from the location component's hidden inputs
                        const locationStreetInput = form.querySelector('input[name="property_location_street"]');
                        const locationPostalInput = form.querySelector('input[name="property_location_postal"]');
                        const locationCityInput = form.querySelector('input[name="property_location_city"]');
                        const locationDistrictInput = form.querySelector('input[name="property_location_district"]');
                        const locationLatInput = form.querySelector('input[name="property_location_latitude"]');
                        const locationLngInput = form.querySelector('input[name="property_location_longitude"]');

                        const streetAddress = locationStreetInput?.value || this.formData.propertyLocation || null;
                        const postalCode = locationPostalInput?.value || this.formData.postalCode || null;
                        const city = locationCityInput?.value || this.formData.municipality || null;
                        const district = locationDistrictInput?.value || this.formData.region || null;
                        const latitude = locationLatInput?.value ? parseFloat(locationLatInput.value) : (this.formData.latitude || null);
                        const longitude = locationLngInput?.value ? parseFloat(locationLngInput.value) : (this.formData.longitude || null);

                        // Prepare form data for submission
                        const submitData = {
                            // Step 1: Service Information
                            bookingType: this.formData.bookingType,
                            cleaningServices: cleaningServices,
                            // Only include date/duration/urgency for personal bookings
                            dateOfService: this.formData.bookingType === 'personal' ? (dateOfService || null) : null,
                            durationOfService: this.formData.bookingType === 'personal' ? (durationOfService || null) : null,
                            typeOfUrgency: this.formData.bookingType === 'personal' ? (typeOfUrgency || null) : null,

                            // Step 2: Property Information
                            propertyType: propertyType,
                            floors: floors,
                            rooms: rooms,
                            peoplePerRoom: peoplePerRoom,
                            floorArea: floorArea > 0 ? floorArea : null,
                            areaUnit: areaUnit,

                            // Property Location
                            locationType: 'address',
                            streetAddress: streetAddress,
                            postalCode: postalCode,
                            city: city,
                            district: district,
                            latitude: latitude,
                            longitude: longitude,

                            // Step 3: Contact Information
                            companyName: this.formData.companyName || null,
                            clientName: this.formData.clientName,
                            phoneNumber: this.formData.phoneNumber,
                            email: this.formData.email
                        };

                        // Log the form data before submission
                        console.log('=== FORM DATA DEBUG ===');
                        console.log('Raw formData object:', JSON.stringify(this.formData, null, 2));
                        console.log('Submit data being sent:', JSON.stringify(submitData, null, 2));
                        console.log('======================');

                        // Send POST request
                        const response = await fetch('{{ route('quotation.submit') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(submitData)
                        });

                        const result = await response.json();

                        // Log full response for debugging
                        console.log('Response status:', response.status);
                        console.log('Response data:', result);

                        if (response.ok) {
                            // Success!
                            alert('✅ ' + (result.message || 'Your quotation request has been submitted successfully! We will contact you soon.'));

                            // Reset the HTML form to clear all inputs
                            const form = document.getElementById('quotation-form');
                            form.reset();

                            // Reset Alpine.js formData
                            this.formData = {
                                bookingType: '',
                                cleaningServices: ['deep_cleaning'], // Default: Deep Cleaning
                                dateOfService: '',
                                durationOfService: '',
                                propertyType: 'apartment', // Default: Apartment/Flat
                                propertyType2: '',
                                floors: 1,
                                rooms: 1,
                                peoplePerRoom: 1,
                                floorArea: 0,
                                areaUnit: 'sqm', // Default: Square Meter
                                propertyLocation: '',
                                postalCode: '',
                                municipality: '',
                                region: '',
                                latitude: null,
                                longitude: null,
                                companyName: '',
                                clientName: '',
                                phoneNumber: '',
                                email: ''
                            };

                            // Clear all hidden inputs from components
                            form.querySelectorAll('input[type="hidden"]').forEach(input => {
                                input.value = '';
                            });

                            // Uncheck all radio buttons and checkboxes
                            form.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(input => {
                                input.checked = false;
                            });

                            // Reset all number inputs to their default values
                            form.querySelectorAll('input[type="number"]').forEach(input => {
                                input.value = input.min || 1;
                            });

                            // Clear all text and email inputs
                            form.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="date"]').forEach(input => {
                                input.value = '';
                            });

                            console.log('Form completely reset');

                            // Scroll to top
                            scrollToSection('step-1');
                        } else {
                            // Handle validation errors
                            if (result.errors) {
                                const errorMessages = Object.values(result.errors).flat().join('\n');
                                alert('❌ Please fix the following errors:\n\n' + errorMessages);
                            } else {
                                alert('❌ ' + (result.message || 'Failed to submit quotation. Please try again.'));
                            }
                        }
                    } catch (error) {
                        console.error('Submission error:', error);
                        alert('❌ An error occurred while submitting your request. Please check your internet connection and try again.');
                    } finally {
                        // Restore button state
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                    }
                }
            }
        }

        // Scroll to section function
        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            const container = document.getElementById('form-scroll-container');

            if (section && container) {
                const sectionTop = section.offsetTop - container.offsetTop;
                container.scrollTo({
                    top: sectionTop - 20,
                    behavior: 'smooth'
                });
            }
        }

        // Google Maps variables
        let map;
        let marker;
        let geocoder;
        let autocomplete;

        // Initialize Google Maps
        function initMap() {
            // Default to Manila, Philippines
            const defaultLocation = { lat: 14.5995, lng: 120.9842 };

            const mapElement = document.getElementById('map');
            if (!mapElement) return;

            // Check if we already have a location from Alpine component
            const formElement = document.querySelector('[x-data]');
            let initialLocation = defaultLocation;

            if (formElement && formElement.__x) {
                const component = formElement.__x.$data;
                if (component.formData.latitude && component.formData.longitude) {
                    initialLocation = {
                        lat: component.formData.latitude,
                        lng: component.formData.longitude
                    };
                }
            }

            // Initialize geocoder
            if (!geocoder) {
                geocoder = new google.maps.Geocoder();
            }

            // Create map
            map = new google.maps.Map(mapElement, {
                center: initialLocation,
                zoom: 15,
                styles: [
                    {
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{ visibility: 'off' }]
                    }
                ]
            });

            // Create marker
            marker = new google.maps.Marker({
                position: initialLocation,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP
            });

            // If we have a pre-existing location, update the address
            if (initialLocation !== defaultLocation) {
                geocodeLocation(new google.maps.LatLng(initialLocation.lat, initialLocation.lng));
            }

            // Setup autocomplete
            const searchInput = document.getElementById('location-search');
            if (searchInput) {
                autocomplete = new google.maps.places.Autocomplete(searchInput, {
                    componentRestrictions: { country: 'ph' },
                    fields: ['address_components', 'geometry', 'formatted_address']
                });

                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (place.geometry) {
                        const location = place.geometry.location;
                        map.setCenter(location);
                        marker.setPosition(location);
                        updateFormAddress(place);
                    }
                });
            }

            // Add click listener to map
            map.addListener('click', (event) => {
                marker.setPosition(event.latLng);
                geocodeLocation(event.latLng);
            });

            // Add drag listener to marker
            marker.addListener('dragend', () => {
                geocodeLocation(marker.getPosition());
            });
        }

        // Geocode location and update form
        function geocodeLocation(latLng) {
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    updateFormAddress(results[0]);
                }
            });
        }

        // Update form with address data
        function updateFormAddress(place) {
            const formElement = document.querySelector('[x-data]');
            if (formElement && formElement.__x) {
                const component = formElement.__x.$data;

                // Set the full address
                component.formData.propertyLocation = place.formatted_address || '';

                // Set coordinates
                if (place.geometry && place.geometry.location) {
                    component.formData.latitude = typeof place.geometry.location.lat === 'function'
                        ? place.geometry.location.lat()
                        : place.geometry.location.lat;
                    component.formData.longitude = typeof place.geometry.location.lng === 'function'
                        ? place.geometry.location.lng()
                        : place.geometry.location.lng;
                }

                // Reset fields before populating
                component.formData.postalCode = '';
                component.formData.municipality = '';
                component.formData.region = '';

                // Extract and populate address components
                if (place.address_components) {
                    place.address_components.forEach(addressComponent => {
                        const types = addressComponent.types;

                        // Postal Code
                        if (types.includes('postal_code')) {
                            component.formData.postalCode = addressComponent.long_name;
                        }

                        // Municipality/City - try locality first, then administrative_area_level_2
                        if (types.includes('locality')) {
                            component.formData.municipality = addressComponent.long_name;
                        } else if (types.includes('administrative_area_level_2') && !component.formData.municipality) {
                            component.formData.municipality = addressComponent.long_name;
                        }

                        // Region - administrative_area_level_1 (Province/State)
                        if (types.includes('administrative_area_level_1')) {
                            component.formData.region = addressComponent.long_name;
                        }
                    });
                }

                // Force Alpine.js to detect changes
                if (component.$nextTick) {
                    component.$nextTick(() => {
                        console.log('Address fields updated:', {
                            location: component.formData.propertyLocation,
                            postal: component.formData.postalCode,
                            city: component.formData.municipality,
                            region: component.formData.region,
                            lat: component.formData.latitude,
                            lng: component.formData.longitude
                        });
                    });
                }
            }
        }

        // Scroll Spy - Update breadcrumb based on visible section
        function initScrollSpy() {
            const container = document.getElementById('form-scroll-container');
            const sections = document.querySelectorAll('.scroll-section');
            const breadcrumbs = {
                'step-1': document.getElementById('breadcrumb-step-1'),
                'step-2': document.getElementById('breadcrumb-step-2'),
                'step-3': document.getElementById('breadcrumb-step-3')
            };
            const arrows = {
                'arrow-1': document.getElementById('arrow-1'),
                'arrow-2': document.getElementById('arrow-2')
            };

            if (!container || sections.length === 0) return;

            function updateBreadcrumb() {
                const containerRect = container.getBoundingClientRect();
                const containerTop = containerRect.top;
                const containerHeight = containerRect.height;
                const viewportMiddle = containerTop + (containerHeight / 3); // Top third of viewport

                let activeSection = null;

                sections.forEach((section) => {
                    const rect = section.getBoundingClientRect();
                    const sectionTop = rect.top;
                    const sectionBottom = rect.bottom;

                    // Check if section is in the top third of the viewport
                    if (sectionTop <= viewportMiddle && sectionBottom > containerTop) {
                        activeSection = section.id;
                    }
                });

                // Update breadcrumb styling
                Object.keys(breadcrumbs).forEach((key, index) => {
                    const breadcrumb = breadcrumbs[key];
                    const span = breadcrumb.querySelector('span');
                    const stepNumber = index + 1;

                    if (key === activeSection) {
                        // Active step
                        span.className = 'text-sm font-medium transition-colors text-gray-900 dark:text-white';
                        breadcrumb.classList.add('breadcrumb-active');
                    } else if (isStepCompleted(activeSection, key)) {
                        // Completed step
                        span.className = 'text-sm font-medium transition-colors text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white';
                        breadcrumb.classList.remove('breadcrumb-active');
                    } else {
                        // Future step
                        span.className = 'text-sm font-medium transition-colors text-gray-400 dark:text-gray-500';
                        breadcrumb.classList.remove('breadcrumb-active');
                    }
                });

                // Update arrows
                const activeStepNum = activeSection ? parseInt(activeSection.split('-')[1]) : 1;

                if (arrows['arrow-1']) {
                    arrows['arrow-1'].className = activeStepNum > 1
                        ? 'fa-solid fa-chevron-right text-xs transition-colors text-gray-600 dark:text-gray-400'
                        : 'fa-solid fa-chevron-right text-xs transition-colors text-gray-300 dark:text-gray-600';
                }

                if (arrows['arrow-2']) {
                    arrows['arrow-2'].className = activeStepNum > 2
                        ? 'fa-solid fa-chevron-right text-xs transition-colors text-gray-600 dark:text-gray-400'
                        : 'fa-solid fa-chevron-right text-xs transition-colors text-gray-300 dark:text-gray-600';
                }
            }

            function isStepCompleted(activeStep, checkStep) {
                const activeNum = activeStep ? parseInt(activeStep.split('-')[1]) : 1;
                const checkNum = parseInt(checkStep.split('-')[1]);
                return checkNum < activeNum;
            }

            // Add scroll listener
            container.addEventListener('scroll', updateBreadcrumb);

            // Initial update
            setTimeout(updateBreadcrumb, 100);
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            initScrollSpy();

            // Initialize geocoder for current location feature
            if (typeof google !== 'undefined' && google.maps) {
                geocoder = new google.maps.Geocoder();
            }
        });
        // Scroll tooltip management
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainer = document.getElementById('form-scroll-container');
    const scrollTooltip = document.getElementById('scroll-tooltip');
    
    if (scrollContainer && scrollTooltip) {
        let autoHideTimer = null;
        
        // Function to check if user has reached the end
        function isAtBottom() {
            const scrollTop = scrollContainer.scrollTop;
            const scrollHeight = scrollContainer.scrollHeight;
            const clientHeight = scrollContainer.clientHeight;
            
            // Consider "end" as within 50px of the bottom
            return (scrollTop + clientHeight) >= (scrollHeight - 50);
        }
        
        // Function to check if user is at the top
        function isAtTop() {
            return scrollContainer.scrollTop <= 100; // Within 100px of top
        }
        
        // Function to hide tooltip
        function hideTooltip() {
            scrollTooltip.classList.remove('tooltip-fade-out');
            scrollTooltip.classList.add('tooltip-fade-out');
            
            setTimeout(() => {
                scrollTooltip.style.display = 'none';
            }, 500);
        }
        
        // Function to show tooltip
        function showTooltip() {
            scrollTooltip.style.display = 'flex';
            scrollTooltip.classList.remove('tooltip-fade-out');
            
            // Reset auto-hide timer
            clearTimeout(autoHideTimer);
            autoHideTimer = setTimeout(() => {
                if (!isAtBottom()) {
                    hideTooltip();
                }
            }, 5000);
        }
        
        // Handle scroll events
        scrollContainer.addEventListener('scroll', function() {
            if (isAtBottom()) {
                // Hide when at bottom
                hideTooltip();
                clearTimeout(autoHideTimer);
            } else if (isAtTop()) {
                // Show when back at top
                showTooltip();
            }
        });
        
        // Check if content is scrollable, if not, hide tooltip immediately
        setTimeout(() => {
            if (scrollContainer.scrollHeight <= scrollContainer.clientHeight) {
                scrollTooltip.style.display = 'none';
            } else {
                // Start initial auto-hide timer
                autoHideTimer = setTimeout(() => {
                    if (!isAtBottom()) {
                        hideTooltip();
                    }
                }, 5000);
            }
        }, 100);
    }
});
    </script>

    <!-- Google Maps API - Replace YOUR_API_KEY with your actual API key -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key', 'YOUR_API_KEY') }}&libraries=places&callback=Function.prototype"></script>
@endpush