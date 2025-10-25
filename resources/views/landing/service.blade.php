<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.5.1/flowbite.min.css" rel="stylesheet" />

</head>
<style>
    [data-carousel-slide-to][aria-current="true"] {
        background-color: #6366f1;
        /* blue-500 */
    }

    [data-carousel-slide-to] {
        background-color: #d1d5db;
        /* gray-300 */
    }

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

    * {
        color: #071957;
        font-family: 'fam-regular';
    }
</style>

<body>
    <!-- NAVBAR -->
    <header class="inset-x-0 top-0 z-50">
        <nav aria-label="Global" class="flex items-center justify-between p-6 lg:px-8">
            <div class="flex lg:flex-1">
                <a href="/" class="-m-1.5 p-1.5">
                    <span class="sr-only"></span>
                    <img src="/images/finnoys-text-logo.svg" alt="" class="h-20 w-auto">
                </a>
            </div>
            <div class="flex lg:hidden">
                <button type="button" command="show-modal" commandfor="mobile-menu"
                    class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-200">
                    <span class="sr-only">Open main menu</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon"
                        aria-hidden="true" class="size-6">
                        <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
                <div class="hidden lg:flex lg:gap-x-12">
                    <a href="/" class="text-sm/6 text-blue-950 hover:text-blue-600 hover:font-bold">Home</a>
                    <a href="/about" class="text-sm/6 text-blue-950 hover:text-blue-600 hover:font-bold">About</a>
                    <a href="/services" class="text-sm/6 text-blue-950 hover:text-blue-600 hover:font-bold">Services</a>
                    <a href="/pricing" class="text-sm/6 text-blue-950 hover:text-blue-600 hover:font-bold">Price Quotation</a>
                </div>
            <div class="hidden lg:flex lg:flex-1 lg:justify-end">
                <span aria-hidden="true"></span>
           </div>
        </nav>
        <el-dialog>
            <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
                <div tabindex="0" class="fixed inset-0 focus:outline-none">
                    <el-dialog-panel
                        class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-blue-100 bg-blend-color-multiply  p-6 sm:max-w-sm sm:ring-1 sm:ring-gray-100/10">
                        <div class="flex items-center justify-between">
                            <a href="/" class="-m-1.5 p-1.5">
                                <span class="sr-only">Fin-noys</span>
                                <img src="/images/finnoys-text-logo.svg" alt="" class="h-20 w-auto">
                            </a>
                            <button type="button" command="close" commandfor="mobile-menu"
                                class="-m-2.5 rounded-md p-2.5 text-gray-200">
                                <span class="sr-only">Close menu</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    data-slot="icon" aria-hidden="true" class="size-6">
                                    <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-6 flow-root">
                            <div class="-my-6 divide-y divide-white/10">
                                <div class="space-y-2 py-6">
                                    <a href="/"
                                        class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 hover:bg-blue-600/10">Home</a>
                                    <a href="/about"
                                        class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 hover:bg-blue-600/10">About</a>
                                    <a href="/services"
                                        class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 hover:bg-blue-600/10">Services</a>
                                    <a href="/pricing"
                                        class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 hover:bg-blue-600/10">Pricing
                                        Quotation</a>
                                </div>
                                <div class="py-6">
                                    <a href="#"
                                        class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 text-blue-950 hover:bg-blue-600/10">Log
                                        in</a>
                                </div>
                            </div>
                        </div>
                    </el-dialog-panel>
                </div>
            </dialog>
        </el-dialog>
    </header>
    <!-- NAVBAR -->

    <div id="main-container" class="m-24 mt-3">
        <!-- SERVICES -->
        <div id="container-1">
            <!-- SERVICES CAROUSEL -->
            <div id="indicators-carousel" class="relative w-full">
                <!-- Carousel wrapper -->
                <div class="relative h-[40rem] sm:h-[30rem] lg:h-[40rem] overflow-hidden rounded-xl">

                    <!-- Cover Page -->
                    <div class="duration-700 ease-in-out" data-carousel-item="active">
                        <div class="relative w-full h-full bg-cover bg-center flex flex-col justify-center items-center text-center px-6"
                            style="background-image: url('/images/backgrounds/services_carousel/cover-page.svg');">
                            <h3 class="text-base text-[#ffffff]">Our Services</h3>
                            <h2 class="text-5xl font-[fam-bold] text-[#ffffff] pt-6 pb-6 tracking-tight">What do we
                                offer?</h2>
                            <p class="mt-4 text-sm text-[#ffffff] max-w-2xl text-center">
                                Discover our range of professional cleaning services designed to make your home or
                                business shine
                            </p>
                            <a href="#"
                                class="mt-12 text-sm inline-block bg-[#ffffff94] text-[#006ce7] font-[fam-bold] px-9 py-3 rounded-full hover:bg-indigo-400 transition">
                                Explore Services
                            </a>
                        </div>
                    </div>

                    <!-- Item 1 -->
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <div class="relative w-full h-full bg-cover bg-center flex flex-col justify-center items-center text-center px-6"
                            style="background-image: url('/images/backgrounds/services_carousel/deep-cleaning.svg');">
                            <h3 class="text-base text-[#081032] pt-6">Estimated Price Range:
                                <span class="font-[fam-bold]">€ 120 - € 200</span>
                            </h3>
                            <h2 class="text-5xl font-[fam-bold] tracking-tight text-[#081032] pt-6 pb-6">Deep Cleaning
                            </h2>

                            <!-- Badges -->
                            <div id="badges-container" class="grid grid-cols-3 items-center gap-3 mt-3 mb-3">
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Linen Laundry
                                </span>
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Toilet Cleaning
                                </span>
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Window Cleaning
                                </span>
                                <span
                                    class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full col-span-2 justify-self-center">
                                    Interior Arrangement
                                </span>
                                <span class="px-6 py-1 rounded-full"></span>
                            </div>

                            <p class="mt-4 text-base text-[#081032] max-w-2xl text-justify">
                                A thorough, top-to-bottom cleaning that tackles dirt and grime in hard-to-reach places.
                                Perfect for spring cleaning or when your space needs a detailed refresh beyond
                                <span class="font-[fam-bold]">routine maintenance.</span>
                            </p>
                            <a href="#"
                                class="mt-12 text-sm inline-block bg-[#081032] text-white px-9 py-3 rounded-full shadow hover:bg-indigo-400 transition">
                                Book A Service
                            </a>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <div class="relative w-full h-full bg-cover bg-center flex flex-col justify-center items-center text-center px-6"
                            style="background-image: url('/images/backgrounds/services_carousel/daily-room-cleaning.svg');">
                            <h3 class="text-base text-[#081032] pt-6">Estimated Price Range:
                                <span class="font-[fam-bold]">€ 120 - € 200</span>
                            </h3>
                            <h2 class="text-5xl font-[fam-bold] text-[#081032] pt-6 pb-6">Daily Room Cleaning</h2>

                            <!-- Badges -->
                            <div id="badges-container"
                                class="grid grid-cols-3 items-center gap-3 mt-3 mb-3 justify-items-center">
                                <!-- Row 1 -->
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Light Cleaning
                                </span>
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Emptying Trash Bins
                                </span>
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Sweeping & Dusting
                                </span>
                                <!-- Row 2 (centered between 2nd and 3rd col) -->
                                <span
                                    class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full col-center-2 justify-self-center">
                                    Replacing Amenities
                                </span>
                                <span
                                    class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full col-center-1 justify-self-center">
                                    Restocking Amenities
                                </span>
                            </div>

                            <p class="mt-4 text-base text-[#081032] max-w-2xl text-justify">
                                Complete <span class="font-[fam-bold]">room refresh tailored for guest
                                    accommodations</span> . Includes bed-making, linen
                                replacement, surface dusting, bathroom sanitizing, floor cleaning, and replenishment of
                                amenities for a welcoming stay.

                            </p>
                            <a href="#"
                                class="mt-12 text-sm inline-block bg-[#081032] text-white px-9 py-3 rounded-full shadow hover:bg-indigo-400 transition">
                                Book A Service
                            </a>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <div class="relative w-full h-full bg-cover bg-center flex flex-col justify-center items-center text-center px-6"
                            style="background-image: url('/images/backgrounds/services_carousel/snow-out-cleaning.svg');">
                            <h3 class="text-base text-[#081032] pt-6">Estimated Price Range:
                                <span class="font-[fam-bold]">€ 120 - € 200</span>
                            </h3>
                            <h2 class="text-5xl font-[fam-bold] text-[#081032] pt-6 pb-6">Snow Out Cleaning</h2>

                            <!-- Badges -->
                            <div id="badges-container"
                                class="grid grid-cols-3 items-center gap-3 mt-3 mb-3 justify-items-center">
                                <!-- Row 1 -->
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Light Cleaning
                                </span>
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Emptying Trash Bins
                                </span>
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Sweeping & Dusting
                                </span>
                                <!-- Row 2 (centered between 2nd and 3rd col) -->
                                <span
                                    class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full col-center-2 justify-self-center">
                                    Replacing Amenities
                                </span>
                                <span
                                    class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full col-center-1 justify-self-center">
                                    Restocking Amenities
                                </span>
                            </div>

                            <p class="mt-4 text-base text-[#081032] max-w-2xl text-justify">
                                Complete <span class="font-[fam-bold]">room refresh tailored for guest
                                    accommodations</span> . Includes bed-making, linen
                                replacement, surface dusting, bathroom sanitizing, floor cleaning, and replenishment of
                                amenities for a welcoming stay.

                            </p>
                            <a href="#"
                                class="mt-12 text-sm inline-block bg-[#081032] text-white px-9 py-3 rounded-full shadow hover:bg-indigo-400 transition">
                                Book A Service
                            </a>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <div class="relative w-full h-full bg-cover bg-center flex flex-col justify-center items-center text-center px-6"
                            style="background-image: url('/images/backgrounds/services_carousel/light-daily-cleaning.svg');">
                            <h3 class="text-base text-[#081032] pt-6">Estimated Price Range:
                                <span class="font-[fam-bold]">€ 120 - € 200</span>
                            </h3>
                            <h2 class="text-5xl font-[fam-bold] text-[#081032] pt-6 pb-6">Light Daily Cleaning</h2>

                            <!-- Badges -->
                            <div id="badges-container"
                                class="grid grid-cols-3 items-center gap-3 mt-3 mb-3 justify-items-center">
                                <!-- Row 1 -->
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Light Cleaning
                                </span>
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Emptying Trash Bins
                                </span>
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Sweeping & Dusting
                                </span>
                                <!-- Row 2 (centered between 2nd and 3rd col) -->
                                <span
                                    class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full col-center-2 justify-self-center">
                                    Replacing Amenities
                                </span>
                                <span
                                    class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full col-center-1 justify-self-center">
                                    Restocking Amenities
                                </span>
                            </div>

                            <p class="mt-4 text-base text-[#081032] max-w-2xl text-justify">
                                Complete <span class="font-[fam-bold]">room refresh tailored for guest
                                    accommodations</span> . Includes bed-making, linen
                                replacement, surface dusting, bathroom sanitizing, floor cleaning, and replenishment of
                                amenities for a welcoming stay.

                            </p>
                            <a href="#"
                                class="mt-12 text-sm inline-block bg-[#081032] text-white px-9 py-3 rounded-full shadow hover:bg-indigo-400 transition">
                                Book A Service
                            </a>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <div class="relative w-full h-full bg-cover bg-center flex flex-col justify-center items-center text-center px-6"
                            style="background-image: url('/images/backgrounds/services_carousel/full-daily-cleaning.svg');">
                            <h3 class="text-base text-[#081032] pt-6">Estimated Price Range:
                                <span class="font-[fam-bold]">€ 120 - € 200</span>
                            </h3>
                            <h2 class="text-5xl font-[fam-bold] text-[#081032] pt-6 pb-6">Full Daily Cleaning</h2>

                            <!-- Badges -->
                            <div id="badges-container"
                                class="grid grid-cols-3 items-center gap-3 mt-3 mb-3 justify-items-center">
                                <!-- Row 1 -->
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Light Cleaning
                                </span>
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Emptying Trash Bins
                                </span>
                                <span class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full">
                                    Sweeping & Dusting
                                </span>
                                <!-- Row 2 (centered between 2nd and 3rd col) -->
                                <span
                                    class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full col-center-2 justify-self-center">
                                    Replacing Amenities
                                </span>
                                <span
                                    class="bg-[#ffffff60] text-gray-900 text-xs px-6 py-1 rounded-full col-center-1 justify-self-center">
                                    Restocking Amenities
                                </span>
                            </div>

                            <p class="mt-4 text-base text-[#081032] max-w-2xl text-justify">
                                Complete <span class="font-[fam-bold]">room refresh tailored for guest
                                    accommodations</span> . Includes bed-making, linen
                                replacement, surface dusting, bathroom sanitizing, floor cleaning, and replenishment of
                                amenities for a welcoming stay.

                            </p>
                            <a href="#"
                                class="mt-12 text-sm inline-block bg-[#081032] text-white px-9 py-3 rounded-full shadow hover:bg-indigo-400 transition">
                                Book A Service
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Indicators -->
                <div class="absolute z-30 flex -translate-x-1/2 space-x-3 rtl:space-x-reverse bottom-5 left-1/2">
                    <button type="button" class="w-1.5 h-1.5 rounded-full" aria-current="true"
                        data-carousel-slide-to="0"></button>
                    <button type="button" class="w-1.5 h-1.5 rounded-full" data-carousel-slide-to="1"></button>
                    <button type="button" class="w-1.5 h-1.5 rounded-full" data-carousel-slide-to="2"></button>
                    <button type="button" class="w-1.5 h-1.5 rounded-full" data-carousel-slide-to="3"></button>
                    <button type="button" class="w-1.5 h-1.5 rounded-full" data-carousel-slide-to="4"></button>
                </div>

                <!-- Controls -->
                <button type="button"
                    class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-prev>
                    <span
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 1 1 5l4 4" />
                        </svg>
                        <span class="sr-only">Previous</span>
                    </span>
                </button>
                <button type="button"
                    class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-next>
                    <span
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white">
                        <svg class="w-4 h-4 text-white rtl:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <span class="sr-only">Next</span>
                    </span>
                </button>
            </div>

            <!-- PRICING -->
            <div id="container-3">
                <!-- PRICING -->
                <div id="container-3" class="bg-white px-6 py-24 sm:py-32 lg:px-8 relative isolate">
                    <div class="mx-auto max-w-4xl text-center">
                        <h2 class="text-sm text-blue-600">Pricing</h2>
                        <p
                            class="p-12 pb-6 pt-6 justify-center mt-2 text-5xl font-[fam-bold] tracking-tight text-gray-900 sm:text-6xl">
                            Find the <span class="text-blue-600 font-[fam-bold]">best plan</span> for your cleaning
                            needs
                        </p>
                    </div>
                    <p class="mx-auto mt-6 max-w-3xl text-center text-lg text-gray-600 sm:text-xl">
                        Flexible cleaning plans designed to keep your space spotless — on your schedule, your way.
                    </p>

                    <!-- Pricing Cards -->
                    <div class="mx-auto mt-16 p-6 grid max-w-6xl grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- CARD TEMPLATE -->
                        <div
                            class="relative bg-white/60 h-[30rem] rounded-3xl p-8 ring-1 ring-gray-900/10 transition-all duration-300 hover:bg-gray-900 hover:text-white hover:scale-105 hover:z-[5] group shadow-sm hover:shadow-2xl">
                            <h3 class="text-base font-[fam-bold] text-blue-600 group-hover:text-blue-400">Contractual
                                Daily
                                Cleaning</h3>
                            <p class="mt-4 flex items-baseline gap-x-2">
                                <span
                                    class="text-5xl font-[fam-bold] tracking-tight text-gray-900 group-hover:text-white">$29</span>
                                <span class="text-base text-gray-500 group-hover:text-gray-300">/year</span>
                            </p>
                            <p class="mt-6 text-base text-gray-600 group-hover:text-gray-300">
                                Ideal for occasional home cleaning needs.
                            </p>
                            <ul role="list" class="mt-8 space-y-3 text-sm text-gray-600 group-hover:text-gray-300">
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none text-blue-600 group-hover:text-blue-400"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                                    </svg>
                                    25 products
                                </li>
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none text-blue-600 group-hover:text-blue-400"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                                    </svg>
                                    Up to 10,000 subscribers
                                </li>
                            </ul>
                            <a href="#"
                                class="mt-8 block rounded-full bg-blue-500 text-center text-sm text-white px-4 py-3.5 hover:bg-blue-400 transition">
                                Purchase
                            </a>
                        </div>

                        <!-- DUPLICATE 2 MORE TIMES -->
                        <div
                            class="relative bg-white/60 rounded-3xl p-8 ring-1 ring-gray-900/10 transition-all duration-300 hover:bg-gray-900 hover:text-white hover:scale-105 hover:z-[5] group shadow-sm hover:shadow-2xl">
                            <h3 class="text-base font-[fam-bold] text-blue-600 group-hover:text-blue-400">Interval
                                Cleaning
                            </h3>
                            <p class="mt-4 flex items-baseline gap-x-2">
                                <span
                                    class="text-5xl font-semibold tracking-tight text-gray-900 group-hover:text-white">$59</span>
                                <span class="text-base text-gray-500 group-hover:text-gray-300">/month</span>
                            </p>
                            <p class="mt-6 text-base text-gray-600 group-hover:text-gray-300">
                                Great for regular home or office cleaning services.
                            </p>
                            <ul role="list" class="mt-8 space-y-3 text-sm text-gray-600 group-hover:text-gray-300">
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none text-blue-600 group-hover:text-blue-400"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                                    </svg>
                                    25 products
                                </li>
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none text-blue-600 group-hover:text-blue-400"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                                    </svg>
                                    Up to 10,000 subscribers
                                </li>
                            </ul>

                            <a href="#"
                                class="mt-8 block rounded-full bg-blue-500 text-center text-sm text-white px-4 py-3.5 hover:bg-blue-400 transition">
                                Purchase
                            </a>
                        </div>

                        <div
                            class="relative bg-white/60 rounded-3xl p-8 ring-1 ring-gray-900/10 transition-all duration-300 hover:bg-gray-900 hover:text-white hover:scale-105 hover:z-[5] group shadow-sm hover:shadow-2xl">
                            <h3 class="text-base font-[fam-bold] text-blue-600 group-hover:text-blue-400">On-Call
                                Cleaning
                            </h3>
                            <p class="mt-4 flex items-baseline gap-x-2">
                                <span
                                    class="text-5xl font-semibold tracking-tight text-gray-900 group-hover:text-white">$99</span>
                                <span class="text-base text-gray-500 group-hover:text-gray-300">/week</span>
                            </p>
                            <p class="mt-6 text-base text-gray-600 group-hover:text-gray-300">
                                Complete cleaning service with 24/7 support and customization.
                            </p>
                            <ul role="list" class="mt-8 space-y-3 text-sm text-gray-600 group-hover:text-gray-300">
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none text-blue-600 group-hover:text-blue-400"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                                    </svg>
                                    25 products
                                </li>
                                <li class="flex gap-x-3">
                                    <svg class="h-6 w-5 flex-none text-blue-600 group-hover:text-blue-400"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M16.7 4.15a.75.75 0 0 1 .14 1.05l-8 10.5a.75.75 0 0 1-1.13.08l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.9 3.9 7.48-9.82a.75.75 0 0 1 1.05-.15Z" />
                                    </svg>
                                    Up to 10,000 subscribers
                                </li>
                            </ul>

                            <a href="#"
                                class="mt-8 block rounded-full bg-blue-500 text-center text-sm text-white px-4 py-3.5 hover:bg-blue-400 transition">
                                Purchase
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LOCATION -->
            <div id="container-2" class="mt-3 w-full">
                <section id="coverage-section"
                    class="relative rounded-xl w-full h-[90vh] flex flex-col items-center justify-center text-center text-white transition-all duration-500 bg-cover bg-center bg-white">

                    <!-- Content -->
                    <div class="relative z-10 px-6 max-w-3xl">
                        <p id="label-1" class="text-sm font-[fam-bold] text-[#2A6DFA] mt-6 pb-6">Our Service Coverage
                        </p>
                        <h1 id="city-name" class="text-6xl font-[fam-bold] text-[#071957] mt-6">Just a <span
                                class="text-[#2A6DFA] text-6xl font-[fam-bold]">click away</span>.</h1>
                        <p id="city-desc" class="text-sm sm:text-sm mt-6 leading-relaxed text-[#071957]">
                            This is one of the municipalities inside Lapland, on the northernmost region, taking up
                            roughly
                            the entire top third of the country, above the Arctic Circle.
                        </p>

                        <!-- Services -->
                        <div class="flex flex-col sm:flex-row justify-center gap-10 mt-8">
                            <div>
                                <p id="label-1" class="text-sm font-[fam-bold] mb-3 text-[#2A6DFA]">Available Services
                                </p>
                                <div id="services" class="flex flex-wrap justify-center gap-2">
                                    <span
                                        class="bg-[#2A6DFA25] text-[#071957] text-xs px-5 py-1 rounded-full">All</span>
                                </div>
                            </div>
                            <div>
                                <p id="label-1" class="text-sm font-[fam-bold] mb-3 text-[#2A6DFA]">Included Cities</p>
                                <div id="communities" class="flex flex-wrap justify-center gap-2">
                                    <span class="bg-[#2A6DFA25] text-[#071957] text-xs px-5 py-1 rounded-full">Lapland
                                        Region</span>
                                    <span class="bg-[#2A6DFA25] text-[#071957] text-xs px-5 py-1 rounded-full">Inari
                                        City</span>
                                    <span
                                        class="bg-[#2A6DFA25] text-[#071957] text-xs px-5 py-1 rounded-full">Saariselkä</span>
                                </div>
                            </div>
                        </div>

                        <!-- Clickable Cards -->
                        <div class="flex justify-center mt-12 gap-4">
                            <button onclick="switchCity('inari')" id="card-inari"
                                class="city-card active bg-[#ffffff80] text-[#081032] px-10 py-4 rounded-2xl shadow-lg flex items-center gap-3 transition duration-300 hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"
                                    class="w-5 h-5 text-[#081032]">
                                    <path fill-rule="evenodd"
                                        d="M8 0a5.53 5.53 0 0 0-5.5 5.5c0 3.038 4.05 8.008 5.047 9.202a.625.625 0 0 0 .906 0C9.45 13.508 13.5 8.538 13.5 5.5A5.53 5.53 0 0 0 8 0ZM8 7.5a2 2 0 1 1 0-4.001 2 2 0 0 1 0 4Z" />
                                </svg>
                                <div class="text-left">
                                    <h3 class="text-base font-[fam-bold] leading-tight">Inari City</h3>
                                    <p class="text-xs">See More →</p>
                                </div>
                            </button>

                            <button onclick="switchCity('lapland')" id="card-lapland"
                                class="city-card bg-[#ffffff30] text-white border border-white/40 px-10 py-4 rounded-2xl shadow-lg flex items-center gap-3 transition duration-300 hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"
                                    class="w-5 h-5 text-white">
                                    <path fill-rule="evenodd"
                                        d="M8 0a5.53 5.53 0 0 0-5.5 5.5c0 3.038 4.05 8.008 5.047 9.202a.625.625 0 0 0 .906 0C9.45 13.508 13.5 8.538 13.5 5.5A5.53 5.53 0 0 0 8 0ZM8 7.5a2 2 0 1 1 0-4.001 2 2 0 0 1 0 4Z" />
                                </svg>
                                <div class="text-left">
                                    <h3 class="text-base font-[fam-bold] leading-tight">Lapland Region</h3>
                                    <p class="text-xs">See More →</p>
                                </div>
                            </button>

                            <button onclick="switchCity('sar')" id="card-sar"
                                class="city-card bg-[#ffffff30] text-white border border-white/40 px-10 py-4 rounded-2xl shadow-lg flex items-center gap-3 transition duration-300 hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"
                                    class="w-5 h-5 text-white">
                                    <path fill-rule="evenodd"
                                        d="M8 0a5.53 5.53 0 0 0-5.5 5.5c0 3.038 4.05 8.008 5.047 9.202a.625.625 0 0 0 .906 0C9.45 13.508 13.5 8.538 13.5 5.5A5.53 5.53 0 0 0 8 0ZM8 7.5a2 2 0 1 1 0-4.001 2 2 0 0 1 0 4Z" />
                                </svg>
                                <div class="text-left">
                                    <h3 class="text-base font-[fam-bold] leading-tight">Saariselkä</h3>
                                    <p class="text-xs">See More →</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </section>
            </div>

        </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.5.1/flowbite.min.js"></script>
