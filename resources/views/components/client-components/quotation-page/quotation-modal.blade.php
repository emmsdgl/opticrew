{{--
    Quotation Request Modal
    Used inside an Alpine.js x-data context that provides:
        showModal, step, submitting, form, openModal(), nextStep(), submitQuotation(),
        cscStates, cscCities, filteredDistricts, districtLoading,
        onRegionChange(), onCityChange(), loadStates()
--}}

<style>
    .quotation-modal-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .quotation-modal-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .quotation-modal-scroll::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.4);
        border-radius: 9999px;
    }
    .quotation-modal-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(156, 163, 175, 0.6);
    }
    .dark .quotation-modal-scroll::-webkit-scrollbar-thumb {
        background: rgba(107, 114, 128, 0.4);
    }
    .dark .quotation-modal-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(107, 114, 128, 0.6);
    }
    /* Firefox */
    .quotation-modal-scroll {
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.4) transparent;
    }
    .dark .quotation-modal-scroll {
        scrollbar-color: rgba(107, 114, 128, 0.4) transparent;
    }
</style>

{{-- Modal Wrapper --}}
<div x-show="showModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display:none">
    {{-- Backdrop --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="showModal = false; document.body.style.overflow = ''" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Modal Content --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-75"
         class="relative z-10 w-full max-w-2xl px-8 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-h-[95vh] min-h-[80vh] flex flex-col overflow-hidden">

        {{-- Header with Stepper --}}
        <div class="px-8 py-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request Quotation</h2>
                <button @click="showModal = false; document.body.style.overflow = ''" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            {{-- Full-width Stepper --}}
            <div class="flex items-center w-full">
                <template x-for="(label, i) in ['Service', 'Property']" :key="i">
                    <div class="flex items-center flex-1" :class="i < 1 ? '' : 'flex-none'">
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold transition-all duration-300"
                                 :class="step > i+1 ? 'bg-green-500 text-white' : step === i+1 ? 'bg-gradient-to-br from-blue-500 to-blue-700 text-white shadow-lg shadow-blue-500/30 ring-2 ring-blue-400/50' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400'"
                                 x-text="step > i+1 ? '✓' : i+1"></div>
                            <span class="text-xs font-medium hidden sm:inline" :class="step === i+1 ? 'text-blue-600 dark:text-blue-400 font-semibold' : step > i+1 ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'" x-text="label"></span>
                        </div>
                        <div x-show="i < 1" class="flex-1 h-0.5 rounded mx-3 transition-all duration-300" :class="step > i+1 ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700'"></div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 px-8 py-6 overflow-y-auto quotation-modal-scroll">

            {{-- Step 1: Service --}}
            <div x-show="step === 1" x-transition>
                <div class="space-y-5">
                    {{-- Booking Type --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Booking Type <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer">
                                <input type="radio" value="personal" x-model="form.bookingType" class="peer sr-only">
                                <div class="w-full p-4 border-2 rounded-xl transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-200 dark:border-gray-700 hover:border-blue-400">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-user text-blue-500 text-lg"></i>
                                        </div>
                                        <div>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white block">Personal</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Individual or household booking</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative flex cursor-pointer">
                                <input type="radio" value="company" x-model="form.bookingType" class="peer sr-only">
                                <div class="w-full p-4 border-2 rounded-xl transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-200 dark:border-gray-700 hover:border-blue-400">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-building text-blue-500 text-lg"></i>
                                        </div>
                                        <div>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white block">Company</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Business or corporate booking</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Service Type --}}
                    <div @service-selected.window="form.serviceType = $event.detail.value">
                        <x-client-components.quotation-page.service-dropdown
                            label="Type of Cleaning Service"
                            name="service_type"
                            :required="true"
                            placeholdericon="fa-solid fa-broom"
                            :options="[
                                ['value' => 'deep_cleaning', 'title' => 'Deep Cleaning', 'description' => '€48/hr · Thorough detail clean', 'icon' => 'fa-solid fa-broom'],
                                ['value' => 'final_cleaning', 'title' => 'Final Cleaning', 'description' => 'Fixed price · Move-out ready', 'icon' => 'fa-solid fa-wand-magic-sparkles'],
                                ['value' => 'daily_cleaning', 'title' => 'Daily Cleaning', 'description' => '€35/hr · Regular upkeep', 'icon' => 'fa-solid fa-house-chimney'],
                                ['value' => 'snowout_cleaning', 'title' => 'Snowout Cleaning', 'description' => '€55/hr · Post-winter cleanup', 'icon' => 'fa-solid fa-snowflake'],
                                ['value' => 'general_cleaning', 'title' => 'General Cleaning', 'description' => '€40/hr · All-purpose clean', 'icon' => 'fa-solid fa-spray-can-sparkles'],
                                ['value' => 'hotel_cleaning', 'title' => 'Hotel Cleaning', 'description' => '€42/hr · Hospitality standard', 'icon' => 'fa-solid fa-hotel'],
                            ]"
                        />
                    </div>

                    {{-- Date & Duration --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div @date-selected.window="form.serviceDate = $event.detail.value">
                            <x-client-components.quotation-page.service-datepicker
                                label="Preferred Date"
                                name="service_date"
                                placeholder="Select date"
                                :minDate="now()->format('Y-m-d')"
                            />
                        </div>
                        <div x-data="{ urgencyOpen: false, positionUrgency() {
                                this.$nextTick(() => {
                                    const rect = this.$refs.urgencyBtn.getBoundingClientRect();
                                    const panel = this.$refs.urgencyPanel;
                                    if (!panel) return;
                                    const panelH = panel.offsetHeight || 180;
                                    panel.style.top = (rect.top - panelH - 4) + 'px';
                                    panel.style.left = rect.left + 'px';
                                    panel.style.width = rect.width + 'px';
                                });
                            } }">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Urgency</label>
                                <button type="button" x-ref="urgencyBtn" @click="urgencyOpen = !urgencyOpen; if (urgencyOpen) $nextTick(() => positionUrgency())"
                                    class="w-full flex items-center justify-between px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                                    :class="urgencyOpen && 'border-blue-500'">
                                    <span class="text-gray-900 dark:text-white" x-text="{
                                        'regular': 'Regular (5+ days)',
                                        'soon': 'Soon (3-4 days)',
                                        'urgent': 'Urgent (1-2 days)',
                                        'emergency': 'Emergency (Same day)'
                                    }[form.urgency] || 'Select urgency...'"></span>
                                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="urgencyOpen && 'rotate-180'"></i>
                                </button>
                                <div x-show="urgencyOpen" @click.away="urgencyOpen = false" x-ref="urgencyPanel"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="fixed z-[10000] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-2xl overflow-hidden" style="display:none;">
                                    <button type="button" @click="form.urgency = 'regular'; urgencyOpen = false"
                                        class="w-full text-left px-4 py-2.5 text-sm text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        :class="form.urgency === 'regular' && 'bg-blue-50 dark:bg-blue-900/20'">Regular (5+ days)</button>
                                    <button type="button" @click="form.urgency = 'soon'; urgencyOpen = false"
                                        class="w-full text-left px-4 py-2.5 text-sm text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        :class="form.urgency === 'soon' && 'bg-blue-50 dark:bg-blue-900/20'">Soon (3-4 days)</button>
                                    <button type="button" @click="form.urgency = 'urgent'; urgencyOpen = false"
                                        class="w-full text-left px-4 py-2.5 text-sm text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        :class="form.urgency === 'urgent' && 'bg-blue-50 dark:bg-blue-900/20'">Urgent (1-2 days)</button>
                                    <button type="button" @click="form.urgency = 'emergency'; urgencyOpen = false"
                                        class="w-full text-left px-4 py-2.5 text-sm text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        :class="form.urgency === 'emergency' && 'bg-blue-50 dark:bg-blue-900/20'">Emergency (Same day)</button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Property --}}
            <div x-show="step === 2" x-transition>
                <div class="space-y-5">
                    {{-- Property Type Dropdown --}}
                    <div @property-selected.window="form.propertyType = $event.detail.value">
                        <x-client-components.quotation-page.service-dropdown
                            label="Property Type"
                            name="property_type"
                            :required="true"
                            placeholdericon="fa-solid fa-house"
                            eventName="property-selected"
                            dropDirection="down"
                            :options="[
                                ['value' => 'apartment', 'title' => 'Apartment / Flat', 'description' => 'Condo, flat, or studio unit', 'icon' => 'fa-solid fa-building'],
                                ['value' => 'house', 'title' => 'House', 'description' => 'Detached or semi-detached home', 'icon' => 'fa-solid fa-house'],
                                ['value' => 'cabin', 'title' => 'Cabin / Cottage', 'description' => 'Vacation or rural property', 'icon' => 'fa-solid fa-tree'],
                                ['value' => 'office', 'title' => 'Office', 'description' => 'Workspace or co-working space', 'icon' => 'fa-solid fa-briefcase'],
                                ['value' => 'hotel', 'title' => 'Hotel / Accommodation', 'description' => 'Hospitality or lodging', 'icon' => 'fa-solid fa-hotel'],
                                ['value' => 'commercial', 'title' => 'Commercial Space', 'description' => 'Retail, warehouse, or industrial', 'icon' => 'fa-solid fa-store'],
                                ['value' => 'other', 'title' => 'Other', 'description' => 'Other property type', 'icon' => 'fa-solid fa-ellipsis'],
                            ]"
                        />
                    </div>

                    {{-- Size & Rooms --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Floors</label>
                            <input type="number" x-model="form.floors" min="1" max="10" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Rooms</label>
                            <input type="number" x-model="form.rooms" min="1" max="20" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Area (m²)</label>
                            <input type="number" x-model="form.floorArea" min="0" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>

                    {{-- Location: Region & City (CSC API) --}}
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Region Dropdown --}}
                        <div x-data="{ regionOpen: false, regionSearch: '', positionRegion() {
                                this.$nextTick(() => {
                                    const rect = this.$refs.regionBtn.getBoundingClientRect();
                                    const panel = this.$refs.regionPanel;
                                    if (!panel) return;
                                    const panelH = panel.offsetHeight || 250;
                                    panel.style.top = (rect.top - panelH - 4) + 'px';
                                    panel.style.left = rect.left + 'px';
                                    panel.style.width = rect.width + 'px';
                                });
                            } }">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Region (Maakunta) <span class="text-red-500">*</span></label>
                                <button type="button" x-ref="regionBtn" @click="regionOpen = !regionOpen; if (regionOpen) $nextTick(() => positionRegion())"
                                    class="w-full flex items-center justify-between px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 text-sm">
                                    <span class="flex items-center gap-2">
                                        <i class="fa-solid fa-map-location-dot text-blue-500 text-xs"></i>
                                        <span x-text="form.region || 'Select Region...'" :class="form.region ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"></span>
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="regionOpen && 'rotate-180'"></i>
                                </button>
                                <div x-show="regionOpen" @click.away="regionOpen = false" x-ref="regionPanel"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="fixed z-[10000] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-2xl overflow-hidden" style="display:none;">
                                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                        <input type="text" x-model="regionSearch" placeholder="Search region..."
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div class="max-h-48 overflow-y-auto">
                                        <template x-for="s in cscStates.filter(s => s.name.toLowerCase().includes(regionSearch.toLowerCase()))" :key="s.iso2">
                                            <button type="button"
                                                @click="form.region = s.name; regionOpen = false; regionSearch = ''; onRegionChange();"
                                                class="w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left"
                                                :class="form.region === s.name ? 'bg-blue-50 dark:bg-blue-900/20' : ''">
                                                <span class="text-gray-900 dark:text-white" x-text="s.name"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                        </div>

                        {{-- City Dropdown --}}
                        <div x-data="{ cityOpen: false, citySearch: '', positionCity() {
                                this.$nextTick(() => {
                                    const rect = this.$refs.cityBtn.getBoundingClientRect();
                                    const panel = this.$refs.cityPanel;
                                    if (!panel) return;
                                    const panelH = panel.offsetHeight || 250;
                                    panel.style.top = (rect.top - panelH - 4) + 'px';
                                    panel.style.left = rect.left + 'px';
                                    panel.style.width = rect.width + 'px';
                                });
                            } }">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">City (Kunta) <span class="text-red-500">*</span></label>
                                <button type="button" x-ref="cityBtn" @click="if (cscCities.length > 0) { cityOpen = !cityOpen; if (cityOpen) $nextTick(() => positionCity()); }"
                                    :class="cscCities.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="w-full flex items-center justify-between px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 text-sm">
                                    <span class="flex items-center gap-2">
                                        <i class="fa-solid fa-city text-blue-500 text-xs"></i>
                                        <span x-text="form.city || 'Select City...'" :class="form.city ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"></span>
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="cityOpen && 'rotate-180'"></i>
                                </button>
                                <div x-show="cityOpen" @click.away="cityOpen = false" x-ref="cityPanel"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="fixed z-[10000] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-2xl overflow-hidden" style="display:none;">
                                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                        <input type="text" x-model="citySearch" placeholder="Search city..."
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div class="max-h-48 overflow-y-auto">
                                        <template x-for="c in cscCities.filter(c => c.name.toLowerCase().includes(citySearch.toLowerCase()))" :key="c.name">
                                            <button type="button"
                                                @click="form.city = c.name; cityOpen = false; citySearch = ''; onCityChange();"
                                                class="w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left"
                                                :class="form.city === c.name ? 'bg-blue-50 dark:bg-blue-900/20' : ''">
                                                <span class="text-gray-900 dark:text-white" x-text="c.name"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                        </div>
                    </div>

                    {{-- Postal Code & District --}}
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Postal Code --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Postal Code</label>
                            <input type="text" x-model="form.postalCode" maxlength="5" placeholder="00100" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 outline-none">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Auto-filled from city. Editable.</p>
                        </div>

                        {{-- District --}}
                        <div x-data="{ distOpen: false, distSearch: '', customDistrictValue: '', isCustomDistrict: false, positionDist() {
                                this.$nextTick(() => {
                                    const rect = this.$refs.distBtn.getBoundingClientRect();
                                    const panel = this.$refs.distPanel;
                                    if (!panel) return;
                                    const panelH = panel.offsetHeight || 300;
                                    panel.style.top = (rect.top - panelH - 4) + 'px';
                                    panel.style.left = rect.left + 'px';
                                    panel.style.width = rect.width + 'px';
                                });
                            } }">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">District (Kaupunginosa)</label>
                                <button type="button" x-ref="distBtn"
                                    @click="if (!districtLoading) { distOpen = !distOpen; if (distOpen) $nextTick(() => positionDist()); }"
                                    :disabled="!form.region || !form.city || districtLoading"
                                    class="w-full flex items-center justify-between px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot text-blue-500 text-xs"></i>
                                        <span x-show="districtLoading"><i class="fa-solid fa-spinner fa-spin text-blue-400 text-xs mr-1"></i>Loading...</span>
                                        <span x-show="!districtLoading && !isCustomDistrict" x-text="form.district || 'Select District...'"
                                              :class="form.district ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"></span>
                                        <span x-show="!districtLoading && isCustomDistrict" class="text-gray-900 dark:text-white"
                                              x-text="customDistrictValue || 'Others (type below)'"></span>
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="distOpen && 'rotate-180'"></i>
                                </button>
                                <div x-show="distOpen" @click.away="distOpen = false" x-ref="distPanel"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="fixed z-[10000] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-2xl overflow-hidden" style="display:none;">
                                    {{-- Search --}}
                                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                                        <input type="text" x-model="distSearch" placeholder="Search district..."
                                            class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    {{-- District options --}}
                                    <div class="max-h-40 overflow-y-auto">
                                        <template x-for="d in filteredDistricts.filter(d => d.toLowerCase().includes(distSearch.toLowerCase()))" :key="d">
                                            <button type="button"
                                                @click="isCustomDistrict = false; customDistrictValue = ''; form.district = d; distOpen = false; distSearch = '';"
                                                class="w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left"
                                                :class="form.district === d && !isCustomDistrict ? 'bg-blue-50 dark:bg-blue-900/20' : ''">
                                                <span class="text-gray-900 dark:text-white" x-text="d"></span>
                                            </button>
                                        </template>
                                    </div>
                                    {{-- Others: custom input --}}
                                    <div class="p-2.5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                        <label class="block text-[10px] font-semibold text-gray-400 dark:text-gray-500 mb-1 uppercase tracking-wider">Others</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" x-model="customDistrictValue"
                                                   @input="isCustomDistrict = true; form.district = customDistrictValue"
                                                   @focus="isCustomDistrict = true"
                                                   @keydown.enter.prevent="if (customDistrictValue.trim()) { form.district = customDistrictValue.trim(); distOpen = false; }"
                                                   class="flex-1 px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                   placeholder="Type district name...">
                                            <button type="button"
                                                @click="if (customDistrictValue.trim()) { form.district = customDistrictValue.trim(); distOpen = false; }"
                                                :disabled="!customDistrictValue.trim()"
                                                class="px-3 py-1.5 text-xs font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>

                    {{-- Special Requests --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Special Requests</label>
                        <textarea x-model="form.specialRequests" rows="3" placeholder="Any additional details or requirements..." class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-8 py-5 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between flex-shrink-0">
            <button x-show="step > 1" @click="step--" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i>Back
            </button>
            <div x-show="step === 1"></div>

            <button x-show="step === 1" @click="nextStep()" class="px-6 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-md">
                Next <i class="fa-solid fa-arrow-right ml-2"></i>
            </button>
            <button x-show="step === 2" @click="submitQuotation()" :disabled="submitting" class="px-6 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-md disabled:opacity-50">
                <span x-show="!submitting">Submit Request</span>
                <span x-show="submitting"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Submitting...</span>
            </button>
        </div>
    </div>
</div>
