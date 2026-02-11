<x-layouts.general-manager :title="'History'">
    <div class="flex flex-col gap-6 w-full">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Service History</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">View past services and leave reviews</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-2">
            <div class="flex gap-2 overflow-x-auto">
                <button class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white">
                    All
                </button>
                <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    Services
                </button>
                <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    To Review
                </button>
                <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    Reports
                </button>
            </div>
        </div>

        <!-- Sort -->
        <div class="flex justify-end">
            <select class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="recent">Most Recent</option>
                <option value="oldest">Oldest First</option>
                <option value="price_high">Price: High to Low</option>
                <option value="price_low">Price: Low to High</option>
            </select>
        </div>

        <!-- History List -->
        <div class="space-y-4">
            @forelse($services ?? [] as $service)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 md:p-5 hover:shadow-lg transition-shadow">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <!-- Service Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center
                                @switch($service['type'] ?? 'default')
                                    @case('deep_clean')
                                        bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                                        @break
                                    @case('daily')
                                        bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400
                                        @break
                                    @case('snow')
                                        bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400
                                        @break
                                    @default
                                        bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                                @endswitch
                            ">
                                <i class="fa-solid fa-{{ $service['icon'] ?? 'broom' }} text-2xl"></i>
                            </div>
                        </div>

                        <!-- Service Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $service['name'] ?? 'Service' }}
                                </h3>
                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @switch($service['status'] ?? 'completed')
                                        @case('completed')
                                            bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @break
                                        @case('ongoing')
                                            bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                            @break
                                        @case('cancelled')
                                            bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @break
                                        @default
                                            bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @endswitch
                                ">
                                    {{ ucfirst($service['status'] ?? 'Completed') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $service['location'] ?? '' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                {{ $service['date'] ?? '' }}
                            </p>
                        </div>

                        <!-- Price -->
                        <div class="flex-shrink-0 text-right">
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $service['price'] ?? '0.00' }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex-shrink-0 flex gap-2">
                            @if(($service['status'] ?? '') === 'completed' && !($service['reviewed'] ?? false))
                                <button class="px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    Review
                                </button>
                            @endif
                            <button class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Details
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
                    <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-solid fa-clock-rotate-left text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">No service history yet</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if(count($services ?? []) > 0)
            <div class="flex justify-center">
                <button class="px-6 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Load More
                </button>
            </div>
        @endif
    </div>
</x-layouts.general-manager>
