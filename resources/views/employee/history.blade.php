<x-layouts.general-employee :title="'Activity History'">
    <div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit" x-data="{ activeTab: 'all', ...feedbackModal() }">

        {{-- Main Content Area --}}
        <div class="flex-1">
            {{-- Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Left Column - Activity List --}}
                <div class="lg:col-span-3 space-y-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock-rotate-left text-blue-600 dark:text-blue-400 text-xl"></i>
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

                        </nav>
                    </div>

                    {{-- Sort Dropdown --}}
                    <div class="flex justify-between items-center">
                        <select
                            class="px-4 py-2 bg-white dark:bg-gray-900 rounded-lg text-sm text-gray-900 dark:text-gray-100">
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
                                    <a href="#" @click.prevent="showFeedbackModal = true"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                        Rate →
                                    </a>
                                </x-slot:actions>
                            </x-client-components.history-page.activity-card>

                            {{-- Activity Card 2 --}}
                            <x-client-components.history-page.activity-card icon="🧹"
                                title="Booked a Deep Cleaning Service" date="14 Dec 2025, 8:50 pm" price="$ 120"
                                status="Completed" statusColor="text-green-600 dark:text-green-400">
                                <x-slot:actions>
                                    <a href="#"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                        Review →
                                    </a>
                                    <a href="#" @click.prevent="showFeedbackModal = true"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                        Rate →
                                    </a>
                                </x-slot:actions>
                            </x-client-components.history-page.activity-card>
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
                                    <a href="#" @click.prevent="showFeedbackModal = true"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Rate
                                        →</a>
                                </x-slot:actions>
                            </x-client-components.history-page.activity-card>
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
                                    <a href="#" @click.prevent="showFeedbackModal = true"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Rate
                                        →</a>
                                </x-slot:actions>
                            </x-client-components.history-page.activity-card>
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
                    <div
                        class="bg-none dark:bg-none rounded-lg p-8 overflow-y-auto h-full scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-200 dark:scrollbar-track-gray-800">

                        {{-- Service Details Title --}}
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-6">Service Details Summary
                        </h3>
                        {{-- Approval Status Alert --}}
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-6">
                            The task is
                            <span class="font-semibold text-orange-600 dark:text-orange-400">is not yet
                                approved</span>.
                            Kindly update the status of your request to track your status
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
                        <!-- Approval Action Buttons -->
                        <div class="space-y-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button
                                class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors text-sm flex items-center justify-center gap-2"
                                disabled>
                                <i class="fas fa-check"></i>
                                Accept
                            </button>
                            <button
                                class="w-full px-4 py-2.5 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg border border-gray-300 dark:border-gray-600 transition-colors text-sm flex items-center justify-center gap-2"
                                disabled>
                                <i class="fas fa-times"></i>
                                Decline
                            </button>
                        </div>
                        {{-- Pending Notice --}}
                        <div class="bg-none dark:bg-none rounded-lg p-4">
                            <p class="text-sm text-orange-400 dark:text-orange-500 text-center">
                                This service is currently
                                <span class="font-semibold">pending</span> and have not started yet
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Feedback Modal -->
        <div x-show="showFeedbackModal" x-cloak @click="closeFeedbackModal()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4"
            style="display: none;">
            <div @click.stop
                class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100 dark:border-gray-800 overflow-hidden"
                x-show="showFeedbackModal" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                <!-- Close button -->
                <button type="button" @click="closeFeedbackModal()"
                    class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-900 dark:bg-gray-800 text-white rounded-full hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700 z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Modal Body -->
                <div class="p-8 sm:p-10">
                    <!-- Header -->
                    <div class="text-center flex flex-col gap-2 my-6">
                        <p class="text-xs text-gray-500 dark:text-gray-400 tracking-wide">
                            Your feedback matters
                        </p>
                        <h3
                            class="text-3xl sm:text-3xl font-bold text-gray-900 dark:text-white leading-tight my-3">
                            How would you rate<br class="hidden sm:block">this task?
                        </h3>
                        <p
                            class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
                            Your input is valuable in helping us better understand your needs.
                        </p>
                    </div>

                    <!-- Emoji Rating -->
                    <div class="flex justify-center items-end gap-2 sm:gap-3 mb-10">
                        @php
                            $emojis = [
                                1 => asset('images/icons/emojis/Very-Dissatisfied.svg'),
                                2 => asset('images/icons/emojis/Dissatisfied.svg'),
                                3 => asset('images/icons/emojis/Neutral.svg'),
                                4 => asset('images/icons/emojis/Satisfied.svg'),
                                5 => asset('images/icons/emojis/Very-Satisfied.svg')
                            ];
                        @endphp
                        @foreach($emojis as $rating => $emojiSrc)
                            <button @click="selectedRating = {{ $rating }}"
                                :class="selectedRating === {{ $rating }} ? 'scale-100 sm:scale-100' : 'scale-100'"
                                class="relative flex flex-col items-center transition-all duration-200 focus:outline-none group"
                                type="button">
                                <div class="rounded-full flex items-center justify-center transition-all duration-200"
                                    :class="selectedRating === {{ $rating }} 
                                        ? 'bg-blue-600 dark:bg-blue-500 ring-4 ring-blue-200 dark:ring-blue-900 w-14 h-14 sm:w-16 sm:h-16' 
                                        : 'bg-gray-200 dark:bg-gray-800 w-12 h-12 sm:w-14 sm:h-14 group-hover:bg-gray-300 dark:group-hover:bg-gray-700'">
                                    <img src="{{ $emojiSrc }}" alt="Rating {{ $rating }}"
                                        :class="selectedRating === {{ $rating }} ? 'w-8 h-8 sm:w-10 sm:h-10' : 'w-6 h-6 sm:w-8 sm:h-8 grayscale opacity-60'"
                                        class="transition-all duration-200">
                                </div>
                                <span x-show="selectedRating === {{ $rating }}" x-transition
                                    class="absolute -bottom-8 text-xs font-semibold text-white bg-blue-600 dark:bg-blue-500 px-3 py-1 rounded-full whitespace-nowrap shadow-lg">
                                    {{ $rating }}.0 Medium
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <!-- Keyword Tags -->
                    <div class="mt-12 mb-4">
                        <div class="flex flex-wrap justify-center gap-2">
                            @php
                                $keywords = [
                                    'Well-Scheduled',
                                    'Clear Instructions',
                                    'Professional Standards',
                                    'Hygiene-Compliant',
                                    'Time-Efficient',
                                    'Rushed Timeline',
                                    'Well-Defined Steps',
                                    'Skill-Appropriate'
                                ];
                            @endphp
                            @foreach($keywords as $keyword)
                                <button @click="toggleKeyword('{{ $keyword }}')"
                                    :class="isKeywordSelected('{{ $keyword }}') 
                                            ? 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 border-gray-900 dark:border-gray-100' 
                                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-600'"
                                    type="button"
                                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs font-medium border rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700">
                                    {{ $keyword }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Detailed Review -->
                    <div class="mb-6">
                        <label class="block text-sm text-gray-900 dark:text-white mb-2">
                            Detailed Review
                        </label>
                        <textarea x-model="feedbackText" rows="3" placeholder="Add a comment"
                            class="w-full px-4 py-3 text-sm text-gray-900 dark:text-white border-0 bg-gray-50 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 focus:outline-none placeholder-gray-400 dark:placeholder-gray-500 resize-none transition-all"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button @click="submitFeedback()" :disabled="selectedRating === 0" :class="selectedRating === 0 
                ? 'opacity-50 cursor-not-allowed bg-blue-600 dark:bg-blue-800' 
                : 'bg-blue-900 dark:bg-blue-700 hover:bg-blue-800 dark:hover:bg-blue-600'" type="button"
                        class="w-full px-6 py-3.5 sm:py-4 text-sm font-bold text-white rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 dark:focus:ring-blue-700 disabled:hover:bg-blue-900 dark:disabled:hover:bg-blue-800">
                        Submit Feedback
                    </button>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function feedbackModal() {
                return {
                    showFeedbackModal: false,
                    selectedRating: 0,
                    selectedKeywords: [],
                    feedbackText: '',

                    closeFeedbackModal() {
                        this.showFeedbackModal = false;
                        // Reset form data
                        this.selectedRating = 0;
                        this.selectedKeywords = [];
                        this.feedbackText = '';
                    },

                    toggleKeyword(keyword) {
                        const index = this.selectedKeywords.indexOf(keyword);
                        if (index > -1) {
                            this.selectedKeywords.splice(index, 1);
                        } else {
                            this.selectedKeywords.push(keyword);
                        }
                    },

                    isKeywordSelected(keyword) {
                        return this.selectedKeywords.includes(keyword);
                    },

                    async submitFeedback() {
                        if (this.selectedRating === 0) {
                            return;
                        }

                        try {
                            // Update this URL to match your route - you'll need to pass the task ID dynamically
                            const response = await fetch('/employee/tasks/TASK_ID/feedback', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    rating: this.selectedRating,
                                    keywords: this.selectedKeywords,
                                    comment: this.feedbackText
                                })
                            });

                            const data = await response.json();

                            if (response.ok) {
                                // Success - close modal and show success message
                                this.closeFeedbackModal();
                                alert('Thank you for your feedback!');
                                // Or use a better notification system
                            } else {
                                alert('Error submitting feedback. Please try again.');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error submitting feedback. Please try again.');
                        }
                    }
                }
            }
        </script>
    @endpush
</x-layouts.general-employee>