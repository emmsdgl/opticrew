<x-layouts.general-stepper-form title="Client Appointment Form" :steps="['Client Details', 'Appointment Details', 'Confirmation']" :currentStep="$currentStep ?? 1">
    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50" x-data="{ toasts: [] }">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform translate-x-full"
                 class="flex items-center w-full max-w-sm p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-lg dark:text-gray-400 dark:bg-gray-800 border-l-4"
                 :class="toast.type === 'error' ? 'border-red-500' : 'border-blue-500'"
                 role="alert">
                <!-- Icon -->
                <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg"
                     :class="toast.type === 'error' ? 'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200' : 'text-blue-500 bg-blue-100 dark:bg-blue-800 dark:text-blue-200'">
                    <svg x-show="toast.type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="toast.type === 'info'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <!-- Message -->
                <div class="ml-3 text-sm font-normal" x-text="toast.message"></div>
                <!-- Close Button -->
                <button type="button"
                        @click="toast.show = false"
                        class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <div class="max-w-6xl mx-auto" x-data="appointmentForm()">

        <!-- STEP 1: CLIENT DETAILS -->
        <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-4"
            x-transition:enter-end="opacity-100 transform translate-x-0">
            <div class="rounded-xl p-8 md:p-8">
                <h2
                    class="text-2xl font-sans font-bold italic w-full items-center text-center mb-2 mt-8 text-gray-900 dark:text-white">
                    What's the details of the client?
                </h2>

                <div class="p-48 pt-3 mt-6 m-12">
                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">First
                                Name <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fi fi-rr-user"></i>
                                </span>
                                <input type="text" x-model="formData.first_name" required class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Firstname">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Last
                                Name <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.last_name" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Lastname">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Email
                            Address <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fi fi-rr-envelope"></i>
                            </span>
                            <input type="email" x-model="formData.email" required class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Ex: yourname@example.com">
                        </div>
                    </div>

                    <!-- Finnish Address Structure -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Street
                            Address <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fi fi-rr-marker"></i>
                            </span>
                            <input type="text" x-model="formData.street_address" required class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Ex: Mannerheimintie 123 A 45">
                        </div>
                    </div>

                    <!-- Postal Code and City -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Postal
                                Code <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.postal_code" required
                                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="00100" maxlength="5">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">City <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.city" required
                                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Helsinki">
                        </div>
                    </div>

                    <!-- Mobile Number -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Mobile
                            Number <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <div class="relative w-36">
                                <select x-model="formData.country_code" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="+358">🇫🇮 +358</option>
                                    <option value="+46">🇸🇪 +46</option>
                                    <option value="+47">🇳🇴 +47</option>
                                    <option value="+45">🇩🇰 +45</option>
                                    <option value="+1">🇺🇸 +1</option>
                                    <option value="+44">🇬🇧 +44</option>
                                </select>
                            </div>
                            <input type="tel" x-model="formData.mobile_number" required class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="40 1234567">
                        </div>
                    </div>

                    <!-- District (Kaupunginosa) -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                            District (Kaupunginosa) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fi fi-rr-marker"></i>
                            </span>
                            <input type="text" x-model="formData.district"
                                   @input="searchDistrict()"
                                   @focus="showDistrictSuggestions = true"
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Ex: Kallio, Kamppi, Punavuori">

                            <!-- District Suggestions Dropdown -->
                            <div x-show="showDistrictSuggestions && filteredDistricts.length > 0"
                                 @click.outside="showDistrictSuggestions = false"
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="district in filteredDistricts" :key="district">
                                    <div @click="selectDistrict(district)"
                                         class="px-4 py-2 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer text-gray-900 dark:text-white">
                                        <span x-text="district"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                   
                    <!-- Navigation -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('client.dashboard') }}">
                            <button type="button" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full
                            hover:bg-gray-300 transition-colors">
                                <i class="fi fi-rr-angle-left mr-2"></i>Back
                            </button>
                        </a>
                        <button type="button" @click="nextStep()"
                            class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                            Next<i class="fi fi-rr-angle-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2: SERVICE DETAILS -->
        <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-4"
            x-transition:enter-end="opacity-100 transform translate-x-0" x-cloak>
            <div class="rounded-xl p-8 md:p-8">
                <h2
                    class="text-2xl font-sans font-bold italic w-full items-center text-center mb-2 mt-8 text-gray-900 dark:text-white">
                    What's the appointment details?
                </h2>

                <div class="p-48 pt-3 mt-6 m-12">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Service
                            Type <span class="text-red-500">*</span></label>
                        <select x-model="formData.service_type" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a Service</option>
                            <option value="Final Cleaning">Final Cleaning (Cabins/Cottages & Holiday Apartments)</option>
                            <option value="Deep Cleaning">Deep Cleaning (Hourly Rate)</option>
                        </select>
                    </div>

                    <!-- Service Date and Time -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Service
                                Date <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fi fi-rr-calendar"></i>
                                </span>
                                <input type="date" x-model="formData.service_date" required @change="checkDateAndCalculate()"
                                       :min="new Date().toISOString().split('T')[0]"
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <!-- Sunday/Holiday indicator -->
                            <div x-show="formData.is_sunday || formData.is_holiday" class="mt-2 text-xs text-orange-600 dark:text-orange-400 flex items-center">
                                <i class="fi fi-rr-calendar-star mr-1"></i>
                                <span x-show="formData.is_holiday && !formData.is_sunday">Holiday - Double the price will apply</span>
                                <span x-show="formData.is_sunday && !formData.is_holiday">Sunday - Double the price will apply</span>
                                <span x-show="formData.is_sunday && formData.is_holiday">Sunday & Holiday - Double the price will apply</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Service
                                Time <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fi fi-rr-clock"></i>
                                </span>
                                <input type="time" x-model="formData.service_time" required class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Number of Units -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                            Number of Units <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <button type="button"
                                    @click="formData.units = Math.max(1, formData.units - 1)"
                                    :disabled="formData.units <= 1"
                                    :class="formData.units <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-300 dark:hover:bg-gray-600'"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                                <i class="fi fi-rr-minus text-sm"></i>
                            </button>
                            <input type="number"
                                   x-model.number="formData.units"
                                   @input="formData.units = Math.max(1, Math.min(20, parseInt(formData.units) || 1))"
                                   min="1"
                                   max="20"
                                   required
                                   class="w-32 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-center
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-semibold text-lg
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button"
                                    @click="formData.units = Math.min(20, formData.units + 1)"
                                    :disabled="formData.units >= 20"
                                    :class="formData.units >= 20 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-300 dark:hover:bg-gray-600'"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                                <i class="fi fi-rr-plus text-sm"></i>
                            </button>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400" x-text="formData.units === 1 ? '1 Unit' : formData.units + ' Units'"></span>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">You can add up to 20 units</p>
                    </div>


                    <!-- Dynamic Unit Fields -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-3 text-gray-700 dark:text-gray-300">
                            Unit Details <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-4">
                            <template x-for="(unit, index) in unitData" :key="index">
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            Unit <span x-text="index + 1"></span>
                                        </h4>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Cabin/Unit Name -->
                                        <div>
                                            <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-400">
                                                Cabin/Unit Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" x-model="unit.name" required
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm
                                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   placeholder="Ex: Kelo A, Cabin 5">
                                        </div>

                                        <!-- Unit Size -->
                                        <div>
                                            <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-400">
                                                Unit Size <span class="text-red-500">*</span>
                                            </label>
                                            <select x-model="unit.size" required @change="calculateUnitPrice(index)"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm
                                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select size</option>
                                                <option value="20-50">20-50 m²</option>
                                                <option value="51-70">51-70 m²</option>
                                                <option value="71-90">71-90 m²</option>
                                                <option value="91-120">91-120 m²</option>
                                                <option value="121-140">121-140 m²</option>
                                                <option value="141-160">141-160 m²</option>
                                                <option value="161-180">161-180 m²</option>
                                                <option value="181-220">181-220 m²</option>
                                            </select>
                                        </div>

                                        <!-- Price Display -->
                                        <div>
                                            <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-400">
                                                <span x-show="formData.service_type === 'Final Cleaning'">Price</span>
                                                <span x-show="formData.service_type === 'Deep Cleaning'">Calculation</span>
                                            </label>
                                            <div class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                                <!-- Final Cleaning Price -->
                                                <div x-show="formData.service_type === 'Final Cleaning'">
                                                    <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                                        €<span x-text="unit.price ? unit.price.toFixed(2) : '0.00'"></span>
                                                    </div>
                                                </div>
                                                <!-- Deep Cleaning Calculation -->
                                                <div x-show="formData.service_type === 'Deep Cleaning'">
                                                    <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">
                                                        <span x-text="unit.hours || '0'"></span>h × €<span x-text="deepCleaningHourlyRate.toFixed(0)"></span>
                                                        <span x-show="formData.is_sunday || formData.is_holiday" class="text-orange-600 dark:text-orange-400 font-semibold"> × 2</span>
                                                    </div>
                                                    <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                                        €<span x-text="unit.price ? unit.price.toFixed(2) : '0.00'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Quotation Summary -->
                    <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200 dark:border-blue-800" x-show="quotation > 0">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Estimated Quotation</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span x-text="formData.units"></span> unit(s)
                                    <span x-show="formData.is_sunday || formData.is_holiday" class="text-orange-600 dark:text-orange-400 font-semibold"> - Double rate applied</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    €<span x-text="quotation.toFixed(2)"></span>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">VAT Inclusive</div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Disclaimer -->
                    <div class="mb-6 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <p class="text-xs text-yellow-800 dark:text-yellow-200">
                            <i class="fi fi-rr-info mr-1"></i>
                            Sundays and holidays will be charged double the price. All rates are inclusive of VAT and prices are subject to change.
                        </p>
                    </div>

                    <!-- Special Requests -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Special
                            Requests</label>
                        <textarea x-model="formData.special_requests" rows="4" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Type in special requests (eg. &quot;Extra cleaning for kitchen&quot;, &quot;Pet-friendly cleaning supplies&quot;)"></textarea>
                    </div>

                    <!-- Navigation -->
                    <div
                        class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="prevStep()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full
                        hover:bg-gray-300 transition-colors">
                        <i class="fi fi-rr-angle-left mr-2"></i>Back
                    </button>
                        <button type="button" @click="nextStep()"
                            class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                            Next<i class="fi fi-rr-angle-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div> <!-- Removed extra closing div that was here (line 307) -->

        <!-- STEP 3: CONFIRMATION -->
        <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-4"
            x-transition:enter-end="opacity-100 transform translate-x-0" x-cloak>
            <div class="rounded-xl p-8 md:p-8">
                <h2
                    class="text-2xl font-sans font-bold italic w-full items-center text-center mb-2 mt-8 text-gray-900 dark:text-white">
                    Review Your Appointment
                </h2>

                <div class="p-48 pt-3 mt-6 m-12">
                    <!-- Appointment Summary -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 md:p-8">
                        <!-- Client Details -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-6">Client Details</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400 mb-3">Name</div>
                                    <div class="font-medium text-gray-900 dark:text-white"
                                        x-text="(formData.first_name + ' ' + formData.last_name) || '-'"></div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400 mb-3">Email</div>
                                    <div class="font-medium text-gray-900 dark:text-white"
                                        x-text="formData.email || '-'">
                                    </div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400 mb-3">Mobile Number</div>
                                    <div class="font-medium text-gray-900 dark:text-white"
                                        x-text="(formData.country_code + ' ' + formData.mobile_number) || '-'"></div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400 mb-3">Address</div>
                                    <div class="font-medium text-gray-900 dark:text-white"
                                        x-text="formData.street_address || '-'"></div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400 mb-3">City</div>
                                    <div class="font-medium text-gray-900 dark:text-white"
                                        x-text="(formData.postal_code + ' ' + formData.city) || '-'"></div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400 mb-3">District</div>
                                    <div class="font-medium text-gray-900 dark:text-white"
                                        x-text="formData.district || '-'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Appointment Details -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Appointment Details</h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Service Type</span>
                                    <span class="font-medium text-gray-900 dark:text-white"
                                        x-text="formData.service_type || '-'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Service Date</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        <span x-text="formData.service_date || '-'"></span>
                                        <span x-show="formData.is_sunday && !formData.is_holiday" class="ml-2 text-xs text-orange-600 dark:text-orange-400 font-semibold">(Sunday - 2x)</span>
                                        <span x-show="formData.is_holiday && !formData.is_sunday" class="ml-2 text-xs text-orange-600 dark:text-orange-400 font-semibold">(Holiday - 2x)</span>
                                        <span x-show="formData.is_sunday && formData.is_holiday" class="ml-2 text-xs text-orange-600 dark:text-orange-400 font-semibold">(Sunday & Holiday - 2x)</span>
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Service Time</span>
                                    <span class="font-medium text-gray-900 dark:text-white"
                                        x-text="formData.service_time || '-'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Unit Details -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Unit Details</h4>
                            <div class="space-y-3">
                                <template x-for="(unit, index) in unitData" :key="index">
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                                Unit <span x-text="index + 1"></span>
                                            </div>
                                            <div class="text-right">
                                                <!-- Deep Cleaning Calculation -->
                                                <div x-show="formData.service_type === 'Deep Cleaning'">
                                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                                        <span x-text="unit.hours || '0'"></span>h × €<span x-text="deepCleaningHourlyRate.toFixed(0)"></span>
                                                        <span x-show="formData.is_sunday || formData.is_holiday" class="text-orange-600 dark:text-orange-400 font-semibold"> × 2</span>
                                                    </div>
                                                </div>
                                                <!-- Price -->
                                                <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                                    €<span x-text="unit.price ? unit.price.toFixed(2) : '0.00'"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                            <div>
                                                <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                                <span class="font-medium text-gray-900 dark:text-white ml-1" x-text="unit.name || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600 dark:text-gray-400">Size:</span>
                                                <span class="font-medium text-gray-900 dark:text-white ml-1" x-text="unit.size ? unit.size + ' m²' : '-'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Special Requests -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700"
                            x-show="formData.special_requests">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Special Requests
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400" x-text="formData.special_requests"></p>
                        </div>

                        <!-- Pricing Notice -->
                        <div class="mb-6 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <p class="text-xs text-yellow-800 dark:text-yellow-200">
                                <i class="fi fi-rr-info mr-1"></i>
                                Sundays and holidays will be charged double the price. All rates are inclusive of VAT and prices are subject to change.
                            </p>
                        </div>

                        <!-- Total -->
                        <div
                            class="flex justify-between items-center pt-4 border-t-2 border-gray-300 dark:border-gray-600 mb-2">
                            <div>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">Total Amount</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">VAT Inclusive</div>
                            </div>
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400"
                                x-text="quotation > 0 ? '€' + quotation.toFixed(2) : '-'"></span>
                        </div>

                        <div class="text-sm font-semibold text-blue-600 dark:text-blue-400 mb-6 text-right">
                            Payment Amount: <span x-text="quotation > 0 ? '€' + quotation.toFixed(2) : '-'"></span>
                        </div>

                        <!-- Confirm Button -->
                        <button type="button" @click="submitForm()"
                            :disabled="submitting"
                            :class="{'opacity-50 cursor-not-allowed': submitting}"
                            class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors">
                            <span x-show="!submitting">Confirm Appointment</span>
                            <span x-show="submitting">Processing...</span>
                        </button>
                    </div>

                    <!-- Navigation -->
                    <div
                        class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="prevStep()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full
                        hover:bg-gray-300 transition-colors">
                            <i class="fi fi-rr-angle-left mr-2"></i>Back
                        </button>
                    </div>
                </div>
            </div>

        </div>
