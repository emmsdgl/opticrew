<x-layouts.general-stepper-form title="Client Appointment Form" :steps="['Client Details', 'Appointment Details', 'Confirmation']" :currentStep="$currentStep ?? 1">
    <div class="max-w-6xl mx-auto" x-data="appointmentForm()">

        <!-- Breadcrumb -->
        <div class="pt-6">
            <x-employer-components.breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('client.dashboard')],
                ['label' => 'Book a Service', 'url' => '#'],
            ]" />
        </div>

        <!-- STEP 1: CLIENT DETAILS -->
        <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-4"
            x-transition:enter-end="opacity-100 transform translate-x-0">
            <div class="rounded-xl p-8 md:p-8">
                <h2
                    class="text-2xl font-sans font-bold italic w-full items-center text-center mb-2 mt-8 text-gray-900 dark:text-white">
                    What are the details of the client?
                </h2>

                <div class="px-56 pt-3 mt-6">
                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <x-material-ui.input-field label="First Name" model="formData.first_name" icon="fi fi-rr-user" placeholder="Firstname" required />
                        <x-material-ui.input-field label="Last Name" model="formData.last_name" icon="fi fi-rr-user" placeholder="Lastname" required />
                    </div>

                    <!-- Email Address -->
                    <div class="mb-4">
                        <x-material-ui.input-field label="Email Address" type="email" model="formData.email" icon="fi fi-rr-envelope" placeholder="Ex: yourname@example.com" required />
                    </div>

                    <!-- Finnish Address Structure -->
                    <div class="mb-4">
                        <x-material-ui.input-field label="Street Address" model="formData.street_address" icon="fi fi-rr-marker" placeholder="Ex: Mannerheimintie 123 A 45" required />
                    </div>

                    <!-- Region (State) and City -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        {{-- Region Dropdown --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Region (Maakunta)<span class="text-red-500">*</span></label>
                            <div class="relative" x-data="{ regionOpen: false, regionSearch: '' }">
                                <button type="button" @click="regionOpen = !regionOpen"
                                    class="w-full flex items-center justify-between px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 text-sm">
                                    <span class="flex items-center gap-2">
                                        <i class="fi fi-rr-map-marker text-blue-500 text-xs"></i>
                                        <span x-text="formData.state || 'Select Region...'" :class="formData.state ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"></span>
                                    </span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="regionOpen && 'rotate-180'"></i>
                                </button>
                                <div x-show="regionOpen" @click.away="regionOpen = false" x-transition
                                    class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-hidden">
                                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                        <input type="text" x-model="regionSearch" placeholder="Search region..."
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div class="max-h-48 overflow-y-auto">
                                        <template x-for="s in cscStates.filter(s => s.name.toLowerCase().includes(regionSearch.toLowerCase()))" :key="s.iso2">
                                            <button type="button"
                                                @click="formData.state = s.name; regionOpen = false; regionSearch = ''; onStateChange();"
                                                class="w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-left"
                                                :class="formData.state === s.name ? 'bg-blue-50 dark:bg-blue-900/20' : ''">
                                                <span class="text-gray-900 dark:text-white" x-text="s.name"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- City Dropdown --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Municipality (Kunta) <span class="text-red-500">*</span></label>
                            <div class="relative" x-data="{ cityOpen: false, citySearch: '' }">
                                <button type="button" @click="if (cscCities.length > 0) cityOpen = !cityOpen"
                                    :class="cscCities.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="w-full flex items-center justify-between px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 text-sm">
                                    <span class="flex items-center gap-2">
                                        <i class="fi fi-rr-building text-blue-500 text-xs"></i>
                                        <span x-text="formData.city || 'Select City...'" :class="formData.city ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"></span>
                                    </span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="cityOpen && 'rotate-180'"></i>
                                </button>
                                <div x-show="cityOpen" @click.away="cityOpen = false" x-transition
                                    class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-hidden">
                                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                        <input type="text" x-model="citySearch" placeholder="Search city..."
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div class="max-h-48 overflow-y-auto">
                                        <template x-for="c in cscCities.filter(c => c.name.toLowerCase().includes(citySearch.toLowerCase()))" :key="c.name">
                                            <button type="button"
                                                @click="formData.city = c.name; cityOpen = false; citySearch = ''; onCityChange();"
                                                class="w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-left"
                                                :class="formData.city === c.name ? 'bg-blue-50 dark:bg-blue-900/20' : ''">
                                                <span class="text-gray-900 dark:text-white" x-text="c.name"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Postal Code (auto-filled) -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Postal Code <span class="text-red-500">*</span></label>
                        <x-material-ui.input-field model="formData.postal_code" icon="fi fi-rr-map-pin" placeholder="00100" maxlength="5" required />
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Auto-filled from city selection. You can edit manually.</p>
                    </div>

                    <!-- Mobile Number -->
                    <div class="mb-4">
                        <div class="flex gap-2">
                            <div class="w-36 flex-shrink-0">
                                <select x-model="formData.country_code" required class="w-full h-full px-3 py-3 text-sm border border-gray-200 dark:border-gray-700 rounded-xl
                                           bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                           focus:outline-none focus:border-blue-500 dark:focus:border-blue-400
                                           focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)] dark:focus:shadow-[0_0_0_3px_rgba(96,165,250,0.1)]
                                           transition-all duration-200">
                                    <option value="+358">🇫🇮 +358</option>
                                    <option value="+46">🇸🇪 +46</option>
                                    <option value="+47">🇳🇴 +47</option>
                                    <option value="+45">🇩🇰 +45</option>
                                    <option value="+1">🇺🇸 +1</option>
                                    <option value="+44">🇬🇧 +44</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <x-material-ui.input-field label="Mobile Number" type="tel" model="formData.mobile_number" icon="fi fi-rr-phone-call" placeholder="40 1234567" required />
                            </div>
                        </div>
                    </div>

                    <!-- District (Kaupunginosa) -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                            District (Kaupunginosa) <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            {{-- Trigger button --}}
                            <button type="button"
                                @click="if (formData.state && formData.city && !districtLoading) districtOpen = !districtOpen"
                                :disabled="!formData.state || !formData.city || districtLoading"
                                class="w-full flex items-center justify-between px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="flex items-center gap-2">
                                    <i class="fi fi-rr-marker text-blue-500 text-xs"></i>
                                    <span x-show="districtLoading"><i class="fa-solid fa-spinner fa-spin text-blue-400 text-xs mr-1"></i>Loading...</span>
                                    <span x-show="!districtLoading && !isCustomDistrict" x-text="formData.district || 'Select District...'"
                                          :class="formData.district ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"></span>
                                    <span x-show="!districtLoading && isCustomDistrict" class="text-gray-900 dark:text-white"
                                          x-text="customDistrictValue || 'Others (type below)'"></span>
                                </span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="districtOpen && 'rotate-180'"></i>
                            </button>

                            {{-- Dropdown panel --}}
                            <div x-show="districtOpen" @click.away="districtOpen = false" x-transition
                                class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-72 overflow-hidden">

                                {{-- Search --}}
                                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                    <input type="text" x-model="districtSearch" placeholder="Search district..."
                                        class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>

                                {{-- District options (always visible) --}}
                                <div class="max-h-40 overflow-y-auto">
                                    <template x-for="d in filteredDistricts.filter(d => d.toLowerCase().includes(districtSearch.toLowerCase()))" :key="d">
                                        <button type="button"
                                            @click="isCustomDistrict = false; customDistrictValue = ''; formData.district = d; districtOpen = false; districtSearch = '';"
                                            class="w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-left"
                                            :class="formData.district === d && !isCustomDistrict ? 'bg-blue-50 dark:bg-blue-900/20' : ''">
                                            <span class="text-gray-900 dark:text-white" x-text="d"></span>
                                        </button>
                                    </template>
                                </div>

                                {{-- Others: text input always at the bottom --}}
                                <div class="p-2.5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                    <label class="block text-[10px] font-semibold text-gray-400 dark:text-gray-500 mb-1 uppercase tracking-wider">Others</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" x-model="customDistrictValue" x-ref="customDistrictInput"
                                               @input="isCustomDistrict = true; formData.district = customDistrictValue"
                                               @focus="isCustomDistrict = true"
                                               @keydown.enter.prevent="if (customDistrictValue.trim()) { formData.district = customDistrictValue.trim(); districtOpen = false; }"
                                               class="flex-1 px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500"
                                               placeholder="Type district name...">
                                        <button type="button"
                                            @click="if (customDistrictValue.trim()) { formData.district = customDistrictValue.trim(); districtOpen = false; }"
                                            :disabled="!customDistrictValue.trim()"
                                            class="px-3 py-1.5 text-xs font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                   
                    <!-- Navigation -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('client.dashboard') }}">
                            <button type="button" class="px-6 py-2 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full
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

                <div class="px-48 pt-3 mt-6">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Service
                            Type <span class="text-red-500">*</span></label>
                        <x-material-ui.select-dropdown
                            model="formData.service_type"
                            placeholder="Select a Service"
                            placeholderDesc="Choose from available cleaning services"
                            placeholderIcon="fas fa-broom"
                            :options="[
                                ['value' => 'Final Cleaning', 'label' => 'Final Cleaning', 'description' => 'Cabins/Cottages & Holiday Apartments', 'icon' => 'fas fa-house', 'iconBg' => 'bg-rose-50 dark:bg-rose-900/30', 'iconColor' => 'text-rose-500 dark:text-rose-400'],
                                ['value' => 'Deep Cleaning', 'label' => 'Deep Cleaning', 'description' => 'Hourly Rate — €48/hr', 'icon' => 'fas fa-spray-can-sparkles', 'iconBg' => 'bg-blue-50 dark:bg-blue-900/30', 'iconColor' => 'text-blue-500 dark:text-blue-400'],
                                ['value' => 'Daily Cleaning', 'label' => 'Daily Cleaning', 'description' => 'Hourly Rate — €35/hr', 'icon' => 'fas fa-calendar-day', 'iconBg' => 'bg-green-50 dark:bg-green-900/30', 'iconColor' => 'text-green-500 dark:text-green-400'],
                                ['value' => 'Snowout Cleaning', 'label' => 'Snow Out Cleaning', 'description' => 'Hourly Rate — €55/hr', 'icon' => 'fas fa-snowflake', 'iconBg' => 'bg-purple-50 dark:bg-purple-900/30', 'iconColor' => 'text-purple-500 dark:text-purple-400'],
                                ['value' => 'General Cleaning', 'label' => 'General Cleaning', 'description' => 'Hourly Rate — €40/hr', 'icon' => 'fas fa-broom', 'iconBg' => 'bg-teal-50 dark:bg-teal-900/30', 'iconColor' => 'text-teal-500 dark:text-teal-400'],
                                ['value' => 'Hotel Cleaning', 'label' => 'Hotel Cleaning', 'description' => 'Hourly Rate — €42/hr', 'icon' => 'fas fa-hotel', 'iconBg' => 'bg-amber-50 dark:bg-amber-900/30', 'iconColor' => 'text-amber-500 dark:text-amber-400'],
                            ]"
                        />
                    </div>

                    <!-- Service Date (full width) -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Service
                            Date <span class="text-red-500">*</span></label>
                        <div x-effect="if (formData.service_date) checkDateAndCalculate()">
                            <x-material-ui.calendar-picker model="formData.service_date" :min="now()->toDateString()" />
                        </div>
                        <!-- Sunday/Holiday indicator -->
                        <div x-show="formData.is_sunday || formData.is_holiday" class="mt-2 text-xs text-orange-600 dark:text-orange-400 flex items-center">
                            <i class="fi fi-rr-calendar-star mr-1"></i>
                            <span x-show="formData.is_holiday && !formData.is_sunday">Holiday - Double the price will apply</span>
                            <span x-show="formData.is_sunday && !formData.is_holiday">Sunday - Double the price will apply</span>
                            <span x-show="formData.is_sunday && formData.is_holiday">Sunday & Holiday - Double the price will apply</span>
                        </div>
                    </div>

                    <!-- Date notice: blocked (tomorrow) — full width -->
                    <div x-show="dateNotice === 'blocked'" x-cloak
                         class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg w-full">
                        <div class="flex items-start">
                            <i class="fi fi-rr-circle-exclamation text-red-500 mt-0.5 mr-2 flex-shrink-0"></i>
                            <p class="text-sm text-red-700 dark:text-red-400" x-text="dateNoticeMessage"></p>
                        </div>
                    </div>

                    <!-- Date notice: priority available (2 days) — full width -->
                    <div x-show="dateNotice === 'priority'" x-cloak
                         class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg w-full">
                        <div class="flex items-start">
                            <i class="fi fi-rr-triangle-warning text-amber-500 mt-0.5 mr-2 flex-shrink-0"></i>
                            <p class="text-sm text-amber-700 dark:text-amber-400" x-text="dateNoticeMessage"></p>
                        </div>
                        <label class="flex items-center mt-3 cursor-pointer group">
                            <input type="checkbox" x-model="formData.is_priority"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 rounded
                                          focus:ring-blue-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-gray-800 dark:text-gray-200 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                Priority Clean — I need this service on short notice
                            </span>
                        </label>
                    </div>

                    <!-- Service Time and Number of Units (side by side) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        {{-- Service Time --}}
                        <div x-data="{ timeOpen: false, timeSearch: '' }">
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">Service
                                Time <span class="text-red-500">*</span></label>
                            <div class="relative">
                                {{-- Trigger --}}
                                <button type="button" @click="timeOpen = !timeOpen"
                                    class="w-full flex items-center justify-between px-4 py-2.5 border border-gray-400 dark:border-gray-700 rounded-xl
                                           focus:outline-none focus:border-blue-500 dark:focus:border-blue-400
                                           focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)] dark:focus:shadow-[0_0_0_3px_rgba(96,165,250,0.1)]
                                           bg-white dark:bg-gray-800 text-sm transition-all duration-200">
                                    <span class="flex items-center gap-2">
                                        <i class="fi fi-rr-clock text-sm text-blue-600"></i>
                                        <span x-show="loadingSlots" class="text-gray-400"><i class="fa-solid fa-spinner fa-spin text-xs mr-1"></i>Loading...</span>
                                        <span x-show="!loadingSlots" :class="formData.service_time ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"
                                              x-text="formData.service_time ? (() => { const [h,m] = formData.service_time.split(':'); const hr = +h > 12 ? +h-12 : (+h||12); return hr+':'+m+' '+(+h>=12?'PM':'AM'); })() : 'Select time...'"></span>
                                    </span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="timeOpen && 'rotate-180'"></i>
                                </button>

                                {{-- Dropdown --}}
                                <div x-show="timeOpen" @click.away="timeOpen = false" x-transition
                                    class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                    {{-- Search --}}
                                    <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                        <input type="text" x-model="timeSearch" placeholder="Search time..."
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    {{-- Time list (Blade-rendered, Alpine-toggled) --}}
                                    <div class="max-h-48 overflow-y-auto time-scroll">
                                        @for($h = 6; $h <= 21; $h++)
                                            @foreach(['00','15','30','45'] as $m)
                                                @php
                                                    $val = str_pad($h, 2, '0', STR_PAD_LEFT) . ':' . $m;
                                                    $hr = $h > 12 ? $h - 12 : ($h == 0 ? 12 : $h);
                                                    $ampm = $h >= 12 ? 'PM' : 'AM';
                                                    $lbl = $hr . ':' . $m . ' ' . $ampm;
                                                @endphp
                                                <button type="button"
                                                    x-show="!timeSearch || '{{ strtolower($lbl) }}'.includes(timeSearch.toLowerCase()) || '{{ $val }}'.includes(timeSearch)"
                                                    @click="if (!bookedSlots.includes('{{ $val }}')) { formData.service_time = '{{ $val }}'; timeOpen = false; timeSearch = ''; }"
                                                    :disabled="bookedSlots.includes('{{ $val }}')"
                                                    class="w-full flex items-center justify-between px-4 py-2 text-sm transition-colors text-left"
                                                    :class="{
                                                        'bg-blue-50 dark:bg-blue-900/20': formData.service_time === '{{ $val }}' && !bookedSlots.includes('{{ $val }}'),
                                                        'hover:bg-blue-50 dark:hover:bg-blue-900/20': !bookedSlots.includes('{{ $val }}'),
                                                        'opacity-40 cursor-not-allowed line-through': bookedSlots.includes('{{ $val }}'),
                                                    }">
                                                    <span :class="bookedSlots.includes('{{ $val }}') ? 'text-gray-400 dark:text-gray-600' : 'text-gray-900 dark:text-white'">{{ $lbl }}</span>
                                                    <span class="text-xs" :class="bookedSlots.includes('{{ $val }}') ? 'text-red-400 dark:text-red-500' : 'text-gray-400 dark:text-gray-500'"
                                                          x-text="bookedSlots.includes('{{ $val }}') ? 'Booked' : '{{ $val }}'"></span>
                                                </button>
                                            @endforeach
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Number of Units --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-300">
                                Number of Units <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <button type="button"
                                        @click="formData.units = Math.max(1, formData.units - 1)"
                                        :disabled="formData.units <= 1"
                                        :class="formData.units <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-300 dark:hover:bg-gray-600'"
                                        class="px-4 py-2.5 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg transition-colors flex-shrink-0">
                                    <i class="fi fi-rr-minus text-sm"></i>
                                </button>
                                <input type="number"
                                       x-model.number="formData.units"
                                       @input="formData.units = Math.max(1, Math.min(20, parseInt(formData.units) || 1))"
                                       min="1"
                                       max="20"
                                       required
                                       class="flex-1 px-4 py-2.5 border border-gray-400 dark:border-gray-700 rounded-xl text-center
                                              bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-semibold text-sm
                                              focus:outline-none focus:border-blue-500 dark:focus:border-blue-400
                                              focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)] dark:focus:shadow-[0_0_0_3px_rgba(96,165,250,0.1)]
                                              transition-all duration-200">
                                <button type="button"
                                        @click="formData.units = Math.min(20, formData.units + 1)"
                                        :disabled="formData.units >= 20"
                                        :class="formData.units >= 20 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-300 dark:hover:bg-gray-600'"
                                        class="px-4 py-2.5 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg transition-colors flex-shrink-0">
                                    <i class="fi fi-rr-plus text-sm"></i>
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="formData.units === 1 ? '1 Unit' : formData.units + ' Units'"></span> — You can add up to 20 units
                            </p>
                        </div>
                    </div>

                    <!-- Dynamic Unit Fields (Accordion) -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-3 text-gray-700 dark:text-gray-300">
                            Unit Details <span class="text-red-500">*</span>
                        </label>

                        <x-material-ui.accordion type="multiple" :defaultOpen="['unit-0']">
                            <template x-for="(unit, index) in unitData" :key="'unit-accordion-' + index">
                                <div class="border-b border-gray-200 dark:border-gray-700/50">
                                    {{-- Trigger --}}
                                    <button type="button"
                                        @click="toggle('unit-' + index)"
                                        class="w-full flex items-center justify-between py-3.5 px-1 text-left transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50 rounded-lg group">
                                        <span class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-blue-50 dark:bg-blue-900/30">
                                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400" x-text="index + 1"></span>
                                            </span>
                                            <span class="min-w-0">
                                                <span class="block text-sm font-semibold text-gray-900 dark:text-white"
                                                      x-text="unit.name ? unit.name : 'Unit ' + (index + 1)"></span>
                                                <span class="block text-xs text-gray-400 dark:text-gray-500 mt-0.5"
                                                      x-text="unit.size ? unit.size + ' m²' + (unit.price ? ' — €' + unit.price.toFixed(2) : '') : 'Not configured'"></span>
                                            </span>
                                        </span>
                                        {{-- Morphing +/- icon --}}
                                        <span class="relative w-5 h-5 flex-shrink-0 ml-2">
                                            <svg class="w-5 h-5 absolute accordion-icon-plus text-gray-400 dark:text-gray-500"
                                                 :class="isOpen('unit-' + index) ? 'is-open' : 'is-closed'"
                                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"/><path d="M12 5v14"/>
                                            </svg>
                                            <svg class="w-5 h-5 absolute accordion-icon-minus text-gray-400 dark:text-gray-500"
                                                 :class="isOpen('unit-' + index) ? 'is-open' : 'is-closed'"
                                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"/>
                                            </svg>
                                        </span>
                                    </button>

                                    {{-- Content --}}
                                    <div x-show="isOpen('unit-' + index)" x-collapse>
                                        <div class="pb-4 pt-1 px-1">
                                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <!-- Cabin/Unit Name -->
                                                    <div>
                                                        <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-400">
                                                            Cabin/Unit Name <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="text" x-model="unit.name" required
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg
                                                                      bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm
                                                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                               placeholder="Ex: Kelo A, Cabin 5">
                                                    </div>

                                                    <!-- Unit Size -->
                                                    <div>
                                                        <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-400">
                                                            Unit Size <span class="text-red-500">*</span>
                                                        </label>
                                                        <select x-model="unit.size" required @change="calculateUnitPrice(index)"
                                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg
                                                                       bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm
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
                                                            <span x-show="formData.service_type !== 'Final Cleaning'">Calculation</span>
                                                        </label>
                                                        <div class="flex flex=row justify-between px-3 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                                            <!-- Final Cleaning Price (fixed) -->
                                                            <div x-show="formData.service_type === 'Final Cleaning'">
                                                                <div class="text-base font-bold text-blue-600 dark:text-blue-400">
                                                                    €<span x-text="unit.price ? unit.price.toFixed(2) : '0.00'"></span>
                                                                </div>
                                                            </div>
                                                            <!-- Hourly-based Calculation (single line) -->
                                                            <div x-show="formData.service_type !== 'Final Cleaning'" class="flex items-center gap-1.5 flex-wrap">
                                                                <span class="text-xs text-gray-500 dark:text-gray-400"><span x-text="unit.hours || '0'"></span>h × €<span x-text="(() => { const rates = {'Deep Cleaning': deepCleaningHourlyRate, 'Daily Cleaning': dailyCleaningHourlyRate, 'Snowout Cleaning': snowoutCleaningHourlyRate, 'General Cleaning': generalCleaningHourlyRate, 'Hotel Cleaning': hotelCleaningHourlyRate}; return (rates[formData.service_type] || 0).toFixed(0); })()"></span><span x-show="formData.is_sunday || formData.is_holiday" class="text-orange-500 font-semibold"> × 2</span> =</span>
                                                                <span class="text-base font-bold text-blue-600 dark:text-blue-400">€<span x-text="unit.price ? unit.price.toFixed(2) : '0.00'"></span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </x-material-ui.accordion>
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
                        <textarea x-model="formData.special_requests" rows="4" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg text-sm
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Type in special requests (eg. &quot;Extra cleaning for kitchen&quot;, &quot;Pet-friendly cleaning supplies&quot;)"></textarea>
                    </div>

                    <!-- Navigation -->
                    <div
                        class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="prevStep()" class="px-6 py-2 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full
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
            <div class="max-w-2xl mx-auto px-4 py-8">

                <!-- Header -->
                <div class="mb-6">
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium mb-1">Almost there!</p>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Review Your Appointment</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Please confirm the details below before submitting.</p>
                </div>

                <!-- Reference Number -->
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Reference number</p>
                    <p class="text-sm font-bold text-blue-600 dark:text-blue-400" x-show="referencePreview" x-text="referencePreview"></p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 italic" x-show="!referencePreview">Generating...</p>
                </div>

                <!-- Service Info -->
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white" x-text="formData.service_type || '-'"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <span x-text="formData.service_date || '-'"></span>
                        <span class="mx-1">|</span>
                        <span x-text="formData.service_time ? (() => { const [h,m] = formData.service_time.split(':'); const hr = +h > 12 ? +h-12 : (+h||12); return hr+':'+m+' '+(+h>=12?'PM':'AM'); })() : '-'"></span>
                        <span class="text-gray-400 dark:text-gray-500" x-text="'(Estimated ' + (unitData.reduce((sum, u) => sum + (parseFloat(u.hours) || 0), 0)) + 'hr duration)'"></span>
                    </p>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <span x-show="formData.is_sunday && !formData.is_holiday" class="text-sm text-orange-600 dark:text-orange-400 font-semibold">(Sunday - 2x rate)</span>
                        <span x-show="formData.is_holiday && !formData.is_sunday" class="text-sm text-orange-600 dark:text-orange-400 font-semibold">(Holiday - 2x rate)</span>
                        <span x-show="formData.is_sunday && formData.is_holiday" class="text-sm text-orange-600 dark:text-orange-400 font-semibold">(Sunday & Holiday - 2x rate)</span>
                        <span x-show="formData.is_priority" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Priority Clean</span>
                    </div>

                    <!-- Unit list -->
                    <div class="mt-4 space-y-2">
                        <template x-for="(unit, index) in unitData" :key="index">
                            <div class="flex justify-between items-center text-sm">
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white" x-text="unit.name || ('Unit ' + (index + 1))"></span>
                                    <span class="text-gray-400 dark:text-gray-500 ml-1" x-text="unit.size ? '(' + unit.size + ' m²)' : ''"></span>
                                </div>
                                <div class="text-right">
                                    <span x-show="formData.service_type !== 'Final Cleaning'" class="text-gray-400 dark:text-gray-500 mr-2">
                                        <span x-text="unit.hours || '0'"></span>h × €<span x-text="(() => { const rates = {'Deep Cleaning': deepCleaningHourlyRate, 'Daily Cleaning': dailyCleaningHourlyRate, 'Snowout Cleaning': snowoutCleaningHourlyRate, 'General Cleaning': generalCleaningHourlyRate, 'Hotel Cleaning': hotelCleaningHourlyRate}; return (rates[formData.service_type] || 0).toFixed(0); })()"></span><span x-show="formData.is_sunday || formData.is_holiday" class="text-orange-500 font-semibold"> × 2</span>
                                    </span>
                                    <span class="font-bold text-gray-900 dark:text-white">€<span x-text="unit.price ? unit.price.toFixed(2) : '0.00'"></span></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Client & Service Address (side by side) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <!-- Client Details -->
                    <div>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">Client details</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="(formData.first_name + ' ' + formData.last_name) || '-'"></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="formData.email || '-'"></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="(formData.country_code + ' ' + formData.mobile_number) || '-'"></p>
                    </div>

                    <!-- Service Address -->
                    <div>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">Service address</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="formData.state || '-'"></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="formData.city || '-'"></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="[formData.postal_code, formData.district].filter(Boolean).join(', ') || '-'"></p>
                    </div>
                </div>

                <!-- Special Requests -->
                <div x-show="formData.special_requests" class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-2">Special requests</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="formData.special_requests"></p>
                </div>

                <!-- Progress Steps -->
                <div class="mb-8">
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-4">Appointment status after submission</p>
                    <div class="relative">
                        <!-- Progress bar background -->
                        <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full">
                            <div class="h-1 bg-blue-600 dark:bg-blue-500 rounded-full w-1/4"></div>
                        </div>
                        <!-- Step labels -->
                        <div class="flex justify-between mt-2">
                            <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">To Submit</span>
                            <span class="text-sm text-gray-400 dark:text-gray-500">Pending</span>
                            <span class="text-sm text-gray-400 dark:text-gray-500">Approved</span>
                            <span class="text-sm text-gray-400 dark:text-gray-500">Completed</span>
                        </div>
                    </div>
                </div>

                <!-- Pricing Table (receipt-style) -->
                <div class="bg-transparent dark:bg-gray-800/50 rounded-xl p-6 mb-6">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                            <span class="text-gray-900 dark:text-white font-medium" x-text="quotation > 0 ? '€' + quotation.toFixed(2) : '-'"></span>
                        </div>
                        <div x-show="formData.is_sunday || formData.is_holiday" class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Rate multiplier</span>
                            <span class="text-orange-600 dark:text-orange-400 font-medium">2x (Sunday/Holiday)</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">VAT</span>
                            <span class="text-gray-900 dark:text-white font-medium">Included</span>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Total amount</span>
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400" x-text="quotation > 0 ? '€' + quotation.toFixed(2) : '-'"></span>
                        </div>
                    </div>
                </div>

                <!-- Pricing Notice -->
                <div class="mb-6 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <p class="text-xs text-yellow-800 dark:text-yellow-200">
                        <i class="fi fi-rr-info mr-1"></i>
                        Sundays and holidays will be charged double the price. All rates are inclusive of VAT and prices are subject to change.
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="button" @click="prevStep()"
                        class="px-6 py-3 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 dark:hover:bg-gray-700 transition-colors text-sm font-medium">
                        <i class="fi fi-rr-angle-left mr-2"></i>Back
                    </button>
                    <button type="button"
                        @click="window.showConfirmDialog('Confirm Appointment', 'Are you sure you want to submit this appointment? Please review all details before proceeding.', 'Submit', 'Review Again').then(() => submitForm()).catch(() => {})"
                        :disabled="submitting"
                        :class="{'opacity-50 cursor-not-allowed': submitting}"
                        class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-full transition-colors text-sm">
                        <span x-show="!submitting">Confirm Appointment</span>
                        <span x-show="submitting">Processing...</span>
                    </button>
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
            districtLoading: false,
            isCustomDistrict: false,
            customDistrictValue: '',
            districtOpen: false,
            districtSearch: '',
            quotation: 0,
            referencePreview: '',
            dateNotice: '',        // 'blocked' = tomorrow (cannot book), 'priority' = 2 days (needs Priority Clean)
            dateNoticeMessage: '',
            postalLoading: false,

            // Time slot availability
            bookedSlots: [],
            loadingSlots: false,

            // CSC API
            cscApiKey: @json($cscApiKey ?? ''),
            cscBaseUrl: 'https://api.countrystatecity.in/v1',
            finlandIso2: 'FI',
            cscStates: [],
            cscCities: [],

            // Holidays data from backend
            holidays: @json($holidays ?? []),

            // Finnish city postal code fallback (main postal code per city)
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
                'hollola': '15870', 'kirkkonummi': '02400', 'siilinjärvi': '71800', 'tuusula': '04300',
                'tornio': '95400', 'iisalmi': '74100', 'valkeakoski': '37600', 'raisio': '21200',
                'muhos': '91500', 'inari': '99800', 'sodankylä': '99600', 'enontekiö': '99400',
                'utsjoki': '99980', 'kittilä': '99100', 'kolari': '95900', 'muonio': '99300',
                'pelkosenniemi': '98500', 'salla': '98900', 'savukoski': '98800',
            },

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
            dailyCleaningHourlyRate: 35.00, // €35/hour
            snowoutCleaningHourlyRate: 55.00, // €55/hour (specialized)
            generalCleaningHourlyRate: 40.00, // €40/hour
            hotelCleaningHourlyRate: 42.00, // €42/hour

            // Hour estimates per size range for other services
            standardCleaningEstimates: {
                '20-50': 1.5,
                '51-70': 2,
                '71-90': 2.5,
                '91-120': 3,
                '121-140': 3.5,
                '141-160': 4,
                '161-180': 4.5,
                '181-220': 5.5
            },

            // Store individual unit data
            unitData: [],

            formData: {
                // Step 1 - Client Details
                booking_type: @json($client->client_type ?? 'personal'),
                first_name: @json($client->first_name ?? ''),
                last_name: @json($client->last_name ?? ''),
                email: @json($user->email ?? ''),
                street_address: @json($client->street_address ?? ''),
                state: @json($client->state ?? ''),
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
                is_priority: false,      // Priority Clean - allows 2-day advance booking
                units: 1,                // Default to 1 unit
                special_requests: ''
            },


            init() {
                // Initialize filtered districts
                this.filteredDistricts = this.helsinkiDistricts;

                // Load CSC states for Finland
                this.loadStates();

                // If state is pre-filled, load cities
                if (this.formData.state) {
                    this.$nextTick(() => this.onStateChange());
                }

                // Initialize unit data array based on default units
                this.initializeUnitData();

                // Pre-fill from URL query params (e.g. ?date=2026-04-05)
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('date')) {
                    this.formData.service_date = urlParams.get('date');
                }

                // Watch for date/service changes to refresh booked time slots
                this.$watch('formData.service_date', (val) => { console.log('[Watch] service_date changed:', val); if (val) this.fetchBookedSlots(); else this.bookedSlots = []; });
                this.$watch('formData.service_type', () => { console.log('[Watch] service_type changed'); if (this.formData.service_date) this.fetchBookedSlots(); });

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

                const serviceType = this.formData.service_type;

                if (serviceType === 'Final Cleaning') {
                    // Final Cleaning pricing (fixed per size)
                    const rates = this.finalCleaningRates[unit.size];
                    if (rates) {
                        unit.price = isSundayOrHoliday ? rates.sunday : rates.normal;
                        unit.hours = 0;
                    }
                } else if (serviceType === 'Deep Cleaning') {
                    // Deep Cleaning pricing (hourly rate)
                    const estimatedHours = this.deepCleaningEstimates[unit.size];
                    if (estimatedHours) {
                        unit.hours = estimatedHours;
                        const basePrice = estimatedHours * this.deepCleaningHourlyRate;
                        unit.price = isSundayOrHoliday ? (basePrice * 2) : basePrice;
                    }
                } else {
                    // Daily, Snowout, General, Hotel — all hourly-based
                    const hourlyRates = {
                        'Daily Cleaning': this.dailyCleaningHourlyRate,
                        'Snowout Cleaning': this.snowoutCleaningHourlyRate,
                        'General Cleaning': this.generalCleaningHourlyRate,
                        'Hotel Cleaning': this.hotelCleaningHourlyRate,
                    };
                    const rate = hourlyRates[serviceType];
                    const estimatedHours = this.standardCleaningEstimates[unit.size];
                    if (rate && estimatedHours) {
                        unit.hours = estimatedHours;
                        const basePrice = estimatedHours * rate;
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

            // ── CSC API Methods ──
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

            async onStateChange() {
                this.cscCities = [];
                this.formData.city = '';
                this.formData.postal_code = '';
                this.formData.district = '';
                this.filteredDistricts = [];
                this.isCustomDistrict = false;
                this.customDistrictValue = '';
                if (!this.formData.state) return;

                const stateObj = this.cscStates.find(s => s.name === this.formData.state);
                if (!stateObj) return;

                try {
                    const cities = await this.cscFetch(`/countries/${this.finlandIso2}/states/${stateObj.iso2}/cities`);
                    this.cscCities = cities.sort((a, b) => a.name.localeCompare(b.name));
                } catch (e) {
                    console.error('Failed to load cities:', e);
                }
            },

            async onCityChange() {
                this.formData.district = '';
                this.filteredDistricts = [];
                this.isCustomDistrict = false;
                this.customDistrictValue = '';

                if (!this.formData.city) {
                    this.formData.postal_code = '';
                    return;
                }

                // Auto-fill postal code & load districts via Nominatim
                this.postalLoading = true;
                this.districtLoading = true;
                try {
                    // First: get postal code — try city-level search
                    const cityEnc = encodeURIComponent(this.formData.city);
                    const stateEnc = encodeURIComponent(this.formData.state);
                    let postalFound = false;

                    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${cityEnc}%2C+${stateEnc}%2C+Finland&addressdetails=1&limit=1`, {
                        headers: { 'Accept-Language': 'en' }
                    });
                    const data = await res.json();
                    if (data.length > 0 && data[0].address?.postcode) {
                        this.formData.postal_code = data[0].address.postcode;
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
                                this.formData.postal_code = structData[0].address.postcode;
                                postalFound = true;
                            }
                        } catch (e) { /* silent */ }
                    }

                    // Fallback 2: static Finnish postal code map
                    if (!postalFound) {
                        const cityKey = this.formData.city.toLowerCase().trim();
                        if (this.finnishPostalCodes[cityKey]) {
                            this.formData.postal_code = this.finnishPostalCodes[cityKey];
                        }
                    }

                    // Second: search for suburbs/districts within this city
                    const cityName = encodeURIComponent(this.formData.city);
                    const districtRes = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=suburb+in+${cityName}+Finland&addressdetails=1&limit=50`,
                        { headers: { 'Accept-Language': 'en' } }
                    );
                    const districtData = await districtRes.json();

                    // Extract unique district names
                    const districts = new Set();
                    districtData.forEach(d => {
                        const addr = d.address || {};
                        const name = addr.suburb || addr.neighbourhood || addr.city_district || addr.quarter || '';
                        if (name) districts.add(name);
                    });

                    // Also add from the hardcoded Helsinki list if city is Helsinki
                    if (this.formData.city.toLowerCase().includes('helsinki')) {
                        this.helsinkiDistricts.forEach(d => districts.add(d));
                    }

                    this.filteredDistricts = [...districts].sort();

                    // Auto-select first district if available
                    if (this.filteredDistricts.length > 0) {
                        this.formData.district = this.filteredDistricts[0];
                    }
                } catch (e) {
                    console.warn('Lookup failed:', e);
                    // Fallback to Helsinki districts if applicable
                    if (this.formData.city.toLowerCase().includes('helsinki')) {
                        this.filteredDistricts = [...this.helsinkiDistricts];
                    }
                } finally {
                    this.postalLoading = false;
                    this.districtLoading = false;
                }
            },

            selectDistrictOption(district) {
                if (district === '__other__') {
                    this.isCustomDistrict = true;
                    this.formData.district = '';
                    this.customDistrictValue = '';
                    this.$nextTick(() => {
                        const input = this.$refs.customDistrictInput;
                        if (input) input.focus();
                    });
                } else {
                    this.isCustomDistrict = false;
                    this.customDistrictValue = '';
                    this.formData.district = district;
                }
            },

            async fetchReferencePreview() {
                this.referencePreview = '';
                try {
                    const response = await fetch('{{ route("client.appointment.reference-preview") }}?service_type=' + encodeURIComponent(this.formData.service_type), {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    this.referencePreview = data.reference_number || '-';
                } catch (e) {
                    console.error('Failed to fetch reference preview:', e);
                    this.referencePreview = '-';
                }
            },

            async fetchBookedSlots() {
                if (!this.formData.service_date) { this.bookedSlots = []; return; }
                this.loadingSlots = true;
                try {
                    let estHours = 0;
                    if (this.unitData && this.unitData.length) {
                        this.unitData.forEach(u => { estHours += (u.hours || 0); });
                    }
                    const params = new URLSearchParams({
                        date: this.formData.service_date,
                        service_type: this.formData.service_type || '',
                        estimated_hours: estHours || 0,
                    });
                    const url = window.location.pathname.replace(/\/book-service.*$/, '/book-service/booked-slots') + '?' + params.toString();
                    console.log('[BookedSlots] Fetching:', url);
                    const res = await fetch(url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    console.log('[BookedSlots] Response status:', res.status);
                    if (!res.ok) { console.error('[BookedSlots] Error response:', res.status); this.bookedSlots = []; this.loadingSlots = false; return; }
                    const data = await res.json();
                    console.log('[BookedSlots] Booked slots:', data.booked);
                    this.bookedSlots = data.booked || [];
                    if (this.formData.service_time && this.bookedSlots.includes(this.formData.service_time)) {
                        this.formData.service_time = '';
                    }
                } catch (e) {
                    console.warn('Failed to fetch booked slots:', e);
                    this.bookedSlots = [];
                } finally {
                    this.loadingSlots = false;
                }
            },

            checkDateAndCalculate() {
                // Check if the selected date is a Sunday or Holiday
                if (this.formData.service_date) {
                    const selectedDate = new Date(this.formData.service_date + 'T00:00:00');
                    // getDay() returns 0 for Sunday, 1 for Monday, etc.
                    this.formData.is_sunday = (selectedDate.getDay() === 0);

                    // Check if the selected date is a holiday
                    this.formData.is_holiday = this.holidays.some(holiday => holiday.date === this.formData.service_date);

                    // Check minimum booking notice (3-day rule)
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const diffTime = selectedDate.getTime() - today.getTime();
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    this.dateNotice = '';
                    this.dateNoticeMessage = '';
                    this.formData.is_priority = false;

                    if (diffDays < 2) {
                        // Tomorrow or today — cannot book at all
                        this.dateNotice = 'blocked';
                        this.dateNoticeMessage = 'This date is too soon. Bookings require at least 2 days advance notice. Please select a later date.';
                    } else if (diffDays < 3) {
                        // 2 days from now — can book with Priority Clean
                        this.dateNotice = 'priority';
                        this.dateNoticeMessage = 'This date is within our standard 3-day advance booking window. To proceed, please enable "Priority Clean" below.';
                    }

                    console.log('Date checked:', {
                        date: this.formData.service_date,
                        dayOfWeek: selectedDate.getDay(),
                        diffDays: diffDays,
                        dateNotice: this.dateNotice,
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
                    window.showErrorDialog('Validation Error',`Please fill in the following required fields: ${fields}`);
                    return false;
                }

                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(this.formData.email)) {
                    window.showErrorDialog('Validation Error','Please enter a valid email address');
                    return false;
                }

                // Postal code validation (5 digits)
                if (this.formData.postal_code.length !== 5 || !/^\d+$/.test(this.formData.postal_code)) {
                    window.showErrorDialog('Validation Error','Please enter a valid 5-digit postal code');
                    return false;
                }

                return true;
            },

            validateStep2() {
                // Check service type
                if (!this.formData.service_type) {
                    window.showErrorDialog('Validation Error','Please select a service type');
                    return false;
                }

                // Check service date
                if (!this.formData.service_date) {
                    window.showErrorDialog('Validation Error','Please select a service date');
                    return false;
                }

                // Block if date is too soon (tomorrow or today)
                if (this.dateNotice === 'blocked') {
                    window.showErrorDialog('Validation Error','This date is too soon. Please select a date at least 2 days from today.');
                    return false;
                }

                // Require Priority Clean checkbox for 2-day advance booking
                if (this.dateNotice === 'priority' && !this.formData.is_priority) {
                    window.showErrorDialog('Validation Error','Please enable "Priority Clean" to book within the 3-day advance window.');
                    return false;
                }

                // Check service time
                if (!this.formData.service_time) {
                    window.showErrorDialog('Validation Error','Please select a service time');
                    return false;
                }

                // Check if at least one unit exists
                if (this.formData.units < 1) {
                    window.showErrorDialog('Validation Error','Please add at least one unit');
                    return false;
                }

                // Check all units have required fields
                for (let i = 0; i < this.unitData.length; i++) {
                    const unit = this.unitData[i];

                    if (!unit.name || unit.name.trim() === '') {
                        window.showErrorDialog('Missing Cabin Name',`Please enter a name for Unit ${i + 1}.`);
                        return false;
                    }

                    if (!unit.size) {
                        const label = unit.name || `Unit ${i + 1}`;
                        window.showErrorDialog('Missing Unit Size',`Please select a size for "${label}".`);
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

                    // Fetch reference number preview when entering step 3
                    if (this.currentStep === 3) {
                        this.fetchReferencePreview();
                    }

                    // Scroll to top when changing steps
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    // Discard reference preview when leaving step 3
                    if (this.currentStep === 3) {
                        this.referencePreview = '';
                    }

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
                        billing_address: this.formData.street_address + ', ' + this.formData.postal_code + ' ' + this.formData.city + ', ' + this.formData.state,

                        // Step 2 fields
                        service_type: this.formData.service_type,
                        service_date: this.formData.service_date,
                        service_time: this.formData.service_time,
                        is_sunday: this.formData.is_sunday,
                        is_holiday: this.formData.is_holiday || false,
                        is_priority: this.formData.is_priority || false,
                        units: this.formData.units,
                        unit_size: firstUnit.size,  // From first unit (for backward compatibility)
                        room_identifier: firstUnit.name,  // From first unit (for backward compatibility)
                        unit_details: this.unitData,  // Send all unit details
                        special_requests: this.formData.special_requests || '',
                        reference_number: this.referencePreview || ''
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
                        console.log('Appointment created:', data);
                        const redirectUrl = data.redirect_url || '{{ route("client.dashboard") }}';
                        window.showSuccessDialog('Appointment Booked!', data.message || 'Your appointment has been successfully submitted.', 'Go to Dashboard', redirectUrl);
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            const errorMessages = Object.entries(data.errors)
                                .map(([field, messages]) => messages.join(', '))
                                .join(', ');
                            window.showErrorDialog('Validation Error', errorMessages);
                        } else {
                            window.showErrorDialog('Booking Failed', data.message || 'Failed to book appointment. Please try again.');
                        }
                        this.submitting = false;
                    }

                } catch (error) {
                    console.error('Error submitting appointment:', error);
                    window.showErrorDialog('Error', 'An error occurred while booking your appointment. Please try again.');
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
    .time-scroll::-webkit-scrollbar { width: 6px; }
    .time-scroll::-webkit-scrollbar-track { background: transparent; }
    .time-scroll::-webkit-scrollbar-thumb { background: rgba(156,163,175,0.3); border-radius: 3px; }
    .time-scroll::-webkit-scrollbar-thumb:hover { background: rgba(156,163,175,0.5); }
    .time-scroll { scrollbar-width: thin; scrollbar-color: rgba(156,163,175,0.3) transparent; }
</style>