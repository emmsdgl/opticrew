<x-layouts.general-stepper-form title="Client Appointment Form" :steps="['Client Details', 'Service Details', 'Confirmation']" :currentStep="$currentStep ?? 1">
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
                    <!-- Booking Type Section -->
                    <div class="mb-8">
                        <label class="block text-sm font-semibold mb-3 text-gray-700 dark:text-gray-300">
                            Booking Type
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Personal Option -->
                            <label class="relative">
                                <input type="radio" x-model="formData.booking_type" value="personal"
                                    class="peer sr-only">
                                <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 cursor-pointer
                                            peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20
                                            hover:border-gray-400 dark:hover:border-gray-500 transition-all">
                                    <div class="font-semibold text-gray-900 dark:text-white mb-1">Personal</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        If you are booking for a single individual with a single location on a listing
                                    </div>
                                </div>
                            </label>

                            <!-- Company Option -->
                            <label class="relative">
                                <input type="radio" x-model="formData.booking_type" value="company"
                                    class="peer sr-only">
                                <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 cursor-pointer
                                            peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20
                                            hover:border-gray-400 dark:hover:border-gray-500 transition-all">
                                    <div class="font-semibold text-gray-900 dark:text-white mb-1">Company</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        If you are booking for a business with a multiple member on a service
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

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

                    <!-- District (Kaupunginosa) and E-Invoice -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                                District (Kaupunginosa)
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

                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">E-Invoice
                                Number (Optional)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fi fi-rr-receipt"></i>
                                </span>
                                <input type="text" x-model="formData.einvoice_number" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Your Finnish e-invoice number">
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
                    What's the cleaning service details?
                </h2>

                <div class="p-48 pt-3 mt-6 m-12">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Service
                            Type <span class="text-red-500">*</span></label>
                        <select x-model="formData.service_type" required @change="calculateQuotation()" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a Service</option>
                            <option value="Deep Cleaning">Deep Cleaning</option>
                            <option value="Daily Room Cleaning">Daily Room Cleaning</option>
                            <option value="Snowout Cleaning">Snowout Cleaning</option>
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
                                <input type="date" x-model="formData.service_date" required @change="checkDateAndCalculate()" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <!-- Sunday/Holiday indicator -->
                            <div x-show="formData.is_sunday || formData.is_holiday" class="mt-2 text-xs text-orange-600 dark:text-orange-400 flex items-center">
                                <i class="fi fi-rr-calendar-star mr-1"></i>
                                <span x-show="formData.is_holiday && !formData.is_sunday">Holiday rate will apply (+50%)</span>
                                <span x-show="formData.is_sunday && !formData.is_holiday">Sunday rate will apply (+50%)</span>
                                <span x-show="formData.is_sunday && formData.is_holiday">Sunday & Holiday rate will apply (+50%)</span>
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
                        <div class="flex justify-between mb-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Number of
                                Units <span class="text-red-500">*</span></label>
                            <span class="text-sm font-semibold text-blue-600 dark:text-blue-400"
                                x-text="formData.units + ' unit(s)'"></span>
                        </div>
                        <input type="range" x-model="formData.units" @input="calculateQuotation()" min="1" max="10" required
                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <span>1 Unit</span>
                            <span x-text="formData.units + ' units'"></span>
                            <span>10 Units</span>
                        </div>
                    </div>

                    <!-- Unit Size -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Unit
                            Size <span class="text-red-500">*</span></label>
                        <select x-model="formData.unit_size" required @change="calculateQuotation()" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select unit size</option>
                            <option value="40-60">40-60 m²</option>
                            <option value="60-90">60-90 m²</option>
                            <option value="90-120">90-120 m²</option>
                            <option value="120-150">120-150 m²</option>
                            <option value="150-180">150-180 m²</option>
                            <option value="180-220">180-220 m²</option>
                        </select>
                    </div>

                    <!-- Quotation Display -->
                    <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200 dark:border-blue-800" x-show="quotation > 0">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Estimated Quotation</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span x-text="formData.units"></span> unit(s) ×
                                    <span x-text="formData.unit_size"></span> m²
                                    <span x-show="formData.is_holiday && !formData.is_sunday" class="text-orange-600 dark:text-orange-400 font-semibold">(Holiday rate)</span>
                                    <span x-show="formData.is_sunday && !formData.is_holiday" class="text-orange-600 dark:text-orange-400 font-semibold">(Sunday rate)</span>
                                    <span x-show="formData.is_sunday && formData.is_holiday" class="text-orange-600 dark:text-orange-400 font-semibold">(Sunday & Holiday rate)</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    €<span x-text="quotation.toFixed(2)"></span>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Total Price (excl. VAT)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Room Identifier (Cabin Name) -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Cabin/Unit Name
                            <span class="text-red-500">*</span></label>
                        <input type="text" x-model="formData.room_identifier" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Ex: Kelo A, Cabin 5, Metsakoti B">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter the name or identifier of the cabin/unit</p>
                    </div>

                    <!-- Special Requests -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Special
                            Requests</label>
                        <textarea x-model="formData.special_requests" rows="4" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg 
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Type in special (eg. &quot;Stressed Cleaning&quot;, &quot;Help&quot;)"></textarea>
                    </div>

                    <!-- Add Room Button -->
                    <button type="button"
                        class="w-full py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:border-blue-400 hover:text-blue-500 transition-colors">
                        <i class="fa-solid fa-plus text-sm mr-3"></i>Add Room
                    </button>

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
                                    <div class="text-gray-500 dark:text-gray-400 mb-3">Booking Type</div>
                                    <div class="font-medium text-gray-900 dark:text-white capitalize"
                                        x-text="formData.booking_type || '-'"></div>
                                </div>
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
                                    <div class="text-gray-500 dark:text-gray-400 mb-3">Location</div>
                                    <div class="font-medium text-gray-900 dark:text-white"
                                        x-text="formData.location || '-'"></div>
                                </div>
                                <div>
                                    <div class="text-gray-500 dark:text-gray-400 mb-3">Billing Address</div>
                                    <div class="font-medium text-gray-900 dark:text-white"
                                        x-text="formData.billing_address || '-'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Service Details -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Service Details</h4>
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
                                        <span x-show="formData.is_sunday" class="ml-2 text-xs text-orange-600 dark:text-orange-400 font-semibold">(Sunday)</span>
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Service Time</span>
                                    <span class="font-medium text-gray-900 dark:text-white"
                                        x-text="formData.service_time || '-'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Cabin/Unit Name</span>
                                    <span class="font-medium text-gray-900 dark:text-white"
                                        x-text="formData.room_identifier || '-'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Units -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Units & Pricing</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Number of Units</span>
                                    <span class="font-medium text-gray-900 dark:text-white"
                                        x-text="formData.units + ' unit(s)'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Unit Size</span>
                                    <span class="font-medium text-gray-900 dark:text-white"
                                        x-text="formData.unit_size ? formData.unit_size + ' m²' : '-'"></span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400 font-semibold">Subtotal</span>
                                    <span class="font-semibold text-gray-900 dark:text-white"
                                        x-text="quotation > 0 ? '€' + quotation.toFixed(2) : '-'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Special Requests -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700"
                            x-show="formData.special_requests">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Special Requests
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400" x-text="formData.special_requests"></p>
                        </div>

                        <!-- Additional Charges -->
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                Additional Charges
                            </h4>
                            <div class="text-sm text-gray-500 dark:text-gray-400 italic">
                                If there's no charge, Cleaning fee varies from service
                            </div>
                        </div>

                        <!-- Price Summary -->
                        <div class="space-y-2 text-sm mb-6">
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Subtotal (excl. VAT)</span>
                                <span x-text="quotation > 0 ? '€' + quotation.toFixed(2) : '-'"></span>
                            </div>
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>VAT (24%)</span>
                                <span x-text="quotation > 0 ? '€' + (quotation * 0.24).toFixed(2) : '-'"></span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div
                            class="flex justify-between items-center pt-4 border-t-2 border-gray-300 dark:border-gray-600 mb-2">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">Total (incl. VAT)</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white"
                                x-text="quotation > 0 ? '€' + (quotation * 1.24).toFixed(2) : '-'"></span>
                        </div>

                        <div class="text-sm font-bold text-blue-600 dark:text-blue-400 mb-6">
                            Payment Amount: <span x-text="quotation > 0 ? '€' + (quotation * 1.24).toFixed(2) : '-'"></span>
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

            // Pricing data for all cleaning services (standard rates)
            pricingRates: {
                '40-60': { normal: 68.25, sunday: 102.50 },
                '60-90': { normal: 84.00, sunday: 126.00 },
                '90-120': { normal: 99.75, sunday: 149.50 },
                '120-150': { normal: 115.50, sunday: 173.25 },
                '150-180': { normal: 131.25, sunday: 196.75 },
                '180-220': { normal: 157.50, sunday: 236.25 }
            },

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
                mobile_number: @json($client->phone_number ? str_replace('+358', '', $client->phone_number) : ''),
                billing_address: @json($client->billing_address ?? ''),
                einvoice_number: @json($client->einvoice_number ?? ''),

                // Step 2 - Service Details
                service_type: '',
                service_date: '',
                service_time: '',
                is_sunday: false,        // Auto-detected from service_date
                is_holiday: false,       // To be flagged by admin later
                units: 5,
                unit_size: '',
                room_identifier: '',
                special_requests: ''
            },

            init() {
                // Initialize filtered districts
                this.filteredDistricts = this.helsinkiDistricts;

                // Set up address autocomplete
                this.setupAddressAutocomplete();
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
                }

                // Trigger calculation
                this.calculateQuotation();
            },

            calculateQuotation() {
                // Reset quotation
                this.quotation = 0;

                // Validate required fields
                if (!this.formData.service_type || !this.formData.unit_size || !this.formData.units) {
                    return;
                }

                // Get pricing for selected unit size
                const sizeRates = this.pricingRates[this.formData.unit_size];

                if (!sizeRates) {
                    console.warn('No pricing data for size:', this.formData.unit_size);
                    return;
                }

                // Get rate based on whether it's Sunday/Holiday
                const ratePerUnit = (this.formData.is_sunday || this.formData.is_holiday)
                    ? sizeRates.sunday
                    : sizeRates.normal;

                // Calculate total: units × rate
                this.quotation = this.formData.units * ratePerUnit;

                console.log('Quotation calculated:', {
                    service: this.formData.service_type,
                    units: this.formData.units,
                    size: this.formData.unit_size,
                    isSunday: this.formData.is_sunday,
                    isHoliday: this.formData.is_holiday,
                    ratePerUnit: ratePerUnit,
                    total: this.quotation
                });
            },

            nextStep() {
                if (this.currentStep < 3) {
                    this.currentStep++;
                    this.updateStepperUI();
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    this.updateStepperUI();
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

                    const response = await fetch('{{ route("client.appointment.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        alert(data.message);
                        console.log('Appointment created:', data);

                        // Redirect to client dashboard
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            window.location.href = '{{ route("client.dashboard") }}';
                        }
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            const errorMessages = Object.entries(data.errors)
                                .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
                                .join('\n');
                            alert('Validation errors:\n' + errorMessages);
                        } else {
                            alert(data.message || 'Failed to book appointment. Please try again.');
                        }
                        this.submitting = false;
                    }

                } catch (error) {
                    console.error('Error submitting appointment:', error);
                    alert('An error occurred while booking your appointment. Please try again.');
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