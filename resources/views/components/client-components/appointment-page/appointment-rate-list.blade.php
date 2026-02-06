@props([
    'items' => [],
    'maxHeight' => '20rem',
    'emptyTitle' => 'Nothing on the list yet',
    'emptyMessage' => 'You don\'t have any completed appointments to rate at the moment.',
])

<style>
[x-cloak] {
    display: none !important;
}

/* Star rating hover effect - highlights hovered star and all stars before it */
.star-rating-group .star-btn:hover svg,
.star-rating-group .star-btn:hover ~ .star-btn svg {
    color: #facc15;
    fill: #facc15;
}
</style>

<div class="w-full" x-data="appointmentRateList({{ Js::from($items) }})">
    @if(empty($items))
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
            <div class="w-64 h-64 mb-6 flex items-center justify-center">
                <img src="{{ asset('images/icons/no-items-found.svg') }}"
                     alt="No appointments"
                     class="w-full h-full object-contain opacity-80 dark:opacity-60">
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                {{ $emptyTitle }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md">
                {{ $emptyMessage }}
            </p>
        </div>
    @else
        <!-- Scrollable container with max height (only when items exist) -->
        <div class="overflow-y-auto"
             style="max-height: {{ $maxHeight }};"
             @scroll.window="openMenuId = null"
             @scroll="openMenuId = null">
            @foreach($items as $index => $item)
            <div class="group border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200"
                 data-rate-item
                 data-service="{{ $item['service'] ?? '' }}"
                 data-date="{{ $item['raw_date'] ?? '' }}">
                <div class="py-6 px-6">
                    <!-- Header Section -->
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                @if(isset($item['status']))
                                    @php
                                        $status = strtolower($item['status']);
                                    @endphp
                                    @if($status === 'completed' || $status === 'complete')
                                        <x-badge
                                            label="Completed"
                                            colorClass="bg-[#00BFFF20] text-[#00BFFF]"
                                            size="text-xs" />
                                    @elseif($status === 'in progress')
                                        <x-badge
                                            label="In Progress"
                                            colorClass="bg-[#FFA50020] text-[#FFA500]"
                                            size="text-xs" />
                                    @elseif($status === 'pending')
                                        <x-badge
                                            label="Pending"
                                            colorClass="bg-[#FFA50020] text-[#FFA500]"
                                            size="text-xs" />
                                    @elseif($status === 'approved' || $status === 'confirmed')
                                        <x-badge
                                            label="{{ ucfirst($item['status']) }}"
                                            colorClass="bg-[#2FBC0020] text-[#2FBC00]"
                                            size="text-xs" />
                                    @elseif($status === 'cancelled' || $status === 'rejected')
                                        <x-badge
                                            label="{{ ucfirst($item['status']) }}"
                                            colorClass="bg-[#FE1E2820] text-[#FE1E28]"
                                            size="text-xs" />
                                    @else
                                        <x-badge
                                            label="{{ $item['status'] }}"
                                            colorClass="bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300"
                                            size="text-xs" />
                                    @endif
                                @endif

                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $item['service'] }}
                                </h3>
                            </div>

                            <!-- Meta Information -->
                            <div class="flex flex-wrap items-center gap-x-3 text-xs text-gray-500 dark:text-gray-400">
                                @if(isset($item['service_date']))
                                <span class="flex items-center gap-1">
                                    Scheduled on <span class="font-bold">{{ $item['service_date'] }}</span>
                                </span>
                                @endif

                                @if(isset($item['service_date']) && isset($item['service_time']))
                                <span>·</span>
                                @endif

                                @if(isset($item['service_time']))
                                <span class="flex items-center gap-1">
                                    At <span class="font-bold">{{ $item['service_time'] }}</span>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Star Rating (uses flex-row-reverse + CSS sibling selectors for proper hover) -->
                        <div class="star-rating-group flex items-center gap-1 ml-4 flex-row-reverse justify-end">
                            @for($i = 5; $i >= 1; $i--)
                            <button
                                @click="openFeedbackModal({{ $index }}, {{ $i }})"
                                class="star-btn transition-all duration-200 hover:scale-110 focus:outline-none"
                                type="button">
                                <svg class="w-7 h-7 transition-colors duration-200 text-gray-300 dark:text-gray-600"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </button>
                            @endfor
                        </div>
                    </div>
                    <!-- Custom Slot for Extra Content -->
                    @if(isset($item['extra_content']))
                    <div class="mt-4">
                        {!! $item['extra_content'] !!}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <!-- Feedback Modal - teleported to body to escape overflow containers -->
    <template x-teleport="body">
        <div x-show="showFeedbackModal"
             x-cloak
             @click="closeFeedbackModal()"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4">

            <!-- Modal Container -->
            <div @click.stop
                 x-show="showFeedbackModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100 dark:border-gray-800 overflow-hidden">

                <!-- Close button -->
                <button type="button"
                        @click="closeFeedbackModal()"
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-900 dark:bg-gray-800 text-white rounded-full hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700 z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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
                        <h3 class="text-3xl sm:text-3xl font-bold text-gray-900 dark:text-white leading-tight my-3">
                            How would you rate<br class="hidden sm:block">our cleaning service?
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
                            Your input is valuable in helping us better understand your needs and tailor our service accordingly.
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
                        <button
                            @click="selectedRating = {{ $rating }}"
                            :class="selectedRating === {{ $rating }} ? 'scale-100 sm:scale-100' : 'scale-100'"
                            class="relative flex flex-col items-center transition-all duration-200 focus:outline-none group"
                            type="button">
                            <div class="rounded-full flex items-center justify-center transition-all duration-200"
                                 :class="selectedRating === {{ $rating }}
                                    ? 'bg-blue-600 dark:bg-blue-500 ring-4 ring-blue-200 dark:ring-blue-900 w-14 h-14 sm:w-16 sm:h-16'
                                    : 'bg-gray-200 dark:bg-gray-800 w-12 h-12 sm:w-14 sm:h-14 group-hover:bg-gray-300 dark:group-hover:bg-gray-700'">
                                <img src="{{ $emojiSrc }}"
                                     alt="Rating {{ $rating }}"
                                     :class="selectedRating === {{ $rating }} ? 'w-8 h-8 sm:w-10 sm:h-10' : 'w-6 h-6 sm:w-8 sm:h-8 grayscale opacity-60'"
                                     class="transition-all duration-200">
                            </div>
                            <span x-show="selectedRating === {{ $rating }}"
                                  x-transition
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
                                $keywords = ['Punctual', 'Friendly', 'Professional', 'Hygienic', 'Rushed', 'Negligent', 'Thorough', 'Skilled'];
                            @endphp
                            @foreach($keywords as $keyword)
                            <button
                                @click="toggleKeyword('{{ $keyword }}')"
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
                        <textarea
                            x-model="feedbackText"
                            rows="3"
                            placeholder="Add a comment"
                            class="w-full px-4 py-3 text-sm text-gray-900 dark:text-white border-0 bg-gray-50 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 focus:outline-none placeholder-gray-400 dark:placeholder-gray-500 resize-none transition-all"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button
                        @click="submitFeedback()"
                        :disabled="selectedRating === 0"
                        :class="selectedRating === 0
                            ? 'opacity-50 cursor-not-allowed bg-blue-600 dark:bg-blue-800'
                            : 'bg-blue-900 dark:bg-blue-700 hover:bg-blue-800 dark:hover:bg-blue-600'"
                        type="button"
                        class="w-full px-6 py-3.5 sm:py-4 text-sm font-bold text-white rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 dark:focus:ring-blue-700 disabled:hover:bg-blue-900 dark:disabled:hover:bg-blue-800">
                        Submit Feedback
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function appointmentRateList(items) {
    return {
        openMenuId: null,
        showFeedbackModal: false,
        selectedItem: null,
        selectedRating: 0,
        feedbackText: '',
        selectedKeywords: [],
        items: items,

        openFeedbackModal(index, rating) {
            this.selectedItem = this.items[index];
            this.selectedRating = rating;
            this.showFeedbackModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeFeedbackModal() {
            this.showFeedbackModal = false;
            this.selectedItem = null;
            this.selectedRating = 0;
            this.feedbackText = '';
            this.selectedKeywords = [];
            document.body.style.overflow = 'auto';
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
            if (!this.selectedItem || !this.selectedItem.id) {
                alert('Error: No appointment selected');
                return;
            }

            try {
                const response = await fetch('/client/appointments/' + this.selectedItem.id + '/feedback', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        rating: this.selectedRating,
                        keywords: this.selectedKeywords,
                        feedback_text: this.feedbackText
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    alert('Thank you for your feedback!');
                    this.closeFeedbackModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to submit feedback. Please try again.');
                }
            } catch (error) {
                console.error('Error submitting feedback:', error);
                alert('An error occurred. Please try again.');
            }
        }
    };
}
</script>
