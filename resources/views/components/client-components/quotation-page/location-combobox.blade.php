{{-- resources/views/components/client-components/quotation-page/location-picker.blade.php --}}
@props([
    'label' => 'Property Location',
    'name' => 'location',
    'required' => false,
])

<div class="space-y-3" x-data="locationPicker()" x-init="init()">
    <!-- Label -->
    <label class="block text-sm text-gray-700 dark:text-gray-300">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <!-- Main Location Type Selector -->
    <div class="relative">
        <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10 pointer-events-none"></i>
        
        <button 
            type="button"
            @click="mainOpen = !mainOpen"
            class="w-full pl-12 pr-10 py-3 text-left border-2 border-gray-300 dark:border-gray-600 rounded-xl
                   focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                   hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300"
            :class="{ 'border-blue-600 bg-blue-50 dark:bg-blue-900/20': mainOpen }">
            <span x-text="displayText" 
                  :class="!selectedOption ? 'text-gray-400 dark:text-gray-500' : ''">
                Select location option
            </span>
        </button>

        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200"
               :class="{ 'rotate-180': mainOpen }"></i>
        </div>

        <!-- Main Dropdown -->
        <div x-show="mainOpen"
             @click.away="mainOpen = false"
             x-transition
             class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 
                    rounded-xl shadow-xl overflow-hidden"
             style="display: none;">
            
            <ul class="py-1">
                <!-- Use Current Location -->
                <li>
                    <button 
                        type="button"
                        @click="selectMainOption('current')"
                        class="w-full px-4 py-3 text-left flex items-center gap-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 
                               transition-colors duration-150"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedOption === 'current' }">
                        <i class="fa-solid fa-location-crosshairs text-blue-600 dark:text-blue-400 text-lg"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Use Current Location</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Automatically detect your location</p>
                        </div>
                        <i x-show="selectedOption === 'current'" class="fa-solid fa-check text-blue-600"></i>
                    </button>
                </li>

                <li class="border-t border-gray-200 dark:border-gray-600 my-1"></li>

                <!-- Select Location -->
                <li>
                    <button 
                        type="button"
                        @click="selectMainOption('select')"
                        class="w-full px-4 py-3 text-left flex items-center gap-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 
                               transition-colors duration-150"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedOption === 'select' }">
                        <i class="fa-solid fa-map-location-dot text-blue-600 dark:text-blue-400 text-lg"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Select a Location</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Choose country and address details</p>
                        </div>
                        <i x-show="selectedOption === 'select'" class="fa-solid fa-check text-blue-600"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Hidden Inputs for Form Submission -->
    <input type="hidden" name="{{ $name }}_type" x-model="selectedOption">
    <input type="hidden" name="{{ $name }}_country" x-model="selectedCountry">
    <input type="hidden" name="{{ $name }}_region" x-model="selectedRegion">
    <input type="hidden" name="{{ $name }}_city" x-model="selectedCity">
    <input type="hidden" name="{{ $name }}_barangay" x-model="selectedBarangay">
    <input type="hidden" name="{{ $name }}_district" x-model="selectedDistrict">
    <input type="hidden" name="{{ $name }}_postal" x-model="postalCode">
    <input type="hidden" name="{{ $name }}_street" x-model="streetAddress">

    <!-- Address Form (shown when "Select a Location" is chosen) -->
    <div x-show="selectedOption === 'select'" 
         x-transition
         class="space-y-4">
        
        <!-- Country Dropdown -->
        <div class="relative">
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                Country <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <i class="fa-solid fa-globe absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                <button 
                    type="button"
                    @click="countryOpen = !countryOpen"
                    class="w-full pl-12 pr-10 py-3 text-left border-2 border-gray-300 dark:border-gray-600 rounded-xl
                           focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                           bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                           hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300">
                    <span x-text="selectedCountry || 'Select a country'" 
                          :class="!selectedCountry ? 'text-gray-400 dark:text-gray-500' : ''">
                    </span>
                </button>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200"
                       :class="{ 'rotate-180': countryOpen }"></i>
                </div>
            </div>

            <!-- Country Dropdown List -->
            <div x-show="countryOpen"
                 @click.away="countryOpen = false"
                 x-transition
                 class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 
                        rounded-xl shadow-xl max-h-60 overflow-auto"
                 style="display: none;">
                
                <ul class="py-1">
                    <template x-for="country in countries" :key="country">
                        <li>
                            <button 
                                type="button"
                                @click="selectCountry(country)"
                                class="w-full px-4 py-2.5 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 
                                       transition-colors duration-150 text-sm text-gray-900 dark:text-white"
                                :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedCountry === country }"
                                x-text="country">
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <!-- Philippines Address Form -->
        <template x-if="selectedCountry === 'Philippines'">
            <div class="space-y-4">
                <!-- Region/Province -->
                <div class="relative">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Region/Province <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-map absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <button 
                            type="button"
                            @click="regionOpen = !regionOpen"
                            class="w-full pl-12 pr-10 py-3 text-left border-2 border-gray-300 dark:border-gray-600 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                   hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300">
                            <span x-text="selectedRegion || 'Select region/province'" 
                                  :class="!selectedRegion ? 'text-gray-400 dark:text-gray-500' : ''">
                            </span>
                        </button>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200"
                               :class="{ 'rotate-180': regionOpen }"></i>
                        </div>
                    </div>

                    <div x-show="regionOpen"
                         @click.away="regionOpen = false"
                         x-transition
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 
                                rounded-xl shadow-xl max-h-60 overflow-auto"
                         style="display: none;">
                        
                        <div class="sticky top-0 p-2 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                            <input 
                                type="text"
                                x-model="regionSearch"
                                placeholder="Search regions..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                       bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                @click.stop>
                        </div>

                        <ul class="py-1">
                            <template x-for="region in filteredRegions" :key="region">
                                <li>
                                    <button 
                                        type="button"
                                        @click="selectRegion(region)"
                                        class="w-full px-4 py-2.5 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 
                                               transition-colors duration-150 text-sm text-gray-900 dark:text-white"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedRegion === region }"
                                        x-text="region">
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- City (appears after region) -->
                <div x-show="selectedRegion" x-transition class="relative">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                        City/Municipality <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-city absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <button 
                            type="button"
                            @click="cityOpen = !cityOpen"
                            class="w-full pl-12 pr-10 py-3 text-left border-2 border-gray-300 dark:border-gray-600 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                   hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300">
                            <span x-text="selectedCity || 'Select city'" 
                                  :class="!selectedCity ? 'text-gray-400 dark:text-gray-500' : ''">
                            </span>
                        </button>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200"
                               :class="{ 'rotate-180': cityOpen }"></i>
                        </div>
                    </div>

                    <div x-show="cityOpen"
                         @click.away="cityOpen = false"
                         x-transition
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 
                                rounded-xl shadow-xl max-h-60 overflow-auto"
                         style="display: none;">
                        
                        <div class="sticky top-0 p-2 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                            <input 
                                type="text"
                                x-model="citySearch"
                                placeholder="Search cities..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                       bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                @click.stop>
                        </div>

                        <ul class="py-1">
                            <template x-for="city in filteredCities" :key="city">
                                <li>
                                    <button 
                                        type="button"
                                        @click="selectCity(city)"
                                        class="w-full px-4 py-2.5 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 
                                               transition-colors duration-150 text-sm text-gray-900 dark:text-white"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedCity === city }"
                                        x-text="city">
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Barangay (appears after city) -->
                <div x-show="selectedCity" x-transition class="relative">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Barangay <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <button 
                            type="button"
                            @click="barangayOpen = !barangayOpen"
                            class="w-full pl-12 pr-10 py-3 text-left border-2 border-gray-300 dark:border-gray-600 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                   hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300">
                            <span x-text="selectedBarangay || 'Select barangay'" 
                                  :class="!selectedBarangay ? 'text-gray-400 dark:text-gray-500' : ''">
                            </span>
                        </button>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200"
                               :class="{ 'rotate-180': barangayOpen }"></i>
                        </div>
                    </div>

                    <div x-show="barangayOpen"
                         @click.away="barangayOpen = false"
                         x-transition
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 
                                rounded-xl shadow-xl max-h-60 overflow-auto"
                         style="display: none;">
                        
                        <div class="sticky top-0 p-2 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                            <input 
                                type="text"
                                x-model="barangaySearch"
                                placeholder="Search barangays..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                       bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                @click.stop>
                        </div>

                        <ul class="py-1">
                            <template x-for="barangay in filteredBarangays" :key="barangay.name">
                                <li>
                                    <button 
                                        type="button"
                                        @click="selectBarangay(barangay)"
                                        class="w-full px-4 py-2.5 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 
                                               transition-colors duration-150 text-sm text-gray-900 dark:text-white"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedBarangay === barangay.name }">
                                        <span x-text="barangay.name"></span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2" x-text="'(' + barangay.postal + ')'"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Postal Code (auto-filled) -->
                <div x-show="postalCode" x-transition>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Postal Code
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <input 
                            type="text"
                            x-model="postalCode"
                            readonly
                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl
                                   bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                    </div>
                </div>

                <!-- Street Address -->
                <div x-show="selectedBarangay" x-transition>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Street Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-road absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <input 
                            type="text"
                            x-model="streetAddress"
                            placeholder="Enter street address, building, unit number"
                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                   placeholder-gray-400 dark:placeholder-gray-500">
                    </div>
                </div>
            </div>
        </template>

        <!-- Finland Address Form -->
        <template x-if="selectedCountry === 'Finland'">
            <div class="space-y-4">
                <!-- Region -->
                <div class="relative">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Region <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-map absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <button 
                            type="button"
                            @click="regionOpen = !regionOpen"
                            class="w-full pl-12 pr-10 py-3 text-left border-2 border-gray-300 dark:border-gray-600 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                   hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300">
                            <span x-text="selectedRegion || 'Select region'" 
                                  :class="!selectedRegion ? 'text-gray-400 dark:text-gray-500' : ''">
                            </span>
                        </button>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200"
                               :class="{ 'rotate-180': regionOpen }"></i>
                        </div>
                    </div>

                    <div x-show="regionOpen"
                         @click.away="regionOpen = false"
                         x-transition
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 
                                rounded-xl shadow-xl max-h-60 overflow-auto"
                         style="display: none;">
                        
                        <ul class="py-1">
                            <template x-for="region in finlandRegions" :key="region">
                                <li>
                                    <button 
                                        type="button"
                                        @click="selectRegion(region)"
                                        class="w-full px-4 py-2.5 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 
                                               transition-colors duration-150 text-sm text-gray-900 dark:text-white"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedRegion === region }"
                                        x-text="region">
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Municipality (appears after region) -->
                <div x-show="selectedRegion" x-transition class="relative">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Municipality <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-city absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <button 
                            type="button"
                            @click="cityOpen = !cityOpen"
                            class="w-full pl-12 pr-10 py-3 text-left border-2 border-gray-300 dark:border-gray-600 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                   hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300">
                            <span x-text="selectedCity || 'Select municipality'" 
                                  :class="!selectedCity ? 'text-gray-400 dark:text-gray-500' : ''">
                            </span>
                        </button>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200"
                               :class="{ 'rotate-180': cityOpen }"></i>
                        </div>
                    </div>

                    <div x-show="cityOpen"
                         @click.away="cityOpen = false"
                         x-transition
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 
                                rounded-xl shadow-xl max-h-60 overflow-auto"
                         style="display: none;">
                        
                        <div class="sticky top-0 p-2 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                            <input 
                                type="text"
                                x-model="citySearch"
                                placeholder="Search municipalities..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                       bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                @click.stop>
                        </div>

                        <ul class="py-1">
                            <template x-for="city in filteredCities" :key="city">
                                <li>
                                    <button 
                                        type="button"
                                        @click="selectCity(city)"
                                        class="w-full px-4 py-2.5 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 
                                               transition-colors duration-150 text-sm text-gray-900 dark:text-white"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedCity === city }"
                                        x-text="city">
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- District (appears after municipality) -->
                <div x-show="selectedCity" x-transition class="relative">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                        District <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-map-pin absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <button 
                            type="button"
                            @click="districtOpen = !districtOpen"
                            class="w-full pl-12 pr-10 py-3 text-left border-2 border-gray-300 dark:border-gray-600 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                   hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300">
                            <span x-text="selectedDistrict || 'Select district'" 
                                  :class="!selectedDistrict ? 'text-gray-400 dark:text-gray-500' : ''">
                            </span>
                        </button>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-200"
                               :class="{ 'rotate-180': districtOpen }"></i>
                        </div>
                    </div>

                    <div x-show="districtOpen"
                         @click.away="districtOpen = false"
                         x-transition
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 
                                rounded-xl shadow-xl max-h-60 overflow-auto"
                         style="display: none;">
                        
                        <div class="sticky top-0 p-2 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                            <input 
                                type="text"
                                x-model="districtSearch"
                                placeholder="Search districts..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                       bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                                @click.stop>
                        </div>

                        <ul class="py-1">
                            <template x-for="district in filteredDistricts" :key="district.name">
                                <li>
                                    <button 
                                        type="button"
                                        @click="selectDistrict(district)"
                                        class="w-full px-4 py-2.5 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 
                                               transition-colors duration-150 text-sm text-gray-900 dark:text-white"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedDistrict === district.name }">
                                        <span x-text="district.name"></span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2" x-text="'(' + district.postal + ')'"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Postal Code (auto-filled) -->
                <div x-show="postalCode" x-transition>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Postal Code
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <input 
                            type="text"
                            x-model="postalCode"
                            readonly
                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl
                                   bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                    </div>
                </div>

                <!-- Street Address -->
                <div x-show="selectedDistrict" x-transition>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Street Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-road absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <input 
                            type="text"
                            x-model="streetAddress"
                            placeholder="Enter street address and apartment number"
                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                   placeholder-gray-400 dark:placeholder-gray-500">
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Location Status Message -->
    <div x-show="locationMessage" x-transition class="mt-3">
        <div class="flex items-start gap-2 p-3 rounded-lg"
             :class="{
                 'bg-blue-50 dark:bg-blue-900/20': locationStatus === 'loading',
                 'bg-green-50 dark:bg-green-900/20': locationStatus === 'success',
                 'bg-red-50 dark:bg-red-900/20': locationStatus === 'error'
             }">
            <i class="mt-0.5" 
               :class="{
                   'fa-solid fa-spinner fa-spin text-blue-600': locationStatus === 'loading',
                   'fa-solid fa-check-circle text-green-600': locationStatus === 'success',
                   'fa-solid fa-exclamation-circle text-red-600': locationStatus === 'error'
               }"></i>
            <p class="text-sm" 
               :class="{
                   'text-blue-700 dark:text-blue-300': locationStatus === 'loading',
                   'text-green-700 dark:text-green-300': locationStatus === 'success',
                   'text-red-700 dark:text-red-300': locationStatus === 'error'
               }"
               x-text="locationMessage"></p>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function locationPicker() {
    return {
        // Main states
        mainOpen: false,
        countryOpen: false,
        regionOpen: false,
        cityOpen: false,
        barangayOpen: false,
        districtOpen: false,
        
        // Selected values
        selectedOption: null,
        selectedCountry: null,
        selectedRegion: null,
        selectedCity: null,
        selectedBarangay: null,
        selectedDistrict: null,
        postalCode: '',
        streetAddress: '',
        displayText: 'Select location option',
        
        // Search
        regionSearch: '',
        citySearch: '',
        barangaySearch: '',
        districtSearch: '',
        
        // Status
        locationStatus: null,
        locationMessage: '',
        
        // Data
        countries: ['Philippines', 'Finland'],
        
        // Philippines Data
        philippinesRegions: [
            'Metro Manila (NCR)',
            'Cordillera Administrative Region (CAR)',
            'Ilocos Region (Region I)',
            'Cagayan Valley (Region II)',
            'Central Luzon (Region III)',
            'CALABARZON (Region IV-A)',
            'MIMAROPA (Region IV-B)',
            'Bicol Region (Region V)',
            'Western Visayas (Region VI)',
            'Central Visayas (Region VII)',
            'Eastern Visayas (Region VIII)',
            'Zamboanga Peninsula (Region IX)',
            'Northern Mindanao (Region X)',
            'Davao Region (Region XI)',
            'SOCCSKSARGEN (Region XII)',
            'Caraga (Region XIII)',
            'Bangsamoro (BARMM)'
        ],
        
        philippinesCitiesByRegion: {
            'Metro Manila (NCR)': [
                'Manila', 'Quezon City', 'Makati', 'Taguig', 'Pasig', 
                'Mandaluyong', 'Pasay', 'Parañaque', 'Las Piñas', 
                'Muntinlupa', 'Caloocan', 'Malabon', 'Navotas', 
                'Valenzuela', 'Marikina', 'San Juan', 'Pateros'
            ],
            'Central Luzon (Region III)': [
                'Angeles', 'San Fernando', 'Mabalacat', 'Olongapo', 
                'Balanga', 'Cabanatuan', 'Tarlac City', 'San Jose'
            ],
            'CALABARZON (Region IV-A)': [
                'Antipolo', 'Batangas City', 'Lipa', 'Lucena', 
                'Calamba', 'Biñan', 'Santa Rosa', 'Bacoor', 
                'Imus', 'Dasmariñas', 'General Trias', 'Trece Martires'
            ],
            'Central Visayas (Region VII)': [
                'Cebu City', 'Mandaue', 'Lapu-Lapu', 'Tagbilaran', 
                'Dumaguete', 'Bogo', 'Carcar', 'Talisay'
            ],
            'Davao Region (Region XI)': [
                'Davao City', 'Tagum', 'Panabo', 'Digos', 
                'Mati', 'Samal', 'Island Garden City of Samal'
            ],
        },
        
        philippinesBarangaysByCity: {
            'Manila': [
                { name: 'Binondo', postal: '1006' },
                { name: 'Ermita', postal: '1000' },
                { name: 'Intramuros', postal: '1002' },
                { name: 'Malate', postal: '1004' },
                { name: 'Paco', postal: '1007' },
                { name: 'Pandacan', postal: '1011' },
                { name: 'Port Area', postal: '1018' },
                { name: 'Quiapo', postal: '1001' },
                { name: 'Sampaloc', postal: '1008' },
                { name: 'San Miguel', postal: '1005' },
                { name: 'San Nicolas', postal: '1010' },
                { name: 'Santa Ana', postal: '1009' },
                { name: 'Santa Cruz', postal: '1003' },
                { name: 'Santa Mesa', postal: '1016' },
                { name: 'Tondo', postal: '1013' },
            ],
            'Quezon City': [
                { name: 'Bagong Pag-asa', postal: '1105' },
                { name: 'Bago Bantay', postal: '1105' },
                { name: 'Bagumbayan', postal: '1110' },
                { name: 'Bahay Toro', postal: '1106' },
                { name: 'Balingasa', postal: '1115' },
                { name: 'Blue Ridge A', postal: '1109' },
                { name: 'Blue Ridge B', postal: '1109' },
                { name: 'Commonwealth', postal: '1121' },
                { name: 'Cubao', postal: '1109' },
                { name: 'Diliman', postal: '1101' },
                { name: 'East Kamias', postal: '1102' },
                { name: 'Fairview', postal: '1118' },
                { name: 'Kamuning', postal: '1103' },
                { name: 'Katipunan', postal: '1108' },
                { name: 'Libis', postal: '1110' },
                { name: 'New Era', postal: '1107' },
                { name: 'Novaliches', postal: '1123' },
                { name: 'Project 4', postal: '1109' },
                { name: 'Project 6', postal: '1100' },
                { name: 'Quirino District', postal: '1102' },
                { name: 'San Antonio', postal: '1105' },
                { name: 'Tandang Sora', postal: '1116' },
                { name: 'Teachers Village', postal: '1101' },
                { name: 'Ugong Norte', postal: '1110' },
                { name: 'White Plains', postal: '1110' },
            ],
            'Makati': [
                { name: 'Bel-Air', postal: '1209' },
                { name: 'Forbes Park', postal: '1220' },
                { name: 'Dasmarinas Village', postal: '1223' },
                { name: 'San Lorenzo Village', postal: '1223' },
                { name: 'Poblacion', postal: '1210' },
                { name: 'Salcedo Village', postal: '1227' },
                { name: 'Urdaneta Village', postal: '1225' },
                { name: 'Legaspi Village', postal: '1229' },
                { name: 'Guadalupe Nuevo', postal: '1212' },
                { name: 'Guadalupe Viejo', postal: '1211' },
                { name: 'Magallanes Village', postal: '1232' },
                { name: 'Bangkal', postal: '1233' },
                { name: 'Rockwell', postal: '1200' },
            ],
            'Taguig': [
                { name: 'Bagumbayan', postal: '1630' },
                { name: 'Bonifacio Global City (BGC)', postal: '1634' },
                { name: 'Fort Bonifacio', postal: '1634' },
                { name: 'Hagonoy', postal: '1630' },
                { name: 'Ibayo-Tipas', postal: '1630' },
                { name: 'Katuparan', postal: '1630' },
                { name: 'Ligid-Tipas', postal: '1630' },
                { name: 'Lower Bicutan', postal: '1632' },
                { name: 'Maharlika Village', postal: '1630' },
                { name: 'McKinley Hill', postal: '1634' },
                { name: 'New Lower Bicutan', postal: '1630' },
                { name: 'Napindan', postal: '1630' },
                { name: 'North Signal Village', postal: '1630' },
                { name: 'Palingon', postal: '1630' },
                { name: 'Pinagsama', postal: '1630' },
                { name: 'San Miguel', postal: '1630' },
                { name: 'Santa Ana', postal: '1630' },
                { name: 'Signal Village', postal: '1630' },
                { name: 'South Signal Village', postal: '1630' },
                { name: 'Tanyag', postal: '1630' },
                { name: 'Tuktukan', postal: '1630' },
                { name: 'Upper Bicutan', postal: '1632' },
                { name: 'Ususan', postal: '1630' },
                { name: 'Wawa', postal: '1630' },
                { name: 'Western Bicutan', postal: '1630' },
            ],
        },
        
        // Finland Data
        finlandRegions: [
            'Uusimaa',
            'Southwest Finland',
            'Satakunta',
            'Kanta-Häme',
            'Pirkanmaa',
            'Päijät-Häme',
            'Kymenlaakso',
            'South Karelia',
            'Southern Savonia',
            'Northern Savonia',
            'North Karelia',
            'Central Finland',
            'Southern Ostrobothnia',
            'Ostrobothnia',
            'Central Ostrobothnia',
            'Northern Ostrobothnia',
            'Kainuu',
            'Lapland',
            'Åland Islands'
        ],
        
        finlandMunicipalitiesByRegion: {
            'Uusimaa': [
                'Helsinki', 'Espoo', 'Vantaa', 'Kauniainen', 'Kerava', 
                'Kirkkonummi', 'Vihti', 'Nurmijärvi', 'Järvenpää', 
                'Hyvinkää', 'Tuusula', 'Sipoo', 'Porvoo', 'Mäntsälä'
            ],
            'Southwest Finland': [
                'Turku', 'Kaarina', 'Raisio', 'Naantali', 'Salo', 
                'Loimaa', 'Uusikaupunki', 'Paimio', 'Parainen', 'Somero'
            ],
            'Pirkanmaa': [
                'Tampere', 'Nokia', 'Ylöjärvi', 'Kangasala', 'Lempäälä', 
                'Pirkkala', 'Valkeakoski', 'Mänttä-Vilppula', 'Orivesi', 'Sastamala'
            ],
            'Northern Ostrobothnia': [
                'Oulu', 'Kempele', 'Haukipudas', 'Oulunsalo', 'Muhos', 
                'Liminka', 'Tyrnävä', 'Raahe', 'Ii', 'Ylivieska'
            ],
            'Lapland': [
                'Rovaniemi', 'Kemi', 'Tornio', 'Kemijärvi', 'Sodankylä', 
                'Inari', 'Kittilä', 'Kolari', 'Muonio', 'Ylitornio'
            ],
        },
        
        finlandDistrictsByMunicipality: {
            'Helsinki': [
                { name: 'Kruununhaka', postal: '00100' },
                { name: 'Kluuvi', postal: '00100' },
                { name: 'Kamppi', postal: '00100' },
                { name: 'Punavuori', postal: '00120' },
                { name: 'Eira', postal: '00130' },
                { name: 'Ullanlinna', postal: '00140' },
                { name: 'Kaivopuisto', postal: '00140' },
                { name: 'Ruoholahti', postal: '00180' },
                { name: 'Jätkäsaari', postal: '00220' },
                { name: 'Lauttasaari', postal: '00200' },
                { name: 'Kallio', postal: '00500' },
                { name: 'Vallila', postal: '00550' },
                { name: 'Pasila', postal: '00520' },
                { name: 'Töölö', postal: '00250' },
                { name: 'Meilahti', postal: '00250' },
                { name: 'Munkkiniemi', postal: '00330' },
                { name: 'Haaga', postal: '00350' },
                { name: 'Pitäjänmäki', postal: '00380' },
                { name: 'Kannelmäki', postal: '00420' },
                { name: 'Malmi', postal: '00700' },
                { name: 'Pukinmäki', postal: '00720' },
                { name: 'Tapanila', postal: '00730' },
                { name: 'Vuosaari', postal: '00980' },
                { name: 'Herttoniemi', postal: '00800' },
                { name: 'Laajasalo', postal: '00840' },
            ],
            'Espoo': [
                { name: 'Espoon keskus', postal: '02100' },
                { name: 'Matinkylä', postal: '02230' },
                { name: 'Olari', postal: '02200' },
                { name: 'Kauklahti', postal: '02700' },
                { name: 'Tapiola', postal: '02100' },
                { name: 'Leppävaara', postal: '02600' },
                { name: 'Kivenlahti', postal: '02320' },
                { name: 'Soukka', postal: '02360' },
                { name: 'Suurpelto', postal: '02600' },
                { name: 'Westend', postal: '02160' },
            ],
            'Vantaa': [
                { name: 'Tikkurila', postal: '01300' },
                { name: 'Myyrmäki', postal: '01600' },
                { name: 'Martinlaakso', postal: '01620' },
                { name: 'Koivukylä', postal: '01300' },
                { name: 'Korso', postal: '01450' },
                { name: 'Hakunila', postal: '01200' },
                { name: 'Aviapolis', postal: '01510' },
                { name: 'Pakkala', postal: '01510' },
            ],
            'Tampere': [
                { name: 'Keskusta', postal: '33100' },
                { name: 'Kaleva', postal: '33500' },
                { name: 'Hervanta', postal: '33720' },
                { name: 'Linnainmaa', postal: '33960' },
                { name: 'Kaukajärvi', postal: '33950' },
                { name: 'Lielahti', postal: '33400' },
                { name: 'Tesoma', postal: '33310' },
                { name: 'Järvensivu', postal: '33820' },
            ],
            'Oulu': [
                { name: 'Keskusta', postal: '90100' },
                { name: 'Tuira', postal: '90500' },
                { name: 'Kaukovainio', postal: '90500' },
                { name: 'Höyhtyä', postal: '90900' },
                { name: 'Kiiminki', postal: '90900' },
                { name: 'Haukipudas', postal: '90830' },
            ],
        },
        
        init() {
            // Initialize
        },
        
        get filteredRegions() {
            if (this.selectedCountry === 'Philippines') {
                if (!this.regionSearch) return this.philippinesRegions;
                return this.philippinesRegions.filter(region => 
                    region.toLowerCase().includes(this.regionSearch.toLowerCase())
                );
            } else if (this.selectedCountry === 'Finland') {
                return this.finlandRegions;
            }
            return [];
        },
        
        get filteredCities() {
            if (!this.selectedRegion) return [];
            
            let cities = [];
            if (this.selectedCountry === 'Philippines') {
                cities = this.philippinesCitiesByRegion[this.selectedRegion] || [];
            } else if (this.selectedCountry === 'Finland') {
                cities = this.finlandMunicipalitiesByRegion[this.selectedRegion] || [];
            }
            
            if (!this.citySearch) return cities;
            return cities.filter(city => 
                city.toLowerCase().includes(this.citySearch.toLowerCase())
            );
        },
        
        get filteredBarangays() {
            if (!this.selectedCity) return [];
            const barangays = this.philippinesBarangaysByCity[this.selectedCity] || [];
            
            if (!this.barangaySearch) return barangays;
            return barangays.filter(barangay => 
                barangay.name.toLowerCase().includes(this.barangaySearch.toLowerCase())
            );
        },
        
        get filteredDistricts() {
            if (!this.selectedCity) return [];
            const districts = this.finlandDistrictsByMunicipality[this.selectedCity] || [];
            
            if (!this.districtSearch) return districts;
            return districts.filter(district => 
                district.name.toLowerCase().includes(this.districtSearch.toLowerCase())
            );
        },
        
        selectMainOption(option) {
            this.selectedOption = option;
            this.mainOpen = false;
            
            if (option === 'current') {
                this.displayText = 'Use Current Location';
                this.getCurrentLocation();
            } else if (option === 'select') {
                this.displayText = 'Select a Location';
                this.locationMessage = '';
                this.locationStatus = null;
            }
        },
        
        selectCountry(country) {
            this.selectedCountry = country;
            this.countryOpen = false;
            this.resetAddress();
        },
        
        selectRegion(region) {
            this.selectedRegion = region;
            this.regionOpen = false;
            this.regionSearch = '';
            this.selectedCity = null;
            this.selectedBarangay = null;
            this.selectedDistrict = null;
            this.postalCode = '';
            this.streetAddress = '';
        },
        
        selectCity(city) {
            this.selectedCity = city;
            this.cityOpen = false;
            this.citySearch = '';
            this.selectedBarangay = null;
            this.selectedDistrict = null;
            this.postalCode = '';
            this.streetAddress = '';
        },
        
        selectBarangay(barangay) {
            this.selectedBarangay = barangay.name;
            this.postalCode = barangay.postal;
            this.barangayOpen = false;
            this.barangaySearch = '';
        },
        
        selectDistrict(district) {
            this.selectedDistrict = district.name;
            this.postalCode = district.postal;
            this.districtOpen = false;
            this.districtSearch = '';
        },
        
        resetAddress() {
            this.selectedRegion = null;
            this.selectedCity = null;
            this.selectedBarangay = null;
            this.selectedDistrict = null;
            this.postalCode = '';
            this.streetAddress = '';
        },
        
        getCurrentLocation() {
            this.locationStatus = 'loading';
            this.locationMessage = 'Getting your location...';
            
            if (!navigator.geolocation) {
                this.locationStatus = 'error';
                this.locationMessage = 'Geolocation is not supported by your browser';
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.locationStatus = 'success';
                    this.locationMessage = 'Location detected successfully!';
                    
                    setTimeout(() => {
                        this.locationMessage = '';
                        this.locationStatus = null;
                    }, 3000);
                },
                (error) => {
                    this.locationStatus = 'error';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            this.locationMessage = 'Location access denied. Please enable location permissions.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            this.locationMessage = 'Location information unavailable.';
                            break;
                        case error.TIMEOUT:
                            this.locationMessage = 'Location request timed out.';
                            break;
                        default:
                            this.locationMessage = 'An unknown error occurred.';
                    }
                }
            );
        }
    }
}
</script>
@endpush
@endonce