<script>
    // Simple custom carousel implementation
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('indicators-carousel');
        if (!carousel) return;

        const items = carousel.querySelectorAll('[data-carousel-item]');
        const prevBtn = carousel.querySelector('[data-carousel-prev]');
        const nextBtn = carousel.querySelector('[data-carousel-next]');
        const indicators = carousel.querySelectorAll('[data-carousel-slide-to]');

        let currentIndex = 0;
        let autoplayInterval;

        function showSlide(index) {
            // Hide all items
            items.forEach(item => {
                item.classList.add('hidden');
                item.classList.remove('block');
            });

            // Show current item
            if (items[index]) {
                items[index].classList.remove('hidden');
                items[index].classList.add('block');
            }

            // Update indicators
            indicators.forEach((indicator, i) => {
                if (i === index) {
                    indicator.setAttribute('aria-current', 'true');
                } else {
                    indicator.removeAttribute('aria-current');
                }
            });

            currentIndex = index;
        }

        function nextSlide() {
            const next = (currentIndex + 1) % items.length;
            showSlide(next);
        }

        function prevSlide() {
            const prev = (currentIndex - 1 + items.length) % items.length;
            showSlide(prev);
        }

        function startAutoplay() {
            stopAutoplay();
            autoplayInterval = setInterval(nextSlide, 5000);
        }

        function stopAutoplay() {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
            }
        }

        // Event listeners
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                prevSlide();
                startAutoplay(); // Restart autoplay after manual navigation
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                nextSlide();
                startAutoplay(); // Restart autoplay after manual navigation
            });
        }

        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                showSlide(index);
                startAutoplay(); // Restart autoplay after manual navigation
            });
        });

        // Initialize
        showSlide(0);
        startAutoplay();

        // Pause on hover
        carousel.addEventListener('mouseenter', stopAutoplay);
        carousel.addEventListener('mouseleave', startAutoplay);
    });

    const cities = {
        inari: {
            label: "",
            name: "Municipality of Inari",
            desc: "This is one of the municipalities inside Lapland, on the northernmost region, taking up roughly the entire top third of the country, above the Arctic Circle.",
            bg: "/images/backgrounds/inari-bg.svg",
            nameColor: "#ffffff",
            descColor: "#dbe2ff",
            services: ["All"],
            communities: ["Saariselkä", "Nellim", "Lemmenjoki", "Ivalo"],
        },
        lapland: {
            label: "",
            name: "Lapland Region",
            desc: "The capital of Lapland and the official hometown of Santa Claus, known for its vibrant urban life and Northern Lights views.",
            bg: "/images/backgrounds/lapland-region-bg.svg",
            nameColor: "#ffffff",
            descColor: "#dbe2ff",
            services: ["Home Cleaning", "Office Cleaning", "Snow Removal"],
            communities: ["Utsjoki", "Inari", "Saariselkä", "Kaamanen"],
        },
        sar: {
            label: "",
            name: "Saariselkä",
            desc: "In Lapland, with the shimmering northern lights dance above snow-covered fells, reindeer roam freely, and adventure awaits year-round through skiing, hiking, and the serene beauty of the Arctic wilderness.",
            bg: "/images/backgrounds/sar-bg.svg",
            nameColor: "#ffffff",
            descColor: "#dbe2ff",
            services: ["Home Cleaning", "Office Cleaning", "Snow Removal"],
            communities: ["Utsjoki", "Inari", "Saariselkä", "Kaamanen"],
        }
    };

    function switchCity(cityKey) {
        const city = cities[cityKey];
        const nameEl = document.getElementById("city-name");
        const descEl = document.getElementById("city-desc");
        const labelEl = document.getElementById("label-1");
        const coverageSection = document.getElementById("coverage-section");

        // Update text and background
        nameEl.textContent = city.name;
        descEl.textContent = city.desc;
        labelEl.textContent = city.label;
        coverageSection.style.backgroundImage = `url('${city.bg}')`;

        // Smooth transition for text
        [nameEl, descEl, labelEl].forEach(el => {
            if (el) el.style.transition = "color 0.5s ease";
        });

        // Apply colors dynamically
        nameEl.style.color = city.nameColor;
        descEl.style.color = city.descColor;
        if (labelEl) labelEl.style.color = city.descColor;

        // Update badges
        document.getElementById("services").innerHTML = city.services
            .map(s => `<span class='bg-[#ffffff30] text-white text-xs px-5 py-1 rounded-full'>${s}</span>`)
            .join("");
        document.getElementById("communities").innerHTML = city.communities
            .map(c => `<span class='bg-[#ffffff30] text-white text-xs px-5 py-1 rounded-full'>${c}</span>`)
            .join("");

        // Update card styles
        document.querySelectorAll(".city-card").forEach(btn => {
            btn.classList.remove("bg-[#ffffff80]", "text-[#081032]");
            btn.classList.add("bg-[#ffffff30]", "text-white", "border", "border-white/40");
            btn.querySelector("svg").classList.add("text-white");
        });

        const activeCard = document.getElementById(`card-${cityKey}`);
        activeCard.classList.add("bg-[#ffffff80]", "text-[#081032]");
        activeCard.classList.remove("bg-[#ffffff30]", "text-white", "border", "border-white/40");
        activeCard.querySelector("svg").classList.remove("text-white");
        activeCard.querySelector("svg").classList.add("text-[#081032]");
    }
</script>



</html>