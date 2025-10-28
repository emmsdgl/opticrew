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
            width: 8px;
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
        <section id="container-2" class="flex flex-col">
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
                                @endphp

                                <x-client-components.quotation-page.service-dropdown label="Type of Cleaning Service"
                                    name="cleaning_service" :options="$cleaningServices" :required="true"
                                    :placeholdericon="'fa-solid fa-broom'" />
                            </div>


                            <!-- Date and Duration -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                <!-- Date of Service -->
                                <div class="space-y-2">
                                    @php
                                        $today = date('Y-m-d');
                                    @endphp

                                    <x-client-components.quotation-page.service-datepicker label="Date of Service"
                                        name="date_of_service" x-model="formData.dateOfService" :required="true"
                                        :min-date="$today" placeholder="What is your preferred date?" />

                                </div>


                                <!-- Duration of Service -->
                                <div class="space-y-2">
                                    <x-client-components.quotation-page.quantity-picker label="Duration of Service"
                                        name="rooms" :min="1" :max="20" :default="1" icon="fa-solid fa-door-open"
                                        :required="true" :showUnit="false" />

                                </div>
                            </div>

                            <!-- Type of urgency -->
                            <div class="space-y-3">
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
                                        name="cleaning_service" :options="$urgencyType" :required="true"
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
                                        name="rooms" :min="1" :max="20" :default="1" icon="fa-solid fa-door-open"
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
                                        name="rooms" :min="1" :max="20" :default="1" icon="fa-solid fa-door-open"
                                        :required="true" :showUnit="false" />
                                </div>
                            </div>

                            <!-- Floor Area Value and Unit -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                <div class="space-y-2">
                                    <x-client-components.quotation-page.quantity-picker label="Floor Area Value"
                                        name="rooms" :min="1" :max="20" :default="1" icon="fa-solid fa-door-open"
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

                            <!-- Client Name -->
                            <div class="space-y-2">
                                <label class="block text-sm text-gray-700 dark:text-gray-300">
                                    Client Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" x-model="formData.clientName" placeholder="Enter your name"
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
                                    <i
                                        class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="email" x-model="formData.email"
                                        placeholder="Where should we send the quotation?"
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
@push('scripts')
    <script>
        function quotationForm() {
            return {
                showLocationPicker: false,
                formData: {
                    // Step 1
                    bookingType: '',
                    cleaningServices: [],
                    dateOfService: '',
                    durationOfService: '',
                    propertyType: '',

                    // Step 2
                    propertyType2: '',
                    floors: 1,
                    rooms: 1,
                    peoplePerRoom: 1,
                    floorArea: 0,
                    areaUnit: '',
                    propertyLocation: '',
                    postalCode: '',
                    municipality: '',
                    region: '',
                    latitude: null,
                    longitude: null,

                    // Step 3
                    clientName: '',
                    email: ''
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

                submitForm() {
                    console.log('Form Data:', this.formData);
                    alert('Quotation submitted successfully! We will contact you soon.');
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

            // Check for saved theme preference
            const currentTheme = localStorage.getItem('theme') || 'light';
            if (currentTheme === 'dark') {
                document.documentElement.classList.add('dark');
            }

            // Initialize geocoder for current location feature
            if (typeof google !== 'undefined' && google.maps) {
                geocoder = new google.maps.Geocoder();
            }
        });
    </script>

    <!-- Google Maps API - Replace YOUR_API_KEY with your actual API key -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key', 'YOUR_API_KEY') }}&libraries=places&callback=Function.prototype"></script>
@endpush