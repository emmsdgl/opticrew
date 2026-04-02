<x-layouts.general-manager :title="'History'">
    <div class="flex flex-col gap-6 w-full" x-data="historyManager()">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Service History</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">View past services and leave reviews</p>
            </div>
            @if(($toReviewCount ?? 0) > 0)
                <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <i class="fa-solid fa-star text-blue-600 dark:text-blue-400"></i>
                    <span class="text-sm text-blue-700 dark:text-blue-300">{{ $toReviewCount }} service{{ $toReviewCount > 1 ? 's' : '' }} awaiting review</span>
                </div>
            @endif
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-2">
            <div class="flex gap-2 overflow-x-auto">
                @foreach(['all' => 'All', 'services' => 'Services', 'to_review' => 'To Review'] as $value => $label)
                    <a href="{{ route('manager.history', ['filter' => $value, 'sort' => $sort ?? 'recent']) }}"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ ($filter ?? 'all') === $value ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        {{ $label }}
                        @if($value === 'to_review' && ($toReviewCount ?? 0) > 0)
                            <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full {{ ($filter ?? 'all') === $value ? 'bg-white/20' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' }}">{{ $toReviewCount }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Sort -->
        <div class="flex justify-end">
            <select onchange="window.location.href = '{{ route('manager.history', ['filter' => $filter ?? 'all']) }}&sort=' + this.value"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="recent" {{ ($sort ?? 'recent') === 'recent' ? 'selected' : '' }}>Most Recent</option>
                <option value="oldest" {{ ($sort ?? '') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                <option value="price_high" {{ ($sort ?? '') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="price_low" {{ ($sort ?? '') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
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
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @switch($service['status'] ?? 'completed')
                                        @case('completed')
                                            bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
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
                                @if($service['reviewed'] ?? false)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        <i class="fa-solid fa-star text-[10px]"></i> Reviewed
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $service['location'] ?? '' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $service['date'] ?? '' }}</p>
                        </div>

                        <!-- Price -->
                        <div class="flex-shrink-0 text-right">
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $service['price'] ?? '0.00' }}</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex-shrink-0 flex gap-2">
                            @if(($service['status'] ?? '') === 'completed' && !($service['reviewed'] ?? false))
                                <button @click="openReviewModal({{ $service['id'] }}, '{{ addslashes($service['name'] ?? 'Service') }}')"
                                        class="px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    <i class="fa-solid fa-star mr-1"></i> Review
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-2 px-6 text-center">
                    <div class="w-48 h-48 mb-6 flex items-center justify-center">
                        <img src="{{ asset('images/icons/no-items-found.svg') }}"
                             alt="No service history"
                             class="w-full h-full object-contain opacity-80 dark:opacity-60">
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                        No service history yet
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">
                        Your service history will appear here.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Review Modal -->
        <div x-show="showReviewModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showReviewModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Review Service</h3>
                        <button @click="showReviewModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                            <i class="fa-solid fa-xmark text-gray-500"></i>
                        </button>
                    </div>
                    <div class="p-4 space-y-5">
                        <p class="text-sm text-gray-600 dark:text-gray-400 text-center" x-text="'How was the service at ' + reviewServiceName + '?'"></p>

                        <!-- Star Rating -->
                        <div class="flex justify-center gap-2">
                            <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                <button @click="reviewForm.rating = star" class="text-3xl transition-transform hover:scale-110">
                                    <span x-text="star <= reviewForm.rating ? ratingEmojis[star - 1] : '&#9898;'" class="select-none"></span>
                                </button>
                            </template>
                        </div>
                        <p class="text-center text-sm font-medium text-gray-700 dark:text-gray-300" x-text="ratingLabels[reviewForm.rating - 1] || 'Tap to rate'"></p>

                        <!-- Feedback Tags -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Feedback</label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="tag in availableTags" :key="tag">
                                    <button @click="toggleTag(tag)"
                                            :class="reviewForm.feedback_tags.includes(tag) ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-blue-300 dark:border-blue-700' : ''"
                                            class="px-3 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors"
                                            x-text="tag"></button>
                                </template>
                            </div>
                        </div>

                        <!-- Review Text -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Details (Optional)</label>
                            <textarea x-model="reviewForm.review_text" rows="3" maxlength="1000" placeholder="Share your experience..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                            <p class="text-xs text-gray-500 text-right mt-1" x-text="(reviewForm.review_text || '').length + '/1000'"></p>
                        </div>

                        <!-- Error -->
                        <div x-show="reviewError" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-sm text-red-600 dark:text-red-400" x-text="reviewError"></p>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="showReviewModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                        <button @click="submitReview()" :disabled="submitting || !reviewForm.rating"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50">
                            <span x-text="submitting ? 'Submitting...' : 'Submit Review'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Toast -->
        <div x-show="toast.show" x-transition
             class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
             x-text="toast.message" style="display: none;"></div>
    </div>

    @push('scripts')
    <script>
        function historyManager() {
            return {
                showReviewModal: false,
                submitting: false,
                reviewTaskId: null,
                reviewServiceName: '',
                reviewError: '',
                toast: { show: false, message: '', type: 'success' },

                reviewForm: {
                    rating: 0,
                    feedback_tags: [],
                    review_text: '',
                },

                ratingEmojis: ['\uD83D\uDE20', '\uD83D\uDE14', '\uD83D\uDE10', '\uD83D\uDE0A', '\uD83D\uDE01'],
                ratingLabels: ['Very Dissatisfied', 'Dissatisfied', 'Neutral', 'Satisfied', 'Very Satisfied'],
                availableTags: ['Punctual', 'Friendly', 'Professional', 'Hygienic', 'Thorough', 'Skilled', 'Rushed', 'Negligent'],

                openReviewModal(taskId, serviceName) {
                    this.reviewTaskId = taskId;
                    this.reviewServiceName = serviceName;
                    this.reviewForm = { rating: 0, feedback_tags: [], review_text: '' };
                    this.reviewError = '';
                    this.showReviewModal = true;
                },

                toggleTag(tag) {
                    const idx = this.reviewForm.feedback_tags.indexOf(tag);
                    if (idx > -1) this.reviewForm.feedback_tags.splice(idx, 1);
                    else this.reviewForm.feedback_tags.push(tag);
                },

                async submitReview() {
                    if (!this.reviewForm.rating) return;
                    this.submitting = true;
                    this.reviewError = '';
                    try {
                        const res = await fetch(`/manager/history/${this.reviewTaskId}/review`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.reviewForm)
                        });
                        const data = await res.json();
                        if (res.ok) {
                            this.showReviewModal = false;
                            this.toast = { show: true, message: 'Review submitted successfully!', type: 'success' };
                            setTimeout(() => this.toast.show = false, 3000);
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            this.reviewError = data.message || 'Failed to submit review';
                        }
                    } catch (e) {
                        this.reviewError = 'An error occurred. Please try again.';
                    }
                    this.submitting = false;
                }
            };
        }
    </script>
    @endpush
</x-layouts.general-manager>
