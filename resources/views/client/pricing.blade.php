<x-layouts.general-client :title="'Pricing'">
    <div class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Hero Section -->
        <div class="bg-blue-600 dark:bg-blue-800 rounded-2xl shadow-lg p-8 md:p-12 text-white">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    Simple, Transparent Pricing
                </h1>
                <p class="text-lg md:text-xl text-blue-100 mb-6">
                    Professional cleaning services tailored to your space. No hidden fees, just quality service.
                </p>
                <a href="{{ route('client.appointment.create') }}"
                   class="inline-block px-8 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-colors shadow-lg">
                    <i class="fi fi-rr-calendar-plus mr-2"></i>Book a Service Now
                </a>
            </div>
        </div>

        <!-- Service Comparison Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Final Cleaning Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-blue-200 dark:border-blue-700 overflow-hidden">
                <div class="bg-blue-600 dark:bg-blue-700 p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-2xl font-bold">Final Cleaning</h2>
                        <span class="px-3 py-1 bg-blue-500 dark:bg-blue-800 rounded-full text-sm font-semibold">Most Popular</span>
                    </div>
                    <p class="text-blue-100">Complete cleaning for your space</p>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white">€70</span>
                            <span class="text-gray-500 dark:text-gray-400">- €315</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Based on unit size</p>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-start gap-2">
                            <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-1"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Kitchen cleaning & surfaces</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-1"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Living room & bedroom tidying</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-1"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Bathroom & sauna cleaning</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-1"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Vacuuming & mopping floors</span>
                        </div>
                    </div>

                    <a href="{{ route('client.appointment.create') }}"
                       class="block w-full text-center px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Choose Final Cleaning
                    </a>
                </div>
            </div>

            <!-- Deep Cleaning Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-purple-200 dark:border-purple-700 overflow-hidden">
                <div class="bg-purple-600 dark:bg-purple-700 p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-2xl font-bold">Deep Cleaning</h2>
                        <span class="px-3 py-1 bg-purple-500 dark:bg-purple-800 rounded-full text-sm font-semibold">Thorough</span>
                    </div>
                    <p class="text-purple-100">Intensive cleaning for spotless results</p>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white">€120</span>
                            <span class="text-gray-500 dark:text-gray-400">- €480</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">€48/hour based on space</p>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-start gap-2">
                            <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-1"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">All Final Cleaning tasks</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-1"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Hard-to-reach areas</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-1"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Detailed scrubbing & sanitization</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-1"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Behind appliances & furniture</span>
                        </div>
                    </div>

                    <a href="{{ route('client.appointment.create') }}"
                       class="block w-full text-center px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                        Choose Deep Cleaning
                    </a>
                </div>
            </div>
        </div>

        <!-- Detailed Pricing Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Final Cleaning Pricing Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="fi fi-rr-broom text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Final Cleaning Rates</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Fixed pricing per unit</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Unit Size (m²)</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Normal Day</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-orange-600 dark:text-orange-400">Sun/Holiday</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">20-50</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€70.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€140.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">51-70</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€105.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€210.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">71-90</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€140.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€280.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">91-120</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€175.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€350.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">121-140</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€210.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€420.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">141-160</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€245.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€490.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">161-180</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€280.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€560.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">181-220</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€315.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€630.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Deep Cleaning Pricing Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <i class="fi fi-rr-sparkles text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Deep Cleaning Rates</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">€48/hour based estimate</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Unit Size (m²)</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Normal Day</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-orange-600 dark:text-orange-400">Sun/Holiday</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">20-50</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€120.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€240.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">51-70</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€168.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€336.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">71-90</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€216.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€432.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">91-120</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€264.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€528.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">121-140</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€312.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€624.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">141-160</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€360.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€720.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">161-180</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€408.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€816.00</td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-2 text-sm text-gray-900 dark:text-white">181-220</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-gray-900 dark:text-white">€480.00</td>
                                <td class="py-3 px-2 text-sm text-right font-semibold text-orange-600 dark:text-orange-400">€960.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Important Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Special Rates -->
            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-6 border border-orange-200 dark:border-orange-800">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fi fi-rr-calendar text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Special Day Rates</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                            <strong class="text-orange-600 dark:text-orange-400">Sundays and public holidays</strong> are charged at <strong>double the regular rate</strong> due to special scheduling requirements.
                        </p>
                        <div class="flex items-center gap-2 text-sm text-orange-700 dark:text-orange-300">
                            <i class="fi fi-rr-info-circle"></i>
                            <span>Holiday rates automatically applied at booking</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VAT Inclusive -->
            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fi fi-rr-check text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">All-Inclusive Pricing</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                            All prices include <strong class="text-green-600 dark:text-green-400">24% VAT</strong>. No hidden fees or additional charges.
                        </p>
                        <div class="flex items-center gap-2 text-sm text-green-700 dark:text-green-300">
                            <i class="fi fi-rr-shield-check"></i>
                            <span>What you see is what you pay</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-blue-600 dark:bg-blue-800 rounded-2xl shadow-lg p-8 md:p-12 text-white text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Book Your Cleaning?</h2>
            <p class="text-lg text-blue-100 mb-6 max-w-2xl mx-auto">
                Get your space professionally cleaned. Choose your preferred date, select your unit size, and we'll take care of the rest.
            </p>
            <a href="{{ route('client.appointment.create') }}"
               class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg font-bold text-lg hover:bg-blue-50 transition-colors shadow-lg">
                <i class="fi fi-rr-calendar-plus mr-2"></i>Book Your Service Now
            </a>
        </div>
    </div>
</x-layouts.general-client>
