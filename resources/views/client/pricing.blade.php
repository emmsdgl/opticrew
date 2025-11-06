<x-layouts.general-client :title="'Pricing'">
    <div class="flex flex-col w-full min-h-[calc(100vh-4rem)] font-sans">
        <!-- Hero Section with Gradient Background -->
        <div class="relative isolate px-6 sm:py-32 lg:px-8 lg:pb-32 overflow-hidden">
            <div class="mx-auto max-w-4xl text-center fade-in">
                <h2 class="text-base/7 font-semibold text-blue-600 dark:text-blue-600">Choose your clean</h2>
                <h3
                    class="my-12 text-5xl md:text-5xl lg:text-5xl font-bold tracking-tight text-[#081032] dark:text-white">
                    Shine brighter with <br><span class="text-blue-600">our cleaning services</span>
                </h3>
                <p class="mx-auto mt-6 max-w-2xl text-center text-base md:text-base text-gray-500 dark:text-gray-300">
                    Professional cleaning services tailored to your space. No hidden fees, transparent pricing, just
                    quality service.
                </p>
                <a href="{{ route('client.appointment.create') }}"
                    class="inline-flex text-sm items-center mt-8 px-8 py-3 bg-blue-600 text-white dark:bg-blue-500 dark:text-white rounded-lg font-semibold hover:bg-blue-50 dark:hover:bg-indigo-400 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fi fi-rr-calendar-plus mr-4"></i>Book a Service Now
                </a>
            </div>
        </div>

        <!-- Main Pricing Cards Section -->
        <div class="px-4 md:px-6 lg:px-8 -mt-16 relative z-10">
            <div class="mx-auto max-w-7xl">
                <!-- Main Pricing Cards Section -->
                <div class="relative isolate bg-white dark:bg-gray-900">
                    <div
                        class="mx-auto mt-6 grid max-w-lg grid-cols-1 items-center gap-y-6 sm:mt-6 sm:gap-y-0 lg:max-w-4xl lg:grid-cols-2">
                        <div
                            class="rounded-3xl rounded-t-3xl bg-white/2.5 p-8 ring-1 ring-white/10 sm:mx-8 sm:rounded-b-none sm:p-10 lg:mx-0 lg:rounded-tr-none lg:rounded-bl-3xl">
                            <span
                                class="px-3 py-1 text-blue-600 dark:text-white bg-blue-600/20 dark:bg-blue-600/30 rounded-full text-xs">Most
                                Popular</span>

                            <h3 id="tier-finalcleaning" class="text-base/7 font-bold text-blue-500 my-6">Final Cleaning
                            </h3>
                            <p class="mt-4 flex items-baseline gap-x-2">
                                <span class="text-5xl font-bold tracking-tight text-blue-600 dark:text-white">€70-
                                    €315</span>
                            </p>
                            <p class="mt-6 text-base/7 text-gray-500 dark:text-gray-300">Complete cleaning solution
                                perfect
                                for regular maintenance and move-out situations.

                            </p>
                            <p class="mt-6 text-base/7 text-gray-500 dark:text-gray-300">Based on unit size</p>
                            <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-500 dark:text-gray-300 sm:mt-10">
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                        class="h-6 w-5 flex-none text-blue-600">
                                        <path
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                    Kitchen cleaning & surfaces
                                </li>
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                        class="h-6 w-5 flex-none text-blue-600">
                                        <path
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                    Living room & bedroom tidying
                                </li>
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                        class="h-6 w-5 flex-none text-blue-600">
                                        <path
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                    Bathroom & sauna cleaning
                                </li>
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                        class="h-6 w-5 flex-none text-blue-600">
                                        <path
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                    Vacuuming & mopping floors
                                </li>
                            </ul>
                            <a href="{{ route('client.appointment.create') }}" aria-describedby="tier-final"
                                class="mt-8 block rounded-lg bg-blue-600 dark:bg-blue-500 px-4 py-3 text-center text-sm font-semibold text-white shadow-md hover:bg-blue-700 dark:hover:bg-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:focus-visible:outline-indigo-500 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5">
                                Book Final Cleaning
                            </a>
                        </div>
                        <div class="relative rounded-3xl bg-blue-600 dark:bg-gray-800 p-8 ring-1 ring-white/10 sm:p-10">
                            <span
                                class="px-3 py-1 text-white dark:text-white bg-white/20 dark:bg-blue-600/30 rounded-full text-xs">Thorough</span>

                            <h3 id="tier-deepcleaning"
                                class="text-base/7 font-semibold text-white dark:text-blue-600 mt-6">
                                Deep Cleaning</h3>
                            <p class="mt-4 flex items-baseline gap-x-2">
                                <span class="text-5xl font-bold tracking-tight text-white">€120 - €480</span>
                            </p>
                            <p class="mt-6 text-base/7 text-gray-100">Intensive cleaning service for spotless results
                                and
                                hard-to-reach areas.
                            <p class="mt-6 text-base/7 text-gray-100">€48/hour based on space
                            </p>
                            <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-100 sm:mt-10">
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                        class="h-6 w-5 flex-none text-indigo-400">
                                        <path
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                    All Final Cleaning tasks included
                                </li>
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                        class="h-6 w-5 flex-none text-indigo-400">
                                        <path
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                    Hard-to-reach areas & corners
                                </li>
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                        class="h-6 w-5 flex-none text-indigo-400">
                                        <path
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                    Detailed scrubbing & sanitization
                                </li>
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                        class="h-6 w-5 flex-none text-indigo-400">
                                        <path
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                    Behind appliances & furniture
                                </li>
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                        class="h-6 w-5 flex-none text-indigo-400">
                                        <path
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                    Marketing automations
                                </li>
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                        class="h-6 w-5 flex-none text-indigo-400">
                                        <path
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" fill-rule="evenodd" />
                                    </svg>
                                    Custom integrations
                                </li>
                            </ul>
                            <a href="{{ route('client.appointment.create') }}" aria-describedby="tier-deep"
                                class="mt-8 block rounded-lg bg-white text-blue-600 dark:bg-blue-500 px-4 py-3 text-center text-sm font-semibold dark:text-white shadow-md hover:bg-blue-700 dark:hover:bg-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5">
                                Book Deep Cleaning
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Insert Important Information Cards Here-->
                <div class="my-24 mx-4 sm:mx-8 lg:mx-32 flex flex-col lg:flex-row gap-8">
                    <!-- Section Header -->
                    <div class="mb-8 lg:mx-6 flex-shrink-0 lg:w-1/2">
                        <p class="text-base font-semibold text-blue-600 dark:text-blue-400 my-6">
                            Detailed Rates</p>
                        <h3
                            class="my-6 text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-[#081032] dark:text-white">
                            Pricing <br><span class="text-blue-600">Rate Inclusions</span>
                        </h3>
                        <p class="mt-3 text-sm sm:text-base text-gray-600 dark:text-gray-400 max-w-2xl">
                            Check out our Special Day Rates and All-Inclusive Pricing for hassle-free, budget-friendly
                            cleaning that fits your schedule.
                        </p>
                    </div>

                    <!-- Information Cards Grid -->
                    <div class="flex flex-col gap-6 flex-1">
                        <!-- Special Day Rates Card -->
                        <div
                            class="info-card bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300">
                            <div class="flex flex-col gap-4">
                                <!-- Icon Badge -->
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center">
                                    <i class="fa-solid fa-money-bill-wave text-lg sm:text-xl text-blue-500 dark:text-white"></i>
                                </div>

                                <!-- Content -->
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold text-blue-500 dark:text-white mb-3">Special Day Rates
                                    </h3>
                                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                        Sundays and public holidays are charged at <strong class="font-semibold">double
                                            the regular rate</strong> due to special scheduling requirements.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- All-Inclusive Pricing Card -->
                        <div
                            class="info-card bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300">
                            <div class="flex flex-col gap-4">
                                <!-- Icon Badge -->
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center">
                                    <i class="fa-solid fa-tags text-lg sm:text-xl text-blue-500"></i>
                                </div>

                                <!-- Content -->
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold text-blue-500 dark:text-white mb-3">All-Inclusive
                                        Pricing</h3>
                                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                        All prices include <strong class="font-semibold">24% VAT</strong>. No hidden
                                        fees or additional charges.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mx-auto max-w-4xl text-center fade-in px-4">
                    <h2 class="text-base/7 font-semibold text-blue-600 dark:text-blue-600">Make Every Day Shine!</h2>
                    <h3
                        class="my-6 text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-[#081032] dark:text-white">
                        Cleaning <span class="text-blue-600">Service Rates</span>
                    </h3>
                    <p
                        class="mx-auto mt-6 max-w-2xl text-center text-sm sm:text-base text-gray-500 dark:text-gray-300">
                        Explore our transparent pricing for standard and specialized cleaning options.
                    </p>
                </div>

                <!-- Detailed Pricing Tables -->
                <div class="flex flex-col gap-6 lg:gap-8 my-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                    <!-- Final Cleaning Pricing Table -->
                    <div class="pricing-table bg-white dark:bg-gray-800 rounded-2xl overflow-hidden transform transition-all duration-300">
                        <div class="p-4 sm:p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3 sm:gap-4">
                                <div>
                                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Final Cleaning Rates</h3>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Fixed pricing per unit size</p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[500px]">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50">
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-semibold text-gray-600 dark:text-gray-400 w-[35%]">Unit Size (m²)</th>
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-semibold text-gray-600 dark:text-gray-400 w-[32.5%]">Normal Day</th>
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-semibold text-gray-600 dark:text-gray-400 w-[32.5%]">Sun/Holiday</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">20-50</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€70.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€140.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">51-70</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€105.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€210.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">71-90</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€140.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€280.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">91-120</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€175.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€350.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">121-140</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€210.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€420.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">141-160</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€245.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€490.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">161-180</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€280.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€560.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">181-220</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€315.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€630.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Deep Cleaning Pricing Table -->
                    <div class="pricing-table bg-white dark:bg-gray-800 rounded-2xl overflow-hidden transform transition-all duration-300">
                        <div class="p-4 sm:p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3 sm:gap-4">
                                <div>
                                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Deep Cleaning Rates</h3>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">€48/hour based estimate</p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[500px]">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900/50">
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-semibold text-gray-600 dark:text-gray-400 w-[35%]">Unit Size (m²)</th>
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-semibold text-gray-600 dark:text-gray-400 w-[32.5%]">Normal Day</th>
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-semibold text-gray-600 dark:text-gray-400 w-[32.5%]">Sun/Holiday</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">20-50</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€120.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€240.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">51-70</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€168.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€336.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">71-90</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€216.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€432.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">91-120</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€264.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€528.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">121-140</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€312.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€624.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">141-160</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€360.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€720.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">161-180</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€408.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€816.00</td>
                                    </tr>
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">181-220</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€480.00</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">€960.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles and Animations -->
    <style>
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulseSlow {

            0%,
            100% {
                opacity: 0.2;
            }

            50% {
                opacity: 0.3;
            }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .pricing-card {
            animation: fadeIn 0.8s ease-out forwards;
            opacity: 0;
        }

        .pricing-card:nth-child(1) {
            animation-delay: 0.2s;
        }

        .pricing-card:nth-child(2) {
            animation-delay: 0.4s;
        }

        .pricing-table {
            animation: fadeIn 0.8s ease-out forwards;
            opacity: 0;
        }

        .pricing-table:nth-child(1) {
            animation-delay: 0.3s;
        }

        .pricing-table:nth-child(2) {
            animation-delay: 0.5s;
        }

        .info-card {
            animation: fadeIn 0.8s ease-out forwards;
            opacity: 0;
        }

        .info-card:nth-child(1) {
            animation-delay: 0.4s;
        }

        .info-card:nth-child(2) {
            animation-delay: 0.6s;
        }

        .feature-item {
            animation: slideInLeft 0.6s ease-out forwards;
            opacity: 0;
        }

        .feature-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .feature-item:nth-child(2) {
            animation-delay: 0.2s;
        }

        .feature-item:nth-child(3) {
            animation-delay: 0.3s;
        }

        .feature-item:nth-child(4) {
            animation-delay: 0.4s;
        }

        .table-row {
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
        }

        .table-row:nth-child(1) {
            animation-delay: 0.1s;
        }

        .table-row:nth-child(2) {
            animation-delay: 0.15s;
        }

        .table-row:nth-child(3) {
            animation-delay: 0.2s;
        }

        .table-row:nth-child(4) {
            animation-delay: 0.25s;
        }

        .table-row:nth-child(5) {
            animation-delay: 0.3s;
        }

        .table-row:nth-child(6) {
            animation-delay: 0.35s;
        }

        .table-row:nth-child(7) {
            animation-delay: 0.4s;
        }

        .table-row:nth-child(8) {
            animation-delay: 0.45s;
        }

        .animate-pulse-slow {
            animation: pulseSlow 4s ease-in-out infinite;
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar for webkit browsers */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(107, 114, 128, 0.7);
        }

        /* Dark mode scrollbar */
        .dark ::-webkit-scrollbar-thumb {
            background: rgba(75, 85, 99, 0.5);
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: rgba(107, 114, 128, 0.7);
        }
    </style>

    <!-- Optional JavaScript for enhanced interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Smooth reveal on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all cards and tables
            document.querySelectorAll('.pricing-card, .pricing-table, .info-card').forEach(el => {
                observer.observe(el);
            });

            // Add hover effect to pricing cards
            document.querySelectorAll('.pricing-card').forEach(card => {
                card.addEventListener('mouseenter', function () {
                    this.style.transform = 'scale(1.02) translateY(-5px)';
                });

                card.addEventListener('mouseleave', function () {
                    this.style.transform = 'scale(1) translateY(0)';
                });
            });

            // Highlight row on hover with smooth transition
            document.querySelectorAll('.table-row').forEach(row => {
                row.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateX(5px)';
                });

                row.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateX(0)';
                });
            });
        });
    </script>
</x-layouts.general-client>