</x-layouts.general-stepper-form>

<script>
    function appointmentForm() {
        return {
            currentStep: 1,
            submitting: false,
            showDistrictSuggestions: false,
            filteredDistricts: [],
            quotation: 0,

            // Holidays data from backend
            holidays: @json($holidays ?? []),

            // Popular Helsinki districts (kaupunginosa)
            helsinkiDistricts: [
                'Kallio', 'Kamppi', 'Punavuori', 'Töölö', 'Kruununhaka',
                'Katajanokka', 'Etu-Töölö', 'Ullanlinna', 'Sörnäinen', 'Vallila',
                'Pasila', 'Lauttasaari', 'Munkkiniemi', 'Haaga', 'Pitäjänmäki',
                'Malmi', 'Vuosaari', 'Mellunmäki', 'Kontula', 'Herttoniemi',
                'Kulosaari', 'Jollas', 'Roihuvuori', 'Koskela', 'Oulunkylä'
            ],

            // Pricing data for Final Cleaning (standard rates)
            finalCleaningRates: {
                '20-50': { normal: 70.00, sunday: 140.00 },
                '51-70': { normal: 105.00, sunday: 210.00 },
                '71-90': { normal: 140.00, sunday: 280.00 },
                '91-120': { normal: 175.00, sunday: 350.00 },
                '121-140': { normal: 210.00, sunday: 420.00 },
                '141-160': { normal: 245.00, sunday: 490.00 },
                '161-180': { normal: 280.00, sunday: 560.00 },
                '181-220': { normal: 315.00, sunday: 630.00 }
            },

            // Deep Cleaning time estimates (in hours) and rate per hour
            deepCleaningEstimates: {
                '20-50': 2.5,
                '51-70': 3.5,
                '71-90': 4.5,
                '91-120': 5.5,
                '121-140': 6.5,
                '141-160': 7.5,
                '161-180': 8.5,
                '181-220': 10
            },
            deepCleaningHourlyRate: 48.00, // €48/hour based on Finnish market research

            // Store individual unit data
            unitData: [],

            formData: {
                // Step 1 - Client Details
                booking_type: @json($client->client_type ?? 'personal'),
                first_name: @json($client->first_name ?? ''),
                last_name: @json($client->last_name ?? ''),
                email: @json($user->email ?? ''),
                street_address: @json($client->street_address ?? ''),
                postal_code: @json($client->postal_code ?? ''),
                city: @json($client->city ?? ''),
                district: @json($client->district ?? ''),
                country_code: '+358',
                mobile_number: @json($user->phone ? str_replace('+358', '', $user->phone) : ''),

                // Step 2 - Appointment Details
                service_type: '',
                service_date: '',
                service_time: '',
                is_sunday: false,        // Auto-detected from service_date
                is_holiday: false,       // To be flagged by admin later
                units: 1,                // Default to 1 unit
                special_requests: ''
            },

            init() {
                // Initialize filtered districts
                this.filteredDistricts = this.helsinkiDistricts;

                // Initialize unit data array based on default units
                this.initializeUnitData();

                // Set up address autocomplete
                this.setupAddressAutocomplete();

                // Watch for changes in units - update unitData array dynamically
                this.$watch('formData.units', (newValue, oldValue) => {
                    console.log('Units changed from', oldValue, 'to', newValue);
                    this.updateUnitData(newValue);
                });

                // Watch for service type changes - recalculate all unit prices
                this.$watch('formData.service_type', (newType) => {
                    console.log('Service type changed to:', newType);
                    // Recalculate all unit prices when service type changes
                    this.unitData.forEach((unit, index) => {
                        if (unit.size) {
                            this.calculateUnitPrice(index);
                        }
                    });
                    this.calculateQuotation();
                });
            },

            showToast(message, type = 'error') {
                // Get toast container
                const toastContainer = Alpine.$data(document.querySelector('#toast-container'));

                const toastId = Date.now();
                const toast = {
                    id: toastId,
                    message: message,
                    type: type,
                    show: true
                };

                toastContainer.toasts.push(toast);

                // Auto-dismiss after 5 seconds
                setTimeout(() => {
                    const index = toastContainer.toasts.findIndex(t => t.id === toastId);
                    if (index !== -1) {
                        toastContainer.toasts[index].show = false;
                        // Remove from array after animation completes
                        setTimeout(() => {
                            toastContainer.toasts.splice(index, 1);
                        }, 300);
                    }
                }, 5000);
            },

            initializeUnitData() {
                const currentUnits = parseInt(this.formData.units) || 1;
                this.unitData = [];
                for (let i = 0; i < currentUnits; i++) {
                    this.unitData.push({
                        name: '',
                        size: '',
                        price: 0,
                        hours: 0
                    });
                }
                console.log('Initialized unitData:', this.unitData);
            },

            updateUnitData(newUnitsCount) {
                const targetCount = parseInt(newUnitsCount) || 1;
                const currentCount = this.unitData.length;

                if (targetCount > currentCount) {
                    // Add new units
                    for (let i = currentCount; i < targetCount; i++) {
                        this.unitData.push({
                            name: '',
                            size: '',
                            price: 0,
                            hours: 0
                        });
                    }
                } else if (targetCount < currentCount) {
                    // Remove units from the end
                    this.unitData.splice(targetCount);
                }

                console.log('Updated unitData:', this.unitData);
                this.calculateQuotation();
            },

            calculateUnitPrice(index) {
                const unit = this.unitData[index];
                if (!unit || !unit.size || !this.formData.service_type) {
                    return;
                }

                const isSundayOrHoliday = this.formData.is_sunday || this.formData.is_holiday;

                if (this.formData.service_type === 'Final Cleaning') {
                    // Final Cleaning pricing
                    const rates = this.finalCleaningRates[unit.size];
                    if (rates) {
                        unit.price = isSundayOrHoliday ? rates.sunday : rates.normal;
                        unit.hours = 0; // Not applicable for Final Cleaning
                    }
                } else if (this.formData.service_type === 'Deep Cleaning') {
                    // Deep Cleaning pricing (hourly rate)
                    const estimatedHours = this.deepCleaningEstimates[unit.size];
                    if (estimatedHours) {
                        unit.hours = estimatedHours;
                        const basePrice = estimatedHours * this.deepCleaningHourlyRate;
                        unit.price = isSundayOrHoliday ? (basePrice * 2) : basePrice;
                    }
                }

                // Recalculate total quotation
                this.calculateQuotation();
            },

            async setupAddressAutocomplete() {
                const NOMINATIM_API = 'https://nominatim.openstreetmap.org/search';
                let streetTimeout, postalTimeout;

                // Street address autocomplete
                const streetInput = document.querySelector('input[x-model="formData.street_address"]');
                if (streetInput) {
                    streetInput.addEventListener('input', async (e) => {
                        clearTimeout(streetTimeout);
                        const query = e.target.value;

                        if (query.length >= 3) {
                            streetTimeout = setTimeout(async () => {
                                try {
                                    const response = await fetch(
                                        `${NOMINATIM_API}?format=json&country=Finland&addressdetails=1&limit=5&q=${encodeURIComponent(query)}`,
                                        {
                                            headers: {
                                                'User-Agent': 'OptiCrew/1.0'
                                            }
                                        }
                                    );

                                    if (!response.ok) {
                                        console.warn('Street lookup error:', response.status);
                                        return;
                                    }

                                    const data = await response.json();

                                    if (Array.isArray(data) && data.length > 0 && data[0].address) {
                                        const addr = data[0].address;

                                        // Auto-fill city if empty
                                        if (!this.formData.city && (addr.city || addr.town || addr.municipality)) {
                                            this.formData.city = addr.city || addr.town || addr.municipality;
                                        }

                                        // Auto-fill postal code if empty
                                        if (!this.formData.postal_code && addr.postcode) {
                                            this.formData.postal_code = addr.postcode;
                                        }

                                        // Auto-fill district if empty
                                        if (!this.formData.district && (addr.suburb || addr.neighbourhood)) {
                                            this.formData.district = addr.suburb || addr.neighbourhood;
                                        }
                                    }
                                } catch (error) {
                                    console.error('Error fetching address:', error);
                                }
                            }, 500);
                        }
                    });
                }

                // Postal code to city lookup
                const postalInput = document.querySelector('input[x-model="formData.postal_code"]');
                if (postalInput) {
                    postalInput.addEventListener('input', async (e) => {
                        clearTimeout(postalTimeout);
                        const postcode = e.target.value.trim();

                        if (postcode.length === 5) {
                            postalTimeout = setTimeout(async () => {
                                try {
                                    const response = await fetch(
                                        `${NOMINATIM_API}?format=json&country=Finland&postalcode=${postcode}&addressdetails=1&limit=1`,
                                        {
                                            headers: {
                                                'User-Agent': 'OptiCrew/1.0'
                                            }
                                        }
                                    );

                                    if (!response.ok) {
                                        console.warn('Postal code lookup error:', response.status);
                                        return;
                                    }

                                    const data = await response.json();

                                    if (Array.isArray(data) && data.length > 0 && data[0].address) {
                                        const addr = data[0].address;

                                        // Auto-fill city
                                        if (!this.formData.city && (addr.city || addr.town || addr.municipality)) {
                                            this.formData.city = addr.city || addr.town || addr.municipality;
                                        }

                                        // Auto-fill district
                                        if (!this.formData.district && (addr.suburb || addr.neighbourhood)) {
                                            this.formData.district = addr.suburb || addr.neighbourhood;
                                            await this.fetchDistrictsForCity(this.formData.city);
                                        }
                                    }
                                } catch (error) {
                                    console.error('Error looking up postal code:', error);
                                }
                            }, 500);
                        }
                    });
                }
            },

            async fetchDistrictsForCity(cityName) {
                if (!cityName) return;

                try {
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&country=Finland&city=${encodeURIComponent(cityName)}&addressdetails=1&limit=20`,
                        {
                            headers: {
                                'User-Agent': 'OptiCrew/1.0'
                            }
                        }
                    );

                    if (!response.ok) {
                        console.warn('District fetch error:', response.status);
                        return;
                    }

                    const data = await response.json();

                    if (!Array.isArray(data)) {
                        console.warn('Invalid response format for districts');
                        return;
                    }

                    const districts = new Set();
                    data.forEach(item => {
                        if (item && item.address) {
                            if (item.address.suburb) districts.add(item.address.suburb);
                            if (item.address.neighbourhood) districts.add(item.address.neighbourhood);
                            if (item.address.quarter) districts.add(item.address.quarter);
                        }
                    });

                    this.helsinkiDistricts = Array.from(districts).sort();
                    this.filteredDistricts = this.helsinkiDistricts;
                } catch (error) {
                    console.error('Error fetching districts:', error);
                }
            },

            async searchDistrict() {
                const query = this.formData.district.toLowerCase();

                // Fetch districts based on city if not already fetched
                if (this.formData.city && this.helsinkiDistricts.length === 0) {
                    await this.fetchDistrictsForCity(this.formData.city);
                }

                if (query.length === 0) {
                    this.filteredDistricts = this.helsinkiDistricts;
                } else {
                    this.filteredDistricts = this.helsinkiDistricts.filter(district =>
                        district.toLowerCase().includes(query)
                    );
                }
                this.showDistrictSuggestions = true;
            },

            selectDistrict(district) {
                this.formData.district = district;
                this.showDistrictSuggestions = false;
            },

            checkDateAndCalculate() {
                // Check if the selected date is a Sunday or Holiday
                if (this.formData.service_date) {
                    const selectedDate = new Date(this.formData.service_date);
                    // getDay() returns 0 for Sunday, 1 for Monday, etc.
                    this.formData.is_sunday = (selectedDate.getDay() === 0);

                    // Check if the selected date is a holiday
                    this.formData.is_holiday = this.holidays.some(holiday => holiday.date === this.formData.service_date);

                    console.log('Date checked:', {
                        date: this.formData.service_date,
                        dayOfWeek: selectedDate.getDay(),
                        isSunday: this.formData.is_sunday,
                        isHoliday: this.formData.is_holiday
                    });

                    // Recalculate all unit prices since Sunday/Holiday affects pricing
                    this.unitData.forEach((unit, index) => {
                        if (unit.size) {
                            this.calculateUnitPrice(index);
                        }
                    });
                }

                // Trigger total calculation
                this.calculateQuotation();
            },

            calculateQuotation() {
                // Reset quotation
                this.quotation = 0;

                // Validate required fields
                if (!this.formData.service_type || !this.unitData.length) {
                    console.log('Cannot calculate: missing service type or no units');
                    return;
                }

                // Sum all unit prices (units are already calculated individually)
                this.quotation = this.unitData.reduce((total, unit) => {
                    return total + (unit.price || 0);
                }, 0);

                console.log('Quotation calculated:', {
                    service: this.formData.service_type,
                    units: this.formData.units,
                    isSunday: this.formData.is_sunday,
                    isHoliday: this.formData.is_holiday,
                    unitData: this.unitData,
                    total: this.quotation
                });
            },

            validateStep1() {
                const required = [
                    { field: this.formData.first_name, name: 'First Name' },
                    { field: this.formData.last_name, name: 'Last Name' },
                    { field: this.formData.email, name: 'Email' },
                    { field: this.formData.street_address, name: 'Street Address' },
                    { field: this.formData.postal_code, name: 'Postal Code' },
                    { field: this.formData.city, name: 'City' },
                    { field: this.formData.district, name: 'District' },
                    { field: this.formData.mobile_number, name: 'Mobile Number' }
                ];

                const missing = required.filter(item => !item.field || item.field.trim() === '');

                if (missing.length > 0) {
                    const fields = missing.map(item => item.name).join(', ');
                    this.showToast(`Please fill in the following required fields: ${fields}`);
                    return false;
                }

                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(this.formData.email)) {
                    this.showToast('Please enter a valid email address');
                    return false;
                }

                // Postal code validation (5 digits)
                if (this.formData.postal_code.length !== 5 || !/^\d+$/.test(this.formData.postal_code)) {
                    this.showToast('Please enter a valid 5-digit postal code');
                    return false;
                }

                return true;
            },

            validateStep2() {
                // Check service type
                if (!this.formData.service_type) {
                    this.showToast('Please select a service type');
                    return false;
                }

                // Check service date
                if (!this.formData.service_date) {
                    this.showToast('Please select a service date');
                    return false;
                }

                // Check service time
                if (!this.formData.service_time) {
                    this.showToast('Please select a service time');
                    return false;
                }

                // Check if at least one unit exists
                if (this.formData.units < 1) {
                    this.showToast('Please add at least one unit');
                    return false;
                }

                // Check all units have required fields
                for (let i = 0; i < this.unitData.length; i++) {
                    const unit = this.unitData[i];

                    if (!unit.name || unit.name.trim() === '') {
                        this.showToast(`Please enter a name for Unit ${i + 1}`);
                        return false;
                    }

                    if (!unit.size) {
                        this.showToast(`Please select a size for Unit ${i + 1}`);
                        return false;
                    }
                }

                return true;
            },

            nextStep() {
                // Validate current step before proceeding
                if (this.currentStep === 1) {
                    if (!this.validateStep1()) {
                        return;
                    }
                } else if (this.currentStep === 2) {
                    if (!this.validateStep2()) {
                        return;
                    }
                }

                // Proceed to next step
                if (this.currentStep < 3) {
                    this.currentStep++;
                    this.updateStepperUI();

                    // Scroll to top when changing steps
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    this.updateStepperUI();

                    // Scroll to top when changing steps
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            updateStepperUI() {
                // Update stepper in header
                window.dispatchEvent(new CustomEvent('update-stepper', {
                    detail: { step: this.currentStep }
                }));
            },

            async submitForm() {
                this.submitting = true;

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    // Send all unit details to backend
                    const firstUnit = this.unitData[0] || {};

                    const submissionData = {
                        // Step 1 fields
                        booking_type: 'personal',  // Default to personal (removed from UI)
                        first_name: this.formData.first_name,
                        last_name: this.formData.last_name,
                        email: this.formData.email,
                        country_code: this.formData.country_code,
                        mobile_number: this.formData.mobile_number,
                        street_address: this.formData.street_address,
                        postal_code: this.formData.postal_code,
                        city: this.formData.city,
                        district: this.formData.district,  // Required field
                        billing_address: this.formData.street_address + ', ' + this.formData.postal_code + ' ' + this.formData.city,

                        // Step 2 fields
                        service_type: this.formData.service_type,
                        service_date: this.formData.service_date,
                        service_time: this.formData.service_time,
                        is_sunday: this.formData.is_sunday,
                        is_holiday: this.formData.is_holiday || false,
                        units: this.formData.units,
                        unit_size: firstUnit.size,  // From first unit (for backward compatibility)
                        room_identifier: firstUnit.name,  // From first unit (for backward compatibility)
                        unit_details: this.unitData,  // Send all unit details
                        special_requests: this.formData.special_requests || ''
                    };

                    console.log('Submitting data:', submissionData);

                    const response = await fetch('{{ route("client.appointment.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(submissionData)
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        this.showToast(data.message, 'info');
                        console.log('Appointment created:', data);

                        // Redirect to client dashboard after a brief delay
                        setTimeout(() => {
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            } else {
                                window.location.href = '{{ route("client.dashboard") }}';
                            }
                        }, 1500);
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            const errorMessages = Object.entries(data.errors)
                                .map(([field, messages]) => messages.join(', '))
                                .join(', ');
                            this.showToast('Validation errors: ' + errorMessages);
                        } else {
                            this.showToast(data.message || 'Failed to book appointment. Please try again.');
                        }
                        this.submitting = false;
                    }

                } catch (error) {
                    console.error('Error submitting appointment:', error);
                    this.showToast('An error occurred while booking your appointment. Please try again.');
                    this.submitting = false;
                }
            }
        }
    }
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>