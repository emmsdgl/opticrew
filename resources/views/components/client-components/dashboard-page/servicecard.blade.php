@props(['service' => [], 'onBook' => '', 'onFavorite' => ''])

<div x-data="{
    isFavorite: false,
    showModal: false,
    serviceTitle: '{{ $service['title'] ?? '' }}',
    serviceData: {{ json_encode($service) }},
    init() {
        // Load favorite state from localStorage
        const favorites = JSON.parse(localStorage.getItem('favoriteServices') || '[]');
        this.isFavorite = favorites.includes(this.serviceTitle);
    },
    toggleFavorite() {
        this.isFavorite = !this.isFavorite;

        // Save to localStorage
        let favorites = JSON.parse(localStorage.getItem('favoriteServices') || '[]');
        if (this.isFavorite) {
            if (!favorites.includes(this.serviceTitle)) {
                favorites.push(this.serviceTitle);
            }
        } else {
            favorites = favorites.filter(s => s !== this.serviceTitle);
        }
        localStorage.setItem('favoriteServices', JSON.stringify(favorites));

        // Dispatch event for other components to listen
        window.dispatchEvent(new CustomEvent('favorites-updated', { detail: { favorites } }));

        if ('{{ $onFavorite }}' && typeof window['{{ $onFavorite }}'] === 'function') {
            window['{{ $onFavorite }}'](this.isFavorite, this.serviceTitle);
        }
    },
    bookService() {
        // Redirect to appointment form
        window.location.href = '{{ route('client.appointment.create') }}';
    },
    viewDetails() {
        this.showModal = true;
        document.body.style.overflow = 'hidden';
    },
    closeModal() {
        this.showModal = false;
        document.body.style.overflow = 'auto';
    }
}"
    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-shadow
           border border-gray-200 dark:border-gray-700 h-full flex flex-col">

    <!-- Header -->
    <div class="p-6 pb-3 flex-grow">
        <div class="flex items-start justify-between mb-3">
            @if(!empty($service['badge']))
                <div class="flex items-center gap-2 px-3 py-1 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/>
                    </svg>
                    <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">
                        {{ $service['badge'] }}
                    </span>
                </div>
            @endif

            @if(!empty($service['rating']))
                <div class="flex items-center gap-1.5">
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 3; $i++)
                            <i class="fa-solid fa-star text-sm text-gray-800 dark:text-gray-300"></i>
                        @endfor
                    </div>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ $service['rating'] }}
                    </span>
                </div>
            @endif
        </div>

        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
            {{ $service['title'] ?? 'Service Title' }}
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed line-clamp-2 my-2">
            {{ $service['description'] ?? '' }}
        </p>
    </div>

    <!-- Footer -->
    <div class="px-6 pb-6 flex items-center justify-between gap-3 mt-auto">
        <div class="flex items-center gap-2">
            <button 
                @click="toggleFavorite()"
                class="p-2.5 rounded-lg border transition-colors group"
                :class="isFavorite ? 'bg-red-50 border-red-300 dark:bg-red-900/20 dark:border-red-700' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                <svg class="w-4 h-4 transition-colors" 
                    :class="isFavorite ? 'text-red-500 fill-current' : 'text-gray-400 group-hover:text-red-500'"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                          :fill="isFavorite ? 'currentColor' : 'none'"/>
                </svg>
            </button>

            <button 
                @click="viewDetails()"
                class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 
                       text-xs font-medium text-gray-700 dark:text-gray-300
                       hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                View
            </button>
        </div>

        <button
            @click="bookService()"
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold
                   rounded-lg transition-colors shadow-sm hover:shadow-md flex-1 max-w-[140px]">
            Book
        </button>
    </div>

    <!-- Service Details Modal -->
    <div x-show="showModal" x-cloak @click="closeModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        style="display: none;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div @click.stop
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md max-h-[85vh] flex flex-col"
            x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <!-- Scrollable Content -->
            <div class="overflow-y-auto flex-1 p-10 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600">
                
                <div class = "flex flex-col mb-3">
                    <!-- Title + Close -->
                    <div class="flex items-start justify-between mb-1">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-text="serviceData.title"></h2>
                        <button @click="closeModal()" class="p-1 -mr-1 -mt-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <!-- Estimated Price -->
                    <p class="text-base text-gray-900 dark:text-white mb-2" x-text="serviceData.price || 'Contact for pricing'"></p>
                </div>


                <!-- Rating (dynamic stars based on actual average) -->
                <div class="flex items-center gap-1.5 mb-4">
                    <div class="flex items-center gap-0.5">
                        <template x-for="star in 5" :key="star">
                            <i class="text-xs"
                               :class="star <= Math.floor(parseFloat(serviceData.rating) || 0)
                                   ? 'fa-solid fa-star text-yellow-400'
                                   : star === Math.ceil(parseFloat(serviceData.rating) || 0) && (parseFloat(serviceData.rating) || 0) % 1 >= 0.25
                                       ? 'fa-solid fa-star-half-stroke text-yellow-400'
                                       : 'fa-regular fa-star text-gray-300 dark:text-gray-600'"></i>
                        </template>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="serviceData.rating"></span>
                </div>

                <!-- Description -->
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-6" x-text="serviceData.description"></p>

                <!-- What's Included -->
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">What's Included</h3>

                <!-- Final Cleaning Inclusions -->
                <template x-if="serviceData.title === 'Final Cleaning'">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Basic final cleaning includes cleaning kitchen, living room, bedrooms, bathroom and sauna.</p>

                        <div class="space-y-3">
                            <!-- Kitchen -->
                            <div class="rounded-xl border-2 border-blue-200 dark:border-blue-700/50 p-4">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Kitchen</h4>
                                <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                    <li>Wipe tables and take out trash</li>
                                    <li>Emptying and clean fridges, oven, microwave and kitchen surfaces</li>
                                    <li>Empty dishwasher and put the dishes in their places</li>
                                </ul>
                            </div>

                            <!-- Living Room & Bedrooms -->
                            <div class="rounded-xl border-2 border-purple-200 dark:border-purple-700/50 p-4">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Living Room & Bedrooms</h4>
                                <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                    <li>Wiping glass windows, do dusting surfaces, carpets and make beds</li>
                                    <li>Emptying the ashes from the fireplace, vacuuming and mopping the floor</li>
                                    <li>Put the furniture in place</li>
                                </ul>
                            </div>

                            <!-- Bathroom & Sauna -->
                            <div class="rounded-xl border-2 border-teal-200 dark:border-teal-700/50 p-4">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Bathroom & Sauna</h4>
                                <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                    <li>Wash the basin and toilet bowl</li>
                                    <li>Refill toiletries</li>
                                    <li>Wipe mirror cabinet, shower wall and sauna glass door</li>
                                    <li>Wash the floor</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Deep Cleaning Inclusions -->
                <template x-if="serviceData.title === 'Deep Cleaning'">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Comprehensive deep cleaning service that goes beyond regular cleaning.</p>

                        <div class="space-y-3">
                            <!-- All Final Cleaning -->
                            <div class="rounded-xl border-2 border-indigo-200 dark:border-indigo-700/50 p-4">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">All Final Cleaning Tasks</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Complete coverage of kitchen, living areas, bedrooms, bathroom and sauna</p>
                            </div>

                            <!-- Extra Deep Clean -->
                            <div class="rounded-xl border-2 border-blue-200 dark:border-blue-700/50 p-4">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Deep Clean Extras</h4>
                                <ul class="space-y-1 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                    <li>Hard-to-reach areas — behind appliances, under furniture, high surfaces</li>
                                    <li>Detailed scrubbing — extra attention to grout, tiles, and stubborn stains</li>
                                    <li>Full sanitization of all surfaces</li>
                                    <li>Spotless finish for exceptional results</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Actions (fixed at bottom) -->
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex-shrink-0 space-y-2">
                <button @click="bookService(); closeModal()"
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    Book This Service
                </button>
                <button @click="closeModal()"
                    class="w-full py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                    View full details
                </button>
            </div>
        </div>
    </div>
</div>