<div x-data="{
        showFeedbackModal: false,
        selectedRating: 3,
        feedbackText: '',
        keywordsSelected: [],
        ratingDescriptions: {
            1: 'Very Dissatisfied',
            2: 'Dissatisfied',
            3: 'Neutral',
            4: 'Satisfied',
            5: 'Very Satisfied'
        },
        closeFeedbackModal() {
            this.showFeedbackModal = false;
        }
    }" @open-rate.window="showFeedbackModal = true">

    <div x-show="showFeedbackModal" x-cloak @click="closeFeedbackModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4"
        style="display: none;">
        <div @click.stop
            class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100 dark:border-gray-800 overflow-hidden"
            x-show="showFeedbackModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <!-- Close button -->
            <button type="button" @click="closeFeedbackModal()"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-900 dark:bg-gray-800 text-white rounded-full hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700 z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Body -->
            <div class="p-6 sm:p-12 sm:py-8">
                <!-- Header -->
                <div class="text-center flex flex-col gap-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400 tracking-wide">
                        {{ $subtitle ?? 'Your feedback matters' }}
                    </p>
                    <h3 class="text-2xl sm:text-2xl font-bold text-gray-900 dark:text-white leading-tight my-2">
                        {!! $title ?? 'How would you rate<br>our service?' !!}
                    </h3>
                    <p class="text-xs sm:text-xs text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
                        {{ $description ?? 'Help us understand how to provide a service that meets your needs.' }}
                    </p>
                </div>

                <!-- Emoji Rating -->
                <div class="flex justify-center items-end gap-2 sm:gap-3 my-9 mt-6">
                    @php
                        $emojis = $emojis ?? [
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
                                {{ $rating }}.0 <span x-text="ratingDescriptions[{{ $rating }}]"></span>
                            </span>
                        </button>
                    @endforeach
                </div>

                <!-- Keyword Tags -->
                <div class="mt-12 mb-4">
                    <div class="flex flex-wrap justify-center gap-3">
                        @php
                            $keywords = $keywords ?? ['Professional', 'Rushed', 'Disrespectful', 'Skilled'];
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

                <!-- Upload Box -->
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-4">
                        Add a Photo Review (Optional)
                    </label>
                    <label
                        class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-6 py-3 text-center cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition">
                        <div class="mb-2 text-blue-600 dark:text-blue-400">
                            <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v9.75M3 16.5L7.5 12l4.5 4.5 4.5-6 4.5 6M3 16.5v.75A2.25 2.25 0 005.25 19.5h13.5A2.25 2.25 0 0021 17.25v-.75" />
                            </svg>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            Upload images or videos or
                            <span class="text-blue-600 dark:text-blue-400 font-semibold">browse</span>
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            JPG, PNG, JPEG · MAX 2MB
                        </p>
                        <input type="file" class="hidden" />
                    </label>
                </div>

                <!-- Detailed Review -->
                <div class="">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-4">
                        Detailed Review
                    </label>
                    <textarea x-model="feedbackText" rows="3" placeholder="Add a comment..."
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-4 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 focus:outline-none resize-none"></textarea>
                </div>

                <div class="flex items-center justify-between w-full gap-2 mt-2">
                    <!-- Submit Button -->
                    <button @click="submitFeedback()" :disabled="selectedRating === 0" :class="selectedRating === 0 
                 ? 'opacity-50 cursor-not-allowed bg-blue-600 dark:bg-blue-800' 
                 : 'bg-blue-900 dark:bg-blue-700 hover:bg-blue-800 dark:hover:bg-blue-600'" type="button"
                        class="flex-1 px-6 py-3 sm:py-3 text-sm font-bold text-white rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 dark:focus:ring-blue-700 disabled:hover:bg-blue-900 dark:disabled:hover:bg-blue-800">
                        Submit Feedback
                    </button>

                    <!-- File a report link -->
                    <a href="#" @click.prevent="$dispatch('open-report')"
                        class="flex-1 text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium whitespace-nowrap items-center text-center">
                        Want to File a report?
                    </a>

                    <x-client-components.history-page.report-form />
                </div>

            </div>
        </div>
    </div>
</div>