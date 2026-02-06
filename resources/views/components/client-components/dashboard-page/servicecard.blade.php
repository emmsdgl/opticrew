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
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
        style="display: none;">
        <div @click.stop class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto"
            x-show="showModal" x-transition>

            <!-- Modal Header -->
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-between items-center z-10">
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="serviceData.title"></h2>
                    <span x-show="serviceData.badge" class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-semibold rounded-full" x-text="serviceData.badge"></span>
                </div>
                <button @click="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <!-- Rating -->
                <div class="flex items-center gap-2 mb-6">
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star text-base text-yellow-400"></i>
                        @endfor
                    </div>
                    <span class="text-lg font-bold text-gray-700 dark:text-gray-300" x-text="serviceData.rating"></span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">(Based on customer reviews)</span>
                </div>

                <!-- Service Description -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Service Overview</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed" x-text="serviceData.description"></p>
                </div>

                <!-- Detailed Information for Final Cleaning -->
                <template x-if="serviceData.title === 'Final Cleaning'">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">What's Included</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Basic final cleaning includes cleaning kitchen, living room, bedrooms, bathroom and sauna.</p>
                        </div>

                        <!-- Kitchen -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-100 dark:border-blue-800">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fi fi-rr-utensils text-blue-600 dark:text-blue-400 text-lg"></i>
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">Kitchen</h4>
                            </div>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span>Wipe tables and take out trash</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span>Emptying and clean fridges, oven, microwave and kitchen surfaces</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span>Empty dishwasher and put the dishes in their places</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Living Room and Bedrooms -->
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-100 dark:border-purple-800">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fi fi-rr-bed text-purple-600 dark:text-purple-400 text-lg"></i>
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">Living Room & Bedrooms</h4>
                            </div>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span>Wiping glass windows, do dusting surfaces, carpets and make beds</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span>Emptying the ashes from the fireplace, vacuuming and mopping the floor</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span>Put the furniture in place</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Bathroom and Sauna -->
                        <div class="bg-teal-50 dark:bg-teal-900/20 rounded-lg p-4 border border-teal-100 dark:border-teal-800">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fi fi-rr-shower text-teal-600 dark:text-teal-400 text-lg"></i>
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">Bathroom & Sauna</h4>
                            </div>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span>Wash the basin and toilet bowl</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span>Refill toiletries</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span>Wipe mirror cabinet, shower wall and sauna glass door</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span>Wash the floor</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </template>

                <!-- Detailed Information for Deep Cleaning -->
                <template x-if="serviceData.title === 'Deep Cleaning'">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">What's Included</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Comprehensive deep cleaning service that goes beyond regular cleaning.</p>
                        </div>

                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 border border-indigo-100 dark:border-indigo-800">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fi fi-rr-sparkles text-indigo-600 dark:text-indigo-400 text-lg"></i>
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">Deep Cleaning Includes</h4>
                            </div>
                            <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span><strong>All Final Cleaning tasks</strong> - Complete coverage of kitchen, living areas, bedrooms, bathroom and sauna</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span><strong>Hard-to-reach areas</strong> - Behind appliances, under furniture, high surfaces</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span><strong>Detailed scrubbing</strong> - Extra attention to grout, tiles, and stubborn stains</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span><strong>Sanitization</strong> - Thorough cleaning and disinfection of all surfaces</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fi fi-rr-check text-green-600 dark:text-green-400 mt-0.5"></i>
                                    <span><strong>Spotless finish</strong> - Comprehensive cleaning for exceptional results</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </template>

                <!-- Pricing Note -->
                <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <div class="flex items-start gap-2">
                        <i class="fi fi-rr-info text-yellow-600 dark:text-yellow-400 mt-0.5"></i>
                        <div class="text-sm text-yellow-800 dark:text-yellow-200">
                            <p class="font-semibold mb-1">Pricing Information</p>
                            <p>Prices vary based on unit size. Sundays and holidays are charged at double rate. Click "Book" to get started with your booking and see exact pricing.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 justify-end mt-6">
                    <button @click="closeModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Close
                    </button>
                    <button @click="bookService(); closeModal()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        <i class="fi fi-rr-calendar-plus mr-2"></i>Book This Service
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>