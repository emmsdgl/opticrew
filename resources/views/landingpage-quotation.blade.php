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
        <div class="relative isolate px-6 py-12 sm:py-24 lg:px-8 lg:pb-32 overflow-hidden">
            <div class="mx-auto max-w-4xl text-center fade-in">
                <h2 class="text-base/7 font-bold text-blue-600">Choose your clean</h2>
                <h3 data-typing data-typing-duration="1.8" class="my-12 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Shine brighter with <br><span class="aurora-text">our cleaning services</span>
                </h3>
                <p class="mx-auto mt-6 max-w-2xl text-center text-sm sm:text-xs lg:text-base text-gray-500 dark:text-gray-300">
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
        <div class="px-4 md:px-6 lg:px-8 mt-0 sm:-mt-16 relative z-10">
            <div class="mx-auto max-w-7xl">
                <div class="relative isolate">
                    <div class="mx-auto mt-6 grid max-w-lg grid-cols-1 items-center gap-y-6 sm:mt-6 sm:gap-y-0 lg:max-w-4xl lg:grid-cols-2">
                        {{-- Final Cleaning Card --}}
                        <div class="pricing-card rounded-3xl rounded-t-3xl bg-gradient-to-br from-white via-blue-50/60 to-indigo-50/40 dark:from-white/5 dark:via-blue-900/10 dark:to-indigo-900/10 p-8 ring-1 ring-gray-200 dark:ring-white/10 sm:mx-8 sm:rounded-b-none sm:p-10 lg:mx-0 lg:rounded-tr-none lg:rounded-bl-3xl">
                            <span class="px-3 py-1 text-blue-600 dark:text-blue-400 bg-blue-600/10 dark:bg-blue-600/20 rounded-full text-xs">Most Popular</span>
                            <h3 class="text-base/7 font-bold text-blue-500 dark:text-blue-400 my-6">Final Cleaning</h3>
                            <p class="mt-4 flex items-baseline gap-x-2">
                                <span class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-blue-600 dark:text-white">€70 - €315</span>
                            </p>
                            <p class="mt-6 text-base/7 text-sm sm:text-xs lg:text-base text-gray-500 dark:text-gray-300">Complete cleaning solution perfect for regular maintenance and move-out situations.</p>
                            <p class="mt-6 text-base/7 text-sm sm:text-xs lg:text-base text-gray-400">Based on unit size</p>
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
                                <span class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-white">€120 - €480</span>
                            </p>
                            <p class="mt-6 text-base/7 text-sm sm:text-xs lg:text-base text-gray-100">Intensive cleaning service for spotless results and hard-to-reach areas.</p>
                            <p class="mt-6 text-base/7 text-sm sm:text-xs lg:text-base text-gray-100">€48/hour based on space</p>
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
                        <h3 class="my-6 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 dark:text-white">
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
                    <h3 class="my-6 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900 dark:text-white">
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
        <x-client-components.quotation-page.quotation-modal />
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

            // CSC API
            cscApiKey: @json($cscApiKey ?? ''),
            cscBaseUrl: 'https://api.countrystatecity.in/v1',
            finlandIso2: 'FI',
            cscStates: [],
            cscCities: [],
            filteredDistricts: [],
            districtLoading: false,

            form: {
                bookingType: 'personal',
                serviceType: '',
                serviceDate: '',
                urgency: 'regular',
                propertyType: '',
                floors: 1,
                rooms: 1,
                floorArea: 0,
                region: '',
                city: '',
                postalCode: '',
                district: '',
                specialRequests: '',
                companyName: '',
            },

            init() {
                this.loadStates();
            },

            openModal(serviceType) {
                this.step = 1;
                this.submitting = false;
                if (serviceType === 'Final Cleaning') this.form.serviceType = 'final_cleaning';
                else if (serviceType === 'Deep Cleaning') this.form.serviceType = 'deep_cleaning';
                this.showModal = true;
                document.body.style.overflow = 'hidden';
            },

            // CSC API methods
            async cscFetch(endpoint) {
                const res = await fetch(`${this.cscBaseUrl}${endpoint}`, {
                    headers: { 'X-CSCAPI-KEY': this.cscApiKey }
                });
                if (!res.ok) throw new Error('CSC API error: ' + res.status);
                return res.json();
            },

            async loadStates() {
                try {
                    const states = await this.cscFetch(`/countries/${this.finlandIso2}/states`);
                    this.cscStates = states.sort((a, b) => a.name.localeCompare(b.name));
                } catch (e) {
                    console.error('Failed to load states:', e);
                }
            },

            async onRegionChange() {
                this.cscCities = [];
                this.form.city = '';
                this.form.postalCode = '';
                this.form.district = '';
                this.filteredDistricts = [];
                if (!this.form.region) return;

                const stateObj = this.cscStates.find(s => s.name === this.form.region);
                if (!stateObj) return;

                try {
                    const cities = await this.cscFetch(`/countries/${this.finlandIso2}/states/${stateObj.iso2}/cities`);
                    this.cscCities = cities.sort((a, b) => a.name.localeCompare(b.name));
                } catch (e) {
                    console.error('Failed to load cities:', e);
                }
            },

            // Static Finnish postal code fallback
            finnishPostalCodes: {
                'helsinki': '00100', 'espoo': '02100', 'tampere': '33100', 'vantaa': '01300',
                'oulu': '90100', 'turku': '20100', 'jyväskylä': '40100', 'lahti': '15100',
                'kuopio': '70100', 'pori': '28100', 'kouvola': '45100', 'joensuu': '80100',
                'lappeenranta': '53100', 'hämeenlinna': '13100', 'vaasa': '65100', 'rovaniemi': '96100',
                'seinäjoki': '60100', 'mikkeli': '50100', 'kotka': '48100', 'salo': '24100',
                'porvoo': '06100', 'kokkola': '67100', 'hyvinkää': '05800', 'lohja': '08100',
                'järvenpää': '04400', 'rauma': '26100', 'kajaani': '87100', 'kerava': '04200',
                'savonlinna': '57100', 'nokia': '37100', 'ylöjärvi': '33470', 'kangasala': '36200',
                'riihimäki': '11100', 'imatra': '55100', 'raasepori': '10600', 'kaarina': '20780',
                'kirkkonummi': '02400', 'siilinjärvi': '71800', 'tuusula': '04300',
                'tornio': '95400', 'iisalmi': '74100', 'valkeakoski': '37600', 'raisio': '21200',
            },

            helsinkiDistricts: [
                'Kallio', 'Kamppi', 'Punavuori', 'Töölö', 'Kruununhaka',
                'Ullanlinna', 'Eira', 'Sörnäinen', 'Vallila', 'Hermanni',
                'Pasila', 'Munkkiniemi', 'Lauttasaari', 'Ruoholahti', 'Jätkäsaari',
                'Herttoniemi', 'Kulosaari', 'Vuosaari', 'Kontula', 'Mellunmäki',
            ],

            async onCityChange() {
                this.form.district = '';
                this.filteredDistricts = [];

                if (!this.form.city) {
                    this.form.postalCode = '';
                    return;
                }

                this.districtLoading = true;
                try {
                    const cityEnc = encodeURIComponent(this.form.city);
                    const stateEnc = encodeURIComponent(this.form.region);
                    let postalFound = false;

                    // Attempt 1: city-level search
                    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${cityEnc}%2C+${stateEnc}%2C+Finland&addressdetails=1&limit=1`, {
                        headers: { 'Accept-Language': 'en' }
                    });
                    const data = await res.json();
                    if (data.length > 0 && data[0].address?.postcode) {
                        this.form.postalCode = data[0].address.postcode;
                        postalFound = true;
                    }

                    // Fallback 1: structured street-level search (forces Nominatim to return postcode)
                    if (!postalFound) {
                        try {
                            const structRes = await fetch(
                                `https://nominatim.openstreetmap.org/search?format=json&street=1&city=${cityEnc}&state=${stateEnc}&country=Finland&addressdetails=1&limit=1`,
                                { headers: { 'Accept-Language': 'en' } }
                            );
                            const structData = await structRes.json();
                            if (structData.length > 0 && structData[0].address?.postcode) {
                                this.form.postalCode = structData[0].address.postcode;
                                postalFound = true;
                            }
                        } catch (e) { /* silent */ }
                    }

                    // Fallback 2: static Finnish postal code map
                    if (!postalFound) {
                        const cityKey = this.form.city.toLowerCase().trim();
                        if (this.finnishPostalCodes[cityKey]) {
                            this.form.postalCode = this.finnishPostalCodes[cityKey];
                        }
                    }

                    // Load districts via Nominatim
                    const districtRes = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=suburb+in+${cityEnc}+Finland&addressdetails=1&limit=50`,
                        { headers: { 'Accept-Language': 'en' } }
                    );
                    const districtData = await districtRes.json();
                    const districts = new Set();
                    districtData.forEach(d => {
                        const addr = d.address || {};
                        const name = addr.suburb || addr.neighbourhood || addr.city_district || addr.quarter || '';
                        if (name) districts.add(name);
                    });

                    // Add Helsinki districts if applicable
                    if (this.form.city.toLowerCase().includes('helsinki')) {
                        this.helsinkiDistricts.forEach(d => districts.add(d));
                    }

                    this.filteredDistricts = [...districts].sort();

                    if (this.filteredDistricts.length > 0) {
                        this.form.district = this.filteredDistricts[0];
                    }
                } catch (e) {
                    console.warn('Location lookup failed:', e);
                } finally {
                    this.districtLoading = false;
                }
            },

            nextStep() {
                if (this.step === 1) {
                    if (!this.form.bookingType) { window.showErrorDialog('Missing Information', 'Please select a booking type.'); return; }
                    if (!this.form.serviceType) { window.showErrorDialog('Missing Information', 'Please select a service type.'); return; }
                }
                this.step++;
            },

            // Show confirmation dialog with Google Auth button
            async submitQuotation() {
                if (!this.form.propertyType) { window.showErrorDialog('Missing Information', 'Please select a property type.'); return; }

                try {
                    await window.showConfirmDialog(
                        'Submit Quotation Request?',
                        'Sign in with Google to submit your quotation. Your name and email will be automatically filled from your Google account.',
                        'Submit with Google',
                        'Cancel'
                    );
                } catch (e) {
                    return; // User cancelled
                }

                // User confirmed — submit form data to server and redirect to Google OAuth
                this.submitting = true;
                this.showModal = false;
                document.body.style.overflow = '';

                const serviceLabels = {
                    deep_cleaning: 'Deep Cleaning', final_cleaning: 'Final Cleaning',
                    daily_cleaning: 'Daily Cleaning', snowout_cleaning: 'Snowout Cleaning',
                    general_cleaning: 'General Cleaning', hotel_cleaning: 'Hotel Cleaning',
                };

                // Create a hidden form and POST to the Google auth route
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("quotation.google.auth") }}';

                const fields = {
                    '_token': '{{ csrf_token() }}',
                    'bookingType': this.form.bookingType,
                    'serviceType': serviceLabels[this.form.serviceType] || this.form.serviceType,
                    'serviceDate': this.form.serviceDate || '',
                    'urgency': this.form.urgency,
                    'propertyType': this.form.propertyType,
                    'floors': this.form.floors,
                    'rooms': this.form.rooms,
                    'floorArea': this.form.floorArea,
                    'region': this.form.region,
                    'city': this.form.city,
                    'postalCode': this.form.postalCode,
                    'district': this.form.district,
                    'specialRequests': this.form.specialRequests,
                    'companyName': this.form.companyName,
                };

                Object.entries(fields).forEach(([key, value]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value ?? '';
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
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

        // Show flash dialogs from Google OAuth redirect
        @if(session('success'))
            window.showSuccessDialog('Quotation Submitted', @json(session('success')));
        @endif
        @if(session('error'))
            window.showErrorDialog('Something Went Wrong', @json(session('error')));
        @endif

        document.querySelectorAll('.table-row').forEach(row => {
            row.addEventListener('mouseenter', function() { this.style.transform = 'translateX(5px)'; });
            row.addEventListener('mouseleave', function() { this.style.transform = 'translateX(0)'; });
        });
    });
</script>
@endpush
