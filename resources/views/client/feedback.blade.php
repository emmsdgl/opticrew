<x-layouts.general-client :title="'Feedback'">
    <x-skeleton-page :preset="'default'">
    <div class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]" x-data="feedbackForm()">
        <div class="mx-auto w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="p-6 sm:p-8">
            <form @submit.prevent="submitFeedback">
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Type of service availed
                    </label>
                    <div class="relative">
                        <select x-model="formData.service_type"
                            class="w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 pr-10 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500">
                            <option value="">Select service</option>
                            <option value="Final Cleaning">Final Cleaning</option>
                            <option value="Deep Cleaning">Deep Cleaning</option>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400 dark:text-gray-500">
                            <i class="fi fi-rr-angle-small-down"></i>
                        </span>
                    </div>
                </div>

                <div class="text-center flex flex-col gap-1.5 my-5">
                    <p class="text-xs text-gray-500 dark:text-gray-400 tracking-wide">Your feedback matters</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white leading-tight my-2.5">
                        How would you rate<br class="hidden sm:block">this service?
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-sm mx-auto">
                        Your input is valuable in helping us better understand your needs.
                    </p>
                </div>

                <div class="mb-8">
                    <div class="flex justify-center items-end gap-2 sm:gap-3">
                        @php
                            $emojis = [
                                1 => asset('images/icons/emojis/Very-Dissatisfied.svg'),
                                2 => asset('images/icons/emojis/Dissatisfied.svg'),
                                3 => asset('images/icons/emojis/Neutral.svg'),
                                4 => asset('images/icons/emojis/Satisfied.svg'),
                                5 => asset('images/icons/emojis/Very-Satisfied.svg')
                            ];
                            $ratingLabels = [
                                1 => 'Very Dissatisfied',
                                2 => 'Dissatisfied',
                                3 => 'Neutral',
                                4 => 'Satisfied',
                                5 => 'Very Satisfied'
                            ];
                        @endphp
                        @foreach ($emojis as $rating => $emojiSrc)
                            <button @click="formData.overall_rating = {{ $rating }}"
                                class="relative flex flex-col items-center transition-all duration-200 focus:outline-none group"
                                type="button">
                                <div class="rounded-full flex items-center justify-center transition-all duration-200"
                                    :class="formData.overall_rating === {{ $rating }}
                                        ? 'bg-blue-600 dark:bg-blue-500 ring-4 ring-blue-200 dark:ring-blue-900 w-12 h-12 sm:w-14 sm:h-14'
                                        : 'bg-gray-200 dark:bg-gray-800 w-10 h-10 sm:w-12 sm:h-12 group-hover:bg-gray-300 dark:group-hover:bg-gray-700'">
                                    <img src="{{ $emojiSrc }}" alt="Rating {{ $rating }}"
                                        :class="formData.overall_rating === {{ $rating }} ? 'w-7 h-7 sm:w-8 sm:h-8' : 'w-5 h-5 sm:w-7 sm:h-7 grayscale opacity-60'"
                                        class="transition-all duration-200">
                                </div>
                                <span x-show="formData.overall_rating === {{ $rating }}" x-transition
                                    class="absolute -bottom-7 text-[11px] font-semibold text-white bg-blue-600 dark:bg-blue-500 px-2.5 py-0.5 rounded-full whitespace-nowrap shadow-lg">
                                    {{ $ratingLabels[$rating] }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mt-10 mb-4">
                    <div class="flex flex-wrap justify-center gap-2">
                        @php
                            $keywords = [
                                'Punctual Service',
                                'Professional Staff',
                                'Thorough Cleaning',
                                'Good Communication',
                                'Value for Money',
                                'Friendly Team',
                                'Met Expectations',
                                'Would Recommend'
                            ];
                        @endphp
                        @foreach ($keywords as $keyword)
                            <button @click="toggleKeyword('{{ $keyword }}')"
                                :class="isKeywordSelected('{{ $keyword }}')
                                        ? 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 border-gray-900 dark:border-gray-100'
                                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-600'"
                                type="button"
                                class="px-2.5 sm:px-3.5 py-1.5 text-xs font-medium border rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700">
                                {{ $keyword }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm text-gray-900 dark:text-white mb-2">
                        Detailed Review
                    </label>
                    <textarea x-model="formData.comments" rows="3" placeholder="Add a comment"
                        class="w-full px-4 py-3 text-sm text-gray-900 dark:text-white border-0 bg-gray-50 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 focus:outline-none placeholder-gray-400 dark:placeholder-gray-500 resize-none transition-all"></textarea>
                </div>

                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" x-model="formData.would_recommend"
                               class="w-5 h-5 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fi fi-rr-thumbs-up mr-2"></i>I would recommend this service to others
                        </span>
                    </label>
                </div>

                <button type="submit" :disabled="submitting || !isFormValid" :class="submitting || !isFormValid
                    ? 'opacity-50 cursor-not-allowed bg-blue-600 dark:bg-blue-800'
                    : 'bg-blue-900 dark:bg-blue-700 hover:bg-blue-800 dark:hover:bg-blue-600'"
                    class="w-full px-6 py-3 text-sm font-bold text-white rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 dark:focus:ring-blue-700">
                    <span x-show="!submitting">Submit Feedback</span>
                    <span x-show="submitting"><i class="fi fi-rr-spinner animate-spin mr-2"></i>Submitting...</span>
                </button>
            </form>
            </div>
        </div>

        <!-- Success Toast -->
        <div x-show="showToast" x-transition
             class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 z-50"
             style="display: none;">
            <i class="fi fi-rr-check-circle text-2xl"></i>
            <div>
                <div class="font-semibold">Thank you for your feedback!</div>
                <div class="text-sm text-green-100">Your review has been submitted successfully.</div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function feedbackForm() {
            return {
                formData: {
                    service_type: '',
                    overall_rating: 0,
                    quality_rating: 3,
                    cleanliness_rating: 3,
                    punctuality_rating: 3,
                    professionalism_rating: 3,
                    value_rating: 3,
                    comments: '',
                    would_recommend: false
                },
                submitting: false,
                showToast: false,
                selectedKeywords: [],

                get isFormValid() {
                    return this.formData.service_type !== '' &&
                           this.formData.overall_rating > 0 &&
                           this.formData.comments.trim().length >= 10;
                },

                async submitFeedback() {
                    if (!this.isFormValid) {
                        window.showErrorDialog('Incomplete Form', 'Please select a service, provide an overall rating, and write at least 10 characters in your comments.');
                        return;
                    }

                    this.submitting = true;
                    const normalizedDetailRating = this.formData.overall_rating > 0 ? this.formData.overall_rating : 3;
                    const payload = {
                        ...this.formData,
                        quality_rating: normalizedDetailRating,
                        cleanliness_rating: normalizedDetailRating,
                        punctuality_rating: normalizedDetailRating,
                        professionalism_rating: normalizedDetailRating,
                        value_rating: normalizedDetailRating,
                    };

                    try {
                        const response = await fetch("{{ route('client.feedback.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            // Show success message
                            this.showToast = true;
                            setTimeout(() => {
                                this.showToast = false;
                            }, 5000);

                            // Reset form
                            this.resetForm();
                        } else {
                            window.showErrorDialog('Submission Failed', data.message || 'Failed to submit feedback. Please try again.');
                        }

                    } catch (error) {
                        console.error('Error submitting feedback:', error);
                        window.showErrorDialog('Error', 'Failed to submit feedback. Please try again.');
                    } finally {
                        this.submitting = false;
                    }
                },

                resetForm() {
                    this.formData = {
                        service_type: '',
                        overall_rating: 0,
                        quality_rating: 3,
                        cleanliness_rating: 3,
                        punctuality_rating: 3,
                        professionalism_rating: 3,
                        value_rating: 3,
                        comments: '',
                        would_recommend: false
                    };
                    this.selectedKeywords = [];
                },

                toggleKeyword(keyword) {
                    const index = this.selectedKeywords.indexOf(keyword);
                    if (index === -1) {
                        this.selectedKeywords.push(keyword);
                    } else {
                        this.selectedKeywords.splice(index, 1);
                    }
                },

                isKeywordSelected(keyword) {
                    return this.selectedKeywords.includes(keyword);
                }
            }
        }
    </script>
    @endpush
    </x-skeleton-page>
</x-layouts.general-client>
