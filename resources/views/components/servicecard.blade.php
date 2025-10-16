@props(['service' => [], 'onBook' => '', 'onFavorite' => ''])

<div x-data="serviceCard('{{ $service['title'] ?? '' }}', '{{ $onBook }}', '{{ $onFavorite }}')" 
    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-shadow 
           border border-gray-200 dark:border-gray-700 overflow-hidden max-w-sm w-full">

    <!-- Header -->
    <div class="p-8 pb-3">
        <div class="flex items-start justify-between mb-3">
            @if(!empty($service['badge']))
                <div class="flex items-center gap-2 px-3 py-1 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
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
                            <div class="w-2 h-2 rounded-full bg-gray-800 dark:bg-gray-300"></div>
                        @endfor
                    </div>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ $service['rating'] }}
                    </span>
                </div>
            @endif
        </div>

        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-2">
            {{ $service['title'] ?? 'Service Title' }}
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed line-clamp-1">
            {{ $service['description'] ?? '' }}
        </p>
    </div>

    <!-- Footer -->
    <div class="px-8 pb-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <button 
                @click="toggleFavorite()"
                class="p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 
                       hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group"
                :class="{ 'bg-red-50 border-red-300': isFavorite }">
                <svg class="w-4 h-4 transition-colors" 
                    :class="isFavorite ? 'text-red-500 fill-current' : 'text-gray-400 group-hover:text-red-500'"
                    fill="none" 
                    :fill="isFavorite ? 'currentColor' : 'none'"
                    stroke="currentColor" 
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
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
            Book Now
        </button>
    </div>
</div>

<script>
function serviceCard(title, onBook, onFavorite) {
    return {
        isFavorite: false,

        toggleFavorite() {
            this.isFavorite = !this.isFavorite;
            if (onFavorite && typeof window[onFavorite] === 'function') {
                window[onFavorite](this.isFavorite, title);
            }
        },

        bookService() {
            if (onBook && typeof window[onBook] === 'function') {
                window[onBook](title);
            }
        },

        viewDetails() {
            alert('Viewing details for: ' + title);
        }
    };
}
</script>
