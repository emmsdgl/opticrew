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
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Automatically detect your location via GPS</p>
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
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Enter address details manually</p>
                        </div>
                        <i x-show="selectedOption === 'select'" class="fa-solid fa-check text-blue-600"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Hidden Inputs for Form Submission -->
    <input type="hidden" name="{{ $name }}_type" x-model="selectedOption">
    <input type="hidden" name="{{ $name }}_street" x-model="streetAddress">
    <input type="hidden" name="{{ $name }}_postal" x-model="postalCode">
    <input type="hidden" name="{{ $name }}_city" x-model="city">
    <input type="hidden" name="{{ $name }}_district" x-model="district">
    <input type="hidden" name="{{ $name }}_latitude" x-model="latitude">
    <input type="hidden" name="{{ $name }}_longitude" x-model="longitude">

    <!-- Address Form (shown when "Select a Location" is chosen) -->
    <div x-show="selectedOption === 'select'"
         x-transition
         class="space-y-4">

        <!-- Street Address with Autocomplete -->
        <div class="relative">
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                Street Address <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <i class="fa-solid fa-road absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                <input
                    type="text"
                    x-model="streetAddress"
                    @input="searchStreet"
                    placeholder="Start typing street name..."
                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl
                           focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                           bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                           placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300">
            </div>
            <!-- Street Suggestions Dropdown -->
            <div x-show="streetSuggestions.length > 0"
                 @click.away="streetSuggestions = []"
                 class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600
                        rounded-xl shadow-xl max-h-60 overflow-y-auto">
                <template x-for="(suggestion, index) in streetSuggestions" :key="index">
                    <button
                        type="button"
                        @click="selectStreetSuggestion(suggestion)"
                        class="w-full px-4 py-3 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20
                               transition-colors duration-150 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="suggestion.street"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="suggestion.details"></p>
                    </button>
                </template>
            </div>
        </div>

        <!-- Postal Code and City (Grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Postal Code -->
            <div class="relative">
                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                    Postal Code <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                    <input
                        type="text"
                        x-model="postalCode"
                        @input="lookupPostalCode"
                        placeholder="e.g., 00100"
                        maxlength="5"
                        class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                               bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                               placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300">
                </div>
            </div>

            <!-- City -->
            <div class="relative">
                <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                    City <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-solid fa-city absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                    <input
                        type="text"
                        x-model="city"
                        @input="fetchDistrictSuggestions"
                        placeholder="e.g., Helsinki"
                        class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                               bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                               placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300">
                </div>
            </div>
        </div>

        <!-- District -->
        <div class="relative">
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">
                District <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <i class="fa-solid fa-map-pin absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                <input
                    type="text"
                    x-model="district"
                    placeholder="e.g., Kamppi"
                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl
                           focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                           bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                           placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300"
                    @focus="showDistrictSuggestions = true"
                    @click="showDistrictSuggestions = true">
            </div>
            <!-- District Suggestions Dropdown -->
            <div x-show="showDistrictSuggestions && districtSuggestions.length > 0"
                 @click.away="showDistrictSuggestions = false"
                 class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600
                        rounded-xl shadow-xl max-h-60 overflow-y-auto">
                <template x-for="(suggestion, index) in districtSuggestions" :key="index">
                    <button
                        type="button"
                        @click="selectDistrict(suggestion)"
                        class="w-full px-4 py-2.5 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20
                               transition-colors duration-150 border-b border-gray-100 dark:border-gray-700 last:border-b-0 text-sm text-gray-900 dark:text-white"
                        x-text="suggestion"></button>
                </template>
            </div>
        </div>
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

        // Selected values
        selectedOption: null,
        streetAddress: '',
        postalCode: '',
        city: '',
        district: '',
        displayText: 'Select location option',

        // Autocomplete
        streetSuggestions: [],
        districtSuggestions: [],
        showDistrictSuggestions: false,

        // Status
        locationStatus: null,
        locationMessage: '',

        // Timeouts
        searchTimeout: null,
        postalTimeout: null,
        cityTimeout: null,

        // Nominatim API
        NOMINATIM_API: 'https://nominatim.openstreetmap.org/search',
        REVERSE_API: 'https://nominatim.openstreetmap.org/reverse',

        init() {
            // Initialize
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

        async getCurrentLocation() {
            this.locationStatus = 'loading';
            this.locationMessage = 'Getting your location via GPS...';

            if (!navigator.geolocation) {
                this.locationStatus = 'error';
                this.locationMessage = 'Geolocation is not supported by your browser';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    try {
                        const response = await fetch(
                            `${this.REVERSE_API}?format=json&lat=${lat}&lon=${lng}&addressdetails=1`,
                            {
                                headers: {
                                    'User-Agent': 'OptiCrew/1.0'
                                }
                            }
                        );

                        if (response.ok) {
                            const result = await response.json();
                            const address = result.address || {};

                            // Auto-fill all address fields
                            this.streetAddress = `${address.road || ''} ${address.house_number || ''}`.trim();
                            this.postalCode = address.postcode || '';
                            this.city = address.city || address.town || address.municipality || '';
                            this.district = address.suburb || address.neighbourhood || address.quarter || '';

                            this.locationStatus = 'success';
                            this.locationMessage = 'Location detected and address fields filled automatically!';

                            setTimeout(() => {
                                this.locationMessage = '';
                                this.locationStatus = null;
                            }, 3000);
                        } else {
                            this.locationStatus = 'error';
                            this.locationMessage = 'Unable to get address from coordinates. Please enter manually.';
                        }
                    } catch (error) {
                        console.error('Reverse geocoding error:', error);
                        this.locationStatus = 'error';
                        this.locationMessage = 'Error fetching address. Please enter manually.';
                    }
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
        },

        searchStreet() {
            clearTimeout(this.searchTimeout);

            if (this.streetAddress.length < 3) {
                this.streetSuggestions = [];
                return;
            }

            this.searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(
                        `${this.NOMINATIM_API}?format=json&countrycodes=FI&addressdetails=1&limit=5&q=${encodeURIComponent(this.streetAddress)}`,
                        {
                            headers: {
                                'User-Agent': 'OptiCrew/1.0'
                            }
                        }
                    );

                    if (response.ok) {
                        const data = await response.json();
                        this.streetSuggestions = data.map(item => {
                            const address = item.address || {};
                            const road = address.road || '';
                            const houseNumber = address.house_number || '';
                            const suburb = address.suburb || address.neighbourhood || '';
                            const city = address.city || address.town || address.municipality || '';
                            const postcode = address.postcode || '';

                            return {
                                street: `${road} ${houseNumber}`.trim(),
                                details: [suburb, city, postcode].filter(x => x).join(', '),
                                postalCode: postcode,
                                city: city,
                                district: suburb
                            };
                        }).filter(item => item.street);
                    }
                } catch (error) {
                    console.error('Street search error:', error);
                    this.streetSuggestions = [];
                }
            }, 300);
        },

        selectStreetSuggestion(suggestion) {
            this.streetAddress = suggestion.street;
            this.postalCode = suggestion.postalCode || this.postalCode;
            this.city = suggestion.city || this.city;
            this.district = suggestion.district || this.district;
            this.streetSuggestions = [];

            if (this.city) {
                this.fetchDistrictSuggestions();
            }
        },

        lookupPostalCode() {
            clearTimeout(this.postalTimeout);

            if (this.postalCode.length !== 5) {
                return;
            }

            this.postalTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(
                        `${this.NOMINATIM_API}?format=json&countrycodes=FI&postalcode=${this.postalCode}&addressdetails=1&limit=1`,
                        {
                            headers: {
                                'User-Agent': 'OptiCrew/1.0'
                            }
                        }
                    );

                    if (response.ok) {
                        const data = await response.json();
                        if (data.length > 0 && data[0].address) {
                            const address = data[0].address;
                            this.city = address.city || address.town || address.municipality || this.city;
                            this.district = address.suburb || address.neighbourhood || this.district;

                            if (this.city) {
                                this.fetchDistrictSuggestions();
                            }
                        }
                    }
                } catch (error) {
                    console.error('Postal code lookup error:', error);
                }
            }, 300);
        },

        fetchDistrictSuggestions() {
            clearTimeout(this.cityTimeout);

            if (!this.city || this.city.length < 3) {
                this.districtSuggestions = [];
                return;
            }

            this.cityTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(
                        `${this.NOMINATIM_API}?format=json&countrycodes=FI&city=${encodeURIComponent(this.city)}&addressdetails=1&limit=20`,
                        {
                            headers: {
                                'User-Agent': 'OptiCrew/1.0'
                            }
                        }
                    );

                    if (response.ok) {
                        const data = await response.json();
                        const districts = new Set();
                        data.forEach(item => {
                            if (item && item.address) {
                                if (item.address.suburb) districts.add(item.address.suburb);
                                if (item.address.neighbourhood) districts.add(item.address.neighbourhood);
                                if (item.address.quarter) districts.add(item.address.quarter);
                            }
                        });
                        this.districtSuggestions = Array.from(districts).sort();
                    }
                } catch (error) {
                    console.error('District fetch error:', error);
                    this.districtSuggestions = [];
                }
            }, 300);
        },

        selectDistrict(districtName) {
            this.district = districtName;
            this.showDistrictSuggestions = false;
        }
    }
}
</script>
@endpush
@endonce
