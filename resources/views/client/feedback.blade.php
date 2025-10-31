<x-layouts.general-client :title="'Feedback'">
    <div class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]" x-data="feedbackForm()">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 md:p-12 border border-gray-200 dark:border-gray-700">
            <div class="max-w-4xl mx-auto text-center">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fi fi-rr-comment-heart text-blue-600 dark:text-blue-400 text-3xl"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Share Your Feedback
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Your opinion matters! Help us improve our services by sharing your experience.
                </p>
            </div>
        </div>

        <!-- Feedback Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 md:p-8 border border-gray-200 dark:border-gray-700">
            <form @submit.prevent="submitFeedback">
                <!-- Service Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fi fi-rr-broom mr-2"></i>Which service did you use?
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="service_type" value="Final Cleaning" x-model="formData.service_type"
                                   class="peer sr-only">
                            <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 border-gray-300 dark:border-gray-600 hover:border-blue-400">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fi fi-rr-broom text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">Final Cleaning</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Complete cleaning service</div>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="service_type" value="Deep Cleaning" x-model="formData.service_type"
                                   class="peer sr-only">
                            <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-purple-600 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 border-gray-300 dark:border-gray-600 hover:border-purple-400">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fi fi-rr-sparkles text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">Deep Cleaning</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Intensive cleaning service</div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Overall Rating -->
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fi fi-rr-star mr-2"></i>Overall Experience
                    </label>
                    <div class="flex items-center gap-2">
                        <template x-for="star in 5" :key="star">
                            <button type="button" @click="formData.overall_rating = star"
                                    class="transition-all hover:scale-110 focus:outline-none cursor-pointer"
                                    style="line-height: 1;">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-10 h-10 transition-all"
                                     :class="star <= formData.overall_rating ? 'text-yellow-500 fill-current' : 'text-gray-300 dark:text-gray-600'"
                                     viewBox="0 0 24 24"
                                     :fill="star <= formData.overall_rating ? 'currentColor' : 'none'"
                                     stroke="currentColor"
                                     stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                </svg>
                            </button>
                        </template>
                        <span class="ml-3 text-lg font-semibold text-gray-700 dark:text-gray-300" x-show="formData.overall_rating > 0">
                            <span x-text="formData.overall_rating"></span> / 5
                        </span>
                    </div>
                </div>

                <!-- Detailed Ratings -->
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rate Specific Aspects</h3>
                    <div class="space-y-4">
                        <!-- Quality of Service -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Quality of Service</label>
                                <span class="text-sm text-gray-500 dark:text-gray-400" x-text="formData.quality_rating + ' / 5'"></span>
                            </div>
                            <input type="range" min="1" max="5" step="1" x-model="formData.quality_rating"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-blue-600">
                        </div>

                        <!-- Cleanliness -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Cleanliness Result</label>
                                <span class="text-sm text-gray-500 dark:text-gray-400" x-text="formData.cleanliness_rating + ' / 5'"></span>
                            </div>
                            <input type="range" min="1" max="5" step="1" x-model="formData.cleanliness_rating"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-blue-600">
                        </div>

                        <!-- Punctuality -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Punctuality</label>
                                <span class="text-sm text-gray-500 dark:text-gray-400" x-text="formData.punctuality_rating + ' / 5'"></span>
                            </div>
                            <input type="range" min="1" max="5" step="1" x-model="formData.punctuality_rating"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-blue-600">
                        </div>

                        <!-- Professionalism -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Professionalism</label>
                                <span class="text-sm text-gray-500 dark:text-gray-400" x-text="formData.professionalism_rating + ' / 5'"></span>
                            </div>
                            <input type="range" min="1" max="5" step="1" x-model="formData.professionalism_rating"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-blue-600">
                        </div>

                        <!-- Value for Money -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Value for Money</label>
                                <span class="text-sm text-gray-500 dark:text-gray-400" x-text="formData.value_rating + ' / 5'"></span>
                            </div>
                            <input type="range" min="1" max="5" step="1" x-model="formData.value_rating"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 accent-blue-600">
                        </div>
                    </div>
                </div>

                <!-- Comments -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fi fi-rr-comment-alt mr-2"></i>Your Comments
                    </label>
                    <textarea x-model="formData.comments" rows="6"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Share your experience with our service. What did we do well? What could we improve?"></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <i class="fi fi-rr-info mr-1"></i>Your feedback helps us improve our services
                    </p>
                </div>

                <!-- Would Recommend -->
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" x-model="formData.would_recommend"
                               class="w-5 h-5 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fi fi-rr-thumbs-up mr-2"></i>I would recommend this service to others
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4 justify-end">
                    <button type="button" @click="resetForm"
                            class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        <i class="fi fi-rr-refresh mr-2"></i>Reset Form
                    </button>
                    <button type="submit" :disabled="submitting || !isFormValid"
                            class="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!submitting">
                            <i class="fi fi-rr-paper-plane mr-2"></i>Submit Feedback
                        </span>
                        <span x-show="submitting">
                            <i class="fi fi-rr-spinner animate-spin mr-2"></i>Submitting...
                        </span>
                    </button>
                </div>
            </form>
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

                get isFormValid() {
                    return this.formData.service_type !== '' &&
                           this.formData.overall_rating > 0 &&
                           this.formData.comments.trim().length >= 10;
                },

                async submitFeedback() {
                    if (!this.isFormValid) {
                        alert('Please select a service, provide an overall rating, and write at least 10 characters in your comments.');
                        return;
                    }

                    this.submitting = true;

                    try {
                        const response = await fetch('{{ route('client.feedback.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.formData)
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
                            alert(data.message || 'Failed to submit feedback. Please try again.');
                        }

                    } catch (error) {
                        console.error('Error submitting feedback:', error);
                        alert('Failed to submit feedback. Please try again.');
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
                }
            }
        }
    </script>
    @endpush
</x-layouts.general-client>
