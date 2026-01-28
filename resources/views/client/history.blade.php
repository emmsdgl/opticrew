<x-layouts.general-client :title="'Activity History'">
    <div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit" x-data="{ activeTab: 'all' }">

        {{-- Main Content Area --}}
        <div class="flex-1">
            {{-- Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Left Column - Activity List --}}
                <div class="lg:col-span-3 space-y-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i class="fa-regular fa-clock-rotate-left text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                            Activity History
                        </h1>
                    </div>

                    {{-- Tabs Navigation --}}
                    <div class="">
                        <nav class="flex space-x-8">
                            <button @click="activeTab = 'all'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'all' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                All
                                <span x-show="activeTab === 'all'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'services'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'services' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Services
                                <span x-show="activeTab === 'services'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'to_rate'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'to_rate' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                To Rate
                                <span x-show="activeTab === 'to_rate'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'reports'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'reports' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Reports
                                <span x-show="activeTab === 'reports'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>
                        </nav>
                    </div>

                    {{-- Sort Dropdown --}}
                    <div class="flex justify-between items-center">
                        <select
                            class="px-4 py-2 bg-transparent dark:bg-transparent rounded-lg text-sm text-gray-900 dark:text-gray-100">
                            <option>Most Recent</option>
                            <option>Oldest First</option>
                            <option>Price: High to Low</option>
                            <option>Price: Low to High</option>
                        </select>
                    </div>

                    {{-- Activity Cards Container --}}
                    <div class="space-y-3">

                        {{-- All Tab Content --}}
                        <div x-show="activeTab === 'all'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <x-client-components.history-page.activity-card icon="🧹"
                                title="Booked a Deep Cleaning Service" date="14 Dec 2025, 8:50 pm" price="$ 120"
                                status="Completed" statusColor="text-green-600 dark:text-green-400">
                                <x-slot:actions>
                                    <a href="#"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                        Review →
                                    </a>

                                    <a href="{{ route('client.appointment.create') }}"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                        Rebook →
                                    </a>
                                </x-slot:actions>
                                </x-activity-card>


                                {{-- Activity Card 2 --}}
                                <x-client-components.history-page.activity-card icon="🧹"
                                    title="Booked a Deep Cleaning Service" date="14 Dec 2025, 8:50 pm" price="$ 120"
                                    status="Completed" statusColor="text-green-600 dark:text-green-400">
                                    <x-slot:actions>
                                        <a href="#"
                                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                            Review →
                                        </a>

                                        <a href="{{ route('client.appointment.create') }}"
                                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                            Rebook →
                                        </a>
                                    </x-slot:actions>
                                    </x-activity-card>
                        </div>

                        {{-- Services Tab Content --}}
                        <div x-show="activeTab === 'services'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <x-client-components.history-page.activity-card icon="🧹"
                                title="Booked a Deep Cleaning Service" date="14 Dec 2025, 8:50 pm" price="$ 120"
                                status="Completed" statusColor="text-green-600 dark:text-green-400">
                                <x-slot:actions>
                                    <a href="#"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Review
                                        →</a>
                                    <a href="{{ route('client.appointment.create') }}"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Rebook
                                        →</a>
                                    <a href="#" @click.prevent="$dispatch('open-rate')"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Rate
                                        →</a>
                                    <x-client-components.history-page.feedback-form />

                                </x-slot:actions>
                                </x-activity-card>
                        </div>

                        {{-- To Rate Tab Content --}}
                        <div x-show="activeTab === 'to_rate'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0">
                            <x-client-components.history-page.activity-card icon="🧹"
                                title="Booked a Deep Cleaning Service" date="14 Dec 2025, 8:50 pm" price="$ 120"
                                status="Completed" statusColor="text-green-600 dark:text-green-400">
                                <x-slot:actions>
                                    <a href="#"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Review
                                        →</a>
                                    <a href="{{ route('client.appointment.create') }}"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Rebook
                                        →</a>
                                    <a href="#" @click.prevent="$dispatch('open-rate')"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Rate
                                        →</a>
                                    <x-client-components.history-page.feedback-form />

                                </x-slot:actions>
                                </x-activity-card>
                        </div>

                        {{-- Reports Tab Content --}}
                        <div x-show="activeTab === 'reports'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0">
                            <div class="bg-none dark:bg-none rounded-lg p-12 text-center">
                                <div class="text-gray-400 dark:text-gray-500 mb-3">
                                    <i class="fa-regular fa-file-lines text-4xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">No reports available</p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Right Column - Service Details Summary --}}
                <div class="lg:col-span-2 h-[calc(100vh-4rem)]">
                    <div class="bg-none dark:bg-none rounded-lg p-8 overflow-y-auto h-full scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-200 dark:scrollbar-track-gray-800">

                        {{-- Service Details Title --}}
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-6">Service Details Summary
                        </h3>
                        {{-- Approval Status Alert --}}
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-6">
                            The service quotation is
                            <span class="font-semibold text-orange-600 dark:text-orange-400">not yet
                                approved</span>.
                            Kindly check the status of your request to track your status
                        </p>
                        {{-- Details List --}}
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Appointment ID</span>
                                <span class="font-semibold text-gray-900 dark:text-white">123456789</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Service Date</span>
                                <span class="font-semibold text-gray-900 dark:text-white">2025-11-27</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Service Time</span>
                                <span class="font-semibold text-gray-900 dark:text-white">9:20 PM</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Service Type</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Final Cleaning</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Service Location</span>
                                <span class="font-semibold text-gray-900 dark:text-white">101 S from, Finland</span>
                            </div>
                        </div>

                        {{-- Pricing --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Amount</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">€280.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Payable Amount</span>
                                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">€120.00</span>
                            </div>
                        </div>

                        {{-- Pending Notice --}}
                        <div class="bg-none dark:bg-none rounded-lg p-4">
                            <p class="text-sm text-orange-400 dark:text-orange-500 text-center">
                                Your appointment is currently
                                <span class="font-semibold">pending</span> and have not started yet
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.general-client>