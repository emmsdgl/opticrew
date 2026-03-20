@extends('components.layouts.general-landing')

@section('title', __('quotation.title'))

@push('styles')
    <style>
        body { background-image: none; }
        html { scroll-behavior: smooth; }

        .shine-btn {
            background-image: linear-gradient(325deg, hsl(217 100% 65%) 0%, hsl(210 100% 76%) 55%, hsl(217 100% 55%) 90%);
            background-size: 280% auto;
            background-position: initial;
            transition: background-position 0.8s, transform 0.15s;
            box-shadow:
                0px 0px 16px rgba(59,130,246,0.35),
                0px 5px 5px -1px rgba(59,130,246,0.2),
                inset 4px 4px 8px rgba(147,197,253,0.3),
                inset -4px -4px 8px rgba(37,99,235,0.25);
        }
        .shine-btn:hover { background-position: right top; }
        .shine-btn:active { transform: scale(0.95); }
        @keyframes shine-sweep {
            0% { left: -75%; opacity: 0; }
            50% { opacity: 0.4; }
            100% { left: 125%; opacity: 0; }
        }
        .shine-btn:hover .shine-effect {
            animation: shine-sweep 0.8s ease-in-out;
        }
    </style>
@endpush

@section('content')
    <div class="flex flex-col w-full min-h-[calc(100vh-4rem)] font-sans" x-data="quotationPage()">
        <!-- Hero Section -->
        <div class="relative isolate px-6 sm:py-24 lg:px-8 lg:pb-32 overflow-hidden">
            <div class="mx-auto max-w-4xl text-center fade-in">
                <h2 class="text-base/7 font-bold text-blue-600">Choose your clean</h2>
                <h3 data-typing data-typing-duration="1.8" class="my-12 text-6xl md:text-6xl lg:text-6xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Shine brighter with <br><span class="aurora-text">our cleaning services</span>
                </h3>
                <p class="mx-auto mt-6 max-w-2xl text-center text-base text-gray-500 dark:text-gray-300">
                    Professional cleaning services tailored to your space. No hidden fees, transparent pricing, just quality service.
                </p>
                <button @click="openModal()"
                    class="shine-btn relative overflow-hidden inline-flex items-center justify-center gap-2 mt-8 rounded-full px-6 py-3 font-bold text-sm sm:text-base text-white cursor-pointer group">
                    <span class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 bg-white/20 rounded-full group-hover:rotate-12 transition-transform duration-300">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M2 9a3 3 0 1 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 1 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M9 9h.01"/><path d="m15 9-6 6"/><path d="M15 15h.01"/></svg>
                    </span>
                    <span class="font-semibold">Request Quotation</span>
                    <div class="shine-effect absolute top-0 left-[-75%] w-[200%] h-full bg-white/30 skew-x-[-20deg] opacity-0 pointer-events-none z-20"></div>
                </button>
            </div>
        </div>

        <!-- Main Pricing Cards Section -->
        <div class="px-4 md:px-6 lg:px-8 -mt-16 relative z-10">
            <div class="mx-auto max-w-7xl">
                <div class="relative isolate">
                    <div class="mx-auto mt-6 grid max-w-lg grid-cols-1 items-center gap-y-6 sm:mt-6 sm:gap-y-0 lg:max-w-4xl lg:grid-cols-2">
                        {{-- Final Cleaning Card --}}
                        <div class="pricing-card rounded-3xl rounded-t-3xl bg-gradient-to-br from-white via-blue-50/60 to-indigo-50/40 dark:from-white/5 dark:via-blue-900/10 dark:to-indigo-900/10 p-8 ring-1 ring-gray-200 dark:ring-white/10 sm:mx-8 sm:rounded-b-none sm:p-10 lg:mx-0 lg:rounded-tr-none lg:rounded-bl-3xl">
                            <span class="px-3 py-1 text-blue-600 dark:text-blue-400 bg-blue-600/10 dark:bg-blue-600/20 rounded-full text-xs">Most Popular</span>
                            <h3 class="text-base/7 font-bold text-blue-500 dark:text-blue-400 my-6">Final Cleaning</h3>
                            <p class="mt-4 flex items-baseline gap-x-2">
                                <span class="text-5xl font-bold tracking-tight text-blue-600 dark:text-white">€70 - €315</span>
                            </p>
                            <p class="mt-6 text-base/7 text-gray-500 dark:text-gray-300">Complete cleaning solution perfect for regular maintenance and move-out situations.</p>
                            <p class="mt-6 text-base/7 text-gray-400">Based on unit size</p>
                            <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-500 dark:text-gray-300 sm:mt-10">
                                @foreach(['Kitchen cleaning & surfaces', 'Living room & bedroom tidying', 'Bathroom & sauna cleaning', 'Vacuuming & mopping floors'] as $feature)
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-6 w-5 flex-none text-blue-600 dark:text-blue-500"><path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" /></svg>
                                    {{ $feature }}
                                </li>
                                @endforeach
                            </ul>
                            <button @click="openModal('Final Cleaning')" class="mt-8 w-full block rounded-lg bg-blue-600 px-4 py-3 text-center text-sm font-bold text-white shadow-md hover:bg-blue-700 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5">
                                Request Quotation
                            </button>
                        </div>

                        {{-- Deep Cleaning Card --}}
                        <div class="pricing-card relative rounded-3xl bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 dark:from-blue-600 dark:via-blue-700 dark:to-indigo-800 p-8 ring-1 ring-blue-400/30 dark:ring-white/10 sm:p-10">
                            <span class="px-3 py-1 text-white bg-white/20 rounded-full text-xs">Thorough</span>
                            <h3 class="text-base/7 font-bold text-white mt-6">Deep Cleaning</h3>
                            <p class="mt-4 flex items-baseline gap-x-2">
                                <span class="text-5xl font-bold tracking-tight text-white">€120 - €480</span>
                            </p>
                            <p class="mt-6 text-base/7 text-gray-100">Intensive cleaning service for spotless results and hard-to-reach areas.</p>
                            <p class="mt-6 text-base/7 text-gray-100">€48/hour based on space</p>
                            <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-100 sm:mt-10">
                                @foreach(['All Final Cleaning tasks included', 'Hard-to-reach areas & corners', 'Detailed scrubbing & sanitization', 'Behind appliances & furniture', 'Window sills & baseboards', 'Deep floor treatment'] as $feature)
                                <li class="flex gap-x-3">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-6 w-5 flex-none text-indigo-300"><path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" /></svg>
                                    {{ $feature }}
                                </li>
                                @endforeach
                            </ul>
                            <button @click="openModal('Deep Cleaning')" class="mt-8 w-full block rounded-lg bg-white text-blue-600 px-4 py-3 text-center text-sm font-bold shadow-md hover:bg-gray-100 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5">
                                Request Quotation
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Rate Inclusions Section -->
                <div class="my-24 mx-4 sm:mx-8 lg:mx-32 flex flex-col lg:flex-row gap-8">
                    <div class="mb-8 lg:mx-6 flex-shrink-0 lg:w-1/2">
                        <p class="text-base font-bold text-blue-600 dark:text-blue-500 my-6">Detailed Rates</p>
                        <h3 class="my-6 text-6xl sm:text-6xl lg:text-6xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Pricing <br><span class="aurora-text">Rate Inclusions</span>
                        </h3>
                        <p class="mt-3 text-sm sm:text-base text-gray-600 dark:text-gray-400 max-w-2xl">
                            Check out our Special Day Rates and All-Inclusive Pricing for hassle-free, budget-friendly cleaning that fits your schedule.
                        </p>
                    </div>

                    <div class="flex flex-col gap-6 flex-1">
                        {{-- Special Day Rates --}}
                        <div class="info-card bg-gray-50 dark:bg-white/5 rounded-2xl p-6 sm:p-8 border border-gray-200 dark:border-white/10 shadow-sm hover:shadow-lg transition-all duration-300">
                            <div class="flex flex-col gap-4">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center">
                                    <i class="fa-solid fa-money-bill-wave text-lg sm:text-xl text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold text-blue-500 dark:text-blue-400 mb-3">Special Day Rates</h3>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                        Sundays and public holidays are charged at <strong class="font-bold text-gray-900 dark:text-white">double the regular rate</strong> due to special scheduling requirements.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- All-Inclusive Pricing --}}
                        <div class="info-card bg-gray-50 dark:bg-white/5 rounded-2xl p-6 sm:p-8 border border-gray-200 dark:border-white/10 shadow-sm hover:shadow-lg transition-all duration-300">
                            <div class="flex flex-col gap-4">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center">
                                    <i class="fa-solid fa-tags text-lg sm:text-xl text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold text-blue-500 dark:text-blue-400 mb-3">All-Inclusive Pricing</h3>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                        All prices include <strong class="font-bold text-gray-900 dark:text-white">24% VAT</strong>. No hidden fees or additional charges.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Rates Header -->
                <div class="mx-auto max-w-4xl text-center fade-in px-4">
                    <h2 class="text-base/7 font-bold text-blue-600 dark:text-blue-500">Make Every Day Shine!</h2>
                    <h3 class="my-6 text-6xl sm:text-6xl lg:text-6xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Cleaning <span class="aurora-text">Service Rates</span>
                    </h3>
                    <p class="mx-auto mt-6 max-w-2xl text-center text-sm sm:text-base text-gray-500 dark:text-gray-400">
                        Explore our transparent pricing for standard and specialized cleaning options.
                    </p>
                </div>

                <!-- Pricing Tables -->
                <div class="flex flex-col gap-6 lg:gap-8 my-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                    {{-- Final Cleaning Rates Table --}}
                    <div class="pricing-table rounded-2xl overflow-hidden border border-gray-200 dark:border-white/10">
                        <div class="p-4 sm:p-6 lg:p-8 border-b border-gray-200 dark:border-white/10">
                            <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Final Cleaning Rates</h3>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Fixed pricing per unit size</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[500px]">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-white/5">
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-bold text-gray-500 dark:text-gray-400 w-[35%]">Unit Size (m²)</th>
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-bold text-gray-500 dark:text-gray-400 w-[32.5%]">Normal Day</th>
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-bold text-gray-500 dark:text-gray-400 w-[32.5%]">Sun/Holiday</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                                    @php
                                        $finalRates = [
                                            ['20-50', '€70.00', '€140.00'],
                                            ['51-70', '€105.00', '€210.00'],
                                            ['71-90', '€140.00', '€280.00'],
                                            ['91-120', '€175.00', '€350.00'],
                                            ['121-140', '€210.00', '€420.00'],
                                            ['141-160', '€245.00', '€490.00'],
                                            ['161-180', '€280.00', '€560.00'],
                                            ['181-220', '€315.00', '€630.00'],
                                        ];
                                    @endphp
                                    @foreach($finalRates as $row)
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-white/5 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">{{ $row[0] }}</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">{{ $row[1] }}</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">{{ $row[2] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Deep Cleaning Rates Table --}}
                    <div class="pricing-table rounded-2xl overflow-hidden border border-gray-200 dark:border-white/10">
                        <div class="p-4 sm:p-6 lg:p-8 border-b border-gray-200 dark:border-white/10">
                            <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Deep Cleaning Rates</h3>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">€48/hour based estimate</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[500px]">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-white/5">
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-bold text-gray-500 dark:text-gray-400 w-[35%]">Unit Size (m²)</th>
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-bold text-gray-500 dark:text-gray-400 w-[32.5%]">Normal Day</th>
                                        <th class="text-center py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-bold text-gray-500 dark:text-gray-400 w-[32.5%]">Sun/Holiday</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                                    @php
                                        $deepRates = [
                                            ['20-50', '€120.00', '€240.00'],
                                            ['51-70', '€168.00', '€336.00'],
                                            ['71-90', '€216.00', '€432.00'],
                                            ['91-120', '€264.00', '€528.00'],
                                            ['121-140', '€312.00', '€624.00'],
                                            ['141-160', '€360.00', '€720.00'],
                                            ['161-180', '€408.00', '€816.00'],
                                            ['181-220', '€480.00', '€960.00'],
                                        ];
                                    @endphp
                                    @foreach($deepRates as $row)
                                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-white/5 transition-colors duration-150">
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm font-medium text-gray-900 dark:text-white text-center">{{ $row[0] }}</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">{{ $row[1] }}</td>
                                        <td class="py-3 sm:py-4 px-3 sm:px-6 text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center">{{ $row[2] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- <!-- CTA Section -->
                <div class="mx-auto max-w-4xl text-center my-16 px-4">
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-4">Ready to get started?</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8">Sign up now to book your first cleaning service.</p>
                    <button @click="openModal()"
                        class="inline-flex text-sm items-center px-8 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fi fi-rr-document mr-3"></i>Request Quotation
                    </button>
                </div> --}}
            </div>
        </div>
        {{-- Quotation Request Modal --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display:none">
            {{-- Backdrop --}}
            <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showModal = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

            {{-- Modal Content --}}
            <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-75"
                 class="relative z-10 w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-h-[90vh] flex flex-col overflow-hidden">

                {{-- Header with Stepper --}}
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Request Quotation</h2>
                        <button @click="showModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    {{-- Stepper --}}
                    <div class="flex items-center gap-2">
                        <template x-for="(label, i) in ['Service', 'Property', 'Contact']" :key="i">
                            <div class="flex items-center gap-2 flex-1">
                                <div class="flex items-center gap-2 flex-1">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300 flex-shrink-0"
                                         :class="step > i+1 ? 'bg-green-500 text-white' : step === i+1 ? 'bg-gradient-to-br from-blue-500 to-blue-700 text-white shadow-lg shadow-blue-500/30 ring-2 ring-blue-400/50' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400'"
                                         x-text="step > i+1 ? '✓' : i+1"></div>
                                    <span class="text-xs font-medium hidden sm:inline" :class="step === i+1 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'" x-text="label"></span>
                                </div>
                                <div x-show="i < 2" class="flex-1 h-0.5 rounded" :class="step > i+1 ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700'"></div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto px-6 py-5">

                    {{-- Step 1: Service --}}
                    <div x-show="step === 1" x-transition>
                        <div class="space-y-4">
                            {{-- Booking Type --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Booking Type <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" value="personal" x-model="form.bookingType" class="peer sr-only">
                                        <div class="w-full p-3 border-2 rounded-xl transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-200 dark:border-gray-700 hover:border-blue-400">
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-user text-blue-500"></i>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">Personal</span>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" value="company" x-model="form.bookingType" class="peer sr-only">
                                        <div class="w-full p-3 border-2 rounded-xl transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-200 dark:border-gray-700 hover:border-blue-400">
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-building text-blue-500"></i>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">Company</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Service Type --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Type of Cleaning Service <span class="text-red-500">*</span></label>
                                <select x-model="form.serviceType" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    <option value="">Select a service...</option>
                                    <option value="deep_cleaning">Deep Cleaning — €48/hr</option>
                                    <option value="final_cleaning">Final Cleaning — Fixed Price</option>
                                    <option value="daily_cleaning">Daily Cleaning — €35/hr</option>
                                    <option value="snowout_cleaning">Snowout Cleaning — €55/hr</option>
                                    <option value="general_cleaning">General Cleaning — €40/hr</option>
                                    <option value="hotel_cleaning">Hotel Cleaning — €42/hr</option>
                                </select>
                            </div>

                            {{-- Date & Duration --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Preferred Date</label>
                                    <input type="date" x-model="form.serviceDate" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Urgency</label>
                                    <select x-model="form.urgency" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                        <option value="regular">Regular (5+ days)</option>
                                        <option value="soon">Soon (3-4 days)</option>
                                        <option value="urgent">Urgent (1-2 days)</option>
                                        <option value="emergency">Emergency (Same day)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Property --}}
                    <div x-show="step === 2" x-transition>
                        <div class="space-y-4">
                            {{-- Property Type --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Property Type <span class="text-red-500">*</span></label>
                                <select x-model="form.propertyType" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="">Select property type...</option>
                                    <option value="apartment">Apartment / Flat</option>
                                    <option value="house">House</option>
                                    <option value="cabin">Cabin / Cottage</option>
                                    <option value="office">Office</option>
                                    <option value="hotel">Hotel / Accommodation</option>
                                    <option value="commercial">Commercial Space</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            {{-- Size & Rooms --}}
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Floors</label>
                                    <input type="number" x-model="form.floors" min="1" max="10" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Rooms</label>
                                    <input type="number" x-model="form.rooms" min="1" max="20" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Area (m²)</label>
                                    <input type="number" x-model="form.floorArea" min="0" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                            </div>

                            {{-- Location --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Location / Address</label>
                                <input type="text" x-model="form.location" placeholder="Enter your address or city" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            {{-- Special Requests --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Special Requests</label>
                                <textarea x-model="form.specialRequests" rows="3" placeholder="Any additional details or requirements..." class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Contact --}}
                    <div x-show="step === 3" x-transition>
                        <div class="space-y-4">
                            {{-- Company Name (if company) --}}
                            <div x-show="form.bookingType === 'company'">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Company Name <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.companyName" placeholder="Enter company name" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            {{-- Name --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2" x-text="form.bookingType === 'company' ? 'Contact Person *' : 'Full Name *'"></label>
                                <input type="text" x-model="form.clientName" placeholder="Enter your name" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Phone Number <span class="text-red-500">*</span></label>
                                <input type="tel" x-model="form.phone" placeholder="+358 XX XXX XXXX" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" x-model="form.email" placeholder="your@email.com" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between flex-shrink-0">
                    <button x-show="step > 1" @click="step--" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i>Back
                    </button>
                    <div x-show="step === 1"></div>

                    <button x-show="step < 3" @click="nextStep()" class="px-6 py-2.5 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-md">
                        Next <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                    <button x-show="step === 3" @click="submitQuotation()" :disabled="submitting" class="px-6 py-2.5 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-md disabled:opacity-50">
                        <span x-show="!submitting">Submit Request</span>
                        <span x-show="submitting"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Submitting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    @keyframes auroraShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .aurora-text {
        background: linear-gradient(135deg, #60a5fa, #3b82f6, #818cf8, #6366f1, #3b82f6, #60a5fa);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: auroraShift 6s ease-in-out infinite;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .fade-in { animation: fadeIn 0.8s ease-out forwards; }
    .pricing-card { animation: fadeIn 0.8s ease-out forwards; opacity: 0; }
    .pricing-card:nth-child(1) { animation-delay: 0.2s; }
    .pricing-card:nth-child(2) { animation-delay: 0.4s; }
    .pricing-table { animation: fadeIn 0.8s ease-out forwards; opacity: 0; }
    .pricing-table:nth-child(1) { animation-delay: 0.3s; }
    .pricing-table:nth-child(2) { animation-delay: 0.5s; }
    .info-card { animation: fadeIn 0.8s ease-out forwards; opacity: 0; }
    .info-card:nth-child(1) { animation-delay: 0.4s; }
    .info-card:nth-child(2) { animation-delay: 0.6s; }
    .table-row { animation: fadeIn 0.5s ease-out forwards; opacity: 0; }
    .table-row:nth-child(1) { animation-delay: 0.1s; }
    .table-row:nth-child(2) { animation-delay: 0.15s; }
    .table-row:nth-child(3) { animation-delay: 0.2s; }
    .table-row:nth-child(4) { animation-delay: 0.25s; }
    .table-row:nth-child(5) { animation-delay: 0.3s; }
    .table-row:nth-child(6) { animation-delay: 0.35s; }
    .table-row:nth-child(7) { animation-delay: 0.4s; }
    .table-row:nth-child(8) { animation-delay: 0.45s; }
</style>
@endpush

@push('scripts')
<script>
    function quotationPage() {
        return {
            showModal: false,
            step: 1,
            submitting: false,
            form: {
                bookingType: 'personal',
                serviceType: '',
                serviceDate: '',
                urgency: 'regular',
                propertyType: '',
                floors: 1,
                rooms: 1,
                floorArea: 0,
                location: '',
                specialRequests: '',
                companyName: '',
                clientName: '',
                phone: '',
                email: '',
            },

            openModal(serviceType) {
                this.step = 1;
                this.submitting = false;
                // Pre-select service type if clicked from a specific card
                if (serviceType === 'Final Cleaning') this.form.serviceType = 'final_cleaning';
                else if (serviceType === 'Deep Cleaning') this.form.serviceType = 'deep_cleaning';
                this.showModal = true;
                document.body.style.overflow = 'hidden';
            },

            nextStep() {
                if (this.step === 1) {
                    if (!this.form.bookingType) { alert('Please select a booking type.'); return; }
                    if (!this.form.serviceType) { alert('Please select a service type.'); return; }
                }
                if (this.step === 2) {
                    if (!this.form.propertyType) { alert('Please select a property type.'); return; }
                }
                this.step++;
            },

            async submitQuotation() {
                if (!this.form.clientName) { alert('Please enter your name.'); return; }
                if (!this.form.phone) { alert('Please enter your phone number.'); return; }
                if (!this.form.email) { alert('Please enter your email.'); return; }
                if (this.form.bookingType === 'company' && !this.form.companyName) { alert('Please enter your company name.'); return; }

                this.submitting = true;
                try {
                    const serviceLabels = {
                        deep_cleaning: 'Deep Cleaning', final_cleaning: 'Final Cleaning',
                        daily_cleaning: 'Daily Cleaning', snowout_cleaning: 'Snowout Cleaning',
                        general_cleaning: 'General Cleaning', hotel_cleaning: 'Hotel Cleaning',
                    };

                    const fd = new FormData();
                    fd.append('_token', '{{ csrf_token() }}');
                    fd.append('booking_type', this.form.bookingType);
                    fd.append('service_type', serviceLabels[this.form.serviceType] || this.form.serviceType);
                    fd.append('service_date', this.form.serviceDate);
                    fd.append('urgency', this.form.urgency);
                    fd.append('property_type', this.form.propertyType);
                    fd.append('floors', this.form.floors);
                    fd.append('rooms', this.form.rooms);
                    fd.append('floor_area', this.form.floorArea);
                    fd.append('area_unit', 'sqm');
                    fd.append('location', this.form.location);
                    fd.append('special_requests', this.form.specialRequests);
                    fd.append('company_name', this.form.companyName);
                    fd.append('client_name', this.form.clientName);
                    fd.append('phone', this.form.phone);
                    fd.append('email', this.form.email);

                    const res = await fetch('{{ route("quotation.submit") }}', {
                        method: 'POST',
                        body: fd,
                        headers: { 'Accept': 'application/json' }
                    });

                    const data = await res.json();

                    if (data.success) {
                        this.showModal = false;
                        document.body.style.overflow = '';
                        if (window.showSuccessDialog) {
                            window.showSuccessDialog('Quotation Submitted', data.message || 'Your quotation request has been submitted successfully. We will get back to you soon.');
                        } else {
                            alert(data.message || 'Quotation submitted successfully!');
                        }
                        // Reset form
                        this.form = { bookingType: 'personal', serviceType: '', serviceDate: '', urgency: 'regular', propertyType: '', floors: 1, rooms: 1, floorArea: 0, location: '', specialRequests: '', companyName: '', clientName: '', phone: '', email: '' };
                    } else {
                        if (window.showErrorDialog) {
                            window.showErrorDialog('Submission Failed', data.message || 'Failed to submit quotation. Please try again.');
                        } else {
                            alert(data.message || 'Submission failed.');
                        }
                    }
                } catch (e) {
                    console.error('Quotation submit error:', e);
                    if (window.showErrorDialog) {
                        window.showErrorDialog('Error', 'An error occurred. Please try again.');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                } finally {
                    this.submitting = false;
                }
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.pricing-card, .pricing-table, .info-card').forEach(el => observer.observe(el));

        document.querySelectorAll('.table-row').forEach(row => {
            row.addEventListener('mouseenter', function() { this.style.transform = 'translateX(5px)'; });
            row.addEventListener('mouseleave', function() { this.style.transform = 'translateX(0)'; });
        });
    });
</script>
@endpush
